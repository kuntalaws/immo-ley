<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex;
add_theme_support('post-thumbnails');
add_post_type_support('page','excerpt');

if(file_exists(dirname(__FILE__) . '/includes/register-post-types.php')){
	include_once(dirname(__FILE__) . '/includes/register-post-types.php');
}
if(file_exists(dirname(__FILE__) . '/includes/register-gb-block.php')){
	include_once(dirname(__FILE__) . '/includes/register-gb-block.php');
}
if(file_exists(dirname(__FILE__) . '/includes/WhiseAPI.php')){
	include_once(dirname(__FILE__) . '/includes/WhiseAPI.php');
}
if(file_exists(dirname(__FILE__) . '/includes/whise-config.php')){
	include_once(dirname(__FILE__) . '/includes/whise-config.php');
}

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Global Content & Theme Settings',
		'menu_title'	=> 'Global',
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false,
		'icon_url' => 'dashicons-admin-site',
		'position' => 3
	));
}

add_filter( 'allowed_block_types',function($allowed){
    return array(
        'acf/hero',
        'acf/text-section',
	   'acf/filter-with-grid',
	   'acf/side-image-with-content-block',
	   'acf/heading-with-text-section',
	   'acf/page-title-block',
	   'acf/blog-listing',
	   'acf/single-project',
	   'acf/overview-content-with-form',
	   'acf/contact-form',
	   'acf/three-column-heading-with-content',
    );
});

add_action('acf/init', function() {
	$gmapapikey = trim(get_field('tv_map_key', 'option') ?? '');
	if(!empty($gmapapikey)){
		acf_update_setting('google_api_key',$gmapapikey);
	}
});
add_filter('acf/fields/google_map/api',function($api)
{
    $gmapkey = trim(get_field('gmapapikey', 'option'));
    if (!empty($gmapkey)) {
        $api['key'] = $gmapkey;
    }

    return $api;
},99999,1);
if(file_exists(dirname(__FILE__) . '/includes/twicpics/TwicPics.php')){
	require_once dirname(__FILE__) . '/includes/twicpics/TwicPics.php';
	$twicpicsed = trim(get_field('twicpicsed','option'));
	if(!$twicpicsed){
	$twicpicsurl = trim(get_field('twicpicsurl','option'));
	if(!empty($twicpicsurl)){
		$TwicPics = new TwicPics($twicpicsurl,'?twic=v1');
		$twicpicsurl = str_replace(get_option('home'),$twicpicsurl,get_bloginfo('stylesheet_directory').'/img/');		
	}else{
		$TwicPics = false;
		$twicpicsurl = get_bloginfo('stylesheet_directory').'/img/';
	}
	}
}else{
	$TwicPics = false;
	$twicpicsurl = get_bloginfo('stylesheet_directory').'/img/';
}
register_nav_menus(
	array(
		'primary_nav'=>'Primary &raquo; Navigation',
		'footer_primary'=>'Footer &raquo; Primary &raquo; Navigation',
		'footer_secondary'=>'Footer &raquo; Secondary &raquo; Navigation',
		'footer_legal_menu' => 'Footer &raquo; Legal Menu',
	)
);
register_sidebar(
	array(
		'name'=> 'Footer &raquo; Navigation &raquo; Columns',
		'id'=> 'footer-nav-columns',
		'before_widget'=> '<div class="footer-menu">',
        'before_title'=>			'<h3>',
        'after_title'=>			'</h3>',
        'after_widget'=> 	   '</div>',
	)
);
add_filter( 'upload_mimes', 'swcCustomUploadMimes', 10 );
function swcCustomUploadMimes( $existing_mimes = array() ) {
	// add the file extension to the array
	$existing_mimes[ 'svg' ] = 'image/svg+xml';

	// call the modified list of extensions
	return $existing_mimes;
}
function swcEnqueueJqueryInHeader(){
	wp_deregister_script( 'jquery' );
	wp_enqueue_script('jquery',get_template_directory_uri().'/jquery-3.7.1.min.js',false,false,false);
}
add_filter('wp_enqueue_scripts','swcEnqueueJqueryInHeader',1);
function swcAddThemeScripts(){	
	wp_enqueue_script('il-main',get_bloginfo('stylesheet_directory').'/js/main.js',array(),filemtime(plugin_dir_path(__FILE__).'/js/main.js'),true);
	// wp_enqueue_script('swc-combined',get_bloginfo('stylesheet_directory').'/js/combined.js',array(),filemtime(plugin_dir_path(__FILE__).'/js/combined.js'),true);
	// // wp_register_script('swc-gravityform', get_bloginfo('stylesheet_directory').'/js/gravityform.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . '/js/gravityform.js'), true);
	// wp_register_script('swiper',get_bloginfo('stylesheet_directory').'/js/swiper.js',array(),filemtime(plugin_dir_path(__FILE__).'/js/swiper.js'),true);
	// wp_register_script('swiper-init',get_bloginfo('stylesheet_directory').'/js/swiper-init.js',array(),filemtime(plugin_dir_path(__FILE__).'/js/swiper-init.js'),true);
	// wp_register_script('fancybox',get_bloginfo('stylesheet_directory').'/js/fancybox.umd.js',array(),filemtime(plugin_dir_path(__FILE__).'/js/fancybox.umd.js'),true);
}
add_action('wp_enqueue_scripts', 'swcAddThemeScripts', 99999999);

/* ######### BOF : PAGE SPEED OPTIMIZATION ####### */
if (!is_admin()) {
    add_filter('script_loader_tag', 'wsds_defer_scripts', 10, 3);
    function wsds_defer_scripts($tag, $handle, $src)
    {
    	//var_dump($handle);
		//The handles of the enqueued scripts we not want to defer
        $nonDeferableScripts = array('jquery');

        if(!in_array($handle,$nonDeferableScripts)) {
            return '<script id="'.$handle.'-js" src="' . $src . '" defer type="text/javascript"></script>' . "\n";
        }

        return $tag;
    }
}

if (!is_admin()) {
    add_filter('style_loader_tag', 'wsds_preload_styles', 10, 3);
    function wsds_preload_styles($tag, $handle, $src){
        // The handles of the enqueued scripts we not want to defer
        $nonPreloadableStyles = array();
        if(!in_array($handle,$nonPreloadableStyles)) {
            $tag = str_replace(
	        			array('rel="stylesheet"',"rel='stylesheet'"),
	        			'rel="preload" '.
	        			'as="style" '.
	        			'onload="this.onload=null;this.rel=\'stylesheet\'" ',
	        			$tag
	        	   )."\n".
				   '<noscript>
				   		'.$tag.'
				   	</noscript>'."\n";
        }

        return $tag;
    }
}
//Defer Gravity forms JS

function swcGetRecipeTermFilters($filter,$taxonomy){
	$return = array();
	$filter = trim($filter);
	if (!empty($filter)) {
		$filters = explode(',',$filter);
		if (is_array($filters) && count($filters) > 0) {
			foreach ($filters as $fltr) {
				$fltr = trim($fltr);
				if (!empty($fltr) && term_exists($fltr,$taxonomy)) {
					$return[] = $fltr;
				}
			}
		}
	}
	if (count($return) <= 0) {
		$return = false;
	}	
	return $return;
}

/*add_filter('gform_init_scripts_footer', '__return_true');

add_filter('gform_cdata_open', 'wrap_gform_cdata_open');
function wrap_gform_cdata_open($content = '')
{
    if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
        return $content;
    }
    $content = 'document.addEventListener( "DOMContentLoaded", function() { ';
    return $content;
}
add_filter('gform_cdata_close', 'wrap_gform_cdata_close');
function wrap_gform_cdata_close($content = '')
{
    if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
        return $content;
    }
    $content = ' }, false );';
    return $content;
}*/

/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	
	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );
remove_filter('the_content', 'wptexturize');
/**
 * Filter out the tinymce emoji plugin.
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}
/* ######### EOF : PAGE SPEED OPTIMIZATION ####### */

add_shortcode('immoleyCurrentYear',function(){return date("Y");});
function swcGetImage($image, $width, $height, $crop=false, $asString, $focus='auto', $attrs = array()){
	global $TwicPics,$TwicPicsDontCrop;
	if($image > 0 && wp_attachment_is_image($image)){
		$return['mime'] = get_post_mime_type($image);
		$return['title'] = get_the_title($image);
		$return['alt'] = get_post_meta(
							$image,
							'_wp_attachment_image_alt',
							true
						 );
		$hardcrop = false;
		if($crop){
			$hardcrop = true;
		}
	    if($TwicPics){
			$dimension	   = $TwicPics->get_dims($image);
			// Focus Parameters: bottom, bottom-left, bottom-right, left, top, top-left, top-right, right
			// TwicPics uses the same coordinate system as CSS: zero-based, left-to-right and top-to-bottom.
			$return['url'] = $TwicPics->get_img($image,$width,$height,$crop,$asString,$focus);
			// $return['width'] = $dimension['width'];
			// $return['height'] = $dimension['height'];
			$return['width'] = $width;
			$return['height'] = $height;
		}else{
			$return['url'] = wp_get_attachment_image_src(
										$image,
										array($width,$height),
										$hardcrop
								   );
			$return['width']  = $return['url'][1];
			$return['height'] = $return['url'][2];
	    	$return['url']    = $return['url'][0];
		}
		
		if(is_admin()){
			$return['attrs']['src'] = 'src';
		}
		
		if($return['mime'] == 'image/svg+xml'){
			$return['width'] = $width;
			$return['height'] = $height;
			$return['attrs']['src'] = 'src';
			$return['attrs']['class'] = 'class="svg"';
		}else{
			$return['width'] = $width;
			$return['height'] = $height;
			$return['attrs']['src'] = 'src';
			$return['attrs']['class'] = 'loading="lazy"';
		}
		if(is_array($attrs) && isset($attrs['class']) && !empty($attrs['class'])){
			$return['attrs']['class'] .= ' '.$attrs['class'];
		}
	}else{
		$return = false;
	}
	
    return $return;
}
function swcGetButton($button){
	if(
	    is_array($button)
	        &&
	    isset($button['type'])
	        &&
	    !empty($button[$button['type']])
	 ){
	    $button['link'] = $button[$button['type']];		
	    if (empty($button['label'])) {
	        $button['label'] = 'Explore';
	    }
	    if ($button['type'] == 'external') {
	        $button['target'] = ' target="_blank" ';
	    }else {
	        $button['target'] = false;
	    }
	}else{
	    $button = false;
	}
	
return $button;
}

function getYoutubeId($ytURL){
    preg_match('/src="([^"]+)"/', $ytURL, $ytURLmatch);
    if (is_array($ytURLmatch) && isset($ytURLmatch[1]) && !empty($ytURLmatch[1])) {
        $ytURL = $ytURLmatch[1];
    }
    if (preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $ytURL, $matches)) {
        $return = $matches[1];
    } else {
        $return = false;
    }
    return $return;
}
function getYoutubeThumb($id){
    return '//img.youtube.com/vi/'.$id.'/maxresdefault.jpg';
}
function getVimeoId($url){
    if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $url, $output_array)) {
        return $output_array[5];
    } else {
        return false;
    }
}
function getVimeoInfo( $video_id, $width='603', $height='389' ){
	$return = array();
	$VimeoCommunicationLink  = 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/'; 
	$VimeoConnectLink        = $VimeoCommunicationLink . $video_id;
	$SiteUrl                 = get_site_url();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $VimeoConnectLink);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $SiteUrl); //<<<< THIS IS THE KEY
	$response = curl_exec($ch);
	if (curl_errno($ch)) 
		{
		curl_close($ch);
		// echo 'Error:' . curl_error($ch);
		return false;                       // return a blank.
		}
	curl_close($ch);
	$video_array = json_decode( $response, true );
	$thumbnail_url = $video_array['thumbnail_url'];
	$imgageDim = '_'.$width.'x'.$height;
	$return['width'] = $width;
	$return['height'] = $height;
	$return['alt'] = '';
	$return['thumb'] = str_replace("_295x166",$imgageDim,$thumbnail_url);
	return $return;
}
/*function getVimeoThumb($id){
    $vimeo = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$id.php"));
	
    if (is_array($vimeo) && isset($vimeo[0]) && is_array($vimeo[0]) && isset($vimeo[0]['thumbnail_large']) && !empty($vimeo[0]['thumbnail_large'])) {
        $return = str_replace(
        				array('http:','https:','_640'),
        				array('','','_1920'),
        				$vimeo[0]['thumbnail_large']
        		  );
    } else {
        $return = false;
    }

    return $return;
}*/
// 
function getVimeoThumb( $video_id, $width='861', $height='574' ){
    $return = array();
    $VimeoCommunicationLink  = 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/';
    $VimeoConnectLink        = $VimeoCommunicationLink . $video_id;
    $SiteUrl                 = get_site_url();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $VimeoConnectLink);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $SiteUrl); //<<<< THIS IS THE KEY
    $response = curl_exec($ch);
    if (curl_errno($ch)) 
        {
        curl_close($ch);
        echo 'Error:' . curl_error($ch);
        return "";                       // return a blank.
        }
    curl_close($ch);
    $video_array = json_decode( $response, true );
    $thumbnail_url = $video_array['thumbnail_url'];
    $imgageDim = '_'.$width.'x'.$height;
    $return['width'] = $width;
    $return['height'] = $height;
    $return['alt'] = '';
    $return['thumb'] = str_replace("_295x166",$imgageDim,$thumbnail_url);
    return $return;
}
// 
function swcGetURLfromIframe($code){
	if(!isset($code)){
		return false;
	}
	preg_match('/src="([^"]+)"/', $code, $match);
	if (is_array($match) && isset($match[1])) {
		$url = $match[1];
		return $url;
	}else{
		return false;
	}
}
function swcGetHeadingTag($htagfield,$defaulthtag = 'h2'){
	$return = $defaulthtag;
	if($htagfield){
		$return = $htagfield['tag'];
	}
	return $return;
}
function swcGetLink($button){
	if(
	    is_array($button)
	        &&
	    isset($button['url'])
	 ){
	    $button['link'] = $button['url'];		
	    if (empty($button['title'])) {
	        $button['label'] = 'Explore';
	    }
		 else
		 {
			 $button['label'] = $button['title'];
		 }
	    if ($button['target'] == '_blank') {
	        $button['target'] = ' target="_blank" ';
	    }else {
	        $button['target'] = false;
	    }
	}else{
	    $button = false;
	}
	
return $button;
}

function char_animation_text_shortcode( $atts, $content = null ) {
	return '<span class="char-animation" data-splitting>' . $content . '</span>';
}
add_shortcode( 'animation', 'char_animation_text_shortcode' );

function wipe_from_left_text_shortcode( $atts, $content = null ) {
	return '<em class="wow wipeFromLeft" data-wow-duration="1.5s" data-wow-delay="1s">' . $content . '</em>';
}
add_shortcode( 'wipeFromLeft', 'wipe_from_left_text_shortcode' );
function strong_text_shortcode( $atts, $content = null ) {
	return '<strong>' . $content . '</strong>';
}
add_shortcode( 'strong', 'strong_text_shortcode' );

add_filter('gform_submit_button', 'tf_form_submit_button', 10, 2, 3);
function tf_form_submit_button($button, $form)
{
	$buttonArr = explode("value='",$button);
	$buttonArr = explode("'",$buttonArr[1]);
	$buttonText = $buttonArr[0];
       return '<button name="submit" class="gform_button button" id="gform_submit_button_'.$form['id'].'" value="submit">'.$buttonText.'</button>';
}


function getSearchExcludedPages(){
	$search_page = get_field('search_page', 'option');
	$search_error_page = get_field('search_error_page', 'option');
	$page_not_found_page = get_field('nfpage', 'option');
	return array($search_page, $search_error_page, $page_not_found_page);
}

add_filter( 'pre_get_posts', 'exclude_pages_search_when_logged_in' );
function exclude_pages_search_when_logged_in($query) {
		if ( $query->is_search && !is_admin()){
			$excluded = getSearchExcludedPages();
			$query->set( 'post__not_in', $excluded ); 
		}
    return $query;
}

add_filter( 'pre_get_posts', 'set_post_per_page_for_search_result' );
function set_post_per_page_for_search_result($query) {
		if(isset($_GET['submit']) && $_GET['submit'] == 'submit'){
			if ( $query->is_search && !is_admin()){ 
				$query->set( 'posts_per_page', 5 ); 
			}
		}		
    return $query;
}

function swcTruncatedExcerpt($excerpt,$length=125){
	$excerpt = wp_kses($excerpt,false);

	if(strlen($excerpt) > ($length+1)){
		$excerpt = substr($excerpt,0,$length);
		$expExcrpt = explode(' ',$excerpt);
		if(is_array($expExcrpt) && count($expExcrpt) > 0){
			foreach($expExcrpt as $key=>$ep){
				if(empty($ep)){
					unset($expExcrpt[$key]);
				}
			}
			$expExcrpt = array_values($expExcrpt);
			unset($expExcrpt[count($expExcrpt)-1]);
			if(is_array($expExcrpt) && count($expExcrpt) > 0){
				$excerpt = implode(' ',$expExcrpt);
			}
		}
		$excerpt .= '&hellip;';
	}
	
	return $excerpt;
}
/**
 * Reversing the wpautop function from Wysiwyg Editor or other Editors
 */
function reverse_wpautop( $string = '' ) {
    /* return if string is empty */
    if ( trim( $string ) === '' )
      return '';
    /* remove all new lines &amp; <p> tags */
    $string = str_replace( array( "\n", "<p>" ), "", $string );
    /* replace <br /> with \r */
    $string = str_replace( array( "<br />", "<br>", "<br/>" ), "\r", $string );
    /* replace </p> with \r\n */
    $string = str_replace( "</p>", "\r\n", $string );
    /* return clean string */
    return trim( $string );        
  }
/**
 * Remove Gutenberg Block Library CSS from loading on the frontend
 */
function swc_remove_wp_block_library_css(){
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
   } 
add_action( 'wp_enqueue_scripts', 'swc_remove_wp_block_library_css', 9999 );
function swcBreadcrumb($post_id){
	global $post;
	$return = false;
	$curPost = get_post($post_id);
	if(is_object($curPost)){
		$return .= '<li>';
		if($curPost->ID != $post->ID){
			$return .= '<a href="'.get_permalink($curPost->ID).'">';
		}
		$return .= $curPost->post_title;
		if($curPost->ID != $post->ID){
			$return .= '</a>';
		}
		$return .= '</li>';

		if($curPost->post_parent > 0)
			$return = swcBreadcrumb($curPost->post_parent).$return;
	}
	return $return;
}
function add_slug_body_class( $classes ) {
	global $post;
	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );
// Use the custom walker in your theme
/*function register_custom_menu_walker($args) {
    $args['walker'] = new Custom_Walker_Nav_Menu();
    return $args;
}
add_filter('wp_nav_menu_args', 'register_custom_menu_walker');*/

/**
 * Whise API AJAX endpoints
 */

// AJAX endpoint for getting estates
add_action('wp_ajax_get_estates', 'whise_get_estates_ajax');
add_action('wp_ajax_nopriv_get_estates', 'whise_get_estates_ajax');

function whise_get_estates_ajax() {
    $whise = new WhiseAPI();
    
    $filters = [];
    
    // Get filter parameters
    if (isset($_POST['purpose']) && !empty($_POST['purpose'])) {
        $filters['PurposeId'] = intval($_POST['purpose']);
    }
    
    if (isset($_POST['city']) && !empty($_POST['city'])) {
        $filters['City'] = sanitize_text_field($_POST['city']);
    }
    
    if (isset($_POST['category']) && !empty($_POST['category'])) {
        $filters['CategoryId'] = intval($_POST['category']);
    }
    
    if (isset($_POST['price_min']) && !empty($_POST['price_min'])) {
        $filters['PriceMin'] = intval($_POST['price_min']);
    }
    
    if (isset($_POST['price_max']) && !empty($_POST['price_max'])) {
        $filters['PriceMax'] = intval($_POST['price_max']);
    }
    
    // Debug logging
    error_log('Whise API - Filters: ' . print_r($filters, true));
    
    $estates = $whise->get_estates($filters);
    
    // Debug logging
    error_log('Whise API - Response: ' . print_r($estates, true));
    
    if ($estates && isset($estates['estates'])) {
        error_log('Whise API - Found ' . count($estates['estates']) . ' estates');
        wp_send_json_success($estates['estates']);
    } else {
        error_log('Whise API - No estates found or error');
        wp_send_json_error('No estates found');
    }
}

// AJAX endpoint for getting filter options
add_action('wp_ajax_get_filter_options', 'whise_get_filter_options_ajax');
add_action('wp_ajax_nopriv_get_filter_options', 'whise_get_filter_options_ajax');

function whise_get_filter_options_ajax() {
    $whise = new WhiseAPI();
    
    $options = [
        'purposes' => $whise->get_static_data('purpose'),
        'categories' => $whise->get_static_data('category'),
        'price_ranges' => $whise->get_static_data('price_ranges'),
        'cities' => []
    ];
    
    // Get cities from API
    $cities_data = $whise->get_cities();
    if ($cities_data && isset($cities_data['cities'])) {
        $options['cities'] = $cities_data['cities'];
    }
    
    wp_send_json_success($options);
}

// Enqueue scripts for Whise API
function whise_enqueue_scripts() {
    wp_enqueue_script('whise-api', get_template_directory_uri() . '/js/whise-api.js', array('jquery'), '1.0.0', true);
    wp_localize_script('whise-api', 'whise_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('whise_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'whise_enqueue_scripts');

