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
        ];
    }

    function column_cb($item){
        return "<input type='checkbox' value='{$item['id']}' />";
    }

    function column_event_time($item){
        $nonce = wp_create_nonce('imit_event_edit');
        $actions = [
            'edit' => sprintf('<a href="?page=imitMenageEvent&eid=%s&n=%s">%s</a>', $item['id'], $nonce, __('View', 'imit-booking-form')),
            'delete' => sprintf('<a href="?page=imitMenageEvent&eid=%s&n=%s&action=%s">%s</a>', $item['id'], $nonce, 'delete', __('Delete', 'imit-booking-form')),
        ];
        return sprintf('%s %s', $item['event_time'], $this->row_actions($actions));
    }

    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

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