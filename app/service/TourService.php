<?php
/* * (c) 2023 Backoffice
 * 
 * This file is responsible for the TourService class.
 * The TourService class is responsible for managing tours.
 * It uses the Tour model to interact with the database and perform operations related to tours.
 * 
 */

namespace Service;

use Model\Channel;
use Model\Tour;

class TourService
{
    private $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
        //$this->tourModel = new Tour($internalApiKey, $baseUrl, $timeOut);
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

    public function showTour($tourId, $channelId, $baseUrl, $timeOut = 0) 
    {
        $internalApiKey = $this->channel->getInternalKey($channelId);

        if ($internalApiKey) {
            error_log("ChannelService: internal_api_key got for channel_id $channelId: $internalApiKey");
            $tourModel = new Tour($internalApiKey, $baseUrl, $timeOut);
            $tour = $tourModel->showTour($tourId, $channelId);
            error_log("TourService: Tour got for channel_id $channelId: " . print_r($tour, true));

            return [
                'id' => (string) $tour->tour_id,
                'name' => (string) $tour->tour_name,
                'image' => $tour->images->image->url,
                'price' => (float) $tour->from_price,
                'currency' => (string) $tour->sale_currency,
                'desc' => (string) $tour->shortdesc,
                'has_sale' => (bool) $tour->has_sale,
                'suitable_for_children' => (bool) $tour->suitable_for_children,
                'channel_id' => (string) $tour->channel_id,
            ];
        }

        error_log("ChannelService: error getting internal_api_key for channel_id $channelId");
        return [];
    }

    public function checkAvailability($tourId, $channelId, $baseUrl, $date, $people,  $timeOut = 0)
    {
        $internalApiKey = $this->channel->getInternalKey($channelId);

        if ($internalApiKey) {
            $tourModel = new Tour($internalApiKey, $baseUrl, $timeOut);
            $available = $tourModel->checkAvailability($tourId, $channelId, $date, $people);
            $availableComponents = count($available->available_components->component);

            if ($availableComponents > 0) {
                return [
                    'tour_name' => $available->tour_name,
                    'available_components' => $available->available_components,
                    'start_date' => $available->available_components->component->start_date,
                    'start_time' => $available->available_components->component->start_time,
                    'end_date' => $available->available_components->component->end_date,
                    'end_time' => $available->available_components->component->end_time,
                    'price_row' => $available->available_components->component->price_breakdown->price_row,
                    'total' => $available->available_components->component->total_price_display,
                    'key' => $available->available_components->component->component_key,
                ];
            } else {
                return [
                    'error' => 'No available components found for the selected date and number of people.',
                ];
            }          
        }

        error_log("ChannelService: error getting internal_api_key for channel_id $channelId");
        return [];
    }
}