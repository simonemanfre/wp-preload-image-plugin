<?php
/*
Plugin Name: Blank
Description: Blueprint for develop new WordPress plugin
Author: Simone manfredini
Author URI: https://simonemanfre.it/
License: GPL2
Domain Path: /languages/
Text Domain: blank
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


// TODO Sostituire "blank" con "nome_plugin" nei nomi e nelle funzioni


// PAGINA OPZIONI PLUGIN
function trp_blank_plugin_option_page() {
    add_options_page(
        'Impostazioni Blank',
        'Blank',
        'manage_options',
        'blank',
        'trp_blank_plugin_option_page_html'
    );
}
add_action('admin_menu', 'trp_blank_plugin_option_page');

function trp_blank_plugin_option_page_html(){
    // Qui puoi aggiungere il contenuto della pagina delle impostazioni
    if (!current_user_can('manage_options')) {
        return;
    }
}