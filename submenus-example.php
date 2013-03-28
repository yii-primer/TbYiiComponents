<?php
return $params2 = array(
	'submenus' => array(
		'forum' => array(
			'Добавить форум' => array ('site/addforum'),
			'Список форумов' => array ('site/listforums'),
			'что-то ещё' => array ('#'),
		),
		'site' => array(
			'Добавить модель' => array ('site/addsomemodel'),
			'Подтверждённые пользователем' => array ('perfomance/ticketslist', 'type' => 2),
			'Одобренные администратором' => array ('perfomance/ticketslist', 'type' => 3),
		),
		'mongo' => array(
			'Главная подраздела' => array ('mongo/testmongo'),
			'Весь журнал' => array ('mongo/showlog'),
			'Удалить все записи' => array ('mongo/removealllogs'),
			'За последнюю минуту' => array ('mongo/showlastminutelogs'),
			'Статистика(агрегация)' => array ('mongo/ShowLevelMeanValue'),
		),
	),
	'2' => array('1'),
);

