<?php
/*
Plugin Name: Tiny Slider
Plugin URI: http://journeybyweb.com
Description: this is only for Tiny Slider under every post
Version: 0.1
Author: Lancer A.K.D
Author URI: http://abir.journeybyweb.com/
License: GPLv2 or later
Text Domain: TinySlider
Domain Path: /languages/
*/


//Plugin Load Text Domain
function tinys_load_textdomain(){
    load_plugin_textdomain("TinySlider", false, dirname(__FILE__)."/languages" );
}
add_action("plugin_loaded", "tinys_load_textdomain");


//Image Size Add
function tinys_init(){
    add_image_size('tinys-slider', 800, 600, true); 
}
add_action('init', 'tinys_init');


//Enqueue CSS and JS File With Plugin
function tinys_slider_assets(){
    wp_enqueue_style( 'tinyslider-css', '//cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/tiny-slider.css', null, '1.0' );
    // wp_enqueue_style( 'tinyslider-css', plugin_dir_url( __FILE__ ) . "/assets/css/tiny-slider.css" );

    wp_enqueue_script( 'tinyslider-js', '//cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js', null, '1.0', true );
    // wp_enqueue_script( 'tinyslider-js', plugin_dir_url( __FILE__ ) . "/assets/js/tiny-slider.js", array("jquery"), '1.0', true );
    wp_enqueue_script( 'tinys-main-js', plugin_dir_url( __FILE__ ) . "/assets/js/main.js", array("jquery"), '1.0', true );
}
add_action('wp_enqueue_scripts', 'tinys_slider_assets');


//Tiny Slider Short Code (Parent)
function tinys_shortcode_tslider( $attributes, $content ){
    //default value
    $default = array(
        'width' => 800,
        'height' => 600,
        'id' => ''
    );
    $params = shortcode_atts( $default, $attributes );
    $content = do_shortcode( $content );

    $shortcode_output = 
            "<div id=\"{$params['id']}\" style=\"width:{$params['width']}; height:{$params['height']}\" >
                <div class=\"slider\">
                    {$content}
                </div>
            </div>";
    return $shortcode_output;
}
add_shortcode('tslider', 'tinys_shortcode_tslider');



//Tiny Slider Short Code (Child)
function tinys_shortcode_tslide($attributes){
    //default value
    $default = array(
        'caption' => '',
        'id' => '',
        'size' => 'tinys-slider'
    );
    $params = shortcode_atts( $default, $attributes );
    
    $image_src = wp_get_attachment_image_src($params['id'],$params['size']);
    $shortcode_output = 
            "<div class=\"slider\">
                <p> <img src=\"{$image_src[0]}\" alt=\"{$params['caption']}\"> </p>
                <p> {$params['caption']} </p>
            </div>";
    return $shortcode_output;
}
add_shortcode('tslide', 'tinys_shortcode_tslide');