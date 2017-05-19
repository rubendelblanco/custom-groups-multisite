<div class="wrap">
  <h2>Nuevo grupo</h2>
  <form id="altaForm" action="<?php echo esc_url( admin_url('admin.php') ); ?>" method="POST">
    <input type="hidden" name="action" value="cgm_add_group">
    <div class="inside">
      Nombre del grupo: <input type="text" name="nombre"></input>
    </div>
    <div class="inside">
      <input type="submit" value="Crear grupo" class="button button-primary">
    </div>
  </form>
</div>
