<?php
namespace Controller;

use Model\Channel;
use Service\ChannelService;

class ChannelController extends BaseController 
{
    private $channelService;

    public function __construct($MARKETPLACE_ID, $API_KEY, $BASE_URL, $TIMEOUT) 
    {
        parent::__construct();

        $channelModel = new Channel($MARKETPLACE_ID, $API_KEY, $BASE_URL, $TIMEOUT);
        $this->channelService = new ChannelService($channelModel);
    }

    public function index() 
    {
        $channels = $this->channelService->listChannels();

        echo $this->template->render('dashboard.html', ['channels' => $channels]);
    }

    public function select() 
    {
        if (isset($_POST['channel_id'])) {
            $_SESSION['channel_id'] = $_POST['channel_id'];
            $this->redirect('/tours');
        }
    }
}