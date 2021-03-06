<?php
/*
Plugin Name: Custom groups multisite
Description: Permite crear grupos de usuarios en wordpress y restringir el acceso a la web con ellos. Soporta multisite.
Author: Ruben del Blanco
Version: 1.1
Network: true
*/
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

define ('PLUGIN_PATH',plugin_dir_path( __FILE__ ));
require ('models/CGMGroupsModel.php');
require ('controllers/CGMAdminMenuController.php');
require ('controllers/CGMStoreFormsController.php');
require ('controllers/CGMUserMassController.php');
include ('flash-messages/WPFlashMessages.php');

function cgm_install(){
  global $wpdb;
  $cgm_groups_table = $wpdb->prefix.'cgm_groups';
  $cgm_users_table = $wpdb->prefix.'cgm_users';
  $cgm_sites = $wpdb->prefix.'cgm_sites';
  $cgm_bloques = $wpdb->prefix.'cgm_bloques';
  $cgm_elementos = $wpdb->prefix.'cgm_elementos';
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

  $sql4 = "CREATE TABLE IF NOT EXISTS $cgm_bloques (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    nombre varchar(256) NOT NULL,
    fecha_registro datetime DEFAULT current_timestamp NOT NULL,
    PRIMARY KEY (id)
  )$charset_collate;";

  $sql5 = "CREATE TABLE IF NOT EXISTS $cgm_elementos (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    group_id mediumint(9) NOT NULL,
    bloque_id mediumint(9) NOT NULL,
    texto varchar(256) NOT NULL,
    img varchar(512) NOT NULL,
    enlace varchar(512)NOT NULL,
    fecha_registro datetime DEFAULT current_timestamp NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (group_id) REFERENCES $cgm_groups_table(id) ON DELETE CASCADE,
    FOREIGN KEY (bloque_id) REFERENCES $cgm_bloques(id) ON DELETE CASCADE
  )$charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql1 );
  dbDelta( $sql2 );
  dbDelta( $sql3 );
  dbDelta( $sql4 );
  dbDelta( $sql5 );
}

//comprueba si el usuario puede entrar al site
function cgm_check_user_entry($template){
  global $wpdb;
  $conn = new CGMGroupsModel($wpdb);
  $response = $conn->can_user_access(get_current_user_id(),get_current_blog_id());
  if (!$response){
    $template = dirname( __FILE__ ) . '/templates/forbidden-template.php';
  }
  return $template;
}

register_activation_hook(__FILE__, 'cgm_install');
add_action('network_admin_menu', 'cgm_admin_menu');
add_action('admin_action_cgm_add_group','cgm_store_group');
add_action('admin_action_cgm_users_to_group','cgm_users_to_group');
add_action('admin_action_cgm_edit_group','cgm_update_group');
add_action('admin_action_cgm_add_user_mass','cgm_add_user_mass');
add_filter('template_include', 'cgm_check_user_entry');
?>
