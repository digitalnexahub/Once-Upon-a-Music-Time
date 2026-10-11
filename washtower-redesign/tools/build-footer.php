<?php
/* One-off: rebuild the "WASHTOWER Footer" Theme Builder template with native Elementor widgets. */
$tpl = get_posts( [ 'post_type' => 'elementor_library', 'title' => 'WASHTOWER Footer', 'numberposts' => 1, 'post_status' => 'any' ] );
if ( ! $tpl ) { return 'footer template not found'; }
$fid = $tpl[0]->ID;
update_post_meta( $fid, '_wt_backup_footer_' . time(), wp_slash( get_post_meta( $fid, '_elementor_data', true ) ) );

$id  = function () { return substr( md5( uniqid( '', true ) ), 0, 7 ); };
$z   = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ];
$w   = function ( $t, $s, $c = '' ) use ( $id ) { if ( $c ) { $s['_css_classes'] = $c; } return [ 'id' => $id(), 'elType' => 'widget', 'widgetType' => $t, 'settings' => $s, 'elements' => [] ]; };
$c   = function ( $s, $els, $cl = '', $in = true ) use ( $id, $z ) { $s += [ 'content_width' => 'full', 'padding' => $z ]; if ( $cl ) { $s['css_classes'] = $cl; } return [ 'id' => $id(), 'elType' => 'container', 'isInner' => $in, 'settings' => $s, 'elements' => $els ]; };
$g   = function ( $n ) { return [ 'size' => $n, 'unit' => 'px', 'column' => (string) $n, 'row' => (string) $n ]; };
$hd  = function ( $t, $tag, $cl, $extra = [] ) use ( $w ) { return $w( 'heading', [ 'title' => $t, 'header_size' => $tag ] + $extra, $cl ); };
$tx  = function ( $t, $cl ) use ( $w ) { return $w( 'text-editor', [ 'editor' => '<p>' . $t . '</p>' ], $cl ); };
$fa  = function ( $v, $lib = 'fa-solid' ) { return [ 'value' => $v, 'library' => $lib ]; };
$links = function ( $arr ) use ( $w, $id ) {
	$items = [];
	foreach ( $arr as $label => $url ) { $items[] = [ '_id' => $id(), 'text' => $label, 'link' => [ 'url' => $url ], 'selected_icon' => [ 'value' => '', 'library' => '' ] ]; }
	return $w( 'icon-list', [ 'icon_list' => $items ], 'wtf-links' );
};
$shop = wc_get_page_permalink( 'shop' );
$cols = [
	'Shop'  => [ 'All Cabinets' => $shop, 'Laundry Cabinets' => wt_cat_url( 'laundry-cabinets' ), 'Stackable Units' => wt_cat_url( 'stackable-washer-dryer' ), 'Storage Cabinets' => wt_cat_url( 'storage-cabinets' ), 'Accessories' => wt_cat_url( 'accessories' ) ],
	'Help'  => [ 'Design Your Own' => wt_page_url( 'Design Your Own' ), 'Delivery Information' => wt_page_url( 'Delivery Information' ), 'Returns & Warranty' => wt_page_url( 'Returns & Warranty' ), 'Installation Guide' => wt_page_url( 'Installation Guide' ), 'FAQs' => wt_page_url( 'FAQs' ) ],
	'About' => [ 'Our Story' => wt_page_url( 'Our Story' ), 'Sustainability' => wt_page_url( 'Sustainability' ), 'Customer Reviews' => wt_page_url( 'Customer Reviews' ), 'Blog' => get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ), 'Contact Us' => wt_page_url( 'Contact Us' ) ],
];

/* 1. CTA card */
$cta = $c( [ 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1240 ], 'flex_direction' => 'column', 'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '24', 'bottom' => '0', 'left' => '24', 'isLinked' => false ] ], [
	$c( [ 'flex_direction' => 'row', 'flex_direction_mobile' => 'column', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $g( 28 ) ], [
		$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 10 ) ], [
			$hd( 'Free design service', 'p', 'wtf-cta__eyebrow' ),
			$hd( 'Let&rsquo;s design your perfect laundry room', 'h2', 'wtf-cta__title' ),
			$tx( 'Send us your measurements and we&rsquo;ll suggest the ideal cabinet layout &ndash; free, with no obligation.', 'wtf-cta__text' ),
		], 'wtf-cta__copy' ),
		$c( [ 'flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_gap' => $g( 12 ) ], [
			$w( 'button', [ 'text' => 'Get a Free Design', 'link' => [ 'url' => wt_page_url( 'Get a Free Design' ) ], 'selected_icon' => $fa( 'fas fa-arrow-right' ), 'icon_align' => 'right' ], 'wtf-btn wtf-btn--light' ),
			$w( 'button', [ 'text' => 'Shop cabinets', 'link' => [ 'url' => $shop ] ], 'wtf-btn wtf-btn--ghost' ),
		], 'wtf-cta__btns' ),
	], 'wtf-cta wtp-a' ),
], 'wtf-cta-wrap', false );

/* 2. Main */
$brand = $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 18 ) ], [
	$hd( 'WASHTOWER<sup>&reg;</sup>', 'p', 'wtf-logo', [ 'link' => [ 'url' => home_url( '/' ) ] ] ),
	$tx( 'Beautifully designed laundry cabinets for modern homes. Customisable, practical and built to last.', 'wtf-about' ),
	$w( 'social-icons', [ 'social_icon_list' => [
		[ '_id' => $id(), 'social_icon' => $fa( 'fab fa-instagram', 'fa-brands' ), 'link' => [ 'url' => 'https://www.instagram.com/', 'is_external' => 'on' ] ],
		[ '_id' => $id(), 'social_icon' => $fa( 'fab fa-facebook-f', 'fa-brands' ), 'link' => [ 'url' => 'https://www.facebook.com/', 'is_external' => 'on' ] ],
		[ '_id' => $id(), 'social_icon' => $fa( 'fab fa-pinterest-p', 'fa-brands' ), 'link' => [ 'url' => 'https://www.pinterest.com/', 'is_external' => 'on' ] ],
		[ '_id' => $id(), 'social_icon' => $fa( 'fab fa-youtube', 'fa-brands' ), 'link' => [ 'url' => 'https://www.youtube.com/', 'is_external' => 'on' ] ],
	], 'shape' => 'circle', 'align' => 'left' ], 'wtf-social' ),
	$w( 'icon-list', [ 'icon_list' => [
		[ '_id' => $id(), 'text' => 'Free UK delivery', 'selected_icon' => $fa( 'fas fa-truck' ) ],
		[ '_id' => $id(), 'text' => '10 year guarantee', 'selected_icon' => $fa( 'fas fa-shield-alt' ) ],
	] ], 'wtf-perks' ),
], 'wtf-brand' );
$linkcols = [];
foreach ( $cols as $h => $arr ) { $linkcols[] = $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 16 ) ], [ $hd( $h, 'h3', 'wtf-h' ), $links( $arr ) ], 'wtf-col' ); }
$news = $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 12 ) ], [
	$hd( 'Join our mailing list', 'h3', 'wtf-h' ),
	$tx( 'Inspiration, new products and exclusive offers &ndash; straight to your inbox.', 'wtf-news__text' ),
	$w( 'form', [
		'form_name' => 'Newsletter',
		'form_fields' => [ [ '_id' => $id(), 'custom_id' => 'email', 'field_type' => 'email', 'field_label' => 'Email address', 'placeholder' => 'Your email address', 'required' => 'true', 'width' => '100' ] ],
		'show_labels' => '', 'button_text' => 'Subscribe', 'selected_button_icon' => $fa( 'fas fa-arrow-right' ), 'button_icon_align' => 'right', 'button_width' => '100',
		'submit_actions' => [ 'email' ], 'email_to' => get_option( 'admin_email' ), 'email_subject' => 'New newsletter sign-up', 'email_content' => '[all-fields]',
		'success_message' => 'Thanks for subscribing!', 'error_message' => 'Something went wrong, please try again.', 'required_field_message' => 'Please enter your email.', 'invalid_message' => 'Please enter a valid email address.',
	], 'wtf-form' ),
	$tx( 'No spam. Unsubscribe any time.', 'wtf-small' ),
], 'wtf-news' );
$main = $c( [ 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1240 ], 'flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_gap' => $g( 40 ), 'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '24', 'bottom' => '0', 'left' => '24', 'isLinked' => false ] ], array_merge( [ $brand ], $linkcols, [ $news ] ), 'wtf-main', false );

/* 3. Wordmark + bottom bar */
$mark = $c( [ 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1240 ], 'flex_direction' => 'column', 'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '24', 'bottom' => '0', 'left' => '24', 'isLinked' => false ] ], [ $hd( 'WASHTOWER', 'p', 'wtf-mark' ) ], 'wtf-mark-wrap', false );
$legal = [];
foreach ( [ 'Privacy Policy' => get_privacy_policy_url() ?: wt_page_url( 'Privacy Policy' ), 'Terms & Conditions' => wt_page_url( 'Terms & Conditions' ), 'Cookies' => wt_page_url( 'Cookies' ) ] as $l => $u ) { $legal[] = [ '_id' => $id(), 'text' => $l, 'link' => [ 'url' => $u ], 'selected_icon' => [ 'value' => '', 'library' => '' ] ]; }
$year = '[elementor-tag id="' . $id() . '" name="current-date-time" settings="' . urlencode( wp_json_encode( [ 'date_format' => 'custom', 'time_format' => '', 'custom_format' => 'Y', 'before' => '&copy; ', 'after' => ' WASHTOWER&reg;. All rights reserved.' ] ) ) . '"]';
$bottom = $c( [ 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1240 ], 'flex_direction' => 'row', 'flex_direction_mobile' => 'column', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $g( 16 ), 'padding' => [ 'unit' => 'px', 'top' => '22', 'right' => '24', 'bottom' => '26', 'left' => '24', 'isLinked' => false ] ], [
	$w( 'heading', [ 'title' => '&copy; ' . gmdate( 'Y' ) . ' WASHTOWER&reg;. All rights reserved.', 'header_size' => 'p', '__dynamic__' => [ 'title' => $year ] ], 'wtf-copy' ),
	$c( [ 'flex_direction' => 'row', 'flex_align_items' => 'center', 'flex_gap' => $g( 18 ), 'width' => [ 'unit' => '%', 'size' => '' ] ], [
		$w( 'icon-list', [ 'view' => 'inline', 'icon_list' => $legal ], 'wtf-legal' ),
		$w( 'button', [ 'text' => 'Back to top', 'link' => [ 'url' => '#top' ], 'selected_icon' => $fa( 'fas fa-arrow-up' ), 'icon_align' => 'right' ], 'wtf-top' ),
	], 'wtf-bottom__r' ),
], 'wtf-bottom', false );

$root = $c( [ '_title' => 'Footer', 'flex_direction' => 'column', 'flex_gap' => $g( 0 ) ], [ $cta, $main, $mark, $bottom ], 'wtf', false );
update_post_meta( $fid, '_elementor_data', wp_slash( wp_json_encode( [ $root ] ) ) );
update_post_meta( $fid, '_elementor_edit_mode', 'builder' );
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return $fid;
