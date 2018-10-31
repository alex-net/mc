<?php
// gHDqS39g5T64UcBE
namespace app\models;

use Yii;
use yii\db\Query;

class User extends \yii\base\Model implements \yii\web\IdentityInterface
{

    // свойства модули .. 
    public $uid;
    public $name;
    public $pass;
    public $mail;
    public $status;
    public $created;
    public $role;
    public $token;
    private $_perms=[];// список прав которые доступны юзеру ..ы


    const SCENARIO_LOGIN='user-login';
    const SCENARIO_LOAD='user-load-data';
    // роли пользователей ..
    const userroles=[
        'admin'=>['title'=>'Администратор'],
        'writer'=>['title'=>'Редактор'],
    ];
    const perms=[
        'administry'=>['title'=>'Администрированик'],
        'contentmanager'=>['title'=>'Управление контентом','descr'=>'Статьи, новости'],
        'blockmanager'=>['title'=>'Управление блоками','descr'=>''],
        'system config'=>['title'=>'Системные настроки'],
        'image manager'=>['title'=>'Управление картинками в контенте'],
    ];


    public function attributeLabels()
    {
        return [
            'name'=>'Логин',
            'pass'=>'Пароль',
        ]; 
    }

    /**
     * {@inheritdoc}
     */
    //  вернуть сущность по id 
    public static function findIdentity($id) {
        $u= new static();
        // загрузить юзера ..
        $d=(new Query())->select('*')->from('users')->where(['uid'=>$id])->limit(1)->one();
        $u->setdatamodel($d);
        // загрузить его права ..
        $u->_perms==(new Query())->select('permkey')->from('permbinder')->where(['role'=>$u->role])->column();
        
        return $u;
    }

    /**
     * {@inheritdoc}
     */
    // искать юзера по токену ....
    public static function findIdentityByAccessToken($token, $type = null) {return static::findOne(['token'=>$token]);}

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    // искать юзера по логину ... 
    public static function findByUsername($username) {return static::findOne(['name'=>$username]);}

    /**
     * {@inheritdoc}
     */
    // вернуть id 
    public function getId() {return $this->uid;}

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(){return null;}

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey){return null;}

    public function scenarios()
    {
        $s=parent::scenarios();
        $s[self::SCENARIO_LOGIN]=['name','pass'];
        $s[self::SCENARIO_LOAD]=array_keys($this->attributes);
        
        
        return $s;
    }

    public function rules()
    {
        $ruls=parent::rules();
        $ruls[]=[array_keys($this->attributes),'safe','on'=>self::SCENARIO_LOAD];
        $ruls[]=[['name','pass'],'required','on'=>[self::SCENARIO_LOGIN]];
        $ruls[]=[['name'],'checkuserpass','on'=>[self::SCENARIO_LOGIN]];
        
        return $ruls;
    }
    // валиадция логина и пароля ...ы
    public function checkuserpass($attr,$params)
    {
        // достаём пароль ... по имени пользователя . 
        $u= new User();
        $d=(new Query())->select('*')->from('users')->where(['name'=>$this->name])->limit(1)->one();

        if ($d)
            $u->setdatamodel($d);
        
        
        if ($u->pass && Yii::$app->security->validatePassword($this->pass,$u->pass)){
            Yii::$app->user->login($u);
            return;
        }

        $this->addError('name','неверное имя пользователя или пароль');
        $this->addError('pass','неверное имя пользователя или пароль');
        
    }
    // росто загрузка данных ... из таблицы .. 
    public function setdatamodel($data){
        //$this->setScenario(static::SCENARIO_LOAD);
        $this->scenario=static::SCENARIO_LOAD;
        $this->attributes=$data;
    }
    // вход в систему ...  
    public function login($data)
    {
        $this->scenario=static::SCENARIO_LOGIN;
        return $this->load($data) && $this->validate();
    }
    // лпоеделяем возможности юзера ... 
    public function can($permname)
    {
        return $this->role=='admin' || in_array($permname,$this->_perms);
    }
}
