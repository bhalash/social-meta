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
 * @link https://github.com/bhalash/article-images
 */

require_once('article-images/article-images.php');

/**
 * Default Social Accounts
 * -----------------------------------------------------------------------------
 * ¡Please override these elsewhere!
 */

if (!isset($social_twitter)) {
    $social_twitter = '@bhalash';
}

if (!isset($social_facebook)) {
    $social_facebook = 'bhalash';
}

/**
 * Post Information
 * -----------------------------------------------------------------------------
 * @param   int         $post_id        ID of the post. 
 * @param   array       $a_into         Post meta information.
 */

function generate_post_meta($post_id = null) {
    global $social_twitter;

    if (is_null($post_id)) {
        global $post;
        setup_postdata($post);
        $post_id = $post->ID;
    }

    // Use blog name unless title length > 0;
    $title = (is_single()) ? get_the_title() : wp_title('-', false, 'right');
    // Use blog description unless it is a single post with an excerpt length > 0.
    $blurb = (is_single() && strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : get_bloginfo('description');

    $a_info = array(
        'ID' => $post->ID,
        'title' => $title,
        'site_name' => get_bloginfo('name'),
        'url' => get_site_url() . $_SERVER['REQUEST_URI'],
        'description' => $blurb,
        'image' => get_post_image($post->ID),
        'image_size' => get_post_image_dimensions($post->ID),
        'twitter' => $social_twitter,
        'type' => (is_single()) ? 'article' : 'website',
        'locale' => get_locale(),
    );

    return $a_info;
}

/**
 * Output Open Graph and Twitter Card Tags
 * -----------------------------------------------------------------------------
 * Call the Open Graph and Twitter Card functions.
 */

function social_meta() {
    $meta_information = generate_post_meta();
    open_graph_tags($meta_information);
    twitter_card_tags($meta_information);
}

/**
 * Twitter Card Meta Information
 * -----------------------------------------------------------------------------
 * This function /should/ present all of the relevant and correct
 * information for Twitter Card. 
 * 
 * @param   array       $meta_info          Array of post meta information.
 */

function twitter_card_tags($meta_info) {
    $twitter_meta = array(
        'twitter:card' => 'summary',
        'twitter:site' => $meta_info['twitter'],
        'twitter:title' => $meta_info['title'],
        'twitter:description' => $meta_info['description'],
        'twitter:image:src' => $meta_info['image'],
        'twitter:url' => $meta_info['url']
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
 * 
 * @param   array       $meta_info          Array of post meta information.
 */

function open_graph_tags($meta_info) {
    $facebook_meta = array(
        'og:title' => $meta_info['title'],
        'og:site_name' => $meta_info['site_name'],
        'og:url' => $meta_info['url'],
        'og:description' => $meta_info['description'],
        'og:image' => $meta_info['image'],
        'og:image:width' => $meta_info['image_size'][0],
        'og:image:height' => $meta_info['image_size'][1],
        'og:type' => $meta_info['type'],
        'og:locale' => $meta_info['locale'],
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
 * 
 * @param   int     $post_id            ID of the post.
 * @return  array   $single_meta        Extra meta infromation for the post.
 */

function facebook_single_info($post_id = null) {
    if (is_null($post_id)) {
        global $post;
        $post_id = $post->ID;
    }

    global $social_facebook;

    $category = get_the_category($post->ID)[0]->cat_name;
    $tags = get_the_tags();
    $taglist = array();
    $single_meta = array();

    foreach ($tags as $tag) {
        $taglist[] = $tag->name;
    }

    $single_meta['article:section'] = $category;
    $single_meta['article:tag'] = implode(', ', $taglist);
    $single_meta['article:publisher'] = $social_facebook;

    return $single_meta;
}

/**
 * Filters, Options and Actions
 * -----------------------------------------------------------------------------
 */

add_action('wp_head', 'social_meta');

?>
