<?php

require_once '.env.php';
require_once './include/helper.php';
require_once './vendor/autoload.php';

// Заполняем БД
Helper::push_dump();

// запускаем таймер
$start = microtime(true);

echo '<p>Получаем данные из базы... </p>';
$pdo = Helper::getConnection();
$query = $pdo->query('SELECT SQL_NO_CACHE * FROM `categories` ORDER BY `parent_id` desc');
$array = $query->fetchAll(PDO::FETCH_ASSOC);

echo '<p>Формируем дерево</p>';
$tree = Helper::buildTree($array);

// Останавливаем таймер
$end = microtime(true);
$runTime = round($end - $start, 4);

echo "Время выполнения скрипта: <b>$runTime</b> секунд";

// Очищаем БД
Helper::drop_table();

// Дерево на экран ...
echo '<pre>';
var_export($tree);
echo '</pre>';