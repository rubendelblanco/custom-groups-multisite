<div class="wrap">
  <h2>Lista de usuarios y grupos</h2>
  <?php
    require_once(PLUGIN_PATH.'controllers/CGMUsersListTableController.php');
    $tabla = new CGMGroupsTable();
    $tabla->prepare_items();
  ?>
  <form id="persons-table" method="GET">
        <?php $tabla->search_box('Buscar', 'search'); ?>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
  </form>
  <?php
    $tabla->display();
   ?>
</div>
<script>
  jQuery('.cgm-user').click(function(){
    var values = jQuery("input[name='id\\[\\]']").map(function(){
      if(jQuery(this).is(':checked')) return jQuery(this).val();
    }).get();
    jQuery('[name="users_list"]').val(values);
  });
</script>
