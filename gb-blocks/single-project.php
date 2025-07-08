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

// Fetch similar properties based on the same purpose (state)
$similar_properties = [];
$current_purpose_id = null;

// Get the purpose ID from the estate data structure - use same logic as filter system
if (isset($estate['purpose']['id'])) {
    $current_purpose_id = intval($estate['purpose']['id']); // Convert to integer like filter system
} elseif (isset($estate['purposeId'])) {
    $current_purpose_id = intval($estate['purposeId']);
}

// Debug purpose ID extraction
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo '<h4>🔍 Purpose ID Extraction Debug:</h4>';
    echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 200px;">';
    echo "Estate purpose object: " . print_r($estate['purpose'] ?? 'Not set', true) . "\n";
    echo "Estate purposeId field: " . ($estate['purposeId'] ?? 'Not set') . "\n";
    echo "Extracted current_purpose_id: " . ($current_purpose_id ?? 'Not set') . "\n";
    echo "Purpose ID type: " . gettype($current_purpose_id) . "\n";
    echo '</pre>';
}

if ($whise && $current_purpose_id) {
    // Use exact same logic as filter system
    $similar_filters = [
        'PurposeIds' => array_map('intval', [$current_purpose_id]) // Convert to array of integers
    ];
    
    // Exclude the current estate from similar properties
    $similar_estates_data = $whise->get_estates($similar_filters);
    
    // Debug the API response
    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
        echo '<h4>🔍 Similar Properties API Debug:</h4>';
        echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 300px;">';
        echo "Filters sent to API: " . print_r($similar_filters, true) . "\n";
        echo "API Response: " . print_r($similar_estates_data, true) . "\n";
        echo "Number of estates returned: " . (isset($similar_estates_data['estates']) ? count($similar_estates_data['estates']) : '0') . "\n";
        if (isset($similar_estates_data['estates']) && count($similar_estates_data['estates']) > 0) {
            echo "First estate structure:\n";
            print_r($similar_estates_data['estates'][0]);
        }
        echo '</pre>';
    }
    
    if ($similar_estates_data && isset($similar_estates_data['estates'])) {
        // Filter out the current estate and limit to 2 similar properties
        foreach ($similar_estates_data['estates'] as $similar_estate) {
            if ($similar_estate['id'] != $estate_id && count($similar_properties) < 2) {
                $similar_properties[] = $similar_estate;
            }
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
    
    echo '<h4>🏠 Similar Properties Debug:</h4>';
    echo '<pre style="background: white; padding: 10px; overflow-x: auto; max-height: 300px;">';
    echo "Current Estate PurposeId: " . ($current_purpose_id ?? 'Not set') . "\n";
    echo "Estate Purpose Object: " . print_r($estate['purpose'] ?? 'Not set', true) . "\n";
    echo "Similar Properties Found: " . count($similar_properties) . "\n";
    echo "Similar Filters Used: " . print_r($similar_filters ?? [], true) . "\n";
    echo "Current Estate ID: " . $estate_id . "\n";
    echo "Current Estate Purpose ID from purpose object: " . (isset($estate['purpose']['id']) ? $estate['purpose']['id'] : 'Not found') . "\n";
    
    // Test API call without filters to see if we get any properties at all
    if ($whise) {
        $all_estates_data = $whise->get_estates([]);
        echo "Total properties available (no filters): " . (isset($all_estates_data['estates']) ? count($all_estates_data['estates']) : '0') . "\n";
        
        if (isset($all_estates_data['estates']) && count($all_estates_data['estates']) > 0) {
            echo "Sample of all properties:\n";
            for ($i = 0; $i < min(3, count($all_estates_data['estates'])); $i++) {
                $sample_estate = $all_estates_data['estates'][$i];
                echo "- ID: " . ($sample_estate['id'] ?? 'N/A') . ", PurposeId: " . ($sample_estate['purposeId'] ?? 'N/A') . ", Purpose Object: " . print_r($sample_estate['purpose'] ?? 'N/A', true) . ", Name: " . ($sample_estate['name'] ?? 'N/A') . "\n";
            }
        }
    }
    
    if (!empty($similar_properties)) {
        echo "\nSimilar Properties Details:\n";
        foreach ($similar_properties as $index => $similar_estate) {
            echo "Property " . ($index + 1) . ":\n";
            echo "- ID: " . ($similar_estate['id'] ?? 'N/A') . "\n";
            echo "- Name: " . ($similar_estate['name'] ?? 'N/A') . "\n";
            echo "- PurposeId: " . ($similar_estate['purposeId'] ?? 'N/A') . "\n";
            echo "- City: " . ($similar_estate['city'] ?? 'N/A') . "\n";
            echo "- Price: " . ($similar_estate['price'] ?? 'N/A') . "\n";
            echo "- Pictures: " . (isset($similar_estate['pictures']) ? count($similar_estate['pictures']) : '0') . " images\n";
            echo "\n";
        }
    } else {
        echo "No similar properties found. Possible reasons:\n";
        echo "- No other properties with same purposeId\n";
        echo "- API call failed\n";
        echo "- All properties with same purposeId are the current property\n";
        echo "- Filter parameter issue (check PurposeIds vs PurposeId)\n";
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
    // Create array of all property images for Fancybox
    var propertyImages = [
      <?php 
      if (!empty($estate_images)) {
          foreach ($estate_images as $index => $image) {
              $image_url = $image['urlXXL'] ?? $image['urlLarge'] ?? $image['url'] ?? '';
              if (!empty($image_url)) {
                  echo "{\n";
                  echo "  src: '" . esc_url($image_url) . "',\n";
                  echo "  type: 'image',\n";
                  echo "  caption: '" . esc_js($estate_title . ' - Afbeelding ' . ($index + 1)) . "'\n";
                  echo "}";
                  if ($index < count($estate_images) - 1) {
                      echo ",\n";
                  }
              }
          }
      } else {
          // Fallback to main image if no images available
          echo "{\n";
          echo "  src: '" . esc_url($main_image_url) . "',\n";
          echo "  type: 'image',\n";
          echo "  caption: '" . esc_js($estate_title) . "'\n";
          echo "}";
      }
      ?>
    ];
    
    Fancybox.show(propertyImages, {
      Thumbs: true,
      Toolbar: true,
      Image: {
        zoom: true,
        click: "close",
        wheel: "slide"
      },
      Carousel: {
        infinite: true,
        center: true,
        fill: true,
        dragFree: true,
        adaptive: true,
        friction: 0.7,
        infinite: true,
        preload: 1,
        slidesPerPage: 1
      }
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

    <?php 
    // Extract long description content from the array structure
    $long_description_content = '';
    if (isset($estate['longDescription']) && is_array($estate['longDescription']) && !empty($estate['longDescription'])) {
        // Get the first description (usually the main one)
        $first_description = $estate['longDescription'][0];
        if (isset($first_description['content'])) {
            $long_description_content = $first_description['content'];
        }
    }
    
    if (!empty($long_description_content)): ?>
    <section class="property-long-description">
        <div class="property-long-description-in fw">
            <?php echo wp_kses_post($long_description_content); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if($estate['details']): ?>
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
    <?php endif; ?>

    <section class="filter-with-grid related-projects">
        <div class="filter-grid">
            <div class="filter-grid-title fw">
                <h3>Gelijkaardige panden</h3>
            </div>
            <div class="filter-grid-item-wrapper">
                <div class="filter-grid-item-wrap fw flex" id="whise-estates-container">
                    <?php 
                    if (!empty($similar_properties)) {
                        foreach ($similar_properties as $similar_estate) {
                            // Get image URL
                            $similar_image_url = get_template_directory_uri() . '/img/grid-item-img-01.jpg'; // Default fallback
                            if (isset($similar_estate['pictures']) && !empty($similar_estate['pictures'])) {
                                $similar_image_url = $similar_estate['pictures'][0]['urlLarge'] ?? $similar_estate['pictures'][0]['url'] ?? $similar_image_url;
                            }
                            
                            // Get estate information
                            $similar_price = $similar_estate['price'] ? '€ ' . number_format($similar_estate['price'], 0, ',', '.') : 'Prijs op aanvraag';
                            $similar_city = $similar_estate['city'] ?? 'Onbekend';
                            $similar_title = $similar_estate['name'] ?? ($similar_estate['shortDescription'] && count($similar_estate['shortDescription']) > 0 ? $similar_estate['shortDescription'][0]['content'] : 'Eigendom');
                            
                            // Determine purpose label based on purposeId
                            $purpose_label = 'Onbekend';
                            if (isset($similar_estate['purposeId'])) {
                                switch ($similar_estate['purposeId']) {
                                    case 1:
                                        $purpose_label = 'te koop';
                                        break;
                                    case 2:
                                        $purpose_label = 'te huur';
                                        break;
                                    case 3:
                                        $purpose_label = 'lijfrente verkoop';
                                        break;
                                }
                            }
                            
                            // Check if property is sold/rented
                            $status_class = '';
                            $status_label = '';
                            if (isset($similar_estate['purposeStatus']['id'])) {
                                if ($similar_estate['purposeStatus']['id'] == 3) {
                                    $status_class = 'sold';
                                    $status_label = 'verkocht';
                                }
                            }
                            ?>
                            <a href="<?php echo esc_url(add_query_arg('estate_id', $similar_estate['id'], get_permalink(get_page_by_path('single-project')))); ?>" class="filter-grid-item" data-estate-id="<?php echo esc_attr($similar_estate['id']); ?>">
                                <div class="filter-grid-item-img">
                                    <?php if (!empty($status_label)): ?>
                                        <span class="pro-type<?php echo $status_class ? ' ' . esc_attr($status_class) : ''; ?>"><?php echo esc_html($status_label); ?></span>
                                    <?php endif; ?>
                                    <div class="filter-grid-item-img-box">
                                        <img decoding="async" loading="lazy" src="<?php echo esc_url($similar_image_url); ?>" alt="<?php echo esc_attr($similar_title); ?>">
                                    </div>
                                </div>
                                <div class="filter-grid-item-info">
                                    <div class="filter-grid-item-info-in">
                                        <h6><span class="filter-grid-item-info-category"><?php echo esc_html($similar_city); ?></span> / <span class="filter-grid-item-info-price"><?php echo esc_html($similar_price); ?></span></h6>
                                        <h4><?php echo esc_html($similar_title); ?></h4>
                                    </div>
                                </div>
                            </a>
                            <?php
                        }
                    } else {
                        echo '<div class="no-similar-properties" style="text-align: center; padding: 40px; color: #666;">';
                        echo '<h4>Geen gelijkaardige projecten gevonden</h4>';
                        echo '<p>Er zijn momenteel geen andere projecten beschikbaar met dezelfde status.</p>';
                        echo '</div>';
                    }
                    ?>
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
