<?php

require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

class WinairRoleTableClass extends WP_List_Table {

    // prepare_items
    public function prepare_items() {

        $orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
        $order = isset($_GET['order']) ? trim($_GET['order']) : "";
        $search_term = isset($_POST['s']) ? trim($_POST['s']) : "";
        $data = $this->wp_list_table_data($orderby, $order, $search_term); 

        $per_page = 2;
        $current_page = $this->get_pagenum();     
        $total_items = count($data);
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        // echo "<pre>";
        //     print_r ($current_page);
        //     print_r ($total_items);
        // echo "</pre>";
        $this->items = $data;     
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    }

    // get_bulk_actions
    public function get_bulk_actions() {
        $actions = array(
            "delete" => "Delete",
            "edit" => "Edit"
        );
        return $actions;
    }


    // get_columns
    public function get_columns() {
        $columns = array(
            "cb" => "<input type='checkbox' />",
            "id" => "ID",
            "title" => "Title",
            "slug" => "Post Slug"
        );
        return $columns;
    }

    // column_cb
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="post[]" value="%s" />', $item['id']);
    }

    public function wp_list_table_data($orderby='', $order='',$search_term) { 
        // define data set from WP_List_Table => data
        // $data = array(
        //     array("id" => 1, "name" => "Sanjay", "email" => "asniay@gmail.com"),
        //     array("id" => 2, "name" => "Aman", "email" => "aman@gmail.com"),
        //     array("id" => 3, "name" => "Rohit", "email" => "rohit@gmail.com"),
        //     array("id" => 4, "name" => "Gopay", "email" => "gopal@gmail.com"),
        // );
        global $wpdb;
        if (!empty($search_term)) {
            //wp_posts
            $all_posts = $wpdb->get_results(
                    "SELECT * from " . $wpdb->posts . " WHERE post_type = 'post' AND post_status = 'publish' AND (post_title LIKE '%$search_term%' OR post_content LIKE '%$search_term%')"
            );
        } else {
            if ($orderby == "title" && $order == "desc") {
                // wp_posts
                $all_posts = $wpdb->get_results(
                        "SELECT * from " . $wpdb->posts . " WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_title DESC"
                );
            } else {
                $all_posts = get_posts( array(
                    "post_type" => "post",
                    "post_status" => "publish"
                ));
            }
        }
        // echo "<pre>";
        //     print_r ($all_posts);
        // echo "</pre>";

        $post_array = array();
        if (count($all_posts) > 0) {
            foreach ($all_posts as $index => $post) {
                $post_array[] = array(
                    "id" => $post->ID,
                    "title" => $post->post_title,
                    "slug" => $post->post_name
                );
            }
        }
        return $post_array;
    }
    // column_default
    public function column_default($items, $column_name) {
        switch($column_name) {
            case 'id':
            case 'title':
            case 'slug':
                return $items[$column_name];
            default:
                return "no value";
        }
    }

    public function get_hidden_columns(){
        return array();
    }

    public function get_sortable_columns() {
        return array(
            "title" => array("title", false),
            "slug" => array("slug", false)
        );
    }

    function column_title($item){       
        $actions = array(
            'edit'     => sprintf('<a href="?page=%s&action=%s&post_id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&post_id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );

        //Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['title'],          
            /*$2%s*/ $this->row_actions($actions)
        );
    }

}

function winair_role_table_layout() {
    $winair_list_table = new WinairRoleTableClass();
    $winair_list_table->prepare_items();
    echo "<h1> This is List </h1>";
    // echo "<form method='post' name='frm_search_post' action='".$_SERVER[PHP_SELF]."?page=winair-role-table'>";
    echo "<form method='post' name='frm_search_post' action='" . $_SERVER['PHP_SELF'] . "?page=winair-role-table'>";
    $winair_list_table->search_box("Search Post(s)", "serch_post_id");
    echo "</form>";
    $winair_list_table->display();
}

winair_role_table_layout();