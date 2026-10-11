<?php
/* One-off builder: Elementor layouts for Delivery Information, Returns & Warranty, Installation Guide and FAQs. */
$id  = function () { return substr( md5( uniqid( '', true ) ), 0, 7 ); };
$z   = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ];
$w   = function ( $t, $s, $c = '' ) use ( $id ) { if ( $c ) { $s['_css_classes'] = $c; } return [ 'id' => $id(), 'elType' => 'widget', 'widgetType' => $t, 'settings' => $s, 'elements' => [] ]; };
$c   = function ( $s, $els, $cl = '', $in = true ) use ( $id, $z ) { $s += [ 'content_width' => 'full', 'padding' => $z ]; if ( $cl ) { $s['css_classes'] = $cl; } return [ 'id' => $id(), 'elType' => 'container', 'isInner' => $in, 'settings' => $s, 'elements' => $els ]; };
$g   = function ( $n ) { return [ 'size' => $n, 'unit' => 'px', 'column' => (string) $n, 'row' => (string) $n ]; };
$pad = function ( $t, $r, $b, $l ) { return [ 'unit' => 'px', 'top' => "$t", 'right' => "$r", 'bottom' => "$b", 'left' => "$l", 'isLinked' => false ]; };
$h   = function ( $t, $tag, $size, $sm, $col = '#1D211E', $cl = '' ) use ( $w ) {
	return $w( 'heading', [ 'title' => $t, 'header_size' => $tag, 'title_color' => $col, 'typography_typography' => 'custom', 'typography_font_family' => 'Barlow', 'typography_font_size' => [ 'unit' => 'px', 'size' => $size ], 'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => $sm ], 'typography_font_weight' => '700', 'typography_line_height' => [ 'unit' => 'em', 'size' => 1.12 ] ], $cl );
};
$txt = function ( $html, $col = '#666B66', $size = 17, $cl = '' ) use ( $w ) {
	return $w( 'text-editor', [ 'editor' => $html, 'text_color' => $col, 'typography_typography' => 'custom', 'typography_font_family' => 'Barlow', 'typography_font_size' => [ 'unit' => 'px', 'size' => $size ], 'typography_line_height' => [ 'unit' => 'em', 'size' => 1.65 ] ], $cl );
};
$box = function ( $s ) { return [ 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1200 ] ] + $s; };
$url = function ( $t ) { return function_exists( 'wt_page_url' ) ? wt_page_url( $t ) : home_url( '/' ); };

/* Shared pieces */
$hero = function ( $eyebrow, $title, $intro, $chips ) use ( $c, $w, $h, $txt, $g, $pad, $box ) {
	$ch = '<div class="wtc-chips">'; foreach ( $chips as $x ) { $ch .= '<span>' . $x . '</span>'; } $ch .= '</div>';
	return $c( $box( [ '_title' => 'Hero', 'flex_direction' => 'column', 'flex_gap' => $g( 18 ), 'padding' => $pad( 96, 24, 120, 24 ), 'padding_mobile' => $pad( 60, 16, 80, 16 ) ] ), [
		$w( 'heading', [ 'title' => $eyebrow, 'header_size' => 'p' ], 'wtc-eyebrow wtp-a' ),
		$h( $title, 'h1', 58, 34, '#FFFFFF', 'wtp-a' ),
		$txt( '<p>' . $intro . '</p>', 'rgba(255,255,255,.8)', 19, 'wtp-a wtp-narrow' ),
		$w( 'html', [ 'html' => $ch ], 'wtp-a' ),
	], 'wtc-hero', false );
};
$section = function ( $title, $sub, $els, $bg = '#FFFFFF', $label = '' ) use ( $c, $h, $txt, $g, $pad, $box, $w ) {
	$head = [];
	if ( $label ) { $head[] = $w( 'heading', [ 'title' => $label, 'header_size' => 'p' ], 'wtp-label wtp-a' ); }
	$head[] = $h( $title, 'h2', 40, 28, '#1D211E', 'wtp-a' );
	if ( $sub ) { $head[] = $txt( '<p>' . $sub . '</p>', '#666B66', 17, 'wtp-a wtp-narrow' ); }
	return $c( $box( [ '_title' => wp_strip_all_tags( $title ), 'flex_direction' => 'column', 'flex_gap' => $g( 34 ), 'background_background' => 'classic', 'background_color' => $bg, 'padding' => $pad( 90, 24, 90, 24 ), 'padding_mobile' => $pad( 56, 16, 56, 16 ) ] ), array_merge( [ $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 12 ) ], $head ) ], $els ), '', false );
};
$cards = function ( $items, $cols = 3 ) use ( $w ) {
	$o = '<div class="wtp-cards wtp-cols-' . $cols . '">';
	foreach ( $items as $i => $it ) { $o .= '<div class="wtp-card wtp-a" style="--d:' . ( $i * 80 ) . 'ms"><span class="wtp-ic">' . $it[0] . '</span><h3>' . $it[1] . '</h3><p>' . $it[2] . '</p></div>'; }
	return $w( 'html', [ 'html' => $o . '</div>' ] );
};
$steps = function ( $items ) use ( $w ) {
	$o = '<ol class="wtp-steps">';
	foreach ( $items as $i => $it ) { $o .= '<li class="wtp-a" style="--d:' . ( $i * 90 ) . 'ms"><span class="wtp-n">' . ( $i + 1 ) . '</span><div><h3>' . $it[0] . '</h3><p>' . $it[1] . '</p></div></li>'; }
	return $w( 'html', [ 'html' => $o . '</ol>' ] );
};
$faq = function ( $items ) use ( $w, $id ) {
	$t = []; foreach ( $items as $q ) { $t[] = [ '_id' => $id(), 'tab_title' => $q[0], 'tab_content' => '<p>' . $q[1] . '</p>' ]; }
	return $w( 'toggle', [ 'tabs' => $t, 'selected_icon' => [ 'value' => 'fas fa-plus', 'library' => 'fa-solid' ], 'selected_active_icon' => [ 'value' => 'fas fa-minus', 'library' => 'fa-solid' ], 'title_html_tag' => 'h3' ], 'wtp-faq wtp-a' );
};
$cta = function ( $title, $text, $btn, $link ) use ( $c, $h, $txt, $w, $g, $pad, $box ) {
	return $c( $box( [ '_title' => 'CTA', 'flex_direction' => 'column', 'padding' => $pad( 20, 24, 100, 24 ), 'padding_mobile' => $pad( 10, 16, 64, 16 ) ] ), [
		$c( [ 'flex_direction' => 'row', 'flex_direction_mobile' => 'column', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $g( 24 ), 'padding' => $pad( 44, 48, 44, 48 ), 'padding_mobile' => $pad( 28, 20, 28, 20 ) ], [
			$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 10 ), 'width' => [ 'unit' => '%', 'size' => 65 ], 'width_mobile' => [ 'unit' => '%', 'size' => 100 ] ], [ $h( $title, 'h2', 32, 26 ), $txt( '<p>' . $text . '</p>', '#555B56', 16 ) ] ),
			$w( 'button', [ 'text' => $btn, 'link' => [ 'url' => $link ], 'selected_icon' => [ 'value' => 'fas fa-arrow-right', 'library' => 'fa-solid' ], 'icon_align' => 'right' ], 'wt-b' ),
		], 'wtc-cta wtp-a' ),
	], '', false );
};
$I = [
	'truck' => '<svg viewBox="0 0 24 24"><path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg>',
	'clock' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
	'home'  => '<svg viewBox="0 0 24 24"><path d="M3 11l9-7 9 7v9H3z"/><path d="M9 20v-6h6v6"/></svg>',
	'box'   => '<svg viewBox="0 0 24 24"><path d="M3 7l9-4 9 4v10l-9 4-9-4z"/><path d="M3 7l9 4 9-4M12 11v10"/></svg>',
	'shield'=> '<svg viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/></svg>',
	'undo'  => '<svg viewBox="0 0 24 24"><path d="M9 14L4 9l5-5"/><path d="M4 9h11a5 5 0 0 1 0 10h-3"/></svg>',
	'tool'  => '<svg viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4z"/></svg>',
	'level' => '<svg viewBox="0 0 24 24"><rect x="2" y="9" width="20" height="6" rx="2"/><path d="M12 9v6M8 12h.01M16 12h.01"/></svg>',
	'chat'  => '<svg viewBox="0 0 24 24"><path d="M4 5h16v11H8l-4 4z"/></svg>',
	'drill' => '<svg viewBox="0 0 24 24"><path d="M4 8h11v6H4zM15 10h5M9 14v6h3"/></svg>',
	'ruler' => '<svg viewBox="0 0 24 24"><path d="M3 17L17 3l4 4L7 21z"/><path d="M7 13l2 2M10 10l2 2M13 7l2 2"/></svg>',
	'users' => '<svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.5 3.4-5.5 6.5-5.5s5.7 2 6.5 5.5"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14.8c1.8.8 3 2.6 3.5 5.2"/></svg>',
];
$contact = $url( 'Contact Us' );
$design  = $url( 'Get a Free Design' );

$pages = [];

$pages['delivery-information'] = [
	$hero( 'Delivery information', 'Delivered free, right to your door', 'Every cabinet order ships free within the UK. Here is how delivery works, from order to unpacking.', [ 'Free UK delivery', 'Signature delivery', 'Tracked shipment' ] ),
	$section( 'How delivery works', 'From the moment you order, we keep you updated at every step.', [ $steps( [ [ 'Order placed', 'You receive an order confirmation by email straight away.' ], [ 'Prepared and packed', 'Your cabinet is checked and carefully flat-packed for transport.' ], [ 'On its way', 'We send tracking details as soon as your order leaves our warehouse.' ], [ 'Delivered to your door', 'A signature is taken on delivery so you know it arrived safely.' ] ] ) ], '#FFFFFF', 'Step by step' ),
	$section( 'Good to know', '', [ $cards( [ [ $I['truck'], 'Free UK delivery', 'Delivery is free on all cabinet orders to UK mainland addresses.' ], [ $I['clock'], 'Delivery time', 'Most orders arrive within 1&ndash;4 weeks. The estimate is shown on each product page.' ], [ $I['home'], 'Signature delivery', 'Delivered to your door. Please make sure someone is home to sign.' ], [ $I['box'], 'Check your parcels', 'Count the boxes and check for damage before signing. Report any issue within 48 hours.' ], [ $I['ruler'], 'Measure access', 'Check doorways, stairs and hallways so the boxes can reach the room.' ], [ $I['chat'], 'Need to change a date?', 'Contact us as early as possible and we will do our best to help.' ] ] ) ], '#F8F7F3' ),
	$section( 'Delivery questions', '', [ $faq( [ [ 'Do you deliver outside the UK mainland?', 'Please contact us before ordering for Northern Ireland, the Highlands and islands, and we will confirm options for your address.' ], [ 'Can I track my order?', 'Yes. You receive tracking details by email when your order is dispatched.' ], [ 'What if something is damaged?', 'Note it on the delivery receipt if you can, take a photo and contact us within 48 hours. We will send a replacement part.' ], [ 'Do you take away the packaging?', 'Packaging is not collected. Most of it is cardboard and can be recycled at home.' ] ] ) ] ),
	$cta( 'Questions about your delivery?', 'Our team is happy to help with dates, access or anything else.', 'Contact us', $contact ),
];

$pages['returns-warranty'] = [
	$hero( 'Returns & warranty', 'Built to last, backed by our guarantee', 'We want you to love your laundry cabinet. If something is not right, here is how returns and warranty claims work.', [ '10 year guarantee', '14-day right to cancel', 'Free replacement parts' ] ),
	$section( 'Our promise to you', '', [ $cards( [ [ $I['shield'], '10 year guarantee', 'Our cabinets are guaranteed for 10 years against manufacturing defects under normal home use.' ], [ $I['undo'], '14-day right to cancel', 'You can cancel your order within 14 days of delivery. Items must be unused, unassembled and in their original packaging.' ], [ $I['box'], 'Missing or damaged parts', 'Something missing or damaged on arrival? Tell us within 48 hours and we will send a replacement part free of charge.' ] ] ) ], '#FFFFFF', 'Peace of mind' ),
	$section( 'How to return an item', 'Returns are simple. Follow these steps and we will guide you through the rest.', [ $steps( [ [ 'Contact us', 'Send us your order number and the item you want to return.' ], [ 'Pack it up', 'Use the original packaging and keep all parts together.' ], [ 'We arrange collection', 'We confirm the return and the collection or drop-off details.' ], [ 'Refund issued', 'Your refund is processed once the item has been received and checked.' ] ] ) ], '#F8F7F3', 'Step by step' ),
	$section( 'What the warranty covers', '', [ $w( 'html', [ 'html' => '<div class="wtp-two"><div class="wtp-list wtp-yes wtp-a"><h3>Covered</h3><ul><li>Manufacturing defects in panels and doors</li><li>Hinges and drawer runners that fail in normal use</li><li>Faulty fittings supplied with your cabinet</li></ul></div><div class="wtp-list wtp-no wtp-a" style="--d:100ms"><h3>Not covered</h3><ul><li>Damage from incorrect assembly or installation</li><li>Water damage from leaks or flooding</li><li>Normal wear, misuse or changes to the cabinet</li></ul></div></div>' ] ) ] ),
	$section( 'Returns questions', '', [ $faq( [ [ 'Who pays for return shipping?', 'Please contact us before returning an item and we will explain the costs for your order.' ], [ 'Can I return an assembled cabinet?', 'Cancellations under the 14-day right apply to unused, unassembled items. If an assembled item is faulty, it is covered by our guarantee.' ], [ 'How do I make a warranty claim?', 'Contact us with your order number, a short description and photos of the issue.' ] ] ) ], '#F8F7F3' ),
	$cta( 'Need help with a return or claim?', 'Send us your order number and we will take it from there.', 'Contact us', $contact ),
];

$pages['installation-guide'] = [
	$hero( 'Installation guide', 'Assemble your cabinet with confidence', 'Our cabinets are designed for easy self-assembly. Follow this guide together with the manual included in your delivery.', [ 'Step-by-step manual included', 'Standard tools', 'Help when you need it' ] ),
	$section( 'Before you start', '', [ $cards( [ [ $I['drill'], 'Tools you need', 'Cordless screwdriver, spirit level, tape measure, pencil and a step stool.' ], [ $I['users'], 'Two people', 'Tall cabinets are easier and safer to lift and fix with two people.' ], [ $I['box'], 'Check all parts', 'Unpack on a soft surface and check every part against the manual.' ] ] ) ], '#FFFFFF', 'Preparation' ),
	$section( 'Installation in 6 steps', 'Take your time &ndash; most cabinets can be assembled in an afternoon.', [ $steps( [ [ 'Measure the space', 'Check the height, width and depth of your space and your appliances.' ], [ 'Assemble the frame', 'Build the cabinet body following the manual, keeping panels square.' ], [ 'Level the cabinet', 'Adjust the feet and use a spirit level so doors close evenly.' ], [ 'Fix to the wall', 'Always secure tall cabinets to the wall with the anchors supplied.' ], [ 'Place your appliances', 'Slide the washer and dryer in and connect them as their manuals describe.' ], [ 'Hang and adjust doors', 'Fit the doors, then use the 3D-adjustable hinges to line them up.' ] ] ) ], '#F8F7F3', 'Step by step' ),
	$section( 'Safety tips', '', [ $cards( [ [ $I['shield'], 'Always anchor', 'Tall cabinets must be fixed to the wall to prevent tipping.' ], [ $I['level'], 'Keep it level', 'A level cabinet reduces vibration and keeps doors aligned.' ], [ $I['tool'], 'Plumbing and power', 'Leave appliance connections to a qualified professional if you are unsure.' ] ] ) ] ),
	$section( 'Installation questions', '', [ $faq( [ [ 'How long does assembly take?', 'This depends on the cabinet, but most customers finish in a few hours with two people.' ], [ 'I lost a screw or part. What now?', 'Contact us with your order number and the part number from the manual and we will send a replacement.' ], [ 'Can my appliances go in any order?', 'Follow your appliance manufacturer&rsquo;s advice. Usually the heavier washing machine goes at the bottom.' ] ] ) ], '#F8F7F3' ),
	$cta( 'Stuck on a step?', 'Send us a photo and your question and we will help you finish.', 'Contact us', $contact ),
];

$pages['faqs'] = [
	$hero( 'FAQs', 'Answers to common questions', 'Everything you need to know about our cabinets, ordering, delivery and returns.', [ 'Products', 'Ordering', 'Delivery', 'Returns' ] ),
	$c( $box( [ '_title' => 'FAQ body', 'flex_direction' => 'column', 'flex_gap' => $g( 50 ), 'padding' => $pad( 80, 24, 60, 24 ), 'padding_mobile' => $pad( 50, 16, 40, 16 ) ] ), [
		$w( 'html', [ 'html' => '<div class="wtp-search wtp-a"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg><input type="search" placeholder="Search questions..." aria-label="Search questions" data-wtp-search></div>' ] ),
		$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 18 ) ], [ $h( 'Products', 'h2', 28, 24, '#1D211E', 'wtp-a' ), $faq( [ [ 'Which washing machines and dryers fit?', 'Most standard full-size appliances fit. Check the internal dimensions on each product page against your appliance measurements.' ], [ 'What are the cabinets made of?', 'High-quality chipboard with a water-repellent melamine coating, with soft-close hinges.' ], [ 'Can I choose a different finish?', 'Available finishes are shown on each product. Contact us if you are looking for something specific.' ] ] ) ] ),
		$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 18 ) ], [ $h( 'Ordering', 'h2', 28, 24, '#1D211E', 'wtp-a' ), $faq( [ [ 'How do I place an order?', 'Add products to your basket and go to checkout. You will receive a confirmation email.' ], [ 'Can I change my order?', 'Contact us as soon as possible with your order number and we will do our best to help.' ], [ 'Do you offer design help?', 'Yes &ndash; request a free design and we will suggest the best layout for your space.' ] ] ) ] ),
		$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 18 ) ], [ $h( 'Delivery', 'h2', 28, 24, '#1D211E', 'wtp-a' ), $faq( [ [ 'How much is delivery?', 'Delivery is free on all cabinet orders within the UK mainland.' ], [ 'How long does delivery take?', 'Most orders arrive within 1&ndash;4 weeks. See the estimate on each product page.' ] ] ) ] ),
		$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 18 ) ], [ $h( 'Returns & warranty', 'h2', 28, 24, '#1D211E', 'wtp-a' ), $faq( [ [ 'What is your guarantee?', 'Our cabinets come with a 10 year guarantee against manufacturing defects.' ], [ 'Can I cancel my order?', 'Yes, within 14 days of delivery for unused, unassembled items in the original packaging.' ] ] ) ] ),
	], '', false ),
	$cta( 'Still have a question?', 'Our team is happy to help &ndash; send us a message.', 'Contact us', $contact ),
];

$out = [];
foreach ( $pages as $slug => $data ) {
	$p = get_page_by_path( $slug );
	if ( ! $p ) { continue; }
	if ( ! get_post_meta( $p->ID, '_wt_backup_content', true ) ) { update_post_meta( $p->ID, '_wt_backup_content', $p->post_content ); }
	update_post_meta( $p->ID, '_elementor_edit_mode', 'builder' );
	update_post_meta( $p->ID, '_elementor_template_type', 'wp-page' );
	update_post_meta( $p->ID, '_elementor_version', ELEMENTOR_VERSION );
	update_post_meta( $p->ID, '_wp_page_template', 'elementor_header_footer' );
	update_post_meta( $p->ID, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	$out[ $slug ] = $p->ID;
}
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return $out;
