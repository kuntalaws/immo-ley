<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
?>
<!--Side Image With Content Block Left image Start Here-->
<div class="side-image-with-content-block left">
    <div class="side-image-with-content-wrap fw">
    <div class="side-image-with-content-block-in flex">
        <div class="side-image-with-content-block-img"><img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/themes/immo-ley/img/over-ons.png" alt="Immo Ley"></div>
        <div class="side-image-with-content-block-content">
            <h2>Persoonlijke begeleiding 
            bij elke stap.</h2>
            <p>Of je nu verkoopt, koopt, verhuurt of huurt — wij staan aan je zijde met een ongeziene service en persoonlijke opvolging. Een woning kopen of verkopen is één van de belangrijkste beslissingen in je leven. Dat verdient aandacht, expertise én warmte.</p>

            <p> We onderscheiden ons door onze sterke visuele aanpak, slimme marketing, en transparante communicatie van begin tot einde. Je mag rekenen op heldere opvolging, en advies dat zowel esthetisch als technisch onderbouwd is.</p>
        </div>
    </div>
    </div>
</div>
<!--Side Image With Content Block Left image End Here-->

<!--Side Image With Content Block Right image Start Here-->
<div class="side-image-with-content-block right">
    <div class="side-image-with-content-wrap fw">
    <div class="side-image-with-content-block-in flex">
        <div class="side-image-with-content-block-img"><img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/themes/immo-ley/img/over-ons.png" alt="Immo Ley"></div>
        <div class="side-image-with-content-block-content">
            <h2>Praktisch, juridisch én technisch.</h2>
            <p>We begeleiden onze klanten van A tot Z. Niet alleen juridisch en praktisch, maar ook esthetisch en op technisch vlak. Dankzij onze samenwerking met onze ervaren bouw- en renovatiepartner Construmax, bieden we diepgaande interieurkennis, materiaalkeuze en advies op maat. Zo helpen we om een pand optimaal in de markt te zetten — of een nieuwe woning écht jouw thuis te maken.</p>
        </div>
    </div>
    </div>
</div>
<!--Side Image With Content Block End Here-->

<!--Side Image With Content Block Left image Start Here-->
<div class="side-image-with-content-block left">
    <div class="side-image-with-content-wrap fw">
    <div class="side-image-with-content-block-in flex">
        <div class="side-image-with-content-block-img"><img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/themes/immo-ley/img/over-ons.png" alt="Immo Ley"></div>
        <div class="side-image-with-content-block-content">
            <h2>Altijd bereikbaar, 
            altijd menselijk.
            </h2>
            <p>
            Bij Immo LEY combineren we stijl en knowhow met echte betrokkenheid. Je mag rekenen op een service die:</p>

            <ul>
                <li>7 op 7 bereikbaar is</li>
                <li>Persoonlijk en creatief werkt</li>
                <li>En vooral: mensgericht is</li>
            </ul>
        </div>
    </div>
    </div>
</div>
<!--Side Image With Content Block Left image End Here-->

<?php
	if(intval($contentRowsInPage['side-image-with-content-block']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/side-image-with-content-block.css')){
			echo '<style>';
		include(get_template_directory().'/css/side-image-with-content-block.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['side-image-with-content-block'] = intval($contentRowsInPage['side-image-with-content-block'])+1;