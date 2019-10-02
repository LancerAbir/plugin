<?php
/*
Plugin Name: Post to QR Code
Plugin URI: http://journeybyweb.com
Description: this is only for QR Code under every post
Version: 0.1
Author: Lancer A.K.D
Author URI: http://abir.journeybyweb.com/
License: GPLv2 or later
Text Domain: QR_code
Domain Path: /languages/
*/



// function word_count_activation_hook(){
// }
// register_activation_hook(_FILE_, "word_count_activation_hook");


// function word_count_deactivation_hook(){
// }
// register_deactivation_hook(_FILE_, "word_count_deactivation_hook");

//global value
$qr_code_countries = array(
    __( 'Afghanistan', 'QR_code' ),
    __( 'Bangladesh', 'QR_code' ),
    __( 'Bhutan', 'QR_code' ),
    __( 'India', 'QR_code' ),
    __( 'Maldives', 'QR_code' ),
    __( 'Nepal', 'QR_code' ),
    __( 'Sri Lanka', 'QR_code' ),
    __( 'Pakistan', 'QR_code' ),
);

//same name যদি 2 টা filer hook use এর জন্য init add করতে হবে
//global function
function qr_code_init(){
    global $qr_code_countries;
    $qr_code_countries = apply_filters('qr_code_countries', $qr_code_countries);
}
add_action("init", "qr_code_init");


//Plugin Load Text Domain
function qr_code_load_textdomain(){
    load_plugin_textdomain("QR_code", false, dirname(__FILE__)."/languages" );
}
add_action("plugin_loaded", "qr_code_load_textdomain");


function qr_code_post_words($content){
    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_type = get_post_type($current_post_id);
    $current_post_url = urlencode( get_the_permalink($current_post_id) );

    // Post Type Check
    $excluded_post_type = apply_filters('qr_code_post_type', array() );
    if (in_array( $current_post_type, $excluded_post_type )) {
        return $content;
    }

    //Size Modify
        //user কে width height change করার অপশন সেট করা.
        $width = get_option("qr_code_width"); 
        $height = get_option("qr_code_height"); 
        $width = $width ? $width : 185;
        $height = $height ? $height : 185;
    $size = apply_filters( "qr_code_size", "{$width}x{$height}" );

    //url এর মাধ্যমে image আনার system
    $image_src = sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $size, $current_post_url );
    $content .= sprintf(" <div class='qr-code'> <img src='%s' alt='%s'/> </div>", $image_src, $current_post_title ); 
    return $content;
}
add_filter('the_content', 'qr_code_post_words');


//Dashboard এর setting penal এর general এ option set করার option 
function qr_code_size_setting(){
    //Group Section 
    add_settings_section("qr_code_setting", __("QR Code Setting Option", "QR_code"), "qr_code_display_setting_call_back", "general"); 
    

    //Bootstrapping
    add_settings_field("qr_code_width", __("QR Code Width", "QR_code"), "qr_code_display_width_call_back", "general", "qr_code_setting");
    add_settings_field("qr_code_height", __("QR Code Height", "QR_code"), "qr_code_display_height_call_back", "general", "qr_code_setting");
    add_settings_field("qr_code_select", __("Select Saarc Countries", "QR_code"), "qr_code_select_call_back", "general", "qr_code_setting");
    add_settings_field("qr_code_checkbox", __("Saarc Countries Checkbox", "QR_code"), "qr_code_checkbox_call_back", "general", "qr_code_setting");
    add_settings_field("qr_code_toggle", __("Toggle Field", "QR_code"), "qr_code_toggle_call_back", "general", "qr_code_setting");


    //Registration
    register_setting("general", "qr_code_width", array("sanitize_callback" => "esc_attr"));
    register_setting("general", "qr_code_height", array("sanitize_callback" => "esc_attr"));
    register_setting("general", "qr_code_select", array("sanitize_callback" => "esc_attr"));
    register_setting("general", "qr_code_checkbox");
    register_setting("general", "qr_code_toggle");


    //Call Back Mini Toggle
    function qr_code_toggle_call_back(){
        $option = get_option('qr_code_toggle');
        echo '<div id="toggle1"></div>';
        echo '<input type="hidden" name="qr_code_toggle" id="qr_code_toggle" value="'.$option.'"/>';
    }
   

    //Call Back Checkbox Saarc Countries
    function qr_code_checkbox_call_back(){
        global $qr_code_countries;
        $option = get_option("qr_code_checkbox");
        
        foreach( $qr_code_countries as $country ){
            $selected = "";
            if ( is_array($option) && in_array( $country, $option )) :
                $selected = 'checked';
            endif;
            printf('<input type="checkbox" name="qr_code_checkbox[]" value="%s" %s /> %s </br>', $country, $selected ,$country);
            
        }
    }

    //Call Back Select Saarc Countries
    function qr_code_select_call_back(){
        global $qr_code_countries;
        $option = get_option("qr_code_select");
        
        printf("<select id='%s' name='%s' >", "qr_code_select", "qr_code_select");
        foreach($qr_code_countries as $country){
            $selected = '';
            if ($option == $country) :
                $selected = 'selected';
            endif;
            printf('<option value="%s" %s>%s</option>', $country, $selected ,$country);
            
        }
        echo "</select>";
    }

    //Call Back Setting Section
    function qr_code_display_setting_call_back(){
        echo "<p>" . __("Setting for QR code plugin", "QR_code") . "<p/>";
    }

    //Call Back Setting QR Code Width
    function qr_code_display_width_call_back(){
        $width = get_option("qr_code_width");
        printf("<input type='text' id='%s' name='%s' value='%s'/>", 'qr_code_width', 'qr_code_width', $width );
    }

    //Call Back Setting QR Code Height
    function qr_code_display_height_call_back(){
        $height = get_option("qr_code_height");
        printf("<input type='text' id='%s' name='%s' value='%s' />", 'qr_code_height', 'qr_code_height', $height );
    }

}
add_action("admin_init", "qr_code_size_setting");




//enqueue css and js file with plugin
function qr_code_toggole_assets($screen){
    if ( 'options-general.php' == $screen ) {
        wp_enqueue_style( 'qr-code-minitoggle-css', plugin_dir_url( __FILE__ ) . "/assets/css/minitoggle.css" );
        wp_enqueue_script( 'qr-code-minitoggle-js', plugin_dir_url( __FILE__ ) . "/assets/js/minitoggle.js", array("jquery"),  true );
        wp_enqueue_script( 'qr-code-qr-main-js', plugin_dir_url( __FILE__ ) . "/assets/js/qr.main.js", array("jquery"), time(), true );
    } 
}
add_action('admin_enqueue_scripts', 'qr_code_toggole_assets');

