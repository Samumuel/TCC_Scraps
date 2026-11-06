const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

document.querySelectorAll('.email-validacao').forEach((campo) => {
    const formulario = campo.closest('form');
    // data-mensagem permite um texto próprio; o aviso fica abaixo do campo (ou do grupo com ícone)
    const textoErro = campo.dataset.mensagem || 'E-mail inválido.';
    const mensagem = document.createElement('div');
    mensagem.className = 'text-danger small mt-1';
    mensagem.textContent = textoErro;
    mensagem.hidden = true;
    (campo.closest('.cl-input-icon') || campo).insertAdjacentElement('afterend', mensagem);

    const validarEmail = () => {
        const invalido = campo.value !== '' && !emailValido.test(campo.value.trim());
        campo.setCustomValidity(invalido ? textoErro : '');
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