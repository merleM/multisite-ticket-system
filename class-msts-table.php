<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/** Load copy of class WP_List_Table
 *  Renamed msts_WP_List_Table to avoid conflicts
 *  A copy has been created since severe changes to WP_List_Table can occur in newer WP versions
 *
 * The WordPress software is under the license GPLv2, thus allowing to copy, modify and distribute the code under GPLv2
 */
if ( !class_exists( 'msts_WP_List_Table' ) ) {
    require_once( 'class-wp-list-table.php' );
}

/** Create new class MSTS_Table which uses WP_List_Table as base class
 *  Uses functions of WP_List_Table
 */
class MSTS_Table extends msts_WP_List_Table {
    /** Basic class constructor, needs to be overridden
    *
    * @param string singular => singular label for listed objects
    * @param string plural => plural label for listed objects
    * @param bool ajax => whether this class supports AJAX
    */
    public function __construct() {
        parent::__construct( array(
                'singular' => __('Ticket'),
                'plural'   => __('Tickets'),
                'ajax'     => false
        ) );

        /** Call function to define repeatedly used variables */
        $this->msts_define_variables();
    }

    /** Define some global variables for multiple use of translatable strings */
    private function msts_define_variables(){
        /** Status */
        global $msts_status_open;
        global $msts_status_pending;
        global $msts_status_closed;

        $msts_status_open = __('Offen', 'multisite-ticket-system');
        $msts_status_pending = __('In Bearbeitung', 'multisite-ticket-system');
        $msts_status_closed = __('Geschlossen', 'multisite-ticket-system');

        /** Priority */
        global $msts_prio_low;
        global $msts_prio_normal;
        global $msts_prio_medium;
        global $msts_prio_high;

        $msts_prio_low = __('Niedrig', 'multisite-ticket-system');
        $msts_prio_normal = __('Normal', 'multisite-ticket-system');
        $msts_prio_medium = __('Mittel', 'multisite-ticket-system');
        $msts_prio_high = __('Hoch', 'multisite-ticket-system');

        /** URL text for going back to ticket display */
        global $titleUrl;
        $titleUrl = __('Zurück zu Tickets', 'multisite-ticket-system');
    }

    /** Fetch data from database
    *  @param int $per_page => number of items to be displayed per page
    *  @param int $page_number => current page number
    *
    *  @param string ARRAY_A => associative array (column=>value)
    *
    *  @return array
    */
    private function msts_get_tickets( $per_page = 10, $page_number = 1 ) {
        global $wpdb;
        $current_user = wp_get_current_user();
        $blog_id = get_current_blog_id();

        /** Checks if current user is not super admin
        * If true, then only show tickets WHERE blog_id is current blog id
        * If false, show all tickets for super admin
        *
        * @param bool $msts_addUsed
        * @param string $msts_addSQL
        */
        if( !is_super_admin( $current_user->ID )) {
            $sql="SELECT t.id, t.admin_name, t.title, t.admin_message, t.update_date, t.status, t.priority, t.date, c.name as category
            FROM {$wpdb->base_prefix}msts_globaltable t
            INNER JOIN {$wpdb->base_prefix}msts_categories c
            ON t.category_id=c.id
            WHERE blog_id = $blog_id";

            $msts_addUsed = false;
            if( isset( $_POST['filterBy']) ) {
                $msts_addSQL = " AND ";

                if ( $_POST['category'] != 0 ) {
                    $msts_addUsed = true;
                    $categoryValue = $_POST['category'];
                    $msts_addSQL .= "c.id=$categoryValue";
                }

                if ( $_POST['priority'] != "all" ) {
                    $msts_addSQL .= ($msts_addUsed)?' AND ':'';
                    $msts_addUsed = true;
                    $priorityValue = $_POST['priority'];
                    $msts_addSQL .= "t.priority=$priorityValue";
                }

                if ( $_POST['status'] != "all" ) {
                    $msts_addSQL .=($msts_addUsed)?' AND ':'';
                    $msts_addUsed = true;
                    $statusValue = $_POST['status'];
                    $msts_addSQL .= "t.status=$statusValue";
                }
            }
        } else {
            $sql="SELECT t.id, t.admin_name, t.title, t.admin_message, t.update_date, t.status, t.priority, t.date, c.name as category
            FROM {$wpdb->base_prefix}msts_globaltable t
            INNER JOIN {$wpdb->base_prefix}msts_categories c
            ON t.category_id=c.id";

            $msts_addUsed = false;
            if( isset( $_POST['filterBy']) ) {
                $msts_addSQL = " WHERE ";

                if ( $_POST['category'] != 0 ) {
                    $msts_addUsed = true;
                    $categoryValue = $_POST['category'];
                    $msts_addSQL .= "c.id=$categoryValue";
                }

                if ( $_POST['priority'] != "all" ) {
                    $msts_addSQL .= ($msts_addUsed)?' AND ':'';
                    $msts_addUsed = true;
                    $priorityValue = $_POST['priority'];
                    $msts_addSQL .= "t.priority='".$priorityValue."'";
                }

                if ( $_POST['status'] != "all" ) {
                    $msts_addSQL .= ($msts_addUsed)?' AND ':'';
                    $msts_addUsed = true;
                    $statusValue = $_POST['status'];
                    $msts_addSQL .= "t.status='".$statusValue."'";
                }
            }
        }

        /** Checks if $msts_addUsed is true
         *
         *  true => add $msts_addSQL to query string
         *  false => return no additional string
         */
        $sql .= ( $msts_addUsed ) ? $msts_addSQL : '';

        /** Order by column if clicked on a column
        *  If false, sorted by date
        */
        if ( !empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        } else {
            $sql .= ' ORDER BY date DESC';
        }

        /** Limit number of shown tickets per page */
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        /** Return array with tickets */
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result;
    }

    /** This text will be displayed when no tickets are available for display */
    public function no_items() {
        _e( 'Keine Tickets vorhanden.', 'multisite-ticket-system' );
    }


    /** Shows the default when no specific column method exists
    *
    *  @param object $item
    *  @param string $column_name
    *
    *  @return array
    */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
            case 'admin_name':
            case 'category':
                return $item[ $column_name ];
            case 'title':
                return "<strong>".$item[ $column_name ]."</strong>";
            default:
                /** Show the whole array for troubleshooting purposes */
                return print_r( $item, true );
        }
    }

    /** Specific column method for id which adds custom action links
    *
    * @param array $item array of DB data
    *
    * @return array
    */
    public function column_id( $item ) {
        /** Generates and returns a nonce
        * Creating and checking the nonce ensures that the request is valid
        */
        $show_nonce = wp_create_nonce( 'show_singleTicket' );
        $delete_nonce = wp_create_nonce( 'delete_ticket' );

        $title = $item[ 'id' ];
        $titleShow = __('Ansehen', 'multisite-ticket-system');
        $titleDelete = __('Löschen', 'multisite-ticket-system');


        $actions = array(
            'show' => sprintf( '<a href="?page=%s&action=%s&ticket=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'show', absint( $item['id'] ), $show_nonce, $titleShow ),
            'delete' => sprintf( '<a href="?page=%s&action=%s&ticket=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce, $titleDelete )
        );

        return $title . $this->row_actions( $actions );
    }

    /** Column method for date which checks whether a ticket has been updated
    *  and shows specific output, either updated on date or created on
    *
    *  @param array $item array of DB data
    *
    *  @return array
    */
    public function column_date( $item ) {
        if ( !empty( $item['update_date']) && $item['update_date'] != $item['date'] ){
            /** Ticket has been updated, show text and update date */
            return sprintf( _e('Zuletzt aktualisiert', 'multisite-ticket-system').'<br>%1$s', date_format( date_create( $item['update_date'] ), 'd.m.Y H:i' ) );
        } else {
            /** Ticket has not been updated yet, show different text and date of creation */
            return sprintf( _e('Erstellt', 'multisite-ticket-system').'<br>%1$s', date_format( date_create( $item['date'] ), 'd.m.Y H:i' ) );
        }
    }

    /** Column method for status formats elements according to each status
    *  Formatting settings can be found in CSS file
    *
    * @param array $item array of DB data
    *
    * @return array
    */
    public function column_status( $item ) {
        global $msts_status_open;
        global $msts_status_pending;
        global $msts_status_closed;

        switch ( $item['status'] ) {
            case $msts_status_open:
                return sprintf('<span class="label status-open">%1$s</span>', $item['status']);
            case $msts_status_pending:
                return sprintf('<span class="label status-pending">%1$s</span>', $item['status']);
            case $msts_status_closed:
                return sprintf('<span class="label status-closed">%1$s</span>', $item['status']);
            default:
                return sprintf('<span>%1$s</span>', $item['status']);
        }
    }

    /** This specific column method defines the output of the priority elements
     *  Every item has to be defined, otherwise they're simply not being displayed
     *  Formatting settings can be found in CSS file
     *
     *  @param array $item array of DB data
     *
     *  @return array
     */
    public function column_priority( $item ) {
        global $msts_prio_low;
        global $msts_prio_normal;
        global $msts_prio_medium;
        global $msts_prio_high;

        switch ( $item['priority'] ) {
            case $msts_prio_normal:
                return sprintf('<span class="priority-normal">%1$s</span>', $item['priority']);
            case $msts_prio_low:
                return sprintf('<span class="priority-low">%1$s</span>', $item['priority']);
            case $msts_prio_medium:
                return sprintf('<span class="priority-medium">%1$s</span>', $item['priority']);
            case $msts_prio_high:
                return sprintf('<span class="priority-high">%1$s</span>', $item['priority']);
            default:
                return sprintf('<span>%1$s</span>', $item['priority']);
        }
    }

    /** Create an associative array of columns
     *
     *  @return array
     */
    public function get_columns() {
        $columns = array(
            'id' => '#' ,
            'title' => __('Betreff', 'multisite-ticket-system') ,
            'admin_name' => __('Von', 'multisite-ticket-system') ,
            'category' =>  __('Kategorie', 'multisite-ticket-system') ,
            'status' =>  __('Status', 'multisite-ticket-system'),
            'priority' =>  __('Priorität', 'multisite-ticket-system') ,
            'date' =>  __('Datum', 'multisite-ticket-system')
        );
        return $columns;
    }

    /** Defines which columns are sortable
     *  true => by default sorted
     *
     *  @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'date' => array( 'date', true ),
            'id' => array( 'id', false ),
            'admin_name' => array( 'admin_name', false ),
            'category' => array( 'category', false ),
            'title' => array( 'title', false ),
            'status' => array( 'status', false ),
            'priority' => array( 'priority', false )
        );
        return $sortable_columns;
    }

    /** Function for formatting notices
     *
     *  @param string $msts_notice
     */
    public function msts_admin_notice( $msts_notice ) {
        echo '<div id="message" class="updated">';
        echo '<p>'. $msts_notice .'</p>';
        echo '</div>';
    }

    /** Function for formatting error notices
     *
     *  @param string $msts_error
     */
    public function msts_admin_error_notice( $msts_error ) {
        echo '<div id="message" class="error">';
        echo '<p>'. $msts_error .'</p>';
        echo '</div>';
    }

    /** Displays settings for categories
     *  Available only in network admin
     *
     */
    public function msts_showSettings() {
        global $wpdb;

        $sql="SELECT *
              FROM {$wpdb->base_prefix}msts_categories";
        $categories = $wpdb->get_results( $sql );

        $table_name = $wpdb->base_prefix . 'msts_categories';
        $globaltable_name = $wpdb->base_prefix . 'msts_globaltable';

        /** Action delete
         *  Checks nonce
         */
        if ('delete' === $this->current_action()) {
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            /** Shows message, if nonce is invalid */
            if ( !wp_verify_nonce( $nonce, 'delete_cat' ) ) {
                die( __('Nicht erlaubt.', 'multisite-ticket-system') );
            }
            else {
                $wpdb->update( $globaltable_name,
                array('category_id' => 1),
                array( 'category_id' =>  $_GET['category'] ) );

                $resultDelete = $wpdb->delete( $table_name, array('id' => $_GET['category']) );

                /** Checks if query was successful and shows corresponding notice */
                if( $resultDelete ) {
                    $message = __('Kategorie wurde gelöscht.', 'multisite-ticket-system');
                    $this->msts_admin_notice( $message );
                } else {
                    $notice = __('Es ist ein Fehler beim Löschen aufgetreten.', 'multisite-ticket-system');
                    $this->msts_admin_error_notice( $notice );
                }
            }
            $categories = $wpdb->get_results( $sql );
        }

        /** New category is added */
        if( isset( $_POST['submit'] ) ) {
            $resultAdd = $wpdb->insert( $table_name, array(
                                                    'name' => $_POST['new_cat_name'],
                                                    'recipient' => $_POST['new_cat_recipient'],
                                                    'recipient_email' => $_POST['new_cat_email'])
            );

            if( $resultAdd ) {
                $message = __('Kategorie wurde hinzugefügt.', 'multisite-ticket-system');
                $this->msts_admin_notice( $message );
            } else {
                $notice = __('Es ist ein Fehler beim Hinzufügen aufgetreten.', 'multisite-ticket-system');
                $this->msts_admin_error_notice( $notice );
            }

            $categories = $wpdb->get_results( $sql );
        }

        /** Existing category is changed */
        if( isset( $_POST['submitCat']) ) {
            $resultUpdate = $wpdb->update( $table_name, array(

                                                        'recipient' => $_POST['cat_recipient'],
                                                        'recipient_email' => $_POST['cat_email']),
                                                        array( 'id' =>  $_POST['cat_id'] )
            );

            if ( $resultUpdate ){
                $message = __('Kategorie wurde aktualisiert.', 'multisite-ticket-system');
                $this->msts_admin_notice( $message );
            } else{
                $notice = __('Es ist ein Fehler bei der Aktualisierung aufgetreten.', 'multisite-ticket-system');
                $this->msts_admin_error_notice( $notice );
            }
            $categories = $wpdb->get_results( $sql );
        }

        include ( MSTS_SETTINGS );

        /** Display input fields for changing and updating category */
        if ('edit' === $this->current_action()) {
            include ( MSTS_SINGLECATEGORY );
        }
    }

    public function show_singleTicket( $item ) {
        global $wpdb;
        global $msts_status_open;
        global $msts_status_pending;
        global $msts_status_closed;
        global $msts_prio_low;
        global $msts_prio_normal;
        global $msts_prio_medium;
        global $msts_prio_high;
        global $titleUrl;
        global $current_user;
        get_currentuserinfo();


        $sql="SELECT t.id, t.blog_name, t.admin_id, t.admin_name, t.title, t.admin_message, t.update_date, t.status, t.priority, t.date, c.name as category, c.recipient as cat_recipient
              FROM {$wpdb->base_prefix}msts_globaltable t
              INNER JOIN {$wpdb->base_prefix}msts_categories c
              ON t.category_id=c.id
              WHERE t.id=$item";

        $ticket = $wpdb->get_row( $sql );

        /** Checks if a new comment is submitted
        *
        *  Inserts comment into table msts_messages
        *  Updates status, priority, and update_date in table msts_globaltable
        */
        if( isset( $_POST["submit"] ) ){
            $resultComment = $wpdb->insert($wpdb->base_prefix . 'msts_messages', array (
                                                                                'ticket_id' => $_POST['ticket_id'],
                                                                                'user_name' => $_POST['user_name'],
                                                                                'update_message' => htmlspecialchars($_POST['comment'],ENT_QUOTES),
																				'update_date' => current_time('mysql', 1),
                                                                                'update_status' => $_POST['ticket_status'],
                                                                                'update_priority' => $_POST['priority'])
            );


            $resultUpdate = $wpdb->update($wpdb->base_prefix . 'msts_globaltable', array(
                                                                                'update_date' => current_time('mysql', 1),
                                                                                'status' => $_POST['ticket_status'],
                                                                                'priority' => $_POST['priority']),
                                                                                array('id' => $_POST['ticket_id'])
            );

            if ( $resultUpdate && $resultComment ) {
                $message = __('Ticket wurde aktualisiert und Kommentar verschickt.', 'multisite-ticket-system');
                $this->msts_admin_notice( $message );
            } else {
                $notice = __('Es gab ein Problem beim Aktualisieren oder/und Verschicken.', 'multisite-ticket-system');
                $this->msts_admin_error_notice( $notice );
            }
            $ticket = $wpdb->get_row( $sql );
        }
        include ( MSTS_SINGLETICKET );

        /** If any comments are available, show all comments under the ticket */
        $sql_comments="SELECT *
                       FROM {$wpdb->base_prefix}msts_messages
                       WHERE ticket_id=$item
                       ORDER BY update_date DESC";

        $comments = $wpdb->get_results( $sql_comments );
        $superAdmin_comment = __('Vom Superadministrator aktualisiert.', 'multisite-ticket-system');

        if( $comments ) {
            include ( MSTS_COMMENTS );
        }
    }

    public function msts_sendMail( $sendTo, $ticket_title, $ticket_body, $sendFrom, $sendCategory ) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= __('Von', 'multisite-ticket-system'). ": " . $sendFrom . "\r\n";
        $headers .= __('Kategorie', 'multisite-ticket-system'). ": " . $sendCategory . "\r\n";
        $ticket_subject = __('[Multisite Ticket System: Neues Ticket]', 'multisite-ticket-system'). $ticket_title;
        $ticket_body .= stripcslashes ( $ticket_body );

        wp_mail( $sendTo, $ticket_subject, $ticket_body, $headers );
    }

    public function msts_createTicket() {
        global $wpdb;
        global $msts_status_open;
        global $msts_prio_low;
        global $msts_prio_normal;
        global $msts_prio_medium;
        global $msts_prio_high;
        global $titleUrl;
        $current_user = wp_get_current_user();
        $msts_blog_name = get_bloginfo('name');

        $sql="SELECT *
              FROM {$wpdb->base_prefix}msts_categories";

        $categories = $wpdb->get_results( $sql );

        if( isset( $_POST["submit"] ) ){
            $result = $wpdb->insert($wpdb->base_prefix . 'msts_globaltable', array(
                                                                            'blog_id' => $wpdb->blogid,
                                                                            'blog_name' => $_POST['blog_name'],
                                                                            'admin_id' => $_POST['user_id'],
                                                                            'admin_name' => $_POST['user_name'],
                                                                            'title' => $_POST['message_title'],
                                                                            'category_id' => $_POST['category'] ,
                                                                            'admin_message' => htmlspecialchars($_POST['user_message']),
                                                                            'status' => $_POST['status'],
                                                                            'priority' => $_POST['priority'],
																			'date' => current_time('mysql', 1))
            );

            $categoryID = $_POST['category'];
            $subject = $_POST['message_title'];
            $ticket_message = $_POST['user_message'];

            $sqlMail = "SELECT *
                        FROM {$wpdb->base_prefix}msts_categories
                        WHERE id = $categoryID";

            $rec_mail = $wpdb->get_row( $sqlMail );

            if( !empty( $rec_mail->recipient_email ) ){
                $email = $rec_mail->recipient_email;
                $sendFrom = $current_user->user_email;
                $sendCategory = $rec_mail->name;
                $this->msts_sendMail( $email, $subject, $ticket_message, $sendFrom, $sendCategory );
            }

            if ( $result ) {
                $message = __('Ticket wurde verschickt.', 'multisite-ticket-system');
                $this->msts_admin_notice( $message );
            } else {
                $notice = __('Es gab ein Problem beim Verschicken.', 'multisite-ticket-system');
                $this->msts_admin_error_notice( $notice );

            }
        }

        include ( MSTS_CREATETICKET );
    }

    /**
     * Prepares the list of items for displaying
     * and calls function msts_get_tickets
     */
    public function prepare_items() {
        global $wpdb;

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'tickets_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items  = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->base_prefix}msts_globaltable");

        /** Sets the pagination arguments such as total count of items and items per page */
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        $this->items = self::msts_get_tickets( $per_page, $current_page );
    }

    /**
     * Defines extra controls between bulk actions and pagination
     *
     * @param string $which => above ("top") or below the table ("bottom")
     */
    public function extra_tablenav( $which ) {
        global $wpdb;

        $sql ="SELECT *
               FROM {$wpdb->base_prefix}msts_categories";
        $categories = $wpdb->get_results( $sql );

        include ( MSTS_TABLENAV );
    }

    /**
     * Processes bulk actions
     * Checks nonce
     *
     */
    public function process_bulk_action() {
        global $wpdb;
        $table_globaltable = $wpdb->base_prefix . 'msts_globaltable';
        $table_messages = $wpdb->base_prefix . 'msts_messages';

        if ('delete' === $this->current_action()) {
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'delete_ticket' ) ) {
                die( _e('Nicht erlaubt.', 'multisite-ticket-system') );
            }
            else {
                $wpdb->delete( $table_globaltable, array( 'id' => $_GET['ticket'] ) );
                $wpdb->delete( $table_messages, array( 'ticket_id' => $_GET['ticket'] ) );
            }
        }
    }
}
?>