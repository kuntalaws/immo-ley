<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$rows = get_field('rows');
if(empty($rows) && is_admin()){
    $rows = array(
        array(
            'heading'=>'Heading goes here..',
            'image'=>array(
                'alt'=>'',
                'title'=>'',
                'url'=>'https://via.placeholder.com/1920x1080/e8e8e8/566C47/?text=Placeholder',
                'width'=>1920,
                'height'=>1080,
                'attrs'=>array(
                                'class' => '',
                                'src' => 'src'
                            )
            ),
            'content'=>'Content goes here..'
        )
    );
}
if(!empty($rows) && is_array($rows) && count($rows) > 0){
    $count = 1;
    foreach($rows as $row){
        $heading = trim($row['heading']);
        $image = intval($row['image']);
        // $imageTab = swcGetImage($image,768,NULL,true,true);
        $imageMob = swcGetImage($image,1024,NULL,true,true);
        $image = swcGetImage($image,455,532,true,true);
        $content = trim($row['content']);
        if(empty($heading) && is_admin()){
            $heading = "Heading goes here..";
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
        if($count % 2 == 0){
            $side = 'right';
        }else{
            $side = 'left';
        }
    ?>
    <div class="side-image-with-content-block <?php echo $side;?>">
        <div class="side-image-with-content-wrap fw">
            <div class="side-image-with-content-block-in flex">
                <?php if(!empty($image)){?>
                    <div class="side-image-with-content-block-img">
                        <img loading="lazy" src="<?php echo $image['url'];?>" alt="<?php echo $image['alt'];?>" title="<?php echo $image['title'];?>" width="<?php echo $image['width'];?>" height="<?php echo $image['height'];?>">
                    </div>
                <?php }
                if(!empty($heading) || !empty($content)){ ?>
                <div class="side-image-with-content-block-content">
                    <?php if(!empty($heading)){?>
                        <h2><?php echo $heading;?></h2>
                    <?php }
                    if(!empty($content)){
                        echo apply_filters('the_content', $content);
                    }?>
                </div>
                <?php }?>
            </div>
        </div>
    </div>
    <?php 
        $count++;
    }

	if(intval($contentRowsInPage['side-image-with-content-block']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/side-image-with-content-block.css')){
			echo '<style>';
		include(get_template_directory().'/css/side-image-with-content-block.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['side-image-with-content-block'] = intval($contentRowsInPage['side-image-with-content-block'])+1;
}
