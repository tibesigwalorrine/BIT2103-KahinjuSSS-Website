<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
kss_banner( 'News', 'Latest from Kahinju Senior Secondary School' );
?>
<section class="section"><div class="wrap narrow">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<article class="news-card big">
		<a class="news-thumb" href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( kss_post_image( get_the_ID() ) ); ?>" alt="" loading="lazy"></a>
		<div>
			<time><?php echo esc_html( get_the_date() ); ?></time>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
		</div>
	</article>
<?php endwhile; the_posts_pagination(); else : ?>
	<p>Nothing found.</p>
<?php endif; ?>
</div></section>
<?php get_footer(); ?>
