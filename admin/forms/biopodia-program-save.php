<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save programs
 *
 * Handle program save and edit programs
 *
 * @packageCategory List Table
 * @since 1.0.0
 */
global $errmsg, $wpdb, $user_ID, $biopodia_model, $error, $biopodia_aws;

$model  = $biopodia_model;
$prefix = BIOPODIA_CORE_META_PREFIX;
// save for program data
if (isset($_POST['biopodia_core_program_save']) && !empty($_POST['biopodia_core_program_save'])) {
    //check submit button click
   
    $error = '';
    
    /* echo '<pre>';
    print_r($_FILES);
    echo '</pre>';
    die; */
   

    if (isset($_POST['biopodia_core_program_title']) && empty($_POST['biopodia_core_program_title'])) {
        //check program title

        $errmsg['program_title'] = __('Please Enter Program title.', BIOPODIA_CORE_TEXT_DOMAIN);
        $error                   = true;
    }
    if (isset($_POST['biopodia_core_program_desc']) && empty($_POST['biopodia_core_program_desc'])) {
        //check program content

        $errmsg['program_desc'] = __('Please Enter Program description.', BIOPODIA_CORE_TEXT_DOMAIN);
        $error                  = true;
    }

    /*if(isset($_POST['biopodia_core_program_cat']) && empty($_POST['biopodia_core_program_cat'])) { //check program category

    $errmsg['program_cat'] = __('Please Select Program Category.',BIOPODIA_CORE_TEXT_DOMAIN);
    $error = true;
    }*/
    if (isset($_POST['biopodia_core_program_avail']) && !empty($_POST['biopodia_core_program_avail'])) {
        //check program availibility

        $biopodia_core_available = implode(',', $_POST['biopodia_core_program_avail']);

    }

    if (isset($_GET['clt_id']) && !empty($_GET['clt_id']) && $error != true) {
        //check no error and program id is set in url

        $postid = $_GET['clt_id'];

        //data needs to update for program
        $update_post = array(
            'ID'           => $postid,
            'post_title'   => $_POST['biopodia_core_program_title'],
            'post_content' => $_POST['biopodia_core_program_desc'],
            'post_status'  => 'publish',
            'post_author'  => $user_ID,
        );

        //update program data
        wp_update_post($model->biopodia_core_escape_slashes_deep($update_post));

        if (isset($_POST['biopodia_core_program_cat']) && !empty($_POST['biopodia_core_program_cat'])) {

            $cat_ids = array($_POST['biopodia_core_program_cat']);
            $cat_ids = array_map('intval', $cat_ids); // to make sure the terms IDs is integers:
            $cat_ids = array_unique($cat_ids);

        } else {
            $cat_ids = null;
        }

        wp_set_object_terms($postid, $cat_ids, BIOPODIA_CORE_TAXONOMY);

        //update_post_meta( $postid, $prefix.'program_cat',$_POST['biopodia_core_program_cat']);
        update_post_meta($postid, $prefix . 'program_avail', isset($_POST['biopodia_core_program_avail']) ? $_POST['biopodia_core_program_avail'] : array());
        update_post_meta($postid, $prefix . 'featured_program', $_POST['biopodia_core_featured_program']);
        update_post_meta($postid, $prefix . 'program_status', $_POST['biopodia_core_program_status']);

        // get redirect url
        $redirect_url = add_query_arg(array('page' => 'biopodia_programs', 'message' => '2'), admin_url('admin.php'));
        wp_redirect($redirect_url);
        exit;

    } else {

        if ($error != true) {
            //check there is no error then insert data in to the table

            // Create post object
            $program_arr = array(
                'post_title'   => $_POST['biopodia_core_program_title'],
                'post_content' => $_POST['biopodia_core_program_desc'],
                'post_status'  => 'publish',
                'post_author'  => $user_ID,
                'post_type'    => BIOPODIA_CORE_POST_TYPE,
            );

            // Insert the post into the database
            $result = wp_insert_post($model->biopodia_core_escape_slashes_deep($program_arr));

            if ($result) {
                //check inserted program id

                //save category
                if (isset($_POST['biopodia_core_program_cat']) && !empty($_POST['biopodia_core_program_cat'])) {

                    $cat_ids = array($_POST['biopodia_core_program_cat']);
                    $cat_ids = array_map('intval', $cat_ids); // to make sure the terms IDs is integers:
                    $cat_ids = array_unique($cat_ids);

                } else {
                    $cat_ids = null;
                }

                wp_set_object_terms($result, $cat_ids, BIOPODIA_CORE_TAXONOMY);

                update_post_meta($result, $prefix . 'program_avail', isset($_POST['biopodia_core_program_avail']) ? $_POST['biopodia_core_program_avail'] : array());
                update_post_meta($result, $prefix . 'featured_program', $_POST['biopodia_core_featured_program']);
                update_post_meta($result, $prefix . 'program_status', $_POST['biopodia_core_program_status']);
                if( !empty( $_FILES ) ){
                    $filenameArr = "";
                    
                    $_FILES = generateFileUploadObject();
                    if( !empty( $_FILES ) ){
                        $awsImage = array();
                        foreach( $_FILES as $fkey => $value ){
                            $imageStorage = $biopodia_aws->aws_file_upload($fkey,'schoolvite-pexels');
                            if(strpos($fkey, 'uploadImage') !== false) {
                                $awsImage['uploadImage'][] = $imageStorage;
                            }
                            if(strpos($fkey, 'uploadvideo') !== false) {
                                $awsImage['uploadvideo'][] = $imageStorage;
                            }
                            if(strpos($fkey, 'uploadAudio') !== false) {
                                $awsImage['uploadAudio'][] = $imageStorage;
                            }
                        }    
                        update_post_meta($result, $prefix .'media', $awsImage);
                    } 
                }
                // get redirect url
                $redirect_url = add_query_arg(array('page' => 'biopodia_programs', 'message' => '1'), admin_url('admin.php'));
                wp_redirect($redirect_url);
                exit;

            }
        }
    }
}


function generateFileUploadObject(){
     if( !empty( $_FILES ) ){
        $fileObject = array();
        $fileCount = 0;
        foreach ($_FILES as $key => $value) {
            if( isset( $value['name'] ) ){
                foreach($value['name'] as $valuekey => $data){
                        if( !empty( $data ) ){
                            //$fileObject[$key][$valuekey] = array(
                            $fileObject[$key.'_'.$valuekey] = array(
                                'name' => $data,
                                'type'  => $value['type'][$valuekey],
                                'tmp_name'  => $value['tmp_name'][$valuekey],
                                'error'  => $value['error'][$valuekey],
                                'size'  => $value['size'][$valuekey],
                            );
                        }
                    
                }
            }
            
            $fileCount++;
        }
    }
   
    if( !empty( $fileObject ) ){
        $_FILES = $fileObject;
    }
    return $_FILES;
   /*  */
}