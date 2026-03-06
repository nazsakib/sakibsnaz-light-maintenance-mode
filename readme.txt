=== Sakibsnaz Light Maintenance Mode ===
Contributors: sakibsnaz
Donate link: https://sakibmdnazmush-shop.fourthwall.com/
Tags: maintenance mode, coming soon, under construction, speed, minimalist
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The fastest, zero-bloat maintenance mode plugin for WordPress. Lightweight, native, and optimized for Core Web Vitals.

== Description ==

**Stop using 5MB plugins for a 1-page maintenance screen.**

Sakibsnaz Light Maintenance Mode is a super lightweight plugin that lets you enable a maintenance/coming soon page with one click.

Visitors will see a clean message with your site admin email for contact, while admins can still view the full site.

Visit our official landing page: [lightmaintenance.site](http://www.lightmaintenance.site)

### Why choose Light Maintenance Mode?
* **Ultra-Lightweight:** Less than 10KB total footprint.
* **SEO Friendly:** Sends a proper 503 Service Unavailable header to notify search engines that your site is temporarily down for maintenance, preserving your rankings.
* **Native UI:** Familiar WordPress interface—no confusing third-party builders.
* **Zero Dependencies:** No external scripts or fonts loaded.
* **Developer First:** Clean code that won’t conflict with your theme or other plugins.

**Features:**
* Enable/disable maintenance mode with one checkbox.
* Displays "Site Under Maintenance" message.
* Shows your admin email as a clickable contact link.
* Returns proper HTTP 503 status (good for SEO).
* Lightweight and translation-ready.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/sakibsnaz-light-maintenance-mode`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings → Maintenance Mode** to enable it.

== Frequently Asked Questions ==

= Does this plugin affect my SEO? =
No. It sends a 503 HTTP status code, which tells Google that the site is temporarily down. This prevents Google from indexing your "Coming Soon" page as the main content of your site.

= Can I see my site while it's in maintenance mode? =
Yes! Logged-in administrators can browse the site normally to test changes. Everyone else will see the maintenance screen.

= Can I customize the email shown? =
Currently, it uses your site’s admin email from WordPress settings.

= Does this block admins? =
No. Logged-in admins can see the normal site.

= Does it load any external fonts? =
No. To keep it as light as possible, it uses system fonts.

== Screenshots ==

1. Plugin settings page with a simple checkbox.
2. Example of the maintenance mode page.

== Changelog ==

= 1.0.1 =
* Added modern CSS styling to the maintenance page.
* Improved SEO headers (503 status).

= 1.0.0 =
* Initial release with required prefix updates for WordPress.org compliance.

== Upgrade Notice ==

= 1.0.1 =
New modern UI for the maintenance page. Highly recommended.