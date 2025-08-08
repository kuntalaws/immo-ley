<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$pagetitle = trim(get_field('pagetitle'));
if(empty($pagetitle) && is_admin()){
	$pagetitle = "Page Title here..";
}
if(!empty($pagetitle)){?>
<!--Text Section Start Here-->
<section class="page-title-block">
	<div class="page-title-block-in fw">
		<h1><?php echo $pagetitle;?></h1>
	</div>
</section>
<!--Text Section End Here-->

<?php
	if(intval($contentRowsInPage['page-title-block']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/page-title-block.css')){
			echo '<style>';
		include(get_template_directory().'/css/page-title-block.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['page-title-block'] = intval($contentRowsInPage['page-title-block'])+1;
}
