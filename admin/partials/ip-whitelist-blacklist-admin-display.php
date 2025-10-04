<div class="wrap ipwb-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="ipwb-settings-section">
        <h2><?php _e('Access Control Mode', 'ip-whitelist-blacklist'); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('ipwb_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Security Mode', 'ip-whitelist-blacklist'); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="ipwb_mode" value="whitelist" <?php checked($mode, 'whitelist'); ?>>
                                <?php _e('Whitelist Mode - Only allow listed IPs to access login', 'ip-whitelist-blacklist'); ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="ipwb_mode" value="blacklist" <?php checked($mode, 'blacklist'); ?>>
                                <?php _e('Blacklist Mode - Block listed IPs from accessing login', 'ip-whitelist-blacklist'); ?>
                            </label>
                        </fieldset>
                        <p class="description">
                            <?php _e('Whitelist mode is more secure but requires you to manually add all allowed IPs.', 'ip-whitelist-blacklist'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ipwb_log_retention_days">
                            <?php _e('Log Retention (days)', 'ip-whitelist-blacklist'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" 
                               id="ipwb_log_retention_days" 
                               name="ipwb_log_retention_days" 
                               value="<?php echo esc_attr($log_retention); ?>" 
                               min="1" 
                               class="small-text">
                        <p class="description">
                            <?php _e('Number of days to keep access logs. Older logs will be automatically deleted.', 'ip-whitelist-blacklist'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'ip-whitelist-blacklist')); ?>
        </form>
    </div>
    
    <div class="ipwb-ip-lists">
        <div class="ipwb-whitelist ipwb-ip-list">
            <h2><?php _e('Whitelisted IPs', 'ip-whitelist-blacklist'); ?></h2>
            <div class="ipwb-ip-form">
                <input type="text" id="ipwb-whitelist-ip" class="regular-text" placeholder="e.g., 192.168.1.1">
                <button type="button" class="button button-primary ipwb-add-ip" data-list-type="white">
                    <?php _e('Add to Whitelist', 'ip-whitelist-blacklist'); ?>
                </button>
            </div>
            <div class="ipwb-ip-table-container">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('IP Address', 'ip-whitelist-blacklist'); ?></th>
                            <th width="100"><?php _e('Actions', 'ip-whitelist-blacklist'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="ipwb-whitelist-ips">
                        <?php foreach ($whitelist as $ip) : ?>
                            <tr>
                                <td><?php echo esc_html($ip); ?></td>
                                <td>
                                    <button type="button" class="button button-small ipwb-remove-ip" data-ip="<?php echo esc_attr($ip); ?>" data-list-type="white">
                                        <?php _e('Remove', 'ip-whitelist-blacklist'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($whitelist)) : ?>
                            <tr>
                                <td colspan="2"><?php _e('No whitelisted IPs yet.', 'ip-whitelist-blacklist'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="ipwb-blacklist ipwb-ip-list">
            <h2><?php _e('Blacklisted IPs', 'ip-whitelist-blacklist'); ?></h2>
            <div class="ipwb-ip-form">
                <input type="text" id="ipwb-blacklist-ip" class="regular-text" placeholder="e.g., 10.0.0.1">
                <button type="button" class="button button-primary ipwb-add-ip" data-list-type="black">
                    <?php _e('Add to Blacklist', 'ip-whitelist-blacklist'); ?>
                </button>
            </div>
            <div class="ipwb-ip-table-container">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('IP Address', 'ip-whitelist-blacklist'); ?></th>
                            <th width="100"><?php _e('Actions', 'ip-whitelist-blacklist'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="ipwb-blacklist-ips">
                        <?php foreach ($blacklist as $ip) : ?>
                            <tr>
                                <td><?php echo esc_html($ip); ?></td>
                                <td>
                                    <button type="button" class="button button-small ipwb-remove-ip" data-ip="<?php echo esc_attr($ip); ?>" data-list-type="black">
                                        <?php _e('Remove', 'ip-whitelist-blacklist'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($blacklist)) : ?>
                            <tr>
                                <td colspan="2"><?php _e('No blacklisted IPs yet.', 'ip-whitelist-blacklist'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="ipwb-access-logs">
        <h2><?php _e('Recent Access Logs', 'ip-whitelist-blacklist'); ?></h2>
        <div class="ipwb-logs-table-container">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="150"><?php _e('Date/Time', 'ip-whitelist-blacklist'); ?></th>
                        <th width="150"><?php _e('IP Address', 'ip-whitelist-blacklist'); ?></th>
                        <th width="100"><?php _e('Action', 'ip-whitelist-blacklist'); ?></th>
                        <th width="120"><?php _e('Username', 'ip-whitelist-blacklist'); ?></th>
                        <th><?php _e('User Agent', 'ip-whitelist-blacklist'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)) : ?>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td><?php echo esc_html($log->access_time); ?></td>
                                <td><?php echo esc_html($log->ip_address); ?></td>
                                <td>
                                    <?php 
                                    $action_labels = array(
                                        'attempt' => __('Attempt', 'ip-whitelist-blacklist'),
                                        'blocked' => __('Blocked', 'ip-whitelist-blacklist'),
                                        'success' => __('Success', 'ip-whitelist-blacklist')
                                    );
                                    echo isset($action_labels[$log->action]) ? esc_html($action_labels[$log->action]) : esc_html($log->action);
                                    ?>
                                </td>
                                <td><?php echo $log->username ? esc_html($log->username) : '—'; ?></td>
                                <td><?php echo esc_html($log->user_agent); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php _e('No access logs found.', 'ip-whitelist-blacklist'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="ipwb-actions">
        <button type="button" class="button button-secondary" id="ipwb-clear-logs">
            <?php _e('Clear All Logs', 'ip-whitelist-blacklist'); ?>
        </button>
        <span class="spinner"></span>
    </div>
</div>
