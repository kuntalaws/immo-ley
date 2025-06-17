<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$args = array(
	'post_type'		  => 'post',
	'post_status'	  => 'publish',
	'posts_per_page' => -1,
	'orderby'		  => 'date',
	'order' 		=> 'DESC'
);
$posts = new WP_Query($args);
// echo '<pre>';
// print_r($posts);
// echo '</pre>';
if($posts->have_posts() || is_admin()){
?>
<!--Blog Listing Start Here-->
<section class="blog-listing-block">
	<div class="blog-listing-block-in fw">
		<div class="blog-listing-row flex">
			<?php 
			if($posts->have_posts()){
				while($posts->have_posts()){
					$posts->the_post();
					$postID = get_the_ID();
					$imageId = get_field('additional_grid_image',$postID);
					if(!empty($imageId) && intval($imageId) > 0){
						$image = swcGetImage($imageId,365,522,true,true);
					}else{
						$image = false;
					}
					$categories = get_the_category();
					$category_names = array_map(function($cat) {
						return $cat->name;
					}, $categories);
					$category_output = implode(', ', $category_names);?>
					<div class="blog-listing-item">
						<?php if($image){?>
							<div class="blog-listing-img">
								<img loading="lazy" src="<?php echo $image['url'];?>" alt="<?php echo $image['alt'];?>" title="<?php echo $image['title'];?>" width="<?php echo $image['width'];?>" height="<?php echo $image['height'];?>">
							</div>
						<?php }?>
						<div class="blog-listing-category-with-date flex">
							<div class="blog-listing-category"><span><?php echo esc_html($category_output); ?></span></div>
							<div class="blog-listing-date"><span><?php echo esc_html(get_the_date()); ?></span></div>
						</div>
						<div class="blog-listing-title-with-link">
							<div class="blog-listing-title-with-link-in">
								<h3><a href="<?php echo get_permalink();?>"><?php echo get_the_title();?></a></h3>
								<a href="<?php echo get_permalink();?>" class="link">lees meer</a>
							</div>
						</div>
					</div>
			<?php }
			}elseif(is_admin()){
				for ($i=0; $i < 6 ; $i++) { ?>
					<div class="blog-listing-item">
						<div class="blog-listing-img">
							<img loading="lazy" src="https://via.placeholder.com/365x522.jpg?text=PLACEHOLDER" alt="Immo Ley">
						</div>
						<div class="blog-listing-category-with-date flex">
							<div class="blog-listing-category"><span>CATEGORY</span></div>
							<div class="blog-listing-date"><span>03 JUNI 2025</span></div>
						</div>
						<div class="blog-listing-title-with-link">
							<div class="blog-listing-title-with-link-in">
								<h3><a href="#">Title goes here.</a></h3>
								<a href="#" class="link">lees meer</a>
							</div>
						</div>
					</div>
				<?php }
			} ?>
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
}
wp_reset_postdata();