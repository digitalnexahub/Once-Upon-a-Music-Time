<?php
/* One-off: replace the 4 HTML widgets on the Home page with native Elementor widgets. */
$pid = (int) get_option( 'page_on_front' );
$raw = get_post_meta( $pid, '_elementor_data', true );
update_post_meta( $pid, '_wt_backup_home_' . time(), wp_slash( $raw ) );
$data = json_decode( $raw, true );

$id  = function () { return substr( md5( uniqid( '', true ) ), 0, 7 ); };
$z   = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ];
$w   = function ( $t, $s, $c = '' ) use ( $id ) { if ( $c ) { $s['_css_classes'] = $c; } return [ 'id' => $id(), 'elType' => 'widget', 'widgetType' => $t, 'settings' => $s, 'elements' => [] ]; };
$c   = function ( $s, $els, $cl = '' ) use ( $id, $z ) { $s += [ 'content_width' => 'full', 'padding' => $z ]; if ( $cl ) { $s['css_classes'] = $cl; } return [ 'id' => $id(), 'elType' => 'container', 'isInner' => true, 'settings' => $s, 'elements' => $els ]; };
$g   = function ( $n ) { return [ 'size' => $n, 'unit' => 'px', 'column' => (string) $n, 'row' => (string) $n ]; };
$hd  = function ( $t, $tag, $cl ) use ( $w ) { return $w( 'heading', [ 'title' => $t, 'header_size' => $tag ], $cl ); };
$tx  = function ( $t, $cl ) use ( $w ) { return $w( 'text-editor', [ 'editor' => '<p>' . $t . '</p>' ], $cl ); };
$img = function ( $file ) {
	global $wpdb;
	$aid = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s LIMIT 1", '%' . $wpdb->esc_like( $file ) . '.%' ) );
	return [ 'id' => $aid, 'url' => $aid ? wp_get_attachment_image_url( $aid, 'large' ) : '' ];
};

/* leaf SVG -> media library attachment */
$leaf_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 260"><g fill="none" stroke="#6f8a6a" stroke-width="2" stroke-linecap="round"><path d="M190 258C160 200 130 140 70 40"/></g><g fill="#8fa889" opacity=".9"><path d="M150 190c-30-8-48-30-50-58 26 6 46 28 50 58z"/><path d="M155 196c26-20 34-46 26-72-24 14-32 42-26 72z"/><path d="M118 132c-28-4-46-24-50-50 26 4 44 24 50 50z"/><path d="M124 136c22-18 28-42 20-66-20 14-26 38-20 66z"/><path d="M92 86c-24-2-40-18-44-40 22 2 38 18 44 40z"/><path d="M98 90c18-14 24-34 18-54-18 12-22 32-18 54z"/></g><g fill="#a9bda3" opacity=".8"><path d="M175 236c-30 0-52-16-60-40 28 0 50 16 60 40z"/><path d="M74 44c-14-10-18-24-12-38 14 8 18 24 12 38z"/></g></svg>';
$up = wp_upload_dir();
$lf = $up['basedir'] . '/wt-icons/leaf-sprig.svg';
wp_mkdir_p( dirname( $lf ) );
file_put_contents( $lf, $leaf_svg );
$leaf_id = (int) get_option( 'wt_leaf_att' );
if ( ! $leaf_id || ! get_post( $leaf_id ) ) {
	$leaf_id = wp_insert_attachment( [ 'post_title' => 'Leaf sprig decoration', 'post_mime_type' => 'image/svg+xml', 'post_status' => 'inherit' ], $lf );
	update_post_meta( $leaf_id, '_wp_attached_file', 'wt-icons/leaf-sprig.svg' );
	update_option( 'wt_leaf_att', $leaf_id );
}
$leaf_url = $up['baseurl'] . '/wt-icons/leaf-sprig.svg';

/* 3 steps */
$step = function ( $n, $file, $alt, $h, $p ) use ( $c, $w, $hd, $tx, $g, $img ) {
	$im = $img( $file );
	return $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 0 ) ], [
		$w( 'image', [ 'image' => $im, 'image_size' => 'large' ], 'wt3n__media' ),
		$hd( (string) $n, 'span', 'wt3n__num' ),
		$hd( $h, 'h3', 'wt3n__h' ),
		$tx( $p, 'wt3n__p' ),
	], 'wt3n__card wtp-a' );
};
$arrow = function () use ( $w ) { return $w( 'icon', [ 'selected_icon' => [ 'value' => 'fas fa-long-arrow-alt-right', 'library' => 'fa-solid' ] ], 'wt3n__arrow wtp-a' ); };
$steps = $c( [ 'flex_direction' => 'column', 'flex_align_items' => 'center', 'flex_gap' => $g( 0 ) ], [
	$hd( 'Create your dream space', 'p', 'wt3n__sub wtp-a' ),
	$hd( 'A beautiful laundry room in 3 simple steps', 'h2', 'wt3n__title wtp-a' ),
	$c( [ 'flex_direction' => 'row', 'flex_gap' => $g( 0 ), 'flex_align_items' => 'stretch' ], [
		$step( 1, 'washtower-s1', 'Floor plan with a measuring tape and pencil', 'Measure Your Space', 'Enter your room dimensions and tell us what you need.' ),
		$arrow(),
		$step( 2, 'washtower-s2', 'Laptop showing a cabinet design', 'Design Your Cabinet', 'Choose your layout, storage options and finishes.' ),
		$arrow(),
		$step( 3, 'washtower-s3', 'Finished laundry room', 'Bring It To Life', 'Order with confidence and transform your space.' ),
	], 'wt3n__row' ),
], 'wt3n' );

/* why + utility */
$feat = function ( $icon, $t, $s ) use ( $c, $w, $hd, $g ) {
	return $c( [ 'flex_direction' => 'row', 'flex_align_items' => 'center', 'flex_gap' => $g( 16 ) ], [
		$w( 'icon', [ 'selected_icon' => [ 'value' => $icon, 'library' => 'fa-solid' ] ], 'wtwn__ic' ),
		$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 4 ) ], [ $hd( $t, 'h3', 'wtwn__ft' ), $hd( $s, 'p', 'wtwn__fs' ) ] ),
	], 'wtwn__feat wtp-a' );
};
$why = $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 0 ) ], [
	$c( [ 'flex_direction' => 'column', 'flex_align_items' => 'center', 'flex_gap' => $g( 0 ) ], [
		$hd( 'Why choose Washtower&reg;', 'p', 'wtwn__sub wtp-a' ),
		$hd( 'Thoughtfully designed. Built for everyday life.', 'h2', 'wtwn__h2 wtp-a' ),
		$c( [ 'flex_direction' => 'row', 'flex_gap' => $g( 24 ) ], [
			$feat( 'far fa-gem', 'Premium quality', 'Built to last for years' ),
			$feat( 'fas fa-leaf', 'Sustainable materials', 'FSC certified boards' ),
			$feat( 'fas fa-th-large', 'Smart storage solutions', 'Maximise every inch' ),
			$feat( 'fas fa-home', 'Designed for real homes', 'Stylish, practical, timeless' ),
		], 'wtwn__feats' ),
	], 'wtwn__in' ),
], 'wtwn__why' );
$show = $c( [ 'flex_direction' => 'row', 'flex_gap' => $g( 0 ) ], [
	$c( [ 'flex_direction' => 'column', 'flex_justify_content' => 'center', 'flex_gap' => $g( 0 ) ], [
		$hd( 'More than a laundry room', 'p', 'wtwn__sub wtwn__sub--l wtp-a' ),
		$hd( 'Smart cabinets for every utility room', 'h2', 'wtwn__h2 wtwn__h2--l wtp-a' ),
		$tx( 'Combine laundry, storage and wardrobe space with beautifully designed cupboards that make your home work harder.', 'wtwn__p wtp-a' ),
		$w( 'button', [ 'text' => 'Explore Utility Cabinets', 'link' => [ 'url' => home_url( '/product-category/galley-utility-walls/' ) ], 'selected_icon' => [ 'value' => 'fas fa-arrow-right', 'library' => 'fa-solid' ], 'icon_align' => 'right' ], 'wtwn__btn wtp-a' ),
	], 'wtwn__text' ),
	$c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 0 ) ], [
		$w( 'image', [ 'image' => $img( 'cabinet-wall-1-8-natural-oak' ), 'image_size' => 'large' ], 'wtwn__frame' ),
		$hd( 'Laundry, storage and more <br>in perfect harmony', 'p', 'wtwn__bubble' ),
	], 'wtwn__media wtp-a' ),
], 'wtwn__show' );
$whyshow = $c( [ 'flex_direction' => 'column', 'flex_gap' => $g( 0 ) ], [ $why, $show ], 'wtwn' );

$repl = [
	'24bf47b' => $w( 'heading', [ 'title' => 'A more<br>beautiful<br>laundry day', 'header_size' => 'p' ], 'wt-note' ),
	'688c683' => $w( 'image', [ 'image' => [ 'id' => $leaf_id, 'url' => $leaf_url ], 'image_size' => 'full' ], 'wt-leaf' ),
	'aa0b32d' => $steps,
	'8867cf0' => $whyshow,
];
$done = [];
$walk = function ( &$els ) use ( &$walk, $repl, &$done ) {
	foreach ( $els as &$e ) {
		if ( isset( $e['id'], $repl[ $e['id'] ] ) && ( $e['widgetType'] ?? '' ) === 'html' ) { $done[] = $e['id']; $e = $repl[ $e['id'] ]; continue; }
		if ( ! empty( $e['elements'] ) ) { $walk( $e['elements'] ); }
	}
};
$walk( $data );
update_post_meta( $pid, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return [ 'replaced' => $done, 'html_left' => substr_count( wp_json_encode( $data ), '"widgetType":"html"' ), 'leaf' => $leaf_id ];
