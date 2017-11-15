<div class="wrap">
  <h2>Lista de bloques<a class="add-new-h2"
  href="<?php echo get_admin_url(null, 'network/admin.php?page=bloques&action=add');?>">Nuevo bloque</a>
  </h2>
  <?php
    require_once(PLUGIN_PATH.'controllers/CGMBloquesTableController.php');
    $tabla = new CGMBloquesTable();
    $tabla->prepare_items();
  ?>
  <form id="groups-table" method="GET">
      <?php $tabla->search_box('Buscar', 'search'); ?>
      <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
    <?php
      $tabla->display();
     ?>
   </form>
</div>
