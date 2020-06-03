<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.gobliz.com/plugin/a2reviews
 * @since      1.0.0
 *
 * @package    A2reviews
 * @subpackage A2reviews/shortcode
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    A2reviews
 * @subpackage A2reviews/shortcode
 * @author     A2reviews <os.solutionvn@gmail.com>
 */
class A2reviews_Shortcode {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct(  ) {
		add_shortcode( 'a2reviews-block', [&$this, 'block'] );
	}
	
	public function block( $atts ) {
	    $atts = shortcode_atts( array(
	        'id' => 0,
	        'name' => 'Home Block',
	        'show_loading' => true
	    ), $atts, 'a2reviews-block' );
	    
	    $loading_place = '';
	    $show_loading = '';
	    
	    if($atts['show_loading']){
		    $loading_place = '<div style="text-align: center;">Loading...</div>';
		    $show_loading = 'show-loading';
	    }
	 
	    return '<a2reviews-block '.$show_loading.' block-id="'. esc_attr($atts['id']) .'" name="'. esc_attr($atts['name']) .'">'.$loading_place.'</a2reviews-block>';
	}
	
}

new A2reviews_Shortcode();