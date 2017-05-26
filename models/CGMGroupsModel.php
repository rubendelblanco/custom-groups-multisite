<?php
/*
* CGMGroupsModel
* Gestiona las consultas de las tablas del plugin
*/
  class CGMGroupsModel {

    private $cgm_groups, $cgm_users, $cgm_sites, $users;
    private $conn;

    public function __construct($wpdb) {
      $this->conn=$wpdb;
      $this->cgm_groups = $this->conn->base_prefix.'cgm_groups';
      $this->cgm_users = $this->conn->base_prefix.'cgm_users';
      $this->cgm_sites = $this->conn->base_prefix.'cgm_sites';
      $this->users = $this->conn->base_prefix.'users';
      $this->blogs = $this->conn->base_prefix.'blogs';
    }

    /*
    * get_groups
    * @return todos los grupos
    */
    public function get_groups(){
      $query = "SELECT * FROM $this->cgm_groups";
      return $this->conn->get_results($query);
    }

    /*
     * get_group
     * Devuelve el grupo con id $id
     * @return grupo, null si no existe.
     */
    public function get_group($id){
      $query = "SELECT * FROM $this->cgm_groups WHERE id=$id";
      $result = $this->conn->get_row($query);
      return $result;
    }

    /*
     * get_sites_in_group
     * Devuelve los sitios a los que tiene acceso el grupo $id
     * @return sites.
     */
    public function get_sites_in_group($id){
      $query = "SELECT {$this->cgm_sites}.*, {$this->blogs}.path ".
      "FROM {$this->cgm_sites}, {$this->blogs} ".
      "WHERE {$this->cgm_sites}.group_id =$id AND ".
      "{$this->blogs}.blog_id = {$this->cgm_sites}.blog_id";
      $result = $this->conn->get_results($query);
      return $result;
    }

    /*
     * get_users
     * Devuelve los usuarios del grupo $id
     * @return usuarios en objeto $this->conn.
     */
    public function get_users($id){
      $query = "SELECT {$this->users}.* FROM $this->users, $this->cgm_groups WHERE {$this->users}.id={$this->cgm_groups}.user_id AND {$this->cgm_groups}.group_id=$id";
      $result = $this->conn->get_results($query);
      return $result;
    }

    /*
     * get_user_groups
     * Devuelve los grupos del usuario $id
     * @return grupos en objeto $this->conn.
     */
    public function get_user_groups($id){
      $query = "SELECT {$this->cgm_groups}.* FROM $this->cgm_groups, $this->cgm_users WHERE {$this->cgm_groups}.id={$this->cgm_users}.group_id AND {$this->cgm_groups}.user_id=$id";
      $result = $this->conn->get_results($query);
      return $result;
    }

    /*
     * is_user_in_group
     * @return true si usuario id = $user esta en el grupo con id = $group. False si no.
     */
    public function is_user_in_group($user, $group){
      $query = "SELECT COUNT(*) FROM $this->cgm_users WHERE {$this->cgm_users}.user_id={$user}".
      " AND {$this->cgm_users}.group_id=$group";
      $result = $this->conn->get_var($query);
      return ($result!=0)? true:false;
    }

    /*
    * is_site_in_any_group
    * Comprueba si un $site esta en algun grupo
    * importante porque TODOS los usuarios podrian entrar si esto es asi
    */
    public function is_site_in_any_group($site){
      $query = "SELECT COUNT(*) FROM {$this->cgm_sites} WHERE {$this->cgm_sites}.blog_id=$site";
      $result = $this->conn->get_var($query);
      return ($result>0)? true:false;
    }

    /*
    * is_user_in_any_group
    * Comprueba si un $user esta en algun grupo
    */
    public function is_user_in_any_group($user){
      $query = "SELECT COUNT(*) FROM {$this->cgm_users} WHERE {$this->cgm_users}.user_id=$user";
      $result = $this->conn->get_var($query);
      return ($result>0)? true:false;
    }

    /*
    * can_user_access
    * Comprueba si un usuario puede entrar a un $site
    * Si el $site no esta en ningun grupo se entiende que el acceso es libre
    * @return true si el usuario $user puede entrar en el site $site
    */
    public function can_user_access($user, $site){
      //primero se comprueba que el site esta en algun grupo. Si no es asi -> acceso=true
      if (!$this->is_site_in_any_group($site)) return true;
      $query = "SELECT COUNT(*) FROM {$this->cgm_users}, {$this->cgm_sites} WHERE {$this->cgm_users}.group_id = {$this->cgm_sites}.group_id".
      " AND {$this->cgm_users}.user_id = $user AND {$this->cgm_sites}.blog_id = $site";
      $result = $this->conn->get_var($query);
      return ($result!=0)? true:false;
    }

    /*
    * can_group_access
    * @return true si el grupo $group puede entrar en el site $site
    */
    public function can_group_access($group, $site){
      $query = "SELECT COUNT (*) FROM $this->cgm_sites WHERE {$this->cgm_sites}.group_id=$group AND {$this->cgm_sites}.blog_id=$site";
      $result = $this->conn->get_var($query);
      return $result != 0;
    }

    /*
    * set_group
    * Crea un nuevo grupo
    * @return el id del grupo insertado o false si hay error
    */
    public function set_group($nombre){
      $query = "INSERT INTO {$this->cgm_groups} (nombre) VALUES ('$nombre')";
      $result = $this->conn->query($query);
      if ($result!==false) return $this->conn->insert_id;
      return false;
    }

    /*
    * set_group_in_site
    * Crea un nuevo permiso de $group en el site $site
    * @return true si todo va bien, false si error o si ya existe ese registro.
    */
    public function set_group_in_site($group, $site){
      $query = "SELECT COUNT (*) FROM {$this->cgm_sites} WHERE {$this->cgm_sites}.group_id=$group AND {$this->cgm_sites}.blog_id=$site";
      $result = $this->conn->get_var($query);
      if ($result == 0){
        $query = "INSERT INTO {$this->cgm_sites} (group_id, blog_id) VALUES ($group, $site)";
        $result = $this->conn->query($query);
        return $result;
      }
      else return false;
    }

    /*
    * set_user_in_group
    * Inserta a $user en el grupo $group
    * @return true si todo va bien, false si error.
    */
    public function set_user_in_group($user, $group){
      $query = "INSERT INTO {$this->cgm_users} (user_id, group_id)
        SELECT $user, $group FROM DUAL
        WHERE NOT EXISTS (SELECT * FROM {$this->cgm_users}
        WHERE user_id=$user AND group_id=$group)";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *update_group
    *actualiza el nombre del group $group
    *@return true|false
    */
    public function update_group($nombre, $group){
      $query = "UPDATE $this->cgm_groups SET nombre='$nombre' WHERE id=$group";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_group
    *borra un grupo
    *@return true|false
    */
    public function delete_group($group){
      $query = "DELETE FROM {$this->cgm_groups} WHERE id=$group";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_groups
    *borra un array de grupos
    *@return true|false
    */
    public function delete_groups($groups){
      if (is_array($groups)) $groups = implode(',',$groups);
      $query = "DELETE FROM {$this->cgm_groups} WHERE id IN ($groups)";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_sites_from_group
    *borra un array de sites del grupo $group
    *@return true|false
    */
    public function delete_sites_from_group($sites, $group){
      $sites = implode(',',$sites);
      $query = "DELETE FROM {$this->cgm_sites} WHERE group_id=$group AND blog_id IN ($sites)";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_group_from_site
    *borra a un grupo $group de un $site
    *@return true|false
    */
    public function delete_group_from_site($group, $site){
      $query = "DELETE FROM {$this->cgm_sites} WHERE group_id=$group AND blog_id=$site";
      $result = $this->conn->query($query);
      return $result;
    }

    /*
    *delete_users_from_group
    *borra a los usuarios $users de un $group
    *@return true|false
    */
    public function delete_users_from_group($users, $group){
      if (is_array($users)) $users = implode(',',$users);
      $query = "DELETE FROM {$this->cgm_users} WHERE group_id=$group AND user_id IN ($users)";
      $result = $this->conn->query($query);
      return $result;
    }

  }
 ?>
