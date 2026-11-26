<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$facilities = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) kss_mod( 'fac_list' ) ) ) );
$fac_icons  = array( '💻', '🏫', '🔬', '🎵', '🎨', '📚', '🍽️', '🏢' );
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), 'Who we are and what we stand for' );
	?>
	<section class="section">
		<div class="wrap split">
			<div class="split-text entry"><?php the_content(); ?></div>
			<div class="split-img"><img src="<?php echo esc_url( kss_img( 'campus.jpg' ) ); ?>" alt="The school campus" loading="lazy"></div>
		</div>
		<div class="wrap">
			<div class="facts-grid">
				<?php for ( $n = 1; $n <= 5; $n++ ) : ?>
					<div class="fact"><span><?php echo esc_html( kss_mod( 'fact' . $n . '_icon' ) ); ?></span><h4><?php echo esc_html( kss_mod( 'fact' . $n . '_title' ) ); ?></h4><p><?php echo esc_html( kss_mod( 'fact' . $n . '_text' ) ); ?></p></div>
				<?php endfor; ?>
			</div>
		</div>
	</section>

	<section class="section section-cream">
		<div class="wrap mv-grid">
			<div class="mv-card mission"><span class="mv-icon">&#127919;</span><h3><?php echo esc_html( kss_mod( 'mission_title' ) ); ?></h3><p><?php echo esc_html( kss_mod( 'mission_text' ) ); ?></p></div>
			<div class="mv-card vision"><span class="mv-icon">&#128065;</span><h3><?php echo esc_html( kss_mod( 'vision_title' ) ); ?></h3><p><?php echo esc_html( kss_mod( 'vision_text' ) ); ?></p></div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<div class="section-head accent"><span class="kicker"><?php echo esc_html( kss_mod( 'values_kicker' ) ); ?></span><h2><?php echo esc_html( kss_mod( 'values_title' ) ); ?></h2></div>
			<div class="values-grid">
				<?php for ( $n = 1; $n <= 4; $n++ ) : ?>
					<div class="value-card"><span><?php echo esc_html( kss_mod( 'val' . $n . '_icon' ) ); ?></span><h3><?php echo esc_html( kss_mod( 'val' . $n . '_title' ) ); ?></h3><p><?php echo esc_html( kss_mod( 'val' . $n . '_text' ) ); ?></p></div>
				<?php endfor; ?>
			</div>
		</div>
	</section>

	<section class="section section-cream">
		<div class="wrap">
			<div class="section-head accent"><span class="kicker"><?php echo esc_html( kss_mod( 'fac_kicker' ) ); ?></span><h2><?php echo esc_html( kss_mod( 'fac_title' ) ); ?></h2></div>
			<div class="fac-grid">
				<?php foreach ( array_values( $facilities ) as $i => $f ) : ?>
					<div class="fac"><span><?php echo esc_html( isset( $fac_icons[ $i ] ) ? $fac_icons[ $i ] : '✅' ); ?></span><?php echo esc_html( $f ); ?></div>
				<?php endforeach; ?>
			</div>
			<p class="center find-us">&#9906; <?php echo esc_html( kss_opt( 'address' ) ); ?> &nbsp;&middot;&nbsp; &#9993; <?php echo esc_html( kss_opt( 'email' ) ); ?></p>
		</div>
	</section>
<?php endwhile;
get_footer();
