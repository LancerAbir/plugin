<?php
/*
Plugin Name: Assets Ninja
Plugin URI: http://journeybyweb.com
Description: this is only for Assets Management.
Version: 0.1
Author: Lancer A.K.D
Author URI: http://abir.journeybyweb.com/
License: GPLv2 or later
Text Domain: AssetsNinja
Domain Path: /languages/
*/

//Constant Define Public Directory URI
define("ASN_ASSETS_DIR", plugin_dir_url(__FILE__) . "assets/");
define("ASN_ASSETS_PUBLIC_DIR", plugin_dir_url(__FILE__) . "assets/public");
define("ASN_ASSETS_ADMIN_DIR", plugin_dir_url(__FILE__) . "assets/admin");

//Object Oriented Style Class Adds
class AssetsNinja{
    
    private $version;
    
    //Object Oriented Style Construct
    function __construct() {

        //version
        $this->version = time();

        add_action('init', array($this, 'asn_init'));

        add_action("plugin_loaded", array( $this, "asn_load_textdomain" ));
        add_action("wp_enqueue_scripts", array($this, "load_front_assets"));
        add_action("admin_enqueue_scripts", array($this, "load_admin_assets"));

        add_shortcode('bg_media', array($this, "asn_bg_media_shortCode" ));
    }

    //style='width:350px; height:250px; background-image:url({$attachment_image_src[0]})'
    //Deregister & Register CSS + JS file
    function asn_init(){
        wp_deregister_style('font-awesome.min.css');
        wp_register_style('font-awesome .min.css', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css');

        wp_deregister_script( 'tinyslider-js' );
        wp_register_script( 'tinyslider-js', plugin_dir_url( __FILE__ ) . "/assets/js/tiny-slider.js", array("jquery"), '1.0', true  );
    }

    //Enqueue CSS and JS File With Plugin Admin
    function load_admin_assets($screen) {
        //শুধু মাত্র এই css & js file dashboard এ setting panel এর general এ add হবে 
        $_screen = get_current_screen();
        if ( 'edit.php' == $screen && ($_screen->post_type == "page" || $_screen->post_type == "post") ) {
            wp_enqueue_style( 'asn-admin-css', ASN_ASSETS_ADMIN_DIR."/css/admin.main.css", null, $this->version );
            
            wp_enqueue_script( 'asn-admin-js', ASN_ASSETS_ADMIN_DIR."/js/admin.main.js", array("jquery"), $this->version, true );
        }
    }

    //Enqueue CSS and JS File With Plugin Public
    function load_front_assets() {
        wp_enqueue_style( 'asn-main-css', ASN_ASSETS_PUBLIC_DIR."/css/main.css", null, $this->version );

        //Inline CSS Off
        $attachment_image_src = wp_get_attachment_image_src( "33", 'medium' );
        $data = <<<EOD
        #media {
            background-image:url({$attachment_image_src[0]});
            background-position: center;
            background-repeat: no-repeat;
        }
EOD;
        wp_add_inline_style( 'asn-main-css', $data );

        wp_enqueue_script( 'asn-main-js', ASN_ASSETS_PUBLIC_DIR."/js/main.js", array("jquery"), $this->version, true );


        //PHP থেকে javaScript এ data pass করার 
        $data = array(
            'name' => 'Lnacer Abir',
            'url'  => 'http://facebook.com'
        );
        $more_data = array(
            'name' => 'A.K.D',
            'url'  => 'http://youtube.com'
        );
        wp_localize_script( 'asn-main-js', 'object_name', $data );
        wp_localize_script( 'asn-main-js', 'object_name_02', $more_data );
    } 

    //Plugin Load Text Domain
    function asn_load_textdomain() {
        load_plugin_textdomain("AssetsNinja", false, dirname(__FILE__)."/languages" );
    }

    //Background Image ShortCode
    function asn_bg_media_shortCode($attributes) {
        $shortcode_output = <<<EOD
        <div id='media'></div>
EOD;

        return $shortcode_output;
    }

    
}
new AssetsNinja();