// JavaScript Document
jQuery(function($){
	//alert(ajaxurl);

   $("._datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
  //  var currentDate = new Date();

    //$("._datepicker").datepicker("setDate", currentDate);
    $("._datepicker").datepicker("option", "showAnim", "blind");

    $("._datepicker").datepicker({
        changeMonth: true,
        changeYear: true,

    });

	$( "#frmOrderItem" ).submit(function( event ) {
		//alert(ajax_object.ajaxurl);
		// data: "{asd:'" + das"'}",
		//alert($("#select_order").val());
		//alert($("#frmOrderItem").serialize());
		$.ajax({
			//url: ajaxurl,
			url:ajax_object.ajaxurl,
			//data:$("#frmOrderItem").serialize(),
			//data: "{action:'my_action',sub_action:'order_item',select_order:'"+ $("#select_order").val() +"'}",
			data: {
            'action':'my_action',
			'ajax_function':'order_item',
            'select_order' : $("#select_order").val(),
			'page' : 'order-item'
        	},
			success:function(data) {
				// This outputs the result of the ajax request
				//console.log(data);
				
				$(".ajax_content").html(data);
			},
			error: function(errorThrown){
				console.log(errorThrown);
				alert("e");
			}
		}); 
		return false; 
	});
	
	
	$("#frmOrderItem").trigger("submit");
	
	$("#select_order").change(function(){
	  //alert("The text has been changed.");
	  $("#frmOrderItem").trigger("submit");
	});
});