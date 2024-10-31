<?php
/**
 * Plugin Name:     retraced
 * Plugin URI:      
 * Description:     A widget that helps consumer see history of the product. Transparently.
 * Author:          retraced    
 * Author URI:      https://retraced.co
 * Text Domain:     retraced-button-woocommerce
 * Domain Path:     /languages
 * Version:         1.15.0
 *
 * @package         retraced_Plugin
 */

if(!defined('RETRACED_PLUGIN_VERSION'))
    define('RETRACED_PLUGIN_VERSION', '1.15.0');

if(!defined('RETRACED_PATH'))
    define('RETRACED_PATH', plugin_dir_path( __FILE__ ));

if ( file_exists( RETRACED_PATH . 'retraced-config.php' ) ) {
    include( RETRACED_PATH . 'retraced-config.php' );
}
else{
    wp_die( __('The retraced config file is missing. Please contact retraced support at hello@retraced.co') );
}


class RetracedWidget {    

    public function __construct(){
        

        // Activate or Deactivate plugin
        register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );

        // Add setting page option
        add_action ( "admin_menu", array($this, 'retraced_add_menu') );        

        // Add widget javascript to header
        add_action( "wp_head", array($this, 'retraced_widget_script') );
        // Add sku listener to the footer
        add_action( "wp_footer", array($this, 'retraced_sku_listener'), 100 );
        

        add_action ( "admin_init", array($this, 'retraced_check_woocommerce' ), 20);   
        
        add_action ( "admin_init", array($this, 'retraced_settings' ));                

        // Add setting link on plugins page
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'retraced_widget_add_setting_link'));
    }

    function _activate(){        
        // Make sure any rewrite functionality has been loaded
        flush_rewrite_rules();        
    }


    
    function _deactivate(){
        try{           

            delete_option('retraced_order_webhook_id');  
            delete_option('retraced_webhook_deliveries_set_limit');
            delete_option('retraced_api_key');

        }catch(Exception $e){

        }
    }


    public function retraced_check_woocommerce(){
        if(!$this->check_woocommerce_version()){
            add_action( 'admin_notices', array($this, 'woocommerce_plugin_notice') );

            deactivate_plugins( plugin_basename( __FILE__ ) ); 
    
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
        
    }

    public function woocommerce_plugin_notice(){
        ?>
        <div class="error">
            <p>
                This plugin requires active Woocommerce version at least 3.0.
            </p>
        </div>
        <?php
    }



    public function retraced_add_menu(){
        add_menu_page('retraced', 'retraced', 'manage_options', 'retraced-settings', array($this, 'retraced_page'), 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACXBIWXMAAAsTAAALEwEAmpwYAAABWWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyI+CiAgICAgICAgIDx0aWZmOk9yaWVudGF0aW9uPjE8L3RpZmY6T3JpZW50YXRpb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgpMwidZAAACf0lEQVQ4EX1STYhSURR++hxNURfmwvzNf/MXFUwXkos2blo02aJN0KrNbBpiNjPgtIhhoIJqEbSoBmZluIlwOdIsy5GEIlPKGNyJII6If8++c3mPpiE6cN853z3nffe75x4Z98cUCOcEXS7Xxmw2u7pcLpWE5XL5iUqlKrVardeEYXIsgQIZfWA81iKdTl/sdrvvZDLZJ/zwQqvV/phOpzzIwuPxeB1EylQqlS+VSlPU/02Sz+f1dru9HQgEVonxXwZl2w6H41DMSYdzJJ1DYs/tdm+JSa3oTzs1AdS99Xg898SEgmTMIeu8IAiXfD7fLjDtncRisbjVan0GVTvZbPYC9sZYnF6v355MJtcphi2omBsMBjE07LhSqUwAhVAodK3f75fRhyb6Me10OoeJRMJDtZlM5tsCFo/HzYBLJh9FGhBQY5iBcMtgMKw2Go0j2vB6vd1er7eD8IbZbF6gnjhUlGMK5vP5T2wSI5dMJo1E5vf7PwOu0J5GoznAFY0Ul8tlM2rP4YBjwkQgb7fbX+D5YDB4uVar9XieH9brdbrnDIsbDod3FAoFEXKj0egW8l+r1SrNzAojoIRSqdxH8j7FOPEBGvWQYiiyQ+FNi8WySRhzcVun0z2iGLaQ3pL8Eh0/UqvV681m8wDPtYuTjLirE+RvaAqdTudTmk409S7q6XpMITybRC4SiVwBCV2HmclkEoCfEAiHwzmbzfa9WCyyvmFLOpzV0oc1DNP2GCftwe9jDp5TolAoaPHzr2g0miacy+XY61F81hg7ij/guV5JSaj4iKdcEzE7SMqd9UzWKZk0uu+hiClB8X9/lsiIhBFh2jIY75diQrq7VMf8b8yw2CMb19iAAAAAAElFTkSuQmCC');
    }



    public function retraced_widget_script(){
        if(!empty(get_option( 'retraced_status' ))){    
            $retraced_data = $this->get_shop_data();
            $initialSku = "";
            $variants = array();
            $locale = get_locale();
            $detected_locale = '';
            if(!empty($locale)){
                $detected_locale = substr($locale, 0, 2);
            }
            if(! empty($retraced_data)){
                $initialSku = $retraced_data['sku'];
                $variants = $retraced_data['variants'];
            }        
            ?>            
            <script name="retraced-tag">                
                window.retraced = {
                    shop: "<?php echo $this->get_shop_url(); ?>",
                    initialSku: "<?php echo $initialSku; ?>",
                    variants: <?php echo json_encode($variants); ?>,
                    locale: "<?php echo $detected_locale; ?>" || (window.navigator ? window.navigator.language : "" || "en")
                };                
            </script>
            <script async src="<?php echo RETRACED_WIDGET_SCRIPT; ?>"></script>
            <?php
        }
    }

    public function retraced_sku_listener(){
		?>            
		<script name="retraced-sku-tag">  			
			(function($){
				$('form.variations_form').on('show_variation', function(event, variation){
					if (window.history.replaceState) {					 
						var params = new URLSearchParams(window.location.search);
						params.set('variant', variation.variation_id);
						window.history.replaceState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
					}
				});
			})(jQuery);
		</script>
		<?php
	}

    public function retraced_widget_add_setting_link($links){
        if($this->check_woocommerce_version())
        {
            $links = array_merge(array(
                '<a href="' .
                        admin_url( 'admin.php?page=retraced-settings' ) .
                        '">' . __('Settings') . '</a>'
            ), $links);
            return $links;
        }

        return $links;
    }

    private function get_shop_data(){
        global $woocommerce; 
        global $product;

        if(function_exists('wc_get_product')){
            $product = wc_get_product(get_the_ID());
        }
        else{
            $product = null;
        }
        $shop_product = array();

        if ($product) {
            if($product->is_type('variable')) {
                $variations = $product->get_available_variations();
                $shop_product = array(
                    'sku' => count($variations) > 0 ? $variations[0]['sku'] : '',
                    'id' => $product->get_id(),
                    'title' => $product->get_name(),
                    'handle' => $product->get_slug(),
                    'variants' => array_map(function($v) {
                        return array(
                            'id' => $v['variation_id'],
                            'sku' => $v['sku']
                        );
                    }, $variations)
                );
            }
            else{
                $shop_product = array(
                    'sku' => $product->get_sku(),
                    'id' => $product->get_id(),
                    'title' => $product->get_name(),
                    'handle' => $product->get_slug(),
                    'variants' => array(array(
                        'id' => $product->get_id(),
                        'sku' => $product->get_sku()
                    ))
                );
            }
        }

        return  $shop_product;
    }


    public function get_shop_url(){
        $protocols = array('https://', 'http://', 'https://www.', 'http://www', ':443', ':80');
        return str_replace($protocols, '', get_site_url());
    }


    public function retraced_page() {
        if (!current_user_can('manage_options'))
       {
         wp_die( __('You do not have sufficient permissions to access this page.') );
       }
        ?>
       <div class="wrap">           
           <form method="post" action="options.php">
                <?php if(!empty( $_GET['settings-updated'] )){ ?>
                    <div id="message" class="updated">
                        <p>
                            <strong>
                                Settings saved.
                            </strong>
                        </p>
                    </div>
                <?php } ?>
                <h3>
                    Retraced Settings 
                </h3>
                <p>
                    Please contact us at <a href="mailto:hello@retraced.com">hello@retraced.com</a> after you activate the plugin for final integration.
                </p>
               <?php
               settings_fields ( "retraced_config" );
               do_settings_sections ( "retraced-config-page" );
               $attributes = array( 'data-style' => 'custom' );
               submit_button ( 'SAVE', 'primary mystyle', 'submit', true, $attributes );
               ?>   
                
           </form>
       </div>
   
       <?php
    }



    /**
     * Init setting section: init setting field and register setting field
     */

    public function retraced_settings(){        
        add_settings_section("retraced_config", "", null, "retraced-config-page");
        add_settings_field ( "retraced_status", "Active status?", array($this, "retraced_option_status"), "retraced-config-page", "retraced_config" );
        register_setting ( "retraced_config", "retraced_status" );
    }

    public function retraced_option_status(){
        ?>
        <input type="checkbox" name="retraced_status"
            value="1" <?php checked(1, get_option('retraced_status'), true); ?> />
        <?php
     }
    

    private function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
    }
    
    private function check_woocommerce_version($version = '3.0'){        
        if($this->is_woocommerce_activated()){
            global $woocommerce;
            if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
                return true;
            }
        }

        return false;
    }
   
    
}

if(!function_exists('get_retraced_data')) {
    function get_retraced_data() {
        global $woocommerce; 
        global $product;

        if (!is_object($product)) {
            if(function_exists('wc_get_product')){
                $product = wc_get_product(get_the_ID());
            }
            else{
                $product = null;
            }
        }

        $shop_product = array();

        if ($product) {
            if($product->is_type('variable')) {
                $variations = $product->get_available_variations();
                $shop_product = array(
                    'sku' => count($variations) > 0 ? $variations[0]['sku'] : '',
                    'id' => $product->get_id(),
                    'title' => $product->get_name(),
                    'handle' => $product->get_slug(),
                    'variants' => array_map(function($v) {
                        return array(
                            'id' => $v['variation_id'],
                            'sku' => $v['sku']
                        );
                    }, $variations)
                );
            }
            else{
                $shop_product = array(
                    'sku' => $product->get_sku(),
                    'id' => $product->get_id(),
                    'title' => $product->get_name(),
                    'handle' => $product->get_slug(),
                    'variants' => array(array(
                        'id' => $product->get_id(),
                        'sku' => $product->get_sku()
                    ))
                );
            }
        }

        return  $shop_product;
    }
}

new RetracedWidget();