<?php 
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
class featured_circles_theme_hook {

public function __construct(){
    add_action( 'wp_fc_t_output', 'circle_theme_output', 10 );
}

public function circle_theme_output() {
   //global $my_settings_page;
   //$option = ;

    //if () {

    //get the global wp_simple_locations class
    global $featured_circles;

    //build default arguments
    $arguments = array(
        number_of_circles => get_option(id_number));

    //uses the main output function of the location class
    $parentClass = $featured_circles->get_circles_output($arguments);
    $html .= $parentClass;

    return $html;

echo $html;
}
}

$featured_circles_theme_hook = new featured_circles_theme_hook;

?>