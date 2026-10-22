const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

document.querySelectorAll('.email-validacao').forEach((campo) => {
    const formulario = campo.closest('form');
    const mensagem = document.createElement('div');
    mensagem.className = 'text-danger small';
    mensagem.textContent = 'E-mail inválido.';
    mensagem.hidden = true;
    campo.insertAdjacentElement('afterend', mensagem);

    const validarEmail = () => {
        const invalido = campo.value !== '' && !emailValido.test(campo.value.trim());
        campo.setCustomValidity(invalido ? 'E-mail inválido.' : '');
        mensagem.hidden = !invalido;
        return !invalido;
    };

    campo.addEventListener('input', validarEmail);
    campo.addEventListener('blur', validarEmail);
    formulario.addEventListener('submit', (evento) => {
        if (!validarEmail()) {
            evento.preventDefault();
        }
    });
});