<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
include_once('report-function.php');  
class OrderItem extends ReportFunction{
  	public function __construct(){
	}
	
	function ajax_call()
	{
		$ajax_function= $this->get_request("ajax_function");
		if($ajax_function=="order_item")
		{ ?>
          <div class="wrap">
          	<?php $this->display_order_item();?>
          </div>
          <?php	
		}
	}
	/*On Page Start Create The Form*/
	function create_form()
	{
		 $today = date("Y-m-d");
	?>
    <form id="frmOrderItem" method="post" class="frm-order-item" >
    	<table>
        	<tr>
            	<td>Select Order</td>
                <td><select name="select_order" id="select_order">
                      <option value="today">Today</option>
                      <option value="yesterday">Yesterday</option>
                      <option value="last_7_days">Last 7 days</option>
                      <option value="last_30_days">Last 30 days</option>
                      <option value="this_year">This year</option>
                    </select>
					</td>
      		</tr>
            <tr>
            	<td colspan="4"  style="text-align:right; visibility:hidden" ><input type="submit" value="Search" id="SearchOrder" /></td>
            </tr>
        </table>
			

			
			
	</form>
		<div class="ajax_content"></div>
		<?php	
	}
	function display_order_item()
	{  
		$item_total = 0;
		$tax_total  = 0;
		$qty		=0;
		$order_item=$this->get_order_item();
		//$this->print_data($order_item);
		if(count($order_item)> 0){
			?>
            <div class="data-table">
			<table >
            	<tr>
                	<th>#ID</th>
                    <th>Order Date</th>
                    <th>Billing First Name</th> 
                    <th>Billing Email</th> 
                    <th>Billing Country</th> 
                    <th>Order Currency</th> 
                    <th>Payment Method Title</th> 
                    <th>Order Status</th>
                    <th>Product Name</th>
                    <th>Qty.</th> 
                    <th>Price.</th> 
                    <th>Line Tax</th> 
                    <th>Line Total</th>   
                </tr>
            
			<?php
			foreach($order_item as $k => $v){
			$item_total += $v->line_total;
			$tax_total 	+= $v->line_tax;
			$qty 		+= $v->qty;
			?>
				<tr>
                	<td> <?php echo $v->order_id;?> </td>
                    <td> <?php echo $v->order_date;?> </td>
                    <td> <?php echo $v->billing_first_name;?> </td>
                    <td> <?php echo $v->billing_email;?> </td>
                    <td> <?php echo $this->get_country_name($v->billing_country);?> </td>
                    <td> <?php echo $v->order_currency;?> </td>
                    <td> <?php echo $v->payment_method_title;?> </td>
                    <td> <?php echo ucfirst ( str_replace("wc-","", $v->order_status));?> </td>
                    <td> <?php echo $v->order_item_name;?> </td>
                   	<td style="text-align:right"> <?php echo $v->qty;?> </td>
                    <td style="text-align:right"> <?php echo  woocommerce_price($v->line_total/$v->qty);?> </td>
                    <td style="text-align:right"> <?php echo  woocommerce_price($v->line_tax);?> </td>
                    <td style="text-align:right"> <?php echo  woocommerce_price($v->line_total);?> </td>
                    
                 
                </tr>	
			<?php
			}
			?>
            	<!--<tr>
                	<td colspan="10" style="text-align:right;">Total</td>
                	<td  style="text-align:right"><?php echo $item_total ?></td>
                    <td  style="text-align:right"><?php echo $tax_total; ?></td>
                </tr>-->
            </table>
           
            	<div style="text-align:right; padding-top:10px; font-weight:bold"> Qty Total: <?php echo $qty ?> | Tax Total:  <?php echo woocommerce_price( $tax_total) ?> | Product Total: <?php echo woocommerce_price($item_total) ?> </div>
            
            </div>
           <?php
		}
	}
	function get_order_item()
	{	$order_data =$this->get_query_data("DEFAULT");
		if(count($order_data)> 0){
			foreach($order_data as $k => $v){
				
				/*Order Data*/
				$order_id =$v->order_id;
				$order_detail = $this->get_order_detail($order_id);
				foreach($order_detail as $dkey => $dvalue)
				{
						$order_data[$k]->$dkey =$dvalue;
					
				}
				/*Order Item Detail*/
				$order_item_id = $v->order_item_id;
				$order_item_detail= $this->get_order_item_detail($order_item_id );
				foreach ($order_item_detail as $mKey => $mValue){
						$new_mKey = $str= ltrim ($mValue->meta_key, '_');
						$order_data[$k]->$new_mKey = $mValue->meta_value;		
				}
			}
		}
		else
		{
			echo "No Record Found";
		}
		return $order_data;
	}
	function get_query_data($type="DEFAULT")
	{
		global $wpdb;	
		$today = date("Y-m-d");
	    $select_order = $this->get_request("select_order");
		
		
		$query = "SELECT
				posts.ID as order_id
				,posts.post_status as order_status
				,woocommerce_order_items.order_item_id as order_item_id
				, date_format( posts.post_date, '%Y-%m-%d') as order_date 
				,woocommerce_order_items.order_item_name
				FROM {$wpdb->prefix}posts as posts			
				LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items ON woocommerce_order_items.order_id=posts.ID 
				
				WHERE 
						posts.post_type ='shop_order' 
						AND woocommerce_order_items.order_item_type ='line_item'
						
						";
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";		
					//AND DATE_ADD(CURDATE(), INTERVAL 1 day)	
				 switch ($select_order) {
					case "today":
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
						break;
					case "yesterday":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') = date_format( DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d')";
						break;
					case "last_7_days":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 7 DAY), '%Y-%m-%d') AND   '{$today}' ";
						break;
					case "last_30_days":
							$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d') AND   '{$today}' ";
						break;	
					case "this_year":
						$query .= " AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(CURDATE(), '%Y-%m-%d'))";			
						break;		
					default:
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
				}
			$query .= "order by posts.post_date DESC";	
				//AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '2014-10-21' AND '2014-10-22'
		 if ($type=="ARRAY_A") /*Export*/
		 	$results = $wpdb->get_results( $query, ARRAY_A );
		 if($type=="DEFAULT") /*default*/
		 	$results = $wpdb->get_results( $query);	
		 if($type=="COUNT") /*Count only*/	
		 	$results = $wpdb->get_var($query);		
			//echo $query;
			echo mysql_error();
		return $results;	
	}
	function get_order_item_detail($order_item_id)
	{
		global $wpdb;
		$sql = "SELECT
				* FROM {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta			
				WHERE order_item_id = {$order_item_id}
				";
				
		$results = $wpdb->get_results($sql);
		return $results;			
	}
	function get_order_detail($order_id)
	{
		$order_detail	= get_post_meta($order_id);
		$order_detail_array = array();
		foreach($order_detail as $k => $v)
		{
			$k =substr($k,1);
			$order_detail_array[$k] =$v[0];
		}
		return 	$order_detail_array;
	}
}
?>