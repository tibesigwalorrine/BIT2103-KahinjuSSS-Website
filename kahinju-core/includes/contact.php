<?php
/**
 * Contact form: shows the form and saves each message in the MySQL table kss_messages.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function kssc_contact_form() {
	$out = '';
	if ( isset( $_GET['kssc_sent'] ) ) {
		$code = sanitize_key( wp_unslash( $_GET['kssc_sent'] ) );
		if ( '1' === $code ) {
			$out .= '<div class="kssc-notice ok">Thank you! Your message has been sent. We will reply soon.</div>';
		} elseif ( 'wait' === $code ) {
			$out .= '<div class="kssc-notice err">Please wait a few seconds before sending another message.</div>';
		} else {
			$out .= '<div class="kssc-notice err">Please fill in your name, a valid email, a subject and your message.</div>';
		}
	}
	$out .= '<form class="kssc-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	$out .= '<input type="hidden" name="action" value="kssc_contact">';
	$out .= wp_nonce_field( 'kssc_contact', 'kssc_nonce', true, false );
	$out .= '<input type="hidden" name="kssc_back" value="' . esc_url( get_permalink() ) . '">';
	$out .= '<div class="kssc-hp"><label>Leave this empty <input type="text" name="kssc_website" tabindex="-1" autocomplete="off"></label></div>';
	$out .= '<div class="row"><div><label for="kssc_name">Your name *</label><input type="text" id="kssc_name" name="kssc_name" required></div>';
	$out .= '<div><label for="kssc_email">Email *</label><input type="email" id="kssc_email" name="kssc_email" required></div></div>';
	$out .= '<div class="row"><div><label for="kssc_phone">Phone</label><input type="tel" id="kssc_phone" name="kssc_phone"></div>';
	$out .= '<div><label for="kssc_subject">Subject *</label><input type="text" id="kssc_subject" name="kssc_subject" required></div></div>';
	$out .= '<label for="kssc_message">Message *</label><textarea id="kssc_message" name="kssc_message" rows="6" required></textarea>';
	$out .= '<button type="submit" class="btn btn-primary">Send Message</button></form>';
	return $out;
}
add_shortcode( 'kahinju_contact_form', 'kssc_contact_form' );

function kssc_handle_contact() {
	$back = isset( $_POST['kssc_back'] ) ? esc_url_raw( wp_unslash( $_POST['kssc_back'] ) ) : home_url( '/' );
	$back = wp_validate_redirect( $back, home_url( '/' ) );
	$go   = function ( $code ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'kssc_sent', $code, $back ) . '#contact-form' );
		exit;
	};

	if ( ! isset( $_POST['kssc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kssc_nonce'] ) ), 'kssc_contact' ) ) {
		$go( 'bad' );
	}
	if ( ! empty( $_POST['kssc_website'] ) ) { $go( '1' ); } // Spam bot filled the hidden field: pretend success.

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	$key = 'kssc_rl_' . md5( $ip );
	if ( get_transient( $key ) ) { $go( 'wait' ); }

	$name    = isset( $_POST['kssc_name'] ) ? sanitize_text_field( wp_unslash( $_POST['kssc_name'] ) ) : '';
	$email   = isset( $_POST['kssc_email'] ) ? sanitize_email( wp_unslash( $_POST['kssc_email'] ) ) : '';
	$phone   = isset( $_POST['kssc_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['kssc_phone'] ) ) : '';
	$subject = isset( $_POST['kssc_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['kssc_subject'] ) ) : '';
	$message = isset( $_POST['kssc_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['kssc_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $subject || '' === $message ) { $go( 'bad' ); }

	global $wpdb;
	$ok = $wpdb->insert(
		$wpdb->prefix . 'kss_messages',
		array(
			'name'       => $name,
			'email'      => $email,
			'phone'      => $phone,
			'subject'    => $subject,
			'message'    => $message,
			'is_read'    => 0,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
	);
	if ( false === $ok ) { $go( 'bad' ); }

	set_transient( $key, 1, 20 );

	// Also email the school (may do nothing on a local PC without a mail server).
	$to = get_option( 'kss_email', '' );
	if ( ! is_email( $to ) ) { $to = get_option( 'admin_email' ); }
	@wp_mail( $to, 'Website message: ' . $subject, "From: $name <$email>\nPhone: $phone\n\n$message", array( 'Reply-To: ' . $email ) );

	$go( '1' );
}
add_action( 'admin_post_nopriv_kssc_contact', 'kssc_handle_contact' );
add_action( 'admin_post_kssc_contact', 'kssc_handle_contact' );
