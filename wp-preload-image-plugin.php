<?php
/*
Plugin Name: WP Preload Image
Description: Blueprint for develop new WordPress plugin
Author: Simone manfredini
Author URI: https://simonemanfre.it/
License: GPL2
Domain Path: /languages/
Text Domain: preload_image
Version: 0.0.1
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


// TODO Sostituire "preload_image" con "nome_plugin" nei nomi e nelle funzioni


// PAGINA OPZIONI
function trp_preload_image_plugin_option_page() {
    add_options_page(
        'Impostazioni WP Preload Image',
        'WP Preload Image',
        'manage_options',
        'preload_image',
        'trp_preload_image_plugin_option_page_html'
    );
}
add_action('admin_menu', 'trp_preload_image_plugin_option_page');

function trp_preload_image_plugin_option_page_html(){
    // Qui puoi aggiungere il contenuto della pagina delle impostazioni
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('preload_image_plugin_options');
            do_settings_sections('preload_image_plugin');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

function preload_image_plugin_settings_init() {
    register_setting('preload_image_plugin', 'preload_image_plugin_options');

    add_settings_section(
        'preload_image_plugin_section_developers',
        __('Settings', 'preload_image_plugin'),
        'preload_image_plugin_section_callback_function',
        'preload_image_plugin'
    );

    add_settings_field(
        'preload_image_global',
        __('Preload image in all pages', 'preload_image_plugin'),
        'preload_image_field_callback_function',
        'preload_image_plugin',
        'preload_image_plugin_section_developers',
        [
            'label_for' => 'preload_image_global',
            'class' => 'preload_image_row',
            'preload_image_plugin_custom_data' => 'custom',
        ]
    );
}
add_action('admin_init', 'preload_image_plugin_settings_init');

function preload_image_plugin_section_callback_function($args) {
    echo "<p>Impostazioni per il preload delle immagini.</p>";
}

function preload_image_field_callback_function($args) {
    $options = get_option('preload_image_plugin_options');
    ?>
    <input type="text" id="<?= esc_attr($args['label_for']); ?>"
           name="preload_image_plugin_options[<?= esc_attr($args['label_for']); ?>]"
           value="<?= esc_attr($options[$args['label_for']] ?? ''); ?>">
    <?php
}

// Registrazione dei Meta Box
function preload_image_add_meta_box() {
    $screens = get_post_types(['public' => true], 'names');
    unset($screens['attachment']);

    foreach ($screens as $screen) {
        add_meta_box(
            'preload_image_id',
            'Preload Image in this Post',
            'preload_image_meta_box_callback',
            $screen,
            'side'
        );
    }
}
add_action('add_meta_boxes', 'preload_image_add_meta_box');

function preload_image_meta_box_callback($post) {
    wp_nonce_field('preload_image_nonce_action', 'preload_image_nonce');
    $value = get_post_meta($post->ID, '_preload_image_url', true);
    echo '<label for="preload_image_field">URL Immagine:</label>';
    echo '<input type="text" id="preload_image_field" name="preload_image_field" value="' . esc_attr($value) . '" size="25" />';
}

function save_preload_image_postdata($post_id) {
    if (!isset($_POST['preload_image_nonce']) ||
        !wp_verify_nonce($_POST['preload_image_nonce'], 'preload_image_nonce_action')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['preload_image_field'])) {
        update_post_meta(
            $post_id,
            '_preload_image_url',
            sanitize_text_field($_POST['preload_image_field'])
        );
    }
}
add_action('save_post', 'save_preload_image_postdata');

// Visualizzazione dei dati nel frontend
function inject_preload_links() {
    $global_preload_url = get_option('preload_image_plugin_options')['preload_image_global'] ?? '';
    
    if (!empty($global_preload_url)) {
        echo '<link rel="preload" href="' . esc_url($global_preload_url) . '" as="image">';
    }

    if (is_singular()) {
        $post_preload_url = get_post_meta(get_the_ID(), '_preload_image_url', true);
        
        if (!empty($post_preload_url)) {
            echo '<link rel="preload" href="' . esc_url($post_preload_url) . '" as="image">';
        }
    }
}
add_action('wp_head', 'inject_preload_links');
