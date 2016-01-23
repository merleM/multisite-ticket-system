<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

/** Plugin can only be used in multisite network
 *  Check if WP installation is multisite
 *
 *  If true, then create tables
 *  If false, then deactivate plugin and show error message
 */
if ( is_multisite() ) {
    /** Create initial tables in database
     *  Check if table for tickets doesn't already exist
     *
     *  If true, create table
     */
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->base_prefix}msts_globaltable'") != $wpdb->base_prefix . 'msts_globaltable') {
        $wpdb->query("CREATE TABLE {$wpdb->base_prefix}msts_globaltable (
											id int NOT NULL AUTO_INCREMENT,
											blog_id int NOT NULL,
											blog_name  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci,
											admin_id int NOT NULL,
											admin_name varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci,
											title varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci,
											category_id int NOT NULL,
											admin_message LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
											update_date datetime,
											status TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
											priority TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
											date datetime,

											PRIMARY KEY (id));");
    }

    /** Check if table for messages (comments) doesn't already exist
     * If true, create table
     */
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->base_prefix}msts_messages'") != $wpdb->base_prefix . 'msts_messages') {
        $wpdb->query("CREATE TABLE {$wpdb->base_prefix}msts_messages (
                                            id int NOT NULL AUTO_INCREMENT,
                                            ticket_id int NOT NULL,
											user_name  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci,
											update_message LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
                                            update_date datetime,
											update_status TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
                                            update_priority TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
                                           
                                            PRIMARY KEY (id));");
    }

    /** Check if table for categories doesn't already exist
     * If true, create table
     */
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->base_prefix}msts_categories'") != $wpdb->base_prefix . 'msts_categories') {
        $wpdb->query("CREATE TABLE {$wpdb->base_prefix}msts_categories (
                                            id int NOT NULL AUTO_INCREMENT,
                                            name TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
                                            recipient TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
                                            recipient_email TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,

                                            PRIMARY KEY (id));");

        /** Get super admin and store in a variable for first category */
        $super_admin = get_user_by( 'id', '1' );
        $superadmin_name = $super_admin->first_name . $super_admin->last_name;
        $superadmin_mail = $super_admin->user_email;


        /** Insert first category which is intended for super admin, not erasable in settings
         *  Also insert name and e-mail of super admin into categories table
         */
        $wpdb->insert($wpdb->base_prefix . 'msts_categories', array('name' => 'WordPress Support', 'recipient' => $superadmin_name, 'recipient_email' => $superadmin_mail ));
    }
} else {
    /** Deactivate plugin and show error message if not multisite */
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    $plugin = plugin_basename( __FILE__ );
    if( is_plugin_active( $plugin ) ) {
        deactivate_plugins( $plugin );
        wp_die( __('Entschuldigung, aber dieses Plugin ist nur fÃ¼r Multisite-Netzwerke geeignet.', 'multisite-ticket-system'));
    }
}
?>