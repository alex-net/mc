<?php

use yii\db\Migration;
use app\models\User;

/**
 * Class m180506_194938_create_user_tbl
 */
class m180506_194938_create_user_tbl extends Migration
{

   
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        // таблица пользователей .. 
        $this->createTable('users',[
            'uid'=>$this->primaryKey()->comment('Ключик'),
            'name'=>$this->string(50)->unique()->notNull()->comment('Имя пользователя, логин'),
            'pass'=>$this->string(60)->notNull()->comment('Пароль юзера'),
            'mail'=>$this->string(60)->notNull()->comment('Ящик пользователя'),
            'status'=>$this->boolean()->defaultValue(1)->comment('Статус юзера'),
            'created'=>$this->integer()->unsigned()->comment('Дата создания юзеря'),
            'role'=>$this->string(10)->defaultValue('writer')->notNull()->comment('Роль юзера'),
            'token'=>$this->string(60)->notNull()->comment('Токен'),
        ]);
        echo "Таблица пользовтелей создана \n";
        $s=  new \yii\base\Security();
        $pass=$s->generateRandomString(16);
        // добавляем пользователя (админа) 
        $this->insert('users',[
            'name'=>'admin',
            'pass'=>$s->generatePasswordHash($pass),
            'mail'=>'alexnet2004@gmail.com',
            'created'=>time(),
            'role'=>'admin',
            'token'=>$s->generateRandomString(60),
        ]);

        echo sprintf('Добавлен админ: логин="admin"; проль="%s"'."\n",$pass);
 

        // таблица связка .. роль - право ...
        $this->createTable('permbinder',[
            'permkey'=>$this->string(40)->notNull()->comment('Ключ права'),
            'role'=>$this->string(10)->notNull()->comment('Ключ роли'),    
        ]);


    }

    public function down()
    {
        $this->dropTable('users');
        //$this->dropTable('permbinder');

    }
    
}
