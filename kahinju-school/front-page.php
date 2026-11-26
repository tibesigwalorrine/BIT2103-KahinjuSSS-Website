<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$has_core    = function_exists( 'kssc_get_gallery' );
$departments = function_exists( 'kssc_get_departments' ) ? kssc_get_departments() : array();
$gallery     = $has_core ? kssc_get_gallery( 6, 'image' ) : array();
$slides      = $has_core ? kssc_get_gallery( 6, 'slide' ) : array();
$video       = $has_core ? kssc_get_gallery( 1, 'video' ) : array();
$events      = function_exists( 'kssc_get_events' ) ? kssc_get_events( 4, true ) : array();
$leaders     = function_exists( 'kssc_get_leaders' ) ? kssc_get_leaders() : array();
$activities  = function_exists( 'kssc_get_activities' ) ? kssc_get_activities( 3 ) : array();
$pool        = function_exists( 'kssc_student_photos' ) ? kssc_student_photos( 12 ) : array();
if ( ! $slides ) {
	$slides = array( array( 'image_url' => kss_img( 'hero.jpg' ), 'title' => kss_mod( 'hero_title' ), 'description' => kss_mod( 'hero_text' ) ) );
}
$news  = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );
$featured = array();
$cards    = array();
foreach ( $leaders as $l ) {
	if ( 'featured' === $l['layout'] ) { $featured[] = $l; } else { $cards[] = $l; }
}
?>

<section class="hero" id="hero">
	<div class="slides">
		<?php foreach ( $slides as $i => $s ) : ?>
			<div class="slide<?php echo 0 === $i ? ' active' : ''; ?>" style="background-image:url('<?php echo esc_url( kss_media_url( $s['image_url'] ) ); ?>')"></div>
		<?php endforeach; ?>
	</div>
	<div class="hero-overlay"></div>
	<div class="wrap hero-inner">
		<span class="eyebrow"><?php echo esc_html( kss_mod( 'hero_eyebrow' ) ); ?></span>
		<div class="hero-copies">
			<?php foreach ( $slides as $i => $s ) : $tag = ( 0 === $i ) ? 'h1' : 'h2'; ?>
				<div class="hero-copy<?php echo 0 === $i ? ' active' : ''; ?>">
					<<?php echo $tag; // phpcs:ignore -- fixed h1 / h2. ?> class="hero-title"><?php echo esc_html( $s['title'] ); ?></<?php echo $tag; // phpcs:ignore -- fixed h1 / h2. ?>>
					<p class="hero-text"><?php echo esc_html( ! empty( $s['description'] ) ? $s['description'] : kss_mod( 'hero_text' ) ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="hero-actions">
			<a class="btn btn-gold" href="<?php echo esc_url( kss_page_url( 'apply-online' ) ); ?>"><?php echo esc_html( kss_mod( 'btn_apply' ) ); ?></a>
			<a class="btn btn-outline" href="<?php echo esc_url( kss_page_url( 'about-us' ) ); ?>"><?php echo esc_html( kss_mod( 'btn_about' ) ); ?></a>
		</div>
	</div>
	<?php if ( count( $slides ) > 1 ) : ?>
		<button class="hero-arrow prev" aria-label="Previous slide">&#10094;</button>
		<button class="hero-arrow next" aria-label="Next slide">&#10095;</button>
		<div class="hero-dots" aria-hidden="true"></div>
	<?php endif; ?>
</section>

<section class="strip">
	<div class="wrap strip-grid">
		<?php for ( $n = 1; $n <= 3; $n++ ) : ?>
			<div class="strip-card">
				<span class="strip-icon"><?php echo esc_html( kss_mod( 'box' . $n . '_icon' ) ); ?></span>
				<div><h3><?php echo esc_html( kss_mod( 'box' . $n . '_title' ) ); ?></h3><p><?php echo esc_html( kss_mod( 'box' . $n . '_text' ) ); ?></p></div>
			</div>
		<?php endfor; ?>
	</div>
</section>

<section class="section">
	<div class="wrap split">
		<div class="split-img"><img src="<?php echo esc_url( kss_mod_image( 'welcome_image', 'about.jpg' ) ); ?>" alt="Students learning at school" loading="lazy"></div>
		<div class="split-text">
			<span class="kicker"><?php echo esc_html( kss_mod( 'welcome_kicker' ) ); ?></span>
			<h2><?php echo esc_html( kss_mod( 'welcome_title' ) ); ?></h2>
			<p><?php echo esc_html( kss_mod( 'welcome_p1' ) ); ?></p>
			<p><?php echo esc_html( kss_mod( 'welcome_p2' ) ); ?></p>
			<a class="btn btn-primary" href="<?php echo esc_url( kss_page_url( 'about-us' ) ); ?>"><?php echo esc_html( kss_mod( 'welcome_btn' ) ); ?></a>
		</div>
	</div>
</section>

<?php if ( $leaders ) : ?>
<section class="section section-cream leaders">
	<div class="wrap">
		<div class="section-head accent">
			<span class="kicker"><?php echo esc_html( kss_mod( 'lead_kicker' ) ); ?></span>
			<h2><?php echo esc_html( kss_mod( 'lead_title' ) ); ?></h2>
		</div>
		<?php foreach ( $featured as $l ) : ?>
			<article class="leader-featured">
				<div class="leader-photo"><img src="<?php echo esc_url( kss_pick_photo( $l['photo'], array(), 0 ) ); ?>" alt="<?php echo esc_attr( $l['name'] ? $l['name'] : $l['role'] ); ?>" loading="lazy"></div>
				<div class="leader-body">
					<span class="quote-mark" aria-hidden="true">&ldquo;</span>
					<div class="leader-text"><?php echo kss_paragraphs( $l['message'] ); // phpcs:ignore -- escaped in function. ?></div>
					<div class="leader-sign">
						<?php if ( $l['name'] ) : ?><strong><?php echo esc_html( $l['name'] ); ?></strong><?php endif; ?>
						<span><?php echo esc_html( $l['role'] ); ?></span>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
		<?php if ( $cards ) : ?>
			<div class="leader-cards">
				<?php foreach ( $cards as $l ) : ?>
					<article class="leader-card">
						<div class="leader-photo"><img src="<?php echo esc_url( kss_pick_photo( $l['photo'], array(), 0 ) ); ?>" alt="<?php echo esc_attr( $l['name'] ? $l['name'] : $l['role'] ); ?>" loading="lazy"></div>
						<div class="leader-body">
							<h3><?php echo esc_html( $l['role'] ); ?></h3>
							<?php if ( $l['name'] ) : ?><p class="leader-name"><?php echo esc_html( $l['name'] ); ?></p><?php endif; ?>
							<div class="leader-text"><?php echo kss_paragraphs( $l['message'] ); // phpcs:ignore -- escaped in function. ?></div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php if ( $departments ) : ?>
<section class="section">
	<div class="wrap">
		<div class="section-head">
			<span class="kicker"><?php echo esc_html( kss_mod( 'dept_kicker' ) ); ?></span>
			<h2><?php echo esc_html( kss_mod( 'dept_title' ) ); ?></h2>
			<p><?php echo esc_html( kss_mod( 'dept_text' ) ); ?></p>
		</div>
		<div class="cards-grid dept-grid">
			<?php foreach ( $departments as $i => $d ) : ?>
				<a class="dept-card" href="<?php echo esc_url( kss_page_url( 'academic-units' ) . '#dept-' . (int) $d['id'] ); ?>">
					<span class="dept-photo"><img src="<?php echo esc_url( kss_pick_photo( $d['photo'], $pool, $i ) ); ?>" alt="" loading="lazy"></span>
					<span class="dept-info">
						<h3><?php echo esc_html( $d['name'] ); ?></h3>
						<p><?php echo esc_html( wp_trim_words( (string) $d['description'], 18 ) ); ?></p>
						<span class="dept-count"><?php echo (int) $d['unit_count']; ?> subjects &rarr;</span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $gallery ) : ?>
<section class="section section-cream">
	<div class="wrap">
		<div class="section-head">
			<span class="kicker"><?php echo esc_html( kss_mod( 'gal_kicker' ) ); ?></span>
			<h2><?php echo esc_html( kss_mod( 'gal_title' ) ); ?></h2>
		</div>
		<div class="gallery-preview">
			<?php foreach ( $gallery as $g ) : ?>
				<a href="<?php echo esc_url( kss_page_url( 'gallery' ) ); ?>"><img src="<?php echo esc_url( kss_media_url( $g['image_url'] ) ); ?>" alt="<?php echo esc_attr( $g['title'] ); ?>" loading="lazy"></a>
			<?php endforeach; ?>
		</div>
		<p class="center"><a class="btn btn-primary" href="<?php echo esc_url( kss_page_url( 'gallery' ) ); ?>"><?php echo esc_html( kss_mod( 'gal_btn' ) ); ?></a></p>
	</div>
</section>
<?php endif; ?>

<?php if ( $video ) : $v = $video[0]; ?>
<section class="section">
	<div class="wrap narrow">
		<div class="section-head">
			<span class="kicker"><?php echo esc_html( kss_mod( 'vid_kicker' ) ); ?></span>
			<h2><?php echo esc_html( kss_mod( 'vid_title' ) ); ?></h2>
			<p><?php echo esc_html( $v['title'] ); ?></p>
		</div>
		<div class="video-box">
			<video controls preload="metadata" playsinline src="<?php echo esc_url( kss_media_url( $v['image_url'] ) ); ?>#t=0.5"></video>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section section-cream">
	<div class="wrap news-events">
		<div>
			<div class="section-head left">
				<span class="kicker"><?php echo esc_html( kss_mod( 'news_kicker' ) ); ?></span>
				<h2><?php echo esc_html( kss_mod( 'news_title' ) ); ?></h2>
			</div>
			<div class="news-list">
				<?php if ( $news->have_posts() ) : while ( $news->have_posts() ) : $news->the_post(); ?>
					<article class="news-card">
						<a class="news-thumb" href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( kss_post_image( get_the_ID() ) ); ?>" alt="" loading="lazy"></a>
						<div>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); else : ?>
					<p>No news yet. Please check back soon.</p>
				<?php endif; ?>
			</div>
		</div>
		<aside>
			<div class="section-head left">
				<span class="kicker"><?php echo esc_html( kss_mod( 'ev_kicker' ) ); ?></span>
				<h2><?php echo esc_html( kss_mod( 'ev_title' ) ); ?></h2>
			</div>
			<?php if ( $events ) : ?>
				<ul class="event-list">
					<?php foreach ( $events as $e ) : $ts = strtotime( $e['event_date'] ); ?>
						<li>
							<div class="date-badge"><strong><?php echo esc_html( gmdate( 'd', $ts ) ); ?></strong><span><?php echo esc_html( gmdate( 'M', $ts ) ); ?></span></div>
							<div><h4><?php echo esc_html( $e['title'] ); ?></h4><p><?php echo esc_html( $e['venue'] ); ?></p></div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p>No upcoming events at the moment.</p>
			<?php endif; ?>
			<a class="btn btn-primary" href="<?php echo esc_url( kss_page_url( 'news-events' ) ); ?>">All News &amp; Events</a>
		</aside>
	</div>
</section>

<?php if ( $activities ) : ?>
<section class="section">
	<div class="wrap">
		<div class="section-head accent">
			<span class="kicker"><?php echo esc_html( kss_mod( 'act_kicker' ) ); ?></span>
			<h2><?php echo esc_html( kss_mod( 'act_title' ) ); ?></h2>
			<p><?php echo esc_html( kss_mod( 'act_text' ) ); ?></p>
		</div>
		<div class="act-grid">
			<?php foreach ( $activities as $a ) : ?>
				<article class="act-card">
					<img src="<?php echo esc_url( kss_media_url( $a['photo'] ) ); ?>" alt="<?php echo esc_attr( $a['title'] ); ?>" loading="lazy">
					<div class="act-body">
						<h3><?php echo esc_html( $a['title'] ); ?></h3>
						<p><?php echo esc_html( $a['description'] ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="cta">
	<div class="cta-bg" style="background-image:url('<?php echo esc_url( kss_mod_image( 'cta_image', 'cta.jpg', 'full' ) ); ?>')"></div>
	<div class="cta-overlay"></div>
	<div class="wrap cta-inner">
		<h2><?php echo esc_html( kss_mod( 'cta_title' ) ); ?></h2>
		<p><?php echo esc_html( kss_mod( 'cta_text' ) ); ?></p>
		<div class="hero-actions cta-actions">
			<a class="btn btn-gold btn-pulse" href="<?php echo esc_url( kss_page_url( 'apply-online' ) ); ?>"><?php echo esc_html( kss_mod( 'btn_apply' ) ); ?></a>
			<a class="btn btn-outline" href="<?php echo esc_url( kss_page_url( 'contact-us' ) ); ?>"><?php echo esc_html( kss_mod( 'cta_btn' ) ); ?></a>
		</div>
	</div>
</section>

<section class="stats">
	<div class="wrap">
		<div class="section-head light">
			<span class="kicker"><?php echo esc_html( kss_mod( 'stats_kicker' ) ); ?></span>
			<h2><?php echo esc_html( kss_mod( 'stats_title' ) ); ?></h2>
		</div>
		<div class="stats-grid">
			<?php for ( $n = 1; $n <= 3; $n++ ) : $num = (int) preg_replace( '/\D+/', '', kss_mod( 'stat' . $n . '_num' ) ); ?>
				<div class="stat">
					<span class="stat-icon"><?php echo esc_html( kss_mod( 'stat' . $n . '_icon' ) ); ?></span>
					<strong class="count" data-target="<?php echo (int) $num; ?>"><?php echo esc_html( number_format_i18n( $num ) ); ?></strong>
					<span class="stat-label"><?php echo esc_html( kss_mod( 'stat' . $n . '_label' ) ); ?></span>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
