<?php 

/**
 * Site Header Social Meta
 * -----------------------------------------------------------------------------
 *  Social Meta generates 
 *
 * @category   PHP Script
 * @package    Social Meta
 * @author     Mark Grealish <mark@bhalash.com>
 * @copyright  Copyright (c) 2015 Mark Grealish
 * @license    https://www.gnu.org/copyleft/gpl.html The GNU General Public License v3.0
 * @version    3.1
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
 * Social Meta. If not, see <http://www.gnu.org/licenses/>.
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

class Social_Meta {
    static $instantiated = false;
    static $version = '3.1';

    private $defaults = array(
        'twitter' => '@bhalash',
        'facebook' => 'bhalash'
    );

    public $args = array();
    public $meta_information = array();

    public function __construct($args) {
        if (self::$instantiated) {
            /* Throw error if more than once instance is running, because more
             * than one instance leads to a mess of code in header */
            throw new Exception('Error: Social Meta can only be instantiated once.');
        }

        if (isset($args['fallback_image'])) {
            set_fallback_image($args['fallback_image']);
        }

        self::$instantiated = true;
        $this->args = wp_parse_args($args, $this->defaults);
        add_action('wp_head', array($this, 'social_meta'));
    }

    /**
     * Output Open Graph and Twitter Card Tags
     * -----------------------------------------------------------------------------
     * Call the Open Graph and Twitter Card functions.
     */

    public function social_meta() {
        // Generate base social meta.
        $this->generate_post_meta();
        // Output Open Graph meta tags.
        $this->open_graph_tags();
        // Output Twitter Card meta tags.
        $this->twitter_card_tags();
    }

    /**
     * Post Information
     * -----------------------------------------------------------------------------
     * @param   int         $post            ID of the post. 
     * @param   array       $a_into          Post meta information.
     */

    private function generate_post_meta($post = null) {
        $post = get_post($post);

        if (!$post) || is_404()) {
            return false;
        }

        $twitter = $this->args['twitter'];
        // Use blog name unless title length > 0;
        $title = (is_single()) ? get_the_title() : wp_title('-', false, 'right');
        // Use blog description unless it is a single post with an excerpt length > 0.
        $blurb = (is_single() && strlen(get_the_excerpt()) > 0) ? get_the_excerpt() : get_bloginfo('description');

        $article_meta = array(
            'ID' => $post,
            'title' => (strlen($title) > 0) ? $title : get_bloginfo('name'),
            'site_name' => get_bloginfo('name'),
            'url' => get_site_url() . $_SERVER['REQUEST_URI'],
            'description' => $blurb,
            'image' => get_post_image($post),
            'image_size' => get_post_image_dimensions($post),
            'twitter' => $twitter,
            'type' => (is_single()) ? 'article' : 'website',
            'locale' => get_locale(),
        );

        $this->meta_information = $article_meta;
    }

    /**
     * Twitter Card Meta Information
     * -----------------------------------------------------------------------------
     * This function /should/ present all of the relevant and correct
     * information for Twitter Card. 
     */

    private function twitter_card_tags() {
        if (is_404()) { 
            return false;
        }
            
        $meta = $this->meta_information;

        $twitter_meta = array(
            'twitter:card' => 'summary',
            'twitter:site' => $meta['twitter'],
            'twitter:title' => $meta['title'],
            'twitter:description' => $meta['description'],
            'twitter:image:src' => $meta['image'],
            'twitter:url' => $meta['url']
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

    private function open_graph_tags() {
        if (is_404()) { 
            return false;
        }

        $meta = $this->meta_information;

        $facebook_meta = array(
            'og:title' => $meta['title'],
            'og:site_name' => $meta['site_name'],
            'og:url' => $meta['url'],
            'og:description' => $meta['description'],
            'og:image' => $meta['image'],
            'og:image:width' => $meta['image_size'][0],
            'og:image:height' => $meta['image_size'][1],
            'og:type' => $meta['type'],
            'og:locale' => $meta['locale'],
        );

        if (is_single()) {
            $facebook_meta = array_merge($facebook_meta, $this->facebook_single_info($meta['ID']));
        }

        foreach ($facebook_meta as $key => $value) {
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
     * @param   int     $post            ID of the post.
     * @return  array   $single_meta        Extra meta infromation for the post.
     */

    private function facebook_single_info($post = null) {
        $post = get_post($post);

        if (!$post || is_404()) {
            return false;
        }

        $facebook = $this->args['facebook'];
        $category = get_the_category($post->ID)[0]->cat_name;
        $tags = get_the_tags();
        $taglist = array();
        $single_meta = array();

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $taglist[] = $tag->name;
            }

            $taglist = implode(', ', $taglist);
        } else {
            $taglist = ' ';
        }

        $single_meta['article:section'] = $category;
        $single_meta['article:tag'] = $taglist;
        $single_meta['article:publisher'] = $facebook;

        return $single_meta;
    }
}

?>
