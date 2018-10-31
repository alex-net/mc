<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\Content;
?>
<?php if($m->mid):?>
	<h1>Редактирование пункта меню</h1>
<?php else:?>
	<h1>Новый пункт меню</h1>
<?php endif;?>
<?php $f=ActiveForm::begin();?>
<?=$f->field($m,'title');?>
<?=$f->field($m,'mid',['template'=>'{input}'])->hiddenInput();?>
<?=$f->field($m,'status')->checkbox();?>

<?=$f->field($m,'contentid')->dropDownlist(Content::getListptions());?>
<?=$f->field($m,'url');?>
<?=Html::submitButton('Сохранить',['class'=>'btn btn-success','name'=>'save']);?>
<?=Html::submitButton('Удалить',['class'=>'btn btn-danger','name'=>'kill']);?>

<?php ActiveForm::end();?>

