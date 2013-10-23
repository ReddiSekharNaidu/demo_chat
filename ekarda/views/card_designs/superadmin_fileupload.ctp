<?= $this->Form->create(__('CardType',true), array('enctype' => 'multipart/form-data')) ?>
<?= $this->Form->input(__('upload file',true), array('type' => 'file')) ?>
<?= $this->Form->end(__('Upload', true)) ?>
