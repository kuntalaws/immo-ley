<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$displayOption = trim(get_field('display_option'));
$heading = trim(get_field('heading'));
$overview = trim(get_field('overview'));
$image = intval(get_field('pimage'));
// $imageTab = swcGetImage($image,768,NULL,true,true);
$imageMob = swcGetImage($image,1024,NULL,true,true);
$image = swcGetImage($image,1920,1080,true,true);
$button = get_field('button');
$button = swcGetLink($button);
$video = get_field('video');
$video = swcGetURLfromIframe($video);
if($vmoVideoID = getVimeoId($video)){
    $video = array(
                'id'=>$vmoVideoID,
                'thumb'=>getVimeoThumb($vmoVideoID,1920,1080)['thumb'],
                'url'=>'https://player.vimeo.com/video/'.$vmoVideoID
            );
}else if($ytVideoID = getYoutubeId($video)){
    $video = array(
                'id'=>$ytVideoID,
                'thumb'=>getYoutubeThumb($ytVideoID),
                'url'=>'https://www.youtube.com/embed/'.$ytVideoID
            );
}else{
    $video = false;
}
if(empty($heading) && is_admin()){
	$heading = "Heading goes here..";
}
if(empty($overview) && is_admin()){
	$overview = "Overview goes here..";
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
	$imageTab = $imageMob = $image;
}
// if(!empty($heading) || !empty($overview) || !empty($image) || !empty($video) || !empty($button)){?>
<!--Container Start Here-->
<div class="container">
	<!--Hero Banner Start-->
	<section class="hero-banner">
		<div class="hero-banner-bg" style="background-image: url(https://anushaweb.com/immo-ley/wp-content/uploads/2025/06/hero-banner-bg.jpg);"></div>
		<div class="hero-banner-in fw flex">
			<div class="hero-banner-content">
				<h1>Sterke panden, sterke service.</h1>
				<div class="button-wrap">
					<a href="#" class="btn"><span>GRATIS WAARDEBEPALING</span></a>
				</div>
			</div>
		</div>
	</section>
	<!--Hero Banner End-->
</div>
<!--Container End Here-->

<?php
	if(intval($contentRowsInPage['hero']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/hero-banner.css')){
			echo '<style>';
		include(get_template_directory().'/css/hero-banner.css');
		echo '</style>';
		}
		if(file_exists(get_template_directory().'/css/hero-banner-1025.css')){
		echo '<style media="(min-width: 1025px)">';
		include(get_template_directory().'/css/hero-banner-1025.css');
		echo '</style>';
		}      
	}
	$contentRowsInPage['hero'] = intval($contentRowsInPage['hero'])+1;
// }
