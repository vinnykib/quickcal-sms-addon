<?php
// Required if your environment does not handle autoloading
require_once __DIR__ . '/../vendor/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

class SMSHandler {

    private $twilio;

    public function __construct() {
        $this->initialize_twilio();
    }

    private function initialize_twilio() {
        $options_value = get_option('sms_control');
        if ($options_value == 'enable') {
            $sid = get_option('application_id');
            $token = get_option('application_token');
            $this->twilio = new Client($sid, $token);
        }
    }

    public function send_sms($to, $message_body) {
        $sender_phone = get_option('sender_phone');
        try {
            $message = $this->twilio->messages->create(
                $to,
                [
                    'from' => $sender_phone,
                    'body' => $message_body
                ]
            );
            return $message->sid;
        } catch (Exception $e) {
            error_log('Twilio Error: ' . $e->getMessage());
            return false;
        }
    }

    public function booked_appointment_sms() {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $user_id = wp_get_current_user();
        $customer_phone = get_user_meta($user_id->ID, 'booked_phone', true);
        $post_id = $this->get_first_booked_post_id();
        $name = $user_id->display_name;
        $date = $this->format_date(get_post_meta($post_id, '_appointment_timestamp', true), $date_format);
        $time = $this->format_time(get_post_meta($post_id, '_appointment_timeslot', true), $time_format);
        $message_body = $this->prepare_message_body(get_option('booked_appt_confirmation_email_content'), $name, $date, $time);
        $this->send_sms($customer_phone, $message_body);
    }

    public function appointment_approved_sms() {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $user_id = wp_get_current_user();
        $customer_phone = get_user_meta($user_id->ID, 'booked_phone', true);
        $appt_id = $_POST['appt_id'];
        $name = $user_id->display_name;
        $date = $this->format_date(get_post_meta($appt_id, '_appointment_timestamp', true), $date_format);
        $time = $this->format_time(get_post_meta($appt_id, '_appointment_timeslot', true), $time_format);
        $message_body = $this->prepare_message_body(get_option('booked_approval_email_content'), $name, $date, $time);
        $this->send_sms($customer_phone, $message_body);
    }

    public function appointment_cancelled_sms() {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $user_id = wp_get_current_user();
        $customer_phone = get_user_meta($user_id->ID, 'booked_phone', true);
        $appt_id = $_POST['appt_id'];
        $name = $user_id->display_name;
        $date = $this->format_date(get_post_meta($appt_id, '_appointment_timestamp', true), $date_format);
        $time = $this->format_time(get_post_meta($appt_id, '_appointment_timeslot', true), $time_format);
        $message_body = $this->prepare_message_body(get_option('booked_cancellation_email_content'), $name, $date, $time);
        $this->send_sms($customer_phone, $message_body);
    }

    private function get_first_booked_post_id() {
        $booked_appointments = [
            'post_type' => 'booked_appointments',
            'post_status' => 'any',
            'orderby' => 'ID'
        ];
        $get_post = get_posts($booked_appointments);
        return !empty($get_post) ? $get_post[0]->ID : 0;
    }

    private function format_date($timestamp, $format) {
        return date($format, $timestamp);
    }

    private function format_time($appointment_timeslot, $format) {
        $time_slot = explode('-', $appointment_timeslot);
        return date($format, strtotime($time_slot[0]));
    }

    private function prepare_message_body($sms_content, $name, $date, $time) {
        $filter = ['%name%', '%date%', '%time%'];
        $replace = [$name, $date, $time];
        return str_replace($filter, $replace, $sms_content);
    }
}

// Create an instance of SMSHandler
$sms_handler = new SMSHandler();

// Hook into actions
add_action('booked_confirmation_email', [$sms_handler, 'booked_appointment_sms']);
add_action('booked_approved_email', [$sms_handler, 'appointment_approved_sms']);
add_action('booked_appointment_cancelled', [$sms_handler, 'appointment_cancelled_sms']);
