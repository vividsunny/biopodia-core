<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Post Type
 *
 * Register Custom Post Type for managing registered taxonomy
 *
 * @package Category List Table
 * @since 1.0.0
 */
add_action('init', 'biopodia_core_reg_create_post_type');
function biopodia_core_reg_create_post_type()
{

    $labels = array(
        'name'               => __('Product Categories', BIOPODIA_CORE_TEXT_DOMAIN),
        'singular_name'      => __('Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'add_new'            => __('Add New', BIOPODIA_CORE_TEXT_DOMAIN),
        'add_new_item'       => __('Add New Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'edit_item'          => __('Edit Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'new_item'           => __('New Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'all_items'          => __('All Categories', BIOPODIA_CORE_TEXT_DOMAIN),
        'view_item'          => __('View Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'search_items'       => __('Search Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'not_found'          => __('No categories found', BIOPODIA_CORE_TEXT_DOMAIN),
        'not_found_in_trash' => __('No categories found in Trash', BIOPODIA_CORE_TEXT_DOMAIN),
        'parent_item_colon'  => '',
        'menu_name'          => __('Categories', BIOPODIA_CORE_TEXT_DOMAIN),
    );
    $args = array(
        'labels'          => $labels,
        'public'          => false,
        'query_var'       => false,
        'rewrite'         => true,
        'capability_type' => BIOPODIA_CORE_POST_TYPE,
        'hierarchical'    => false,
        'supports'        => array('title'),
    );

    register_post_type(BIOPODIA_CORE_POST_TYPE, $args);
}

/**
 * Register Category/Taxonomy
 *
 * Register Category like wordpress
 *
 * @packageCategory List Table
 * @since 1.0.0
 */
add_action('init', 'biopodia_core_reg_taxonomy');
function biopodia_core_reg_taxonomy()
{

    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x('Category', 'taxonomy general name', BIOPODIA_CORE_TEXT_DOMAIN),
        'singular_name'     => _x('Category', 'taxonomy singular name', BIOPODIA_CORE_TEXT_DOMAIN),
        'search_items'      => __('Search Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'all_items'         => __('All Categories', BIOPODIA_CORE_TEXT_DOMAIN),
        'parent_item'       => __('Parent Category'),
        'parent_item_colon' => __('Parent Category:', BIOPODIA_CORE_TEXT_DOMAIN),
        'edit_item'         => __('Edit Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'update_item'       => __('Update Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'add_new_item'      => __('Add New Category', BIOPODIA_CORE_TEXT_DOMAIN),
        'new_item_name'     => __('New Category Name', BIOPODIA_CORE_TEXT_DOMAIN),
        'menu_name'         => __('Categories', BIOPODIA_CORE_TEXT_DOMAIN),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => false, //array( 'slug' => WW_CLT_POST_TYPE )
    );
    register_taxonomy(BIOPODIA_CORE_TAXONOMY, array(BIOPODIA_CORE_POST_TYPE), $args);

}
