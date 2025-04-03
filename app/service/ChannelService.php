<?php
namespace Service;

use Model\Channel;

class ChannelService 
{
    private $channelModel;

    public function __construct(Channel $channelModel) 
    {
        $this->channelModel = $channelModel;
    }

    public function listChannels() 
    {
        $channels = $this->channelModel->listChannels();

        $processedChannels = [];
        foreach ($channels as $channel) {
            $processedChannels[] = [
                'id' => (string) $channel->channel_id,
                'name' => (string) $channel->channel_name
            ];
        }

        return $processedChannels;
    }
}