<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'AutenticacaoController@login');
$router->post('/logar', 'AutenticacaoController@logar');


$router->get('/usuarios', 'UsuarioController@index');
$router->get('/usuarios/cadastrar', 'UsuarioController@cadastrar');
$router->post('/usuarios/salvar', 'UsuarioController@salvar');
$router->get('/usuarios/editar', 'UsuarioController@editar');
$router->post('/usuarios/atualizar', 'UsuarioController@atualizar');
$router->get('/usuarios/excluir', 'UsuarioController@excluir');

$router ->get('/empresa', 'EmpresaController@listarTodos');
$router ->get('/empresa/animal', 'EmpresaController@verAnimal');
$router ->get('/empresa/cadastrar', 'EmpresaController@criar');
$router ->post('/empresa/salvar', 'EmpresaController@salvar');
$router ->get('/empresa/editar', 'EmpresaController@editar');
$router ->post('/empresa/atualizar', 'EmpresaController@atualizar');
$router ->get('/empresa/excluir', 'EmpresaController@excluir');



$router->run();
