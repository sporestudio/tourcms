<?php
/*
 * (c) 2023 Backoffice
 * 
 * This file is reponsible for displaying the customer data.
 * It manages the data returned from the customer service and formats it for the application.
 *  
 */

namespace Controller;

use Service\CustomerService;
use Lib\RedisService;
use Model\Customer;


class CustomerController extends BaseController
{
    protected $config;
    private $customerService;

    public function __construct($config)
    {
        $this->config = $config;
        parent::__construct($this->config);

        $marketplaceId = $this->config["MARKETPLACE_ID"];
        $apiKey = $this->config["API_KEY"];
        $baseUrl = $this->config["BASE_URL"];
        $timeout = $this->config["TIMEOUT"];

        $customerModel = new Customer($marketplaceId, $apiKey, $baseUrl, $timeout);
        $this->customerService = new CustomerService($customerModel);
    }

    public function listCustomers()
    {
        try {
            $channelId = $this->redis->getItemFromRedis('channel_id', RedisService::REDIS_TYPE_STRING);
            $params = '';
            $customers = $this->customerService->listCustomers($params, $channelId);

            return $this->template->render('customers.html', $customers);
        } catch (\Exception $e) {
            error_log('Error listing customers: '. $e->getMessage());
                return $this->template->render('customers.html', ['error' => 'Invalid customers data']);
        }
    }

    public function showCustomer()
    {
        try {
           if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $customerId = $_POST['customer_id'];
                $channelId = $this->redis->getItemFromRedis('channel_id', RedisService::REDIS_TYPE_STRING);

                if (!$customerId) {
                    throw new \InvalidArgumentException('Customer ID is required');
                }
                
                $customer = $this->customerService->showCustomer($customerId, $channelId);

                return $this->template->render('customer.html', ['customer' => $customer]);
           }
        } catch (\Exception $e) {
            error_log('Error listing customers: '. $e->getMessage());
                return $this->template->render('customer.html', ['error' => 'Invalid customers data']);
        }
    }
}