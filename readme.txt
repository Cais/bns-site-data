=== BNS Site Data ===
Contributors: cais
Donate link: http://buynowshop.com
Tags: widget-only, site-statistics
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 0.4
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Display various toggleable site statistics.

== Description ==

Display various site statistics (read: counts) such as: posts, pages, categories, tags, comments, and attachments. Each site statistic can be toggled via a checkbox in the widget option panel.

== Installation ==

Under the Plugins | Add New menu item:

* Using Search: look for "BNS Site Data" and click the install link, or
* Using Upload: locate the archive file from your desktop and click the "Upload Now" button

Read this article for further assistance: http://wpfirstaid.com/2009/12/plugin-installation/

----
= Shortcode: bns_site_data =
Parameters are very similar to the plugin:

* 'posts'       => true,
* 'pages'       => true,
* 'categories'  => true,
* 'tags'        => true,
* 'comments'    => true,
* 'attachments' => true,

NB: Use the shortcode at your own risk!

== Frequently Asked Questions ==

= Can I use this in more than one widget area? =
Yes, this plugin has been made for multi-widget compatibility. Each instance of the widget will display, if wanted, differently than every other instance of the widget.

= Where can I get help with this plugin? =
Please note, support may be available on the WordPress Support forums; but, it may be faster to visit one of the following sites:

- https://github.com/Cais/bns-site-data/issues/
- http://buynowshop.com/plugins/bns-site-data/

= Why does my site not look like the screenshot? =
The screenshot was taken as an example when the plugin is used with the Twenty Ten theme. How the widget displays is strongly affected by the active theme styles.
I would recommend creating and using the 'bns-site-data-custom-style.css' stylesheet to set the style according to your own tastes and preferences.

== Screenshots ==
1. The options panel as it appears in default.
2. The default widget and default shortcode output (using sample data).

== Other Notes ==
* Copyright 2012-2014  Edward Caissie  (email : edward.caissie@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 2,
  as published by the Free Software Foundation.

  You may NOT assume that you can use any other version of the GPL.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

  The license for this software can also likely be found here:
  http://www.gnu.org/licenses/gpl-2.0.html

== Upgrade Notice ==
Please stay current with your WordPress installation, your active theme, and your plugins.

== Changelog ==
= 0.4 =
* Released December 2014
* Added `bns-site-data.pot` file for translators
* Added "in plugin update message"
* Added WordPress version check for 3.6 with exit messages
* Changed `bns-sd` text domain to `bns-site-data`
* Improved i18n implementation on output labels
* Renamed function to `__construct` from `BNS_Site_Data_Widget`
* Updates to code formatting to better meet WordPress Coding Standards

= 0.3.2 =
* Released September 2013
* Added third parameter to `shortcode_atts` for automatic filter creation

= 0.3.1 =
* Release May 2013
* Version number compatibility update
* Removed border and padding in widget areas

= 0.3 =
* Release February 2013
* Added code block termination comments
* Moved all code into class structure
* Renamed `BNS_Site_Data_Scripts_and_Styles` to `scripts_and_styles`

= 0.2 =
* Release November 2012
* Add custom script (end-user supplied) file call

= 0.1.1 =
* Correct error with undefined function
* Correct typos in file headers

= 0.1 =
* Initial release