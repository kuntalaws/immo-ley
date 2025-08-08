<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$content = trim(get_field('content'));
if(empty($content) && is_admin()){
	$content = "Content goes here..";
}
if(!empty($content)){?>
<!--Text Section Start Here-->
<section class="text-section">
	<div class="text-section-in fw">
		<h3><?php echo $content;?></h3>
	</div>
</section>
<!--Text Section End Here-->

<?php
	if(intval($contentRowsInPage['text-section']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/text-section.css')){
			echo '<style>';
		include(get_template_directory().'/css/text-section.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['text-section'] = intval($contentRowsInPage['text-section'])+1;
}
