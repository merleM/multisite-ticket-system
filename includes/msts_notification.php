<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

global $menu;
global $current_user;
global $wpdb;
get_currentuserinfo();

$userID = $current_user->ID;
$lastLogin = $current_user->last_login;


    if( !is_network_admin() ) {
		$sql_dates = "SELECT update_date
              FROM {$wpdb->base_prefix}msts_globaltable
			  WHERE admin_id = $userID";
		$sqlDates = $wpdb->get_results($sql_dates);
		$msts_count = 0;

		foreach( $sqlDates as $sqlDate ) {
			if( strtotime( $sqlDate->update_date ) > strtotime( $lastLogin ) ) {
				$msts_count++;
			}
		}
		
		if( $msts_count != 0 ) {
			$menu[90][0] .= ' <span class=\'update-plugins count-3\'><span class=\'update-count\'>' . $msts_count . '</span></span>';
		}
    } else {
		$sql_dates = "SELECT update_date
              FROM {$wpdb->base_prefix}msts_globaltable";
		$sqlDates = $wpdb->get_results($sql_dates);
		$msts_count = 0;

		foreach( $sqlDates as $sqlDate ) {
			if( strtotime( $sqlDate->update_date ) > strtotime( $lastLogin ) ) {
				$msts_count++;
			}
		}
		
		if( $msts_count != 0 ) {
			$menu[90][0] .= ' <span class=\'update-plugins count-3\'><span class=\'update-count\'> ! </span></span>';
		}
    }


?>