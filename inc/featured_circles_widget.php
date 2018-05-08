<?php
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
//main widget used for displaying locations
class featured_circle_widget extends WP_widget{
//initialise widget values
public function __construct(){
    //set base values for the widget (override parent)
    parent::__construct(
        'featured_circle_widget',
        'Featured Circles Widget', 
        array('description' => 'A widget that displays your featured circles')
    );
    add_action('widgets_init',array($this,'register_wp_circle_widgets'));
}

//handles the back-end admin of the widget
    //$instance - saved values for the form
    public function form($instance){
        //collect variables 
        $circle_id = (isset($instance['circle_id']) ? $instance['circle_id'] : 'default');
        $number_of_circles = (isset($instance['number_of_circles']) ? $instance['number_of_circles'] : 5);

        ?>
        <p>Select your options below</p>
        <p>
            <label for="<?php echo $this->get_field_name('circle_id'); ?>">Circle to display</label>
            <select class="widefat" name="<?php echo $this->get_field_name('circle_id'); ?>" id="<?php echo $this->get_field_id('circle_id'); ?>" value="<?php echo $circle_id; ?>">
                <option value="default">All Circles</option>
                <?php
                $args = array(
                    'posts_per_page'    => -1,
                    'post_type'         => 'wp_featuured_circles'
                );
                $circles = get_posts($args);
                if($circles){
                    foreach($circles as $circle){
                        if($circle->ID == $circle_id){
                            echo '<option selected value="' . $circle->ID . '">' . get_the_title($circle->ID) . '</option>';
                        }else{
                            echo '<option value="' . $circle->ID . '">' . get_the_title($circle->ID) . '</option>';
                        }
                    }
                }
                ?>
            </select>
        </p>
        <p>
            <small>If you want to display multiple circless select how many below</small><br/>
            <label for="<?php echo $this->get_field_id('number_of_circles'); ?>">Number of Circles</label>
            <select class="widefat" name="<?php echo $this->get_field_name('number_of_circles'); ?>" id="<?php echo $this->get_field_id('number_of_circles'); ?>" value="<?php echo $number_of_circles; ?>">
                <option value="default" <?php if($number_of_circles == 'default'){ echo 'selected';}?>>All Circles</option>
                <option value="1" <?php if($number_of_circles == '1'){ echo 'selected';}?>>1</option>
                <option value="2" <?php if($number_of_circles == '2'){ echo 'selected';}?>>2</option>
                <option value="3" <?php if($number_of_circles == '3'){ echo 'selected';}?>>3</option>
                <option value="4" <?php if($number_of_circles == '4'){ echo 'selected';}?>>4</option>
                <option value="5" <?php if($number_of_circles == '5'){ echo 'selected';}?>>5</option>
                <option value="10" <?php if($number_of_circles == '10'){ echo 'selected';}?>>10</option>
            </select>
        </p>
        <?php
    }

	//handles updating the widget 
//$new_instance - new values, $old_instance - old saved values
public function update($new_instance, $old_instance){

    $instance = array();

    $instance['circle_id'] = $new_instance['circle_id'];
    $instance['number_of_circles'] = $new_instance['number_of_circles'];

    return $instance;
}
	
//handles public display of the widget
//$args - arguments set by the widget area, $instance - saved values
public function widget( $args, $instance ) {

    //get wp_simple_location class (as it builds out output)
    global $featured_circles;

    //pass any arguments if we have any from the widget
    $arguments = array();
    //if we specify a location

    //if we specify a single location
    if($instance['circle_id'] != 'default'){
        $arguments['circle_id'] = $instance['circle_id'];
    }
    //if we specify a number of locations
    if($instance['number_of_circles'] != 'default'){
        $arguments['number_of_circles'] = $instance['number_of_circles'];
    }

    $test = $featured_circles->get_circles_output($arguments);
	
    //get the output
    $html = '';

    $html .= $args['before_widget'];
    $html .= '<div class="widget">';
    $html .= $args[before_title];
    $html .= "Circles";
    $html .= $args["after_title"];
    //uses the main output function of the location class
	if ($test != "") {
    $html .= $featured_circles->get_circles_output($arguments);}
	else {$html .= "no output";}
    $html .= '</div>';
    $html .= $args['after_widget'];

    echo $html;
}
	
//registers our widget for use
public function register_wp_circle_widgets(){
    register_widget('featured_circle_widget');
}
	
}

$featured_circle_widget = new featured_circle_widget;
?>
