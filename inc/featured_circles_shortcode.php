<?php
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
//defines the functionality for the location shortcode
class featured_circle_shortcode{
//on initialize
public function __construct(){
    add_action('init', array($this,'register_circle_shortcodes')); //shortcodes
}

//location shortcode
public function register_circle_shortcodes(){
    add_shortcode('wp_circles', array($this,'circle_shortcode_output'));
}

//shortcode display
public function circle_shortcode_output($atts, $content = '', $tag){

    //get the global wp_simple_locations class
    global $featured_circles;

    //build default arguments
    $arguments = shortcode_atts(array(
        'circle_id' => '',
        'number_of_circles' => -1)
    ,$atts,$tag);

    //uses the main output function of the location class
    $html = $featured_circles->get_circles_output($arguments);

    return $html;
}

}

$featured_circle_shortcode = new featured_circle_shortcode;
?>