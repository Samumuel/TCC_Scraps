1º -> Realizar o Forms para criação de atividades por parte da empresa, só sendo possível caso esteja logado com empresa, será criado uma atividade com o ID desta empresa, no forms da criação será necessário inserir as informações de categoria, localização e sessão.
    Categoria-> Será apenas o nome e ID, para facilitar a pesquisa, caso não haja categorias, a empresa deverá criar no mínimo 1.
    Localização-> Terá ID, CEP, complemento e ID da atividade, pois toda atividade deve possuir ao menos 1 localização.
    Sessão-> Sessão serve para separar os horários e vagas da atividade. Possui ID, data, hora, quantidade mínima de pessoas, quantidade máxima de pessoas, quantidade disponível de pessoas e ID da atividade, visto que cada atividade tem no mínimo 1 sessão.
    
    Para isso será necessário criar Controller, service e repositorie para atividade, categoria, localização e sessão, de forma que seja possível criar, editar, excluir e listar cada uma delas.

    --REALIZADO--
    Foi realizado o cadastro, exclusao, edição de atividades e vizualização, validando a empresa que criou, se esta logado ou não, porém não está funcionando o uso das imagens.
    -----
    --FALTA--
    Configurar localização e sessão.
    Localização será uma tela que irá abrir apos confirmar a informações iniciais, será requisitado cep e complemento, sendo número.
    Sessão também sera uma tela que abrirá após a configuração de localização.
    -----

2º -> Usúarios poderam listar atividade já criadas, selecionar elas para ver mais informações, estilo arquivos _show.php na pasta views, será necessário ver informações das sessões disponíveis, localização e categorias.
