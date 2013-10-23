<div id="content">
	<h1><?php __('ekarda Admin') ?></h1>
	<?= $this->element('login_info') ?>
	<div class="float-fix"></div>
	<div id ='msg'> <?= $this->element('_session_flash_msg') ?> </div>
	<div class="box lrg" id="id1"> <?= $this->element(CARD_DESIGNS.'customdesign_request') ?> </div>
	<div class="actions"><?= $html->link(__('Back to Admin Panel<span></span>',true), array('controller' => 'users', 'action' => 'dashboard', 'superadmin' => true), array('class' => 'btn grey','escape'=>false)) ?> </div>
	<div class="float-fix"></div>
	<div class="push"></div>
</div>