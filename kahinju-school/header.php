<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo esc_url( kss_logo_url() ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>

<div class="topbar">
	<div class="wrap">
		<div class="topbar-info">
			<span>&#9742; <?php echo esc_html( kss_opt( 'phone' ) ); ?></span>
			<span>&#9993; <a href="mailto:<?php echo esc_attr( kss_opt( 'email' ) ); ?>"><?php echo esc_html( kss_opt( 'email' ) ); ?></a></span>
		</div>
		<div class="topbar-info topbar-right">
			<span>&#9719; Open <?php echo esc_html( kss_opt( 'hours' ) ); ?></span>
			<?php if ( kss_opt( 'facebook' ) ) : ?><a href="<?php echo esc_url( kss_opt( 'facebook' ) ); ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
		</div>
	</div>
</div>

<header class="site-header" id="top">
	<div class="wrap header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( kss_logo_url() ); ?>" alt="School logo">
			<span class="brand-text"><strong><?php echo esc_html( kss_mod( 'brand_name' ) ); ?></strong><em><?php echo esc_html( kss_mod( 'brand_tag' ) ); ?></em></span>
		</a>
		<button class="nav-toggle" aria-label="Open menu" aria-expanded="false"><span></span><span></span><span></span></button>
		<nav class="main-nav" aria-label="Main menu">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'menu',
				'fallback_cb'    => 'kss_fallback_menu',
			) );
			?>
			<a class="btn btn-gold nav-apply" href="<?php echo esc_url( kss_page_url( 'apply-online' ) ); ?>"><?php echo esc_html( kss_mod( 'btn_apply' ) ); ?></a>
		</nav>
	</div>
</header>
<main id="main">
