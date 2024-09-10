<?php
/*
Plugin Name: Quickcal SMS Add-on
Plugin URI: 
Description: This plugin SMS functionality to Quickcal plugin fields..
Version: 1.0.0
Author: Vinny
Author URI: 
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

//check if Quickcal plugin is active
if ( ! in_array( 'quickcal/quickcal.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
return;

define( 'QUICKCAL_ADDON_URL', plugin_dir_url( __FILE__ ) );
define( 'QUICKCAL_ADDON_DIR', plugin_dir_path( __FILE__ ) );

//Require Quickcal plugin main file
require_once QUICKCAL_ADDON_DIR .'../quickcal/quickcal.php';


if(!class_exists('quickcal_sms_addon')) {
	class quickcal_sms_addon {

        public function __construct() {

            add_action( 'admin_menu', array( $this,'booked_addon_topmenu' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'add_plugin_scripts' ) );

            add_action( 'admin_init', array( $this,'addonsms_register_settings' ) );
            
            add_action( 'admin_init', array( $this, 'sms_file' ) );

        }


        //Enqueue scripts
        public function add_plugin_scripts() {
            wp_enqueue_style( 'css-style', QUICKCAL_ADDON_URL .'assets/css/css.css' );

            wp_enqueue_script( 'script', QUICKCAL_ADDON_URL . 'assets/js/js.js', array( 'jquery' ) );

        }

        //Add admin menu
        public function booked_addon_topmenu() {        
            add_submenu_page(
                'booked-appointments',
                'SMS Notifications',
                'SMS Notifications',
                'manage_options',
                'sms-notifications',
                array( $this, 'sms_notifications')
            );
        }

        public function addonsms_register_settings() {
            // Check and save submitted data
            if (isset($_POST['sms_control'])) {
                $sms_enable = $_POST['sms_control'];
                $application_id = isset($_POST['application_id']) ? $_POST['application_id'] : "";
                $application_token = isset($_POST['application_token']) ? $_POST['application_token'] : "";
                $sender_phone = isset($_POST['sender_phone']) ? $_POST['sender_phone'] : "";
        
                // Update or add options
                update_option('sms_control', $sms_enable);
                update_option('application_id', $application_id);
                update_option('application_token', $application_token);
                update_option('sender_phone', $sender_phone);
            }
        
            // Register settings
            register_setting('sms_options_group', 'sms_control');
            register_setting('sms_options_group', 'application_id');
            register_setting('sms_options_group', 'application_token');
            register_setting('sms_options_group', 'sender_phone');
        }
        
        public function sms_notifications(){
            require QUICKCAL_ADDON_DIR . 'admin/sms-notifications.php';
        }

        //Require booked addon SMS functions
        public function sms_file(){
            if ( isset( $_POST['application_id'] ) && isset( $_POST['application_token'] ) ):
                include QUICKCAL_ADDON_DIR . 'includes/sms-functions.php';
            endif;
        }

    }

}

$booked_addon = new quickcal_sms_addon();


