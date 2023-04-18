<?php
/**
 * execute the shortcode of the page and show only it's result:
 * by this you can create feeds....
 */
?>
<?php
#function filter_function_name($content) { return $content; }
#add_filter( 'the_content', 'filter_function_name' );
while ( have_posts() ) : the_post();
  the_content();
endwhile;
?>