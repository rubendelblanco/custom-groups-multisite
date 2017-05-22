<?php
/*
* CGMStoreForms.php
* Gestion de altas de los formularios
*/

include_once(PLUGIN_PATH.'/gump/gump.class.php');
/*
*cgm_store_group
*Guarda los grupos
*/
function cgm_store_group(){
  $result = GUMP::is_valid($_POST, array(
	'nombre' => 'required|alpha_numeric|min_len,3'
  ));

  if ($result!==true){
    echo 'error';
    display_errors($result);
    wp_redirect(get_admin_url().'admin.php?page=grupos_multisite&action=add');
  }
}

/*
* display_errors
* Crea un flash message con los errores y los renderiza
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
