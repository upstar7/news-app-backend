<?php

namespace App\NewsFeed;

use App\Models\News;
use App\Models\Source;

class NewsFeeder {
    protected $apiUrl;
    protected $apiKey;
    protected $apiKeyParamName;
    protected $otherParams;
    protected $source;
    protected $page;

    public static function set(&$instance, $config, $sourceId)
    {
        $instance->apiUrl = $config['api_url'];
        $instance->apiKey = $config['api_key'];
        $instance->apiKeyParamName = $config['api_parameter_name'];
        $instance->source = Source::whereId($sourceId)->first();
        $instance->page = $instance->source ? $instance->source->last_page_retrieved + 1 : 1;
    }

    public static function saveNews($title, $body, $category, $author, $thumb, $url, $publishedAt, $sourceId)
    {
        News::create([
            'title'         => $title,
            'body'          => $body,
            'category'      => $category,
            'author'        => $author,
            'thumb'         => $thumb ?? 'https://placehold.co/400x200?text=No+Image',
            'web_url'       => $url,
            'published_at'  => date('Y-m-d H:i:s', strtotime($publishedAt)),
            'source_id'     => $sourceId,
        ]);
    }

    public static function successResponse()
    {
        return [
            'status'        => true,
            'msg'           => 'Fetched successfuly and saved',
        ];
    }

    public static function failedResponse($jsonResponse)
    {
        return [
            'status'    => false,
            'msg'       => isset($jsonResponse['message']) ? $jsonResponse['message'] : 'Internal server error',
        ];
    }
}