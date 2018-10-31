<?php 

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;

?><h1>
<?php if($bid):?>
	Редактирование блока
<?php else: ?>
	Новый блок
<?php endif;?>
</h1>

<?php $f=ActiveForm::begin();?>
<?=$f->field($b,'id',['template'=>'{input}'])->hiddenInput();?>
<?=$f->field($b,'content')->textarea(['rows'=>5]);?>
<?=$f->field($b,'status')->checkbox();?>
<?=$f->field($b,'cid')->dropDownlist(\app\models\Content::getListptions());?>
<?=Html::submitButton('Сохранить',['class'=>'btn btn-success','name'=>'save']);?>
<?php if($bid):?>
	<?=Html::submitButton('Удалить',['class'=>'btn btn-danger','name'=>'kill']);?>
<?php endif;?>
<?php ActiveForm::end();?>