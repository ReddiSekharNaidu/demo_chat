// JavaScript Document
var minDateAux = "-12Y";
var yearRangeAux = "-12:+12";
var editors = {};
$j(function(){
	$j("body").scroll(function(){
		alert("");
	});
    $j("#content").on("mouseover",".event-needs-fancybox",function(event){
        event.preventDefault();
        initializeFancyBoxes();
    });
    initializeFancyBoxes();
     /*$j("body").on("mouseover",'input[id^="dob_"], input.event-needs-datepicker',function(event){
        if (!$j(this).hasClass("hasDatepicker")){
            event.preventDefault();
            initializeDatePicker(minDateAux, yearRangeAux);
        }
    });*/
    /* Call DatePicker */
    if(typeof Ekarda != 'undefined'){
        Ekarda.Tools.calculateSearchTableHeadersPosition();
        Ekarda.MyEcards.Recipients.assignDatePicker();
        $j("#content").on("click",'.event-pagination a',function(event){
                event.preventDefault();
                $j("#"+$j(this).attr("update-element")).load(this.href);
            	return false;
        });
        Ekarda.Tools.searchTrigger();    	
    }
});
function initializeFancyBoxes(params){
    $j(".event-needs-fancybox").each(function(){
        var defaults = {
            'titlePosition'     : 'inside',
            'transitionIn'      : 'none',
            'transitionOut'     : 'none',
            'padding'           : '15',
            'autoDimensions'    : 'true'
        };
        $j.extend(defaults, params);
        $j(this).fancybox(defaults).removeClass("event-needs-fancybox");
    });
}
var messageTimeout;
function updateElements(response){
    if (typeof response == "string")
        response = $j(response);
    response.children("div").each(function(){
        $j("#" + $j(this).attr('id')).html($j(this).html())
        var currentReplacedId = $j(this).attr('id');
        if ( typeof currentReplacedId == "string" && currentReplacedId.match("\w*msg")){
        	clearTimeout( messageTimeout );
        	messageTimeout = window.setTimeout(function(){
        		$j("*[id$=msg]:not([id^=signup])").html("");
        	},2500);
        }
    })
}
/*
 * Initializes pagination for designs: /elements/fancybox/paginate.ctp
 */
function initializeFancyPaginate(){
    $j(".event-pages a").click(function(event){
        event.preventDefault();
        var pageIndex = $j(".event-pages a").index($j(this));
        var selectedPage = $j("#galleryslider_items_list li:eq("+pageIndex+")");
        selectedPage.siblings("li:visible").fadeOut(function(){
            selectedPage.fadeIn();
        })
        $j(this).addClass("on").siblings("a").removeClass('on');
    });
    $j(".event-pages a:eq(0)").click();
}
function initializeDatePicker(minDate, yearRange){
    minDateAux = minDate;
    yearRangeAux = yearRange;
    $j('input[id^="dob_"], input.event-needs-datepicker')
            .datepicker(
                    {
                        dateFormat : 'dd/mm/yy',
                        minDate : minDate,
                        yearRange : yearRange,
                        changeYear : true,
                        changeMonth : true,
                        maxDate : 0
                    }).removeClass("event-needs-datepicker");
}
function initializeTinyEditors(){
	$j(".event-needs-tinyEditor").each(function(){
		var tinyEditor = $j(this);
		var tinyId = tinyEditor.attr("id");
		editors[tinyId] = new TINY.editor.edit('editor', {
			id: tinyId,
			width: 584,
			height: 175,
			cssclass: 'tinyeditor',
			controlclass: 'tinyeditor-control',
			rowclass: 'tinyeditor-header',
			dividerclass: 'tinyeditor-divider',
			controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
				'orderedlist', 'unorderedlist', '|', 'outdent', 'indent', '|', 'leftalign',
				'centeralign', 'rightalign', 'blockjustify', '|', 'unformat', '|', 'undo', 'redo', 'n',
				'font', 'size', 'style', '|', 'image', 'hr', 'link', 'unlink', '|', 'print'],
			footer: true,
			fonts: ['Verdana','Arial','Georgia','Trebuchet MS'],
			xhtml: true,
			cssfile: 'custom.css',
			bodyid: 'editor',
			footerclass: 'tinyeditor-footer',
			toggle: {text: 'source', activetext: 'HTML', cssclass: 'toggle'},
			resize: {cssclass: 'resize'}
		});
		tinyEditor.removeClass("event-needs-tinyEditor");
	});
}
