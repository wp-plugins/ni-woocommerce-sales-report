<?php 
if( !class_exists( 'ReportFunction' ) ) {
class ReportFunction{
	
	public function __construct(){
		
	}
	function get_request($request, $default = NULL)
	{
	 	$v = $_REQUEST[$request];
		$r = isset($v) ? $v : $default;
	 return $r;
	}
	function print_data($r)
	{
		echo '<pre>',print_r($r,1),'</pre>';	
	}
	function get_country_name($code)
	{
		$name= WC()->countries->countries[ $code];	
		$name  = isset($name) ? $name : $code;
		
		return $name;
	}
	function get_currency($default = "SYMBOL")
	{
		$r = '';
		 $currency_name = get_woocommerce_currency();
		 $symbol = get_woocommerce_currency_symbol($currency_name);
		 
		 if ($default=="NAME")
		 $r =  $currency_name;
		 if ($default=="SYMBOL")
		 $r =  $symbol;
		 
		 return $r;
	}
	
}
}
 new ReportFunction();  
?>