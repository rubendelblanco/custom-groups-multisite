<?php
/*
* CGMElementosModel
* Gestiona las consultas de los elementos restringidos
*/
  class CGMElementosModel {

    private $cgm_elementos;
    private $conn;

    public function __construct($wpdb) {
      $this->conn=$wpdb;
      $this->cgm_elementos = $this->conn->base_prefix.'cgm_elementos';
      $this->cgm_bloques = $this->conn->base_prefix.'cgm_bloques';
      $this->cgm_groups = $this->conn->base_prefix.'cgm_groups';
    }

    /*
    * get_elementos
    * @return todos los elementos
    */
    public function get_elementos(){
      $query = "SELECT * FROM $this->cgm_elementos";
      return $this->conn->get_results($query);
    }

    /*
     * get_elemento
     * Devuelve el elemento con id $id
     * @return elemento, null si no existe.
     */
    public function get_elemento($id){
      $query = "SELECT * FROM $this->cgm_elementos WHERE id=$id";
      $result = $this->conn->get_row($query);
      return $result;
    }

    /*
     * get_elementos_in_group
     * Devuelve los elementos a los que tiene acceso el grupo $id
     * @return sites.
     */
    public function get_elementos_in_group($id){
      $query = "SELECT {$this->cgm_elementos}.* ".
      "FROM {$this->cgm_elementos} ".
      "WHERE {$this->cgm_elementos}.group_id =$id;";
      $result = $this->conn->get_results($query);
      return $result;
    }

    /*
    * set_elemento
    * Crea un nuevo elemento
    * @return el id del elemento insertado o false si hay error
    */
    public function set_elemento($data){
      $query = "INSERT INTO {$this->cgm_elementos} (group_id, bloque_id, texto, img, enlace) VALUES ".
      "($data['group_id'], $data['bloque_id'], $data['texto'], $data['img'], $data['enlace'])";
      $result = $this->conn->query($query);
      if ($result!==false) return $this->conn->insert_id;
      return false;
    }

    /*
    *update_elemento
    *actualiza el elemento con id = $id
    *@return true|false
    */
    public function update_group($data, $id){
      $query = "UPDATE $this->cgm_elementos SET group_id={$data['group_id']}, bloque_id={$data['bloque_id']}, texto={$data['texto']}, ".
      "img={$data['img']}, enlace={$data['enlace']}".
      " WHERE id=$group";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_elemento
    *borra un elemento
    *@return true|false
    */
    public function delete_elemento($id){
      $query = "DELETE FROM {$this->cgm_elementos} WHERE id=$id";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_elementos
    *borra un array de elementos
    *@return true|false
    */
    public function delete_elementos($elementos){
      if (is_array($elementos)) $elementos = implode(',',$elementos);
      $query = "DELETE FROM {$this->cgm_elementos} WHERE id IN ($elementos)";
      $result = $this->conn->query($query);
      return $result;
    }

  }
 ?>
