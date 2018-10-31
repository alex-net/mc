<?php 

namespace app\models;

class WriteUnsForm extends \yii\base\Model
{
	public $name;// Имя 
	public $mail; // мыло
	public $text;// сообщение

	public function attributeLabels()
	{
		return [
			'name'=>'Имя',
			'mail'=>'Почта',
			'text'=>'Сообщение',
		];
	}
	public function rules()
	{
		return [
			['name','yii\validators\DefaultValueValidator','value'=>'-'],
			[['mail','text'],'required'],
			['mail','\yii\validators\EmailValidator'],
		];
	}

}