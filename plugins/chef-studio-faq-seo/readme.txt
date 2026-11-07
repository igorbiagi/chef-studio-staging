=== FAQ Import & Schema ===
Contributors: codex
Tags: faq, acf, schema, json-ld, seo
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Import GPT-generated FAQs into ACF, render them at the end of posts, and output FAQPage JSON-LD from one official repeater field.

== Description ==

FAQ Import & Schema is a reusable WordPress plugin for editorial FAQ management on classic post edit screens.

Features:

* Registers an ACF field group in PHP for one official FAQ repeater.
* Imports FAQ data from a simple JSON array, FAQPage JSON-LD, or a Q:/A: text format.
* Always uses the repeater as the single source of truth.
* Appends FAQ markup to eligible singular posts through `the_content`.
* Outputs FAQPage JSON-LD from the same FAQ data shown on the page.
* Degrades safely when ACF Pro is unavailable and shows an admin notice.

== Installation ==

1. Copy the plugin folder into `/wp-content/plugins/`.
2. Activate the plugin in WordPress.
3. Make sure ACF Pro is active.
4. Edit a supported post type and use the `Editorial FAQs` field group.
5. Paste FAQ data into `Quick FAQ import`, choose `Append` or `Replace`, and save the post.

== Supported import formats ==

JSON array:

`[{"question":"Question 1","answer":"Answer 1"}]`

FAQPage JSON-LD:

`<script type="application/ld+json">{"@type":"FAQPage","mainEntity":[{"name":"Question 1","acceptedAnswer":{"text":"Answer 1"}}]}</script>`

Q:/A: text:

`Q: Question 1`
`A: Answer 1`

== Filters ==

* `csfaq_enabled_post_types` - array of supported post types. Default: `array( 'post' )`.
* `csfaq_acf_field_group_args` - full ACF field group array before registration.
* `csfaq_enable_frontend_render` - enable or disable automatic FAQ rendering.
* `csfaq_frontend_position` - `after_content`, `before_content`, or `disabled`.
* `csfaq_frontend_filter_priority` - priority for the `the_content` filter.
* `csfaq_frontend_section_title` - section heading text.
* `csfaq_faqs_visible_on_page` - declare FAQs visible when rendering is handled manually.
* `csfaq_enable_schema_output` - enable or disable FAQPage JSON-LD.
* `csfaq_schema_hook` - action hook used for JSON-LD output. Default: `wp_head`.
* `csfaq_schema_priority` - priority used on the schema hook.
* `csfaq_schema_data` - final schema array before encoding.
* `csfaq_schema_json_flags` - JSON encoding flags for `wp_json_encode()`.

== Data retention ==

The plugin does not delete FAQ data on deactivation.

On uninstall, data is preserved by default. To delete plugin data during uninstall, define this constant in `wp-config.php` before deleting the plugin:

`define( 'CSFAQ_DELETE_DATA_ON_UNINSTALL', true );`

== Changelog ==

= 1.4.0 =
* Added GPT import support inside the existing `faq` layout of `layout_flessibile_1`.
* Included flexible-layout FAQs in FAQPage schema generation, including reusable blocks.
* Relaxed CPT discovery to support admin-visible custom post types.

= 1.3.0 =
* Added per-post-type FAQ titles.
* Added per-post-type output modes: content, shortcode, or PHP template.
* Added a shortcode snippet alongside the PHP snippet in the settings page.

= 1.2.0 =
* Added backend selection of supported post types and CPTs for FAQ fields, frontend output and schema.

= 1.1.2 =
* Simplified FAQ schema answers to plain text without HTML tags.

= 1.1.1 =
* Hardened JSON-LD escaping for safer script output.
* Preserved simple paragraph markup in schema answers for better parity with frontend content.

= 1.1.0 =
* Added a backend setting to choose between automatic content output and manual template output.
* Added a copyable theme snippet in the settings page for manual FAQ rendering.
* Kept schema generation aligned with the selected output mode.

= 1.0.2 =
* Fixed append mode so new imports are added to the existing repeater rows instead of replacing them.
* Added helper functions for manual FAQ output in theme templates.
* Translated backend labels, instructions and notices to Italian.

= 1.0.1 =
* Added compatibility for FAQPage JSON-LD imports, including pasted `<script type="application/ld+json">` blocks.

= 1.0.0 =
* Initial release.
