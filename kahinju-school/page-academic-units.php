<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$rows = function_exists( 'kssc_get_units' ) ? kssc_get_units() : array();
$pool = function_exists( 'kssc_student_photos' ) ? kssc_student_photos( 12 ) : array();
$k    = 0;
$grouped = array();
foreach ( $rows as $r ) {
	$k = (int) $r['department_id'];
	if ( ! isset( $grouped[ $k ] ) ) {
		$grouped[ $k ] = array( 'name' => $r['department_name'] ? $r['department_name'] : 'Other', 'desc' => $r['department_description'], 'photo' => $r['department_photo'], 'units' => array() );
	}
	$grouped[ $k ]['units'][] = $r;
}
while ( have_posts() ) : the_post();
	kss_banner( get_the_title(), 'Departments and subjects at Kahinju S.S.S' );
	?>
	<section class="section">
		<div class="wrap">
			<div class="entry narrow center"><?php the_content(); ?></div>
			<?php if ( $grouped ) : foreach ( $grouped as $id => $g ) : ?>
				<div class="dept-block" id="dept-<?php echo (int) $id; ?>">
					<div class="dept-hero" style="background-image:url('<?php echo esc_url( kss_pick_photo( $g['photo'], $pool, (int) $id ) ); ?>')">
						<div class="dept-hero-text">
							<h2><?php echo esc_html( $g['name'] ); ?></h2>
							<?php if ( $g['desc'] ) : ?><p><?php echo esc_html( $g['desc'] ); ?></p><?php endif; ?>
						</div>
					</div>
					<div class="unit-grid">
						<?php foreach ( $g['units'] as $u ) : ?>
							<div class="unit-card">
								<div class="unit-photo"><img src="<?php echo esc_url( kss_pick_photo( $u['photo'], $pool, $k++ ) ); ?>" alt="<?php echo esc_attr( $u['name'] ); ?>" loading="lazy"><span class="pill"><?php echo esc_html( $u['level'] ); ?></span></div>
								<div class="unit-body">
									<h3><?php echo esc_html( $u['name'] ); ?></h3>
									<?php if ( $u['description'] ) : ?><p><?php echo esc_html( $u['description'] ); ?></p><?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; else : ?>
				<p class="center">Academic units will appear here once the Kahinju Core plugin is active.</p>
			<?php endif; ?>
		</div>
	</section>
<?php endwhile;
get_footer();
