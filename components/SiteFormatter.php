<?php 

namespace app\components;

class SiteFormatter extends \yii\i18n\Formatter
{
	// формат даты/времени 
	public function asDateTimeRu($val,$opt=[])
	{
		return date('Y.m.d H:i',$val);
	}
	// статус
	public function asCStatus($val,$opt=[])
	{
		return $val?'Активно':'Неактивно';
	}


}

?>