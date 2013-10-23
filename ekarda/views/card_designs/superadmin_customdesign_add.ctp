<div id="content">
	<h1><?php __('ekarda Admin') ?></h1>
	<?= $this->element('login_info') ?>
	<div class="float-fix"></div>
	<?= $this->element('_session_flash_msg') ?>
	<div class="box lrg box_lrg1"> <?= $this->element(CARD_DESIGNS.'add_custom_design') ?> </div>
	<div class="actions"> <?= $html->link(__('Back to Admin Panel<span></span>',true),  array('controller' => 'users', 'action' => 'dashboard', 'superadmin' => true), array('class' => 'btn grey','escape'=>false)) ?> </div>
	<div class="float-fix"></div>
	<div class="push"></div>
</div>
