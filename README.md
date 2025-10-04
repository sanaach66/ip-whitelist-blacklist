# IP Whitelist & Blacklist for WordPress

A powerful WordPress security plugin that allows you to control access to your WordPress login page by whitelisting or blacklisting IP addresses. Track login attempts and manage IP access with ease.

## Features

- **Whitelist Mode**: Allow only specific IP addresses to access the login page
- **Blacklist Mode**: Block specific IP addresses from accessing the login page
- **IP Management**: Easily add or remove IPs from whitelist/blacklist
- **Access Logs**: View detailed logs of login attempts
- **User-Friendly Interface**: Clean and intuitive admin interface
- **Secure**: Built with security best practices in mind

## Installation

1. Upload the `ip-whitelist-blacklist` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'IP Control' in the WordPress admin menu to configure the plugin

## Usage

### Setting Up Access Control

1. Navigate to **IP Control** in your WordPress admin menu
2. Choose your preferred security mode:
   - **Whitelist Mode**: Only allow listed IPs to access the login page
   - **Blacklist Mode**: Block listed IPs from accessing the login page

### Managing IP Addresses

1. To add an IP:
   - Enter the IP address in the appropriate field
   - Click "Add to Whitelist" or "Add to Blacklist"

2. To remove an IP:
   - Click the "Remove" button next to the IP you want to remove
   - Confirm the removal when prompted

### Viewing Access Logs

1. The Access Logs section displays recent login attempts
2. You can see the date, IP address, action type, and user agent
3. Use the "Clear All Logs" button to remove all log entries

## Frequently Asked Questions

### What happens if I lock myself out?

If you accidentally block your own IP address, you can:
1. Access your site via FTP/SFTP
2. Navigate to `/wp-content/plugins/`
3. Rename the `ip-whitelist-blacklist` folder to deactivate the plugin
4. Log in to your WordPress admin
5. Rename the folder back to `ip-whitelist-blacklist`
6. Reactivate the plugin and adjust your settings

### How often are logs cleared?

By default, logs older than 30 days are automatically cleared. You can adjust this in the plugin settings.

## Support

For support, please open an issue on the [GitHub repository](https://github.com/yourusername/ip-whitelist-blacklist) or contact the plugin author.

## Changelog

### 1.0.0
* Initial release

## License

GPL v2 or later
