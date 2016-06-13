<?php
/**
 * Plugin Name: Kiwi Social Sharing WordPress Plugin
 * Plugin URI: https://www.machothemes.com/plugins/kiwi-social-sharing/
 * Description: Really beautiful & simple social sharing buttons. Simplicity & speed is key with this social sharing plugin.
 * Author: Macho Themes
 * Author URI: https://www.machothemes.com/
 * Version: 1.0.0
 * License: GPLv3
 * Text Domain: kiwi-social-share
 * Domain Path: /languages/
 */



/**
 * @TODO: - sanitizare a tuturor functiilor de randare
 * @TODO: - rezolva cu ID-ul duplicat al field-urilor
 * 		o idee ar fi ca cheia array-ului, sa fie doar ID-ul field-ului; practic
 * 	'cheie' => array() => array() (fara cheie explicit)
 * @TODO: - re-organizare fisiere si comentarii
 * @TODO: - re-organizare CSS si comentarii
 * @TODO: - iconita de afisat in meniu
 * @TODO: - uninstall.php ( pt. dezinstalarea plugin-ului si stergerea datelor din DB atunci cand este dezinstalat )
 * @TODO: - vezi cum s-ar putea implementa mai bine faza cu default value
 * @TODO: - functie care sa parcurga std-ul si sa adauge valorile alea default in DB
 * 	https://github.com/leemason/NHP-Theme-Options-Framework/blob/master/options/options.php#L187
 * @TODO: - rescris un pic logica de mark-up de la elementele de tip radio img; foloeste <label> in loc de helper :)
 * @TODO: - hook-uri pt. activate/deactivate plugin (aici intra si uninstall.php);
 */


define('KIWI__MINIMUM_WP_VERSION', '4.5.2');
define('KIWI__STRUCTURE_VERSION', '1.0.0');
define('KIWI__PLUGIN_VERSION', '1.0.0');

define('KIWI__PLUGINS_URL', plugin_dir_url(__FILE__) );
define('KIWI__PLUGINS_PATH', plugin_dir_path(__FILE__) );

require KIWI__PLUGINS_PATH . 'inc/class.plugin-utilities.php';
require KIWI__PLUGINS_PATH . 'inc/front-end/class.render-share-bar.php';
require KIWI__PLUGINS_PATH . 'inc/back-end/class.settings-panel.php';

/*****************************************************************
 *                                                                *
 *     JETPACK: Disable crap                                      *
 *                                                                *
 ******************************************************************/
if( !has_filter( 'jetpack_enable_open_graph', '__return_false' ) ) {
    add_filter( 'jetpack_enable_open_graph', '__return_false' ); // this filter usually gets added by Yoast SEO
}