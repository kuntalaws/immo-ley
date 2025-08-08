<?php
add_action('acf/init', function(){
	add_filter( 'register_post_type_args', 'customize_default_post_labels_for_blog',9999999, 2 );
	function customize_default_post_labels_for_blog( $args, $post_type ) {
		// Let's make sure that we're customizing the post type we really need
		if ( $post_type !== 'post' ) {
			return $args;
		}
		// $args['public'] =  false;
		$args['show_in_rest'] =  false;
		return $args;
	}
},99999);
