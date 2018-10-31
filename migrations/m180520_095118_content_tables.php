<?php

use yii\db\Migration;

/**
 * Class m180520_095118_content_tables
 */
class m180520_095118_content_tables extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        
        $this->createTable('content',[
            'cid'=>$this->primaryKey()->comment('Ключик контента'),
            'title'=>$this->string(100)->notNull()->comment('Заголоовк'),
            'type'=>$this->string(15)->notNull()->comment('тип контента: новости статьи альбомы'),
            'alias'=>$this->string(120)->notNull()->comment('Алиас'),
            'aliasisuser'=>$this->boolean()->comment('Алиас указал юзер'),
            'owner'=>$this->integer()->unsigned()->comment('Кто создал'),
            'status'=>$this->boolean()->defaultValue(1)->comment('Статус'),
            'body'=>$this->text()->comment('Содержимое'),
            'teaser'=>$this->text()->comment('Содержимое'),
            'created'=>$this->integer()->comment('Создан'),
            'nomenuitem'=>$this->boolean()->defaultValue(0)->comment('Без пункта меню'),
        ]);
        echo "Таблица контента создана \n";

        $this->createTable('files',[
            'cid'=>$this->integer()->notNull()->unsigned()->comment('Контент с файом'),
            'filename'=>$this->string(20)->notnull()->comment('Файл'),
        ]);
        $this->createIndex('cid','files',['cid']);
    }

    public function down()
    {
        //echo "m180520_095118_content_tables cannot be reverted.\n";
        //$this->dropTable('content');
        foreach(\app\models\Content::ContentTypes as $x)
            if (file_exists(\Yii::$aliases['@app'].'/files/'.$x))
                \yii\helpers\FileHelper::removeDirectory(\Yii::$alisases['@files'].'/'.$x);
        //$this->dropTable('files');
        //return false;
    }

}
