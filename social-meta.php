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

require_once('article-images/article-images.php');

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
 * Output Open Graph and Twitter Card Tags
 * -----------------------------------------------------------------------------
 * Call the Open Graph and Twitter Card functions.
 */

function social_meta() {
    if (is_admin()) {
        return;
    }

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
    global $social_fallback, $post;
    $the_post = get_post($post->ID);
    setup_postdata($the_post);

    $site_meta = array(
        'twitter:card' => 'summary',
        'twitter:site' => $social_fallback['twitter'],
        'twitter:title' => get_the_title(),
        'twitter:description' => (is_single()) ? get_the_excerpt() : $social_fallback['description'],
        'twitter:image:src' => get_post_image($post->ID),
        'twitter:url' => get_site_url() . $_SERVER['REQUEST_URI'],
    );

    foreach ($site_meta as $key => $value) {
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
    global $social_fallback, $post;
    $the_post = get_post($post->ID);
    setup_postdata($the_post);

    $image_dimensions = get_post_image_dimensions($post->ID);

    $site_meta = array(
        'og:title' => get_the_title(),
        'og:site_name' => get_bloginfo('name'),
        'og:url' => get_site_url() . $_SERVER['REQUEST_URI'],
        'og:description' => (is_single()) ? get_the_excerpt() : $social_fallback['description'],
        'og:image' => get_post_image($post->ID),
        'og:image:width' => $image_dimensions[0],
        'og:image:height' => $image_dimensions[1],
        'og:type' => (is_single()) ? 'article' : 'website',
        'og:locale' => get_locale(),
    );

    if (is_single()) {
        // If single post, add category and tag information.
        $category = get_the_category($post->ID)[0]->cat_name;

        $tags = get_the_tags();
        $taglist = array();
        $i = 0;

        foreach ($tags as $key => $value) {
            if ($i > 0) {
                $taglist[] = ', ';
            }

            $taglist[] = $value->name;
            $i++;
        }

        $article_meta = array(
            'article:section' => $category,
            'article:tag' => implode('', $taglist),
            'article:publisher' => $social_fallback['publisher'],
        );

        $site_meta = array_merge($site_meta, $article_meta);
    }

    foreach ($site_meta as $key => $value) {
        // Iterate all information and output.
        printf('<meta property="%s" content="%s">', $key, $value);
    }
}

/**
 * Filters, Options and Actions
 * -----------------------------------------------------------------------------
 */

add_action('wp_head', 'social_meta');

?>