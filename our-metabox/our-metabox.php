<?php
/*
Plugin Name: Our Metabox
Plugin URI: http://journeybyweb.com
Description: this is only Metabox for every post.
Version: 0.1
Author: Lancer A.K.D
Author URI: http://abir.journeybyweb.com/
License: GPLv2 or later
Text Domain: our_metabox
Domain Path: /languages/
*/


//Object Oriented Style Class Adds
class OurMetaBox{

    //Object Oriented Style Construct
    public function __construct() {

        //version
        $this->version = time();

        // All hook
            //add text domain
            add_action("plugin_loaded", array( $this, "omb_load_textdomain" ));

            //add meta box hook
            add_action("admin_menu", array( $this, "omb_add_metabox" ));
            
            //save meta box data hook
            add_action('save_post', array( $this, "omb_save_metabox" ) );
    }


    private function is_secured($nonce_field, $action, $post_id){

        $nonce = isset( $_POST[ $nonce_field ] ) ? $_POST[$nonce_field] : '' ;

        //যদি $nonce null থাকে তাহলে $post_id return করবে
        if ( $nonce == "") {
            return false;
        }

        //যদি $nonce থাকে তাহলে $nonce কে verify করবো, আসলেই আমাদের form থেকে এসেছে কিনা 
        if (!wp_verify_nonce( $nonce, $action )) {
            return false;
        }

        //current user আসলেই edit করতে পারে কিনা
        if (!current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        //যদি WordPress post auto save করে তাহলে আমাদের meta box এর দরকার নাই
        if (wp_is_post_autosave($post_id) ) {
            return false;
        }

        //যদি WordPress post revision করে তাহলে আমাদের meta box এর দরকার নাই
        if (wp_is_post_revision($post_id) ) {
            return false;
        }
        return true;
    }

    //save meta data box 
    function omb_save_metabox($post_id) {
        if (!$this->is_secured('omb_location_field', 'omb_location', $post_id)) {
            return $post_id; 
        }
        
        $location     = isset( $_POST['omb_location'] ) ? $_POST['omb_location'] : '' ;
        $country      = isset( $_POST['omb_country'] ) ? $_POST['omb_country'] : '' ;
        $are_you_sure = isset( $_POST['omb_are_you_sure'] ) ? $_POST['omb_are_you_sure'] : '' ;
        $colors       = isset( $_POST['omb_clr'] ) ? $_POST['omb_clr'] : array();
        // $colors2      = isset( $_POST['omb_color'] ) ? $_POST['omb_color'] : array();

        //যদি $location null থাকে তাহলে $post_id || $country return করবে
        if ($location == "" || $country == "") {
            return $post_id;
        } 

        //sanitize data
        $location = sanitize_text_field($location);
        $country  = sanitize_text_field($country);

        
        //update data কাজ করার জন্য
        update_post_meta($post_id, 'omb_location', $location );
        update_post_meta($post_id, 'omb_country', $country );
        update_post_meta($post_id, 'omb_are_you_sure', $are_you_sure );
        update_post_meta($post_id, 'omb_clr', $colors );
        // update_post_meta($post_id, 'omb_color', $colors2 );
    }

    //add meta box
    function omb_add_metabox() {
        add_meta_box('omb_post_location', __('Location Info', 'our_metabox'), array( $this, 'omb_display_metabox_call_back'), array('post', 'page'), 'normal' );
    }
        //meta box call back -> omb_add_metabox
        function omb_display_metabox_call_back($post) {
            $location     = get_post_meta( $post->ID, 'omb_location', true );
            $country      = get_post_meta( $post->ID, 'omb_country', true );
            $are_you_sure = get_post_meta( $post->ID, 'omb_are_you_sure', true );
            $checked      = $are_you_sure = 1 ? 'checked' : ''; 

            $saved_colors  = get_post_meta( $post->ID, 'omb_clr', true );
            print_r($saved_colors);
            // $saved_color   = get_post_meta( $post->ID, 'omb_color', true );
          
            $label_1      = __( "Location", "our_metabox" );
            $label_2      = __( "Country", "our_metabox" );
            $label_3      = __( "Are You Sure", "our_metabox" );
            $label_4      = __( "Colors", "our_metabox" );

            $colors       = array( 'red', 'yellow', 'blue', 'white', 'green', 'black' );
            

            //admin panel থেকে data submit হছে কিনা & valid data কিনা তা check করার জন্য wp_nonce_field( '', '' );
            wp_nonce_field( 'omb_location', 'omb_location_field' );

            $metabox_html = <<<EOD
            <p>
                <label for="omb_location">{$label_1}: </label>
                <input type="text" name="omb_location" id="omb_location" value="{$location}" />
                    <br/>
                <label for="omb_country">{$label_2}: </label>
                <input type="text" name="omb_country" id="omb_country" value="{$country}" />
            </p>
            <p>
                <label for="omb_are_you_sure">{$label_3}: </label>    
                <input type="checkbox" name="omb_are_you_sure" id="omb_are_you_sure" value="1" {$checked} />
            </p>
            
            <p>
                <label> {$label_4}: </label> 
EOD;
            
                foreach( $colors as $color ) {
                    $upt_color = strtoupper($color);
                    $color_checked = in_array( $color, $saved_color ) ? 'checked' : '';
                    $metabox_html .= <<<EOD
                    <label for="omb_clr_{$color}"><br/>{$upt_color} </label> 
                    <input type="checkbox" name="omb_clr[]" id="omb_clr_{$color}" value="{$color}" {$color_checked}/>
EOD;
            }
            $metabox_html .= "</p>";



            echo $metabox_html;
        }


    //Plugin Load Text Domain
    public function omb_load_textdomain() {
        load_plugin_textdomain("our_metabox", false, dirname(__FILE__)."/languages" );
        
    }
}
new OurMetaBox();