<?php

namespace App\NewsFeed;
use App\Models\News;
use App\Models\Source;
use App\NewsFeed\NewsFeeder;
use Illuminate\Support\Facades\Http;

class NewsApiFeeder extends NewsFeeder {

    public static function init()
    {
        $config = config('news-sources.newsapi');
        $instance = new self;
        parent::set($instance, $config, News::NEWS_API_SOURCE_ID);
        $instance->otherParams = $config['other_paremeters'];
        return $instance;
    }

    public function fetch($perPage = 10)
    {
        $categories = ['general', 'business', 'sports', 'science', 'health', 'entertainment', 'technology'];

        $queryParams = array_merge([
            'category'              => '',
            'pageSize'              => $perPage,
            'page'                  => $this->page,
            $this->apiKeyParamName  => $this->apiKey,
        ], $this->otherParams);
        

        foreach($categories as $category){
            $queryParams['category'] = $category;
            $response = Http::get($this->apiUrl, $queryParams);
            $jsonResponse = $response->json();
            if($response->ok() && $jsonResponse['status'] == 'ok'){
                // save into news table
                foreach($jsonResponse['articles'] as $item){
                    parent::saveNews(
                        $item['title'],
                        $item['content'],
                        $category,
                        !empty($item['author']) ? $item['author'] : null,
                        $item['urlToImage'] ?? null,
                        $item['url'],
                        $item['publishedAt'],
                        News::NEWS_API_SOURCE_ID
                    );
                }
            }
        }
        if($response->ok()){
            $this->source->update(['last_page_retrieved' => $this->page]);
            return parent::successResponse();
        }
        return parent::failedResponse($jsonResponse);
    }
}