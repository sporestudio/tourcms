<?php
/*
 * (c) 2023 Backoffice
 * 
 * This file is responsible for managing the customer data.
 * It handles the requests to the TourCMS API for searching and showing customer information.
 * 
 */

namespace Model;

use Model\Api;

class Customer
{
    private $tourcms;

    public function __construct($marketplaceId, $apiKey, $baseUrl, $timeOut = 0)
    {
        $api = new API($marketplaceId, $apiKey, $baseUrl, $timeOut);
        $this->tourcms = $api->getTourCMS();
    }

    public function search_customers($params, $channelId)
    {
        $result = $this->tourcms->search_enquiries($params, $channelId);
        return $result;
    }

    public function show_customer($customerId, $channelId)
    {
        $result = $this->tourcms->show_customer($customerId, $channelId);
        return $result;
    }
}