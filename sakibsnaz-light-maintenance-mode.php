<?php
/**
 * Plugin Name: Light Maintenance Mode
 * Plugin URI:  https://wordpress.org/plugins/sakibsnaz-light-maintenance-mode/
 * Description: A lightweight plugin to enable a Light Maintenance/coming soon page with one click. Perfect for beginners and small businesses. 
 * Version:     1.2.0
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
 * Display the maintenance page.
 */
function slmm_display_maintenance_mode()
{
    if (slmm_is_enabled() && !current_user_can('manage_options') && !is_admin()) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $custom_msg = get_option('slmm_message', __('Site Under Maintenance', 'sakibsnaz-light-maintenance-mode'));
        $bg_color = get_option('slmm_bg_color', '#f0f2f5');

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
                    height: 100vh;
                    background-color:
                        <?php echo esc_attr($bg_color); ?>
                    ;
                    font-family: sans-serif;
                    color: #3c434a;
                    text-align: center;
                }

                .container {
                    max-width: 500px;
                    padding: 50px;
                    background: #fff;
                    border-radius: 15px;
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
                    margin: 20px;
                }

                h1 {
                    font-size: 2rem;
                    margin-bottom: 15px;
                    color: #1d2327;
                }

                p {
                    font-size: 1.1rem;
                    line-height: 1.5;
                    color: #646970;
                }

                .contact {
                    margin-top: 25px;
                    padding-top: 20px;
                    border-top: 1px solid #f0f2f5;
                }

                .email {
                    color: #2271b1;
                    text-decoration: none;
                    font-weight: 600;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <h1><?php echo esc_html($custom_msg); ?></h1>
                <p><?php echo esc_html($site_name); ?>
                    <?php esc_html_e('is currently down for maintenance. Please check back soon!', 'sakibsnaz-light-maintenance-mode'); ?>
                </p>
                <?php if ($admin_email): ?>
                    <div class="contact">
                        <p><?php esc_html_e('Questions? Contact us at:', 'sakibsnaz-light-maintenance-mode'); ?><br>
                            <a href="mailto:<?php echo esc_attr($admin_email); ?>"
                                class="email"><?php echo esc_html($admin_email); ?></a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </body>

        </html>
        <?php
        exit;
    }
}
add_action('template_redirect', 'slmm_display_maintenance_mode');

/**
 * Add "Maintenance Mode ON" notice to admin bar.
 */
function slmm_admin_bar_notice($wp_admin_bar)
{
    if (slmm_is_enabled() && current_user_can('manage_options')) {
        $wp_admin_bar->add_node(array(
            'id' => 'slmm-notice',
            'title' => '<span style="color:#ff4d4d; font-weight:bold;">● ' . __('Maintenance Mode ON', 'sakibsnaz-light-maintenance-mode') . '</span>',
            'href' => admin_url('options-general.php?page=sakibsnaz-light-maintenance-mode'),
        ));
    }
}
add_action('admin_bar_menu', 'slmm_admin_bar_notice', 999);

/**
 * Load Color Picker scripts in admin.
 */
function slmm_load_color_picker($hook)
{
    if ('settings_page_sakibsnaz-light-maintenance-mode' !== $hook) {
        return;
    }
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
                    <td>
                        <label><input type="checkbox" name="slmm_enabled" value="1" <?php checked(slmm_is_enabled(), true); ?> />
                            <?php esc_html_e('Enable Maintenance Mode', 'sakibsnaz-light-maintenance-mode'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Custom Message', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td>
                        <input type="text" name="slmm_message"
                            value="<?php echo esc_attr(get_option('slmm_message', __('Site Under Maintenance', 'sakibsnaz-light-maintenance-mode'))); ?>"
                            class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Background Color', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td>
                        <input type="text" name="slmm_bg_color"
                            value="<?php echo esc_attr(get_option('slmm_bg_color', '#f0f2f5')); ?>"
                            class="slmm-color-field" />
                        <p class="description">
                            <?php esc_html_e('Choose a background color for the maintenance page.', 'sakibsnaz-light-maintenance-mode'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}