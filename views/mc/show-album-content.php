<div>
	<?= \app\widgets\SiteAdminWidget::widget(['cid'=>$model->cid,'ctype'=>$model->type]);?>
	<h1><?=$model->title; ?></h1>
	<div class='content'>
		<?=$model->body;?>
		<?=\app\widgets\AlbumImageslist::widget(['cid'=>$model->cid,'preset'=>'albums-preset']);?>
		

	</div>
</div>