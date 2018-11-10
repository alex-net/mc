<?php

use yii\db\Migration;

/**
 * Class m180506_191110_create_session_tbl
 */
class m180506_191110_create_session_tbl extends Migration
{
   

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('sessi',[
            'id'=>$this->char(40)->notnull()->comment('Ключик'),
            'expire'=>$this->integer(),
            'data'=>$this->binary(),
        ]);
        $this->addPrimaryKey('sesipkey','sessi',['id']);
        echo "Таблица с данными сессий создана \n";
    }

    public function down()
    {
        $this->dropTable('sessi');
    }
    
}
