<?= $html->script(array('support.function-1.7.2')) ?>
<div id="content">
	<h1><?php __('ekarda Super Admin Panel') ?></h1>
	<?= $this->element('login_info') ?>
	<div class="float-fix"></div>
	<div id="msg"> <?= $this->element('_session_flash_msg') ?> </div>
	<div class="box lrg" id="edit_custom_carddesign_div"> <?= $this->element(CARD_DESIGNS."edit_custom_carddesign") ?> </div>
	<?php if(isset($custom_carddesign_request_obj)): ?>
			<?php if(in_array($custom_carddesign_request_obj['CustomCardDesignRequest']['status'], array('paid','completed'))): ?>
				<div class="box lrg"> <?= $this->element(CARD_DESIGNS."edit_custom_carddesign_request") ?> </div>
			<?php endif; ?>
	<?php endif; ?>
	<div class="actions"> <?= $html->link(__('Back to Admin Panel<span></span>',true), array('controller' => 'users', 'action' => 'dashboard', 'superadmin' => true), array('class' => 'btn grey','escape'=>false)) ?> </div>
	<div class="float-fix"></div>
	<div class="push"></div>
</div>
<?= $html->scriptStart() ?>
$j(function(){
	params = {
		"wrapperSelector": "#edit_custom_carddesign_div",
		"confirmMessage": "<?= __('Are you sure that you want to delete?',true) ?>"
	};
    Ekarda.Shared.onConfirmationLinks(params);
})
<?= $html->scriptEnd() ?>