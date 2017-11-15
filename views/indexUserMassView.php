<div class="wrap">
  <h2> Añadir usuarios en masa
  </h2>
  <form action="<?php echo esc_url( admin_url('admin.php') ); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="cgm_add_user_mass">
    <div class="inside" style="margin-top:10px;font-weight: bold;">Elige grupo al que añadir los usuarios: </div>
    <div class="inside" style="margin-top:10px">
      <select name="grupo">
        <option value="0">
          Elige grupo...
        </option>
        <?php
        foreach($grupos as $grupo){
          echo '<option value="'.$grupo->id.'">'.$grupo->nombre.'</option>';
        }
        ?>
      </select>
    </div>
    <div class="inside" style="margin-top:10px;font-weight: bold;">Ingresa el archivo CSV: </div>
    <div class="inside" style="margin-top:10px">
      <input name="archivo" type="file" />
    </div>
    <div class="inside" style="margin-top:10px">
      <input type="submit" value="Subir archivo" class="button button-primary">
    </div>
  </form>
</div>
