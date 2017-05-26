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
    function column_nombre($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=grupos_multisite&action=edit&id=%s">%s</a>', $item['id'], __('Editar', 'grupos')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Borrar', 'grupos')),
        );

        return sprintf('%s %s',
            $item['nombre'],
            $this->row_actions($actions)
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
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function column_sites($item)
    {
      $item['sites'] = str_replace('/','',$item['sites']);
      return $item['sites'];
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
            'nombre' => __('Grupo','custom_groups'),
            'miembros'  => __('Número de usuarios','reparaciones'),
            'sites' => __('Sites accesibles', 'reparaciones')
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
            'nombre' => array('display_name', true),
            'miembros' => array('miembros', true)
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Borrar'
        );
        return $actions;
    }

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
        $table_groups = $wpdb->base_prefix . 'cgm_groups'; // do not forget about tables prefix
        $table_users = $wpdb->base_prefix. 'cgm_users';
        $table_sites = $wpdb->base_prefix. 'cgm_sites';
        $table_blogs = $wpdb->base_prefix. 'blogs';
        $per_page = 20; // constant, how much records will be shown per page
        $like =''; //variable para poner el search en caso de que lo haya en los dos SELECTS

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_groups");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        if (isset( $_REQUEST ["s"] )){
           $search = $_REQUEST["s"];
           $like= " AND {$table_groups}.nombre LIKE '%%{$search}%%'";
         }

         //Consulta mejorable
         $query = "SELECT {$table_groups}.id, {$table_groups}.nombre, ".
         "GROUP_CONCAT({$table_blogs}.path, IF({$table_blogs}.path='/','escritorio','')) AS sites,".
         " (SELECT Count(*) FROM {$table_users} WHERE {$table_users}.group_id = {$table_groups}.id)".
         " AS miembros FROM {$table_blogs}, {$table_groups}, {$table_sites} WHERE {$table_blogs}.blog_id={$table_sites}.blog_id".
         " AND {$table_sites}.group_id={$table_groups}.id".$like." GROUP BY {$table_groups}.id ".
         "UNION ALL SELECT {$table_groups}.id, {$table_groups}.nombre, '-' as sites,".
         " (SELECT Count(*) FROM {$table_users} WHERE {$table_users}.group_id = {$table_groups}.id) AS miembros".
         " FROM {$table_groups} WHERE {$table_groups}.id NOT IN (SELECT {$table_sites}.group_id FROM {$table_sites})".$like;

        $query .= " ORDER BY $orderby $order";
        $query .= " LIMIT ".$per_page*$paged.",$per_page";
        $this->items = $wpdb->get_results($wpdb->prepare($query, $per_page, $paged), ARRAY_A);
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'search' =>$_REQUEST["s"] , // busqueda
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));

    }
}
?>
