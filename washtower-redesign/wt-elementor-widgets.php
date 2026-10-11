<?php
/**
 * Plugin Name: Washtower Elementor Widgets
 * Description: Native Elementor widgets for the WASHTOWER site (Product Carousel).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WT_ASSET_URL', content_url( 'uploads/wt-assets/' ) );
define( 'WT_ASSET_VER', '1.3.1' );

add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'wt-product-carousel', WT_ASSET_URL . 'wtpc.css', [], WT_ASSET_VER );
	wp_register_script( 'wt-product-carousel', WT_ASSET_URL . 'wtpc.js', [], WT_ASSET_VER, true );
} );

function wt_pc_markup( array $s, array $list, $uid ) {
	$shop  = ! empty( $s['link']['url'] ) ? $s['link']['url'] : wc_get_page_permalink( 'shop' );
				$bag   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>';
	?>
	<section class="wtpc" data-ssr="1" data-shop="<?php echo esc_url( $shop ); ?>" aria-labelledby="<?php echo esc_attr( $uid ); ?>">
		<div class="wtpc__in">
			<header class="wtpc__head">
				<div>
					<?php if ( $s['subtitle'] ) : ?><p class="wtpc__sub wtpc__rv"><?php echo esc_html( $s['subtitle'] ); ?></p><?php endif; ?>
					<h2 id="<?php echo esc_attr( $uid ); ?>" class="wtpc__title wtpc__rv"><?php echo esc_html( $s['title'] ); ?></h2>
				</div>
				<?php if ( $s['link_text'] ) : ?>
					<a class="wtpc__all wtpc__rv" href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html( $s['link_text'] ); ?> <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
				<?php endif; ?>
			</header>
			<div class="wtpc__car">
				<button type="button" class="wtpc__nav wtpc__nav--prev" aria-label="Previous products"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg></button>
				<div class="wtpc__view" tabindex="0" aria-roledescription="carousel" aria-label="<?php echo esc_attr( $s['title'] ); ?>">
					<ul class="wtpc__track">
						<?php foreach ( $list as $p ) :
							$img    = $p->get_image_id();
							$count  = (int) $p->get_review_count();
							$rating = (float) $p->get_average_rating();
							$simple = $p->is_type( 'simple' ) && $p->is_purchasable() && $p->is_in_stock();
							$sale   = $p->is_on_sale() && 'yes' === $s['show_regular'] && $p->get_regular_price();
							?>
							<li class="wtpc__card">
								<a class="wtpc__img" href="<?php echo esc_url( $p->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
									<?php echo $img ? wp_get_attachment_image( $img, 'woocommerce_single', false, [ 'loading' => 'lazy', 'sizes' => '(max-width:767px) 85vw,(max-width:1024px) 48vw,300px', 'alt' => $p->get_name() ] ) : wc_placeholder_img( 'woocommerce_single' ); ?>
								</a>
								<div class="wtpc__body">
									<h3 class="wtpc__name"><a href="<?php echo esc_url( $p->get_permalink() ); ?>"><?php echo esc_html( $p->get_name() ); ?></a></h3>
									<div class="wtpc__rate">
										<?php if ( 'yes' === $s['show_rating'] && $count ) : ?>
											<span class="wtpc__stars" aria-label="<?php echo esc_attr( sprintf( 'Rated %.1f out of 5', $rating ) ); ?>">&#9733;&#9733;&#9733;&#9733;&#9733;<span style="width:<?php echo esc_attr( $rating / 5 * 100 ); ?>%">&#9733;&#9733;&#9733;&#9733;&#9733;</span></span>
											<span><?php echo esc_html( number_format( $rating, 1 ) . ' (' . $count . ')' ); ?></span>
										<?php endif; ?>
									</div>
									<div class="wtpc__price">
										<span class="wtpc__now"><?php echo wp_kses_post( wc_price( wc_get_price_to_display( $p ) ) ); ?></span>
										<?php if ( $sale ) : ?><span class="wtpc__was"><span class="screen-reader-text">Was </span><?php echo wp_kses_post( wc_price( wc_get_price_to_display( $p, [ 'price' => $p->get_regular_price() ] ) ) ); ?></span><?php endif; ?>
									</div>
									<?php if ( $simple ) : ?>
										<button type="button" class="wtpc__btn" data-id="<?php echo esc_attr( $p->get_id() ); ?>" data-name="<?php echo esc_attr( $p->get_name() ); ?>"><span><?php echo $bag . esc_html( $s['button_text'] ); ?></span></button>
									<?php else : ?>
										<a class="wtpc__btn" href="<?php echo esc_url( $p->get_permalink() ); ?>"><span><?php echo $bag; ?>Choose options</span></a>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<button type="button" class="wtpc__nav wtpc__nav--next" aria-label="Next products"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg></button>
			</div>
			<div class="wtpc__dots" role="tablist" aria-label="Choose slide"></div>
			<p class="wtpc__msg" role="status" aria-live="polite"></p>
		</div>
	</section>
	<?php
}

add_action( 'elementor/elements/categories_registered', function ( $manager ) {
	$manager->add_category( 'washtower', [ 'title' => 'WASHTOWER', 'icon' => 'fa fa-plug' ] );
} );

add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
	if ( ! class_exists( 'WooCommerce' ) ) { return; }

	class WT_Product_Carousel_Widget extends \Elementor\Widget_Base {

		public function get_name() { return 'wt-product-carousel'; }
		public function get_title() { return 'WASHTOWER Product Carousel'; }
		public function get_icon() { return 'eicon-slider-push'; }
		public function get_categories() { return [ 'washtower', 'woocommerce-elements' ]; }
		public function get_keywords() { return [ 'product', 'carousel', 'slider', 'woocommerce', 'washtower' ]; }
		public function get_style_depends() { return [ 'wt-product-carousel' ]; }
		public function get_script_depends() { return [ 'wt-product-carousel' ]; }

		protected function register_controls() {
			$this->start_controls_section( 'heading_section', [ 'label' => 'Heading' ] );
			$this->add_control( 'subtitle', [ 'label' => 'Subtitle', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Featured products' ] );
			$this->add_control( 'title', [ 'label' => 'Title', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Our most popular cabinets', 'label_block' => true ] );
			$this->add_control( 'link_text', [ 'label' => 'Link text', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'View all products' ] );
			$this->add_control( 'link', [ 'label' => 'Link', 'type' => \Elementor\Controls_Manager::URL, 'default' => [ 'url' => '' ], 'description' => 'Leave empty to use the shop page.' ] );
			$this->end_controls_section();

			$this->start_controls_section( 'query_section', [ 'label' => 'Products' ] );
			$this->add_control( 'source', [
				'label'   => 'Source',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'featured',
				'options' => [ 'featured' => 'Featured (then newest)', 'latest' => 'Newest', 'sale' => 'On sale', 'category' => 'Category', 'ids' => 'Hand-picked' ],
			] );
			$cats = [];
			foreach ( get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] ) as $t ) { $cats[ $t->slug ] = $t->name; }
			$this->add_control( 'category', [ 'label' => 'Category', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $cats, 'condition' => [ 'source' => 'category' ] ] );
			$this->add_control( 'ids', [ 'label' => 'Product IDs', 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => '69, 70, 71', 'description' => 'Comma-separated, in display order.', 'condition' => [ 'source' => 'ids' ] ] );
			$this->add_control( 'limit', [ 'label' => 'Number of products', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 8, 'min' => 1, 'max' => 24 ] );
			$this->add_control( 'button_text', [ 'label' => 'Button text', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Add to basket' ] );
			$this->add_control( 'show_rating', [ 'label' => 'Show rating (real reviews only)', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
			$this->add_control( 'show_regular', [ 'label' => 'Show original price when on sale', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
			$this->end_controls_section();

			$this->start_controls_section( 'style_section', [ 'label' => 'Colours', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
			$this->add_control( 'c_button', [ 'label' => 'Button', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#284B3D', 'selectors' => [ '{{WRAPPER}} .wtpc' => '--g: {{VALUE}};' ] ] );
			$this->add_control( 'c_button_hover', [ 'label' => 'Button hover', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#33604E', 'selectors' => [ '{{WRAPPER}} .wtpc' => '--g2: {{VALUE}};' ] ] );
			$this->add_control( 'c_text', [ 'label' => 'Text', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#242424', 'selectors' => [ '{{WRAPPER}} .wtpc' => '--tx: {{VALUE}};' ] ] );
			$this->add_control( 'c_stars', [ 'label' => 'Stars', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#E5A500', 'selectors' => [ '{{WRAPPER}} .wtpc' => '--gold: {{VALUE}};' ] ] );
			$this->add_control( 'c_bg', [ 'label' => 'Section background', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => [ '{{WRAPPER}} .wtpc' => 'background: {{VALUE}};' ] ] );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'title_typo', 'label' => 'Title typography', 'selector' => '{{WRAPPER}} .wtpc .wtpc__title' ] );
			$this->end_controls_section();
		}

		private function products( $s ) {
			$limit = max( 1, (int) $s['limit'] );
			$base  = [ 'status' => 'publish', 'limit' => $limit, 'visibility' => 'catalog' ];
			switch ( $s['source'] ) {
				case 'ids':
					$ids = array_filter( array_map( 'absint', explode( ',', (string) $s['ids'] ) ) );
					if ( ! $ids ) { return []; }
					$list = wc_get_products( array_merge( $base, [ 'include' => $ids, 'limit' => count( $ids ) ] ) );
					usort( $list, fn( $a, $b ) => array_search( $a->get_id(), $ids, true ) <=> array_search( $b->get_id(), $ids, true ) );
					return $list;
				case 'category':
					return wc_get_products( array_merge( $base, [ 'category' => [ $s['category'] ], 'orderby' => 'date', 'order' => 'DESC' ] ) );
				case 'sale':
					$ids = wc_get_product_ids_on_sale();
					return $ids ? wc_get_products( array_merge( $base, [ 'include' => $ids ] ) ) : [];
				case 'latest':
					return wc_get_products( array_merge( $base, [ 'orderby' => 'date', 'order' => 'DESC' ] ) );
				default:
					$list = wc_get_products( array_merge( $base, [ 'featured' => true, 'orderby' => 'date', 'order' => 'ASC' ] ) );
					if ( count( $list ) < $limit ) {
						$have = array_map( fn( $p ) => $p->get_id(), $list );
						$list = array_merge( $list, wc_get_products( array_merge( $base, [ 'limit' => $limit - count( $list ), 'exclude' => $have, 'orderby' => 'date', 'order' => 'DESC' ] ) ) );
					}
					return $list;
			}
		}

		protected function render() {
			$s = $this->get_settings_for_display();
			wt_pc_markup( $s, $this->products( $s ), 'wtpc-' . $this->get_id() );
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<script>window.wtpcInit&&document.querySelectorAll(".wtpc:not([data-wtpc-ready])").forEach(window.wtpcInit);</script>';
			}
		}
	}

	$widgets_manager->register( new WT_Product_Carousel_Widget() );
} );

/* Single product: related products as the carousel. */
add_action( 'wp', function () {
	if ( ! is_product() ) { return; }
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	add_action( 'woocommerce_after_single_product', function () {
		global $product;
		if ( ! $product ) { return; }
		$ids = array_unique( array_merge( $product->get_upsell_ids(), wc_get_related_products( $product->get_id(), 12 ) ) );
		if ( count( $ids ) < 4 ) {
			$ids = array_unique( array_merge( $ids, wc_get_products( [ 'status' => 'publish', 'limit' => 12, 'return' => 'ids', 'exclude' => [ $product->get_id() ], 'orderby' => 'date', 'order' => 'DESC' ] ) ) );
		}
		$ids  = array_values( array_diff( $ids, [ $product->get_id() ] ) );
		$list = $ids ? wc_get_products( [ 'status' => 'publish', 'include' => $ids, 'limit' => 12, 'visibility' => 'catalog' ] ) : [];
		if ( ! $list ) { return; }
		wp_enqueue_style( 'wt-product-carousel' );
		wp_enqueue_script( 'wt-product-carousel' );
		echo '<div class="wt-related">';
		wt_pc_markup( [ 'subtitle' => 'You may also like', 'title' => 'Complete your laundry room', 'link_text' => 'View all products', 'link' => [ 'url' => '' ], 'button_text' => 'Add to basket', 'show_rating' => 'yes', 'show_regular' => 'yes' ], $list, 'wtpc-related' );
		echo '</div>';
	}, 5 );
} );

/* Dynamic tags for the Testimonials loop item (data comes from WP Admin > Testimonials). */
add_action( 'elementor/dynamic_tags/register', function ( $tags ) {
	if ( ! class_exists( '\Elementor\Core\DynamicTags\Tag' ) ) { return; }
	class WT_Tag_Stars extends \Elementor\Core\DynamicTags\Tag {
		public function get_name() { return 'wt-review-stars'; }
		public function get_title() { return 'Review stars'; }
		public function get_group() { return 'post'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }
		public function render() {
			$r = (int) get_post_meta( get_the_ID(), '_wt_rating', true ) ?: 5;
			echo esc_html( str_repeat( '★', $r ) . str_repeat( '☆', 5 - $r ) );
		}
	}
	class WT_Tag_Text extends \Elementor\Core\DynamicTags\Tag {
		public function get_name() { return 'wt-review-text'; }
		public function get_title() { return 'Review text'; }
		public function get_group() { return 'post'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }
		public function render() { echo '&ldquo;' . esc_html( wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ), 40 ) ) . '&rdquo;'; }
	}
	class WT_Tag_Label extends \Elementor\Core\DynamicTags\Tag {
		public function get_name() { return 'wt-review-label'; }
		public function get_title() { return 'Review label (Verified buyer / Customer)'; }
		public function get_group() { return 'post'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }
		public function render() { echo get_post_meta( get_the_ID(), '_wt_verified', true ) ? 'Verified buyer' : 'Customer'; }
	}
	class WT_Tag_Avatar extends \Elementor\Core\DynamicTags\Data_Tag {
		public function get_name() { return 'wt-review-avatar'; }
		public function get_title() { return 'Customer photo'; }
		public function get_group() { return 'post'; }
		public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY ]; }
		public function get_value( array $options = [] ) {
			$id = (int) get_post_meta( get_the_ID(), '_wt_avatar', true );
			return $id ? [ 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'thumbnail' ) ] : [ 'id' => '', 'url' => get_avatar_url( 0, [ 'size' => 96, 'default' => 'mystery' ] ) ];
		}
	}
	$tags->register( new WT_Tag_Stars() );
	$tags->register( new WT_Tag_Label() );
	$tags->register( new WT_Tag_Text() );
	$tags->register( new WT_Tag_Avatar() );
} );

/* Review slider widget: reviews are added and edited in the Elementor panel (repeater). */
add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
	class WT_Review_Slider_Widget extends \Elementor\Widget_Base {
		public function get_name() { return 'wt-review-slider'; }
		public function get_title() { return 'WASHTOWER Review Slider'; }
		public function get_icon() { return 'eicon-testimonial-carousel'; }
		public function get_categories() { return [ 'washtower', 'general' ]; }
		public function get_keywords() { return [ 'review', 'testimonial', 'slider', 'carousel', 'washtower' ]; }

		protected function register_controls() {
			$this->start_controls_section( 'reviews_section', [ 'label' => 'Reviews' ] );
			$r = new \Elementor\Repeater();
			$r->add_control( 'rating', [ 'label' => 'Stars', 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '5', 'options' => [ '5' => '5', '4' => '4', '3' => '3', '2' => '2', '1' => '1' ] ] );
			$r->add_control( 'star_color', [ 'label' => 'Star colour', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#2E8B57' ] );
			$r->add_control( 'text', [ 'label' => 'Review', 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => '' ] );
			$r->add_control( 'name', [ 'label' => 'Customer name', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ] );
			$r->add_control( 'verified', [ 'label' => 'Verified buyer', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '', 'description' => 'Only turn on if this person really bought from you.' ] );
			$r->add_control( 'avatar', [ 'label' => 'Customer photo (with permission)', 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => [ 'url' => '' ] ] );
			$r->add_control( 'image', [ 'label' => 'Room photo', 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => [ 'url' => '' ] ] );
			$this->add_control( 'reviews', [ 'label' => 'Reviews', 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'default' => [], 'title_field' => '{{{ name || "New review" }}}' ] );
			$this->add_control( 'demo_mode', [ 'label' => 'Demo mode (only editors see the reviews)', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '', 'description' => 'Turn OFF after you replace the demo reviews with real ones, so visitors can see them.' ] );
			$this->add_control( 'empty_text', [ 'label' => 'Text when there are no reviews', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Be the first to share your review.' ] );
			$this->end_controls_section();

			$this->start_controls_section( 'slider_section', [ 'label' => 'Slider' ] );
			$this->add_responsive_control( 'per_view', [ 'label' => 'Cards visible', 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 1, 'max' => 4, 'default' => 3, 'tablet_default' => 2, 'mobile_default' => 1, 'selectors' => [ '{{WRAPPER}} .wt-rs' => '--per: {{VALUE}};' ] ] );
			$this->add_control( 'gap', [ 'label' => 'Gap (px)', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 20, 'selectors' => [ '{{WRAPPER}} .wt-rs' => '--gap: {{VALUE}}px;' ] ] );
			$this->add_control( 'autoplay', [ 'label' => 'Autoplay', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
			$this->end_controls_section();

			$this->start_controls_section( 'style_section', [ 'label' => 'Card', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
			$this->add_control( 'card_bg', [ 'label' => 'Background', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => [ '{{WRAPPER}} .wt-rs__card' => 'background: {{VALUE}};' ] ] );
			$this->add_control( 'card_border', [ 'label' => 'Border', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#E7E9E5', 'selectors' => [ '{{WRAPPER}} .wt-rs__card' => 'border-color: {{VALUE}};' ] ] );
			$this->add_control( 'text_color', [ 'label' => 'Review text', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#3A3F3B', 'selectors' => [ '{{WRAPPER}} .wt-rs__q' => 'color: {{VALUE}};' ] ] );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'q_typo', 'label' => 'Review typography', 'selector' => '{{WRAPPER}} .wt-rs__q' ] );
			$this->end_controls_section();
		}

		protected function render() {
			$s    = $this->get_settings_for_display();
			$list = array_filter( (array) $s['reviews'], function ( $r ) { return trim( (string) $r['text'] ) !== '' && trim( (string) $r['name'] ) !== ''; } );
			$is_editor = current_user_can( 'edit_posts' ) || \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode();
			if ( 'yes' === ( $s['demo_mode'] ?? '' ) && ! $is_editor ) { $list = []; }
			if ( ! $list ) {
				echo '<p class="wt-reviews-empty">' . esc_html( $s['empty_text'] ) . '</p>';
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					echo '<p class="wt-admin-note">Editor note: add reviews in the left panel under <b>Reviews</b> &rarr; <b>+ Add Item</b>.</p>';
				}
				return;
			}
			$fallback = [ 65, 63, 64 ];
			echo '<div class="wt-rs" data-autoplay="' . ( 'yes' === $s['autoplay'] ? '1' : '0' ) . '"><div class="wt-rs__view" tabindex="0" aria-roledescription="carousel" aria-label="Customer reviews"><ul class="wt-rs__track">';
			$i = 0;
			foreach ( $list as $r ) {
				$n   = max( 1, min( 5, (int) $r['rating'] ) );
				$img = ! empty( $r['image']['id'] ) ? wp_get_attachment_image( $r['image']['id'], 'medium_large', false, [ 'class' => 'wt-rs__img', 'loading' => 'lazy', 'alt' => '' ] ) : ( ! empty( $r['image']['url'] ) ? '<img class="wt-rs__img" src="' . esc_url( $r['image']['url'] ) . '" alt="" loading="lazy">' : wp_get_attachment_image( $fallback[ $i % 3 ], 'medium_large', false, [ 'class' => 'wt-rs__img', 'loading' => 'lazy', 'alt' => '' ] ) );
				$av  = ! empty( $r['avatar']['url'] ) ? '<img class="wt-rs__av" src="' . esc_url( ! empty( $r['avatar']['id'] ) ? wp_get_attachment_image_url( $r['avatar']['id'], 'thumbnail' ) : $r['avatar']['url'] ) . '" alt="" loading="lazy">' : '<span class="wt-rs__av wt-rs__av--i" aria-hidden="true">' . esc_html( mb_substr( $r['name'], 0, 1 ) ) . '</span>';
				echo '<li class="wt-rs__slide"><figure class="wt-rs__card"><div class="wt-rs__l"><div class="wt-rs__stars" style="color:' . esc_attr( $r['star_color'] ?: '#2E8B57' ) . '" aria-label="' . esc_attr( $n . ' out of 5 stars' ) . '">' . str_repeat( '★', $n ) . '<span class="wt-rs__off">' . str_repeat( '★', 5 - $n ) . '</span></div><blockquote class="wt-rs__q">&ldquo;' . esc_html( $r['text'] ) . '&rdquo;</blockquote><figcaption class="wt-rs__who">' . $av . '<span><b>' . esc_html( $r['name'] ) . '</b><small>' . ( 'yes' === $r['verified'] ? 'Verified buyer' : 'Customer' ) . '</small></span></figcaption></div><div class="wt-rs__r">' . $img . '</div></figure></li>';
				$i++;
			}
			echo '</ul></div><div class="wt-rs__nav"><button type="button" class="wt-rs__btn wt-rs__prev" aria-label="Previous review"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg></button><div class="wt-rs__dots"></div><button type="button" class="wt-rs__btn wt-rs__next" aria-label="Next review"><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></button></div></div>';
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<script>window.wtRsInit&&document.querySelectorAll(".wt-rs:not([data-ready])").forEach(window.wtRsInit);</script>';
			}
		}
	}
	$widgets_manager->register( new WT_Review_Slider_Widget() );
} );
