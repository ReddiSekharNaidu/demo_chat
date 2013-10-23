<?php $this->addScript($this->Html->script(array("tinyeditor/tiny.editor.packed"))) ?>
<div id="content-outer">
    <div id="content">
        <h1><?php __('ekarda Super Admin Panel') ?></h1>
        <?= $this->element('login_info') ?>
        <div class="float-fix"></div>
        <?= $this->element('_session_flash_msg') ?>
        <div class="box lrg">
            <h2><?php __('eCard Templates') ?></h2>
            <?= $form->create('CardTemplate',array('action'=>'add','type'=>'post','name'=>'Add_card_templates')) ?>
            <div class="field invalid">
                <span class="icn"></span>
                <label><?php __('Template Name:') ?></label>
                <?= $form->text('CardTemplate.name',array('maxLength' =>30)) ?>
                <p class="message"><?php __('Salutation can only contain A-Z characters') ?></p>
                <?= $form->error('CardTemplate.name') ?>
                <div id="custom_CardTemplateName" class="custom_CardTemplateName-class"></div>
            </div>
            <div class="field fckeditorLeft">
                <label><?php __('eCard HTML:') ?></label>
                <div id="ecard-html-id">
                    <?= $form->textarea('CardTemplate.html_content',array('class'=>'CardTemplateclss')) ?>
                </div>
                <div id="tag">
                    <p>
                        <?= $html->link('{subject}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)' ,'name'=>'{subject}','div'=>false)) ?>
                        <?= $html->link('{sender-name}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{sender-name}' ,'div'=>false)) ?>
                        <?= $html->link('{sender-email}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{sender-email}' ,'div'=>false)) ?>
                        <?= $html->link('{sender-company}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{sender-company}' ,'div'=>false)) ?>
                        <?= $html->link('{recipient-ID}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{recipient-ID}'  ,'div'=>false)) ?>
                        <?= $html->link('{recipient-name}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)', 'name'=>'{recipient-name}' ,'div'=>false)) ?>
                        <?= $html->link('{recipient-email}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)' ,'name'=>'{recipient-email}','div'=>false)) ?>
                        <?= $html->link('{logo}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)' ,'name'=>'{logo}','div'=>false)) ?>
                        <?= $html->link('{card-type}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{card-type}' ,'div'=>false)) ?>
                        <?= $html->link('{socialmedia}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{socialmedia}' ,'div'=>false)) ?>
                        <?= $html->link('{card-design}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{card-design}' ,'div'=>false)) ?>
                        <?= $html->link('{card-message}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{card-message}' ,'div'=>false)) ?>
                        <?= $html->link('{unsubscribe}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{unsubscribe}' ,'div'=>false)) ?>
                        <?= $html->link('{view-online}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)' ,'name'=>'{view-online}','div'=>false)) ?>
                        <?= $html->link('{footer-html}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{footer-html}','div'=>false)) ?>
                        <?= $html->link('{additional-greeting}','#tag',array('onclick'=>'return insetdata(document.Add_card_templates.CardTemplateHtmlContent, this.name)','name'=>'{additional-greeting}','div'=>false)) ?>
                    </p>
                </div>
            </div>
            <div class="field fckeditorLeft">
                <label><?php __('eCard Text Only:') ?></label>
                <div id="ecard-html-id">
                    <?= $form->textarea('CardTemplate.text_content',array('class'=>'CardTemplateclss')) ?>
                    <?= $form->error('CardTemplate.text_content',array('class'=>'noPadLeft CardTemplateclss3')) ?>
                </div>
                <p>
                    <?= $html->link('{subject}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)' ,'name'=>'{subject}','div'=>false)) ?>
                    <?= $html->link('{sender-name}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{sender-name}' ,'div'=>false)) ?>
                    <?= $html->link('{sender-email}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{sender-email}' ,'div'=>false)) ?>
                    <?= $html->link('{sender-company}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{sender-company}' ,'div'=>false)) ?>
                    <?= $html->link('{recipient-ID}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{recipient-ID}'  ,'div'=>false)) ?>
                    <?= $html->link('{recipient-name}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)', 'name'=>'{recipient-name}' ,'div'=>false)) ?>
                    <?= $html->link('{recipient-email}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)' ,'name'=>'{recipient-email}','div'=>false)) ?>
                    <?= $html->link('{logo}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)' ,'name'=>'{logo}','div'=>false)) ?>
                    <?= $html->link('{card-type}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{card-type}' ,'div'=>false)) ?>
                    <?= $html->link('{socialmedia}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{socialmedia}' ,'div'=>false)) ?>
                    <?= $html->link('{card-design}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{card-design}' ,'div'=>false)) ?>
                    <?= $html->link('{card-message}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{card-message}' ,'div'=>false)) ?>
                    <?= $html->link('{unsubscribe}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{unsubscribe}' ,'div'=>false)) ?>
                    <?= $html->link('{view-online}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)' ,'name'=>'{view-online}','div'=>false)) ?>
                    <?= $html->link('{footer-html}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{footer-html}','div'=>false)) ?>
                    <?= $html->link('{additional-greeting}','#tag',array('onclick'=>'return insertAtCursor(document.Add_card_templates.CardTemplateTextContent, this.name)','name'=>'{additional-greeting}','div'=>false)) ?>
                </p>
            </div>
            <?php if(isset($this->data['CardTemplate']['id']) && $this->data['CardTemplate']['id']>0): ?>
                <?= $form->hidden('id') ?>
            <?php endif; ?>
            <div id="clar_id">
                <?php if(isset($this->data['CardTemplate']['id']) && !empty($this->data['CardTemplate']['id'])): ?>
                    <?= $html->link(__('+ Add New Template HTML',true), array('controller' => 'card_templates', 'action' => 'add'),array('div' => false,'class'=>"btn orange table-manip tml_html-cls")) ?>
                    <?= $form->submit(__('Save',true), array('class'=>'submitBtn btn orange savebtnextra','div' => false)) ?>					
                <?php else: ?>
                    <?= $form->submit(__('Save',true), array('class'=>'submitBtn btn orange savebtnextra2','div' => false)) ?>
                <?php endif; ?>
                <?= $validanguage->printTags() ?>
                <?= $form->end() ?>
                <?= $form->button(__('Cancel',true), array('class'=>"submitBtn btn orange canbtnextra", 'onclick' => 'cancel();','onmouseover'=>'this.focus();')) ?> 
            </div>
        </div >
        <div class="actions">
            <?= $html->link(__('Back to Admin Panel<span></span>',true), array('controller' => 'users', 'action' => 'dashboard', 'superadmin' => true), array('class' => 'btn grey','escape'=>false)) ?>
        </div>
        <div class="float-fix"></div>
        <div class="push"></div>
    </div>
</div>
<?= $html->scriptStart() ?>
validanguage.settings.showErrorCustomArea = true;
validanguage.settings.showErrorCustomAreaFields = new Array('CardTemplateName');
function insertAtCursor(myField, myValue) {
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    } else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
        + myValue
        + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
    return false;
}
function insetdata(myField, myValue) {
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    } else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
        + myValue
        + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
    return false;
}
function cancel() {
	location.href="<?= $html->url(array('superadmin' => true, 'controller' => 'card_templates', 'action' => 'index'), true) ?>";
}
<?= $html->scriptEnd() ?>
