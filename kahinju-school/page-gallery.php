<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$items = function_exists( 'kssc_get_gallery' ) ? kssc_get_gallery() : array();
$cats  = array();
foreach ( $items as $i ) { $cats[ $i['category'] ] = true; }
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), 'Moments from life at Kahinju S.S.S' );
	?>
	<section class="section">
		<div class="wrap">
			<div class="entry narrow center"><?php the_content(); ?></div>
			<?php if ( $items ) : ?>
				<div class="filters">
					<button class="filter active" data-filter="all">All</button>
					<?php foreach ( array_keys( $cats ) as $c ) : ?>
						<button class="filter" data-filter="<?php echo esc_attr( sanitize_title( $c ) ); ?>"><?php echo esc_html( $c ); ?></button>
					<?php endforeach; ?>
				</div>
				<div class="gallery-grid">
					<?php foreach ( $items as $i ) : $url = kss_media_url( $i['image_url'] ); $is_video = ( isset( $i['media_type'] ) && 'video' === $i['media_type'] ); ?>
						<figure class="g-item" data-cat="<?php echo esc_attr( sanitize_title( $i['category'] ) ); ?>">
							<?php if ( $is_video ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" class="g-link" data-type="video"><video src="<?php echo esc_url( $url ); ?>#t=0.5" preload="metadata" muted></video><span class="play">&#9654;</span></a>
							<?php else : ?>
								<a href="<?php echo esc_url( $url ); ?>" class="g-link" data-type="image"><img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $i['title'] ); ?>" loading="lazy"></a>
							<?php endif; ?>
							<figcaption><?php echo esc_html( $i['title'] ); ?></figcaption>
						</figure>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="center">Photos will appear here once the Kahinju Core plugin is active.</p>
			<?php endif; ?>
		</div>
	</section>
	<div class="lightbox" id="lightbox" hidden><button class="lb-close" aria-label="Close">&times;</button><img src="" alt=""><video controls playsinline></video></div>
<?php endwhile;
get_footer();
