<?php
/* * (c) 2023 Backoffice
 * 
 * This file is responsible for managing the channel selection process.
 * It handles the display of available channels and the selection of a channel.
 * 
 */

namespace Controller;

use Model\Channel;
use Lib\RedisService;
use Service\ChannelService;

class ChannelController extends BaseController 
{
    private $channelService;
    protected $config;

    public function __construct(array $config) 
    {
        $this->config = $config;
        parent::__construct($config);

        $MARKETPLACE_ID = $this->config["MARKETPLACE_ID"];
        $API_KEY = $this->config['API_KEY'];
        $BASE_URL = $this->config["BASE_URL"];
        $TIMEOUT = $this->config["TIMEOUT"];

        $channelModel = new Channel($MARKETPLACE_ID, $API_KEY, $BASE_URL, $TIMEOUT);
        $this->channelService = new ChannelService($channelModel);
    }

    public function index() 
    {
        $channels = $this->channelService->listChannels();
        return $this->template->render('dashboard.html', ['channels' => $channels]);
    }

    public function select() 
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            if (isset($_POST['channel_id'])) {
                $channelId = $_POST['channel_id'];

                $cacheChannelId = $channelId;
                $this->redis->storeItemInRedis('channel_id', $cacheChannelId, RedisService::REDIS_TYPE_STRING);
                      
                $this->redirect('/tours');
            } else {
                error_log("ChannelController: channel_id not set in POST");
                $this->redirect('/dashboard');
            }
    }
}