<?php

if(!class_exists('WP_List_table')){
    require_once (ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class ImitEventTime extends WP_List_Table{
    private $_items;
    function __construct($data)
    {
        parent::__construct();
        $this->_items = $data;
    }

    function get_columns()
    {
        return [
          'cb' => '<input type="checkbox" />',
          'event_time' => __('Event time', 'imit-booking-form'),
            'status' => __('Status', 'imit-booking-form'),
            'created_at' => __('Created at', 'imit-booking-form'),
            'updated_at' => __('Updated at', 'imit-booking-form'),
            'action' => __('Action', 'imit-booking-form'),
        ];
    }

    /**
     * @param array|object $item
     * @return string|void
     *
     * for check box
     */
    function column_cb($item){
        return "<input type='checkbox' value='{$item['id']}' />";
    }

    /**
     * @param array|object $item
     * @param string $column_name
     * @return mixed|void
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * for action column
     */
    function column_action($item){
        $edit = wp_nonce_url(admin_url('admin.php?page=imitMenageEvent&eid='.$item['id']), 'imit_event_edit', 'n');
        $delete = wp_nonce_url(admin_url('admin.php?page=imitMenageEvent&action=delete&eid='.$item['id']), 'imit_event_edit', 'n');
        return "<a href='".esc_url($edit)."'>View</a> | <a href='".esc_url($delete)."' style='color:red;'>Delete</a>";
    }

    /**
     * @param $item
     * column status
     */
    function column_status($item){
        if($item['status'] == '1'){
            echo '<strong class="status-badge status-success">Published</strong>';
        }else{
            echo '<strong class="status-badge status-danger">Denied</strong>';
        }
    }

    function prepare_items()
    {
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->_items);
        $this -> set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page
        ]);
        $data = array_slice($this->_items, ($current_page-1)*$per_page, $per_page);

        $this->items = $data;
        $this->_column_headers = array($this->get_columns(), array(), array());
    }
}