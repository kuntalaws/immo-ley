<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @package WordPress
 * @subpackage RECOVERY_FRIENDLY_WORKPLACE
 */

get_header();
	if(have_posts()){ 
		while (have_posts()){
			the_post();
			the_content();
		}
	}
get_footer();
