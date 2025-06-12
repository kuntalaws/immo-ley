<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @package WordPress
 * @subpackage RECOVERY_FRIENDLY_WORKPLACE
 */
$logo = intval(get_field('logo','option'));
$logo = swcGetImage($logo,null,null,false,true);
$ilphone = trim(get_field('ilphone','option'));
$ilemail = trim(get_field('ilemail','option'));
$primaryNav = wp_nav_menu(
  array(
    'theme_location'=>'primary_nav',
    'menu_class'=>'mainmenu flex',
    'container'=>false,
    'echo'=>false
  )
);
$include_in_head_tag = trim(get_field('include_in_head_tag', 'option'));
$include_at_top_of_body_tag = trim(get_field('include_at_top_of_body_tag', 'option'));

?>
<!doctype html>
<html class="no-js" lang="en-US">
<head>
    <meta charset="utf-8">
    <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/rpc6tye.css">

  <?php 
      // Include script snippets as defined on theme settings page
      if ( $include_in_head_tag && !empty($include_in_head_tag) ) { 
        echo $include_in_head_tag;
      }      
  ?>
    
    <style>
        <?php
            if(file_exists(plugin_dir_path(__FILE__).'/css/main.css')){
                include(plugin_dir_path(__FILE__).'/css/main.css');
            }
            if(file_exists(plugin_dir_path(__FILE__).'/css/footer.css')){
                include(plugin_dir_path(__FILE__).'/css/footer.css');
            }
        ?>    
    </style>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="wrapper">  
        <!--Header Start Here-->
        <header class="header">
            <div class="header-in fw flex">
                <?php if(!empty($logo)){?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php printf(esc_attr__('%1$s - %2$s', 'immo-ley'), get_bloginfo('name'), get_bloginfo('description')); ?>" class="logo">
                        <img <?php echo $logo['attrs']['class'];?> <?php echo $logo['attrs']['src'];?>="<?php echo esc_url($logo['url']);?>" alt="<?php echo esc_attr($logo['alt']); ?>" width="<?php echo esc_attr($logo['width']); ?>" height="<?php echo esc_attr($logo['height']); ?>">
                    </a>
                <?php }?>
                <div class="header-nav-wrap">
                    <?php if(!empty($primaryNav)){?>
                        <nav class="nav">
                            <?php echo $primaryNav;?>
                        </nav>
                    <?php }
                    if(!empty($ilphone) || !empty($ilemail)){?>
                    <div class="header-contact-info">
                        <?php if(!empty($ilphone)){?>
                            <h6><?php echo $ilphone;?></h6>
                        <?php }
                        if(!empty($ilemail)){?>
                            <h6><a href="mailto:<?php echo $ilemail;?>"><?php echo $ilemail;?></a></h6>
                        <?php }?>
                    </div>
                    <?php }?>
                </div>
                <span class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </div>
        </header>
        <!--Header End Here-->
        <!--Container Start Here-->
        <div class="container">
