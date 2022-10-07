<?php
/*
Plugin Name: R-API Custom fields
Plugin URI: 
Description: This plugin has been generated for the rest api custom field manage.
Author: Shubham Raturi
Version: 1.0
Author URI: 
*/
global $wpdb;
define('PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('PLUGIN_URL', plugin_dir_url( __FILE__ ) );


register_activation_hook(__FILE__,'ith_rest_api_install');
register_deactivation_hook(__FILE__ , 'ith_rest_api_uninstall' );
function ith_rest_api_install(){ 
	
    global $wpdb;
	
}
function ith_rest_api_uninstall(){ 
global $wpdb;	
}

function create_ACF_meta_in_REST() {
    $postypes_to_exclude = ['acf-field-group','acf-field'];
    $extra_postypes_to_include = ["page"];
    $post_types = array_diff(get_post_types(["_builtin" => false], 'names'),$postypes_to_exclude);
    html_entity_decode();
    array_push($post_types, $extra_postypes_to_include);

    foreach ($post_types as $post_type) {
        register_rest_field( $post_type, 'ACF', [
            'get_callback'    => 'expose_ACF_fields',
            'schema'          => null,
       ]
     );
    }

}

function expose_ACF_fields( $object ) {
    $ID = $object['id'];
    return get_fields($ID);
}

add_action( 'rest_api_init', 'create_ACF_meta_in_REST' );



/// remove special characters from title


function is_get_posts_request( \WP_REST_Request $request ) {
    return 'GET' === $request->get_method();
}

function mutate_get_posts_response( $response ) {
    if ( ! ( $response instanceof \WP_REST_Response ) ) {
        return;
    }
    $data = array_map(
        'prefix_post_response',
        
        $response->get_data()
    );
    $response->set_data( $data );
}

function prefix_post_response(  array $post ) {
    if ( isset( $post['title']['rendered'] ) ) {
      
        //$type = $request->get_param( 'type' ),
        $post['title']['rendered'] = html_entity_decode( $post['title']['rendered']);
    }
    return $post;
}

add_filter(
    'rest_request_after_callbacks',
    function( $response, array $handler, \WP_REST_Request $request ) {
        if ( is_get_posts_request( $request ) ) {
            mutate_get_posts_response( $response );
        }
        return $response;
    },
    10,
    3
);

