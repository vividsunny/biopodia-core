<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Biopodia_Core
 * @subpackage Biopodia_Core/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Biopodia_Core
 * @subpackage Biopodia_Core/includes
 * @author     # <#>
 */
class Biopodia_Core
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Biopodia_Core_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

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
     * Taxonomy of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $taxonomy    Taxonomy of the plugin..
     */
    protected $biopodia_taxonomy;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('BIOPODIA_CORE_VERSION')) {
            $this->version = BIOPODIA_CORE_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        if (defined('BIOPODIA_CORE_POST_TYPE')) {
            $this->biopodia_posttype = BIOPODIA_CORE_POST_TYPE;
        } else {
            $this->biopodia_posttype = 'ww_biop_programs';
        }

        if (defined('BIOPODIA_CORE_TEXT_DOMAIN')) {
            $this->biopodia_text_domain = BIOPODIA_CORE_TEXT_DOMAIN;
        } else {
            $this->biopodia_text_domain = 'biopodia-core';
        }

        if (defined('BIOPODIA_CORE_TAXONOMY')) {
            $this->biopodia_taxonomy = BIOPODIA_CORE_TAXONOMY;
        } else {
            $this->biopodia_taxonomy = 'ww_biop_category';
        }

        $this->plugin_name = 'biopodia-core';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        
        $this->define_biopodia_model_hooks();
        $this->define_biopodia_scripts_hooks();
        $this->define_biopodia_admin_hooks();

        $options = get_option( 'biopodia_core_aws_options' );

        if( isset( $options['access_key'] ) ) {
            $access_key = $options['access_key'];
        }

        if( isset( $options['secret_key'] ) ) {
            $secret_key = $options['secret_key'];
        }
        
        if(!empty( $access_key ) && !empty( $secret_key )){
            $this->define_biopodia_aws( $access_key, $secret_key );
        }else{
            add_action( 'admin_notices',array( $this, 'sample_admin_notice__error' ) );
        }
        

    }

    
    

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Biopodia_Core_Loader. Orchestrates the hooks of the plugin.
     * - Biopodia_Core_i18n. Defines internationalization functionality.
     * - Biopodia_Core_Admin. Defines all hooks for the admin area.
     * - Biopodia_Core_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-biopodia-core-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-biopodia-core-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-biopodia-core-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-biopodia-core-public.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-biopodia-core-model.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-biopodia-core-scripts.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-biopodia-core-admin.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-biopodia-aws-upload.php';

        $this->loader = new Biopodia_Core_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Biopodia_Core_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Biopodia_Core_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Biopodia_Core_Admin($this->get_plugin_name(), $this->get_version(), $this->get_posttype_name(), $this->get_text_domain(), $this->get_taxonomy());

        $plugin_settings = new Biopodia_Core_Settings( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action( 'admin_menu', $plugin_settings, 'setup_plugin_options_menu' );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_display_options' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Biopodia_Core_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the ( Biopodia )admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_biopodia_admin_hooks()
    {
        global $biopodia_admin;

        $biopodia_admin = new Ww_Biopodia_Core_Admin_Pages();
        $biopodia_admin->add_hooks();
    }

    /**
     * Register all of the hooks related to model functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_biopodia_model_hooks()
    {
        global $biopodia_model;
        $biopodia_model = new Ww_Biopodia_Core_Model();
    }

    /**
     * Register all of the hooks related to script functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_biopodia_scripts_hooks()
    {
        global $biopodia_scripts;
        $biopodia_scripts = new Ww_Biopodia_Core_Scripts();
        $biopodia_scripts->add_hooks();
    }

    /**
     * Register all of the hooks related to script functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_biopodia_aws($access_key, $secret_key)
    {
        global $biopodia_aws;
        $biopodia_aws = new Biopodia_Core_Aws($access_key, $secret_key);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Biopodia_Core_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retrieve the posttype name of the plugin.
     *
     * @since     1.0.0
     * @return    string    The posttype name of the plugin.
     */
    public function get_posttype_name()
    {
        return $this->biopodia_posttype;
    }

    /**
     * Retrieve text domain of the plugin.
     *
     * @since     1.0.0
     * @return    string    The text domain of the plugin.
     */
    public function get_text_domain()
    {
        return $this->biopodia_text_domain;
    }

    /**
     * Retrieve text domain of the plugin.
     *
     * @since     1.0.0
     * @return    string    The text domain of the plugin.
     */
    public function get_taxonomy()
    {
        return $this->biopodia_taxonomy;
    }

    /**
     * Admin notice
     *
     * @since     1.0.0
     * @return    string    Admin notice.
     */
    public function sample_admin_notice__error() {
        $class      = 'notice notice-error is-dismissible';
        $message    = __( 'Please! Finish aws setup.', BIOPODIA_CORE_TEXT_DOMAIN );
        $setup      = __( 'Setup S3', BIOPODIA_CORE_TEXT_DOMAIN );
        $url        = get_admin_url() . 'admin.php?page=biopodia_options';

        printf( '<div class="%1$s"><p> %2$s &nbsp;<a href="%3$s" target="%4$s">%5$s</a> </p> </div>', esc_attr( $class ), esc_html( $message ), esc_html( $url ), '_blank', esc_html( $setup ) ); 
    }
}
