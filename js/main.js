"use strict";

//Declaramos el rol del usuario y su nombre que luego usaremos.
let rolUsuario;
let usuario;
let idTarjeta;

//Declaramos todos los contenedores de las diferentes tarjetas.
let containerTarjetasIdea = document.getElementById("containerTarjetasIdea");
let containerTarjetasToDo = document.getElementById("containerTarjetasToDo");
let containerTarjetasWorking = document.getElementById("containerTarjetasWorking"); 
let containerTarjetasDone = document.getElementById("containerTarjetasDone");

//Obtenemos la sesion del servidor
function obtenerDatosSesion() {
    fetch("./php/obtenerSesiones.php")
        .then((response) => response.json())
        .then((sessionData) => {
            if (sessionData.success) {
                rolUsuario = sessionData.rol;
                usuario = sessionData.usuario;
                mostrarNombre();
            } else {
                console.error("No se encontró una sesión activa.");
                alert("Por favor, inicie sesión.");
                // Redirigir al login si no hay sesión
                window.location.href = "./login.html";
            }
        })
        .catch((error) => {
            console.error("Error al obtener datos de la sesión:", error);
        });
}

//Funcion para obtener el nombre del usuario y aplicarlo en la pantalla.
function mostrarNombre(){
    let contenedorNombre = document.getElementById("nombreUsuario");
    contenedorNombre.textContent = usuario.toUpperCase();
}

//Función para obtener las tarjetas desde el servidor
function obtenerTarjetas() {
    fetch("./php/obtenerTarjetas.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                distribuirTarjetas(data.tarjetas);
                
            } else {
                console.error("Error al obtener tarjetas:", data.message);
            }
        })
        .catch(error => console.error("Error en la solicitud:", error));
}

//Función para distribuir tarjetas en las columnas correctas
function distribuirTarjetas(tarjetas) {
    //Limpiar contenedores antes de agregar nuevas tarjetas
    containerTarjetasIdea.innerHTML = "";
    containerTarjetasToDo.innerHTML = "";
    containerTarjetasWorking.innerHTML = "";
    containerTarjetasDone.innerHTML = "";

    tarjetas.forEach(tarjeta => {
        let tarjetaDiv = document.createElement("div");
        idTarjeta = tarjeta._id;       
        tarjetaDiv.classList.add("carta", "tarjeta", "m-2", "p-2", "d-flex", "flex-column", "justify-content-center");
        tarjetaDiv.innerHTML = `
            <h5>${tarjeta.titulo}</h5>
            <p><strong>Propietario:</strong> ${tarjeta.propietario}</p>
            <p><strong>Colaboradores:</strong> ${tarjeta.colaboradores.join(", ")}</p>
        `;

        //Agregar evento de clic para mostrar el modal con la información de la tarjeta
        tarjetaDiv.addEventListener("click", () => mostrarModal(tarjeta));

        // Dependiendo del estado, la colocamos en el contenedor correcto
        switch (tarjeta.estado) {
            case "idea":
                containerTarjetasIdea.appendChild(tarjetaDiv);
                break;
            case "toDo":
                containerTarjetasToDo.appendChild(tarjetaDiv);
                break;
            case "working":
                containerTarjetasWorking.appendChild(tarjetaDiv);
                break;
            case "done":
                containerTarjetasDone.appendChild(tarjetaDiv);
                break;
            default:
                console.warn("Estado desconocido:", tarjeta.estado);
        }
    });
}

function mostrarModal(tarjeta, tarjetaElement){
    const modal = document.getElementById("modal");

    document.getElementById("tituloModal").textContent = `${tarjeta.titulo}`;
    document.getElementById("propietarioModal").textContent = `Propietario: ${tarjeta.propietario}`;
    document.getElementById("colaboradoresModal").textContent = `Colaboradores: ${tarjeta.colaboradores || "No hay disponibles"}`;
    document.getElementById("notasModal").textContent = `Notas: ${tarjeta.notas || "No hay notas"}`;

    // Asignar evento al botón eliminar dentro del modal
    const eliminarButton = document.getElementById("botonEliminar");
    eliminarButton.onclick = () => {
        if (rolUsuario === "admin" || usuario === tarjeta.propietario) {
            eliminarTarjeta(tarjeta.id, tarjetaElement);
        } else {
            alert("No dispones de permisos para realizar esta acción.");
        }
    };

    modal.style.display = "flex";
}


function eliminarTarjeta(idTarjeta, tarjetaElement) {
    if (!idTarjeta) {
        alert("Error: No se ha encontrado la tarea.");
        return;
    }

    fetch("./php/eliminarTarjetas.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(idTarjeta)}`,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Asegurarse de que tarjetaElement es un elemento DOM válido
                if (tarjetaElement instanceof HTMLElement) {
                    tarjetaElement.remove();  // Eliminar el elemento del DOM
                } else {
                    console.error("Elemento no válido para eliminar del DOM");
                }
                document.getElementById("modal").style.display = "none";
                alert("Tarea eliminada con éxito.");
            } else {
                alert("Error al eliminar la tarea: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error al eliminar la tarea:", error);
            alert("Hubo un problema al procesar la solicitud.");
        });
}

// Evento para cerrar el modal al hacer clic en la "X"
document.getElementById("cerrarModal").addEventListener("click", () => {
    document.getElementById("modal").style.display = "none";
});

//Llamar a la función para obtener y mostrar las tarjetas después de la sesión
obtenerDatosSesion();
setTimeout(obtenerTarjetas, 1000);