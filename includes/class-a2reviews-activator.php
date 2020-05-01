<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.gobliz.com/plugin/a2reviews
 * @since      1.0.0
 *
 * @package    A2reviews
 * @subpackage A2reviews/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    A2reviews
 * @subpackage A2reviews/includes
 * @author     A2reviews <os.solutionvn@gmail.com>
 */
class A2reviews_Activator {

	public static $option_key = 'a2reviews_options';

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static $default_settings = 'O:8:"stdClass":7:{s:14:"authentication";b:0;s:15:"widget_position";s:19:"Replace default tab";s:22:"widget_custom_position";i:9;s:14:"tab_label_mask";s:29:"Reviews ({% total_reviews %})";s:18:"remove_reviews_tab";b:1;s:21:"widget_total_position";i:2;s:25:"cat_widget_total_position";i:2;}'; 
	 
	public static function activate() {
		$settings = get_option( self::$option_key );
		
		if(!$settings){
			update_option( self::$option_key, $default_settings );
		}
		
		flush_rewrite_rules();
	}

}
