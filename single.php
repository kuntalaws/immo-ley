<?php 
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
        $categories = get_the_category();
        $category_names = array_map(function($cat) {
            return $cat->name;
        }, $categories);
        $category_output = implode(', ', $category_names);
        ?>
        <div class="single-blog">
            <div class="single-blog-in fw">
                <div class="single-blog-header">      
                    <?php if(!empty($category_output)) { ?>
                        <div class="single-blog-category"><span><?php echo esc_html($category_output); ?></span></div>
                    <?php } ?>
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
                    <?php
                    // Get current post categories
                    $current_categories = get_the_category();
                    $category_ids = array();
                    foreach($current_categories as $category) {
                        $category_ids[] = $category->term_id;
                    }

                    // Query related posts
                    $related_args = array(
                        'post_type' => 'post',
                        'post_status' => 'publish',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()), // Exclude current post
                        'category__in' => $category_ids,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    $related_posts = new WP_Query($related_args);

                    if($related_posts->have_posts()) {
                        while($related_posts->have_posts()) {
                            $related_posts->the_post();
                            $postID = get_the_ID();
                            $imageId = get_field('additional_grid_image', $postID);
                            if(!empty($imageId) && intval($imageId) > 0) {
                                $image = swcGetImage($imageId, 365, 522, true, true);
                            } else {
                                $image = false;
                            }
                            $categories = get_the_category();
                            $category_names = array_map(function($cat) {
                                return $cat->name;
                            }, $categories);
                            $category_output = implode(', ', $category_names);
                            ?>
                            <div class="blog-listing-item">
                                <?php if($image) { ?>
                                    <div class="blog-listing-img">
                                        <img loading="lazy" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" title="<?php echo $image['title']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>">
                                    </div>
                                <?php } ?>
                                <div class="blog-listing-category-with-date flex">
                                    <div class="blog-listing-category"><span><?php echo esc_html($category_output); ?></span></div>
                                    <div class="blog-listing-date"><span><?php echo esc_html(get_the_date()); ?></span></div>
                                </div>
                                <div class="blog-listing-title-with-link">
                                    <div class="blog-listing-title-with-link-in">
                                        <h3><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></h3>
                                        <a href="<?php echo get_permalink(); ?>" class="link">lees meer</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
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
