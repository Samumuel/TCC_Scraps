<script>
// Botões de mostrar/ocultar senha: data-alternar-senha indica o id do campo
document.querySelectorAll('[data-alternar-senha]').forEach(function(botao) {
    const campo = document.getElementById(botao.dataset.alternarSenha);
    const icone = botao.querySelector('i');

    botao.addEventListener('click', function() {
        const tipo = campo.getAttribute('type') === 'password' ? 'text' : 'password';
        campo.setAttribute('type', tipo);

        icone.classList.toggle('bi-eye');
        icone.classList.toggle('bi-eye-slash');
    });
});
</script>
