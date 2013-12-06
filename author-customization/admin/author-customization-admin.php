<?php
/*
Functions for managing the plugin inside wp-admin

Included/Required by:
author-customization.php
*/


/**
 * Create entry in Settings menu
 * A submenu entry titled 'Custom Authors' is shown under Settings
 */
function cc_author_create_menu() {
	add_options_page(
		'Author Customization',				// Page title. This is displayed in the browser title bar.
		'Author Custom',					// Menu title. This is displayed in the Settings submenu.
		'manage_options',					// Capability required to access the options page for this plugin
		'cc-author',						// Menu slug
		'cc_author_options_page'			// Function to render the options page
	);
} // cc_author_create_menu()
add_action( 'admin_menu', 'cc_author_create_menu' ); // Hook menu entry into API
/**
 * End create entry in Settings menu
 */


/**
 * Options page configuration
 * Sections, fields, callbacks and validations
 */
add_action( 'admin_init', 'cc_author_features_init' ); // Hook admin initialization for plugin features

function cc_author_features_init() {
	register_setting( 'cc_author_options_features', 'cc_author_features', 'cc_author_features_validate' ); // Register the settings group and specify validation and database locations
	
	add_settings_section(
		'features',							// Name of the section
		'Features',							// Title of the section, displayed on the options page
		'cc_author_features_callback',		// Callback function for displaying information
		'cc-author'							// Page ID for the options page
	);
	
	add_settings_field(						// Set whether author info is pulled from post meta or global user data
		'perpost',							// Field ID
		'Use author data from post',		// Field title, displayed to the left of the field on the options page
		'cc_author_perpost_callback',		// Callback function to display the field
		'cc-author',						// Page ID for the options page
		'features'							// Settings section in which to display the field
	);
	add_settings_field(						// Set whether author info is pulled from post meta or global user data
		'wysiwyg',							// Field ID
		'WYSIWYG editor for author bio',	// Field title, displayed to the left of the field on the options page
		'cc_author_wysiwyg_callback',		// Callback function to display the field
		'cc-author',						// Page ID for the options page
		'features'							// Settings section in which to display the field
	);
} // cc_author_features_list()

/* Settings section callback */
function cc_author_features_callback() {
	echo '<p>Please select the features you would like to enable.</p>';
} // cc_author_features_callback()

/* Call back for 'perpost' option */
function cc_author_perpost_callback() {
	$features = get_option( 'cc_author_features' ); // Retrieve plugin options from the database
	
	/* Determine whether the box should be checked based on setting in database */
	if ( isset( $features['perpost'] ) ) {
		$checked = 'checked';
	}
	else {
		$checked = '';
	}
	
	echo '<input id="perpost" name="cc_author_features[perpost]" type="checkbox" value="Post" ' . $checked . '>'; // Print the input field to the screen
	echo '<p class="description">If checked, the plugin will retrieve author information from the post metadata instead of the user database. Useful for keeping author information specific to the time a post was published.</p><p class="description"><strong>Note:</strong> You can toggle this at any time, as this plugin always saves author information to post metadata regardless of this setting.</p>'; // Description of option
} // cc_author_perpost_callback()

/* Call back for 'wysiwyg' option */
function cc_author_wysiwyg_callback() {
	$features = get_option( 'cc_author_features' ); // Retrieve plugin options from the database
	
	/* Determine whether the box should be checked based on setting in database */
	if ( isset( $features['wysiwyg'] ) ) {
		$checked = 'checked';
	}
	else {
		$checked = '';
	}
	
	echo '<input id="wysiwyg" name="cc_author_features[wysiwyg]" type="checkbox" value="WYSIWYG" ' . $checked . '>'; // Print the input field to the screen
	echo '<p class="description">Enable a WYSIWYG editor for the author bio field, both in the user profile area and in the post/page meta box.</p>'; // Description of option
} // cc_author_wysiwyg_callback()

/* Validate submitted options */
function cc_author_features_validate( $input ) {
	$features = get_option( 'cc_author_features' ); // Retrieve existing options values from the database
	
	/* Directly set values that don't require validation */
	$features['perpost']		=	$input['perpost'];
	$features['wysiwyg']		=	$input['wysiwyg'];
	
	return $features; // Send values to database
} // cc_author_features_validate()
/**
 * End Options page configuration
 */


/**
 * Options Page
 */
function cc_author_options_page() {
	/* Prevent users with insufficient permissions from accessing settings */
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( '<p>You do not have sufficient permissions to access this page.</p>' );
	}
	?>
	
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Author Customization</h2>

		<form action="options.php" method="post">
			<?php
			settings_fields( 'cc_author_options_features' ); 	// Retrieve the fields created for the options page
			do_settings_sections( 'cc-author' ); 				// Display the section(s) for the options page
			submit_button();									// Form submit button generated by WordPress
			?>
		</form>
	</div>
	
	<?php	
} // cc_author_options_page()
/**
 * End Options Page
 */


/* If editing post, include the functions for use while editing a post */
if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/edit.php' ) ) {
	require_once( dirname( __FILE__ ) . '/includes/edit-post.php' ); // Retrieve file containing edit post functions
}
?>