<?php
namespace Model;

use Model\Api;

class Channel 
{
    private $tourcms;

    public function __construct($marketplaceId, $apiKey, $baseUrl, $timeOut = 0) 
    {
        $api = new Api($marketplaceId, $apiKey, $baseUrl, $timeOut);
        $this->tourcms = $api->getTourCms();
    }

    public function listChannels() 
    {
        $result = $this->tourcms->list_channels();

        return $result->channel;
    }

    public function showChannel($channelId) 
    {
        $result = $this->tourcms->show_channel($channelId);

        return $result;
    }
}
