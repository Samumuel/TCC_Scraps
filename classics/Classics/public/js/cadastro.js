const btnUsuario = document.getElementById("btnUsuario");
const btnEmpresa = document.getElementById("btnEmpresa");

const formUsuario = document.getElementById("formUsuario");
const formEmpresa = document.getElementById("formEmpresa");

const tipo = document.getElementById("tipo");

btnUsuario.addEventListener("click", () => {

    if (tipo.value === "usuario") return;

    tipo.value = "usuario";

    formUsuario.style.display = "block";
    formEmpresa.style.display = "none";

    btnUsuario.classList.add("ativo");
    btnEmpresa.classList.remove("ativo");

});

btnEmpresa.addEventListener("click", () => {

    if (tipo.value === "empresa") return;

    tipo.value = "empresa";

    formUsuario.style.display = "none";
    formEmpresa.style.display = "block";

    btnEmpresa.classList.add("ativo");
    btnUsuario.classList.remove("ativo");

});
