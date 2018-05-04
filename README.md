# wp_featured_circles
Featured Circles is a php based plugin for wordpress. It's aim to to have a variety of methods for creating sizeable circles that featured choose content through a custom post type that can be used with a shortcode, widgets and a theme hook.
Currently the widget and shortcode are implimented.
Styles are to come later.

The shortcode is ['wp_circles']

*NEW Feature*
Theme Hook
A basic theme hook has been added to display all featured circles. Copy and paste the following code into your template where you would like it displayed.
<?php echo $theme_hook->circle_theme_output(); ?>
