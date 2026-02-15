<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		$style_path    = get_parent_theme_file_path( 'style.css' );
		$style_version = file_exists( $style_path ) ? filemtime( $style_path ) : wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			$style_version
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

// Auto-creates core portfolio pages so slug templates resolve without manual admin setup.
if ( ! function_exists( 'twentytwentyfive_seed_portfolio_pages' ) ) :
	/**
	 * Creates required portfolio pages once if they do not already exist.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_seed_portfolio_pages() {
		if ( get_option( 'twentytwentyfive_portfolio_pages_seeded' ) ) {
			return;
		}

		$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

		$pages = array(
			array(
				'title' => 'SEO',
				'slug'  => 'seo',
			),
			array(
				'title' => 'Copywriting',
				'slug'  => 'copywriting',
			),
			array(
				'title' => 'Development',
				'slug'  => 'development',
			),
			array(
				'title' => 'About',
				'slug'  => 'about',
			),
			array(
				'title' => 'Contact',
				'slug'  => 'contact',
			),
		);

		foreach ( $pages as $page ) {
			$existing_page = get_page_by_path( $page['slug'], OBJECT, 'page' );

			if ( $existing_page ) {
				continue;
			}

			wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $page['title'],
					'post_name'    => $page['slug'],
					'post_content' => $lorem,
				)
			);
		}

		update_option( 'twentytwentyfive_portfolio_pages_seeded', 1, false );
	}
endif;
add_action( 'init', 'twentytwentyfive_seed_portfolio_pages', 20 );

// Forces noindex/nofollow on all front-end responses for private pre-launch sites.
if ( ! function_exists( 'twentytwentyfive_force_noindex' ) ) :
	/**
	 * Adds noindex directives via WordPress robots API.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @param array<string, bool> $robots Associative array of robots directives.
	 * @return array<string, bool>
	 */
	function twentytwentyfive_force_noindex( $robots ) {
		$robots['noindex']   = true;
		$robots['nofollow']  = true;
		$robots['noarchive'] = true;
		$robots['nosnippet'] = true;
		return $robots;
	}
endif;
add_filter( 'wp_robots', 'twentytwentyfive_force_noindex' );

if ( ! function_exists( 'twentytwentyfive_send_noindex_header' ) ) :
	/**
	 * Sends noindex directives in HTTP headers for crawlers that respect X-Robots-Tag.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_send_noindex_header() {
		if ( is_admin() || headers_sent() ) {
			return;
		}

		header( 'X-Robots-Tag: noindex, nofollow, noarchive, nosnippet', true );
	}
endif;
add_action( 'send_headers', 'twentytwentyfive_send_noindex_header' );

// Adds baseline security headers and disables XML-RPC for reduced attack surface.
if ( ! function_exists( 'twentytwentyfive_security_headers' ) ) :
	/**
	 * Sends baseline security headers.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_security_headers() {
		if ( is_admin() || headers_sent() ) {
			return;
		}

		header( 'X-Frame-Options: SAMEORIGIN', true );
		header( 'X-Content-Type-Options: nosniff', true );
		header( 'Referrer-Policy: strict-origin-when-cross-origin', true );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()', true );
	}
endif;
add_action( 'send_headers', 'twentytwentyfive_security_headers', 20 );

if ( ! function_exists( 'twentytwentyfive_disable_xmlrpc' ) ) :
	/**
	 * Disables XML-RPC endpoint and pingback functionality.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return bool
	 */
	function twentytwentyfive_disable_xmlrpc() {
		return false;
	}
endif;
add_filter( 'xmlrpc_enabled', 'twentytwentyfive_disable_xmlrpc' );
