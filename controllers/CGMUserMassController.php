<?php
  /*
  * CGMUserMassController.php
  * Controlador para subir usuarios en masa a un grupo
  */
include_once(PLUGIN_PATH.'/gump/gump.class.php');

  function cgm_add_user_mass(){
    $archivo = $_FILES['archivo']['tmp_name'];
    $grupo = $_POST['grupo'];
    $file = fopen($archivo, 'r');
    global $wpdb;
    $conn = new CGMGroupsModel($wpdb);
    $usuariosYaEstaban = '';

    $phpFileUploadErrors = array(
      0 => 'No hay error, fichero subido con éxito',
      1 => 'El archivo excede la directiva upload_max_filesize de php.ini',
      2 => 'El fichero subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML',
      3 => 'El fichero fue sólo parcialmente subido',
      4 => 'No se subió ningún fichero',
      6 => 'Falta la carpeta temporal',
      7 => 'No se pudo escribir el fichero en el disco.',
      8 => 'Una extensión de PHP detuvo la subida de ficheros.',
    );

    //validar
    $is_valid = GUMP::is_valid($_POST, array(
    	'grupo' => 'required|integer'
    ));

    if ($grupo<=0 or !$is_valid){
      queue_flash_message( 'Debe elegir un grupo', $class = 'error' );
      wp_redirect(get_admin_url().'network/admin.php?page=mass_users_grupos');
      die();
    }

    if (($_FILES['archivo']['error'] > 0)){
      queue_flash_message( $phpFileUploadErrors[$_FILES['archivo']['error']], $class = 'error' );
      wp_redirect(get_admin_url().'network/admin.php?page=mass_users_grupos');
      die();
    }

    while(!feof($file))
    {
      $email = fgets($file);
      $user = get_user_by( 'email', trim($email) );

      if (!$conn->is_user_in_group($user->id, $grupo)){
        $result = $conn->set_user_in_group($user->id, $grupo);
      }
      else $usuariosYaEstaban .= '<li>'.$user->user_email.'</li>';
    }
    fclose($file);

    queue_flash_message( 'Usuarios añadidos al grupo', $class = 'updated' );

    if (strlen($usuariosYaEstaban)>0) $message = 'Los siguientes usuarios ya estaban'.
    ' en el grupo:<ul>'.$usuariosYaEstaban.'</ul>';
    queue_flash_message( $message, $class = 'updated' );

    wp_redirect(get_admin_url().'network/admin.php?page=mass_users_grupos');
    die();
  }
 ?>
