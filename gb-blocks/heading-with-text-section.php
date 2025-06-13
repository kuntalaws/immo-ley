<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
?>
<!--Side Image With Content Block Start Here-->
<div class="side-image-with-content-block">
    <div class="side-image-with-content-block-in flex">
        <div class="side-image-with-content-block-content">
           
        </div>
    </div>
</div>
<!--Side Image With Content Block End Here-->

<?php
	if(intval($contentRowsInPage['header-with-text-section']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/header-with-text-section.css')){
			echo '<style>';
		include(get_template_directory().'/css/header-with-text-section.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['header-with-text-section'] = intval($contentRowsInPage['header-with-text-section'])+1;