<?= $html->scriptStart() ?>
function getDesc(val)
{
	window.location.href = "<?= $html->url(array('controller' => 'cms_contents', 'action' => 'index', 'admin' => true)) ?>"+val;
	return true;
}
<?= $html->scriptEnd() ?>
<?php
	$underCompany = array('How CorkScroo Started', 'Executive Team', 'Investors');
	for($i=1; $i<= count($cms); $i++) :
		if(in_array($cms[$i], $underCompany)):
			$s = '__'.$cms[$i];
		else:
			$s = $cms[$i];
		endif;
		$cms1[$i] = $s;
	endfor;
?>
<?= $this->renderElement('cms/admin/admin_managecontent') ?>
<?php $validanguage->printTags() ?>
<!-- rightpart starts Here-->
<div class="RightPart">
	<div class="right_content">
		<div>
			<h3><?php __('CMS') ?></h3>
			<div id="vdErrorDiv" class="RSVError CenterError" ></div>
			<div class="row_setFlash_class" align="center" id="row_setFlash">
				<?php
					if($session->check('Message.flash')):
						$session->flash();
					endif;
				?>
			</div>
			<?= $form->create('CmsContent',array('name'=>'CmsContent','type'=>'post','url'=>array('controller' => 'cms_contents', 'action' => 'index', 'admin' => true))) ?>
			<div class="marginbtm">
				<p class="textlable textclslable"><?php __('Select Page:') ?></p>
				<?= $form->select('CmsContent.id',$cms1,$id,array('class'=>'listmenu','id'=>'cms_content','onChange'=>'getDesc(this.value);'),false) ?>
			</div>
			<div class="marginbtm">
				<p class="textlable textclslable"><?php __('Content:') ?></p>
				<div id="t-area-id">
					<?= $form->textarea('CmsContent.description',array('rows' => 5, 'cols' => 5)) ?>
					<?= $fck->load('CmsContent.description') ?>
				</div>
			</div>
			<div class="btn tbtnclslable"><?= $form->submit(__('Update',true),array('class'=>'SubmitBtn btn orange')) ?></div>
			<?= $form->end() ?> 
		</div>
	</div>
</div>
<!-- rightpart ends Here-->
