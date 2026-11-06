<?php
date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/class/rb.php';

if (!class_exists('R') && class_exists('RedBeanPHP\R')) {
    class_alias('RedBeanPHP\R', 'R');
}

R::setup('mysql:host=127.0.0.1;dbname=todolist;charset=utf8mb4', 'root', '');

