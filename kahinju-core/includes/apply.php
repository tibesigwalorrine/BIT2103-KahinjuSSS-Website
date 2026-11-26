<?php
/**
 * Apply Online: public form, saved applications, and the admin screen to read and reply.
 * All database writes use $wpdb->insert / update / delete / prepare, with nonces.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Classes a learner can apply for. */
function kssc_class_options() {
	return array(
		'Senior One (S.1)',
		'Senior Two (S.2)',
		'Senior Three (S.3)',
		'Senior Four (S.4)',
		'Senior Five (S.5) - A-Level',
		'Senior Six (S.6) - A-Level',
	);
}

/** 0772 123 456 -> 256772123456 (for WhatsApp and SMS links). */
function kssc_phone_intl( $phone ) {
	$d = preg_replace( '/\D+/', '', (string) $phone );
	if ( '' === $d ) { return ''; }
	if ( 0 === strpos( $d, '00' ) ) { $d = substr( $d, 2 ); }
	if ( 0 === strpos( $d, '0' ) ) { $d = '256' . substr( $d, 1 ); }
	if ( 9 === strlen( $d ) ) { $d = '256' . $d; }
	return $d;
}

/* ---------- Public form ---------- */
function kssc_apply_form() {
	if ( isset( $_GET['kssc_apply'] ) ) {
		$code = sanitize_key( wp_unslash( $_GET['kssc_apply'] ) );
		if ( 'ok' === $code ) {
			$ref = isset( $_GET['ref'] ) ? sanitize_text_field( wp_unslash( $_GET['ref'] ) ) : '';
			return '<div class="kssc-success"><span style="font-size:2.4rem">&#127881;</span><strong>Application received!</strong>'
				. '<p>Thank you. Your reference number is <b>' . esc_html( $ref ) . '</b>.<br>The school office will reply to the phone number you gave.</p></div>';
		}
	}
	$out = '';
	if ( isset( $_GET['kssc_apply'] ) ) {
		$code = sanitize_key( wp_unslash( $_GET['kssc_apply'] ) );
		if ( 'wait' === $code ) {
			$out .= '<div class="kssc-notice err">Please wait a few seconds before sending again.</div>';
		} else {
			$out .= '<div class="kssc-notice err">Please fill in all the fields marked * (and tick the box at the bottom).</div>';
		}
	}
	$out .= '<form class="kssc-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	$out .= '<input type="hidden" name="action" value="kssc_apply">';
	$out .= wp_nonce_field( 'kssc_apply', 'kssc_nonce', true, false );
	$out .= '<input type="hidden" name="kssc_back" value="' . esc_url( get_permalink() ) . '">';
	$out .= '<div class="kssc-hp"><label>Leave this empty <input type="text" name="kssc_website" tabindex="-1" autocomplete="off"></label></div>';
	$out .= '<label for="ka_name">Learner&#8217;s full name *</label><input type="text" id="ka_name" name="ka_name" required>';
	$out .= '<div class="row"><div><label for="ka_class">Class applying for *</label><select id="ka_class" name="ka_class" required><option value="">- choose -</option>';
	foreach ( kssc_class_options() as $c ) {
		$out .= '<option value="' . esc_attr( $c ) . '">' . esc_html( $c ) . '</option>';
	}
	$out .= '</select></div><div><label for="ka_prev">Previous school</label><input type="text" id="ka_prev" name="ka_prev"></div></div>';
	$out .= '<label for="ka_guardian">Parent / guardian full name *</label><input type="text" id="ka_guardian" name="ka_guardian" required>';
	$out .= '<div class="row"><div><label for="ka_phone">Phone number (we reply here) *</label><input type="tel" id="ka_phone" name="ka_phone" placeholder="0772 000 000" required></div>';
	$out .= '<div><label for="ka_email">Email (optional)</label><input type="email" id="ka_email" name="ka_email"></div></div>';
	$out .= '<label for="ka_message">Anything else we should know</label><textarea id="ka_message" name="ka_message" rows="4"></textarea>';
	$out .= '<label class="kssc-check"><input type="checkbox" name="ka_consent" value="1" required><span>I agree that the school may use these details to contact me about this application.</span></label>';
	$out .= '<button type="submit" class="btn btn-primary">Send Application</button></form>';
	return $out;
}
add_shortcode( 'kahinju_apply_form', 'kssc_apply_form' );

function kssc_handle_apply() {
	$back = isset( $_POST['kssc_back'] ) ? esc_url_raw( wp_unslash( $_POST['kssc_back'] ) ) : home_url( '/' );
	$back = wp_validate_redirect( $back, home_url( '/' ) );
	$go   = function ( $code, $ref = '' ) use ( $back ) {
		$url = add_query_arg( array( 'kssc_apply' => $code, 'ref' => $ref ), $back );
		wp_safe_redirect( $url . '#apply-form' );
		exit;
	};

	if ( ! isset( $_POST['kssc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kssc_nonce'] ) ), 'kssc_apply' ) ) {
		$go( 'bad' );
	}
	if ( ! empty( $_POST['kssc_website'] ) ) { $go( 'bad' ); } // Hidden field filled: a bot.

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	$key = 'kssc_ap_' . md5( $ip );
	if ( get_transient( $key ) ) { $go( 'wait' ); }

	$get = function ( $name, $multi = false ) {
		if ( ! isset( $_POST[ $name ] ) || ! is_string( $_POST[ $name ] ) ) { return ''; }
		$v = wp_unslash( $_POST[ $name ] );
		return $multi ? sanitize_textarea_field( $v ) : sanitize_text_field( $v );
	};
	$name     = $get( 'ka_name' );
	$class    = $get( 'ka_class' );
	$prev     = $get( 'ka_prev' );
	$guardian = $get( 'ka_guardian' );
	$phone    = $get( 'ka_phone' );
	$email    = isset( $_POST['ka_email'] ) && is_string( $_POST['ka_email'] ) ? sanitize_email( wp_unslash( $_POST['ka_email'] ) ) : '';
	$message  = $get( 'ka_message', true );

	if ( '' === $name || '' === $guardian || ! in_array( $class, kssc_class_options(), true ) ) { $go( 'bad' ); }
	if ( ! preg_match( '/^[0-9+\s()-]{7,20}$/', $phone ) ) { $go( 'bad' ); }
	if ( empty( $_POST['ka_consent'] ) ) { $go( 'bad' ); }
	if ( '' !== $email && ! is_email( $email ) ) { $go( 'bad' ); }

	global $wpdb;
	$t  = $wpdb->prefix . 'kss_applications';
	$ok = $wpdb->insert( $t, array(
		'applicant_name'  => $name,
		'applying_for'    => $class,
		'previous_school' => $prev,
		'guardian_name'   => $guardian,
		'phone'           => $phone,
		'email'           => $email,
		'message'         => $message,
		'status'          => 'new',
		'created_at'      => current_time( 'mysql' ),
	), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
	if ( false === $ok ) { $go( 'bad' ); }

	$id  = (int) $wpdb->insert_id;
	$ref = sprintf( 'KSS-%s-%04d', gmdate( 'Y' ), $id );
	$wpdb->update( $t, array( 'reference' => $ref ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	set_transient( $key, 1, 20 );

	// Email the school, and the applicant if they gave an email (may do nothing on a local PC).
	$to = get_option( 'kss_email', '' );
	if ( ! is_email( $to ) ) { $to = get_option( 'admin_email' ); }
	@wp_mail( $to, 'New application ' . $ref . ': ' . $name, "Learner: $name\nClass: $class\nParent/guardian: $guardian\nPhone: $phone\nEmail: $email\n\n$message" );
	if ( '' !== $email ) {
		@wp_mail( $email, 'We received your application (' . $ref . ')', "Dear $guardian,\n\nWe have received the application of $name to $class at Kahinju Senior Secondary School.\nYour reference number is $ref.\nThe school office will reply to you soon.\n\nThank you." );
	}
	$go( 'ok', $ref );
}
add_action( 'admin_post_nopriv_kssc_apply', 'kssc_handle_apply' );
add_action( 'admin_post_kssc_apply', 'kssc_handle_apply' );

/* ---------- Admin: applications list, reply and delete ---------- */
class KSSC_Applications {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_post_kssc_app_reply', array( __CLASS__, 'handle_reply' ) );
		add_action( 'admin_post_kssc_app_delete', array( __CLASS__, 'handle_delete' ) );
	}

	public static function menu() {
		global $wpdb;
		$n     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}kss_applications WHERE status = 'new'" );
		$label = $n ? 'Applications <span class="awaiting-mod">' . $n . '</span>' : 'Applications';
		add_submenu_page( 'kssc', 'Applications', $label, 'kss_manage', 'kssc_applications', array( __CLASS__, 'page' ) );
	}

	public static function page() {
		if ( ! current_user_can( 'kss_manage' ) ) { wp_die( 'You do not have access to this page.' ); }
		global $wpdb;
		$t    = $wpdb->prefix . 'kss_applications';
		$base = admin_url( 'admin.php?page=kssc_applications' );
		$id   = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

		echo '<div class="wrap"><h1>Online Applications</h1><hr class="wp-header-end">';
		if ( isset( $_GET['msg'] ) ) {
			$m = sanitize_key( wp_unslash( $_GET['msg'] ) );
			$text = array( 'replied' => 'Reply saved. If the applicant gave an email, an email was sent. Use the WhatsApp or SMS button to reach the phone.', 'deleted' => 'Application deleted.' );
			if ( isset( $text[ $m ] ) ) { echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $text[ $m ] ) . '</p></div>'; }
		}

		if ( $id ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE id = %d", $id ), ARRAY_A );
			if ( $row ) { self::view( $row, $base ); echo '</div>'; return; }
		}

		$rows = $wpdb->get_results( "SELECT * FROM $t ORDER BY created_at DESC", ARRAY_A );
		if ( ! $rows ) { echo '<p>No applications yet.</p></div>'; return; }
		echo '<table class="widefat striped"><thead><tr><th>Ref</th><th>Learner</th><th>Class</th><th>Parent / guardian</th><th>Phone</th><th>Received</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
		foreach ( $rows as $r ) {
			$view = add_query_arg( 'id', (int) $r['id'], $base );
			$del  = wp_nonce_url( admin_url( 'admin-post.php?action=kssc_app_delete&id=' . (int) $r['id'] ), 'kssc_app_' . (int) $r['id'] );
			$new  = ( 'new' === $r['status'] );
			echo '<tr' . ( $new ? ' style="font-weight:600;background:#fff8e5"' : '' ) . '>';
			echo '<td>' . esc_html( $r['reference'] ) . '</td><td>' . esc_html( $r['applicant_name'] ) . '</td><td>' . esc_html( $r['applying_for'] ) . '</td>';
			echo '<td>' . esc_html( $r['guardian_name'] ) . '</td><td>' . esc_html( $r['phone'] ) . '</td><td>' . esc_html( $r['created_at'] ) . '</td>';
			echo '<td>' . ( $new ? 'New' : 'Replied' ) . '</td>';
			echo '<td><a href="' . esc_url( $view ) . '">Open / Reply</a> | <a href="' . esc_url( $del ) . '" style="color:#b32d2e" onclick="return confirm(\'Delete this application?\')">Delete</a></td></tr>';
		}
		echo '</tbody></table></div>';
	}

	private static function view( $r, $base ) {
		$default = $r['reply_text'];
		if ( '' === (string) $default ) {
			$default = sprintf(
				"Dear %s,\nWe have received the application of %s to %s at Kahinju Senior Secondary School (Ref %s).\nOur office will contact you soon with the next steps. Thank you.\n- Kahinju S.S.S Administration",
				$r['guardian_name'], $r['applicant_name'], $r['applying_for'], $r['reference']
			);
		}
		$intl = kssc_phone_intl( $r['phone'] );
		echo '<p><a href="' . esc_url( $base ) . '">&larr; Back to all applications</a></p>';
		echo '<table class="widefat striped" style="max-width:720px"><tbody>';
		$fields = array(
			'Reference'         => $r['reference'],
			'Learner'           => $r['applicant_name'],
			'Class applying for' => $r['applying_for'],
			'Previous school'   => $r['previous_school'],
			'Parent / guardian' => $r['guardian_name'],
			'Phone'             => $r['phone'],
			'Email'             => $r['email'],
			'Message'           => $r['message'],
			'Received'          => $r['created_at'],
			'Status'            => ( 'new' === $r['status'] ) ? 'New (not replied yet)' : 'Replied on ' . $r['replied_at'],
		);
		foreach ( $fields as $k => $v ) {
			echo '<tr><th style="width:200px">' . esc_html( $k ) . '</th><td>' . nl2br( esc_html( (string) $v ) ) . '</td></tr>';
		}
		echo '</tbody></table>';

		echo '<h2>Reply to the applicant</h2>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="max-width:720px">';
		echo '<input type="hidden" name="action" value="kssc_app_reply"><input type="hidden" name="id" value="' . (int) $r['id'] . '">';
		wp_nonce_field( 'kssc_app_' . (int) $r['id'] );
		echo '<textarea id="kssc_reply" name="reply_text" rows="7" class="large-text">' . esc_textarea( $default ) . '</textarea>';
		echo '<p><button type="submit" class="button button-primary">Save reply' . ( $r['email'] ? ' and email it' : '' ) . '</button> ';
		if ( $intl ) {
			echo '<a id="kssc-wa" class="button" target="_blank" rel="noopener" data-phone="' . esc_attr( $intl ) . '" href="#">Send on WhatsApp</a> ';
			echo '<a id="kssc-sms" class="button" data-phone="' . esc_attr( $intl ) . '" href="#">Send as SMS (opens on a phone)</a>';
		}
		echo '</p><p class="description">The WhatsApp and SMS buttons open the message ready to send to ' . esc_html( $r['phone'] ) . '. Sending SMS automatically from the website needs an SMS service account, which can be added later.</p></form>';
		?>
		<script>
		(function(){
			var box=document.getElementById('kssc_reply');
			function link(id,build){
				var a=document.getElementById(id);
				if(!a){return;}
				a.addEventListener('click',function(){
					a.href=build(a.getAttribute('data-phone'),encodeURIComponent(box.value));
				});
			}
			link('kssc-wa',function(p,t){return 'https://wa.me/'+p+'?text='+t;});
			link('kssc-sms',function(p,t){return 'sms:+'+p+'?body='+t;});
		})();
		</script>
		<?php
	}

	public static function handle_reply() {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		check_admin_referer( 'kssc_app_' . $id );
		if ( ! current_user_can( 'kss_manage' ) || ! $id ) { wp_die( 'You do not have access.' ); }
		global $wpdb;
		$t     = $wpdb->prefix . 'kss_applications';
		$text  = isset( $_POST['reply_text'] ) && is_string( $_POST['reply_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reply_text'] ) ) : '';
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE id = %d", $id ), ARRAY_A );
		if ( $row && '' !== $text ) {
			$wpdb->update( $t, array( 'reply_text' => $text, 'status' => 'replied', 'replied_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s', '%s' ), array( '%d' ) );
			if ( is_email( $row['email'] ) ) {
				@wp_mail( $row['email'], 'Your application to Kahinju S.S.S (' . $row['reference'] . ')', $text );
			}
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'kssc_applications', 'id' => $id, 'msg' => 'replied' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function handle_delete() {
		$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		check_admin_referer( 'kssc_app_' . $id );
		if ( ! current_user_can( 'kss_manage' ) || ! $id ) { wp_die( 'You do not have access.' ); }
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'kss_applications', array( 'id' => $id ), array( '%d' ) );
		wp_safe_redirect( add_query_arg( array( 'page' => 'kssc_applications', 'msg' => 'deleted' ), admin_url( 'admin.php' ) ) );
		exit;
	}
}
KSSC_Applications::init();
