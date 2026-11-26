<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
</main>

<footer class="site-footer">
	<div class="wrap footer-grid">
		<div class="footer-about">
			<div class="footer-logo"><img src="<?php echo esc_url( kss_logo_url() ); ?>" alt="School logo"></div>
			<h3><?php echo esc_html( kss_mod( 'hero_title' ) ); ?></h3>
			<p class="motto"><?php echo esc_html( kss_mod( 'hero_motto' ) ); ?></p>
			<p><?php echo esc_html( kss_mod( 'footer_text' ) ); ?></p>
		</div>
		<div>
			<h4>Quick Links</h4>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
				<li><a href="<?php echo esc_url( kss_page_url( 'about-us' ) ); ?>">About Us</a></li>
				<li><a href="<?php echo esc_url( kss_page_url( 'academic-units' ) ); ?>">Academic Units</a></li>
				<li><a href="<?php echo esc_url( kss_page_url( 'co-curricular-activities' ) ); ?>">Co-curricular Activities</a></li>
				<li><a href="<?php echo esc_url( kss_page_url( 'gallery' ) ); ?>">Gallery</a></li>
				<li><a href="<?php echo esc_url( kss_page_url( 'news-events' ) ); ?>">News and Events</a></li>
				<li><a href="<?php echo esc_url( kss_page_url( 'contact-us' ) ); ?>">Contact Us</a></li>
				
				<li><a href="<?php echo esc_url( wp_login_url() ); ?>">Staff Login</a></li>

			</ul>
		</div>
		<div>
			<h4>Contact</h4>
			<ul class="footer-contact">
				<li>&#9906; <?php echo esc_html( kss_opt( 'address' ) ); ?></li>
				<li>&#9742; <?php echo esc_html( kss_opt( 'phone' ) ); ?> (Head Teacher)</li>
				<li>&#9993; <a href="mailto:<?php echo esc_attr( kss_opt( 'email' ) ); ?>"><?php echo esc_html( kss_opt( 'email' ) ); ?></a></li>
				<?php if ( kss_opt( 'email2' ) ) : ?><li>&#9993; <a href="mailto:<?php echo esc_attr( kss_opt( 'email2' ) ); ?>"><?php echo esc_html( kss_opt( 'email2' ) ); ?></a></li><?php endif; ?>
				<li>&#9719; <?php echo esc_html( kss_opt( 'hours' ) ); ?></li>
			</ul>
		</div>
		<div>
			<h4>Join Us</h4>
			<p>Applications are made online. Fill in the form and our office will reply to your phone.</p>
			<a class="btn btn-gold" href="<?php echo esc_url( kss_page_url( 'apply-online' ) ); ?>"><?php echo esc_html( kss_mod( 'btn_apply' ) ); ?></a>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="wrap">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( kss_mod( 'footer_copy' ) ); ?>. All rights reserved.</span>
			<span><?php echo esc_html( kss_mod( 'hero_motto' ) ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

<p style="margin-top: 8px;"><a href="<?php echo esc_url( wp_login_url() ); ?>">Staff Login</a></p>

