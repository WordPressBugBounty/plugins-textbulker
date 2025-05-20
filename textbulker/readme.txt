=== TextBulker (IA Redaction) ===
Company: ASF Collector (TextBulker.com)
Contributors: textbulker
Tags: ai, content generation, seo, yoast, rank math
Requires at least: 5.6
Tested up to: 6.8
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Official plugin for TextBulker.com – inject SEO metadata via REST API when publishing AI-generated content.

== Description ==
**TextBulker** is a content and image automation platform powered by AI.
This plugin allows your WordPress site to receive SEO meta fields (title, meta description, focus keyword) during article publishing – ideal for integrations using Yoast SEO or Rank Math.

🔧 Current capabilities:
- Inject SEO metas via REST API on post creation
- Compatible with **Yoast SEO** and **Rank Math**
- Diagnostics page to monitor plugin status
- Fully local (no ping or remote sync)

🚀 Future updates will allow full article pushing, media handling, scheduling, and more via TextBulker.com

== Installation ==
1. Upload the plugin to `/wp-content/plugins/`
2. Activate it from the Plugins screen
3. Go to "Settings > TextBulker" to configure SEO exposure

== Frequently Asked Questions ==
= Does this plugin contact external servers? =
No. It only registers SEO metadata fields locally. The communication with TextBulker.com is managed by your API client.

= Can I use it without Yoast or Rank Math? =
The plugin is useful primarily for automating SEO fields with those plugins. Future features will expand its utility.

= Can I push full articles with this plugin? =
Not yet – but this is planned. Currently, it focuses on enabling SEO meta support.

== Changelog ==
= 1.0.1 =
* Applied feedback from WordPress plugin team: permission_callback, normalized text domain, and improved naming conventions.

= 1.0.0 =
* Initial release
* REST API route for version info
* Meta registration for Yoast and Rank Math
* Admin diagnostics and toggle options
