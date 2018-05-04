<?php
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
/*
Plugin Name: Featured Circles
Plugin URI:  https://github.com/Sental/wp_featured_circles
Description: Creates circles to show featured items for a website.
Version:     1.0.0
Author:      Mark Rees
Author URI:  http://www.rees.solutions
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class wp_featured_circles{
	
//magic function (triggered on initialization)
public function __construct(){

    add_action('init', array($this,'register_fcircles_content_type')); //register location content type
	add_action('init', array($this,'circles_init')); //register category for circles
    add_action('add_meta_boxes', array($this,'add_circle_meta_boxes')); //add meta boxes
    add_action('save_post_wp_featuured_circles', array($this,'save_circle')); //save location
    add_action('admin_enqueue_scripts', array($this,'enqueue_admin_scripts_and_styles')); //admin scripts and styles
    add_action('wp_enqueue_scripts', array($this,'enqueue_public_scripts_and_styles')); //public scripts and styles
    add_filter('the_content', array($this,'prepend_circle_meta_to_content')); //gets our meta data and dispayed it before the content

    register_activation_hook(__FILE__, array($this,'plugin_activate')); //activate hook
    register_deactivation_hook(__FILE__, array($this,'plugin_deactivate')); //deactivate hook

}

//register the location content type
public function register_fcircles_content_type(){
     //Labels for post type
     $labels = array(
           'name'               => 'Featured Circle',
           'singular_name'      => 'Featured Circle',
           'menu_name'          => 'Featured Circles',
           'name_admin_bar'     => 'Featured Circle',
           'add_new'            => 'Add New', 
           'add_new_item'       => 'Add New Circle',
           'new_item'           => 'New Circle', 
           'edit_item'          => 'Edit Circle',
           'view_item'          => 'View Circle',
           'all_items'          => 'All Circles',
           'search_items'       => 'Search Circles',
           'parent_item_colon'  => 'Parent Circle:', 
           'not_found'          => 'No Circles found.', 
           'not_found_in_trash' => 'No Circles found in Trash.',
       );
       //arguments for post type
       $args = array(
           'labels'            => $labels,
           'public'            => true,
           'publicly_queryable'=> true,
           'show_ui'           => true,
           'show_in_nav'       => true,
           'query_var'         => true,
           'hierarchical'      => true,
           'supports'          => array('title','thumbnail','editor'),
           'has_archive'       => true,
           'menu_position'     => 20,
           'show_in_admin_bar' => true,
           'menu_icon'         => 'dashicons-layout',
           'rewrite'            => array('slug' => 'circles', 'with_front' => 'true'),
           'capability_type'    => 'post',
		   'taxonomies'          => array( 'circlecat' )
       );
       //register post type
       register_post_type('wp_featuured_circles', $args);
}

function circles_init() {
    // create a new taxonomy
    register_taxonomy(
        'circlecat',
        'wp_featuured_circles',
        array(
            'label' => __( 'Featured Circles' ),
            'rewrite' => array( 'slug' => 'circlecat' ),
            'hierarchical' => true,
            )
        );
}

//adding meta boxes for the location content type*/
public function add_circle_meta_boxes(){

    add_meta_box(
        'wp_circle_meta_box', //id
        'Circle Link', //name
        array($this,'circle_meta_box_display'), //display function
        'wp_featuured_circles', //post type
        'normal', //location
        'default' //priority
    );
}

//display function used for our custom location meta box*/
public function circle_meta_box_display($post){

    //set nonce field
    wp_nonce_field('wp_circle_nonce', 'wp_circle_nonce_field');

    //collect variables
    $wp_circle_link = get_post_meta($post->ID,'wp_circle',true);

    ?>
    <p>Enter additional information about your Circle </p>
    <div class="field-container">
        <?php 
        //before main form elementst hook
        do_action('wp_circle_admin_form_start'); 
        ?>
        <div class="field">
            <label for="wp_circle">Link to Featured</label>
            <input type="text" name="wp_circle" id="wp_circle" value="<?php echo $wp_circle_link;?>"/>
        </div>
        
    <?php 
    //after main form elementst hook
    do_action('wp_circle_admin_form_end'); 
    ?>
    </div>
    <?php

}

//triggered on activation of the plugin (called only once)
public function plugin_activate(){  
    //call our custom content type function
    $this->register_fcircles_content_type();
    //flush permalinks
    flush_rewrite_rules();
}

//trigered on deactivation of the plugin (called only once)
public function plugin_deactivate(){
    //flush permalinks
    flush_rewrite_rules();
}

public function prepend_circle_meta_to_content($content){

    global $post, $post_type;

    //display meta only on our locations (and if its a single location)
    if($post_type == 'wp_featuured_circles' && is_singular('wp_featuured_circles')){

        //collect variables
        $wp_circle_id = $post->ID;
        $wp_circle_link = get_post_meta($post->ID,'wp_circle',true);

        //display
        $html = '';

        $html .= '<section class="meta-data">';

        //hook for outputting additional meta data (at the start of the form)
        do_action('wp_circle_meta_data_output_start',$wp_circle_id);

        $html .= '<p>';
        //phone
        if(!empty($wp_circle_link)){
            $html .= '<b>Circle Link:</b> ' . $wp_circle_link . '</br>';
        }
        $html .= '</p>';

        //hook for outputting additional meta data (at the end of the form)
        do_action('wp_circle_meta_data_output_end',$wp_circle_id);

        $html .= '</section>';
        $html .= $content;

        return $html;  


    }else{
        return $content;
    }

}

//main function for displaying locations (used for our shortcodes and widgets)
public function get_circles_output($arguments = ""){

    //default args
    $default_args = array(
        'circle_id'   => '',
        'number_of_circles'   => '-1'
    );

    //update default args if we passed in new args
    if(!empty($arguments) && is_array($arguments)){
        //go through each supplied argument
        foreach($arguments as $arg_key => $arg_val){
            //if this argument exists in our default argument, update its value
            if(array_key_exists($arg_key, $default_args)){
                $default_args[$arg_key] = $arg_val;
            }
        }
    }

    //find locations
    $circle_args = array(
        'post_type'     => 'wp_featuured_circles',
        'posts_per_page' => $default_args['number_of_circles'],
        'post_status'   => 'publish'
    );
    //if we passed in a single location to display
    if(!empty($default_args['circle_id'])){
        $circle_args['include'] = $default_args['circle_id'];
    }

    //output
    $html = '';
    $circles = get_posts($circle_args);
    //if we have locations 
    if($circles){
        $html .= '<article class="circle_list cf">';
        //foreach location
        foreach($circles as $circle){
            $html .= '<section class="circle">';
                //collect location data
                $wp_circle_id = $circle->ID;
                $wp_circle_title = get_the_title($wp_circle_id);
                $wp_circle_thumbnail = get_the_post_thumbnail($wp_circle_id,'thumbnail');
                $wp_circle_content = apply_filters('the_content', $circle->post_content);
                if(!empty($wp_circle_content)){
                    $wp_circle_content = strip_shortcodes(wp_trim_words($wp_circle_content, 40, '...'));
                }
                $wp_circle_permalink = get_permalink($wp_circle_id);
                $wp_circle_link = get_post_meta($wp_circle_id, 'wp_circle', true);

                //apply the filter before our main content starts 
                //(lets third parties hook into the HTML output to output data)
                $html = apply_filters('wp_circle_before_main_content', $html);

                //title
                $html .= '<h2 class="title">';
                    $html .= '<a href="' . $wp_circle_permalink . '" title="view circle">';
                        $html .= $wp_circle_title;
                    $html .= '</a>';
               $html .= '</h2>';


                //image & content
                if(!empty($wp_circle_thumbnail) || !empty($wp_circle_content)){
                //phone & email output
                if(!empty($wp_circle_link)){
                    $link .= '<a href="' . $wp_circle_link . '">' . $wp_circle_thumbnail . '</a>';
                    $html .= $link;}
                    else {
                    if(!empty($wp_circle_thumbnail)){
                        $html .= $wp_circle_thumbnail;}}

                    $html .= '<p class="image_content">';
                    if(!empty($wp_circle_content)){
                        $html .=  $wp_circle_content;}
                    

                    $html .= '</p>';}
                

                //apply the filter after the main content, before it ends 
                //(lets third parties hook into the HTML output to output data)
                $html = apply_filters('wp_circle_after_main_content', $html);

                //readmore
                $html .= '<a class="link" href="' . $wp_circle_permalink . '" title="view circle">View Circle</a>';
            $html .= '</section>';
        }
        $html .= '</article>';
        $html .= '<div class="cf"></div>';
    }
else {$html .= 'circles was null';}
    return $html;
}

//triggered when adding or editing a location
public function save_circle($post_id){

    //check for nonce
    if(!isset($_POST['wp_circle_nonce_field'])){
        return $post_id;
    }   
    //verify nonce
    if(!wp_verify_nonce($_POST['wp_circle_nonce_field'], 'wp_circle_nonce')){
        return $post_id;
    }
    //check for autosave
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
        return $post_id;
    }

    //get our phone, email and address fields
    $wp_circle_link = $wp_circle_link = isset($_POST['wp_circle']) ? sanitize_text_field($_POST['wp_circle'])  : '';

    //update phone, memil and address fields
    update_post_meta($post_id, 'wp_circle', $wp_circle_link);

    //location save hook 
    //used so you can hook here and save additional post fields added via 'wp_location_meta_data_output_end' or 'wp_location_meta_data_output_end'
    do_action('wp_circle_admin_save',$post_id);

}

//enqueus scripts and stles on the back end
public function enqueue_admin_scripts_and_styles(){
    wp_enqueue_style('wp_circle_admin_styles', plugin_dir_url(__FILE__) . '/css/wp_circles_admin_styles.css');
}

//enqueues scripts and styled on the front end
public function enqueue_public_scripts_and_styles(){
    wp_enqueue_style('wp_circle_public_styles', plugin_dir_url(__FILE__). '/css/wp_circles_public_styles.css');

}

}

//include shortcodes
include(plugin_dir_path(__FILE__) . 'inc/wp_circles_shortcode.php');
//include widgets
include(plugin_dir_path(__FILE__) . 'inc/wp_circles_widget.php');
//include settings
include(plugin_dir_path(__FILE__) . 'inc/wp_featured_circles_settings.php');
//include theme hook
include(plugin_dir_path(__FILE__) . 'inc/theme_hook.php');

$wp_featured_circles = new wp_featured_circles;
?>
