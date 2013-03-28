<?php
/* рассмотрим здесь "параметризированный" виджет*/
class AdminMenuWidget extends CWidget
{
	public $params = 'submenus.php';
	public $params2 = ''; //

	public function getSubMenu($action)
	{
		$result= 0;
		//echo $action;

		foreach ($this->params2['submenus'] as $key => $value)
		{
			if ($key == $action){
				$result = $key;
				break;
			} else
				foreach ($value as $key2 => $val) {
					if ($action == end(explode("/", $val[0]))){
						$result = $key;
						break;
					}
				}
		}

		return $result;
	}



	public function run()
	{
		$this->params2 = include $this->params;
		//var_dump($this->params2);
		//echo 'kzkzkzk';
		$action = Yii::app()->getController()->getAction()->getId(); // получаем значение текущего действия
		$subitem = $this->getSubMenu($action);

		if ($subitem) {
			$arr = $this->params2['submenus'][$subitem];
			$active = $subitem; // какой пунк в подменю сделать активным в случае его наличия

			// передаем данные в представление виджета
			$this->render('adminmenu',array('params' => $arr,'active' => $active));
		} //else echo  '!!!';
	}
}