<?php

use yii\db\Migration;

/**
 * Class m180606_101535_blocks
 */
class m180606_101535_blocks extends Migration
{

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createtable('blocks',[
            'id'=>$this->primaryKey()->comment('Ключик'),
            'content'=>$this->text()->comment('Содержимое'),
            'weight'=>$this->integer()->defaultValue(0)->comment('Вес'),
            'status'=>$this->boolean()->defaultValue(true)->comment('Статус'),
            'cid'=>$this->integer()->unsigned()->comment('Ссылка на отображаемый контент'),
        ]);
    }

    public function down()
    {
        $this->dropTable('blocks');
        //echo "m180606_101535_blocks cannot be reverted.\n";

        //return false;
    }
    
}
