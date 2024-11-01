<?php 
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Rmpf_List_Table extends WP_List_Table {

    // Constructor method
    public function __construct() {
        parent::__construct(array(
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => false
        ));

        ob_start();

        $this->handle_actions();
        $this->process_bulk_action();
        
    }

    public function get_columns() {
        return array(
            'cb'    => '<input type="checkbox" />',
            'name'  => 'Title',
            'date'  => 'Date'
        );
    }

    public function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        global $wpdb;

        $search_query = isset($_REQUEST['search_name']) ? sanitize_text_field($_REQUEST['search_name']) : '';

        $query = "SELECT * FROM {$wpdb->prefix}list_table";
        
        if (!empty($search_query)) {
            $query .= " WHERE name LIKE %s";
            $query = $wpdb->prepare($query,  $wpdb->esc_like($search_query) );
        }

        $data = $wpdb->get_results($query, ARRAY_A);
        usort($data, array($this, 'usort_reorder'));

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
    }

    public function get_sortable_columns() {
        return array(
            'name' => array('name', false),
            'date' => array('date', true)
        );
    }

    public function usort_reorder($a, $b) {
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'date';
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ($order === 'asc') ? $result : $result;
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'cb':
                return '<input type="checkbox" value="' . $item['id'] . '" />';
            case 'name':
                return $this->column_name($item);
            case 'date':
                return $item['date'];
            default:
                return print_r($item, true); 
        }
    }

    public function column_name($item) {
        $feedback_board_link = sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=wp_roadmap_feedback_dashboard&action=view_item&item=' . $item['id'])), $item['name']);
        $edit_link = sprintf('<a href="%s">Edit</a>', esc_url(admin_url('admin.php?page=wp_roadmap_feedback_dashboard&action=view_item&item=' . $item['id'])));
        $trash_link = sprintf('<a href="?page=%s&action=%s&item=%s">Trash</a>', $_REQUEST['page'], 'trash', $item['id']);
        $actions = '<div class="row-actions"><span class="edit">' . $edit_link . ' | </span><span class="trash">' . $trash_link . '</span></div>';

        return '<strong style="color: #2271B1;">' . $feedback_board_link . '</strong>' . $actions;
    }

    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    private function handle_actions() {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'trash' && isset($_REQUEST['item'])) {
            $item_id = intval($_REQUEST['item']);

            if ($item_id > 0) {
                global $wpdb;
                $wpdb->delete("{$wpdb->prefix}list_table", array('id' => $item_id), array('%d'));
                $wpdb->delete("{$wpdb->prefix}feedback", array('item_id' => $item_id), array('%d'));
                $wpdb->delete("{$wpdb->prefix}feedback_status", array('item_id' => $item_id), array('%d'));
            }
        }
    }

    public function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            global $wpdb;

            $delete_ids = esc_sql($_POST['bulk-delete']);
            if (!empty($delete_ids)) {
                foreach ($delete_ids as $id) {
                    $wpdb->delete("{$wpdb->prefix}list_table", array('id' => $id));
                    $wpdb->delete("{$wpdb->prefix}feedback", array('item_id' => $id));
                }
            }
        }
    }

    public function get_bulk_actions() {
        $actions = [
            'delete' => 'Delete'
        ];
        return $actions;
    }

    public function search_box($text, $input_id) {
        echo '<p class="search-box">';
        echo '<label class="screen-reader-text" for="' . $input_id . '">' . $text . ':</label>';
        echo '<input type="search" id="' . $input_id . '" name="search_name" value="' . esc_attr($this->get_search_text()) . '" />';
        echo '<input type="submit" id="search-submit" class="button" value="' . esc_attr__('Search') . '" />';
        echo '</p>';
    }

    public function __destruct() {
        ob_get_clean();
    }
}
