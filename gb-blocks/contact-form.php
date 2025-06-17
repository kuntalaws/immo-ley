<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;

$short_code = get_field('short_code');
$info_group_heading = get_field('info_group_heading');
$info_groups = get_field('info_groups');
$info_group_text = get_field('info_group_text');

// Set placeholders for admin view
if(is_admin()) {
    if(empty($info_group_heading)) {
        $info_group_heading = "Heading goes here..";
    }
    if(empty($info_group_text)) {
        $info_group_text = "Appointment text goes here..";
    }
    if(empty($info_groups)) {
        $info_groups = array(
            array(
                'info_group' => array(
                    array(
                        'info_type' => 'bold',
                        'info' => 'Info text goes here..'
                    ),
                    array(
                        'info_type' => 'text',
                        'info' => 'Additional info goes here..'
                    )
                )
            )
        );
    }
}
?>

<div class="contact-form-section">
    <div class="contact-form-section-in fw flex">
        <div class="contact-form">
            <div class="contact-form-in">
                <?php 
                if(!empty($short_code)) {
                    echo do_shortcode($short_code);
                } elseif(is_admin()) {
                    echo '<p>Short code goes here.</p>';
                }
                ?>
            </div>
        </div>
    
        <div class="contact-widget">
            <?php if(!empty($info_group_heading)) { ?>
                <h2 class="title"><?php echo esc_html($info_group_heading); ?></h2>
            <?php } ?>
            
            <?php if(!empty($info_groups)) { ?>
                <div class="contact-info">
                    <?php foreach($info_groups as $group) { 
                        if(!empty($group['info_group'])) { ?>
                            <div class="info-group">
                                <?php foreach($group['info_group'] as $info) { 
                                    $info_type = $info['info_type'];
                                    $info_text = $info['info'];
                                    
                                    if($info_type === 'email') { ?>
                                        <a href="mailto:<?php echo esc_attr($info_text); ?>" class="info-line email"><?php echo esc_html($info_text); ?></a>
                                    <?php } elseif($info_type === 'phone') { ?>
                                        <a href="tel:<?php echo esc_attr($info_text); ?>" class="info-line phone"><?php echo esc_html($info_text); ?></a>
                                    <?php } else { ?>
                                        <div class="info-line <?php echo esc_attr($info_type); ?>"><?php echo esc_html($info_text); ?></div>
                                    <?php }
                                } ?>
                            </div>
                        <?php }
                    } ?>
                </div>
            <?php } ?>
            
            <?php if(!empty($info_group_text)) { ?>
                <div class="appointment"><?php echo esc_html($info_group_text); ?></div>
            <?php } ?>
        </div>
    </div>
</div>

<?php
if(intval($contentRowsInPage['contact-form']) == 0 || is_admin()){
    if(file_exists(get_template_directory().'/css/contact-form.css')){
        echo '<style>';
        include(get_template_directory().'/css/contact-form.css');
        echo '</style>';
    }    
}
$contentRowsInPage['contact-form'] = intval($contentRowsInPage['contact-form'])+1;