<?php
/**
 * Plugin Name: NotSale Woocommerce
 * Plugin URI: http://caiovinicius.org
 * Description: Define you Woocommerce show or not show price of products
 * Version: 0.2
 * Author: Caio Vinicius <para@caiovinicius.org>
 * Author URI: http://caiovinicius.org
 * License: GPLv2 or later
 *
 * Copyright 2014 Caio Vinicius  (email : para@caiovinicius.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Active Plugin
 */
function notsale_active() {

}
register_activation_hook(__FILE__, 'notsale_active');

/**
 * Desactive Plugin
 */
function notsale_desactive() {

}
register_deactivation_hook(__FILE__, 'notsale_desactive'); 

// register options plugin
function notsale_register_settings () {
	register_setting( 'notsale_settings_group', 'notsale_show_price_product' );
	register_setting( 'notsale_settings_group', 'notsale_show_cart_button' ); 
}

// Create page config plugin
function notsale_config_page() {
?>
	<div class="wrap">
		<h2><?php echo __('Show Price in Woocommerce');?></h2>

		<form method="post" action="options.php">
			<?php
				settings_fields('notsale_settings_group');
				do_settings_sections('notsale_settings_group');
			?>
			<table class="form-table">
				<tr>
					
						<h3><?php echo __('Settings Show Price Woocommerce', 'notPrice');?></h3>
					
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php echo __('Show Price for only users logged', 'notPrice');?>:
					</th>
					<td>
						<select name="notsale_show_price_product" id="notsale_show_price_user_logged_product">
							<option value="1"><?php echo __('Yes', 'notPrice');?></option>
							<option value="2" <?php if (get_option('notsale_show_price_product') == 2) echo 'selected="selected"';?>><?php echo __('No', 'notPrice');?></option>

						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php echo __('Show Cart button', 'notPrice');?>:
					</th>
					<td>
						<select name="notsale_show_cart_button" id="notsale_show_cart_button">
							<option value="1"><?php echo __('Show Cart button usually', 'notPrice');?></option>
							<option value="2" <?php if (get_option('notsale_show_cart_button') == 2) echo 'selected="selected"';?>><?php echo __('Show Cart button only list products', 'notPrice');?></option>
							<option value="3" <?php if (get_option('notsale_show_cart_button') == 3) echo 'selected="selected"';?>><?php echo __('Show Cart Button in single products page only', 'notPrice');?></option>
							<option value="4" <?php if (get_option('notsale_show_cart_button') == 4) echo 'selected="selected"';?>><?php echo __('Show Cart button anywhere', 'notPrice');?></option>

						</select>
					</td>
				</tr>

			</table>
			<?php submit_button(); ?>
		</form>
	</div><!--- wrap -->

<?php
}

 // Add page settings of Wordle
add_action('admin_menu', 'notsale_page_settings');

// Create page menu settings Wordle
function notsale_page_settings() {

	add_options_page(__('Config Prices Woocommerce', 'notPrice'), __('Config Prices Woocommerce', 'notPrice'), 'manage_options', 'notsale_config_page', 'notsale_config_page');

	add_action('admin_init', 'notsale_register_settings');
}

/**
 * Woocommerce Filters
 */

// show price user logged
function show_price_only_legged_users ($price){
	
	if(is_user_logged_in() ){
    		return $price;
	}
	else 
	{
		return '<a href="' .get_permalink(woocommerce_get_page_id('myaccount')). '">'. __('Login', 'notPrice') . '</a> '. __('or', 'notPrice') . ' <a href="'.site_url('/wp-login.php?action=register&redirect_to=' . get_permalink()).'">'. __('Register', 'notPrice') . '</a> '. __('to see price', 'notPrice') . '!';
	}	
}

// remove cart button list 
function notsale_cart_button_list(){

	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}

// remove cart button single
function notsale_cart_button_single(){

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	$showPriceUserLogged = get_option('notsale_show_price_product');
	$showCart = get_option('notsale_show_cart_button');
	if($showPriceUserLogged == 1 )
	{	
		// show link to login or regiter for see price
		add_filter('woocommerce_get_price_html','show_price_only_legged_users');

		add_action('init','notsale_cart_button_list');
		add_action('init','notsale_cart_button_single');
	}


	if($showCart == 1)
	{
		# show all
	}
	elseif($showCart == 2)
	{
		add_action('init','notsale_cart_button_single');
	}
	elseif($showCart == 3)
	{
		add_action('init','notsale_cart_button_list');
	}
	elseif($showCart == 4)
	{
		add_action('init','notsale_cart_button_single');
		add_action('init','notsale_cart_button_list');
	}

  
}
