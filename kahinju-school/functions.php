<?php
/**
 * Kahinju School theme - setup, editable texts (Customizer), helpers and first-time content.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'KSS_VER', '1.2.0' );

/* ---------- Theme setup ---------- */
function kss_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array( 'primary' => 'Main Menu' ) );
	add_image_size( 'kss-card', 800, 520, true );
}
add_action( 'after_setup_theme', 'kss_setup' );

function kss_assets() {
	wp_enqueue_style( 'kss-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Merriweather:wght@700;900&display=swap', array(), null );
	wp_enqueue_style( 'kss-main', get_theme_file_uri( 'assets/css/main.css' ), array(), KSS_VER );
	wp_enqueue_script( 'kss-main', get_theme_file_uri( 'assets/js/main.js' ), array(), KSS_VER, true );
}
add_action( 'wp_enqueue_scripts', 'kss_assets' );

/* ---------- Every editable text, image and colour (shown in Appearance > Customize) ---------- */
function kss_fields() {
	return array(
		'kss_brand'    => array( 'title' => 'Header, footer and logo', 'fields' => array(
			'logo'        => array( 'School logo (replaces the default)', 'image', 0 ),
			'brand_name'  => array( 'School short name (header)', 'text', 'Kahinju S.S.S' ),
			'brand_tag'   => array( 'Motto under the name', 'text', 'Ready to Serve' ),
			'footer_text' => array( 'Footer description', 'textarea', 'A church-founded Government school in Fort Portal. Mixed, day and boarding, O-Level and A-Level.' ),
			'footer_copy' => array( 'Copyright name', 'text', 'Kahinju Senior Secondary School, Fort Portal' ),
		) ),
		'kss_hero'     => array( 'title' => 'Home: top slider (buttons and small line)', 'fields' => array(
			'hero_eyebrow' => array( 'Small line above the slider headline', 'text', 'Fort Portal, Uganda · Day & Boarding · O-Level & A-Level' ),
			'hero_title'   => array( 'School full name (footer and fallback)', 'text', 'Kahinju Senior Secondary School' ),
			'hero_motto'   => array( 'Motto line', 'text', '“Ready to Serve”' ),
			'hero_text'    => array( 'Slider text when a slide has none', 'textarea', 'A school where learners grow in knowledge, character and service to their community.' ),
			'btn_apply'    => array( 'Gold button text', 'text', 'Apply Online' ),
			'btn_about'    => array( 'Outline button text', 'text', 'About Our School' ),
		) ),
		'kss_boxes'    => array( 'title' => 'Home: three highlight boxes', 'fields' => array(
			'box1_icon'  => array( 'Box 1 icon (emoji)', 'text', '🏫' ),
			'box1_title' => array( 'Box 1 title', 'text', 'O-Level & A-Level' ),
			'box1_text'  => array( 'Box 1 text', 'text', 'Arts and Sciences, from Senior One to Senior Six.' ),
			'box2_icon'  => array( 'Box 2 icon (emoji)', 'text', '📍' ),
			'box2_title' => array( 'Box 2 title', 'text', 'Fort Portal' ),
			'box2_text'  => array( 'Box 2 text', 'text', 'Mixed, day and boarding school in western Uganda.' ),
			'box3_icon'  => array( 'Box 3 icon (emoji)', 'text', '🤝' ),
			'box3_title' => array( 'Box 3 title', 'text', 'Ready to Serve' ),
			'box3_text'  => array( 'Box 3 text', 'text', 'Our motto guides everything we teach and do.' ),
		) ),
		'kss_welcome'  => array( 'title' => 'Home: welcome section', 'fields' => array(
			'welcome_kicker' => array( 'Small heading', 'text', 'Welcome' ),
			'welcome_title'  => array( 'Heading', 'text', 'Learning with purpose, serving with pride' ),
			'welcome_p1'     => array( 'Paragraph 1', 'textarea', 'Kahinju Senior Secondary School is a church-founded Government school in Fort Portal. We are a mixed day and boarding school with O-Level and A-Level, offering both Arts and Sciences.' ),
			'welcome_p2'     => array( 'Paragraph 2', 'textarea', 'Our mission is to provide quality, relevant and meaningful education that produces independent-minded, healthy and self-sustaining citizens.' ),
			'welcome_btn'    => array( 'Button text', 'text', 'Read More About Us' ),
			'welcome_image'  => array( 'Photo', 'image', 0 ),
		) ),
		'kss_leaders'  => array( 'title' => 'Home: leader messages (heading)', 'fields' => array(
			'lead_kicker' => array( 'Small heading', 'text', 'Leadership' ),
			'lead_title'  => array( 'Heading', 'text', 'Messages from our leaders' ),
			'lead_text'   => array( 'Short text', 'text', 'The messages themselves are edited under School Data > Leader Messages.' ),
		) ),
		'kss_sections' => array( 'title' => 'Home: section headings', 'fields' => array(
			'dept_kicker' => array( 'Departments: small heading', 'text', 'Academic Units' ),
			'dept_title'  => array( 'Departments: heading', 'text', 'Where learning happens' ),
			'dept_text'   => array( 'Departments: text', 'text', 'Our subjects are taught in departments led by dedicated teachers.' ),
			'gal_kicker'  => array( 'Gallery: small heading', 'text', 'Gallery' ),
			'gal_title'   => array( 'Gallery: heading', 'text', 'Life at Kahinju' ),
			'gal_btn'     => array( 'Gallery: button text', 'text', 'View Full Gallery' ),
			'vid_kicker'  => array( 'Video: small heading', 'text', 'Watch' ),
			'vid_title'   => array( 'Video: heading', 'text', 'See Kahinju in action' ),
			'news_kicker' => array( 'News: small heading', 'text', 'News' ),
			'news_title'  => array( 'News: heading', 'text', 'Latest news' ),
			'ev_kicker'   => array( 'Events: small heading', 'text', 'Events' ),
			'ev_title'    => array( 'Events: heading', 'text', 'Upcoming events' ),
			'act_kicker'  => array( 'Recent activities: small heading', 'text', 'What is happening' ),
			'act_title'   => array( 'Recent activities: heading', 'text', 'Recent Activities' ),
			'act_text'    => array( 'Recent activities: text', 'text', 'A look at what our learners have been doing lately.' ),
		) ),
		'kss_cta'      => array( 'title' => 'Home: join us message', 'fields' => array(
			'cta_title' => array( 'Heading', 'text', 'Come and Join the Kahinju Family' ),
			'cta_text'  => array( 'Text', 'textarea', 'Give your child quality education, discipline and strong values in a church-founded school in Fort Portal. Day and boarding, O-Level and A-Level, Arts and Sciences. Places are open for new students.' ),
			'cta_btn'   => array( 'Second button text', 'text', 'Contact Us' ),
			'cta_image' => array( 'Background photo', 'image', 0 ),
		) ),
		'kss_stats'    => array( 'title' => 'Home: numbers above the footer', 'fields' => array(
			'stats_kicker' => array( 'Small heading', 'text', 'Our school at a glance' ),
			'stats_title'  => array( 'Heading', 'text', 'Kahinju S.S.S in numbers' ),
			'stat1_icon'   => array( 'Number 1 icon (emoji)', 'text', '🎓' ),
			'stat1_num'    => array( 'Number 1 (digits only)', 'text', '1420' ),
			'stat1_label'  => array( 'Number 1 label', 'text', 'Students' ),
			'stat2_icon'   => array( 'Number 2 icon (emoji)', 'text', '👩‍🏫' ),
			'stat2_num'    => array( 'Number 2 (digits only)', 'text', '78' ),
			'stat2_label'  => array( 'Number 2 label', 'text', 'Staff' ),
			'stat3_icon'   => array( 'Number 3 icon (emoji)', 'text', '🏫' ),
			'stat3_num'    => array( 'Number 3 (digits only)', 'text', '19' ),
			'stat3_label'  => array( 'Number 3 label', 'text', 'Classes / Streams' ),
		) ),
		'kss_about'    => array( 'title' => 'About page: facts, mission, vision, values, facilities', 'fields' => array(
			'fact1_icon'  => array( 'Fact 1 icon (emoji)', 'text', '⛪' ),
			'fact1_title' => array( 'Fact 1 title', 'text', 'Church-founded' ),
			'fact1_text'  => array( 'Fact 1 text', 'text', 'Built on Christian values.' ),
			'fact2_icon'  => array( 'Fact 2 icon (emoji)', 'text', '🏛️' ),
			'fact2_title' => array( 'Fact 2 title', 'text', 'Government school' ),
			'fact2_text'  => array( 'Fact 2 text', 'text', 'Serving the Fort Portal community.' ),
			'fact3_icon'  => array( 'Fact 3 icon (emoji)', 'text', '👫' ),
			'fact3_title' => array( 'Fact 3 title', 'text', 'Mixed' ),
			'fact3_text'  => array( 'Fact 3 text', 'text', 'Boys and girls learn together.' ),
			'fact4_icon'  => array( 'Fact 4 icon (emoji)', 'text', '🏠' ),
			'fact4_title' => array( 'Fact 4 title', 'text', 'Day and Boarding' ),
			'fact4_text'  => array( 'Fact 4 text', 'text', 'Learners come from home or board at school.' ),
			'fact5_icon'  => array( 'Fact 5 icon (emoji)', 'text', '🎓' ),
			'fact5_title' => array( 'Fact 5 title', 'text', 'O-Level and A-Level' ),
			'fact5_text'  => array( 'Fact 5 text', 'text', 'Offering both Arts and Sciences.' ),
			'mission_title' => array( 'Mission heading', 'text', 'Our Mission' ),
			'mission_text'  => array( 'Mission text', 'textarea', 'To provide quality, relevant and meaningful education aimed at producing independent-minded, healthy and self-sustaining relevant citizens.' ),
			'vision_title'  => array( 'Vision heading', 'text', 'Our Vision' ),
			'vision_text'   => array( 'Vision text', 'textarea', 'To nurture citizens who strive for quality education that enhances productivity and self-reliance.' ),
			'values_kicker' => array( 'Values: small heading', 'text', 'What we stand for' ),
			'values_title'  => array( 'Values: heading', 'text', 'Our Core Values' ),
			'val1_icon'  => array( 'Value 1 icon (emoji)', 'text', '📏' ),
			'val1_title' => array( 'Value 1 title', 'text', 'Discipline' ),
			'val1_text'  => array( 'Value 1 text', 'text', 'Order, respect and self-control in all we do.' ),
			'val2_icon'  => array( 'Value 2 icon (emoji)', 'text', '💪' ),
			'val2_title' => array( 'Value 2 title', 'text', 'Hard Work' ),
			'val2_text'  => array( 'Value 2 text', 'text', 'Effort and commitment in learning and service.' ),
			'val3_icon'  => array( 'Value 3 icon (emoji)', 'text', '🙏' ),
			'val3_title' => array( 'Value 3 title', 'text', 'Fear of God' ),
			'val3_text'  => array( 'Value 3 text', 'text', 'A life guided by faith and respect for God.' ),
			'val4_icon'  => array( 'Value 4 icon (emoji)', 'text', '⚖️' ),
			'val4_title' => array( 'Value 4 title', 'text', 'Integrity' ),
			'val4_text'  => array( 'Value 4 text', 'text', 'Honesty and doing what is right.' ),
			'fac_kicker' => array( 'Facilities: small heading', 'text', 'Learning spaces' ),
			'fac_title'  => array( 'Facilities: heading', 'text', 'Our School Facilities' ),
			'fac_list'   => array( 'Facilities (one per line)', 'textarea', "Computer Lab\nClassrooms\nScience Lab\nMusic Room\nArt Room\nLibrary\nSchool Canteen\nAdministration Block" ),
		) ),
		'kss_apply'    => array( 'title' => 'Apply Online page', 'fields' => array(
			'apply_heading' => array( 'Heading', 'text', 'Join Kahinju S.S.S' ),
			'step1_title'   => array( 'Step 1 title', 'text', 'Fill in the form' ),
			'step1_text'    => array( 'Step 1 text', 'text', 'Tell us about the learner and the parent or guardian.' ),
			'step2_title'   => array( 'Step 2 title', 'text', 'We reply to you' ),
			'step2_text'    => array( 'Step 2 text', 'text', 'The school office reads your application and replies to your phone.' ),
			'step3_title'   => array( 'Step 3 title', 'text', 'Visit the school' ),
			'step3_text'    => array( 'Step 3 text', 'text', 'Come to the school with the learner to complete admission.' ),
			'apply_image'   => array( 'Side photo', 'image', 0 ),
		) ),
		'kss_colours'  => array( 'title' => 'Site colours', 'fields' => array(
			'color_main'   => array( 'Main colour (buttons, headers)', 'color', '#8f1a2c' ),
			'color_dark'   => array( 'Dark colour (top bar, footer)', 'color', '#5e0f1c' ),
			'color_bright' => array( 'Bright red (highlights)', 'color', '#b5172b' ),
			'color_accent' => array( 'Accent colour (gold)', 'color', '#e3b23c' ),
		) ),
	);
}

function kss_defaults() {
	static $d = null;
	if ( null === $d ) {
		$d = array();
		foreach ( kss_fields() as $sec ) {
			foreach ( $sec['fields'] as $k => $f ) { $d[ $k ] = $f[2]; }
		}
	}
	return $d;
}

/** Text or colour set in the Customizer (or its default). */
function kss_mod( $key ) {
	$d = kss_defaults();
	return get_theme_mod( 'kss_' . $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

/** Photo chosen in the Customizer, or the bundled photo. */
function kss_mod_image( $key, $fallback, $size = 'large' ) {
	$id = absint( get_theme_mod( 'kss_' . $key, 0 ) );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, $size );
		if ( $url ) { return $url; }
	}
	return kss_img( $fallback );
}

function kss_customize( $wp_customize ) {
	$sanitize = array(
		'text'     => 'sanitize_text_field',
		'textarea' => 'sanitize_textarea_field',
		'image'    => 'absint',
		'color'    => 'sanitize_hex_color',
	);
	$wp_customize->add_panel( 'kss_panel', array( 'title' => 'School Website Texts', 'priority' => 20, 'description' => 'Change the words, photos and colours of the website.' ) );
	foreach ( kss_fields() as $sid => $sec ) {
		$wp_customize->add_section( $sid, array( 'title' => $sec['title'], 'panel' => 'kss_panel' ) );
		foreach ( $sec['fields'] as $key => $f ) {
			$id = 'kss_' . $key;
			$wp_customize->add_setting( $id, array( 'default' => $f[2], 'sanitize_callback' => $sanitize[ $f[1] ] ) );
			$args = array( 'label' => $f[0], 'section' => $sid );
			if ( 'color' === $f[1] ) {
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $args ) );
			} elseif ( 'image' === $f[1] ) {
				$args['mime_type'] = 'image';
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, $args ) );
			} else {
				$args['type'] = $f[1];
				$wp_customize->add_control( $id, $args );
			}
		}
	}
}
add_action( 'customize_register', 'kss_customize' );

/** Colours chosen by the admin become CSS variables. */
function kss_colour_css() {
	$map = array( 'color_main' => '--maroon', 'color_dark' => '--deep', 'color_bright' => '--red', 'color_accent' => '--gold' );
	$css = '';
	foreach ( $map as $key => $var ) {
		$v = sanitize_hex_color( kss_mod( $key ) );
		if ( $v ) { $css .= $var . ':' . $v . ';'; }
	}
	if ( $css ) { echo '<style id="kss-colours">:root{' . $css . '}</style>' . "\n"; }
}
add_action( 'wp_head', 'kss_colour_css', 20 );

/* ---------- Small helpers used by the templates ---------- */
function kss_img( $file ) {
	return get_theme_file_uri( 'assets/img/' . $file );
}

function kss_logo_url() {
	return kss_mod_image( 'logo', 'logo.png', 'medium' );
}

/** School details are edited by staff under School Data > School Settings. */
function kss_opt( $key ) {
	$defaults = array(
		'phone'    => '0772 874 003',
		'email'    => 'Kahinju@yahoo.com',
		'email2'   => 'Kahinju2022@gmail.com',
		'address'  => 'P.O. Box 449, Fort Portal, Uganda',
		'hours'    => '8:00 am - 4:30 pm',
		'facebook' => '',
	);
	$val = get_option( 'kss_' . $key, '' );
	return ( '' !== $val ) ? $val : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/** Address of a page by slug (falls back to /slug/). */
function kss_page_url( $slug ) {
	$p = get_page_by_path( $slug );
	return $p ? get_permalink( $p->ID ) : home_url( '/' . $slug . '/' );
}

/** Turns a stored image path (relative or full) into a full URL. */
function kss_media_url( $path ) {
	if ( function_exists( 'kssc_url' ) ) { return kssc_url( $path ); }
	return preg_match( '#^https?://#', $path ) ? $path : home_url( $path );
}

/** Banner photo: the page's Featured Image, or the bundled photo for that page. */
function kss_banner_image() {
	if ( is_page() ) {
		$id = get_queried_object_id();
		if ( $id && has_post_thumbnail( $id ) ) {
			return get_the_post_thumbnail_url( $id, 'full' );
		}
		$slug = sanitize_file_name( (string) get_post_field( 'post_name', $id ) );
		$file = 'assets/img/banners/' . $slug . '.jpg';
		if ( $slug && file_exists( get_theme_file_path( $file ) ) ) {
			return get_theme_file_uri( $file );
		}
	}
	return '';
}

/** Banner at the top of inner pages. */
function kss_banner( $title, $sub = '' ) {
	$img   = kss_banner_image();
	$class = 'page-banner' . ( $img ? ' has-photo' : '' );
	$style = $img ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
	echo '<section class="' . esc_attr( $class ) . '"' . $style . '><div class="wrap">';
	echo '<nav class="crumbs"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a> <span>/</span> ' . esc_html( $title ) . '</nav>';
	echo '<h1>' . esc_html( $title ) . '</h1>';
	if ( $sub ) { echo '<p>' . esc_html( $sub ) . '</p>'; }
	echo '</div></section>';
}

function kss_fallback_menu() {
	echo '<ul class="menu">';
	wp_list_pages( array( 'title_li' => '' ) );
	echo '</ul>';
}

function kss_post_image( $post_id, $size = 'kss-card' ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail_url( $post_id, $size );
	}
	return kss_img( 'fallback.jpg' );
}

/* ---------- Branded admin login page ---------- */
function kss_login_style() {
	?>
	<style>
		body.login { background: linear-gradient(160deg, rgba(94,15,28,.94), rgba(181,23,43,.86)), url('<?php echo esc_url( kss_img( 'hero.jpg' ) ); ?>') center / cover fixed no-repeat; }
		#login { width: 390px; padding-top: 5vh; }
		#login h1 a { background-image: url('<?php echo esc_url( kss_logo_url() ); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; width: 130px; height: 140px; margin: 0 auto 6px; filter: drop-shadow(0 4px 10px rgba(0,0,0,.35)); background-color: #fff; border-radius: 50%; padding: 12px; box-sizing: content-box; }
		.kss-login-title { text-align: center; color: #fff; margin: 0 0 18px; }
		.kss-login-title h2 { font-family: Georgia, serif; font-size: 24px; color: #fff; margin: 0 0 4px; }
		.kss-login-title p { margin: 0; color: #f6dce0; font-size: 14px; letter-spacing: .06em; text-transform: uppercase; }
		.login form { border-radius: 18px; border: 0; box-shadow: 0 24px 60px rgba(0,0,0,.4); padding: 28px 26px 24px; border-top: 6px solid #e3b23c; }
		.login label { font-weight: 600; color: #5e0f1c; font-size: 14px; }
		.login form .input, .login input[type=text], .login input[type=password] { border-radius: 10px; border: 1.5px solid #eadfd8; padding: 6px 12px; font-size: 17px; background: #fffdfb; }
		.login form .input:focus { border-color: #8f1a2c; box-shadow: 0 0 0 3px rgba(143,26,44,.15); }
		.login .button-primary { background: #8f1a2c; border-color: #8f1a2c; border-radius: 30px; padding: 4px 28px; height: auto; font-weight: 600; text-shadow: none; box-shadow: none; }
		.login .button-primary:hover { background: #5e0f1c; border-color: #5e0f1c; }
		.login #backtoblog a, .login #nav a { color: #fff; }
		.login .message, .login .notice { border-radius: 10px; }
	</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'kss_login_style' );
add_filter( 'login_headerurl', function () { return home_url( '/' ); } );
add_filter( 'login_headertext', function () { return get_bloginfo( 'name' ); } );
add_filter( 'login_message', function ( $message ) {
	return '<div class="kss-login-title"><h2>Kahinju Senior Secondary School</h2><p>Admin and staff login</p></div>' . $message;
} );

/* ---------- Pages and first-time content ---------- */
function kss_pages() {
	return array(
		'home'           => array( 'Home', '' ),
		'about-us'       => array( 'About Us', "<p>Kahinju Senior Secondary School is a secondary school in Fort Portal, Uganda, offering O-Level and A-Level education. Our motto is <strong>\"Ready to Serve\"</strong>.</p><p>Admin: replace this text with the real history of the school, its vision and its mission. Go to Pages &gt; About Us and edit.</p>" ),
		'academic-units' => array( 'Academic Units', '<p>Our academic work is organised in departments. Each department teaches the subjects listed below.</p>' ),
		'co-curricular-activities' => array( 'Co-curricular Activities', '<p>Learning at Kahinju goes beyond the classroom. Our clubs and activities help learners discover their talents, make friends and serve others.</p>' ),
		'gallery'        => array( 'Gallery', '<p>Moments from life at Kahinju Senior Secondary School.</p>' ),
		'news-events'    => array( 'News and Events', '<p>The latest news and upcoming events at our school.</p>' ),
		'contact-us'     => array( 'Contact Us', '<p>We would love to hear from you. Send us a message using the form below.</p>' ),
		'apply-online'   => array( 'Apply Online', '<p>Thank you for your interest in Kahinju Senior Secondary School. Fill in the form on this page and our office will reply to you.</p>' ),
	);
}

/** Creates any missing page. Returns slug => page ID. */
function kss_ensure_pages() {
	$ids = array();
	foreach ( kss_pages() as $slug => $data ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}
		$ids[ $slug ] = wp_insert_post( array(
			'post_title'   => $data[0],
			'post_name'    => $slug,
			'post_content' => $data[1],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		) );
	}
	return $ids;
}

/** Adds the Co-curricular page to the menu (after Academic Units) when it is missing. */
function kss_sync_menu() {
	$menu = wp_get_nav_menu_object( 'Main Menu' );
	$page = get_page_by_path( 'co-curricular-activities' );
	if ( ! $menu || ! $page ) { return; }
	$found = false;
	foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $it ) {
		if ( 'page' === $it->object && (int) $it->object_id === (int) $page->ID ) { $found = true; }
	}
	if ( $found ) { return; }
	wp_update_nav_menu_item( $menu->term_id, 0, array(
		'menu-item-title'     => 'Co-curricular',
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $page->ID,
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish',
	) );
	$by = array();
	foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $it ) {
		if ( 'page' === $it->object ) { $by[ (int) $it->object_id ] = $it; }
	}
	$pos = 1;
	foreach ( array( 'home', 'about-us', 'academic-units', 'co-curricular-activities', 'gallery', 'news-events', 'contact-us' ) as $slug ) {
		$pg = get_page_by_path( $slug );
		if ( $pg && isset( $by[ $pg->ID ] ) ) {
			wp_update_nav_menu_item( $menu->term_id, $by[ $pg->ID ]->ID, array(
				'menu-item-title'     => $by[ $pg->ID ]->title,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $pg->ID,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $pos,
			) );
			$pos++;
		}
	}
}

/** When the theme files are updated, add any new page and menu link. */
function kss_maybe_update() {
	if ( get_option( 'kss_theme_ver' ) !== KSS_VER ) {
		kss_ensure_pages();
		kss_sync_menu();
		update_option( 'kss_theme_ver', KSS_VER );
	}
}
add_action( 'init', 'kss_maybe_update', 20 );

function kss_seed_site() {
	global $wp_rewrite;

	update_option( 'blogdescription', 'Ready to Serve' );
	$ids = kss_ensure_pages();

	// Home page as the front page.
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );

	// Menu (Apply Online is a button in the header, so it is not in the menu).
	if ( ! wp_get_nav_menu_object( 'Main Menu' ) ) {
		$menu_id = wp_create_nav_menu( 'Main Menu' );
		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( $ids as $slug => $pid ) {
				if ( 'apply-online' === $slug ) { continue; }
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'     => ( 'co-curricular-activities' === $slug ) ? 'Co-curricular' : get_the_title( $pid ),
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $pid,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}
			$locations            = get_theme_mod( 'nav_menu_locations', array() );
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	// Remove WordPress defaults.
	$hello = get_post( 1 );
	if ( $hello && 'Hello world!' === $hello->post_title ) { wp_delete_post( 1, true ); }
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample ) { wp_delete_post( $sample->ID, true ); }

	// Sample news (admin can edit or delete these).
	if ( ! get_option( 'kss_news_seeded' ) ) {
		$news = array(
			array( 'Welcome to the new Kahinju S.S.S website', '<p>We are happy to launch the new website of Kahinju Senior Secondary School. Here you will find news, events, photos and information about our academic units.</p><p>Teachers and the school admin can log in to add news and update the site.</p>' ),
			array( 'Educate Club at Kahinju S.S.S', '<p>Our students take part in the Educate Club, working together on projects that serve the school and the community. Admin: edit this post to add the real details and photos.</p>' ),
			array( 'Life at Kahinju S.S.S', '<p>From classrooms to assemblies and school events, there is always something happening at Kahinju. Visit the Gallery page to see more photos.</p>' ),
		);
		foreach ( $news as $n ) {
			wp_insert_post( array(
				'post_title'   => $n[0],
				'post_content' => $n[1],
				'post_status'  => 'publish',
				'post_type'    => 'post',
			) );
		}
		update_option( 'kss_news_seeded', 1 );
	}

	// Friendly URLs (exam: SEO friendly URLs).
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules( true );
}
add_action( 'after_switch_theme', 'kss_seed_site' );

/** Turns plain message text into safe paragraphs. */
function kss_paragraphs( $text ) {
	return wp_kses_post( wpautop( esc_html( (string) $text ) ) );
}

/** Friendly "icon" fallback photo for a card: first the card's own photo, then a student photo from the pool. */
function kss_pick_photo( $own, $pool, $i ) {
	if ( ! empty( $own ) ) { return kss_media_url( $own ); }
	if ( ! empty( $pool ) ) { return kss_media_url( $pool[ $i % count( $pool ) ] ); }
	return kss_img( 'fallback.jpg' );
}
