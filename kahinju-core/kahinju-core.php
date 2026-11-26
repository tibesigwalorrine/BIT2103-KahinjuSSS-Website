<?php
/**
 * Plugin Name: Kahinju Core
 * Description: Custom MySQL module for Kahinju Senior Secondary School - departments, academic units (JOIN), clubs, leader messages, recent activities, events, gallery and slider, applications, contact messages, staff role, school settings and an admin dashboard. Full CRUD with prepared statements.
 * Version: 1.4.1
 * Author: Tibesigwa Lorrine
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'KSSC_DIR', plugin_dir_path( __FILE__ ) );
define( 'KSSC_URL', plugin_dir_url( __FILE__ ) );
define( 'KSSC_DB_VERSION', '1.5' );

require_once KSSC_DIR . 'includes/data.php';
require_once KSSC_DIR . 'includes/admin.php';
require_once KSSC_DIR . 'includes/contact.php';
require_once KSSC_DIR . 'includes/apply.php';
require_once KSSC_DIR . 'includes/dashboard.php';

/* ---------- Database tables ---------- */
function kssc_install_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$c = $wpdb->get_charset_collate();
	$p = $wpdb->prefix;

	dbDelta( "CREATE TABLE {$p}kss_departments (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(120) NOT NULL,
		description text NULL,
		photo varchar(255) NULL,
		PRIMARY KEY  (id)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_units (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		department_id bigint(20) unsigned NOT NULL,
		name varchar(150) NOT NULL,
		level varchar(40) NOT NULL DEFAULT 'O-Level',
		description text NULL,
		photo varchar(255) NULL,
		PRIMARY KEY  (id),
		KEY department_id (department_id)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_events (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		title varchar(180) NOT NULL,
		event_date date NOT NULL,
		venue varchar(180) NULL,
		description text NULL,
		PRIMARY KEY  (id),
		KEY event_date (event_date)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_gallery (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		title varchar(180) NOT NULL,
		category varchar(80) NOT NULL,
		image_url varchar(255) NOT NULL,
		media_type varchar(10) NOT NULL DEFAULT 'image',
		description text NULL,
		PRIMARY KEY  (id),
		KEY category (category)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_messages (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(120) NOT NULL,
		email varchar(150) NOT NULL,
		phone varchar(40) NULL,
		subject varchar(180) NOT NULL,
		message text NOT NULL,
		is_read tinyint(1) NOT NULL DEFAULT 0,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_applications (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		reference varchar(24) NULL,
		applicant_name varchar(150) NOT NULL,
		applying_for varchar(60) NOT NULL,
		previous_school varchar(180) NULL,
		guardian_name varchar(150) NOT NULL,
		phone varchar(40) NOT NULL,
		email varchar(150) NULL,
		message text NULL,
		status varchar(12) NOT NULL DEFAULT 'new',
		reply_text text NULL,
		replied_at datetime NULL,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_clubs (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(150) NOT NULL,
		icon varchar(20) NULL,
		description text NULL,
		photo varchar(255) NULL,
		photo2 varchar(255) NULL,
		photo3 varchar(255) NULL,
		patron_name varchar(150) NULL,
		patron_photo varchar(255) NULL,
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (id)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_leaders (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		role varchar(180) NOT NULL,
		name varchar(150) NULL,
		message text NOT NULL,
		photo varchar(255) NULL,
		layout varchar(12) NOT NULL DEFAULT 'card',
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (id)
	) $c;" );

	dbDelta( "CREATE TABLE {$p}kss_activities (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		title varchar(180) NOT NULL,
		description text NULL,
		photo varchar(255) NULL,
		PRIMARY KEY  (id)
	) $c;" );
}

/* ---------- Staff role and demo teacher ---------- */
function kssc_setup_roles() {
	remove_role( 'kss_staff' );
	add_role( 'kss_staff', 'School Staff', array(
		'read'                   => true,
		'upload_files'           => true,
		'edit_posts'             => true,
		'edit_others_posts'      => true,
		'publish_posts'          => true,
		'edit_published_posts'   => true,
		'delete_posts'           => true,
		'delete_published_posts' => true,
		'edit_pages'             => true,
		'edit_others_pages'      => true,
		'edit_published_pages'   => true,
		'kss_manage'             => true,
	) );
	$admin = get_role( 'administrator' );
	if ( $admin ) { $admin->add_cap( 'kss_manage' ); }

	// Simple demo teacher account for local testing (change the password before going live).
	if ( ! username_exists( 'kss_teacher' ) ) {
		wp_insert_user( array(
			'user_login'   => 'kss_teacher',
			'user_pass'    => 'teacher123',
			'user_email'   => 'teacher@kahinjuss.local',
			'display_name' => 'Demo Teacher',
			'role'         => 'kss_staff',
		) );
	}
}

function kssc_activate() {
	kssc_install_tables();
	kssc_setup_roles();
	kssc_seed();
	kssc_remove_duplicates();
	update_option( 'kssc_db_version', KSSC_DB_VERSION );
}
register_activation_hook( __FILE__, 'kssc_activate' );

/**
 * When the plugin files are updated, upgrade the tables and add new sample content.
 * A lock makes sure only one page load runs this at a time (two at once added things twice).
 */
function kssc_maybe_upgrade() {
	if ( get_option( 'kssc_db_version' ) === KSSC_DB_VERSION ) { return; }
	if ( ! add_option( 'kssc_upgrade_lock', time(), '', 'no' ) ) {
		if ( time() - (int) get_option( 'kssc_upgrade_lock' ) < 120 ) { return; }
		update_option( 'kssc_upgrade_lock', time() );
	}
	kssc_activate();
	delete_option( 'kssc_upgrade_lock' );
}
add_action( 'init', 'kssc_maybe_upgrade', 5 );

/** Removes repeated rows (keeps the first one) so nothing shows twice on the site. */
function kssc_remove_duplicates() {
	global $wpdb;
	$p = $wpdb->prefix;

	// Departments: keep the oldest and move its duplicates' subjects to it.
	$keep = array();
	foreach ( (array) $wpdb->get_results( "SELECT id, name FROM {$p}kss_departments ORDER BY id ASC", ARRAY_A ) as $r ) {
		$k = strtolower( trim( $r['name'] ) );
		if ( ! isset( $keep[ $k ] ) ) { $keep[ $k ] = (int) $r['id']; continue; }
		$wpdb->update( "{$p}kss_units", array( 'department_id' => $keep[ $k ] ), array( 'department_id' => (int) $r['id'] ), array( '%d' ), array( '%d' ) );
		$wpdb->delete( "{$p}kss_departments", array( 'id' => (int) $r['id'] ), array( '%d' ) );
	}

	// Other tables: the columns that make a row "the same".
	$sets = array(
		'kss_units'      => array( 'department_id', 'name' ),
		'kss_clubs'      => array( 'name' ),
		'kss_leaders'    => array( 'role', 'name', 'message' ),
		'kss_activities' => array( 'title', 'photo' ),
		'kss_events'     => array( 'title', 'event_date' ),
		'kss_gallery'    => array( 'image_url' ),
	);
	foreach ( $sets as $table => $cols ) {
		$seen = array();
		foreach ( (array) $wpdb->get_results( 'SELECT id, ' . implode( ', ', $cols ) . " FROM {$p}{$table} ORDER BY id ASC", ARRAY_A ) as $r ) {
			$key = '';
			foreach ( $cols as $c ) { $key .= '|' . strtolower( trim( substr( (string) $r[ $c ], 0, 120 ) ) ); }
			if ( isset( $seen[ $key ] ) ) {
				$wpdb->delete( "{$p}{$table}", array( 'id' => (int) $r['id'] ), array( '%d' ) );
			} else {
				$seen[ $key ] = true;
			}
		}
	}
}

/** Path inside the plugin -> stored (migration-safe) address. */
function kssc_asset( $path ) {
	return kssc_relative( plugins_url( $path, __FILE__ ) );
}

/** Add a row only when no row with the same value in $field exists. */
function kssc_insert_once( $table, $field, $value, $data, $formats ) {
	global $wpdb;
	$has = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}{$table} WHERE {$field} = %s", $value ) );
	if ( 0 === $has ) { $wpdb->insert( $wpdb->prefix . $table, $data, $formats ); }
}

/* ---------- Real school details and sample content (admin can edit everything) ---------- */
function kssc_seed() {
	global $wpdb;
	$p = $wpdb->prefix;

	// Contact details given by the school (kept if the admin already set their own).
	$opts = array(
		'kss_phone'   => '0772 874 003',
		'kss_email'   => 'Kahinju@yahoo.com',
		'kss_email2'  => 'Kahinju2022@gmail.com',
		'kss_address' => 'P.O. Box 449, Fort Portal, Uganda',
		'kss_hours'   => '8:00 am - 4:30 pm',
	);
	foreach ( $opts as $k => $v ) {
		if ( '' === (string) get_option( $k, '' ) ) { update_option( $k, $v ); }
	}

	if ( 0 === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}kss_departments" ) ) {
		$depts = array(
			'Sciences'                  => 'Biology, Chemistry, Physics and Mathematics build strong problem-solving skills.',
			'Arts and Humanities'       => 'History, Geography and Religious Education help learners understand people and society.',
			'Languages'                 => 'English, Literature and Kiswahili develop confident speaking, reading and writing.',
			'ICT and Practical Studies' => 'Computer studies, agriculture and entrepreneurship prepare learners for real life.',
		);
		$ids = array();
		foreach ( $depts as $name => $desc ) {
			$wpdb->insert( "{$p}kss_departments", array( 'name' => $name, 'description' => $desc ), array( '%s', '%s' ) );
			$ids[ $name ] = (int) $wpdb->insert_id;
		}
		$units = array(
			array( 'Sciences', 'Biology', 'O-Level & A-Level' ),
			array( 'Sciences', 'Chemistry', 'O-Level & A-Level' ),
			array( 'Sciences', 'Physics', 'O-Level & A-Level' ),
			array( 'Sciences', 'Mathematics', 'O-Level & A-Level' ),
			array( 'Arts and Humanities', 'History', 'O-Level & A-Level' ),
			array( 'Arts and Humanities', 'Geography', 'O-Level & A-Level' ),
			array( 'Arts and Humanities', 'Christian Religious Education', 'O-Level & A-Level' ),
			array( 'Languages', 'English Language', 'O-Level' ),
			array( 'Languages', 'Literature in English', 'O-Level & A-Level' ),
			array( 'Languages', 'Kiswahili', 'O-Level' ),
			array( 'ICT and Practical Studies', 'Computer Studies', 'O-Level & A-Level' ),
			array( 'ICT and Practical Studies', 'Agriculture', 'O-Level' ),
			array( 'ICT and Practical Studies', 'Entrepreneurship', 'O-Level' ),
		);
		foreach ( $units as $u ) {
			$wpdb->insert( "{$p}kss_units", array(
				'department_id' => $ids[ $u[0] ],
				'name'          => $u[1],
				'level'         => $u[2],
				'description'   => '',
			), array( '%d', '%s', '%s', '%s' ) );
		}
	}

	// Photos of students on the department cards (only where none is set yet).
	$dept_photos = array(
		'Sciences'                  => 'assets/gallery/kahinju-m3-16.jpg',
		'Arts and Humanities'       => 'assets/gallery/kahinju-m3-21.jpg',
		'Languages'                 => 'assets/gallery/kahinju-m3-19.jpg',
		'ICT and Practical Studies' => 'assets/gallery/kahinju-m3-17.jpg',
	);
	foreach ( $dept_photos as $name => $path ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$p}kss_departments SET photo = %s WHERE name = %s AND (photo IS NULL OR photo = '')", kssc_asset( $path ), $name ) );
	}

	if ( 0 === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}kss_events" ) ) {
		$wpdb->insert( "{$p}kss_events", array( 'title' => 'Sports and cultural day', 'event_date' => gmdate( 'Y-m-d', strtotime( '+45 days' ) ), 'venue' => 'School grounds', 'description' => '' ), array( '%s', '%s', '%s', '%s' ) );
	}
	// Dates given by the school: Christmas carols on the 25th and break-up on 4 December.
	$yr = (int) gmdate( 'Y' );
	kssc_insert_once( 'kss_events', 'title', 'Christmas carols', array( 'title' => 'Christmas carols', 'event_date' => $yr . '-11-25', 'venue' => 'Kahinju S.S.S', 'description' => 'Join us for Christmas carols at school.' ), array( '%s', '%s', '%s', '%s' ) );
	kssc_insert_once( 'kss_events', 'title', 'Students break off for the term', array( 'title' => 'Students break off for the term', 'event_date' => $yr . '-12-04', 'venue' => 'Kahinju S.S.S', 'description' => 'Students go home at the end of the term.' ), array( '%s', '%s', '%s', '%s' ) );

	// Gallery photos, video and home slider (each file is added once).
	$media = array(
		array( 'Clubs & Students', 'Educate Club members', 'assets/gallery/kahinju-m3-03.jpg', 'image', '' ),
		array( 'Clubs & Students', 'Educate Club exhibition', 'assets/gallery/kahinju-m3-04.jpg', 'image', '' ),
		array( 'Clubs & Students', 'Educate Club exhibition', 'assets/gallery/kahinju-m3-05.jpg', 'image', '' ),
		array( 'Clubs & Students', 'Educate Club practical activity', 'assets/gallery/kahinju-m3-01.jpg', 'image', '' ),
		array( 'Clubs & Students', 'Educate Club practical activity', 'assets/gallery/kahinju-m3-02.jpg', 'image', '' ),
		array( 'Clubs & Students', 'Educate Club display', 'assets/gallery/kahinju-m3-00.jpg', 'image', '' ),
		array( 'Clubs & Students', 'Educate Club with staff', 'assets/gallery/kahinju-m3-25.jpg', 'image', '' ),
		array( 'Clubs & Students', 'Educate Club display', 'assets/gallery/kahinju-m3-26.jpg', 'image', '' ),
		array( 'Classroom', 'Learners in class', 'assets/gallery/kahinju-m3-21.jpg', 'image', '' ),
		array( 'Classroom', 'Learners in class', 'assets/gallery/kahinju-m3-19.jpg', 'image', '' ),
		array( 'Classroom', 'Learners in class', 'assets/gallery/kahinju-m3-16.jpg', 'image', '' ),
		array( 'Classroom', 'Learners in class', 'assets/gallery/kahinju-m3-17.jpg', 'image', '' ),
		array( 'Classroom', 'Learners in class', 'assets/gallery/kahinju-m3-18.jpg', 'image', '' ),
		array( 'Classroom', 'Students at work in class', 'assets/gallery/kahinju-0043.jpg', 'image', '' ),
		array( 'Classroom', 'Group discussion', 'assets/gallery/kahinju-0049.jpg', 'image', '' ),
		array( 'Campus & Bus', 'School campus', 'assets/gallery/kahinju-m3-20.jpg', 'image', '' ),
		array( 'Campus & Bus', 'School campus and bus', 'assets/gallery/kahinju-m3-23.jpg', 'image', '' ),
		array( 'Campus & Bus', 'School buildings', 'assets/gallery/kahinju-m3-10.jpg', 'image', '' ),
		array( 'Campus & Bus', 'School buildings', 'assets/gallery/kahinju-m3-11.jpg', 'image', '' ),
		array( 'Campus & Bus', 'School buildings', 'assets/gallery/kahinju-m3-12.jpg', 'image', '' ),
		array( 'Campus & Bus', 'The Kahinju school bus', 'assets/gallery/kahinju-0062.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'Students together on the school lawn', 'assets/gallery/kahinju-m3-13.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'Students together on the school lawn', 'assets/gallery/kahinju-m3-14.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'Students together on the school lawn', 'assets/gallery/kahinju-m3-15.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'School assembly', 'assets/gallery/kahinju-0009.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'Event under the tent', 'assets/gallery/kahinju-0065.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'Students gathered', 'assets/gallery/kahinju-0053.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'Assembly in front of the school blocks', 'assets/gallery/kahinju-n04.jpg', 'image', '' ),
		array( 'Events & Assemblies', 'Students on the school grounds', 'assets/gallery/kahinju-n07.jpg', 'image', '' ),
		array( 'Videos', 'Students performing at a school event', 'assets/video/kahinju-video.mp4', 'video', '' ),
		array( 'Homepage Slider', 'Welcome to Kahinju Senior Secondary School', 'assets/slider/hero-1.jpg', 'slide', 'A church-founded Government school in Fort Portal. Mixed, day and boarding, with O-Level and A-Level.' ),
		array( 'Homepage Slider', 'Quality, Relevant and Meaningful Education', 'assets/slider/hero-2.jpg', 'slide', 'Producing independent-minded, healthy and self-sustaining citizens.' ),
		array( 'Homepage Slider', 'Discipline, Hard Work, Fear of God and Integrity', 'assets/slider/hero-3.jpg', 'slide', 'Our core values guide every learner at Kahinju.' ),
		array( 'Homepage Slider', 'Learning Beyond the Classroom', 'assets/slider/hero-4.jpg', 'slide', 'Join our clubs: sports, debate, Educate Club, peer education and scouting.' ),
		array( 'Homepage Slider', 'Arts and Sciences at O-Level and A-Level', 'assets/slider/hero-5.jpg', 'slide', 'Day and boarding, with dedicated teachers who guide every learner.' )
	);
	foreach ( $media as $m ) {
		$rel = kssc_asset( $m[2] );
		kssc_insert_once( 'kss_gallery', 'image_url', $rel, array(
			'title'       => $m[1],
			'category'    => $m[0],
			'image_url'   => $rel,
			'media_type'  => $m[3],
			'description' => $m[4],
		), array( '%s', '%s', '%s', '%s', '%s' ) );
	}

	if ( 0 === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}kss_clubs" ) ) {
		$clubs = array(
			array( 'Sports', '⚽', 'Games and athletics that build fitness, teamwork and school pride.', '', '', '', '', '', 1 ),
			array( 'Educate Club', '🎓', 'Learners work together on projects and activities that serve the school and the community.', 'assets/gallery/kahinju-m3-03.jpg', 'assets/gallery/kahinju-m3-04.jpg', 'assets/gallery/kahinju-m3-01.jpg', '', 'assets/people/patron-educate-club.jpg', 2 ),
			array( 'Debate Club', '🎤', 'Builds confidence, public speaking and clear thinking through friendly debates.', '', '', '', '', '', 3 ),
			array( 'Peer Education Rwenzori Kahinju Club', '🤝', 'Learners support one another with life skills and healthy choices.', '', '', '', '', '', 4 ),
			array( 'Scouting Club of Kahinju', '⛺', 'Scouting builds discipline, service to others and outdoor skills.', '', '', '', '', '', 5 ),
		);
		foreach ( $clubs as $c ) {
			$wpdb->insert( "{$p}kss_clubs", array(
				'name'         => $c[0],
				'icon'         => $c[1],
				'description'  => $c[2],
				'photo'        => $c[3] ? kssc_asset( $c[3] ) : '',
				'photo2'       => $c[4] ? kssc_asset( $c[4] ) : '',
				'photo3'       => $c[5] ? kssc_asset( $c[5] ) : '',
				'patron_name'  => $c[6],
				'patron_photo' => $c[7] ? kssc_asset( $c[7] ) : '',
				'sort_order'   => $c[8],
			), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ) );
		}
	}

	if ( 0 === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}kss_leaders" ) ) {
		$leaders = array(
			array( 'Chairperson, Board of Governors', 'Rev. Abigaba Robert Atwooki', 'It is with great pride that I welcome you to the website of Kahinju Senior Secondary School, Fort Portal.

This website marks an important milestone in the life of our school, providing a platform to celebrate our achievements, and to share and document our journey as a learning community.

Kahinju Senior Secondary School is an institution built on a strong foundation of Christianity, discipline, academic excellence and moral integrity. The school has nurtured young men and women of character, confidence and competence who continue to make meaningful contributions to society.

The Board of Governors remains committed to providing sound governance and strategic leadership to ensure that the school continues to grow and thrive. Working closely with the headteacher, Mr. Patrick Akora, and the school administration, we support initiatives that enhance quality teaching and learning, promote responsible management of resources, improve infrastructure and safeguard the welfare of students and staff.

As our motto says, Ready to Serve.', 'assets/people/chairperson.jpg', 'featured', 1 ),
			array( 'Head of Humanities Department', '', 'As the Head of Department for Humanities at Kahinju Senior Secondary School, I can state with pride that our department has been, and remains, a crucial basis for developing critical thinkers and global citizens since the very start of this institution.

The Humanities subjects, encompassing History, Geography and Christian Religious Education, are taught competently at both O-Level and A-Level by a team of highly equipped and dedicated teachers.', 'assets/people/head-humanities.jpg', 'card', 2 ),
			array( 'Head of Biological Sciences Department', '', 'To our valued students who inspire us, the devoted staff who guide and mentor them, cherished parents, and the entire Kahinju community: it is my honour to present this message on behalf of the Department of Biological Sciences at Kahinju Senior Secondary School through this website.

Our department brings together three interrelated disciplines, Biology, Chemistry and Physical Education, each contributing uniquely to the holistic development of our learners.', 'assets/people/head-biological-sciences.jpg', 'card', 3 ),
		);
		foreach ( $leaders as $l ) {
			$wpdb->insert( "{$p}kss_leaders", array(
				'role'       => $l[0],
				'name'       => $l[1],
				'message'    => $l[2],
				'photo'      => kssc_asset( $l[3] ),
				'layout'     => $l[4],
				'sort_order' => $l[5],
			), array( '%s', '%s', '%s', '%s', '%s', '%d' ) );
		}
	}

	if ( 0 === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}kss_activities" ) ) {
		$acts = array(
			array( 'Educate Club exhibition', 'Members of the Educate Club showed their projects to the school community.', 'assets/gallery/kahinju-m3-04.jpg' ),
			array( 'Educate Club practical activity', 'Learners took part in a hands-on activity organised by the Educate Club.', 'assets/gallery/kahinju-m3-01.jpg' ),
			array( 'Together as one school', 'Learners gathered together on the school lawn for a group photo.', 'assets/gallery/kahinju-m3-15.jpg' ),
		);
		foreach ( $acts as $a ) {
			$wpdb->insert( "{$p}kss_activities", array( 'title' => $a[0], 'description' => $a[1], 'photo' => kssc_asset( $a[2] ) ), array( '%s', '%s', '%s' ) );
		}
	}

	// One-time tidy-up: fewer old photos and slides, no untouched sample events.
	if ( ! get_option( 'kssc_refresh_v14' ) ) {
		$keep = array( 'kahinju-0062.jpg', 'kahinju-0043.jpg', 'kahinju-0009.jpg', 'kahinju-0065.jpg', 'kahinju-0053.jpg', 'kahinju-n04.jpg', 'kahinju-n07.jpg', 'kahinju-0049.jpg' );
		$old  = $wpdb->get_results( "SELECT id, image_url FROM {$p}kss_gallery WHERE image_url LIKE '%assets/gallery/kahinju-%'", ARRAY_A );
		foreach ( (array) $old as $r ) {
			if ( preg_match( '#/assets/gallery/(kahinju-(?:\d{4}|n\d{2})\.jpg)$#', $r['image_url'], $mm ) && ! in_array( $mm[1], $keep, true ) ) {
				$wpdb->delete( "{$p}kss_gallery", array( 'id' => (int) $r['id'] ), array( '%d' ) );
			}
		}
		$wpdb->query( "DELETE FROM {$p}kss_gallery WHERE media_type = 'slide' AND image_url LIKE '%assets/slider/slide-%'" );
		$wpdb->query( "DELETE FROM {$p}kss_events WHERE description LIKE 'Sample event.%'" );
		update_option( 'kssc_refresh_v14', 1 );
	}
}
