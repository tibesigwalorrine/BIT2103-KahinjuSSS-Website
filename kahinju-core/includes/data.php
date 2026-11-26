<?php
/**
 * Read functions used by the theme. Every query with a variable uses $wpdb->prepare().
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Make an image address migration-safe: store it without the site domain. */
function kssc_relative( $url ) {
	$home = untrailingslashit( home_url() );
	if ( 0 === strpos( $url, $home ) ) {
		return substr( $url, strlen( $home ) );
	}
	return $url;
}

/** Turn a stored address back into a full URL for the current site. */
function kssc_url( $path ) {
	if ( preg_match( '#^https?://#', $path ) ) { return $path; }
	return home_url( $path );
}

/** Departments with the number of subjects in each (LEFT JOIN + GROUP BY). */
function kssc_get_departments() {
	global $wpdb;
	$p = $wpdb->prefix;
	return $wpdb->get_results(
		"SELECT d.id, d.name, d.description, d.photo, COUNT(u.id) AS unit_count
		 FROM {$p}kss_departments d
		 LEFT JOIN {$p}kss_units u ON u.department_id = d.id
		 GROUP BY d.id, d.name, d.description, d.photo
		 ORDER BY d.name ASC",
		ARRAY_A
	);
}

/** All subjects joined with their department. */
function kssc_get_units() {
	global $wpdb;
	$p = $wpdb->prefix;
	return $wpdb->get_results(
		"SELECT u.id, u.name, u.level, u.description, u.photo, u.department_id,
		        d.name AS department_name, d.description AS department_description, d.photo AS department_photo
		 FROM {$p}kss_units u
		 LEFT JOIN {$p}kss_departments d ON d.id = u.department_id
		 ORDER BY d.name ASC, u.name ASC",
		ARRAY_A
	);
}

/** Events: upcoming (soonest first) or latest first. */
function kssc_get_events( $limit = 10, $upcoming = true ) {
	global $wpdb;
	$p     = $wpdb->prefix;
	$today = current_time( 'Y-m-d' );
	$limit = absint( $limit );
	if ( $upcoming ) {
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$p}kss_events WHERE event_date >= %s ORDER BY event_date ASC LIMIT %d", $today, $limit
		), ARRAY_A );
	}
	return $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM {$p}kss_events WHERE event_date < %s ORDER BY event_date DESC LIMIT %d", $today, $limit
	), ARRAY_A );
}

/**
 * Gallery items. $limit 0 = all.
 * $type '' = photos and videos (home-slider photos are left out), or 'image' / 'video' / 'slide'.
 */
function kssc_get_gallery( $limit = 0, $type = '' ) {
	global $wpdb;
	$p    = $wpdb->prefix;
	$args = array();
	if ( in_array( $type, array( 'image', 'video', 'slide' ), true ) ) {
		$where  = 'WHERE media_type = %s';
		$args[] = $type;
	} else {
		$where = "WHERE media_type <> 'slide'";
	}
	if ( 'slide' === $type ) {
		$order = 'id ASC';
	} elseif ( $limit > 0 ) {
		$order = 'id DESC';
	} else {
		$order = 'category ASC, id ASC';
	}
	$sql = "SELECT * FROM {$p}kss_gallery $where ORDER BY $order";
	if ( $limit > 0 ) {
		$sql   .= ' LIMIT %d';
		$args[] = absint( $limit );
	}
	if ( $args ) {
		return $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A );
	}
	return $wpdb->get_results( $sql, ARRAY_A );
}

/** Co-curricular clubs. */
function kssc_get_clubs() {
	global $wpdb;
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}kss_clubs ORDER BY sort_order ASC, id ASC", ARRAY_A );
}

/** Messages from leaders (chairperson, heads of department). */
function kssc_get_leaders() {
	global $wpdb;
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}kss_leaders ORDER BY sort_order ASC, id ASC", ARRAY_A );
}

/** Latest school activities (cards on the home page). */
function kssc_get_activities( $limit = 3 ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kss_activities ORDER BY id DESC LIMIT %d", absint( $limit ) ), ARRAY_A );
}

/** Student photos used on subject cards that have no photo of their own. */
function kssc_student_photos( $limit = 12 ) {
	global $wpdb;
	$rows = $wpdb->get_col( $wpdb->prepare(
		"SELECT image_url FROM {$wpdb->prefix}kss_gallery WHERE category = %s AND media_type = %s ORDER BY id ASC LIMIT %d",
		'Classroom', 'image', absint( $limit )
	) );
	return $rows ? $rows : array();
}
