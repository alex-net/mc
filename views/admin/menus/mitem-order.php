<?php 

use yii\helpers\Html;

use app\widgets\MenuSorterWidget;
?>
<?=Html::a('Добавить пункт меню',['admin/menus/elitem','mid'=>0]);?>
<?=MenuSorterWidget::widget(['menulist'=>$mlist]);?>