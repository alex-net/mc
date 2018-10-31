<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="form-wrapper">
<h2>Написать нам</h2>
<?php $f=ActiveForm::begin(['options'=>['class'=>'write-uns-form'],]);?>
<?=$f->field($model,'name');?>
<?=$f->field($model,'mail');?>
<?=$f->field($model,'text')->textarea(['rows'=>4]);?>
<?=Html::submitButton('Написать',['class'=>'writer']);?>
<?php ActiveForm::end(); ?>
</div>