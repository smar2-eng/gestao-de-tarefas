<?php
require_once __DIR__ . '/autenticacao.php';

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
