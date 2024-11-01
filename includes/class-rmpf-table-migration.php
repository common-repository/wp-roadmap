<?php
class RMPF_Table_Migration {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since   1.0.7
     */

    /**Feedback Table Migration*/
    public static function rmpf_migrate_feedback() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'feedback';
        $sql = "CREATE TABLE $table_name (
            id  bigint(20) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            description longtext NOT NULL,
            UNIQUE KEY id (id),
            status_id  bigint(20) NOT NULL,
            sequence_order int(11) NOT NULL,
            total_upvote bigint(20) NOT NULL,
            created_date date NOT NULL,
            item_id bigint(20) NOT NULL
        )$charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        self::updateItemID($table_name);

        //Insert Default Data in Table.
        // $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");
        // if(count($result) == 0) {
        //     //Insert data in table
        //     $wpdb->query("INSERT INTO $table_name (title,description,status_id,sequence_order,item_id)VALUES('Pending-Task1','pending',1,1,1)");
        //     $wpdb->query("INSERT INTO $table_name (title,description,status_id,sequence_order,item_id)VALUES('Progress-Task1','progress',2,1,1)");
        //     $wpdb->query("INSERT INTO  $table_name (title,description,status_id,sequence_order,iten_id)VALUES('Complete-Task1','complete',3,1,1)");
        // }
    }

    /**Up Vote Table Migration*/
    public static function rmpf_migrate_upvote() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'feedback_upvote';
        $sql = "CREATE TABLE $table_name (
            id  bigint(20) NOT NULL AUTO_INCREMENT,
            visitor_ip_address longtext NOT NULL,
            UNIQUE KEY id (id),
            feedback_id  bigint(20) NOT NULL
        )$charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    /**Feedback Status Table Migration*/
    public static function rmpf_migrate_feedback_status(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'feedback_status';
        $sql = "CREATE TABLE $table_name (
            id  bigint(20) NOT NULL AUTO_INCREMENT,
            title longtext NOT NULL,
            color longtext NOT NULL,
            active_status int(11) NOT NULL DEFAULT '1' ,
            sequence_order int(11) NOT NULL,
            UNIQUE KEY id (id),
            sequence  bigint(20) NOT NULL,
            item_id bigint(20) NOT NULL
        )$charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        self::updateItemID($table_name);
         //Insert Default Data in Table.
        //  $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");
        //  if(count($result) == 0) {
        //     // Insert data in table
        //     $wpdb->query("INSERT INTO $table_name (title,color,active_status,sequence_order,item_id)VALUES('Pending','#4931ad',1,1,1,1)");
        //     $wpdb->query("INSERT INTO $table_name (title,color,active_status,sequence_order,item_id)VALUES('Progress','#4931ad ',1,2,1,1)");
        //     $wpdb->query("INSERT INTO $table_name (title,color,active_status,sequence_order,iten_id)VALUES('Complete','#4931ad ',1,3,1,1)");
        //  }
    }

    /**Set Default Value In wp_options Table */
//      public static function wp_general_setting_default_options () {
//          $default = array(
//             'title'     => 'Your Site Title',
//              'description'   => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
//              'wp_roadmap_status'=>'roadmap',
//              'suggestion'=>'Your Suggestion',
//              'request_feature_link' => "",
//              'pages'=>[]

//          );
//          update_option( 'wp_feedback_roadmap_general_settings', $default );
//  }

    public static function wp_general_setting () {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'general_setting';
        $sql = "CREATE TABLE $table_name (
            id  bigint(20) NOT NULL AUTO_INCREMENT,
            title longtext NOT NULL,
            description longtext,
            tab longtext ,
            suggestion longtext ,
            link longtext,
            pages longtext ,
            item_id bigint(20) NOT NULL,
            UNIQUE KEY id (id)
        )$charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");
        if(count($result) == 0) {
            $wpdb->query("INSERT INTO $table_name (title, description, tab, suggestion, link, pages, item_id) VALUES ('your title', 'Enter your description', 'roadmap', 'Your Suggestion', '', '', 1)");

        }
    }
    
    public static function rmpf_list_table(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'list_table';
        $sql = "CREATE TABLE $table_name (
            id  bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(20) NOT NULL,
            date DATE NOT NULL,
            UNIQUE KEY id (id)
        )$charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");
         if(count($result) == 0) {
            // Insert data in table
            $wpdb->query("INSERT INTO $table_name (name, date) VALUES ('Board 1', CURDATE())");
         }
    }


    private static function updateItemID($table_name) {
        global $wpdb;
        $wpdb->query("UPDATE $table_name SET item_id = 1 WHERE item_id = 0");
    }
}