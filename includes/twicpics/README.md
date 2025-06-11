## Motivations
This class is being developed as a way to more quickly add Imgix functionality to a custom WordPress theme. The goal of using this approach is to omit any image handling by WordPress and off load all optimization, resizing, and related functionality to Imgix (https://www.imgix.com/). When using this class, no custom thumbnail sizes should be created in WordPress. Additionally, no image optimization plugins should be used. Image sizing and optimization are handled by Imgix in real time.

## Setup
1. **Create Imgix source**
You'll need to create a new "source" on Imgix (https://docs.imgix.com/setup/creating-sources - use Web Proxy option). After creating a new source Imgix will provide you with a source URL. You'll need this URL to update the classes configuration.
2. **Add Imgix source URL**  
Replace the existing string value in the constructor method on line 14 with your Imgix source URL. 
3. **Include Imgix class**  
You'll need to require the Imgix.php class in your functions.php file. Copy the class file into your theme directory and require the class file in your functions.php file.  
Example with Imgix.php class included in the /inc project directory:  `require_once dirname(__FILE__) . '/inc/Imgix.php';`

## Usage
1. **Instantiate class**
To use this class you'll need to instantiate it each template that you want to use it in. In the template that you're using this class in, instantiate a new instance of the class and assign it to a local variable. Example: `$Imgix = new Imgix();`
2. **Use appropriate helper class**
This class exposes two public functions that can be called against an instance of the Imgix class. Each function returns image markup transformed to use the Imgix source URL.

## Available Functions
1. **get_img($id, $width, $height, $html_sizes_attr, $as_string)**
Imgix will crop images to meet the requested dimensions. If width or height are omitted they Imgix will return the full size of the image. If only one value is provided (width or height) Imgix will crop the image to match the requested dimension. This function returns either a single image. The last argument controls weather the image is returned as a URLs string or fully contructed markup (<Img> tag).  

    1. $id (Int): the WordPress attachment image ID you're requesting
    2. $width (Int): the width of the image you're requesting
    3. $height (Int): the height of the image you're requesting
    4. $html_sizes_attr (String): to fully support responsize images, this option represents the "sizes" HTML attribute to be applied to the returned markup. Add this attribute exactly like you would when hard-coding the "sizes" attribute when using source sets.
    5. $as_string (Bool): if false function returns pre-built markup. If true function returns image URL as string (useful for defining background images).  

2. **get_img_srcset($id, $img_sizes, $html_sizes_attr)**
This function returns an <img> tag with all attributes defined and ready to be rendered to the front-end.  

    1. $id (Int): the WordPress attachment image ID you're requesting  
    2. $img_sizes (Array): this argument supports either a width (only) or a width and height for each image size requested. To define only a width, provide an Int value. To define a width and height, provide a string formatted as '[width Int]x[height Int]'.
    3. $html_sizes_attr (String): to fully support responsize images, this option represents the "sizes" HTML attribute to be applied to the returned markup. Add this attribute exactly like you would when hard-coding the "sizes" attribute when using source sets. 

## Example Usage
1. Render <img> tag with all attributes:  
`<?php echo $Imgix->get_img_srcset($img_id, array(880, 668, 380), "(max-width: 768px) 80vw, 60vw"); ?>`
2. Return image URL as tring:  
`<?php $img_url = $Imgix->get_img($img_id, 746, 420, null, true); ?>`
