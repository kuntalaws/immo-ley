<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$content = trim(get_field('content'));
$background = get_field('background');
$backgroundClass = '';
if($background){
	$backgroundClass = ' bg-light-blue';
}
if(empty($content) && is_admin()){
	$content = "Content goes here..";
}
if(!empty($content)){?>
<div class="heading-with-text-section<?php echo $backgroundClass;?>">
    <div class="heading-with-text-section-in fw">
        <div class="heading-with-text-content">
			<?php echo apply_filters('the_content', $content);?>
        </div>
    </div>
</div>
<?php
	if(intval($contentRowsInPage['heading-with-text-section']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/heading-with-text-section.css')){
			echo '<style>';
		include(get_template_directory().'/css/heading-with-text-section.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['heading-with-text-section'] = intval($contentRowsInPage['heading-with-text-section'])+1;
}
