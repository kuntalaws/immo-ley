<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
$nfpage = intval(get_field('nfpage','option'));
get_header();
if($nfpage > 0 && get_post_type($nfpage) == 'page' && get_post_status($nfpage) == 'publish'){
	echo apply_filters('the_content',get_post_field('post_content',$nfpage));
}else{
	?>
	<section class="not-found-header overlay wow fadeIn" data-wow-duration="0.5s">
        <div class="not-found-header-inn">
            
        </div>
    </section>
	<?php
		if(file_exists(get_template_directory().'/css/not-found-header.css')){
			echo '<style>';
            include(get_template_directory().'/css/not-found-header.css');
            echo '</style>';
		}
		if(file_exists(get_template_directory().'/css/not-found-header-1025.css')){
            echo '<style media="(min-width: 1025px)">';
            include(get_template_directory().'/css/not-found-header-1025.css');
            echo '</style>';
		}
	?>
	<?php
}
get_footer();