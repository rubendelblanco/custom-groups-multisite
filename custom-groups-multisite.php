<?php
/*
Plugin Name: Custom groups multisite
Description: Permite crear grupos de usuarios en wordpress y restringir el acceso a la web con ellos. Soporta multisite.
Author: Ruben del Blanco
Version: 1.0
Network: true
*/

define ('PLUGIN_PATH',plugin_dir_path( __FILE__ ));
require ('models/CGMGroupsModel.php');
require ('controllers/CGMAdminMenuController.php');
require ('controllers/CGMStoreFormsController.php');
include_once ('flash-messages/WPFlashMessages.php');

function cgm_install(){
  global $wpdb;
  $cgm_groups_table = $wpdb->prefix.'cgm_groups';
  $cgm_users_table = $wpdb->prefix.'cgm_users';
  $cgm_sites = $wpdb->prefix.'cgm_sites';
  $users = $wpdb->prefix.'users';

  $sql1 = "CREATE TABLE IF NOT EXISTS $cgm_groups_table (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    nombre varchar(256) NOT NULL,
    fecha_registro datetime DEFAULT current_timestamp NOT NULL,
    PRIMARY KEY (id)
  )$charset_collate;";

  $sql2 = "CREATE TABLE IF NOT EXISTS $cgm_users_table (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) UNSIGNED NOT NULL,
    group_id mediumint(9) NOT NULL,
    fecha_registro datetime DEFAULT current_timestamp NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES $users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES $cgm_groups_table(id) ON DELETE CASCADE
  )$charset_collate;";

  $sql3 = "CREATE TABLE IF NOT EXISTS $cgm_sites(
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    group_id mediumint(9) NOT NULL,
    blog_id mediumint(9) NOT NULL,
    fecha_registro datetime DEFAULT current_timestamp NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (group_id) REFERENCES $cgm_groups_table(id) ON DELETE CASCADE
  )$charset_collate";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql1 );
  dbDelta( $sql2 );
  dbDelta( $sql3 );
}

register_activation_hook(__FILE__, 'cgm_install');
add_action('admin_menu', 'cgm_admin_menu');
add_action('admin_action_cgm_add_group','cgm_store_group');
?>
