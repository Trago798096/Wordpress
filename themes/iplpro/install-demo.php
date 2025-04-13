<?php
/**
 * Template Name: Install Demo Data
 * 
 * This template allows users to manually install demo data
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Call the demo install function
if (function_exists('iplpro_trigger_demo_install')) {
    iplpro_trigger_demo_install();
    
    // Redirect back to homepage
    wp_redirect(home_url());
    exit;
}