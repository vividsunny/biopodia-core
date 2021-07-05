<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Ww_Biopodia_Core_Model
{

    public function __construct()
    {

    }

    /**
     * Get Coupons Data
     *
     * Handles get all coupons from database
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_get_programs($args = array())
    {

        $prefix = BIOPODIA_CORE_META_PREFIX;

        $query_args = array('post_type' => BIOPODIA_CORE_POST_TYPE, 'post_status' => 'publish');

        //Meta query argumnts
        $metaquery1 = array();

        //if search is called then retrive searching data
        if (isset($args['search'])) {
            $query_args['s'] = $args['search'];
        }

        //if sorting is called then retrived sorted data
        if (isset($args['meta_query'])) {
            $query_args['meta_query'] = $args['meta_query'];
        }

        //get payment status wise records
        if (isset($args['clt_status'])) {

            $metaquery1['key']     = $prefix . 'program_status';
            $metaquery1['value']   = $args['clt_status'];
            $metaquery1['compare'] = '=';

            $query_args['meta_query'][] = $metaquery1;
        }

        //fire query in to table for retriving data
        $result = new WP_Query($query_args);

        //retrived data is in object format so assign that data to array for listing
        $postslist = $this->biopodia_core_object_to_array($result->posts);

        return $postslist;
    }

    /**
     * Convert Object To Array
     *
     * Converting Object Type Data To Array Type
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_object_to_array($result)
    {
        $array = array();
        foreach ($result as $key => $value) {
            if (is_object($value)) {
                $array[$key] = $this->biopodia_core_object_to_array($value);
            } else {
                $array[$key] = $value;
            }

        }

        return $array;
    }

    /**
     * Escape Tags & Slashes
     *
     * Handles escapping the slashes and tags
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_escape_attr($data)
    {
        return esc_attr(stripslashes($data));
    }

    /**
     * Stripslashes
     *
     * It will strip slashes from the content
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_escape_slashes_deep($data = array(), $flag = false, $limited = false)
    {

        if ($flag != true) {

            $data = $this->biopodia_core_nohtml_kses($data);

        } else {

            if ($limited == true) {
                $data = wp_kses_post($data);
            }

        }
        $data = stripslashes_deep($data);
        return $data;
    }

    /**
     * Strip Html Tags
     *
     * It will sanitize text input (strip html tags, and escape characters)
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_nohtml_kses($data = array())
    {

        if (is_array($data)) {

            $data = array_map(array($this, 'biopodia_core_nohtml_kses'), $data);

        } elseif (is_string($data)) {

            $data = wp_filter_nohtml_kses($data);
        }

        return $data;
    }
    /**
     * Bulk Deletion
     *
     * Does handle deleting category from the
     * database table.
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_bulk_delete($args = array())
    {

        global $wpdb;

        if (isset($args['clt_id']) && !empty($args['clt_id'])) {

            wp_delete_post($args['clt_id']);

        }
    }
    /**
     * Return Text from value of status
     *
     * @packageCategory List Table
     * @since 1.0.0
     */
    public function biopodia_core_text_from_value_status($value)
    {

        switch ($value) {

            case '0':return __('Pending', BIOPODIA_CORE_TEXT_DOMAIN);
            case '1':return __('Approved', BIOPODIA_CORE_TEXT_DOMAIN);
            case '2':return __('Cancelled', BIOPODIA_CORE_TEXT_DOMAIN);
            default:return __('Pending', BIOPODIA_CORE_TEXT_DOMAIN);
        }
    }

}
