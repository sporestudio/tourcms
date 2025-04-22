<?php
/*
 * (c) 2023 Backoffice
 * 
 * This file is reponsible to managing the logic related to the customer data.
 * I
 *  
 */ 

 namespace Service;

 use Model\Customer;

 class CustomerService
 {
    private $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
    
    private function getValueOrNull($value)
    {
        return isset($value) && $value !== '' ? (string) $value : null;
    }

    public function showCustomer($customerId, $channelId)
    {
        $result = $this->customer->show_customer($customerId, $channelId);
        error_log('Show customer api response: ' . print_r($result, true));

        return [
            'firstname' => $this->getValueOrNull($result->customer->firstname),
            'surname' => $this->getValueOrNull($result->customer->surname),
            'customer_id' => $this->getValueOrNull($result->customer->customer_id),
            'customer_email' => $this->getValueOrNull($result->customer->email),
            'address' => $this->getValueOrNull($result->cusotmer->address),
            'city' => $this->getValueOrNull($result->customer->city),
            'country' => $this->getValueOrNull($result->customer->country),
            'nationality' => $this->getValueOrNull($result->customer->nationality),
            'gender' => $this->getValueOrNull($result->customer->gender),
            'phone' => $this->getValueOrNull($result->customer->tel_mobile),        
        ];
    }
 }