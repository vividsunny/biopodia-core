<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Pages Class
 *
 * Handles all admin functinalities
 *
 * @packageCategory List Table
 * @since 1.0.0
 */
class Ww_Biopodia_Core_Admin_Pages
{

    public $model, $scripts;

    public function __construct()
    {

        global $biopodia_model, $biopodia_scripts;
        $this->model   = $biopodia_model;
        $this->scripts = $biopodia_scripts;

    }

    /**
     * Add Top Level Menu Page
     *
     * Runs when the admin_menu hook fires and adds a new
     * top level admin page and menu item
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_admin_menu()
    {

        //main menu page
        add_menu_page(__('Programs', BIOPODIA_CORE_TEXT_DOMAIN), __('Programs', BIOPODIA_CORE_TEXT_DOMAIN), BIOPODIA_CORE_LEVEL, 'biopodia_programs', '');

        add_submenu_page('biopodia_programs', __('Programs', BIOPODIA_CORE_TEXT_DOMAIN), __('Programs', BIOPODIA_CORE_TEXT_DOMAIN), BIOPODIA_CORE_LEVEL, 'biopodia_programs', array($this, 'biopodia_core_add_submenu_list_table_page'));

        $biopodia_core_admin_add_page = add_submenu_page('biopodia_programs', __('Programs', BIOPODIA_CORE_TEXT_DOMAIN), __('Add New', BIOPODIA_CORE_TEXT_DOMAIN), BIOPODIA_CORE_LEVEL, 'biopodia_core_add_form', array($this, 'biopodia_core_add_submenu_page'));

        add_submenu_page('biopodia_programs', __('Categories', BIOPODIA_CORE_TEXT_DOMAIN), __('Categories', BIOPODIA_CORE_TEXT_DOMAIN), BIOPODIA_CORE_LEVEL, 'edit-tags.php?taxonomy=' . BIOPODIA_CORE_TAXONOMY . '&post_type=' . BIOPODIA_CORE_POST_TYPE);

        //loads javascript needed for add page for toggle metaboxes
        add_action("admin_head-$biopodia_core_admin_add_page", array($this->scripts, 'biopodia_core_add_program_page_load_scripts'));
    }

    /**
     * List of all Product
     *
     * Handles Function to listing all program
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_add_submenu_list_table_page()
    {

        include_once BIOPODIA_CORE_ADMIN . '/forms/biopodia-program-list.php';

    }

    /**
     * Adding Admin Sub Menu Page
     *
     * Handles Function to adding add data form
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_add_submenu_page()
    {

        include_once BIOPODIA_CORE_ADMIN . '/forms/biopodia-add-edit-program.php';

    }

    /**
     * Add action admin init
     *
     * Handles add and edit functionality of program
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_admin_init()
    {
        include_once BIOPODIA_CORE_ADMIN . '/forms/biopodia-program-save.php';
    }

    /**
     * Bulk Delete
     *
     * Handles bulk delete functinalities of program
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_admin_bulk_delete()
    {

        if (((isset($_GET['action']) && $_GET['action'] == 'delete') || (isset($_GET['action2']) && $_GET['action2'] == 'delete')) && isset($_GET['page']) && $_GET['page'] == 'biopodia_programs') {
            //check action and page

            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'biopodia_programs'), admin_url('admin.php'));

            //get bulk program array from $_GET
            $action_on_id = $_GET['category'];

            if (count($action_on_id) > 0) {
                //check there is some checkboxes are checked or not

                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_id as $biopodia_core_id) {

                    //parameters for delete function
                    $args = array(
                        'clt_id' => $biopodia_core_id,
                    );

                    //call delete function from model class to delete records
                    $this->model->biopodia_core_bulk_delete($args);
                }
                $redirect_url = add_query_arg(array('message' => '3'), $redirect_url);

                //if bulk delete is performed successfully then redirect
                wp_redirect($redirect_url);
                exit;
            } else {
                //if there is no checboxes are checked then redirect to listing page
                wp_redirect($redirect_url);
                exit;
            }
        }
    }

    /**
     * Status Change
     *
     * Handles changing status of program
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_admin_change_status()
    {
        $prefix = BIOPODIA_CORE_META_PREFIX;
        if (isset($_GET['clt_status']) && isset($_GET['clt_id']) && !empty($_GET['clt_id'])) {

            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'biopodia_programs', 'message' => '4'), admin_url('admin.php'));

            $postid = $_GET['clt_id'];
            update_post_meta($postid, $prefix . 'program_status', $_GET['clt_status']);

            wp_redirect($redirect_url);
            exit;

        }

    }

    /**
     * Display programs using category
     *
     * Handles to display programs using category
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_category_search($where)
    {

        if (is_admin()) {
            global $wpdb;

            if (isset($_GET['clt_category']) && !empty($_GET['clt_category']) && intval($_GET['clt_category']) != 0) {

                $program_category = intval($_GET['clt_category']);

                $where .= " AND ID IN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id=$program_category )";
            }
        }
        return $where;
    }

    /**
     * Adding Hooks
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function add_hooks()
    {

        //add new admin menu page
        add_action('admin_menu', array($this, 'biopodia_core_admin_menu'));

        //add admin init for saving data
        add_action('admin_init', array($this, 'biopodia_core_admin_init'));

        //add admin init for bult delete functionality
        add_action('admin_init', array($this, 'biopodia_core_admin_bulk_delete'));

        //add admin init for changing status
        add_action('admin_init', array($this, 'biopodia_core_admin_change_status'));

        // Add filter for display programs using category
        add_filter('posts_where', array($this, 'biopodia_core_category_search'));

    }
}
