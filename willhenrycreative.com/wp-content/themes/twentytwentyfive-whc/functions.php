<?php
/**
 * Twenty Twenty-Five WHC child theme setup.
 */

if ( ! function_exists( 'twentytwentyfive_whc_enqueue_styles' ) ) :
	/**
	 * Enqueue parent + child styles with filemtime-based cache busting.
	 *
	 * @return void
	 */
	function twentytwentyfive_whc_enqueue_styles() {
		$parent_style_path = get_template_directory() . '/style.css';
		$child_style_path  = get_stylesheet_directory() . '/style.css';

		$parent_version = file_exists( $parent_style_path ) ? filemtime( $parent_style_path ) : wp_get_theme( get_template() )->get( 'Version' );
		$child_version  = file_exists( $child_style_path ) ? filemtime( $child_style_path ) : wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'twentytwentyfive-parent-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$parent_version
		);

		wp_enqueue_style(
			'twentytwentyfive-whc-style',
			get_stylesheet_uri(),
			array( 'twentytwentyfive-parent-style' ),
			$child_version
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_whc_enqueue_styles' );

if ( ! function_exists( 'twentytwentyfive_whc_force_front_page_file_template' ) ) :
	/**
	 * Forces front-page rendering from the child theme file.
	 *
	 * @param string $template Resolved template path.
	 * @return string
	 */
	function twentytwentyfive_whc_force_front_page_file_template( $template ) {
		if ( is_admin() || ! is_front_page() ) {
			return $template;
		}

		global $_wp_current_template_content, $_wp_current_template_id;

		$template_file = get_stylesheet_directory() . '/templates/front-page.html';
		if ( ! file_exists( $template_file ) ) {
			return $template;
		}

		$_wp_current_template_content = file_get_contents( $template_file );
		$_wp_current_template_id      = get_stylesheet() . '//front-page';

		return ABSPATH . WPINC . '/template-canvas.php';
	}
endif;
add_filter( 'template_include', 'twentytwentyfive_whc_force_front_page_file_template', 999 );
