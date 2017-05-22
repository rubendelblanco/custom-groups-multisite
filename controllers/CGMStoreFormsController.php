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

  print_r($result);
}
?>
