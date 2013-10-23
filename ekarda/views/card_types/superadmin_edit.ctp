<?= $html->link(__('Dashboard',true), array('controller' => 'users', 'action' => 'dashboard')) ?>
&nbsp;&nbsp;
<?= $html->link(__('Go Back',true), array('controller' => 'card_types', 'action' => 'index')) ?>
<h1><?php __('Edit eCard Type') ?></h1>
<?= $form->create('CardType', array('enctype' => 'multipart/form-data')) ?>
<label><?php __('Select File') ?></label>
<?= $form->file('upload file',array('type' => 'file','div'=>false)) ?>
<label><?php __('Name') ?></label>
<?= $form->text('title',array('div'=>false)) ?>
<?= $form->hidden('id') ?>
<?= $form->submit(__('Save',true)) ?>
<?= $form->end() ?>
