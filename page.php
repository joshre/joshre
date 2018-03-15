<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context         = Timber::get_context();
$post            = new TimberPost();
$context['post'] = $post;
// $context['posts'] =  getCustomPosts('post', -1, null, 'date', null, null);
$context['pagination'] = Timber::get_pagination();
$numberOfPosts         = get_option('posts_per_page');

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$offset = $paged * floatval($numberOfPosts) - floatval($numberOfPosts);

$context['posts'] = Timber::get_posts();

if (is_page('home')) {
    $context['home'] = prepareHomepage();
}
Timber::render(array('page-' . $post->post_name . '.twig', 'page.twig'), $context);
