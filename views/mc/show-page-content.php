<div>
	<?= \app\widgets\SiteAdminWidget::widget(['cid'=>$model->cid,'ctype'=>$model->type]);?>
	<div class='content'>
	<?=$model->body;?>
	</div>
</div>