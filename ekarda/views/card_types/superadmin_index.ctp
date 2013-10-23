<div id="content">
	<h1><?php __('ekarda Super Admin Panel') ?></h1>
	<?= $this->element('login_info') ?>
	<div class="float-fix"></div>
	<div id="card_type_msg"> <?= $this->element('_session_flash_msg') ?> </div>
	<div class="box lrg" id='card_type_list'><?= $this->element(CARD_TYPES.'list_card_types') ?> </div>
	<div class="actions">
		<?= $html->link(__('Back to Admin Panel<span></span>',true), array('superadmin' => true, 'controller'=>'users', 'action' => 'dashboard'), array('class' => 'btn grey','escape'=>false)) ?>
	</div>
</div>
<?php $html->scriptStart() ?>
$j(function(){
    Ekarda.Shared.onConfirmationLinks({
        "wrapperSelector": '#card_type_list',
        "confirmMessage": '<?php __("Are you sure that you want to delete?"); ?>'
    });
});
<?= $html->scriptEnd() ?>