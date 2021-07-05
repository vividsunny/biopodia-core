<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Biopodia_Core
 * @subpackage Biopodia_Core/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Biopodia_Core
 * @subpackage Biopodia_Core/includes
 * @author     # <#>
 */
class Biopodia_Core_i18n
{

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {

        load_plugin_textdomain(
            'biopodia-core',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );

    }

}
