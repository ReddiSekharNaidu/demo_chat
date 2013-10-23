<?= $html->script(array('support.function-1.7.2')) ?>
<div id="content">
	<h1><?php __('ekarda Super Admin Panel') ?></h1>
	<?= $this->element('login_info') ?>
	<div class="float-fix"></div>
	<div id="msg">
		<?= $this->element('_session_flash_msg') ?>
	</div>
	<div class="box lrg" id="custom_carddesign_id">
		<?= $this->element(CARD_DESIGNS.'custom_carddesign') ?>
	</div>
	<div class="actions">
		<?= $html->link(__('Back to Admin Panel<span></span>', true), array('controller' => 'users', 'action' => 'dashboard', 'superadmin' => true), array('class' => 'btn grey','escape'=>false)) ?>
	</div>
	<div class="float-fix"></div>
	<div class="push"></div>
</div>
<?= $html->scriptStart() ?>
$j(function(){
	Ekarda.Shared.onEditLinks({
		"wrapperSelector": "#custom_carddesign_id"
	});
	Ekarda.Shared.onNavigation({
		"wrapperSelector": "#custom_carddesign_id"
	});
    Ekarda.Shared.onConfirmationLinks({
		"wrapperSelector": "#custom_carddesign_id",
		"confirmMessage": "<?= CUSTOME_DESIGN_CANCEL_CONFIRMATION ?>",
		"elementsSelector": 'a[id^="cancel_item_"]'
	});
    Ekarda.Shared.onConfirmationLinks({
		"wrapperSelector": "#custom_carddesign_id",
		"confirmMessage": "<?= CUSTOME_DESIGN_REINSTATE_CONFIRMATION ?>",
		"elementsSelector": 'a[id^="reinstantiate_item_"]'
	});
	Ekarda.Shared.onSearchFields({
		"wrapperSelector": "#custom_carddesign_id",
		"neededFormSelector": "#CustomCardDesignRequestCustomCarddesignForm",
		"eventType": "keyup"
	});
})
<?= $html->scriptEnd() ?>