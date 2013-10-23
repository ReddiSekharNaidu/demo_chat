<?= $this->element('_navigation_links') ?>
&nbsp;
<h1><?php __('Edit eCard Design') ?></h1>
<?= $form->create('CardDesign', array('enctype' => 'multipart/form-data')) ?>
<label><?php __('Select File') ?></label>
<?= $form->file('upload file',array('type' => 'file','div'=>false)) ?>
<label><?php __('Name') ?></label>
<?= $form->text('title',array('div'=>false)) ?>
<?= $form->hidden('id') ?>
<?= $form->submit('Save') ?>
<?= $form->end() ?>
