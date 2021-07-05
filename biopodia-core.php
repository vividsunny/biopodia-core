<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              #
 * @since             1.0.0
 * @package           Biopodia_Core
 *
 * @wordpress-plugin
 * Plugin Name:       Biopodia Core
 * Plugin URI:        #
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            #
 * Author URI:        #
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       biopodia-core
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('BIOPODIA_CORE_VERSION', '1.0.0');

if (!defined('BIOPODIA_CORE_POST_TYPE')) {
    define('BIOPODIA_CORE_POST_TYPE', 'ww_biop_programs');
}

if (!defined('BIOPODIA_CORE_TEXT_DOMAIN')) {
    define('BIOPODIA_CORE_TEXT_DOMAIN', 'biopodia-core');
}

if (!defined('BIOPODIA_CORE_TAXONOMY')) {
    define('BIOPODIA_CORE_TAXONOMY', 'ww_biop_category');
}

if (!defined('BIOPODIA_CORE_DIR')) {
    define('BIOPODIA_CORE_DIR', dirname(__FILE__)); // plugin dir
}

if (!defined('BIOPODIA_CORE_ADMIN')) {
    define('BIOPODIA_CORE_ADMIN', BIOPODIA_CORE_DIR . '/admin'); // plugin admin dir
}

if (!defined('BIOPODIA_CORE_LEVEL')) {
    define('BIOPODIA_CORE_LEVEL', 'manage_options');
}

if (!defined('BIOPODIA_CORE_URL')) {
    define('BIOPODIA_CORE_URL', plugin_dir_url(__FILE__)); // plugin url
}

if (!defined('BIOPODIA_CORE_META_PREFIX')) {
    define('BIOPODIA_CORE_META_PREFIX', '_ww_biopodia_');
}

if (!defined('BIOPODIA_CORE_UPLOAD')) {
    define('BIOPODIA_CORE_UPLOAD', BIOPODIA_CORE_DIR . '/upload/'); // plugin upload folder for aws
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-biopodia-core-activator.php
 */
function activate_biopodia_core()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-biopodia-core-activator.php';
    Biopodia_Core_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-biopodia-core-deactivator.php
 */
function deactivate_biopodia_core()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-biopodia-core-deactivator.php';
    Biopodia_Core_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_biopodia_core');
register_deactivation_hook(__FILE__, 'deactivate_biopodia_core');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-biopodia-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_biopodia_core()
{

    $plugin = new Biopodia_Core();
    $plugin->run();

}
run_biopodia_core();
