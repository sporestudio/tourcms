<?php
/* * (c) 2023 Backoffice
 * 
 * This file is reponsible for managing the booking process.
 * It handles the display of booking form, processing the booking data,
 * committing the booking, listing bookings, showing a specific booking,
 * and cancelling a booking.
 * 
 */

namespace Controller;

use Service\BookingService;
use Lib\RedisService;
use Model\Book;

class BookingController extends BaseController
{
    protected $config;
    private $bookingService;

    public function __construct($config)
    {
        $this->config = $config;
        parent::__construct($this->config);

        $marketplaceId = $this->config["MARKETPLACE_ID"];
        $apiKey = $this->config["API_KEY"];
        $baseUrl = $this->config["BASE_URL"];
        $timeout = $this->config["TIMEOUT"];

        $bookModel = new Book($marketplaceId, $apiKey, $baseUrl, $timeout);
        $this->bookingService = new BookingService($bookModel);
    }

    public function index()
    {
        return $this->template->render("book.html");
    }

    private function processData($postData)
    {
        $firstname = htmlspecialchars($postData['firstname'] ?? '', ENT_QUOTES, 'UTF-8');
        $lastname = htmlspecialchars($postData['lastname'] ?? '', ENT_QUOTES, 'UTF-8');
        $email = filter_var($postData['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $phone = htmlspecialchars($postData['phone'] ?? '', ENT_QUOTES, 'UTF-8');

        if (empty($firstname) || empty($lastname) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid customer data');
        }

        return [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
        ];
    }

    public function processBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $customer = $this->processData($_POST);
                $this->redis->storeItemInRedis('CUSTOMER', json_encode($customer), RedisService::REDIS_TYPE_STRING);
                $channelId = $this->redis->getItemFromRedis('channel_id', RedisService::REDIS_TYPE_STRING);
                $componentKey = $this->redis->getItemFromRedis('component_key', RedisService::REDIS_TYPE_STRING);

                $data = $this->bookingService->startBooking($componentKey, $customer, $channelId);
                $this->redis->storeItemInRedis('booking_id', $data['booking_id'], RedisService::REDIS_TYPE_STRING);
                $this->redis->storeItemInRedis('customer_id', $data['customer_id'], RedisService::REDIS_TYPE_STRING);

                error_log('Data passed to confirmation.html: ' . print_r($data, true));
                return $this->template->render('confirmation.html', ['data' => $data]); 
            } catch (\InvalidArgumentException $e) {
                error_log("BookingController: Error processing booking: " . $e->getMessage());
                return $this->template->render('book.html', ['error' => 'Invalid customer data']);               
            }
        }
    }

    public function commitBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $bookingId = $this->redis->getItemFromRedis('booking_id', RedisService::REDIS_TYPE_STRING);
                $channelId = $this->redis->getItemFromRedis('channel_id', RedisService::REDIS_TYPE_STRING);

                $data = $this->bookingService->commitBooking($bookingId, $channelId);

                return $this->template->render('success.html', ['data' => $data]);
            } catch (\InvalidArgumentException $e) {
                error_log('Error commiting booking: '. $e->getMessage());
                return $this->template->render('book.html', ['error' => 'Invalid booking data']);
            }
        }
    }

    public function listBookings()
    {
        $channelId = $this->redis->getItemFromRedis('channel_id', RedisService::REDIS_TYPE_STRING);
        $params = '';
        
        $bookings = $this->bookingService->listBookings($params, $channelId);

        return $this->template->render('bookings.html', $bookings);
    }

    public function showBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $bookingId = $_GET['booking_id'] ?? null;
                if (!$bookingId) {
                    throw new \InvalidArgumentException('Booking ID is required');
                }
                $channelId = $this->redis->getItemFromRedis('channel_id', RedisService::REDIS_TYPE_STRING);

                $data = $this->bookingService->showBooking($bookingId, $channelId);
                return $this->template->render('booking.html', ['booking' => $data]);
            } catch (\Exception $e) {
                error_log('Error showing booking: '. $e->getMessage());
                return $this->template->render('bookings.html', ['error' => 'Invalid booking data']);
            }
        }
    }

    public function cancelBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $bookingId = $_GET['booking_id'] ?? null;
                if (!$bookingId) {
                    throw new \InvalidArgumentException('Booking ID is required');
                }
                $channelId = $this->redis->getItemFromRedis('channel_id', RedisService::REDIS_TYPE_STRING);
                $note = '';

                $data = $this->bookingService->cancelBooking($bookingId, $channelId, $note);
                return $this->template->render('success.html', ['cancel' => $data]);
            } catch (\Exception $e) {
                error_log('Error cancelling booking: '. $e->getMessage());
                return $this->template->render('bookings.html', ['error' => 'Invalid booking data']);
            }
        }
    }
}
