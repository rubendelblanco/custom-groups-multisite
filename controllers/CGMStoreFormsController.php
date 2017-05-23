<?php
/*
* CGMStoreForms.php
* Gestion de altas de los formularios
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
    display_errors($result);
    wp_redirect(get_admin_url().'admin.php?page=grupos_multisite&action=add');
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
    wp_redirect(get_admin_url().'admin.php?page=grupos_multisite&action=add');
  }
}

function cgm_users_to_group(){
  print_r($_POST);
}

/*
* display_errors
* Crea un flash message con los errores de validacion y los renderiza
*/
function display_errors($errors){
  $message = 'Tienes los siguientes errores: ';
  $message .= '<ul>';

  foreach ($errors as $e){
    $message .= '<li>'.$e.'</li>';
  }

  $message .= '</ul>';
  queue_flash_message( $message, $class = 'error' );
}
?>
