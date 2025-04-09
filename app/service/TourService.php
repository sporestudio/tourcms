<?php
namespace Service;

use Model\Channel;
use Model\Tour;

class TourService
{
    private $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public function listTours($channelId, $baseUrl, $timeOut = 0) 
    {
        $internalApiKey = $this->channel->getInternalKey($channelId);

        if ($internalApiKey) {
            error_log("ChannelService: internal_api_key got for channel_id $channelId: $internalApiKey");
  
            $tourModel = new Tour($internalApiKey, $baseUrl, $timeOut);

            $tours = $tourModel->listTours($channelId);
            error_log("TourService: Tours got for channel_id $channelId: " . print_r($tours, true));

            $thumbnails = $tourModel->getImages($channelId);
            error_log("TourService: Images got for channel_id $channelId: " . print_r($thumbnails, true));

            $imageMap = [];
            foreach ($thumbnails as $thumbnail) {
                $tourId = (string) $thumbnail->tour_id;
                $imageMap[$tourId] = (string) $thumbnail->images->image->url_thumbnail;
            }

            error_log("Thumbnails!". print_r($imageMap, true));

            $processedTours = [];
            foreach ($tours as $tour) {
                $tourId = (string) $tour->tour_id;
                $processedTours[] = [
                    'id' => $tourId,
                    'name'=> (string) $tour->tour_name,
                    'last_update' => (string) $tour->descriptions_last_updated,
                    'thumbnail_image' => $imageMap[$tourId] ?? null,
                ];
            }

            return $processedTours;
        }

        error_log("ChannelService: error getting internal_api_key for channel_id $channelId");
        return [];
    }
}