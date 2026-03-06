<?php
/**
 * Plugin Name: Sakibsnaz Light Maintenance Mode
 * Plugin URI:  https://www.lightmaintenance.site/
 * Description: A lightweight plugin to enable a simple maintenance/coming soon page with one click.
 * Version:     1.1.0
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
 * Display maintenance mode page for non-logged-in users.
 */
function slmm_display_maintenance_mode()
{
    if (slmm_is_enabled() && !current_user_can('manage_options') && !is_admin()) {
        $admin_email = get_option('admin_email');
        $headline    = get_option('slmm_headline', __('We’ll Be Right Back!', 'sakibsnaz-light-maintenance-mode'));
        $message     = get_option('slmm_message', __('Our site is currently undergoing scheduled maintenance to improve your experience. Please check back soon.', 'sakibsnaz-light-maintenance-mode'));

        // Fallback to defaults if options are empty strings
        if (empty($headline)) {
            $headline = __('We’ll Be Right Back!', 'sakibsnaz-light-maintenance-mode');
        }
        if (empty($message)) {
            $message = __('Our site is currently undergoing scheduled maintenance to improve your experience. Please check back soon.', 'sakibsnaz-light-maintenance-mode');
        }

        // Use a 503 Service Unavailable header for SEO
        header('HTTP/1.1 503 Service Unavailable');
        header('Retry-After: 3600');

        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html($headline); ?></title>
            <style>
                body {
                    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                    color: #2d3436;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    margin: 0;
                    text-align: center;
                }

                .container {
                    background: white;
                    padding: 3rem;
                    border-radius: 20px;
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                    max-width: 500px;
                    width: 90%;
                }

                .icon {
                    font-size: 60px;
                    margin-bottom: 20px;
                    display: inline-block;
                }

                h1 {
                    font-size: 2rem;
                    margin-bottom: 1rem;
                    color: #0984e3;
                }

                p {
                    font-size: 1.1rem;
                    line-height: 1.6;
                    color: #636e72;
                }

                .contact {
                    margin-top: 2rem;
                    font-size: 0.9rem;
                    padding-top: 1.5rem;
                    border-top: 1px solid #eee;
                }

                .contact a {
                    color: #0984e3;
                    text-decoration: none;
                    font-weight: bold;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <div class="icon">🚧</div>
                <h1><?php echo esc_html($headline); ?></h1>
                <p><?php echo esc_html($message); ?></p>

                <?php if ($admin_email): ?>
                    <div class="contact">
                        <?php esc_html_e('Need to reach us?', 'sakibsnaz-light-maintenance-mode'); ?>
                        <br>
                        <a href="mailto:<?php echo esc_attr($admin_email); ?>"><?php echo esc_html($admin_email); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </body>

        </html>
        <?php
        exit; // Stop everything else from loading
    }
}
add_action('template_redirect', 'slmm_display_maintenance_mode');

/**
 * Add settings page.
 */
function slmm_add_settings_page()
{
    add_options_page(
        __('Maintenance Mode', 'sakibsnaz-light-maintenance-mode'),
        __('Maintenance Mode', 'sakibsnaz-light-maintenance-mode'),
        'manage_options',
        'sakibsnaz-light-maintenance-mode',
        'slmm_render_settings_page'
    );
}
add_action('admin_menu', 'slmm_add_settings_page');

/**
 * Register settings.
 */
function slmm_register_settings()
{
    register_setting('slmm_settings_group', 'slmm_enabled', array(
        'type' => 'boolean',
        'sanitize_callback' => 'rest_sanitize_boolean',
        'default' => false,
    ));

    register_setting('slmm_settings_group', 'slmm_headline', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ));

    register_setting('slmm_settings_group', 'slmm_message', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default' => '',
    ));
}
add_action('admin_init', 'slmm_register_settings');

/**
 * Render settings page.
 */
function slmm_render_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Sakibsnaz Light Maintenance Mode', 'sakibsnaz-light-maintenance-mode'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('slmm_settings_group'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Maintenance Mode', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td>
                        <input type="checkbox" name="slmm_enabled" value="1" <?php checked(slmm_is_enabled(), true); ?> />
                        <p class="description">
                            <?php esc_html_e('Check this box to enable maintenance mode for non-admin visitors.', 'sakibsnaz-light-maintenance-mode'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Custom Headline', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td>
                        <input type="text" name="slmm_headline" value="<?php echo esc_attr(get_option('slmm_headline')); ?>" class="regular-text" placeholder="<?php esc_attr_e('We’ll Be Right Back!', 'sakibsnaz-light-maintenance-mode'); ?>" />
                        <p class="description">
                            <?php esc_html_e('The main heading displayed on the maintenance page.', 'sakibsnaz-light-maintenance-mode'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Custom Message', 'sakibsnaz-light-maintenance-mode'); ?></th>
                    <td>
                        <textarea name="slmm_message" rows="5" cols="50" class="large-text" placeholder="<?php esc_attr_e('Our site is currently undergoing scheduled maintenance to improve your experience. Please check back soon.', 'sakibsnaz-light-maintenance-mode'); ?>"><?php echo esc_textarea(get_option('slmm_message')); ?></textarea>
                        <p class="description">
                            <?php esc_html_e('A detailed message explaining the maintenance to your visitors.', 'sakibsnaz-light-maintenance-mode'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Add "Visit Site" link to plugin action links.
 */
function slmm_add_action_links($links)
{
    $mylinks = array(
        '<a href="https://www.lightmaintenance.site/" target="_blank">' . esc_html__('Visit Site', 'sakibsnaz-light-maintenance-mode') . '</a>',
    );
    return array_merge($links, $mylinks);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'slmm_add_action_links');