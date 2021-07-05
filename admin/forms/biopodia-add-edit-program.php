<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add/Edit program
 *
 * Handle Add / Edit program
 * 
 * @packageCategory List Table
 * @since 1.0.0
 */

	global $biopodia_model, $errmsg, $error; //make global for error message to showing errors
	
	$model = $biopodia_model;
	$prefix = BIOPODIA_CORE_META_PREFIX;	
	
	//set default value as blank for all fields
	//preventing notice and warnings
	$data = array( 
					'biopodia_core_program_title'		=>	'',
					'biopodia_core_program_desc' 		=>	'',
					'biopodia_core_program_cat' 		=>	'', 
					'biopodia_core_program_avail' 		=>	array(), 
					'biopodia_core_featured_program'	=>	'0',
					'biopodia_core_program_color'		=>	'',
					'biopodia_core_program_status'		=>	'0'
				);
	
	if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['clt_id']) && !empty($_GET['clt_id'])) { //check action & id is set or not
		
		//program page title
		$program_lable = __('Edit Program', BIOPODIA_CORE_TEXT_DOMAIN);
		
		//program page submit button text either it is Add or Update
		$program_btn = __('Update', BIOPODIA_CORE_TEXT_DOMAIN);
		
		//get the program id from url to update the data and get the data of program to fill in editable fields
		$post_id = $_GET['clt_id'];
		
		//get the data from program id
		$getpost = get_post( $post_id );
		if($error != true) { //if error is not occured then fill with database values
			//assign retrived data to current page fields data to show filled in fields
			$data['biopodia_core_program_title'] = $getpost->post_title;
			$data['biopodia_core_program_desc'] = $getpost->post_content;
			//$data['biopodia_core_program_cat'] = get_post_meta($post_id,$prefix.'program_cat',true);
			$data['biopodia_core_program_status'] = get_post_meta($post_id,$prefix.'program_status',true);
			$data['biopodia_core_program_avail'] = get_post_meta($post_id,$prefix.'program_avail',true);
			$data['biopodia_core_featured_program'] = get_post_meta($post_id,$prefix.'featured_program',true);
			$data['biopodia_media'] =  get_post_meta($post_id, $prefix.'media', true);
			$program_terms = wp_get_object_terms($post_id, BIOPODIA_CORE_TAXONOMY);
			$data['biopodia_core_program_cat']	= !empty($program_terms[0]->term_id) ? $program_terms[0]->term_id : '';
			
			/* echo '<pre>';
			print_r($data);
			echo '</pre>'; */

		} else {
			$data = $_POST;
		}		 
		
	} else {
		
		//program page title
		$program_lable = __('Add New Program', BIOPODIA_CORE_TEXT_DOMAIN);
		
		//program page submit button text either it is Add or Update
		$program_btn = __('Save', BIOPODIA_CORE_TEXT_DOMAIN);
		
		//if when error occured then assign $_POST to be field fields with none error fields
		if($_POST) { //check if $_POST is set then set all $_POST values
			$data = $_POST;
		}
	}	
	
	//when program availablity array is null then after submitting then assign with blank array
	if (empty($data['biopodia_core_program_avail'])) { //check if program avail is empty
		$data['biopodia_core_program_avail'] = array();
	}
?>

	<div class="wrap">
		<?php echo screen_icon('options-general'); ?>
	
		<h2> <?php echo __( $program_lable , BIOPODIA_CORE_TEXT_DOMAIN); ?>	
			<a class="add-new-h2" href="admin.php?page=biopodia_programs"><?php echo __('Back to List',BIOPODIA_CORE_TEXT_DOMAIN) ?></a>
		</h2>
	
	<!-- beginning of the program meta box -->

		<div id="biopodia-program" class="post-box-container">
		
			<div class="metabox-holder">	
		
				<div class="meta-box-sortables ui-sortable">
		
					<div id="program" class="postbox">	
		
						<div class="handlediv" title="<?php echo __( 'Click to toggle', BIOPODIA_CORE_TEXT_DOMAIN ) ?>"><br />
						</div>
		
						<!-- program box title -->				
						<h3 class="hndle">				
							<span style="vertical-align: top;"><?php echo __( $program_lable, BIOPODIA_CORE_TEXT_DOMAIN ) ?></span>				
						</h3>
		
						<div class="inside">
					
							<form action="" method="POST" id="biopodia-add-edit-form" enctype="multipart/form-data">
								<input type="hidden" name="page" value="biopodia_core_add_form" />
								
								<div id="biopodia-require-message">
									<strong>(</strong> <span class="biopodia-require">*</span> <strong>)<?php echo __( 'Required fields', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
								</div>
								
								<table class="form-table biopodia-program-box"> 
									<tbody>
														
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Title:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
													<span class="biopodia-require"> * </span>
												</label>
											</th>
											<td><input type="text" id="biopodia_core_program_title" name="biopodia_core_program_title" value="<?php echo $model->biopodia_core_escape_attr($data['biopodia_core_program_title']) ?>" class="large-text"/><br />
												<span class="description"><?php echo __( 'Enter the program title.', BIOPODIA_CORE_TEXT_DOMAIN ) ?></span>
											</td>
											<td class="biopodia-program-error">
												<?php
												if(isset($errmsg['program_title']) && !empty($errmsg['program_title'])) { //check error message for program title
													echo '<div>'.$errmsg['program_title'].'</div>';
												}
												?>
											</td>
										 </tr>										
								
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Description:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
													<span class="biopodia-require"> * </span>
												</label>
											</th>
											<td  width="35%">
												<textarea id="biopodia_core_program_desc" name="biopodia_core_program_desc" rows="4" class="large-text"><?php echo $model->biopodia_core_escape_attr($data['biopodia_core_program_desc']) ?></textarea><br />
												<span class="description"><?php echo __( 'Enter the program description.', BIOPODIA_CORE_TEXT_DOMAIN ) ?></span>
											</td>
											<td class="biopodia-program-error">
												<?php
												if(isset($errmsg['program_desc']) && !empty($errmsg['program_desc'])) { //check error message for program content
													echo '<div>'.$errmsg['program_desc'].'</div>';
												}
												?>
											</td>
										</tr>										
								
										<tr class="hide">
											<th scope="row">
												<label>
													<strong><?php echo __( 'Status:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
												</label>
											</th>
											<td  width="35%">
												<select id="biopodia_core_program_status" name="biopodia_core_program_status">
													<?php
													$type_arr = array(
																	'0'	=>	__( 'Pending', BIOPODIA_CORE_TEXT_DOMAIN ),
																	'1'	=>	__( 'Approved', BIOPODIA_CORE_TEXT_DOMAIN ),
																	'2'	=>	__( 'Cancelled', BIOPODIA_CORE_TEXT_DOMAIN ),
																);
													foreach ($type_arr as $key => $value) {
														echo '<option value="'.$key.'" '.selected($data['biopodia_core_program_status'],$key,false).'>'.$value.'</option>';
													}
													?>
												</select><br />
												<span class="description"><?php echo __( 'Select the program category.', BIOPODIA_CORE_TEXT_DOMAIN ) ?></span>
											</td>
										</tr>										
								
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Category:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
												</label>
											</th>
											<td  width="35%">
												<select id="biopodia_core_program_cat" name="biopodia_core_program_cat">
													<span class="biopodia-require"> * </span>
													<?php
													$catargs = array(	
																		'type'		 	=> 'post',
																		'child_of'	 	=> '0',
																		'parent'     	=> '',
																		'orderby'    	=> 'name',
																		'order'      	=> 'ASC',
																		'hide_empty' 	=> '0',
																		'hierarchical'	=> '1',
																		'exclude'		=> '',
																		'include'       => '',
																		'number'        => '',
																		'taxonomy'      => BIOPODIA_CORE_TAXONOMY,
																		'pad_counts'    => false );
																		
													$type_arr = get_categories($catargs);
													echo '<option value="">'.__('Select Category',BIOPODIA_CORE_TEXT_DOMAIN).'</option>';
													foreach ($type_arr as $cat) {
														echo '<option value="'.$cat->cat_ID.'" '.selected($data['biopodia_core_program_cat'],$cat->cat_ID,false).'>'.$cat->name.'</option>';
													}
													?>
												</select><br />
												<span class="description"><?php echo __( 'Select the program category.', BIOPODIA_CORE_TEXT_DOMAIN ) ?></span>
											</td>
											<td class="biopodia-program-error">
												<?php
												if(isset($errmsg['program_type']) && !empty($errmsg['program_type'])) { //check error message for program content
													echo '<div>'.$errmsg['program_cat'].'</div>';
												}
												?>
											</td>
										</tr>
								
										<tr class="hide">
											<th scope="row">
												<label>
													<strong><?php echo __( 'Availability:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
												</label>
											</th>
											<td class="biopodia-avail-chk" width="35%">
												<input type="checkbox" name="biopodia_core_program_avail[]" value="Client"<?php echo checked(in_array('Client', $data['biopodia_core_program_avail']), true, false) ?>/>
												<label><?php echo __( ' Client', BIOPODIA_CORE_TEXT_DOMAIN ) ?></label>
												<input type="checkbox" name="biopodia_core_program_avail[]" value="Distributor"<?php echo checked(in_array('Distributor', $data['biopodia_core_program_avail']), true, false) ?>/>
												<label><?php echo __( ' Distributor', BIOPODIA_CORE_TEXT_DOMAIN ) ?></label>
												<br />
												<span class="description"><?php echo __( 'Choose the program availability.', BIOPODIA_CORE_TEXT_DOMAIN ) ?></span>
											</td>
										</tr>
								
										<tr class="hide">
											<th scope="row">
												<label>
													<strong><?php echo __( 'Featured program:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
												</label>
											</th>
											<td width="35%">
												<input type="radio" id="biopodia_core_featured_program" name="biopodia_core_featured_program" value="1"<?php echo checked('1',$data['biopodia_core_featured_program'],false) ?>/><?php echo __('Yes',BIOPODIA_CORE_TEXT_DOMAIN) ?>
												<input type="radio" id="biopodia_core_featured_program" name="biopodia_core_featured_program" value="0"<?php echo checked('0',$data['biopodia_core_featured_program'],false) ?>/><?php echo __('No',BIOPODIA_CORE_TEXT_DOMAIN) ?>
												<br /><span class="description"><?php echo __( 'Enter the featured program.', BIOPODIA_CORE_TEXT_DOMAIN ) ?></span>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Featured Graphic:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
													<span class="biopodia-require"> * </span>
												</label>
											</th>
											<td  width="35%">
												<aks-file-upload></aks-file-upload>
											</td>
											
										</tr>

										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Video:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
													<span class="biopodia-require"> * </span>
												</label>
											</th>
											<td  width="35%">
												<video-file-upload></video-file-upload>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Audio:', BIOPODIA_CORE_TEXT_DOMAIN ) ?></strong>
													<span class="biopodia-require"> * </span>
												</label>
											</th>
											<td  width="35%">
												<audio-file-upload></audio-file-upload>
												<p id="uploadAudio" type="json"></p>
											</td>
										</tr>

										<tr>
											<td colspan="3">
												<input type="submit" class="button-primary margin_button" name="biopodia_core_program_save" id="biopodia_core_program_save" value="<?php echo $program_btn ?>" />
											</td>
										</tr>
										
									</tbody>
								</table>
								
							</form>
					
						</div><!-- .inside -->
			
					</div><!-- #program -->
		
				</div><!-- .meta-box-sortables ui-sortable -->
		
			</div><!-- .metabox-holder -->
		
		</div><!-- #wps-program-general -->
			
	<!-- end of the program meta box -->
	
	</div><!-- .wrap -->
	<script>
  jQuery(function () {
    jQuery("aks-file-upload").aksFileUpload({
      fileUpload: "#uploadfile",
	  input : "#uploadImage",
      dragDrop: true,
      maxSize: "90 GB",
      multiple: true,
      maxFile: 50,
      fileType: [
              "jpg",
              "jpeg",
              "png",
              "jpeg",
              "jpg",
              "png",
              "svg",
            ],
      label:"Please featured image."
    });
  });

  jQuery(function () {
    jQuery("video-file-upload").aksFileUpload({
      fileUpload: "#uploadfile",
      input : "#uploadvideo",
      dragDrop: true,
      maxSize: "90 GB",
      multiple: true,
      maxFile: 50,
      fileType: [
        "avi",
        "flv",
        "mov",
        "mp4",
        "mpg",
        "mpeg",
        "vob",
        "wmv",
      ],
      label:"Please upload video."
    });
  });

  jQuery(function () {
    jQuery("audio-file-upload").aksFileUpload({
      fileUpload: "#uploadfile",
      input : "#uploadAudio",
      dragDrop: true,
      maxSize: "90 GB",
      multiple: true,
      maxFile: 50,
      fileType: [
        "mp3",
      ],
      label:"Please upload audio."
    });
  });

</script>