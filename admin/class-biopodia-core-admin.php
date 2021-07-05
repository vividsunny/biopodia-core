<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Biopodia_Core
 * @subpackage Biopodia_Core/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Biopodia_Core
 * @subpackage Biopodia_Core/admin
 * @author     # <#>
 */
class Biopodia_Core_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The register posttype of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $biopodia_posttype    The register posttype of the plugin..
     */
    protected $biopodia_posttype;

    /**
     * The text domain of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $biopodia_text_domain    The text domain of the plugin..
     */
    protected $biopodia_text_domain;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version, $posttype, $text_domain, $taxonomy)
    {

        $this->plugin_name          = $plugin_name;
        $this->version              = $version;
        $this->biopodia_posttype    = $posttype;
        $this->biopodia_text_domain = $text_domain;
        $this->biopodia_taxonomy    = $taxonomy;

        $this->load_dependencies();
    }

    /**
     * Load the required dependencies for the Admin facing functionality.
     *
     * Include the following files that make up the plugin:
     *
     * - Wppb_Demo_Plugin_Admin_Settings. Registers the admin settings and page.
     *
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class/class-biopodia-post-types.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class/class-biopodia-settings.php';

    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Biopodia_Core_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Biopodia_Core_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/biopodia-core-admin.css', array(), $this->version, 'all');

        wp_enqueue_style($this->plugin_name.'-aksFileUpload', plugin_dir_url(__FILE__) . 'css/aksFileUpload.min.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Biopodia_Core_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Biopodia_Core_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/biopodia-core-admin.js', array('jquery'), $this->version, false);

        wp_enqueue_script($this->plugin_name.'-aksFileUpload', plugin_dir_url(__FILE__) . 'js/aksFileUpload.min.js', array('jquery'), $this->version, false);
    }

}
