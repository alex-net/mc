<?php 

namespace app\components;

use Yii;
use yii\web\UrlRule;

class UrlmanagerInit implements \yii\base\BootstrapInterface
{
	function bootstrap($app)
	{
		$ct=implode('|',\app\models\Content::ContentTypes);
		$app->urlManager->addRules([
			// форма входа и страница пользователя 
			['pattern'=>'admin','route'=>'admin/user/index'],
			// выход из учётки . 
			'admin/logout'=>'admin/user/logout',

			// Содержмое 
			'admin/content'=>'admin/content/list',
			// по типам контента .. 
			"admin/content/<type:$ct>s"=>'admin/content/ct-list',

			"admin/content/<type:$ct>s/add"=>'admin/content/ct-add',
			// редактировать содержимое ... 
			"admin/content/<type:$ct>s/<id:\d+>"=>'admin/content/ct-edit',
			// управление файлами ... 
			"admin/content/file-man"=>'admin/content/file-manager',

			// работа с картинками 
			'files/<preset>/<fn>'=>'image/file',
			'files/kill'=>'image/file-kill',
			'files/up'=>'image/file-upload',// загрузка файлов ...
			'set-files-weight'=>'image/set-files-weight',
			
			// редактирование пункта менб 
			'admin/menu/<mid>'=>'admin/menus/elitem',
			// редактирование менюшки 
			'admin/menu'=>'admin/menus',
			// менюшки*/
			//'admin/menus'=>'admin5/menus/list',
			'admin/blocks'=>'admin/blocks/index',
			
			'admin/blocks/<bid:\d+>/edit'=>'admin/blocks/edit',
			'admin/blocks/add'=>'admin/blocks/edit',
			

			'write-uns'=>'mc/write-uns',


			'news'=>'mc/news-list',
			'albums'=>'mc/albums-list',
			['pattern'=>'<path:.*>','route'=>'mc/show-content','mode'=>UrlRule::PARSING_ONLY],

		]);
		//Yii::info('asdsa');
	}
}