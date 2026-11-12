// Botões de mostrar/ocultar senha: data-alternar-senha indica o id do campo
document.querySelectorAll('[data-alternar-senha]').forEach(function (botao) {
    const campo = document.getElementById(botao.dataset.alternarSenha);
    const icone = botao.querySelector('i');
    if (!campo) {
        return;
    }

    botao.addEventListener('click', function () {
        const mostrar = campo.getAttribute('type') === 'password';
        campo.setAttribute('type', mostrar ? 'text' : 'password');
        botao.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');

        if (icone) {
            icone.classList.toggle('bi-eye', !mostrar);
            icone.classList.toggle('bi-eye-slash', mostrar);
        }
    });
});
