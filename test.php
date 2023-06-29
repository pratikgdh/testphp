<?php
/*
Plugin Name: Amazon Geo Affiliate
Description: Plugin to convert Amazon affiliate links based on visitor's country
Author: Your Name
Version: 1.0
*/

$response = wp_remote_get( 'https://www.amazon.in/dp/B0BQHBWPYX/?tag=lightappssk-21' );


if( is_wp_error( $response ) ) {
    echo 'There was an error making the request.';
} else {
    echo 'Response:<pre>';
    print_r( $response );
    echo '</pre>';
}
