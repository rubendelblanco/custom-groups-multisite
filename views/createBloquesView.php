<div class="wrap">
  <h2>Nuevo bloque</h2>
  <form id="altaForm" action="<?php echo esc_url( admin_url('admin.php') ); ?>" method="POST">
    <input type="hidden" name="action" value="cgm_add_bloque">
    <div class="inside" style="margin-top:10px;margin-bottom:10px">
      Nombre del bloque: <input type="text" name="nombre"></input>
    </div>
    <div class="inside">
      <input type="submit" value="Crear bloque" class="button button-primary">
    </div>
  </form>
</div>
