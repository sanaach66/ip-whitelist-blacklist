<?php
class IP_Whitelist_Blacklist_Admin {
    private $plugin_name;
    private $version;
    
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add action links in the plugins list
        add_filter('plugin_action_links_' . $this->plugin_name . '/ip-whitelist-blacklist.php', array($this, 'add_action_links'));
        
        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Handle AJAX requests
        add_action('wp_ajax_ipwb_add_ip', array($this, 'ajax_add_ip'));
        add_action('wp_ajax_ipwb_remove_ip', array($this, 'ajax_remove_ip'));
        add_action('wp_ajax_ipwb_clear_logs', array($this, 'ajax_clear_logs'));
    }
    
    public function enqueue_styles($hook) {
        // Only load on our plugin page
        if ('toplevel_page_ip-whitelist-blacklist' !== $hook) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name . '-admin',
            plugins_url('admin/css/ip-whitelist-blacklist-admin.css', dirname(__FILE__)),
            array(),
            $this->version,
            'all'
        );
    }
    
    public function enqueue_scripts($hook) {
        // Only load on our plugin page
        if ('toplevel_page_ip-whitelist-blacklist' !== $hook) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            plugins_url('admin/js/ip-whitelist-blacklist-admin.js', dirname(__FILE__)),
            array('jquery'),
            $this->version,
            true
        );
        
        wp_localize_script(
            $this->plugin_name . '-admin',
            'ipwb_ajax',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ipwb_nonce'),
                'enter_ip' => __('Please enter an IP address', 'ip-whitelist-blacklist'),
                'confirm_remove' => __('Are you sure you want to remove this IP?', 'ip-whitelist-blacklist'),
                'confirm_clear_logs' => __('Are you sure you want to clear all logs? This cannot be undone.', 'ip-whitelist-blacklist'),
                'error_occurred' => __('An error occurred. Please try again.', 'ip-whitelist-blacklist'),
                'no_ips' => __('No IPs found.', 'ip-whitelist-blacklist'),
                'no_logs' => __('No logs found.', 'ip-whitelist-blacklist'),
                'dismiss' => __('Dismiss this notice.', 'ip-whitelist-blacklist'),
                'adding' => __('Adding...', 'ip-whitelist-blacklist'),
                'removing' => __('Removing...', 'ip-whitelist-blacklist'),
                'add_to_whitelist' => __('Add to Whitelist', 'ip-whitelist-blacklist'),
                'add_to_blacklist' => __('Add to Blacklist', 'ip-whitelist-blacklist'),
                'remove' => __('Remove', 'ip-whitelist-blacklist')
            )
        );
    }
    
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('IP Access Control', 'ip-whitelist-blacklist'),
            __('IP Control', 'ip-whitelist-blacklist'),
            'manage_options',
            'ip-whitelist-blacklist',
            array($this, 'display_plugin_admin_page'),
            'dashicons-shield',
            100
        );
    }
    
    public function register_settings() {
        register_setting('ipwb_settings', 'ipwb_mode');
        register_setting('ipwb_settings', 'ipwb_log_retention_days', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 30
        ));
    }
    
    public function display_plugin_admin_page() {
        $mode = get_option('ipwb_mode', 'whitelist');
        $whitelist = get_option('ipwb_whitelist', array());
        $blacklist = get_option('ipwb_blacklist', array());
        $log_retention = get_option('ipwb_log_retention_days', 30);
        
        // Get recent access logs
        global $wpdb;
        $table_name = $wpdb->prefix . 'ip_access_log';
        $logs = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY access_time DESC LIMIT 50"
        );
        
        include_once plugin_dir_path(__FILE__) . '../admin/partials/ip-whitelist-blacklist-admin-display.php';
    }
    
    public function add_action_links($links) {
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', 'ip-whitelist-blacklist') . '</a>'
        );
        return array_merge($settings_link, $links);
    }
    
    public function ajax_add_ip() {
        check_ajax_referer('ipwb_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have sufficient permissions.'));
        }
        
        if (!isset($_POST['ip']) || !isset($_POST['list_type'])) {
            wp_send_json_error(__('Missing required parameters.', 'ip-whitelist-blacklist'));
        }
        
        $ip = sanitize_text_field($_POST['ip']);
        $list_type = sanitize_text_field($_POST['list_type']);
        
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            wp_send_json_error(__('Invalid IP address.', 'ip-whitelist-blacklist'));
        }
        
        $result = IP_Whitelist_Blacklist::add_ip_to_list($ip, $list_type);
        
        if ($result) {
            $list_name = ($list_type === 'white') ? 'whitelist' : 'blacklist';
            wp_send_json_success(array(
                'message' => sprintf(__('IP %s added to %s list.', 'ip-whitelist-blacklist'), $ip, $list_name),
                'ip' => $ip,
                'list_type' => $list_type
            ));
        } else {
            $list_name = ($list_type === 'white') ? 'whitelist' : 'blacklist';
            wp_send_json_error(sprintf(__('IP %s is already in the %s list.', 'ip-whitelist-blacklist'), $ip, $list_name));
        }
    }
    
    public function ajax_remove_ip() {
        check_ajax_referer('ipwb_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have sufficient permissions.'));
        }
        
        if (!isset($_POST['ip']) || !isset($_POST['list_type'])) {
            wp_send_json_error(__('Missing required parameters.', 'ip-whitelist-blacklist'));
        }
        
        $ip = sanitize_text_field($_POST['ip']);
        $list_type = sanitize_text_field($_POST['list_type']);
        
        $result = IP_Whitelist_Blacklist::remove_ip_from_list($ip, $list_type);
        
        if ($result) {
            $list_name = ($list_type === 'white') ? 'whitelist' : 'blacklist';
            wp_send_json_success(array(
                'message' => sprintf(__('IP %s removed from %s list.', 'ip-whitelist-blacklist'), $ip, $list_name),
                'ip' => $ip,
                'list_type' => $list_type
            ));
        } else {
            $list_name = ($list_type === 'white') ? 'whitelist' : 'blacklist';
            wp_send_json_error(sprintf(__('IP %s not found in the %s list.', 'ip-whitelist-blacklist'), $ip, $list_name));
        }
    }
}
