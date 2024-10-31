=== retraced ===
Contributors:
Donate link:
Tags: tracing, supplychain, blockchain
Requires at least: 5.0
Tested up to: 5.6
Stable tag: 1.15.0
Requires PHP: 5.6.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically links your shop products with retraced and adds a tracing map widget to each product page.

== Description ==

This shop plugin adds a widget to each product page showing the supply chain and deep insights about the craftsmanship of the good.

about the products provenance links the products in your WooCommerce shop automatically to all products covered in [retraced](https://retraced.com) 🗺.

# about retraced

We are providing deep supply chain insights and impact analysis with a special focus on end consumers. The concept is also outlined on [retraced.com](https://retraced.com) 🗺.

🤔 As a consumer, have you ever wondered:

-   where the raw materials of the product come from?
-   what a specific certificate on a product means?
-   how many people were involved crafting the product?
-   ...

Then retraced is the solution. Ask your brand of choice to onboard the global movement to trace all items and show deep insights for everything you buy!

# Join retraced

If you got interested 🧐, just drop us an email: [hello@retraced.com](mailto:hello@retraced.com) ✉️

# How this plugin works

The linking is established automatically through the SKU of the product. With your company API key on hand, which you find on your [admin dashboard](https://dashboard.retraced.com).

Since there is no single specific product on a page, but rather a total product line, the item shown will be as specific as possible, but usually is just one example item retraced with the given SKU.

# Under the hood: Blockchain

All items are written down into a blockchain ⛓, which means they are temper proof audited and no possibility of manual intervention of manipulation can happen.

# Help

If you have any problems with the plugin, simply send us an [E-Mail](mailto:hello@retraced.com) ✉️ and we will get in touch with you soon.

== Installation ==

1. Upload `retraced` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Find out your company API key in the [retraced admin dashboard](https://dashboard.retraced.com) in your profile page.
4. Copy and paste the `Company API Key` to the wordpress plugin page.

== Frequently Asked Questions ==

= Can anyone just use plugin? =

Since product codes (SKUs) are not globally unique, we require the shop owner to be registered with retraced in order to link the items properly.

= Can I customise the widget? =

We add as much options as possible to the widget to adjust it for each shop individually. Please regard, that it must be ensured that users are able to recognise the traceability option throughout several shops which slightly limits the possibilities, but yet we offer everything necessary to fit into your corporate design.

= What happens if a product is not traced? =

Before a product page is opened, we do a very tiny call upfront evaluating if the product is traced. Even if the SKU is entered into the retraced platform, you may want to add the item to the shop already without having it manufactured. In these cases, we disable the widget (for this specific product). This is all done automatically.

= Can I request changes? =

We are very happy to learn about customization requests. We try to process them as quickly as possible, since only together we can provide end consumers a complete new shopping experience. Please get in touch: hello@retraced.com.

== Screenshots ==

1. The tracing widget in the product page
2. The product tracing overlay
3. The settings page for customizations of the plugin
4. The requried SKU in WooCommerce to match a retraced product

== Changelog ==

= 0.1.0 =

-   Initial release

== Upgrade Notice ==

_n/a_
