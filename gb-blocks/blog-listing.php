<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
?>
<!--Blog Listing Start Here-->
<section class="blog-listing-block">
	<div class="blog-listing-block-in fw">
		<div class="blog-listing-row flex">
			<div class="blog-listing-item">
				<div class="blog-listing-img">
					<img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/uploads/2025/06/blog-listing-img-01.jpg" alt="Immo Ley">
				</div>
				<div class="blog-listing-category-with-date flex">
					<div class="blog-listing-category"><span>NIEUWS</span></div>
					<div class="blog-listing-date"><span>03 JUNI 2025</span></div>
				</div>
				<div class="blog-listing-title-with-link">
					<div class="blog-listing-title-with-link-in">
						<h3><a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/">Wat kost een ingrijpende renovatie?</a></h3>
						<a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/" class="link">lees meer</a>
					</div>
				</div>
			</div>
			<div class="blog-listing-item">
				<div class="blog-listing-img">
					<img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/uploads/2025/06/blog-listing-img-02.jpg" alt="Immo Ley">
				</div>
				<div class="blog-listing-category-with-date flex">
					<div class="blog-listing-category"><span>NIEUWS</span></div>
					<div class="blog-listing-date"><span>03 JUNI 2025</span></div>
				</div>
				<div class="blog-listing-title-with-link">
					<div class="blog-listing-title-with-link-in">
						<h3><a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/">Boutique hotel at home</a></h3>
						<a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/" class="link">lees meer</a>
					</div>
				</div>
			</div>
			<div class="blog-listing-item">
				<div class="blog-listing-img">
					<img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/uploads/2025/06/blog-listing-img-03.jpg" alt="Immo Ley">
				</div>
				<div class="blog-listing-category-with-date flex">
					<div class="blog-listing-category"><span>NIEUWS</span></div>
					<div class="blog-listing-date"><span>03 JUNI 2025</span></div>
				</div>
				<div class="blog-listing-title-with-link">
					<div class="blog-listing-title-with-link-in">
						<h3><a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/">Wat moet je regelen voor je verkoopt?</a></h3>
						<a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/" class="link">lees meer</a>
					</div>
				</div>
			</div>
			<div class="blog-listing-item">
				<div class="blog-listing-img">
					<img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/uploads/2025/06/blog-listing-img-01.jpg" alt="Immo Ley">
				</div>
				<div class="blog-listing-category-with-date flex">
					<div class="blog-listing-category"><span>NIEUWS</span></div>
					<div class="blog-listing-date"><span>03 JUNI 2025</span></div>
				</div>
				<div class="blog-listing-title-with-link">
					<div class="blog-listing-title-with-link-in">
						<h3><a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/">Wat kost een ingrijpende renovatie?</a></h3>
						<a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/" class="link">lees meer</a>
					</div>
				</div>
			</div>
			<div class="blog-listing-item">
				<div class="blog-listing-img">
					<img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/uploads/2025/06/blog-listing-img-02.jpg" alt="Immo Ley">
				</div>
				<div class="blog-listing-category-with-date flex">
					<div class="blog-listing-category"><span>NIEUWS</span></div>
					<div class="blog-listing-date"><span>03 JUNI 2025</span></div>
				</div>
				<div class="blog-listing-title-with-link">
					<div class="blog-listing-title-with-link-in">
						<h3><a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/">Boutique hotel at home</a></h3>
						<a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/" class="link">lees meer</a>
					</div>
				</div>
			</div>
			<div class="blog-listing-item">
				<div class="blog-listing-img">
					<img loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/uploads/2025/06/blog-listing-img-03.jpg" alt="Immo Ley">
				</div>
				<div class="blog-listing-category-with-date flex">
					<div class="blog-listing-category"><span>NIEUWS</span></div>
					<div class="blog-listing-date"><span>03 JUNI 2025</span></div>
				</div>
				<div class="blog-listing-title-with-link">
					<div class="blog-listing-title-with-link-in">
						<h3><a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/">Wat moet je regelen voor je verkoopt?</a></h3>
						<a href="https://anushaweb.com/immo-ley/wat-kost-eeningrijpende-renovatie/" class="link">lees meer</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Blog Listing End Here-->

<?php
	if(intval($contentRowsInPage['blog-listing']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/blog-listing.css')){
			echo '<style>';
		include(get_template_directory().'/css/blog-listing.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['blog-listing'] = intval($contentRowsInPage['blog-listing'])+1;

