<?php

if(!class_exists('WP_List_Table')){
    require_once (ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class ImitAppointment extends WP_List_Table{
    private $_items;
    function __construct($data)
    {
        parent::__construct();
        $this->_items = $data;
    }

    /**
     * @return array
     * get all column
     */
    function get_columns()
    {
        return [
          'cb' => '<input type="checkbox" />',
            'braces' => __('Question 1', 'imit-booking-form'),
            'straighten' => __('Question 2', 'imit-booking-form'),
            'straightening' => __('Question 3', 'imit-booking-form'),
            'first_name' => __('First name', 'imit-booking-form'),
            'last_name' => __('Last name', 'imit-booking-form'),
            'email' => __('Email', 'imit-booking-form'),
            'location' => __('Location', 'imit-booking-form'),
            'event_date' => __('Event date', 'imit-booking-form'),
            'event_time' => __('Event time', 'imit-booking-form'),
            'client_cell' => __('Client cell', 'imit-booking-form'),
            'referred_by' => __('Referred by', 'imit-booking-form'),
            'referral_name' => __('Referral name', 'imit-booking-form'),
            'status' => __('Status', 'imit-booking-form'),
            'created_at' => __('Created at', 'imit-booking-form'),
            'updated_at' => __('Updated at', 'imit-booking-form'),
        ];
    }

    /**
     * @param array|object $item
     * @return string|void
     * for row check box
     */
    function column_cb($item){
        return "<input type='checkbox' value='{$item['id']}' />";
    }

    /**
     * @param array|object $item
     * @param string $column_name
     * @return mixed|void
     * for colum default
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * @param $item
     * @return string
     * for colum braces
     */
    function column_braces($item){
        $nonce = wp_create_nonce('imit_appointment_edit');
        $actions = [
          'edit' => sprintf('<a href="?page=imitAppointmentBooking&bid=%s&n=%s">%s</a>', $item['id'], $nonce, __('View', 'imit-booking-form')),
            'delete' => sprintf('<a href="?page=imitAppointmentBooking&bid=%s&n=%s&action=%s">%s</a>', $item['id'], $nonce, 'delete', __('Delete', 'imit-booking-form'))
        ];
        return sprintf('%s %s', $item['braces'], $this->row_actions($actions));
    }

    /**
     * for view status
     */
    function column_status($item){
        if($item['status'] == '0'){
            echo '<strong class="status-badge status-info">Pending</strong>';
        }else if($item['status'] == '1'){
            echo '<strong class="status-badge status-primary">Active</strong>';
        }else if($item['status'] == '2'){
            echo '<strong class="status-badge status-danger">Denied</strong>';
        }else{
            echo '<strong class="status-badge status-success">Completed</strong>';
        }
    }


    /**
     * prepare items for display
     */
    function prepare_items()
    {
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->_items);
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page
        ]);
        $data = array_slice($this->_items, ($current_page-1)*$per_page, $per_page);
        $this->items = $data;
        $this->_column_headers = array($this->get_columns(), array(), array());
    }
}