<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
?>

<div class="contact-form-section">
    <div class="contact-form-section-in fw flex">
        <div class="contact-form">
            <div class="contact-form-in">
                <?php echo do_shortcode('[contact-form-7 id="6205547" title="contact form"]'); ?>
            </div>
        </div>
    
    <div class="contact-widget">
        <h2 class="title">Onze kantoren</h2>
        
        <div class="contact-info">
            <div class="info-group">
                <div class="info-line primary">KERREMANSSTRAAT 31</div>
                <div class="info-line">2540 RUMST</div>
            </div>
            
            <div class="info-group">
                <div class="info-line">0497 72 52 12</div>
                <div class="info-line">03 535 20 00</div>
            </div>
            
            <div class="info-group">
                <a href="mailto:info@immoley.be" class="info-line email">INFO@IMMOLEY.BE</a>
            </div>
            
            <div class="info-group">
                <div class="info-line">BIV 516.614</div>
                <div class="info-line">BTW BE 0767 847 446</div>
            </div>
        </div>
        
        <div class="appointment">OPEN OP AFSPRAAK</div>
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