<?php 
class TwicPics {        

    // Require class in functions file: require_once dirname(__FILE__) . '/inc/TwicPics.php';
    // Example with src set markup: $TwicPics = new TwicPics(); echo $TwicPics->get_img_srcset($img_id, array(880, 668, 380), "(max-width: 768px) 80vw, 60vw");
    // Example with single URL as string: echo $TwicPics->get_img($img_id, 746, 420, null, true);

    // Define member vars
    private $twicpics_source_url;
    private $params;
    
    // Define class constructor and assign member vars
    public function __construct($source,$params) {
        $this->twicpics_source_url = $source;
        $this->params = $params;        
    } 
    
    public function get_dims($id) {
        // Get WordPress image data
        $permalink = wp_get_attachment_image_src( $id, 'full', false );
        return [
          'width' =>$permalink[1],
          'height' => $permalink[2]
        ];
     }

    /**
     * @param Int attachment ID for requested image
     * @param Numeric optional numeric value representing desired image with
     * @param Numeric optional numeric value representing desired image height 
     * @return String HTML markup representing requested image and alt text
     */
    public function get_img($id, $width = null, $height = null, $crop = false, $as_string = false, $focus = 'auto') {                                   
        
        // Get WordPress image data
        $permalink = wp_get_attachment_image_src( $id, 'full', false );  
        $params = $this->params;                     
        $path = parse_url($permalink[0])['path'];	
        $default_width = $permalink[1];
        $default_height = $permalink[2];   

        // Prevent caller from request image taller than original image
        if ( $height > $default_height ) {
            $height = $default_height;
        }
        
        // Get image alt attribute value, provide fallback
        $altArr = get_post_meta( $id, '_wp_attachment_image_alt');       
        if ( isset($altArr[0]) ) {
            $alt = $altArr[0];
        }  else{
            $alt = get_bloginfo('name');
        }       

        // Set focus for the image 
        if(isset($focus) && !empty(trim($focus))){
            $params .= "/focus=" . $focus;
        }
   
        // Check for optional arguments and append values to $parms string
        if ( ! is_null($width) && is_numeric($width)  ) {
            $params .= "/resize=" . $width;
        }
        // if ( ! is_null($height) && is_numeric($height)  ) {
        //     $params .= "&h=" . $height;
        // }
        // Cropping image
        if($crop){
            if ( ! is_null($width) && is_numeric($width)  ) {
                $params .= "/crop=" . $width;
            }
    
            if ( ! is_null($height) && is_numeric($height)  ) {
                $params .= "x" . $height;
            }
        }
                      
        // Build twicpics url and associated markup
        $src = $this->twicpics_source_url . $path . $params;                
        $markup = "<img width='" . $default_width . "' height='" . $default_height . "' src='" . $src . "' alt='" . $alt . "'>";        
                
        if ( $src && $markup ) {
            if ( $as_string == true ) {
                return $src;
            } else {
                return $markup;
            }
        }
    }

    /**
     * @param Int attachment ID for requested image
     * @param Array array of image sizes. Default value is defined in constructor 
     * @return Array [default_img][sources][alt]
     */
    private function get_img_srcset_array($id, $img_sizes = null) {
        // Get WordPress image data        
        $permalink = wp_get_attachment_image_src( $id, 'full', false );          
        $path = parse_url($permalink[0])['path'];	        
        $default_width = $permalink[1];
        $default_height = $permalink[2];
        $params = $this->params;          
        
        // Get image alt attribute value, provide fallback
        $alt = get_post_meta( $id, '_wp_attachment_image_alt')[0];       
        if ( ! $alt ) { $alt = get_bloginfo('name'); }   
        
        // Define array of return values
        $srcset = array(
            'default_img' => null,
            'default_width' => $default_width,
            'default_height' => $default_height,
            'sources' => array(),                                  
            'alt' => $alt            
        );        
           
        // Loop through pre-defined sizes array and build twicpics URLs for each size.
        // Append each size to local $srcset['sources'] array
        foreach ( $img_sizes as $size ) { 
                        
            $size_params = "&w=" . $size['width']; 
            if ( $size['height'] ) {
                $size_params .= "&h=" . $size['height'];
            }

            $src = $this->twicpics_source_url . $path . $params . $size_params;    
            $src .= " " . $size['width'] . "w";        

            array_push($srcset['sources'], $src);
        }

        // Convert array values to comma separated string for easier template handling
        if ( ! empty($srcset['sources']) ) {
            $srcset['default_img'] = $this->twicpics_source_url . $path . $params;
            $srcset['sources'] = implode(', ', $srcset['sources']);
        }      

        // Return array of values
        if ($srcset) return $srcset; 
    }

    /**
     * @param Int attachment ID for requested image
     * @param Array array of image sizes: $min_width => $img_size
     * @return String markup with all associated image values
     */
    public function get_img_srcset($id, $img_sizes = null, $html_sizes_attr = null) {        
        
        // Check that call includes $img_sizes aregument, and that this argument is an array
        if ( isset($img_sizes) && is_array($img_sizes) ) {
            $img_sizes_array = array();

            // Loop through provided array values, split the values at "x." This supports the 
            // option to include a height definition at the function call site. If "x" char exists,
            // build an array with [width][height] and push to $sizes_array
            foreach( $img_sizes as $img_size ) {                                
                if ( is_string($img_size) && strpos($img_size, "x") ) {                    
                    $size_array = explode("x", $img_size);                                                         
                                        
                    if ( ! empty($size_array) ) {                        
                        array_push($img_sizes_array, array('width' => $size_array[0], 'height' => $size_array[1]));
                    }

                // If the function caller does not include an "x" in one of the sizes arguments, then we'll treat
                // this as just a width. Take this value and add it to the $img_sizes_array
                } else {
                    array_push($img_sizes_array, array('width' => $img_size));
                }               
            }                          

            // PAss img ID and formatted sizes to helper function to build return value
            $img = $this->get_img_srcset_array($id, $img_sizes_array);

            // Format return value and pass string back to function caller. NOTE: if is admin, omit lazy loading
            if ( ! empty($img) && is_array($img) ) {   
                if ( is_admin() ) {
                    $markup = "<img width='" . $img['default_width'] . "' height='" . $img['default_height'] . "' class='lazy' data-src='" . $img['default_img'] . "' data-srcset='" . $img['sources'] . "' alt='" . $img['alt'] . "' sizes='" . $html_sizes_attr . "'>";                                                
                    if ( ! empty( $markup ) ) return $markup;
                } else {
                    $markup = "<img data-polyfill-src='" . $img['default_img'] . "' width='" . $img['default_width'] . "' height='" . $img['default_height'] . "' srcset='" . $img['sources'] . "' alt='" . $img['alt'] . "' sizes='" . $html_sizes_attr . "'>";                                                
                    if ( ! empty( $markup ) ) return $markup;
                }                                 
            }
        }                    
    }
}