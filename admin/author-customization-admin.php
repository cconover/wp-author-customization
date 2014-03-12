<?php
/**
 * Admin methods for Author Customization plugin
 * admin/author-customization-admin.php
 **/

/**
 * Plugin admin class
 **/
class cc_author_admin extends cc_author {
	// Class constructor
	public function __construct() {
		/* Hooks and filters */
		add_action( 'admin_menu', array( &$this, 'create_options_menu' ) ); // Add menu entry to Settings menu
		add_action( 'admin_init', array( &$this, 'options_init' ) ); // Initialize plugin options
	}
	
	/**
	 * Plugin options
	 **/
	// Create the menu entry under the Settings menu
	public function create_options_menu() {
		add_options_page(
			self::NAME,							// Page title. This is displayed in the browser title bar.
			self::NAME,							// Menu title. This is displayed in the Settings submenu.
			'manage_options',					// Capability required to access the options page for this plugin
			self::ID,							// Menu slug
			array( &$this, 'options_page' )		// Function to render the options page
		);
	} // End create_options_menu()
	
	// Initialize plugin options
	function options_init() {
		// Register the plugin options call and the sanitation callback
		register_setting(
			$this->prefix . 'options_fields',	// The namespace for plugin options fields. This must match settings_fields() used when rendering the form.
			$this->prefix . 'options',			// The name of the plugin options entry in the database
			array( &$this, 'options_validate' )	// The callback method to validate plugin options
		);
		
		// Settings section for Post/Page options
		add_settings_section(
			'postpage',								// Name of the section
			'Post/Page Settings',					// Title of the section, displayed on the options page
			array( &$this, 'postpage_callback' ),	// Callback method to display plugin options
			self::ID								// Page ID for the options page
		);
		
		// Settings section for admin options
		add_settings_section(
			'admin_options',							// Name of the section
			'Admin Settings',							// Title of the section, displayed on the options page
			array( &$this, 'admin_options_callback' ),	// Callback method to display plugin options
			self::ID									// Page ID for the options page
		);
		
		// Whether to display per-post author information
		add_settings_field(
			'perpost',								// Field ID
			'Use author data from post',			// Field title/label, displayed to the user
			array( &$this, 'perpost_callback' ),	// Callback method to display the option field
			self::ID,								// Page ID for the options page
			'postpage'								// Settings section in which to display the field
		);
		
		// Support for multiple authors on a single post
		add_settings_field(
			'multiple-authors',								// Field ID
			'Enable multiple authors on a single post',		// Field title/label, displayed to the user
			array( &$this, 'multiple_authors_callback' ),	// Callback method to display the option field
			self::ID,										// Page ID for the options page
			'postpage'										// Settings section in which to display the field
		);
		
		// Add rel="nofollow" to links inside an author's biographical info
		add_settings_field(
			'relnofollow',									// Field ID
			'Add rel="nofollow" to links in author bio',	// Field title/label, displayed to the user
			array( &$this, 'relnofollow_callback' ),		// Callback method to display the option field
			self::ID,										// Page ID for the options page
			'postpage'										// Settings section in which to display the field
		);
		
		// Enable WYSIWYG editor for author biographical info
		add_settings_field(
			'wysiwyg',								// Field ID
			'WYSIWYG editor for author bio',		// Field title/label, displayed to the user
			array( &$this, 'wysiwyg_callback' ),	// Callback method to display the option field
			self::ID,								// Page ID for the options page
			'admin_options'							// Settings section in which to display the field
		);
	} // End options_init()
	
	/* Plugin options callbacks */
	// Callback for post/page options section
	function postpage_callback() {
		echo '<p>These options are specific to posts and pages.</p>';
	} // End postpage_callback()
	
	// Callback for admin option section
	function admin_options_callback() {
		echo '<p>These options are for things that happen inside WordPress admin.</p>';
	} // End admin_options_callback()
	
	// Callback for per-post author information option
	function perpost_callback() {
		// Check the status of this option in the database
		if ( isset( $this->options['perpost'] ) ) {
			$checked = 'checked';
		}
		else {
			$checked = '';
		}
	
		echo '<input id="cc_author_postpage[perpost]" name="cc_author_postpage[perpost]" type="checkbox" value="Post" ' . $checked . '>'; // Print the input field to the screen
		echo '<p class="description">Display author information from the post metadata instead of the user database. Useful for keeping author information specific to the time a post was published.</p><p class="description"><strong>Note:</strong> You can toggle this at any time, as this plugin always saves author information to post metadata regardless of this setting.</p>'; // Description of option
	} // End perpost_callback()
	
	// Callback for multiple authors per post
	function multiple_authors_callback() {
		// Check the status of this option in the database
		if ( isset( $this->options['multiple-authors'] ) ) {
			$checked = 'checked';
		}
		else {
			$checked = '';
		}
		
		echo '<input id="cc_author_postpage[multiple-authors]" name="cc_author_postpage[multiple-authors]" type="checkbox" value="Multiple" ' . $checked . '>'; // Print the input field to the screen
		echo '<p class="description">Enable support for multiple authors on a single post or page.</p>'; // Description of option
	} // End multiple_authors_callback()
	
	// Callback for rel="nofollow" option
	function relnofollow_callback() {
		// Check the status of this option in the database
		if ( isset( $this->options['relnofollow'] ) ) {
			$checked = 'checked';
		}
		else {
			$checked = '';
		}
		
		echo '<input id="cc_author_postpage[relnofollow]" name="cc_author_postpage[relnofollow]" type="checkbox" value="Nofollow" ' . $checked . '>'; // Print the input field to the screen
		echo '<p class="description">Add a <a href="https://support.google.com/webmasters/answer/96569?hl=en" target="_blank">rel="nofollow"</a> attribute to any links in an author\'s biographical info when displayed. This prevents search engines from counting those links as part of your rank score. If you\'re unsure what this is, leave it checked.</p>'; // Description of option
	} // End relnofollow_callback()
	
	// Callback for WYSIWYG option
	function wysiwyg_callback() {
		// Check the status of this option in the database
		if ( isset( $this->options['wysiwyg'] ) ) {
			$checked = 'checked';
		}
		else {
			$checked = '';
		}
		
		echo '<input id="cc_author_admin_options[wysiwyg]" name="cc_author_admin_options[wysiwyg]" type="checkbox" value="WYSIWYG" ' . $checked . '>'; // Print the input field to the screen
		echo '<p class="description">Enable a WYSIWYG editor for the author bio field, both in the user profile area and in the post/page meta box.</p>'; // Description of option
	} // End wysiwyg_callback()
	
	// Validate options when submitted
	function options_validate( $input ) {
		// Set local variable for plugin options stored in the database
		$options = $this->options;
		
		// Directly set options that require no validation (such as checkboxes)
		$options['perpost']				= $input['perpost'];
		$options['multiple-authors']	= $input['multiple-authors'];
		$options['relnofollow']			= $input['relnofollow'];
		$options['wysiwyg']				= $input['wysiwyg'];
		
		return $options;
	} // End options_validate()
	
	// Options page
	function options_page() {
		// Make sure the user has permissions to access the plugin options
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( '<p>You do not have sufficient privileges to access this page.' );
		}
		?>
		
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Author Customization</h2>

			<form action="options.php" method="post">
				<?php
				settings_fields( $this->prefix . 'options_fields' );	// Retrieve the fields created for plugin options
				do_settings_sections( self::ID ); 						// Display the section(s) for the options page
				submit_button();										// Form submit button generated by WordPress
				?>
			</form>
		</div>
		
		<?php
	} // End options_page()
	/* End plugin options callbacks */
	/**
	 * End plugin options
	 **/
	
	/**
	 * Plugin activation and deactivation methods
	 **/
	 // Plugin activation
	 public function activate() {
	 	// Check WordPress version for plugin compatibility
	 	if ( version_compare( get_bloginfo( 'version' ), self::VERSION, '<' ) ) {
	 		wp_die( 'Your version of WordPress is too old to use this plugin. Please upgrade to the latest version of WordPress.' );
	 	}
	 	
	 	/*
	 	MOVE TO UPDATE METHOD
	 	Prior to version 0.3.0 plugin options were spread out across a few database entries. From 0.3.0 on they are all in a single entry.
	 	We need to determine whether old plugin settings are present, and if so update the database with the new setup.
	 	*/
		if ( get_option( 'cc_author_postpage' ) ) {
			// If the old options entries are present, we need to retrieve those values and assign them to the new structure
			$postpage = get_option( 'cc_author_postpage' );
			$adminoptions = get_option( 'cc_author_admin_options' );
			
			// Set up the new options structure with old values
			$options = array (
				'perpost'			=>	$postpage['perpost'],			// Save author info to each individual post, rather than pulling from global author data
				'multiple-authors'	=>	$postpage['multiple-authors'],	// Enable support for multiple authors per post/page
				'relnofollow'		=>	$postpage['relnofollow'],		// Add rel="nofollow" to links in bio entries
				'wysiwyg'			=>	$adminoptions['wysiwyg']		// Enable the WYSIWYG editor for author bio fields
			);
			
			// Delete the old options entries from the database
			delete_option( 'cc_author_postpage' );
			delete_option( 'cc_author_admin_options' );
		}
		// If old options are not present, we can proceed to set up our options unchanged
		else {
	 		/* Set options for plugin */
			$options = array (
				'perpost'			=>	'Post',		// Save author info to each individual post, rather than pulling from global author data
				'multiple-authors'	=>	'Multiple',	// Enable support for multiple authors per post/page
				'relnofollow'		=>	'Nofollow',	// Add rel="nofollow" to links in bio entries
				'wysiwyg'			=>	'WYSIWYG'	// Enable the WYSIWYG editor for author bio fields
			);
		}
		add_option( $this->prefix . 'options', $options ); // Save options to database
	} // End activate()
	 
	// Plugin deactivation
	public function deactivate() {
		// Remove the plugin options from the database
		delete_option( $this->prefix . 'options' );
	} // End deactivate()
	/**
	 *End plugin activation and deactivation methods
	 **/
}
/**
 * End cc_author_admin
 **/
?>