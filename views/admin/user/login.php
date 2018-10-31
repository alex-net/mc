<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Html;
$form=ActiveForm::begin();

echo $form->field($u,'name');
echo $form->field($u,'pass');
echo Html::submitButton('Вход',['class'=>'btn btn-success']);
ActiveForm::end();

?>