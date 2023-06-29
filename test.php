<?php
/*
Plugin Name: Amazon Geo Affiliate
Description: Plugin to convert Amazon affiliate links based on visitor's country
Author: Your Name
Version: 1.0
*/

function replace_amazon_url_in_content($content) {
    // Amazon domains and tracking ids
    $amazon_domains = array(
        'US' => 'www.amazon.com',
        'GB' => 'www.amazon.co.uk',
        'FR' => 'www.amazon.fr',
		 'IN' => 'www.amazon.in',
       
        // add the rest of your country codes and domains here
    );
    $tracking_ids = array(
        'US' => 'your-us-affiliate-id',
        'GB' => 'your-uk-affiliate-id',
        'FR' => 'your-uk-affiliate-id',
        'IN' => 'lightappssk-21',
        // add the rest of your country codes and tracking ids here
    );

    // Get country code
    $country_code = 'US'; // Default to US
    $response = @file_get_contents('https://ipinfo.io/json');
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['country'])) {
            $country_code = $data['country'];
        }
    }

    //echo 'Country Code: ' . $country_code;  // Echo the country code

    // Check if we have a domain and tracking id for this country code
    if (isset($amazon_domains[$country_code]) && isset($tracking_ids[$country_code])) {
        // Domain and tracking id
        $domain = $amazon_domains[$country_code];
        $tracking_id = $tracking_ids[$country_code];

        // Replace the Amazon URLs in anchor tags
        $content = preg_replace_callback('#<a([^>]*?)href=[\'"](https?://(www\.)?amazon\..+?/dp/([0-9A-Za-z]{10})).*?[\'"]([^>]*?)>(.*?)</a>#is', function($matches) use ($domain, $tracking_id) {
            // ASIN is in $matches[4]
            return '<a' . $matches[1] . 'href="https://' . $domain . '/dp/' . $matches[4] . '/?tag=' . $tracking_id . '"' . $matches[5] . '>' . $matches[6] . '</a>';
        }, $content);

        // Replace the Amazon URLs in button tags
        $content = preg_replace_callback('#<button([^>]*?)data-href=[\'"](https?://(www\.)?amazon\..+?/dp/([0-9A-Za-z]{10})).*?[\'"]([^>]*?)>(.*?)</button>#is', function($matches) use ($domain, $tracking_id) {
            // ASIN is in $matches[4]
            return '<button' . $matches[1] . 'data-href="https://' . $domain . '/dp/' . $matches[4] . '/?tag=' . $tracking_id . '"' . $matches[5] . '>' . $matches[6] . '</button>';
        }, $content);
    } else {
        // If the country code is not supported, search product name on Amazon
        $product_name = 'YOUR_PRODUCT_NAME'; // Replace with the actual product name

        $search_url = 'https://' . $amazon_domains['US'] . '/s?k=' . urlencode($product_name);

        // Add the search URL as a fallback link
        $fallback_link = '<a href="' . $search_url . '">Click here</a> to search on Amazon';

        $content .= '<div>' . $fallback_link . '</div>';
    }

    return $content;
}

add_filter('the_content', 'replace_amazon_url_in_content');

?>
