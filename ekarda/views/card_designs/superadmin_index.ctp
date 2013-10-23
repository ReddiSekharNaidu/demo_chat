<?= $html->script(array('support.function-1.7.2')) ?>
<div id="content">
	<div id="sub-content-id">
		<h1><?php __('ekarda Super Admin Panel') ?></h1>
		<?= $this->element('login_info') ?>
		<div class="float-fix"></div>
		<div id="card_design_msg"> <?= $this->element('_session_flash_msg') ?> </div>
		<div class="box lrg" id='card_design_list'><?= $this->element(CARD_DESIGNS.'list_card_design') ?> </div>
		<div class="actions">
			<?= $html->link(__('Back to Admin Panel<span></span>',true), array('controller' => 'users', 'action' => 'dashboard', 'superadmin' => true), array('class' => 'btn grey','escape'=>false)) ?>
		</div>
	</div>
</div>
<div class="space_for_ie">&nbsp;</div>
<?= $html->scriptStart() ?>
$j(function(){
	var params = {
		"wrapperSelector": "#card_design_list",
		"elementsSelector": 'select#CardDesignCardTypeId',
		"neededFormSelector": "#CardDesignCardTypeId",
		"eventType": "change"
	};
	Ekarda.Shared.onSearchFields(params);
    Ekarda.Shared.onConfirmationLinks({
        "wrapperSelector": '#card_design_list',
        "confirmMessage": '<?php __("Are you sure that you want to delete?"); ?>'
    });
	$j("#logo").focus();
})
function check_validatation(id){
    re = /^[A-Za-z0-9 ]+$/;
    var imagetitle = "CardDesignTitle" + id;
    var image_title = document.getElementById(imagetitle).value;
    var imagetitle = "CardDesignTitle" + id;
    var card_type = document.getElementById('CardDesignCardTypeId').value;
    //return false;
    //var fornname ="frmcardtype"+id;
    var extensions = new Array();
    extensions[1] = "jpg";
    extensions[0] = "jpeg";
    extensions[2] = "gif";
    extensions[3] = "png";
    extensions[4] = "bmp";
    var text_extensions = new Array();
    text_extensions[0] = "dtm";
    var imagename = "CardDesignImgpath" + id;
    var image_file = document.getElementById(imagename).value;
    var image_length = document.getElementById(imagename).value.length;
    var pos = document.getElementById(imagename).value.lastIndexOf('.') + 1;
    var ext = image_file.substring(pos, image_length);
    var final_ext = ext.toLowerCase();
    var flag = false;
    for (i = 0; i < extensions.length; i++) {
        if (extensions[i] == final_ext) {
            flag = true;
        }
    }
    var filename = "CardDesignMotif" + id;
    var motif_file = document.getElementById(filename).value;
    var file_length = document.getElementById(filename).value.length;
    var pos1 = document.getElementById(filename).value.lastIndexOf('.') + 1;
    var motif_ext = motif_file.substring(pos1, file_length);
    var motif_final_ext = motif_ext.toLowerCase();
    var motf_flag = false;
    for (k = 0; k < text_extensions.length; k++) {
        if (text_extensions[k] == motif_final_ext) {
            motf_flag = true;
        }
    }
    if (card_type != '') {
        if (image_title == '') {
            showError('Please Enter name for card type', imagetitle);
            if (!flag) {
                if (image_length != 0) {
                    showError('Incorrect image type!', imagename);
                    if (!motf_flag) {
                        if (file_length != 0) {
                            showError('Incorrect file type!', filename);
                            return false;
                        }
                    }
                    else {
                        showError('', filename);
                    }
                }
            }
            else {
                showError('', imagename);
                if (!motf_flag) {
                    if (file_length != 0) {
                        showError('Incorrect file type!', filename);
                        return false;
                    }
                }
                else {
                    showError('', filename);
                }
            }
            if (!motf_flag) {
                if (file_length != 0) {
                    showError('Incorrect file type!', filename);
                    return false;
                }
            }
            else {
                showError('', filename);
            }
            return false;
        }
        else {
            showError('', imagetitle);
            if (!re.test(image_title)) {
                showError('Please enter alphabet and numeric only', imagetitle);
                if (!flag) {
                    if (image_length != 0) {
                        showError('Incorrect file type!', imagename);
                        if (!motf_flag) {
                            if (file_length != 0) {
                                showError('Incorrect file type!', filename);
                                return false;
                            }
                        }
                        return false;
                    }
                }
                else {
                    showError('', imagename);
                    if (!motf_flag) {
                        if (file_length != 0) {
                            showError('Incorrect file type!', filename);
                            return false
                        }
                    }
                    else {
                        showError('', filename);
                    }
                }
                return false;
            }
            else {
                if (!flag) {
                    if (image_length != 0) {
                        showError('Incorrect image type!', imagename);
                        if (!motf_flag) {
                            if (file_length != 0) {
                                showError('Incorrect file type!', filename);
                                return false;
                            }
                        }
                        else {
                            showError('', filename);
                        }
                        return false;
                    }
                    else {
                        showError('', imagename);
                        if (!motf_flag) {
                            if (file_length != 0) {
                                showError('Incorrect file type!', filename);
                                return false;
                            }
                        }
                        else {
                            showError('', filename);
                        }
                    }
                }
                else {
                    showError('', imagename);
                    ;
                    if (!motf_flag) {
                        if (file_length != 0) {
                            showError('Incorrect file type!', filename);
                            return false;
                        }
                    }
                    return true;
                }
            }
            return true;
        }
    }
    else {
        showError('Please select card type for card design', 'CardDesignCardTypeId');
        if (image_title == '') {
            showError('Please Enter name for card design', imagetitle);
            if (!flag) {
                if (image_length != 0) {
                    showError('Incorrect image type!', imagename);
                    if (!motf_flag) {
                        if (file_length != 0) {
                            showError('Incorrect file type!', filename);
                            return false;
                        }
                        else {
                            showError('', filename);
                            return false;
                        }
                    }
                }
            }
            else {
                showError('', imagename);
                if (!motf_flag) {
                    if (file_length != 0) {
                        showError('Incorrect file type!', filename);
                        return false;
                    }
                }
                else {
                    showError('', filename);
                    return false;
                }
            }
            return false;
        }
        else {
            showError('', imagetitle);
            if (!flag) {
                if (image_length != 0) {
                    showError('Incorrect image type!', imagename);
                    if (!motf_flag) {
                        if (file_length != 0) {
                            showError('Incorrect file type!', filename);
                            return false;
                        }
                        else {
                            showError('', filename);
                            return false;
                        }
                    }
                    return false;
                }
                else {
                    showError('', imagename);
                    if (!motf_flag) {
                        if (file_length != 0) {
                            showError('Incorrect file type!', filename);
                            return false;
                        }
                        else {
                            showError('', filename);
                        }
                    }
                }
            }
            else {
                if (!motf_flag) {
                    if (file_length != 0) {
                        showError('Incorrect file type!', filename);
                        return false;
                    }
                }
                else {
                    showError('', filename);
                }
            }
            return false;
        }
        return false;
    }
}
function showError(errorMsg, id){
    jQuery("#" + id + "_error").html(errorMsg);
}
function uniquecarddesign(id){
    if (check_validatation(id)) {
        return true;
    }
    else {
        return false;
    }
}
function scollertop(){
    jQuery('html').animate({
        scrollTop: 0
    }, 'slow');
}
<?= $html->scriptEnd() ?>
