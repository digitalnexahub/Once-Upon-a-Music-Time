<?php
/**
 * Plugin Name: Washtower Elementor Widgets
 * Description: Native Elementor widgets for the WASHTOWER site (Product Carousel).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WT_ASSET_URL', content_url( 'uploads/wt-assets/' ) );
define( 'WT_ASSET_VER', '1.1.0' );

add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'wt-product-carousel', WT_ASSET_URL . 'wtpc.css', [], WT_ASSET_VER );
	wp_register_script( 'wt-product-carousel', WT_ASSET_URL . 'wtpc.js', [], WT_ASSET_VER, true );
} );

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
			$s     = $this->get_settings_for_display();
			$list  = $this->products( $s );
			$shop  = ! empty( $s['link']['url'] ) ? $s['link']['url'] : wc_get_page_permalink( 'shop' );
			$uid   = 'wtpc-' . $this->get_id();
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
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<script>window.wtpcInit&&document.querySelectorAll(".wtpc").forEach(function(r){r.removeAttribute("data-wtpc-ready");window.wtpcInit(r);});</script>';
			}
		}
	}

	$widgets_manager->register( new WT_Product_Carousel_Widget() );
} );
