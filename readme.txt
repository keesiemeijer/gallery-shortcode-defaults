=== Gallery Defaults ===
Contributors: keesiemeijer
Tags: gallery,gallery shortcode
Requires at least: 3.7
Tested up to: 3.7
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set new default gallery settings in the media uploader.

== Description ==
This plugin sets the attachment link url to "Media File" instead of "Attachment Page". Other defaults like 'Columns', 'Random Order' and 'Size' can be set in your (child) theme's functions.php file with a filter.

Filter Usage

<pre>
add_filter( 'media_uploader_gallery_defaults', 'new_gallery_shortcode_defaults' );
function new_gallery_shortcode_defaults( $defaults ) {
	
	$defaults = array(
		'link'           => 'post', // 'post', 'file', 'none'
		'columns'        => 3, // # number
		'_orderbyRandom' => 'on', // 'on' or empty string ''
		'size'           => 'thumbnail', // 'thumbnail', 'medium', 'large', 'full'
	);

	return $defaults;
}
</pre>

== Installation ==
1. Upload the "gallery-shortcode-defaults" folder to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Set new defaults in your (child) theme's functions.php file with a filter.
