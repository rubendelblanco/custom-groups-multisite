<div class="wrap">
  <h2>Lista de usuarios y grupos</h2>
  <?php
    require_once(PLUGIN_PATH.'controllers/CGMUsersListTableController.php');
    $tabla = new CGMGroupsTable();
    $tabla->prepare_items();
    $tabla->display();
   ?>
</div>
<script>
jQuery(document).ready(){
  jQuery('.cgm-user').click(){
    
  }
}
</script>
