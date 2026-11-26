<?php
/**
 * Admin dashboard: one clear "Kahinju S.S.S Website" panel with counts and quick links to everything on the site.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function kssc_dashboard_setup() {
	if ( ! current_user_can( 'kss_manage' ) ) { return; }
	wp_add_dashboard_widget( 'kssc_overview', 'Kahinju S.S.S Website', 'kssc_dashboard_widget' );

	// Put it first and remove the WordPress news clutter.
	global $wp_meta_boxes;
	if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['kssc_overview'] ) ) {
		$mine = array( 'kssc_overview' => $wp_meta_boxes['dashboard']['normal']['core']['kssc_overview'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['kssc_overview'] );
		$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $mine, $wp_meta_boxes['dashboard']['normal']['core'] );
	}
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'kssc_dashboard_setup' );

function kssc_dashboard_widget() {
	global $wpdb;
	$p     = $wpdb->prefix;
	$count = function ( $sql ) use ( $wpdb ) { return (int) $wpdb->get_var( $sql ); };
	$new_apps = $count( "SELECT COUNT(*) FROM {$p}kss_applications WHERE status = 'new'" );
	$new_msgs = $count( "SELECT COUNT(*) FROM {$p}kss_messages WHERE is_read = 0" );

	echo '<style>
		.kssc-dash{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;margin:12px 0}
		.kssc-dash a{display:block;background:#fbf6f0;border-left:4px solid #8f1a2c;border-radius:6px;padding:10px 12px;text-decoration:none;color:#2a1a1d;font-weight:600}
		.kssc-dash a:hover{background:#8f1a2c;color:#fff}
		.kssc-dash small{display:block;font-weight:400;opacity:.8}
		.kssc-alert{background:#fff8e5;border-left:4px solid #e3b23c;padding:8px 12px;margin:8px 0;border-radius:4px}
	</style>';
	echo '<p>Welcome. Everything on the school website can be edited from here.</p>';
	if ( $new_apps ) { echo '<div class="kssc-alert"><strong>' . (int) $new_apps . '</strong> new application(s) waiting. <a href="' . esc_url( admin_url( 'admin.php?page=kssc_applications' ) ) . '">Open applications</a></div>'; }
	if ( $new_msgs ) { echo '<div class="kssc-alert"><strong>' . (int) $new_msgs . '</strong> unread message(s). <a href="' . esc_url( admin_url( 'admin.php?page=kssc_messages' ) ) . '">Open messages</a></div>'; }

	$links = array(
		array( 'Home page words and colours', admin_url( 'customize.php' ), 'Appearance > Customize' ),
		array( 'Pages', admin_url( 'edit.php?post_type=page' ), 'About, Contact, Apply...' ),
		array( 'News', admin_url( 'edit.php' ), 'Write a news story' ),
		array( 'Events', admin_url( 'admin.php?page=kssc_events' ), $count( "SELECT COUNT(*) FROM {$p}kss_events" ) . ' events' ),
		array( 'Gallery and Slider', admin_url( 'admin.php?page=kssc_gallery' ), $count( "SELECT COUNT(*) FROM {$p}kss_gallery" ) . ' items' ),
		array( 'Clubs', admin_url( 'admin.php?page=kssc_clubs' ), $count( "SELECT COUNT(*) FROM {$p}kss_clubs" ) . ' clubs' ),
		array( 'Leader Messages', admin_url( 'admin.php?page=kssc_leaders' ), 'Chairperson, heads' ),
		array( 'Recent Activities', admin_url( 'admin.php?page=kssc_activities' ), 'Home page cards' ),
		array( 'Departments', admin_url( 'admin.php?page=kssc_departments' ), 'With photos' ),
		array( 'Academic Units', admin_url( 'admin.php?page=kssc_units' ), $count( "SELECT COUNT(*) FROM {$p}kss_units" ) . ' subjects' ),
		array( 'Applications', admin_url( 'admin.php?page=kssc_applications' ), $new_apps . ' new' ),
		array( 'Messages', admin_url( 'admin.php?page=kssc_messages' ), $new_msgs . ' unread' ),
		array( 'School Settings', admin_url( 'admin.php?page=kssc_settings' ), 'Phone, emails, address' ),
		array( 'Menus', admin_url( 'nav-menus.php' ), 'Top menu links' ),
		array( 'Users', admin_url( 'users.php' ), 'Teachers and admins' ),
		array( 'View the website', home_url( '/' ), 'Open the live site' ),
	);
	echo '<div class="kssc-dash">';
	foreach ( $links as $l ) {
		echo '<a href="' . esc_url( $l[1] ) . '">' . esc_html( $l[0] ) . '<small>' . esc_html( $l[2] ) . '</small></a>';
	}
	echo '</div>';
}
