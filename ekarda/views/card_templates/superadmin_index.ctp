<div id="content">
	<h1><?php __('ekarda Super Admin Panel') ?></h1>
	<?= $this->element('login_info') ?>
	<div class="float-fix"></div>
	<div id="msg"><?= $this->element('_session_flash_msg') ?></div>
	<div class="box lrg"> <?= $form->create('CardTemplate', array('action'=>'index','enctype'=>"multipart/form-data",'type'=>'file','inputDefaults' => array('label' => false,'escape'=>false,'div' => false))) ?>
	<div class="field">
		<h2><?php __('Card Templates') ?></h2>
		<table class="table-head" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th class="sort-down"><?php __('Template Name') ?></th>
					<th><?php __('Date Created') ?></th>
					<th><?php __('Action') ?></th>
				</tr>
			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" id="table-content-id" class="table-content">
			<colgroup>
				<col width="35%">
				<col width="38%">
				<col width="26%">
				<col width="25%">
				<col width="13%">
				<col width="14%">
				<col width="14%">
				<col width="5%">
				<col width="6%">
			</colgroup>
			<tbody>
				<?php if(count($result)>0):
						foreach($result as $data): ?>
						<tr>
							<td><?= wordwrap($data['CardTemplate']['name'],35,'<br/>',true) ?></td>
							<td><?= wordwrap(date('d M Y',strtotime($data['CardTemplate']['created'])),11,"\n",1) ?></td>
							<td><?= $html->link(__('Edit',true), array('controller' => 'card_templates', 'action' => 'add',$data['CardTemplate']['id'])) ?>
								<?= $html->link(__('Delete',true), array('controller' => 'card_templates', 'action' => 'delete',$data['CardTemplate']['id']), null,ALERT_DELETE_MSG) ?>
							</td>
						</tr>
						<?php endforeach; ?>
				<?php else: ?>
						<tr>
							<td colspan="9" align="center"><?= NO_RECORDS_FOUND ?></td>
						</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?= $validanguage->printTags() ?>
		<?= $this->element('paging') ?>
		<?= $form->end() ?>
		<?= $html->link(__('+ Add New Template HTML',true), array('controller' => 'card_templates', 'action' => 'add'),array('div' => false,'class'=>"btn orange table-manip addnewtpl")) ?>
	</div>
	</div>
	<div class="actions"> <?= $html->link(__('Back to Admin Panel<span></span>',true),  '/superadmin/users/dashboard', array('class' => 'btn grey','escape'=>false)) ?> </div>
	<div class="float-fix"></div>
	<div class="push"></div>
</div>
