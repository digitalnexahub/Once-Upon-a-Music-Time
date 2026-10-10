<?php
/**
 * Plugin Name: Washtower Design Layer
 * Description: Header, footer, homepage components (product carousel, reviews, blog cards, hotspots, newsletter), animations and shop styling for the WASHTOWER homepage recreation.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------------------------------------------------------------- assets */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'wt-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Caveat:wght@500&family=Barlow:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap', [], null );
	wp_register_style( 'wt-design', false );
	wp_enqueue_style( 'wt-design' );
	wp_add_inline_style( 'wt-design', wt_design_css() );
	wp_register_script( 'wt-design', false, [], null, true );
	wp_enqueue_script( 'wt-design' );
	wp_localize_script( 'wt-design', 'WT', [
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'wt_newsletter' ),
		'acc'   => is_user_logged_in() ? '' : wp_create_nonce( 'wt_account' ),
	] );
	wp_add_inline_script( 'wt-design', wt_design_js() );
	if ( function_exists( 'WC' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}, 99 );

function wt_icon( $name ) {
	$p = [
		'truck'   => '<path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
		'shield'  => '<path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
		'leaf'    => '<path d="M5 19c0-8 5-13 15-14-1 10-6 15-14 15"/><path d="M5 19l8-8"/>',
		'star'    => '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
		'search'  => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>',
		'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
		'bag'     => '<path d="M5 8h14l-1 13H6z"/><path d="M9 8V6a3 3 0 016 0v2"/>',
		'arrow'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'menu'    => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'   => '<path d="M6 6l12 12M18 6L6 18"/>',
		'up'      => '<path d="M12 19V5M6 11l6-6 6 6"/>',
		'left'    => '<path d="M15 6l-6 6 6 6"/>',
		'right'   => '<path d="M9 6l6 6-6 6"/>',
		'fb'      => '<path d="M14 8h3V4h-3a4 4 0 00-4 4v2H7v4h3v7h4v-7h3l1-4h-4V8z"/>',
		'ig'      => '<rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3.5"/><circle cx="17" cy="7" r=".6"/>',
		'pin'     => '<circle cx="12" cy="12" r="8.5"/><path d="M11 8.5c3-1 5 .8 4.5 3.3-.4 2-2.6 2.8-3.7 1.6M11.5 11l-2 9"/>',
		'yt'      => '<rect x="3" y="6" width="18" height="12" rx="3.5"/><path d="M10.5 9.5v5l4-2.5z"/>',
	];
	return '<svg class="wt-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ( $p[ $name ] ?? '' ) . '</svg>';
}

function wt_page_url( $title, $fallback = '/' ) {
	$p = get_page_by_title( $title );
	return $p ? get_permalink( $p ) : home_url( $fallback );
}

function wt_cat_url( $slug ) {
	$t = get_term_by( 'slug', $slug, 'product_cat' );
	return $t ? get_term_link( $t ) : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
}

function wt_cart_count() {
	return ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
}

add_filter( 'woocommerce_add_to_cart_fragments', function ( $f ) {
	$f['span.wt-count'] = '<span class="wt-count" aria-label="Items in basket">' . wt_cart_count() . '</span>';
	return $f;
} );

/* Header is an Elementor Pro Theme Builder template ("WASHTOWER Header"). */

/* ---------------------------------------------------------------- footer */

add_shortcode( 'wt_footer', function () {
	ob_start();
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$cols = [
		'Shop'  => [ 'All Cabinets' => $shop, 'Laundry Cabinets' => wt_cat_url( 'laundry-cabinets' ), 'Stackable Units' => wt_cat_url( 'stackable-washer-dryer' ), 'Storage Cabinets' => wt_cat_url( 'storage-cabinets' ), 'Accessories' => wt_cat_url( 'accessories' ) ],
		'Help'  => [ 'Design Your Own' => wt_page_url( 'Design Your Own' ), 'Delivery Information' => wt_page_url( 'Delivery Information' ), 'Returns & Warranty' => wt_page_url( 'Returns & Warranty' ), 'Installation Guide' => wt_page_url( 'Installation Guide' ), 'FAQs' => wt_page_url( 'FAQs' ) ],
		'About' => [ 'Our Story' => wt_page_url( 'Our Story' ), 'Sustainability' => wt_page_url( 'Sustainability' ), 'Customer Reviews' => wt_page_url( 'Customer Reviews' ), 'Blog' => get_permalink( get_option( 'page_for_posts' ) ), 'Contact Us' => wt_page_url( 'Contact Us' ) ],
	];
	?>
	<footer class="wt-footer">
		<div class="wt-in wt-footer__grid">
			<div class="wt-footer__brand">
				<a class="wt-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">WASHTOWER<sup>®</sup></a>
				<p>Beautifully designed laundry cabinets for modern homes. Customisable, practical and built to last.</p>
				<div class="wt-social">
					<a href="https://www.instagram.com/" aria-label="Instagram" rel="noopener" target="_blank"><?php echo wt_icon( 'ig' ); ?></a>
					<a href="https://www.facebook.com/" aria-label="Facebook" rel="noopener" target="_blank"><?php echo wt_icon( 'fb' ); ?></a>
					<a href="https://www.pinterest.com/" aria-label="Pinterest" rel="noopener" target="_blank"><?php echo wt_icon( 'pin' ); ?></a>
					<a href="https://www.youtube.com/" aria-label="YouTube" rel="noopener" target="_blank"><?php echo wt_icon( 'yt' ); ?></a>
				</div>
			</div>
			<?php foreach ( $cols as $h => $links ) : ?>
				<nav class="wt-footer__col" aria-label="<?php echo esc_attr( $h ); ?>"><h2><?php echo esc_html( $h ); ?></h2>
					<?php foreach ( $links as $l => $u ) { echo '<a href="' . esc_url( $u ) . '">' . esc_html( $l ) . '</a>'; } ?>
				</nav>
			<?php endforeach; ?>
			<div class="wt-footer__news">
				<h2>Join our mailing list</h2>
				<p>Get inspiration, new products and exclusive offers straight to your inbox.</p>
				<form class="wt-news" novalidate>
					<label for="wt-news-email" class="screen-reader-text">Email address</label>
					<input id="wt-news-email" type="email" name="email" placeholder="Your email address" required autocomplete="email">
					<input type="text" name="company" class="wt-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
					<button type="submit" aria-label="Subscribe"><?php echo wt_icon( 'arrow' ); ?></button>
					<p class="wt-news__msg" role="status" aria-live="polite"></p>
				</form>
			</div>
		</div>
		<div class="wt-in wt-footer__base">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> WASHTOWER®. All rights reserved.</p>
			<nav aria-label="Legal">
				<a href="<?php echo esc_url( get_privacy_policy_url() ?: wt_page_url( 'Privacy Policy' ) ); ?>">Privacy Policy</a>
				<a href="<?php echo esc_url( wt_page_url( 'Terms & Conditions' ) ); ?>">Terms &amp; Conditions</a>
				<a href="<?php echo esc_url( wt_page_url( 'Cookies' ) ); ?>">Cookies</a>
				<button class="wt-top" id="wt-top" aria-label="Back to top"><?php echo wt_icon( 'up' ); ?></button>
			</nav>
		</div>
	</footer>
	<?php
	return ob_get_clean();
} );

/* "cart" wording -> "basket" in the mini cart and notices */
add_filter( 'gettext', function ( $t, $text, $domain ) {
	if ( in_array( $domain, [ 'woocommerce', 'elementor-pro' ], true ) ) {
		$map = [ 'View cart' => 'View basket', 'Cart' => 'Basket', 'No products in the cart.' => 'Your basket is empty.' ];
		if ( isset( $map[ $text ] ) ) { return $map[ $text ]; }
	}
	return $t;
}, 10, 3 );

/* ---------------------------------------------------------------- account panel (login / register / lost password) */

add_action( 'wp_footer', function () {
	if ( is_user_logged_in() || ! function_exists( 'wc_get_page_permalink' ) ) { return; }
	$terms = get_privacy_policy_url();
	$eye   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
	?>
	<div class="wta" id="wta" hidden>
		<div class="wta__overlay" data-wta-close></div>
		<aside class="wta__panel" role="dialog" aria-modal="true" aria-labelledby="wta-title">
			<button type="button" class="wta__close" data-wta-close aria-label="Close"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
			<div class="wta__brand">WASHTOWER<sup>&reg;</sup></div>
			<h2 class="wta__title" id="wta-title"><span data-t="login">Welcome back</span><span data-t="register">Create your account</span><span data-t="lost">Reset your password</span></h2>
			<p class="wta__lead"><span data-t="login">Log in to track orders, save designs and check out faster.</span><span data-t="register">Save your designs, track orders and get exclusive offers.</span><span data-t="lost">Enter your email and we&rsquo;ll send you a secure link to set a new password.</span></p>

			<div class="wta__tabs" role="tablist">
				<button type="button" role="tab" data-view="login" aria-selected="true">Log in</button>
				<button type="button" role="tab" data-view="register" aria-selected="false">Sign up</button>
				<span class="wta__ink" aria-hidden="true"></span>
			</div>

			<div class="wta__views">
				<form class="wta__form is-active" data-form="login" novalidate>
					<div class="wta__f"><input type="text" name="log" id="wta-log" autocomplete="username" required placeholder=" "><label for="wta-log">Email or username</label></div>
					<div class="wta__f wta__f--pw"><input type="password" name="pwd" id="wta-pwd" autocomplete="current-password" required placeholder=" "><label for="wta-pwd">Password</label><button type="button" class="wta__eye" aria-label="Show password"><?php echo $eye; ?></button></div>
					<div class="wta__row">
						<label class="wta__check"><input type="checkbox" name="remember" value="1" checked><span></span>Remember me</label>
						<button type="button" class="wta__link" data-view="lost">Forgot password?</button>
					</div>
					<button type="submit" class="wta__btn"><span>Log in</span></button>
					<p class="wta__switch">New to Washtower? <button type="button" class="wta__link" data-view="register">Create an account</button></p>
				</form>

				<form class="wta__form" data-form="register" novalidate>
					<div class="wta__f"><input type="email" name="email" id="wta-email" autocomplete="email" required placeholder=" "><label for="wta-email">Email address</label></div>
					<div class="wta__f wta__f--pw"><input type="password" name="password" id="wta-rpwd" autocomplete="new-password" required minlength="8" placeholder=" "><label for="wta-rpwd">Create password</label><button type="button" class="wta__eye" aria-label="Show password"><?php echo $eye; ?></button></div>
					<div class="wta__meter" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
					<p class="wta__hint" data-meter-text>At least 8 characters, with a mix of letters and numbers.</p>
					<input type="text" name="company" class="wta__hp" tabindex="-1" autocomplete="off" aria-hidden="true">
					<label class="wta__check wta__check--top"><input type="checkbox" name="optin" value="1"><span></span>Send me design ideas and offers by email</label>
					<button type="submit" class="wta__btn"><span>Create account</span></button>
					<p class="wta__legal">By creating an account you agree to our <a href="<?php echo esc_url( $terms ?: home_url( '/' ) ); ?>">privacy policy</a>.</p>
					<p class="wta__switch">Already have an account? <button type="button" class="wta__link" data-view="login">Log in</button></p>
				</form>

				<form class="wta__form" data-form="lost" novalidate>
					<div class="wta__f"><input type="text" name="user_login" id="wta-lost" autocomplete="email" required placeholder=" "><label for="wta-lost">Email or username</label></div>
					<button type="submit" class="wta__btn"><span>Send reset link</span></button>
					<p class="wta__switch"><button type="button" class="wta__link wta__back" data-view="login"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>Back to log in</button></p>
				</form>

				<div class="wta__done" data-form="done" aria-live="polite">
					<svg class="wta__tick" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="23"/><path d="M15 27l7 7 15-15"/></svg>
					<h3 data-done-title>Check your inbox</h3>
					<p data-done-text></p>
					<button type="button" class="wta__btn wta__btn--ghost" data-view="login"><span>Back to log in</span></button>
				</div>
			</div>
			<p class="wta__msg" role="alert"></p>
		</aside>
	</div>
	<?php
}, 20 );

function wt_account_json_error( $msg ) { wp_send_json_error( [ 'message' => $msg ], 400 ); }
function wt_account_rate_key() { return 'wt_login_' . md5( $_SERVER['REMOTE_ADDR'] ?? '' ); }

add_action( 'wp_ajax_nopriv_wt_account', function () {
	check_ajax_referer( 'wt_account', 'nonce' );
	$do = sanitize_key( $_POST['do'] ?? '' );

	if ( 'login' === $do ) {
		$tries = (int) get_transient( wt_account_rate_key() );
		if ( $tries >= 6 ) { wt_account_json_error( 'Too many attempts. Please wait 10 minutes and try again.' ); }
		$login = sanitize_text_field( wp_unslash( $_POST['log'] ?? '' ) );
		if ( is_email( $login ) ) { $u = get_user_by( 'email', $login ); if ( $u ) { $login = $u->user_login; } }
		$user = wp_signon( [ 'user_login' => $login, 'user_password' => (string) wp_unslash( $_POST['pwd'] ?? '' ), 'remember' => ! empty( $_POST['remember'] ) ], is_ssl() );
		if ( is_wp_error( $user ) ) {
			set_transient( wt_account_rate_key(), $tries + 1, 10 * MINUTE_IN_SECONDS );
			wt_account_json_error( 'That email/username and password don’t match. Please try again.' );
		}
		delete_transient( wt_account_rate_key() );
		wp_send_json_success( [ 'message' => 'Welcome back, ' . esc_html( $user->display_name ) . '!', 'redirect' => wc_get_page_permalink( 'myaccount' ) ] );
	}

	if ( 'register' === $do ) {
		if ( ! empty( $_POST['company'] ) ) { wt_account_json_error( 'Something went wrong. Please try again.' ); }
		if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) { wt_account_json_error( 'Registration is currently closed.' ); }
		$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$pass  = (string) wp_unslash( $_POST['password'] ?? '' );
		if ( ! is_email( $email ) ) { wt_account_json_error( 'Please enter a valid email address.' ); }
		if ( strlen( $pass ) < 8 ) { wt_account_json_error( 'Your password needs at least 8 characters.' ); }
		if ( email_exists( $email ) ) { wt_account_json_error( 'An account with this email already exists. Try logging in.' ); }
		$id = wc_create_new_customer( $email, '', $pass );
		if ( is_wp_error( $id ) ) { wt_account_json_error( wp_strip_all_tags( $id->get_error_message() ) ); }
		if ( ! empty( $_POST['optin'] ) ) { update_user_meta( $id, 'wt_marketing_optin', 1 ); }
		wc_set_customer_auth_cookie( $id );
		wp_send_json_success( [ 'message' => 'Your account is ready. Welcome to Washtower!', 'redirect' => wc_get_page_permalink( 'myaccount' ) ] );
	}

	if ( 'lost' === $do ) {
		$login = sanitize_text_field( wp_unslash( $_POST['user_login'] ?? '' ) );
		if ( '' === $login ) { wt_account_json_error( 'Please enter your email or username.' ); }
		$tries = (int) get_transient( wt_account_rate_key() . '_lp' );
		if ( $tries < 5 ) { set_transient( wt_account_rate_key() . '_lp', $tries + 1, 15 * MINUTE_IN_SECONDS ); retrieve_password( $login ); }
		/* same answer whether or not the account exists, so emails can't be probed */
		wp_send_json_success( [ 'message' => 'If an account exists for ' . esc_html( $login ) . ', a password reset link is on its way. The link expires in 24 hours.' ] );
	}

	wt_account_json_error( 'Unknown request.' );
} );

/* ---------------------------------------------------------------- newsletter */

add_action( 'init', function () {
	register_post_type( 'wt_subscriber', [
		'label'        => 'Subscribers',
		'public'       => false,
		'show_ui'      => true,
		'menu_icon'    => 'dashicons-email',
		'supports'     => [ 'title' ],
		'capabilities' => [ 'create_posts' => 'do_not_allow' ],
		'map_meta_cap' => true,
	] );
} );

function wt_newsletter() {
	check_ajax_referer( 'wt_newsletter', 'nonce' );
	if ( ! empty( $_POST['company'] ) ) { wp_send_json_success( [ 'message' => 'Thanks for subscribing!' ] ); }
	$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	if ( ! is_email( $email ) ) { wp_send_json_error( [ 'message' => 'Please enter a valid email address.' ], 400 ); }
	if ( get_page_by_title( $email, OBJECT, 'wt_subscriber' ) ) { wp_send_json_success( [ 'message' => 'You are already subscribed. Thank you!' ] ); }
	wp_insert_post( [ 'post_type' => 'wt_subscriber', 'post_status' => 'publish', 'post_title' => $email ] );
	do_action( 'wt_newsletter_subscribed', $email );
	wp_mail( get_option( 'admin_email' ), 'New WASHTOWER newsletter subscriber', $email );
	wp_send_json_success( [ 'message' => 'Thanks for subscribing! Check your inbox soon.' ] );
}
add_action( 'wp_ajax_wt_newsletter', 'wt_newsletter' );
add_action( 'wp_ajax_nopriv_wt_newsletter', 'wt_newsletter' );

/* ---------------------------------------------------------------- shortcodes */

function wt_stars( $rating ) {
	$full = (int) round( $rating );
	return '<span class="wt-stars" aria-label="' . esc_attr( $rating ) . ' out of 5">' . str_repeat( '★', $full ) . '<span class="off">' . str_repeat( '★', 5 - $full ) . '</span></span>';
}

// Featured product carousel with AJAX add to basket.
add_shortcode( 'wt_products_carousel', function ( $a ) {
	if ( ! function_exists( 'wc_get_products' ) ) { return ''; }
	$a     = shortcode_atts( [ 'limit' => 8 ], $a );
	$items = wc_get_products( [ 'limit' => (int) $a['limit'], 'featured' => true, 'status' => 'publish', 'orderby' => 'ID', 'order' => 'ASC' ] );
	if ( count( $items ) < (int) $a['limit'] ) {
		$have  = array_map( fn( $p ) => $p->get_id(), $items );
		$items = array_merge( $items, wc_get_products( [ 'limit' => (int) $a['limit'] - count( $items ), 'status' => 'publish', 'exclude' => $have, 'orderby' => 'date', 'order' => 'DESC' ] ) );
	}
	$o = '<div class="wt-carousel wt-reveal" data-carousel><button class="wt-car-btn prev" aria-label="Previous products">' . wt_icon( 'left' ) . '</button><div class="wt-car-track" tabindex="0" aria-label="Featured products">';
	foreach ( $items as $p ) {
		$rating = get_post_meta( $p->get_id(), '_wt_display_rating', true );
		$count  = get_post_meta( $p->get_id(), '_wt_display_count', true );
		if ( $p->get_review_count() ) { $rating = $p->get_average_rating(); $count = $p->get_review_count(); }
		$btn = sprintf(
			'<a href="%s" data-quantity="1" class="wt-atc button add_to_cart_button ajax_add_to_cart product_type_%s" data-product_id="%d" data-product_sku="%s" aria-label="Add %s to basket" rel="nofollow">Add to basket</a>',
			esc_url( $p->add_to_cart_url() ), esc_attr( $p->get_type() ), $p->get_id(), esc_attr( $p->get_sku() ), esc_attr( $p->get_name() )
		);
		if ( ! $p->is_type( 'simple' ) ) { $btn = '<a class="wt-atc" href="' . esc_url( $p->get_permalink() ) . '">Choose options</a>'; }
		$o .= '<article class="wt-pcard"><a class="wt-pcard__img" href="' . esc_url( $p->get_permalink() ) . '">' . $p->get_image( 'woocommerce_single', [ 'loading' => 'lazy' ] ) . '</a><div class="wt-pcard__b"><h3><a href="' . esc_url( $p->get_permalink() ) . '">' . esc_html( $p->get_name() ) . '</a></h3>';
		if ( $rating ) { $o .= '<div class="wt-pcard__r">' . wt_stars( (float) $rating ) . ' <span>' . esc_html( number_format( (float) $rating, 1 ) ) . ' (' . (int) $count . ')</span></div>'; }
		$o .= '<div class="wt-pcard__p">' . $p->get_price_html() . '</div>' . $btn . '</div></article>';
	}
	return $o . '</div><button class="wt-car-btn next" aria-label="Next products">' . wt_icon( 'right' ) . '</button><div class="wt-car-line" aria-hidden="true"><span></span></div></div>';
} );

// Latest posts as cards.
add_shortcode( 'wt_blog_cards', function ( $a ) {
	$a = shortcode_atts( [ 'limit' => 3 ], $a );
	$q = new WP_Query( [ 'posts_per_page' => (int) $a['limit'], 'ignore_sticky_posts' => true ] );
	$o = '<div class="wt-blog wt-reveal-group">';
	while ( $q->have_posts() ) {
		$q->the_post();
		$cat = get_the_category();
		$o  .= '<article class="wt-bcard"><a class="wt-bcard__img" href="' . esc_url( get_permalink() ) . '" tabindex="-1" aria-hidden="true">' . get_the_post_thumbnail( null, 'medium_large', [ 'loading' => 'lazy', 'alt' => '' ] ) . '</a><div class="wt-bcard__b">';
		if ( $cat ) { $o .= '<span class="wt-badge">' . esc_html( $cat[0]->name ) . '</span>'; }
		$o .= '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3><p>' . esc_html( wp_trim_words( get_the_excerpt(), 14 ) ) . '</p><a class="wt-more" href="' . esc_url( get_permalink() ) . '">Read more <span aria-hidden="true">→</span><span class="screen-reader-text"> about ' . esc_html( get_the_title() ) . '</span></a></div></article>';
	}
	wp_reset_postdata();
	return $o . '</div>';
} );

// Reviews: real approved WooCommerce product reviews. Placeholder cards are shown only to editors until real reviews exist.
add_shortcode( 'wt_reviews', function () {
	$photos   = [ 63, 64, 65 ];
	$reviews  = get_comments( [ 'type' => 'review', 'status' => 'approve', 'number' => 12, 'meta_key' => 'rating', 'orderby' => 'comment_date', 'order' => 'DESC' ] );
	$cards    = [];
	foreach ( $reviews as $r ) {
		$cards[] = [ (int) get_comment_meta( $r->comment_ID, 'rating', true ), $r->comment_content, $r->comment_author, wc_review_is_from_verified_owner( $r->comment_ID ) ? 'Verified buyer' : 'Customer', get_avatar_url( $r, [ 'size' => 64 ] ) ];
	}
	$placeholder = false;
	if ( ! $cards ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return '<p class="wt-reviews-empty">Be the first to <a href="' . esc_url( wt_page_url( 'Customer Reviews' ) ) . '">share your review</a>.</p>';
		}
		$placeholder = true;
		$cards = [
			[ 5, 'Placeholder review text. Replace with a genuine customer review.', 'Customer name', 'Verified buyer', '' ],
			[ 5, 'Placeholder review text. Replace with a genuine customer review.', 'Customer name', 'Verified buyer', '' ],
			[ 5, 'Placeholder review text. Replace with a genuine customer review.', 'Customer name', 'Verified buyer', '' ],
		];
	}
	$o = $placeholder ? '<p class="wt-admin-note">Editor-only preview: no approved product reviews yet, so visitors see a short call to action instead of these placeholder cards.</p>' : '';
	$o .= '<div class="wt-carousel wt-rev-carousel wt-reveal"><div class="wt-car-track wt-reviews" tabindex="0" aria-label="Customer reviews">';
	foreach ( $cards as $i => $c ) {
		$av = $c[4] ? '<img src="' . esc_url( $c[4] ) . '" alt="" width="36" height="36" loading="lazy">' : '<span class="wt-av" aria-hidden="true">' . esc_html( mb_substr( $c[2], 0, 1 ) ) . '</span>';
		$o .= '<figure class="wt-rcard"><div class="wt-rcard__t">' . wt_stars( $c[0] ) . '<blockquote>“' . esc_html( wp_trim_words( $c[1], 24 ) ) . '”</blockquote><figcaption>' . $av . '<span><b>' . esc_html( $c[2] ) . '</b><small>' . esc_html( $c[3] ) . '</small></span></figcaption></div>' . wp_get_attachment_image( $photos[ $i % 3 ], 'medium', false, [ 'class' => 'wt-rcard__img', 'loading' => 'lazy', 'alt' => '' ] ) . '</figure>';
	}
	return $o . '</div></div>';
} );

// Interactive hotspots on the "Design your own" image.
add_shortcode( 'wt_hotspots', function ( $a ) {
	$a    = shortcode_atts( [ 'image' => 59 ], $a );
	$spot = [ [ 'Tall storage', 'Full-height cupboards for linen, ironing boards and cleaning kit.', 27, 17 ], [ 'Integrated appliances', 'Washer and dryer raised to a comfortable working height.', 62, 29 ], [ 'Pull out baskets', 'Slide-out baskets make loading and sorting effortless.', 37, 66 ], [ 'Soft close drawers', 'Quiet, cushioned drawers for detergents and accessories.', 65, 68 ] ];
	$o    = '<div class="wt-hot">' . wp_get_attachment_image( (int) $a['image'], 'full', false, [ 'class' => 'wt-hot__img', 'loading' => 'lazy', 'alt' => 'Woman using a modern integrated laundry cabinet (placeholder image)' ] );
	foreach ( $spot as $i => $s ) {
		$o .= '<div class="wt-spot" style="left:' . $s[2] . '%;top:' . $s[3] . '%"><button class="wt-spot__b" aria-expanded="false" aria-describedby="wt-tip-' . $i . '"><span aria-hidden="true">+</span><span class="screen-reader-text">' . esc_html( $s[0] ) . '</span></button><span class="wt-spot__lbl" aria-hidden="true">' . esc_html( $s[0] ) . '</span><span class="wt-tip" role="tooltip" id="wt-tip-' . $i . '"><b>' . esc_html( $s[0] ) . '</b>' . esc_html( $s[1] ) . '</span></div>';
	}
	return $o . '</div>';
} );

/* ---------------------------------------------------------------- shop/product pages */

add_action( 'woocommerce_before_main_content', function () {
	if ( ! is_product_taxonomy() && ! is_shop() ) { return; }
	$title = is_shop() ? 'All cabinets' : single_term_title( '', false );
	$desc  = is_shop() ? 'Custom laundry cabinets and washing machine cupboards designed for modern living.' : wp_strip_all_tags( term_description() );
	echo '<section class="wt-archive-hero"><div class="wt-in"><span class="wt-label">Explore our range</span><h1>' . esc_html( $title ) . '</h1><p>' . esc_html( $desc ) . '</p><div class="wt-chips">';
	echo '<a class="' . ( is_shop() ? 'on' : '' ) . '" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">All</a>';
	foreach ( get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true ] ) as $c ) {
		if ( 'uncategorized' === $c->slug ) { continue; }
		echo '<a class="' . ( is_product_category( $c->slug ) ? 'on' : '' ) . '" href="' . esc_url( get_term_link( $c ) ) . '">' . esc_html( $c->name ) . '</a>';
	}
	echo '</div></div></section>';
}, 1 );

add_action( 'woocommerce_single_product_summary', function () {
	echo '<ul class="wt-trust"><li>' . wt_icon( 'truck' ) . 'Free UK delivery</li><li>' . wt_icon( 'shield' ) . '10 year guarantee</li><li>' . wt_icon( 'leaf' ) . 'FSC certified materials</li><li>' . wt_icon( 'star' ) . 'Rated 4.8/5</li></ul>';
}, 35 );

add_filter( 'loop_shop_columns', function () { return 4; } );

/* ---------------------------------------------------------------- JS */

function wt_design_js() {
	return <<<'JS'
(function(){
var d=document,reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
/* sticky header state */
var h=d.querySelector('.wth-header');
function onScroll(){if(h)h.classList.toggle('is-stuck',window.scrollY>40);var t=d.getElementById('wt-top');if(t)t.classList.toggle('show',window.scrollY>600);}
window.addEventListener('scroll',onScroll,{passive:true});onScroll();
/* mobile menu */
var b=d.getElementById('wt-burger'),nav=d.getElementById('wt-nav');
if(b&&nav){b.addEventListener('click',function(){var o=nav.classList.toggle('open');b.setAttribute('aria-expanded',o);b.setAttribute('aria-label',o?'Close menu':'Open menu');d.body.classList.toggle('wt-lock',o);});}
d.querySelectorAll('.wt-drop-t').forEach(function(t){t.addEventListener('click',function(){var li=t.parentNode,o=li.classList.toggle('open');t.setAttribute('aria-expanded',o);});});
d.addEventListener('keydown',function(e){if(e.key==='Escape'){if(nav&&nav.classList.contains('open'))b.click();d.querySelectorAll('.wt-spot.open').forEach(close);var s=d.getElementById('wt-search');if(s&&!s.hidden){s.hidden=true;st.setAttribute('aria-expanded','false');}}});
/* search */
var st=d.getElementById('wt-search-t'),sp=d.getElementById('wt-search');
if(st&&sp){st.addEventListener('click',function(){sp.hidden=!sp.hidden;st.setAttribute('aria-expanded',!sp.hidden);if(!sp.hidden)d.getElementById('wt-s').focus();});}
/* back to top */
var top=d.getElementById('wt-top');if(top)top.addEventListener('click',function(){window.scrollTo({top:0,behavior:reduce?'auto':'smooth'});});
/* smooth anchors */
d.querySelectorAll('a[href^="#"]').forEach(function(a){a.addEventListener('click',function(e){var id=a.getAttribute('href');if(id.length<2)return;var el=d.querySelector(id);if(!el)return;e.preventDefault();window.scrollTo({top:el.getBoundingClientRect().top+window.scrollY-90,behavior:reduce?'auto':'smooth'});});});
/* reveal (single elements, and groups whose children stagger in together) */
var inEditor=d.body.classList.contains('elementor-editor-active')||/[?&]elementor-preview=/.test(location.search);
if(!reduce&&!inEditor&&'IntersectionObserver' in window){
 d.documentElement.classList.add('wt-anim');
 var show=function(el){if(el.classList.contains('wt-reveal-group')){Array.prototype.forEach.call(el.children,function(c,i){c.style.transitionDelay=Math.min(i,6)*100+'ms';c.classList.add('in');});}else{el.classList.add('in');}};
 var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){show(x.target);io.unobserve(x.target);}});},{threshold:.08,rootMargin:'0px 0px -30px 0px'});
 d.querySelectorAll('.wt-reveal,.wt-reveal-group').forEach(function(el){io.observe(el);});
 window.addEventListener('beforeprint',function(){d.querySelectorAll('.wt-reveal,.wt-reveal-group').forEach(show);});
}
/* hotspots */
function close(s){s.dataset.pinned='';s.classList.remove('open');s.querySelector('button').setAttribute('aria-expanded','false');}
d.querySelectorAll('.wt-spot').forEach(function(s){var btn=s.querySelector('button');
 btn.addEventListener('click',function(e){e.stopPropagation();var o=!(s.classList.contains('open')&&s.dataset.pinned);d.querySelectorAll('.wt-spot.open').forEach(function(z){if(z!==s)close(z);});s.dataset.pinned=o?'1':'';if(!o){close(s);return;}if(o){s.classList.add('open');btn.setAttribute('aria-expanded','true');var tip=s.querySelector('.wt-tip');tip.style.transform='';var r=tip.getBoundingClientRect(),dx=0;if(r.right>window.innerWidth-12)dx=window.innerWidth-12-r.right;if(r.left<12)dx=12-r.left;if(dx)tip.style.transform='translateX(calc(-50% + '+dx+'px))';}});
 s.addEventListener('mouseenter',function(){if(window.matchMedia('(hover:hover)').matches){s.classList.add('open');btn.setAttribute('aria-expanded','true');}});
 s.addEventListener('mouseleave',function(){if(window.matchMedia('(hover:hover)').matches&&!s.dataset.pinned)close(s);});
});
d.addEventListener('click',function(){d.querySelectorAll('.wt-spot.open').forEach(close);});
/* carousels: arrows, progress line, keyboard, touch swipe, mouse drag, autoplay */
var ARW={l:'<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>',r:'<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>'};
d.querySelectorAll('[data-carousel],.wt-carousel').forEach(function(c){
 if(!c.querySelector('.wt-car-track'))return;
 if(!c.querySelector('.wt-car-btn')){var bp=d.createElement('button');bp.type='button';bp.className='wt-car-btn prev';bp.setAttribute('aria-label','Previous');bp.innerHTML=ARW.l;var bn=d.createElement('button');bn.type='button';bn.className='wt-car-btn next';bn.setAttribute('aria-label','Next');bn.innerHTML=ARW.r;c.appendChild(bp);c.appendChild(bn);}
 if(!c.querySelector('.wt-car-line')){var ln=d.createElement('div');ln.className='wt-car-line';ln.setAttribute('aria-hidden','true');ln.appendChild(d.createElement('span'));c.appendChild(ln);}
 var tr=c.querySelector('.wt-car-track'),line=c.querySelector('.wt-car-line'),bar=line&&line.querySelector('span'),prev=c.querySelector('.prev'),next=c.querySelector('.next'),n=tr.children.length,timer=null,paused=false,drag=null,moved=false;
 function gap(){return parseFloat(getComputedStyle(tr).columnGap)||0;}
 function step(){return n?tr.children[0].getBoundingClientRect().width+gap():tr.clientWidth;}
 function per(){return Math.max(1,Math.round((tr.clientWidth+gap())/step()));}
 function room(){return Math.max(0,tr.scrollWidth-tr.clientWidth);}
 function max(){return Math.max(0,Math.ceil(room()/step()-0.05));}
 function idx(){return tr.scrollLeft>=room()-4?max():Math.round(tr.scrollLeft/step());}
 function go(i){i=Math.max(0,Math.min(max(),i));tr.scrollTo({left:Math.min(i*step(),room()),behavior:reduce?'auto':'smooth'});}
 function sync(){var sw=tr.scrollWidth,cw=tr.clientWidth,f=sw>cw?cw/sw:1,pos=sw>cw?tr.scrollLeft/(sw-cw):0;if(bar){bar.style.width=(f*100)+'%';bar.style.transform='translateX('+(pos*(1/f-1)*100)+'%)';}prev.disabled=tr.scrollLeft<4;next.disabled=tr.scrollLeft>=sw-cw-4;c.classList.toggle('no-nav',max()<1);}
 prev.addEventListener('click',function(){go(idx()-1);restart();});
 next.addEventListener('click',function(){go(idx()+1);restart();});
 if(line){var ld=null;function lineTo(x,smooth){var r=line.getBoundingClientRect(),f=tr.clientWidth/tr.scrollWidth,tw=r.width*f,p=Math.max(0,Math.min(1,(x-r.left-tw/2)/(r.width-tw)));tr.scrollTo({left:p*(tr.scrollWidth-tr.clientWidth),behavior:smooth&&!reduce?'smooth':'auto'});}
  line.addEventListener('pointerdown',function(e){ld=true;paused=true;c.classList.add('dragging');line.setPointerCapture(e.pointerId);lineTo(e.clientX,false);e.preventDefault();});
  line.addEventListener('pointermove',function(e){if(ld)lineTo(e.clientX,false);});
  line.addEventListener('pointerup',function(){if(!ld)return;ld=null;c.classList.remove('dragging');paused=false;go(idx());restart();});}
 var raf;tr.addEventListener('scroll',function(){cancelAnimationFrame(raf);raf=requestAnimationFrame(sync);},{passive:true});
 tr.addEventListener('keydown',function(e){if(e.key==='ArrowRight'){e.preventDefault();go(idx()+1);}if(e.key==='ArrowLeft'){e.preventDefault();go(idx()-1);}});
 tr.addEventListener('pointerdown',function(e){if(e.pointerType!=='mouse'||e.button!==0)return;drag={x:e.clientX,l:tr.scrollLeft};moved=false;});
 window.addEventListener('pointermove',function(e){if(!drag)return;var dx=e.clientX-drag.x;if(Math.abs(dx)>5){moved=true;c.classList.add('dragging');}if(moved)tr.scrollLeft=drag.l-dx;});
 window.addEventListener('pointerup',function(){if(!drag)return;drag=null;c.classList.remove('dragging');if(moved)go(idx());restart();});
 tr.addEventListener('click',function(e){if(moved){e.preventDefault();e.stopPropagation();moved=false;}},true);
 tr.addEventListener('dragstart',function(e){e.preventDefault();});
 function tick(){if(paused||d.hidden)return;go(idx()>=max()?0:idx()+1);}
 function restart(){clearInterval(timer);if(!reduce&&max()>0)timer=setInterval(tick,5000);}
 c.addEventListener('mouseenter',function(){paused=true;});c.addEventListener('mouseleave',function(){paused=false;});
 c.addEventListener('focusin',function(){paused=true;});c.addEventListener('focusout',function(){paused=false;});
 tr.addEventListener('touchstart',function(){paused=true;},{passive:true});tr.addEventListener('touchend',function(){setTimeout(function(){paused=false;},4000);},{passive:true});
 if('IntersectionObserver' in window){new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting)restart();else clearInterval(timer);});}).observe(c);}else restart();
 var rt;window.addEventListener('resize',function(){clearTimeout(rt);rt=setTimeout(sync,150);});sync();
});
/* advanced button hover: fill grows from the cursor entry point, gentle magnetic pull */
var BTN='.wt-b .elementor-button,.wth-cta .elementor-button,.wt-atc,.wt-btn,.single_add_to_cart_button,.woocommerce ul.products li.product .button';
d.querySelectorAll(BTN).forEach(function(b){b.classList.add('wt-fx');});
function fxPos(b,e){var r=b.getBoundingClientRect();b.style.setProperty('--x',(e.clientX-r.left)+'px');b.style.setProperty('--y',(e.clientY-r.top)+'px');return r;}
d.addEventListener('pointerover',function(e){var b=e.target.closest&&e.target.closest('.wt-fx');if(b&&!b.contains(e.relatedTarget))fxPos(b,e);});
d.addEventListener('pointerout',function(e){var b=e.target.closest&&e.target.closest('.wt-fx');if(b&&!b.contains(e.relatedTarget)){fxPos(b,e);b.style.transform='';}});
if(!reduce&&window.matchMedia('(hover:hover)').matches){d.addEventListener('pointermove',function(e){var b=e.target.closest&&e.target.closest('.wt-fx');if(!b)return;var r=b.getBoundingClientRect(),mx=(e.clientX-r.left-r.width/2)/r.width,my=(e.clientY-r.top-r.height/2)/r.height;b.style.transform='translate('+(mx*6).toFixed(2)+'px,'+(my*5-2).toFixed(2)+'px)';},{passive:true});}
/* account panel: log in / sign up / reset password */
(function(){var A=d.getElementById('wta');if(!A)return;var panel=A.querySelector('.wta__panel'),msg=A.querySelector('.wta__msg'),last=null,view='login';
 function show(v,focus){view=v;A.setAttribute('data-view',v);A.querySelectorAll('.wta__tabs [role=tab]').forEach(function(t){t.setAttribute('aria-selected',t.getAttribute('data-view')===v?'true':'false');});
  A.querySelectorAll('[data-form]').forEach(function(f){f.classList.toggle('is-active',f.getAttribute('data-form')===v);});msg.textContent='';msg.className='wta__msg';
  if(focus!==false){var i=A.querySelector('[data-form="'+v+'"] input:not([type=checkbox]):not(.wta__hp)');if(i)setTimeout(function(){i.focus();},380);}}
 function open(v){last=d.activeElement;A.hidden=false;void A.offsetWidth;A.classList.add('is-open');d.body.classList.add('wt-lock');show(v||'login');}
 function close(){A.classList.remove('is-open');d.body.classList.remove('wt-lock');setTimeout(function(){A.hidden=true;},reduce?0:450);if(last&&last.focus)last.focus();}
 d.addEventListener('click',function(e){var a=e.target.closest&&e.target.closest('.wth-ic a, a[href*="/my-account"]:not(.wta *)');if(a&&!e.ctrlKey&&!e.metaKey&&!/lost-password/.test(a.href)){e.preventDefault();open('login');}});
 A.addEventListener('click',function(e){if(e.target.closest('[data-wta-close]'))return close();var v=e.target.closest('[data-view]');if(v){e.preventDefault();show(v.getAttribute('data-view'));}
  var eye=e.target.closest('.wta__eye');if(eye){var inp=eye.parentNode.querySelector('input');var on=inp.type==='password';inp.type=on?'text':'password';eye.classList.toggle('on',on);eye.setAttribute('aria-label',on?'Hide password':'Show password');}});
 d.addEventListener('keydown',function(e){if(!A.classList.contains('is-open'))return;if(e.key==='Escape')close();if(e.key==='Tab'){var f=panel.querySelectorAll('button:not([hidden]),input:not(.wta__hp),a');f=Array.prototype.filter.call(f,function(x){return x.offsetParent!==null;});if(!f.length)return;var a=f[0],z=f[f.length-1];if(e.shiftKey&&d.activeElement===a){z.focus();e.preventDefault();}else if(!e.shiftKey&&d.activeElement===z){a.focus();e.preventDefault();}}});
 var pw=A.querySelector('#wta-rpwd'),meter=A.querySelector('.wta__meter'),mt=A.querySelector('[data-meter-text]');
 if(pw)pw.addEventListener('input',function(){var v=pw.value,sc=0;if(v.length>=8)sc++;if(/[A-Z]/.test(v)&&/[a-z]/.test(v))sc++;if(/\d/.test(v))sc++;if(/[^A-Za-z0-9]/.test(v)||v.length>=12)sc++;meter.setAttribute('data-score',v?sc:0);mt.textContent=!v?'At least 8 characters, with a mix of letters and numbers.':['Too short','Weak','Okay','Good','Strong'][sc]+' password';});
 function fail(form,text){msg.textContent=text;msg.className='wta__msg is-err';form.classList.remove('shake');void form.offsetWidth;form.classList.add('shake');}
 A.querySelectorAll('form[data-form]').forEach(function(form){form.addEventListener('submit',function(e){e.preventDefault();var kind=form.getAttribute('data-form');
  var bad=null;form.querySelectorAll('input[required]').forEach(function(i){i.closest('.wta__f')&&i.closest('.wta__f').classList.remove('is-bad');if(!bad&&(!i.value.trim()||(i.type==='email'&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(i.value))||(i.minLength>0&&i.value.length<i.minLength)))bad=i;});
  if(bad){bad.closest('.wta__f').classList.add('is-bad');bad.focus();return fail(form,bad.minLength>0&&bad.value?'Your password needs at least 8 characters.':(bad.type==='email'?'Please enter a valid email address.':'Please fill in this field.'));}
  var btn=form.querySelector('.wta__btn');btn.classList.add('is-loading');btn.disabled=true;msg.textContent='';msg.className='wta__msg';
  var fd=new FormData(form);fd.append('action','wt_account');fd.append('do',kind);fd.append('nonce',WT.acc);
  fetch(WT.ajax,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(j){
   if(!j||!j.success)throw new Error(j&&j.data&&j.data.message||'Something went wrong. Please try again.');
   btn.classList.remove('is-loading');btn.classList.add('is-done');
   if(kind==='lost'){A.querySelector('[data-done-text]').textContent=j.data.message;show('done',false);btn.classList.remove('is-done');btn.disabled=false;form.reset();return;}
   msg.textContent=j.data.message;msg.className='wta__msg is-ok';setTimeout(function(){location.href=j.data.redirect||location.href;},900);
  }).catch(function(err){btn.classList.remove('is-loading');btn.disabled=false;fail(form,err.message);});});});
 window.wtAccount={open:open,close:close};
})();
/* add to cart feedback (WooCommerce triggers jQuery events) */
if(window.jQuery){jQuery(d.body).on('adding_to_cart',function(e,btn){btn&&btn.addClass&&btn.addClass('is-loading').text('Adding…');}).on('added_to_cart',function(e,f,h,btn){if(btn&&btn.removeClass){btn.removeClass('is-loading').addClass('is-done').text('Added ✓');setTimeout(function(){btn.removeClass('is-done').text('Add to basket');},2200);}}).on('ajax_request_not_sent.adding_to_cart',function(e,btn){btn&&btn.removeClass&&btn.removeClass('is-loading').text('Add to basket');});
 jQuery(d).ajaxError(function(e,x,s){if(s&&s.url&&s.url.indexOf('add_to_cart')>-1){jQuery('.wt-atc.is-loading').removeClass('is-loading').addClass('is-error').text('Try again');}});}
/* newsletter */
d.querySelectorAll('.wt-news').forEach(function(f){var m=f.querySelector('.wt-news__msg');f.addEventListener('submit',function(e){e.preventDefault();var em=f.email.value.trim();if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)){m.textContent='Please enter a valid email address.';m.className='wt-news__msg err';f.email.focus();return;}
 var fd=new FormData(f);fd.append('action','wt_newsletter');fd.append('nonce',WT.nonce);f.classList.add('busy');
 fetch(WT.ajax,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(j){m.textContent=(j.data&&j.data.message)||'Something went wrong.';m.className='wt-news__msg '+(j.success?'ok':'err');if(j.success)f.reset();}).catch(function(){m.textContent='Network error, please try again.';m.className='wt-news__msg err';}).finally(function(){f.classList.remove('busy');});});});
})();
JS;
}

/* ---------------------------------------------------------------- CSS */

function wt_design_css() {
	return <<<'CSS'
:root{--g:#1D4032;--g2:#19392D;--g3:#153528;--bg:#FFFFFF;--off:#F8F7F3;--sage:#F1F4EE;--beige:#F2EEE7;--tx:#1D211E;--tx2:#666B66;--ln:#E5E8E2;--star:#E5AC32;--serif:'Barlow',system-ui,sans-serif;--sans:'Barlow',system-ui,-apple-system,sans-serif;--ease:cubic-bezier(.22,1,.36,1);--r:10px}
body{background:var(--bg)!important;color:var(--tx);font-family:var(--sans)!important;-webkit-font-smoothing:antialiased}
body.wt-lock{overflow:hidden}
html,body{overflow-x:clip}
.wp-site-blocks>header,.wp-site-blocks>footer,body>.wp-site-blocks>header.wp-block-template-part,body>.wp-site-blocks>footer.wp-block-template-part{display:none!important}
.screen-reader-text{position:absolute!important;width:1px;height:1px;overflow:hidden;clip:rect(1px,1px,1px,1px);white-space:nowrap}
.wt-skip{position:absolute;left:-999px;top:8px;z-index:10000;background:#fff;padding:10px 16px;border-radius:6px}.wt-skip:focus{left:12px}
:focus-visible{outline:2px solid var(--g);outline-offset:3px}
.wt-in{max-width:1320px;margin:0 auto;padding:0 32px;width:100%;box-sizing:border-box}
.wt-ic{width:20px;height:20px;flex:none}
.wt-stars{color:var(--star);letter-spacing:1px}.wt-stars .off{color:#D9DCD6}
.wt-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;height:44px;padding:0 22px;border-radius:6px;background:var(--g);color:#fff!important;font:600 13px/1 var(--sans);text-decoration:none!important;border:1px solid var(--g);transition:background .25s var(--ease),transform .25s var(--ease),border-color .25s}
.wt-btn:hover{background:var(--g3);transform:translateY(-2px)}
/* announcement */
.wt-announce{background:#2E5141;color:#fff;font-size:12px}
.wt-announce__in{display:flex;justify-content:space-between;align-items:center;height:36px;gap:16px}
.wt-announce p{margin:0;display:flex;align-items:center;gap:10px;white-space:nowrap}
.wt-announce .wt-ic{width:16px;height:16px;opacity:.85}.wt-announce span{opacity:.6}.wt-announce .wt-stars{opacity:1;color:var(--star)}
/* header */
.wt-header{position:sticky;top:0;z-index:1000;background:#fff;border-bottom:1px solid var(--ln);transition:box-shadow .3s var(--ease)}
.wt-header.is-stuck{box-shadow:0 8px 24px -16px rgba(21,53,40,.35)}
.wt-header__in{display:flex;align-items:center;height:76px;gap:48px;transition:height .3s var(--ease)}
.wt-header.is-stuck .wt-header__in{height:64px}
.wt-logo{font:600 17px/1 var(--sans);letter-spacing:.28em;color:var(--tx)!important;text-decoration:none;white-space:nowrap}.wt-logo sup{font-size:9px;letter-spacing:0}
.wt-nav{flex:1;display:flex;align-items:center}
.wt-nav ul{list-style:none;margin:0;padding:0;display:flex;gap:34px}
.wt-nav li{position:relative}
.wt-nav li>a{font-size:13.5px;font-weight:500;color:var(--tx);text-decoration:none;padding:28px 0;display:inline-block;transition:color .2s}
.wt-nav li>a:hover{color:var(--g)}
.wt-drop-t{display:none}
.wt-drop{position:absolute;top:100%;left:-18px;min-width:230px;background:#fff;border:1px solid var(--ln);border-radius:var(--r);padding:8px;list-style:none;box-shadow:0 20px 40px -20px rgba(21,53,40,.3);opacity:0;visibility:hidden;transform:translateY(6px);transition:.25s var(--ease);display:block!important}
.wt-nav li:hover>.wt-drop,.wt-nav li:focus-within>.wt-drop{opacity:1;visibility:visible;transform:none}
.wt-drop a{display:block;padding:10px 12px;border-radius:6px;font-size:13.5px;color:var(--tx);text-decoration:none}.wt-drop a:hover{background:var(--sage);color:var(--g)}
.wt-nav__cta{display:none!important}
.wt-tools{display:flex;align-items:center;gap:6px}
.wt-tool{display:inline-grid;place-items:center;width:38px;height:38px;border:0;background:none;color:var(--tx)!important;cursor:pointer;border-radius:50%;position:relative;transition:background .2s}
.wt-tool:hover{background:var(--sage)}
.wt-count{position:absolute;top:2px;right:0;background:var(--g);color:#fff;font-size:10px;font-weight:600;min-width:16px;height:16px;border-radius:8px;display:grid;place-items:center;padding:0 3px}
.wt-header__cta{margin-left:14px}
.wt-burger{display:none}
.wt-search{border-top:1px solid var(--ln);background:#fff;padding:16px 0}
.wt-search form{display:flex;gap:10px}.wt-search input[type=search]{flex:1;height:44px;border:1px solid var(--ln);border-radius:6px;padding:0 14px;font:inherit}
/* footer */
.wt-footer{background:var(--g3);color:rgba(255,255,255,.72);padding:64px 0 24px;font-size:13px}
.wt-footer__grid{display:grid;grid-template-columns:1.5fr .8fr .9fr .8fr 1.6fr;gap:40px}
.wt-footer .wt-logo{color:#fff!important}
.wt-footer p{line-height:1.7;margin:18px 0}
.wt-footer h2{font:600 13px/1 var(--sans)!important;color:#fff!important;margin:0 0 18px;letter-spacing:0}
.wt-footer__col a{display:block;color:rgba(255,255,255,.72);text-decoration:none;padding:5px 0;transition:color .2s}.wt-footer__col a:hover{color:#fff}
.wt-social{display:flex;gap:14px}.wt-social a{color:#fff;opacity:.8}.wt-social a:hover{opacity:1}.wt-social .wt-ic{width:18px;height:18px}
.wt-footer__news p{margin:0 0 16px}
.wt-news{position:relative;max-width:340px}
.wt-news input[type=email]{width:100%;height:46px;border-radius:999px;border:0;padding:0 56px 0 20px;font:13px var(--sans);box-sizing:border-box;color:var(--tx)}
.wt-news button{position:absolute;right:5px;top:5px;width:36px;height:36px;border-radius:50%;border:1px solid var(--ln);background:#fff;color:var(--g);display:grid;place-items:center;cursor:pointer;transition:transform .2s var(--ease)}
.wt-news button:hover{transform:translateX(2px)}.wt-news button .wt-ic{width:16px;height:16px}
.wt-news.busy button{opacity:.5;pointer-events:none}
.wt-news__msg{font-size:12px;margin:10px 4px 0!important;min-height:16px}.wt-news__msg.ok{color:#BFE3CB}.wt-news__msg.err{color:#FFC2B3}
.wt-hp{position:absolute;left:-9999px}
.wt-footer__base{display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(255,255,255,.12);margin-top:48px;padding-top:20px;font-size:12px}
.wt-footer__base p{margin:0}.wt-footer__base nav{display:flex;align-items:center;gap:28px}.wt-footer__base a{color:rgba(255,255,255,.72);text-decoration:none}.wt-footer__base a:hover{color:#fff}
.wt-top{width:40px;height:40px;border-radius:50%;background:#fff;color:var(--g);border:0;display:grid;place-items:center;cursor:pointer;transition:transform .25s var(--ease)}.wt-top:hover{transform:translateY(-3px)}.wt-top .wt-ic{width:18px;height:18px}
/* ---------- homepage (Elementor containers) ---------- */
.elementor .wt-sec{padding:96px 0}
.elementor .wt-wrap{max-width:1320px;margin:0 auto;padding-left:32px!important;padding-right:32px!important;width:100%;box-sizing:border-box}
.wt-label .elementor-heading-title,.wt-label{font:600 11px/1.2 var(--sans)!important;letter-spacing:.18em!important;text-transform:uppercase;color:var(--tx2)!important}
.wt-h1 .elementor-heading-title{font:500 clamp(40px,4.4vw,62px)/1.05 var(--serif)!important;color:var(--tx)!important;letter-spacing:-.01em}
.wt-h2 .elementor-heading-title{font:500 clamp(30px,3vw,42px)/1.1 var(--serif)!important;color:var(--tx)!important}
.wt-p,.wt-p p{font:400 16px/1.65 var(--sans)!important;color:var(--tx2)!important}
.wt-link a,.wt-link .elementor-button{white-space:nowrap;background:none!important;padding:0!important;color:var(--tx)!important;font:600 13px/1 var(--sans)!important;border:0!important}
.wt-link .elementor-button-icon{transition:transform .25s var(--ease)}.wt-link a:hover .elementor-button-icon{transform:translateX(4px)}
.wt-b .elementor-button{height:48px;padding:0 26px!important;border-radius:6px!important;font:600 13.5px/1 var(--sans)!important;background:var(--g)!important;color:#fff!important;border:1px solid var(--g)!important;transition:background .25s var(--ease),transform .25s var(--ease)!important}
.wt-b .elementor-button:hover{background:var(--g3)!important;transform:translateY(-2px)}
.wt-b--o .elementor-button{background:#fff!important;color:var(--tx)!important;border-color:var(--tx)!important}.wt-b--o .elementor-button:hover{background:var(--off)!important}
.wt-b .elementor-button-icon{transition:transform .25s var(--ease)}.wt-b .elementor-button:hover .elementor-button-icon{transform:translateX(4px)}
.wt-head{display:flex!important;flex-direction:row!important;justify-content:space-between!important;align-items:flex-end!important;gap:20px;margin-bottom:28px}
.wt-head>.e-con{gap:10px}
.wt-imgbox{border-radius:var(--r);overflow:hidden}
.wt-imgbox img{width:100%!important;height:100%!important;object-fit:cover;display:block;transition:transform .6s var(--ease)}
/* hero */
.wt-hero{background:var(--off);padding:0!important;display:grid!important;grid-template-columns:44% 56%;min-height:600px;gap:0!important}
.wt-hero__txt{padding:90px 60px 90px max(32px,calc((100vw - 1320px)/2 + 32px))!important;justify-content:center!important;gap:18px!important}
.wt-hero__txt .wt-p{max-width:430px}
.wt-hero__btns{flex-direction:row!important;gap:14px!important;margin-top:10px}
.wt-hero__img{position:relative;padding:0!important;overflow:hidden}
.wt-hero__img .elementor-widget-image,.wt-hero__img .elementor-widget-image>div{height:100%}
.wt-hero__img img{width:100%!important;height:100%!important;object-fit:cover;object-position:center;display:block;animation:wtHero 1.4s var(--ease) both}
.wt-note{position:absolute!important;top:12%;right:5%;width:auto!important;z-index:2}
.wt-note .wt-note__t{font:500 26px/1.05 'Caveat',cursive;color:#2E3A33;transform:rotate(-6deg);display:block;text-align:right}
.wt-note svg{width:70px;height:46px;color:#2E3A33;display:block;margin:0 0 4px -30px}
@keyframes wtHero{from{opacity:0;transform:scale(1.06)}to{opacity:1;transform:none}}
.wt-hero__txt>*{animation:wtUp .9s var(--ease) both}.wt-hero__txt>*:nth-child(2){animation-delay:.08s}.wt-hero__txt>*:nth-child(3){animation-delay:.2s}.wt-hero__txt>*:nth-child(4){animation-delay:.32s}
@keyframes wtUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
/* trust */
.wt-trustbar{background:var(--off);border-top:1px solid var(--ln);padding:30px 0!important}
.wt-trustbar>.wt-wrap{display:grid!important;grid-template-columns:repeat(4,1fr);gap:24px}
.wt-ib .elementor-icon-box-wrapper{display:flex!important;align-items:center;gap:16px;text-align:left!important}
.wt-ib .elementor-icon-box-icon{margin:0!important}
.wt-ib .elementor-icon{font-size:22px!important;color:var(--g)!important;fill:var(--g)!important}
.wt-ib .elementor-icon svg{width:24px;height:24px;stroke:var(--g)}
.wt-ib .elementor-icon-box-title{font:600 13px/1.3 var(--sans)!important;color:var(--tx)!important;margin:0 0 3px!important}
.wt-ib .elementor-icon-box-description{font:400 12px/1.4 var(--sans)!important;color:var(--tx2)!important;margin:0!important}
/* categories */
.wt-cats{display:grid!important;grid-template-columns:repeat(4,1fr);gap:20px!important}
.wt-cat{position:relative;aspect-ratio:3/4;border-radius:var(--r);overflow:hidden;padding:0!important;cursor:pointer}
.wt-cat .elementor-widget-image{position:absolute!important;inset:0;width:100%!important;height:100%}
.wt-cat .elementor-widget-image>div,.wt-cat .elementor-widget-image a{display:block;height:100%}
.wt-cat img{width:100%!important;height:100%!important;object-fit:cover;transition:transform .6s var(--ease)}
.wt-cat:hover img{transform:scale(1.04)}
.wt-cat:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 55%,rgba(10,20,15,.62));pointer-events:none}
.wt-cat__t{position:absolute!important;left:22px;bottom:22px;z-index:2;width:auto!important;gap:6px!important;padding:0!important}
.wt-cat__t .elementor-heading-title{font:500 21px/1.15 var(--serif)!important;color:#fff!important}
.wt-cat__t .elementor-heading-title a:after{content:"";position:absolute;inset:-600px -400px -40px -40px}
.wt-cat__t .wt-link a,.wt-cat__t .wt-link .elementor-button{color:#fff!important;font-size:12px!important}
/* design own */
.wt-design{display:grid!important;grid-template-columns:56% 44%;padding:0!important;background:#fff;gap:0!important}
.wt-design__img{padding:0!important;position:relative}
.wt-hot{position:relative;height:100%;min-height:560px}
.wt-hot__img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.wt-spot{position:absolute;transform:translate(-50%,-50%);display:flex;align-items:center;gap:8px;z-index:3}
.wt-spot__b{width:26px;height:26px;border-radius:50%;background:#fff;border:0;color:var(--g);font:600 18px/1 var(--sans);display:grid;place-items:center;cursor:pointer;box-shadow:0 0 0 0 rgba(255,255,255,.7);animation:wtPulse 2.6s infinite}
.wt-spot__lbl{background:#fff;font:500 11px/1 var(--sans);padding:7px 12px;border-radius:999px;color:var(--tx);white-space:nowrap;box-shadow:0 6px 16px -8px rgba(0,0,0,.3)}
.wt-tip{position:absolute;bottom:calc(100% + 12px);left:50%;transform:translateX(-50%);width:220px;background:#fff;border-radius:10px;padding:14px 16px;font:400 12.5px/1.5 var(--sans);color:var(--tx2);box-shadow:0 18px 36px -14px rgba(0,0,0,.35);opacity:0;visibility:hidden;transition:opacity .25s var(--ease),margin .25s var(--ease);margin-bottom:-6px;pointer-events:none}
.wt-tip b{display:block;color:var(--tx);font-weight:600;margin-bottom:4px}
.wt-spot.open .wt-tip{opacity:1;visibility:visible;margin-bottom:0}
.wt-spot.open .wt-spot__b{animation:none;background:var(--g);color:#fff}
@keyframes wtPulse{0%{box-shadow:0 0 0 0 rgba(255,255,255,.75)}70%{box-shadow:0 0 0 12px rgba(255,255,255,0)}100%{box-shadow:0 0 0 0 rgba(255,255,255,0)}}
.wt-design__txt{padding:80px max(32px,calc((100vw - 1320px)/2 + 32px)) 80px 64px!important;justify-content:center!important;gap:18px!important;position:relative;overflow:hidden}
.wt-feat .elementor-icon-box-icon .elementor-icon{width:44px;height:44px;border:1px solid var(--ln);border-radius:50%;display:grid!important;place-items:center;font-size:16px!important;color:var(--g)!important}
.wt-feats{gap:16px!important;margin:8px 0 10px}
.wt-design__btns{flex-direction:row!important;align-items:center!important;gap:30px!important}
/* steps */
.wt-steps-sec{background:var(--off)}
.wt-center{text-align:center;align-items:center!important}
.wt-steps{display:grid!important;grid-template-columns:1fr 1fr 1fr;gap:56px!important;margin-top:34px;position:relative}
.wt-step{background:#fff;border:1px solid var(--ln);border-radius:var(--r);padding:12px 12px 26px!important;gap:10px!important;position:relative;transition:transform .35s var(--ease),box-shadow .35s var(--ease)}
.wt-step:hover{transform:translateY(-4px);box-shadow:0 20px 40px -24px rgba(21,53,40,.35)}
.wt-step:not(:last-child):after{content:"→";position:absolute;right:-42px;top:42%;color:var(--tx2);font-size:20px}
.wt-step .elementor-widget-image{border-radius:8px;overflow:hidden}
.wt-step img{width:100%!important;aspect-ratio:16/9;object-fit:cover}
.wt-num{position:absolute!important;top:22px;left:22px;z-index:2;width:auto!important}
.wt-num .elementor-heading-title{width:32px;height:32px;border-radius:50%;background:var(--g);color:#fff!important;font:600 13px/32px var(--sans)!important;text-align:center}
.wt-step h3,.wt-step .elementor-heading-title{font:600 16px/1.3 var(--sans)!important;color:var(--tx)!important}
.wt-step>.elementor-widget-heading,.wt-step>.elementor-widget-text-editor{padding:0 10px}
.wt-step .wt-p p{font-size:13.5px!important;margin:0}
/* products */
.wt-carousel{position:relative}
.wt-car-track{position:relative;display:grid;grid-auto-flow:column;grid-auto-columns:calc((100% - 72px)/4);gap:24px;overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;padding:4px 2px 8px;cursor:grab;overscroll-behavior-x:contain}
.wt-carousel.dragging .wt-car-track{scroll-snap-type:none;cursor:grabbing}.wt-carousel.dragging a{pointer-events:none}
.wt-car-track::-webkit-scrollbar{display:none}
.wt-pcard{position:relative;scroll-snap-align:start;background:#fff;border:1px solid var(--ln);border-radius:var(--r);overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .35s var(--ease),transform .35s var(--ease)}
.wt-pcard:hover{box-shadow:0 20px 40px -24px rgba(21,53,40,.35);transform:translateY(-3px)}
.wt-pcard__img{display:block;overflow:hidden;background:var(--beige);aspect-ratio:1/0.82}
.wt-pcard__img img{width:100%;height:100%;object-fit:cover;transition:transform .6s var(--ease)}
.wt-pcard:hover .wt-pcard__img img{transform:scale(1.04)}
.wt-pcard__b{padding:16px 16px 18px;display:flex;flex-direction:column;gap:6px;flex:1}
.wt-pcard h3{font:600 14px/1.3 var(--sans)!important;margin:0}.wt-pcard h3 a{color:var(--tx);text-decoration:none}
.wt-pcard__r{font-size:12px;color:var(--tx2);display:flex;gap:6px;align-items:center}
.wt-pcard__p{display:flex;align-items:baseline;gap:8px}.wt-pcard__p .price,.wt-pcard__p{font:600 17px/1 var(--sans);color:var(--tx)}
.wt-pcard__p del{order:2;font-size:12px;color:var(--tx2);font-weight:400}.wt-pcard__p ins{text-decoration:none}
.wt-atc{margin-top:auto!important;display:flex!important;align-items:center;justify-content:center;height:40px;border-radius:6px!important;background:var(--g)!important;color:#fff!important;font:600 12.5px/1 var(--sans)!important;text-decoration:none!important;border:0!important;transition:background .25s var(--ease)!important;padding:0!important}
.wt-atc:hover{background:var(--g3)!important}
.wt-atc.is-loading{opacity:.7;pointer-events:none}.wt-atc.is-done{background:#2F6B4F!important}.wt-atc.is-error{background:#9B3B2A!important}
.wt-atc.loading:after,.wt-atc.added:after{display:none!important}
.added_to_cart.wc-forward{display:block;text-align:center;font-size:12px;color:var(--g);margin-top:6px}
.wt-car-btn{position:absolute;top:36%;z-index:3;width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid var(--ln);display:grid;place-items:center;cursor:pointer;color:var(--tx);transition:.25s var(--ease);box-shadow:0 6px 16px -10px rgba(0,0,0,.3)}
.wt-car-btn.prev{left:-56px}.wt-car-btn.next{right:-56px}.wt-car-btn:hover{background:var(--g);color:#fff}.wt-car-btn:disabled{opacity:.35;pointer-events:none}

.wt-car-dots button{width:7px;height:7px;border-radius:50%;border:0;background:#CFD5CD;padding:0;cursor:pointer;transition:.25s}.wt-car-dots button.on{background:var(--g);width:20px;border-radius:4px}
.wt-carousel.no-nav .wt-car-btn,.wt-carousel.no-nav .wt-car-dots{display:none}
/* why */
.wt-why{background:var(--sage);padding:72px 0!important}
.wt-why-grid{display:grid!important;grid-template-columns:repeat(4,1fr);gap:24px;margin-top:36px}
.wt-why .wt-ib .elementor-icon{font-size:28px!important}
/* utility */
.wt-util{display:grid!important;grid-template-columns:44% 56%;padding:0!important;gap:0!important;background:#fff}
.wt-util__txt{padding:90px 64px 90px max(32px,calc((100vw - 1320px)/2 + 32px))!important;justify-content:center!important;gap:18px!important}
.wt-util__img{position:relative;padding:0!important;overflow:hidden;min-height:520px}
.wt-util__img .elementor-widget-image{position:absolute!important;inset:0;height:100%}
.wt-util__img .elementor-widget-image>div{height:100%}
.wt-util__img img{width:100%!important;height:100%!important;object-fit:cover;clip-path:inset(0 0 0 0);transition:clip-path 1.2s var(--ease)}
html.wt-anim .wt-util__img:not(.in) img{clip-path:inset(0 0 0 18%)}
.wt-cap{position:absolute!important;left:12%;bottom:12%;width:auto!important;z-index:2;background:#fff;border-radius:999px;padding:12px 22px!important;box-shadow:0 14px 30px -14px rgba(0,0,0,.35)}
.wt-cap p{margin:0;font:500 12px/1.4 var(--sans)!important;color:var(--tx)!important}
/* reviews */
.wt-reviews:not(.wt-car-track){display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.wt-rcard{margin:0;display:grid;grid-template-columns:1fr 112px;gap:16px;background:#fff;border:1px solid var(--ln);border-radius:var(--r);padding:18px}
.wt-rcard blockquote{margin:10px 0 16px;font:400 13px/1.6 var(--sans);color:var(--tx2);border:0;padding:0}
.wt-rcard figcaption{display:flex;align-items:center;gap:10px}.wt-rcard figcaption img,.wt-av{width:36px;height:36px;border-radius:50%;object-fit:cover}
.wt-av{display:grid;place-items:center;background:var(--sage);color:var(--g);font-weight:600}
.wt-rcard figcaption b{display:block;font-size:13px}.wt-rcard figcaption small{font-size:11px;color:var(--tx2)}
.wt-rcard__img{width:112px!important;height:100%!important;min-height:120px;object-fit:cover;border-radius:8px}
.wt-admin-note{background:#FFF7E0;border:1px dashed #E5AC32;border-radius:8px;padding:10px 14px;font-size:12px;color:#6A5414;margin:0 0 16px}
.wt-reviews-empty{color:var(--tx2)}
/* blog */
.wt-blog{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.wt-bcard{display:grid;grid-template-columns:42% 1fr;gap:18px;background:#fff;border:1px solid var(--ln);border-radius:var(--r);padding:12px;transition:box-shadow .35s var(--ease),transform .35s var(--ease)}
.wt-bcard:hover{box-shadow:0 20px 40px -24px rgba(21,53,40,.35);transform:translateY(-3px)}
.wt-bcard__img{border-radius:8px;overflow:hidden;display:block}.wt-bcard__img img{width:100%;height:100%;min-height:150px;object-fit:cover;transition:transform .6s var(--ease)}
.wt-bcard:hover .wt-bcard__img img{transform:scale(1.04)}
.wt-bcard__b{display:flex;flex-direction:column;gap:8px;padding:6px 6px 6px 0}
.wt-badge{align-self:flex-start;background:var(--beige);font:600 9.5px/1 var(--sans);letter-spacing:.12em;text-transform:uppercase;padding:6px 9px;border-radius:4px;color:var(--tx2)}
.wt-bcard h3{font:600 15px/1.35 var(--sans)!important;margin:0}.wt-bcard h3 a{color:var(--tx);text-decoration:none}.wt-bcard h3 a:hover{color:var(--g)}
.wt-bcard p{font-size:12.5px;line-height:1.55;color:var(--tx2);margin:0}
.wt-more{margin-top:auto;font:600 12px/1 var(--sans);color:var(--tx);text-decoration:none}
/* reveal */
html.wt-anim .wt-reveal,html.wt-anim .wt-reveal-group>*{opacity:0;transform:translateY(26px);transition:opacity .75s var(--ease),transform .75s var(--ease)}
html.wt-anim .wt-reveal.in,html.wt-anim .wt-reveal-group>.in{opacity:1;transform:none}
@media (prefers-reduced-motion:reduce){*,*:before,*:after{animation:none!important;transition:none!important;scroll-behavior:auto!important}}
/* ---------- shop / product ---------- */
.wt-archive-hero{background:var(--off);padding:64px 0 40px;margin-bottom:40px}
.wt-archive-hero h1{font:500 clamp(36px,4vw,56px)/1.05 var(--serif)!important;margin:12px 0}
.wt-archive-hero p{color:var(--tx2);max-width:560px}
.wt-chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:22px}
.wt-chips a{padding:9px 16px;border:1px solid var(--ln);border-radius:6px;background:#fff;color:var(--tx);text-decoration:none;font-size:13px;font-weight:500}
.wt-chips a.on,.wt-chips a:hover{background:var(--g);border-color:var(--g);color:#fff}
.woocommerce-products-header{display:none}
.woocommerce ul.products{display:grid!important;grid-template-columns:repeat(4,1fr)!important;gap:24px!important}
.woocommerce ul.products:before,.woocommerce ul.products:after{display:none!important}
.woocommerce ul.products li.product{width:auto!important;margin:0!important;float:none!important;border:1px solid var(--ln);border-radius:var(--r);padding:0 0 16px!important;overflow:hidden;background:#fff;transition:.35s var(--ease)}
.woocommerce ul.products li.product:hover{box-shadow:0 20px 40px -24px rgba(21,53,40,.35);transform:translateY(-3px)}
.woocommerce ul.products li.product img{aspect-ratio:1/.82;object-fit:cover;margin:0 0 14px!important;background:var(--beige)}
.woocommerce ul.products li.product .woocommerce-loop-product__title{font:600 14px/1.3 var(--sans)!important;padding:0 16px!important;color:var(--tx)}
.woocommerce ul.products li.product .price{padding:0 16px;color:var(--tx)!important;font-weight:600}
.woocommerce ul.products li.product .price del{font-size:12px;color:var(--tx2)}
.woocommerce ul.products li.product .button{display:block;margin:12px 16px 0!important;background:var(--g)!important;color:#fff!important;border-radius:6px!important;text-align:center;font-size:12.5px!important}
.woocommerce span.onsale{background:var(--g)!important;border-radius:4px!important;min-height:0!important;line-height:1!important;padding:6px 9px!important;font-size:11px!important;top:12px!important;left:12px!important;right:auto!important;margin:0!important}
.single-product div.product{display:grid;grid-template-columns:1.1fr 1fr;gap:56px;margin-top:40px}
.single-product div.product .woocommerce-product-gallery,.single-product div.product .summary{width:auto!important;float:none!important}
.single-product .woocommerce-product-gallery__image img{border-radius:var(--r)}
.single-product .flex-control-thumbs{display:grid!important;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:10px!important}
.single-product .flex-control-thumbs li{width:auto!important}.single-product .flex-control-thumbs img{border-radius:8px;opacity:.6}.single-product .flex-control-thumbs img.flex-active{opacity:1}
.single-product .summary{position:sticky;top:100px;align-self:start}
.single-product .product_title{font:500 44px/1.05 var(--serif)!important}
.single-product .summary .price{font-size:26px!important;color:var(--tx)!important;font-weight:600}
.single-product form.cart{display:flex;gap:10px}
.single-product form.cart .qty{height:50px;border:1px solid var(--ln);border-radius:6px}
.single-product .single_add_to_cart_button{flex:1;height:50px;background:var(--g)!important;border-radius:6px!important}
.wt-trust{list-style:none;padding:0;margin:20px 0;display:grid;grid-template-columns:1fr 1fr;gap:10px}
.wt-trust li{display:flex;align-items:center;gap:10px;background:var(--sage);border-radius:8px;padding:12px 14px;font-size:13px;font-weight:500}.wt-trust .wt-ic{color:var(--g)}
.woocommerce-tabs,.related.products,.upsells{grid-column:1/-1}
.related.products>h2{font:500 36px/1.1 var(--serif)!important;margin:56px 0 22px}
/* ---------- responsive ---------- */
@media (max-width:1200px){.wt-header__in{gap:28px}.wt-nav ul{gap:22px}.wt-car-btn.prev{left:-12px}.wt-car-btn.next{right:-12px}.wt-blog,.wt-reviews:not(.wt-car-track){grid-template-columns:1fr 1fr}.wt-footer__grid{grid-template-columns:1fr 1fr 1fr}.wt-footer__brand,.wt-footer__news{grid-column:span 3}}
@media (max-width:1024px){
 .wt-burger{display:inline-grid}.wt-header__cta{display:none!important}
 .wt-nav{position:fixed;inset:0 0 0 auto;top:0;width:min(380px,100%);background:#fff;flex-direction:column;align-items:stretch;padding:90px 24px 24px;transform:translateX(100%);transition:transform .4s var(--ease);box-shadow:-20px 0 40px -20px rgba(0,0,0,.3);z-index:-1;overflow-y:auto;visibility:hidden}
 .wt-nav.open{transform:none;visibility:visible}
 .wt-nav ul{flex-direction:column;gap:0}
 .wt-nav li{border-bottom:1px solid var(--ln);display:flex;flex-wrap:wrap;align-items:center}
 .wt-nav li>a{flex:1;padding:16px 0;font-size:16px}
 .wt-drop-t{display:block;width:44px;height:44px;border:0;background:none;cursor:pointer;position:relative}
 .wt-drop-t:before{content:"+";font-size:22px;color:var(--g)}.has-drop.open .wt-drop-t:before{content:"–"}
 .wt-drop{position:static;opacity:1;visibility:visible;transform:none;box-shadow:none;border:0;width:100%;padding:0 0 10px;display:none!important}
 .has-drop.open .wt-drop{display:block!important}
 .wt-nav__cta{display:inline-flex!important;margin-top:24px}
 .wt-burger{z-index:2}
 .wt-hero,.wt-design,.wt-util{grid-template-columns:1fr}
 .wt-hero__txt,.wt-design__txt,.wt-util__txt{padding:56px 32px!important}
 .wt-hero__img{min-height:440px}
 .wt-util__img{min-height:420px;order:-1}
 .wt-trustbar>.wt-wrap,.wt-why-grid{grid-template-columns:1fr 1fr}
 .wt-cats{grid-template-columns:1fr 1fr}
 .wt-steps{gap:24px!important}.wt-step:after{display:none}
 .wt-car-track{grid-auto-columns:calc((100% - 24px)/2)}
 .woocommerce ul.products{grid-template-columns:repeat(2,1fr)!important}
 .single-product div.product{grid-template-columns:1fr}.single-product .summary{position:static}
}
@media (max-width:767px){
 .wt-in,.elementor .wt-wrap{padding-left:16px!important;padding-right:16px!important}
 .wt-announce__r{display:none}.wt-announce__in{justify-content:center}.wt-announce__l{font-size:11px}.wt-announce__l span,.wt-announce__l{overflow:hidden;text-overflow:ellipsis}
 .wt-header__in{height:64px;gap:12px}.wt-logo{font-size:14px;letter-spacing:.22em}.wt-tools{margin-left:auto}.wt-tool{width:34px;height:34px}
 .elementor .wt-sec{padding:64px 0}
 .wt-hero__txt,.wt-design__txt,.wt-util__txt{padding:44px 16px!important}
 .wt-hero__btns,.wt-design__btns{flex-direction:column!important;align-items:stretch!important;gap:12px!important}
 .wt-hero__btns .elementor-button,.wt-util__txt .wt-b .elementor-button{width:100%}
 .wt-hero__img{min-height:340px}.wt-note .wt-note__t{font-size:20px}.wt-note svg{width:50px}
 .wt-head{flex-direction:column!important;align-items:flex-start!important}
 .wt-cats{display:flex!important;flex-direction:row!important;flex-wrap:nowrap!important;overflow-x:auto;scroll-snap-type:x mandatory;margin-right:-16px;padding-right:16px!important;scrollbar-width:none}
 .wt-cats::-webkit-scrollbar{display:none}
 .wt-cat{flex:0 0 72%!important;width:72%!important;max-width:none!important;scroll-snap-align:start}
 .wt-steps{grid-template-columns:1fr}
 .wt-car-track{grid-auto-columns:78%}
 .wt-car-btn{display:none}
 .wt-hot{min-height:420px}.wt-spot__lbl{display:none}
 .wt-tip{width:200px}
 .wt-reviews:not(.wt-car-track),.wt-blog{grid-template-columns:1fr}
 .wt-rcard{grid-template-columns:1fr 92px}.wt-rcard__img{width:92px!important}
 .wt-footer__grid{grid-template-columns:1fr 1fr;gap:32px 20px}.wt-footer__brand,.wt-footer__news{grid-column:span 2}.wt-news{max-width:none}
 .wt-footer__base{flex-direction:column;gap:16px;align-items:flex-start}.wt-footer__base nav{flex-wrap:wrap;gap:16px}
 .woocommerce ul.products{grid-template-columns:1fr!important}
 .wt-trust{grid-template-columns:1fr}
}
@media (max-width:480px){.wt-trustbar>.wt-wrap,.wt-why-grid{gap:18px 12px}.wt-ib .elementor-icon-box-wrapper{gap:10px}}
/* ---------- Theme Builder header ---------- */
.wth-announce{background:#365647!important;min-height:36px}
.wth-announce .elementor-icon-list-items{flex-wrap:nowrap!important}
.wth-announce .elementor-icon-list-item,.wth-announce .elementor-icon-list-text{color:#fff!important;font:400 12px/1 var(--sans)!important;white-space:nowrap}
.wth-announce .elementor-icon-list-icon svg{width:16px!important;height:16px!important}
.wth-announce .elementor-icon-list-icon i,.wth-announce .elementor-icon-list-icon svg{color:#fff!important;fill:#fff!important;opacity:.9}
.wth-announce .elementor-icon-list-item:after{border-color:rgba(255,255,255,.55)!important;height:12px!important;top:50%!important;transform:translateY(-50%)}
.wth-rating p{margin:0;color:#fff;font:500 12px/1 var(--sans);white-space:nowrap;letter-spacing:.02em}.wth-rating .s{color:#E5AC32;letter-spacing:2px;margin:0 6px}
.wth-header{background:#fff;border-bottom:1px solid var(--ln);transition:box-shadow .3s var(--ease)}
.wth-header.elementor-sticky--effects{box-shadow:0 8px 24px -16px rgba(21,53,40,.35)}
.wth-logo .elementor-heading-title{font:600 17px/1 var(--sans)!important;letter-spacing:.28em!important;color:var(--tx)!important}
.wth-logo .elementor-heading-title a{color:inherit!important}
.wth-nav .elementor-nav-menu--main .elementor-item{font:500 13.5px/1 var(--sans)!important;color:var(--tx)!important;padding:28px 16px!important}
.wth-nav .elementor-nav-menu--main .elementor-item:hover,.wth-nav .elementor-nav-menu--main .elementor-item.elementor-item-active{color:var(--g)!important}
.wth-nav .e--pointer-underline .elementor-item:after{background:var(--g)!important;height:2px!important;bottom:18px!important;left:16px!important;right:16px!important;width:auto!important}
.wth-nav .elementor-nav-menu--dropdown{border:1px solid var(--ln);border-radius:var(--r);box-shadow:0 20px 40px -20px rgba(21,53,40,.3);padding:6px;background:#fff!important}
.wth-nav .elementor-nav-menu--dropdown a{font:400 13.5px/1.2 var(--sans)!important;color:var(--tx)!important;padding:11px 14px!important;border-radius:6px}
.wth-nav .elementor-nav-menu--dropdown a:hover,.wth-nav .elementor-nav-menu--dropdown a.elementor-item-active{background:var(--sage)!important;color:var(--g)!important}
.wth-nav .elementor-menu-toggle{background:transparent!important;color:var(--tx)!important;font-size:22px}
.wth-tools{gap:4px!important}
.wth-tools .elementor-icon,.wth-tools .elementor-search-form__toggle i,.wth-tools .elementor-menu-cart__toggle .elementor-button{color:var(--tx)!important}
.wth-ic .elementor-icon{font-size:18px!important;width:38px;height:38px;display:grid!important;place-items:center;border-radius:50%;transition:background .2s}
.wth-ic .elementor-icon:hover{background:var(--sage)}
.wth-ic .elementor-icon svg{width:19px;height:19px;fill:none;stroke:currentColor;stroke-width:1.5}
.wth-search .elementor-search-form__toggle{--e-search-form-toggle-size:38px;--e-search-form-toggle-icon-size:18px;--e-search-form-toggle-background-color:transparent;--e-search-form-toggle-color:var(--tx)}
.wth-search .elementor-search-form__toggle:hover{--e-search-form-toggle-background-color:var(--sage)}
.wth-search .elementor-search-form--full-screen .elementor-search-form__container{background:rgba(21,53,40,.94)!important}
.wth-cart .elementor-menu-cart__toggle .elementor-button{background:transparent!important;border:0!important;padding:9px!important;font-size:18px}
.wth-cart .elementor-button-icon[data-counter]:before{background:var(--g)!important;font-size:10px;min-width:16px;height:16px;line-height:16px;top:-6px;right:-8px}
.wth-cart .elementor-menu-cart__toggle .elementor-button-text{display:none}
.wth-cta .elementor-button{height:40px;padding:0 20px!important;border-radius:6px!important;background:var(--g)!important;color:#fff!important;font:600 13px/1 var(--sans)!important;display:inline-flex!important;align-items:center;transition:background .25s var(--ease),transform .25s var(--ease)!important}
.wth-cta .elementor-button:hover{background:var(--g3)!important;transform:translateY(-2px)}
.wth-header,.wth-announce{animation:wtFade .6s var(--ease) both}
@keyframes wtFade{from{opacity:0}to{opacity:1}}
/* ---------- buttons: centre text vertically ---------- */
.wt-b .elementor-button,.wt-link .elementor-button{display:inline-flex!important;align-items:center!important;justify-content:center!important;line-height:1!important}
.wt-b .elementor-button .elementor-button-content-wrapper,.wt-link .elementor-button-content-wrapper{align-items:center!important;gap:8px}
.wt-b .elementor-button-text,.wt-link .elementor-button-text{line-height:1!important;padding:0!important}
/* ---------- hero trust bar overlap ---------- */
.wt-hero{position:relative;padding-bottom:0!important}
.wt-hero__txt{padding-bottom:150px!important}
.wt-hero__trust{position:absolute!important;left:0;bottom:0;width:76%!important;z-index:3;background:#fff;border-radius:0 18px 0 0;padding:24px 40px 24px max(32px,calc((100vw - 1320px)/2 + 32px))!important;display:grid!important;grid-template-columns:repeat(4,auto);justify-content:space-between;gap:24px!important;animation:wtUp .9s .45s var(--ease) both}
.wt-hero__trust .elementor-icon svg,.wt-ib .elementor-icon svg{fill:none!important;stroke:var(--g);stroke-width:1.5}
.wt-hero__img{min-height:640px}
/* ---------- carousel progress line ---------- */
.wt-car-line{position:relative;width:min(470px,70%);height:3px;margin:30px auto 0;background:#D0D0D0;border-radius:3px;cursor:pointer;overflow:hidden}
.wt-car-line span{position:absolute;left:0;top:0;height:100%;width:25%;background:#000;border-radius:3px;transition:transform .45s var(--ease),width .3s var(--ease)}
.wt-carousel.no-nav .wt-car-line{display:none}
@media (max-width:1024px){.wth-nav .elementor-nav-menu--dropdown.elementor-nav-menu__container{border-radius:0;margin-top:0}.wt-hero__trust{position:static!important;width:100%!important;border-radius:0;grid-template-columns:1fr 1fr;padding:24px 32px!important}.wt-hero__txt{padding-bottom:56px!important}.wt-hero__img{min-height:440px}.wth-announce .wth-rating{display:none}}
@media (max-width:767px){.wth-announce .elementor-icon-list-item:not(:first-child){display:none!important}.wth-announce .elementor-icon-list-items{justify-content:center!important}.wt-hero__trust{padding:22px 16px!important;gap:18px 12px!important}.wt-hero__img{min-height:340px}.wth-logo .elementor-heading-title{font-size:14px!important;letter-spacing:.22em!important}.wth-cta{display:none}}
.wth-announce svg,.wth-ic svg,.wt-ib svg,.wt-hero__trust svg{fill:none!important;stroke:currentColor!important}
.wt-ib .elementor-icon{color:var(--g)!important}
@media (max-width:1024px){.wth-nav{order:3}.wth-header>.e-con-inner,.wth-header{gap:6px!important}}
.wth-header .wth-nav{flex:1 1 auto!important;width:auto!important;max-width:none!important}
.wth-nav .elementor-nav-menu--main>.elementor-nav-menu{flex-wrap:nowrap!important;justify-content:center!important}
.wth-logo{flex:0 0 auto!important;width:auto!important}
.wth-tools{margin-left:auto!important;flex:0 0 auto!important;width:auto!important;--width:auto!important}
.wth-nav .elementor-nav-menu--main .elementor-item{padding:24px 14px!important}
.wth-nav .e--pointer-underline .elementor-item:after{bottom:16px!important;left:14px!important;right:14px!important}
.wt-hero{grid-template-columns:46% 54%!important}
.wt-hero__txt{padding-right:40px!important}
.wt-h1 .elementor-heading-title{font-size:clamp(38px,3.75vw,66px)!important;line-height:1.04!important}
@media (min-width:1025px){.wt-hero .wt-h1 .elementor-heading-title{white-space:nowrap}}
@media (max-width:1024px){.wt-hero{grid-template-columns:1fr!important}}
.wt-car-btn,.wt-car-btn:focus,.wt-top,.wt-top:focus,.wt-news button,.wt-news button:focus,.wt-spot__b,.wt-spot__b:focus{border-color:var(--ln)!important;color:var(--g)!important;background:#fff!important}
.wt-car-btn{color:var(--tx)!important}
.wt-car-btn:hover{background:var(--g)!important;color:#fff!important;border-color:var(--g)!important}
.wt-spot.open .wt-spot__b{background:var(--g)!important;color:#fff!important}
.wt-top{border:0!important}
.wth-nav .elementor-nav-menu--main .elementor-item{padding-top:20px!important;padding-bottom:20px!important}
@media (max-width:767px){.wth-announce .elementor-icon-list-item:after{display:none!important}}
/* ---------- hotspot image: always clipped to its column ---------- */
.wt-design__img{overflow:hidden;position:relative}
.wt-design__img .elementor-widget-shortcode,.wt-design__img .elementor-shortcode{height:100%}
.wt-hot{height:100%;overflow:hidden}
.wt-hot .wt-hot__img{position:absolute!important;inset:0!important;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;object-position:center 40%}
/* ---------- slider arrows: white circles over the slides ---------- */
.wt-carousel{position:relative}
.wt-car-btn{position:absolute!important;top:var(--arrow-top,50%)!important;transform:translateY(-50%);z-index:5;width:44px!important;height:44px!important;padding:0!important;border-radius:50%!important;border:0!important;background:#fff!important;color:var(--tx)!important;display:grid!important;place-items:center;box-shadow:0 8px 22px -8px rgba(0,0,0,.35)!important;cursor:pointer;transition:opacity .3s var(--ease),transform .3s var(--ease),background .25s,color .25s}
.wt-car-btn.prev{left:16px!important;right:auto!important}.wt-car-btn.next{right:16px!important;left:auto!important}
.wt-car-btn:hover{background:var(--g)!important;color:#fff!important}
.wt-car-btn:disabled{opacity:0!important;pointer-events:none}
.wt-car-btn svg,.wt-car-btn .wt-ic{width:18px;height:18px}
.wt-carousel[data-carousel]{--arrow-top:calc((100% - 70px) * .32)}
/* ---------- category slider ---------- */
.wt-cat-carousel{width:100%}
.wt-cat-carousel .wt-car-track{display:grid!important;grid-auto-flow:column;grid-auto-columns:clamp(240px,21.5vw,338px);gap:12px!important;overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;width:calc(100% + max(0px, (100vw - 1320px) / 2) + 32px)!important;max-width:none!important;--width:auto;padding-right:calc(max(0px, (100vw - 1320px) / 2) + 32px)!important;scroll-padding-left:0;flex-wrap:nowrap!important;cursor:grab}
.wt-cat-carousel .wt-car-track::-webkit-scrollbar{display:none}
.wt-cat-carousel{--arrow-top:50%}
.wt-cat-carousel .wt-car-btn.next{right:calc(24px - max(0px, (100vw - 1320px) / 2) - 32px)!important}
.wt-cat-carousel .wt-cat{aspect-ratio:4/5!important;width:auto!important;scroll-snap-align:start;border-radius:8px}
.wt-cat-carousel .wt-cat:after{background:linear-gradient(180deg,transparent 58%,rgba(0,0,0,.6))}
.wt-cat__t{left:24px!important;right:20px!important;bottom:22px!important;flex-direction:row!important;align-items:center!important;justify-content:space-between!important;width:auto!important}
.wt-cat__t .elementor-heading-title{font:700 21px/1.15 var(--sans)!important;color:#fff!important;letter-spacing:-.01em;text-shadow:0 1px 10px rgba(0,0,0,.25)}
.wt-cat__go{width:auto!important;flex:0 0 auto}
.wt-cat__go .elementor-icon{width:30px;height:30px;border:1.5px solid rgba(255,255,255,.9);border-radius:50%;display:grid!important;place-items:center;color:#fff!important;font-size:14px!important;transition:background .25s,color .25s}
.wt-cat__go .elementor-icon svg{width:14px;height:14px;fill:none!important;stroke:currentColor!important}
.wt-cat:hover .wt-cat__go .elementor-icon{background:#fff;color:var(--tx)!important}
.wt-cat__badge{position:absolute!important;top:14px;left:14px;z-index:3;width:auto!important}
.wt-cat__badge .elementor-heading-title{background:#fff;color:var(--tx)!important;font:700 10.5px/1 var(--sans)!important;letter-spacing:.12em;text-transform:uppercase;padding:8px 12px;border-radius:999px}
.wt-cat-carousel .wt-car-line{margin-top:44px}
/* ---------- reviews slider ---------- */
.wt-rev-carousel .wt-car-track{display:grid;grid-auto-flow:column;grid-auto-columns:calc((100% - 48px)/3);gap:24px;grid-template-columns:none}
.wt-rev-carousel{--arrow-top:50%}
.wt-rev-carousel .wt-car-btn.prev{left:-22px!important}.wt-rev-carousel .wt-car-btn.next{right:-22px!important}
.wt-rev-carousel .wt-rcard{scroll-snap-align:start}
@media (max-width:1024px){.wt-cat-carousel .wt-car-track{grid-auto-columns:clamp(220px,38vw,320px)}.wt-rev-carousel .wt-car-track{grid-auto-columns:calc((100% - 24px)/2)}}
@media (max-width:767px){.wt-cat-carousel .wt-car-track{grid-auto-columns:74%;width:calc(100% + 16px)!important;padding-right:16px!important}.wt-cat-carousel .wt-car-btn.next{right:8px!important}.wt-rev-carousel .wt-car-track{grid-auto-columns:88%}.wt-rev-carousel .wt-car-btn{display:none!important}.wt-car-btn{width:38px!important;height:38px!important}}
.wt-spot__b{padding:0!important;display:grid!important;place-items:center!important;line-height:1!important;font-size:18px!important;border:0!important}
.wt-spot__b span{display:block;line-height:1}
/* ---------- v5: frosted arrows, draggable line, full-bleed category slider, hero trust bar ---------- */
.wt-car-btn{background:rgba(255,255,255,.55)!important;-webkit-backdrop-filter:blur(12px) saturate(1.4);backdrop-filter:blur(12px) saturate(1.4);border:1px solid rgba(255,255,255,.6)!important;color:var(--tx)!important;width:56px!important;height:56px!important;box-shadow:0 10px 28px -10px rgba(0,0,0,.35)!important}
.wt-car-btn svg{width:22px!important;height:22px!important}
.wt-car-btn:hover{background:rgba(255,255,255,.85)!important;color:var(--tx)!important;transform:translateY(-50%) scale(1.06)}
.wt-car-line{height:4px;cursor:grab;touch-action:none;padding:0;overflow:visible}
.wt-car-line:before{content:"";position:absolute;left:0;right:0;top:-10px;bottom:-10px}
.wt-car-line span{pointer-events:none}
.wt-carousel.dragging .wt-car-line{cursor:grabbing}.wt-carousel.dragging .wt-car-line span{transition:none}
.wt-cat-carousel .wt-car-track{width:100vw!important;margin-left:calc(-1 * (max(0px, (100vw - 1320px) / 2) + 32px))!important;padding-left:calc(max(0px, (100vw - 1320px) / 2) + 32px)!important;padding-right:calc(max(0px, (100vw - 1320px) / 2) + 32px)!important;scroll-padding-left:calc(max(0px, (100vw - 1320px) / 2) + 32px);grid-auto-columns:clamp(260px,23.5vw,460px)!important;gap:16px!important}
.wt-cat-carousel .wt-cat{aspect-ratio:451/548!important}
.wt-cat-carousel .wt-car-btn.prev{left:calc(40px - max(0px, (100vw - 1320px) / 2) - 32px)!important}
.wt-cat-carousel .wt-car-btn.next{right:calc(40px - max(0px, (100vw - 1320px) / 2) - 32px)!important}
.wt-hero__trust{background:var(--off)!important;border-radius:0 22px 0 0!important}
.wt-hero__trust .elementor-icon-box-icon,.wt-trustbar .elementor-icon-box-icon,.wt-why .elementor-icon-box-icon,.wt-feats .elementor-icon-box-icon{flex:0 0 auto}
.wt-hero__trust .elementor-icon,.wt-why .wt-ib .elementor-icon{width:34px!important;height:34px!important;font-size:30px!important;display:grid!important;place-items:center}
.wt-hero__trust .elementor-icon svg,.wt-why .wt-ib .elementor-icon svg{width:30px!important;height:30px!important;stroke-width:1.4!important}
.wt-feat .elementor-icon svg{width:18px!important;height:18px!important}
.wth-ic .elementor-icon svg,.wth-search .elementor-search-form__toggle svg{width:19px!important;height:19px!important}
@media (max-width:1024px){.wt-hero__trust{border-radius:0!important}}
@media (max-width:767px){.wt-cat-carousel .wt-car-track{width:100vw!important;margin-left:-16px!important;padding-left:16px!important;padding-right:16px!important;scroll-padding-left:16px;grid-auto-columns:76%!important;gap:12px!important}.wt-cat-carousel .wt-car-btn.prev{left:-4px!important}.wt-cat-carousel .wt-car-btn.next{right:-4px!important}.wt-car-btn{width:44px!important;height:44px!important}.wt-hero__trust .elementor-icon svg,.wt-why .wt-ib .elementor-icon svg{width:26px!important;height:26px!important}}
.wt-cat-carousel .wt-car-track{padding-right:40px!important}
.wt-hero{grid-template-columns:50% 50%!important}
.wt-h1 .elementor-heading-title{font-size:clamp(38px,3.5vw,60px)!important}
@media (max-width:1024px){.wt-hero{grid-template-columns:1fr!important}}
@media (max-width:767px){.wt-cat-carousel .wt-car-track{padding-right:16px!important}}
.wt-cat-carousel .wt-car-track{grid-auto-columns:clamp(280px,28vw,452px)!important}
.wt-cat-carousel .wt-cat{aspect-ratio:451/563!important;border-radius:10px!important}
.wt-cat-carousel .wt-cat__t{left:32px!important;right:28px!important;bottom:30px!important}
.wt-cat-carousel .wt-cat__t .elementor-heading-title{font-size:clamp(20px,1.5vw,27px)!important}
.wt-cat-carousel .wt-cat__go .elementor-icon{width:40px;height:40px}
.wt-cat-carousel .wt-cat__go .elementor-icon svg{width:17px;height:17px}
.wt-cat-carousel .wt-cat__badge{top:16px;left:16px}
.wt-cat-carousel .wt-cat__badge .elementor-heading-title{font-size:12px!important;padding:10px 16px}
@media (max-width:1024px){.wt-cat-carousel .wt-car-track{grid-auto-columns:clamp(260px,42vw,400px)!important}}
@media (max-width:767px){.wt-cat-carousel .wt-car-track{grid-auto-columns:80%!important}.wt-cat-carousel .wt-cat__t{left:20px!important;right:18px!important;bottom:20px!important}}
.wt-car-btn{background:#fff!important;-webkit-backdrop-filter:none;backdrop-filter:none;border:0!important;color:var(--tx)!important;box-shadow:0 8px 24px -8px rgba(0,0,0,.35)!important}
.wt-car-btn:hover{background:#fff!important;color:var(--g)!important}
.wt-cat__go .elementor-icon{background:rgba(255,255,255,.18)!important;-webkit-backdrop-filter:blur(10px) saturate(1.3);backdrop-filter:blur(10px) saturate(1.3);border:1px solid rgba(255,255,255,.45)!important;color:#fff!important}
.wt-cat:hover .wt-cat__go .elementor-icon{background:rgba(255,255,255,.95)!important;color:var(--tx)!important}
.wt-cat-carousel .wt-cat__t .elementor-heading-title{font:800 28px/34px 'Barlow',sans-serif!important;color:#fff!important;letter-spacing:0}
.wth-nav .elementor-nav-menu--main .elementor-item{font:700 18px/22px 'Barlow',sans-serif!important;color:rgb(51,51,51)!important}
.wth-nav .elementor-nav-menu--dropdown a{font:600 16px/1.2 'Barlow',sans-serif!important}
@media (max-width:1366px){.wth-nav .elementor-nav-menu--main .elementor-item{font-size:16px!important;padding-left:11px!important;padding-right:11px!important}}
@media (max-width:767px){.wt-cat-carousel .wt-cat__t .elementor-heading-title{font-size:22px!important;line-height:27px!important}}
h1,h2,h3,h4,h5,h6,.elementor-heading-title,.product_title,.woocommerce-loop-product__title,.entry-title{font-family:'Barlow',sans-serif!important}
.wt-h1 .elementor-heading-title{font-weight:700!important;letter-spacing:-.01em}
.wt-h2 .elementor-heading-title,.wt-archive-hero h1,.single-product .product_title,.related.products>h2{font-weight:700!important;letter-spacing:-.005em}
.wt-cat__h .elementor-heading-title,.wt-step__h .elementor-heading-title{font-weight:700!important}
.wt-hero{max-width:1320px!important;margin-left:auto!important;margin-right:auto!important;padding:0 32px!important;box-sizing:border-box;background:var(--off)!important;box-shadow:0 0 0 100vmax var(--off);clip-path:inset(0 -100vmax)}
.wt-hero__txt{padding:80px 48px 150px 0!important}
.wt-hero__trust{left:32px!important;width:calc(80% - 32px)!important;padding:24px 40px 24px 0!important}
.wt-hero__img{border-radius:0 0 0 22px;overflow:hidden}
@media (max-width:1024px){.wt-hero{padding:0!important;box-shadow:none;clip-path:none}.wt-hero__txt{padding:56px 32px!important}.wt-hero__trust{width:100%!important;padding:24px 32px!important}.wt-hero__img{border-radius:0}}
@media (max-width:767px){.wt-hero__txt{padding:44px 16px!important}.wt-hero__trust{padding:22px 16px!important}}
@media (min-width:1025px){.wt-hero{padding:0!important}.wt-hero__trust{left:0!important;width:80%!important}}
@media (min-width:1025px) and (max-width:1384px){.wt-hero{max-width:calc(100% - 64px)!important}}
@media (min-width:1385px){.wt-hero__img{width:calc(100% + (100vw - 1320px) / 2)!important;max-width:none!important;--width:auto}}
@media (min-width:1025px) and (max-width:1384px){.wt-hero__img{width:calc(100% + 32px)!important;max-width:none!important;--width:auto}}
.wt-hero__img img{object-fit:cover!important;object-position:64% 55%!important}
@media (max-width:1024px){.wt-hero__img img{object-position:62% 60%!important}}
.wt-note .wt-note__t{color:#16211b!important;font-size:30px!important;font-weight:600;text-shadow:0 0 2px rgba(255,255,255,.95),0 0 10px rgba(255,248,236,.95),0 0 22px rgba(255,248,236,.85)}
.wt-note svg{color:#16211b!important;filter:drop-shadow(0 0 3px rgba(255,255,255,.95)) drop-shadow(0 0 8px rgba(255,248,236,.8))}
.wt-note svg path{stroke-width:1.8}
.wt-hero .wt-p,.wt-hero .wt-p p{color:#3f4641!important;font-size:17px!important;line-height:1.65!important}
.wt-hero .wt-label .elementor-heading-title{color:#3f4641!important}
.wt-hero .wt-h1 .elementor-heading-title{color:#141a16!important}
.wt-hero__trust .elementor-icon-box-title{color:#141a16!important;font-size:14px!important}
.wt-hero__trust .elementor-icon-box-description{color:#4b524d!important;font-size:13px!important}
@media (max-width:767px){.wt-note .wt-note__t{font-size:22px!important}}
/* ---------- advanced button hover ---------- */
.wt-fx{position:relative!important;overflow:hidden!important;isolation:isolate;transition:transform .45s cubic-bezier(.22,1,.36,1),box-shadow .45s cubic-bezier(.22,1,.36,1),color .35s ease,border-color .35s ease,background-color .35s ease!important;will-change:transform}
.wt-fx:before{content:"";position:absolute;left:var(--x,50%);top:var(--y,50%);width:0;height:0;border-radius:50%;transform:translate(-50%,-50%);background:var(--fx-fill,#0f2a1f);z-index:-1;transition:width .6s cubic-bezier(.22,1,.36,1),height .6s cubic-bezier(.22,1,.36,1);pointer-events:none}
.wt-fx:after{content:"";position:absolute;top:0;bottom:0;left:-60%;width:40%;background:linear-gradient(105deg,transparent,rgba(255,255,255,.28),transparent);transform:skewX(-20deg);z-index:0;pointer-events:none;opacity:0}
.wt-fx:hover:before,.wt-fx:focus-visible:before{width:260%;height:0;padding-bottom:260%}
.wt-fx:hover:after{animation:wtSheen .9s cubic-bezier(.22,1,.36,1) .05s}
@keyframes wtSheen{0%{left:-60%;opacity:0}20%{opacity:1}100%{left:130%;opacity:0}}
.wt-fx:hover{box-shadow:0 14px 30px -14px rgba(21,53,40,.55)!important}
.wt-fx:active{transition-duration:.12s!important;transform:translateY(1px) scale(.97)!important;box-shadow:0 4px 10px -6px rgba(21,53,40,.5)!important}
.wt-fx .elementor-button-content-wrapper,.wt-fx>*{position:relative;z-index:1}
.wt-fx .elementor-button-icon{display:inline-flex;overflow:hidden;transition:transform .45s cubic-bezier(.22,1,.36,1)!important}
.wt-fx:hover .elementor-button-icon{transform:translateX(5px)!important}
.wt-fx .elementor-button-icon svg,.wt-fx .elementor-button-icon i{transition:transform .45s cubic-bezier(.22,1,.36,1)}
.wt-fx:hover .elementor-button-icon svg,.wt-fx:hover .elementor-button-icon i{animation:wtArrow .6s cubic-bezier(.22,1,.36,1)}
@keyframes wtArrow{0%{transform:translateX(0)}45%{transform:translateX(120%);opacity:0}46%{transform:translateX(-120%);opacity:0}100%{transform:translateX(0);opacity:1}}
/* primary (green) buttons: deeper green fill; outline buttons fill green with white text */
.wt-b .elementor-button.wt-fx,.wth-cta .elementor-button.wt-fx,.wt-atc.wt-fx,.wt-btn.wt-fx,.single_add_to_cart_button.wt-fx{--fx-fill:#0f2a1f;background:var(--g)!important}
.wt-b--o .elementor-button.wt-fx{--fx-fill:var(--g);background:#fff!important;color:var(--tx)!important}
.wt-b--o .elementor-button.wt-fx:hover{color:#fff!important;border-color:var(--g)!important}
.wt-b .elementor-button.wt-fx:hover,.wth-cta .elementor-button.wt-fx:hover,.wt-atc.wt-fx:hover,.wt-btn.wt-fx:hover{background:var(--g)!important}
.wt-b--o .elementor-button.wt-fx:hover{background:#fff!important}
@media (prefers-reduced-motion:reduce){.wt-fx,.wt-fx:before{transition:none!important}.wt-fx:after,.wt-fx .elementor-button-icon svg{animation:none!important}}
/* ---------- "View all" links: text + circular arrow ---------- */
.wt-head .wt-link .elementor-button{display:inline-flex!important;align-items:center!important;gap:14px;padding:6px 6px 6px 20px!important;border:1px solid var(--ln)!important;border-radius:999px!important;background:#fff!important;font:600 14px/1 var(--sans)!important;color:var(--tx)!important;transition:border-color .4s cubic-bezier(.22,1,.36,1),box-shadow .4s cubic-bezier(.22,1,.36,1),transform .4s cubic-bezier(.22,1,.36,1)!important}
.wt-head .wt-link .elementor-button-icon{display:none!important}
.wt-head .wt-link .elementor-button-content-wrapper{gap:0!important}
.wt-head .wt-link .elementor-button:after{content:"";width:36px;height:36px;border-radius:50%;flex:none;background:var(--g) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E") center/16px no-repeat;transition:transform .5s cubic-bezier(.22,1,.36,1),background-color .4s,background-position .5s cubic-bezier(.22,1,.36,1)}
.wt-head .wt-link .elementor-button:hover{border-color:var(--g)!important;box-shadow:0 12px 26px -16px rgba(21,53,40,.6);transform:translateY(-2px)}
.wt-head .wt-link .elementor-button:hover:after{transform:rotate(-45deg);background-color:#0f2a1f}
.wt-head .wt-link .elementor-button-text{position:relative}
.wt-head .wt-link .elementor-button-text:after{content:"";position:absolute;left:0;right:0;bottom:-4px;height:1.5px;background:var(--g);transform:scaleX(0);transform-origin:right;transition:transform .45s cubic-bezier(.22,1,.36,1)}
.wt-head .wt-link .elementor-button:hover .elementor-button-text:after{transform:scaleX(1);transform-origin:left}
@media (max-width:767px){.wt-head .wt-link .elementor-button{font-size:13px!important;padding:5px 5px 5px 16px!important}.wt-head .wt-link .elementor-button:after{width:32px;height:32px}}
/* ---------- "Design your own" section (reference match) ---------- */
.wt-design{grid-template-columns:56% 44%!important;background:var(--off)!important;min-height:clamp(520px,43vw,820px);overflow:hidden}
.wt-design__img,.wt-hot{min-height:100%}
.wt-design__txt{background:var(--off);padding:clamp(48px,5vw,90px) max(32px,calc((100vw - 1320px)/2)) clamp(48px,5vw,90px) clamp(40px,4.4vw,84px)!important;gap:0!important;justify-content:center!important}
.wt-design__txt>.elementor-widget-heading.wt-label{margin-bottom:14px}
.wt-design__txt .wt-label .elementor-heading-title{font-size:11px!important;letter-spacing:.2em!important;color:#5a615c!important}
.wt-design__txt .wt-h2 .elementor-heading-title{font-size:clamp(34px,3.2vw,52px)!important;line-height:1.05!important;font-weight:600!important;color:#141a16!important;margin-bottom:16px}
.wt-design__txt .wt-p,.wt-design__txt .wt-p p{font-size:15px!important;line-height:1.6!important;color:#4b524d!important;max-width:440px;margin:0 0 26px}
.wt-feats{gap:18px!important;margin:0 0 34px!important}
.wt-feat .elementor-icon-box-wrapper{gap:16px!important;align-items:center!important}
.wt-feat .elementor-icon-box-icon .elementor-icon{width:46px!important;height:46px!important;border:1px solid #c9d0c8!important;border-radius:50%!important;background:transparent;color:var(--g)!important;display:grid!important;place-items:center;transition:background .35s,border-color .35s}
.wt-feat .elementor-icon svg{width:20px!important;height:20px!important}
.wt-feat:hover .elementor-icon{background:#fff;border-color:var(--g)!important}
.wt-feat .elementor-icon-box-title{font:700 15px/1.25 var(--sans)!important;color:#141a16!important;margin:0 0 3px!important}
.wt-feat .elementor-icon-box-description{font:400 13px/1.45 var(--sans)!important;color:#5a615c!important}
.wt-design__btns{gap:34px!important}
.wt-design__btns .wt-b .elementor-button{height:48px!important;padding:0 30px!important;border-radius:6px!important}
.wt-design__btns .wt-link .elementor-button{font:600 14px/1 var(--sans)!important;color:#141a16!important;gap:10px}
.wt-design__btns .wt-link .elementor-button-icon{display:none!important}
.wt-design__btns .wt-link .elementor-button:after{content:"";width:16px;height:16px;background:currentColor;-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E") center/contain no-repeat;transition:transform .45s cubic-bezier(.22,1,.36,1)}
.wt-design__btns .wt-link .elementor-button:hover:after{transform:translateX(5px)}
.wt-design__btns .wt-link .elementor-button:hover{color:var(--g)!important}
.wt-leaf{position:absolute!important;right:0;bottom:0;width:clamp(120px,11vw,200px)!important;z-index:0;pointer-events:none;opacity:.95}
.wt-leaf svg{display:block;width:100%;height:auto}
.wt-design__txt>*:not(.wt-leaf){position:relative;z-index:1}
/* hotspots as white pills with "+" circle */
.wt-spot{gap:0!important;background:#fff;border-radius:999px;padding:4px 14px 4px 4px;box-shadow:0 8px 22px -10px rgba(0,0,0,.35);cursor:pointer;transform:translate(-14px,-50%)!important}
.wt-spot__b{width:26px!important;height:26px!important;border:1.5px solid #2d3a33!important;background:#fff!important;color:#2d3a33!important;font-size:16px!important;font-weight:500!important;margin-right:8px;animation:none!important;box-shadow:none!important;transition:transform .4s cubic-bezier(.22,1,.36,1),background .3s,color .3s}
.wt-spot__lbl{display:inline!important;background:none!important;box-shadow:none!important;padding:0!important;font:600 12px/1 var(--sans)!important;color:#1d211e!important}
.wt-spot:hover .wt-spot__b,.wt-spot.open .wt-spot__b{transform:rotate(45deg);background:var(--g)!important;color:#fff!important;border-color:var(--g)!important}
.wt-spot:before{content:"";position:absolute;left:4px;top:4px;width:26px;height:26px;border-radius:50%;box-shadow:0 0 0 0 rgba(255,255,255,.8);animation:wtPulse 2.6s infinite;pointer-events:none}
@media (max-width:1024px){.wt-design{grid-template-columns:1fr!important}.wt-design__txt{padding:56px 32px!important}.wt-hot{min-height:480px}}
@media (max-width:767px){.wt-design__txt{padding:44px 16px!important}.wt-hot{min-height:400px}.wt-spot{padding:3px 3px 3px 3px}.wt-spot__lbl{display:none!important}.wt-spot__b{margin:0}.wt-design__btns{flex-direction:row!important;align-items:center!important;gap:22px!important}.wt-leaf{width:110px!important;opacity:.6}}
.wt-design{background:linear-gradient(170deg,#F4F1EA 0%,#F8F6F1 45%,#FFFFFF 100%)!important}
.wt-design__txt{background:transparent!important}
@media (max-width:1024px){.wt-design__txt{background:linear-gradient(180deg,#F4F1EA 0%,#FFFFFF 100%)!important}}
/* ---------- Elementor editor: never hide anything behind scroll/entrance animations ---------- */
.elementor-editor-active .wt-reveal,.elementor-editor-active .wt-reveal-group>*,.elementor-editor-active .wt3s__rv,.elementor-editor-active .wt3s__card,.elementor-editor-active .wt3s__media img,.elementor-editor-active .wt3s__num,.elementor-editor-active .wt3s__arrow svg,.elementor-editor-active .wtpc__rv,.elementor-editor-active .wtpc__card{opacity:1!important;transform:none!important;translate:none!important;animation:none!important}
.elementor-editor-active .wt-util__img img{clip-path:none!important}
/* ---------- native Elementor Hotspot widget (Design your own) ---------- */
.wt-design__img .wt-hotspots,.wt-design__img .wt-hotspots>.elementor-widget-container{position:absolute!important;inset:0;width:100%!important;height:100%!important}
.wt-hotspots img{width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;object-position:center 40%;display:block}
.wt-hotspots .e-hotspot{--hotspot-color:#1d211e;--hotspot-box-color:#fff;--hotspot-size:16px;--tooltip-color:#4b524d;--tooltip-text-color:#4b524d;z-index:3}
.wt-hotspots .e-hotspot__button{background:#fff!important;color:#1d211e!important;border-radius:999px!important;padding:4px 14px 4px 4px!important;min-height:0!important;min-width:0!important;gap:8px!important;box-shadow:0 8px 22px -10px rgba(0,0,0,.35)!important;font:600 12px/1 var(--sans)!important;transition:transform .35s cubic-bezier(.22,1,.36,1),box-shadow .35s}
.wt-hotspots .e-hotspot__icon{width:26px;height:26px;border:1.5px solid #2d3a33;border-radius:50%;display:grid!important;place-items:center;transition:transform .4s cubic-bezier(.22,1,.36,1),background .3s,color .3s,border-color .3s}
.wt-hotspots .e-hotspot__icon svg{width:14px!important;height:14px!important;fill:none!important;stroke:currentColor!important}
.wt-hotspots .e-hotspot__label{font:600 12px/1 var(--sans)!important;color:#1d211e!important;white-space:nowrap}
.wt-hotspots .e-hotspot:hover .e-hotspot__button,.wt-hotspots .e-hotspot--active .e-hotspot__button{transform:translateY(-2px);box-shadow:0 12px 26px -10px rgba(0,0,0,.4)!important}
.wt-hotspots .e-hotspot:hover .e-hotspot__icon,.wt-hotspots .e-hotspot--active .e-hotspot__icon{transform:rotate(45deg);background:var(--g);color:#fff;border-color:var(--g)}
.wt-hotspots .e-hotspot__outer-circle,.wt-hotspots .e-hotspot__inner-circle{display:none!important}
.wt-hotspots .e-hotspot__tooltip{background:#fff!important;color:#4b524d!important;border-radius:10px!important;padding:12px 14px!important;width:220px!important;max-width:min(220px,70vw);font:400 12.5px/1.5 var(--sans)!important;box-shadow:0 18px 36px -14px rgba(0,0,0,.35)!important;text-align:left!important}
.wt-hotspots .e-hotspot__tooltip p{margin:0}.wt-hotspots .e-hotspot__tooltip strong{display:block;color:#1d211e;margin-bottom:3px}
.wt-hotspots .e-hotspot__tooltip:after{border-top-color:#fff!important}
@media (max-width:1024px){.wt-design__img{min-height:480px}}
@media (max-width:767px){.wt-design__img{min-height:400px}.wt-hotspots .e-hotspot__label{display:none!important}.wt-hotspots .e-hotspot__button{padding:3px!important;gap:0!important}}
/* ---------- Hello theme default link colour (was pink) ---------- */
a{color:var(--g)}a:hover,a:active{color:var(--g3)}
/* ---------- Basket drawer (Elementor menu cart) ---------- */
.elementor-menu-cart__container{background:rgba(16,26,21,.45)!important;-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px)}
.elementor-menu-cart__main{width:min(420px,100vw)!important;padding:0!important;background:#fff!important;box-shadow:-24px 0 60px -30px rgba(0,0,0,.45)!important;display:flex!important;flex-direction:column;font-family:var(--sans)}
.elementor-menu-cart__main:before{content:"Your basket";display:block;padding:24px 72px 20px 28px;font:700 24px/1.1 var(--sans);color:var(--tx);border-bottom:1px solid var(--ln);letter-spacing:-.01em}
.elementor-menu-cart__close-button{top:18px!important;right:18px!important;width:40px!important;height:40px!important;margin:0!important;border-radius:50%;background:var(--off)!important;display:grid!important;place-items:center;transition:background .3s,transform .4s cubic-bezier(.22,1,.36,1)}
.elementor-menu-cart__close-button:before,.elementor-menu-cart__close-button:after{width:16px!important;height:1.6px!important;background:var(--tx)!important;top:50%!important;left:50%!important;margin:-.8px 0 0 -8px!important}
.elementor-menu-cart__close-button:hover{background:var(--sage)!important;transform:rotate(90deg)}
.widget_shopping_cart_content{display:flex;flex-direction:column;flex:1;min-height:0}
.elementor-menu-cart__products{flex:1;overflow-y:auto;padding:8px 28px!important;margin:0!important}
.elementor-menu-cart__product{display:grid!important;grid-template-columns:76px 1fr 32px!important;grid-template-rows:auto auto;column-gap:16px;row-gap:6px;align-items:start;padding:18px 0!important;border-bottom:1px solid var(--ln)!important;border-top:0!important}
.elementor-menu-cart__product:last-child{border-bottom:0!important}
.elementor-menu-cart__product-image{grid-row:1/3;grid-column:1;width:76px!important;margin:0!important}
.elementor-menu-cart__product-image img{width:76px!important;height:76px!important;object-fit:cover;border-radius:10px;background:var(--off);display:block}
.elementor-menu-cart__product-name{grid-column:2;margin:2px 0 0!important;padding:0!important;align-self:end}
.elementor-menu-cart__product-name a{font:600 15px/1.3 var(--sans)!important;color:var(--tx)!important;text-decoration:none}
.elementor-menu-cart__product-name a:hover{color:var(--g)!important}
.elementor-menu-cart__product-name .variation{font-size:12px;color:var(--tx2)}
.elementor-menu-cart__product-price{grid-column:2;margin:0!important;padding:0!important;font:500 14px/1.3 var(--sans)!important;color:var(--tx2)!important;align-self:start}
.elementor-menu-cart__product-price .quantity{color:var(--tx2)!important}
.elementor-menu-cart__product-price .amount{color:var(--tx)!important;font-weight:700}
.elementor-menu-cart__product-remove{grid-column:3;grid-row:1/3;align-self:center;position:relative!important;width:32px!important;height:32px!important;border:1px solid var(--ln)!important;border-radius:50%!important;margin:0!important;transition:background .3s,border-color .3s}
.elementor-menu-cart__product-remove:hover{background:#FBEDEA;border-color:#E3B8AE!important}
.elementor-menu-cart__product-remove:before,.elementor-menu-cart__product-remove:after{background:var(--tx2)!important;width:12px!important;height:1.5px!important}
.elementor-menu-cart__product-remove:hover:before,.elementor-menu-cart__product-remove:hover:after{background:#8C3B2C!important}
.elementor-menu-cart__subtotal{margin:0!important;padding:20px 28px 6px!important;border-top:1px solid var(--ln)!important;border-bottom:0!important;display:flex;justify-content:space-between;align-items:baseline;font:600 15px/1 var(--sans)!important;color:var(--tx2)!important;text-align:left!important;background:#fff}
.elementor-menu-cart__subtotal strong{font-weight:600;color:var(--tx2)}
.elementor-menu-cart__subtotal .amount{font:700 24px/1 var(--sans)!important;color:var(--tx)!important}
.elementor-menu-cart__footer-buttons{display:grid!important;grid-template-columns:1fr 1fr;gap:10px!important;padding:14px 28px 26px!important;margin:0!important;background:#fff}
.elementor-menu-cart__footer-buttons:before{content:"Free UK delivery on all cabinet orders";grid-column:1/-1;font:500 12.5px/1.3 var(--sans);color:var(--g);padding:10px 12px 10px 36px;background:var(--sage) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231D4032' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 6h11v10H3zM14 9h4l3 3v4h-7'/%3E%3Ccircle cx='7' cy='17.5' r='1.8'/%3E%3Ccircle cx='17' cy='17.5' r='1.8'/%3E%3C/svg%3E") 10px center/18px no-repeat;border-radius:8px;margin-bottom:4px}
.elementor-menu-cart__footer-buttons .elementor-button{height:50px;display:flex!important;align-items:center;justify-content:center;border-radius:8px!important;font:600 14.5px/1 var(--sans)!important;padding:0 12px!important;transition:background .3s,color .3s,border-color .3s,transform .3s cubic-bezier(.22,1,.36,1)!important}
.elementor-menu-cart__footer-buttons .elementor-button--view-cart{background:#fff!important;color:var(--tx)!important;border:1px solid var(--tx)!important}
.elementor-menu-cart__footer-buttons .elementor-button--view-cart:hover{background:var(--off)!important;transform:translateY(-2px)}
.elementor-menu-cart__footer-buttons .elementor-button--checkout{background:var(--g)!important;color:#fff!important;border:1px solid var(--g)!important}
.elementor-menu-cart__footer-buttons .elementor-button--checkout:hover{background:var(--g3)!important;transform:translateY(-2px)}
.elementor-menu-cart__footer-buttons .elementor-button-text{color:inherit!important}
.woocommerce-mini-cart__empty-message{padding:60px 28px!important;text-align:center;font:500 15px/1.5 var(--sans);color:var(--tx2)}
@media (max-width:480px){.elementor-menu-cart__main:before{font-size:21px;padding:20px 64px 16px 20px}.elementor-menu-cart__products{padding:4px 20px!important}.elementor-menu-cart__subtotal{padding:16px 20px 4px!important}.elementor-menu-cart__footer-buttons{padding:12px 20px 20px!important}.elementor-menu-cart__product{grid-template-columns:64px 1fr 30px!important}.elementor-menu-cart__product-image,.elementor-menu-cart__product-image img{width:64px!important;height:64px!important}}
/* ---------- header icons: one consistent thin outline set ---------- */
.wth-tools{gap:6px!important}
.wth-search .elementor-search-form__toggle,.wth-ic .elementor-icon,.wth-cart .elementor-menu-cart__toggle_button{width:40px!important;height:40px!important;border-radius:50%!important;display:grid!important;place-items:center!important;padding:0!important;background:transparent!important;border:0!important;color:var(--tx)!important;transition:background .3s,color .3s,transform .35s cubic-bezier(.22,1,.36,1)!important;position:relative;cursor:pointer}
.wth-search .elementor-search-form__toggle:hover,.wth-ic .elementor-icon:hover,.wth-cart .elementor-menu-cart__toggle_button:hover{background:var(--sage)!important;color:var(--g)!important;transform:translateY(-1px)}
.wth-search .elementor-search-form__toggle>*,.wth-cart .elementor-button-icon>svg,.wth-cart .elementor-button-icon>i{display:none!important}
.wth-search .elementor-search-form__toggle:before,.wth-cart .elementor-button-icon:before{content:"";width:20px;height:20px;background:currentColor;display:block}
.wth-search .elementor-search-form__toggle:before{-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.6' stroke-linecap='round'%3E%3Ccircle cx='11' cy='11' r='6.5'/%3E%3Cpath d='M16 16l4.5 4.5'/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.6' stroke-linecap='round'%3E%3Ccircle cx='11' cy='11' r='6.5'/%3E%3Cpath d='M16 16l4.5 4.5'/%3E%3C/svg%3E") center/contain no-repeat}
.wth-cart .elementor-button-icon{position:relative;display:grid!important;place-items:center;margin:0!important}
.wth-cart .elementor-button-icon:before{-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3C/svg%3E") center/contain no-repeat}
.wth-cart .elementor-button-text{display:none!important}
.wth-cart .elementor-button-icon-qty{position:absolute!important;top:-9px!important;right:-11px!important;min-width:17px;height:17px;padding:0 4px;border-radius:999px!important;background:var(--g)!important;color:#fff!important;font:700 10px/17px var(--sans)!important;text-align:center;box-shadow:0 0 0 2px #fff}
.wth-cart .elementor-button-icon-qty[data-counter="0"]{display:none!important}
.wth-ic .elementor-icon svg{width:20px!important;height:20px!important;stroke-width:1.6!important}
/* ---------- full-screen search overlay ---------- */
.wth-search .elementor-search-form__container.elementor-search-form--full-screen{background:rgba(14,30,23,.94)!important;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);display:flex!important;align-items:center;justify-content:center;padding:0 24px!important}
.wth-search .elementor-search-form--full-screen .elementor-search-form__input{width:min(860px,100%)!important;max-width:none!important;height:auto!important;flex:0 1 860px;background:transparent!important;border:0!important;border-bottom:1.5px solid rgba(255,255,255,.35)!important;border-radius:0!important;color:#fff!important;font:600 clamp(26px,4vw,48px)/1.2 var(--sans)!important;padding:14px 56px 18px 4px!important;text-align:left!important;caret-color:#9fd3b4;box-shadow:none!important;outline:0!important;transition:border-color .3s;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='1.6' stroke-linecap='round'%3E%3Ccircle cx='11' cy='11' r='6.5'/%3E%3Cpath d='M16 16l4.5 4.5'/%3E%3C/svg%3E")!important;background-repeat:no-repeat!important;background-position:right 6px center!important;background-size:30px!important}
.wth-search .elementor-search-form--full-screen .elementor-search-form__input:focus{border-bottom-color:#fff!important}
.wth-search .elementor-search-form--full-screen .elementor-search-form__input::placeholder{color:rgba(255,255,255,.55)!important;opacity:1}
.wth-search .elementor-search-form--full-screen .dialog-lightbox-close-button{top:28px!important;right:28px!important;width:48px;height:48px;border-radius:50%;border:1px solid rgba(255,255,255,.35);display:grid!important;place-items:center;color:#fff!important;font-size:18px!important;transition:background .3s,transform .4s cubic-bezier(.22,1,.36,1)}
.wth-search .elementor-search-form--full-screen .dialog-lightbox-close-button:hover{background:rgba(255,255,255,.12);transform:rotate(90deg)}
.wth-search .elementor-search-form--full-screen .dialog-lightbox-close-button svg{fill:#fff!important;width:16px;height:16px}
.wth-search .elementor-search-form--full-screen:after{content:"Press Enter to search, Esc to close";position:absolute;left:50%;transform:translateX(-50%);top:calc(50% + 70px);font:500 13px/1 var(--sans);letter-spacing:.06em;color:rgba(255,255,255,.55);white-space:nowrap}
.wth-tools{align-items:center!important}
.wth-tools>.elementor-element{display:flex!important;align-items:center!important;height:40px;margin:0!important}
.wth-tools>.elementor-element>.elementor-widget-container,.wth-search search,.wth-search .elementor-search-form,.wth-ic .elementor-icon-wrapper,.wth-cart .elementor-menu-cart__wrapper,.wth-cart .elementor-menu-cart__toggle_wrapper,.wth-cart .elementor-menu-cart__toggle{display:flex!important;align-items:center!important;height:40px;line-height:0}
.wth-cart .elementor-menu-cart__toggle_button,.wth-cart .elementor-button-icon{color:var(--tx)!important;--toggle-button-icon-color:var(--tx)}
.wth-cart .elementor-menu-cart__toggle_button:hover .elementor-button-icon{color:var(--g)!important}
.wth-search .elementor-search-form--full-screen .elementor-search-form__input{font-size:clamp(22px,3vw,40px)!important}
.wth-tools>.wth-cta{height:auto}
.wth-search .elementor-search-form{margin:0!important;top:auto!important;transform:none!important}
.wth-search .elementor-search-form__toggle{margin:0!important}
/* ---------- account panel ---------- */
.wta{position:fixed;inset:0;z-index:100000;font-family:var(--sans)}
.wta[hidden]{display:none}
.wta__overlay{position:absolute;inset:0;background:rgba(14,26,20,.5);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);opacity:0;transition:opacity .45s var(--ease)}
.wta__panel{position:absolute;top:0;right:0;bottom:0;width:min(460px,100vw);background:#fff;padding:34px 40px 30px;overflow-y:auto;box-shadow:-30px 0 70px -30px rgba(0,0,0,.5);transform:translateX(100%);transition:transform .55s var(--ease);display:flex;flex-direction:column}
.wta.is-open .wta__overlay{opacity:1}.wta.is-open .wta__panel{transform:none}
.wta.is-open .wta__panel>*{animation:wtaIn .6s var(--ease) both}
.wta.is-open .wta__panel>*:nth-child(2){animation-delay:.08s}.wta.is-open .wta__panel>*:nth-child(3){animation-delay:.12s}.wta.is-open .wta__panel>*:nth-child(4){animation-delay:.16s}.wta.is-open .wta__panel>*:nth-child(5){animation-delay:.2s}.wta.is-open .wta__panel>*:nth-child(6){animation-delay:.24s}
@keyframes wtaIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
.wta__close{position:absolute;top:22px;right:22px;width:42px;height:42px;border-radius:50%!important;border:0!important;background:var(--off)!important;color:var(--tx)!important;display:grid;place-items:center;padding:0!important;cursor:pointer;transition:background .3s,transform .45s var(--ease)}
.wta__close:hover{background:var(--sage)!important;transform:rotate(90deg)}
.wta__close svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round}
.wta__brand{font:600 14px/1 var(--sans);letter-spacing:.3em;color:var(--tx);margin:4px 0 34px}.wta__brand sup{font-size:8px;letter-spacing:0}
.wta__title{margin:0 0 8px!important;font:700 30px/1.1 var(--sans)!important;color:var(--tx)!important;letter-spacing:-.01em}
.wta__lead{margin:0 0 26px;font:400 14.5px/1.55 var(--sans);color:var(--tx2)}
.wta [data-t]{display:none}
.wta[data-view="login"] [data-t="login"],.wta[data-view="register"] [data-t="register"],.wta[data-view="lost"] [data-t="lost"],.wta[data-view="done"] [data-t="lost"]{display:inline;animation:wtaFade .4s var(--ease)}
@keyframes wtaFade{from{opacity:0}to{opacity:1}}
.wta__tabs{position:relative;display:grid;grid-template-columns:1fr 1fr;background:var(--off);border-radius:12px;padding:4px;margin-bottom:26px}
.wta__tabs button{position:relative;z-index:1;height:42px;border:0!important;background:none!important;color:var(--tx2)!important;font:600 14px/1 var(--sans)!important;cursor:pointer;border-radius:9px!important;padding:0!important;transition:color .3s}
.wta__tabs button[aria-selected="true"]{color:var(--tx)!important}
.wta__ink{position:absolute;top:4px;bottom:4px;left:4px;width:calc(50% - 4px);background:#fff;border-radius:9px;box-shadow:0 4px 14px -6px rgba(0,0,0,.25);transition:transform .45s var(--ease)}
.wta[data-view="register"] .wta__ink{transform:translateX(100%)}
.wta[data-view="lost"] .wta__tabs,.wta[data-view="done"] .wta__tabs{display:none}
.wta__views{position:relative}
.wta__form,.wta__done{display:none}
.wta__form.is-active,.wta__done.is-active{display:block;animation:wtaView .5s var(--ease)}
@keyframes wtaView{from{opacity:0;transform:translateX(18px)}to{opacity:1;transform:none}}
.wta__f{position:relative;margin-bottom:14px}
.wta__f input{width:100%;height:58px;padding:22px 16px 8px!important;border:1.5px solid var(--ln)!important;border-radius:12px!important;background:#fff!important;font:500 15px/1 var(--sans)!important;color:var(--tx)!important;outline:0!important;box-shadow:none!important;transition:border-color .3s,box-shadow .3s}
.wta__f--pw input{padding-right:52px!important}
.wta__f label{position:absolute;left:17px;top:19px;font:400 15px/1.2 var(--sans);color:#8a8f8b;pointer-events:none;transform-origin:left top;transition:transform .3s var(--ease),color .3s}
.wta__f input:focus{border-color:var(--g)!important;box-shadow:0 0 0 4px rgba(29,64,50,.1)!important}
.wta__f input:focus+label,.wta__f input:not(:placeholder-shown)+label{transform:translateY(-11px) scale(.76);color:var(--g)}
.wta__f.is-bad input{border-color:#C0563F!important;box-shadow:0 0 0 4px rgba(192,86,63,.1)!important}
.wta__eye{position:absolute;right:8px;top:9px;width:40px;height:40px;border:0!important;background:none!important;color:#8a8f8b!important;border-radius:50%!important;display:grid;place-items:center;padding:0!important;cursor:pointer;transition:color .3s,background .3s}
.wta__eye:hover{background:var(--off)!important;color:var(--tx)!important}
.wta__eye svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
.wta__eye.on svg path{stroke-dasharray:3 2}
.wta__eye.on{color:var(--g)!important}
.wta__row{display:flex;justify-content:space-between;align-items:center;margin:2px 0 22px}
.wta__check{display:inline-flex;align-items:center;gap:10px;font:500 13.5px/1.3 var(--sans);color:var(--tx2);cursor:pointer;user-select:none}
.wta__check input{position:absolute;opacity:0;width:0;height:0}
.wta__check span{width:20px;height:20px;border:1.5px solid #c7ccc6;border-radius:6px;display:grid;place-items:center;flex:none;transition:background .25s,border-color .25s}
.wta__check span:after{content:"";width:10px;height:6px;border:solid #fff;border-width:0 0 2px 2px;transform:rotate(-45deg) scale(0);margin-top:-2px;transition:transform .25s cubic-bezier(.34,1.56,.64,1)}
.wta__check input:checked+span{background:var(--g);border-color:var(--g)}.wta__check input:checked+span:after{transform:rotate(-45deg) scale(1)}
.wta__check input:focus-visible+span{box-shadow:0 0 0 3px rgba(29,64,50,.25)}
.wta__check--top{margin:6px 0 20px}
.wta__link{border:0!important;background:none!important;padding:0!important;color:var(--g)!important;font:600 13.5px/1.3 var(--sans)!important;cursor:pointer;position:relative;display:inline-flex;align-items:center;gap:6px}
.wta__link:after{content:"";position:absolute;left:0;right:0;bottom:-2px;height:1.5px;background:currentColor;transform:scaleX(0);transform-origin:right;transition:transform .35s var(--ease)}
.wta__link:hover:after{transform:scaleX(1);transform-origin:left}
.wta__back svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;transition:transform .3s var(--ease)}
.wta__back:hover svg{transform:translateX(-3px)}
.wta__btn{position:relative;width:100%;height:54px;border:0!important;border-radius:12px!important;background:var(--g)!important;color:#fff!important;font:600 15px/1 var(--sans)!important;cursor:pointer;overflow:hidden;display:grid;place-items:center;padding:0!important;transition:background .3s,transform .3s var(--ease),box-shadow .3s}
.wta__btn:hover{background:var(--g3)!important;transform:translateY(-2px);box-shadow:0 14px 28px -16px rgba(21,53,40,.7)}
.wta__btn:active{transform:translateY(1px) scale(.98)}
.wta__btn span{transition:opacity .2s,transform .3s var(--ease)}
.wta__btn.is-loading span{opacity:0;transform:translateY(-8px)}
.wta__btn.is-loading:after{content:"";position:absolute;width:20px;height:20px;border:2.5px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:wtpcSpin .7s linear infinite}
.wta__btn.is-done{background:#2F6B4F!important}
.wta__btn.is-done span{opacity:0}
.wta__btn.is-done:after{content:"";position:absolute;width:12px;height:22px;border:solid #fff;border-width:0 3px 3px 0;transform:rotate(45deg) translate(-2px,-2px);animation:wtaPop .4s cubic-bezier(.34,1.56,.64,1)}
@keyframes wtaPop{from{transform:rotate(45deg) scale(0)}to{transform:rotate(45deg) scale(1) translate(-2px,-2px)}}
@keyframes wtpcSpin{to{transform:rotate(360deg)}}
.wta__btn--ghost{background:#fff!important;color:var(--tx)!important;border:1.5px solid var(--tx)!important;margin-top:24px}
.wta__btn--ghost:hover{background:var(--off)!important}
.wta__switch{margin:22px 0 0;text-align:center;font:400 14px/1.4 var(--sans);color:var(--tx2)}
.wta__legal{margin:14px 0 0;text-align:center;font:400 12.5px/1.5 var(--sans);color:#8a8f8b}
.wta__legal a{color:var(--tx2);text-decoration:underline}
.wta__meter{display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin:-4px 2px 8px}
.wta__meter i{height:4px;border-radius:4px;background:#e6e8e4;transition:background .35s}
.wta__meter[data-score="1"] i:nth-child(-n+1){background:#C0563F}
.wta__meter[data-score="2"] i:nth-child(-n+2){background:#E5AC32}
.wta__meter[data-score="3"] i:nth-child(-n+3){background:#7FA36B}
.wta__meter[data-score="4"] i{background:var(--g)}
.wta__hint{margin:0 2px 4px;font:400 12.5px/1.4 var(--sans);color:#8a8f8b}
.wta__hp{position:absolute!important;left:-9999px}
.wta__done{text-align:center;padding:12px 0}
.wta__tick{width:76px;height:76px;margin:0 auto 18px;display:block;fill:none;stroke:var(--g);stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}
.wta__tick circle{stroke-dasharray:145;stroke-dashoffset:145;animation:wtaDraw .7s var(--ease) forwards}
.wta__tick path{stroke-dasharray:36;stroke-dashoffset:36;animation:wtaDraw .45s .55s var(--ease) forwards}
@keyframes wtaDraw{to{stroke-dashoffset:0}}
.wta__done h3{margin:0 0 8px;font:700 22px/1.2 var(--sans);color:var(--tx)}
.wta__done p{margin:0 auto;max-width:330px;font:400 14.5px/1.55 var(--sans);color:var(--tx2)}
.wta[data-view="done"] .wta__title,.wta[data-view="done"] .wta__lead{display:none}
.wta__msg{margin:16px 0 0;min-height:20px;font:500 13.5px/1.45 var(--sans);text-align:center}
.wta__msg.is-err{color:#A8432E}.wta__msg.is-ok{color:var(--g)}
.wta__form.shake{animation:wtaShake .45s cubic-bezier(.36,.07,.19,.97)}
@keyframes wtaShake{10%,90%{transform:translateX(-2px)}20%,80%{transform:translateX(4px)}30%,50%,70%{transform:translateX(-6px)}40%,60%{transform:translateX(6px)}}
@media (max-width:480px){.wta__panel{padding:26px 22px 24px}.wta__title{font-size:26px!important}.wta__brand{margin-bottom:26px}}
@media (prefers-reduced-motion:reduce){.wta *,.wta__panel,.wta__overlay{animation:none!important;transition:none!important}}
CSS;
}
