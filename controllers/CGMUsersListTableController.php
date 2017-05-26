<?php
/* Tabla de los grupos*/
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class CGMGroupsTable extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'grupo',
            'plural' => 'grupos',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_user_login($item)
    {
      return sprintf(
      '<img src="%s" height="32" width="32"/> <span>%s</span>',
        $item['avatar'],$item['user_login']
      );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" class="cgm-user" name="id[]" value="%s" />',
            $item['ID']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'user_login' => __('Nombre de usuario','custom_groups'),
            'display_name'  => __('Nombre','custom_groups'),
            'user_email' => __('Email', 'custom_groups'),
            'grupos'    => __('Grupos', 'custom_groups'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'user_login' => array('user_login', false),
            'user_email' => array('user_email', false),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_users = $wpdb->base_prefix . 'users'; // do not forget about tables prefix
        $table_meta = $wpdb->base_prefix . 'usermeta';
        $table_cgm_users = $wpdb->base_prefix.'cgm_users';
        $table_groups = $wpdb->base_prefix.'cgm_groups';
        $per_page = 20; // constant, how much records will be shown per page
        $s='';

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_users");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'user_login';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        //consulta de busqueda
        if (isset( $_REQUEST ["s"] )){
           $search = $_REQUEST["s"];
           $s = "WHERE (user_login LIKE '%%{$search}%%' OR user_email LIKE '%%{$search}%%' OR display_name LIKE '%%{$search}%%') ";
           $total_items = $wpdb->query($query);
         }

        $query = "SELECT {$table_users}.*, GROUP_CONCAT(COALESCE({$table_groups}.nombre, '') SEPARATOR ',') ".
        "AS grupos FROM {$table_users} LEFT JOIN {$table_cgm_users} INNER JOIN {$table_groups} ".
        "ON {$table_cgm_users}.group_id = {$table_groups}.id ON {$table_users}.ID = {$table_cgm_users}.user_id ".$s.
        "GROUP BY {$table_users}.ID";

         $query .= " ORDER BY $orderby $order";
         $query .= " LIMIT ".$per_page*$paged.",$per_page";

         //por lo que sea no funcionan los parametros $paged y $per_page en esta funcion. De ahi que haya
         //puesto el LIMIT en la consulta
         $this->items = $wpdb->get_results($wpdb->prepare($query,$paged,$per_page), ARRAY_A);

         //asignar avatar (deberia estar en la consulta pero no he sido quien)
         for ($i = 0; $i<count($this->items); $i++){
           $avatar = get_user_meta($this->items[$i]['ID'], 'gpa_user_avatar');
           $this->items[$i]['avatar'] = $avatar[0];
         }

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'search' =>$_REQUEST["s"] , // busqueda
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));

    }

    function extra_tablenav( $which ) {
  		if ( $which == "top" ){
        global $wpdb;
  			$conn = new CGMGroupsModel($wpdb);
        $grupos = $conn->get_groups();
        $option1 = '<select name="accion_grupo">';
        $option1 .= '<option value="add">Añadir a grupo</option>';
        $option1 .= '<option value="delete">Borrar de grupo</option>';
        $option1 .= '</select>';
        $option2 = '<select name="id_grupo">';
        foreach ($grupos as $grupo){
          $option2 .= '<option value="'.$grupo->id.'">'.$grupo->nombre.'</option>';
        }
        $option2 .= '</select>';
        $form = '<form action="'.esc_url( admin_url('admin.php') ).'" method="POST">';
        $form .= '<input type="hidden" name="action" value="cgm_users_to_group">';
        $form .= '<input type="hidden" name="users_list">';
        $form .= $option1.' '.$option2.'<input type="submit" class="button action" value="Aplicar"></form>';
        echo $form;
  		}
	   }
}
?>
