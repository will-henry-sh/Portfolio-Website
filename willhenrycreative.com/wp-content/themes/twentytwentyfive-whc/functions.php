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

/**
 * Contact form shortcode — renders the form with a nonce.
 */
function whc_contact_form_shortcode() {
	$status = sanitize_key( $_GET['status'] ?? '' );
	ob_start();

	if ( $status === 'success' ) {
		echo '<p class="whc-form-success">Thanks — I\'ll be in touch soon.</p>';
		return ob_get_clean();
	}

	if ( $status === 'error' ) {
		echo '<p class="whc-form-error">Something went wrong. Try again or email <a href="mailto:hello@willhenrycreative.com">hello@willhenrycreative.com</a> directly.</p>';
	}
	?>
	<form class="whc-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="whc_contact">
		<?php wp_nonce_field( 'whc_contact_form', 'whc_contact_nonce' ); ?>
		<div class="whc-form-field">
			<label for="whc-name">Name</label>
			<input type="text" id="whc-name" name="name" required>
		</div>
		<div class="whc-form-field">
			<label for="whc-email">Email</label>
			<input type="email" id="whc-email" name="email" required>
		</div>
		<div class="whc-form-field">
			<label for="whc-message">Message</label>
			<textarea id="whc-message" name="message" rows="6" required></textarea>
		</div>
		<button type="submit" class="wp-block-button__link wp-element-button">Send message</button>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'whc_contact_form', 'whc_contact_form_shortcode' );

/**
 * Handle contact form submission.
 */
function whc_handle_contact_form() {
	if ( ! isset( $_POST['whc_contact_nonce'] ) || ! wp_verify_nonce( $_POST['whc_contact_nonce'], 'whc_contact_form' ) ) {
		wp_die( 'Security check failed.' );
	}

	$name    = sanitize_text_field( $_POST['name'] ?? '' );
	$email   = sanitize_email( $_POST['email'] ?? '' );
	$message = sanitize_textarea_field( $_POST['message'] ?? '' );

	if ( ! $name || ! $email || ! $message || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'status', 'error', home_url( '/contact/' ) ) );
		exit;
	}

	$to      = 'hello@willhenrycreative.com';
	$subject = "Portfolio contact from {$name}";
	$body    = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
	$headers = [ "Reply-To: {$name} <{$email}>" ];

	$sent = wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'status', $sent ? 'success' : 'error', home_url( '/contact/' ) ) );
	exit;
}
add_action( 'admin_post_whc_contact', 'whc_handle_contact_form' );
add_action( 'admin_post_nopriv_whc_contact', 'whc_handle_contact_form' );
