<div class="wrap">
  <h2>Lista de grupos <a class="add-new-h2"
  href="<?php echo get_admin_url(null, 'network/admin.php?page=grupos_multisite&action=add');?>">
  Nuevo grupo</a>
  </h2>
  <?php
    require_once(PLUGIN_PATH.'controllers/CGMGroupsTableController.php');
    $tabla = new CGMGroupsTable();
    $tabla->prepare_items();
    $tabla->display();
   ?>
</div>
