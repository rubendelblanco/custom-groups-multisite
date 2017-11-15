<?php
/*
* CGMBloquesModel
* Gestiona las consultas de los bloques
*/
  class CGMBloquesModel {

    private $cgm_elementos;
    private $cgm_bloques;
    private $conn;

    public function __construct($wpdb) {
      $this->conn=$wpdb;
      $this->cgm_elementos = $this->conn->base_prefix.'cgm_elementos';
      $this->cgm_bloques = $this->conn->base_prefix.'cgm_bloques';
    }

    /*
    * get_bloques
    * @return todos los bloques
    */
    public function get_bloques(){
      $query = "SELECT * FROM $this->cgm_bloques";
      return $this->conn->get_results($query);
    }

    /*
     * get_bloque
     * Devuelve el bloque con id $id
     * @return bloque, null si no existe.
     */
    public function get_bloque($id){
      $query = "SELECT * FROM $this->cgm_bloques WHERE id=$id";
      $result = $this->conn->get_row($query);
      return $result;
    }

    /*
    * set_bloque
    * Crea un nuevo bloque
    * @return el id del bloque insertado o false si hay error
    */
    public function set_bloque($data){
      $query = "INSERT INTO {$this->cgm_bloques} (nombre) VALUES ".
      "($data['nombre'])";
      $result = $this->conn->query($query);
      if ($result!==false) return $this->conn->insert_id;
      return false;
    }

    /*
    *update_bloque
    *actualiza el bloque con id = $id
    *@return true|false
    */
    public function update_group($data, $id){
      $query = "UPDATE $this->cgm_bloques SET group_id={$data['group_id']}, bloque_id={$data['bloque_id']}, texto={$data['texto']}, ".
      "img={$data['img']}, enlace={$data['enlace']}".
      " WHERE id=$group";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_bloque
    *borra un bloque
    *@return true|false
    */
    public function delete_bloque($id){
      $query = "DELETE FROM {$this->cgm_bloques} WHERE id=$id";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_bloques
    *borra un array de bloques
    *@return true|false
    */
    public function delete_bloques($bloques){
      if (is_array($bloques)) $bloques = implode(',',$bloques);
      $query = "DELETE FROM {$this->cgm_bloques} WHERE id IN ($bloques)";
      $result = $this->conn->query($query);
      return $result;
    }

  }
 ?>
