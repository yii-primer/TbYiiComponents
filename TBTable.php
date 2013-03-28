<?php

/* виджет "таблица" - предназначение для
вывода свойств(значений полей) "плоского"
массива объектов (т.е. без инкапсуляции
одного объекта в другом)

вызывать компонент следует в представлении контроллере -
	после того как в это представление из дейсвтия передан
	массив элементов-массивов или элементов-объектов -

чтобы сделаеть использование кода универсальным
я отказываюсь делать данный компонент "виджетом" yii -
а весь html(его, впрочем  - немного) выведу как php-строки

ПРИМЕР инициаллизации класса:

$tbl =  new TBTable();
		$columns = array ('time' => 'Время', 'level' => 'Уровень', 'type'=> 'Тип', 'message' => 'Сообщение');
		$isarray = true;
		$tablename = 'Записи трассировки';
		$tablenamecomment = 'то есть которые вносит разработчик чтобы что-то отследить';
		$options = array(
			'удалить' =>array('mongo/deletelogrecord', '_id' => ''),
		);

		$tbl->config($columns, $isarray, $tablename, $tablenamecomment, $options);
		$tbl->printIt($cursor); // $cursor - здесь как раз массив элементов

====================
*/


class TBTable extends CWidget
{

	/*пример (значение) задания массива колонок(ключи)
	и их отображаемых имён(значения)*/

    public $addid = true;
	public $isArray = false; // по умолчанию передётся массив объектов

	public $TableName = 'Имя таблицы';
	public $TableNameComment = 'комментарий к имени таблицы';
	/*Каждая строка наше таблицы часто
	 посвящена некоторому элементу базы данных
	- вполне возможно, что у разработчика появится желание вывести ссылки
	на опции обработки этого элемента - для этого заведём массив опций -
	передаём в него основной url + список имён необходимых параметров+ список значений
	лучше всего передавать это ассоциативным массивом где под индексом 0 (ноль)
	будет url-путь, а остальные ассоциативные элементы - это пара параметр-значение-
	если значение пусто - то компонент TBTable сам сделает попытку извлечь
	значение из очередного элемента $this->DataArr() массива элементов.


	пока что вывод ссылок ($this->printOption()) будет реализован в стиле Yii (средствами Yii) -
	но его не сложно будет переписать "универсалаьным образом"*/

	public $options = false;
	public $ImagePreview = false; // передаём ширину и высоту иконки

	/* массив элементов (основной), которые нужно выводить
	построчно в таблице  */
	public $DataArr = array();
	public $columns = array(
		'value1' => 'Первая колонка таблицы',
		'value2' => 'Вторая колонка таблицы',
	);



	/* следует вызвать до вызова printIt()*/
	public function config($columns, $isarray = false, $tablename = 'Имя таблицы', $tablenamecomment = 'Комментарий к имени',
	                       $options = false, $ImagePreview = false)
	{
		$this->columns = $columns;
		$this->isArray = $isarray;
		$this->TableName = $tablename;
		$this->TableNameComment = $tablenamecomment;
		$this->options = $options;
		$this->ImagePreview = $ImagePreview;
	}

	public function printIt($arr)
	{
		//
		//var_dump($arr);
		$this->DataArr = $arr;
		$this->beginIt();

		foreach ($arr as $value) {
			//var_dump($value);
			echo '<tr>';
			foreach ($this->columns as $key => $val) {

				echo '<td>';
				if ($this->isAttributeSet($value,$key)) {
					$this->printAttribute($value,$key);
				}
				echo '</td>';
			}
			if ($this->ImagePreview) {
				echo '<td>';
			    $this->printImagePreview($value->id);
				echo '</td>';
			}

			/*выводим значения колонки опций*/
			if ($this->options) {
				echo '<td>';
				/* $key  - как раз и есть выводимое имя опции*/
				foreach ($this->options as $key => $val) {
					$this->printOption($value, $key, $val);
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		$this->endIt();
	}

	/*эта функция призвана вывести на экран картинки по некоторому id*/
	public function printImagePreview($id)
	{
		$img = Image::model()->findByPk($id);
		if ($img) {
			$url = $img->getThumbnail($this->ImagePreview['width'], $this->ImagePreview['height']);
			echo CHtml::image($url,"картинка", array('class' => 'img-polaroid'));
		}
	}

	public function printOption($element, $optionname, $params)
	{
		/*сначала проверим, что все параметры указаны -
		 если параметр не указан, то пробуем получить его
		$element()*/
		foreach ($params as $key => $val) {
			/*если это не первый элемент и значение не определено*/
			if (($key) && (!$val)) {
				if ($this->isAttributeSet($element, $key)) {
					$params[$key] = $this->getAttributeValue($element, $key);
				}
			}
		}

		/*после того, как необходимые параметры url получены
		- мы можем вывести ссылку*/

		/*printOptionLink() пока что использует функцию Yii для вывода ссылки*/
		$this->printOptionLink($optionname, $params);
	}

	public function printOptionLink($optionname, $params)
	{
		if ($this->addid) echo '<div id="">';
		echo CHtml::link('['. $optionname .']', $params);

	}

	/* функция проверит определён ли элемент
	 массива или свойство объекта (универсальная проверка)*/
	public function isAttributeSet($element,$attributename)
	{
		$result = false;
		if ($this->isArray) {
			$result = isset($element[$attributename]);
		} else {
			$result = isset($element->$attributename);
		}
		return $result;
	}

	public function printAttribute($element,$attributename)
	{
		if ($this->isArray) {
			echo $element[$attributename];
		} else {
			echo $element->$attributename;
		}
	}

	public function getAttributeValue($element,$attributename)
	{
		$result = '';

		if ($this->isArray) {
			$result = $element[$attributename];
		} else {
			$result = $element->$attributename;
		}
		return $result;
	}

	public function beginIt()
	{
		/*выводим название таблицы и комментарий
		и имена колонок*/


		echo '<h4>' . $this->TableName . ' <br><small><em>' . $this->TableNameComment . '</em></small></h4>';
        echo '<table class="table table-striped table-bordered" id="tbtable"><tr>';
		foreach ($this->columns as $val) {
	        echo '<th>' . $val . '</th>';
		}

		if ($this->ImagePreview)
			echo '<th>' . 'Изображение' . '</th>';


		if ($this->options)
			echo '<th>' . 'Опции' . '</th>';

	}

	public function endIt()
	{
		echo ('</table>');
	}

	public function demo()
	{
		$this->printIt(array());
	}
}