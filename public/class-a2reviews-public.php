<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.gobliz.com/plugin/a2reviews
 * @since      1.0.0
 *
 * @package    A2reviews
 * @subpackage A2reviews/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    A2reviews
 * @subpackage A2reviews/public
 * @author     A2reviews <os.solutionvn@gmail.com>
 */
class A2reviews_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * CDN of app.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $app_script = 'https://cdn.huzhop.com/a2/client-core/js/app.js';
	
	/**
	 * The domain of client site.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $domain = '';
	
	/**
	 * The regx find prop of snippets tag.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $regx 
	 */
	private $regx = '/{{\s*.*?\s*}}/m';
	
	/**
	 * The options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $options
	 */
	private $options;
	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$site_url = get_site_url();
		$domain = str_replace(['http://', 'https://', 'http://www.', 'https://www.'], '', $site_url);
		
		$this->domain = $domain;
		$this->options = get_option( 'a2reviews_options' );
		
		add_action( 'wp_head', array($this, 'head_script') );
		
		// Add a2reviews to body class
		add_filter( 'body_class', function( $classes ) {
		    return array_merge( $classes, array( 'a2reviews' ) );
		} );
		
		// Remove storefront sidebar
		add_action( 'get_header', function(){
			if ( is_woocommerce() || is_checkout() ) {
				remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
			}
		} );
		
		$this->add_snippets_code();
	}
	
	/**
     * A2 reviews variable init
     *
     * @param Request object $request Data.
     * @return void echo
     */
	public function head_script(){
		echo "<script>
			var A2_Reviews_Woo = {
				domain: '". $this->domain ."'
			}
		</script>";
	}
	
	/**
     * A2 reviews snippets code
     *
     * @param Request object $request Data.
     * @return JSON data
     */
    private  function snippets_code($type){
        $code = '<!-- A2 Reviews, Empty file content -->';

        if($type == 'widget'){
            $code = <<<A2
	<div class="a2reviews-wrapper">
	    <a2-reviews 
	        handle="{{ product_handle }}" 
	        lang="en">
	        A2 Reviews Widget
	    </a2-reviews>
	</div>
A2;
        }

        if($type == 'total'){
            $code = <<<A2
	<a2-reviews-total 
	    handle="{{ product_handle }}" 
	    total="{{ total_rating }}" 
	    avg="{{ avg_rating }}" 
	    lang="en"
	    is-scroll="false">
	</a2-reviews-total>
A2;
        }

        if($type == 'collection-total'){
            $code = <<<A2
	<a2-reviews-total
	    collection
	    handle="{{ product_handle }}" 
	    total="{{ total_rating }}" 
	    avg="{{ avg_rating }}" 
	    lang="en"
	    is-scroll="false">
	</a2-reviews-total>
A2;
        }
        
        if($type == 'questions-answers'){
	        $code = <<<A2
	<a2-questions handle="{{ product_handle }}"></a2-questions>
A2;
        }

        return $code;
    }
    
    /**
	 * Check is woocommerce plugin is active
	 *
	 * @since    1.0.0
	 */
    public function isWooCommerce(){
	    if(in_array( 
		    'woocommerce/woocommerce.php', 
		    apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) 
		)){
			return true;
		}else{
			return false;
		}
    }
    
    /**
	 * widget generate
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
    public function widget_generate($type){
	    global $post;
	    $post_id = isset($post->ID)? intval($post->ID): 0;
	    
	    $product_handle = isset($post->post_name)? $post->post_name: $post_id;
	    $total_rating 	= get_post_meta( $post_id, 'a2_meta_total_rating', true );
	    $avg_rating 	= get_post_meta( $post_id, 'a2_meta_avg_rating', true );
	    
	    $total_rating 	= $total_rating? $total_rating: 0;
	    $avg_rating 	= $avg_rating? $avg_rating: 0;
	    
	    $scode = $this->snippets_code($type);
	    preg_match_all($this->regx, $scode, $matches, PREG_SET_ORDER, 0);
	    
	    if(count($matches) > 0 ){
		    foreach($matches as $match){
			    if(isset($match[0]) && strpos($match[0], 'product_handle') !== false){
				    $scode = str_replace($match[0], $product_handle, $scode);
			    }
			    
			    if(isset($match[0]) && strpos($match[0], 'total_rating') !== false){
				    $scode = str_replace($match[0], $total_rating, $scode);
			    }
			    
			    if(isset($match[0]) && strpos($match[0], 'avg_rating') !== false){
				    $scode = str_replace($match[0], $avg_rating, $scode);
			    }
		    }
	    }

	    return $scode;
    }
    
    /**
	 * Add and register a2reviews snippet code
	 *
	 * @since    1.0.0
	 */
    public function add_snippets_code(){
	    $replace_default_reivews_tab = $this->options->widget_position === 'Replace default tab';
	    
	    //A2 reviews Wiget
	    if($replace_default_reivews_tab){
		    add_filter( 'woocommerce_product_tabs', function($tabs){
			    global $post;
			    
			    $post_id 			= isset($post->ID)? intval($post->ID): 0;
			    $total_rating 		= get_post_meta( $post_id, 'a2_meta_total_rating', true );
			    $total_rating 		= $total_rating? $total_rating: 0;
			    $tab_label_mask 	= isset($this->options->tab_label_mask)? $this->options->tab_label_mask: '';
			    
			    $re = '/{%\s*.*?\s*%}/m';
			    preg_match($re, $tab_label_mask, $matches, PREG_OFFSET_CAPTURE, 0);
			    
			    if(count($matches) > 0 ){
				    foreach($matches as $match){
					    if(isset($match[0]) && strpos($match[0], 'total_reviews') !== false){
						    $tab_label_mask = str_replace($match[0], $total_rating, $tab_label_mask);
					    }
				    }
			    }
			     
			    if($tab_label_mask == '') $tab_label_mask = 'Reviews';
			    
			    $tabs['a2_reviews_tab'] = array(
			        'title'     => __( $tab_label_mask, 'woocommerce' ),
			        'priority'  => 50,
			        'callback'  => function(){
				        echo $this->widget_generate('widget');
			        }
			    );
			    
			    unset($tabs['reviews']);
			
			    return $tabs;
		    }, 65 );
	    }else{
		    $widget_hook = 'woocommerce_after_single_product_summary';
		    $widget_priority = 10;

		    if($this->options->widget_custom_position == 9){
			    $widget_hook = 'a2reviews_widget';
		    }else{
			    switch ($this->options->widget_custom_position) {
				    case 1:
				        $widget_hook = 'woocommerce_before_single_product';
				        break;
				    case 2:
				        $widget_hook = 'woocommerce_after_single_product';
				        break;
				    case 3:
				        $widget_hook = 'woocommerce_before_single_product_summary';
				        break;
				    case 4:
				        $widget_hook = 'woocommerce_after_single_product_summary';
				        break;
				}
		    }

			add_action( $widget_hook , function(){
			    echo $this->widget_generate('widget');
			}, $widget_priority);
			
			if($this->options->remove_reviews_tab){
				add_filter( 'woocommerce_product_tabs', function($tabs){
					unset($tabs['reviews']);
					return $tabs;
				}, 65);
			}
	    }
	    
	    
	    //Wiget total
	    $total_single_hook_position = 'woocommerce_single_product_summary';
	    $widget_total_priority = 7;
	    
	    if($this->options->widget_total_position == 1){
		    $widget_total_priority = 3;
	    }
	    
	    add_action( $total_single_hook_position, function(){
		    echo $this->widget_generate('total');
	    }, $widget_total_priority);
	    
	    
	    //For loop page
	    $collect_total_hook_position = 'woocommerce_shop_loop_item_title';
	    $widget_cat_total_priority = 20;
	    
	    if($this->options->cat_widget_total_position == 1){
		    $widget_cat_total_priority = 3;
	    }
	    
	    add_action( $collect_total_hook_position, function(){
		    echo $this->widget_generate('collection-total');
	    }, $widget_cat_total_priority );
	    
	    //For questions an answers
	    $qa_widget_intab = $this->options->qa_position === 'In tab';
	    
	    if($qa_widget_intab){
		    add_filter( 'woocommerce_product_tabs', function($tabs){
			    global $post;
			    
			    $post_id 			= isset($post->ID)? intval($post->ID): 0;
			    $total_questions 	= get_post_meta( $post_id, 'a2_meta_total_questions', true );
			    $total_questions 	= $total_questions? $total_questions: 0;
			    $tab_qa_label_mask 	= isset($this->options->tab_qa_label_mask)? $this->options->tab_qa_label_mask: '';
			    
			    $re = '/{%\s*.*?\s*%}/m';
			    preg_match($re, $tab_qa_label_mask, $qa_matches, PREG_OFFSET_CAPTURE, 0);
			    
			    if(count($qa_matches) > 0 ){
				    foreach($qa_matches as $match){
					    if(isset($match[0]) && strpos($match[0], 'total_questions') !== false){
						    $tab_qa_label_mask = str_replace($match[0], $total_questions, $tab_qa_label_mask);
					    }
				    }
			    }
			    if($tab_qa_label_mask == '') $tab_qa_label_mask = 'Questions and Answers';
			    
			    $tabs['a2_qa_tab'] = array(
			        'title'     => __( $tab_qa_label_mask, 'woocommerce' ),
			        'priority'  => 60,
			        'callback'  => function(){
				        echo $this->widget_generate('questions-answers');
			        }
			    );
			    
			    return $tabs;
			}, 66 );
	    }else{
		    $widget_hook = 'woocommerce_after_single_product_summary';
		    $widget_priority = 10;

		    if($this->options->qa_custom_position == 9){
			    $widget_hook = 'a2_questions_answers_widget';
		    }else{
			    switch ($this->options->qa_custom_position) {
				    case 1:
				        $widget_hook = 'woocommerce_before_single_product';
				        break;
				    case 2:
				        $widget_hook = 'woocommerce_after_single_product';
				        break;
				    case 3:
				        $widget_hook = 'woocommerce_before_single_product_summary';
				        break;
				    case 4:
				        $widget_hook = 'woocommerce_after_single_product_summary';
				        break;
				}
		    }

			add_action( $widget_hook , function(){
			    echo $this->widget_generate('questions-answers');
			}, $widget_priority);
	    }
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in A2reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The A2reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/a2reviews-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in A2reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The A2reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name.'-cdn', $this->app_script.'?shop='.$this->domain , array(), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/a2reviews-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'a2reviews_settings', array( 'options' => $this->options ) );
	}

}
