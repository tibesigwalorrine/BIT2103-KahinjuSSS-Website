<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
kss_banner( 'Page not found' );
?>
<section class="section"><div class="wrap narrow center">
	<h2>Sorry, we could not find that page.</h2>
	<p><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Go to Home</a></p>
</div></section>
<?php get_footer(); ?>
