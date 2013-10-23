function makeAllDisabledNull()
{
	document.getElementById("id_card_number_error").innerHTML="";
	document.getElementById("id_card_number_error_div").style.display="none";
	document.getElementById("id_expiry_date_error").innerHTML="";
	document.getElementById("id_expiry_date_error_div").style.display="none";
	document.getElementById("id_name_on_card_error").innerHTML="";
	document.getElementById("id_name_on_card_error_div").style.display="none";
	document.getElementById("id_card_verification_error").innerHTML="";
	document.getElementById("id_card_verification_error_div").style.display="none";
}
function checkValidate()
{
	document.getElementById("card_number").value=trim(document.getElementById("card_number").value);
	document.getElementById("name_on_card").value=trim(document.getElementById("name_on_card").value);
	document.getElementById("card_verification").value=trim(document.getElementById("card_verification").value);
	makeAllDisabledNull();
	var total=parseFloat(document.getElementById("total_custom_amt").value) +parseFloat(document.getElementById("total_credit_amt").value)+parseFloat(document.getElementById("total_sender_amt").value);
	$j("#id_form_error").hide();
	flag=0;
	if(total<=0)
	{
		$j("#id_form_error").show().text(ENTER_PAYMENT);
		$j("#InvoiceCardType").focus();
		flag=1;
	}
	if(document.getElementById("card_number").value=='')
	{
		$j("#id_card_number_error").text(ENTER_CARD_NUMBER);
		$j("#id_card_number_error_div").show().removeClass("id_card_total-cls");
		$j("#card_number").focus();
		flag=1;
	}
	if(document.getElementById("InvoiceMonth").value<=curr_month && curr_year==document.getElementById("InvoiceYear").value)
	{
		$j("#id_expiry_date_error").text(ENTER_VALID_EXPIREDATE);
		$j("#id_expiry_date_error_div").show().removeClass("id_expiry_date-cls");
		$j("#InvoiceMonth").focus();
		flag=1;
	}
	if(document.getElementById("name_on_card").value=='')
	{
		$j("#id_name_on_card_error").text(ENTER_NAME_ON_CARD);
		$j("#id_name_on_card_error_div").show().removeClass("id_name_on_card-cls");
		$j("#name_on_card").focus();
		flag=1;
	}
	if(document.getElementById("card_verification").value=='')
	{
		$j("#id_card_verification_error").text(ENTER_VERIFICATION_NUMBER);
		$j("#id_card_verification_error_div").show().removeClass("id_card_verification-cls");
		$j("#card_verification").focus();
		flag=1;
	}
	if((!checkOnlyNumbers(document.getElementById("card_verification").value)) || !(document.getElementById("card_verification").value>= 0 && document.getElementById("card_verification").value<9999 ))
	{
		$j("#id_card_verification_error").text(ENTER_VALID_VERIFICATION_NUMBER);
		$j("#id_card_verification_error_div").show().removeClass("id_card_verification-cls");
		$j("#card_verification").focus();
		flag=1;
	}
	if(!(user_type=='corporate_paid' || user_type=='corporate_free'))
	{
		if(document.getElementById("accept_terms").checked==false)
		{
			$j("#id_form_error").show().text(ACCEPT_TERMS_CONDITION);
			$j("#accept_terms").focus();
			flag=1;
		}
	}
	if(flag==1)
	{
		return false;
	}
	else
	{
		if(confirm(SURE_TO_PAYMENT_CONTINUE))
			return true;
		else
			return false;
	}
}
function checkValidCredit(admin)
{
	if (admin != true) admin = false;
	document.getElementById("credit").value=parseInt(trim(document.getElementById("credit").value));
	credit=document.getElementById("credit").value;
	document.getElementById("id_credit_error").innerHTML="";
	document.getElementById("id_credit_error").style.display='';
	setValue("credit","0");
	if(credit!='' && !isNaN(credit))
	{
		if(credit<100 && admin == false)
		{
			document.getElementById("id_credit_error").innerHTML=ENTER_VALID_CREDIT_LIMIT;
		}
		else
		{
			document.getElementById("id_credit_error").style.display='none';
			calculateCreditCost(credit);
			// checkValidateForGenerateInvoice();
		}
	}
	else
	{
		document.getElementById("id_credit_error").innerHTML=ENTER_VALID_CREDIT;
		document.getElementById("credit").value='';
	}
	return false;
}
function calculateCreditCost(credit)
{
	var remaining_amt    = credit;
	var first_level_amt  = 0.985;
	var second_level_amt = 0.60;
	var third_level_amt  = 0.45;
	var four_level_amt   = 0.30;
	var fifth_level_amt  = 0.075;
	var total_cost       = 0;
	if(remaining_amt>1000)
	{
		total_cost=total_cost+(first_level_amt*1000);
		remaining_amt=remaining_amt-1000;
	}
	else
	{
		total_cost=total_cost+(first_level_amt*remaining_amt);
		remaining_amt=0;
	}
	if(remaining_amt>0)
	{
		if(credit>1500)
		{
			total_cost=total_cost+(second_level_amt*500);
			remaining_amt=remaining_amt-500;
		}
		else
		{
			total_cost=total_cost+(second_level_amt*remaining_amt);
			remaining_amt=0;
		}
	}
	if(remaining_amt>0)
	{
		if(credit>2000)
		{
			total_cost=total_cost+(third_level_amt*500);
			remaining_amt=remaining_amt-500;
		}
		else
		{
			total_cost=total_cost+(third_level_amt*remaining_amt);
			remaining_amt=0;
		}
	}
	if(remaining_amt>0)
	{
		if(credit>2500)
		{
			total_cost=total_cost+(four_level_amt*500);
			remaining_amt=remaining_amt-500;
		}
		else
		{
			total_cost=total_cost+(four_level_amt*remaining_amt);
			remaining_amt=0;
		}
	}
	if(remaining_amt>0)
	{
			total_cost=total_cost+(fifth_level_amt*(credit-2500));
	}
	setValue("credit",total_cost);
}
function checkValidSender()
{
	document.getElementById("sender").value=parseInt(trim(document.getElementById("sender").value));
	sender=document.getElementById("sender").value;
	document.getElementById("id_sender_error").style.display='';
	document.getElementById("id_sender_error").innerHTML="";
	setValue("sender",0);
	if(sender!='' && !isNaN(sender))
	{
		if(sender<1)
		{
			document.getElementById("id_sender_error").innerHTML=ENTER_VALID_SENDER_LIMIT;
		}
		else
		{
			document.getElementById("id_sender_error").style.display='none';
			calculateSenderCost(sender);
			//checkValidateForGenerateInvoice();
}
	}
	else
	{
		document.getElementById("id_sender_error").innerHTML=ENTER_VALID_SENDER;
		document.getElementById("sender").value='';
	}
	return false;
}
function calculateSenderCost(sender)
{
	var remaining_amt=sender;
	var first_level_amt=10;
	var second_level_amt=7;
	var third_level_amt=5;
	var four_level_amt=3;
	var fifth_level_amt=1;
	var total_cost=0;
	if(remaining_amt>10)
	{
		total_cost=total_cost+(first_level_amt*10);
		remaining_amt=remaining_amt-10;
	}
	else
	{
		total_cost=total_cost+(first_level_amt*remaining_amt);
		remaining_amt=0;
	}
	if(remaining_amt>0)
	{
		if(sender>20)
		{
			total_cost=total_cost+(second_level_amt*10);
			remaining_amt=remaining_amt-10;
		}
		else
		{
			total_cost=total_cost+(second_level_amt*remaining_amt);
			remaining_amt=0;
		}
	}
	if(remaining_amt>0)
	{
		if(sender>30)
		{
			total_cost=total_cost+(third_level_amt*10);
			remaining_amt=remaining_amt-10;
		}
		else
		{
			total_cost=total_cost+(third_level_amt*remaining_amt);
			remaining_amt=0;
		}
	}
	if(remaining_amt>0)
	{
		if(sender>40)
		{
			total_cost=total_cost+(four_level_amt*10);
			remaining_amt=remaining_amt-10;
		}
		else
		{
			total_cost=total_cost+(four_level_amt*remaining_amt);
			remaining_amt=0;
		}
	}
	if(remaining_amt>0)
	{
			total_cost=total_cost+(fifth_level_amt*(sender-40));
	}
	setValue("sender",total_cost);
}
function clearValue(type)
{
	document.getElementById(type).value="";
	document.getElementById("id_"+type+"_text").innerHTML="$0.00";
	document.getElementById("total_"+type+"_amt").value=0;;
	document.getElementById("id_total_"+type+"_amt").innerHTML="$0.00";
	document.getElementById("id_total_"+type+"_amt_label").innerHTML="$0.00";
	if(document.getElementById("total_orignal_"+type+"_amt")!=undefined)
		document.getElementById("total_orignal_"+type+"_amt").value=0;;
	calculate_GST();
	return false;
}
function setValue(type,value) {
	document.getElementById("total_"+type+"_amt").value=value;
	document.getElementById("id_"+type+"_text").innerHTML="$"+(parseFloat(value)).toFixed(2);
	document.getElementById("id_total_"+type+"_amt").innerHTML="$"+(parseFloat(value)).toFixed(2);
	document.getElementById("id_total_"+type+"_amt_label").innerHTML="$"+(parseFloat(value)).toFixed(2);
	if(document.getElementById("total_orignal_"+type+"_amt")!=undefined)
		document.getElementById("total_orignal_"+type+"_amt").value=value;
	calculate_GST();
}
function calculate_GST_for_create_invoice()
{
	if(user_type=='corporate_paid' || user_type=='corporate_free')
	{
		var total=parseFloat(document.getElementById("total_custom_amt").value) +parseFloat(document.getElementById("total_credit_amt").value)+parseFloat(document.getElementById("total_sender_amt").value);
	}
	else
	{
		var total=parseFloat(document.getElementById("total_custom_amt").value)+parseFloat(document.getElementById("total_credit_amt").value);
	}
	if(count_tax==1)
	{
		var gst=(total*TAX)/100;
		var total=total+gst;
		document.getElementById("id_total").innerHTML="$"+(parseFloat(total)).toFixed(2);
		document.getElementById("id_gst").innerHTML="$"+(parseFloat(gst)).toFixed(2);
		document.getElementById("id_gst_row").style.display='';
	}
	else
	{
		var gst=0;
		var total=total+gst;
		document.getElementById("id_total").innerHTML="$"+(parseFloat(total)).toFixed(2);
		document.getElementById("id_gst").innerHTML="$"+(parseFloat(gst)).toFixed(2);
		document.getElementById("id_gst_row").style.display='none';
	}
}
function calculate_GST()
{
	if(user_type=='corporate_paid' || user_type=='corporate_free')
	{
		var total=parseFloat(document.getElementById("total_custom_amt").value) +parseFloat(document.getElementById("total_credit_amt").value)+parseFloat(document.getElementById("total_sender_amt").value);
	}
	else
	{
		var total=parseFloat(document.getElementById("total_custom_amt").value);
	}
	if(count_tax==1)
	{
		var gst=(total*TAX)/100;
		var total=total+gst;
		document.getElementById("id_total").innerHTML="$"+(parseFloat(total)).toFixed(2);
		document.getElementById("id_gst").innerHTML="$"+(parseFloat(gst)).toFixed(2);
		document.getElementById("id_gst_row").style.display='';
	}
	else
	{
		var gst=0;
		var total=total+gst;
		document.getElementById("id_total").innerHTML="$"+(parseFloat(total)).toFixed(2);
		document.getElementById("id_gst").innerHTML="$"+(parseFloat(gst)).toFixed(2);
		document.getElementById("id_gst_row").style.display='none';
	}
}
/*
function openPopup1(url)
{
	myWindow=window.open(url,'_blank','width=500,height=500,menubar=no,resizable=0,status=no,scrollbars=no,titlebar=no,toolbar=no,top=100,left=100,fullscreen=no');
	myWindow.focus();
}*/
