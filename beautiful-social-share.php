<?php
/**
 * Plugin Name: Kiwi Social
 * Plugin URI: https://www.machothemes.com/plugins/kiwi-social-sharing/
 * Description: Really Beautiful & Simple Social Sharing buttons. Also comes pre-package with a social sharing widget which you can use throughout your website.
 * Author: Macho Themes
 * Author URI: https://www.machothemes.com/
 * Version: 1.0.0
 * License: GPLv3
 * Text Domain: kiwi-social-share
 * Domain Path: /languages/
 */


define('KIWI__MINIMUM_WP_VERSION', '4.5.2');
define('KIWI__STRUCTURE_VERSION', '1.0.0');
define('KIWI__PLUGIN_VERSION', '1.0.0');

define('KIWI__PLUGINS_URL', plugin_dir_url(__FILE__) );

require plugin_dir_path(__FILE__) . 'admin/settings-panel.php';

/*****************************************************************
 *                                                                *
 *     JETPACK: Disable crap                                      *
 *                                                                *
 ******************************************************************/
if( !has_filter( 'jetpack_enable_open_graph', '__return_false' ) ) {
    add_filter('jetpack_enable_open_graph', '__return_false'); // this filter usually gets added by Yoast
}



/**
 * Let's run this puppy
 */
$kiwi_settings_panel = kiwi_options_panel::singleton();