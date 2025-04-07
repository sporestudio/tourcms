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

    public function getInternalKey($channelId) 
    {
        error_log("Channel: getInternalKey called with channel_id: $channelId");

        $result = $this->tourcms->show_channel($channelId);
        error_log("Channel: show_channel response for channel_id $channelId: " . print_r($result, true));

        if (isset($result->channel->internal_api_key)) {
            $internalApiKey = (string) $result->channel->internal_api_key;
            error_log("Channel: internal_api_key got for channel_id $channelId: $internalApiKey");
            return $internalApiKey;
        }

        error_log("Channel: Error getting internal api key for channel_id $channelId");
        return null;
    }
}
