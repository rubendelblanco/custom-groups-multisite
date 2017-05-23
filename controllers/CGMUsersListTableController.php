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
            'display_name'  => __('Nombre','reparaciones'),
            'user_email' => __('Email', 'reparaciones')
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
            'Nombre de usuario' => array('display_name', true),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
     /*
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Borrar'
        );
        return $actions;
    }*/

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
     function process_bulk_action()
     {
         global $wpdb;
         $db = new CGMGroupsModel($wpdb);

         if ('delete' === $this->current_action()) {

             $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
             if (is_array($ids)) $ids = implode(',', $ids);

             if (!empty($ids)) {
                 $db->delete_groups($ids);
             }
         }
     }

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
        $per_page = 10; // constant, how much records will be shown per page

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
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $query = "SELECT {$table_users}.*,meta_value AS avatar FROM $table_users, $table_meta".
        " WHERE {$table_users}.id={$table_meta}.user_id AND meta_key='gpa_user_avatar'";

        //consulta de busqueda
        /*if (isset( $_REQUEST ["s"] )){
           $search = $_REQUEST["s"];
           $query .= " WHERE cliente.display_name LIKE '%%{$search}%%' OR marca LIKE '%%{$search}%%' OR cliente.user_email LIKE '%%{$search}%%'";
         }*/
        $this->items = $wpdb->get_results($wpdb->prepare($query, $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            //'search' =>$_REQUEST["s"] , // busqueda
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
