<?php
/*
Functions to include when editing a post
*/


/**
 * Author Meta Box
 */
/* Add meta box to Edit Post and Edit Page, and remove WordPress default Author meta box */
function cc_author_add_metabox() {
	$screens = array( 'post', 'page' ); // Locations where the metabox should show
	
	/* Remove WordPress default Author meta box */
	foreach ( $screens as $screen ) {
		remove_meta_box( 'authordiv', $screen, 'normal' ); // Parameters for removing Author meta box from Post and Page edit screens
	}
	
	/* Iterate through locations to add meta box */
	foreach( $screens as $screen ) {
		add_meta_box( 'cc-author-metabox', 'Author', 'cc_author_metabox', $screen, 'normal', 'default' ); // Parameters for adding meta box
	}
	
	/* Add custom style for meta box */
	$styleurl = plugins_url( 'assets/css/edit-post.css', dirname( __FILE__ ) ); // Set URL to CSS file
	wp_enqueue_style( 'cc-author-metabox', $styleurl ); // Add style call to <head>
} // cc_author_add_metabox()
add_action( 'add_meta_boxes', 'cc_author_add_metabox' ); // Hook meta box updates into WordPress

/* Meta box code: $post is the data for the current post */
function cc_author_metabox( $post ) {
	/* Retrieve current values if they exist */
	$cc_author_meta = get_post_meta( $post->ID, '_cc_author_meta', true ); // Author meta data (stored as an array)
	
	/* If any of the values are missing from the post, retrieve them from the author's global profile */
	if ( !$cc_author_meta ) {
		$currentuserid = get_current_user_id(); // Get the user ID of the current user
		
		$currentuser = get_userdata( $currentuserid ); // Retrieve the details of the current user
		
		$cc_author_meta = array(); // Initialize array
		$cc_author_meta['display_name'] = $currentuser->display_name; // Set display name from current user's data
		$cc_author_meta['description'] = $currentuser->description; // Set bio from the current user's data
	}
	
	/* Display the meta box contents */
	?>
	<div class="cc_author_metabox">
		<label for="cc_author_meta[display_name]" class="selectit">Name</label>
		<input type="text" name="cc_author_meta[display_name]" value="<?php echo esc_attr( $cc_author_meta['display_name'] ); ?>" />

		<label for="cc_author_meta[description]" class="selectit">Bio</label>
		<textarea name="cc_author_meta[description]" rows="5" cols="50" required><?php echo esc_attr( $cc_author_meta['description'] ); ?></textarea>
	</div>
	<?php
} // cc_author_metabox( $post )

/* Save the meta box data to post meta */
function cc_author_save_meta( $post_id ) {
	if ( isset( $_POST['cc_author_meta'] ) ) { // Verify that values have been provided
		$authormeta = $_POST['cc_author_meta']; // Assign POST data to local variable
		
		/* Sanitize array values */
		foreach ( $authormeta as $key => $meta ) {
			$authormeta[$key] = strip_tags( $meta );
		}
		update_post_meta( $post_id, '_cc_author_meta', $authormeta ); // Save author meta data to post meta
	}
} // cc_author_save_meta( $post_id )
add_action( 'save_post', 'cc_author_save_meta' ); // Hook WordPress to save meta data when saving post/page
/**
 * End Author Meta Box
 */
?>