<?php
namespace Model;

use Model\Api;

class Tour 
{
    private $tourcms;

    public function __construct($internalApiKey, $baseUrl, $timeOut = 0, $marketplaceId = 0) 
    {
        $api = new Api($marketplaceId, $internalApiKey, $baseUrl, $timeOut);
        $this->tourcms = $api->getTourCMS();
    }

    public function listTours($channelId)
    {
        $result = $this->tourcms->list_tours($channelId);
        error_log("List_tours response for channel_id $channelId: " . print_r($result, true));
        
        return $result->tour;
    }

    public function getImages($channelId) 
    {
        $result = $this->tourcms->list_tour_images($channelId);
        error_log("List_tours response for channel_id $channelId: " . print_r($result, true));

        return $result->tour;
    }
}