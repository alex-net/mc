<?php

use yii\db\Migration;

/**
 * Class m180529_151004_files
 */
class m180529_151004_files extends Migration
{


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('files',[
            'filename'=>$this->string(30)->notnull()->comment('Ключик таблицы'),
            'cid'=>$this->integer()->notnull()->comment('ссылка на контент'),
            'ct'=>$this->string(10)->notnull()->com_message_pump()nt('Тип контента (он же путь к папке с файлами'),
        ]);
        $this->addPrimaryKey('pk','files',['filename']);
    }

    public function down($fn)
    {
        //echo "m180529_151004_files cannot be reverted.\n";
        $this->dropTable('files');

        //return false;
    }

}
