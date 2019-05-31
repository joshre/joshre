<?php

// Adding documentation to the dash
function bc_dashboard_widget_function()
{
    $docs_template = get_template_directory() . '/library/docs.php';
    load_template($docs_template);
}
function bc_add_dashboard_widgets()
{
    wp_add_dashboard_widget('wp_dashboard_widget', 'Buscemi Docs', 'bc_dashboard_widget_function');
}
add_action('wp_dashboard_setup', 'bc_add_dashboard_widgets');

// add ie conditional html5 shim to header
function add_ie_html5_shim()
{
    echo '<!--[if lt IE 9]>';
    echo '<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>';
    echo '<![endif]-->';
}
add_action('wp_head', 'add_ie_html5_shim');

// Remove WP 4.2 emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

// Getting rid of WP Default jquery and adding from google
if (!is_admin()) {
    add_action("wp_enqueue_scripts", "jquery_enqueue", 11);
}

function jquery_enqueue()
{
    wp_dequeue_script('jquery');
    wp_deregister_script('jquery');
    wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js", false, null);
}

function localInstall()
{
    if ('127.0.0.1' == $_SERVER["REMOTE_ADDR"]) {
        $res = false;
    } else {

        $res = true;
    }
    return ($res);
}

// Enqueuing all of our scripts and styles
function buscemi_scripts()
{
    wp_enqueue_script('jquery');
    if (localInstall() == true) {
        $reloadScript = 'http://localhost:35729/livereload.js';
        wp_register_script('livereload', $reloadScript, null, false, true);
        wp_enqueue_script('livereload');
    }
    wp_register_script('bigfoot', get_template_directory_uri() . '/app/vendors/bigfoot.min.js', array('jquery'), null, null, true);
    wp_enqueue_script('bigfoot');

    wp_enqueue_style('buscemi_style', get_template_directory_uri() . '/app/main.min.css', null, null, null);
    wp_enqueue_script('buscemi_script', get_template_directory_uri() . '/app/app.min.js', array('jquery'), null, null, true);
}
add_action('wp_enqueue_scripts', 'buscemi_scripts');

// Allowing SVG preveiw in WP Upload
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

// Setting up ACF options page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page();
}

require_once 'functions--custom-fields.php';
require_once 'functions--custom-posts.php';
function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }

    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
function saveWeatherData()
{

    $response = array();

    $hour = 0;

    $today     = strtotime($hour . ':00:00');
    $yesterday = strtotime('-1 day', strtotime('-1 day', $today));

    $fileDate       = date("Y-m-d", $yesterday);
    $dayOpen        = $yesterday;
    $weatherDataURL = "https://api.darksky.net/forecast/8b49204b4878fe62a139e636ce5e45a5/38.546906, -121.474489," . $dayOpen;
    
    $weatherDataResponse = CallAPI('GET',$weatherDataURL,null);
print_r(json_decode($weatherDataResponse,true));
    // file_put_contents(get_template_directory() . "/library/weather/weather-" . $fileDate . ".json", $weatherDataResponse);

    // fwrite($fp, json_encode($response));
    // fclose($fp);
}

saveWeatherData();
