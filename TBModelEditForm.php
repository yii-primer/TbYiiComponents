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

В данный класс следует передать подель
- и массив конфигурации, ключами в котором являются имена полей, которые необходимо редактировать
пример инициаллизации:
$arr = $fieldsarray = array
(
	'file' => array('type' => file, 'comment' => 'Файл изображения');

)


====================
*/


class TBModelEditForm extends CWidget
{

	/*пример (значение) задания массива колонок(ключи)
	и их отображаемых имён(значения)*/

    public $isArray = false; // по умолчанию передётся массив объектов

	public $FormName = 'Название формы';
	public $FormNameComment = 'комментарий формы';
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
	public function config($columns, $isarray, $tablename, $tablenamecomment, $options, $ImagePreview)
	{
		$this->columns = $columns;
		$this->isArray = $isarray;
		$this->TableName = $tablename;
		$this->TableNameComment = $tablenamecomment;
		$this->options = $options;
		$this->ImagePreview = $ImagePreview;
	}

	public function printIt($model, $arr)
	{
		$this->DataArr = $arr;
		$this->beginIt($model);

		foreach ($arr as $key => $value) {
			//$method = $key['type'];
			$this->$key($model, $key, $value);
		}
		$this->endIt();
	}

	// выводит поля для закачки файла (YII стиль и средства для вывода элементов)
	public function file($model, $key, $value)
	{
		$this->startField($model,$value['comment']);
		echo CHtml::activeFileField($model, $key);
		$this->endField();
	}

	public function startField($model, $text)
	{
		echo '<div class="control-group">';
		echo CHtml::activeLabel($model, $text, array('class' => 'control-label'));
		echo '<div class="controls">';
	}



	public function endField()
	{
		echo '</div></div>';
	}


	public function textfield($model, $key, $value)
	{
		$this->startField($model,$value['comment']);
		 echo CHtml::activeTextField($model, $value['field']);
		$this->endField();
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

	public function beginIt($model)
	{
		/*выводим название таблицы и комментарий
		и имена колонок*/


		if ($this->ImagePreview)
			echo '<th>' . 'Изображение' . '</th>';
		echo '</tr>';

		if ($this->options)
			echo '<th>' . 'Опции' . '</th>';
		echo '</tr>';


		echo '<div class="page-header"><h4>';
		echo $this->FormName;
		echo '<br></a><small><em>' . $this->FormNameComment . '</em>';
		echo '</small></h4></div>';

		echo '
		<style type="text/css">
			.hero-unit {
				font-size: 12px;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				color: #333366;
				font-weight: 200;
				line-height: 20px;
				padding: 20px;
				margin-bottom: 10px;
			}

			label {
				display: inline;
				margin-bottom: 5px;
			}

			input[type="radio"],
			input[type="checkbox"] {
				margin: 0px 0 0;
				margin-top: 1px \9;
				*margin-top: 0;
				line-height: normal;
			}

		</style>
		';

		echo ' <div class="span12"><div class="hero-unit">
		<div class="form">';
		echo CHtml::form('','post',array('enctype' => 'multipart/form-data','class' => 'form-horizontal'));
		echo '<fieldset>';
		echo CHtml::errorSummary($model);
	}

	public function endIt()
	{
		echo '<div class="form-actions">' . CHtml::submitButton('Сохранить', array('class' => "btn btn-primary  btn-large")) .
			'</div>';

		echo '</fieldset>' . CHtml::endForm() . '</div><!-- form --></div>';
	}

}