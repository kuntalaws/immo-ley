<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
?>
<!--Single Project Start Here-->
<section class="single-project-section">
	<div class="container">
		<div class="single-project-content">
			<h1>Single Project</h1>
		</div>
	</div>
</section>

<?php
	if(intval($contentRowsInPage['single-project']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/single-project.css')){
			echo '<style>';
		include(get_template_directory().'/css/single-project.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['filter-with-grid'] = intval($contentRowsInPage['filter-with-grid'])+1;
