<?php
function cgm_admin_menu()
{
    add_menu_page(__('Grupos Multisite', 'grupos-multisite'), __('Grupos Multisite', 'grupos-multisite'), 'activate_plugins', 'grupos_multisite', 'cgm_menu_index','dashicons-groups');
    add_submenu_page( 'grupos_multisite', 'Usuarios y grupos', 'Usuarios y grupos','manage_network','users_grupos',
    'cgm_users_index');
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
?>
