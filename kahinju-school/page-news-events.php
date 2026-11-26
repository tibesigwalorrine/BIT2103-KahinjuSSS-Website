<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$paged  = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$news   = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 6, 'paged' => $paged, 'ignore_sticky_posts' => true ) );
$events = function_exists( 'kssc_get_events' ) ? kssc_get_events( 10, true ) : array();
$past   = function_exists( 'kssc_get_events' ) ? kssc_get_events( 5, false ) : array();
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), 'Stay up to date with Kahinju S.S.S' );
	?>
	<section class="section">
		<div class="wrap news-events">
			<div>
				<div class="entry"><?php the_content(); ?></div>
				<div class="news-list">
					<?php if ( $news->have_posts() ) : while ( $news->have_posts() ) : $news->the_post(); ?>
						<article class="news-card big">
							<a class="news-thumb" href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( kss_post_image( get_the_ID() ) ); ?>" alt="" loading="lazy"></a>
							<div>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
								<a class="more" href="<?php the_permalink(); ?>">Read more &rarr;</a>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); else : ?>
						<p>No news yet.</p>
					<?php endif; ?>
				</div>
				<?php
				echo '<div class="pager">' . wp_kses_post( (string) paginate_links( array(
					'base'    => trailingslashit( get_permalink() ) . 'page/%#%/',
					'format'  => '',
					'current' => $paged,
					'total'   => $news->max_num_pages,
				) ) ) . '</div>';
				?>
			</div>
			<aside>
				<div class="section-head left"><span class="kicker">Events</span><h2>Upcoming events</h2></div>
				<?php if ( $events ) : ?>
					<ul class="event-list">
						<?php foreach ( $events as $e ) : $ts = strtotime( $e['event_date'] ); ?>
							<li>
								<div class="date-badge"><strong><?php echo esc_html( gmdate( 'd', $ts ) ); ?></strong><span><?php echo esc_html( gmdate( 'M Y', $ts ) ); ?></span></div>
								<div><h4><?php echo esc_html( $e['title'] ); ?></h4><p><?php echo esc_html( $e['venue'] ); ?></p><?php if ( $e['description'] ) : ?><p class="small"><?php echo esc_html( $e['description'] ); ?></p><?php endif; ?></div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p>No upcoming events at the moment.</p>
				<?php endif; ?>
			</aside>
		</div>
	</section>
<?php endwhile;
get_footer();
