<title><?php __('ekarda') ?></title>
<?= $html->css(array('style','custom-theme/jquery-ui-1.8.14.custom')) ?>
<?= $html->script(array('js/jquery-ui.min-1.8.23.js')) ?>
<?= $html->scriptStart() ?>
	$j(function() {
		$j( "#tabs" ).tabs();
	});
<?= $html->scriptEnd() ?>
</head>
<body>
<div id="headcon"> </div>
<div id="content-outer">
	<div id="content">
		<h1><?php __('User Admin') ?></h1>
		<p class="summary"><?php __('Logged in as ') ?><?= $html->link(__('Jane Weathers',true),'#') ?><?php __(' with ') ?><?= $html->link(__('25 credits',true),'#') ?><?php __(' remaining') ?></p>
		<div class="float-fix"></div>
		<div class="box lrg">
			<h2><?php __('eCard Setup') ?></h2>
			<div class="eCard-set-cls">
				<table cellpadding="0" cellspacing="0" class="eCard-set-cls-tbl">
					<tr>
						<td><h3><?php __('Logos ') ?><span id="logs-cls"><?= $html->link(__('hide',true),'#') ?></span></h3></td>
					</tr>
					<tr>
						<td><p><?php __('Uploaded logos') ?></p></td>
					</tr>
					<?= $html->link(__('hide',true),'#') ?>
					<?= $html->link($this->element(CARD_DESIGNS.'helement'),'#') ?>
					<tr>
						<td>
							<label class="lbl-size-cls"><?php __('Size:') ?></label>
							<?php $position = array('Small'=>'Small','Medium'=>'Medium','Large'=>'Large'); ?>
							<?= $form->select('select',$position,NULL,array('empty'=>false, 'class' => 'sel-class-1')) ?>
						</td>
						<td>
							<label class="lbl-size-cls"><?php __('Size:') ?></label>
							<?php $position = array('Small'=>'Small','Medium'=>'Medium','Large'=>'Large'); ?>
							<?= $form->select('select',$position,NULL,array('empty'=>false,'class' => 'sel-class-1')) ?>
						</td>
					</tr>
					<tr>
						<td id="new-logo"><p><?php __('Add a new logo') ?></p></td>
					</tr>
					<tr>
						<td><?= $form->text('text',array()) ?></td>
						<td><?= $form->submit(__('browse',true),array('div'=>false,'class'=>"btn orange extbrowsbtn",'value'=>"browse")) ?> <?= $form->submit(__('upload',true),array('div'=>false,'class'=>"btn orange extupldbtn",'value'=>"upload")) ?> </td>
					</tr>
				</table>
				<h3><?php __('Social Media') ?></h3>
				<p><?php __('Select the Social Media links you wish to inlcude in your eCard') ?></p>
				<p><?= $form->checkbox( 'checkbox', array( )) ?><?= __("&nbsp;Facebook", true) ?></p>
				<p><?= $form->checkbox( 'checkbox', array( )) ?><?= __("&nbsp;Twitter", true) ?></p>
				<h3><?php __('Setup eCard') ?></h3>
				<label id="lbl-type"><?php __('Please select eCard type:') ?></label>
				<td><?php $position =array('Custom'=>'Custom','Christmas'=>'Christmas','Birthday'=>'Birthday','Easter'=>'Easter'); ?>
					<?= $form->select('select',$position,NULL,array('empty'=>false)) ?>
				</td>
			</div>
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
					<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"> <?= $html->link(__('Step 1: Design',true), '#tabs-1') ?> </li>
					<li class="ui-state-default ui-corner-top"> <?= $html->link(__('Step 2: Message',true), '#tabs-2') ?> </li>
					<li class="ui-state-default ui-corner-top"> <?= $html->link(__('Step 3: Footer',true), '#tabs-3') ?> </li>
				</ul>
			</div>
			<div id="tabs-1"> <?= $this->element(CARD_DESIGNS.'taste-1') ?> </div>
			<div id="tabs-2"> <?= $this->element(CARD_DESIGNS.'taste-2') ?> </div>
			<div id="tabs-3"> <?= $this->element(CARD_DESIGNS.'taste-3') ?> </div>
			<ul class="selector">
				<li><?php __('Select: ') ?><?= $html->link(__('All',true), '#') ?></li>
				<li><?= $html->link(__('None',true), '#') ?></li>
			</ul>
			<?= $html->link(__('Save',true), 'admin-addsender.html', array('id' => 'link-save-id', 'class' => 'btn orange table-manip')) ?>
		</div>
	</div>
</div>
</div>
<div class="float-fix"></div>
<div class="push"></div>
</div>
</div>
</body>
</html>