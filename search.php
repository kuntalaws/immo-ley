<?php if (!defined('ABSPATH')) exit;
$search_page = intval(get_field('search_page', 'option'));
get_header(); 
if($search_page > 0 && get_post_type($search_page)== "page" && get_post_status($search_page)=="publish"){
	echo apply_filters("the_content",get_post_field('post_content', $search_page));
}else{
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $sectionClass = '';
    if(!have_posts()){
        $sectionClass = ' no-result';
    }
?>
<section class="search-result<?php echo $sectionClass;?>">
    <div class="fw">
        <div class="search-result-inn">
            <?php if(have_posts()){ ?>
                <h2 class="subheading wow fadeIn" data-wow-duration="0.5s">SEARCH RESULTS</h2>
                <h1 class="heading wow fadeIn" data-wow-duration="0.5s"><?php echo get_query_var('s');?></h1>
            <?php }else{?>
                <h2 class="subheading wow fadeIn" data-wow-duration="0.5s">NO RESULTS FOUND FOR</h2>
                <h1 class="heading wow fadeIn" data-wow-duration="0.5s"><?php echo get_query_var('s');?></h1>
                <div class="no-result-area wow fadeIn" data-wow-duration="0.5s">
                    <p>Sorry, but nothing matched your search terms. Please try again with some different keywords.</p>
                    <div class="search-wrap">
                        <form class="flex" method="get" action="<?php echo trailingslashit(get_option('home'));?>">
                            <input name="s" type="text" value="<?php echo get_query_var('s');?>" placeholder="Search" id="">
                            <input type="submit" value="submit" name="submit">
                        </form>
                    </div>					
                </div>
            <?php 
            }
            if(have_posts()){ ?>
                <div class="result-area">
                    <?php if(have_posts()){ 
                        while ( have_posts() ) { the_post(); 
                            $postId = get_the_id();
                            $imageId = get_post_thumbnail_id( $postId );
                            $image = swcGetImage($imageId,280,196,true,true);                    
                            if(!$image){						
                                $image = array(
                                    'alt'=>'',
                                    'title'=>'',
                                    'url' =>'https://via.placeholder.com/280x196/182525/566C47/?text=No%20Image',
                                    'width'=>280,
                                    'height'=>196,
                                    'attrs'=>array(
                                            'class' => '',
                                            'src' => 'src'
                                            )
                                    );
                            }
                            if(get_the_excerpt($postId)){
                                $excerpt = swcTruncatedExcerpt(get_the_excerpt($postId), 90);
                            }
                            $postTypeSlug = get_post_type($postId);
                            $postTypeName = get_post_type_object($postTypeSlug);?>
                            <div class="result-area-item flex wow fadeIn" data-wow-duration="0.5s">
                                <?php if(!empty($image)){?>
                                    <div class="result-area-item-img">
                                        <img loading="lazy" src="<?php echo $image['url'];?>" alt="<?php echo $image['alt'];?>" width="<?php echo $image['width'];?>" height="<?php echo $image['height'];?>">
                                    </div>
                                <?php }?>
                                <div class="result-area-item-text">
                                    <div class="top-text"><?php echo $postTypeName->labels->singular_name;?></div>
                                    <h3><?php the_title();?></h3>
                                    <?php if(!empty($excerpt)){ ?>
                                        <p><?php echo $excerpt; ?></p>
                                    <?php } ?>
                                    <div class="link-area">
                                        <a href="<?php echo get_permalink();?>">VIEW THIS PAGE</a>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    }?>
                </div>
            <?php if($wp_query->max_num_pages > 1){ ?>
                <div class="search-result-pageination flex wow fadeIn">
                <?php 
                    if(function_exists('wp_paginate')){
                        echo wp_paginate(array('pages'=>$wp_query->max_num_pages,'page'=>$paged));
                    }                           
                ?>	
                </div>								
            <?php }
            }?>
        </div>
    </div>
</section> 
<?php
	if(file_exists(get_template_directory().'/css/search-result.css')){
		echo '<style>';
		include(get_template_directory().'/css/search-result.css');
		echo '</style>';
	  }
	  if(file_exists(get_template_directory().'/css/search-result-1025.css')){
		echo '<style media="(min-width: 1025px)">';
		include(get_template_directory().'/css/search-result-1025.css');
		echo '</style>';
	  }
}
get_footer(); ?>
