/* --------------- Ekarda Global Js Function ---------------------------------------------
 *  Author : Ekarda Developers
 *  Project: Ekarda
 */
var defaultSearchTableHeight = 36;
var augmentedSearchTableHeight = 85;
var triggerElement;
var genericDisabler;
var isIncompatibleBrowser = false;
$j(function(){
	Ekarda.init();
});
var Ekarda = {
	init:function(){
		Ekarda.Effects.onAjaxRequestGradient();
		var userAgent = $j.browser;
		if(userAgent.msie && userAgent.version < 9)
			isIncompatibleBrowser = true;
		Ekarda.Tools.calculateSearchTableHeadersPosition();
	},
	Properties: {
		messageTimeout: null
	},
    Initialize:{
        fancybox:function() {
            $j(".fancyBox").fancybox();
        }
    },
    Tools: {
        updateElements: function(response){
            if (typeof response == "string")
                response = $j(response);
            response.children("div").each(function(){
            	var elementToUpdate = $j("#" + $j(this).attr('id'));
            	if(elementToUpdate.length > 0){
            		if (isIncompatibleBrowser == true)
                		elementToUpdate.get(0).innerHTML = $j(this).get(0).innerHTML;
                	else
                		elementToUpdate.html($j(this).html());
                	elementToUpdate.change();
            	}
                var currentReplacedId = $j(this).attr('id');
                if ( typeof currentReplacedId == "string" && currentReplacedId.match("\w*msg")){
                	Ekarda.Tools.setMessagesTimeout();
                }
            })
        },
        setMessagesTimeout: function(){
        	if (Ekarda.Properties.messageTimeout != null)
        		clearTimeout( Ekarda.Properties.messageTimeout );
        	Ekarda.Properties.messageTimeout = window.setTimeout(function(){
            	$j("*[id$=msg]:not([id^=signup])").html("");
            },2500);
        },
        searchTrigger: function(){
            $j("#content").on("click",'a[search-inputs]',function(event){
                event.preventDefault();
                var searchInputs = $j("tbody[search-inputs="+$j(this).attr("search-inputs")+"]");
                var areSearchInputsVisible = searchInputs.is(':visible');
                if (areSearchInputsVisible){
                	//if ($j('.table-head').length > 0)
                		searchInputs.hide();
                	//else
                	//	searchInputs.css('visibility', 'hidden');
                	$j('.table-head').height(defaultSearchTableHeight);
                }
                else{
                	if ($j('.table-head').length > 0)
                		searchInputs.css('display', 'block');
                	else
                		searchInputs.show();
                    $j('.table-head').height(augmentedSearchTableHeight);
                    Ekarda.Tools.calculateSearchTableHeadersPosition();
                }
                return false;
            });
        },
        checkDatabaseConsistence: function(){
            $j("#content").on("click",'a[check-consistence]',function(event){
                event.preventDefault();
                $j.ajax({
                    url: $j(this).attr("href"),
                    async: true,
                    type: "POST",
                    data: {"data[dataToCheck]": $j(this).attr("check-consistence")},
                    success: function(response){
                        Ekarda.Tools.updateElements(response);
                    }
                });
                return false;
            });
        },
        calculateSearchTableHeadersPosition: function(){
        	var searchTableSelector = '.table-head';
        	/*if ($j(searchTableSelector).length == 0)
        		searchTableSelector = '.table-head-contact';*/
        	if($j('.table-content tr:eq(0) td').length > 1){
        		$j(searchTableSelector+' thead').css('display', 'block');
	        	$j(searchTableSelector+' th'+','+searchTableSelector+' td').each(
					function() {
						//$j(this).css('position', 'absolute');
						var tagSelector = $j(this).is('td')? ' td': ' th'
						var index = $j(searchTableSelector+tagSelector).index(this);
						var referenceColumn = $j('.table-content tr:eq(0) td:eq('+ index + ')')
						var referenceColumnOffset = referenceColumn.offset();
						var newTagWidth = referenceColumn.width()
						/*
						 * if(searchTableSelector = '.table-head-contact')
						 * newTagWidth += 200;
						 */
						$j(this).width(newTagWidth).children('div').width(newTagWidth);
						$j(this).width(newTagWidth).children('input').outerWidth(newTagWidth);
						if (referenceColumnOffset != null) {
							$j(this).offset({
								left : referenceColumnOffset.left + 1
							});
						}
					}
			);
        	}
        }
    },
    Effects:{
        onAjaxRequestGradient: function() {
    		$j('.btn').bind('click',function(){
    			triggerElement = $j(this);
    			
    			triggerElement.attr("original-text", triggerElement.text());
    			if(triggerElement.is('input'))
    				triggerElement.val('Form submitting - please wait');
    			else
    				triggerElement.text('Form submitting - please wait');
    		});
            $j(document).ajaxSend(function(event,xhr,options){
            	var userAgent = $j.browser;
            	if (userAgent.mozilla)
            		triggerElement = $j(event.target.activeElement);
                if (triggerElement && triggerElement.is('.btn')){
                	var triggerOffset = triggerElement.offset();
            		genericDisabler = $j('<div></div>');
            		genericDisabler.addClass('generic-disabler');
            		$j('body').append(genericDisabler);
                	genericDisabler.width(triggerElement.outerWidth());
                	genericDisabler.height(triggerElement.outerHeight());
                	genericDisabler.offset({
                		left:triggerOffset.left,
                		top:triggerOffset.top
                	});
                	triggerElement.toggleClass('gray-ajax-gradient','fast').animate({
                		opacity: 0.6
                	},'normal');
                }
            });
            $j(document).ajaxComplete(function(event,xhr,options){
            	if (triggerElement && triggerElement.is('.btn')){
            		triggerElement.animate({
                		opacity: 1
                	},'normal', function(){
                    	triggerElement.toggleClass('gray-ajax-gradient','fast');
                		if (genericDisabler)
	            			genericDisabler.remove()
	            		if(triggerElement.is('input'))
	            			triggerElement.val(triggerElement.attr('original-text'));
	            		else
	            			triggerElement.text(triggerElement.attr('original-text'));
	            		triggerElement = null;
                	})
            	}
            });
        },
        Animate: {
            onInlineEdit:function(elem) {
                $(elem).animate({
                    background: "#FFFFFF"
                }, 5000, function() {
                });
            }
        }
    },
    Senders:{
        Index: {
            onPerformAction: {
                Change: function(params) {
                    $j("#content").on("change", "#SenderPerformActionDIV select#SenderPerformAction", function() {
                        var selectedAction = $j("select#SenderPerformAction option:selected").val();
                        $j("select#SenderPerformAction").val(0);
                        //ensure we have some items checked
                        if ($j("#listsenderdivid").find("input:checked").length > 0) {
                            if (selectedAction == 'delete') Ekarda.Senders.Index.onPerformAction.Delete(params);
                            if (selectedAction == 'resend') Ekarda.Senders.Index.onPerformAction.Resend(params);
                        } else {
                            alert(params.messages.no_item_selected);
                        }
                    });
                },
                getSelectedSenderIds: function() {
                    var sender_ids = [];
                    $j("#listsenderdivid").find("input:checked").each(function() {
                        sender_ids.push($j(this).val());
                    });
                    return sender_ids;
                },
                Delete:function(params) {
                    var ans = confirm(params.messages.delete_confirmation);
                    var parents = this;
                    if(ans) {
                        var sender_ids = Ekarda.Senders.Index.onPerformAction.getSelectedSenderIds();
                        var url = params.actions.delete_sender + "/" + sender_ids.join(",");
                        var jqxhr = $j.post(url, function(data) {
                            $j(parents).parent().parent().parent().delay(500).fadeOut("slow");
                        })
                        .success(function(response) {
                            Ekarda.Tools.updateElements(response);
                            setTimeout(function(){
                                window.location.reload();
                            },1000)
                        })
                        .error(function() {})
                        .complete(function() {});
                    }
                },
                Resend:function(params) {
                    $j('#resend_trigger').trigger("click");
                }
            },
            onNavigation: function(params) {
                $j(params.wrapperSelector).on("click", params.elementsSelector, function(event) {
                    event.preventDefault();
                    $j('#msg').html('');
                    $j.ajax({
                        url: $j(this).attr("href"),
                        async: true,
                        type: "POST",
                        success: function(response){
                            Ekarda.Tools.updateElements(response);
                        }
                    });
                });
            }
        }
    },
    Shared: {
        onCheckAllLinks: function(params) {
            var wrapperSelector = params["wrapperSelector"];
            var elementsSelector = params["elementsSelector"];
            var additionalFunction = params["additionalFunction"];
            $j(wrapperSelector).on("click",elementsSelector, function(event){
                event.preventDefault;
                $j(wrapperSelector).find("input[type=checkbox]").not(":checked").attr("checked", "checked");
            });
        },
        onCheckNoneLinks: function(params) {
            var wrapperSelector = params["wrapperSelector"];
            var elementsSelector = params["elementsSelector"];
            var additionalFunction = params["additionalFunction"];
            $j(wrapperSelector).on("click",elementsSelector, function(event){
                event.preventDefault;
                $j(wrapperSelector).find("input[type=checkbox]:checked").attr("checked", false);
            });
        },
        onConfirmationLinks: function(params){
          var wrapperSelector = params["wrapperSelector"];
          var confirmMessage = params["confirmMessage"];
          var additionalFunction = params["additionalFunction"];
          var elementsSelector = params["elementsSelector"];
          if (typeof elementsSelector == "undefined")
            elementsSelector = 'a[id^="delete_item_"]';
            $j(wrapperSelector).on("click",elementsSelector, function(event){
                event.preventDefault();
                $j('#msg').html('');
                if (confirm(confirmMessage)) {
                    $j.ajax({
                        url: $j(this).attr("href"),
                        async: true,
                        type: "POST",
                        success: function(response){
                          Ekarda.Tools.updateElements(response)
                        if (typeof additionalFunction == 'function'){
                          additionalFunction.call();
                        }
                        }
                    });
                }
            })
        },
        onNavigation: function(params) {
        	var elementsSelector = params["elementsSelector"];
            if (typeof elementsSelector == "undefined")
            	elementsSelector = '.event-navigation-list';
            $j(params.wrapperSelector).on("click", elementsSelector, function(event) {
                event.preventDefault();
                $j('#msg').html('');
                $j.ajax({
                    url: $j(this).attr("href"),
                    async: true,
                    type: "POST",
                    success: function(response){
                        Ekarda.Tools.updateElements(response);
                    }
                });
            });
        },
        onSearchFields: function(params) {
            var wrapperSelector = params["wrapperSelector"];
            var neededFormSelector = params["neededFormSelector"];
            var elementsSelector = params["elementsSelector"];
            var eventType = params["eventType"];
            var additionalFunction = params["additionalFunction"];
            if (typeof elementsSelector == "undefined")
                elementsSelector = 'input.event-search-list';
            if (typeof eventType == "undefined")
                eventType = 'keyup';
            $j(wrapperSelector).on(eventType, elementsSelector,
                function(event) {
                    if ($j(this).is("a"))
                        event.preventDefault();
                    var setAction = params["setAction"];
                    if (typeof setAction == "undefined")
                        setAction = $j(neededFormSelector).attr('action');
                    $j.ajax({
                        url : setAction,
                        async : true,
                        type : "POST",
                        data : $j(neededFormSelector).serialize(),
                        success : function(response) {
                            Ekarda.Tools.updateElements(response);
                            if (typeof additionalFunction == 'function'){
                              additionalFunction.call();
                            }
                        }
                    });
                }
            );
        },
        onEditLinks: function(params){
          var wrapperSelector = params["wrapperSelector"];
          var elementsSelector = params["elementsSelector"];
          var additionalFunction = params["additionalFunction"];
          if (typeof elementsSelector == "undefined")
            elementsSelector = 'a[id^="edit_item_"]';
            $j(wrapperSelector).on("click",elementsSelector, function(event){
                event.preventDefault();
                $j('#msg').html('');
                $j.ajax({
                    url: $j(this).attr("href"),
                    async: true,
                    type: "POST",
                    success: function(response){
                        Ekarda.Tools.updateElements(response);
                        if (typeof additionalFunction == 'function'){
                          additionalFunction.call();
                        }
                    }
                });
            })
        }
    },
    Contacts:{
        Index:{
            onEdit:function(message) {
                $j('.table-content').find("tr").find("td").find("a.editContacts").on("click", function() {
                    return false;
                });
            },
            onEditSuccess:function(param) {
                $j.fancybox.close();
            },
            onEditSubmit:function() {
                $j(".loader").bind("ajaxStart", function(){
                    $j(this).show();
                }).bind("ajaxStop", function(){
                    $j(this).hide();
                });
            },
            onSearch:function(params) {
                $j("#ContactIndexForm").on("keyup", "input.event-search-contact-list", function() {
                    $j.ajax({
                        url: params.actions,
                        async: true,
                        type: "POST",
                        data: $j("#ContactIndexForm").serialize(),
                        success: function(response){
                            $j("#ContactListDIV").html(response);
                            Ekarda.Contacts.Index.updateActions();
                        }
                    });
                });
            },
            onPerformAction: {
                Change: function(params) {
                    $j("#content").on("change", "#ContactListMainDIV select#ContactPerformAction", function() {
                        var selectedAction = $j("select#ContactPerformAction option:selected").val();
                        $j("select#ContactPerformAction").val(0);
                        //ensure we have some items checked
                        if ($j("#ContactListDIV").find("input:checked").length > 0) {
                            if (selectedAction == 'delete') Ekarda.Contacts.Index.onPerformAction.Delete(params);
                            if (selectedAction == 'unsubscribe') Ekarda.Contacts.Index.onPerformAction.Unsubscribe(params);
                            if (selectedAction == 'subscribe') Ekarda.Contacts.Index.onPerformAction.Subscribe(params);
                        } else {
                            alert(params.messages.no_item_selected);
                        }
                    });
                },
                getSelectedContactIds: function() {
                    var contact_ids = [];
                    $j("#ContactListDIV").find("input:checked").each(function() {
                        contact_ids.push($j(this).val());
                    });
                    return contact_ids;
                },
                Delete:function(params) {
                    var ans = confirm(params.messages.delete_confirmation);
                    var parents = this;
                    if(ans) {
                        var contact_ids = Ekarda.Contacts.Index.onPerformAction.getSelectedContactIds();
                        var url = params.actions.delete_contact + "/" + contact_ids.join(",");
                        var jqxhr = $j.post(url, function(data) {
                            $j(parents).parent().parent().parent().delay(500).fadeOut("slow");
                        })
                        .success(function() {
                            $j("#listnav").find("a.current").trigger("click");
                        })
                        .error(function() {})
                        .complete(function() {});
                    }
                },
                Unsubscribe:function(params) {
                    var ans = confirm(params.messages.subscription_confirmation);
                    var parents = this;
                    if(ans) {
                        var contact_ids = Ekarda.Contacts.Index.onPerformAction.getSelectedContactIds();
                        var url = params.actions.unsubscribe_contact + "/" + contact_ids.join(",");
                        var jqxhr = $j.post(url, function(data) {
                            $j(parents).parent().parent().parent().delay(500).fadeOut("slow");
                        })
                        .success(function() {
                            $j("#listnav").find("a.current").trigger("click");
                        })
                        .error(function() {})
                        .complete(function() {});
                    }
                },
                Subscribe:function(params) {
                    var ans = confirm(params.messages.subscription_confirmation);
                    var parents = this;
                    if(ans) {
                        var contact_ids = Ekarda.Contacts.Index.onPerformAction.getSelectedContactIds();
                        var url = params.actions.subscribe_contact + "/" + contact_ids.join(",");
                        var jqxhr = $j.post(url, function(data) {
                            $j(parents).parent().parent().parent().delay(500).fadeOut("slow");
                        })
                        .success(function() {
                            $j("#listnav").find("a.current").trigger("click");
                        })
                        .error(function() {})
                        .complete(function() {});
                    }
                }
            },
            onNavigation: function(params) {
                $j(params.wrapperSelector).on("click", params.elementsSelector, function(event) {
                    event.preventDefault();
                    $j('#msg').html('');
                    $j.ajax({
                        url: $j(this).attr("href"),
                        async: true,
                        type: "POST",
                        success: function(response){
                            Ekarda.Tools.updateElements(response);
                            Ekarda.Contacts.Index.updateActions();
                        }
                    });
                });
            },
            updateActions: function() {
                var dropdown = $j("#ContactPerformAction").find("option:last");
                if ($j("#listnav").find("a:eq(0)").hasClass("current")) {
                    dropdown.text("Unsubscribe");
                    dropdown.val("unsubscribe");
                    $j("#ContactPerformActionDIV").show();
                } else if ($j("#listnav").find("a:eq(1)").hasClass("current")) {
                    dropdown.text("Subscribe");
                    dropdown.val("subscribe");
                    $j("#ContactPerformActionDIV").show();
                } else {
                    $j("#ContactPerformActionDIV").hide();
                }
            }
        }
    },
    MyEcardRecipients:{
        CardSend: {
            onSelectingVariableDate:function(types) {
                Ekarda.Initialize.fancybox();
                Ekarda.MyEcardRecipients.CardSend.onSelectingDeliveryType();
                Ekarda.MyEcardRecipients.CardSend.onEditVariableDate();
                $j('.onFancyBox').on("click", function() {
                    if(Ekarda.MyEcardRecipients.CardSend.isVariableDateSelected()) {
                        $j(".fancyBoxTrigger").click();
                    } else {
                        $j("#btnCardDesign").trigger("click");
                    }
                    var cardOccasion = $j("#MyecardOccasionVar option:selected").text();
                    $j(".dateSelected").val(cardOccasion);
                });
                $j('.onCancelFancybox').on("click", function() {
                    $j.fancybox.close();
                    return false;
                });
                $j('.onYesFancyBox').on("click", function() {
                    $j("#btnCardDesign").trigger("click");
                });
                if(types == "custom_filed") {
                    $j("#id_3").trigger("click");
                }
            },
            isVariableDateSelected:function() {
                return $j("#id_3.radio_btn_active").length;
            },
            onEditVariableDate:function() {
                $j('.onEditVariableDate').on("click", function() {
                    $j("input").removeAttr("disabled");
                    $j("select").removeAttr("disabled");
                    $j("#btnCardDesign").trigger("click");
                    /* returned back to disabled after button is click for security reason*/
                    $j("input").attr("disabled", "disabled");
                    $j("select").attr("disabled", "disabled");
                });
            },
            onFormSubmit:function(scheduleType) {
                var optId = "";
                switch(scheduleType){
                    case "sendnow":
                        $j("#MyecardScheduletype1").val('sendnow');
                        optId = "id_1";
                        break;
                    case "other":
                        $j("#MyecardScheduletype1").val('scheduledtime');
                        optId = "id_2";
                        break;
                    case "custom_filed":
                        $j("#MyecardScheduletype1").val('variabledate');
                        optId = "id_3";
                        break;
                }
               $j("#" + optId).addClass("radio_btn_active");
               $j("#" + optId).parent().parent().find("div.hidden_opt").slideDown("slow");
                var params = {
                    "wrapperSelector": "#card_send_options_wrapper",
                    "neededFormSelector": "#MyecardCardSendForm",
                    "eventType": "change",
                    "elementsSelector": 'select.event-update-ocassion',
                    "setAction": '/myecard_recipients/contact_occasion'
                };
                Ekarda.Shared.onSearchFields(params);
            },
            checkValidate:function() {
                if($j("#MyecardScheduletype1").val()=='sendnow')
                {
                    $j("#MyecardConfirmationEmailSendnow").val(trim($j("#MyecardConfirmationEmailSendnow").val()));
                    if($j("#MyecardConfirmationEmailSendnow").val()=='')
                    {
			alert(ECARD_CONFIRMATION_MAIL);
			$j("#MyecardConfirmationEmailSendnow").focus();
			return false;
                    }
                    else if(validateEmail($j("#MyecardConfirmationEmailSendnow").val())==false)
                    {
			alert(VALID_EMAIL_REQUIRED);
			$j("#MyecardConfirmationEmailSendnow").focus();
			return false;
                    }
                }
                else if($j("#MyecardScheduletype1").val()=='scheduledtime')
                {
                    var tyear = $j("#MyecardYearSch").val();
                    var tmonth = $j("#MyecardMonthSch").val();
                    var tdays = $j("#MyecardDaySch").val();
                    var thour = $j("#MyecardHourSch").val();
                    var tminute = $j("#MyecardMinuteSch").val();
                    var tam_pm = $j("#MyecardAmPmSch").val();
                    var date1 = new Date(tyear + "/" +  tmonth + "/" + tdays + " " + thour + ":" + tminute + " " + tam_pm);
                    var sysDate = new Date();
                    var dateDiff = date1.getTime() - sysDate.getTime();
                    if(dateDiff <= 0){
                        alert(VALID_SCHEDULE_DATE);
                        return false;
                    }
                    $j("#MyecardConfirmationEmailSch").val(trim($j("#MyecardConfirmationEmailSch").val()));
                    if($j("#MyecardConfirmationEmailSch").val()=='')
                    {
			alert(ECARD_CONFIRMATION_MAIL);
			$j("#MyecardConfirmationEmailSch").focus();
			return false;
                    }
                    else if(validateEmail($j("#MyecardConfirmationEmailSch").val())==false)
                    {
			alert(VALID_EMAIL_REQUIRED);
			$j("#MyecardConfirmationEmailSch").focus();
			return false;
                    }
                }
                else if($j("#MyecardScheduletype1").val()=='variabledate')
                {
                    $j("#MyecardOffsetVar").val(trim($j("#MyecardOffsetVar").val()));
                    $j("#MyecardConfirmationEmailVar").val(trim($j("#MyecardConfirmationEmailVar").val()));
                    if($j("#MyecardContactListVar").val()=='')
                    {
			alert(SELECT_CONTACT_LIST);
			$j("#MyecardContactListVar").focus();
			return false;
                    }
                    else if($j("#MyecardOccasionVar").val()=='')
                    {
			alert(SELECT_SUBSCRIPTION_FIELD);
			$j("#MyecardOccasionVar").focus();
			return false;
                    }
                        /*		if(!isInteger($j("#MyecardOffsetVar").val()))
                    {
                            alert(ENTER_OFFSET_VALUE);
                            $j("#MyecardOffsetVar").focus();
                            return false;
                    }
                        */
                    var oldMyecardOffsetVar=$j("#MyecardOffsetVar").val();
                    var myecardOffsetVar=parseInt($j("#MyecardOffsetVar").val());
                    if(myecardOffsetVar!=NaN && myecardOffsetVar!='NaN')
                    {
			$j("#MyecardOffsetVar").val(myecardOffsetVar);
                    }
                    if(!($j("#MyecardOffsetVar").val()>=0 && $j("#MyecardOffsetVar").val()<=99))
                    {
			$j("#MyecardOffsetVar").val(oldMyecardOffsetVar);
			alert(ENTER_OFFSET_VALUE);
			$j("#MyecardOffsetVar").focus();
			return false;
                    }
                    if($j("#MyecardConfirmationEmailVar").val()=='')
                    {
			alert(ECARD_CONFIRMATION_MAIL);
			$j("#MyecardConfirmationEmailVar").focus();
			return false;
                    }
                    else if(validateEmail($j("#MyecardConfirmationEmailVar").val())==false)
                    {
			alert(VALID_EMAIL_REQUIRED);
			$j("#MyecardConfirmationEmailVar").focus();
			return false;
                    }
                }
                else
                {
                    alert(ECARD_SEND_TYPE);
                    return false;
                }
                return true;
            },
            initSearchFieldsOrUpdaters:function(params) {},
            onSelectingDeliveryType:function() {
                $j('.radio_btn').on("click", function() {
                    var id = $j(this).attr("id");
                });
            }
        }
    },
    MyEcards:{
        CardTypes:{
            onNextStep:function(params) {
                 $j('.onNextSubmit').live("click", function() {
                     if ($j(".select_design:checked").length == 0) {
                        alert(params.noSelectionMessage);
                     } else {
                        $j('#btnCardTypeNext').trigger("click");
                    }
                 });
            }
        },
        CardMessage: {
            onNextStep:function(params) {
                 $j('.onNextSubmit').live("click", function() {
                        $j(params.buttonSelector).trigger("click");
                 });
             },
             setFormFieldMaxLengthFromConfig:function(jsonData) {
                var parseJson = $j.parseJSON(jsonData);
                var newElements  = new Array()
                var i = 0;
                $j.each(parseJson, function(index,value) {
                    var len = $j("#"+index).val();
                    if(typeof(len) !="undefined") {
                        var remaining = value - len.length;
                        $j("#"+index).after("<b class='c"+index+"'>characters remaining: "+remaining+"</b>");
                        $j(".c" + index).css("margin","20px 0 0 10px").css("font-size","11px").css("font-weight","normal").css("color","#FC7A02");
                         var inc = value - 1;
                         newElements[i] = "#"+index;
                         i++;
                    }
                });
                var splits = newElements.join(",");
                $j('body').on('keyup',splits,function(event){
                    var values = $j(this).val();
                    var maxLength = $j(this).attr("maxlength");
                    var remaining = parseInt(maxLength) - values.length;
                    $j(this).parent().find("b").text("characters remaining: " + remaining);
                    setLength(this);
                });
                $j('body').on('blur',splits,function(event){
                    setLength(this);
                });
                $j('body').on('keypress',"#MyecardCardMessage",function(event){
                    if(event.which == 13) {
                        return false;
                    }
                })
                $j(splits).trigger("blur");
                function setLength(parent) {
                    var maxLength = $j(parent).attr("maxlength");
                    var valLengh = $j(parent).val().length;
                    if (valLengh > parseInt(maxLength)) {
			$j(parent).val($j(parent).val().slice(0,maxLength));
                    }
                }
             }
        },
        Recipients:{
            onReady:function(type, listVar, varType) {
                if(type == "variabledate") {
                    console.log(listVar);
                    $j("#MyecardContactListId").val(listVar);
                    $j("#MyecardRecipientAddingMethod").val("existing");
                    $j("#MyecardRecipientAddingMethod").trigger("change");
                    //var texts = $j("#MyecardContactListId option:selected").text();
                    //var vals =  $j("#MyecardContactListId").val();
                   // $j("#MyecardContactListId").find("option").remove();
                    //$j("#MyecardContactListId").append("<option value='"+vals+"'>"+texts+"<option>");
                    this.onEditContact();
                    this.variableDate();
                }
            },
            onEditContact:function() {
                $j('#submit').on("click", function() {
                    $j("#MyecardContactListId").removeAttr("disabled");
                    $j("#MyecardContactListId").trigger("change");
                });
                $j('.mainDiveCardsRecipients').find(".table-content").find("a").on("click", function() {
                    var listItem = $j(this).parent().parent().parent()
                    var rowNumber = $j("tr").index(listItem);
                });
            },
            assignDatePicker:function() {
               var minDateAux = "-12Y";
               var yearRangeAux = "-12:+12";
               var parents = this;
               $j("body").on("mouseover",'.hasInitDatePicker, input.event-needs-datepicker',function(event){
                    if (!$j(this).hasClass("hasDatepicker")){
                        event.preventDefault();
                       parents.initializeDatePicker(minDateAux, yearRangeAux);
                    }
                });
            },
            initializeDatePicker:function(minDate, yearRange) {
                var minDateAux = minDate;
                var yearRangeAux = yearRange;
                $j('.hasInitDatePicker, input.event-needs-datepicker')
                .datepicker(
                {
                    dateFormat : 'dd/mm/yy',
                    minDate : minDate,
                    yearRange : yearRange,
                    changeYear : true,
                    changeMonth : true,
                    maxDate : 0
                }).removeClass("event-needs-datepicker");
            },
            variableDate:function() {
               var subscriberList = $j("#MyecardContactListId option:selected").text();
               $j(".search-contacts-trigger").trigger("click");
               $j(".subscriberList").find("strong").text(subscriberList);
            },
            submitClosestForm:function(element){
                $j.ajax({
                    url: $j(element).attr("href"),
                    async: true,
                    type: "POST",
                    data: $j(element).closest("form").serialize(),
                    success: function(response){
                        Ekarda.Tools.updateElements(response);
                    }
                });
            },
            addSenderValidation:function(msg) {
                $j('.invalidInput').text(msg);
                $j('.invalidInput').show().delay(2000).fadeOut(1000);
            },
            onChangeSubscriberList:function() {
                 $j('#MyecardContactListId').change(function(event){
                    event.preventDefault();
                        $j.ajax({
                            url: '/myecards/subscribe_contacts/'+$j(this).attr("item"),
                            async: true,
                            type: "POST",
                            data: $j('#MyecardRecipientsForm').serialize(),
                            success: function(response){
                            	Ekarda.Tools.updateElements(response);
                            }
                        });
                });
            },
            manageRecipient:function(ctrl, myecardID){
                    if ($j(ctrl).val()=='') {
                        $j('#mainDiveCardsRecipients').hide();
                        $j('#DivManualRecipient').hide();
                        $j('#subscripbe_contact').hide();
                        return false;
                    }
                    if ($j(ctrl).val()=='existing'){
                        $j('#mainDiveCardsRecipients').show();
                        $j('#DivManualRecipient').hide();
                        $j('#subscripbe_contact').show();
                        loadConactRecipients(myecardID);
                    } else if($j(ctrl).val()=='manual'){
                        $j('#mainDiveCardsRecipients').hide();
                        $j('#DivManualRecipient').show();
                        $j('#subscripbe_contact').hide();
                    }
            },
            validateManualContacts:function(msgRecipient, msgSelectContact){
                var contactValue = $j("#MyecardContact").val();
                if (contactValue == ''){
                    $j("#msg").html( '<div id="flashMsg" class="alert error"><p>'+msgRecipient+'</p><a title="" href="#" class="close"></a></div>');
                    Ekarda.MyEcards.Recipients.addSenderValidation("'" + msgRecipient + "'");
                    return false;
                }
                if ($j("#MyecardContactListId1").val()==''){
                    $j("#msg").html( '<div id="flashMsg" class="alert error"><p>'+msgSelectContact+'</p><a title="" href="#" class="close"></a></div>');
                    Ekarda.MyEcards.Recipients.addSenderValidation("'" + msgSelectContact + "'");
                    return false;
                }
                return true;
            },
            onContent:function(alertMsg) {
                 $j("#content").on("keydown",'#MyecardSearchMyecardscontactsForm .event-main-contact-list-search, #MyecardSearchRecipientsForm .event-recipient-list-search', function(event){
                    $j.ajax({
                        url: $j(this).closest("form").attr("action")+"/"+$j(this).closest("form").attr("item"),
                        async: true,
                        type: "POST",
                        //dataType: "JSON",
                        data: $j(this).closest("form").serialize(),
                        success: function(response){
                        	Ekarda.Tools.updateElements(response);
                        }
                    });
                });
                $j("#content").on("click",'.event-submit-closest-form-link', function(event){
                    event.preventDefault();
                    if (event.target.id != 'link-add-all-contacts' && event.target.id != 'link-remove-all-contacts' && $j(this).closest("form").find("input:checked").length == 0){
                        alert(alertMsg);
                        return false;
                    }
                    if (event.target.id != 'link-add-all-contacts' && event.target.id != 'link-remove-all-contacts') {
                         if (confirm($j(this).attr("message")))
                            Ekarda.MyEcards.Recipients.submitClosestForm(this);
                    } else {
                        $j('#MyecardAllChecked').val(1);
                        Ekarda.MyEcards.Recipients.submitClosestForm(this);
                    }
                });
                $j("#content").on("click",'.event-submit-manual-contacts-link', function(event){
                    event.preventDefault();
                    if (validateManualContacts()) {
                        Ekarda.MyEcards.Recipients.submitClosestForm(this);
                    }
                });
            },
            onFillContactList:function(obj, siteURL) {
                $j("#MyecardContactListId").val(obj.value);
                var url = siteURL+obj.value;
                $j("#importFileLink").attr("href", url);
            },
            showExistingOrManual:function(myecard_id) {
                if ($j('#MyecardRecipientAddingMethod').val() == 'existing') {
                    $j('#mainDiveCardsRecipients').show();
                    $j('#DivManualRecipient').hide();
                    $j('#subscripbe_contact').show();
                    loadConactRecipients(myecard_id);
                } else if($j('#MyecardRecipientAddingMethod').val() == 'manual') {
                    $j('#mainDiveCardsRecipients').hide();
                    $j('#DivManualRecipient').show();
                    $j('#subscripbe_contact').hide();
                } else{
                    $j('#subscripbe_contact').hide();
                }
            },
            onSubmitPage:function(msgRecipient, msgSelectRecipient) {
                $j('.onNextSubmit').live("click", function() {
                    $j("#MyecardRecipientAddingMethod").removeAttr("disabled");
                    $j("#MyecardContactListId").removeAttr("disabled");
                    $j('#btnRecipientNext').trigger("click");
                });
                $j('#btnRecipientNext').live("click", function() {
                    if($j("#MyecardContact").is(":visible") == false) {
                        var val = $j("#MyecardRecipientAddingMethod").val();
                        if(val.length <=0) {
                            alert(msgRecipient);
                            return false;
                        }
                        var isThereCheckbox = $j("#MyecardDeleteMyecardscontactsForm").find("input[type=checkbox]").length;
                        if(isThereCheckbox <=0) {
                            alert(msgSelectRecipient);
                            return false;
                        }
                        return true;
                    } else {
                       var contactValue = $j("#MyecardContact").val();
                       var selectValue = $j("#MyecardContactListId1").val();
                       var isThereCheckbox = $j("#MyecardDeleteMyecardscontactsForManualForm").find("input[type=checkbox]").length;
                      if(isThereCheckbox <=0) {
                          $j("#msg").html( '<div id="flashMsg" class="alert error"><p>'+msgSelectRecipient+'</p><a title="" href="#" class="close"></a></div>');
                          Ekarda.MyEcards.Recipients.addSenderValidation("'" + msgSelectRecipient + "'");
                          alert(msgSelectRecipient);
                          return false;
                       }
                       return true;
                    }
                });
            },
            onAddOrRemoveSelected:function() {
                 $j('.onAddRecipient').live("click", function() {
                     enableDisable(1);
                 });
                 $j('.onRemoveRecipient').live("click", function() {
                      var totalCheckBox = $j("#MyecardDeleteMyecardscontactsForm").find("input[type=checkbox]").length;
                      var selectedRecords = $j("#MyecardDeleteMyecardscontactsForm").find("input[type=checkbox]:checked").length;
                      var currentSelected = totalCheckBox - selectedRecords;
                      if(currentSelected >=1) {
                          enableDisable(1);
                      } else {
                           enableDisable(0);
                      }
                 });
                 $j(document).ajaxStop(function() {
                    var totalRecipientCheckBox = $j("#MyecardDeleteMyecardscontactsForm").find("input[type=checkbox]").length;
                    //console.log(totalRecipientCheckBox);
                    if(totalRecipientCheckBox >=1) {
                        enableDisable(1);
                    }
                 });
                 function enableDisable(status) {
                     if(status == 1){
                        $j("#MyecardRecipientAddingMethod").attr("disabled","disabled");
                        $j("#MyecardContactListId").attr("disabled","disabled");
                     } else {
                        $j("#MyecardRecipientAddingMethod").removeAttr("disabled");
                        $j("#MyecardContactListId").removeAttr("disabled");
                     }
                 }
            }
        },
        CardPreview: {
            onReady: function(params) {
                this.onReliability(params.reliability);
                this.onSubmit(params.submit);
            },
            onReliability: function(messages) {
                $j("#id_reliability_error").hide();
                $j("#id_reliability_error").text(messages.warning);
                $j("#email_reliable_delivery").on("change").change(function(event){
                    if (!$j("#email_reliable_delivery").is(":checked")) {
                        $j("#id_reliability_error").show();
                        alert(messages.popup);
                    } else {
                        $j("#id_reliability_error").hide();
                    }
                });
            },
            onSubmit: function(params) {
                $j("#content").on("click", "#btn_submit", function(event) {
                    event.preventDefault();
                    if ($j("#email_reliable_delivery").is(":checked")) {
                        window.location = params.url + '/T';
                    } else {
                        window.location = params.url + '/F';
                    }
                });
            }
        }
    },
    Invoices:{
        Shop:{
            onCalculateValueCredits:function(){
                $j('#credit').on('keyup', function(event){
                    checkValidCredit();
                });
            },
            onCalculateValueSenders:function(){
                $j('#sender').on('keyup', function(event){
                    checkValidSender();
                });
            }
        }
    },
    AdminDesignSetups: {
        onAccountAutomaticCardAdding: function(params) {
            $j("#AccountAutomaticCardAdding").on("change", function(event){
                $j.ajax({
                    url: params.url,
                    async: true,
                    type: "POST",
                    data: $j("#automatic_card_selection").serialize(),
                    success: function(response){
                        Ekarda.Tools.updateElements(response);
                    }
                });
            });
        }
    },
    TopMenus: {
        onClickSupportTab : function() {
            $j('li a#link_support').on("click", function(event){
                event.preventDefault();
                window.open('http://help.ekarda.com');
            });
        }
    }
}
$j.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ?
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}
