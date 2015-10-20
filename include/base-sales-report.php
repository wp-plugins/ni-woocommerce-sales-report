<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
include_once('report-function.php'); 
if( !class_exists( 'BaseSalesReport' ) ) {
class BaseSalesReport extends ReportFunction{
 	 public function __construct(){
	 
	 //$this->print_data( $_REQUEST["page"] );
	 	
		
		
		add_action( 'admin_menu',  array(&$this,'register_my_custom_menu_page' ));
		
		if (isset($_REQUEST["page"])){
			 $page =  	$this->get_request("page");
			if ($page =="order-product" || $page =="sales-report")
		
			add_action( 'admin_enqueue_scripts',  array(&$this,'my_enqueue' ));
		}
		add_action( 'wp_ajax_sales_order',  array(&$this,'ajax_sales_order' )); /*used in form field name="action" value="my_action"*/
		add_action('admin_init', array( &$this, 'admin_init' ) );
    }
	function my_enqueue($hook) {
   		
		 wp_enqueue_script( 'ajax-script', plugins_url( '../assets/js/script.js', __FILE__ ), array('jquery') );
		 wp_enqueue_script( 'jquery-ui', plugins_url( '../assets/js/jquery-ui.js', __FILE__ ), array('jquery') );
		 
		 wp_register_style( 'jquery-ui', "//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css");
		
		 wp_enqueue_style( 'jquery-ui' );

		 
		 wp_register_style( 'sales-report-style', plugins_url( '../assets/css/sales-report-style.css', __FILE__ ));
		
		 wp_enqueue_style( 'sales-report-style' );


		 // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		 wp_localize_script( 'ajax-script', 'ajax_object',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
		
    }
	/*Ajax Call*/
	function ajax_sales_order()
	{
		$page= $this->get_request("page");
		if($page=="order-item")
		{	include_once('order-item.php');
			$obj = new OrderItem();  
			$obj->ajax_call();
		}
		die;
	}
	function register_my_custom_menu_page(){
   		add_menu_page('Sales Report','Sales Report','manage_options','sales-report',array(&$this,'AddMenuPage')
		,plugins_url( '../images/icon.png', __FILE__ )
		,8);
    	add_submenu_page('sales-report', 'Summary', 'Sales Summary', 'manage_options', 'sales-report' , array(&$this,'AddMenuPage'));
    	add_submenu_page('sales-report', 'Order Product', 'Order Product', 'manage_options', 'order-product' , array(&$this,'AddMenuPage'));
	}
	function AddMenuPage()
	{
		$page= $this->get_request("page");
		/*Order Item*/
		if($page=="order-product")
		{	include_once('order-item.php');
			$initialize = new OrderItem();  
			$initialize->create_form();
		}
		/*Order Item*/
		if($page=="sales-report")
		{	include_once('order-summary.php');
			$initialize = new Summary();  
		}
	}
	function admin_init()
	{
		if(isset($_REQUEST['btn_print'])){
			include_once('order-item.php');
			$obj = new OrderItem();
			$obj->get_print_content();
			die;
		}	
	}
	public function activation() {
      // To override
    }	
	 // Called when the plugin is deactivated
    public function deactivation() {
      // To override
    }
	 // Called when the plugin is loaded
    public function loaded() {
      // To override
    }
}
}
?>