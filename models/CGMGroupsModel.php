<?php
/*
* CGMGroupsModel
* Gestiona las consultas de las tablas del plugin
*/
  class CGMGroupsModel {
    global $wpdb;
    private $cgm_groups_table = $wpdb->prefix.'cgm_groups';
    private $cgm_users_table = $wpdb->prefix.'cgm_users';
    private $cgm_sites = $wpdb->prefix.'cgm_sites';
    private $users = $wpdb->prefix.'users';

    /*
     * get_group_name
     * Devuelve el nombre del grupo
     * @return nombre, null si no existe.
     */
    public function get_group_name($id){
      $query = "SELECT nombre FROM $cgm_groups_table WHERE id=$id";
      $result = $wpdb->get_row($query);

    }

    /*
     * get_users
     * Devuelve los usuarios del grupo $id
     * @return usuarios en objeto $wpdb.
     */
    public function get_users($id){
      $query = "SELECT {$users}.* FROM $users, $cgm_users_table WHERE {$users}.id={$cgm_users_table}.user_id AND {$cgm_users_table}.group_id=$id";
      $result = $wpdb->query($query);
      return $result;
    }

    /*
     * get_user_groups
     * Devuelve los grupos del usuario $id
     * @return grupos en objeto $wpdb.
     */
    public function get_user_groups($id){
      $query = "SELECT {$cgm_groups_table}.* FROM $cgm_groups_table, $cgm_users_table WHERE {$cgm_groups_table}.id={$cgm_users_table}.group_id AND {$cgm_users_table}.user_id=$id";
      $result = $wpdb->query($query);
      return $result;
    }

    /*
     * is_user_in_group
     * @return true si usuario id = $user esta en el grupo con id = $group. False si no.
     */
    public function is_user_in_group($user, $group){
      $query = "SELECT COUNT (*) FROM $cgm_users_table WHERE {$cgm_users_table}.user_id={$user}".
      " AND {$cgm_users_table}.group_id=$group";
      $result = $wpdb->get_var($query);
      return $result != 0;
    }
  }
 ?>
