<?php 
class FacetWP_Facet_Tag_It
{

    function __construct() {
        $this->label = __( 'Tag-it', 'fwp' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;
    
    }


    /**
     * Generate the output HTML
     */
    function render( $params ) {

        $output = '';
        $facet = $params['facet'];
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];
                
        $output .= '<ul id="myTags">';
        $key = 0;
        if(is_array($selected_values)) foreach($selected_values as $selected_value)
        {
                 $output .= '<li class="facetwp-tagit-tag" data-value="' . $selected_value . '">';
                 $output .= esc_html( $selected_value );
                 $output .= '</li>';        
        }       
        $output .= '</ul>';

        return $output;
    }


    /**
     * Return array of post IDs matching the selected values
     * using the wp_facetwp_index table
     */
    function filter_posts( $params ) {
        global $wpdb;

        $output = array();
        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        //echo "<xmp>";print_r($params);exit;
        $sql = $wpdb->prepare( "SELECT DISTINCT post_id
            FROM {$wpdb->prefix}facetwp_index
            WHERE facet_name = %s",
            $facet['name']
        );
        
        foreach ( $selected_values as $key => $value ) {
            $results = facetwp_sql( $sql . " AND facet_display_value like ('%$value%')", $facet );                
            $output = ( $key > 0 ) ? array_intersect( $output, $results ) : $results;

            if ( empty( $output ) ) {
                break;
            }
        }     

        return $output;
    }


    /**
     * Load and save facet settings
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/tagit', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-count').val(obj.count);
    });

    wp.hooks.addFilter('facetwp/save/tagit', function($this, obj) {
        obj['source'] = $this.find('.facet-source').val();
        obj['count'] = $this.find('.facet-count').val();
        return obj;
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Parse the facet selections + other front-facing handlers
     */
    function front_scripts() {
?>
<script>

(function($) {

    
    wp.hooks.addAction('facetwp/refresh/tagit', function($this, facet_name) {                
        var selected_values = [];

        $('#myTags').find('.tagit-hidden-field').each(function() {
            selected_values.push($(this).val());
        });

        FWP.facets[facet_name] = selected_values;                        
    });

    wp.hooks.addFilter('facetwp/selections/tagit', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            choices.push({
                value: val,
                label: val
            });
        });
        return choices;
    });

  
     
})(jQuery);
</script>
<?php
    }


    /**
     * Admin settings HTML
     */
    function settings_html() {
?>
        <tr>
            <td>
                <?php _e('Count', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'The maximum number of facet choices to show', 'fwp' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="facet-count" value="10" /></td>
        </tr>
<?php
    }
}