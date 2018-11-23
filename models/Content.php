<?php 

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\helpers\Html;

/**
 * модель контента .. 
 * */
class Content extends \yii\base\Model
{
	/**
	 * Ключик контента
	 * @var int
	 * */
	public $cid =0;
	public $title; //Заголоовк
	public $type;// 'тип контента: новости статьи альбомы
	public $alias;// 'Алиас'
	public $aliasisuser=0;  /// автогенерация алиаса 
	public $owner; // 'Кто создал',
    public $status=1; // Статус
	public $body;// Содержимое
	public $teaser;// Содержимое
	public $created;// Создан
	public $nomenuitem; // без пункта меню 

	const TeaserSize=12;/// число слов попадающих в тизер .. из body

	const CTAlbum='album';
	const CTNews='new';
	const CTPage='page';

	const ContentTypes=[self::CTPage,self::CTNews,self::CTAlbum];
	const ContentTypesNames=['page'=>'Страница','new'=>'Новость','album'=>'Альбом'];

	// число картинок ..  0 = неограниченно
	const ContentTypesFileCount=['page'=>1,'new'=>1,'album'=>100];

	const SCENARIO_EDIT='edit-content';
	const SCENARIO_KILL='kill-content';

	/**
	 * ссылка на объект модели меню 
	 * @var object \app\models\Menu.php
	 * */
	private $_menu;// пункт меню ...
	/**
	 * массиа ссылка на объект моделей файлов 
	 * @var object[] \app\models\FilesModel
	 * */
	//private $_files; // набор файлов ..


	public function init()
	{
		parent::init();
		// для новых элементов 

		// файлы/ загрузка файлов .. 
		//$this->_files=new FilesModel(['ct'=>$this->type]);
		// меню 
		$this->_menu=Menu::findByContent($this->cid);
		$this->nomenuitem=empty($this->_menu->mid);
		
	}


	public function scenarios(){
		$s=parent::scenarios();
		$s[static::SCENARIO_EDIT]=['cid','title','type','alias','status','body','teaser','aliasisuser','nomenuitem'];
		$s[static::SCENARIO_KILL]=['cid'];
		return $s;
	}

	
	public function attributeLabels()
	{
		return [
			'title'=>'Заголовок',
			'body'=>'Содержимое',
			'teaser'=>'Анонс',
			'status'=>'Опублковано',
			'aliasisuser'=>'Пользовательский алиас',
			'alias'=>'Алиас',
			'nomenuitem'=>'без пункта меню',
		];
	}
	public function attributeHints()
	{
		return [
			'alias'=>'Для главной страницы укажите "front-page"',
			'body'=>'в контенте доступны следующие токены: %%title%% = вывести заголовок; %%img[:preset]%% вывести первую загруженую картинку через пресет/или оригинальную' 
		];
	}
	

	public function rules()
	{
		$rules=[
			[['title','type'],'required'],
			['cid','\yii\validators\DefaultValueValidator','value'=>0],
			['cid','\yii\validators\NumberValidator','min'=>0],

			//  для альбоома валидаия не нужна ..
			['body',$this->type==self::CTAlbum?'safe':'required'],
			['title','string','max'=>100],
			['type','yii\validators\RangeValidator','range'=>static::ContentTypes],
			['teaser','safe'],
			
			
			['status','yii\validators\BooleanValidator'],
			
			['aliasisuser','boolean'],
			['alias','aliasvalidate','skipOnEmpty'=>false],
			['alias','string','max'=>120],
			
			
			['nomenuitem','boolean'],
		];
		
		return $rules;
	}
	/**
	 * проверка алиаса на совпадение .. 
	 * @param $attr string название атрибута .. 
	 * @param $params array параметры алидатора ..
	 * */
	public function aliasvalidate($attr,$params=[])
	{
		$alias=trim($this->alias);
		$alias=($this->aliasisuser && $alias)?$alias:trim($this->title);
		$alias=\URLify::filter($alias);
		// проверка алиасов в базе .. в контенте .. 
		$res=(new Query())->select('*')->from('content')->where(['alias'=>$alias]);
		if ($this->cid)
			$res->andWhere(['!=','cid',$this->cid]);
		$res=$res->exists();

		if ($res || Menu::alloasExists($alias))// что то нашлось 
			$this->addError($attr,sprintf('Алиас %s занят',$this->$attr));
	}
	/**
	 * загрузка .  контента..  
	 * @param int $id Идентифиатор сущности
	 * */
	public static function findById($id)
	{
		if ($id && $d=(new Query())->select('*')->from('content')->where(['cid'=>$id])->limit(1)->one())
			return new static($d);
		return null;
	}
	/**
	 * запрос сущностей по id 
	 * @param int[] $ids  массив идентификаторов загружаемых сущностей 
	 * */
	public static function findperids($ids=[])
	{
		if (!$ids)
			return [];
		$q= (new Query())->select(['cid','title','type'])->from('content')->where(['cid'=>$ids])->indexBy('cid')->all();
		foreach($q as $x=>$y)
			if ($y)

			$q[$x]=new static ($y);
		
		return $q;
	}
	/**
	 * загрузка контента по алиасу .. 
	 * @param string $path  путь)алиас) который надо найти 
	 * */
	public static function loadByAlias($path)
	{
		$path=array_reverse(explode('/',$path));
		if (isset($path[1]) && in_array($path[1],['news','albums']))
			array_pop($path);
		$q=(new Query());
		$q->select('c0.*')->from(['c0'=>'content']);

		$q->andWhere(['c0.alias'=>$path[0]]);
		if (count($path)>1){
			$q->leftjoin(['m0'=>'menuitems'],'m0.contentid=c0.cid');
			$q->leftjoin(['m1'=>'menuitems'],'m0.pmid=m1.mid');
			$q->leftJoin(['c1'=>'content'],'m1.contentid=c1.cid');
			$q->andWhere(['c1.alias'=>$path[1]]);
		}
			
		$d=$q->limit(1)->one();

		if (!$d)
			return null;
		return new Content($d);
	}
	
	/**
	 * получить модель пункта меню 
	 * */
	public function getMenu()
	{
		return $this->_menu;
	}

	
	/**
	 * сохранение контента .. 
	 * @param array $post  POST данные формы .. (пост запрос) 
	 * 
	 * @return void
	 * */
	public function save($post)
	{
		$this->setScenario(static::SCENARIO_EDIT);
		$nomenustate=$this->nomenuitem;
		if (!$this->load($post) || !$this->validate())
			return false;
		

		if (!empty($this->aliasisuser) && !empty($this->alias))
			$this->alias=\URLify::filter($this->alias);
		else
			$this->alias=\URLify::filter($this->title);
			 
		$data=$this->getAttributes(null,['nomenuitem','cid']);
		foreach($data as $x=>$y)
			if (!isset($y))
				unset($data[$x]);


		if ($this->cid)
			unset($data['created'],$data['owner']);
			
	
		if ($this->cid)// старый  элемент ..
			Yii::$app->db->createCommand()->update('content',$data,['cid'=>$this->cid])->execute();
		else{
			//nomenuitem
			$data['created']=time();
			$data['owner']=Yii::$app->user->id;
			Yii::$app->db->createCommand()->insert('content',$data)->execute();
			$this->cid=Yii::$app->db->lastInsertId;
			// обновляем cid для файлов ..
			FilesModel::updateCid($this->cid);
		}
		$ret=true;
		// менюгка 
		// обновим ...контент .для менюшки ..
		if (!$this->nomenuitem ) // без меню 
			$ret=$this->_menu->updateContentItemFromPost($post,$this->cid);
		else
			if ($nomenustate!=$this->nomenuitem)
				$ret=$this->_menu->killContentItem($post);
		return $ret;

	}
	/**
	 * удаление элемента 
	 * @param array $data POST запрос 
	 * */
	public function kill ($data)
	{
		$this->setScenario(static::SCENARIO_KILL);
		if (!$this->cid || !$this->load($data) || !$this->validate())
			return false;

		Yii::$app->db->createCommand()->delete('content',['cid'=>$this->cid])->execute();
		// чистим пункт меню .. 
		$this->_menu->dropItem();
		Yii::$app->session->AddFlash('info','Удалено '.$this->cid);
		return true;
		
	}

	/**
	* Запрос в базу на просмотр контента ... 
	*/
	public static function contentquerylist($type)
	{
		$list=(new Query())->select(['c.cid','c.title','c.created','c.status','c.type','owner'=>'u.name'])->from(['c'=>'content'])->leftJoin(['u'=>'users'],'c.owner=u.uid')->distinct(true);
		// пристыковываем меню 
		$list->andWhere(['c.type'=>$type]);
		$list->leftjoin(['m1'=>'menuitems'],'m1.contentid=c.cid');// текущий уровнь 
		$list->leftjoin(['m2'=>'menuitems'],'m2.mid=m1.pmid'); // ровень выше .. 
		$list->leftjoin(['c0'=>'content'],'c0.cid=m2.contentid'); // статья предок ....
		$list->addSelect(['calias'=>'concat(if(isnull(c0.alias),"",concat(c0.alias,"/")),c.alias)']);// 
		$list->orderBy(['c.created'=>SORT_DESC]);
		
		return $list;
	}

	// получаем список контента .. для вставки в меню 
	public static function getListptions($ct='')
	{
		$list=['Не указано'];
		$q=(new Query())->from('content')->select(['cid','title','type','status'])->orderBy('title',SORT_ASC);
		if ($ct)
			$q->whee(['type'=>$ct]);
		$q=$q->all();
		foreach($q as $v)
			$list[$v['cid']]=sprintf('%s (%s, %s)',$v['title'],$v['type'],$v['status']);// ContentTypesNames

		return $list;
	}

	/**
	* поготовка к выводу данных
	*
	**/
	public function preparebody()
	{
		if($this->type=='new')
			$this->body='<div class="content-img">%%img%%</div><h1>%%title%%</h1>'.$this->body;
		// ищем и заменяем токен title и image 
		if (preg_match('#%%title%%#',$this->body)){
			$this->body=preg_replace('#%%title%%#', $this->title, $this->body);

		}

		// ищем токены картинки ..
		while(preg_match('#%%img:?(.*?):?(\w*)%%#', $this->body,$params)){
			$files =\app\models\FilesModel::findFilesPerCid($this->cid);
			Yii::info($files,'$files');
			$plac='';
			if(!empty($files)){
				list($source,$fn,$preset)=$params;
				if (empty($fn))
					$fn=$files[0]['filename'];
				foreach($files as $x=>$f)
					if ($f['filename']==$fn){
						if(!$preset || $preset=='original')
							$plac=$f['url'];
						else
							$plac=Yii::$aliases['@filesUrl'].'/'.$preset.'/'.$f['filename'];		
					}

			}
			
			$this->body=str_replace($source, Html::img($plac), $this->body);
			//preg_replace('#%%img:?(.*?):?(\w*)%%#',Html::img($plac),$this->body);
		}

		
	}

	// запрос запроса .. на получение контента для блоков .. 
	public static function ContentForblocks($cids)
	{
		$q=(new Query())->select(['title','cid','alias','type'])->from('content')->indexBy('cid');
		if ($cids)
			$q->where(['in','cid',$cids]);
		return $q->all();
	}

	// список новостей ... 
	public function NewsList()
	{
		$res=(new Query())->select(['c.cid','c.title','c.teaser','c.body','created'=>'c.created','c.alias'])->from(['c'=>'content'])->where(['type'=>'new','status'=>1]);
		$res->groupby('c.cid');
		$res->leftjoin(['f'=>'files'],'f.cid=c.cid');
		$res->addSelect(['fn'=>'group_concat(f.filename)']);
		return $res;
	}

	// список альбомов ... 
	public function AlbumsList()
	{
		$res=(new Query())->select(['c.cid','c.title','c.teaser','c.body','c.created','c.alias'])->from(['c'=>'content'])->where(['type'=>'album','status'=>1]);
		$res->groupby('c.cid');
		$res->leftjoin(['f'=>'files'],'f.cid=c.cid');
		$res->addSelect(['fn'=>'group_concat(f.filename)']);

		return $res;
	}

	// получить последние N Новостей ... 
	public static function NewsListLast($count=0)
	{
		$res=(new Query())->from('content')->select(['cid','title','created','alias','teaser','body'])->where(['type'=>'new','status'=>1])->orderBy(['created'=>SORT_DESC])->indexBy('cid');
		if($count)
			$res->limit($count);
		$res=$res->all();
		foreach($res as $x=>$y){
			if (!trim($y['teaser']))
				$res[$x]['teaser']=\yii\helpers\StringHelper::truncateWords(strip_tags($y['body']),self::TeaserSize);
			unset($res[$x]['body']);
			$res[$x]['url']='/news/'.$y['alias'];
			unset($res[$x]['alias']);
		}
		return $res;

	}
}