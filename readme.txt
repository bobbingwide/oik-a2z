=== oik-a2z ===
Contributors: bobbingwide
Donate link: http://www.oik-plugins.com/oik/oik-donate/
Tags: shortcodes, smart, lazy
Requires at least: 4.5
Tested up to: 4.7.2
Stable tag: 0.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Letter taxonomy pagination.

This is what this plugin is supposed to do.
See [github bobbingwide oik-a2z issue 1]

- Automatically sets the value for the identified first letter taxonomies using extendable rules
- Provides a taxonomy page with selectable letters
- Taxonomy can be associated to any post type



== Installation ==
1. Upload the contents of the oik-a2z plugin to the `/wp-content/plugins/oik-a2z' directory
1. Activate the oik-a2z plugin through the 'Plugins' menu in WordPress
1. Define additional post type to pagination taxonomy relationships

== Screenshots ==
1. Displaying the Letter taxonomy for 'l'

== Upgrade Notice ==
= 0.0.4 = 
Now provides the [bw_terms[] shortcode.

= 0.0.3 = 
Now automaticallly sets the letter taxonomy term when a post is saved.

= 0.0.2 =
Now highlights the currently active taxonomy term.

= 0.0.1 =
Now provides 'oik_a2z_display' action for use in themes.

= 0.0.0 =
New plugin, available from oik-plugins and GitHub

== Changelog ==
= 0.0.4 = 
* Added: [bw_terms] shortcode [github bobbingwide oik-a2z issue 3]
* Fixed: Cater for special characters like '&' [github bobbingwide oik-a2z issue 2]

= 0.0.3 = 
* Changed: Automatically set the 'letter' taxonomy term when a post is saved.

= 0.0.2 = 
* Changed: Highlights the currently active term.
 
= 0.0.1 =
* Changed: Implement 'oik_a2z_display' [github bobbingwide oik-a2z issue 1]
* Changed: Improve improve batch facility to set letter terms [github bobbingwide oik-a2z issue 2]
* Tested: With WordPress 4.7.2 and WordPress Multisite

= 0.0.0 =
* Added: New plugin [github bobbingwide oik-a2z issue 1]
* Added: use oikwp oik-a2z.php to populate the "Letter" taxonomy for posts


