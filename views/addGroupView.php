<style>
select, input[type="text"] {
    width: 200px;
    box-sizing: border-box;
}
section {
    padding-top: 8px;
    padding-bottom: 8px;
    background-color: #f0f0f0;
    overflow: auto;
}
section > div {
    float: left;
    padding: 4px;
}
section > div + div {
    width: 40px;
    text-align: center;
}
</style>
<?php
  $sites = get_sites();
?>
<div class="wrap">
  <h2>Nuevo grupo</h2>
  <form id="altaForm" action="<?php echo esc_url( admin_url('admin.php') ); ?>" method="POST">
    <input type="hidden" name="action" value="cgm_add_group">
    <div class="inside">
      Nombre del grupo: <input type="text" name="nombre"></input>
    </div>
    <div class="inside">
      <p>Mover sites a la derecha para que este nuevo grupo tenga acceso a ellos.</p>
      <div style="display:inline-block">
        <span style="width:270px; float:left; font-weight: bold;">Sites restringidos</span>
        <span style="width:200px; float:left; font-weight: bold;">Sites permitidos</span>
      </div>
      <section class="container">
        <div>
            <select id="leftValues" size="15" multiple>
              <?php foreach ($sites as $s):
                $site_name = str_replace('/','',$s->path);
                if ($s->blog_id == 1) $site_name = 'escritorio';
              ?>

              <option name="<?php echo $s->blog_id?>"><?php echo $site_name?></option>
              <?php endforeach;?>
            </select>
        </div>
        <div>
            <input type="button" id="btnLeft" value="&lt;&lt;" />
            <input type="button" id="btnRight" value="&gt;&gt;" />
        </div>
        <div>
            <select id="rightValues" size="15" name="sites[]" multiple>
            </select>
        </div>
      </section>
    </div>
    <div class="inside">
      <input type="submit" value="Crear grupo" class="button button-primary">
    </div>
  </form>
</div>

<script>
jQuery("#btnLeft").click(function () {
    var selectedItem = jQuery("#rightValues option:selected");
    jQuery("#leftValues").append(selectedItem);
});

jQuery("#btnRight").click(function () {
    var selectedItem = jQuery("#leftValues option:selected");
    jQuery("#rightValues").append(selectedItem);
});
</script>
