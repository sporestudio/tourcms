<?php
/* * (c) 2023 Backoffice
 * 
 * This file is repomsible for mananging the booking API connection to TourCMS.
 * It uses the TourCMS library to start, commit, list, show, and cancel bookings.s
 * 
 */

namespace Model;

use Model\Api;

class Book
{
    private $tourcms;

    public function __construct($marketplaceId, $apiKey, $baseUrl, $timeOut = 0)
    {
        $api = new Api($marketplaceId, $apiKey, $baseUrl, $timeOut);
        $this->tourcms = $api->getTourCMS();
    }

    public function start_booking($booking, $channelId)
    {
        $result = $this->tourcms->start_new_booking($booking, $channelId);
        return $result->booking;
    }

    public function commit_booking($booking, $channelId)
    {
        $result = $this->tourcms->commit_new_booking($booking, $channelId);
        return $result->booking;
    }

    public function list_bookings($params, $channelId)
    {
        $result = $this->tourcms->list_bookings($params, $channelId);
        return $result;
    }

    public function show_booking($booking, $channelId)
    {
        $result = $this->tourcms->show_booking($booking, $channelId);
        return $result;
    }

    public function cancel_booking($booking, $channelId)
    {
        $result = $this->tourcms->cancel_booking($booking, $channelId);
        return $result;
    }
}