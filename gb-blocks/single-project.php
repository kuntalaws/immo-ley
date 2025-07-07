<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;

// Get estate ID from URL parameter
$estate_id = isset($_GET['estate_id']) ? intval($_GET['estate_id']) : 0;

// Initialize Whise API and fetch estate data
$estate = null;
$estate_images = [];
$estate_details = [];
$representatives = [];

if ($estate_id > 0) {
    $whise = new WhiseAPI();
    if ($whise) {
        // Fetch estate details
        $estate_data = $whise->get_estate_details($estate_id);
        if ($estate_data && isset($estate_data['estate'])) {
            $estate = $estate_data['estate'];
            
            // Get images
            if (isset($estate['pictures']) && is_array($estate['pictures'])) {
                $estate_images = $estate['pictures'];
            }
            
            // Get detailed information
            if (isset($estate['details']) && is_array($estate['details'])) {
                $estate_details = $estate['details'];
            }
        }
        
        // Fetch representatives/agents
        $representatives_data = $whise->get_representatives();
        if ($representatives_data && is_array($representatives_data)) {
            $representatives = $representatives_data;
        }
    }
}

// If no estate found, redirect to 404 or show error
if (!$estate) {
    wp_redirect(home_url('/404'));
    exit;
}

// Extract estate information
$estate_title = $estate['name'] ?? 'Eigendom';
$estate_description = '';
if (isset($estate['shortDescription']) && is_array($estate['shortDescription']) && count($estate['shortDescription']) > 0) {
    $estate_description = $estate['shortDescription'][0]['content'] ?? '';
}
$estate_price = $estate['price'] ? '€ ' . number_format($estate['price'], 0, ',', '.') : 'Prijs op aanvraag';
$estate_city = $estate['city'] ?? 'Onbekend';
$estate_address = $estate['address'] ?? '';

// Extract agent/contact information
$agent_name = '';
$agent_phone = '';
$agent_email = '';
$agent_whatsapp = '';

// First, try to get agent from representatives list if estate has a representative ID
if (!empty($representatives) && isset($estate['representativeId'])) {
    foreach ($representatives as $representative) {
        if (isset($representative['id']) && $representative['id'] == $estate['representativeId']) {
            $agent_name = $representative['name'] ?? $representative['firstName'] ?? '';
            $agent_phone = $representative['directLine'] ?? '';
            $agent_email = $representative['email'] ?? '';
            break;
        }
    }
}

// If no representative found by ID, use the first available representative (if only one exists)
if (empty($agent_name) && !empty($representatives) && count($representatives) == 1) {
    $representative = $representatives[0];
    $agent_name = $representative['name'] ?? $representative['firstName'] ?? '';
    $agent_phone = $representative['directLine'] ?? '';
    $agent_email = $representative['email'] ?? '';
    $agent_pic =  $representative['pictureUrl'] ?? '';
}

// If still no agent found, check other possible locations
if (empty($agent_name)) {
    if (isset($estate['agent']) && is_array($estate['agent'])) {
        $agent_name = $estate['agent']['name'] ?? '';
        $agent_phone = $estate['agent']['phone'] ?? '';
        $agent_email = $estate['agent']['email'] ?? '';
    } elseif (isset($estate['contact']) && is_array($estate['contact'])) {
        $agent_name = $estate['contact']['name'] ?? '';
        $agent_phone = $estate['contact']['phone'] ?? '';
        $agent_email = $estate['contact']['email'] ?? '';
    } elseif (isset($estate['broker']) && is_array($estate['broker'])) {
        $agent_name = $estate['broker']['name'] ?? '';
        $agent_phone = $estate['broker']['phone'] ?? '';
        $agent_email = $estate['broker']['email'] ?? '';
    } elseif (isset($estate['client']) && is_array($estate['client'])) {
        // Check if client contains agent information
        $agent_name = $estate['client']['name'] ?? $estate['client']['displayName'] ?? '';
        $agent_phone = $estate['client']['phone'] ?? $estate['client']['phoneNumber'] ?? '';
        $agent_email = $estate['client']['email'] ?? $estate['client']['emailAddress'] ?? '';
    } elseif (isset($estate['owner']) && is_array($estate['owner'])) {
        $agent_name = $estate['owner']['name'] ?? '';
        $agent_phone = $estate['owner']['phone'] ?? '';
        $agent_email = $estate['owner']['email'] ?? '';
    } elseif (isset($estate['responsible']) && is_array($estate['responsible'])) {
        $agent_name = $estate['responsible']['name'] ?? '';
        $agent_phone = $estate['responsible']['phone'] ?? '';
        $agent_email = $estate['responsible']['email'] ?? '';
    }
}

// Fallback to default values if no agent info found
if (empty($agent_name)) {
    $agent_name = 'Immo Ley';
}
if (empty($agent_phone)) {
    $agent_phone = '03 333 33 33';
}
if (empty($agent_email)) {
    $agent_email = 'info@immoley.be';
}

// Get main image
$main_image_url = get_template_directory_uri() . '/img/single-page-banner.png'; // Default fallback
if (!empty($estate_images)) {
    $main_image_url = $estate_images[0]['urlLarge'] ?? $estate_images[0]['url'] ?? $main_image_url;
}

// Extract property characteristics from details array
$property_details = [];
if (isset($estate['details']) && is_array($estate['details'])) {
    foreach ($estate['details'] as $detail) {
        if (isset($detail['label']) && isset($detail['value'])) {
            $property_details[$detail['label']] = $detail['value'];
            // Also store by ID for easier lookup
            $property_details['id_' . $detail['id']] = $detail['value'];
        }
    }
}

// Helper function to get detail value by label or ID
function get_detail_value($details, $label, $default = 'niet vermeld') {
    if (isset($details[$label])) {
        return $details[$label];
    }
    return $default;
}

// Helper function to get characteristic value (keeping for backward compatibility)
function get_characteristic($characteristics, $key, $default = 'niet vermeld') {
    return isset($characteristics[$key]) ? $characteristics[$key] : $default;
}

// Debug display (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc; font-family: monospace; font-size: 12px;">';
    echo '<h3>🔍 Single Project Debug Info:</h3>';
    echo '<p><strong>Estate ID:</strong> ' . $estate_id . '</p>';
    
    echo '<h4>📋 All Available Estate Fields:</h4>';
    echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 400px;">';
    print_r($estate);
    echo '</pre>';
    
    echo '<h4>🏠 Property Characteristics:</h4>';
    echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 300px;">';
    print_r($property_details);
    echo '</pre>';
    
    echo '<h4>📋 Estate Details Array:</h4>';
    echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 400px;">';
    print_r($estate['details'] ?? 'No details array');
    echo '</pre>';
    
    echo '<h4>📸 Estate Images:</h4>';
    echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 200px;">';
    print_r($estate_images);
    echo '</pre>';
    
    echo '<h4>📝 Short Description:</h4>';
    echo '<pre style="background: white; padding: 10px;">';
    print_r($estate['shortDescription'] ?? 'No short description');
    echo '</pre>';
    
    echo '<h4>📄 Long Description:</h4>';
    echo '<pre style="background: white; padding: 10px;">';
    print_r($estate['longDescription'] ?? 'No long description');
    echo '</pre>';
    
    echo '<h4>📍 Address Information:</h4>';
    echo '<pre style="background: white; padding: 10px;">';
    print_r([
        'address' => $estate['address'] ?? 'No address',
        'city' => $estate['city'] ?? 'No city',
        'zip' => $estate['zip'] ?? 'No zip',
        'country' => $estate['country'] ?? 'No country'
    ]);
    echo '</pre>';
    
    echo '<h4>👤 Agent/Contact Information:</h4>';
    echo '<pre style="background: white; padding: 10px;">';
    print_r([
        'agent' => $estate['agent'] ?? 'No agent data',
        'contact' => $estate['contact'] ?? 'No contact data',
        'broker' => $estate['broker'] ?? 'No broker data',
        'client' => $estate['client'] ?? 'No client data',
        'owner' => $estate['owner'] ?? 'No owner data',
        'responsible' => $estate['responsible'] ?? 'No responsible data',
        'representativeId' => $estate['representativeId'] ?? 'No representative ID',
        'representatives_count' => count($representatives),
        'representatives' => $representatives,
        'extracted_agent_name' => $agent_name,
        'extracted_agent_phone' => $agent_phone,
        'extracted_agent_email' => $agent_email
    ]);
    echo '</pre>';
    
    echo '<h4>🔍 Complete Estate Data Structure (All Keys):</h4>';
    echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 300px;">';
    if (is_array($estate)) {
        echo "Available top-level keys:\n";
        foreach (array_keys($estate) as $key) {
            echo "- " . $key . "\n";
        }
        
        echo "\n\nSearching for agent-related fields:\n";
        foreach ($estate as $key => $value) {
            if (is_string($key) && (
                stripos($key, 'agent') !== false || 
                stripos($key, 'contact') !== false || 
                stripos($key, 'broker') !== false || 
                stripos($key, 'phone') !== false || 
                stripos($key, 'email') !== false ||
                stripos($key, 'name') !== false
            )) {
                echo "- " . $key . ": " . (is_array($value) ? 'Array' : $value) . "\n";
            }
        }
    }
    echo '</pre>';
    
    echo '</div>';
}
?>
<!--Single Project Start Here-->
<section class="single-project-section">
	<div class="single-project-section-in fw">
		<div class="single-project-section-image" style="background-image:url('<?php echo esc_url($main_image_url); ?>')">
			<div class="single-project-section-button fw">
				<a href="javascript:void(0);" class="btn flex img-icon-btn" id="all-projects-link">
				<span class="img-icon"><img loading="lazy" src="/immo-ley/wp-content/themes/immo-ley/img/img-icon2.png" alt="Immo Ley"></span>
				<span class="btn-text">Alle projecten</span></a>
			</div>
		</div>
	</div>
</section>
<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
<!-- Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
<script>
  document.getElementById('all-projects-link').addEventListener('click', function () {
    Fancybox.show([
      {
        src: '<?php echo esc_url($main_image_url); ?>',
        type: 'image',
      },
      {
        src: '<?php echo esc_url($main_image_url); ?>',
        type: 'image',
      },
      {
        src: '<?php echo esc_url($main_image_url); ?>',
        type: 'image',
      },
    ], {
      Thumbs: true,
      Toolbar: true,
    });
  });
</script>

<section class="single-project-content-section">
	<div class="single-project-content-section-in fw">
		<div class="single-project-content flex">
			<div class="single-project-content-left">
				<h3><?php echo esc_html($estate_address); ?></h3>
				<h2><?php echo esc_html($estate_title); ?></h2>
				<p><?php echo wp_kses_post($estate_description); ?></p>
			</div>
			<div class="single-project-content-right">
				<h3>VRAAGPRIJS</h3>
				<div class="single-project-content-right-price"><span><?php echo esc_html($estate_price); ?></span></div>

				<div class="image-with-info-sec">
					<div class="image-with-info flex">
						<div class="image-left"><img src="<?php echo esc_url($agent_pic); ?>" alt="Immo Ley" /> </div>
						<div class="content-right">
							<h3>JOUW EXPERT</h3>
							<p><?php echo esc_html(ucfirst($agent_name)); ?></p>
							<ul>
                                <li><a href="tel:<?php echo esc_attr($agent_phone); ?>"><?php echo esc_html($agent_phone); ?></a></li>
                                <li><a href="mailto:<?php echo esc_attr($agent_email); ?>"><?php echo esc_html($agent_email); ?></a></li>
                                <li>
                                    <a href="https://wa.me/+<?php echo $agent_phone; ?>?text=<?php echo urlencode($estate_title); ?>" target="_blank">
                                        Chat on WhatsApp
                                    </a>
                                </li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

   <section class="kenmerken">       
        <div class="kenmerken-stats-wrap fw">
		<h2 class="kenmerken-title">Kenmerken</h2>
            <div class="kenmerken-stats-row flex">
                <div class="kenmerken-stat">
                    <div class="kenmerken__stat-icon">
                       <img src="/immo-ley/wp-content/themes/immo-ley/img/ic1.svg" alt="Immo Ley">
                    </div>
                    <span class="kenmerken-stat-value"><?php echo esc_html(get_detail_value($property_details, 'oppervlakte')); ?>m²</span>
                </div>

                <div class="kenmerken-stat">
                    <div class="kenmerken__stat-icon">
					<img src="/immo-ley/wp-content/themes/immo-ley/img/ic22.svg" alt="Immo Ley">
                    </div>
                    <span class="kenmerken-stat-value"><?php echo esc_html(get_detail_value($property_details, 'gebouwd oppervlakte')); ?>m²</span>
                </div>

                <div class="kenmerken-stat">
                    <div class="kenmerken__stat-icon">
						<img src="/immo-ley/wp-content/themes/immo-ley/img/ic3.svg" alt="Immo Ley"></div>
                    <span class="kenmerken-stat-value"><?php echo esc_html(get_detail_value($property_details, 'Bouwjaar')); ?></span>
                </div>

                <div class="kenmerken-stat">
                    <div class="kenmerken__stat-icon">
					<img src="/immo-ley/wp-content/themes/immo-ley/img/ic4.svg" alt="Immo Ley">
                    </div>
                    <span class="kenmerken-stat-value"><?php echo esc_html(get_detail_value($property_details, 'aantal kamers')); ?></span>
                </div>

                <div class="kenmerken-stat">
                    <div class="kenmerken__stat-icon">
					<img src="/immo-ley/wp-content/themes/immo-ley/img/ic5.svg" alt="Immo Ley">
                    </div>
                    <span class="kenmerken-stat-value"><?php echo esc_html(get_detail_value($property_details, 'aantal badkamers')); ?></span>
                </div>

				<div class="kenmerken-stat">
                    <div class="kenmerken__stat-icon">
					<img src="/immo-ley/wp-content/themes/immo-ley/img/ic6.svg" alt="Immo Ley">
                    </div>
                    <span class="kenmerken-stat-value"><?php echo esc_html(get_detail_value($property_details, 'Bouwjaar')); ?></span>
                </div>
				<div class="kenmerken-stat">
                    <div class="kenmerken__stat-icon">
                        <img src="/immo-ley/wp-content/themes/immo-ley/img/ic7.svg" alt="Immo Ley">
                    </div>
                    <span class="kenmerken-stat-value"><?php echo esc_html(get_detail_value($property_details, 'energieverbruik')); ?> kWh/m²</span>
                </div>
            </div>
        </div>
    </section>


    <section class="property-long-description">
        <div class="property-long-description-in fw">
            <h2>Meer over deze woning</h2>
            <div class="property-long-description-content">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p> 
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p> 
            </div>
        </div>
    </section>

	<section class="property-widget">
		<div class="property-widget-wrap fw">
		<h2 class="widget-title">Algemene info</h2>
        
        <div class="property-table">
                <?php 
                // Display all available property details dynamically
                if (isset($estate['details']) && is_array($estate['details'])) {
                    foreach ($estate['details'] as $detail) {
                        if (isset($detail['label']) && isset($detail['value'])) {
                            $label = $detail['label'];
                            $value = $detail['value'];
                            
                            // Format the value based on type
                            $formatted_value = $value;
                            if ($detail['type'] === 'yes/no') {
                                $formatted_value = $value == 1 ? 'ja' : 'nee';
                            } elseif ($detail['type'] === 'year') {
                                $formatted_value = $value;
                            } elseif ($detail['type'] === 'int') {
                                $formatted_value = $value;
                            } elseif ($detail['type'] === 'float') {
                                $formatted_value = number_format($value, 2, ',', '.');
                            } elseif ($detail['type'] === 'currency') {
                                $formatted_value = '€ ' . number_format($value, 0, ',', '.');
                            }
                            
                            // Add units if needed
                            if (stripos($label, 'oppervlakte') !== false || stripos($label, 'surface') !== false) {
                                $formatted_value .= ' m²';
                            } elseif (stripos($label, 'energie') !== false || stripos($label, 'energy') !== false) {
                                $formatted_value .= ' kWh/m²';
                            }
                            
                            echo '<p><span>' . esc_html(ucwords($label)) . '</span><strong>' . esc_html($formatted_value) . '</strong></p>';
                        }
                    }
                } else {
                    echo '<p><span>Geen details beschikbaar</span><strong>-</strong></p>';
                }
                ?>
        </div>

            <div class="share-section">
                <h3>DEEL DEZE WONING</h3>
                <ul class="social-icons flex">
                    <li class="social-icon"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink() . '?estate_id=' . $estate_id); ?>&quote=<?php echo urlencode($estate_title . ' - ' . $estate_address); ?>" target="_blank" title="Deel op Facebook">
                       <img src="/immo-ley/wp-content/themes/immo-ley/img/facebook-white.svg" alt="Facebook">
                    </a></li>
                    <li class="social-icon"><a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink() . '?estate_id=' . $estate_id); ?>&text=<?php echo urlencode($estate_title . ' - ' . $estate_address . ' - ' . $estate_price); ?>" target="_blank" title="Deel op Twitter">
                    <img src="/immo-ley/wp-content/themes/immo-ley/img/insta-white.svg" alt="Twitter">
                    </a></li>
                    <li class="social-icon"><a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink() . '?estate_id=' . $estate_id); ?>" target="_blank" title="Deel op LinkedIn">
                    <img src="/immo-ley/wp-content/themes/immo-ley/img/linkedin-white.svg" alt="LinkedIn">
                    </a></li>
                </ul>
            </div>
		</div>     
    </section>


    <section class="filter-with-grid related-projects">
	<div class="filter-grid">
		<div class="filter-grid-title fw">
			<h3>Gelijkaardige panden</h3>
		</div>
		<div class="filter-grid-item-wrapper">
			<div class="filter-grid-item-wrap fw flex" id="whise-estates-container">
				<a href="https://anushaweb.com/immo-ley/single-project/?estate_id=6973025" class="filter-grid-item" data-estate-id="6973025">
                    <div class="filter-grid-item-img">
                                                                                            <span class="pro-type sold">verkocht</span>
                                                        <div class="filter-grid-item-img-box">
                            <img decoding="async" loading="lazy" src="https://anushaweb.com/immo-ley/wp-content/themes/immo-ley/img/grid-item-img-01.jpg" alt="Kerremansstraat 31 - 2840 Rumst">
                        </div>
                    </div>
                    <div class="filter-grid-item-info">
                        <div class="filter-grid-item-info-in">
                            <h6><span class="filter-grid-item-info-category">Reet</span> / <span class="filter-grid-item-info-price">€ 950.000</span></h6>
                            <h4>Kerremansstraat 31 - 2840 Rumst</h4>
                        </div>
                    </div>
                </a>
                                                            <a href="https://anushaweb.com/immo-ley/single-project/?estate_id=6955982" class="filter-grid-item" data-estate-id="6955982">
                    <div class="filter-grid-item-img">
                                                                                        <div class="filter-grid-item-img-box">
                            <img decoding="async" loading="lazy" src="https://whisestorageprod.blob.core.windows.net/public/storage12889/Pictures/6955982/640/665d4faa033a47b6b96e18635fae5d23.jpg" alt="Edegemsestraat 135 - 2640 Mortsel">
                        </div>
                    </div>
                    <div class="filter-grid-item-info">
                        <div class="filter-grid-item-info-in">
                            <h6><span class="filter-grid-item-info-category">Mortsel</span> / <span class="filter-grid-item-info-price">€ 450.000</span></h6>
                            <h4>Edegemsestraat 135 - 2640 Mortsel</h4>
                        </div>
                    </div>
                </a>
			</div>
		</div>
	</div>
</section>

<?php
	if(intval($contentRowsInPage['single-project']) == 0 || is_admin()){
		if(file_exists(get_template_directory().'/css/single-project.css')){
			echo '<style>';
		include(get_template_directory().'/css/single-project.css');
		echo '</style>';
		} 
        if(file_exists(get_template_directory().'/css/filter-with-grid.css')){
			echo '<style>';
		include(get_template_directory().'/css/filter-with-grid.css');
		echo '</style>';
		}   
	}
	$contentRowsInPage['single-project'] = intval($contentRowsInPage['single-project'])+1;
