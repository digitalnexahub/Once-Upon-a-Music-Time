<?php
/**
 * Plugin Name: Washtower Design Layer
 * Description: Header, footer, homepage components (product carousel, reviews, blog cards, hotspots, newsletter), animations and shop styling for the WASHTOWER homepage recreation.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------------------------------------------------------------- assets */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'wt-fonts', 'https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap', [], null );
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
}, 1 );

/* ---------- logged-in account panel + My Account dashboard ---------- */

function wt_account_data( $uid ) {
	$paid   = [ 'wc-completed', 'wc-processing', 'wc-on-hold' ];
	$orders = wc_get_orders( [ 'customer_id' => $uid, 'limit' => -1, 'status' => array_keys( wc_get_order_statuses() ), 'orderby' => 'date', 'order' => 'DESC' ] );
	$months = [];
	for ( $i = 11; $i >= 0; $i-- ) { $months[ gmdate( 'Y-m', strtotime( "first day of -$i month" ) ) ] = 0.0; }
	$spent = 0.0; $status = [];
	foreach ( $orders as $o ) {
		$st = 'wc-' . $o->get_status();
		$status[ $o->get_status() ] = ( $status[ $o->get_status() ] ?? 0 ) + 1;
		if ( ! in_array( $st, $paid, true ) ) { continue; }
		$spent += (float) $o->get_total();
		$k = $o->get_date_created() ? $o->get_date_created()->date( 'Y-m' ) : '';
		if ( isset( $months[ $k ] ) ) { $months[ $k ] += (float) $o->get_total(); }
	}
	return [ 'orders' => $orders, 'months' => $months, 'spent' => $spent, 'status' => $status ];
}

function wt_spend_chart( $months ) {
	$w = 640; $h = 220; $pl = 8; $pr = 8; $pt = 18; $pb = 30;
	$max = max( $months ) ?: 1; $n = count( $months ); $step = ( $w - $pl - $pr ) / ( $n - 1 );
	$pts = []; $i = 0;
	foreach ( $months as $k => $v ) { $pts[] = [ $pl + $i * $step, $pt + ( $h - $pt - $pb ) * ( 1 - $v / $max ), $k, $v ]; $i++; }
	$line = ''; foreach ( $pts as $j => $p ) {
		if ( 0 === $j ) { $line .= 'M' . round( $p[0], 1 ) . ' ' . round( $p[1], 1 ); continue; }
		$q = $pts[ $j - 1 ]; $cx = ( $q[0] + $p[0] ) / 2;
		$line .= ' C' . round( $cx, 1 ) . ' ' . round( $q[1], 1 ) . ' ' . round( $cx, 1 ) . ' ' . round( $p[1], 1 ) . ' ' . round( $p[0], 1 ) . ' ' . round( $p[1], 1 );
	}
	$area = $line . ' L' . round( end( $pts )[0], 1 ) . ' ' . ( $h - $pb ) . ' L' . $pl . ' ' . ( $h - $pb ) . ' Z';
	$o  = '<svg class="wtd-chart__svg" viewBox="0 0 ' . $w . ' ' . $h . '" preserveAspectRatio="none" role="img" aria-label="Spending per month for the last 12 months">';
	$o .= '<defs><linearGradient id="wtdG" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1D4032" stop-opacity=".28"/><stop offset="1" stop-color="#1D4032" stop-opacity="0"/></linearGradient></defs>';
	for ( $g = 0; $g < 4; $g++ ) { $y = $pt + ( $h - $pt - $pb ) * $g / 3; $o .= '<line class="wtd-chart__grid" x1="0" x2="' . $w . '" y1="' . $y . '" y2="' . $y . '"/>'; }
	$o .= '<path class="wtd-chart__area" d="' . $area . '"/><path class="wtd-chart__line" d="' . $line . '" pathLength="1"/>';
	foreach ( $pts as $p ) {
		$label = date_i18n( 'M Y', strtotime( $p[2] . '-01' ) );
		$o .= '<g class="wtd-chart__pt" data-label="' . esc_attr( $label ) . '" data-value="' . esc_attr( wp_strip_all_tags( wc_price( $p[3] ) ) ) . '"><rect x="' . round( $p[0] - $step / 2, 1 ) . '" y="0" width="' . round( $step, 1 ) . '" height="' . $h . '" fill="transparent"/><circle cx="' . round( $p[0], 1 ) . '" cy="' . round( $p[1], 1 ) . '" r="4.5"/></g>';
	}
	$o .= '</svg><div class="wtd-chart__x">';
	foreach ( $pts as $p ) { $o .= '<span>' . esc_html( date_i18n( 'M', strtotime( $p[2] . '-01' ) ) ) . '</span>'; }
	return $o . '</div><div class="wtd-chart__tip" aria-hidden="true"></div>';
}

function wt_ic( $name ) {
	$p = [
		'home' => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/>', 'box' => '<path d="M3 7l9-4 9 4v10l-9 4-9-4z"/><path d="M3 7l9 4 9-4M12 11v10"/>',
		'pin' => '<path d="M12 21s7-6.5 7-12a7 7 0 0 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/>', 'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
		'out' => '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 17l-5-5 5-5M5 12h11"/>', 'bag' => '<path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'pound' => '<path d="M17 19H7c2-1.5 2.5-3 2.5-5V8.5a3.5 3.5 0 0 1 6.8-1.2M7 13h7"/>', 'cal' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
		'pen' => '<path d="M4 20l4-1 11-11-3-3L5 16z"/>', 'dl' => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>', 'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
	];
	return '<svg class="wt-ic2" viewBox="0 0 24 24" aria-hidden="true">' . ( $p[ $name ] ?? '' ) . '</svg>';
}

/* logged-in panel (opens from the header account icon) */
add_action( 'wp_footer', function () {
	if ( ! is_user_logged_in() || ! function_exists( 'wc_get_account_endpoint_url' ) ) { return; }
	$u = wp_get_current_user(); $d = wt_account_data( $u->ID );
	$name = $u->first_name ?: $u->display_name;
	$links = [ [ 'home', 'Dashboard', wc_get_account_endpoint_url( 'dashboard' ) ], [ 'box', 'Orders', wc_get_account_endpoint_url( 'orders' ) ], [ 'pin', 'Addresses', wc_get_account_endpoint_url( 'edit-address' ) ], [ 'user', 'Account details', wc_get_account_endpoint_url( 'edit-account' ) ] ];
	?>
	<div class="wta" id="wtu" hidden data-view="user">
		<div class="wta__overlay" data-wta-close></div>
		<aside class="wta__panel" role="dialog" aria-modal="true" aria-labelledby="wtu-title">
			<button type="button" class="wta__close" data-wta-close aria-label="Close"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
			<div class="wta__brand">WASHTOWER<sup>&reg;</sup></div>
			<div class="wtu__me"><span class="wtu__av"><?php echo esc_html( strtoupper( mb_substr( $name, 0, 1 ) ) ); ?></span><span><strong id="wtu-title">Hi, <?php echo esc_html( $name ); ?></strong><small><?php echo esc_html( $u->user_email ); ?></small></span></div>
			<div class="wtu__stats"><div><b><?php echo count( $d['orders'] ); ?></b><span>Orders</span></div><div><b><?php echo wp_kses_post( wc_price( $d['spent'], [ 'decimals' => 0 ] ) ); ?></b><span>Total spent</span></div><div><b><?php echo (int) ( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></b><span>In basket</span></div></div>
			<nav class="wtu__links" aria-label="Account">
				<?php foreach ( $links as $i => $l ) : ?><a href="<?php echo esc_url( $l[2] ); ?>" style="--i:<?php echo (int) $i; ?>"><?php echo wt_ic( $l[0] ); ?><span><?php echo esc_html( $l[1] ); ?></span><?php echo wt_ic( 'arrow' ); ?></a><?php endforeach; ?>
			</nav>
			<a class="wta__btn wtu__out" href="<?php echo esc_url( wc_logout_url() ); ?>"><span><?php echo wt_ic( 'out' ); ?>Log out</span></a>
		</aside>
	</div>
	<?php
}, 1 );

/* My Account: dashboard with stats, spending graph and recent orders */
add_action( 'woocommerce_account_dashboard', function () {
	$u = wp_get_current_user(); $d = wt_account_data( $u->ID );
	$name   = $u->first_name ?: $u->display_name;
	$recent = array_slice( $d['orders'], 0, 4 );
	$basket = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	$since  = $u->user_registered ? date_i18n( 'M Y', strtotime( $u->user_registered ) ) : '';
	$has    = array_sum( $d['months'] ) > 0;
	?>
	<div class="wtd">
		<div class="wtd-hello">
			<div><p class="wtd-eyebrow">Your account</p><h2>Welcome back, <?php echo esc_html( $name ); ?></h2><p>Track orders, manage addresses and keep your laundry room plans in one place.</p></div>
			<a class="wtd-cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Shop cabinets <?php echo wt_ic( 'arrow' ); ?></a>
		</div>
		<div class="wtd-stats">
			<div class="wtd-stat" style="--i:0"><span class="wtd-stat__ic"><?php echo wt_ic( 'box' ); ?></span><b data-count="<?php echo count( $d['orders'] ); ?>"><?php echo count( $d['orders'] ); ?></b><small>Orders placed</small></div>
			<div class="wtd-stat" style="--i:1"><span class="wtd-stat__ic"><?php echo wt_ic( 'pound' ); ?></span><b><?php echo wp_kses_post( wc_price( $d['spent'], [ 'decimals' => 0 ] ) ); ?></b><small>Total spent</small></div>
			<div class="wtd-stat" style="--i:2"><span class="wtd-stat__ic"><?php echo wt_ic( 'bag' ); ?></span><b data-count="<?php echo (int) $basket; ?>"><?php echo (int) $basket; ?></b><small>Items in basket</small></div>
			<div class="wtd-stat" style="--i:3"><span class="wtd-stat__ic"><?php echo wt_ic( 'cal' ); ?></span><b><?php echo esc_html( $since ); ?></b><small>Member since</small></div>
		</div>
		<div class="wtd-grid">
			<section class="wtd-card wtd-chart">
				<header><div><h3>Spending</h3><p>Last 12 months</p></div><b><?php echo wp_kses_post( wc_price( array_sum( $d['months'] ), [ 'decimals' => 0 ] ) ); ?></b></header>
				<div class="wtd-chart__box<?php echo $has ? '' : ' is-empty'; ?>"><?php echo wt_spend_chart( $d['months'] ); ?><?php if ( ! $has ) : ?><p class="wtd-chart__empty">Your spending will appear here after your first order.</p><?php endif; ?></div>
			</section>
			<section class="wtd-card wtd-recent">
				<header><div><h3>Recent orders</h3><p><?php echo count( $d['orders'] ) ? 'Your latest purchases' : 'Nothing ordered yet'; ?></p></div><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">View all</a></header>
				<?php if ( $recent ) : ?><ul>
					<?php foreach ( $recent as $o ) : ?><li><a href="<?php echo esc_url( $o->get_view_order_url() ); ?>"><span class="wtd-o__n">#<?php echo esc_html( $o->get_order_number() ); ?><small><?php echo esc_html( wc_format_datetime( $o->get_date_created() ) ); ?></small></span><span class="wtd-pill wtd-pill--<?php echo esc_attr( $o->get_status() ); ?>"><?php echo esc_html( wc_get_order_status_name( $o->get_status() ) ); ?></span><b><?php echo wp_kses_post( $o->get_formatted_order_total() ); ?></b></a></li><?php endforeach; ?>
				</ul><?php else : ?>
					<div class="wtd-empty"><?php echo wt_ic( 'box' ); ?><p>When you place an order it will show up here with live status updates.</p><a class="wtd-cta wtd-cta--sm" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Start shopping <?php echo wt_ic( 'arrow' ); ?></a></div>
				<?php endif; ?>
			</section>
		</div>
		<div class="wtd-quick">
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>"><?php echo wt_ic( 'pin' ); ?><span><b>Addresses</b><small>Billing &amp; delivery</small></span></a>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>"><?php echo wt_ic( 'pen' ); ?><span><b>Account details</b><small>Name, email &amp; password</small></span></a>
			<a href="<?php echo esc_url( ( $p = get_page_by_title( 'Get a Free Design' ) ) ? get_permalink( $p ) : home_url( '/' ) ); ?>"><?php echo wt_ic( 'home' ); ?><span><b>Free design</b><small>Plan your laundry room</small></span></a>
		</div>
	</div>
	<?php
}, 1 );

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

/* ---------------------------------------------------------------- cart / checkout / single product */

function wt_steps( $active ) {
	$steps = [ 'Basket', 'Details', 'Payment', 'Done' ];
	$o = '<nav class="wt-steps-bar" aria-label="Checkout progress"><ol>';
	foreach ( $steps as $i => $st ) {
		$n = $i + 1; $cls = $n < $active ? 'is-done' : ( $n === $active ? 'is-current' : '' );
		$o .= '<li class="' . $cls . '"' . ( $n === $active ? ' aria-current="step"' : '' ) . '><span class="wt-steps-bar__dot">' . ( $n < $active ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 12.5l4 4L18 8"/></svg>' : $n ) . '</span><span>' . esc_html( $st ) . '</span></li>';
	}
	return $o . '</ol></nav>';
}
function wt_trust_row() {
	return '<ul class="wt-trustmini"><li>' . wt_ic( 'box' ) . 'Free UK delivery</li><li><svg class="wt-ic2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/></svg>10 year guarantee</li><li><svg class="wt-ic2" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>Secure checkout</li></ul>';
}

add_action( 'woocommerce_before_cart', function () {
	echo '<div class="wt-co-head">' . wt_steps( 1 ) . '<h1 class="wt-co-title">Your basket</h1><p class="wt-co-sub">' . (int) WC()->cart->get_cart_contents_count() . ' item' . ( WC()->cart->get_cart_contents_count() === 1 ? '' : 's' ) . ' &middot; free UK delivery on every cabinet order</p></div>';
}, 1 );
add_action( 'woocommerce_cart_is_empty', function () {
	echo '<div class="wt-co-head">' . wt_steps( 1 ) . '</div><div class="wt-empty-cart"><div class="wt-empty-cart__ic">' . wt_ic( 'bag' ) . '</div><h1>Your basket is empty</h1><p>Find the perfect cabinet for your laundry room, or let our designers plan it with you for free.</p><div class="wt-empty-cart__btns"><a class="wtd-cta wtd-cta--sm" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Shop cabinets ' . wt_ic( 'arrow' ) . '</a></div></div>';
}, 1 );
add_action( 'woocommerce_after_cart_totals', function () { echo wt_trust_row(); } );
add_action( 'woocommerce_before_checkout_form', function () {
	echo '<div class="wt-co-head">' . wt_steps( 2 ) . '<h1 class="wt-co-title">Checkout</h1><p class="wt-co-sub">Secure checkout &middot; your details are encrypted</p></div>';
}, 1 );
add_action( 'woocommerce_review_order_after_submit', function () { echo wt_trust_row(); } );
add_action( 'woocommerce_before_thankyou', function () { echo '<div class="wt-co-head">' . wt_steps( 4 ) . '</div>'; }, 1 );
add_filter( 'body_class', function ( $c ) { if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) { $c[] = 'wt-co'; } return $c; } );

/* single product extras */
add_action( 'woocommerce_single_product_summary', function () {
	global $product; $cats = wc_get_product_category_list( $product->get_id(), ' &middot; ' );
	if ( $cats ) { echo '<p class="wt-sp-eyebrow">' . wp_kses_post( $cats ) . '</p>'; }
}, 4 );
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( $product->is_on_sale() && $product->get_regular_price() ) {
		$r = (float) $product->get_regular_price(); $s = (float) $product->get_price();
		if ( $r > $s ) { echo '<p class="wt-sp-save">Save ' . wp_kses_post( wc_price( $r - $s, [ 'decimals' => 0 ] ) ) . ' <span>' . round( ( 1 - $s / $r ) * 100 ) . '% off</span></p>'; }
	}
}, 11 );
add_action( 'woocommerce_single_product_summary', function () {
	$from = date_i18n( 'j M', strtotime( '+7 days' ) ); $to = date_i18n( 'j M', strtotime( '+28 days' ) );
	echo '<div class="wt-sp-deliv"><span class="wt-sp-deliv__ic">' . wt_ic( 'box' ) . '</span><span><b>Free delivery ' . esc_html( $from . ' – ' . $to ) . '</b><small>Signature delivery to your door, anywhere in the UK</small></span></div>';
}, 31 );
add_action( 'wp', function () { remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 ); } );

/* Buy now: add to basket and go straight to checkout. */
add_action( 'woocommerce_after_add_to_cart_button', function () {
	global $product;
	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) { return; }
	if ( $product->is_type( 'simple' ) ) { echo '<input type="hidden" name="add-to-cart" value="' . esc_attr( $product->get_id() ) . '">'; }
	echo '<button type="submit" name="wt_buy_now" value="1" class="wt-buynow"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg>Buy now</button>';
} );
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	return ! empty( $_REQUEST['wt_buy_now'] ) ? wc_get_checkout_url() : $url;
}, 99 );
add_action( 'wp_footer', function () {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) { return; }
	global $product; if ( ! $product ) { $product = wc_get_product( get_the_ID() ); }
	if ( ! $product ) { return; }
	echo '<div class="wt-sticky-buy" aria-hidden="true"><div class="wt-sticky-buy__in">' . $product->get_image( 'thumbnail' ) . '<span class="wt-sticky-buy__t"><b>' . esc_html( $product->get_name() ) . '</b>' . wp_kses_post( $product->get_price_html() ) . '</span><button type="button" class="wt-sticky-buy__btn" tabindex="-1">' . ( $product->is_type( 'simple' ) ? 'Add to basket' : 'Choose options' ) . '</button></div></div>';
}, 1 );

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
/* Testimonials you add by hand: WP Admin > Testimonials. */
add_action( 'init', function () {
	register_post_type( 'wt_testimonial', [
		'label' => 'Testimonials', 'labels' => [ 'name' => 'Testimonials', 'singular_name' => 'Testimonial', 'add_new_item' => 'Add testimonial', 'edit_item' => 'Edit testimonial', 'new_item' => 'New testimonial' ],
		'public' => true, 'publicly_queryable' => false, 'exclude_from_search' => true, 'has_archive' => false, 'rewrite' => false, 'show_in_nav_menus' => true, 'show_ui' => true, 'show_in_menu' => true, 'menu_icon' => 'dashicons-format-quote', 'menu_position' => 26,
		'supports' => [ 'title', 'editor', 'thumbnail', 'page-attributes' ], 'show_in_rest' => false,
	] );
} );
add_filter( 'enter_title_here', function ( $t, $p ) { return 'wt_testimonial' === $p->post_type ? 'Customer name (e.g. Sarah L.)' : $t; }, 10, 2 );
add_action( 'add_meta_boxes_wt_testimonial', function () {
	add_meta_box( 'wt_t', 'Review details', function ( $p ) {
		wp_nonce_field( 'wt_t', 'wt_t_nonce' );
		wp_enqueue_media();
		$r  = (int) get_post_meta( $p->ID, '_wt_rating', true ) ?: 5;
		$av = (int) get_post_meta( $p->ID, '_wt_avatar', true );
		echo '<p><label><b>Rating</b><br><select name="wt_rating">';
		for ( $i = 5; $i >= 1; $i-- ) { echo '<option value="' . $i . '"' . selected( $r, $i, false ) . '>' . str_repeat( '★', $i ) . '</option>'; }
		echo '</select></label></p>';
		echo '<p><label><input type="checkbox" name="wt_verified" value="1"' . checked( (bool) get_post_meta( $p->ID, '_wt_verified', true ), true, false ) . '> Verified buyer (only tick if this person really bought from you)</label></p>';
		echo '<p><b>Customer photo</b> (optional, with their permission)<br><img id="wt_av_img" src="' . esc_url( $av ? wp_get_attachment_image_url( $av, 'thumbnail' ) : '' ) . '" style="width:56px;height:56px;border-radius:50%;object-fit:cover;' . ( $av ? '' : 'display:none;' ) . '"><input type="hidden" name="wt_avatar" id="wt_avatar" value="' . esc_attr( $av ?: '' ) . '"> <button type="button" class="button" id="wt_av_btn">Choose photo</button> <button type="button" class="button-link" id="wt_av_rm">Remove</button></p>';
		echo '<p class="description">Review text goes in the main editor. The room photo on the card is the <b>Featured image</b> (right sidebar).</p>';
		echo <<<'JS'
<script>jQuery(function($){var f;$('#wt_av_btn').on('click',function(){f=f||wp.media({title:'Customer photo',multiple:false,library:{type:'image'}});f.off('select').on('select',function(){var a=f.state().get('selection').first().toJSON();$('#wt_avatar').val(a.id);$('#wt_av_img').attr('src',(a.sizes&&a.sizes.thumbnail?a.sizes.thumbnail.url:a.url)).show();});f.open();});$('#wt_av_rm').on('click',function(){$('#wt_avatar').val('');$('#wt_av_img').hide();});});</script>
JS;
	}, 'wt_testimonial', 'side', 'high' );
} );
add_action( 'save_post_wt_testimonial', function ( $id ) {
	if ( ! isset( $_POST['wt_t_nonce'] ) || ! wp_verify_nonce( $_POST['wt_t_nonce'], 'wt_t' ) || ! current_user_can( 'edit_post', $id ) ) { return; }
	update_post_meta( $id, '_wt_rating', max( 1, min( 5, (int) ( $_POST['wt_rating'] ?? 5 ) ) ) );
	update_post_meta( $id, '_wt_verified', empty( $_POST['wt_verified'] ) ? '' : '1' );
	update_post_meta( $id, '_wt_avatar', absint( $_POST['wt_avatar'] ?? 0 ) );
} );

add_shortcode( 'wt_reviews', function () {
	$photos   = [ 63, 64, 65 ];
	$reviews  = get_comments( [ 'type' => 'review', 'status' => 'approve', 'number' => 12, 'meta_key' => 'rating', 'orderby' => 'comment_date', 'order' => 'DESC' ] );
	$cards    = [];
	foreach ( get_posts( [ 'post_type' => 'wt_testimonial', 'post_status' => 'publish', 'numberposts' => 12, 'orderby' => 'menu_order date', 'order' => 'ASC' ] ) as $t ) {
		$av = (int) get_post_meta( $t->ID, '_wt_avatar', true );
		$cards[] = [ max( 1, min( 5, (int) get_post_meta( $t->ID, '_wt_rating', true ) ?: 5 ) ), wp_strip_all_tags( $t->post_content ), get_the_title( $t ), get_post_meta( $t->ID, '_wt_verified', true ) ? 'Verified buyer' : 'Customer', $av ? wp_get_attachment_image_url( $av, 'thumbnail' ) : '', get_post_thumbnail_id( $t ) ];
	}
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
	$o = $placeholder ? '<p class="wt-admin-note">Editor-only preview: no reviews yet. Add them in WP Admin &rarr; Testimonials &rarr; Add testimonial. Visitors see a short call to action until then.</p>' : '';
	$o .= '<div class="wt-carousel wt-rev-carousel wt-reveal"><div class="wt-car-track wt-reviews" tabindex="0" aria-label="Customer reviews">';
	foreach ( $cards as $i => $c ) {
		$av = $c[4] ? '<img src="' . esc_url( $c[4] ) . '" alt="" width="36" height="36" loading="lazy">' : '<span class="wt-av" aria-hidden="true">' . esc_html( mb_substr( $c[2], 0, 1 ) ) . '</span>';
		$ph = ! empty( $c[5] ) ? (int) $c[5] : $photos[ $i % 3 ];
		$o .= '<figure class="wt-rcard"><div class="wt-rcard__t">' . wt_stars( $c[0] ) . '<blockquote>“' . esc_html( wp_trim_words( $c[1], 24 ) ) . '”</blockquote><figcaption>' . $av . '<span><b>' . esc_html( $c[2] ) . '</b><small>' . esc_html( $c[3] ) . '</small></span></figcaption></div>' . wp_get_attachment_image( $ph, 'medium', false, [ 'class' => 'wt-rcard__img', 'loading' => 'lazy', 'alt' => '' ] ) . '</figure>';
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
	echo '<section class="wt-archive-hero"><div class="wt-in"><nav class="wt-crumbs" aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a><span>/</span><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Shop</a>' . ( is_shop() ? '' : '<span>/</span><b>' . esc_html( $title ) . '</b>' ) . '</nav><span class="wt-label">Explore our range</span><h1>' . esc_html( $title ) . '</h1><p>' . esc_html( $desc ) . '</p><div class="wt-chips">';
	echo '<a class="' . ( is_shop() ? 'on' : '' ) . '" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">All<i>' . (int) wp_count_posts( 'product' )->publish . '</i></a>';
	foreach ( get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true ] ) as $c ) {
		if ( 'uncategorized' === $c->slug ) { continue; }
		echo '<a class="' . ( is_product_category( $c->slug ) ? 'on' : '' ) . '" href="' . esc_url( get_term_link( $c ) ) . '">' . esc_html( $c->name ) . '<i>' . (int) $c->count . '</i></a>';
	}
	echo '</div></div></section>';
}, 1 );

add_action( 'woocommerce_single_product_summary', function () {
	echo '<ul class="wt-trust"><li>' . wt_icon( 'truck' ) . 'Free UK delivery</li><li>' . wt_icon( 'shield' ) . '10 year guarantee</li><li>' . wt_icon( 'leaf' ) . 'FSC certified materials</li><li>' . wt_icon( 'star' ) . 'Rated 4.8/5</li></ul>';
}, 35 );

add_filter( 'loop_shop_columns', function () { return 4; } );

/* Archive: toolbar, card badges, category label, hover image. */
add_action( 'wp', function () {
	if ( ! is_shop() && ! is_product_taxonomy() ) { return; }
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
} );
add_action( 'woocommerce_before_shop_loop', function () { echo '<div class="wt-toolbar">'; }, 19 );
add_action( 'woocommerce_before_shop_loop', function () {
	echo '<div class="wt-view" role="group" aria-label="Grid size"><button type="button" data-cols="3" aria-label="3 per row"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="5" height="5"/><rect x="9.5" y="3" width="5" height="5"/><rect x="16" y="3" width="5" height="5"/><rect x="3" y="10" width="5" height="5"/><rect x="9.5" y="10" width="5" height="5"/><rect x="16" y="10" width="5" height="5"/></svg></button><button type="button" data-cols="4" aria-label="4 per row"><svg viewBox="0 0 24 24"><rect x="2" y="4" width="4" height="4"/><rect x="7.3" y="4" width="4" height="4"/><rect x="12.6" y="4" width="4" height="4"/><rect x="18" y="4" width="4" height="4"/><rect x="2" y="10" width="4" height="4"/><rect x="7.3" y="10" width="4" height="4"/><rect x="12.6" y="10" width="4" height="4"/><rect x="18" y="10" width="4" height="4"/></svg></button></div></div>';
}, 31 );
add_action( 'woocommerce_before_shop_loop_item_title', function () {
	global $product;
	if ( ! $product ) { return; }
	if ( $product->is_on_sale() && $product->get_regular_price() > 0 && $product->get_price() > 0 ) {
		$pc = round( 100 - ( (float) $product->get_price() / (float) $product->get_regular_price() ) * 100 );
		if ( $pc > 0 ) { echo '<span class="wt-pbadge">-' . (int) $pc . '%</span>'; }
	}
	$g = $product->get_gallery_image_ids();
	if ( $g ) { echo wp_get_attachment_image( $g[0], 'woocommerce_thumbnail', false, [ 'class' => 'wt-alt-img', 'loading' => 'lazy', 'alt' => '' ] ); }
}, 11 );
add_action( 'woocommerce_shop_loop_item_title', function () {
	global $product;
	$t = $product ? get_the_terms( $product->get_id(), 'product_cat' ) : false;
	if ( $t && ! is_wp_error( $t ) ) { echo '<span class="wt-pcat">' . esc_html( $t[0]->name ) . '</span>'; }
}, 9 );
add_filter( 'woocommerce_product_single_add_to_cart_text', function () { return __( 'Add to basket', 'woocommerce' ); } );
add_filter( 'woocommerce_product_add_to_cart_text', function ( $t, $p ) {
	return ( $p && $p->is_purchasable() && $p->is_in_stock() && $p->is_type( 'simple' ) ) ? __( 'Add to basket', 'woocommerce' ) : $t;
}, 10, 2 );

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
 A.addEventListener('click',function(e){if(e.target.closest('[data-wta-close]'))return close();var v=e.target.closest('button[data-view]');if(v&&A.contains(v)){e.preventDefault();show(v.getAttribute('data-view'));}
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
/* logged-in account panel */
(function(){var U=d.getElementById('wtu');if(!U)return;var last=null;
 function open(){last=d.activeElement;U.hidden=false;void U.offsetWidth;U.classList.add('is-open');d.body.classList.add('wt-lock');setTimeout(function(){var a=U.querySelector('.wtu__links a');if(a)a.focus();},400);}
 function close(){U.classList.remove('is-open');d.body.classList.remove('wt-lock');setTimeout(function(){U.hidden=true;},reduce?0:450);if(last&&last.focus)last.focus();}
 d.addEventListener('click',function(e){var a=e.target.closest&&e.target.closest('.wth-ic a');if(a&&!e.ctrlKey&&!e.metaKey){e.preventDefault();open();}});
 U.addEventListener('click',function(e){if(e.target.closest('[data-wta-close]'))close();});
 d.addEventListener('keydown',function(e){if(e.key==='Escape'&&U.classList.contains('is-open'))close();});
})();
/* My Account spending chart tooltip */
d.querySelectorAll('.wtd-chart__box').forEach(function(box){var tip=box.querySelector('.wtd-chart__tip'),svg=box.querySelector('svg');if(!tip||!svg)return;
 box.querySelectorAll('.wtd-chart__pt').forEach(function(g){g.addEventListener('mouseenter',function(){var c=g.querySelector('circle'),r=c.getBoundingClientRect(),b=box.getBoundingClientRect();tip.innerHTML='<b>'+g.getAttribute('data-value')+'</b><span>'+g.getAttribute('data-label')+'</span>';tip.style.left=(r.left-b.left+r.width/2)+'px';tip.style.top=(r.top-b.top)+'px';tip.classList.add('on');g.classList.add('on');});
  g.addEventListener('mouseleave',function(){tip.classList.remove('on');g.classList.remove('on');});});});
/* quantity stepper (cart + product page) */
function wtQty(scope){(scope||d).querySelectorAll('.quantity:not(.wt-q)').forEach(function(q){var i=q.querySelector('input.qty');if(!i||i.type==='hidden')return;q.classList.add('wt-q');
 var m=d.createElement('button'),p=d.createElement('button');m.type=p.type='button';m.className='wt-q__b';p.className='wt-q__b';m.setAttribute('aria-label','Decrease quantity');p.setAttribute('aria-label','Increase quantity');m.innerHTML='&minus;';p.innerHTML='+';q.insertBefore(m,i);q.appendChild(p);
 function set(v){var mn=parseFloat(i.min)||0,mx=parseFloat(i.max)||Infinity,st=parseFloat(i.step)||1;v=Math.max(mn,Math.min(mx,v));if(v===parseFloat(i.value))return;i.value=v;i.dispatchEvent(new Event('change',{bubbles:true}));i.dispatchEvent(new Event('input',{bubbles:true}));q.classList.remove('bump');void q.offsetWidth;q.classList.add('bump');}
 m.addEventListener('click',function(){set((parseFloat(i.value)||0)-(parseFloat(i.step)||1));});p.addEventListener('click',function(){set((parseFloat(i.value)||0)+(parseFloat(i.step)||1));});});}
wtQty();
/* cart: update automatically when a quantity changes */
var cartForm=d.querySelector('.woocommerce-cart-form');
if(cartForm){var ct;cartForm.addEventListener('change',function(e){if(!e.target.classList.contains('qty'))return;clearTimeout(ct);ct=setTimeout(function(){var u=cartForm.querySelector('[name="update_cart"]');if(u){u.disabled=false;u.click();}},450);});}
if(window.jQuery){jQuery(d.body).on('updated_cart_totals updated_wc_div',function(){wtQty();});}
/* product tabs -> accordion */
d.querySelectorAll('.single-product .woocommerce-tabs').forEach(function(t){var tabs=t.querySelectorAll('ul.tabs li a');t.classList.add('wt-acc');
 tabs.forEach(function(a,i){var panel=t.querySelector(a.getAttribute('href'));if(!panel)return;var b=d.createElement('button');b.type='button';b.className='wt-acc__h';b.setAttribute('aria-expanded',i===0?'true':'false');b.innerHTML='<span>'+a.textContent+'</span><i aria-hidden="true"></i>';panel.parentNode.insertBefore(b,panel);panel.classList.add('wt-acc__p');if(!panel.querySelector(':scope>.wt-acc__in')){var w=d.createElement('div');w.className='wt-acc__in';while(panel.firstChild){w.appendChild(panel.firstChild);}panel.appendChild(w);}panel.style.display='';if(i===0)panel.classList.add('open');
  b.addEventListener('click',function(){var o=b.getAttribute('aria-expanded')!=='true';b.setAttribute('aria-expanded',o);panel.classList.toggle('open',o);});});});
/* product: sticky buy bar */
var sb=d.querySelector('.wt-sticky-buy'),cartF=d.querySelector('.single-product form.cart');
if(sb&&cartF&&'IntersectionObserver' in window){new IntersectionObserver(function(en){en.forEach(function(x){var show=!x.isIntersecting&&x.boundingClientRect.top<0;sb.classList.toggle('show',show);sb.setAttribute('aria-hidden',show?'false':'true');sb.querySelector('button').tabIndex=show?0:-1;});}).observe(cartF);
 sb.querySelector('button').addEventListener('click',function(){var btn=cartF.querySelector('.single_add_to_cart_button');if(btn&&cartF.querySelector('input[name="add-to-cart"],button[name="add-to-cart"]')&&!cartF.classList.contains('variations_form')){btn.click();}else{cartF.scrollIntoView({behavior:reduce?'auto':'smooth',block:'center'});}});}
/* product gallery: soft fade when switching image */
d.querySelectorAll('.single-product .flex-control-thumbs img').forEach(function(t){t.addEventListener('click',function(){var g=d.querySelector('.woocommerce-product-gallery');if(!g)return;g.classList.remove('wt-swap');void g.offsetWidth;g.classList.add('wt-swap');});});
/* add to cart feedback (WooCommerce triggers jQuery events) */
if(window.jQuery){var bt=function(btn,t){var n=btn[0];if(!n)return;for(var k=0;k<n.childNodes.length;k++){if(n.childNodes[k].nodeType===3&&n.childNodes[k].nodeValue.trim()){n.childNodes[k].nodeValue=t;return;}}n.insertBefore(d.createTextNode(t),n.firstChild);};
jQuery(d.body).on('adding_to_cart',function(e,btn){if(btn&&btn.addClass){btn.addClass('is-loading');bt(btn,'Adding…');}}).on('added_to_cart',function(e,f,h,btn){if(btn&&btn.removeClass&&btn.length){btn.removeClass('is-loading').addClass('is-done');bt(btn,'Added');setTimeout(function(){btn.removeClass('is-done added');bt(btn,'Add to basket');},2400);}}).on('ajax_request_not_sent.adding_to_cart',function(e,btn){if(btn&&btn.removeClass){btn.removeClass('is-loading');bt(btn,'Add to basket');}});
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
.wt-note .wt-note__t{font:italic 500 24px/1.05 'Barlow',sans-serif;color:#2E3A33;transform:rotate(-6deg);display:block;text-align:right}
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
.wth-cart .elementor-menu-cart__toggle .elementor-button-text{display:none!important}.elementor-menu-cart__footer-buttons .elementor-button-text{display:inline!important}
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
/* ---------- logged-in panel ---------- */
.wtu__me{display:flex;align-items:center;gap:14px;margin:0 0 22px}
.wtu__av{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#1D4032,#2F6B4F);color:#fff;display:grid;place-items:center;font:700 22px/1 var(--sans);box-shadow:0 10px 24px -12px rgba(21,53,40,.7);flex:none}
.wtu__me strong{display:block;font:700 22px/1.2 var(--sans);color:var(--tx)}
.wtu__me small{display:block;font:400 13.5px/1.3 var(--sans);color:var(--tx2);margin-top:3px;word-break:break-all}
.wtu__stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:0 0 22px}
.wtu__stats div{background:var(--off);border-radius:12px;padding:14px 12px}
.wtu__stats b{display:block;font:700 18px/1.1 var(--sans);color:var(--tx)}
.wtu__stats div>span{display:block;font:500 12px/1.2 var(--sans);color:var(--tx2);margin-top:4px}
.wtu__links{display:flex;flex-direction:column;gap:6px;margin-bottom:22px}
.wtu__links a{display:flex;align-items:center;gap:14px;padding:14px 16px;border:1px solid var(--ln);border-radius:12px;color:var(--tx)!important;text-decoration:none;font:600 15px/1 var(--sans);transition:border-color .3s,background .3s,transform .35s var(--ease)}
.wta.is-open .wtu__links a{animation:wtaIn .55s var(--ease) both;animation-delay:calc(.15s + var(--i) * .06s)}
.wtu__links a span{flex:1}
.wtu__links a:hover{border-color:var(--g);background:var(--sage);transform:translateX(4px)}
.wtu__links .wt-ic2:last-child{opacity:.4;transition:transform .35s var(--ease),opacity .3s}
.wtu__links a:hover .wt-ic2:last-child{opacity:1;transform:translateX(3px)}
.wt-ic2{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round;flex:none}
.wtu__links .wt-ic2:first-child{color:var(--g)}
.wtu__out{margin-top:auto;text-decoration:none!important}
.wtu__out span{display:inline-flex;align-items:center;gap:10px}
/* ---------- My Account page ---------- */
.woocommerce-account .entry-title,.woocommerce-account .page-header .entry-title{font:700 clamp(30px,3vw,44px)/1.1 var(--sans)!important;color:var(--tx);max-width:1320px;margin:44px auto 26px!important;padding:0 32px}
.woocommerce-account .page-content,.woocommerce-account main .woocommerce{max-width:1320px;margin:0 auto;padding:0 32px 80px}
.woocommerce-account .woocommerce{display:grid;grid-template-columns:270px 1fr;gap:32px;align-items:start}
.woocommerce-account .woocommerce:before,.woocommerce-account .woocommerce:after{display:none!important}
.woocommerce-account .woocommerce-MyAccount-navigation,.woocommerce-account .woocommerce-MyAccount-content{float:none!important;width:auto!important}
.woocommerce-MyAccount-navigation{position:sticky;top:110px;background:#fff;border:1px solid var(--ln);border-radius:18px;padding:10px}
.woocommerce-MyAccount-navigation ul{list-style:none!important;margin:0!important;padding:0!important}
.woocommerce-MyAccount-navigation li{margin:0!important}
.woocommerce-MyAccount-navigation a{display:flex;align-items:center;gap:12px;padding:13px 14px;border-radius:12px;color:var(--tx)!important;text-decoration:none!important;font:600 14.5px/1 var(--sans);transition:background .3s,color .3s,transform .35s var(--ease)}
.woocommerce-MyAccount-navigation a:before{content:"";width:20px;height:20px;flex:none;background:currentColor;-webkit-mask:var(--ic) center/contain no-repeat;mask:var(--ic) center/contain no-repeat;opacity:.75}
.woocommerce-MyAccount-navigation a:hover{background:var(--off);transform:translateX(3px)}
.woocommerce-MyAccount-navigation .is-active a{background:var(--g);color:#fff!important}
.woocommerce-MyAccount-navigation .is-active a:before{opacity:1}
.woocommerce-MyAccount-navigation-link--customer-logout{border-top:1px solid var(--ln);margin-top:8px!important;padding-top:8px}
.woocommerce-MyAccount-navigation-link--customer-logout a{color:#A8432E!important}
.woocommerce-MyAccount-navigation-link--customer-logout a:hover{background:#FBEDEA}
.woocommerce-MyAccount-navigation-link--dashboard a{--ic:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='7' height='9' rx='1.5'/%3E%3Crect x='14' y='3' width='7' height='5' rx='1.5'/%3E%3Crect x='14' y='12' width='7' height='9' rx='1.5'/%3E%3Crect x='3' y='16' width='7' height='5' rx='1.5'/%3E%3C/svg%3E")}
.woocommerce-MyAccount-navigation-link--orders a{--ic:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7l9-4 9 4v10l-9 4-9-4z'/%3E%3Cpath d='M3 7l9 4 9-4M12 11v10'/%3E%3C/svg%3E")}
.woocommerce-MyAccount-navigation-link--downloads a{--ic:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 4v11M7 10l5 5 5-5M5 20h14'/%3E%3C/svg%3E")}
.woocommerce-MyAccount-navigation-link--edit-address a{--ic:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 21s7-6.5 7-12a7 7 0 0 0-14 0c0 5.5 7 12 7 12z'/%3E%3Ccircle cx='12' cy='9' r='2.5'/%3E%3C/svg%3E")}
.woocommerce-MyAccount-navigation-link--edit-account a{--ic:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='8' r='4'/%3E%3Cpath d='M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6'/%3E%3C/svg%3E")}
.woocommerce-MyAccount-navigation-link--customer-logout a{--ic:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3'/%3E%3Cpath d='M10 17l-5-5 5-5M5 12h11'/%3E%3C/svg%3E")}
.woocommerce-MyAccount-content{min-width:0;font:400 15px/1.6 var(--sans);color:var(--tx2)}
.woocommerce-MyAccount-content:has(.wtd)>p{display:none}
.wtd{display:flex;flex-direction:column;gap:20px}
.wtd>*{animation:wtaIn .7s var(--ease) both}.wtd>*:nth-child(2){animation-delay:.08s}.wtd>*:nth-child(3){animation-delay:.16s}.wtd>*:nth-child(4){animation-delay:.24s}
.wtd-hello{display:flex;justify-content:space-between;align-items:center;gap:24px;padding:30px 32px;border-radius:20px;color:#fff;background:radial-gradient(120% 140% at 100% 0%,#2F6B4F 0%,#1D4032 45%,#153528 100%);position:relative;overflow:hidden}
.wtd-hello:after{content:"";position:absolute;right:-60px;bottom:-90px;width:260px;height:260px;border-radius:50%;border:1px solid rgba(255,255,255,.12);box-shadow:0 0 0 40px rgba(255,255,255,.03)}
.wtd-eyebrow{margin:0 0 8px!important;font:600 11px/1 var(--sans)!important;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.65)!important}
.wtd-hello h2{margin:0 0 8px!important;font:700 clamp(24px,2.4vw,34px)/1.1 var(--sans)!important;color:#fff!important}
.wtd-hello p{margin:0;color:rgba(255,255,255,.8);font-size:15px;max-width:460px}
.wtd-cta{position:relative;z-index:1;display:inline-flex;align-items:center;gap:10px;height:48px;padding:0 22px;border-radius:12px;background:#fff;color:var(--g)!important;font:600 14.5px/1 var(--sans);text-decoration:none!important;white-space:nowrap;transition:transform .35s var(--ease),box-shadow .35s}
.wtd-cta:hover{transform:translateY(-2px);box-shadow:0 14px 28px -14px rgba(0,0,0,.5)}
.wtd-cta .wt-ic2{transition:transform .35s var(--ease)}.wtd-cta:hover .wt-ic2{transform:translateX(4px)}
.wtd-cta--sm{height:42px;background:var(--g);color:#fff!important}
.wtd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.wtd-stat{background:#fff;border:1px solid var(--ln);border-radius:16px;padding:18px;transition:transform .35s var(--ease),box-shadow .35s}
.wtd-stat:hover{transform:translateY(-3px);box-shadow:0 16px 30px -22px rgba(21,53,40,.5)}
.wtd-stat__ic{width:40px;height:40px;border-radius:12px;background:var(--sage);color:var(--g);display:grid;place-items:center;margin-bottom:14px}
.wtd-stat b{display:block;font:700 24px/1.1 var(--sans);color:var(--tx)}
.wtd-stat small{display:block;margin-top:4px;font:500 13px/1.2 var(--sans);color:var(--tx2)}
.wtd-grid{display:grid;grid-template-columns:1.5fr 1fr;gap:20px}
.wtd-card{background:#fff;border:1px solid var(--ln);border-radius:18px;padding:22px 22px 18px;min-width:0}
.wtd-card header{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:16px}
.wtd-card h3{margin:0!important;font:700 18px/1.2 var(--sans)!important;color:var(--tx)!important}
.wtd-card header p{margin:3px 0 0;font-size:13px;color:var(--tx2)}
.wtd-card header>b{font:700 22px/1 var(--sans);color:var(--tx)}
.wtd-card header>a{font:600 13.5px/1 var(--sans);color:var(--g)!important;text-decoration:none}
.wtd-chart__box{position:relative}
.wtd-chart__svg{display:block;width:100%;height:220px;overflow:visible}
.wtd-chart__grid{stroke:#ECEDE9;stroke-width:1;vector-effect:non-scaling-stroke}
.wtd-chart__area{fill:url(#wtdG);opacity:0;animation:wtdFade 1s .6s var(--ease) forwards}
.wtd-chart__line{fill:none;stroke:var(--g);stroke-width:2.5;vector-effect:non-scaling-stroke;stroke-linecap:round;stroke-dasharray:1;stroke-dashoffset:1;animation:wtdDraw 1.4s .25s var(--ease) forwards}
.wtd-chart__pt circle{fill:#fff;stroke:var(--g);stroke-width:2;vector-effect:non-scaling-stroke;opacity:0;transform-box:fill-box;transform-origin:center;transition:transform .25s var(--ease),opacity .25s}
.wtd-chart__pt.on circle{opacity:1;transform:scale(1.4)}
.wtd-chart__pt{cursor:pointer}
@keyframes wtdDraw{to{stroke-dashoffset:0}}@keyframes wtdFade{to{opacity:1}}
.wtd-chart__x{display:grid;grid-template-columns:repeat(12,1fr);margin-top:8px;font:500 11.5px/1 var(--sans);color:#9aa09b;text-align:center}
.wtd-chart__tip{position:absolute;transform:translate(-50%,calc(-100% - 12px));background:var(--tx);color:#fff;border-radius:10px;padding:8px 12px;font:500 12px/1.3 var(--sans);white-space:nowrap;pointer-events:none;opacity:0;transition:opacity .2s,transform .25s var(--ease)}
.wtd-chart__tip b{display:block;font-size:14px}.wtd-chart__tip span{opacity:.7}
.wtd-chart__tip.on{opacity:1;transform:translate(-50%,calc(-100% - 16px))}
.wtd-chart__box.is-empty .wtd-chart__line{stroke:#CFD5CD;stroke-dasharray:6 6;animation:none;stroke-dashoffset:0}
.wtd-chart__empty{position:absolute;left:50%;top:42%;transform:translate(-50%,-50%);margin:0;background:#fff;border:1px solid var(--ln);border-radius:999px;padding:10px 18px;font:500 13px/1.3 var(--sans);color:var(--tx2);white-space:nowrap}
.wtd-recent ul{list-style:none;margin:0;padding:0}
.wtd-recent li a{display:grid;grid-template-columns:1fr auto auto;gap:12px;align-items:center;padding:12px 6px;border-top:1px solid var(--ln);color:var(--tx)!important;text-decoration:none!important;transition:background .3s}
.wtd-recent li:first-child a{border-top:0}
.wtd-recent li a:hover{background:var(--off)}
.wtd-o__n{font:600 14px/1.2 var(--sans)}.wtd-o__n small{display:block;font-weight:400;font-size:12px;color:var(--tx2);margin-top:3px}
.wtd-recent b{font:700 14px/1 var(--sans)}
.wtd-pill{font:600 11px/1 var(--sans);padding:6px 10px;border-radius:999px;background:var(--off);color:var(--tx2)}
.wtd-pill--completed{background:#E3F1E8;color:#24603F}.wtd-pill--processing{background:#E6EEF8;color:#2D5B8F}.wtd-pill--on-hold,.wtd-pill--pending{background:#FFF4D6;color:#7A5A00}.wtd-pill--cancelled,.wtd-pill--failed,.wtd-pill--refunded{background:#FBEDEA;color:#A8432E}
.wtd-empty{text-align:center;padding:16px 8px 8px}
.wtd-empty>.wt-ic2{width:44px;height:44px;color:var(--g);padding:10px;background:var(--sage);border-radius:14px;margin-bottom:10px}
.wtd-empty p{margin:0 auto 16px;max-width:260px;font-size:13.5px}
.wtd-quick{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.wtd-quick a{display:flex;align-items:center;gap:14px;padding:18px;border:1px solid var(--ln);border-radius:16px;background:#fff;color:var(--tx)!important;text-decoration:none!important;transition:border-color .3s,transform .35s var(--ease),box-shadow .35s}
.wtd-quick a>.wt-ic2{width:42px;height:42px;padding:10px;border-radius:12px;background:var(--off);color:var(--g);transition:background .3s,color .3s}
.wtd-quick a:hover{border-color:var(--g);transform:translateY(-3px);box-shadow:0 16px 30px -22px rgba(21,53,40,.5)}
.wtd-quick a:hover>.wt-ic2{background:var(--g);color:#fff}
.wtd-quick b{display:block;font:700 15px/1.2 var(--sans)}.wtd-quick small{display:block;font-size:12.5px;color:var(--tx2);margin-top:3px}
/* orders / addresses / forms */
.woocommerce-MyAccount-content table.shop_table{border:1px solid var(--ln)!important;border-radius:16px!important;overflow:hidden;border-collapse:separate!important;background:#fff}
.woocommerce-MyAccount-content table.shop_table th{background:var(--off);font:600 13px/1 var(--sans);color:var(--tx2);text-transform:uppercase;letter-spacing:.06em;padding:14px 16px!important;border:0!important}
.woocommerce-MyAccount-content table.shop_table td{padding:16px!important;border-top:1px solid var(--ln)!important;border-left:0!important;border-right:0!important;font-size:14.5px;color:var(--tx)}
.woocommerce-MyAccount-content .button,.woocommerce-MyAccount-content button[type=submit]{background:var(--g)!important;color:#fff!important;border:0!important;border-radius:10px!important;padding:12px 20px!important;font:600 14px/1 var(--sans)!important;transition:background .3s,transform .3s var(--ease)!important}
.woocommerce-MyAccount-content .button:hover,.woocommerce-MyAccount-content button[type=submit]:hover{background:var(--g3)!important;transform:translateY(-2px)}
.woocommerce-MyAccount-content input.input-text,.woocommerce-MyAccount-content select,.woocommerce-MyAccount-content textarea{height:50px;border:1.5px solid var(--ln)!important;border-radius:12px!important;padding:0 14px!important;font:500 15px/1 var(--sans)!important;background:#fff!important;transition:border-color .3s,box-shadow .3s}
.woocommerce-MyAccount-content input.input-text:focus{border-color:var(--g)!important;box-shadow:0 0 0 4px rgba(29,64,50,.1)!important;outline:0}
.woocommerce-MyAccount-content label{font:600 13.5px/1.3 var(--sans)!important;color:var(--tx)}
.woocommerce-Addresses{display:grid!important;grid-template-columns:1fr 1fr;gap:16px}
.woocommerce-Addresses:before,.woocommerce-Addresses:after{display:none!important}
.woocommerce-Addresses .woocommerce-Address{width:auto!important;float:none!important;background:#fff;border:1px solid var(--ln);border-radius:16px;padding:20px}
.woocommerce-Address-title{display:flex;justify-content:space-between;align-items:center}
.woocommerce-Address-title h2{font:700 18px/1.2 var(--sans)!important;margin:0!important}
.woocommerce-Address-title a{font:600 13.5px/1 var(--sans);color:var(--g)!important}
.woocommerce-MyAccount-content fieldset{border:1px solid var(--ln);border-radius:16px;padding:20px;margin:24px 0}
.woocommerce-MyAccount-content mark{background:var(--sage);color:var(--g);padding:2px 6px;border-radius:6px}
.woocommerce-account .woocommerce-info,.woocommerce-account .woocommerce-message{border:0!important;border-radius:14px;background:var(--off)!important;color:var(--tx)!important;padding:16px 20px!important}
.woocommerce-account .woocommerce-info:before,.woocommerce-account .woocommerce-message:before{color:var(--g)!important}
@media (max-width:1024px){.woocommerce-account .woocommerce{grid-template-columns:1fr}.woocommerce-MyAccount-navigation{position:static}.woocommerce-MyAccount-navigation ul{display:flex;overflow-x:auto;gap:4px;scrollbar-width:none}.woocommerce-MyAccount-navigation li{flex:none}.woocommerce-MyAccount-navigation-link--customer-logout{border:0;margin:0!important;padding:0}.wtd-grid{grid-template-columns:1fr}.wtd-stats{grid-template-columns:1fr 1fr}}
@media (max-width:767px){.woocommerce-account .entry-title,.woocommerce-account .page-content,.woocommerce-account main .woocommerce{padding-left:16px;padding-right:16px}.wtd-hello{flex-direction:column;align-items:flex-start;padding:24px}.wtd-quick{grid-template-columns:1fr}.woocommerce-Addresses{grid-template-columns:1fr}.wtd-chart__svg{height:180px}.wtd-chart__x span:nth-child(even){visibility:hidden}.wtd-chart__empty{white-space:normal;width:80%;text-align:center}}
@media (prefers-reduced-motion:reduce){.wtd>*,.wtd-chart__line,.wtd-chart__area{animation:none!important;stroke-dashoffset:0!important;opacity:1!important}}
/* ---------- steps bar ---------- */
.wt-co .entry-title,.wt-co .page-header{display:none!important}
.wt-co .page-content,.wt-co main .woocommerce,.wt-co .site-main{max-width:1320px;margin:0 auto}
.wt-co main{padding:0 32px 90px}
.wt-co-head{padding:40px 0 26px;grid-column:1/-1}
.wt-steps-bar ol{list-style:none;margin:0 0 30px;padding:0;display:flex;align-items:center;gap:0;max-width:620px}
.wt-steps-bar li{display:flex;align-items:center;gap:10px;flex:1;font:600 13.5px/1 var(--sans);color:#9aa09b;white-space:nowrap}
.wt-steps-bar li:not(:last-child):after{content:"";flex:1;height:2px;margin:0 12px;border-radius:2px;background:linear-gradient(90deg,var(--g) var(--fill,0%),#E3E6E0 var(--fill,0%));transition:--fill .6s}
.wt-steps-bar li.is-done:after{--fill:100%}
.wt-steps-bar li.is-current,.wt-steps-bar li.is-done{color:var(--tx)}
.wt-steps-bar__dot{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;border:1.5px solid #D6DAD3;font:700 13px/1 var(--sans);color:#9aa09b;background:#fff;flex:none}
.wt-steps-bar li.is-current .wt-steps-bar__dot{border-color:var(--g);color:var(--g);box-shadow:0 0 0 5px rgba(29,64,50,.1);animation:wtStepPulse 2.2s ease-out infinite}
.wt-steps-bar li.is-done .wt-steps-bar__dot{background:var(--g);border-color:var(--g)}
.wt-steps-bar__dot svg{width:16px;height:16px;fill:none;stroke:#fff;stroke-width:2.6;stroke-linecap:round;stroke-linejoin:round}
@keyframes wtStepPulse{0%{box-shadow:0 0 0 0 rgba(29,64,50,.25)}70%{box-shadow:0 0 0 9px rgba(29,64,50,0)}100%{box-shadow:0 0 0 0 rgba(29,64,50,0)}}
.wt-co-title{margin:0 0 6px!important;font:700 clamp(32px,3.2vw,46px)/1.05 var(--sans)!important;color:var(--tx)!important}
.wt-co-sub{margin:0;font:400 15px/1.5 var(--sans);color:var(--tx2)}
.wt-trustmini{list-style:none;margin:16px 0 0!important;padding:0!important;display:grid;gap:8px}
.wt-trustmini li{display:flex;align-items:center;gap:10px;font:500 13.5px/1.3 var(--sans);color:var(--tx2);margin:0!important}
.wt-trustmini .wt-ic2{color:var(--g);width:18px;height:18px}
/* ---------- quantity stepper ---------- */
.quantity.wt-q{display:inline-flex!important;align-items:center;height:48px;border:1.5px solid var(--ln);border-radius:12px;background:#fff;overflow:hidden;transition:border-color .3s}
.quantity.wt-q:focus-within{border-color:var(--g)}
.quantity.wt-q input.qty{width:44px!important;height:100%!important;border:0!important;background:transparent!important;text-align:center;font:700 15px/1 var(--sans)!important;color:var(--tx);padding:0!important;-moz-appearance:textfield;box-shadow:none!important;outline:0}
.quantity.wt-q input.qty::-webkit-inner-spin-button,.quantity.wt-q input.qty::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}
.wt-q__b{width:40px;height:100%;border:0!important;background:transparent!important;color:var(--tx)!important;font:500 20px/1 var(--sans)!important;cursor:pointer;padding:0!important;border-radius:0!important;transition:background .25s,color .25s}
.wt-q__b:hover{background:var(--sage)!important;color:var(--g)!important}
.quantity.wt-q.bump input.qty{animation:wtQ .35s cubic-bezier(.34,1.56,.64,1)}
@keyframes wtQ{0%{transform:scale(1)}40%{transform:scale(1.25)}100%{transform:scale(1)}}
/* ---------- cart ---------- */
.woocommerce-cart .woocommerce{display:grid;grid-template-columns:1fr 400px;gap:32px;align-items:start}
.woocommerce-cart .woocommerce>.woocommerce-notices-wrapper,.woocommerce-cart .woocommerce>.wt-co-head{grid-column:1/-1}
.woocommerce-cart .woocommerce-cart-form{grid-column:1;min-width:0}
.woocommerce-cart .cart-collaterals{grid-column:2;width:auto!important;position:sticky;top:110px}
.woocommerce-cart .cart-collaterals:before,.woocommerce-cart .cart-collaterals:after{display:none}
.woocommerce-cart .cart-collaterals .cart_totals{width:auto!important;float:none!important;background:#fff;border:1px solid var(--ln);border-radius:20px;padding:24px;box-shadow:0 30px 60px -45px rgba(21,53,40,.45)}
.woocommerce-cart .cart_totals h2{font:700 20px/1.2 var(--sans)!important;margin:0 0 14px!important}
.woocommerce-cart .cart_totals table{border:0!important;margin:0 0 6px!important}
.woocommerce-cart .cart_totals th,.woocommerce-cart .cart_totals td{border:0!important;border-bottom:1px solid var(--ln)!important;padding:13px 0!important;font:500 14.5px/1.4 var(--sans);background:none!important;vertical-align:top}
.woocommerce-cart .cart_totals td{text-align:right}
.woocommerce-cart .cart_totals .order-total th,.woocommerce-cart .cart_totals .order-total td{border-bottom:0!important;padding-top:16px!important;font:700 17px/1 var(--sans)}
.woocommerce-cart .cart_totals .order-total .amount{font:700 26px/1 var(--sans);color:var(--tx)}
.woocommerce-cart .wc-proceed-to-checkout{padding:12px 0 0!important}
.wc-proceed-to-checkout .checkout-button,#place_order{display:flex!important;align-items:center;justify-content:center;gap:10px;width:100%;height:58px;margin:0!important;border-radius:14px!important;background:var(--g)!important;color:#fff!important;font:600 16px/1 var(--sans)!important;border:0!important;padding:0 20px!important;transition:background .3s,transform .3s var(--ease),box-shadow .3s!important}
.wc-proceed-to-checkout .checkout-button:after{content:"";width:18px;height:18px;background:currentColor;-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E") center/contain no-repeat;transition:transform .35s var(--ease)}
.wc-proceed-to-checkout .checkout-button:hover,#place_order:hover{background:var(--g3)!important;transform:translateY(-2px);box-shadow:0 16px 30px -16px rgba(21,53,40,.7)}
.wc-proceed-to-checkout .checkout-button:hover:after{transform:translateX(4px)}
.woocommerce-cart table.cart{border:0!important;border-collapse:separate!important;border-spacing:0 12px!important;margin:-12px 0 0!important;background:none!important}
.woocommerce-cart table.cart thead{display:none}
.woocommerce-cart table.cart tr.cart_item{display:grid;grid-template-columns:110px 1fr auto auto 40px;grid-template-areas:"img name qty sub rm" "img price qty sub rm";align-items:center;column-gap:20px;background:#fff;border:1px solid var(--ln);border-radius:18px;padding:14px;margin-bottom:12px;transition:box-shadow .35s,border-color .35s,transform .35s var(--ease);animation:wtaIn .55s var(--ease) both}
.woocommerce-cart table.cart tr.cart_item:hover{border-color:#D3D8D2;box-shadow:0 18px 36px -28px rgba(21,53,40,.45)}
.woocommerce-cart table.cart tr.cart_item:nth-child(2){animation-delay:.06s}.woocommerce-cart table.cart tr.cart_item:nth-child(3){animation-delay:.12s}.woocommerce-cart table.cart tr.cart_item:nth-child(4){animation-delay:.18s}
.woocommerce-cart table.cart td{border:0!important;padding:0!important;background:none!important;display:block!important}
.woocommerce-cart table.cart td:before{display:none!important}
.woocommerce-cart table.cart .product-thumbnail{grid-area:img}
.woocommerce-cart table.cart .product-thumbnail img{width:110px!important;height:110px!important;object-fit:cover;border-radius:12px;background:var(--off);display:block;max-width:none}
.woocommerce-cart table.cart .product-name{grid-area:name;align-self:end}
.woocommerce-cart table.cart .product-name a{font:700 17px/1.3 var(--sans);color:var(--tx)!important;text-decoration:none}
.woocommerce-cart table.cart .product-name a:hover{color:var(--g)!important}
.woocommerce-cart table.cart .product-price{grid-area:price;align-self:start;font:500 14px/1.4 var(--sans);color:var(--tx2)}
.woocommerce-cart table.cart .product-quantity{grid-area:qty}
.woocommerce-cart table.cart .product-subtotal{grid-area:sub;font:700 18px/1 var(--sans);color:var(--tx);min-width:90px;text-align:right}
.woocommerce-cart table.cart .product-remove{grid-area:rm;justify-self:end}
.woocommerce-cart table.cart a.remove{width:36px!important;height:36px!important;border-radius:50%!important;border:1px solid var(--ln);display:grid!important;place-items:center;color:transparent!important;font-size:0!important;position:relative;transition:background .3s,border-color .3s,transform .35s var(--ease)}
.woocommerce-cart table.cart a.remove:before,.woocommerce-cart table.cart a.remove:after{content:"";position:absolute;width:13px;height:1.6px;background:var(--tx2);border-radius:2px;transform:rotate(45deg)}
.woocommerce-cart table.cart a.remove:after{transform:rotate(-45deg)}
.woocommerce-cart table.cart a.remove:hover{background:#FBEDEA!important;border-color:#E3B8AE;transform:rotate(90deg)}
.woocommerce-cart table.cart a.remove:hover:before,.woocommerce-cart table.cart a.remove:hover:after{background:#A8432E}
.woocommerce-cart table.cart tr:not(.cart_item) td.actions{display:flex!important;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;background:#fff;border:1px dashed #D6DAD3!important;border-radius:16px;padding:14px!important}
.woocommerce-cart table.cart td.actions .coupon{display:flex;gap:8px;flex:1;max-width:420px}
.woocommerce-cart table.cart td.actions .coupon label{display:none}
.woocommerce-cart table.cart td.actions .coupon input.input-text{flex:1;height:46px;border:1.5px solid var(--ln)!important;border-radius:12px!important;padding:0 14px!important;font:500 14.5px/1 var(--sans)!important;width:auto!important}
.woocommerce-cart table.cart td.actions .coupon .button{height:46px;border-radius:12px!important;background:#fff!important;color:var(--tx)!important;border:1.5px solid var(--tx)!important;font:600 14px/1 var(--sans)!important;padding:0 18px!important}
.woocommerce-cart table.cart td.actions .coupon .button:hover{background:var(--off)!important}
.woocommerce-cart table.cart td.actions>[name="update_cart"]{display:none!important}
.woocommerce-cart .blockUI.blockOverlay{background:rgba(255,255,255,.6)!important;border-radius:18px}
.wt-empty-cart{max-width:520px;margin:20px auto 40px;text-align:center;animation:wtaIn .6s var(--ease) both}
.wt-empty-cart__ic{width:88px;height:88px;margin:0 auto 20px;border-radius:28px;background:var(--sage);color:var(--g);display:grid;place-items:center}
.wt-empty-cart__ic .wt-ic2{width:40px;height:40px;animation:wtpcBump 2.6s ease-in-out infinite}
.wt-empty-cart h1{font:700 32px/1.1 var(--sans)!important;margin:0 0 10px!important;color:var(--tx)!important}
.wt-empty-cart p{color:var(--tx2);margin:0 0 24px}
.woocommerce-cart .cart-empty.woocommerce-info,.woocommerce-cart .return-to-shop{display:none!important}
/* ---------- checkout ---------- */
.woocommerce-checkout form.checkout{display:grid;grid-template-columns:1fr 430px;gap:32px;align-items:start}
.woocommerce-checkout form.checkout #customer_details{grid-column:1;width:auto!important;float:none!important;display:flex;flex-direction:column;gap:20px}
.woocommerce-checkout form.checkout #customer_details:before,.woocommerce-checkout form.checkout #customer_details:after{display:none}
.woocommerce-checkout #customer_details .col-1,.woocommerce-checkout #customer_details .col-2{width:auto!important;float:none!important;max-width:none!important;background:#fff;border:1px solid var(--ln);border-radius:20px;padding:26px}
.woocommerce-checkout #order_review_heading{grid-column:2;grid-row:1;margin:0 0 -18px!important;padding:24px 26px 0;font:700 20px/1.2 var(--sans)!important;position:relative;z-index:1}
.woocommerce-checkout #order_review{grid-column:2;grid-row:1/span 3;position:sticky;top:110px;background:#fff;border:1px solid var(--ln);border-radius:20px;padding:64px 26px 26px;box-shadow:0 30px 60px -45px rgba(21,53,40,.45)}
.woocommerce-checkout h3{font:700 20px/1.2 var(--sans)!important;margin:0 0 18px!important;color:var(--tx)!important}
.woocommerce-checkout .form-row{margin:0 0 14px!important;padding:0!important}
.woocommerce-checkout .form-row label{font:600 13px/1.3 var(--sans)!important;color:var(--tx);margin-bottom:6px!important;display:block}
.woocommerce-checkout .form-row .required{color:#A8432E;text-decoration:none}
.woocommerce-checkout .form-row input.input-text,.woocommerce-checkout .form-row textarea,.woocommerce-checkout .form-row select,.woocommerce-checkout .select2-container .select2-selection--single{height:52px!important;border:1.5px solid var(--ln)!important;border-radius:12px!important;padding:0 14px!important;font:500 15px/1 var(--sans)!important;background:#fff!important;color:var(--tx);box-shadow:none!important;transition:border-color .3s,box-shadow .3s}
.woocommerce-checkout .form-row textarea{height:110px!important;padding:14px!important;line-height:1.5!important}
.woocommerce-checkout .select2-container .select2-selection--single .select2-selection__rendered{line-height:49px!important;padding:0!important}
.woocommerce-checkout .select2-container .select2-selection--single .select2-selection__arrow{height:50px!important;right:10px!important}
.woocommerce-checkout .form-row input.input-text:focus,.woocommerce-checkout .form-row textarea:focus{border-color:var(--g)!important;box-shadow:0 0 0 4px rgba(29,64,50,.1)!important;outline:0}
.woocommerce-checkout .form-row.woocommerce-invalid input.input-text{border-color:#C0563F!important}
.woocommerce-checkout .form-row.woocommerce-validated input.input-text{border-color:#9BC2A9!important}
.woocommerce-checkout .form-row-first,.woocommerce-checkout .form-row-last{width:calc(50% - 7px)!important}
.woocommerce-checkout table.shop_table{border:0!important;margin:0 0 18px!important}
.woocommerce-checkout table.shop_table th,.woocommerce-checkout table.shop_table td{border:0!important;border-bottom:1px solid var(--ln)!important;padding:12px 0!important;background:none!important;font:500 14px/1.4 var(--sans)}
.woocommerce-checkout table.shop_table thead{display:none}
.woocommerce-checkout table.shop_table td.product-total,.woocommerce-checkout table.shop_table tfoot td{text-align:right}
.woocommerce-checkout table.shop_table .product-name{font-weight:600;color:var(--tx)}
.woocommerce-checkout table.shop_table .product-quantity{color:var(--tx2);font-weight:500}
.woocommerce-checkout table.shop_table .order-total th,.woocommerce-checkout table.shop_table .order-total td{border-bottom:0!important;font:700 17px/1 var(--sans);padding-top:16px!important}
.woocommerce-checkout table.shop_table .order-total .amount{font:700 26px/1 var(--sans)}
.woocommerce-checkout #payment{background:var(--off)!important;border-radius:16px!important;padding:6px!important}
.woocommerce-checkout #payment ul.payment_methods{border:0!important;padding:10px 12px!important}
.woocommerce-checkout #payment .payment_box{background:#fff!important;border-radius:12px;font-size:13.5px}
.woocommerce-checkout #payment .payment_box:before{display:none!important}
.woocommerce-checkout #payment div.form-row.place-order{padding:12px!important;margin:0!important}
.woocommerce-checkout .woocommerce-privacy-policy-text p{font-size:12.5px;color:var(--tx2)}
.woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info,.woocommerce-checkout .woocommerce-form-login-toggle .woocommerce-info{grid-column:1/-1;border:0!important;border-radius:14px;background:var(--off)!important;color:var(--tx2)!important;padding:14px 18px!important;font-size:14px}
.woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info:before,.woocommerce-checkout .woocommerce-form-login-toggle .woocommerce-info:before{color:var(--g)!important}
.woocommerce-checkout .woocommerce-info a{color:var(--g)!important;font-weight:600}
.woocommerce-checkout form.checkout_coupon{border:1px dashed #D6DAD3!important;border-radius:16px;padding:16px!important;background:#fff}
.woocommerce-checkout .woocommerce{display:block}
.woocommerce-checkout .woocommerce>.wt-co-head,.woocommerce-checkout .woocommerce-notices-wrapper{display:block}
/* ---------- thank you ---------- */
.woocommerce-order .woocommerce-notice--success{font:700 28px/1.2 var(--sans);color:var(--tx);padding:26px 26px 26px 80px;background:var(--sage) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231D4032' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpath d='M7 12.5l3.5 3.5L17 9'/%3E%3C/svg%3E") 24px center/36px no-repeat;border-radius:20px;animation:wtaIn .7s var(--ease) both}
.woocommerce-order ul.order_details{display:grid!important;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;padding:0!important;margin:20px 0 30px!important}
.woocommerce-order ul.order_details:before,.woocommerce-order ul.order_details:after{display:none}
.woocommerce-order ul.order_details li{float:none!important;border:1px solid var(--ln)!important;border-radius:16px;padding:16px!important;margin:0!important;font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:var(--tx2)}
.woocommerce-order ul.order_details li strong{display:block;margin-top:6px;font:700 17px/1.2 var(--sans);text-transform:none;letter-spacing:0;color:var(--tx)}
/* ---------- single product ---------- */
.single-product div.product{gap:clamp(32px,4vw,64px)!important;margin-top:20px!important}
.single-product .woocommerce-breadcrumb{max-width:1320px;margin:26px auto 0!important;padding:0 32px;font:500 13px/1.4 var(--sans)!important;color:#9aa09b!important}
.single-product .woocommerce-breadcrumb a{color:var(--tx2)!important;text-decoration:none}
.single-product .woocommerce-product-gallery{position:sticky!important;top:100px;align-self:start}
.single-product .woocommerce-product-gallery__wrapper{border-radius:22px;overflow:hidden;background:var(--off)}
.single-product .woocommerce-product-gallery__image img{border-radius:0!important;transition:transform .8s var(--ease)}
.single-product .woocommerce-product-gallery__image:hover img{transform:scale(1.03)}
.single-product .woocommerce-product-gallery.wt-swap .flex-viewport{animation:wtFadeIn .5s var(--ease)}
@keyframes wtFadeIn{from{opacity:.4}to{opacity:1}}
.single-product .flex-control-thumbs{grid-template-columns:repeat(5,1fr)!important;gap:10px!important;margin-top:12px!important}
.single-product .flex-control-thumbs img{border-radius:12px!important;border:2px solid transparent;transition:opacity .3s,border-color .3s,transform .3s var(--ease)!important;cursor:pointer}
.single-product .flex-control-thumbs img:hover{opacity:1!important;transform:translateY(-2px)}
.single-product .flex-control-thumbs img.flex-active{border-color:var(--g)}
.single-product .woocommerce-product-gallery__trigger{background:#fff!important;border-radius:50%!important;box-shadow:0 8px 20px -10px rgba(0,0,0,.35);top:16px!important;right:16px!important}
.single-product div.product .summary{background:none!important;border:0!important;padding:8px 0 0!important;box-shadow:none!important;top:100px}
.wt-sp-eyebrow{margin:0 0 10px!important;font:600 12px/1.3 var(--sans);letter-spacing:.14em;text-transform:uppercase;color:var(--tx2)}
.wt-sp-eyebrow a{color:var(--g)!important;text-decoration:none}
.single-product .product_title{font:700 clamp(32px,3.2vw,46px)/1.05 var(--sans)!important;color:var(--tx)!important;margin:0 0 12px!important;letter-spacing:-.01em}
.single-product .summary .price{display:flex!important;align-items:baseline;gap:12px;flex-wrap:wrap;margin:6px 0 4px!important;font:700 34px/1 var(--sans)!important;color:var(--tx)!important}
.single-product .summary .price del{order:2;font-size:18px;font-weight:500;color:#9aa09b;opacity:1}
.single-product .summary .price ins{text-decoration:none}
.wt-sp-save{display:inline-flex;align-items:center;gap:8px;margin:6px 0 18px!important;font:600 13.5px/1 var(--sans);color:var(--g)}
.wt-sp-save span{background:var(--g);color:#fff;border-radius:999px;padding:5px 10px;font-size:12px}
.single-product .woocommerce-product-details__short-description{font:400 15.5px/1.65 var(--sans);color:var(--tx2);border-top:1px solid var(--ln);padding-top:18px;margin-bottom:6px}
.single-product .woocommerce-product-details__short-description ul{border:0!important;margin:12px 0 0!important;display:grid;gap:8px}
.single-product .woocommerce-product-details__short-description li{border:0!important;padding:0 0 0 28px!important;position:relative;color:var(--tx)}
.single-product .woocommerce-product-details__short-description li:before{content:"";position:absolute;left:0;top:3px;width:18px;height:18px;border-radius:50%;background:var(--sage) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231D4032' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 12.5l4 4L18 8'/%3E%3C/svg%3E") center/11px no-repeat}
.single-product form.cart{display:flex!important;gap:12px;margin:24px 0 14px!important;align-items:stretch}
.single-product form.cart .quantity.wt-q{height:58px}
.single-product .single_add_to_cart_button{flex:1;height:58px!important;border-radius:14px!important;font:600 16px/1 var(--sans)!important;display:flex!important;align-items:center;justify-content:center;gap:10px}
.single-product .single_add_to_cart_button:before{content:"";width:20px;height:20px;background:currentColor;-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3C/svg%3E") center/contain no-repeat}
.wt-sp-deliv{display:flex;align-items:center;gap:14px;padding:14px 16px;border:1px solid var(--ln);border-radius:14px;margin:4px 0 4px}
.wt-sp-deliv__ic{width:42px;height:42px;border-radius:12px;background:var(--sage);color:var(--g);display:grid;place-items:center;flex:none}
.wt-sp-deliv b{display:block;font:700 14.5px/1.3 var(--sans);color:var(--tx)}.wt-sp-deliv small{display:block;font-size:13px;color:var(--tx2);margin-top:2px}
.single-product .wt-trust{grid-template-columns:repeat(2,1fr)!important;gap:8px!important;margin:12px 0 18px!important}
.single-product .wt-trust li{background:var(--off)!important;border-radius:12px!important;font:600 13px/1.3 var(--sans)!important}
.single-product .product_meta{font-size:13px;color:#9aa09b;border-top:1px solid var(--ln);padding-top:14px}
.single-product .product_meta a{color:var(--tx2)!important}
.wt-acc ul.tabs{display:none!important}
.wt-acc{background:none!important;margin-top:50px!important;border-top:1px solid var(--ln)}
.wt-acc__h{width:100%;display:flex;justify-content:space-between;align-items:center;padding:22px 2px!important;border:0!important;border-bottom:1px solid var(--ln)!important;background:none!important;color:var(--tx)!important;font:700 19px/1.2 var(--sans)!important;cursor:pointer;border-radius:0!important;text-align:left}
.wt-acc__h i{position:relative;width:34px;height:34px;border-radius:50%;background:var(--off);flex:none;transition:background .3s,transform .45s var(--ease)}
.wt-acc__h i:before,.wt-acc__h i:after{content:"";position:absolute;left:50%;top:50%;width:12px;height:1.8px;margin:-.9px 0 0 -6px;background:var(--tx);border-radius:2px;transition:transform .45s var(--ease)}
.wt-acc__h i:after{transform:rotate(90deg)}
.wt-acc__h[aria-expanded="true"] i{background:var(--g);transform:rotate(180deg)}
.wt-acc__h[aria-expanded="true"] i:before,.wt-acc__h[aria-expanded="true"] i:after{background:#fff}
.wt-acc__h[aria-expanded="true"] i:after{transform:rotate(0)}
.wt-acc__h:hover{color:var(--g)!important}
.wt-acc .wt-acc__p{display:grid!important;grid-template-rows:0fr;transition:grid-template-rows .5s var(--ease),padding .5s var(--ease);padding:0 2px!important;margin:0!important;background:none!important;border:0!important;border-radius:0!important}
.wt-acc .wt-acc__p>*{overflow:hidden;min-height:0}
.wt-acc .wt-acc__p.open{grid-template-rows:1fr;padding:18px 2px 26px!important}
.wt-acc .wt-acc__p>h2:first-child{display:none}
.wt-acc__p{font:400 15.5px/1.7 var(--sans);color:var(--tx2)}
.wt-acc__p table.shop_attributes{border:0!important}
.wt-acc__p table.shop_attributes th,.wt-acc__p table.shop_attributes td{border:0!important;border-bottom:1px solid var(--ln)!important;padding:12px 0!important;background:none!important;font-style:normal}
.wt-acc__p table.shop_attributes th{width:200px;color:var(--tx);font-weight:600}
.wt-sticky-buy{position:fixed;left:0;right:0;bottom:0;z-index:900;transform:translateY(110%);transition:transform .5s var(--ease);padding:0 16px 16px;pointer-events:none}
.wt-sticky-buy.show{transform:none}
.wt-sticky-buy__in{max-width:900px;margin:0 auto;display:flex;align-items:center;gap:14px;background:rgba(255,255,255,.92);-webkit-backdrop-filter:blur(14px);backdrop-filter:blur(14px);border:1px solid var(--ln);border-radius:18px;padding:10px 10px 10px 12px;box-shadow:0 24px 50px -24px rgba(0,0,0,.45);pointer-events:auto}
.wt-sticky-buy__in img{width:52px!important;height:52px!important;border-radius:10px;object-fit:cover}
.wt-sticky-buy__t{flex:1;min-width:0}
.wt-sticky-buy__t b{display:block;font:700 15px/1.2 var(--sans);color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.wt-sticky-buy__t .price{font:600 14px/1.2 var(--sans);color:var(--tx)}
.wt-sticky-buy__t del{color:#9aa09b;font-size:12px;margin-right:4px}
.wt-sticky-buy__t ins{text-decoration:none}
.wt-sticky-buy__btn{height:48px;padding:0 24px!important;border:0!important;border-radius:12px!important;background:var(--g)!important;color:#fff!important;font:600 14.5px/1 var(--sans)!important;cursor:pointer;transition:background .3s,transform .3s var(--ease)}
.wt-sticky-buy__btn:hover{background:var(--g3)!important;transform:translateY(-2px)}
.single-product .related.products{margin-top:60px}
@media (max-width:1024px){.woocommerce-cart .woocommerce,.woocommerce-checkout form.checkout{grid-template-columns:1fr}.woocommerce-cart .cart-collaterals,.woocommerce-checkout #order_review,.woocommerce-checkout #order_review_heading{grid-column:1;position:static}.woocommerce-checkout #order_review{grid-row:auto}.woocommerce-checkout #order_review_heading{grid-row:auto}.single-product .woocommerce-product-gallery{position:static!important}}
@media (max-width:767px){.wt-co main{padding:0 16px 70px}.wt-steps-bar li span:not(.wt-steps-bar__dot){display:none}.wt-steps-bar li.is-current span:not(.wt-steps-bar__dot){display:inline!important}
 .woocommerce-cart table.cart tr.cart_item{grid-template-columns:84px 1fr 36px;grid-template-areas:"img name rm" "img price rm" "qty qty sub";row-gap:10px;column-gap:14px}
 .woocommerce-cart table.cart .product-thumbnail img{width:84px!important;height:84px!important}
 .woocommerce-cart table.cart .product-subtotal{align-self:center}
 .woocommerce-checkout .form-row-first,.woocommerce-checkout .form-row-last{width:100%!important}
 .woocommerce-checkout #customer_details .col-1,.woocommerce-checkout #customer_details .col-2,.woocommerce-checkout #order_review{padding:20px}
 .single-product .woocommerce-breadcrumb{padding:0 16px}.single-product .flex-control-thumbs{grid-template-columns:repeat(4,1fr)!important}
 .wt-sticky-buy{padding:0 10px 10px}.wt-sticky-buy__in img{display:none}.wt-sticky-buy__btn{padding:0 16px!important}}
@media (prefers-reduced-motion:reduce){.wt-steps-bar__dot,.woocommerce-cart table.cart tr.cart_item,.wt-empty-cart,.wt-empty-cart__ic .wt-ic2{animation:none!important}}
/* checkout fixes */
.woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info{padding-left:46px!important;position:relative}
.woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info:before{left:18px!important;top:50%!important;transform:translateY(-50%)}
.woocommerce-checkout form.checkout_coupon{display:flex;gap:10px;align-items:center}.woocommerce-checkout form.checkout_coupon[style*="none"]{display:none!important}
.woocommerce-checkout form.checkout_coupon p{margin:0!important;float:none!important;width:auto!important}.woocommerce-checkout form.checkout_coupon .form-row-first{flex:1}
.woocommerce-checkout form.checkout_coupon .clear{display:none}
.woocommerce-checkout form.checkout_coupon button{height:46px;border-radius:999px!important;background:var(--g)!important;color:#fff!important;padding:0 22px!important;font-weight:600}
.woocommerce-checkout #place_order{float:none!important;width:100%!important}
#payment .place-order .wt-trustmini{display:flex!important;flex-wrap:wrap;justify-content:center;gap:8px 18px;clear:both;margin-top:14px!important}
#payment .place-order .wt-trustmini li{display:flex!important;align-items:center;gap:6px;padding:0!important;float:none!important;font-size:12.5px;white-space:nowrap}
#payment .place-order .wt-trustmini li:before{display:none!important}
.woocommerce-checkout #payment div.payment_box,.woocommerce-checkout #payment .woocommerce-info{background:#fff!important;border:1px solid var(--ln)!important;border-radius:12px;color:var(--tx2)!important;font-size:14px;line-height:1.5;padding:14px 16px!important}
.woocommerce-checkout #payment .woocommerce-info:before{display:none}
.woocommerce-checkout #payment ul.payment_methods{border:0!important;padding:0 0 12px!important}
.woocommerce-checkout #payment ul.payment_methods li{list-style:none;background:#fff;border:1px solid var(--ln);border-radius:12px;padding:14px 16px!important;margin-bottom:8px!important}
.woocommerce-NoticeGroup-checkout .woocommerce-error,.woocommerce-checkout .woocommerce-error{border:1px solid #F1C9C4!important;border-left:4px solid #C0392B!important;border-radius:14px;background:#FDF4F2!important;color:#7A2A20!important;padding:16px 20px 16px 50px!important;animation:wtShake .45s var(--ease)}
.woocommerce-checkout .woocommerce-error a{color:#7A2A20!important;text-decoration:none!important;font-weight:600}
.woocommerce-checkout .woocommerce-invalid input.input-text,.woocommerce-checkout .woocommerce-invalid select{animation:wtShake .45s var(--ease)}
@keyframes wtShake{0%,100%{transform:none}20%{transform:translateX(-6px)}40%{transform:translateX(5px)}60%{transform:translateX(-3px)}80%{transform:translateX(2px)}}
/* toasts */
.woocommerce-checkout .woocommerce-NoticeGroup-checkout,.woocommerce-checkout form.checkout>.woocommerce-error{display:none!important}
.wt-toasts{position:fixed;left:auto!important;right:20px;max-width:calc(100vw - 32px);box-sizing:border-box;bottom:20px;z-index:100000;display:flex;flex-direction:column;gap:10px;pointer-events:none;width:min(380px,calc(100vw - 32px))}
.wt-toast{pointer-events:auto;display:flex;gap:12px;align-items:flex-start;background:#fff;color:var(--tx);border-radius:16px;padding:14px 16px;box-shadow:0 18px 40px -12px rgba(16,30,22,.28),0 2px 6px rgba(16,30,22,.06);border:1px solid var(--ln);position:relative;overflow:hidden;font:500 14px/1.45 var(--sans);transform:translateX(120%);opacity:0;transition:transform .55s var(--ease),opacity .4s}
.wt-toast.is-in{animation:wtToastIn .55s var(--ease) forwards}@keyframes wtToastIn{from{transform:translateX(120%);opacity:0}to{transform:none;opacity:1}}.wt-toast.is-out{transform:translateX(120%);opacity:0}
.wt-toast__ic{flex:0 0 32px;height:32px;border-radius:50%;display:grid;place-items:center;background:var(--sage);color:var(--g)}
.wt-toast--error .wt-toast__ic{background:#FDECEA;color:#C0392B}.wt-toast--info .wt-toast__ic{background:#EEF3F8;color:#2D5A87}
.wt-toast__ic svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round}
.wt-toast__b{flex:1;min-width:0}.wt-toast__t{font-weight:700;margin-bottom:2px}.wt-toast__m a{color:var(--g);font-weight:600}
.wt-toast__x{background:none!important;border:0!important;color:var(--tx2)!important;cursor:pointer;padding:2px!important;font-size:18px;line-height:1}
.wt-toast__bar{position:absolute;left:0;bottom:0;height:3px;background:var(--g);transform-origin:left;animation:wtBar linear forwards}
.wt-toast--error .wt-toast__bar{background:#C0392B}
@keyframes wtBar{from{transform:scaleX(1)}to{transform:scaleX(0)}}
@media (max-width:600px){.wt-toasts{right:16px;left:16px;bottom:16px;width:auto}}

/* archive v2 */
.wt-archive-hero{position:relative;overflow:hidden;padding:44px 0 36px!important;margin-bottom:0!important;background:linear-gradient(180deg,var(--off),#fff)!important}
.wt-archive-hero:after{content:"";position:absolute;right:-120px;top:-160px;width:520px;height:520px;border-radius:50%;background:radial-gradient(closest-side,rgba(29,64,50,.07),transparent);pointer-events:none}
.wt-archive-hero .wt-in{max-width:1320px;margin:0 auto;padding:0 24px;position:relative;z-index:1}
.wt-crumbs{display:flex;gap:8px;font:500 13px var(--sans);color:var(--tx2);margin-bottom:28px}.wt-crumbs a{color:var(--tx2)!important;text-decoration:none}.wt-crumbs a:hover{color:var(--g)!important}.wt-crumbs b{color:var(--tx);font-weight:600}
.wt-archive-hero h1{animation:wtUp .7s var(--ease) both}.wt-archive-hero p{animation:wtUp .7s .08s var(--ease) both}.wt-chips{animation:wtUp .7s .16s var(--ease) both}
@keyframes wtUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
.wt-chips{flex-wrap:nowrap!important;overflow-x:auto;scrollbar-width:none;padding:2px 2px 6px}.wt-chips::-webkit-scrollbar{display:none}
.wt-chips a{display:inline-flex!important;align-items:center;gap:8px;white-space:nowrap;border-radius:999px!important;padding:10px 16px 10px 18px!important;font-weight:600!important;transition:background .3s var(--ease),color .3s,border-color .3s,transform .3s var(--ease)}
.wt-chips a:hover{transform:translateY(-2px)}
.wt-chips a i{font-style:normal;font-size:11.5px;min-width:22px;height:22px;padding:0 6px;border-radius:999px;display:inline-grid;place-items:center;background:var(--off);color:var(--tx2);transition:.3s}
.wt-chips a.on i,.wt-chips a:hover i{background:rgba(255,255,255,.18);color:#fff}
.post-type-archive-product .site-main,.tax-product_cat .site-main,.tax-product_tag .site-main,.post-type-archive-product main#content,.tax-product_cat main#content{max-width:1320px!important;width:100%;margin:0 auto;padding:0 24px 80px!important}
.wt-toolbar{position:sticky;top:0;z-index:20;display:flex;align-items:center;gap:16px;margin:26px 0 28px;padding:10px 10px 10px 20px;background:rgba(255,255,255,.86);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border:1px solid var(--ln);border-radius:999px}
.admin-bar .wt-toolbar{top:32px}
.wt-toolbar .woocommerce-result-count{margin:0 auto 0 0!important;float:none!important;font:500 14px var(--sans);color:var(--tx2)}
.wt-toolbar .woocommerce-ordering{margin:0!important;float:none!important}
.wt-toolbar .woocommerce-ordering select{appearance:none;-webkit-appearance:none;height:42px;border:1px solid var(--ln)!important;border-radius:999px!important;padding:0 42px 0 18px!important;font:600 14px var(--sans);color:var(--tx);background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231D4032' stroke-width='2.2' stroke-linecap='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 14px center/16px;cursor:pointer;transition:border-color .25s}
.wt-toolbar .woocommerce-ordering select:hover,.wt-toolbar .woocommerce-ordering select:focus{border-color:var(--g)!important;outline:0}
.wt-view{display:flex;gap:4px;background:var(--off);border-radius:999px;padding:4px}
.wt-view button{width:34px;height:34px;border:0!important;border-radius:50%!important;background:transparent!important;color:var(--tx2)!important;display:grid;place-items:center;padding:0!important;cursor:pointer;transition:.25s}
.wt-view button svg{width:16px;height:16px;fill:currentColor}.wt-view button.on{background:#fff!important;color:var(--g)!important;box-shadow:0 2px 8px rgba(0,0,0,.08)}
.woocommerce ul.products{gap:28px 22px!important}
.woocommerce ul.products.wt-c3{grid-template-columns:repeat(3,1fr)!important}
.woocommerce ul.products li.product{border:0!important;border-radius:20px!important;padding:0 0 18px!important;background:transparent!important;box-shadow:none!important;transform:none!important;display:flex;flex-direction:column;opacity:0;animation:wtUp .7s var(--ease) forwards}
.woocommerce ul.products li.product>a.woocommerce-LoopProduct-link{position:relative;display:flex;flex-direction:column;flex:1;text-decoration:none!important}
.woocommerce ul.products li.product img{aspect-ratio:4/5!important;width:100%;height:auto;border-radius:20px;margin:0 0 16px!important;background:var(--beige);transition:transform .8s var(--ease),opacity .5s}
.woocommerce ul.products li.product>a:first-child{overflow:visible}
.woocommerce ul.products li.product .wt-alt-img{position:absolute;left:0;top:0;opacity:0}
.woocommerce ul.products li.product:hover .wt-alt-img{opacity:1}
.woocommerce ul.products li.product:hover img{transform:scale(1.025)}
.woocommerce ul.products li.product>a:first-child:before{content:"";position:absolute;left:0;right:0;top:0;aspect-ratio:4/5;border-radius:20px;box-shadow:inset 0 0 0 1px rgba(0,0,0,.04);z-index:2;pointer-events:none}
.wt-pbadge{position:absolute;left:14px;top:14px;z-index:3;background:#fff;color:var(--g);font:700 12.5px/1 var(--sans);padding:8px 11px;border-radius:999px;box-shadow:0 4px 14px rgba(0,0,0,.08)}
.wt-pcat{display:block;padding:0 4px;font:600 11px/1 var(--sans);letter-spacing:.12em;text-transform:uppercase;color:var(--tx2);margin-bottom:8px}
.woocommerce ul.products li.product .woocommerce-loop-product__title{font:600 17px/1.3 var(--sans)!important;padding:0 4px!important;margin:0 0 8px!important;color:var(--tx)!important}
.woocommerce ul.products li.product .price{padding:0 4px!important;display:flex!important;gap:8px;align-items:baseline;flex-wrap:wrap;font:700 17px var(--sans)!important;color:var(--tx)!important;margin:0!important}
.woocommerce ul.products li.product .price ins{text-decoration:none!important;color:var(--g)}
.woocommerce ul.products li.product .price del{font-weight:500;font-size:14px!important;opacity:.7;order:2}
.woocommerce ul.products li.product .button{position:relative;display:flex!important;align-items:center;justify-content:center;gap:8px;margin:16px 0 0!important;height:48px;padding:0 20px!important;border-radius:999px!important;font:600 14px var(--sans)!important;background:var(--g)!important;color:#fff!important;transition:background .3s,transform .3s var(--ease)!important;overflow:hidden}
.wt-bi{position:relative;z-index:1;width:16px;height:16px;flex:0 0 16px;display:inline-block;background:no-repeat center/16px url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E");transition:transform .45s var(--ease)}
.wt-fx:hover .wt-bi{transform:translateX(4px)}
.loading>.wt-bi{background:none;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;width:16px;height:16px;animation:wtSpin .7s linear infinite;transform:none!important}
.added>.wt-bi{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12l5 5 9-10'/%3E%3C/svg%3E");animation:wtPop .45s var(--ease)}
.woocommerce ul.products li.product .button.loading,.single_add_to_cart_button.loading{opacity:1!important;pointer-events:none}
.woocommerce ul.products li.product .button.loading:after,.single_add_to_cart_button.loading:after{display:none!important}
.woocommerce ul.products li.product .button.added,.single_add_to_cart_button.added{background:#2E6B4F!important}
@keyframes wtPop{0%{transform:scale(.3)}60%{transform:scale(1.25)}100%{transform:none}}
@keyframes wtSpin{to{transform:rotate(360deg)}}
.wt-ajaxing ul.products{opacity:.35;filter:blur(2px);pointer-events:none;transition:opacity .3s,filter .3s}
.wt-loadbar{position:fixed;left:0;top:0;height:3px;width:0;background:var(--g);z-index:100001;transition:width .4s var(--ease),opacity .3s}
.woocommerce ul.products li.product .added_to_cart{display:none!important}
.woocommerce nav.woocommerce-pagination ul{border:0!important;display:flex;justify-content:center;gap:8px;margin-top:48px!important}
.woocommerce nav.woocommerce-pagination ul li{border:0!important}
.woocommerce nav.woocommerce-pagination ul li a,.woocommerce nav.woocommerce-pagination ul li span{width:44px;height:44px;display:grid!important;place-items:center;padding:0!important;border-radius:50%;border:1px solid var(--ln)!important;font-weight:600;color:var(--tx)!important;transition:.25s}
.woocommerce nav.woocommerce-pagination ul li span.current,.woocommerce nav.woocommerce-pagination ul li a:hover{background:var(--g)!important;border-color:var(--g)!important;color:#fff!important}
.woocommerce-no-products-found .woocommerce-info{border:0!important;border-radius:20px;background:var(--off)!important;padding:40px!important;text-align:center}
@media (max-width:1024px){.woocommerce ul.products,.woocommerce ul.products.wt-c3{grid-template-columns:repeat(3,1fr)!important}.wt-view{display:none}}
@media (max-width:768px){.woocommerce ul.products,.woocommerce ul.products.wt-c3{grid-template-columns:repeat(2,1fr)!important;gap:24px 14px!important}.wt-toolbar{border-radius:16px;padding:10px 12px}.wt-toolbar .woocommerce-result-count{font-size:13px}.woocommerce ul.products li.product .woocommerce-loop-product__title{font-size:15px!important}.admin-bar .wt-toolbar{top:46px}}
@media (max-width:420px){.woocommerce ul.products li.product .button{height:42px;font-size:13px!important}}
@media (prefers-reduced-motion:reduce){.woocommerce ul.products li.product,.wt-archive-hero h1,.wt-archive-hero p,.wt-chips{animation:none!important;opacity:1!important}}

/* drawer fit + toasts on top */
.elementor-menu-cart__container{inset:0!important;width:auto!important;overflow:hidden!important}
.elementor-menu-cart__main{position:fixed!important;top:0!important;right:0!important;left:auto!important;bottom:0!important;height:100%!important;max-width:100vw!important;box-sizing:border-box!important;overflow:hidden!important}
.admin-bar .elementor-menu-cart__main{top:32px!important;height:calc(100% - 32px)!important}
@media (max-width:782px){.admin-bar .elementor-menu-cart__main{top:46px!important;height:calc(100% - 46px)!important}}
.elementor-menu-cart__close-button{position:absolute!important;z-index:3}
.elementor-menu-cart__subtotal .amount{white-space:nowrap}
.wt-toasts{top:20px!important;bottom:auto!important;right:auto!important;left:50%!important;transform:translateX(-50%);align-items:center}
.admin-bar .wt-toasts{top:52px!important}
.wt-toast{width:100%;transform:translateY(-140%)}
.wt-toast.is-out{transform:translateY(-140%)!important;opacity:0}
@keyframes wtToastIn{from{transform:translateY(-140%) scale(.96);opacity:0}to{transform:none;opacity:1}}
@media (max-width:600px){.wt-toasts{left:50%!important;right:auto!important;width:calc(100vw - 32px)}}

/* cart totals shipping */
.woocommerce-cart .cart_totals>h2{font-size:0!important}.woocommerce-cart .cart_totals>h2:before{content:"Order summary";font:700 22px/1.2 var(--sans)}
.woocommerce-cart .cart_totals table.shop_table,.woocommerce-cart .cart_totals table.shop_table tbody{display:block;border:0!important}
.woocommerce-cart .cart_totals table.shop_table tr{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid var(--ln)}
.woocommerce-cart .cart_totals table.shop_table th,.woocommerce-cart .cart_totals table.shop_table td{display:block;border:0!important;padding:0!important;background:none!important;text-align:right;width:auto!important}
.woocommerce-cart .cart_totals table.shop_table th{text-align:left;font:600 14px var(--sans);color:var(--tx2)}
.woocommerce-cart .cart_totals tr.woocommerce-shipping-totals td:before,.woocommerce-cart .cart_totals tr td:before{display:none!important}
.woocommerce-cart .cart_totals #shipping_method{list-style:none;margin:0!important;padding:0!important}
.woocommerce-cart .cart_totals #shipping_method li{margin:0!important;display:inline-flex;align-items:center;gap:6px;background:var(--sage);color:var(--g);border-radius:999px;padding:6px 12px;font:700 13px var(--sans)}
.woocommerce-cart .cart_totals #shipping_method li input{display:none}
.woocommerce-cart .cart_totals #shipping_method li:before{content:"";width:14px;height:14px;background:no-repeat center/14px url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231D4032' stroke-width='2.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12l5 5 9-10'/%3E%3C/svg%3E")}
.woocommerce-cart .cart_totals .woocommerce-shipping-destination,.woocommerce-cart .cart_totals .woocommerce-shipping-calculator{display:none!important}
.woocommerce-cart .cart_totals tr.order-total{border-bottom:0;padding-top:18px}
.woocommerce-cart .cart_totals tr.order-total th{color:var(--tx);font-size:16px}

/* single product layout fix */
.single-product div.product{grid-template-columns:minmax(0,1.1fr) minmax(0,1fr)!important;align-items:start}
.single-product div.product>*:not(.woocommerce-product-gallery):not(.summary){grid-column:1/-1!important}
.single-product .woocommerce-product-gallery,.single-product div.product .summary{position:relative!important;top:auto!important}
.single-product .woocommerce-product-gallery{min-width:0;overflow:hidden}
.single-product .woocommerce-tabs.wt-acc{max-width:900px;margin:56px auto 0!important;width:100%}
.single-product .woocommerce-tabs .panel{margin:0!important}
.single-product .woocommerce-tabs .panel>h2:first-child{display:none}
.wt-related{margin:80px 0 20px}
.wt-related .wtpc{padding-top:40px;border-top:1px solid var(--ln)}

.wt-acc .wt-acc__p{grid-template-rows:0fr!important;visibility:hidden}
.wt-acc .wt-acc__p.open{grid-template-rows:1fr!important;visibility:visible}
.wt-acc .wt-acc__in{overflow:hidden;min-height:0}
.wt-acc .wt-acc__in>h2:first-child{display:none}

/* single add-to-cart: bag icon instead of arrow */
.single-product .single_add_to_cart_button:before{display:none!important}
.single_add_to_cart_button .wt-bi{order:-1;width:20px;height:20px;flex-basis:20px;background-size:20px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3C/svg%3E")}
.single_add_to_cart_button.wt-fx:hover .wt-bi{transform:translateY(-2px) rotate(-8deg)}

/* buy now */
.single-product form.cart{flex-wrap:wrap}
.wt-buynow{flex:1 1 100%;height:56px;display:flex;align-items:center;justify-content:center;gap:10px;border:1.5px solid var(--g)!important;background:#fff!important;color:var(--g)!important;border-radius:14px!important;font:700 16px/1 var(--sans)!important;cursor:pointer;position:relative;overflow:hidden;isolation:isolate;transition:color .35s,transform .2s,box-shadow .35s}
.wt-buynow:before{content:"";position:absolute;inset:0;background:var(--g);transform:scaleX(0);transform-origin:left;transition:transform .5s var(--ease);z-index:-1}
.wt-buynow:hover{color:#fff!important;box-shadow:0 14px 30px -14px rgba(21,53,40,.55)}.wt-buynow:hover:before{transform:none}
.wt-buynow:active{transform:scale(.98)}
.wt-buynow svg{width:18px;height:18px;fill:currentColor;transition:transform .4s var(--ease)}.wt-buynow:hover svg{transform:scale(1.15) rotate(-8deg)}
.wt-buynow.is-loading{pointer-events:none;opacity:.8}.wt-buynow.is-loading svg{animation:wtSpin .8s linear infinite}
.variations_form .wt-buynow{margin-top:0}

/* Barlow everywhere (icon fonts excluded) */
html,body,body :where(*:not(i):not(svg):not(svg *):not([class*="eicon"]):not([class*="fa-"]):not([class^="fa"]):not(.dashicons):not([class*="dashicons-"]):not(.star-rating):not(.star-rating *):not(.stars *)),button,input,select,textarea,.elementor-widget-container,.elementor *:not(i):not([class*="eicon"]):not([class*="fa-"]),.woocommerce *:not(.star-rating):not(.star-rating *):not(i),.wtpc *,.wt3s *,.wtwu *,.wta *,.wtu *,.wtd *,.wt-toast *{font-family:'Barlow',system-ui,-apple-system,sans-serif!important}
#wpadminbar *{font-family:inherit}

/* Reviews v2 (Elementor template) */
.wt-rv2 .wt-rv2__lbl .elementor-heading-title{font:500 12px/1 var(--sans)!important;letter-spacing:2px;text-transform:uppercase;color:#555B56!important}
.wt-rv2 .wt-rv2__h .elementor-heading-title{font:600 clamp(28px,2.8vw,40px)/1.15 'Barlow',serif!important;color:#1D211E!important;letter-spacing:-.01em}
.wt-rv2 .wt-rv2__all .elementor-button{background:none!important;padding:0!important;color:#1D211E!important;font:600 14px var(--sans)!important;border:0!important;box-shadow:none!important;transition:color .3s}
.wt-rv2 .wt-rv2__all .elementor-button:hover{color:var(--g)!important}
.wt-rv2 .wt-rv2__all .elementor-button-icon{transition:transform .3s var(--ease)}.wt-rv2 .wt-rv2__all .elementor-button:hover .elementor-button-icon{transform:translateX(4px)}
.wt-rv2 .wt-rv2__all .elementor-button-icon svg{width:12px;fill:currentColor}
.wt-rv2__row>.wt-rv2__card,.wt-rv2 .wt-rv2__card{flex:1 1 0;min-width:0;background:#fff;border:1px solid #E7E9E5;border-radius:12px;transition:transform .3s var(--ease),box-shadow .3s var(--ease)}
.wt-rv2 .wt-rv2__card:hover{transform:translateY(-4px);box-shadow:0 14px 30px -18px rgba(16,30,22,.22)}
.wt-rv2 .wt-rv2__stars .elementor-star-rating{font-size:15px}
.wt-rv2 .wt-rv2__q p{margin:0;font:400 14.5px/1.55 var(--sans);color:#3A3F3B}
.wt-rv2 .wt-rv2__av img{width:40px!important;height:40px!important;border-radius:50%;object-fit:cover}
.wt-rv2 .wt-rv2__n .elementor-heading-title{font:600 14px/1.2 var(--sans)!important;color:#1D211E!important}
.wt-rv2 .wt-rv2__v p{margin:0;font:400 12.5px/1.3 var(--sans);color:#8A8F8B}
.wt-rv2 .wt-rv2__r{align-self:stretch}
.wt-rv2 .wt-rv2__img,.wt-rv2 .wt-rv2__img .elementor-widget-container{height:100%;border-radius:10px;overflow:hidden}
.wt-rv2 .wt-rv2__img img{width:100%!important;height:100%!important;min-height:170px;object-fit:cover;display:block;transition:transform .6s var(--ease)}
.wt-rv2 .wt-rv2__card:hover .wt-rv2__img img{transform:scale(1.03)}
.wt-rv2-dots{display:none;justify-content:center;gap:8px;margin-top:4px}
.wt-rv2-dots button{width:8px;height:8px;padding:0!important;border:0!important;border-radius:999px!important;background:#D3D7D2!important;transition:width .3s var(--ease),background .3s}
.wt-rv2-dots button.on{width:22px;background:var(--g)!important}
@media (max-width:1024px){.wt-rv2 .wt-rv2__row{overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;padding-bottom:6px!important}.wt-rv2 .wt-rv2__row::-webkit-scrollbar{display:none}
 .wt-rv2 .wt-rv2__card{flex:0 0 calc(50% - 10px)!important;scroll-snap-align:start}.wt-rv2-dots{display:flex}}
@media (max-width:767px){.wt-rv2 .wt-rv2__card{flex:0 0 88%!important}.wt-rv2 .wt-rv2__head{flex-direction:column!important;align-items:flex-start!important;gap:10px}}
@media (prefers-reduced-motion:reduce){.wt-rv2 .wt-rv2__card,.wt-rv2 .wt-rv2__img img{transition:none!important}.wt-rv2 .animated{animation:none!important}}

/* mobile polish */
@media (max-width:767px){
 .wth-tools>.elementor-element.wth-cta,body .elementor-element.wth-cta.elementor-hidden-mobile{display:none!important}
 .wth-header,.wth-header>.e-con-inner{flex-wrap:nowrap!important;gap:2px!important;padding-left:16px!important;padding-right:12px!important;min-height:60px}
 .wth-logo{margin-right:auto!important}.wth-logo .elementor-heading-title{font-size:15px!important;letter-spacing:.22em!important}
 .wth-tools{gap:0!important}.wth-tools .elementor-icon,.wth-tools .elementor-menu-cart__toggle .elementor-button{padding:10px!important}
 .wth-nav{flex:0 0 auto!important;order:5}.wth-nav .elementor-menu-toggle{width:44px;height:44px;display:grid!important;place-items:center;padding:0!important;border-radius:12px!important;background:var(--off)!important;margin-left:4px}
 .wth-announce{font-size:12px}
 html,body{overflow-x:clip}
 .elementor-widget-heading h1.elementor-heading-title{font-size:clamp(30px,8.6vw,40px)!important;line-height:1.08!important}
 .elementor-widget-heading h2.elementor-heading-title{font-size:clamp(26px,7.4vw,34px)!important;line-height:1.12!important}
 .wt-b .elementor-button,.wt-b--o .elementor-button{width:100%;justify-content:center;min-height:52px;border-radius:12px!important}
 input,select,textarea{font-size:16px!important}
 .wt-toolbar{top:auto;position:relative}
 .woocommerce ul.products li.product .button{height:44px}
 .single-product .product_title{font-size:clamp(26px,7.5vw,34px)!important}
 .single-product .summary .price{font-size:28px!important}
 .wt-sticky-buy{padding-bottom:max(10px,env(safe-area-inset-bottom))}
 .wt-toast{font-size:13.5px;padding:12px 14px}
 .woocommerce-checkout #order_review,.woocommerce-cart .cart-collaterals{position:static!important}
}
@media (max-width:380px){.wth-tools .elementor-widget:first-child{display:none}.wth-logo .elementor-heading-title{letter-spacing:.16em!important}}

/* mobile slide panel menu */
@media (max-width:1024px){.wth-nav .elementor-nav-menu--dropdown.elementor-nav-menu__container{display:none!important}}
.wtm-ov{position:fixed;inset:0;background:rgba(16,26,21,.45);backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);z-index:100002;opacity:0;visibility:hidden;transition:opacity .4s,visibility .4s}
.wtm{position:fixed;top:0;right:0;bottom:0;width:min(400px,88vw);background:#fff;z-index:100003;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .55s var(--ease);box-shadow:-30px 0 60px -30px rgba(0,0,0,.4);font-family:var(--sans)}
.admin-bar .wtm{top:46px}@media (min-width:783px){.admin-bar .wtm{top:32px}}
body.wtm-open .wtm{transform:none}body.wtm-open .wtm-ov{opacity:1;visibility:visible}body.wtm-open{overflow:hidden}
.wtm__top{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--ln)}
.wtm__logo{font:600 15px/1 var(--sans);letter-spacing:.24em;color:var(--tx)}
.wtm__x{width:42px;height:42px;border-radius:50%!important;border:0!important;background:var(--off)!important;color:var(--tx)!important;display:grid;place-items:center;padding:0!important;cursor:pointer;transition:transform .4s var(--ease),background .3s}
.wtm__x:hover{transform:rotate(90deg);background:var(--sage)!important}.wtm__x svg{width:18px;height:18px;stroke:currentColor;stroke-width:2;fill:none}
.wtm__body{flex:1;overflow-y:auto;padding:8px 20px 20px;overscroll-behavior:contain}
.wtm ul{list-style:none;margin:0;padding:0}
.wtm__nav>li{border-bottom:1px solid var(--ln);opacity:0;transform:translateX(24px);transition:opacity .45s,transform .55s var(--ease)}
body.wtm-open .wtm__nav>li{opacity:1;transform:none}
.wtm__row{display:flex;align-items:center}
.wtm__nav>li>.wtm__row>a{flex:1;display:block;padding:18px 0;font:700 19px/1.2 var(--sans);color:var(--tx)!important;text-decoration:none}
.wtm__nav a:hover{color:var(--g)!important}
.wtm__sub-t{width:40px;height:40px;border-radius:50%!important;border:0!important;background:var(--off)!important;color:var(--tx)!important;padding:0!important;display:grid;place-items:center;cursor:pointer;transition:transform .4s var(--ease),background .3s}
.wtm__sub-t svg{width:14px;height:14px;stroke:currentColor;stroke-width:2.4;fill:none}
li.is-open>.wtm__row .wtm__sub-t{transform:rotate(180deg);background:var(--g)!important;color:#fff!important}
.wtm__nav .sub-menu{display:grid;grid-template-rows:0fr;transition:grid-template-rows .45s var(--ease)}
.wtm__nav .sub-menu>.wtm__in{overflow:hidden;min-height:0}
.wtm__nav li.is-open>.sub-menu{grid-template-rows:1fr}
.wtm__nav .sub-menu a{display:block;padding:11px 0 11px 14px;font:500 16px/1.3 var(--sans);color:var(--tx2)!important;text-decoration:none;border-left:2px solid var(--ln)}
.wtm__nav li.is-open>.sub-menu{padding-bottom:12px}.wtm__nav .sub-menu{transition:grid-template-rows .45s var(--ease),padding .45s var(--ease)}
.wtm__quick{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:22px}
.wtm__quick a{display:flex;align-items:center;gap:10px;padding:14px;border-radius:14px;background:var(--off);color:var(--tx)!important;text-decoration:none;font:600 14px var(--sans)}
.wtm__quick svg{width:18px;height:18px;stroke:var(--g);stroke-width:1.8;fill:none}
.wtm__foot{padding:16px 20px max(16px,env(safe-area-inset-bottom));border-top:1px solid var(--ln)}
.wtm__cta{display:flex;align-items:center;justify-content:center;gap:10px;height:54px;border-radius:14px;background:var(--g);color:#fff!important;text-decoration:none;font:700 16px var(--sans);transition:background .3s}
.wtm__cta:hover{background:var(--g3)}.wtm__cta svg{width:16px;height:16px;stroke:#fff;stroke-width:2.2;fill:none;transition:transform .35s var(--ease)}.wtm__cta:hover svg{transform:translateX(4px)}
.wtm__note{margin:10px 0 0;text-align:center;font:500 12.5px var(--sans);color:var(--tx2)}
@media (prefers-reduced-motion:reduce){.wtm,.wtm__nav>li,.wtm-ov{transition:none!important}}

/* Elementor loop carousels: reviews + products */
.wt-lc-rev .e-loop-item,.wt-lc-prod .e-loop-item{height:100%}
.wt-rvc{height:100%;background:#fff;border:1px solid #E7E9E5;border-radius:12px;transition:transform .3s var(--ease),box-shadow .3s var(--ease)}
.wt-rvc:hover{transform:translateY(-4px);box-shadow:0 14px 30px -18px rgba(16,30,22,.22)}
.wt-rvc .wt-rvc__stars .elementor-heading-title{color:#2E8B57!important;font-size:17px!important;letter-spacing:2px;line-height:1!important}
.wt-lc-rev .swiper-slide:is([aria-label^="2 /"],[aria-label^="5 /"],[aria-label^="8 /"],[aria-label^="11 /"]) .wt-rvc__stars .elementor-heading-title{color:#E5AC32!important}
.wt-rvc__img,.wt-rvc__img .elementor-widget-container{height:100%;border-radius:10px;overflow:hidden}
.wt-rvc__img img{width:100%!important;height:100%!important;min-height:180px;object-fit:cover;transition:transform .6s var(--ease)}
.wt-rvc:hover .wt-rvc__img img{transform:scale(1.03)}
.wt-rvc__av img{width:42px!important;height:42px!important;border-radius:50%;object-fit:cover}
.wt-pdc{height:100%;background:#fff;border:1px solid #E5E7E4;border-radius:12px;overflow:hidden;transition:transform .3s var(--ease),box-shadow .3s var(--ease)}
.wt-pdc:hover{transform:translateY(-6px);box-shadow:0 22px 40px -24px rgba(16,30,22,.3)}
.wt-pdc__img .elementor-widget-container,.wt-pdc__img a{display:block;overflow:hidden;background:#EDEDED}
.wt-pdc__img img{width:100%!important;aspect-ratio:4/3;object-fit:cover;display:block;transition:transform .6s var(--ease)}
.wt-pdc:hover .wt-pdc__img img{transform:scale(1.03)}
.wt-pdc__title .elementor-heading-title a{color:inherit!important}
.wt-pdc__price .price{display:flex!important;justify-content:space-between;align-items:baseline;width:100%;margin:0!important;font:700 26px/1 var(--sans)!important;color:#202522!important}
.wt-pdc__price .price ins{text-decoration:none;order:1}.wt-pdc__price .price del{order:2;font:500 14px var(--sans)!important;color:#8A8F8B!important;opacity:1}
.wt-pdc__price .price .screen-reader-text{position:absolute!important}
.wt-pdc__atc form.cart{margin:0!important;display:block!important}
.wt-pdc__atc .quantity{display:none!important}
.wt-pdc__atc .single_add_to_cart_button,.wt-pdc__atc .button{width:100%!important;height:52px!important;border-radius:6px!important;background:#244D3C!important;color:#fff!important;font:600 16px var(--sans)!important;transition:background .25s!important}
.wt-pdc__atc .single_add_to_cart_button:hover{background:#1A3A2D!important}
.wt-pdc__atc .variations,.wt-pdc__atc .single_variation_wrap .woocommerce-variation{display:none}
.wt-lc-prod .swiper{overflow:hidden}
.wt-lc-prod .elementor-swiper-button{width:64px;height:64px;border-radius:50%;background:#fff;box-shadow:0 10px 30px -10px rgba(0,0,0,.3);color:#111;transition:transform .3s var(--ease),opacity .3s}
.wt-lc-prod .elementor-swiper-button:active{transform:translateY(-50%) scale(.92)}
.wt-lc-prod .elementor-swiper-button.swiper-button-disabled{opacity:0;pointer-events:none}
.wt-lc-prod .swiper-pagination-progressbar,.wt-lc-prod .swiper-pagination{position:relative!important;width:min(650px,70%)!important;height:4px!important;margin:44px auto 0!important;border-radius:999px;overflow:hidden;background:#D4D4D4!important;top:auto!important;left:auto!important}
.wt-lc-prod .swiper-pagination-progressbar-fill{background:#111!important;border-radius:999px;transition-timing-function:ease-in-out!important}
.wt-lc-rev .elementor-swiper-button{width:44px;height:44px;border-radius:50%;background:#fff;border:1px solid #E7E9E5;color:#111}
.wt-lc-rev .elementor-swiper-button.swiper-button-disabled{opacity:.35}
.wt-pill .elementor-button{background:#fff!important;border:1px solid #DADDD8!important;border-radius:999px!important;color:#202522!important;padding:8px 8px 8px 26px!important;font:600 16px var(--sans)!important;box-shadow:none!important}
.wt-pill .elementor-button-content-wrapper{align-items:center;gap:18px}
.wt-pill .elementor-button-icon{width:46px;height:46px;border-radius:50%;background:#244D3C;color:#fff;display:grid!important;place-items:center;transition:transform .35s var(--ease)}
.wt-pill .elementor-button-icon svg{width:16px;fill:#fff}
.wt-pill .elementor-button:hover .elementor-button-icon{transform:translateX(4px)}
@media (max-width:767px){.wt-lc-prod .elementor-swiper-button{display:none}.wt-lc-prod .swiper-pagination-progressbar{width:80%!important}}

.wt-lchead{flex-wrap:nowrap!important}.wt-lchead>.e-con{flex:1 1 auto!important;width:auto!important}
.wt-lchead .elementor-widget-button{flex:0 0 auto}
.wt-textlink .elementor-button{background:none!important;border:0!important;padding:0!important;color:#202522!important;font:600 15px var(--sans)!important;box-shadow:none!important;transition:color .3s}
.wt-textlink .elementor-button:hover{color:var(--g)!important}.wt-textlink .elementor-button-icon{transition:transform .3s var(--ease)}.wt-textlink .elementor-button:hover .elementor-button-icon{transform:translateX(4px)}
.wt-textlink .elementor-button-icon svg{width:13px;fill:currentColor}
.wt-lcsec{overflow-x:clip}
.wt-lc-prod .swiper{overflow:visible!important}
.wt-pdc__atc form.cart{display:flex!important;width:100%}
.wt-pdc__atc .single_add_to_cart_button{flex:1 1 100%;display:flex!important;align-items:center;justify-content:center;gap:10px}
.wt-pdc__atc .elementor-add-to-cart,.wt-pdc__atc .elementor-widget-container{width:100%}
.wt-rvc__q,.wt-rvc__q p{font:400 15px/1.55 var(--sans);color:#3A3F3B;margin:0}
@media (max-width:767px){.wt-lchead{flex-wrap:wrap!important;align-items:flex-start!important}.wt-rvc__img img{min-height:150px}}

.wt-lc-rev .swiper{padding:2px!important;margin:-2px!important}
.wt-pdc__atc .add_to_cart_button,.wt-pdc__atc a.button{display:flex!important;align-items:center;justify-content:center;gap:10px;width:100%!important;height:52px!important;padding:0 16px!important;border-radius:6px!important;background:#244D3C!important;color:#fff!important;font:600 16px/1 var(--sans)!important;text-decoration:none!important;transition:background .25s,transform .2s!important}
.wt-pdc__atc .add_to_cart_button:hover{background:#1A3A2D!important}.wt-pdc__atc .add_to_cart_button:active{transform:scale(.98)}
.wt-pdc__atc .add_to_cart_button:before{content:"";width:18px;height:18px;flex:0 0 18px;background:currentColor;-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3C/svg%3E") center/contain no-repeat;position:static!important;transition:transform .35s var(--ease)}
.wt-pdc__atc .add_to_cart_button:hover:before{transform:translateY(-2px) rotate(-8deg)}
.wt-pdc__atc .add_to_cart_button.loading{opacity:1!important;pointer-events:none}
.wt-pdc__atc .add_to_cart_button.loading:before{-webkit-mask:none;mask:none;background:none;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;width:16px;height:16px;flex-basis:16px;animation:wtSpin .7s linear infinite}
.wt-pdc__atc .add_to_cart_button.loading:after,.wt-pdc__atc .add_to_cart_button.added:after{display:none!important}
.wt-pdc__atc .add_to_cart_button.added{background:#2E6B4F!important}
.wt-pdc__atc .added_to_cart{display:none!important}

/* Review slider widget */
.wt-rs{--per:3;--gap:20px;position:relative}
.wt-rs__view{overflow:hidden;outline:0;margin:-4px;padding:4px}
.wt-rs__track{list-style:none;margin:0;padding:0;display:flex;gap:var(--gap);transition:transform .6s ease-in-out;will-change:transform;touch-action:pan-y}
.wt-rs__slide{flex:0 0 calc((100% - (var(--per) - 1) * var(--gap)) / var(--per));min-width:0;display:flex}
.wt-rs__card{margin:0;display:flex;gap:16px;width:100%;padding:18px;background:#fff;border:1px solid #E7E9E5;border-radius:12px;transition:transform .3s ease,box-shadow .3s ease}
.wt-rs__card:hover{transform:translateY(-4px);box-shadow:0 14px 30px -18px rgba(16,30,22,.22)}
.wt-rs__l{flex:1 1 60%;display:flex;flex-direction:column;gap:12px;min-width:0}
.wt-rs__stars{font-size:17px;letter-spacing:2px;line-height:1}.wt-rs__off{color:#D9DCD7}
.wt-rs__q{margin:0;padding:0;border:0;font:400 15px/1.55 var(--sans);color:#3A3F3B;quotes:none}
.wt-rs__q:before,.wt-rs__q:after{display:none}
.wt-rs__who{margin-top:auto;display:flex;align-items:center;gap:10px}
.wt-rs__who b{display:block;font:600 15px/1.2 var(--sans);color:#1D211E}.wt-rs__who small{font:400 13px var(--sans);color:#8A8F8B}
.wt-rs__av{width:42px;height:42px;border-radius:50%;object-fit:cover;flex:none}
.wt-rs__av--i{display:grid;place-items:center;background:var(--sage);color:var(--g);font:700 16px var(--sans)}
.wt-rs__r{flex:0 0 38%;border-radius:10px;overflow:hidden;min-height:190px}
.wt-rs__img{width:100%!important;height:100%!important;object-fit:cover;display:block;transition:transform .6s ease}
.wt-rs__card:hover .wt-rs__img{transform:scale(1.03)}
.wt-rs__nav{display:flex;align-items:center;justify-content:center;gap:16px;margin-top:22px}
.wt-rs__btn{width:44px;height:44px;border-radius:50%!important;border:1px solid #E7E9E5!important;background:#fff!important;color:#111!important;display:grid;place-items:center;padding:0!important;cursor:pointer;transition:background .25s,opacity .25s,transform .2s}
.wt-rs__btn:hover{background:var(--off)!important}.wt-rs__btn:active{transform:scale(.92)}.wt-rs__btn[disabled]{opacity:.35;pointer-events:none}
.wt-rs__btn svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.wt-rs__dots{display:flex;gap:8px}
.wt-rs__dots button{width:8px;height:8px;padding:0!important;border:0!important;border-radius:999px!important;background:#D3D7D2!important;cursor:pointer;transition:width .3s,background .3s}
.wt-rs__dots button.on{width:22px;background:var(--g)!important}
.wt-rs.is-static .wt-rs__nav{display:none}
@media (max-width:1024px){.wt-rs{--per:2}}@media (max-width:767px){.wt-rs{--per:1}.wt-rs__card{padding:14px;gap:12px}.wt-rs__r{min-height:160px}}
@media (prefers-reduced-motion:reduce){.wt-rs__track,.wt-rs__card,.wt-rs__img{transition:none!important}}

/* header: equal gaps between logo, menu and tools */
@media (min-width:1025px){
 .wth-header>.e-con-inner,.wth-header.e-con-full{display:flex!important;flex-wrap:nowrap!important;justify-content:space-between!important;align-items:center;gap:24px}
 .wth-header .wth-logo,.wth-header .wth-tools{flex:0 0 auto!important;width:auto!important;margin:0!important}
 .wth-header .wth-nav{flex:0 1 auto!important;width:auto!important;margin:0!important}
}
@media (min-width:1025px) and (max-width:1300px){.wth-nav .elementor-nav-menu--main .elementor-item{padding-left:9px!important;padding-right:9px!important;font-size:16px!important}.wth-nav .e--pointer-underline .elementor-item:after{left:9px!important;right:9px!important}.wth-cta .elementor-button{padding:0 14px!important}}

/* header: lock vertical centring */
@media (min-width:1025px){
 .wth-header{padding-top:0!important;padding-bottom:0!important;min-height:0!important}
 .wth-header>.e-con-inner{height:76px!important;min-height:76px!important;padding-top:0!important;padding-bottom:0!important;align-items:center!important}
 .wth-header>.e-con-inner>.elementor-element{align-self:center!important;margin-top:0!important;margin-bottom:0!important}
 .wth-nav .elementor-nav-menu--main .elementor-item{padding-top:27px!important;padding-bottom:27px!important;line-height:22px!important}
 .wth-nav .e--pointer-underline .elementor-item:after{bottom:16px!important}
 .wth-logo .elementor-heading-title{line-height:1!important;display:block}
}

/* remove 20px gap between announce bar and header */
.wth{--gap:0px!important;--row-gap:0px!important;row-gap:0!important;gap:0!important}

/* contact page */
.wtc-hero{position:relative;overflow:hidden;background:radial-gradient(120% 140% at 85% 0%,#2F5A47 0%,#1D4032 45%,#153528 100%)!important}
.wtc-hero:after{content:"";position:absolute;right:-140px;bottom:-200px;width:520px;height:520px;border-radius:50%;border:1px solid rgba(255,255,255,.08);box-shadow:0 0 0 60px rgba(255,255,255,.025),0 0 0 140px rgba(255,255,255,.02);pointer-events:none}
.wtc-hero .elementor-heading-title{color:#fff!important}
.wtc-eyebrow .elementor-heading-title{font:600 12px/1 var(--sans)!important;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,.7)!important}
.wtc-chips{display:flex;flex-wrap:wrap;gap:10px;margin-top:6px}
.wtc-chips span{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:999px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);color:#fff;font:500 14px var(--sans);backdrop-filter:blur(6px)}
.wtc-chips span:before{content:"";width:8px;height:8px;border-radius:50%;background:#7FD3A6;box-shadow:0 0 0 4px rgba(127,211,166,.18)}
.wtc-main{margin-top:-90px!important;position:relative;z-index:2}
.wtc-card{background:#fff;border:1px solid var(--ln);border-radius:22px;box-shadow:0 30px 60px -40px rgba(16,30,22,.35)}
.wtc-info .elementor-icon-box-wrapper{display:flex!important;gap:16px;align-items:flex-start;text-align:left!important}
.wtc-info{padding:18px!important;border-radius:16px;transition:background .3s,transform .3s var(--ease)}
.wtc-info:hover{background:var(--sage);transform:translateX(4px)}
.wtc-info .elementor-icon{width:48px;height:48px;border-radius:14px;background:var(--sage);color:var(--g)!important;display:grid!important;place-items:center;font-size:20px!important;transition:background .3s,color .3s}
.wtc-info:hover .elementor-icon{background:var(--g);color:#fff!important}
.wtc-info .elementor-icon svg{fill:currentColor;width:20px;height:20px}
.wtc-info .elementor-icon-box-title{margin:0 0 4px!important;font:700 16px/1.3 var(--sans)!important;color:var(--tx)}
.wtc-info .elementor-icon-box-description{font:400 15px/1.5 var(--sans)!important;color:var(--tx2)}
.wtc-form .elementor-field-group{margin-bottom:16px!important}
.wtc-form .elementor-field-label{font:600 13px var(--sans)!important;color:var(--tx)!important;margin-bottom:8px!important}
.wtc-form .elementor-field-textual,.wtc-form select{background:var(--off)!important;border:1px solid transparent!important;border-radius:12px!important;min-height:52px;padding:12px 16px!important;font:500 15px var(--sans)!important;color:var(--tx)!important;transition:border-color .25s,background .25s,box-shadow .25s}
.wtc-form textarea.elementor-field-textual{min-height:140px}
.wtc-form .elementor-field-textual:focus,.wtc-form select:focus{background:#fff!important;border-color:var(--g)!important;box-shadow:0 0 0 4px rgba(29,64,50,.1)!important;outline:0}
.wtc-form .elementor-field-type-acceptance label{font:400 13px/1.5 var(--sans);color:var(--tx2)}
.wtc-form .elementor-button{width:100%;min-height:56px;border-radius:14px!important;background:var(--g)!important;font:700 16px var(--sans)!important;transition:background .3s,transform .2s!important}
.wtc-form .elementor-button:hover{background:var(--g3)!important}.wtc-form .elementor-button:active{transform:scale(.98)}
.wtc-form .elementor-message{border-radius:12px;padding:12px 14px;margin-top:12px;font:500 14px var(--sans)}
.wtc-form .elementor-message-success{background:var(--sage);color:var(--g)}
.wtc-cta{background:var(--beige)!important;border-radius:24px}
.wtc-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.wtc-steps div{background:#fff;border-radius:16px;padding:18px;font:500 14px/1.45 var(--sans);color:var(--tx2)}
.wtc-steps b{display:grid;place-items:center;width:30px;height:30px;border-radius:50%;background:var(--g);color:#fff;font:700 13px var(--sans);margin-bottom:10px}
.wtc-steps strong{display:block;color:var(--tx);font:700 15px var(--sans);margin-bottom:4px}
@media (max-width:767px){.wtc-main{margin-top:-50px!important}.wtc-steps{grid-template-columns:1fr}}

@media (max-width:767px){.wtc-info .elementor-icon-box-wrapper{flex-direction:row!important;text-align:left!important}.wtc-info .elementor-icon-box-icon{margin:0!important;flex:none}.wtc-info{padding:12px 4px!important}}

/* info pages */
.wtp-narrow{max-width:720px}
.wtp-label .elementor-heading-title{font:600 12px/1 var(--sans)!important;letter-spacing:3px;text-transform:uppercase;color:var(--g)!important}
.wtp-a{opacity:0;transform:translateY(22px);transition:opacity .8s var(--ease),transform .8s var(--ease);transition-delay:var(--d,0ms)}
.wtp-a.is-in,.elementor-editor-active .wtp-a{opacity:1;transform:none}
.wtc-hero .wtp-a.is-in:nth-child(2){transition-delay:80ms}.wtc-hero .wtp-a.is-in:nth-child(3){transition-delay:160ms}.wtc-hero .wtp-a.is-in:nth-child(4){transition-delay:240ms}
.wtp-cards{display:grid;gap:20px}.wtp-cols-3{grid-template-columns:repeat(3,1fr)}
.wtp-card{background:#fff;border:1px solid var(--ln);border-radius:20px;padding:28px;transition:opacity .8s var(--ease),transform .5s var(--ease),box-shadow .4s var(--ease),border-color .3s}
.wtp-card.is-in:hover{transform:translateY(-6px);box-shadow:0 26px 50px -30px rgba(16,30,22,.35);border-color:#D5DCD3}
.wtp-ic{width:54px;height:54px;border-radius:16px;background:var(--sage);color:var(--g);display:grid;place-items:center;margin-bottom:18px;transition:background .35s,color .35s,transform .5s var(--ease)}
.wtp-card:hover .wtp-ic{background:var(--g);color:#fff;transform:rotate(-6deg) scale(1.05)}
.wtp-ic svg{width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.wtp-card h3{margin:0 0 8px;font:700 19px/1.3 var(--sans);color:var(--tx)}
.wtp-card p{margin:0;font:400 15.5px/1.6 var(--sans);color:var(--tx2)}
.wtp-steps{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;counter-reset:s;position:relative}
.wtp-steps li{position:relative;background:#fff;border-radius:20px;padding:26px 24px;display:flex;flex-direction:column;gap:14px;border:1px solid var(--ln);transition:opacity .8s var(--ease),transform .5s var(--ease),box-shadow .4s}
.wtp-steps li.is-in:hover{transform:translateY(-4px);box-shadow:0 20px 40px -28px rgba(16,30,22,.35)}
.wtp-n{width:44px;height:44px;border-radius:50%;background:var(--g);color:#fff;display:grid;place-items:center;font:700 17px var(--sans);box-shadow:0 0 0 6px var(--sage)}
.wtp-steps h3{margin:0 0 6px;font:700 18px/1.3 var(--sans);color:var(--tx)}.wtp-steps p{margin:0;font:400 15px/1.6 var(--sans);color:var(--tx2)}
.wtp-steps li:not(:last-child):after{content:"";position:absolute;top:48px;right:-20px;width:20px;height:2px;background:linear-gradient(90deg,var(--g),transparent)}
.wtp-two{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.wtp-list{border-radius:20px;padding:28px}.wtp-list h3{margin:0 0 14px;font:700 20px var(--sans)}
.wtp-list ul{list-style:none;margin:0;padding:0;display:grid;gap:12px}.wtp-list li{position:relative;padding-left:32px;font:400 16px/1.5 var(--sans);color:var(--tx)}
.wtp-list li:before{content:"";position:absolute;left:0;top:1px;width:22px;height:22px;border-radius:50%;background:center/12px no-repeat}
.wtp-yes{background:var(--sage)}.wtp-yes h3{color:var(--g)}.wtp-yes li:before{background-color:var(--g);background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12l5 5 9-10'/%3E%3C/svg%3E")}
.wtp-no{background:#FBF1EF}.wtp-no h3{color:#8C3B2C}.wtp-no li:before{background-color:#C0574A;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round'%3E%3Cpath d='M7 7l10 10M17 7L7 17'/%3E%3C/svg%3E")}
.wtp-faq .elementor-toggle-item{background:#fff;border:1px solid var(--ln)!important;border-radius:16px;margin-bottom:12px!important;overflow:hidden;transition:box-shadow .3s,border-color .3s}
.wtp-faq .elementor-toggle-item:hover{border-color:#D5DCD3;box-shadow:0 14px 30px -24px rgba(16,30,22,.4)}
.wtp-faq .elementor-tab-title{border:0!important;padding:20px 22px!important;display:flex!important;align-items:center;flex-direction:row-reverse;justify-content:space-between;gap:16px;cursor:pointer}
.wtp-faq .elementor-tab-title .elementor-toggle-title{font:600 17px/1.4 var(--sans)!important;color:var(--tx)!important}
.wtp-faq .elementor-tab-title.elementor-active .elementor-toggle-title{color:var(--g)!important}
.wtp-faq .elementor-toggle-icon{width:34px!important;height:34px;flex:none;border-radius:50%;background:var(--off);display:grid!important;place-items:center;color:var(--tx)!important;transition:background .3s,color .3s,transform .4s var(--ease);float:none!important}
.wtp-faq .elementor-tab-title.elementor-active .elementor-toggle-icon{background:var(--g);color:#fff!important;transform:rotate(180deg)}
.wtp-faq .elementor-toggle-icon svg{width:12px;fill:currentColor}.wtp-faq .elementor-toggle-icon i{font-size:12px}
.wtp-faq .elementor-tab-content{border:0!important;padding:0 22px 20px!important;font:400 16px/1.65 var(--sans);color:var(--tx2)}
.wtp-faq .elementor-tab-content p{margin:0}
.wtp-search{display:flex;align-items:center;gap:12px;max-width:640px;background:#fff;border:1px solid var(--ln);border-radius:999px;padding:6px 8px 6px 22px;box-shadow:0 20px 40px -32px rgba(16,30,22,.4);transition:border-color .3s,box-shadow .3s}
.wtp-search:focus-within{border-color:var(--g);box-shadow:0 0 0 4px rgba(29,64,50,.1)}
.wtp-search svg{width:20px;height:20px;fill:none;stroke:var(--g);stroke-width:2;flex:none}
.wtp-search input{flex:1;border:0!important;background:none!important;height:46px;font:500 16px var(--sans);color:var(--tx);outline:0;box-shadow:none!important;padding:0!important}
.wtp-hide{display:none!important}
@media (max-width:1024px){.wtp-cols-3{grid-template-columns:1fr 1fr}.wtp-steps li:after{display:none}}
@media (max-width:767px){.wtp-cols-3,.wtp-two{grid-template-columns:1fr}.wtp-card{padding:22px}}
@media (prefers-reduced-motion:reduce){.wtp-a{opacity:1!important;transform:none!important;transition:none!important}}

/* FAQ icon: clean drawn plus/minus */
.wtp-faq .elementor-toggle-icon{position:relative;width:36px!important;height:36px!important;min-width:36px;font-size:0!important;line-height:0!important;margin:0!important}
.wtp-faq .elementor-toggle-icon>span,.wtp-faq .elementor-toggle-icon svg,.wtp-faq .elementor-toggle-icon i{display:none!important}
.wtp-faq .elementor-toggle-icon:before,.wtp-faq .elementor-toggle-icon:after{content:"";position:absolute;left:50%;top:50%;width:13px;height:2px;margin:-1px 0 0 -6.5px;border-radius:2px;background:currentColor;transition:transform .4s var(--ease),opacity .3s}
.wtp-faq .elementor-toggle-icon:after{transform:rotate(90deg)}
.wtp-faq .elementor-tab-title.elementor-active .elementor-toggle-icon{transform:none!important}
.wtp-faq .elementor-tab-title.elementor-active .elementor-toggle-icon:after{transform:rotate(0);opacity:0}
.wtp-faq .elementor-tab-title:hover .elementor-toggle-icon{background:var(--sage)}
.wtp-faq .elementor-tab-title.elementor-active:hover .elementor-toggle-icon{background:var(--g)}

/* FAQ smooth open/close */
.wtp-faq .elementor-tab-content.wtp-sm{display:block!important;height:0;overflow:hidden;opacity:0;padding-top:0!important;padding-bottom:0!important;transition:height .5s cubic-bezier(.22,1,.36,1),opacity .4s ease,padding .5s cubic-bezier(.22,1,.36,1)}
.wtp-faq .elementor-tab-content.wtp-sm.is-open{opacity:1;padding-bottom:20px!important}
.wtp-faq .elementor-tab-content.wtp-sm>*{transform:translateY(-6px);transition:transform .5s cubic-bezier(.22,1,.36,1)}
.wtp-faq .elementor-tab-content.wtp-sm.is-open>*{transform:none}

/* tighter gap between reviews and blog */
.wt-lcsec+.wt-sec{padding-top:56px!important}
@media (max-width:767px){.wt-lcsec+.wt-sec{padding-top:40px!important}}

/* mobile header: icons grouped on the right */
@media (max-width:1024px){
 .wth-header>.e-con-inner{display:flex!important;flex-wrap:nowrap!important;justify-content:flex-start!important;align-items:center!important;gap:0!important}
 .wth-header .wth-logo{order:1;margin-right:auto!important;flex:0 1 auto!important;width:auto!important;min-width:0}
 .wth-header .wth-tools{order:2;flex:0 0 auto!important;width:auto!important;margin:0!important;gap:2px!important;justify-content:flex-end!important}
 .wth-header .wth-nav{order:3;flex:0 0 auto!important;width:auto!important;margin:0 0 0 6px!important}
 .wth-tools>.elementor-element:not(.wth-cta){width:40px!important;height:40px!important;display:grid!important;place-items:center;border-radius:12px;transition:background .25s}
 .wth-tools>.elementor-element:not(.wth-cta):hover{background:var(--off)}
 .wth-tools .elementor-icon,.wth-tools .elementor-menu-cart__toggle .elementor-button,.wth-tools .elementor-search-form__toggle{padding:0!important;width:40px!important;height:40px!important;display:grid!important;place-items:center}
 .wth-tools svg{width:21px!important;height:21px!important}
 .wth-nav .elementor-menu-toggle{width:42px!important;height:42px!important;border-radius:12px!important;background:var(--g)!important;color:#fff!important}
 .wth-nav .elementor-menu-toggle svg,.wth-nav .elementor-menu-toggle i{fill:#fff!important;color:#fff!important;width:20px;height:20px;font-size:20px}
}

/* steps bar mobile fit */
@media (max-width:767px){.wt-steps-bar ol{max-width:100%;width:100%}.wt-steps-bar li{min-width:0}.wt-steps-bar li:not(:last-child):after{margin:0 8px;min-width:12px}.wt-steps-bar__dot{display:grid!important;place-items:center}}

/* footer round buttons: perfectly centred icons */
.wt-news button,.wt-top{padding:0!important;margin:0!important;box-sizing:border-box!important;min-width:0!important;line-height:0!important;font-size:0!important;display:flex!important;align-items:center!important;justify-content:center!important;flex:none}
.wt-news button{width:36px!important;height:36px!important;top:5px!important;right:5px!important}
.wt-top{width:40px!important;height:40px!important}
.wt-news button .wt-ic,.wt-top .wt-ic,.wt-news button svg,.wt-top svg{display:block!important;margin:0!important;flex:none}

/* info pages: native Elementor widgets */
.wtp-grid{width:100%}
.wtp-grid>.e-con{flex:1 1 0!important;min-width:0;width:auto!important}
.wtp-grid-3>.e-con{flex-basis:calc(33.333% - 14px)!important}.wtp-grid-2>.e-con{flex-basis:calc(50% - 10px)!important}.wtp-grid-4>.e-con{flex-basis:calc(25% - 15px)!important}.wtp-grid-6>.e-con{flex-basis:calc(33.333% - 14px)!important}
.wtp-card,.wtp-step{background:#fff!important;border:1px solid var(--ln)!important;border-radius:20px!important;--padding-top:28px;--padding-bottom:28px;--padding-left:26px;--padding-right:26px;padding:28px 26px!important;transition:opacity .8s var(--ease),transform .5s var(--ease),box-shadow .4s var(--ease),border-color .3s}
.wtp-card.is-in:hover,.wtp-step.is-in:hover{transform:translateY(-6px);box-shadow:0 26px 50px -30px rgba(16,30,22,.35);border-color:#D5DCD3!important}
.wtp-card .elementor-heading-title,.wtp-step .elementor-heading-title{margin:0}
.wtp-card .elementor-widget-text-editor p,.wtp-step .elementor-widget-text-editor p{margin:0}
.wtp-ic-n .elementor-icon-wrapper{text-align:left!important}
.wtp-ic-n .elementor-icon{width:54px;height:54px;border-radius:16px;background:var(--sage);color:var(--g)!important;fill:var(--g);display:grid!important;place-items:center;font-size:22px!important;margin-bottom:8px;transition:background .35s,color .35s,transform .5s var(--ease)}
.wtp-ic-n .elementor-icon svg{width:22px;height:22px;fill:currentColor}
.wtp-card:hover .wtp-ic-n .elementor-icon{background:var(--g);color:#fff!important;transform:rotate(-6deg) scale(1.05)}
.wtp-n-n .elementor-heading-title{width:44px;height:44px;border-radius:50%;background:var(--g);color:#fff!important;display:grid!important;place-items:center;font:700 17px/1 var(--sans)!important;box-shadow:0 0 0 6px var(--sage)}
.wtp-step--sm{padding:18px!important;border-radius:16px!important;border:0!important}
.wtp-step--sm .wtp-n-n .elementor-heading-title{width:30px;height:30px;font-size:13px!important;box-shadow:none}
.wtp-list-n{border-radius:20px!important;padding:28px!important}
.wtp-list-n.wtp-yes{background:var(--sage)!important}.wtp-list-n.wtp-no{background:#FBF1EF!important}
.wtp-list-n .elementor-icon-list-item{padding:6px 0!important;font:400 16px/1.5 var(--sans)!important;color:var(--tx)!important}
.wtp-list-n .elementor-icon-list-icon{width:22px!important;height:22px;border-radius:50%;display:grid!important;place-items:center;margin-right:10px!important;flex:none}
.wtp-list-n.wtp-yes .elementor-icon-list-icon{background:var(--g)}.wtp-list-n.wtp-no .elementor-icon-list-icon{background:#C0574A}
.wtp-list-n .elementor-icon-list-icon svg,.wtp-list-n .elementor-icon-list-icon i{width:11px!important;height:11px;fill:#fff!important;color:#fff!important;font-size:11px!important}
.wtc-chips-n .elementor-icon-list-items{display:flex!important;flex-wrap:wrap;gap:10px;margin:6px 0 0!important}
.wtc-chips-n .elementor-icon-list-item{margin:0!important;padding:10px 16px!important;border-radius:999px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);color:#fff!important;font:500 14px var(--sans)!important;backdrop-filter:blur(6px)}
.wtc-chips-n .elementor-icon-list-item:after{display:none!important}
.wtc-chips-n .elementor-icon-list-text{color:#fff!important;padding:0!important}
.wtc-chips-n .elementor-icon-list-icon{width:18px!important;height:18px;border-radius:50%;background:#7FD3A6;display:grid!important;place-items:center;margin-right:8px!important}
.wtc-chips-n .elementor-icon-list-icon svg,.wtc-chips-n .elementor-icon-list-icon i{width:9px!important;fill:#153528!important;color:#153528!important;font-size:9px!important}
@media (max-width:1024px){.wtp-grid-3>.e-con,.wtp-grid-4>.e-con,.wtp-grid-6>.e-con{flex-basis:calc(50% - 10px)!important}}
@media (max-width:767px){.wtp-grid>.e-con{flex-basis:100%!important}.wtp-card,.wtp-step{padding:22px!important}}

/* product short description: no bullet dots */
.single-product .woocommerce-product-details__short-description ul{list-style:none!important;padding-left:0!important;margin-left:0!important}
.single-product .woocommerce-product-details__short-description li{list-style:none!important}
.single-product .woocommerce-product-details__short-description li::marker{content:""!important}

/* single product: mobile/tablet layout */
@media (max-width:1024px){
 .single-product div.product{grid-template-columns:minmax(0,1fr)!important;gap:22px!important;margin-top:12px!important}
 .single-product .woocommerce-product-gallery,.single-product div.product .summary{grid-column:1/-1!important;width:100%!important;max-width:100%!important}
 .single-product .woocommerce-product-gallery{overflow:hidden}
 .single-product .woocommerce-product-gallery__wrapper{border-radius:18px}
 .single-product .flex-control-thumbs{grid-template-columns:repeat(4,1fr)!important;gap:8px!important}
}
@media (max-width:767px){
 .single-product .site-main,.single-product main#content{padding-left:16px!important;padding-right:16px!important}
 .single-product .woocommerce-breadcrumb{padding:0!important;margin-top:16px!important;font-size:12.5px!important}
 .single-product .product_title{font-size:30px!important;line-height:1.08!important}
 .single-product .summary .price{font-size:28px!important}
 .single-product form.cart{gap:10px!important}
 .single-product form.cart .quantity.wt-q{flex:0 0 auto}
 .single-product .single_add_to_cart_button{min-width:0;font-size:15px!important}
 .single-product .wt-trust{grid-template-columns:1fr 1fr!important}
 .single-product .wt-trust li{padding:10px 12px!important;font-size:12.5px!important}
 .single-product .woocommerce-tabs.wt-acc{margin-top:36px!important}
 .wt-related{margin-top:48px}
 .wt-sticky-buy__in{gap:10px}
}

/* ===== Home: native versions of former HTML widgets ===== */
.wt-note .elementor-heading-title{font:italic 600 30px/1.05 'Barlow',sans-serif!important;color:#16211b!important;transform:rotate(-6deg);text-align:right;text-shadow:0 0 2px rgba(255,255,255,.95),0 0 10px rgba(255,248,236,.95),0 0 22px rgba(255,248,236,.85)}
.wt-note:after{content:"";display:block;width:70px;height:46px;margin:4px 0 0 -30px;background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 70 46' fill='none' stroke='%2316211b' stroke-width='1.8' stroke-linecap='round'%3E%3Cpath d='M66 4C50 2 30 8 18 30'/%3E%3Cpath d='M12 22l6 9 9-5'/%3E%3C/svg%3E") no-repeat center/contain;filter:drop-shadow(0 0 3px rgba(255,255,255,.95))}
@media (max-width:767px){.wt-note .elementor-heading-title{font-size:22px!important}.wt-note:after{width:50px}}
.wt-leaf img{display:block;width:100%!important;height:auto!important}
/* 3 steps */
.wt3n{--n-g:#1D4032;--n-g2:#2F6B4F;width:100%;max-width:1150px;margin:0 auto;text-align:center;padding:clamp(56px,6vw,96px) 20px!important}
.wt3n__sub .elementor-heading-title{margin:0 0 12px;font:500 11px/1.2 'Barlow',sans-serif!important;letter-spacing:2px;text-transform:uppercase;color:#4f5550!important}
.wt3n__title .elementor-heading-title{margin:0 0 clamp(28px,3vw,40px)!important;font:600 clamp(28px,2.6vw,38px)/1.18 'Barlow',sans-serif!important;color:#161a17!important;letter-spacing:-.01em}
.wt3n__row{width:100%;display:grid!important;grid-template-columns:1fr 56px 1fr 56px 1fr;align-items:stretch}
.wt3n__card{position:relative;background:#fff!important;border-radius:12px!important;padding:14px 14px 26px!important;text-align:left;transition:opacity .8s var(--ease),transform .35s var(--ease),box-shadow .35s var(--ease)}
.wt3n__card.is-in:hover{transform:translateY(-6px);box-shadow:0 22px 40px -24px rgba(22,40,30,.32)}
.wt3n__media{position:relative;width:100%;border-radius:10px}
.wt3n__media:before{content:"";position:absolute;inset:0;border-radius:10px;background:#eceae4;-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Cpath d='M65.9 0H80V80H0V65.9A10 10 0 0 1 13.72 56.63A33 33 0 0 0 56.63 13.72A10 10 0 0 1 65.9 0Z'/%3E%3C/svg%3E") 0 0/80px 80px no-repeat,linear-gradient(#000,#000) 80px 0/calc(100% - 80px) 100% no-repeat,linear-gradient(#000,#000) 0 80px/100% calc(100% - 80px) no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Cpath d='M65.9 0H80V80H0V65.9A10 10 0 0 1 13.72 56.63A33 33 0 0 0 56.63 13.72A10 10 0 0 1 65.9 0Z'/%3E%3C/svg%3E") 0 0/80px 80px no-repeat,linear-gradient(#000,#000) 80px 0/calc(100% - 80px) 100% no-repeat,linear-gradient(#000,#000) 0 80px/100% calc(100% - 80px) no-repeat}
.wt3n__media img{position:relative;z-index:1;display:block;width:100%!important;aspect-ratio:16/9;object-fit:cover;border-radius:10px!important;-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Cpath d='M65.9 0H80V80H0V65.9A10 10 0 0 1 13.72 56.63A33 33 0 0 0 56.63 13.72A10 10 0 0 1 65.9 0Z'/%3E%3C/svg%3E") 0 0/80px 80px no-repeat,linear-gradient(#000,#000) 80px 0/calc(100% - 80px) 100% no-repeat,linear-gradient(#000,#000) 0 80px/100% calc(100% - 80px) no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Cpath d='M65.9 0H80V80H0V65.9A10 10 0 0 1 13.72 56.63A33 33 0 0 0 56.63 13.72A10 10 0 0 1 65.9 0Z'/%3E%3C/svg%3E") 0 0/80px 80px no-repeat,linear-gradient(#000,#000) 80px 0/calc(100% - 80px) 100% no-repeat,linear-gradient(#000,#000) 0 80px/100% calc(100% - 80px) no-repeat;transition:transform .35s var(--ease)}
.wt3n__card:hover .wt3n__media img{transform:scale(1.03)}
.wt3n__num{position:absolute!important;top:16px;left:16px;width:48px!important;z-index:3}
.wt3n__num .elementor-heading-title{width:48px;height:48px;border-radius:50%;background:var(--n-g);color:#fff!important;display:grid!important;place-items:center;font:600 17px/1 'Barlow',sans-serif!important;transition:transform .35s cubic-bezier(.34,1.56,.64,1),background .35s,box-shadow .35s}
.wt3n__card:hover .wt3n__num .elementor-heading-title{transform:scale(1.1);background:var(--n-g2);box-shadow:0 0 0 5px rgba(47,107,79,.15)}
.wt3n__card.is-in .wt3n__num .elementor-heading-title{animation:wt3nPulse 1.4s ease-out .6s 1}
@keyframes wt3nPulse{0%{box-shadow:0 0 0 0 rgba(47,107,79,.4)}100%{box-shadow:0 0 0 14px rgba(47,107,79,0)}}
.wt3n__h .elementor-heading-title{margin:18px 6px 8px!important;font:600 17px/1.3 'Barlow',sans-serif!important;color:#161a17!important}
.wt3n__p,.wt3n__p p{margin:0 6px!important;font:400 14px/1.5 'Barlow',sans-serif!important;color:#4f5550!important;max-width:240px}
.wt3n__arrow{display:grid!important;place-items:center;align-self:center}
.wt3n__arrow .elementor-icon{font-size:22px!important;color:#4a504c!important;fill:#4a504c;transition:color .35s,transform .35s var(--ease)}
.wt3n__arrow .elementor-icon svg{width:24px;height:auto;fill:currentColor}
.wt3n__arrow.is-in .elementor-icon{animation:wt3nFlow 2.8s ease-in-out 1.6s infinite}
@keyframes wt3nFlow{0%,60%,100%{transform:translateX(0)}30%{transform:translateX(5px)}}
.wt3n__row>.wtp-a:nth-child(2){transition-delay:.15s}.wt3n__row>.wtp-a:nth-child(3){transition-delay:.3s}.wt3n__row>.wtp-a:nth-child(4){transition-delay:.45s}.wt3n__row>.wtp-a:nth-child(5){transition-delay:.6s}
@media (max-width:1024px){.wt3n__row{grid-template-columns:1fr 36px 1fr 36px 1fr}.wt3n__h .elementor-heading-title{font-size:16px!important}.wt3n__p,.wt3n__p p{font-size:13px!important}}
@media (max-width:767px){.wt3n{padding:52px 16px!important}.wt3n__row{grid-template-columns:1fr;justify-items:center;row-gap:6px}.wt3n__card{width:100%!important;max-width:420px}.wt3n__p,.wt3n__p p{max-width:none}.wt3n__arrow{height:44px}.wt3n__arrow .elementor-icon{transform:rotate(90deg)}.wt3n__arrow.is-in .elementor-icon{animation:none}}
/* why + utility */
.wtwn{width:100%}
.wtwn__why{background:#F3F4F0!important;padding:clamp(48px,5vw,72px) 20px clamp(44px,4.5vw,64px)!important}
.wtwn__in{max-width:1200px;margin:0 auto;width:100%;text-align:center}
.wtwn__sub .elementor-heading-title{margin:0 0 12px!important;font:500 11px/1.2 'Barlow',sans-serif!important;letter-spacing:3px;text-transform:uppercase;color:#6d736e!important}
.wtwn__h2 .elementor-heading-title{margin:0!important;font:600 clamp(28px,2.7vw,42px)/1.15 'Barlow',sans-serif!important;color:#1b1f1c!important;letter-spacing:-.01em}
.wtwn__feats{width:100%;display:grid!important;grid-template-columns:repeat(4,1fr);gap:24px!important;margin-top:clamp(30px,3vw,42px)}
.wtwn__feat{justify-content:center;text-align:left;padding:10px 8px!important;border-radius:12px;transition:opacity .7s var(--ease),transform .35s var(--ease)}
.wtwn__feats>.wtp-a:nth-child(2){transition-delay:.11s}.wtwn__feats>.wtp-a:nth-child(3){transition-delay:.22s}.wtwn__feats>.wtp-a:nth-child(4){transition-delay:.33s}
.wtwn__feat>.e-con{width:auto!important}
.wtwn__ic .elementor-icon{width:52px;height:52px;border-radius:50%;display:grid!important;place-items:center;font-size:30px!important;color:#3F5F46!important;fill:#3F5F46;transition:transform .35s var(--ease),box-shadow .35s var(--ease),background .35s}
.wtwn__ic .elementor-icon svg{width:30px;height:30px;fill:currentColor}
.wtwn__feat.is-in:hover{transform:translateY(-3px)}
.wtwn__feat:hover .wtwn__ic .elementor-icon{transform:scale(1.08);background:rgba(78,107,80,.08);box-shadow:0 0 0 6px rgba(78,107,80,.06),0 10px 24px -12px rgba(63,95,70,.55)}
.wtwn__ft .elementor-heading-title{margin:0!important;font:700 15px/1.25 'Barlow',sans-serif!important;color:#2b302c!important}
.wtwn__fs .elementor-heading-title{margin:0!important;font:400 13px/1.3 'Barlow',sans-serif!important;color:#6d736e!important}
.wtwn__show{display:grid!important;grid-template-columns:44% 56%;background:#F8F7F3!important;min-height:clamp(420px,32vw,560px)}
.wtwn__text{padding:clamp(48px,5vw,80px) clamp(32px,4vw,64px) clamp(48px,5vw,80px) max(20px,calc((100vw - 1200px) / 2))!important;align-items:flex-start}
.wtwn__sub--l .elementor-heading-title{text-align:left;margin-bottom:14px!important}
.wtwn__h2--l .elementor-heading-title{text-align:left;font-size:clamp(32px,3.3vw,52px)!important;line-height:1.08!important;max-width:9.6em}
.wtwn__p,.wtwn__p p{margin:18px 0 32px!important;max-width:440px;font:400 16px/1.6 'Barlow',sans-serif!important;color:#4f5550!important}
.wtwn__btn .elementor-button{display:inline-flex!important;align-items:center;gap:12px;height:54px;padding:0 30px!important;border-radius:12px!important;background:#1D3A2E!important;color:#fff!important;font:600 14.5px/1 'Barlow',sans-serif!important;transition:background .3s var(--ease),transform .3s var(--ease),box-shadow .3s var(--ease)!important}
.wtwn__btn .elementor-button:hover{background:#2A5141!important;transform:translateY(-2px);box-shadow:0 14px 28px -14px rgba(29,58,46,.7)}
.wtwn__btn .elementor-button-icon{transition:transform .3s var(--ease)}.wtwn__btn .elementor-button:hover .elementor-button-icon{transform:translateX(4px)}
.wtwn__btn .elementor-button-icon svg{width:15px;fill:#fff}
.wtwn__media{position:relative;min-height:100%}
.wtwn__frame{position:absolute!important;inset:0;width:100%!important;height:100%;overflow:hidden;border-radius:12px 0 0 12px}
.wtwn__frame img{width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;border-radius:0!important;display:block;transition:transform 1.2s var(--ease)}
.wtwn__media:hover .wtwn__frame img{transform:scale(1.03)}
.wtwn__bubble{position:absolute!important;left:16%;bottom:13%;width:auto!important;z-index:2;animation:wtwnFloat 5.5s ease-in-out 1.6s infinite}
.wtwn__bubble .elementor-heading-title{margin:0;padding:16px 30px;background:#fff;border-radius:22px;box-shadow:0 18px 40px -18px rgba(0,0,0,.4);font:500 14px/1.5 'Barlow',sans-serif!important;color:#1b1f1c!important;text-align:center}
@keyframes wtwnFloat{0%,100%{translate:0 0}50%{translate:0 -6px}}
@media (max-width:1024px){.wtwn__feats{grid-template-columns:repeat(2,1fr);row-gap:22px!important}.wtwn__feat{justify-content:flex-start;padding-left:16%!important}.wtwn__show{grid-template-columns:1fr 1fr}.wtwn__text{padding:56px 32px!important}}
@media (max-width:767px){.wtwn__why{padding:48px 16px!important}.wtwn__feats{grid-template-columns:1fr 1fr;gap:20px 12px!important}.wtwn__feat{flex-direction:column!important;align-items:flex-start!important;gap:10px!important;padding:6px!important}.wtwn__ic .elementor-icon{width:44px;height:44px;font-size:26px!important}.wtwn__show{grid-template-columns:1fr}.wtwn__text{padding:44px 16px 32px!important}.wtwn__btn,.wtwn__btn .elementor-button{width:100%;justify-content:center}.wtwn__media{min-height:360px;margin:0 16px 44px;width:auto!important}.wtwn__frame{border-radius:14px}.wtwn__bubble{left:50%;bottom:18px;transform:translateX(-50%);white-space:nowrap}.wtwn__bubble .elementor-heading-title{padding:12px 20px;font-size:13px!important}.wtwn__bubble br{display:none}}
@media (prefers-reduced-motion:reduce){.wtwn__bubble,.wt3n__arrow .elementor-icon{animation:none!important}}

.e-con:has(>.wt3n){background:#F8F7F4!important}

/* ===== Footer (native Elementor) ===== */
.wtf{background:#12241B!important;color:rgba(255,255,255,.72);position:relative;overflow:hidden;padding-top:0!important;margin-top:120px}
.wtf:before{content:"";position:absolute;left:-10%;right:-10%;top:0;height:520px;background:radial-gradient(60% 70% at 80% 0%,rgba(127,211,166,.12),transparent 70%);pointer-events:none}
.wtf-cta-wrap{position:relative;z-index:2;margin-top:-90px!important}
.wtf-cta{background:linear-gradient(120deg,#2F5A47 0%,#1D4032 55%,#163126 100%)!important;border-radius:28px!important;padding:44px 48px!important;box-shadow:0 40px 80px -40px rgba(10,25,18,.6);position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.08)}
.wtf-cta:after{content:"";position:absolute;right:-60px;top:-80px;width:280px;height:280px;border-radius:50%;border:1px solid rgba(255,255,255,.1);box-shadow:0 0 0 40px rgba(255,255,255,.03),0 0 0 90px rgba(255,255,255,.02);pointer-events:none}
.wtf-cta__copy{max-width:620px;position:relative;z-index:1}
.wtf-cta__eyebrow .elementor-heading-title{font:600 12px/1 var(--sans)!important;letter-spacing:3px;text-transform:uppercase;color:#9ED9B8!important;margin:0}
.wtf-cta__title .elementor-heading-title{font:700 clamp(26px,2.8vw,38px)/1.12 var(--sans)!important;color:#fff!important;margin:0;letter-spacing:-.01em}
.wtf-cta__text,.wtf-cta__text p{color:rgba(255,255,255,.75)!important;font:400 16px/1.6 var(--sans)!important;margin:0!important}
.wtf-cta__btns{position:relative;z-index:1;width:auto!important;flex:none}
.wtf-btn .elementor-button{height:54px;display:inline-flex!important;align-items:center;gap:10px;padding:0 26px!important;border-radius:999px!important;font:700 15px/1 var(--sans)!important;transition:transform .3s var(--ease),background .3s,color .3s,box-shadow .3s!important}
.wtf-btn--light .elementor-button{background:#fff!important;color:#163126!important}
.wtf-btn--light .elementor-button:hover{transform:translateY(-2px);box-shadow:0 16px 30px -14px rgba(0,0,0,.5)}
.wtf-btn--light .elementor-button-icon svg{fill:#163126;width:14px}
.wtf-btn--light .elementor-button:hover .elementor-button-icon{transform:translateX(4px)}.wtf-btn .elementor-button-icon{transition:transform .3s var(--ease)}
.wtf-btn--ghost .elementor-button{background:transparent!important;color:#fff!important;border:1px solid rgba(255,255,255,.35)!important}
.wtf-btn--ghost .elementor-button:hover{background:rgba(255,255,255,.08)!important;border-color:#fff!important}
.wtf-main{padding-top:72px!important;padding-bottom:40px!important;position:relative;z-index:1}
.wtf-main>.e-con-inner,.wtf-main{display:grid!important;grid-template-columns:1.4fr .8fr .9fr .8fr 1.5fr;gap:40px}
.wtf-logo .elementor-heading-title{font:600 18px/1 var(--sans)!important;letter-spacing:.32em;color:#fff!important;margin:0}
.wtf-logo .elementor-heading-title a{color:#fff!important}.wtf-logo sup{font-size:9px;letter-spacing:0}
.wtf-about,.wtf-about p{font:400 14.5px/1.7 var(--sans)!important;color:rgba(255,255,255,.66)!important;margin:0!important;max-width:300px}
.wtf-social .elementor-social-icons-wrapper{display:flex!important;gap:10px}
.wtf-social .elementor-social-icon{width:40px!important;height:40px!important;border-radius:50%!important;background:rgba(255,255,255,.07)!important;border:1px solid rgba(255,255,255,.12);display:grid!important;place-items:center;font-size:15px!important;transition:background .3s,transform .3s var(--ease),border-color .3s;margin:0!important}
.wtf-social .elementor-social-icon svg{fill:#fff!important;width:15px;height:15px}
.wtf-social .elementor-social-icon i{color:#fff!important}
.wtf-social .elementor-social-icon:hover{background:#7FD3A6!important;border-color:#7FD3A6;transform:translateY(-3px)}
.wtf-social .elementor-social-icon:hover svg{fill:#12241B!important}
.wtf-perks .elementor-icon-list-item{color:rgba(255,255,255,.75)!important;font:500 13px var(--sans)!important;padding:3px 0!important}
.wtf-perks .elementor-icon-list-icon svg,.wtf-perks .elementor-icon-list-icon i{fill:#7FD3A6!important;color:#7FD3A6!important;width:14px!important;font-size:14px!important}
.wtf-perks .elementor-icon-list-text{color:rgba(255,255,255,.75)!important}
.wtf-h .elementor-heading-title{font:700 14px/1 var(--sans)!important;color:#fff!important;letter-spacing:.06em;text-transform:uppercase;margin:0}
.wtf-links .elementor-icon-list-item{padding:6px 0!important}
.wtf-links .elementor-icon-list-icon{display:none!important}
.wtf-links .elementor-icon-list-text{position:relative;color:rgba(255,255,255,.66)!important;font:400 14.5px/1.4 var(--sans)!important;padding:0!important;transition:color .25s,padding .3s var(--ease)}
.wtf-links .elementor-icon-list-text:before{content:"";position:absolute;left:0;top:50%;width:0;height:1.5px;background:#7FD3A6;transition:width .3s var(--ease)}
.wtf-links a:hover .elementor-icon-list-text{color:#fff!important;padding-left:16px!important}
.wtf-links a:hover .elementor-icon-list-text:before{width:10px}
.wtf-news{background:rgba(255,255,255,.05)!important;border:1px solid rgba(255,255,255,.1);border-radius:22px!important;padding:26px!important;backdrop-filter:blur(6px);align-self:start}
.wtf-news__text,.wtf-news__text p{font:400 14px/1.6 var(--sans)!important;color:rgba(255,255,255,.66)!important;margin:0!important}
.wtf-form .elementor-form-fields-wrapper{display:grid!important;grid-template-columns:1fr;gap:10px}
.wtf-form .elementor-field-group{margin:0!important;padding:0!important;width:100%!important}
.wtf-form input.elementor-field{height:52px;background:#fff!important;border:0!important;border-radius:999px!important;padding:0 20px!important;font:500 14.5px var(--sans)!important;color:#1D211E!important}
.wtf-form input.elementor-field:focus{box-shadow:0 0 0 4px rgba(127,211,166,.35)!important;outline:0}
.wtf-form .elementor-button{height:50px;border-radius:999px!important;background:#7FD3A6!important;color:#12241B!important;font:700 15px var(--sans)!important;transition:background .3s,transform .2s!important}
.wtf-form .elementor-button:hover{background:#9ED9B8!important}.wtf-form .elementor-button:active{transform:scale(.98)}
.wtf-form .elementor-button-icon svg{fill:#12241B;width:13px}
.wtf-form .elementor-message{color:#9ED9B8!important;font:500 13px var(--sans);margin-top:8px}
.wtf-small,.wtf-small p{font:400 12px var(--sans)!important;color:rgba(255,255,255,.45)!important;margin:0!important}
.wtf-mark-wrap{position:relative;z-index:0;pointer-events:none;user-select:none}
.wtf-mark .elementor-heading-title{font:800 clamp(64px,15.5vw,230px)/.8 var(--sans)!important;letter-spacing:.06em;color:transparent!important;-webkit-text-stroke:1px rgba(255,255,255,.12);text-align:center;margin:10px 0 -.12em!important;white-space:nowrap;background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,0) 80%);-webkit-background-clip:text;background-clip:text}
.wtf-bottom{border-top:1px solid rgba(255,255,255,.1);position:relative;z-index:1;background:#12241B}
.wtf-copy .elementor-heading-title{font:400 13px var(--sans)!important;color:rgba(255,255,255,.55)!important;margin:0}
.wtf-bottom__r{width:auto!important;flex:none}
.wtf-legal .elementor-icon-list-items{display:flex!important;flex-wrap:wrap;gap:20px}
.wtf-legal .elementor-icon-list-item{margin:0!important;padding:0!important}
.wtf-legal .elementor-icon-list-item:after{display:none!important}
.wtf-legal .elementor-icon-list-icon{display:none!important}
.wtf-legal .elementor-icon-list-text{color:rgba(255,255,255,.55)!important;font:400 13px var(--sans)!important;padding:0!important;transition:color .25s}
.wtf-legal a:hover .elementor-icon-list-text{color:#fff!important}
.wtf-top .elementor-button{height:40px;padding:0 16px!important;border-radius:999px!important;background:rgba(255,255,255,.08)!important;border:1px solid rgba(255,255,255,.14)!important;color:#fff!important;font:600 13px var(--sans)!important;display:inline-flex!important;align-items:center;gap:8px;transition:background .3s,transform .3s var(--ease)!important}
.wtf-top .elementor-button:hover{background:#7FD3A6!important;color:#12241B!important;transform:translateY(-2px)}
.wtf-top .elementor-button-icon svg{width:12px;fill:currentColor}
@media (max-width:1100px){.wtf-main>.e-con-inner,.wtf-main{grid-template-columns:1fr 1fr 1fr}.wtf-brand,.wtf-news{grid-column:span 3}.wtf-news{max-width:none}}
@media (max-width:767px){.wtf{margin-top:90px}.wtf-cta{padding:28px 22px!important;border-radius:22px!important}.wtf-cta__btns,.wtf-btn,.wtf-btn .elementor-button{width:100%!important;justify-content:center}
 .wtf-main{padding-top:48px!important}.wtf-main>.e-con-inner,.wtf-main{grid-template-columns:1fr 1fr;gap:32px 20px}.wtf-brand,.wtf-news{grid-column:span 2}.wtf-col:nth-child(4){grid-column:span 2}
 .wtf-bottom{text-align:center}.wtf-bottom__r{flex-direction:column!important;gap:14px!important}.wtf-legal .elementor-icon-list-items{justify-content:center}}

CSS;
}


/* Default checkout country to UK, and WooCommerce toast notifications. */
add_filter( 'default_checkout_billing_country', function ( $c ) { return ( ! $c || 'US' === $c ) ? 'GB' : $c; } );
add_filter( 'default_checkout_shipping_country', function ( $c ) { return ( ! $c || 'US' === $c ) ? 'GB' : $c; } );

add_action( 'wp_footer', function () {
	if ( is_admin() ) { return; }
	?>
<script>
(function(){
var box,IC={success:'<path d="M5 12l5 5 9-10"/>',error:'<path d="M12 7v6M12 17h.01"/>',info:'<path d="M12 11v6M12 7h.01"/>'};
function el(){if(!box){box=document.createElement('div');box.className='wt-toasts';box.setAttribute('aria-live','polite');document.body.appendChild(box);}return box;}
function toast(type,title,msg,ms){
 ms=ms||4500;var t=document.createElement('div');t.className='wt-toast wt-toast--'+type;t.setAttribute('role',type==='error'?'alert':'status');
 t.innerHTML='<span class="wt-toast__ic"><svg viewBox="0 0 24 24">'+IC[type]+'</svg></span><div class="wt-toast__b"><div class="wt-toast__t"></div><div class="wt-toast__m"></div></div><button type="button" class="wt-toast__x" aria-label="Close">&times;</button><span class="wt-toast__bar" style="animation-duration:'+ms+'ms"></span>';
 t.querySelector('.wt-toast__t').textContent=title;if(msg){t.querySelector('.wt-toast__m').innerHTML=msg;}
 var b=el();b.appendChild(t);while(b.children.length>3){b.removeChild(b.firstChild);}
 requestAnimationFrame(function(){requestAnimationFrame(function(){t.classList.add('is-in');});});
 var tm,bar=t.querySelector('.wt-toast__bar');
 function close(){clearTimeout(tm);t.classList.add('is-out');setTimeout(function(){t.remove();},500);}
 tm=setTimeout(close,ms);t.querySelector('.wt-toast__x').onclick=close;
 t.onmouseenter=function(){clearTimeout(tm);bar.style.animationPlayState='paused';};
 t.onmouseleave=function(){bar.style.animationPlayState='running';tm=setTimeout(close,1800);};
}
window.wtToast=toast;
function clean(h){var d=document.createElement('div');d.innerHTML=h;d.querySelectorAll('a.button,a.wc-forward').forEach(function(a){a.remove();});return d.innerHTML.trim();}
function fromNotices(root,keep){
 (root||document).querySelectorAll('.woocommerce-message,.woocommerce-error,.woocommerce-info').forEach(function(n){
  if(n.dataset.wtToasted||n.closest('#payment,.woocommerce-form-coupon-toggle,.woocommerce-form-login-toggle,.wt-empty-cart,.woocommerce-MyAccount-content')){return;}
  n.dataset.wtToasted=1;var err=n.classList.contains('woocommerce-error'),txt;
  if(err){var li=n.querySelectorAll('li');txt=li.length>1?'Please fill in '+li.length+' required fields.':clean(li.length?li[0].innerHTML:n.innerHTML);}else{txt=clean(n.innerHTML);}
  if(!txt){return;}
  toast(err?'error':(n.classList.contains('woocommerce-info')?'info':'success'),err?'Please check your details':'Done',txt,err?6000:4500);
  if(err&&n.closest('form.checkout')){var f=document.querySelector('.woocommerce-invalid input,.woocommerce-invalid select');if(f){f.scrollIntoView({behavior:'smooth',block:'center'});setTimeout(function(){f.focus({preventScroll:true});},400);}}
  if(!keep&&!err&&n.classList.contains('woocommerce-message')){n.style.display='none';}
 });
}
document.addEventListener('DOMContentLoaded',function(){fromNotices(document,false);});
if(window.jQuery){var $=jQuery,$b=$(document.body);
 $b.on('added_to_cart',function(e,f,h,btn){var name=btn&&btn.closest&&btn.closest('.product,.wtpc__card,li');name=name&&name.find?name.find('.woocommerce-loop-product__title,h2,h3').first().text().trim():'';toast('success','Added to basket',(name?'<b>'+name.replace(/</g,'&lt;')+'</b> is in your basket. ':'')+'<a href="'+(window.wc_add_to_cart_params&&wc_add_to_cart_params.cart_url||'/cart/')+'">View basket</a>');});
 $b.on('removed_from_cart',function(){toast('info','Removed','Item removed from your basket.');});
 $b.on('updated_cart_totals',function(){fromNotices(document,false);});
 $b.on('applied_coupon applied_coupon_in_checkout removed_coupon removed_coupon_in_checkout',function(){setTimeout(function(){fromNotices(document,false);},50);});
 $b.on('checkout_error',function(){fromNotices(document,true);});
 $(document).ajaxComplete(function(){setTimeout(function(){fromNotices(document,false);},60);});
}
})();
</script>
	<?php
}, 30 );

add_action( 'wp_loaded', function () {
	if ( empty( $_POST['wt_ajax'] ) || empty( $_REQUEST['add-to-cart'] ) ) { return; }
	$errors = wc_get_notices( 'error' );
	wc_clear_notices();
	if ( $errors ) {
		wp_send_json( [ 'error' => wp_kses_post( implode( ' ', wp_list_pluck( $errors, 'notice' ) ) ) ] );
	}
	WC_AJAX::get_refreshed_fragments();
}, 21 );

add_action( 'wp_footer', function () {
	if ( is_admin() ) { return; }
	?>
<script>
(function(){
var d=document;
function deco(root){
 (root||d).querySelectorAll('.woocommerce ul.products li.product .button,.single_add_to_cart_button').forEach(function(b){b.classList.add('wt-fx');if(!b.querySelector('.wt-bi')){var i=d.createElement('span');i.className='wt-bi';i.setAttribute('aria-hidden','true');b.appendChild(i);}});
 var ul=(root||d).querySelector('ul.products');if(ul){[].forEach.call(ul.children,function(li,i){li.style.animationDelay=(Math.min(i,11)*60)+'ms';});}
 var bs=d.querySelectorAll('.wt-view button'),c='4';try{c=localStorage.getItem('wtCols')||'4';}catch(e){}
 function set(v){var u=d.querySelector('.woocommerce ul.products');if(u){u.classList.toggle('wt-c3',v==='3');}[].forEach.call(bs,function(b){b.classList.toggle('on',b.dataset.cols===v);b.setAttribute('aria-pressed',b.dataset.cols===v);});try{localStorage.setItem('wtCols',v);}catch(e){}}
 if(bs.length){set(c);[].forEach.call(bs,function(b){b.onclick=function(){set(b.dataset.cols);};});}
}
deco();
var bar;function prog(p){if(!bar){bar=d.createElement('div');bar.className='wt-loadbar';d.body.appendChild(bar);}bar.style.opacity=1;bar.style.width=p+'%';if(p>=100){setTimeout(function(){bar.style.opacity=0;setTimeout(function(){bar.style.width=0;},300);},250);}}
/* AJAX archive navigation */
var main=function(){return d.querySelector('.site-main')||d.querySelector('main');};
function isArchive(){return !!d.querySelector('.wt-archive-hero');}
function load(url,push){
 var m=main();if(!m){location.href=url;return;}
 d.body.classList.add('wt-ajaxing');prog(30);
 fetch(url,{credentials:'same-origin'}).then(function(r){return r.text();}).then(function(h){
  var doc=new DOMParser().parseFromString(h,'text/html'),nm=doc.querySelector('.site-main')||doc.querySelector('main');
  if(!nm||!doc.querySelector('.wt-archive-hero')){location.href=url;return;}
  prog(80);var hero=d.querySelector('.wt-archive-hero'),nh=doc.querySelector('.wt-archive-hero');
  if(hero&&nh&&!m.contains(hero)){hero.replaceWith(nh);}
  m.innerHTML=nm.innerHTML;d.title=doc.title;
  if(push){history.pushState({wt:1},'',url);}
  var top=(d.querySelector('.wt-archive-hero')||m).getBoundingClientRect().top+scrollY-20;if(scrollY>top){scrollTo({top:top,behavior:'smooth'});}
  deco();if(window.jQuery){jQuery(d.body).trigger('wt_ajax_loaded');}
 }).catch(function(){location.href=url;}).finally(function(){d.body.classList.remove('wt-ajaxing');prog(100);});
}
d.addEventListener('click',function(e){
 if(!isArchive()||e.metaKey||e.ctrlKey||e.shiftKey||e.button)return;
 var a=e.target.closest('.wt-chips a,.woocommerce-pagination a,.wt-crumbs a');if(!a||a.host!==location.host)return;
 if(/\/shop\/?$/.test(a.pathname)===false&&!a.closest('.wt-chips,.woocommerce-pagination')&&a.pathname==='/')return;
 e.preventDefault();d.querySelectorAll('.wt-chips a').forEach(function(x){x.classList.toggle('on',x===a);});load(a.href,true);
});
d.addEventListener('change',function(e){
 var s=e.target.closest('.woocommerce-ordering select');if(!s||!isArchive())return;
 e.stopImmediatePropagation();var f=s.form,u=new URL(f.action||location.href,location.href);new FormData(f).forEach(function(v,k){if(k!=='paged')u.searchParams.set(k,v);});u.pathname=u.pathname.replace(/\/page\/\d+\/?/,'/');load(u.toString(),true);
},true);
d.addEventListener('submit',function(e){if(e.target.closest('.woocommerce-ordering')&&isArchive()){e.preventDefault();}},true);
addEventListener('popstate',function(){if(isArchive()){load(location.href,false);}});
/* AJAX add to basket on product pages (all product types) */
d.addEventListener('submit',function(e){
 var f=e.target.closest('form.cart');if(e.submitter&&e.submitter.name==='wt_buy_now'){if(e.submitter.classList.contains('is-loading'))e.preventDefault();e.submitter.classList.add('is-loading');return;}if(!f||!window.fetch||f.closest('.grouped_form')&&false)return;
 var btn=f.querySelector('.single_add_to_cart_button');if(!btn||btn.classList.contains('disabled'))return;
 e.preventDefault();var fd=new FormData(f);if(btn.name&&btn.value){fd.set(btn.name,btn.value);}if(!fd.get('add-to-cart')){fd.set('add-to-cart',btn.value||f.querySelector('[name=product_id]')&&f.querySelector('[name=product_id]').value||'');}
 fd.set('wt_ajax','1');btn.classList.remove('added');btn.classList.add('loading');prog(40);
 fetch(location.href,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(j){
  btn.classList.remove('loading');
  if(j.error){window.wtToast&&wtToast('error','Could not add',j.error,6000);return;}
  btn.classList.add('added');setTimeout(function(){btn.classList.remove('added');},2600);
  if(window.jQuery){jQuery(d.body).trigger('added_to_cart',[j.fragments,j.cart_hash,jQuery()]);if(j.fragments){jQuery.each(j.fragments,function(k,v){jQuery(k).replaceWith(v);});}}
 }).catch(function(){btn.classList.remove('loading');f.submit();}).finally(function(){prog(100);});
});
})();
</script>
	<?php
}, 30 );

add_action( 'wp_footer', function () {
	?>
<script>
(function(){
document.querySelectorAll('.wt-rv2 .wt-rv2__row').forEach(function(row){
 var cards=row.querySelectorAll('.wt-rv2__card');if(cards.length<2)return;
 var dots=document.createElement('div');dots.className='wt-rv2-dots';row.parentNode.insertBefore(dots,row.nextSibling);
 cards.forEach(function(c,i){var b=document.createElement('button');b.type='button';b.setAttribute('aria-label','Review '+(i+1));b.onclick=function(){row.scrollTo({left:c.offsetLeft-row.offsetLeft,behavior:'smooth'});};dots.appendChild(b);});
 function upd(){var x=row.scrollLeft,best=0,bd=1e9;cards.forEach(function(c,i){var dd=Math.abs(c.offsetLeft-row.offsetLeft-x);if(dd<bd){bd=dd;best=i;}});if(row.scrollLeft+row.clientWidth>=row.scrollWidth-4)best=cards.length-1;[].forEach.call(dots.children,function(b,i){b.classList.toggle('on',i===best);});}
 row.addEventListener('scroll',upd,{passive:true});upd();
});
})();
</script>
	<?php
}, 30 );

add_action( 'wp_footer', function () {
	$cta = home_url( '/get-a-free-design/' );
	$acc = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
	$bag = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
	?>
<div class="wtm-ov" data-wtm-close></div>
<aside class="wtm" id="wtm" role="dialog" aria-modal="true" aria-label="Menu" aria-hidden="true">
	<div class="wtm__top"><span class="wtm__logo">WASHTOWER&reg;</span><button type="button" class="wtm__x" data-wtm-close aria-label="Close menu"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg></button></div>
	<div class="wtm__body"><ul class="wtm__nav"></ul>
		<div class="wtm__quick"><a href="<?php echo esc_url( $acc ); ?>"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>My account</a><a href="<?php echo esc_url( $bag ); ?>"><svg viewBox="0 0 24 24"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>Basket</a></div>
	</div>
	<div class="wtm__foot"><a class="wtm__cta" href="<?php echo esc_url( $cta ); ?>">Get a Free Design<svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><p class="wtm__note">Free delivery on all cabinet orders</p></div>
</aside>
<script>
(function(){
var d=document,P=d.getElementById('wtm');if(!P)return;var nav=P.querySelector('.wtm__nav'),src=d.querySelector('.wth-nav .elementor-nav-menu--main>ul')||d.querySelector('.wth-nav ul.elementor-nav-menu');
var CH='<svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>';
function build(ul,into){[].forEach.call(ul.children,function(li){var a=li.querySelector(':scope>a');if(!a)return;var n=d.createElement('li'),row=d.createElement('div');row.className='wtm__row';var l=d.createElement('a');l.href=a.href;l.textContent=a.textContent.trim();row.appendChild(l);n.appendChild(row);
 var sub=li.querySelector(':scope>ul');if(sub){var b=d.createElement('button');b.type='button';b.className='wtm__sub-t';b.setAttribute('aria-label','Show '+l.textContent+' links');b.setAttribute('aria-expanded','false');b.innerHTML=CH;row.appendChild(b);
  var su=d.createElement('div');su.className='sub-menu';var inn=d.createElement('ul');inn.className='wtm__in';build(sub,inn);su.appendChild(inn);n.appendChild(su);
  b.onclick=function(){var o=n.classList.toggle('is-open');b.setAttribute('aria-expanded',o);};}
 into.appendChild(n);});}
if(src)build(src,nav);
[].forEach.call(nav.children,function(li,i){li.style.transitionDelay=(80+i*50)+'ms';});
var last;function open(){last=d.activeElement;d.body.classList.add('wtm-open');P.setAttribute('aria-hidden','false');setTimeout(function(){P.querySelector('.wtm__x').focus();},50);}
function close(){d.body.classList.remove('wtm-open');P.setAttribute('aria-hidden','true');if(last&&last.focus)last.focus();}
d.addEventListener('click',function(e){var t=e.target.closest('.wth-nav .elementor-menu-toggle');if(t){e.preventDefault();e.stopImmediatePropagation();open();return;}if(e.target.closest('[data-wtm-close]'))close();},true);
d.addEventListener('keydown',function(e){if(e.key==='Escape'&&d.body.classList.contains('wtm-open'))close();});
})();
</script>
	<?php
}, 30 );

/* Shown under the reviews loop carousel only while no testimonials are published. */
add_shortcode( 'wt_reviews_empty', function () {
	if ( wp_count_posts( 'wt_testimonial' )->publish > 0 ) { return ''; }
	$o = '<p class="wt-reviews-empty">Be the first to <a href="' . esc_url( wt_page_url( 'Customer Reviews' ) ) . '">share your review</a>.</p>';
	if ( current_user_can( 'edit_posts' ) ) {
		$o .= '<p class="wt-admin-note">Editor-only note: add reviews in WP Admin &rarr; <a href="' . esc_url( admin_url( 'post-new.php?post_type=wt_testimonial' ) ) . '">Testimonials</a>. They appear in this slider automatically.</p>';
	}
	return $o;
} );

add_action( 'wp_footer', function () {
	?>
<script>
(function(){
function init(root){
 if(root.dataset.ready)return;root.dataset.ready=1;
 var view=root.querySelector('.wt-rs__view'),track=root.querySelector('.wt-rs__track'),slides=track.children,prev=root.querySelector('.wt-rs__prev'),next=root.querySelector('.wt-rs__next'),dots=root.querySelector('.wt-rs__dots'),idx=0,timer;
 function per(){return Math.max(1,Math.round(parseFloat(getComputedStyle(root).getPropertyValue('--per'))||1));}
 function max(){return Math.max(0,slides.length-per());}
 function go(i,user){idx=Math.max(0,Math.min(max(),i));var s=slides[0];if(!s)return;var gap=parseFloat(getComputedStyle(track).columnGap)||0;track.style.transform='translateX('+(-idx*(s.getBoundingClientRect().width+gap))+'px)';prev.disabled=idx===0;next.disabled=idx>=max();[].forEach.call(dots.children,function(b,k){b.classList.toggle('on',k===idx);b.setAttribute('aria-current',k===idx);});if(user)stop();}
 function build(){dots.innerHTML='';for(var k=0;k<=max();k++){(function(k){var b=document.createElement('button');b.type='button';b.setAttribute('aria-label','Go to review '+(k+1));b.onclick=function(){go(k,true);};dots.appendChild(b);})(k);}root.classList.toggle('is-static',max()===0);go(idx);}
 prev.onclick=function(){go(idx-1,true);};next.onclick=function(){go(idx+1,true);};
 view.addEventListener('keydown',function(e){if(e.key==='ArrowLeft'){go(idx-1,true);}if(e.key==='ArrowRight'){go(idx+1,true);}});
 var x0=null,dx=0;view.addEventListener('pointerdown',function(e){x0=e.clientX;dx=0;track.style.transition='none';});
 addEventListener('pointermove',function(e){if(x0===null)return;dx=e.clientX-x0;});
 addEventListener('pointerup',function(){if(x0===null)return;track.style.transition='';if(Math.abs(dx)>40)go(idx+(dx<0?1:-1),true);x0=null;});
 view.addEventListener('touchstart',function(e){x0=e.touches[0].clientX;dx=0;},{passive:true});
 view.addEventListener('touchmove',function(e){if(x0!==null)dx=e.touches[0].clientX-x0;},{passive:true});
 view.addEventListener('touchend',function(){if(x0!==null&&Math.abs(dx)>40)go(idx+(dx<0?1:-1),true);x0=null;});
 function stop(){clearInterval(timer);timer=null;}
 function start(){if(root.dataset.autoplay!=='1'||matchMedia('(prefers-reduced-motion:reduce)').matches)return;stop();timer=setInterval(function(){go(idx>=max()?0:idx+1);},6000);}
 root.addEventListener('mouseenter',stop);root.addEventListener('mouseleave',start);root.addEventListener('focusin',stop);
 addEventListener('resize',build);build();start();
}
window.wtRsInit=init;document.querySelectorAll('.wt-rs').forEach(init);
})();
</script>
	<?php
}, 30 );

add_action( 'wp_footer', function () {
	?>
<script>
(function(){
var els=document.querySelectorAll('.wtp-a');if(els.length){
 if(!('IntersectionObserver' in window)){els.forEach(function(e){e.classList.add('is-in');});}
 else{var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('is-in');io.unobserve(x.target);}});},{rootMargin:'0px 0px -8% 0px',threshold:.08});els.forEach(function(e){io.observe(e);});}
}
var q=document.querySelector('[data-wtp-search]');if(q){q.addEventListener('input',function(){var v=q.value.trim().toLowerCase();
 document.querySelectorAll('.wtp-faq .elementor-toggle-item').forEach(function(it){it.classList.toggle('wtp-hide',!!v&&it.textContent.toLowerCase().indexOf(v)<0);});
 document.querySelectorAll('.wtp-faq').forEach(function(f){var box=f.closest('.e-con');var any=f.querySelector('.elementor-toggle-item:not(.wtp-hide)');if(box)box.classList.toggle('wtp-hide',!any);});
});}
})();
</script>
	<?php
}, 30 );

add_action( 'wp_footer', function () {
	?>
<script>
(function(){
var d=document;
d.querySelectorAll('.wtp-faq .elementor-tab-content').forEach(function(c){c.classList.add('wtp-sm');c.removeAttribute('hidden');c.style.display='';});
function setOpen(t,open){var c=d.getElementById(t.getAttribute('aria-controls'))||t.nextElementSibling;if(!c)return;
 t.classList.toggle('elementor-active',open);t.setAttribute('aria-expanded',open);c.classList.toggle('elementor-active',open);
 var start=c.getBoundingClientRect().height;
 if(open){c.style.transition='none';c.classList.add('is-open');c.style.height='auto';var end=c.getBoundingClientRect().height;c.classList.remove('is-open');c.style.height=start+'px';void c.offsetHeight;c.style.transition='';c.classList.add('is-open');c.style.height=end+'px';}
 else{c.style.height=start+'px';void c.offsetHeight;c.classList.remove('is-open');c.style.height='0px';}
 c.addEventListener('transitionend',function f(e){if(e.propertyName!=='height')return;c.removeEventListener('transitionend',f);if(c.classList.contains('is-open'))c.style.height='auto';});
}
d.addEventListener('click',function(e){var t=e.target.closest('.wtp-faq .elementor-tab-title');if(!t)return;e.preventDefault();e.stopImmediatePropagation();setOpen(t,!t.classList.contains('elementor-active'));},true);
d.addEventListener('keydown',function(e){var t=e.target.closest&&e.target.closest('.wtp-faq .elementor-tab-title');if(t&&(e.key==='Enter'||e.key===' ')){e.preventDefault();e.stopImmediatePropagation();setOpen(t,!t.classList.contains('elementor-active'));}},true);
})();
</script>
	<?php
}, 31 );

/* FAQ search box (used on the FAQs page via the Shortcode widget). */
add_shortcode( 'wt_faq_search', function () {
	return '<div class="wtp-search wtp-a"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg><input type="search" placeholder="Search questions..." aria-label="Search questions" data-wtp-search></div>';
} );

add_action( 'wp_footer', function () {
	?>
<script>
document.addEventListener('click',function(e){var a=e.target.closest('.wtf-top a,.wtf-top .elementor-button');if(!a)return;e.preventDefault();window.scrollTo({top:0,behavior:'smooth'});});
</script>
	<?php
}, 40 );
