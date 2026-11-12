// Regra de senha: pelo menos 8 caracteres, 1 letra maiúscula e 1 número.
// data-validar-senha            -> mostra o checklist abaixo do campo
// data-validar-senha="opcional" -> campo de edição: em branco mantém a senha atual
// data-confirmar-senha="idCampo" -> avisa quando a confirmação não é igual à senha
const regrasSenha = [
    ['8 caracteres', (senha) => senha.length >= 8],
    ['1 letra maiúscula', (senha) => /[A-Z]/.test(senha)],
    ['1 número', (senha) => /\d/.test(senha)]
];

document.querySelectorAll('[data-validar-senha]').forEach((campo) => {
    const lista = document.createElement('ul');
    lista.className = 'cl-senha-regras';
    lista.setAttribute('aria-live', 'polite');
    const itens = regrasSenha.map(([texto]) => {
        const item = document.createElement('li');
        item.innerHTML = '<i class="bi bi-circle"></i> ' + texto;
        lista.appendChild(item);
        return item;
    });
    (campo.closest('.input-group') || campo).insertAdjacentElement('afterend', lista);

    const atualizar = () => {
        const senha = campo.value;
        const opcionalVazio = campo.dataset.validarSenha === 'opcional' && senha === '';
        regrasSenha.forEach(([, regra], indice) => {
            const ok = !opcionalVazio && regra(senha);
            itens[indice].classList.toggle('ok', ok);
            itens[indice].querySelector('i').className = ok ? 'bi bi-check-circle-fill' : 'bi bi-circle';
        });
    };

    campo.addEventListener('input', atualizar);
    atualizar();
});

document.querySelectorAll('[data-confirmar-senha]').forEach((confirmacao) => {
    const original = document.getElementById(confirmacao.dataset.confirmarSenha);
    const aviso = document.createElement('div');
    aviso.className = 'text-danger small mt-1';
    aviso.textContent = 'As senhas não coincidem.';
    aviso.hidden = true;
    (confirmacao.closest('.input-group') || confirmacao).insertAdjacentElement('afterend', aviso);

    const conferir = () => {
        const diferente = confirmacao.value !== '' && confirmacao.value !== original.value;
        confirmacao.setCustomValidity(diferente ? 'As senhas não coincidem.' : '');
        aviso.hidden = !diferente;
    };

    confirmacao.addEventListener('input', conferir);
    original.addEventListener('input', conferir);
});
