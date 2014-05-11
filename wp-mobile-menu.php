<?php

/*
Plugin Name: Responsive Burger Menu
Plugin URI: http://www.techtalkshq.com/
Description: Transform your default WordPress menu into a responsive and modern app-like mobile menu (Burger Menu) similar to Facebook. Configuration Options available at <strong><em>Settings</strong> > <strong>OD Mobile Menu</em></strong>
Version: 1.7.2
Author: Freddie Lore
Author URI: http://techtalkshq.com/
License:
*/

require_once( plugin_dir_path(__FILE__) . '/lib/wp_auto_update.php');

if (!class_exists('OracleDigital_Mobile_Menu')) {
 
	class OracleDigital_Mobile_Menu {

		public function __construct() {
			
			// Load the auto-update class
			add_action('init', array(&$this, 'od_burger_menu_activate'));
			add_action('wp_head', array(&$this, 'init_od_mobile_menu'));
			add_action('wp_head', array(&$this, 'init_od_mobile_menu_scripts'));
			add_action('wp_head', array(&$this, 'add_mobile_menu_header'));

			// create custom plugin settings menu
			add_action('admin_menu', array(&$this, 'od_mobile_menu'));	
			add_action( 'admin_enqueue_scripts', array(&$this, 'od_mmenu_enqueue_custom_scripts_css' ));
			add_action('wp_footer', array(&$this, 'od_mobile_menu_css'));

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this,'od_mobile_menu_settings_link' ));
		}
		
		function od_burger_menu_activate(){
			$od_mmenu_plugin_current_version = '1.7.2';
		    $od_mmenu_plugin_remote_path = 'http://techtalkshq.com/plugin-api/wp-mobile-menu/wp-mobile-menu.php';
		    $od_mmenu_plugin_slug = plugin_basename(__FILE__);
		    new wp_auto_update ($od_mmenu_plugin_current_version, $od_mmenu_plugin_remote_path, $od_mmenu_plugin_slug); 
		}	
		
		function init_od_mobile_menu(){ 
			define('OD_MOBILE_MENU_FILE', __FILE__);
			define('OD_MOBILE_MENU_PATH', plugin_dir_url(__FILE__));
		} 

		function init_od_mobile_menu_scripts(){
			
			wp_enqueue_script('od_mobile_menu_js', OD_MOBILE_MENU_PATH . '/js/mmenu.js');
			wp_enqueue_script('od_mobile_detect_js', OD_MOBILE_MENU_PATH . '/js/detectmobilebrowser.js');
			
			//extract options
			$s = ( get_option('od_mobile_search_label') == '' ) ? 'true' : 'false';
			$breakpoint = ( get_option('od_mobile_breakpoint') ) ? get_option('od_mobile_breakpoint') : '767px';
			
			printf('<script type="text/javascript">
						jQuery(document).ready(function($){
							$(".menu").parent().attr("id", "mainNav");
							$("#mainNav").mmenu({
									searchfield	: %s
								}, {
									clone: true
								}
							);
						});
					</script>', $s);
			
			wp_enqueue_script('od_mobile_detect_custom_js', OD_MOBILE_MENU_PATH . '/js/custom.js');
			wp_enqueue_style('od_mobile_menu_css', OD_MOBILE_MENU_PATH . '/css/odmobilemenu.css'); 
			
			printf( '<link rel="stylesheet" href="' . OD_MOBILE_MENU_PATH . '/css/custom.css" type="text/css" media="only screen and (max-width: %s)" />', $breakpoint );	
			
			if ( is_user_logged_in() ) { ?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						$("html").addClass('admin');
					});
				</script>
			<?php }  ?>
		
		<?php

		}

		function add_mobile_menu_header(){
			if( get_option('od_mobile_phone_number_link') || get_option('od_mobile_phone_number_link') != '' ){
				$phone = ( get_option('od_mobile_phone_number_link') ) ? get_option('od_mobile_phone_number_link') : '#';
			}
			else{
				$phone = ( get_option('od_mobile_phone_number') ) ? 'tel:' . get_option('od_mobile_phone_number') : '#';
			}

			$onclick = ( get_option('od_mobile_phone_number_onclick') ) ? get_option('od_mobile_phone_number_onclick') : '';
			$onclick = str_replace('"', "'", $onclick);

			$phone_label = ( get_option('od_mobile_phone_label') ) ? get_option('od_mobile_phone_label') : 'Call Us';
			echo '<div id="mobile-header" style="display: none;"><a href="#mainNav" class="left"></a><span><a href="#mainNav">Menu</a></span><a href="'.$phone.'" class="right" onclick="' . $onclick . '">' . $phone_label . '</a></div>';
		}

		
		function od_mobile_menu() {
			//create new top-level menu
			add_submenu_page('options-general.php', 'Mobile Menu Settings', 'OD Mobile Menu', 'administrator', 'od-mobile-menu', array(&$this,'od_mobile_menu_settings_page'));
			
			//call register settings function
			add_action( 'admin_init', array(&$this, 'od_mobile_menu_settings') );
		}

		function od_mobile_menu_settings() {
			//register our settings
			register_setting( 'od-mobile-menu-settings-group', 'od_mobile_bg_color' );
			register_setting( 'od-mobile-menu-settings-group', 'od_mobile_phone_number' );
			register_setting( 'od-mobile-menu-settings-group', 'od_mobile_phone_number_link' );
			register_setting( 'od-mobile-menu-settings-group', 'od_mobile_phone_number_onclick' );
			register_setting( 'od-mobile-menu-settings-group', 'od_mobile_phone_label' );
			register_setting( 'od-mobile-menu-settings-group', 'od_mobile_float_label' );
			register_setting( 'od-mobile-menu-settings-group', 'od_mobile_breakpoint' );
		}

		function od_mmenu_enqueue_custom_scripts_css($hook) {
		    wp_register_style( 'custom_od_mmenu_wp_admin_css', plugin_dir_url( __FILE__ ) . 'css/admin-style.css' );
		    wp_enqueue_style( 'custom_od_mmenu_wp_admin_css' );
			wp_register_script( 'custom_od_mmenu_wp_admin_js', plugin_dir_url( __FILE__ ) . 'js/admin-js.js' );
		    wp_enqueue_script( 'custom_od_mmenu_wp_admin_js' );	
		}

		// Add settings link on plugin page
		function od_mobile_menu_settings_link($links) { 
		  $settings_link = '<a href="options-general.php?page=od-mobile-menu">Settings</a>'; 
		  array_unshift($links, $settings_link); 
		  return $links; 
		}
		 
		function od_mobile_menu_settings_page() { ?>
			<div class="wrap">
				<h2>Mobile Menu Settings</h2>
				<form method="post" action="options.php">
					<?php settings_fields( 'od-mobile-menu-settings-group' ); ?>
					<?php //do_settings( 'od-mobile-menu-settings-group' ); ?>
					<table class="form-table">
					<tr valign="top">
						<th scope="row">
							Mobile Menu Background Color:
						</th>
						<td>
							<label for="od_mobile_bg_color">
							<input type="text" id="od_mobile_bg_color" name="od_mobile_bg_color" size="30" value="<?php echo get_option('od_mobile_bg_color'); ?>" /> </label>
							<span class="description">Default: #BDBDBD</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							'Tap to Call' Phone Number:
						</th>
						<td>
							<label for="od_mobile_phone_number">
								<input type="text" id="od_mobile_phone_number" name="od_mobile_phone_number" size="30" value="<?php echo get_option('od_mobile_phone_number'); ?>" />
							</label>
							<?php
								$phone_custom_link_label = (get_option('od_mobile_phone_number_link') != '' ) ? 'Hide options' : 'Show options';
								$phone_custom_link_style = (get_option('od_mobile_phone_number_link') != '' ) ? 'display: block;' : 'display: none;';
								$phone_custom_link_class = (get_option('od_mobile_phone_number_link') != '' ) ? 'od-shown' : 'od-hidden';
							?>
							<span class="description"><a id="od-menu-options" class="<?php echo $phone_custom_link_class; ?>"  href="#"><?php echo $phone_custom_link_label; ?></a></span>
							<label for="od_mobile_phone_number_link" id="phone-custom-link" style="<?php echo $phone_custom_link_style; ?>">
								<span class="description">Enter full URL below to disable "tap to call" functionality &amp; point the user to specific page when it's clicked. Leave this field empty if you want to activate it back.</span>
								<input type="text" id="od_mobile_phone_number_link" name="od_mobile_phone_number_link" size="30" value="<?php echo get_option('od_mobile_phone_number_link'); ?>" />
								<span class="description">Add <strong style="font-weight: bold;">onclick</strong> attribute (useful for call conversion tracking)</span>
								<input type="text" id="od_mobile_phone_number_onclick" name="od_mobile_phone_number_onclick" size="30" value="<?php echo get_option('od_mobile_phone_number_onclick'); ?>" />
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							'Tap to Call' Label:
						</th>
						<td>
							<label for="od_mobile_phone_label">
							<input type="text" id="od_mobile_phone_label" name="od_mobile_phone_label" size="30" value="<?php echo get_option('od_mobile_phone_label'); ?>" /> <span class="description">Default: 'Call Us'</span>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							CSS Breakpoint:
						</th>
						<td>
							<label for="od_mobile_breakpoint">
							<input type="text" id="od_mobile_breakpoint" name="od_mobile_breakpoint" size="30" value="<?php echo get_option('od_mobile_breakpoint'); ?>" /> <span class="description">Default: 767px</span>
							</label>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<strong>DO NOT</strong> float the menu at the top
						</th>
						<td>
							<label for="od_mobile_float_label">
							<input type="checkbox" id="od_mobile_float_label" name="od_mobile_float_label" size="30" value="<?php echo get_option('od_mobile_float_label'); ?>" <?php echo ( get_option('od_mobile_float_label') == 'true' ) ? 'checked' : ''; ?>
							/> <span class="description">Leave unticked if you want the menu to be constantly fixed on top</span>
							</label>
						</td>
					</tr>
					</table>
					<?php submit_button(); ?>
				</form>

				<div class="od-mobile-menu">
					<h3>Live Preview</h3>
					<div class="simulator">
						<div id="iphone-scrollcontainer">
							<div id="iphone-inside">
								<iframe width="100%" height="100%" src="<?php echo site_url(); ?>"></iframe>
							</div><!-- end #iphone-inside -->		
						</div><!-- end #iphone-scrollcontainer -->	
					</div><!-- end .simulator -->
				</div><!-- end .od-mobile-menu -->

			</div>
		<?php 
		}

		function od_mobile_menu_css(){

			$b = ( get_option('od_mobile_bg_color')=='' ) ? '#BDBDBD' : get_option('od_mobile_bg_color');
			$p = ( get_option('od_mobile_float_label')=='' ) ? 'fixed' : 'absolute';
			
			if( get_option('od_mobile_bg_color') ){

				print('<style type="text/css">
							#mobile-header{ 
								background: ' . $b . '!important; 
								position: ' . $p . '!important; 
							}
					  </style>');

			}

		}

	}
}

new OracleDigital_Mobile_Menu();

?>