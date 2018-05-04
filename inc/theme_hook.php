<?php 
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
class theme_hook {

public function __construct(){
    add_action( 'wp_fc_before_main_content', 'output_content_wrapper', 10 );
    add_action( 'wp_fc_after_main_content', 'output_content_wrapper_end', 10 );
    add_action( 'wp_fc_t_output', 'circle_theme_output', 10 );
}

public function output_content_wrapper() {
echo '<div class="featured_circles">';
do_action('wp_fc_t_output');
}

public function output_content_wrapper_end() {
echo '</div>';
}

public function circle_theme_output() {
   //global $my_settings_page;
   //$option = ;

    //if () {

    //get the global wp_simple_locations class
    global $wp_featured_circles;

    //build default arguments
    $arguments = array(
        number_of_circles => get_option(id_number));

    //uses the main output function of the location class
    $parentClass = $wp_featured_circles->get_circles_output($arguments);
    $html .= $parentClass;

    return $html;

echo $html;
}
}

$theme_hook = new theme_hook;

?>