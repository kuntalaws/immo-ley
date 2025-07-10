<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$displayOption = trim(get_field('display_option'));
$heading = trim(get_field('heading'));
$image = intval(get_field('pimage'));
// $imageTab = swcGetImage($image,768,NULL,true,true);
$imageMob = swcGetImage($image,1024,NULL,true,true);
$image = swcGetImage($image,1920,1080,true,true);
$button = get_field('button');
$button = swcGetLink($button);
$video_url = get_field('herovideo');
$video_poster = intval(get_field('video_poster_bg'));
$video_posterMob = swcGetImage($video_poster,1024,NULL,true,true);
$video_poster = swcGetImage($video_poster,1920,1080,true,true);
if(empty($heading) && is_admin()){
	$heading = "Heading goes here..";
}
if(!$button && is_admin()){
	$button = array('link'=>'#','target'=>'','label'=>'Button Label');
}

if(!$image && is_admin()) {
    $image = array(
		'alt'=>'',
		'title'=>'',
		'url'=>'https://via.placeholder.com/1920x1080/e8e8e8/566C47/?text=Placeholder',
		'width'=>1920,
		'height'=>1080,
		'attrs'=>array(
						'class' => '',
						'src' => 'src'
					)
	);
}
if(!empty($heading) || !empty($image) || !empty($button)){?>
	<!--Hero Banner Start-->
	<section class="hero-banner">
		<?php if(!empty($image)){?>
			<div class="hero-banner-bg" style="background-image: url(<?php echo $image['url'];?>);">
			</div>
		<?php }?>
		<?php if(!empty($video_url)){?>
			<div class="hero-banner-bg" style="background-image: url(<?php echo $video_poster['url'];?>);">
				<video autoplay loop muted playsinline class="background-video">
					<source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
					Your browser does not support the video tag.
				</video>
			</div>
		<?php }?>		
		<div class="hero-banner-in fw flex">
			<div class="hero-banner-content">
				<?php if(!empty($heading)){
					$htag = get_field('htag');
					$htag = swcGetHeadingTag($htag,'h1');?>
					<<?php echo $htag;?>><?php echo $heading;?></<?php echo $htag;?>>
				<?php }
				if(!empty($button)){?>
					<div class="button-wrap">
						<a href="<?php echo $button['link'];?>"<?php echo $button['target'];?> class="btn"><span><?php echo $button['label'];?></span></a>
					</div>
				<?php }?>
			</div>
		</div>
	</section>
	<!--Hero Banner End-->

<?php
	if(intval($contentRowsInPage['hero']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/hero-banner.css')){
			echo '<style>';
		include(get_template_directory().'/css/hero-banner.css');
		echo '</style>';
		}     
	}
	$contentRowsInPage['hero'] = intval($contentRowsInPage['hero'])+1;
}
