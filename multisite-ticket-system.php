<?php
/**
 *	Plugin Name:  Multisite Ticket System
 *	Description: Ticket support system for multisite network. Super Admin receives tickets and manages settings (categories and e-mail addresses). Administrators of the sites (only) can submit tickets. Uses a comment section for updating tickets.
 *	Version: 0.5
 *	Author: Merle Miller
 *	Text Domain: multisite-ticket-system
 *  Domain Path: /languages/
 *	License: GPLv2+
 *	License URI: https://www.gnu.org/licenses/gpl-2.0.html

 *	Multisite Ticket System is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 2 of the license, or
 *	any later version.
 *
 *	Multisite Ticket System is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with Multisite Ticket System. If not, see License URI.
*/

/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/** This class will create the page to load the table */
class MSTS_Plugin {

	/**  Class instance */
	static $instance;

	/**  WP_List_Table object */
	public $tickets_obj;

	/** Class constructor */
	public function __construct() {
		$this->msts_files();
		register_activation_hook( __FILE__, array( $this, 'msts_installation' ) );
		$this->msts_installation();
		$this->msts_class();
		$this->msts_add();
	}

	/** Include file with class MSTS_Table */
	public function msts_class() {
		include ( MSTS_CLASS );
	}

	/** Load plugin translation files */
	function msts_load_textdomain() {
		load_plugin_textdomain( 'multisite-ticket-system', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/** Add filter and action hooks */
	public function msts_add(){
		/** Add action hook for plugin translation files */
		add_action( 'init', array( $this, 'msts_load_textdomain' ) );

		/** Add WordPress Screen Options for hiding or showing columns */
		add_filter( 'set-screen-option', array( $this, 'set_screen' ), 1, 3 );

		/** Add extra submenus and menu options to admin panel menu structure */
		add_action( 'admin_menu', array( $this, 'msts_admin_menu' ) );

		/** Add extra submenus and menu options to network admin panel menu structure */
		add_action( 'network_admin_menu', array(  $this, 'msts_superadmin_menu' ) );

		/** Hook function for logging last login time in users table */
		add_action( 'wp_login', array( $this,'msts_log_time' ) );

		/** Hook function to show notification bubble for plugin in network admin menu */
		add_action( 'network_admin_menu', array( $this,'msts_add_notification' ) );

		/** Hook function to show notification bubble for plugin in admin menu */
		add_action( 'admin_menu', array( $this,'msts_add_notification' ) );
	}

	/** Define different urls and files for this plugin */
	public function msts_files() {
		define('MSTS_PLUGIN_URL', plugin_dir_url(__FILE__));
		define('MSTS_CLASS', 'class-msts-table.php');
		define('MSTS_INCLUDE', 'includes/');
		define('MSTS_INSTALLATION', MSTS_INCLUDE . 'msts_installation.php');
		define('MSTS_NOTIFICATION', MSTS_INCLUDE . 'msts_notification.php');
		define('MSTS_COMMENTS', MSTS_INCLUDE . 'msts_showComments.php');
		define('MSTS_CREATETICKET', MSTS_INCLUDE . 'msts_createTicket.php');
		define('MSTS_SETTINGS', MSTS_INCLUDE . 'msts_showSettings.php');
		define('MSTS_SINGLECATEGORY', MSTS_INCLUDE . 'msts_showSingleCategory.php');
		define('MSTS_SINGLETICKET', MSTS_INCLUDE . 'msts_showSingleTicket.php');
		define('MSTS_TABLENAV', MSTS_INCLUDE . 'msts_tablenav.php');
	}

	/** Logs last login time of current user in WP users table */
	public function msts_log_time( $user_login ) {
		global $wpdb;

		$wpdb->update( 'wp_users', array( 'last_login' => current_time( 'mysql' )), array( 'user_login' => $user_login ) );
	}

	/** Shows notification bubble if there are new or updated tickets
	 *  Checks with all the tickets if update_date of a ticket is bigger than last login time
	 *  If true, show number of new or updated tickets
	 */
	public function msts_add_notification(){
		include ( MSTS_NOTIFICATION );
	}

	/** Activation function
	 *  Installs necessary database tables for plugin
	 */
	public function msts_installation(){
		include_once ( MSTS_INSTALLATION );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/** Add custom menu elements to network admin menu */
	public function msts_superadmin_menu() {
		/** Include CSS file */
		wp_enqueue_style( 'msts_style', MSTS_PLUGIN_URL .('assets/css/msts_style.css') );

		$hook = add_menu_page(
				'Multisite Ticket System',
				'Multisite Ticket System',
				'administrator',
				'msts_plugin',
				array( $this, 'show_network_tickets' ),
				MSTS_PLUGIN_URL. 'assets/images/msts_logo.png',
				90
		);

		add_action( "load-$hook", array( $this, 'screen_option' ) );
		add_submenu_page( 'msts_plugin', 'Multisite Ticket System Settings', __('Einstellungen', 'multisite-ticket-system'), 'administrator', 'msts_settings', array($this,'msts_settings') );
	}

	/** Add custom menu elements to admin menu */
	public function msts_admin_menu() {
		/** Include CSS file */
		wp_enqueue_style( 'msts_style', MSTS_PLUGIN_URL .('assets/css/msts_style.css') );

		$hook = add_menu_page(
			    'Multisite Ticket System',
			    'Multisite Ticket System',
			    'administrator',
			    'msts_plugin',
			    array( $this, 'show_blog_tickets' ),
			    MSTS_PLUGIN_URL . 'assets/images/msts_logo.png',
			    90
		);
		add_action( "load-$hook", array( $this, 'screen_option' ) );
		add_submenu_page( 'msts_plugin', 'Multisite Ticket System Create Ticket',  __('Ticket erstellen', 'multisite-ticket-system'), 'administrator', 'msts_create_ticket', array($this,'msts_create_ticket') );
	}

	/** Call function msts_createTicket() from class MSTS_Table */
	public function msts_create_ticket() {
		$this->tickets_obj = new MSTS_Table();
		$this->tickets_obj->msts_createTicket();
	}

	/** Call function msts_showSettings() from class MSTS_Table */
	public function msts_settings() {
		$this->tickets_obj = new MSTS_Table();
		$this->tickets_obj->msts_showSettings();
	}

	/** Show tickets in network admin
	 *
	 *  Call function prepare_items() from class MSTS_Table
	 *  and display() from WP_List_Table
	 *
	 *  Prepares and displays tickets
	 */
	public function show_network_tickets() {
		/** Call function show_singleTicket() from class MSTS_Table if clicked */
		if( isset( $_GET['action']) && $_GET['action'] == 'show' ) {
			$this->tickets_obj->show_singleTicket($_GET['ticket']);
		}else {
			?>
			<div class="wrap">
			    <h2><?php $titleSettings = __('Einstellungen', 'multisite-ticket-system');
					_e('Tickets', 'multisite-ticket-system');  echo sprintf('<a href="?page=msts_settings" class="page-title-action">%s</a>', $titleSettings); ?></h2>
				    <form method="post">
					    <?php
						$this->tickets_obj->prepare_items();
						$this->tickets_obj->display();
						?>
					</form>
			</div>
			<?php
		}
	}

	/** Show tickets in admin
	 *
	 *  Call function prepare_items() from class MSTS_Table
	 *  and display() from WP_List_Table
	 *
	 *  Prepares and displays tickets
	 */
	public function show_blog_tickets() {
		if( isset( $_GET['action']) && $_GET['action'] == 'show' ) {
			$this->tickets_obj->show_singleTicket($_GET['ticket']);
		}else {
			?>
			<div class="wrap">
		        <h2><?php $titleCreate = __('Erstellen', 'multisite-ticket-system');
					_e('Tickets', 'multisite-ticket-system'); echo sprintf('<a href="?page=msts_create_ticket" class="page-title-action">%s</a>', $titleCreate); ?></h2>
				    <form method="post">
					    <?php
						$this->tickets_obj->prepare_items();
						$this->tickets_obj->display();
						?>
					</form>
			</div>
			<?php
		}
	}

	/** Defines screen options
     *  Shows option to set number of tickets per page
	 */
	public function screen_option() {
		$option = 'per_page';
		$args   = array (
			'label'   => 'Tickets',
			'default' => 10,
			'option'  => 'tickets_per_page'
		);

		add_screen_option( $option, $args );
		$this->tickets_obj = new MSTS_Table();
	}

	/** Singleton instance */
	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

/** This action hook is called once any activated plugins have been loaded */
add_action( 'plugins_loaded', function () {
	MSTS_Plugin::get_instance();
} );
?>