<?php

namespace App\NewsFeed;
use App\Models\News;
use App\Models\Source;
use Illuminate\Support\Facades\Http;

class NewYorkTimesNewsApiFeeder extends NewsFeeder {

    public static function init()
    {
        $config = config('news-sources.newyorktimes');
        $instance = new self;
        parent::set($instance, $config, News::NEW_YORK_TIMES_API_SOURCE_ID);
        return $instance;
    }

    public function fetch()
    {
        $queryParams = [
            'page'                  => $this->page,
            $this->apiKeyParamName  => $this->apiKey,
        ];

        $response = Http::get($this->apiUrl, $queryParams);
        $jsonResponse = $response->json();
        if($response->ok() && strtolower($jsonResponse['status']) == 'ok'){
                // save into news table
                foreach($jsonResponse['response']['docs'] as $item){
                    parent::saveNews(
                        $item['abstract'],
                        $item['lead_paragraph'],
                        $item['section_name'],
                        isset($item['byline']) ? $item['byline']['person'][0]['firstname'] . ' ' . $item['byline']['person'][0]['lastname'] : null,
                        isset($item['multimedia']) && count($item['multimedia']) > 0 ? $this->extractImageFromMultimedia($item['multimedia']) : 'https://placehold.co/400x200?text=No+Image',
                        $item['web_url'],
                        $item['pub_date'],
                        News::NEW_YORK_TIMES_API_SOURCE_ID
                    );
                }
            $this->source->update(['last_page_retrieved' => $this->page]);
            return parent::successResponse();
        }
        return parent::failedResponse($jsonResponse);
    }

    public function extractImageFromMultimedia($multimediaArray)
    {
        return 'https://www.nytimes.com/' . $multimediaArray[0]['url'];
    }
}