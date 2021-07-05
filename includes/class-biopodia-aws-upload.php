<?php

/**
 * aws upload class
 */

class Biopodia_Core_Aws 
{
    /**
     * summary
     */
    private $aws;

     /**
     * The register aws access_key.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $biopodia_posttype    The register posttype of the plugin..
     */
    private $access_key;

     /**
     * The register aws secret_key.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $biopodia_posttype    The register posttype of the plugin..
     */
    private $secret_key;

    private $biopodia_dir;
    public function __construct($access_key,$secret_key)
    {
		require plugin_dir_path(dirname(__FILE__)).'libraries/aws/vendor/autoload.php';
		$this->aws = new Aws\S3\S3Client([
			'region'  => 'us-east-2',
			'scheme' =>'http',
			'version' => 'latest',
			'credentials' => [
			    'key'    => $access_key,
			    'secret' => $secret_key,
			]
		]);
    }

    public function aws_region(){

    	$location = $this->aws->getBucketLocation([
		    'Bucket' => 'schoolvite-buyseasons',
		]);

		$region   = empty( $location['LocationConstraint'] ) ? '' : $location['LocationConstraint'];
		return $region;
    }

    public function aws_upload($args){
    	$result = $this->aws->putObject($args);
    }
     public function aws_delete($args){
    	$result = $this->aws->deleteObject($args);

    }
    public function aws_check_image($bucketname , $key){
    	return $this->aws->doesObjectExist($bucketname, $key);
    }
    public function aws_file_upload($Fieldname,$bucketname){
    	
    	$upload_dir = wp_upload_dir();
    	#buyseason-image directory
    	//$this->biopodia_dir = $upload_dir['basedir'].'/buyseason-img/';
    	$this->biopodia_dir = BIOPODIA_CORE_UPLOAD;
		/* echo "biopodia_dir=".$this->biopodia_dir;
		die; */
    	#check file or not
    	if(isset( $_FILES[$Fieldname] ) && $_FILES[$Fieldname] != ''){
    		$temp_file = $_FILES[$Fieldname]['name'];
    		$file_name     = wp_basename( $temp_file );
     		$content_type = wp_check_filetype($temp_file);
     		$type = $content_type['type'];
     		$ext = '.'.$content_type['ext'];
     		$file_name = str_replace($ext, '',$file_name);
     		$upload_path = $this->biopodia_dir.$file_name.$ext;

     		$date_format = 'dHis';
		    $time_ = current_time( 'timestamp' );
		    $time  = date( $date_format, $time_ ) . '/';
		    $bucket_name = $bucketname;
		    $subdir = $upload_dir['subdir'].'/';
		    $s3path = 'wp-content/uploads'.$subdir.$time;
		    #upload image on buyseason folder
		    if (move_uploaded_file($_FILES[$Fieldname]['tmp_name'], $upload_path)){
		    	$orignal_file_path = $upload_path;
		    	$orignal_key = $s3path.$file_name.$ext;
		    	$og_img_arg  = array(
			      	'Bucket' => $bucket_name,
			      	'Key' => $orignal_key,
			      	'SourceFile' => $orignal_file_path,
			      	'ContentType' => $type,

			    );
			    $this->aws_upload($og_img_arg);
			    $s3_path['original'] = $orignal_key;
			    if($ext == '.jpg' || $ext == '.png' || $ext == '.jpeg'){
					$resize_images = $this->aws_resize_image($file_name,$ext,$s3path,$orignal_file_path);
					foreach ($resize_images as $size => $resize_args) {
						$resize_args['Bucket'] = $bucket_name;
						$this->aws_upload($resize_args);
						$s3_path[$size] = $resize_args['Key'];
						unlink($resize_args['SourceFile']);
					}
				}
			    
			    unlink($orignal_file_path);
			    return $s3_path;
		    }
    	
    	}

    }
    public function aws_resize_image($file_name,$ext,$s3path,$orignal_file_path){
    	
    	$dimentions = $this->aws_image_dimentions();
    	
    	foreach( $dimentions as  $sizename => $dimention){
    		
    		$resized_file = $file_name.'-'.$dimention["image_suffix"].$ext;
    		$resize_key = $s3path.$resized_file;
			$filepath = $orignal_file_path;
			$max_w = $dimention["imagewidth"];
			$max_h = $dimention[ "imageheight"];
			$crop = false;
			$suffix = $dimention["image_suffix"] ;
			$dest_path = $this->biopodia_dir.$resized_file;

			if (true !== ($pic_error = @image_resize($filepath, $max_w , $max_h , $crop ,$suffix , $dest_path))) {

                  @image_resize($filepath, $max_w , $max_h , $crop ,$suffix , $dest_path);
                 
                  $resize_arg[$sizename] = array(
                   // 'Bucket' => $bucket_name,
                    'Key' => $resize_key,
                    'SourceFile' => $dest_path,
                    //'ContentType' => $content_type['type'],

                  );
            }

    	}
    	return $resize_arg;
    }

    public function aws_image_dimentions(){

    	$dimentions = array(
		   	'thumbnail' => array(
		        "imagewidth"  => 150, 
		        "imageheight" => 150, 
		        "image_suffix" => "150x150"
		    ),
		    'woocommerce_thumbnail' => array(
		         "imagewidth"  => 300, 
		         "imageheight" => 300, 
		         "image_suffix" => "300x300"
		    ),
		    'woocommerce_single' => array(
		         "imagewidth"  => 600, 
		         "imageheight" => 584, 
		         "image_suffix" => "600x584"
		    )
		); 

		return $dimentions;
    }

    public function aws_file_upload_url($url,$bucket_name){
    	$upload_dir = wp_upload_dir();
    	$this->biopodia_dir = $upload_dir['basedir'].'/buyseason-img/';
    	$image_url        = $url;
	    $image_name       = basename( $image_url );
	    $upload_dir       = wp_upload_dir(); // Set upload folder
	    $image_data       = file_get_contents($image_url); // Get image data
	    $unique_file_name = wp_unique_filename( $this->biopodia_dir, $image_name ); // Generate unique name
	    $filename         = basename( $unique_file_name ); // Create image file name

	    // Check folder permission and define file location
	    if( wp_mkdir_p( $this->biopodia_dir ) ) {
	        $file = $this->biopodia_dir.$filename;
	    } else {
	        $file = $this->biopodia_dir.$filename;
	    }

	    // Create the image  file on the server
	    file_put_contents( $file, $image_data );
	    
	    // Check image file type
	    $wp_filetype = wp_check_filetype( $filename, null );
	    $ext = '.'.$wp_filetype['ext'];
	    $type = $wp_filetype['type'];
	    $filename = str_replace( $ext, '', $filename );

	    $date_format = 'dHis';
	    $time_ = current_time( 'timestamp' );
	    $time = date( $date_format, $time_ ) . '/';
	   	$s3_path = '';
	    $subdir = $upload_dir['subdir'].'/';
	    $s3path = 'wp-content/uploads'.$subdir.$time;
	    $orignal_key = $s3path.$filename.$ext;
    	$og_img_arg = array(
	      	'Bucket' => $bucket_name,
	      	'Key' => $orignal_key,
	      	'SourceFile' => $file,
	      	'ContentType' => $type,

	    );
	    if(file_exists($file)){
	    	$this->aws_upload($og_img_arg);
			$s3_path['original'] = $orignal_key;	
	    }
		
		$resize_images = $this->aws_resize_image($filename,$ext,$s3path,$file);
	    foreach ($resize_images as $size => $resize_args) {
	    	$resize_args['Bucket'] = $bucket_name;
	    	if(file_exists($resize_args['SourceFile'])){
	    		$this->aws_upload($resize_args);
		    	$s3_path[$size] = $resize_args['Key'];
		    	unlink($resize_args['SourceFile']);	
	    	}
	    	
	    }
	    if(file_exists($file)){
		    unlink($file);
		}
	    return $s3_path;
    }

    public function aws_pexels_upload_url($url,$bucket_name,$post_id = ''){
    	$upload_dir = wp_upload_dir();
    	
    	$key_url = $url;
		$url_arr = parse_url($key_url);
		if(isset($url_arr['query'])){
			$query = $url_arr['query'];
			$key_url = str_replace(array($query,'?'), '', $key_url);	
		}else{
			$key_url = $url;
		}
		

    	$image_url        = $url;
	    $image_name       = basename( $key_url );
	    $upload_dir       = wp_upload_dir(); // Set upload folder
	    $image_data       = file_get_contents($image_url); // Get image data
	   

	   	//echo "====>>".$image_name."<<====";
	        
	    // Check image file type
	    $wp_filetype = wp_check_filetype( $image_name, null );
	    $ext = '.'.$wp_filetype['ext'];
	    $type = $wp_filetype['type'];
	    $filename = str_replace( $ext, '', $image_name );

	    $date_format = 'dHis';
	    $time_ = current_time( 'timestamp' );
	    $time = date( $date_format, $time_ ) . '/';
	   	$s3_path = '';
	   	if( !empty( $post_id ) ){
	    	$subdir = $upload_dir['subdir'].'/'.$post_id.'/';	
	    }else{
	    	$subdir = $upload_dir['subdir'].'/';	
	    }
	    $s3path = 'wp-content/uploads'.$subdir.$time;
	    $orignal_key = $s3path.$filename.$ext;
	   // echo "====>".$orignal_key;
    	$og_img_arg = array(
	      	'Bucket' => $bucket_name,
	      	'Key' => $orignal_key,
	      	'Body'   => $image_data,
        	//'ContentType' => $type,

	    );
	   /* debug($og_img_arg);
	    die;*/
	    if(@getimagesize($image_url)){
	    	$this->aws_upload($og_img_arg);
			$s3_path['original'] = $orignal_key;	
	    }
		
		
	    return $s3_path;
    }
    public function aws_upload_direct_file( $Fieldname, $bucketname, $post_id = ''){
    	$upload_dir = wp_upload_dir();
    	#buyseason-image directory
    	$this->biopodia_dir = $upload_dir['basedir'].'/buyseason-img/';

    	#check file or not
    	if(isset( $_FILES[$Fieldname] ) && $_FILES[$Fieldname] != ''){
    		$temp_file = $_FILES[$Fieldname]['name'];
    		$file_name     = wp_basename( $temp_file );
     		$content_type = wp_check_filetype($temp_file);
     		$type = $content_type['type'];
     		$ext = '.'.$content_type['ext'];
     		$file_name = str_replace($ext, '',$file_name);
     		$upload_path = $this->biopodia_dir.$file_name.$ext;

     		$date_format = 'dHis';
		    $time_ = current_time( 'timestamp' );
		    $time = date( $date_format, $time_ ) . '/';
		    $bucket_name = $bucketname;
		    if( !empty( $post_id ) ){
		    	$subdir = $upload_dir['subdir'].'/'.$post_id.'/';	
		    }else{
		    	$subdir = $upload_dir['subdir'].'/';	
		    }
		    
		    $s3path = 'wp-content/uploads'.$subdir.$time;
		    #upload image on buyseason folder
		    if (move_uploaded_file($_FILES[$Fieldname]['tmp_name'], $upload_path)){
		    	$orignal_file_path = $upload_path;
		    	$orignal_key = $s3path.$file_name.$ext;
		    	$og_img_arg = array(
			      	'Bucket' => $bucket_name,
			      	'Key' => $orignal_key,
			      	'SourceFile' => $orignal_file_path,
			      	'ContentType' => $type,

			    );
			    $this->aws_upload($og_img_arg);
			    $s3_path['original'] = $orignal_key;
			    
			    
			    unlink($orignal_file_path);
			    return $s3_path;
		    }
    	
    	}

    }
}

?>