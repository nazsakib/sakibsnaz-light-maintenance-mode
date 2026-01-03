<?php
/**
 * Plugin Name: Light Maintenance Mode
 * Plugin URI:  https://wordpress.org/plugins/sakibsnaz-light-maintenance-mode/
 * Description: A lightweight plugin to enable a Light Maintenance/coming soon page with one click. Perfect for beginners and small businesses. 
 * Version:     1.5.0
 * Author:      Sakib MD Nazmush
 * Author URI:  https://sakibmdnazmush.vercel.app
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sakibsnaz-light-maintenance-mode
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Check if maintenance mode is enabled.
 */
function slmm_is_enabled()
{
    return (bool) get_option('slmm_enabled', false);
}

/**
 * Check if the current visitor's IP is whitelisted.
 */
function slmm_is_ip_whitelisted()
{
    $whitelist = get_option('slmm_ip_whitelist', '');
    if (empty($whitelist)) {
        return false;
    }

    $whitelisted_ips = array_map('trim', explode(',', $whitelist));
    $user_ip = $_SERVER['REMOTE_ADDR'];

    return in_array($user_ip, $whitelisted_ips, true);
}

/**
 * Display the maintenance page.
 */
function slmm_display_maintenance_mode()
{
    if (slmm_is_enabled() && !current_user_can('manage_options') && !slmm_is_ip_whitelisted() && !is_admin()) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $custom_msg = get_option('slmm_message', __('Site Under Maintenance', 'sakibsnaz-light-maintenance-mode'));
        $bg_color = get_option('slmm_bg_color', '#f0f2f5');

        $socials = array(
            'fb' => array('url' => get_option('slmm_facebook', ''), 'color' => '#1877F2', 'label' => 'f'),
            'x' => array('url' => get_option('slmm_twitter', ''), 'color' => '#000000', 'label' => '𝕏'),
            'ig' => array('url' => get_option('slmm_instagram', ''), 'color' => '#E4405F', 'label' => 'ig'),
        );

        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 3600');

        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html($custom_msg); ?></title>
            <style>
                body {
                    margin: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    background-color:
                        <?php echo esc_attr($bg_color); ?>
                    ;
                    font-family: -apple-system, sans-serif;
                    color: #3c434a;
                    text-align: center;
                }

                .container {
                    max-width: 450px;
                    padding: 40px;
                    background: #fff;
                    border-radius: 12px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                    margin: 20px;
                }

                h1 {
                    font-size: 1.8rem;
                    margin-bottom: 10px;
                    color: #1d2327;
                }

                p {
                    font-size: 1rem;
                    line-height: 1.6;
                    color: #646970;
                }

                .contact {
                    margin-top: 25px;
                    padding-top: 20px;
                    border-top: 1px solid #f0f2f5;
                    font-size: 0.9rem;
                }

                .email {
                    color: #2271b1;
                    text-decoration: none;
                    font-weight: 600;
                }

                .social-links {
                    margin-top: 25px;
                    display: flex;
                    justify-content: center;
                    gap: 15px;
                }

                .social-icon {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    color: #fff;
                    text-decoration: none;
                    font-size: 14px;
                    font-weight: bold;
                    transition: transform 0.2s ease;
                }

                .social-icon:hover {
                    transform: scale(1.1);
                }
            </style>
        </head>

        <body>
            <div class="container">
                <h1><?php echo esc_html($custom_msg); ?></h1>
                <p><?php echo esc_html($site_name); ?>
                    <?php esc_html_e('is currently undergoing maintenance. Please check back soon!', 'sakibsnaz-light-maintenance-mode'); ?>
                </p>

                <?php if ($admin_email): ?>
                    <div class="contact">
                        <p><?php esc_html_e('Need help? Email us:', 'sakibsnaz-light-maintenance-mode'); ?><br>
                            <a href="mailto:<?php echo esc_attr($admin_email); ?>"
                                class="email"><?php echo esc_html($admin_email); ?></a>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="social-links">
                    <?php foreach ($socials as $key => $data):
                        if (!empty($data['url'])): ?>
                            <a href="<?php echo esc_url($data['url']); ?>" target="_blank" class="social-icon"
                                style="background-color: <?php echo esc_attr($data['color']); ?>;"
                                title="<?php echo esc_attr($key); ?>">
                                <?php echo esc_html($data['label']); ?>
                            </a>
                        <?php endif; endforeach; ?>
                </div>
            </div>
        </body>

        </html>
        <?php
        exit;
    }
}
add_action('template_redirect', 'slmm_display_maintenance_mode');

/**
 * Add Dashboard Notice for Admins.
 */
function slmm_admin_notice()
{
    if (slmm_is_enabled() && current_user_can('manage_options')) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><strong><?php esc_html_e('Maintenance Mode is ON.', 'sakibsnaz-light-maintenance-mode'); ?></strong>
                <?php esc_html_e('Visitors are currently seeing your maintenance page.', 'sakibsnaz-light-maintenance-mode'); ?>
                <a
                    href="<?php echo admin_url('options-general.php?page=sakibsnaz-light-maintenance-mode'); ?>"><?php esc_html_e('Configure Settings', 'sakibsnaz-light-maintenance-mode'); ?></a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'slmm_admin_notice');

/**
 * Add status notice to admin bar.
 */
function slmm_admin_bar_notice($wp_admin_bar)
{
    if (!slmm_is_enabled())
        return;
    if (current_user_can('manage_options')) {
        $title = '<span style="color:#ff4d4d; font-weight:bold;">● ' . __('Maintenance Mode ON', 'sakibsnaz-light-maintenance-mode') . '</span>';
    } elseif (slmm_is_ip_whitelisted()) {
        $title = '<span style="color:#4CAF50; font-weight:bold;">● ' . __('Whitelisted Access', 'sakibsnaz-light-maintenance-mode') . '</span>';
    } else {
        return;
    }
    $wp_admin_bar->add_node(array('id' => 'slmm-notice', 'title' => $title, 'href' => current_user_can('manage_options') ? admin_url('options-general.php?page=sakibsnaz-light-maintenance-mode') : '#'));
}
add_action('admin_bar_menu', 'slmm_admin_bar_notice', 999);

/**
 * Load Color Picker scripts.
 */
function slmm_load_color_picker($hook)
{
    if ('settings_page_sakibsnaz-light-maintenance-mode' !== $hook)
        return;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('slmm-script', false, array('wp-color-picker'), false, true);
    wp_add_inline_script('slmm-script', 'jQuery(document).ready(function($){ $(".slmm-color-field").wpColorPicker(); });');
}
add_action('admin_enqueue_scripts', 'slmm_load_color_picker');

/**
 * Settings Page Setup.
 */
function slmm_add_settings_page()
{
    add_options_page(__('Maintenance Mode', 'sakibsnaz-light-maintenance-mode'), __('Maintenance Mode', 'sakibsnaz-light-maintenance-mode'), 'manage_options', 'sakibsnaz-light-maintenance-mode', 'slmm_render_settings_page');
}
add_action('admin_menu', 'slmm_add_settings_page');

function slmm_register_settings()
{
    register_setting('slmm_settings_group', 'slmm_enabled', array('type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean', 'default' => false));
    register_setting('slmm_settings_group', 'slmm_message', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => __('Site Under Maintenance', 'sakibsnaz-light-maintenance-mode')));
    register_setting('slmm_settings_group', 'slmm_bg_color', array('type' => 'string', 'sanitize_callback' => 'sanitize_hex_color', 'default' => '#f0f2f5'));
    register_setting('slmm_settings_group', 'slmm_ip_whitelist', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => ''));
    register_setting('slmm_settings_group', 'slmm_facebook', 'esc_url_raw');
    register_setting('slmm_settings_group', 'slmm_twitter', 'esc_url_raw');
    register_setting('slmm_settings_group', 'slmm_instagram', 'esc_url_raw');
}
add_action('admin_init', 'slmm_register_settings');

function slmm_render_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Light Maintenance Mode', 'sakibsnaz-light-maintenance-mode'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('slmm_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Status', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td><label><input type="checkbox" name="slmm_enabled" value="1" <?php checked(slmm_is_enabled(), true); ?> />
                            <?php esc_html_e('Enable Maintenance Mode', 'sakibsnaz-light-maintenance-mode'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Custom Heading', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td><input type="text" name="slmm_message"
                            value="<?php echo esc_attr(get_option('slmm_message', __('Site Under Maintenance', 'sakibsnaz-light-maintenance-mode'))); ?>"
                            class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Background Color', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td><input type="text" name="slmm_bg_color"
                            value="<?php echo esc_attr(get_option('slmm_bg_color', '#f0f2f5')); ?>"
                            class="slmm-color-field" /></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('IP Whitelist', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td>
                        <input type="text" name="slmm_ip_whitelist"
                            value="<?php echo esc_attr(get_option('slmm_ip_whitelist', '')); ?>" class="regular-text"
                            placeholder="e.g. 127.0.0.1" />
                        <p class="description"><?php esc_html_e('Your current IP:', 'sakibsnaz-light-maintenance-mode'); ?>
                            <?php echo esc_html($_SERVER['REMOTE_ADDR']); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Social Links', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td>
                        <p><input type="url" name="slmm_facebook"
                                value="<?php echo esc_url(get_option('slmm_facebook')); ?>" class="regular-text"
                                placeholder="Facebook URL" /></p>
                        <p><input type="url" name="slmm_twitter" value="<?php echo esc_url(get_option('slmm_twitter')); ?>"
                                class="regular-text" placeholder="X (Twitter) URL" /></p>
                        <p><input type="url" name="slmm_instagram"
                                value="<?php echo esc_url(get_option('slmm_instagram')); ?>" class="regular-text"
                                placeholder="Instagram URL" /></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}