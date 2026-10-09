<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'AutenticacaoController@login');
$router->get('/cadastrar', 'AutenticacaoController@cadastrar');
$router->post('/logar', 'AutenticacaoController@logar');


$router->get('/usuarios', 'UsuarioController@index');
$router->get('/usuarios/cadastrar', 'UsuarioController@cadastrar');
$router->post('/usuarios/salvar', 'UsuarioController@salvar');
$router->get('/usuarios/editar', 'UsuarioController@editar');
$router->post('/usuarios/atualizar', 'UsuarioController@atualizar');
$router->get('/usuarios/excluir', 'UsuarioController@excluir');
$router->get('/usuarios/ver', 'UsuarioController@verUsuario');

$router ->get('/empresa', 'EmpresaController@listarTodos');
$router ->get('/empresa/cadastrar', 'EmpresaController@criar');
$router ->post('/empresa/salvar', 'EmpresaController@salvar');
$router ->get('/empresa/editar', 'EmpresaController@editar');   
$router ->post('/empresa/atualizar', 'EmpresaController@atualizar');
$router ->get('/empresa/excluir', 'EmpresaController@excluir');
$router ->get('/empresa/ver', 'EmpresaController@verEmpresa');

$router ->get('/atividade', 'AtividadeController@listarTodos');
$router ->get('/atividade/listar', 'AtividadeController@listarTodos');
$router ->get('/atividade/cadastrar', 'AtividadeController@criar'); 
$router ->post('/atividade/salvar', 'AtividadeController@salvar');
$router ->get('/atividade/localizacao/cadastrar', 'AtividadeController@localizacaoCadastrar');
$router ->post('/atividade/localizacao/salvar', 'AtividadeController@salvarLocalizacao');
$router ->get('/atividade/sessao/cadastrar', 'AtividadeController@sessaoCadastrar');
$router ->post('/atividade/sessao/salvar', 'AtividadeController@salvarSessao');
$router ->post('/atividade/sessao/edicao/salvar', 'AtividadeController@salvarSessaoEdicao');
$router ->post('/atividade/sessao/edicao/atualizar', 'AtividadeController@atualizarSessaoEdicao');
$router ->post('/atividade/sessao/edicao/excluir', 'AtividadeController@excluirSessaoEdicao');
$router ->post('/atividade/cadastro/concluir', 'AtividadeController@concluirCadastro');
$router ->get('/atividade/editar', 'AtividadeController@editar'); 
$router ->post('/atividade/atualizar', 'AtividadeController@atualizar'); 
$router ->get('/atividade/excluir', 'AtividadeController@excluir');
$router ->get('/atividade/ver', 'AtividadeController@verAtividade');

$router->get('/categoria', 'CategoriaController@listarTodos');
$router->get('/categoria/listar', 'CategoriaController@listarTodos');
$router->get('/categoria/cadastrar', 'CategoriaController@criar');
$router->post('/categoria/salvar', 'CategoriaController@salvar');
$router->get('/categoria/editar', 'CategoriaController@editar');
$router->post('/categoria/atualizar', 'CategoriaController@atualizar');
$router->get('/categoria/excluir', 'CategoriaController@excluir');



$router->run();
