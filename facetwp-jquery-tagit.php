<?php
/*
Plugin Name: jQuery TagIt field for FacetWP
Plugin URI: https://github.com/alfonsomatute/wordpress_facetwp_jquery_tag_it_plugin
Description: FacetWP extension that lets create a field that uses the jQuery-UI TagIt field type. This field lets the user create tags on the fly and FacetWP filters the content that matches the tags.
Version: 1.0
Author: Alfonso Matute Baena
*/
function custom_hooks_enqueue_styles() {
    wp_enqueue_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js', array ( 'jquery' ));
    wp_enqueue_script( 'tag-it', '/wp-content/plugins/custom-hooks/js/tag-it.js', array ( 'jquery','jquery-ui' ));
    wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css');
    wp_enqueue_style( 'tag-it', '/wp-content/plugins/facetwp-jquery-tagit/css/jquery.tagit.css');
    
}
add_action( 'wp_enqueue_scripts', 'custom_hooks_enqueue_styles' );


include_once('FacetWP_Facet_Tag_It.class.php');
add_filter( 'facetwp_facet_types', function( $facet_types ) {
    $facet_types['tagit'] = new FacetWP_Facet_Tag_It();
    return $facet_types;
});

add_action( 'wp_head', function() {
?>
<script>
(function($) {
    $(document).on('facetwp-loaded', function() {        
        $("#myTags").tagit({
            afterTagAdded: function(event, ui) {                
                if(!ui.duringInitialization) FWP.autoload();                
            },
            afterTagRemoved: function(event, ui) {                
                FWP.autoload();                
            }
});       
     });
})(jQuery);
</script>
<?php
}, 100 );






