=== Insert Estimated Reading Time ===
Contributors: nigauri
Donate link:
Tags: insert,content,Estimated Reading Time
Requires at least: 3.5
Tested up to: 5.2.2
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin to insert "Estimated Reading Time" at the beginning of content.

== Description ==

This plugin to insert "Estimated Reading Time" at the beginning of content.

* Can choose "Minutes" or "Minutes and seconds".
* Can output in free format.
* Can switch show/hide in Home, Front page, Post, and Page.
* Support shortcode and tag.
* If you want to translate this plugin, you put '*.po' and '*.mo' in 'languages' directory. Japanese language file is included in this plugin.

== Installation ==

1. Upload this directory to the 'wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Open 'Options' -> 'Insert Estimated Reading Time' menu, choose your options.

== Frequently asked questions ==

= Aren't there any translation files other than Japanese?  =
It does not exist yet. My mother tongue is Japanese and I can't speak any other languages.

== Screenshots ==

1. The options screen.

== Changelog ==

= 1.2 =
* Changed setting of "Hide in ..." to setting of "Show in ...". (existing settings will be automatically inherited)
* Support custom post types. (available only when "content" exists)

= 1.1.3 =
* Fix deprecated error.

= 1.1.1 =
* Changed to snake case from camel case functions name. (ex. estimatedReadingTime -> estimated_reading_time)
* You can continue to use old name of function `<?php estimatedReadingTime(); ?>`.

= 1.1.0 =
* Shortcode support. Add `[estimated_reading_time]` to your post content. Estimated reading time is displayed regardless of the state of checkbox.
* Tag support. Add `<?php estimatedReadingTime(); ?>` to your theme/template. Estimated reading time is displayed regardless of the state of checkbox.

= 1.0.2 =
* Fixed round-up process when you choose "Minutes" in "Time output".

= 1.0.1 =
* Added pot file in languages directory.

= 1.0.0 =
* First release.

== Upgrade notice ==
