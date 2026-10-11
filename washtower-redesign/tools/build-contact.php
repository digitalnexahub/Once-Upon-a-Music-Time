<?php
/* One-off builder: creates the Elementor layout for the Contact us page (id from slug contact-us). */
$page = get_page_by_path( 'contact-us' );
if ( ! $page ) { return 'no page'; }
$pid = $page->ID;
$id  = function () { return substr( md5( uniqid( '', true ) ), 0, 7 ); };
$z   = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ];
$w   = function ( $t, $s, $c = '' ) use ( $id ) { if ( $c ) { $s['_css_classes'] = $c; } return [ 'id' => $id(), 'elType' => 'widget', 'widgetType' => $t, 'settings' => $s, 'elements' => [] ]; };
$c   = function ( $s, $els, $cl = '', $in = true ) use ( $id, $z ) { $s += [ 'content_width' => 'full', 'padding' => $z ]; if ( $cl ) { $s['css_classes'] = $cl; } return [ 'id' => $id(), 'elType' => 'container', 'isInner' => $in, 'settings' => $s, 'elements' => $els ]; };
$g   = function ( $n ) { return [ 'size' => $n, 'unit' => 'px', 'column' => (string) $n, 'row' => (string) $n ]; };
$pad = function ( $t, $r, $b, $l ) { return [ 'unit' => 'px', 'top' => "$t", 'right' => "$r", 'bottom' => "$b", 'left' => "$l", 'isLinked' => false ]; };
$h   = function ( $t, $tag, $size, $sm, $col = '#1D211E', $wt = '700' ) use ( $w ) {
	return $w( 'heading', [ 'title' => $t, 'header_size' => $tag, 'title_color' => $col, 'typography_typography' => 'custom', 'typography_font_family' => 'Barlow', 'typography_font_size' => [ 'unit' => 'px', 'size' => $size ], 'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => $sm ], 'typography_font_weight' => $wt, 'typography_line_height' => [ 'unit' => 'em', 'size' => 1.1 ] ] );
};
$txt = function ( $html, $col = '#666B66', $size = 17 ) use ( $w ) {
	return $w( 'text-editor', [ 'editor' => $html, 'text_color' => $col, 'typography_typography' => 'custom', 'typography_font_family' => 'Barlow', 'typography_font_size' => [ 'unit' => 'px', 'size' => $size ], 'typography_line_height' => [ 'unit' => 'em', 'size' => 1.6 ] ] );
};
$ib = function ( $icon, $title, $desc, $link = '' ) use ( $w ) {
	$s = [ 'selected_icon' => [ 'value' => $icon, 'library' => 'fa-solid' ], 'title_text' => $title, 'description_text' => $desc, 'position' => 'left', 'title_size' => 'h3' ];
	if ( $link ) { $s['link'] = [ 'url' => $link ]; }
	return $w( 'icon-box', $s, 'wtc-info' );
};
$design = function_exists( 'wt_page_url' ) ? wt_page_url( 'Get a Free Design' ) : home_url( '/get-a-free-design/' );
$faq    = function_exists( 'wt_page_url' ) ? wt_page_url( 'FAQs' ) : home_url( '/faqs/' );

$hero = $c( [ '_title' => 'Contact Hero', 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1280 ], 'flex_direction' => 'column', 'flex_gap' => $g( 18 ), 'padding' => $pad( 100, 24, 170, 24 ), 'padding_mobile' => $pad( 64, 16, 110, 16 ) ], [
	$w( 'heading', [ 'title' => 'Contact us', 'header_size' => 'p' ], 'wtc-eyebrow' ),
	$h( 'Let&rsquo;s plan your perfect laundry room', 'h1', 60, 36, '#FFFFFF' ),
	$txt( '<p>Questions about sizes, finishes, delivery or your order? Our team is happy to help &ndash; send us a message and we&rsquo;ll get back to you.</p>', 'rgba(255,255,255,.78)', 19 ),
	$w( 'icon-list', [ 'view' => 'inline', 'icon_list' => [ [ '_id' => $id(), 'text' => 'Free design advice', 'selected_icon' => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ] ], [ '_id' => $id(), 'text' => 'Free UK delivery', 'selected_icon' => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ] ], [ '_id' => $id(), 'text' => '10 year guarantee', 'selected_icon' => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ] ] ] ], 'wtc-chips-n' ),
], 'wtc-hero', false );

$info = $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 6 ), 'width' => [ 'unit' => '%', 'size' => 40 ], 'width_tablet' => [ 'unit' => '%', 'size' => 100 ], 'padding' => $pad( 30, 22, 30, 22 ) ], [
	$h( 'Get in touch', 'h2', 28, 24 ),
	$txt( '<p>Choose what suits you best. Update these details in Elementor.</p>', '#666B66', 15 ),
	$ib( 'fas fa-envelope', 'Email us', 'Add your email address here' ),
	$ib( 'fas fa-phone', 'Call us', 'Add your phone number here' ),
	$ib( 'fas fa-clock', 'Opening hours', 'Mon&ndash;Fri, add your hours here' ),
	$ib( 'fas fa-map-marker-alt', 'Showroom', 'Add your address here' ),
	$ib( 'fas fa-question-circle', 'Quick answers', 'Read our FAQs about sizes, delivery and returns', $faq ),
], 'wtc-card' );

$form = $w( 'form', [
	'form_name'   => 'Contact form',
	'form_fields' => [
		[ '_id' => $id(), 'custom_id' => 'name', 'field_type' => 'text', 'field_label' => 'Your name', 'placeholder' => 'Jane Smith', 'required' => 'true', 'width' => '50' ],
		[ '_id' => $id(), 'custom_id' => 'email', 'field_type' => 'email', 'field_label' => 'Email', 'placeholder' => 'you@example.com', 'required' => 'true', 'width' => '50' ],
		[ '_id' => $id(), 'custom_id' => 'phone', 'field_type' => 'tel', 'field_label' => 'Phone (optional)', 'placeholder' => '', 'width' => '50' ],
		[ '_id' => $id(), 'custom_id' => 'topic', 'field_type' => 'select', 'field_label' => 'Topic', 'field_options' => "Design advice\nProduct question\nMy order\nDelivery\nReturns & warranty\nSomething else", 'width' => '50' ],
		[ '_id' => $id(), 'custom_id' => 'message', 'field_type' => 'textarea', 'field_label' => 'Message', 'placeholder' => 'Tell us about your space or question', 'required' => 'true', 'rows' => 5, 'width' => '100' ],
		[ '_id' => $id(), 'custom_id' => 'consent', 'field_type' => 'acceptance', 'field_label' => '', 'acceptance_text' => 'I agree that my details are used to answer my question.', 'required' => 'true', 'width' => '100' ],
	],
	'input_size' => 'md', 'show_labels' => 'yes', 'mark_required' => 'yes',
	'button_text' => 'Send message', 'button_size' => 'md', 'button_width' => '100',
	'submit_actions' => [ 'email' ], 'email_to' => get_option( 'admin_email' ), 'email_subject' => 'New message from ' . get_bloginfo( 'name' ) . ' contact form', 'email_content' => '[all-fields]',
	'success_message' => 'Thanks! Your message has been sent &ndash; we&rsquo;ll be in touch soon.', 'error_message' => 'Something went wrong, please try again.', 'required_field_message' => 'This field is required.',
], 'wtc-form' );

$formCol = $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 8 ), 'width' => [ 'unit' => '%', 'size' => 60 ], 'width_tablet' => [ 'unit' => '%', 'size' => 100 ], 'padding' => $pad( 34, 34, 34, 34 ), 'padding_mobile' => $pad( 22, 18, 22, 18 ) ], [
	$h( 'Send us a message', 'h2', 28, 24 ),
	$txt( '<p>We usually reply within one working day.</p>', '#666B66', 15 ),
	$form,
], 'wtc-card' );

$main = $c( [ '_title' => 'Contact Main', 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1280 ], 'flex_direction' => 'row', 'flex_direction_tablet' => 'column', 'flex_align_items' => 'flex-start', 'flex_gap' => $g( 24 ), 'padding' => $pad( 0, 24, 40, 24 ), 'padding_mobile' => $pad( 0, 16, 30, 16 ) ], [ $info, $formCol ], 'wtc-main', false );

$cta = $c( [ '_title' => 'Free Design CTA', 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1280 ], 'flex_direction' => 'column', 'padding' => $pad( 40, 24, 100, 24 ), 'padding_mobile' => $pad( 20, 16, 64, 16 ) ], [
	$c( [ 'flex_direction' => 'row', 'flex_direction_tablet' => 'column', 'flex_align_items' => 'center', 'flex_gap' => $g( 40 ), 'padding' => $pad( 48, 48, 48, 48 ), 'padding_mobile' => $pad( 28, 20, 28, 20 ) ], [
		$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 14 ), 'width' => [ 'unit' => '%', 'size' => 42 ], 'width_tablet' => [ 'unit' => '%', 'size' => 100 ] ], [
			$h( 'Prefer a free design consultation?', 'h2', 36, 28 ),
			$txt( '<p>Share your room size and we&rsquo;ll suggest the best cabinet layout &ndash; free and with no obligation.</p>', '#555B56', 16 ),
			$w( 'button', [ 'text' => 'Get a Free Design', 'link' => [ 'url' => $design ], 'selected_icon' => [ 'value' => 'fas fa-arrow-right', 'library' => 'fa-solid' ], 'icon_align' => 'right' ], 'wt-b' ),
		] ),
		$c( [ 'flex_direction' => 'column', 'width' => [ 'unit' => '%', 'size' => 58 ], 'width_tablet' => [ 'unit' => '%', 'size' => 100 ] ], [
			$c( [ 'flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_gap' => $g( 14 ) ], array_map( function ( $st ) use ( $c, $w, $h, $txt, $g ) {
				return $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 8 ) ], [ $w( 'heading', [ 'title' => $st[0], 'header_size' => 'span' ], 'wtp-n-n' ), $h( $st[1], 'h3', 16, 16 ), $txt( '<p>' . $st[2] . '</p>', '#666B66', 14 ) ], 'wtp-step wtp-step--sm' );
			}, [ [ '1', 'Tell us your space', 'Measurements and a photo are enough.' ], [ '2', 'Get your design', 'We suggest a layout and finish.' ], [ '3', 'Delivered to you', 'Free UK delivery to your door.' ] ] ), 'wtp-grid wtp-grid-3' ),
		] ),
	], 'wtc-cta' ),
], '', false );

update_post_meta( $pid, '_wt_backup_content', $page->post_content );
update_post_meta( $pid, '_elementor_edit_mode', 'builder' );
update_post_meta( $pid, '_elementor_template_type', 'wp-page' );
update_post_meta( $pid, '_elementor_version', ELEMENTOR_VERSION );
update_post_meta( $pid, '_wp_page_template', 'elementor_header_footer' );
update_post_meta( $pid, '_elementor_data', wp_slash( wp_json_encode( [ $hero, $main, $cta ] ) ) );
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return $pid;
