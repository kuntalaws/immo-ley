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
        
        // Check cache first
        $cached_token = get_transient('whise_auth_token');
        if ($cached_token !== false) {
            $this->token = $cached_token;
            error_log('Whise API: Using cached auth token');
            return true;
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
            // Cache token for 1 hour (tokens usually expire after 2 hours)
            set_transient('whise_auth_token', $this->token, 3600);
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
        
        // Check cache first
        $cached_client_token = get_transient('whise_client_token_' . $this->client_id);
        if ($cached_client_token !== false) {
            $this->client_token = $cached_client_token;
            error_log('Whise API: Using cached client token');
            return true;
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
            // Cache client token for 1 hour
            set_transient('whise_client_token_' . $this->client_id, $this->client_token, 3600);
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
        
        $request_body = [
            'Field' => [
                'excluded' => ['longDescription']
            ]
        ];
        
        // Add filters if provided
        if (!empty($filters)) {
            // Map filter names to API field names
            $api_filters = [];
            foreach ($filters as $key => $value) {
                switch ($key) {
                    case 'purpose':
                        $api_filters['PurposeId'] = intval($value);
                        break;
                    case 'city':
                        $api_filters['City'] = $value;
                        break;
                    case 'category':
                        $api_filters['CategoryId'] = intval($value);
                        break;
                    case 'price_min':
                        $api_filters['PriceMin'] = intval($value);
                        break;
                    case 'price_max':
                        $api_filters['PriceMax'] = intval($value);
                        break;
                    default:
                        $api_filters[$key] = $value;
                }
            }
            $request_body = array_merge($request_body, $api_filters);
        }
        
        // Create cache key based on filters
        $cache_key = 'estates_' . md5(serialize($request_body));
        
        // Check cache first
        $cached_estates = get_transient('whise_' . $cache_key);
        if ($cached_estates !== false) {
            error_log('Whise API: Returning cached estates data');
            return $cached_estates;
        }
        
        error_log('Whise API: Step 4 - Requesting estates with body: ' . json_encode($request_body));
        
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
        
        error_log('Whise API: Estates response code: ' . $status_code);
        error_log('Whise API: Estates response body: ' . substr($body, 0, 1000) . '...');
        
        $data = json_decode($body, true);
        
        if ($data) {
            // Cache for 30 minutes
            set_transient('whise_' . $cache_key, $data, 1800);
            error_log('Whise API: Step 4 - Estates data cached successfully');
        } else {
            error_log('Whise API: Step 4 - Failed to decode estates response');
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
        
        // Check cache first
        $cached_cities = get_transient('whise_cities');
        if ($cached_cities !== false) {
            return $cached_cities;
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
            // Cache for 1 hour
            set_transient('whise_cities', $data, 3600);
            error_log('Whise API: Cities data cached successfully');
        }
        
        return $data;
    }
    
    /**
     * Get static data (purpose, category, etc.)
     */
    public function get_static_data($type) {
        // For now, we'll return hardcoded data based on the API documentation
        // In a real implementation, you might want to cache this data
        
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
} 