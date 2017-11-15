<?php
function cgm_admin_menu()
{
    add_menu_page(__('Grupos Multisite', 'grupos-multisite'), __('Grupos Multisite', 'grupos-multisite'), 'activate_plugins', 'grupos_multisite', 'cgm_menu_index','dashicons-groups');
    add_submenu_page( 'grupos_multisite', 'Usuarios y grupos', 'Usuarios y grupos','manage_network','users_grupos',
    'cgm_users_index');
    add_submenu_page( 'grupos_multisite', 'Añadir usuarios masivo', 'Añadir usuarios masivo','manage_network','mass_users_grupos',
    'cgm_mass_users_grupos');
    add_submenu_page( 'grupos_multisite', 'Bloques de elementos', 'Bloques de elementos','manage_network','bloques',
    'cgm_bloques');
    add_submenu_page( 'grupos_multisite', 'Elementos restringidos', 'Elementos restringidos','manage_network','elementos',
    'cgm_elementos');
}

function cgm_menu_index()
{
  $path = PLUGIN_PATH.'/views';

  //anadir grupo
  if (isset($_GET['action']) and $_GET['action']=='add'){
    include ($path.'/addGroupView.php');
    add_action ('admin_action_cgm_add_group', 'cgm_store_group');
  }
  //editar grupo
  else if (isset($_GET['action']) and $_GET['action']=='edit' and isset($_GET['id'])){
    include( $path.'/editGroupView.php');
  }
  else //listar grupos
  include( $path.'/indexGroupView.php');
}

function cgm_users_index(){
  $path = PLUGIN_PATH.'/views';
  include($path.'/indexUserListView.php');
}

function cgm_mass_users_grupos(){
  $path = PLUGIN_PATH.'/views';
  global $wpdb;
  $conn = new CGMGroupsModel($wpdb);
  $grupos = $conn->get_groups();
  include($path.'/indexUserMassView.php');
}

function cgm_bloques(){
  $path = PLUGIN_PATH.'/views';

  if( isset($_GET['action']) && $_GET['action'] == 'add' )
    include($path.'/createBloquesView.php');
  else
    include($path.'/indexBloquesView.php');
}

?>
