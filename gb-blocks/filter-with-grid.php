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

// Get current filter values from URL parameters
$current_purpose = isset($_GET['purpose']) ? sanitize_text_field($_GET['purpose']) : '';
$current_city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$current_price_min = isset($_GET['price_min']) ? sanitize_text_field($_GET['price_min']) : '';
$current_price_max = isset($_GET['price_max']) ? sanitize_text_field($_GET['price_max']) : '';

// Debug logging
error_log('Whise Filter Debug - URL Parameters: ' . print_r($_GET, true));
error_log('Whise Filter Debug - Current values: purpose=' . $current_purpose . ', city=' . $current_city . ', category=' . $current_category . ', price_min=' . $current_price_min . ', price_max=' . $current_price_max);

// Build filters array for API call
$filters = [];
if (!empty($current_purpose)) {
    $filters['PurposeId'] = intval($current_purpose);
}
if (!empty($current_city)) {
    $filters['City'] = $current_city;
}
if (!empty($current_category)) {
    $filters['CategoryId'] = intval($current_category);
}
if (!empty($current_price_min)) {
    $filters['PriceMin'] = intval($current_price_min);
}
if (!empty($current_price_max)) {
    $filters['PriceMax'] = intval($current_price_max);
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
    echo '<li>Purpose: ' . ($current_purpose ?: 'empty') . '</li>';
    echo '<li>City: ' . ($current_city ?: 'empty') . '</li>';
    echo '<li>Category: ' . ($current_category ?: 'empty') . '</li>';
    echo '<li>Price Min: ' . ($current_price_min ?: 'empty') . '</li>';
    echo '<li>Price Max: ' . ($current_price_max ?: 'empty') . '</li>';
    echo '</ul>';
    echo '<p><strong>Built Filters:</strong> ' . print_r($filters, true) . '</p>';
    echo '<p><strong>API Response:</strong> ' . print_r($estates_data, true) . '</p>';
    echo '<p><strong>Estates Count:</strong> ' . count($estates) . '</p>';
    echo '</div>';
}
?>
<!--Filter With Grid Start Here-->
<section class="filter-with-grid">
	<form method="GET" action="<?php echo esc_url($current_url); ?>" class="filter-form">
		<div class="filter-row">
			<div class="filter-row-in fw flex">
				<div class="filter-item">
					<input type="text" name="purpose-input" placeholder="Te koop" value="<?php echo esc_attr($current_purpose); ?>" class="select-input" data-select="purpose">
					<ul name="purpose" class="select-options whise-purpose-select" data-select="purpose">
						<?php if (isset($filter_options['purposes'])): ?>
							<?php foreach ($filter_options['purposes'] as $purpose): ?>
								<li value="<?php echo esc_attr($purpose['id']); ?>" <?php selected($current_purpose, $purpose['id']); ?>><?php echo esc_html($purpose['name']); ?></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<div class="selected-tags" data-selected="purpose"></div>
				</div>
				<div class="filter-item">
					<input type="text" name="city-input" placeholder="Gemeente" value="<?php echo esc_attr($current_city); ?>" class="select-input" data-select="city">
					<ul name="city" class="select-options whise-city-select" data-select="city">
						<?php if (isset($filter_options['cities'])): ?>
							<?php foreach ($filter_options['cities'] as $city): ?>
								<li value="<?php echo esc_attr($city['name']); ?>" <?php selected($current_city, $city['name']); ?>><?php echo esc_html($city['name']); ?></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<div class="selected-tags" data-selected="city"></div>
				</div>
				<div class="filter-item">
					<input type="text" name="category-input" placeholder="Type" value="<?php echo esc_attr($current_category); ?>" class="select-input" data-select="category">
					<ul name="category" class="select-options whise-category-select" data-select="category">
						<?php if (isset($filter_options['categories'])): ?>
							<?php foreach ($filter_options['categories'] as $category): ?>
								<li value="<?php echo esc_attr($category['id']); ?>" <?php selected($current_category, $category['id']); ?>><?php echo esc_html($category['name']); ?></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<div class="selected-tags" data-selected="category"></div>
				</div>
				<div class="filter-item">
					<input type="text" name="price_range-input" placeholder="Prijs" value="<?php echo esc_attr($current_price_range); ?>" class="select-input" data-select="price_range">
					<ul name="price_range" class="select-options whise-price-range" data-select="price_range">
						<?php if (isset($filter_options['price_ranges'])): ?>
							<?php foreach ($filter_options['price_ranges'] as $range): ?>
								<?php 
								$range_value = $range['min'] . '-' . ($range['max'] ?: '');
								$current_range = '';
								if (!empty($current_price_min) || !empty($current_price_max)) {
									$current_range = $current_price_min . '-' . $current_price_max;
								}
								?>
								<li value="<?php echo esc_attr($range_value); ?>" <?php selected($current_range, $range_value); ?>><?php echo esc_html($range['label']); ?></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<input type="hidden" name="price_min" value="<?php echo esc_attr($current_price_min); ?>">
					<input type="hidden" name="price_max" value="<?php echo esc_attr($current_price_max); ?>">
					<div class="selected-tags" data-selected="price_range"></div>
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
					<?php foreach ($estates as $estate): ?>
						<?php
						$imageUrl = $estate['pictures'] && count($estate['pictures']) > 0 
							? $estate['pictures'][0]['urlLarge'] 
							: get_template_directory_uri() . '/img/grid-item-img-01.jpg';
						
						$price = $estate['price'] ? '€ ' . number_format($estate['price'], 0, ',', '.') : 'Prijs op aanvraag';
						$city = $estate['city'] ?? 'Onbekend';
						$title = $estate['name'] ?? ($estate['shortDescription'] && count($estate['shortDescription']) > 0 ? $estate['shortDescription'][0]['content'] : 'Eigendom');
						?>
						<a href="<?php echo esc_url($estate['url']); ?>" class="filter-grid-item" data-estate-id="<?php echo esc_attr($estate['id']); ?>">
							<div class="filter-grid-item-img">
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
						<h4>Geen eigendommen gevonden</h4>
						<p>Probeer andere zoekcriteria of neem contact met ons op.</p>
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
			<!--Show After 4 Items-->
			<div class="filter-grid-item-wrap fw flex" id="whise-estates-container">
				<?php if (!empty($estates)): ?>
					<?php foreach ($estates as $estate): ?>
						<?php
						$imageUrl = $estate['pictures'] && count($estate['pictures']) > 0 
							? $estate['pictures'][0]['urlLarge'] 
							: get_template_directory_uri() . '/img/grid-item-img-01.jpg';
						
						$price = $estate['price'] ? '€ ' . number_format($estate['price'], 0, ',', '.') : 'Prijs op aanvraag';
						$city = $estate['city'] ?? 'Onbekend';
						$title = $estate['name'] ?? ($estate['shortDescription'] && count($estate['shortDescription']) > 0 ? $estate['shortDescription'][0]['content'] : 'Eigendom');
						?>
						<a href="<?php echo esc_url($estate['url']); ?>" class="filter-grid-item" data-estate-id="<?php echo esc_attr($estate['id']); ?>">
							<div class="filter-grid-item-img">
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
						<h4>Geen eigendommen gevonden</h4>
						<p>Probeer andere zoekcriteria of neem contact met ons op.</p>
					</div>
				<?php endif; ?>
			</div>
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
document.querySelectorAll('.select-input').forEach(input => {
  const key = input.dataset.select;
  const optionsList = document.querySelector(`.select-options[data-select="${key}"]`);
  const selectedContainer = document.querySelector(`.selected-tags[data-selected="${key}"]`);

  // Filter on input
  input.addEventListener('input', () => {
    const term = input.value.toLowerCase();
    optionsList.style.display = 'block';
    [...optionsList.children].forEach(li => {
      li.style.display = li.textContent.toLowerCase().includes(term) ? 'block' : 'none';
    });
  });

  // Show dropdown on focus
  input.addEventListener('focus', () => {
    optionsList.style.display = 'block';
  });

  // Hide dropdown on outside click
  document.addEventListener('click', e => {
    if (!input.closest('.filter-item').contains(e.target)) {
      optionsList.style.display = 'none';
    }
  });

  // Option click handler
  optionsList.querySelectorAll('li').forEach(li => {
    li.addEventListener('click', () => {
      const tag = document.createElement('div');
      tag.className = 'selected-tag';
      tag.innerHTML = `<span>${li.textContent}</span><div class="remove-tag">×</div>`;
      selectedContainer.appendChild(tag);

      // Hide option and clear input
      li.style.display = 'none';
      input.value = '';
      input.focus();

      // Remove tag on click and restore option
      tag.querySelector('.remove-tag').addEventListener('click', () => {
        selectedContainer.removeChild(tag);
        li.style.display = 'block';
      });
    });
  });
});

jQuery(document).ready(function($) {
    // Handle price range selection
    $('.whise-price-range').on('change', function() {
        const selectedRange = $(this).val();
        if (selectedRange) {
            const [min, max] = selectedRange.split('-');
            $('input[name="price_min"]').val(min || '');
            $('input[name="price_max"]').val(max || '');
        } else {
            $('input[name="price_min"]').val('');
            $('input[name="price_max"]').val('');
        }
        // Auto-submit form when price range changes
        $(this).closest('form').submit();
    });
    
    // Auto-submit form when other filters change
    $('.whise-purpose-select, .whise-city-select, .whise-category-select').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>

