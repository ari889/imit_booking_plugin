<?php

if(!class_exists('WP_List_table')){
    require_once (ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class ImitQuestions extends WP_List_Table{
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
          'question' => __('Question', 'imit-booking-form'),
            'answer' => __('Answers', 'imit-booking-form'),
            'priority' => __('Priority', 'imit-booking-form'),
            'status' => __('Status', 'imit-booking-form'),
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
     * for answer column
     */
    function column_answer($item){
        $ansdecode = json_decode($item['answer']);
        $empans = implode($ansdecode, ', ');
        return $empans;
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
        $edit = wp_nonce_url(admin_url('admin.php?page=imitQuestions&qid='.$item['id']), 'imit_question_add', 'n');
        $delete = wp_nonce_url(admin_url('admin.php?page=imitQuestions&action=delete&qid='.$item['id']), 'imit_question_add', 'n');
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