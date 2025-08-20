<?php
/**
 * Plugin Name: Job Board Lite
 * Description: Simple job listings plugin (CPT + meta + shortcodes).
 * Version: 1.0.0
 * Author: Tawfik Habib
 * Text Domain: job-board-lite
 */
if (!defined('ABSPATH')) exit;

define('JBL_PATH', plugin_dir_path(__FILE__));
define('JBL_URL', plugin_dir_url(__FILE__));
define('JBL_VER', '1.0.0');

// Includes
require_once JBL_PATH . 'includes/cpt.php';
require_once JBL_PATH . 'includes/meta.php';
require_once JBL_PATH . 'includes/shortcodes.php';
require_once JBL_PATH . 'includes/rest.php';

// Assets
add_action('wp_enqueue_scripts', function () {
    wp_register_style('jbl-style', JBL_URL . 'assets/css/style.css', [], JBL_VER);
    wp_register_script('jbl-main', JBL_URL . 'assets/js/main.js', ['jquery'], JBL_VER, true);
});

// Activation/Deactivation: flush rewrites
register_activation_hook(__FILE__, function () {
    jbl_register_cpt_and_tax();
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});