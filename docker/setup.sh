#!/bin/sh
# Usage (from the plugin root):
#   docker compose -f docker/compose.yml run --rm setup            # install / repair
#   docker compose -f docker/compose.yml run --rm setup blocks     # checkout page uses the Checkout block
#   docker compose -f docker/compose.yml run --rm setup classic    # checkout page uses [woocommerce_checkout]
set -e

if [ ! -f wp-content/plugins/contribuinte-checkout/vendor/autoload.php ]; then
  echo "Run 'composer install' in the plugin folder first." >&2
  exit 1
fi

until [ -f wp-config.php ]; do sleep 2; done
until wp db check >/dev/null 2>&1; do sleep 2; done

checkout_id() { wp option get woocommerce_checkout_page_id; }

case "$1" in
  classic)
    wp post update "$(checkout_id)" --post_content='<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->'
    exit 0 ;;
  blocks)
    wp post update "$(checkout_id)" wp-content/uploads/checkout-block.html
    exit 0 ;;
esac

wp core is-installed || wp core install --url="$WP_URL" --title="Contribuinte Checkout" \
  --admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email

if ! wp plugin is-installed woocommerce; then
  wp plugin install woocommerce ${WC_VERSION:+--version=$WC_VERSION}
fi
wp plugin activate woocommerce contribuinte-checkout

wp rewrite structure '/%postname%/' --hard
wp option update woocommerce_default_country PT
wp option update woocommerce_currency EUR
wp option update woocommerce_coming_soon no
wp option update woocommerce_onboarding_profile '{"skipped":true}' --format=json
wp option update woocommerce_cod_settings '{"enabled":"yes","title":"Cash on delivery"}' --format=json
wp wc tool run install_pages --user=admin >/dev/null

# Keep the default (block) checkout content so "setup blocks" can restore it
[ -f wp-content/uploads/checkout-block.html ] || \
  wp post get "$(checkout_id)" --field=post_content > wp-content/uploads/checkout-block.html

# A shipping method makes the checkout show the shipping address form too
if [ -z "$(wp wc shipping_zone_method list 0 --user=admin --format=ids)" ]; then
  wp wc shipping_zone_method create 0 --method_id=flat_rate --enabled=true --user=admin >/dev/null
fi

if [ -z "$(wp post list --post_type=product --format=ids)" ]; then
  wp wc product create --name="Produto 10€" --regular_price=10 --user=admin >/dev/null
  wp wc product create --name="Produto 1500€" --regular_price=1500 --user=admin >/dev/null
fi

echo "Ready: $WP_URL (admin / admin) — WooCommerce $(wp plugin get woocommerce --field=version)"
