<?php
add_action('acf/init',function(){
	if( function_exists('acf_register_block') ) {
		
		add_action('block_categories',function($categories) {
			$return = array(
						array(
							'slug'  => 'immoley',
							'title' => __('Immo Ley','immoley')
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
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'align-pull-right',
			'keywords'			=> array(
									'hero',
									'immoley' 
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

		// ######## Register Text Section Block ########
		
		acf_register_block(array(
			'name'				=> 'text-section',
			'title'				=> __('Text Section'),
			'description'		=> __('A Text Section Block for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'media-text',
			'keywords'			=> array(
									'text section',
									'immoley' 
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


		// ######## Register Filter With Grid Block ########
		
		acf_register_block(array(
			'name'				=> 'filter-with-grid',
			'title'				=> __('Filter With Grid'),
			'description'		=> __('A Filter With Grid Block for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'filter',
			'keywords'			=> array(
									'filter grid',
									'immoley' 
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



		// ######## Register Side Image With Content Block ########
		
		acf_register_block(array(
			'name'				=> 'side-image-with-content-block',
			'title'				=> __('Side Image With Content Block'),
			'description'		=> __('A Side Image With Content Block for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'media-document',
			'keywords'			=> array(
									'side image content block',
									'immoley' 
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


		// ######## Register Heading With Text Section Block ########
		
		acf_register_block(array(
			'name'				=> 'heading-with-text-section',
			'title'				=> __('Heading With Text Section'),
			'description'		=> __('A Heading With Text Section for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'text-page',
			'keywords'			=> array(
									'heading text section',
									'immoley' 
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


		// ######## Register Page Title Block ########
		
		acf_register_block(array(
			'name'				=> 'page-title-block',
			'title'				=> __('Page Title Block'),
			'description'		=> __('A Page Title Block for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'welcome-write-blog',
			'keywords'			=> array(
									'title block',
									'immoley' 
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


		// ######## Register Blog Listing Block ########
		
		acf_register_block(array(
			'name'				=> 'blog-listing',
			'title'				=> __('Blog Listing'),
			'description'		=> __('A Blog Listing for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'admin-page',
			'keywords'			=> array(
									'blog listing',
									'immoley' 
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


		// ######## Register Project Single Block ########
		
		acf_register_block(array(
			'name'				=> 'single-project',
			'title'				=> __('Project Single'),
			'description'		=> __('A Project Single for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'admin-home',
			'keywords'			=> array(
									'project single',
									'immoley' 
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


		// ######## Overview Content With Form Block ########
		
		acf_register_block(array(
			'name'				=> 'overview-content-with-form',
			'title'				=> __('Overview Content With Form'),
			'description'		=> __('A Overview Content With Form for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'list-view',
			'keywords'			=> array(
									'overview content form',
									'immoley' 
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


		// ######## Contact Form Block ########
		
		acf_register_block(array(
			'name'				=> 'contact-form',
			'title'				=> __('Contact Form'),
			'description'		=> __('A Contact Form for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'feedback',
			'keywords'			=> array(
									'contact form',
									'immoley' 
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

		// ######## Three Column Heading With Content Block ########
		
		acf_register_block(array(
			'name'				=> 'three-column-heading-with-content',
			'title'				=> __('Three Column Heading With Content'),
			'description'		=> __('A Three Column Heading With Content for All of Your Pages.'),
			'render_callback'	=> 'immoley_block_render',
			'category'			=> 'immoley',
			'icon'				=> 'media-spreadsheet',
			'keywords'			=> array(
									'three column heading with content',
									'immoley' 
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

		
		function immoley_block_render( $block ) {
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
				if(file_exists(get_template_directory().'/css/immoley-global-admin.css')){
					wp_register_style(
						'immoley-global',
						get_bloginfo('stylesheet_directory').'/css/immoley-global-admin.css',
						false, 
						filemtime(get_template_directory().'/css/immoley-global-admin.css'), 
						'all' 
					);
					wp_enqueue_style('immoley-global');
				}
				/*if(file_exists(get_template_directory().'/js/swiper.js')){
					wp_register_script(
						'immoley-swiper-admin',
						get_bloginfo('stylesheet_directory').'/js/swiper.js',
						array(), 
						filemtime(get_template_directory().'/js/swiper.js'),
						true
					);
					wp_enqueue_script( 'immoley-swiper-admin' );
				}*/
		    }
		});
	}
	
});
