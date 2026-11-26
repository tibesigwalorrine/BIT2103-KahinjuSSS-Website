<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), 'We would love to hear from you' );
	?>
	<section class="section">
		<div class="wrap contact-grid">
			<div>
				<div class="entry"><?php the_content(); ?></div>
				<div class="contact-cards">
					<div class="contact-card"><span>&#9906;</span><div><h4>Address</h4><p><?php echo esc_html( kss_opt( 'address' ) ); ?></p></div></div>
					<div class="contact-card"><span>&#9742;</span><div><h4>Phone (Head Teacher)</h4><p><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', kss_opt( 'phone' ) ) ); ?>"><?php echo esc_html( kss_opt( 'phone' ) ); ?></a></p></div></div>
					<div class="contact-card"><span>&#9993;</span><div><h4>Email</h4><p><a href="mailto:<?php echo esc_attr( kss_opt( 'email' ) ); ?>"><?php echo esc_html( kss_opt( 'email' ) ); ?></a><?php if ( kss_opt( 'email2' ) ) : ?><br><a href="mailto:<?php echo esc_attr( kss_opt( 'email2' ) ); ?>"><?php echo esc_html( kss_opt( 'email2' ) ); ?></a><?php endif; ?></p></div></div>
					<div class="contact-card"><span>&#9719;</span><div><h4>Opening hours</h4><p><?php echo esc_html( kss_opt( 'hours' ) ); ?></p></div></div>
				</div>
				<p><a class="btn btn-primary" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&amp;query=Kahinju+Senior+Secondary+School+Fort+Portal">Open in Google Maps</a></p>
			</div>
			<div class="form-box" id="contact-form">
				<h2>Send us a message</h2>
				<?php
				if ( function_exists( 'kssc_contact_form' ) ) {
					echo kssc_contact_form(); // phpcs:ignore -- escaped inside the plugin.
				} else {
					echo '<p>The contact form needs the Kahinju Core plugin to be active.</p>';
				}
				?>
			</div>
		</div>
	</section>
<?php endwhile;
get_footer();
