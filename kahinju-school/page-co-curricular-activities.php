<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$clubs = function_exists( 'kssc_get_clubs' ) ? kssc_get_clubs() : array();
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), 'Clubs and activities beyond the classroom' );
	?>
	<section class="section">
		<div class="wrap">
			<div class="entry narrow center"><?php the_content(); ?></div>
			<?php if ( $clubs ) : ?>
				<div class="club-list">
					<?php foreach ( $clubs as $i => $c ) :
						$photos = array_filter( array( $c['photo'], $c['photo2'], $c['photo3'] ) );
						$first  = $photos ? array_shift( $photos ) : '';
						?>
						<article class="club<?php echo ( $i % 2 ) ? ' flip' : ''; ?>" id="club-<?php echo (int) $c['id']; ?>">
							<div class="club-media">
								<?php if ( $first ) : ?>
									<div class="club-main"><img src="<?php echo esc_url( kss_media_url( $first ) ); ?>" alt="<?php echo esc_attr( $c['name'] ); ?>" loading="lazy"></div>
									<?php if ( $photos ) : ?>
										<div class="club-thumbs">
											<?php foreach ( $photos as $ph ) : ?><img src="<?php echo esc_url( kss_media_url( $ph ) ); ?>" alt="<?php echo esc_attr( $c['name'] ); ?>" loading="lazy"><?php endforeach; ?>
										</div>
									<?php endif; ?>
								<?php else : ?>
									<div class="club-main club-placeholder"><span><?php echo esc_html( $c['icon'] ? $c['icon'] : '⭐' ); ?></span></div>
								<?php endif; ?>
							</div>
							<div class="club-body">
								<span class="club-icon"><?php echo esc_html( $c['icon'] ? $c['icon'] : '⭐' ); ?></span>
								<h2><?php echo esc_html( $c['name'] ); ?></h2>
								<p><?php echo esc_html( $c['description'] ); ?></p>
								<?php if ( $c['patron_photo'] || $c['patron_name'] ) : ?>
									<div class="patron">
										<?php if ( $c['patron_photo'] ) : ?><img src="<?php echo esc_url( kss_media_url( $c['patron_photo'] ) ); ?>" alt="Club patron" loading="lazy"><?php endif; ?>
										<div><small>Club Patron</small><strong><?php echo esc_html( $c['patron_name'] ); ?></strong></div>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="center">Clubs will appear here once the Kahinju Core plugin is active.</p>
			<?php endif; ?>
		</div>
	</section>
<?php endwhile;
get_footer();
