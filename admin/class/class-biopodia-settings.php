<?php

/**
 * The settings of the plugin.
 *
 * @link       http://devinvinson.com
 * @since      1.0.0
 *
 * @package    Wppb_Demo_Plugin
 * @subpackage Wppb_Demo_Plugin/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Biopodia_Core_Settings {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * This function introduces the theme options into the 'Appearance' menu and into a top-level
	 * 'WPPB Demo' menu.
	 */
	public function setup_plugin_options_menu() {

		//Add the menu to the Plugins set of menu items

		//main menu page
        add_menu_page(
        	__('Biopodia Options', BIOPODIA_CORE_TEXT_DOMAIN),
         	__('Biopodia Options', BIOPODIA_CORE_TEXT_DOMAIN),
          	BIOPODIA_CORE_LEVEL, 
          	'biopodia_options', 
          	array( $this, 'render_settings_page_content')
        );

	}

	/**
	 * Provides default values for the Display Options.
	 *
	 * @return array
	 */
	public function default_display_options() {

		$defaults = array(
			'access_key'		=>	'',
			'secret_key'		=>	'',
		);

		return $defaults;

	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content( $active_tab = '' ) {
		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<h2><?php _e( 'Biopodia Options', BIOPODIA_CORE_TEXT_DOMAIN ); ?></h2>
			<?php settings_errors(); ?>

			<?php if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else {
				$active_tab = 'display_options';
			} // end if/else ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=biopodia_options&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'AWS Setup', BIOPODIA_CORE_TEXT_DOMAIN ); ?></a>
			</h2>

			<form method="post" action="options.php">
				<?php

				if( $active_tab == 'display_options' ) {

					settings_fields( 'biopodia_core_aws_options' );
					do_settings_sections( 'biopodia_core_aws_options' );

				}// end if/else

				submit_button();

				?>
			</form>

		</div><!-- /.wrap -->
	<?php
	}


	/**
	 * This function provides a simple description for the General Options page.
	 *
	 * It's called from the 'wppb-demo_initialize_theme_options' function by being passed as a parameter
	 * in the add_settings_section function.
	 */
	public function general_options_callback() {
		$options = get_option('biopodia_core_aws_options');
		// var_dump($options);
		// echo '<p>' . __( 'Select which areas of content you wish to display.', BIOPODIA_CORE_TEXT_DOMAIN ) . '</p>';
	} // end general_options_callback


	/**
	 * Initializes the theme's display options page by registering the Sections,
	 * Fields, and Settings.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_display_options() {

		// If the theme options don't exist, create them.
		if( false == get_option( 'biopodia_core_aws_options' ) ) {
			$default_array = $this->default_display_options();
			add_option( 'biopodia_core_aws_options', $default_array );
		}


		add_settings_section(
			'general_settings_section',			            
			__( 'AWS Detail', BIOPODIA_CORE_TEXT_DOMAIN ),
			array( $this, 'general_options_callback'),
			'biopodia_core_aws_options'	
		);

		add_settings_field(
			'access_key',
			__( 'Access key', BIOPODIA_CORE_TEXT_DOMAIN ),
			array( $this, 'input_element_access_key_callback'),
			'biopodia_core_aws_options',
			'general_settings_section'
		);

		add_settings_field(
			'secret_key',
			__( 'Secret key', BIOPODIA_CORE_TEXT_DOMAIN ),
			array( $this, 'input_element_secret_key_callback'),
			'biopodia_core_aws_options',
			'general_settings_section'
		);
		

		// Finally, we register the fields with WordPress
		register_setting(
			'biopodia_core_aws_options',
			'biopodia_core_aws_options',
			array( $this, 'validate_input_examples')
		);

	} // end wppb-demo_initialize_theme_options

	public function input_element_access_key_callback() {

		$options = get_option( 'biopodia_core_aws_options' );

		// Render the output
		echo '<input type="text" id="access_key" name="biopodia_core_aws_options[access_key]" value="' . $options['access_key'] . '" />';

	} // end input_element_callback

	public function input_element_secret_key_callback() {

		$options = get_option( 'biopodia_core_aws_options' );

		// Render the output
		echo '<input type="text" id="secret_key" name="biopodia_core_aws_options[secret_key]" value="' . $options['secret_key'] . '" />';

	} // end input_element_callback

	public function validate_input_examples( $input ) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {

				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );

			} // end if

		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_input_examples', $output, $input );

	} // end validate_input_examples

	/**
	 * This will replace the first half of a string with "*" characters.
	 *
	 * @param string $string
	 * @return string
	 */
	function biopodia_obfuscate_string( $string ) {
		$length            = strlen( $string );
		$obfuscated_length = ceil( $length / 2 );
		$string            = str_repeat( '*', $obfuscated_length ) . substr( $string, $obfuscated_length );
		return $string;
	}
}