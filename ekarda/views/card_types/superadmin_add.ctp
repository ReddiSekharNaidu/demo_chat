<div id="content">
	<h1><?php __('ekarda Admin') ?></h1>
	<?= $this->element('login_info') ?>
	<div class="float-fix"></div>
	<?= $this->element('_session_flash_msg') ?>
	<div class="box lrg box_lrg1">
		<h2><?php __('Add Card Types') ?></h2>
		<?= $form->create('CardType', array('name' =>'frmcardtype','id' => 'frmcardtype','controller' =>'CardType','action' => 'add','enctype' => 'multipart/form-data','type' => 'file','div' => false)) ?>
		<div class="field">
			<div id="admtabledivs">
				<table class="admtable">
					<tbody>
						<tr>
							<td><label><?php __('Image :') ?></label></td>
							<td>
								<?= $form->file('CardType.imgpath',array('type' => 'file','div'=>false,'tabindex' => '1')) ?>
								<?= $form->error('CardType.imgpath') ?>
							</td>
						</tr>
						<tr>
							<td><label><?php __('Name :') ?></label></td>
							<td>
								<?= $form->text('CardType.title',array('div'=>false,'tabindex' => '2','class' =>'main','maxLength' =>30)) ?>
								<?= $form->error('CardType.title') ?>
							</td>
						</tr>
                        <tr>
                            <td><label><?php __('Tag :') ?></label></td>
                            <td>
                                <?= $form->text('CardType.tagName',array('div'=>false,'tabindex' => '2','class' =>'main','maxLength' =>30)) ?>
                                <?= $form->error('CardType.tagName') ?>
                            </td>
                        </tr>
						<tr>
							<td></td>
							<td>
								<?= $form->submit(__('Save',true), array('class' => 'btn orange', 'div' => false,'tabindex' => '3')) ?>
								<?= $form->button(__('Cancel',true), array('type'=>'button','class'=>'btn orange btnsvcls', 'onclick' => 'javascript:history.go(-1)','onmouseover'=>'this.focus();')) ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?= $validanguage->printTags() ?>
		<?= $form->end() ?>
	</div>
	<div class="actions">
		<?= $html->link(__('Back to Admin Panel<span></span>',true), array('superadmin' => true, 'controller'=>'users', 'action' => 'dashboard'), array('class' => 'btn grey','escape'=>false)) ?>
	</div>
	<div class="float-fix"></div>
	<div class="push"></div>
</div>
