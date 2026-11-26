<?php
/**
 * School Data admin screens: full CRUD (create, read, update, delete) on the custom MySQL tables.
 * All writes use $wpdb->insert / update / delete / prepare (prepared statements) and nonces.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class KSSC_Admin {

	/** Description of every managed table. */
	public static function entities() {
		global $wpdb;
		$p = $wpdb->prefix;
		return array(
			'departments' => array(
				'title'    => 'Departments',
				'singular' => 'Department',
				'table'    => $p . 'kss_departments',
				'fields'   => array(
					'name'        => array( 'label' => 'Department name', 'type' => 'text', 'required' => true ),
					'description' => array( 'label' => 'Short description', 'type' => 'textarea' ),
					'photo'       => array( 'label' => 'Photo of students (shown on the department card)', 'type' => 'image' ),
				),
				'columns'  => array( 'photo' => 'Photo', 'name' => 'Name', 'description' => 'Description' ),
				'sql'      => "SELECT * FROM {$p}kss_departments ORDER BY name ASC",
			),
			'units'       => array(
				'title'    => 'Academic Units',
				'singular' => 'Academic unit',
				'table'    => $p . 'kss_units',
				'fields'   => array(
					'department_id' => array( 'label' => 'Department', 'type' => 'select', 'required' => true, 'options' => self::department_options() ),
					'name'          => array( 'label' => 'Subject / unit name', 'type' => 'text', 'required' => true ),
					'level'         => array( 'label' => 'Level', 'type' => 'select', 'required' => true, 'options' => array(
						'O-Level'           => 'O-Level',
						'A-Level'           => 'A-Level',
						'O-Level & A-Level' => 'O-Level & A-Level',
					) ),
					'description'   => array( 'label' => 'Description (optional)', 'type' => 'textarea' ),
					'photo'         => array( 'label' => 'Photo (optional, otherwise a student photo is used)', 'type' => 'image' ),
				),
				'columns'  => array( 'photo' => 'Photo', 'name' => 'Subject', 'department_name' => 'Department', 'level' => 'Level' ),
				// JOIN: show the department name next to every subject.
				'sql'      => "SELECT u.*, d.name AS department_name FROM {$p}kss_units u LEFT JOIN {$p}kss_departments d ON d.id = u.department_id ORDER BY d.name ASC, u.name ASC",
			),
			'clubs'       => array(
				'title'    => 'Clubs',
				'singular' => 'Club',
				'table'    => $p . 'kss_clubs',
				'fields'   => array(
					'name'         => array( 'label' => 'Club name', 'type' => 'text', 'required' => true ),
					'icon'         => array( 'label' => 'Icon (an emoji, shown when there is no photo)', 'type' => 'text' ),
					'description'  => array( 'label' => 'About the club', 'type' => 'textarea' ),
					'photo'        => array( 'label' => 'Main photo', 'type' => 'image' ),
					'photo2'       => array( 'label' => 'Photo 2 (optional)', 'type' => 'image' ),
					'photo3'       => array( 'label' => 'Photo 3 (optional)', 'type' => 'image' ),
					'patron_name'  => array( 'label' => 'Club patron name (optional)', 'type' => 'text' ),
					'patron_photo' => array( 'label' => 'Club patron photo (optional)', 'type' => 'image' ),
					'sort_order'   => array( 'label' => 'Order on the page (1 = first)', 'type' => 'number' ),
				),
				'columns'  => array( 'photo' => 'Photo', 'name' => 'Club', 'sort_order' => 'Order' ),
				'sql'      => "SELECT * FROM {$p}kss_clubs ORDER BY sort_order ASC, id ASC",
			),
			'leaders'     => array(
				'title'    => 'Leader Messages',
				'singular' => 'Message',
				'table'    => $p . 'kss_leaders',
				'fields'   => array(
					'role'       => array( 'label' => 'Position (for example Chairperson, Board of Governors)', 'type' => 'text', 'required' => true ),
					'name'       => array( 'label' => 'Name', 'type' => 'text' ),
					'message'    => array( 'label' => 'Message (leave a blank line between paragraphs)', 'type' => 'textarea', 'required' => true ),
					'photo'      => array( 'label' => 'Photo', 'type' => 'image' ),
					'layout'     => array( 'label' => 'Style on the home page', 'type' => 'select', 'required' => true, 'options' => array(
						'featured' => 'Big (full width)',
						'card'     => 'Card (side by side)',
					) ),
					'sort_order' => array( 'label' => 'Order on the page (1 = first)', 'type' => 'number' ),
				),
				'columns'  => array( 'photo' => 'Photo', 'role' => 'Position', 'name' => 'Name', 'layout' => 'Style' ),
				'sql'      => "SELECT * FROM {$p}kss_leaders ORDER BY sort_order ASC, id ASC",
			),
			'activities'  => array(
				'title'    => 'Recent Activities',
				'singular' => 'Activity',
				'table'    => $p . 'kss_activities',
				'fields'   => array(
					'title'       => array( 'label' => 'Activity title', 'type' => 'text', 'required' => true ),
					'description' => array( 'label' => 'Short description', 'type' => 'textarea' ),
					'photo'       => array( 'label' => 'Photo', 'type' => 'image', 'required' => true ),
				),
				'columns'  => array( 'photo' => 'Photo', 'title' => 'Activity', 'description' => 'Description' ),
				'sql'      => "SELECT * FROM {$p}kss_activities ORDER BY id DESC",
			),
			'events'      => array(
				'title'    => 'Events',
				'singular' => 'Event',
				'table'    => $p . 'kss_events',
				'fields'   => array(
					'title'       => array( 'label' => 'Event title', 'type' => 'text', 'required' => true ),
					'event_date'  => array( 'label' => 'Date', 'type' => 'date', 'required' => true ),
					'venue'       => array( 'label' => 'Venue', 'type' => 'text' ),
					'description' => array( 'label' => 'Details', 'type' => 'textarea' ),
				),
				'columns'  => array( 'title' => 'Event', 'event_date' => 'Date', 'venue' => 'Venue' ),
				'sql'      => "SELECT * FROM {$p}kss_events ORDER BY event_date DESC",
			),
			'gallery'     => array(
				'title'    => 'Gallery and Slider',
				'singular' => 'Photo',
				'table'    => $p . 'kss_gallery',
				'fields'   => array(
					'title'       => array( 'label' => 'Caption (for a slider photo this is the big headline)', 'type' => 'text', 'required' => true ),
					'category'    => array( 'label' => 'Category', 'type' => 'select', 'required' => true, 'options' => array(
						'Classroom'           => 'Classroom',
						'Events & Assemblies' => 'Events & Assemblies',
						'Campus & Bus'        => 'Campus & Bus',
						'Clubs & Students'    => 'Clubs & Students',
						'Videos'              => 'Videos',
						'Homepage Slider'     => 'Homepage Slider',
					) ),
					'media_type'  => array( 'label' => 'Type', 'type' => 'select', 'required' => true, 'options' => array(
						'image' => 'Photo',
						'video' => 'Video',
						'slide' => 'Home slider photo',
					) ),
					'image_url'   => array( 'label' => 'Photo or video file', 'type' => 'image', 'required' => true ),
					'description' => array( 'label' => 'Slider text (the sentence under the headline; slider photos only)', 'type' => 'textarea' ),
				),
				'columns'  => array( 'image_url' => 'Preview', 'title' => 'Caption', 'category' => 'Category', 'media_type' => 'Type' ),
				'sql'      => "SELECT * FROM {$p}kss_gallery ORDER BY id DESC",
			),
		);
	}

	public static function department_options() {
		global $wpdb;
		$out  = array();
		$rows = $wpdb->get_results( "SELECT id, name FROM {$wpdb->prefix}kss_departments ORDER BY name ASC", ARRAY_A );
		foreach ( (array) $rows as $r ) { $out[ $r['id'] ] = $r['name']; }
		return $out;
	}

	/* ---------- Menu ---------- */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_kssc_save', array( __CLASS__, 'handle_save' ) );
		add_action( 'admin_post_kssc_delete', array( __CLASS__, 'handle_delete' ) );
		add_action( 'admin_post_kssc_msg', array( __CLASS__, 'handle_message_action' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function assets( $hook ) {
		if ( false !== strpos( $hook, 'kssc' ) ) { wp_enqueue_media(); }
	}

	public static function menu() {
		add_menu_page( 'School Data', 'School Data', 'kss_manage', 'kssc', array( __CLASS__, 'page_overview' ), 'dashicons-welcome-learn-more', 3 );
		add_submenu_page( 'kssc', 'Overview', 'Overview', 'kss_manage', 'kssc', array( __CLASS__, 'page_overview' ) );
		foreach ( self::entities() as $key => $e ) {
			add_submenu_page( 'kssc', $e['title'], $e['title'], 'kss_manage', 'kssc_' . $key, function () use ( $key ) {
				KSSC_Admin::page( $key );
			} );
		}
		add_submenu_page( 'kssc', 'Messages', self::messages_menu_label(), 'kss_manage', 'kssc_messages', array( __CLASS__, 'page_messages' ) );
		add_submenu_page( 'kssc', 'School Settings', 'School Settings', 'kss_manage', 'kssc_settings', array( __CLASS__, 'page_settings' ) );
	}

	public static function messages_menu_label() {
		global $wpdb;
		$n = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}kss_messages WHERE is_read = 0" );
		return $n ? 'Messages <span class="awaiting-mod">' . $n . '</span>' : 'Messages';
	}

	/* ---------- Overview ---------- */
	public static function page_overview() {
		global $wpdb;
		$p = $wpdb->prefix;
		$count = function ( $t ) use ( $wpdb, $p ) { return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}{$t}" ); };
		echo '<div class="wrap"><h1>School Data</h1>';
		echo '<p>Manage the content that appears on the Kahinju S.S.S website. Use the menu on the left.</p>';
		echo '<table class="widefat striped" style="max-width:520px"><tbody>';
		printf( '<tr><td>Departments</td><td><strong>%d</strong></td></tr>', $count( 'kss_departments' ) );
		printf( '<tr><td>Academic units (subjects)</td><td><strong>%d</strong></td></tr>', $count( 'kss_units' ) );
		printf( '<tr><td>Clubs</td><td><strong>%d</strong></td></tr>', $count( 'kss_clubs' ) );
		printf( '<tr><td>Events</td><td><strong>%d</strong></td></tr>', $count( 'kss_events' ) );
		printf( '<tr><td>Gallery photos</td><td><strong>%d</strong></td></tr>', $count( 'kss_gallery' ) );
		printf( '<tr><td>Contact messages</td><td><strong>%d</strong></td></tr>', $count( 'kss_messages' ) );
		printf( '<tr><td>Online applications</td><td><strong>%d</strong></td></tr>', $count( 'kss_applications' ) );
		echo '</tbody></table>';
		echo '<h2>Quick guide for teachers</h2><ul style="list-style:disc;margin-left:20px">';
		echo '<li><strong>News:</strong> Posts &gt; Add New.</li>';
		echo '<li><strong>Events, subjects and photos:</strong> use the School Data menu.</li>';
		echo '<li><strong>Online applications:</strong> School Data &gt; Applications. Open one to reply.</li>';
		echo '<li><strong>Home page words, photos and colours:</strong> Appearance &gt; Customize &gt; School Website Texts.</li>';
		echo '<li><strong>Page text (About, Contact):</strong> Pages &gt; edit the page.</li>';
		echo '<li><strong>Phone, email and address:</strong> School Data &gt; School Settings.</li>';
		echo '</ul></div>';
	}

	/* ---------- List / form pages ---------- */
	public static function page( $key ) {
		if ( ! current_user_can( 'kss_manage' ) ) { wp_die( 'You do not have access to this page.' ); }
		global $wpdb;
		$ents = self::entities();
		$e    = $ents[ $key ];
		$do   = isset( $_GET['do'] ) ? sanitize_key( wp_unslash( $_GET['do'] ) ) : 'list';
		$id   = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		$base = admin_url( 'admin.php?page=kssc_' . $key );

		echo '<div class="wrap"><h1 class="wp-heading-inline">' . esc_html( $e['title'] ) . '</h1>';
		if ( 'list' === $do ) {
			echo ' <a class="page-title-action" href="' . esc_url( add_query_arg( 'do', 'add', $base ) ) . '">Add new</a>';
		}
		echo '<hr class="wp-header-end">';
		self::notice();

		if ( 'add' === $do || 'edit' === $do ) {
			$row = null;
			if ( 'edit' === $do && $id ) {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$e['table']} WHERE id = %d", $id ), ARRAY_A );
			}
			self::form( $key, $e, $row, $base );
		} else {
			self::table( $key, $e, $base );
		}
		echo '</div>';
	}

	private static function notice() {
		if ( isset( $_GET['msg'] ) ) {
			$m = sanitize_key( wp_unslash( $_GET['msg'] ) );
			$text = array( 'saved' => 'Saved successfully.', 'deleted' => 'Deleted.', 'inuse' => 'This department still has subjects. Delete or move them first.' );
			if ( isset( $text[ $m ] ) ) {
				printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', 'inuse' === $m ? 'error' : 'success', esc_html( $text[ $m ] ) );
			}
		}
		if ( isset( $_GET['err'] ) ) {
			printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( sanitize_text_field( wp_unslash( $_GET['err'] ) ) ) );
		}
	}

	private static function table( $key, $e, $base ) {
		global $wpdb;
		$rows = $wpdb->get_results( $e['sql'], ARRAY_A ); // Static query, no user input.
		echo '<table class="widefat striped"><thead><tr>';
		foreach ( $e['columns'] as $label ) { echo '<th>' . esc_html( $label ) . '</th>'; }
		echo '<th style="width:140px">Actions</th></tr></thead><tbody>';
		if ( ! $rows ) {
			echo '<tr><td colspan="' . ( count( $e['columns'] ) + 1 ) . '">Nothing here yet. Click "Add new".</td></tr>';
		}
		foreach ( (array) $rows as $r ) {
			echo '<tr>';
			foreach ( $e['columns'] as $col => $label ) {
				if ( 'photo' === $col ) {
					echo '<td>' . ( ! empty( $r[ $col ] ) ? '<img src="' . esc_url( kssc_url( $r[ $col ] ) ) . '" style="width:80px;height:56px;object-fit:cover;border-radius:4px">' : '-' ) . '</td>';
					continue;
				}
				if ( 'image_url' === $col ) {
					if ( isset( $r['media_type'] ) && 'video' === $r['media_type'] ) {
						echo '<td><video src="' . esc_url( kssc_url( $r[ $col ] ) ) . '#t=0.5" preload="metadata" muted style="width:80px;height:56px;object-fit:cover;border-radius:4px"></video></td>';
					} else {
						echo '<td><img src="' . esc_url( kssc_url( $r[ $col ] ) ) . '" style="width:80px;height:56px;object-fit:cover;border-radius:4px"></td>';
					}
				} else {
					echo '<td>' . esc_html( wp_trim_words( (string) $r[ $col ], 18 ) ) . '</td>';
				}
			}
			$edit = add_query_arg( array( 'do' => 'edit', 'id' => $r['id'] ), $base );
			$del  = wp_nonce_url( admin_url( 'admin-post.php?action=kssc_delete&entity=' . $key . '&id=' . (int) $r['id'] ), 'kssc_delete_' . $key . '_' . (int) $r['id'] );
			echo '<td><a href="' . esc_url( $edit ) . '">Edit</a> | <a href="' . esc_url( $del ) . '" style="color:#b32d2e" onclick="return confirm(\'Delete this item?\')">Delete</a></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

	private static function form( $key, $e, $row, $base ) {
		echo '<h2>' . ( $row ? 'Edit ' : 'Add ' ) . esc_html( strtolower( $e['singular'] ) ) . '</h2>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="max-width:640px">';
		echo '<input type="hidden" name="action" value="kssc_save"><input type="hidden" name="entity" value="' . esc_attr( $key ) . '">';
		echo '<input type="hidden" name="id" value="' . ( $row ? (int) $row['id'] : 0 ) . '">';
		wp_nonce_field( 'kssc_save_' . $key );
		echo '<table class="form-table" role="presentation">';
		foreach ( $e['fields'] as $name => $f ) {
			$val = $row ? (string) $row[ $name ] : '';
			echo '<tr><th><label for="f_' . esc_attr( $name ) . '">' . esc_html( $f['label'] ) . ( ! empty( $f['required'] ) ? ' *' : '' ) . '</label></th><td>';
			switch ( $f['type'] ) {
				case 'textarea':
					echo '<textarea class="large-text" rows="4" id="f_' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $val ) . '</textarea>';
					break;
				case 'select':
					echo '<select id="f_' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '"><option value="">- choose -</option>';
					foreach ( $f['options'] as $ov => $ol ) {
						echo '<option value="' . esc_attr( $ov ) . '"' . selected( (string) $val, (string) $ov, false ) . '>' . esc_html( $ol ) . '</option>';
					}
					echo '</select>';
					break;
				case 'number':
					echo '<input type="number" min="0" id="f_' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '">';
					break;
				case 'date':
					echo '<input type="date" id="f_' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '">';
					break;
				case 'image':
					echo '<input type="text" class="regular-text" id="f_' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '"> ';
					echo '<button type="button" class="button kssc-pick" data-target="f_' . esc_attr( $name ) . '">Choose file</button>';
					$is_vid = (bool) preg_match( '/\.(mp4|webm|mov)$/i', $val );
					echo '<p><img id="prev_' . esc_attr( $name ) . '" src="' . esc_url( ( $val && ! $is_vid ) ? kssc_url( $val ) : '' ) . '" style="max-width:220px;margin-top:8px;border-radius:6px' . ( ( $val && ! $is_vid ) ? '' : ';display:none' ) . '"></p>';
					if ( $is_vid ) { echo '<p><em>Video file selected.</em></p>'; }
					break;
				default:
					echo '<input type="text" class="regular-text" id="f_' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '">';
			}
			echo '</td></tr>';
		}
		echo '</table>';
		submit_button( $row ? 'Update' : 'Save' );
		echo ' <a href="' . esc_url( $base ) . '">Cancel</a></form>';
		?>
		<script>
		jQuery(function($){
			$('.kssc-pick').on('click',function(e){
				e.preventDefault();
				var t=$(this).data('target');
				var frame=wp.media({title:'Choose a photo or video',multiple:false});
				frame.on('select',function(){
					var a=frame.state().get('selection').first().toJSON();
					$('#'+t).val(a.url);
					$('#prev_'+t.replace('f_','')).attr('src',a.url).show();
				});
				frame.open();
			});
		});
		</script>
		<?php
	}

	/* ---------- Save (create + update) ---------- */
	public static function handle_save() {
		$key  = isset( $_POST['entity'] ) ? sanitize_key( wp_unslash( $_POST['entity'] ) ) : '';
		$ents = self::entities();
		if ( ! isset( $ents[ $key ] ) ) { wp_die( 'Invalid request.' ); }
		check_admin_referer( 'kssc_save_' . $key );
		if ( ! current_user_can( 'kss_manage' ) ) { wp_die( 'You do not have access.' ); }

		global $wpdb;
		$e      = $ents[ $key ];
		$id     = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$data   = array();
		$errors = array();

		foreach ( $e['fields'] as $name => $f ) {
			$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
			if ( ! is_string( $raw ) ) { $raw = ''; }
			switch ( $f['type'] ) {
				case 'textarea': $v = sanitize_textarea_field( $raw ); break;
				case 'select':
					$v = sanitize_text_field( $raw );
					if ( ! array_key_exists( $v, $f['options'] ) ) { $v = ''; }
					break;
				case 'date': $v = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : ''; break;
				case 'number': $v = (string) absint( $raw ); break;
				case 'image': $v = kssc_relative( esc_url_raw( $raw ) ); break;
				default: $v = sanitize_text_field( $raw );
			}
			if ( ! empty( $f['required'] ) && '' === $v ) { $errors[] = $f['label'] . ' is required.'; }
			$data[ $name ] = $v;
		}

		$page = admin_url( 'admin.php' );
		if ( $errors ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'kssc_' . $key, 'do' => $id ? 'edit' : 'add', 'id' => $id, 'err' => rawurlencode( implode( ' ', $errors ) ) ), $page ) );
			exit;
		}

		$formats = array_fill( 0, count( $data ), '%s' );
		if ( $id ) {
			$wpdb->update( $e['table'], $data, array( 'id' => $id ), $formats, array( '%d' ) );
		} else {
			$wpdb->insert( $e['table'], $data, $formats );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'kssc_' . $key, 'msg' => 'saved' ), $page ) );
		exit;
	}

	/* ---------- Delete ---------- */
	public static function handle_delete() {
		$key  = isset( $_GET['entity'] ) ? sanitize_key( wp_unslash( $_GET['entity'] ) ) : '';
		$id   = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		$ents = self::entities();
		if ( ! isset( $ents[ $key ] ) || ! $id ) { wp_die( 'Invalid request.' ); }
		check_admin_referer( 'kssc_delete_' . $key . '_' . $id );
		if ( ! current_user_can( 'kss_manage' ) ) { wp_die( 'You do not have access.' ); }

		global $wpdb;
		$msg = 'deleted';
		if ( 'departments' === $key ) {
			$used = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}kss_units WHERE department_id = %d", $id ) );
			if ( $used > 0 ) { $msg = 'inuse'; }
		}
		if ( 'deleted' === $msg ) {
			$wpdb->delete( $ents[ $key ]['table'], array( 'id' => $id ), array( '%d' ) );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'kssc_' . $key, 'msg' => $msg ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/* ---------- Messages from the contact form ---------- */
	public static function page_messages() {
		if ( ! current_user_can( 'kss_manage' ) ) { wp_die( 'You do not have access to this page.' ); }
		global $wpdb;
		echo '<div class="wrap"><h1>Contact Messages</h1><hr class="wp-header-end">';
		if ( isset( $_GET['msg'] ) ) { echo '<div class="notice notice-success is-dismissible"><p>Done.</p></div>'; }
		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}kss_messages ORDER BY created_at DESC", ARRAY_A );
		if ( ! $rows ) { echo '<p>No messages yet.</p></div>'; return; }
		echo '<table class="widefat striped"><thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th>Actions</th></tr></thead><tbody>';
		foreach ( $rows as $r ) {
			$id   = (int) $r['id'];
			$read = wp_nonce_url( admin_url( 'admin-post.php?action=kssc_msg&do=read&id=' . $id ), 'kssc_msg_' . $id );
			$del  = wp_nonce_url( admin_url( 'admin-post.php?action=kssc_msg&do=delete&id=' . $id ), 'kssc_msg_' . $id );
			echo '<tr' . ( $r['is_read'] ? '' : ' style="font-weight:600;background:#fff8e5"' ) . '>';
			echo '<td>' . esc_html( $r['name'] ) . '<br><a href="mailto:' . esc_attr( $r['email'] ) . '">' . esc_html( $r['email'] ) . '</a><br>' . esc_html( $r['phone'] ) . '</td>';
			echo '<td>' . esc_html( $r['subject'] ) . '</td><td style="max-width:360px">' . nl2br( esc_html( $r['message'] ) ) . '</td>';
			echo '<td>' . esc_html( $r['created_at'] ) . '</td><td>';
			if ( ! $r['is_read'] ) { echo '<a href="' . esc_url( $read ) . '">Mark read</a> | '; }
			echo '<a href="' . esc_url( $del ) . '" style="color:#b32d2e" onclick="return confirm(\'Delete this message?\')">Delete</a></td></tr>';
		}
		echo '</tbody></table></div>';
	}

	public static function handle_message_action() {
		$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		$do = isset( $_GET['do'] ) ? sanitize_key( wp_unslash( $_GET['do'] ) ) : '';
		check_admin_referer( 'kssc_msg_' . $id );
		if ( ! current_user_can( 'kss_manage' ) ) { wp_die( 'You do not have access.' ); }
		global $wpdb;
		$t = $wpdb->prefix . 'kss_messages';
		if ( 'read' === $do ) { $wpdb->update( $t, array( 'is_read' => 1 ), array( 'id' => $id ), array( '%d' ), array( '%d' ) ); }
		if ( 'delete' === $do ) { $wpdb->delete( $t, array( 'id' => $id ), array( '%d' ) ); }
		wp_safe_redirect( add_query_arg( array( 'page' => 'kssc_messages', 'msg' => 'done' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/* ---------- School settings (phone, email, address...) ---------- */
	public static function register_settings() {
		$text = array( 'phone', 'address', 'hours' );
		foreach ( $text as $k ) { register_setting( 'kssc_settings', 'kss_' . $k, array( 'sanitize_callback' => 'sanitize_text_field' ) ); }
		register_setting( 'kssc_settings', 'kss_email', array( 'sanitize_callback' => 'sanitize_email' ) );
		register_setting( 'kssc_settings', 'kss_email2', array( 'sanitize_callback' => 'sanitize_email' ) );
		register_setting( 'kssc_settings', 'kss_facebook', array( 'sanitize_callback' => 'esc_url_raw' ) );
	}

	public static function page_settings() {
		if ( ! current_user_can( 'kss_manage' ) ) { wp_die( 'You do not have access to this page.' ); }
		echo '<div class="wrap"><h1>School Settings</h1><p>These details appear in the top bar, the footer and the Contact page.</p>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'kssc_settings' );
		$fields = array( 'phone' => 'Phone (Head Teacher)', 'email' => 'Email', 'email2' => 'Second email', 'address' => 'Address (P.O. Box and place)', 'hours' => 'Opening hours', 'facebook' => 'Facebook page link' );
		echo '<table class="form-table" role="presentation">';
		foreach ( $fields as $k => $label ) {
			printf( '<tr><th><label for="kss_%1$s">%2$s</label></th><td><input type="text" class="regular-text" id="kss_%1$s" name="kss_%1$s" value="%3$s"></td></tr>', esc_attr( $k ), esc_html( $label ), esc_attr( get_option( 'kss_' . $k, '' ) ) );
		}
		echo '</table>';
		submit_button();
		echo '</form></div>';
	}
}
KSSC_Admin::init();

/* Let staff reach options.php for the settings form. */
add_filter( 'option_page_capability_kssc_settings', function () { return 'kss_manage'; } );
