<?php
/**
 * Plugin Name: IP Whitelist & Blacklist
 * Plugin URI: https://example.com/plugins/ip-whitelist-blacklist
 * Description: Control access to your WordPress login by whitelisting or blacklisting IP addresses.
 * Version: 1.0.0
 * Author: Sana Ullah
 * Author URI: https://devsanaa.com/
 * Text Domain: ip-whitelist-blacklist
 * Domain Path: /languages
 * License: GPL v2 or later
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('IPWB_VERSION', '1.0.0');

define('IPWB_PLUGIN_FILE', __FILE__);
define('IPWB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IPWB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once IPWB_PLUGIN_DIR . 'includes/class-ip-whitelist-blacklist.php';
require_once IPWB_PLUGIN_DIR . 'includes/class-ip-whitelist-blacklist-admin.php';

/**
 * Initialize the plugin
 */
function ipwb_init() {
    // Initialize the main plugin class
    $plugin = new IP_Whitelist_Blacklist();
    
    // Initialize the admin class
    $plugin_admin = new IP_Whitelist_Blacklist_Admin(
        $plugin->get_plugin_name(),
        $plugin->get_version()
    );
    
    // Add action hooks
    add_action('init', array($plugin, 'check_ip_access'));
    
    // Add admin menu
    add_action('admin_menu', array($plugin_admin, 'add_plugin_admin_menu'));
    
    // Register settings
    add_action('admin_init', array($plugin_admin, 'register_settings'));
    
    // Add action links in the plugins list
    add_filter('plugin_action_links_' . plugin_basename(IPWB_PLUGIN_FILE), 
        array($plugin_admin, 'add_action_links'));
    
    // Enqueue admin scripts and styles
    add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles'));
    add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
    
    // Add AJAX handlers
    add_action('wp_ajax_ipwb_add_ip', array($plugin_admin, 'ajax_add_ip'));
    add_action('wp_ajax_ipwb_remove_ip', array($plugin_admin, 'ajax_remove_ip'));
    add_action('wp_ajax_ipwb_clear_logs', array($plugin_admin, 'ajax_clear_logs'));
}
add_action('plugins_loaded', 'ipwb_init');

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'ipwb_activate');
register_deactivation_hook(__FILE__, 'ipwb_deactivate');

function ipwb_activate() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ip_access_log';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip_address varchar(45) NOT NULL,
        access_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        action varchar(20) NOT NULL,
        username varchar(60),
        user_agent text,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Add default options
    add_option('ipwb_mode', 'whitelist'); // 'whitelist' or 'blacklist'
    add_option('ipwb_whitelist', array());
    add_option('ipwb_blacklist', array());
    add_option('ipwb_log_retention_days', 30);
}

function ipwb_deactivate() {
    // Clean up if needed
}
