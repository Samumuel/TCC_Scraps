-- Painel do administrador (RF02)
-- Rode este script uma única vez no phpMyAdmin, no banco Classics.

-- 1) Primeiro usuário administrador
--    E-mail: admin@classics.com
--    Senha:  Admin@123  (troque depois em Usuários > Editar)
--    A senha abaixo já está criptografada (bcrypt), como o sistema exige.
INSERT INTO Usuario (Nome, Email, CPF, Telefone, Idade, Tipo_usuario, Senha, Foto)
VALUES ('Administrador', 'admin@classics.com', '', '', 0, 'admin', '$2y$10$MkwYNFUOwfDqZ9Xr87LQyugbRkAlQWsPI0RU7O0mGmu4S8w4CoUya', '');

-- 2) As empresas já cadastradas passam a ser aprovadas,
--    para que as atividades delas continuem aparecendo no Explorar (RN02)
UPDATE Empresa SET Status = 'aprovada';
