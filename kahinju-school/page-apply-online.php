<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), kss_mod( 'apply_heading' ) );
	?>
	<section class="section">
		<div class="wrap apply-grid">
			<div class="apply-info">
				<div class="apply-photo"><img src="<?php echo esc_url( kss_mod_image( 'apply_image', 'apply-side.jpg' ) ); ?>" alt="Learners at school" loading="lazy"></div>
				<div class="entry"><?php the_content(); ?></div>
				<ol class="steps">
					<?php for ( $n = 1; $n <= 3; $n++ ) : ?>
						<li><span class="step-no"><?php echo (int) $n; ?></span><div><h4><?php echo esc_html( kss_mod( 'step' . $n . '_title' ) ); ?></h4><p><?php echo esc_html( kss_mod( 'step' . $n . '_text' ) ); ?></p></div></li>
					<?php endfor; ?>
				</ol>
			</div>
			<div class="form-box" id="apply-form">
				<h2>Application form</h2>
				<?php
				if ( function_exists( 'kssc_apply_form' ) ) {
					echo kssc_apply_form(); // phpcs:ignore -- escaped inside the plugin.
				} else {
					echo '<p>The application form needs the Kahinju Core plugin to be active.</p>';
				}
				?>
			</div>
		</div>
	</section>
<?php endwhile;
get_footer();
