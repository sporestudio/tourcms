<?php
/*
 * (c) 2023 Backoffice
 * 
 * This file is responsible for managing the TourController.
 * It handles the listing of tours, showing a specific tour, and checking availability.
 * 
 */

namespace Controller;

use Model\Channel;
use Service\TourService;

class TourController extends BaseController
{
    private $tourService;
    protected $config;

    public function __construct(array $config) 
    {
        $this->config = $config;
        parent::__construct($config);

        $MARKETPLACE_ID = $this->config["MARKETPLACE_ID"];
        $API_KEY = $this->config["API_KEY"];
        $BASE_URL = $this->config["BASE_URL"];
        $TIMEOUT = $this->config["TIMEOUT"];

        $channelModel = new Channel($MARKETPLACE_ID, $API_KEY, $BASE_URL, $TIMEOUT);
        $this->tourService = new TourService($channelModel);
    }

    public function listTours() 
    {
        $channelId = $this->redis->getItemFromRedis('channel_id', 'string');

        if ($channelId) {
            $tours = $this->tourService->listTours($channelId, $this->config['BASE_URL'], $this->config['TIMEOUT']);
            error_log("TourController: Tours got from service: " . print_r($tours, true));

            return $this->template->render('tours.html', ['tours' => $tours]);
        } else {
            error_log("TourController: channel_id not found on session");
            $this->redirect('/dashboard');
        }
    }

    public function showTour()
    {
        $tourId = $_POST['tour_id'] ?? null;
        $this->redis->storeItemInRedis('tour_id', $tourId, 'string');

        $channelId = $this->redis->getItemFromRedis('channel_id', 'string');

        if ($channelId) {
            error_log("TourController: Channel ID for show tour on session: $channelId");

            $tour = $this->tourService->showTour($tourId, $channelId, $this->config['BASE_URL'], $this->config['TIMEOUT']);
            error_log('TourController: tour got from service'. print_r($tour, true));

            return $this->template->render('tour.html', ['tour' => $tour]);
        } else {
            error_log('TourController: tour_id not found in session');
            $this->redirect('/dashboard');
        }
    }

    public function availabililty()
    {
        if (isset($_POST['date']) && isset($_POST['people'])) {
            $date = $_POST['date'] ?? null;
            $people = $_POST['people'] ?? null;

            $channelId = $this->redis->getItemFromRedis('channel_id', 'string');
            $tourId = $this->redis->getItemFromRedis('tour_id', 'string');

            $available = $this->tourService->checkAvailability($tourId, $channelId, $this->config['BASE_URL'], $date, $people, $this->config['TIMEOUT']);
            $this->redis->storeItemInRedis('component_key', $available['key'], 'string');

            if (isset($available['error'])) {
                return $this->template->render('availability.html', ['error' => $available['error']]);
            }

            return $this->template->render('availability.html', ['availability' => $available]);
        } else {
            error_log('TourController: error checking availability');
            $this->redirect('/dashboard');
        }
    }
}