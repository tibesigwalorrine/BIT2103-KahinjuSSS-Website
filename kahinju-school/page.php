<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	kss_banner( get_the_title() );
	?>
	<section class="section"><div class="wrap narrow entry"><?php the_content(); ?></div></section>
<?php endwhile;
get_footer();
