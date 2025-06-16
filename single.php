<?php 
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
        ?>
        <div class="single-blog">
            <div class="single-blog-in fw">
                <div class="single-blog-header">                                    
                    <div class="single-blog-category"><span><?php echo get_the_category(); ?></span></div>
                    <h1 class="single-blog-title"><?php echo get_the_title(); ?></h1>
                    <div class="single-blog-date"><span><?php echo get_the_date(); ?></span></div>
                    <div class="single-blog-excerpt"><?php echo wpautop(get_the_excerpt()); ?></div>
                </div>
                <div class="single-blog-img">
                    <?php echo get_the_post_thumbnail(); ?>
                </div>
                <div class="single-blog-content">
                    <?php echo apply_filters('the_content', get_the_content()); ?>
                </div>
            </div>
        </div>
        
        <section class="blog-listing-block related-blog">
            <div class="blog-listing-block-in fw">
                <h2>Gerelateerd nieuws</h2>
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
                </div>
            </div>
        </section>

        
<?php
        if(file_exists(get_template_directory().'/css/blog-listing.css')){
            echo '<style>';
        include(get_template_directory().'/css/blog-listing.css');
            echo '</style>';
        } 
        if(file_exists(get_template_directory().'/css/blog-single.css')){
            echo '<style>';
        include(get_template_directory().'/css/blog-single.css');
            echo '</style>';
        }

    }
}
get_footer();
