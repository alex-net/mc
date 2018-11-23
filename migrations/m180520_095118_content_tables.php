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
            'status'=>$this->boolean()->defaultValue(true)->comment('Статус'),
            'body'=>$this->text()->comment('Содержимое'),
            'teaser'=>$this->text()->comment('Содержимое'),
            'created'=>$this->integer()->comment('Создан'),
            /**
             * @todo  nomenuitem можно бахнуь из базы
             * */
            'nomenuitem'=>$this->boolean()->defaultValue(false)->comment('Без пункта меню'),
        ]);
        echo "Таблица контента создана \n";

        $this->createTable('files',[
            'filename'=>$this->string(30)->notnull()->comment('Ключик таблицы'),
            'weight'=>$this->integer(10)->notnull()->defaultValue(0)->comment('Вес файла'),
            'cid'=>$this->integer()->notnull()->comment('ссылка на контент'),
            'ct'=>$this->string(10)->notnull()->comment('Тип контента (он же путь к папке с файлами'),
            'uid'=>$this->integer()->notnull()->comment('Ссылка на юзера загрузившего файл'),
        ]);
        $this->createIndex('cidpfiles','files',['cid']);
        try{
            \yii\helpers\FileHelper::createDirectory(\Yii::getAlias('@files'));
        }catch(Expression $e){

        }
    }

    public function down()
    {
        //echo "m180520_095118_content_tables cannot be reverted.\n";
        //
        $this->dropTable('content');
        $this->dropTable('files');
        foreach(\app\models\Content::ContentTypes as $x)
            if (file_exists(\Yii::$aliases['@app'].'/files/'.$x))
                \yii\helpers\FileHelper::removeDirectory(\Yii::$alisases['@files'].'/'.$x);
        //_7OwUW3CkG18JMfG

        //$this->dropTable('files');
        //return false;
    }

}
