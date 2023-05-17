<?php

namespace App\NewsFeed;
use App\Models\News;
use App\Models\Source;
use Illuminate\Support\Facades\Http;

class TheGuardianNewsApiFeeder extends NewsFeeder {

    public static function init()
    {
        $config = config('news-sources.theguardian');
        $instance = new self;
        parent::set($instance, $config, News::THE_GUARDIAN_API_SOURCE_ID);
        return $instance;
    }

    public function fetch($perPage = 10)
    {
        $queryParams = [
            'pageSize'              => $perPage,
            'page'                  => $this->page,
            $this->apiKeyParamName  => $this->apiKey,
        ];
        
        $response = Http::get($this->apiUrl, $queryParams);
        $jsonResponse = $response->json();
        if($response->ok() && $jsonResponse['response']['status'] == 'ok'){
            // save into news table
            foreach($jsonResponse['response']['results'] as $item){
                parent::saveNews(
                    $item['webTitle'],
                    $item['webTitle'],
                    $item['pillarName'],
                    null,
                    'https://placehold.co/400x200?text=No+Image',
                    $item['webUrl'],
                    $item['webPublicationDate'],
                    News::THE_GUARDIAN_API_SOURCE_ID
                );
            }
            $this->source->update(['last_page_retrieved' => $this->page]);
            return parent::successResponse();
        }
        return parent::failedResponse($jsonResponse);
    }
}