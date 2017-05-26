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
  global $wpdb;
  $conn = new CGMGroupsModel($wpdb);
  if ($conn->get_group($_GET['id']) == null) die();
  $sites = get_sites();
  $group = $conn->get_group($_GET['id']);
  $accesibles = $conn->get_sites_in_group($group->id);
  $sites_no_accesibles = [];
  $sites_no_accesibles_id = [];
  $sites_accesibles = [];
  $sites_accesibles_id = [];

  //lista de todos los path de los sites sin /
  foreach ($sites as $s){
    $site_name = str_replace('/','',$s->path);
    if ($s->blog_id == 1) $site_name = 'escritorio';
    array_push($sites_no_accesibles, $site_name);
    array_push($sites_no_accesibles_id, $s->blog_id);
  }

  //idem con todos los sites accesibles del grupo
  foreach ($accesibles as $s){
    $site_name = str_replace('/','',$s->path);
    if ($s->blog_id == 1) $site_name = 'escritorio';
    array_push($sites_accesibles, $site_name);
    array_push($sites_accesibles_id, $s->blog_id);
  }

  $sites_no_accesibles = array_diff($sites_no_accesibles,$sites_accesibles);
  $sites_no_accesibles_id = array_diff($sites_no_accesibles_id,$sites_accesibles_id);
?>
<div class="wrap">
  <h2>Editar grupo</h2>
  <form id="altaForm" action="<?php echo esc_url( admin_url('admin.php') ); ?>" method="POST">
    <input type="hidden" name="action" value="cgm_edit_group">
    <input type="hidden" name="id" value="<?php echo $group->id?>"
    <div class="inside">
      Nombre del grupo: <input type="text" name="nombre" value="<?php echo $group->nombre?>">
    </div>
    <div class="inside">
      <p>Mover sites a la derecha para que este grupo tenga acceso a ellos.</p>
      <div style="display:inline-block">
        <span style="width:270px; float:left; font-weight: bold;">Sites restringidos</span>
        <span style="width:200px; float:left; font-weight: bold;">Sites permitidos</span>
      </div>
      <section class="container">
        <div>
            <select id="leftValues" size="15" multiple>
              <?php for ($i = 0; $i <= count($sites_no_accesibles); $i++):
                  if ($sites_no_accesibles[$i]!=''):
              ?>

              <option value="<?php echo $sites_no_accesibles_id[$i] ?>">
                <?php echo $sites_no_accesibles[$i]?>
              </option>
            <?php endif; endfor;?>
            </select>
        </div>
        <div>
            <input type="button" id="btnLeft" value="&lt;&lt;" />
            <input type="button" id="btnRight" value="&gt;&gt;" />
        </div>
        <div>
            <select id="rightValues" size="15" name="sites[]" multiple>
              <?php for ($i = 0; $i <= count($sites_no_accesibles); $i++):
                  if ($sites_accesibles[$i]!=''):
              ?>
              <option value="<?php echo $sites_accesibles_id[$i] ?>">
                <?php echo $sites_accesibles[$i]?>
              </option>
              <?php endif; endfor;?>
            </select>
        </div>
      </section>
    </div>
    <div class="inside">
      <input type="submit" value="Guardar cambios" class="input-form button button-primary">
    </div>
  </form>
</div>

<script>
jQuery('input[type="submit"]').click(function(){
    jQuery('select#rightValues option').prop('selected', true);
  }
);

jQuery("#btnLeft").click(function () {
    var selectedItem = jQuery("#rightValues option:selected");
    jQuery("#leftValues").append(selectedItem);
});

jQuery("#btnRight").click(function () {
    var selectedItem = jQuery("#leftValues option:selected");
    jQuery("#rightValues").append(selectedItem);
});
</script>
