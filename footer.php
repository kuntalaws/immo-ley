<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @package WordPress
 * @subpackage RECOVERY_FRIENDLY_WORKPLACE
 */
$flogo = intval(get_field('footerlogo','option'));
$flogo = swcGetImage($flogo,150,150,true,true);
$footerbottomlogofirst = intval(get_field('footerbottomlogofirst','option'));
$footerbottomlogofirst = swcGetImage($footerbottomlogofirst,null,null,true,true);
$footerbottomlogosecond = intval(get_field('footerbottomlogosecond','option'));
$footerbottomlogosecond = swcGetImage($footerbottomlogosecond,null,null,true,true);
$footerbottomlogothird = intval(get_field('footerbottomlogothird','option'));
$footerbottomlogothird = swcGetImage($footerbottomlogothird,null,null,true,true);

$disclaimer = trim(get_field('footer_disclaimer','option'));
$ilphone = trim(get_field('ilphone','option'));
$ilemail = trim(get_field('ilemail','option'));
$address = trim(get_field('address','option'));

$footerlegalmenu = wp_nav_menu(
  array(
    'theme_location'=>'footer_legal_menu',
    'menu_class'=>'flex',
    'container'=>false,
    'echo'=>false
  )
);
?>
    </div>
    <!--Container End Here-->
    <!--Footer Start Here-->
    <footer class="footer">
        <div class="footer-in fw">
            <div class="footer-top-row flex">
            <div class="footer-top-row-left">
                <?php if(!empty($flogo)){?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo">
                        <img <?php echo $flogo['attrs']['class'];?> <?php echo $flogo['attrs']['src'];?>="<?php echo esc_url($flogo['url']);?>" alt="<?php echo esc_attr($flogo['alt']); ?>" width="<?php echo esc_attr($flogo['width']); ?>" height="<?php echo esc_attr($flogo['height']); ?>">
                    </a>
                <?php }?>
            </div>
            <div class="footer-top-row-right">
                <?php if(is_active_sidebar('footer-nav-columns')){
                    dynamic_sidebar('footer-nav-columns');
                }
                if(!empty($address)){?>
                    <div class="footer-menu">
                        <h3>Adres </h3>
                        <div class="footer-address">
                            <p><?php echo $address;?></p>                         
                        </div>
                    </div>
                <?php }
                if(!empty($ilphone) || !empty($ilemail)){?>
                    <div class="footer-contact-info">
                        <h3>Contacteer ons </h3>
                        <ul>
                            <?php if(!empty($ilphone)){?>
                                <li> <a href="tel:<?php echo $ilphone;?>"><?php echo $ilphone;?></a> </li>   
                            <?php }
                            if(!empty($ilemail)){?> 
                                <li> <a href="mailto:<?php echo $ilemail;?>"><?php echo $ilemail;?></a> </li>     
                            <?php }?>                 
                        </ul>
                    </div>
                <?php } ?>
            </div>
            </div>
            <div class="footer-bottom-row flex">
                <div class="footer-bottom-row-left">
                    <?php if(!empty($disclaimer)){?>
                        <div class="footer-credit"><?php echo do_shortcode(wpautop($disclaimer));?></div>
                    <?php }
                    if(!empty($footerlegalmenu)){?>
                        <div class="footer-bottom-menu">
                            <?php echo $footerlegalmenu;?>
                        </div>
                    <?php } ?>
                </div>
                <div class="footer-bottom-row-right">
                    <div class="footer-bottom-logo-wrap flex">
                        <?php if(!empty($footerbottomlogofirst)){?>
                            <div class="footer-bottom-logo">
                                <img <?php echo $footerbottomlogofirst['attrs']['class'];?> <?php echo $footerbottomlogofirst['attrs']['src'];?>="<?php echo esc_url($footerbottomlogofirst['url']);?>" alt="<?php echo esc_attr($footerbottomlogofirst['alt']); ?>" width="<?php echo esc_attr($footerbottomlogofirst['width']); ?>" height="<?php echo esc_attr($footerbottomlogofirst['height']); ?>">
                            </div>
                        <?php }
                        if(!empty($footerbottomlogosecond)){?>
                            <div class="footer-bottom-logo">
                                <img <?php echo $footerbottomlogosecond['attrs']['class'];?> <?php echo $footerbottomlogosecond['attrs']['src'];?>="<?php echo esc_url($footerbottomlogosecond['url']);?>" alt="<?php echo esc_attr($footerbottomlogosecond['alt']); ?>" width="<?php echo esc_attr($footerbottomlogosecond['width']); ?>" height="<?php echo esc_attr($footerbottomlogosecond['height']); ?>">
                            </div>
                        <?php }
                        if(!empty($footerbottomlogothird)){?>
                            <div class="footer-bottom-logo">
                                <img <?php echo $footerbottomlogothird['attrs']['class'];?> <?php echo $footerbottomlogothird['attrs']['src'];?>="<?php echo esc_url($footerbottomlogothird['url']);?>" alt="<?php echo esc_attr($footerbottomlogothird['alt']); ?>" width="<?php echo esc_attr($footerbottomlogothird['width']); ?>" height="<?php echo esc_attr($footerbottomlogothird['height']); ?>">
                            </div>
                        <?php }?>
                    </div>
                        <?php if(have_rows('social_links_global','option')){?>
                            <div class="footer-social-icons">
                                <div class="footer-social-icons-text"><span>Volg ons op</span></div>
                                <ul class="social-menu flex">
                                    <?php 
                                    while(have_rows('social_links_global','option')){
                                        the_row();
                                        $icon = intval(get_sub_field('icon'));
                                        $url = trim(get_sub_field('link'));
                                        
                                        $icon = swcGetImage($icon,null,null,true,true);
                                    
                                        if($icon && !empty($url)){
                                        echo '<li>'.
                                            '<a aria-label="'.$icon['alt'].'" href="'.$url.'" target="_blank">'.
                                            '<img '.$icon['attrs']['src'].'="'.$icon['url'].'" alt="'.$icon['alt'].'" class="'.$icon['attrs']['class'].'" width="'.$icon['width'].'" height="'.$icon['height'].'" />'.
                                            '</a>'.
                                            '</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </footer>
    <!--Footer End Here-->
    <?php 
        wp_footer();
        // Include script snippets as defined on theme settings page
        if ( isset($include_at_bottom_of_body_tag) && !empty($include_at_bottom_of_body_tag) ) {
            echo $include_at_bottom_of_body_tag;
        }   
    ?>   
</body>
</html>