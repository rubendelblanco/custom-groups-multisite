<?php
/*
* CGMStoreForms.php
* Gestion de altas, bajas y updates de los formularios
*/

include_once(PLUGIN_PATH.'/gump/gump.class.php');
include_once(PLUGIN_PATH.'/models/CGMGroupsModel.php');
/*
*cgm_store_group
*Guarda los grupos
*/
function cgm_store_group(){
  $result = GUMP::is_valid($_POST, array(
	'nombre' => 'required|min_len,3'
  ));

  if ($result!==true){
    cgm_display_errors($result);
    wp_redirect(get_admin_url().'network/admin.php?page=grupos_multisite&action=add');
  }
  else{
    global $wpdb;
    $error = false;
    $conn = new CGMGroupsModel($wpdb);
    $last_id = $conn->set_group($_POST['nombre']);

    if ($last_id==false) $error = true;

    foreach ($_POST['sites'] as $site){
        $result = $conn->set_group_in_site($last_id,$site);
        if ($result == false) {$error = true; break;}
    }

    if (!$error){
      queue_flash_message( 'Grupo creado correctamente', $class = 'update' );
    }
    else{
      queue_flash_message('Error al añadir el grupo :(', $class='error');
    }
    wp_redirect(get_admin_url().'network/admin.php?page=grupos_multisite&action=add');
  }
}

/*
* cgm_users_to_group
* anadir/borrar usuarios al grupo
*/
function cgm_users_to_group(){
  global $wpdb;
  $conn = new CGMGroupsModel($wpdb);
  $users = explode(',',$_POST['users_list']);

  if ($_POST['accion_grupo']=='add'){
    $message = '';

    foreach($users as $user) {
      $success = $conn->set_user_in_group($user,$_POST['id_grupo']);
      if ($success==false) {
          queue_flash_message('Error al añadir al usuario ID='.$id, $class='error');
          wp_redirect(get_admin_url().'network/admin.php?page=users_grupos');
      }
    }

    queue_flash_message( 'Usuario(s) añadido(s) al grupo', $class = 'update' );

    wp_redirect(get_admin_url().'network/admin.php?page=users_grupos');
  }
  else if ($_POST['accion_grupo']=='delete'){
    $success = $conn->delete_users_from_group($_POST['users_list'],$_POST['id_grupo']);
    if ($success) queue_flash_message( 'Usuario(s) borrado(s) del grupo', $class = 'update' );
    else queue_flash_message('Error al borrar usuarios', $class='error');
    wp_redirect(get_admin_url().'network/admin.php?page=users_grupos');
  }
}

/*
* cgm_update_group
* Actualizar grupo
*/
function cgm_update_group(){
  if (!isset($_POST['id'])) die();
  $result = GUMP::is_valid($_POST, array(
	'id' => 'integer'
  ));
  if ($result!==true){
    cgm_display_errors($result);
    wp_redirect(get_admin_url().'network/admin.php?page=grupos_multisite');
  }

  global $wpdb;
  $conn = new CGMGroupsModel($wpdb);
  $sites_anteriores = $conn->get_sites_in_group($_POST['id']);
  $sites_anteriores_id = [];
  $sites_actuales_id = $_POST['sites'];

  foreach ($sites_anteriores as $s){
    array_push($sites_anteriores_id,$s->blog_id);
  }

  $conn->delete_sites_from_group($sites_anteriores_id, $_POST['id']);

  //insertar sitios nuevos
  foreach ($sites_actuales_id as $s){
      $result = $conn->set_group_in_site($_POST['id'],$s);
      if ($result == false) {$error = true; break;}
  }

  if (!$error){
    queue_flash_message( 'Grupo editado correctamente', $class = 'update' );
  }
  else{
    queue_flash_message('Error al editar el grupo :(', $class='error');
  }

  //cambiar el nombre del grupo
  $conn->update_group($_POST['nombre'], $_POST['id']);

  wp_redirect(get_admin_url().'network/admin.php?page=grupos_multisite');

}

/*
* cgm_display_errors
* Crea un flash message con los errores de validacion y los renderiza
*/
function cgm_display_errors($errors){
  $message = 'Tienes los siguientes errores: ';
  $message .= '<ul>';

  foreach ($errors as $e){
    $message .= '<li>'.$e.'</li>';
  }

  $message .= '</ul>';
  queue_flash_message( $message, $class = 'error' );
}
?>
