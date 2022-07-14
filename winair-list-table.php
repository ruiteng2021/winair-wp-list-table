<?php

/*
 * Plugin name: Winair Role Table
 * Description: This is simple plugin for Winair role table using WP_List_Table 
 * Author: Rui
 */

 add_action("admin_menu", "wpl_winair_list_table_menu");

 function wpl_winair_list_table_menu() {
    add_menu_page("Winair Role Table", "Winair Role Table", "manage_options", "winair-role-table", "wpl_winair_role_table");
 }

 function wpl_winair_role_table() {
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'views/table-list.php';
    $template = ob_get_contents();
    ob_end_clean();  
    echo $template;
 }