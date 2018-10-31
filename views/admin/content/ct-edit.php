<?php 
use yii\helpers\Html; 
use yii\widgets\ActiveForm;
use app\widgets\McFilesViewWidget;

\app\assets\CKEditorAsset::register($this);
$titles=[
	'page'=>$id?'Редактирование текстовой страницы':'Новая текстовая страница',
	'new'=>$id?'Редактирование новости':'Новая новость',
	'album'=>$id?'Редактирование альбома':'Новый альбом',
];
?>
<h1><?=$titles[$type];?></h1>


<?php 
	$f=ActiveForm::begin(['options'=>['class'=>'content-creator-form']]);
	echo $f->errorSummary($model);
?>
<?=$f->field($model,'title');?>
<?=$f->field($model,'cid',['template'=>'{input}'])->hiddenInput();?>
<?=$f->field($model,'type',['template'=>'{input}'])->hiddenInput();?>

<?=$f->field($model,'teaser',['template'=>"{label}<div>{input}\n{hint}\n{error}</div>"])->textarea(['rows'=>5]);?>


<?= $f->field($model,'body')->textarea(['rows'=>10,]);?>
<?=$f->field($model,'status')->checkbox();?>

<div class="alias-settings">
	<label>Настройки алиаса (текущий: <?=$model->alias;?>)</label>
	<div >
<?=$f->field($model,'aliasisuser')->checkbox(); ?>
<?=$f->field($model,'alias');?>
</div>
</div>
<div class="file-settings">
	<label>Загрузить файл(ы)</label>
	<div>
		<?php if ($model->type=='album'):?>
			<?=$f->field($files,'files[]')->fileinput(['multiple'=>'multiple']);?>
		<?php else:?>
			<?=$f->field($files,'files')->fileinput();?>
			
		<?php endif;?>
		<?=McFilesViewWidget::widget(['cid'=>$model->cid,'preset'=>'thumb']);?>
	</div>
	
</div>

<div class="add-menu">
	<label>Добавить пункт меню (<?=$menu->title?$menu->title:'без пункта меню'?>)</label>
	<div>
		<?=$f->field($model,'nomenuitem')->checkbox();?>
		<?=$f->field($menu,'title');?>
		<?=$f->field($menu,'mid',['template'=>'{input}'])->hiddenInput();?>
		<?=$f->field($menu,'pmid')->dropDownList($menu->generatemMenuOptions());?>
	</div>
</div>

<div class="footer">
	<?=Html::submitButton('Сохранить',['class'=>'btn btn-success','name'=>'save']);?>
	<?php if($id):?>
		<?=Html::submitButton('Удалить',['class'=>'btn btn-danger','name'=>'kill']);?>
	<?php endif;?>
</div>
<?php 
	ActiveForm::end();
?>

