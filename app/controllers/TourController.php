<?php
namespace Controller;

use Model\Channel;
use Service\TourService;

class TourController extends BaseController
{
    private $tourService;

    public function __construct($MARKETPLACE_ID, $API_KEY, $BASE_URL, $TIMEOUT) 
    {
        parent::__construct();

        $channelModel = new Channel($MARKETPLACE_ID, $API_KEY, $BASE_URL, $TIMEOUT);
        $this->tourService = new TourService($channelModel, $BASE_URL, $TIMEOUT);
    }

    public function listTours() 
    {
        $channelId = $_SESSION['channel_id'] ?? null;

        if ($channelId) {
            error_log("TourController: Channel ID on session: $channelId");
            //error_log("Globals " . print_r($GLOBALS, true));

            $tours = $this->tourService->listTours($channelId, $GLOBALS['BASE_URL'], $GLOBALS['TIMEOUT']);
            error_log("TourController: Tours got from service: " . print_r($tours, true));

            echo $this->template->render('tours.html', ['tours' => $tours]);
        } else {
            error_log("TourController: channel_id not found on session");
            $this->redirect('/dashboard');
        }
    }
}