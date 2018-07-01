<?php
/*
 Plugin Name: Easy Meta Tags
 Description: Adds meta keywords and description to each posts!
 Version: 1.0.0
 Author: Dmitry Ruzgas
 Author URI: http://dimafreelance.ru/
 License: GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

const EASY_META_DESCRIPTION_FIELD = 'easy_meta_tags_description';
const EASY_META_KEYWORDS_FIELD = 'easy_meta_tags_keywords';

if ( is_admin() ) {
    add_action( 'add_meta_boxes', 'admin_meta_tags_box' );
    add_action( 'save_post', 'admin_meta_tags_save' );
} else {
    remove_action( 'wp_head', 'rel_canonical' );
    add_action( 'wp_head', 'frontend_view_meta_tags' );
    add_action( 'wp_head', 'rel_canonical' );
}

function admin_meta_tags_box() {
    add_meta_box(
        'add_easy_meta_tag_container',
        'Easy Meta Tags',
        'admin_view_meta_tags_box'
    );
}

function frontend_view_meta_tags() {
    $meta_keywords = null;
    $meta_description = null;
    if ( is_single() || is_page() ) {
        global $post;
        $meta_keywords = get_post_meta( $post->ID, EASY_META_KEYWORDS_FIELD, true );
        $meta_description = get_post_meta( $post->ID, EASY_META_DESCRIPTION_FIELD, true );
    } elseif ( is_tag() || is_category() || is_tax()) {
        $meta_description = trim( strip_tags( term_description() ) );
    } elseif ( is_home() ) {
        $home_id = get_option( 'page_for_posts' );
        $meta_keywords = get_post_meta( $home_id, EASY_META_KEYWORDS_FIELD, true );
        $meta_description = get_post_meta( $home_id, EASY_META_DESCRIPTION_FIELD, true );
    }
    if( $meta_keywords != null ) {
        echo '<meta name="keywords" content="'.$meta_keywords.'">' . PHP_EOL ;
    }
    if( $meta_description != null ) {
        echo '<meta name="description" content="'.$meta_description.'">' . PHP_EOL;
    }
}

function admin_view_meta_tags_box() {
    wp_nonce_field( EASY_META_KEYWORDS_FIELD, EASY_META_KEYWORDS_FIELD . '_nonce' );
    wp_nonce_field( EASY_META_DESCRIPTION_FIELD, EASY_META_DESCRIPTION_FIELD . '_nonce' );

    $post_id = get_the_ID();

    $meta_keywords = get_post_meta( $post_id, EASY_META_KEYWORDS_FIELD, true );
    $meta_description = get_post_meta( $post_id, EASY_META_DESCRIPTION_FIELD, true );

    echo "
        <!-- keywords -->
        <strong>Meta keywords</strong>
        <div class='wp-editor-container'>
            <textarea 
                class='wp-editor-area' 
                id='easy-meta-tags-keywords' 
                name='".EASY_META_KEYWORDS_FIELD."' 
                cols='80' 
                rows='2'>{$meta_keywords}</textarea>
        </div>
        <p>Length: <span id='easy-meta-tags-keywords-length'></span></p>
        
        <!-- description -->
        <strong>Meta description</strong>
        <div class='wp-editor-container'>
            <textarea 
                class='wp-editor-area' 
                id='easy-meta-tags-description' 
                name='".EASY_META_DESCRIPTION_FIELD."' 
                cols='80' 
                rows='5'>{$meta_description}</textarea>
        </div>
        <p>Length: <span id='easy-meta-tags-description-length'></span></p>
        <script type='text/javascript' src='". plugins_url( 'easy_meta_tags.js', __FILE__ ) . "'></script>";
}

function admin_meta_tags_save( $post_ID ) {
    if ( array_key_exists(EASY_META_DESCRIPTION_FIELD . '_nonce', $_POST) == false
        || array_key_exists(EASY_META_KEYWORDS_FIELD . '_nonce', $_POST) == false ) {
        return false;
    }
    if ( wp_verify_nonce($_POST[EASY_META_DESCRIPTION_FIELD . '_nonce'], EASY_META_DESCRIPTION_FIELD) == false ) {
        return false;
    }
    if ( wp_verify_nonce($_POST[EASY_META_KEYWORDS_FIELD . '_nonce'], EASY_META_KEYWORDS_FIELD) == false ) {
        return false;
    }
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return false;
    }
    if ( isset( $_POST[EASY_META_KEYWORDS_FIELD] ) ) {
        $meta_keywords = sanitize_text_field( $_POST[EASY_META_KEYWORDS_FIELD] );
        update_post_meta( $post_ID, EASY_META_KEYWORDS_FIELD, $meta_keywords );
    }
    if ( isset( $_POST[EASY_META_DESCRIPTION_FIELD] ) ) {
        $meta_keywords = sanitize_text_field( $_POST[EASY_META_DESCRIPTION_FIELD] );
        update_post_meta( $post_ID, EASY_META_DESCRIPTION_FIELD, $meta_keywords );
    }
    return true;
}