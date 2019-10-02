<?php
/*
Plugin Name: Word Count
Plugin URI: http://journeybyweb.com
Description: this is only count of post word
Version: 0.1
Author: Lancer A.K.D
Author URI: http://abir.journeybyweb.com/
License: GPLv2 or later
Text Domain: word_count
Domain Path: /languages/
*/



// function word_count_activation_hook(){
// }
// register_activation_hook(_FILE_, "word_count_activation_hook");


// function word_count_deactivation_hook(){
// }
// register_deactivation_hook(_FILE_, "word_count_deactivation_hook");


function word_count_load_textdomain(){
    load_plugin_textdomain("word_count", false, dirname(__FILE__)."/languages" );
}
add_action("plugin_loaded", "word_count_load_textdomain");


function word_count_post_words($content){
    $stripped_content = strip_tags($content);
    $post_words  = str_word_count($stripped_content);
    $label = __("Total Number of Words", "$post_words");
    $label = apply_filters("word_count_title", $label);
    $tag = apply_filters("word_count_tag", "h1");
    $content .= sprintf('<%s> %s : %s</%s>', $tag, $label, $post_words, $tag); 
    return $content;
}
add_filter('the_content', 'word_count_post_words');


function word_count_post_words_reading_time($content){
    $stripped_content = strip_tags($content);
    $post_words  = str_word_count($stripped_content);
    $reading_minute = floor($post_words/200);
    $reading_seconds = floor($post_words % 200 / (200/60) );
    $is_visible = apply_filters('show post word reading time', 1);
    if ($is_visible) {
        $label = __('এই পোস্ট এর reading সময় হছে', 'word_count');
        $label = apply_filters("word_reading_time_title", $label);
        $tag = apply_filters("word_reading_time_tag", "h5");
        $content .= sprintf('<%s> %s : %s মিনিট %s সেকেন্ড </%s>', $tag, $label, $reading_minute, $reading_seconds, $tag);
        }

    return $content;

}
add_filter('the_content', 'word_count_post_words_reading_time');