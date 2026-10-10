# Washtower redesign (WordPress / WooCommerce)

Live on https://darkslategrey-donkey-246288.hostingersite.com

- **Stack:** Twenty Twenty-Five + WooCommerce + Elementor (home page).
- **Design layer:** `wp-content/novamira-sandbox/washtower-design.php` on the server.
  It adds the custom header (with a mega menu), footer, category hero, product trust panel and all styling.
- **Templates:** the `archive-product`, `taxonomy-product_cat`, `single-product` and `product-search-results`
  block templates use the WooCommerce legacy template, so the PHP hooks render.
- **Catalogue:** 5 categories and 10 products (with sale prices and image galleries) imported from washtower.com/en_IE.
- **Design:** warm off-white `#F5F2EC`, forest ink `#18302A`, terracotta accent `#C6623A`.
  Headings use Fraunces and body text uses Manrope. Cards have 22px corners.
