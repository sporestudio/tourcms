<?php
/* * (c) 2023 Backoffice
 * 
 * This file is responsible for managing the API connection to TourCMS.
 * It uses the TourCMS library to handle API requests and responses.
 * 
 */

namespace Model;

use TourCMS\Utils\TourCMS as TourCMS;

class Api 
{
    private $marketplaceId;
    private $apiKey;
    private $resultType;
    private $timeOut = 0;
    private $baseUrl;

    public function __construct($marketplaceId, $apiKey, $baseUrl, $timeOut) 
    {
        $this->marketplaceId = $marketplaceId;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->resultType = "simplexml";
        $this->timeOut = $timeOut;
    }


    public function getTourCMS() 
    {
        $tourcms = new TourCMS($this->marketplaceId, $this->apiKey, $this->resultType, $this->timeOut);
        $tourcms->set_base_url($this->baseUrl);

        return $tourcms;
    }
}