<?php
/**
 * Plugin Name: Washtower Design Layer
 * Description: Header, footer, homepage components (product carousel, reviews, blog cards, hotspots, newsletter), animations and shop styling for the WASHTOWER homepage recreation.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------------------------------------------------------------- assets */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'wt-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Caveat:wght@500&family=Inter:wght@400;500;600&display=swap', [], null );
	wp_register_style( 'wt-design', false );
	wp_enqueue_style( 'wt-design' );
	wp_add_inline_style( 'wt-design', wt_design_css() );
	wp_register_script( 'wt-design', false, [], null, true );
	wp_enqueue_script( 'wt-design' );
	wp_localize_script( 'wt-design', 'WT', [
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'wt_newsletter' ),
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
	$reviews  = get_comments( [ 'type' => 'review', 'status' => 'approve', 'number' => 3, 'meta_key' => 'rating', 'orderby' => 'comment_date', 'order' => 'DESC' ] );
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
	$o .= '<div class="wt-reviews wt-reveal-group">';
	foreach ( $cards as $i => $c ) {
		$av = $c[4] ? '<img src="' . esc_url( $c[4] ) . '" alt="" width="36" height="36" loading="lazy">' : '<span class="wt-av" aria-hidden="true">' . esc_html( mb_substr( $c[2], 0, 1 ) ) . '</span>';
		$o .= '<figure class="wt-rcard"><div class="wt-rcard__t">' . wt_stars( $c[0] ) . '<blockquote>“' . esc_html( wp_trim_words( $c[1], 24 ) ) . '”</blockquote><figcaption>' . $av . '<span><b>' . esc_html( $c[2] ) . '</b><small>' . esc_html( $c[3] ) . '</small></span></figcaption></div>' . wp_get_attachment_image( $photos[ $i % 3 ], 'medium', false, [ 'class' => 'wt-rcard__img', 'loading' => 'lazy', 'alt' => '' ] ) . '</figure>';
	}
	return $o . '</div>';
} );

// Interactive hotspots on the "Design your own" image.
add_shortcode( 'wt_hotspots', function ( $a ) {
	$a    = shortcode_atts( [ 'image' => 59 ], $a );
	$spot = [ [ 'Tall storage', 'Full-height cupboards for linen, ironing boards and cleaning kit.', 26, 9 ], [ 'Integrated appliances', 'Washer and dryer raised to a comfortable working height.', 74, 16 ], [ 'Pull out baskets', 'Slide-out baskets make loading and sorting effortless.', 28, 48 ], [ 'Soft close drawers', 'Quiet, cushioned drawers for detergents and accessories.', 70, 60 ] ];
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
if(!reduce&&'IntersectionObserver' in window){
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
d.querySelectorAll('[data-carousel]').forEach(function(c){
 var tr=c.querySelector('.wt-car-track'),line=c.querySelector('.wt-car-line'),bar=line&&line.querySelector('span'),prev=c.querySelector('.prev'),next=c.querySelector('.next'),n=tr.children.length,timer=null,paused=false,drag=null,moved=false;
 function gap(){return parseFloat(getComputedStyle(tr).columnGap)||0;}
 function step(){return n?tr.children[0].getBoundingClientRect().width+gap():tr.clientWidth;}
 function per(){return Math.max(1,Math.round((tr.clientWidth+gap())/step()));}
 function max(){return Math.max(0,n-per());}
 function idx(){return Math.round(tr.scrollLeft/step());}
 function go(i){i=Math.max(0,Math.min(max(),i));tr.scrollTo({left:i*step(),behavior:reduce?'auto':'smooth'});}
 function sync(){var sw=tr.scrollWidth,cw=tr.clientWidth,f=sw>cw?cw/sw:1,pos=sw>cw?tr.scrollLeft/(sw-cw):0;if(bar){bar.style.width=(f*100)+'%';bar.style.transform='translateX('+(pos*(1/f-1)*100)+'%)';}prev.disabled=tr.scrollLeft<4;next.disabled=tr.scrollLeft>=sw-cw-4;c.classList.toggle('no-nav',max()<1);}
 prev.addEventListener('click',function(){go(idx()-1);restart();});
 next.addEventListener('click',function(){go(idx()+1);restart();});
 if(line)line.addEventListener('click',function(e){var r=line.getBoundingClientRect();go(Math.round((e.clientX-r.left)/r.width*max()));restart();});
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
:root{--g:#1D4032;--g2:#19392D;--g3:#153528;--bg:#FFFFFF;--off:#F8F7F3;--sage:#F1F4EE;--beige:#F2EEE7;--tx:#1D211E;--tx2:#666B66;--ln:#E5E8E2;--star:#E5AC32;--serif:'Cormorant Garamond',Georgia,serif;--sans:'Inter',system-ui,-apple-system,sans-serif;--ease:cubic-bezier(.22,1,.36,1);--r:10px}
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
.wt-reviews{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
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
@media (max-width:1200px){.wt-header__in{gap:28px}.wt-nav ul{gap:22px}.wt-car-btn.prev{left:-12px}.wt-car-btn.next{right:-12px}.wt-blog,.wt-reviews{grid-template-columns:1fr 1fr}.wt-footer__grid{grid-template-columns:1fr 1fr 1fr}.wt-footer__brand,.wt-footer__news{grid-column:span 3}}
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
 .wt-reviews,.wt-blog{grid-template-columns:1fr}
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
CSS;
}
