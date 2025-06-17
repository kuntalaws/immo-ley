<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;

$heading = get_field('heading');
$columns = get_field('columns');

// Set placeholders for admin view
if(is_admin()) {
    if(empty($heading)) {
        $heading = "Heading goes here..";
    }
    if(empty($columns)) {
        $columns = array(
            array(
                'title' => 'Column Title 1',
                'content' => 'Column content goes here..'
            ),
            array(
                'title' => 'Column Title 2',
                'content' => 'Column content goes here..'
            ),
            array(
                'title' => 'Column Title 3',
                'content' => 'Column content goes here..'
            )
        );
    }
}
?>
<!--Filter With Grid Start Here-->
<section class="three-column-heading-with-content">
	<div class="container">
		<div class="three-column-heading-with-content-content">
			<?php if(!empty($heading)) { ?>
				<h2><?php echo esc_html($heading); ?></h2>
			<?php } ?>
			
			<?php if(!empty($columns)) { ?>
				<div class="columns-wrapper">
					<?php foreach($columns as $column) { ?>
						<div class="column">
							<?php if(!empty($column['title'])) { ?>
								<h3><?php echo esc_html($column['title']); ?></h3>
							<?php } ?>
							<?php if(!empty($column['content'])) { ?>
								<div class="content"><?php echo wp_kses_post($column['content']); ?></div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>

<?php
	if(intval($contentRowsInPage['three-column-heading-with-content']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/three-column-heading-with-content.css')){
			echo '<style>';
		include(get_template_directory().'/css/three-column-heading-with-content.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['three-column-heading-with-content'] = intval($contentRowsInPage['three-column-heading-with-content'])+1;

