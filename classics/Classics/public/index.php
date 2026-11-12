<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'AtividadeController@listarTodos');
$router->get('/login', 'AutenticacaoController@login');
$router->post('/logout', 'AutenticacaoController@logout');
$router->get('/cadastrar', 'AutenticacaoController@cadastrar');
$router->post('/logar', 'AutenticacaoController@logar');


$router->post('/usuarios/salvar', 'UsuarioController@salvar');
$router->get('/usuarios/editar', 'UsuarioController@editar');
$router->post('/usuarios/atualizar', 'UsuarioController@atualizar');
$router->post('/usuarios/excluir', 'UsuarioController@excluir');
$router->get('/usuarios/ver', 'UsuarioController@verUsuario');

$router ->post('/empresa/salvar', 'EmpresaController@salvar');
$router ->get('/empresa/editar', 'EmpresaController@editar');   
$router ->post('/empresa/atualizar', 'EmpresaController@atualizar');
$router ->get('/empresa/ver', 'EmpresaController@verEmpresa');

$router ->get('/atividade/cadastrar', 'AtividadeController@criar'); 
$router ->post('/atividade/salvar', 'AtividadeController@salvar');
$router ->post('/agendamento/salvar', 'AgendamentoController@salvar');
$router ->get('/agendamento/meus', 'AgendamentoController@meusAgendamentos');
$router ->post('/agendamento/atualizar', 'AgendamentoController@atualizar');
$router ->post('/agendamento/cancelar', 'AgendamentoController@cancelar');
$router ->post('/agendamento/trocar-sessao', 'AgendamentoController@trocarSessao');
$router ->get('/agendamento/empresa', 'AgendamentoController@reservasEmpresa');
$router ->post('/agendamento/empresa/cancelar', 'AgendamentoController@cancelarReservaEmpresa');
$router ->post('/agendamento/empresa/aprovar', 'AgendamentoController@aprovarReservaEmpresa');
$router ->post('/agendamento/empresa/recusar', 'AgendamentoController@recusarReservaEmpresa');

$router->get('/favorito/meus', 'FavoritoController@meusFavoritos');
$router->post('/favorito/alternar', 'FavoritoController@alternar');

$router->post('/avaliacao/salvar', 'AvaliacaoController@salvar');
$router->post('/avaliacao/excluir', 'AvaliacaoController@excluir');

$router->get('/notificacao/abrir', 'NotificacaoController@abrir');
$router->post('/notificacao/lidas', 'NotificacaoController@marcarTodasLidas');
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
$router ->post('/atividade/excluir', 'AtividadeController@excluir');
$router ->get('/empresa/atividades', 'AtividadeController@listarDaEmpresa');
$router ->get('/atividade/ver', 'AtividadeController@verAtividade');

// Painel do administrador (RF02 e manutenção de usuários)
$router->get('/admin', 'AdminController@empresas');
$router->post('/admin/empresa/status', 'AdminController@alterarStatusEmpresa');
$router->get('/admin/empresa/editar', 'AdminController@editarEmpresa');
$router->post('/admin/empresa/atualizar', 'AdminController@atualizarEmpresa');
$router->post('/admin/empresa/excluir', 'AdminController@excluirEmpresa');
$router->get('/admin/usuarios', 'AdminController@usuarios');
$router->get('/admin/usuario/editar', 'AdminController@editarUsuario');
$router->post('/admin/usuario/atualizar', 'AdminController@atualizarUsuario');
$router->post('/admin/usuario/excluir', 'AdminController@excluirUsuario');
$router->get('/admin/usuario/cadastrar', 'AdminController@criarUsuario');
$router->post('/admin/usuario/salvar', 'AdminController@salvarUsuario');
$router->get('/admin/empresa/cadastrar', 'AdminController@criarEmpresa');
$router->post('/admin/empresa/salvar', 'AdminController@salvarEmpresa');
$router->get('/admin/empresa/atividade', 'AdminController@criarAtividade');

$router->get('/categoria', 'CategoriaController@listarTodos');
$router->get('/categoria/cadastrar', 'CategoriaController@criar');
$router->post('/categoria/salvar', 'CategoriaController@salvar');
$router->get('/categoria/editar', 'CategoriaController@editar');
$router->post('/categoria/atualizar', 'CategoriaController@atualizar');
$router->post('/categoria/excluir', 'CategoriaController@excluir');



$router->run();
