<?php

use yii\db\Migration;

/**
 * Class m180521_193656_menu_tbls
 */
class m180521_193656_menu_tbls extends Migration
{
    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        // элементы меню 
        $this->createTable('menuitems',[
            'mid'=>$this->primaryKey()->comment('Ключик менюшки'),
            'pmid'=>$this->integer()->unsigned()->defaultValue(0)->comment('Родительский элемент'),
            'status'=>$this->boolean()->defaultValue(1)->comment('Статус стрницы'),
            'title'=>$this->string(128)->notNull()->comment('Заголовок пункта меню'),
            'weight'=>$this->integer()->defaultValue(0)->comment('Вес пункта меню'),
            'contentid'=>$this->integer()->unsigned()->comment('ID контента '),
            'url'=>$this->string(128)->comment('Возможный адрес'),
        ]);
    }

    public function down()
    {
        $this->dropTable('menuitems');
    }
    
}
