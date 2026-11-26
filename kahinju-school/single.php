<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), get_the_date() );
	?>
	<section class="section"><div class="wrap narrow entry">
		<img class="entry-img" src="<?php echo esc_url( kss_post_image( get_the_ID(), 'large' ) ); ?>" alt="">
		<?php the_content(); ?>
		<p><a class="btn btn-primary" href="<?php echo esc_url( kss_page_url( 'news-events' ) ); ?>">&larr; Back to News and Events</a></p>
	</div></section>
<?php endwhile;
get_footer();
