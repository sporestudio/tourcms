<?php
/* * (c) 2023 Backoffice
 * 
 * This file is reponsible to manage the Booking service class.
 * The BookingService class is responsible for managing bookings.
 * It uses the Book model to interact with the TourCMS API and perform operations related to bookings.
 * 
 */

namespace Service;

use Model\Book;
use SimpleXMLElement;

class BookingService
{
    private $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function startBooking($component_key, $costumer, $channelId)
    {
        // Builtin the XML response 
        $booking = new SimpleXMLElement('<booking />');
        $booking->addChild('total_customers', '1');

        $components = $booking->addChild('components');
        $component = $components->addChild('component');
        $component->addChild('component_key', $component_key);

        $customers = $booking->addChild('customers');
        $customer = $customers->addChild('customer');
        $customer->addChild('firstname', $costumer['firstname']);
        $customer->addChild('surname', $costumer['lastname']);
        $customer->addChild('email', $costumer['email']);

        error_log('Booking to send: ' . print_r($booking, true));
        $book = $this->book->start_booking(
            $booking,
            $channelId
        );
        error_log('Booking response: ' . print_r($book, true));

        $result = [
            'booking_id' => (string) $book->booking_id,
            'customer_name' => (string) $book->customers->customer->firstname . ' ' . (string) $book->customers->customer->surname,
            'customer_id' => (string) $book->customers->customer->customer_id,
            'price' => (string) $book->sales_revenue,
            'currency' => (string) $book->sale_currency,
        ];

        return $result;
    }

    public function commitBooking($bookingId, $channelId)
    {
        // Builting XML to commit the booking
        $booking = new SimpleXMLElement('<booking />');
        $booking->addChild('booking_id', $bookingId);

        $commitBook = $this->book->commit_booking(
            $booking,
            $channelId
        );
        error_log('Booking commit response: ' . print_r($commitBook, true));

        $result = [
            'booking_id' => (string) $commitBook->booking_id,
            'voucher_url' => (string) $commitBook->voucher_url,
        ];

        return $result;
    }

    public function listBookings($params, $channelId)
    {
        $result = $this->book->list_bookings($params, $channelId);
        error_log('Bookings response: ' . print_r($result, true));

        $availableBookings = [];
        $cancelledBookings = [];
    
        foreach ($result->bookings->booking as $booking) {
            $mappedBooking = $this->mapBooking($booking);
    
            if ((int) $booking->cancel_reason > 0) {
                $cancelledBookings[] = $mappedBooking;
            } else {
                $availableBookings[] = $mappedBooking;
            }
        }
    
        error_log('Available bookings: ' . print_r($availableBookings, true));
        error_log('Cancelled bookings: ' . print_r($cancelledBookings, true));
    
        return [
            'availableBookings' => $availableBookings,
            'cancelledBookings' => $cancelledBookings,
        ];
    }

    private function mapBooking($booking)
    {
        return [
            'cancel_reason' => (string) $booking->cancel_reason,
            'booking_id' => (string) $booking->booking_id,
            'channel_id' => (string) $booking->channel_id,
            'channel_name' => (string) $booking->channel_name,
            'lead_customer_name' => (string) $booking->lead_customer_name,
            'booking_name' => (string) $booking->booking_name,
            'customer_id' => (string) $booking->lead_customer_id,
        ];
    }

    public function showBooking($bookingId, $channelId)
    {
        $result = $this->book->show_booking($bookingId, $channelId);
        error_log('Booking response: ' . print_r($result, true));
        
        $booking = [
            'booking_id' => (string) $result->booking->booking_id,
            'booking_name' => (string) $result->booking->booking_name,
            'start_date' => date("jS F Y (l)", strtotime($result->booking->start_date)),
            'end_date' => date("jS F Y (l)", strtotime($result->booking->end_date)),
            'channel_name' => (string) $result->booking->channel_name,
            'lead_customer_name' => (string) $result->booking->lead_customer_name,
            'voucher_url' => (string) $result->booking->voucher_url,
        ];
    
        return $booking;
    }

    public function cancelBooking($bookingId, $channelId, $note)
    {
        // Builtin the XML to cancel the booking
        $booking = new SimpleXMLElement('<booking />');
        $booking->addChild('booking_id', $bookingId);
        $booking->addChild('note', $note);

        $result = $this->book->cancel_booking($booking, $channelId);
        error_log('Booking cancel response: ' . print_r($result, true));

        if ($result->error == 'OK') {
            echo "<script>alert('Booking cancelled successfully!'); window.location.href='/bookings';</script>";
        } elseif ($result->error == 'PREVIOUSLY CANCELLED') {
            echo "<script>alert('Booking already cancelled!'); window.location.href='/bookings';</script>";
        } else {
            echo "<script>alert('Error cancelling booking: " . $result->error . "'); window.location.href='/bookings';</script>";
        }
    }
}