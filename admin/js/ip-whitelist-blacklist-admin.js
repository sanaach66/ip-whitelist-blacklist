jQuery(document).ready(function($) {
    // Add IP to list
    $(document).on('click', '.ipwb-add-ip', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var listType = $button.data('list-type');
        var $input = $('#ipwb-' + listType + 'list-ip');
        var ip = $input.val().trim();
        
        if (!ip) {
            alert(ipwb_ajax.enter_ip);
            return;
        }
        
        $button.prop('disabled', true).text(ipwb_ajax.adding);
        
        $.ajax({
            url: ipwb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ipwb_add_ip',
                ip: ip,
                list_type: listType,
                nonce: ipwb_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Add the new IP to the table
                    var row = '<tr>' +
                        '<td>' + ip + '</td>' +
                        '<td><button type="button" class="button button-small ipwb-remove-ip" data-ip="' + ip + '" data-list-type="' + listType + '">' + 
                        ipwb_ajax.remove + '</button></td>' +
                        '</tr>';
                    
                    var $table = $('#ipwb-' + listType + 'list-ips');
                    
                    // Remove the "no items" row if it exists
                    $table.find('tr:contains("No IPs")').remove();
                    
                    // Add the new row
                    $table.prepend(row);
                    $input.val('');
                    
                    // Show success notice
                    showNotice(response.data.message, 'success');
                } else {
                    showNotice(response.data, 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotice(ipwb_ajax.error_occurred, 'error');
                console.error(error);
            },
            complete: function() {
                $button.prop('disabled', false).text(listType === 'white' ? ipwb_ajax.add_to_whitelist : ipwb_ajax.add_to_blacklist);
            }
        });
    });
    
    // Remove IP from list
    $(document).on('click', '.ipwb-remove-ip', function() {
        if (!confirm(ipwb_ajax.confirm_remove)) {
            return;
        }
        
        var $button = $(this);
        var ip = $button.data('ip');
        var listType = $button.data('list-type');
        var $row = $button.closest('tr');
        
        $button.prop('disabled', true).text(ipwb_ajax.removing);
        
        $.ajax({
            url: ipwb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ipwb_remove_ip',
                ip: ip,
                list_type: listType,
                nonce: ipwb_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Remove the row
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        
                        // If no more rows, add a "no items" row
                        var $table = $('#ipwb-' + listType + 'list-ips');
                        if ($table.find('tr').length === 0) {
                            $table.html('<tr><td colspan="2">' + ipwb_ajax.no_ips + '</td></tr>');
                        }
                    });
                    
                    showNotice(response.data.message, 'success');
                } else {
                    showNotice(response.data, 'error');
                    $button.prop('disabled', false).text(ipwb_ajax.remove);
                }
            },
            error: function(xhr, status, error) {
                showNotice(ipwb_ajax.error_occurred, 'error');
                console.error(error);
                $button.prop('disabled', false).text(ipwb_ajax.remove);
            }
        });
    });
    
    // Clear all logs
    $('#ipwb-clear-logs').on('click', function() {
        if (!confirm(ipwb_ajax.confirm_clear_logs)) {
            return;
        }
        
        var $button = $(this);
        var $spinner = $button.next('.spinner');
        
        $button.prop('disabled', true);
        $spinner.addClass('is-active');
        
        $.ajax({
            url: ipwb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ipwb_clear_logs',
                nonce: ipwb_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Clear the logs table
                    $('.ipwb-access-logs tbody').html(
                        '<tr><td colspan="5">' + ipwb_ajax.no_logs + '</td></tr>'
                    );
                    
                    showNotice(response.data.message, 'success');
                } else {
                    showNotice(response.data, 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotice(ipwb_ajax.error_occurred, 'error');
                console.error(error);
            },
            complete: function() {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });
    
    // Show a notice message
    function showNotice(message, type) {
        // Remove any existing notices
        $('.notice').remove();
        
        // Create and show the notice
        var notice = $(
            '<div class="notice notice-' + type + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            '<button type="button" class="notice-dismiss">' +
            '<span class="screen-reader-text">' + ipwb_ajax.dismiss + '</span>' +
            '</button>' +
            '</div>'
        );
        
        // Add the notice after the first h1
        $('h1').first().after(notice);
        
        // Make the notice dismissible
        notice.on('click', '.notice-dismiss', function() {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    // Add IP to list
    $('.ipwb-add-ip').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const listType = $button.data('list-type');
        const $input = $(`#ipwb-${listType}list-ip`);
        const ip = $input.val().trim();
        
        if (!ip) {
            alert(ipwb_vars.enter_ip);
            return;
        }
        
        $button.prop('disabled', true);
        
        $.ajax({
            url: ipwb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ipwb_add_ip',
                ip: ip,
                list_type: listType,
                nonce: ipwb_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Add the new IP to the table
                    const row = `
                        <tr>
                            <td>${ip}</td>
                            <td>
                                <button type="button" class="button button-small ipwb-remove-ip" 
                                        data-ip="${ip}" data-list-type="${listType}">
                                    ${ipwb_vars.remove}
                                </button>
                            </td>
                        </tr>
                    `;
                    
                    const $table = $(`#ipwb-${listType}list-ips`);
                    
                    // Remove the "no items" row if it exists
                    $table.find('tr:contains("No IPs")').remove();
                    
                    // Add the new row
                    $table.prepend(row);
                    $input.val('');
                    
                    // Show success notice
                    showNotice(response.data.message, 'success');
                } else {
                    showNotice(response.data, 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotice(ipwb_vars.error_occurred, 'error');
                console.error(error);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // Remove IP from list
    $(document).on('click', '.ipwb-remove-ip', function() {
        if (!confirm(ipwb_vars.confirm_remove)) {
            return;
        }
        
        const $button = $(this);
        const ip = $button.data('ip');
        const listType = $button.data('list-type');
        const $row = $button.closest('tr');
        
        $button.prop('disabled', true);
        
        $.ajax({
            url: ipwb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ipwb_remove_ip',
                ip: ip,
                list_type: listType,
                nonce: ipwb_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Remove the row
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        
                        // If no more rows, add a "no items" row
                        const $table = $(`#ipwb-${listType}list-ips`);
                        if ($table.find('tr').length === 0) {
                            $table.append(`
                                <tr>
                                    <td colspan="2">${ipwb_vars.no_ips}</td>
                                </tr>
                            `);
                        }
                    });
                    
                    showNotice(response.data.message, 'success');
                } else {
                    showNotice(response.data, 'error');
                    $button.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                showNotice(ipwb_vars.error_occurred, 'error');
                console.error(error);
                $button.prop('disabled', false);
            }
        });
    });
    
    // Clear all logs
    $('#ipwb-clear-logs').on('click', function() {
        if (!confirm(ipwb_vars.confirm_clear_logs)) {
            return;
        }
        
        const $button = $(this);
        const $spinner = $button.next('.spinner');
        
        $button.prop('disabled', true);
        $spinner.addClass('is-active');
        
        $.ajax({
            url: ipwb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ipwb_clear_logs',
                nonce: ipwb_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Clear the logs table
                    $('.ipwb-access-logs tbody').html(`
                        <tr>
                            <td colspan="5">${ipwb_vars.no_logs}</td>
                        </tr>
                    `);
                    
                    showNotice(response.data.message, 'success');
                } else {
                    showNotice(response.data, 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotice(ipwb_vars.error_occurred, 'error');
                console.error(error);
            },
            complete: function() {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });
    
    // Show a notice message
    function showNotice(message, type = 'success') {
        // Remove any existing notices
        $('.notice').remove();
        
        // Create and show the notice
        const notice = $(`
            <div class="notice notice-${type} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        `);
        
        // Add the notice after the first h1
        $('h1').first().after(notice);
        
        // Make the notice dismissible
        notice.on('click', '.notice-dismiss', function() {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Enable/disable form fields based on mode
    $('input[name="ipwb_mode"]').on('change', function() {
        $('.ipwb-ip-list').toggleClass('disabled', false);
        if ($(this).val() === 'whitelist') {
            $('.ipwb-blacklist').addClass('disabled');
        } else {
            $('.ipwb-whitelist').addClass('disabled');
        }
    }).trigger('change');
});
