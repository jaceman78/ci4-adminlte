<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();
$db->query('DROP TABLE IF EXISTS aulas_credito');
echo "Tabela aulas_credito apagada com sucesso!\n";
