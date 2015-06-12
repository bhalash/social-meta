<?php 

/**
 * Site Header Social Meta
 * -----------------------------------------------------------------------------
 * @category   PHP Script
 * @package    Social Meta
 * @author     Mark Grealish <mark@bhalash.com>
 * @copyright  Copyright (c) 2015 Mark Grealish
 * @license    https://www.gnu.org/copyleft/gpl.html The GNU General Public License v3.0
 * @version    3.0
 * @link       https://github.com/bhalash/social-meta
 *
 * This file is part of Social Meta.
 * 
 * Social Meta is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software 
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * Social Meta is distributed in the hope that it will be useful, but WITHOUT ANY 
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with 
 * Sheepie. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Load Article Images (if it hasn't been loaded)
 * -----------------------------------------------------------------------------
 * This small standalone librairy is required to correctly get /any/ relevant
 * image from the post, and get its dimensions, for Facebook's Open Graph 
 * reader.
 *
 * See: https://github.com/bhalash/article-images
 */

if (is_admin()) {
    return;
}

require_once('article-images/article-images.php');

global $post;
$the_post = get_post($post->ID);
setup_postdata($the_post);
$article_image_dimensions = get_post_image_dimensions($post->ID);

/**
 * Social Meta Fallback
 * -----------------------------------------------------------------------------
 * This array is called on if the relevant information isn't available. 
 * FALLBACK_IMAGE_URL is used, because this file requires the use of the Article
 * Images script.
 */

if (!isset($social_fallback)) {
    $social_fallback = array(
        'publisher' => $_SERVER['SERVER_NAME'],
        'description' => get_bloginfo('description'),
        // ...you probably want to change this! 
        'twitter' => '@bhalash'
    );
}

/**
 * Post Information
 * -----------------------------------------------------------------------------
 */

$a_info = array(
    'ID' => $post->ID,
    'title' => get_the_title(),
    'site_name' => get_bloginfo('name'),
    'url' => get_site_url() . $_SERVER['REQUEST_URI'],
    'description' => (is_single()) ? get_the_excerpt() : $social_fallback['description'],
    'image' => get_post_image($post->ID),
    'image_size' => array($article_image_dimensions[0], $article_image_dimensions[1]),
    'twitter' => $social_fallback['twitter'],
    'type' => (is_single()) ? 'article' : 'website',
    'locale' => get_locale(),
);

/**
 * Output Open Graph and Twitter Card Tags
 * -----------------------------------------------------------------------------
 * Call the Open Graph and Twitter Card functions.
 */

function social_meta() {
    open_graph_tags();
    twitter_card_tags();
}

/**
 * Twitter Card Meta Information
 * -----------------------------------------------------------------------------
 * This function /should/ present all of the relevant and correct
 * information for Twitter Card. 
 */

function twitter_card_tags() {
    global $a_info;

    $twitter_meta = array(
        'twitter:card' => 'summary',
        'twitter:site' => $a_info['twitter'],
        'twitter:title' => $a_info['title'],
        'twitter:description' => $a_info['description'],
        'twitter:image:src' => $a_info['image'],
        'twitter:url' => $a_info['url']
    );

    foreach ($twitter_meta as $key => $value) {
        printf('<meta name="%s" content="%s">', $key, $value);
    }
}

/**
 * Open Graph Meta Information
 * -----------------------------------------------------------------------------
 * This function /should/ present all of the relevant and correct
 * information for Open Graph scrapers. 
 */

function open_graph_tags() {
    global $a_info;

    $facebook_meta = array(
        'og:title' => $a_info['title'],
        'og:site_name' => $a_info['site_name'],
        'og:url' => $a_info['url'],
        'og:description' => $a_info['description'],
        'og:image' => $a_info['image'],
        'og:image:width' => $a_info['image_size'][0],
        'og:image:height' => $a_info['image_size'][1],
        'og:type' => $a_info['type'],
        'og:locale' => $a_info['locale'],
    );

    if (is_single()) {
        $facebook_meta = array_merge($facebook_meta, facebook_single_info($a_info['ID']));
    }

    foreach ($facebook_meta as $key => $value) {
        // Iterate all information and output.
        printf('<meta property="%s" content="%s">', $key, $value);
    }
}

/**
 * Facebook Single Post Information
 * -----------------------------------------------------------------------------
 * Facebook requires some extra categorization information for single posts:
 * 
 * 1. Category. First post category is ascending numerical order is chosen.
 * 2. Tags. All tags are iteratively added.
 * 3. Publisher URL. Site URL is chosen.
 */

function facebook_single_info($post_id) {
    if (is_null($post_id)) {
        global $post;
        $post_id = $post->ID;
    }

    $category = get_the_category($post->ID)[0]->cat_name;
    $tags = get_the_tags();
    $taglist = array();
    $single_meta = array();
    $i = 0;

    foreach ($tags as $key => $value) {
        if ($i > 0) {
            $taglist[] = ', ';
        }

        $taglist[] = $value->name;
        $i++;
    }

    $single_meta['article:section'] = $category;
    $single_meta['article:tag'] = implode('', $taglist);
    $single_meta['article:publisher'] = $social_fallback['publisher'];

    return $single_meta;
}

/**
 * Filters, Options and Actions
 * -----------------------------------------------------------------------------
 */

add_action('wp_head', 'social_meta');

?>