<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the admin pages
 * as well as the front pages.
 *
 * @packageCategory List Table
 * @since 1.0.0
 */
class Ww_Biopodia_Core_Scripts
{

    public function __construct()
    {

    }

    /**
     * Enqueuing Styles
     *
     * Loads the required stylesheets for displaying
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_admin_print_styles()
    {

        // loads the required styles for the plugin settings page
        // wp_register_style( 'biopodia-admin', BIOPODIA_CORE_URL . 'includes/css/biopodia-admin.css' );
        // wp_enqueue_style( 'biopodia-admin' );
    }

    /**
     * Loading Additional Java Script
     *
     * Loads the JavaScript required for toggling the meta boxes on the theme settings page
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_add_program_page_load_scripts()
    {
        ?>
            <script>
                //<![CDATA[
                jQuery(document).ready( function($) {
                    $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                    postboxes.add_postbox_toggles( 'wp-category-list-table_page_biopodia_core_add_form' );
                });
                //]]>
            </script>
        <?php
}

    /**
     * Load Some Javascript
     *
     * Load JavaScript for handling functionalities for metaboxes
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_page_print_scripts($hook_suffix)
    {

        if ($hook_suffix == 'wp-category-list-table_page_biopodia_core_add_form') {

            // loads the required scripts for the meta boxes
            wp_enqueue_script('common');
            wp_enqueue_script('wp-lists');
            wp_enqueue_script('postbox');
        }
    }

    /**
     *
     * Make menu selected
     *
     * Handle menu selected when custom register taxonomy added
     * to below our custom menu
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function ww_select_category_submenu()
    {

        global $current_screen;

        // Not our post type, exit earlier
        if ($current_screen->post_type != BIOPODIA_CORE_POST_TYPE) {
            return;
        }

        if (isset($_GET['post_type']) && $_GET['post_type'] == BIOPODIA_CORE_POST_TYPE) {

            ?>
            <script type="text/javascript">
                jQuery(document).ready( function($)
                {

                    //removing "wp-has-current-submenu" from all menu
                    $('li.wp-has-submenu').removeClass('wp-has-current-submenu');

                    //removing "wp-menu-open" from all menu
                    $('li.wp-has-submenu').removeClass('wp-menu-open');

                    //adding class "wp-not-current-submenu" to all menu
                    $('li.wp-has-submenu').addClass('wp-not-current-submenu');

                    //remove "wp-not-current-submenu" from current menu selected & current menu's first anchor link
                    $('li#toplevel_page_biopodia_programs, li#toplevel_page_biopodia_programs a:first').removeClass('wp-not-current-submenu');

                    //adding class "wp-has-current-submenu" to our menu to selected & current menu's first anchor link
                    $('li#toplevel_page_biopodia_programs, li#toplevel_page_biopodia_programs a:first').addClass('wp-has-current-submenu');

                    //adding class "wp-menu-open" to our menu to selected & current menu's first anchor link
                    $('li#toplevel_page_biopodia_programs, li#toplevel_page_biopodia_programs a:first').addClass('wp-menu-open');

                    //adding class "current" which link and li you want to selected
                    $('li#toplevel_page_biopodia_programs ul li:last, li#toplevel_page_biopodia_programs ul li a:last').addClass('current');


                });
            </script>
            <?php

        }

    }

    /**
     * Adding Hooks
     *
     * Adding hooks for the styles and scripts.
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function add_hooks()
    {

        //add style sheets for backend
        add_action('admin_enqueue_scripts', array($this, 'biopodia_core_admin_print_styles'));

        //add script for adding some required scripts for metaboxes
        add_action('admin_enqueue_scripts', array($this, 'biopodia_core_page_print_scripts'));

        //add for the make selected menu via javascript
        add_action('admin_head-edit-tags.php', array($this, 'ww_select_category_submenu'));

    }
}
?>