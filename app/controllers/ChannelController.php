<?php
namespace Controller;
require_once 'BaseController.php';
require_once __DIR__ . "/../../vendor/autoload.php";

use Service\ChannelService;

class ChannelController extends BaseController 
{
    private $channelService;

    public function __construct() 
    {
        parent::__construct();
        $this->channelService = new ChannelService();
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
            $this->redirect('index.php?controller=tour&action=index');
        }
    }
}
