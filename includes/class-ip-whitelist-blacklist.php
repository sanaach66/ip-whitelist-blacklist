<?php
class IP_Whitelist_Blacklist {
    protected $plugin_name;
    protected $version;
    
    public function __construct() {
        $this->plugin_name = 'ip-whitelist-blacklist';
        $this->version = '1.0.0';
    }
    
    public function get_plugin_name() {
        return $this->plugin_name;
    }
    
    public function get_version() {
        return $this->version;
    }
    
    public function check_ip_access() {
        // Skip for admin-ajax.php and admin-post.php
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        
        // Skip for admin area (except login)
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }
        
        // Only process login and registration pages
        $is_login_page = in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
        if (!$is_login_page) {
            return;
        }
        
        $ip = $this->get_client_ip();
        $mode = get_option('ipwb_mode', 'whitelist');
        $whitelist = get_option('ipwb_whitelist', array());
        $blacklist = get_option('ipwb_blacklist', array());
        
        // Log the access attempt
        $this->log_access($ip, 'attempt');
        
        // Check blacklist first
        if (in_array($ip, $blacklist)) {
            $this->log_access($ip, 'blocked');
            $this->block_access();
            return;
        }
        
        // If in whitelist mode, check if IP is whitelisted
        if ($mode === 'whitelist' && !in_array($ip, $whitelist)) {
            $this->log_access($ip, 'blocked');
            $this->block_access();
            return;
        }
    }
    
    private function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // Handle multiple IPs in X-Forwarded-For
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
    
    private function block_access() {
        status_header(403);
        wp_die(
            '<h1>' . __('Access Denied', 'ip-whitelist-blacklist') . '</h1>' .
            '<p>' . __('Your IP address is not authorized to access this page.', 'ip-whitelist-blacklist') . '</p>',
            __('Access Denied', 'ip-whitelist-blacklist'),
            array('response' => 403)
        );
    }
    
    public function log_access($ip, $action, $username = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ip_access_log';
        
        $wpdb->insert(
            $table_name,
            array(
                'ip_address' => $ip,
                'access_time' => current_time('mysql'),
                'action' => $action,
                'username' => $username ?: (is_user_logged_in() ? wp_get_current_user()->user_login : ''),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
    }
    
    public static function add_ip_to_list($ip, $list_type) {
        $option_name = 'ipwb_' . $list_type . 'list';
        $list = get_option($option_name, array());
        
        if (!in_array($ip, $list)) {
            $list[] = $ip;
            update_option($option_name, $list);
            return true;
        }
        
        return false;
    }
    
    public static function remove_ip_from_list($ip, $list_type) {
        $option_name = 'ipwb_' . $list_type . 'list';
        $list = get_option($option_name, array());
        
        if (($key = array_search($ip, $list)) !== false) {
            unset($list[$key]);
            update_option($option_name, array_values($list)); // Re-index array
            return true;
        }
        
        return false;
    }
}
