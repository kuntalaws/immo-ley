<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;
$left_title = trim(get_field('left_title'));
$left_overview = trim(get_field('left_overview'));
$left_button = get_field('left_button');
$image = intval(get_field('image'));

// Process the image using swcGetImage function
$image = swcGetImage($image, 455, 532, true, true);

// Process the button using swcGetLink function
$left_button = swcGetLink($left_button);

if(empty($left_title) && is_admin()){
	$left_title = "Heading goes here..";
}
if(empty($left_overview) && is_admin()){
	$left_overview = "Overview goes here..";
}
if(!$left_button && is_admin()){
	$left_button = array('link'=>'#','target'=>'','label'=>'Button Label');
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

// Initialize Whise API
$whise = new WhiseAPI();
$filter_options = [];

// Get filter options for initial population
if ($whise) {
    $filter_options['purposes'] = $whise->get_static_data('purpose');
    $filter_options['categories'] = $whise->get_static_data('category');
    $filter_options['price_ranges'] = $whise->get_static_data('price_ranges');
    
    $cities_data = $whise->get_cities();
    if ($cities_data && isset($cities_data['cities'])) {
        $filter_options['cities'] = $cities_data['cities'];
    }
}

// Get current filter values from URL parameters - support multiple values
$current_purposes = [];
$current_cities = [];
$current_categories = [];
$current_price_ranges = [];

// Handle both URL parameters and form submission arrays
if (isset($_GET['purpose'])) {
    if (is_array($_GET['purpose'])) {
        $current_purposes = array_map('sanitize_text_field', $_GET['purpose']);
    } else {
        $current_purposes = [sanitize_text_field($_GET['purpose'])];
    }
}

if (isset($_GET['city'])) {
    if (is_array($_GET['city'])) {
        $current_cities = array_map('sanitize_text_field', $_GET['city']); // now zip codes
    } else {
        $current_cities = [sanitize_text_field($_GET['city'])];
    }
}

if (isset($_GET['category'])) {
    if (is_array($_GET['category'])) {
        $current_categories = array_map('sanitize_text_field', $_GET['category']);
    } else {
        $current_categories = [sanitize_text_field($_GET['category'])];
    }
}

if (isset($_GET['price_range'])) {
    if (is_array($_GET['price_range'])) {
        $current_price_ranges = array_map('sanitize_text_field', $_GET['price_range']);
    } else {
        $current_price_ranges = [sanitize_text_field($_GET['price_range'])];
    }
}

// Debug logging
error_log('Whise Filter Debug - URL Parameters: ' . print_r($_GET, true));
error_log('Whise Filter Debug - Current values: purposes=' . print_r($current_purposes, true) . ', cities=' . print_r($current_cities, true) . ', categories=' . print_r($current_categories, true) . ', price_ranges=' . print_r($current_price_ranges, true));

// Additional debugging for form submission
if (isset($_GET['purpose']) || isset($_GET['city']) || isset($_GET['category'])) {
    error_log('Whise Filter Debug - Form submission detected');
    error_log('Whise Filter Debug - Purpose values: ' . print_r($_GET['purpose'] ?? 'not set', true));
    error_log('Whise Filter Debug - City values: ' . print_r($_GET['city'] ?? 'not set', true));
    error_log('Whise Filter Debug - Category values: ' . print_r($_GET['category'] ?? 'not set', true));
}

// Build filters array for API call
$filters = [];
if (!empty($current_purposes)) {
    $filters['PurposeIds'] = array_map('intval', $current_purposes);
}
if (!empty($current_cities)) {
    // $current_cities now contains zip codes directly
    $filters['ZipCodes'] = $current_cities;
    error_log('Whise Filter Debug - Using ZipCodes for cities: ' . print_r($current_cities, true));
}
if (!empty($current_categories)) {
    $filters['CategoryIds'] = array_map('intval', $current_categories);
}

// Handle multiple price ranges
if (!empty($current_price_ranges)) {
    $all_min_prices = [];
    $all_max_prices = [];
    
    foreach ($current_price_ranges as $range) {
        $parts = explode('-', $range);
        if (count($parts) >= 2) {
            $min = trim($parts[0]);
            $max = trim($parts[1]);
            
            if (!empty($min) && $min !== '') {
                $all_min_prices[] = intval($min);
            }
            if (!empty($max) && $max !== '') {
                $all_max_prices[] = intval($max);
            }
        }
    }
    
    // Create a comprehensive price range that covers all selected ranges
    if (!empty($all_min_prices) || !empty($all_max_prices)) {
        $filters['PriceRange'] = [];
        if (!empty($all_min_prices)) {
            $filters['PriceRange']['Min'] = min($all_min_prices);
        }
        if (!empty($all_max_prices)) {
            $filters['PriceRange']['Max'] = max($all_max_prices);
        }
    }
}

error_log('Whise Filter Debug - Built filters: ' . print_r($filters, true));

// Get estates based on current filters
$estates = [];
if ($whise) {
    $estates_data = $whise->get_estates($filters);
    error_log('Whise Filter Debug - API Response: ' . print_r($estates_data, true));
    if ($estates_data && isset($estates_data['estates'])) {
        $estates = $estates_data['estates'];
        error_log('Whise Filter Debug - Found ' . count($estates) . ' estates');
    } else {
        error_log('Whise Filter Debug - No estates found or API error');
    }
}

// Get current page URL for form action
$current_url = get_permalink();

// Temporary debug display (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc; font-family: monospace; font-size: 12px;">';
    echo '<h3>🔍 Whise Filter Debug Info:</h3>';
    echo '<p><strong>URL Parameters:</strong> ' . print_r($_GET, true) . '</p>';
    echo '<p><strong>Current Values:</strong></p>';
    echo '<ul>';
    echo '<li>Purposes: ' . print_r($current_purposes, true) . '</li>';
    echo '<li>Cities: ' . print_r($current_cities, true) . '</li>';
    if (isset($filters['ZipCodes'])) {
        echo '<li>Zip Codes: ' . print_r($filters['ZipCodes'], true) . '</li>';
    } elseif (isset($filters['City'])) {
        echo '<li>Single City: ' . $filters['City'] . '</li>';
    }
    echo '<li>Categories: ' . print_r($current_categories, true) . '</li>';
    if (!empty($current_price_ranges)) {
        echo '<li>Price Ranges: ' . print_r($current_price_ranges, true) . '</li>';
    }
    echo '</ul>';
    
    // Show available cities from API
    if (isset($filter_options['cities']) && is_array($filter_options['cities'])) {
        echo '<p><strong>Available Cities from API:</strong></p>';
        echo '<ul>';
        foreach (array_slice($filter_options['cities'], 0, 10) as $city) { // Show first 10 cities
            echo '<li>' . esc_html($city['name']) . ' (Zip: ' . esc_html($city['zip']) . ')</li>';
        }
        if (count($filter_options['cities']) > 10) {
            echo '<li>... and ' . (count($filter_options['cities']) - 10) . ' more cities</li>';
        }
        echo '</ul>';
    } else {
        echo '<p><strong>No cities available from API</strong></p>';
    }
    
    echo '<p><strong>Built Filters:</strong> ' . print_r($filters, true) . '</p>';
    echo '<p><strong>API Response:</strong> ' . print_r($estates_data, true) . '</p>';
    echo '<p><strong>Estates Count:</strong> ' . count($estates) . '</p>';
    echo '</div>';
    
    // Add test links to demonstrate multiple selections
    echo '<div style="background: #e8f4f8; padding: 10px; margin: 10px; border: 1px solid #4CAF50; font-family: monospace; font-size: 12px;">';
    echo '<h3>🧪 Test Multiple Selections:</h3>';
    echo '<p><strong>Test Links:</strong></p>';
    echo '<ul>';
    echo '<li><a href="' . add_query_arg(['purpose' => ['1', '2'], 'category' => ['1', '4'], 'city' => ['brussels', 'antwerp'], 'price_min' => '500000', 'price_max' => '1000000'], $current_url) . '">Multiple purposes + categories + multiple cities + price</a></li>';
    echo '<li><a href="' . add_query_arg(['purpose' => '1', 'city' => 'brussels'], $current_url) . '">Single purpose + city</a></li>';
    echo '<li><a href="' . add_query_arg(['category' => '4', 'price_min' => '1000000'], $current_url) . '">Single category + price min</a></li>';
    echo '<li><a href="' . add_query_arg(['purpose' => '1', 'category' => '4', 'city' => ['brussels', 'ghent'], 'price_min' => '500000', 'price_max' => '1500000'], $current_url) . '">All filters with multiple cities</a></li>';
    echo '</ul>';
    echo '</div>';
}
?>
<!--Filter With Grid Start Here-->
<section class="filter-with-grid">
	<form method="GET" action="<?php echo esc_url($current_url); ?>" class="filter-form">
		<div class="filter-row">
			<div class="filter-row-in fw flex">
				<div class="filter-item">
					<div class="filter-item-wrap">
						<input type="text" placeholder="Te koop" value="" class="select-input" data-select="purpose">
						<ul name="purpose" class="select-options whise-purpose-select" data-select="purpose" multiple>
							<?php if (isset($filter_options['purposes'])): ?>
								<?php foreach ($filter_options['purposes'] as $purpose): ?>
									<li value="<?php echo esc_attr($purpose['id']); ?>" <?php echo in_array($purpose['id'], $current_purposes) ? 'class="selected" style="display: none;"' : ''; ?>><?php echo esc_html($purpose['name']); ?></li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
					<div class="selected-tags" data-selected="purpose">
						<?php foreach ($current_purposes as $purpose_id): ?>
							<?php 
							$purpose_name = '';
							foreach ($filter_options['purposes'] as $purpose) {
								if ($purpose['id'] == $purpose_id) {
									$purpose_name = $purpose['name'];
									break;
								}
							}
							if ($purpose_name): ?>
								<div class="selected-tag" data-value="<?php echo esc_attr($purpose_id); ?>">
									<span><?php echo esc_html($purpose_name); ?></span>
									<div class="remove-tag" data-value="<?php echo esc_attr($purpose_id); ?>" data-type="purpose">×</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="filter-item">
					<div class="filter-item-wrap">
						<input type="text" placeholder="Gemeente" value="" class="select-input" data-select="city">
						<ul name="city" class="select-options whise-city-select" data-select="city" multiple>
							<?php if (isset($filter_options['cities']) && !empty($filter_options['cities'])): ?>
								<?php foreach ($filter_options['cities'] as $city): ?>
									<li value="<?php echo esc_attr($city['zip']); ?>" <?php echo in_array($city['zip'], $current_cities) ? 'class="selected" style="display: none;"' : ''; ?>>
										<?php echo mb_convert_case($city['name'], MB_CASE_TITLE, 'UTF-8'); ?>
									</li>
								<?php endforeach; ?>
							<?php else: ?>
								<li style="color: #999; font-style: italic;">Geen steden beschikbaar</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="selected-tags" data-selected="city">
						<?php foreach ($current_cities as $city_zip): ?>
							<?php 
							$city_name = '';
							// Look up the city name from the zip code
							if (isset($filter_options['cities']) && is_array($filter_options['cities'])) {
								foreach ($filter_options['cities'] as $city) {
									if ($city['zip'] == $city_zip) {
										$city_name = mb_convert_case($city['name'], MB_CASE_TITLE, 'UTF-8');
										break;
									}
								}
							}
							// Fallback to zip code if city name not found
							if (empty($city_name)) {
								$city_name = $city_zip;
							}
							?>
							<div class="selected-tag" data-value="<?php echo esc_attr($city_zip); ?>">
								<span><?php echo esc_html($city_name); ?></span>
								<div class="remove-tag" data-value="<?php echo esc_attr($city_zip); ?>" data-type="city">×</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="filter-item">
					<div class="filter-item-wrap">
						<input type="text" placeholder="Type" value="" class="select-input" data-select="category">
						<ul name="category" class="select-options whise-category-select" data-select="category" multiple>
							<?php if (isset($filter_options['categories'])): ?>
								<?php foreach ($filter_options['categories'] as $category): ?>
									<li value="<?php echo esc_attr($category['id']); ?>" <?php echo in_array($category['id'], $current_categories) ? 'class="selected" style="display: none;"' : ''; ?>><?php echo esc_html($category['name']); ?></li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
					<div class="selected-tags" data-selected="category">
						<?php foreach ($current_categories as $category_id): ?>
							<?php 
							$category_name = '';
							foreach ($filter_options['categories'] as $category) {
								if ($category['id'] == $category_id) {
									$category_name = $category['name'];
									break;
								}
							}
							if ($category_name): ?>
								<div class="selected-tag" data-value="<?php echo esc_attr($category_id); ?>">
									<span><?php echo esc_html($category_name); ?></span>
									<div class="remove-tag" data-value="<?php echo esc_attr($category_id); ?>" data-type="category">×</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="filter-item">
					<div class="filter-item-wrap">
						<input type="text" placeholder="Prijs" value="" class="select-input" data-select="price_range">
						<ul name="price_range" class="select-options whise-price-range" data-select="price_range">
							<?php if (isset($filter_options['price_ranges'])): ?>
								<?php foreach ($filter_options['price_ranges'] as $range): ?>
									<?php 
									$range_value = $range['min'] . '-' . ($range['max'] ?: '');
									$current_range = '';
									if (!empty($current_price_ranges)) {
										$current_range = $current_price_ranges[0];
									}
									$is_selected = ($current_range === $range_value);
									?>
									<li value="<?php echo esc_attr($range_value); ?>" <?php echo $is_selected ? 'class="selected" style="display: none;"' : ''; ?>><?php echo esc_html($range['label']); ?></li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
					<div class="selected-tags" data-selected="price_range">
						<?php 
						// Display selected price ranges from form submission
						if (!empty($current_price_ranges)): 
							foreach ($current_price_ranges as $range): 
								$parts = explode('-', $range);
								$min = trim($parts[0] ?? '');
								$max = trim($parts[1] ?? '');
								
								$price_range_text = '';
								if (!empty($min) && !empty($max)) {
									$price_range_text = '€' . number_format(intval($min), 0, ',', '.') . ' - €' . number_format(intval($max), 0, ',', '.');
								} elseif (!empty($min)) {
									$price_range_text = '€' . number_format(intval($min), 0, ',', '.') . '+';
								} elseif (!empty($max)) {
									$price_range_text = 'Tot €' . number_format(intval($max), 0, ',', '.');
								}
								
								if ($price_range_text): ?>
									<div class="selected-tag" data-value="<?php echo esc_attr($range); ?>">
										<span><?php echo esc_html($price_range_text); ?></span>
										<div class="remove-tag" data-value="<?php echo esc_attr($range); ?>" data-type="price_range">×</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="filter-item">
					<!-- <h5>Zoeken</h5> -->
					<button type="submit" class="whise-search-btn btn"><span>Zoeken</span></button>
				</div>
			</div>
		</div>
	</form>
	<div class="filter-grid">
		<div class="filter-grid-title fw">
			<h3>Een greep uit ons aanbod</h3>
		</div>
		<div class="filter-grid-item-wrapper">
			<!--Show First 4 Items-->
			<div class="filter-grid-item-wrap fw flex" id="whise-estates-container">
				<?php if (!empty($estates)): ?>
					<?php 
					$first_four_estates = array_slice($estates, 0, 4);
					foreach ($first_four_estates as $estate): ?>
						<?php
						$imageUrl = $estate['pictures'] && count($estate['pictures']) > 0 
							? $estate['pictures'][0]['urlLarge'] 
							: get_template_directory_uri() . '/img/grid-item-img-01.jpg';
						
						$price = $estate['price'] ? '€ ' . number_format($estate['price'], 0, ',', '.') : 'Prijs op aanvraag';
						$city = $estate['city'] ?? 'Onbekend';
						$title = $estate['name'] ?? ($estate['shortDescription'] && count($estate['shortDescription']) > 0 ? $estate['shortDescription'][0]['content'] : 'Eigendom');
						?>
						<a href="<?php echo esc_url(add_query_arg('estate_id', $estate['id'], get_permalink(get_page_by_path('single-project')))); ?>" class="filter-grid-item" data-estate-id="<?php echo esc_attr($estate['id']); ?>">
							<div class="filter-grid-item-img">
                                <?php                                    
                                    $status_class = '';
                                    $status_label = '';
                                    if (isset($estate['purposeStatus']['id'])) {
                                        if ($estate['purposeStatus']['id'] == 3) {
                                            $status_class = 'sold';
                                        }
                                        
                                        if (function_exists('get_whise_status_by_id')) {
                                            $status_data = get_whise_status_by_id($estate['purposeStatus']['id']);
                                            $status_label = isset($status_data['name']) ? $status_data['name'] : 'Status';
                                        } else {
                                            $status_label = 'Status';
                                        }
                                    }
                                ?>
                                <?php if ($status_label): ?>
                                    <span class="pro-type<?php echo $status_class ? ' ' . esc_attr($status_class) : ''; ?>"><?php echo esc_html($status_label); ?></span>
                                <?php endif; ?>
								<div class="filter-grid-item-img-box">
									<img loading="lazy" src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($title); ?>">
								</div>
							</div>
							<div class="filter-grid-item-info">
								<div class="filter-grid-item-info-in">
									<h6><span class="filter-grid-item-info-category"><?php echo esc_html($city); ?></span> / <span class="filter-grid-item-info-price"><?php echo esc_html($price); ?></span></h6>
									<h4><?php echo esc_html($title); ?></h4>
								</div>
							</div>
						</a>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="no-results">
						<div class="no-results-in">
                            <h4>Geen eigendommen gevonden</h4>
						    <p>Probeer andere zoekcriteria of neem contact met ons op.</p>
                        </div>
					</div>
				<?php endif; ?>
			</div>
			<?php if(!empty($left_title) || !empty($left_overview) || !empty($left_button) || !empty($image)){ ?>
			<div class="additional-info-row flex">
				<div class="additional-info-content-col">
					<div class="additional-info-content">
						<?php if(!empty($left_title)){ ?>
							<h2><?php echo wp_kses_post($left_title); ?></h2>
						<?php } ?>
						<?php if(!empty($left_overview)){ ?>
							<p><?php echo wp_kses_post($left_overview); ?></p>
						<?php } ?>
						<?php if(!empty($left_button)){ ?>
							<div class="button-wrap">
								<a href="<?php echo esc_url($left_button['link']); ?>"<?php echo $left_button['target']; ?> class="btn"><span><?php echo esc_html($left_button['label']); ?></span></a>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php if(!empty($image)){ ?>
					<div class="additional-info-item-img">
						<img loading="lazy" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" title="<?php echo esc_attr($image['title']); ?>" width="<?php echo esc_attr($image['width']); ?>" height="<?php echo esc_attr($image['height']); ?>">
					</div>
				<?php } ?>
			</div>
			<?php } ?>
			<!--Show Remaining Items After Additional Info-->
			<?php if (!empty($estates) && count($estates) > 4): ?>
			<div class="filter-grid-item-wrap fw flex" id="whise-estates-container-remaining">
				<?php 
				$remaining_estates = array_slice($estates, 4);
				foreach ($remaining_estates as $estate): ?>
					<?php
					$imageUrl = $estate['pictures'] && count($estate['pictures']) > 0 
						? $estate['pictures'][0]['urlLarge'] 
						: get_template_directory_uri() . '/img/grid-item-img-01.jpg';
					
					$price = $estate['price'] ? '€ ' . number_format($estate['price'], 0, ',', '.') : 'Prijs op aanvraag';
					$city = $estate['city'] ?? 'Onbekend';
					$title = $estate['name'] ?? ($estate['shortDescription'] && count($estate['shortDescription']) > 0 ? $estate['shortDescription'][0]['content'] : 'Eigendom');
					?>
					<a href="<?php echo esc_url(add_query_arg('estate_id', $estate['id'], get_permalink(get_page_by_path('single-project')))); ?>" class="filter-grid-item" data-estate-id="<?php echo esc_attr($estate['id']); ?>">
						<div class="filter-grid-item-img">
                            <?php
                                $status_class = '';
                                $status_label = '';
                                if (isset($estate['purposeStatus']['id'])) {
                                    if ($estate['purposeStatus']['id'] == 3) {
                                        $status_class = 'sold';
                                    }
                                    
                                    if (function_exists('get_whise_status_by_id')) {
                                        $status_data = get_whise_status_by_id($estate['purposeStatus']['id']);
                                        $status_label = isset($status_data['name']) ? $status_data['name'] : 'Status';
                                    } else {
                                        $status_label = 'Status';
                                    }
                                }
                            ?>
                            <?php if ($status_label): ?>
                                <span class="pro-type<?php echo $status_class ? ' ' . esc_attr($status_class) : ''; ?>"><?php echo esc_html($status_label); ?></span>
                            <?php endif; ?>
							<div class="filter-grid-item-img-box">
								<img loading="lazy" src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($title); ?>">
							</div>
						</div>
						<div class="filter-grid-item-info">
							<div class="filter-grid-item-info-in">
								<h6><span class="filter-grid-item-info-category"><?php echo esc_html($city); ?></span> / <span class="filter-grid-item-info-price"><?php echo esc_html($price); ?></span></h6>
								<h4><?php echo esc_html($title); ?></h4>
							</div>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
	if(intval($contentRowsInPage['filter-with-grid']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/filter-with-grid.css')){
			echo '<style>';
		include(get_template_directory().'/css/filter-with-grid.css');
		echo '</style>';
		}    
	}
	$contentRowsInPage['filter-with-grid'] = intval($contentRowsInPage['filter-with-grid'])+1;
?>

<script>
jQuery(document).ready(function($) {
    // Initialize filter functionality
    $('.select-input').each(function () {
        const $input = $(this);
        const key = $input.data('select');
        const $optionsList = $(`.select-options[data-select="${key}"]`);
        const $selectedContainer = $(`.selected-tags[data-selected="${key}"]`);
        const $form = $input.closest('form');

        // Hide dropdown initially
        $optionsList.hide();

        // Create hidden input fields for selected values
        if (!$form.find(`input[name="${key}[]"]`).length) {
            $form.append(`<input type="hidden" name="${key}[]" value="">`);
        }

        // Filter on input
        $input.on('input', function () {
            const term = $input.val().toLowerCase();
            $optionsList.show();
            $optionsList.children('li').each(function () {
                const $li = $(this);
                const match = $li.text().toLowerCase().includes(term);
                $li.toggle(match);
            });
        });

        // Show dropdown on focus
        $input.on('focus', function () {
            $optionsList.show();
        });

        // Hide dropdown on outside click
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.filter-item').is($input.closest('.filter-item'))) {
                $optionsList.hide();
            }
        });

        // Option click handler
        $optionsList.children('li').each(function () {
            const $li = $(this);
            $li.on('click', function () {
                const value = $li.attr('value');
                const text = $li.text();
                
                // Check if already selected
                if ($selectedContainer.find(`[data-value="${value}"]`).length) {
                    return;
                }

                // Create tag
                const $tag = $(`
                    <div class="selected-tag" data-value="${value}">
                        <span>${text}</span>
                        <div class="remove-tag" data-value="${value}" data-type="${key}">×</div>
                    </div>
                `);
                $selectedContainer.append($tag);

                // Update hidden input fields
                updateHiddenInputs(key);

                // Hide option and clear input
                $li.hide();
                $input.val('');
                //$input.focus();

                // Hide dropdown after selection
                $optionsList.hide();

                // Remove tag on click and restore option
                $tag.find('.remove-tag').on('click', function () {
                    $tag.remove();
                    $li.show();
                    updateHiddenInputs(key);
                });
            });
        });

        // Function to update hidden input fields
        function updateHiddenInputs(filterType) {
            const selectedValues = [];
            $(`.selected-tags[data-selected="${filterType}"] .selected-tag`).each(function() {
                selectedValues.push($(this).data('value'));
            });
            
            // Remove existing hidden inputs for this type
            $form.find(`input[name="${filterType}[]"]`).remove();
            
            // Add new hidden inputs for each selected value
            selectedValues.forEach(value => {
                $form.append(`<input type="hidden" name="${filterType}[]" value="${value}">`);
            });
        }

        // Handle existing selected tags (from page load)
        $selectedContainer.find('.remove-tag').on('click', function () {
            const $tag = $(this).closest('.selected-tag');
            const value = $(this).data('value');
            const filterType = $(this).data('type');
            
            // Show the corresponding option
            $(`.select-options[data-select="${filterType}"] li[value="${value}"]`).show();
            
            // Remove the tag
            $tag.remove();
            
            // Update hidden inputs
            updateHiddenInputs(filterType);
        });
    });

    // Handle price range selection
    $('.whise-price-range li').on('click', function() {
        const $li = $(this);
        const selectedRange = $li.attr('value');
        const $form = $li.closest('form');
        const $priceContainer = $('.selected-tags[data-selected="price_range"]');
        
        if (selectedRange) {
            const [min, max] = selectedRange.split('-');
            
            // Check if already selected
            if ($priceContainer.find(`[data-value="${selectedRange}"]`).length) {
                return;
            }
            
            // Create price range text
            let priceText = '';
            if (min && max) {
                priceText = `€${parseInt(min).toLocaleString('nl-BE')} - €${parseInt(max).toLocaleString('nl-BE')}`;
            } else if (min) {
                priceText = `€${parseInt(min).toLocaleString('nl-BE')}+`;
            } else if (max) {
                priceText = `Tot €${parseInt(max).toLocaleString('nl-BE')}`;
            }
            
            // Create tag
            const $tag = $(`
                <div class="selected-tag" data-value="${selectedRange}">
                    <span>${priceText}</span>
                    <div class="remove-tag" data-value="${selectedRange}" data-type="price_range">×</div>
                </div>
            `);
            $priceContainer.append($tag);
            
            // Hide the option and clear input
            $li.hide();
            
            // Handle price range removal
            $tag.find('.remove-tag').on('click', function() {
                $tag.remove();
                $li.show();
            });
        }
        
        // Remove auto-submit - let user click the search button
        // $form.submit();
    });

    // Handle existing selected price range tags (from page load)
    $('.selected-tags[data-selected="price_range"] .remove-tag').on('click', function() {
        const $tag = $(this).closest('.selected-tag');
        const value = $(this).data('value');
        
        // Show the corresponding option
        $(`.whise-price-range li[value="${value}"]`).show();
        
        // Remove the tag
        $tag.remove();
    });

    // Auto-submit form when filters change (for non-price filters)
    $('.whise-purpose-select, .whise-city-select, .whise-category-select').on('change', function() {
        // Small delay to ensure hidden inputs are updated
        setTimeout(() => {
            $(this).closest('form').submit();
        }, 100);
    });

    // Handle form submission
    $('.filter-form').on('submit', function(e) {
        // Ensure all selected values are properly included
        $('.selected-tags').each(function() {
            const filterType = $(this).data('selected');
            const selectedValues = [];
            
            $(this).find('.selected-tag').each(function() {
                selectedValues.push($(this).data('value'));
            });
            
            // Update hidden inputs
            const $form = $(this).closest('form');
            $form.find(`input[name="${filterType}[]"]`).remove();
            selectedValues.forEach(value => {
                $form.append(`<input type="hidden" name="${filterType}[]" value="${value}">`);
            });
        });
        
        // Handle multiple price ranges
        const $priceContainer = $('.selected-tags[data-selected="price_range"]');
        const selectedPriceRanges = [];
        
        $priceContainer.find('.selected-tag').each(function() {
            selectedPriceRanges.push($(this).data('value'));
        });
        
        // Remove existing price range inputs
        $('input[name="price_range[]"]').remove();
        
        // Add hidden inputs for each selected price range
        selectedPriceRanges.forEach(range => {
            $('.filter-form').append(`<input type="hidden" name="price_range[]" value="${range}">`);
        });
    });
});
</script>

