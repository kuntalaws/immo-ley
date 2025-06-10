<?php
add_action('acf/init',function(){
	if( function_exists('acf_register_block') ) {
		
		add_action('block_categories',function($categories) {
			$return = array(
						array(
							'slug'  => 'mattresstheme',
							'title' => __('Recovery Friendly Workplace','mattress')
						)
					  );
			$return = array_merge($return,$categories);

			return $return;
		},10,2);


		// ######## Register Hero Block ########
		
		acf_register_block(array(
			'name'				=> 'hero',
			'title'				=> __('Hero'),
			'description'		=> __('A Hero Banner Block for All of Your Pages.'),
			'render_callback'	=> 'mattress_block_render',
			'category'			=> 'mattresstheme',
			'icon'				=> 'align-pull-right',
			'keywords'			=> array(
									'hero',
									'mattress' 
								   ),
			'example'  => array(
	            'attributes' => array(
	                'mode' => 'preview',
	                'data' => array(
	                	'_is_preview' => 'preview',
	                )
	            )
	        )
		));			

		// ######## End of Registration of All Custom Content Block ########

		
		function mattress_block_render( $block ) {
			global $contentRowsInPage,$curContIndex;
			
			$curContIndex++;
			
			$slug = str_replace('acf/', '', $block['name']);
			
			if(isset($block['data']['_is_preview']) && !empty($block['data']['_is_preview'])){
				echo '<img src="'.trailingslashit(get_bloginfo('stylesheet_directory')).'img/gb-block-previews/'.$slug.'.jpg" height="100%" width="100%" />';
			}else{
				if(file_exists( get_theme_file_path("/gb-blocks/{$slug}.php"))){
					$sectionID = trim(get_field('crowid_section_id'));
					if(!isset($contentRowsInPage[$slug])){
						$contentRowsInPage[$slug] = '';
					}
					if(empty($sectionID)){
						$sectionID = $slug.$contentRowsInPage[$slug];
					}
					echo '<div id="'.$sectionID.'">';
					include( get_theme_file_path("/gb-blocks/{$slug}.php") );
					echo '</div>';
					$contentRowsInPage[$slug] = intval($contentRowsInPage[$slug])+1;
				}
			}
		}
		
		add_action('admin_enqueue_scripts',function(){
			global $pagenow;
			if(
				$pagenow == 'post-new.php'
					||
				(
					$pagenow == 'post.php'
						&&
					intval($_GET['post']) > 0
						&&
					in_array(get_post_type($_GET['post']),array('page','post','block_templates'))
				)
			){
				if(file_exists(get_template_directory().'/css/mattress-global-admin.css')){
					wp_register_style(
						'mattress-global',
						get_bloginfo('stylesheet_directory').'/css/mattress-global-admin.css',
						false, 
						filemtime(get_template_directory().'/css/mattress-global-admin.css'), 
						'all' 
					);
					wp_enqueue_style('mattress-global');
				}
				/*if(file_exists(get_template_directory().'/js/swiper.js')){
					wp_register_script(
						'mattress-swiper-admin',
						get_bloginfo('stylesheet_directory').'/js/swiper.js',
						array(), 
						filemtime(get_template_directory().'/js/swiper.js'),
						true
					);
					wp_enqueue_script( 'mattress-swiper-admin' );
				}*/
		    }
		});
	}
	
});
