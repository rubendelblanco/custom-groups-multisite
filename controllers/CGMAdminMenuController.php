<?php
function cgm_admin_menu()
{
    add_menu_page(__('Grupos Multisite', 'grupos-multisite'), __('Grupos Multisite', 'grupos-multisite'), 'activate_plugins', 'grupos_multisite', 'cgm_menu_index','dashicons-groups');
}

function cgm_menu_index()
{
  $path = plugin_dir_path( __FILE__ ).'../views/';

  if (isset($_GET['action']) and $_GET['action']=='add'){
    include ($path.'addGroupView.php');
    add_action ('admin_action_cgm_add_group', 'cgm_store_group');
  }
  else
  include( $path.'indexGroupView.php');
}
?>
