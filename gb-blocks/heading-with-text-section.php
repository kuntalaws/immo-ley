<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
?>
<!--heading-with-text-section Start Here-->
<div class="heading-with-text-section bg-light-blue">
    <div class="heading-with-text-section-in fw">
        <div class="heading-with-text-content">
			<h2>Wat kunnen we voor jou doen?</h2>
			<p>Bij Immo LEY gaan we verder dan enkel panden tonen of verkoopborden plaatsen. We luisteren écht, geven advies dat bij jou past, en zorgen voor een begeleiding die je volledig ontzorgt. 

Of je nu je woning wil verkopen, op zoek bent naar een nieuwe thuis, je patrimonium wil laten beheren of een project wil ontwikkelen — wij staan klaar met kennis van zaken. Bouwkundig, juridisch én menselijk.

We onderscheiden ons door:
</p>
<ul>
	<li>
	een sterke visuele en esthetische aanpak</li>
	<li>slimme marketing op maat van jouw doelgroep</li>
	<li>duidelijke communicatie en opvolging van begin tot einde
	</li>
</ul>
<p>Kortom: we zorgen ervoor dat jij je op elk moment gerust en begrepen voelt.</p>
        </div>
    </div>
</div>
<!--heading-with-text-section End Here-->

<!--heading-with-text-section Start Here-->
<div class="heading-with-text-section">
    <div class="heading-with-text-section-in fw">
        <div class="heading-with-text-content">
			<h2>U wilt verkopen?</h2>
			<p>Uw vastgoed verdient een aanpak op maat én het juiste publiek.</p>
			<p>Bij Immo LEY brengen we residentieel vastgoed op een doordachte en persoonlijke manier, met oog voor detail en gevoel voor timing. We begeleiden u van begin tot verkoop, en matchen uw eigendom met de juiste kopers — in de Zuidrand van Antwerpen.</p>
        </div>
    </div>
</div>
<!--heading-with-text-section End Here-->
<?php
	if(intval($contentRowsInPage['heading-with-text-section']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/heading-with-text-section.css')){
			echo '<style>';
		include(get_template_directory().'/css/heading-with-text-section.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['heading-with-text-section'] = intval($contentRowsInPage['heading-with-text-section'])+1;