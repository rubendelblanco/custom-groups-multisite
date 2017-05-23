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
  jQuery('.cgm-user').click(function(){
    var values = jQuery("input[name='id\\[\\]']").map(function(){
      if(jQuery(this).is(':checked')) return jQuery(this).val();
    }).get();
    jQuery('[name="users_list"]').val(values);
  });
</script>
