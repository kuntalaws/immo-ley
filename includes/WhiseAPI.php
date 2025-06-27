<?php
/**
 * Whise API Integration Class
 * Handles authentication and API calls to Whise real estate platform
 * 
 * Authentication Flow:
 * 1. Get token using username/password
 * 2. Get client list using token
 * 3. Get client token using client ID and token
 * 4. Fetch estates using client token
 */

class WhiseAPI {
    private $api_url;
    private $username;
    private $password;
    private $client_id;
    private $token;
    private $client_token;
    
    public function __construct() {
        $this->api_url = get_field('whise_api_url', 'option') ?: 'https://api.whise.eu';
        $this->username = get_field('whise_username', 'option');
        $this->password = get_field('whise_password', 'option');
        $this->client_id = get_field('whise_client_id', 'option');
    }
    
    /**
     * Step 1: Authenticate and get token using username/password
     */
    public function authenticate() {
        if (empty($this->username) || empty($this->password)) {
            error_log('Whise API: Missing username or password');
            return false;
        }
        
        $request_body = [
            'username' => $this->username,
            'password' => $this->password
        ];
        
        error_log('Whise API: Step 1 - Authenticating with username: ' . $this->username);
        
        $response = wp_remote_post($this->api_url . '/token', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($request_body),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            error_log('Whise API: Authentication error - ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        error_log('Whise API: Authentication response code: ' . $status_code);
        error_log('Whise API: Authentication response body: ' . $body);
        
        $data = json_decode($body, true);
        
        if (isset($data['token'])) {
            $this->token = $data['token'];
            error_log('Whise API: Step 1 - Authentication successful');
            return true;
        }
        
        error_log('Whise API: Step 1 - Authentication failed - no token in response');
        return false;
    }
    
    /**
     * Step 2: Get client list using the token
     */
    public function get_client_list() {
        if (!$this->token) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        
        error_log('Whise API: Step 2 - Getting client list');
        
        $response = wp_remote_post($this->api_url . '/v1/admin/clients/list', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([]),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            error_log('Whise API: Client list error - ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        error_log('Whise API: Client list response code: ' . $status_code);
        error_log('Whise API: Client list response body: ' . $body);
        
        $data = json_decode($body, true);
        
        if ($data && isset($data['clients'])) {
            error_log('Whise API: Step 2 - Found ' . count($data['clients']) . ' clients');
            return $data['clients'];
        }
        
        error_log('Whise API: Step 2 - Failed to get client list');
        return false;
    }
    
    /**
     * Step 3: Get client token using client ID and token
     */
    public function get_client_token() {
        if (!$this->token) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        
        if (empty($this->client_id)) {
            error_log('Whise API: Missing client ID');
            return false;
        }
        
        $request_body = [
            'ClientId' => intval($this->client_id)
        ];
        
        error_log('Whise API: Step 3 - Getting client token for ClientId: ' . $this->client_id);
        
        $response = wp_remote_post($this->api_url . '/v1/admin/clients/token', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode($request_body),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            error_log('Whise API: Client token error - ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        error_log('Whise API: Client token response code: ' . $status_code);
        error_log('Whise API: Client token response body: ' . $body);
        
        $data = json_decode($body, true);
        
        if (isset($data['token'])) {
            $this->client_token = $data['token'];
            error_log('Whise API: Step 3 - Client token obtained successfully');
            return true;
        }
        
        error_log('Whise API: Step 3 - Failed to get client token - no token in response');
        return false;
    }
    
    /**
     * Step 4: Get estates list using client token
     */
    public function get_estates($filters = []) {
        if (!$this->client_token) {
            if (!$this->get_client_token()) {
                error_log('Whise API: Failed to get client token for estates request');
                return false;
            }
        }
        
        // Build request body according to Whise API documentation
        $request_body = [
            'Filter' => [],
            'Field' => [
                'Excluded' => ['longDescription']
            ],
            'Page' => [
                'Limit' => 100,  // Set a reasonable limit
                'Offset' => 0
            ]
        ];
        
        // Add filters if provided - map to correct API parameter names
        if (!empty($filters)) {
            error_log('Whise API: Processing filters: ' . print_r($filters, true));
            
            // Map filter names to API field names according to Whise API documentation
            foreach ($filters as $key => $value) {
                switch ($key) {
                    case 'purpose':
                    case 'PurposeId':
                        $request_body['Filter']['PurposeIds'] = [intval($value)];
                        break;
                    case 'city':
                    case 'City':
                        $request_body['Filter']['City'] = $value;
                        break;
                    case 'category':
                    case 'CategoryId':
                        $request_body['Filter']['CategoryIds'] = [intval($value)];
                        break;
                    case 'price_min':
                    case 'PriceMin':
                        if (!isset($request_body['Filter']['PriceRange'])) {
                            $request_body['Filter']['PriceRange'] = ['Min' => 0, 'Max' => 999999999];
                        }
                        $request_body['Filter']['PriceRange']['Min'] = intval($value);
                        break;
                    case 'price_max':
                    case 'PriceMax':
                        if (!isset($request_body['Filter']['PriceRange'])) {
                            $request_body['Filter']['PriceRange'] = ['Min' => 0, 'Max' => 999999999];
                        }
                        $request_body['Filter']['PriceRange']['Max'] = intval($value);
                        break;
                    default:
                        // Pass through any other parameters as-is to Filter object
                        $request_body['Filter'][$key] = $value;
                }
            }
        }
        
        error_log('Whise API: Step 4 - Requesting estates with body: ' . json_encode($request_body));
        error_log('Whise API: Step 4 - API URL: ' . $this->api_url . '/v1/estates/list');
        error_log('Whise API: Step 4 - Client token: ' . substr($this->client_token, 0, 20) . '...');
        
        $response = wp_remote_post($this->api_url . '/v1/estates/list', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->client_token
            ],
            'body' => json_encode($request_body),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            error_log('Whise API: Estates request error - ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $headers = wp_remote_retrieve_headers($response);
        
        error_log('Whise API: Estates response code: ' . $status_code);
        error_log('Whise API: Estates response headers: ' . print_r($headers, true));
        error_log('Whise API: Estates response body length: ' . strlen($body));
        error_log('Whise API: Estates response body: ' . $body);
        
        $data = json_decode($body, true);
        
        if ($data) {
            error_log('Whise API: Step 4 - Estates data received successfully');
            if (isset($data['estates'])) {
                error_log('Whise API: Step 4 - Found ' . count($data['estates']) . ' estates');
                // Log first estate details for debugging
                if (count($data['estates']) > 0) {
                    $first_estate = $data['estates'][0];
                    error_log('Whise API: Step 4 - First estate: ID=' . ($first_estate['id'] ?? 'N/A') . 
                             ', PurposeId=' . ($first_estate['purposeId'] ?? 'N/A') . 
                             ', CategoryId=' . ($first_estate['categoryId'] ?? 'N/A') . 
                             ', Price=' . ($first_estate['price'] ?? 'N/A') . 
                             ', City=' . ($first_estate['city'] ?? 'N/A'));
                }
            } else {
                error_log('Whise API: Step 4 - No estates array in response');
            }
        } else {
            error_log('Whise API: Step 4 - Failed to decode estates response');
            error_log('Whise API: Step 4 - JSON decode error: ' . json_last_error_msg());
        }
        
        return $data;
    }
    
    /**
     * Get cities list using client token
     */
    public function get_cities() {
        if (!$this->client_token) {
            if (!$this->get_client_token()) {
                error_log('Whise API: Failed to get client token for cities request');
                return false;
            }
        }
        
        error_log('Whise API: Requesting cities list');
        
        $response = wp_remote_post($this->api_url . '/v1/estates/usedcities/list', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->client_token
            ],
            'body' => json_encode([]),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            error_log('Whise API: Cities request error - ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        error_log('Whise API: Cities response code: ' . $status_code);
        error_log('Whise API: Cities response body: ' . $body);
        
        $data = json_decode($body, true);
        
        if ($data) {
            error_log('Whise API: Cities data received successfully');
        }
        
        return $data;
    }
    
    /**
     * Get static data (purpose, category, etc.)
     */
    public function get_static_data($type) {
        // Try to get data from API first, fallback to hardcoded data
        if (!$this->client_token) {
            if (!$this->get_client_token()) {
                error_log('Whise API: Failed to get client token for static data');
                return $this->get_hardcoded_static_data($type);
            }
        }
        
        switch ($type) {
            case 'purpose':
                // Try to get purposes from API
                $response = wp_remote_post($this->api_url . '/v1/estates/purposes/list', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->client_token
                    ],
                    'body' => json_encode([]),
                    'timeout' => 30
                ]);
                
                if (!is_wp_error($response)) {
                    $data = json_decode(wp_remote_retrieve_body($response), true);
                    if ($data && isset($data['purposes'])) {
                        error_log('Whise API: Got purposes from API: ' . print_r($data['purposes'], true));
                        return $data['purposes'];
                    }
                }
                error_log('Whise API: Using hardcoded purposes');
                return $this->get_hardcoded_static_data($type);
                
            case 'category':
                // Try to get categories from API
                $response = wp_remote_post($this->api_url . '/v1/estates/categories/list', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->client_token
                    ],
                    'body' => json_encode([]),
                    'timeout' => 30
                ]);
                
                if (!is_wp_error($response)) {
                    $data = json_decode(wp_remote_retrieve_body($response), true);
                    if ($data && isset($data['categories'])) {
                        error_log('Whise API: Got categories from API: ' . print_r($data['categories'], true));
                        return $data['categories'];
                    }
                }
                error_log('Whise API: Using hardcoded categories');
                return $this->get_hardcoded_static_data($type);
                
            case 'price_ranges':
                return $this->get_hardcoded_static_data($type);
                
            default:
                return [];
        }
    }
    
    /**
     * Get hardcoded static data as fallback
     */
    private function get_hardcoded_static_data($type) {
        switch ($type) {
            case 'purpose':
                return [
                    ['id' => 1, 'name' => 'Te koop'],
                    ['id' => 2, 'name' => 'Te huur']
                ];
            case 'category':
                return [
                    ['id' => 1, 'name' => 'Appartement'],
                    ['id' => 2, 'name' => 'Huis'],
                    ['id' => 3, 'name' => 'Villa'],
                    ['id' => 4, 'name' => 'Kantoor'],
                    ['id' => 5, 'name' => 'Winkel'],
                    ['id' => 6, 'name' => 'Grond']
                ];
            case 'price_ranges':
                return [
                    ['min' => 0, 'max' => 500000, 'label' => '0 - €500.000'],
                    ['min' => 500000, 'max' => 1000000, 'label' => '€500.000 - €1.000.000'],
                    ['min' => 1000000, 'max' => 1500000, 'label' => '€1.000.000 - €1.500.000'],
                    ['min' => 1500000, 'max' => null, 'label' => '€1.500.000+']
                ];
            default:
                return [];
        }
    }
    
    /**
     * Format price for display
     */
    public function format_price($price, $currency = '€') {
        if ($price >= 1000000) {
            return $currency . ' ' . number_format($price / 1000000, 1, ',', '.') . 'M';
        } elseif ($price >= 1000) {
            return $currency . ' ' . number_format($price / 1000, 0, ',', '.') . 'K';
        } else {
            return $currency . ' ' . number_format($price, 0, ',', '.');
        }
    }
    
    /**
     * Get estate image URL
     */
    public function get_estate_image($estate, $size = 'urlLarge') {
        if (isset($estate['pictures']) && !empty($estate['pictures'])) {
            // Sort by order and get the first image
            usort($estate['pictures'], function($a, $b) {
                return $a['order'] - $b['order'];
            });
            return $estate['pictures'][0][$size];
        }
        return false;
    }
    
    /**
     * Test complete API connection flow
     */
    public function test_connection() {
        error_log('Whise API: Starting complete connection test');
        
        // Step 1: Authentication
        if (!$this->authenticate()) {
            return ['success' => false, 'message' => 'Step 1: Authentication failed'];
        }
        
        // Step 2: Get client list
        $clients = $this->get_client_list();
        if ($clients === false) {
            return ['success' => false, 'message' => 'Step 2: Failed to get client list'];
        }
        
        // Step 3: Get client token
        if (!$this->get_client_token()) {
            return ['success' => false, 'message' => 'Step 3: Failed to get client token'];
        }
        
        // Step 4: Get estates
        $estates = $this->get_estates();
        if ($estates === false) {
            return ['success' => false, 'message' => 'Step 4: Failed to fetch estates'];
        }
        
        return [
            'success' => true, 
            'message' => 'All steps successful', 
            'clients_count' => count($clients),
            'estates_count' => count($estates['estates'] ?? [])
        ];
    }
    
    /**
     * Test method to debug API calls with detailed logging
     */
    public function debug_api_call($filters = []) {
        if (!$this->client_token) {
            if (!$this->get_client_token()) {
                error_log('Whise API Debug: Failed to get client token');
                return false;
            }
        }
        
        // Build request body according to Whise API documentation
        $request_body = [
            'Filter' => [],
            'Field' => [
                'Excluded' => ['longDescription']
            ],
            'Page' => [
                'Limit' => 100,
                'Offset' => 0
            ]
        ];
        
        // Add filters if provided
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                switch ($key) {
                    case 'PurposeId':
                        $request_body['Filter']['PurposeIds'] = [intval($value)];
                        break;
                    case 'CategoryId':
                        $request_body['Filter']['CategoryIds'] = [intval($value)];
                        break;
                    case 'PriceMin':
                        if (!isset($request_body['Filter']['PriceRange'])) {
                            $request_body['Filter']['PriceRange'] = ['Min' => 0, 'Max' => 999999999];
                        }
                        $request_body['Filter']['PriceRange']['Min'] = intval($value);
                        break;
                    case 'PriceMax':
                        if (!isset($request_body['Filter']['PriceRange'])) {
                            $request_body['Filter']['PriceRange'] = ['Min' => 0, 'Max' => 999999999];
                        }
                        $request_body['Filter']['PriceRange']['Max'] = intval($value);
                        break;
                    default:
                        $request_body['Filter'][$key] = $value;
                }
            }
        }
        
        error_log('Whise API Debug: Making test API call');
        error_log('Whise API Debug: Request body: ' . json_encode($request_body));
        error_log('Whise API Debug: API URL: ' . $this->api_url . '/v1/estates/list');
        
        $response = wp_remote_post($this->api_url . '/v1/estates/list', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->client_token
            ],
            'body' => json_encode($request_body),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            error_log('Whise API Debug: Request error - ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $headers = wp_remote_retrieve_headers($response);
        
        error_log('Whise API Debug: Response status: ' . $status_code);
        error_log('Whise API Debug: Response headers: ' . print_r($headers, true));
        error_log('Whise API Debug: Response body: ' . $body);
        
        $data = json_decode($body, true);
        
        if ($data) {
            error_log('Whise API Debug: JSON decoded successfully');
            if (isset($data['estates'])) {
                error_log('Whise API Debug: Found ' . count($data['estates']) . ' estates');
            }
        } else {
            error_log('Whise API Debug: JSON decode failed: ' . json_last_error_msg());
        }
        
        return [
            'status_code' => $status_code,
            'headers' => $headers,
            'body' => $body,
            'data' => $data
        ];
    }
} 