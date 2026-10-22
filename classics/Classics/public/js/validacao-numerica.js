document.querySelectorAll('.somente-numeros').forEach((campo) => {
    campo.addEventListener('input', () => {
        campo.value = campo.value.replace(/\D/g, '').slice(0, Number(campo.dataset.maxlength));
    });
});