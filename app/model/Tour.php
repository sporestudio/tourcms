<?php
/* * (c) 2023 Backoffice
 * 
 * This file is responsible for managing the TourCMS API interactions.
 * It provides methods to list tours, get images, show a specific tour,
 * and check availability for a tour.
 * 
 */

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

    public function showTour($tourId, $channelId)
    {
        $result = $this->tourcms->show_tour($tourId, $channelId);
        error_log("Show_tour response for tour_id $tourId: " . print_r($result, true));

        return $result->tour;
    }

    public function checkAvailability($tourId, $channelId, $date, $people)
    {
        $qs = "date=$date";
        $qs .= "&r1=$people";

        $result = $this->tourcms->check_tour_availability($qs, $tourId, $channelId);
        error_log("Tour: Check_availability response for tour_id $tourId: " . print_r($result, true));

        return $result;
    }
}