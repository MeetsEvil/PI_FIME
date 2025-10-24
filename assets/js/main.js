// Menu Toggle (Mantenido)
const toggle = document.querySelector(".toggle");
const navigation = document.querySelector(".navigation");
const main = document.querySelector(".main");

if (toggle && navigation && main) {
    toggle.onclick = function () {
        navigation.classList.toggle("active");
        main.classList.toggle("active");
    };
}


// ==================== LÓGICA DE MODALES GENERALES ====================

// Modal de Contacto
const contactModal = document.getElementById("contactModal");
const closeContact = document.getElementById("closeContact");

if (contactModal && closeContact) {
    // Estas funciones deben ser globales (window.) para que los botones 'onclick' del HTML puedan llamarlas.
    window.mostrarInfo = function () {
        contactModal.style.display = "flex";
    };

    closeContact.onclick = function () {
        contactModal.style.display = "none";
    };
}

// Modal de Cerrar Sesión
const logoutModal = document.getElementById("logoutModal");
const closeLogoutBtn = document.querySelector("#logoutModal .close-btn");
const cancelBtn = document.getElementById("cancelBtn");

if (logoutModal && closeLogoutBtn && cancelBtn) {
    // Estas funciones deben ser globales (window.) para que los botones 'onclick' del HTML puedan llamarlas.
    window.showLogoutModal = function () {
        logoutModal.style.display = "flex";
    };

    closeLogoutBtn.onclick = function () {
        logoutModal.style.display = "none";
    };

    cancelBtn.onclick = function () {
        logoutModal.style.display = "none";
    };
}

// Lógica de cierre general de modales
window.onclick = function (event) {
    if (contactModal && event.target === contactModal) {
        contactModal.style.display = "none";
    }
    if (logoutModal && event.target === logoutModal) {
        logoutModal.style.display = "none";
    }
};


// ==================== LÓGICA DE PAGINACIÓN Y FORMULARIOS ====================

const form = document.getElementById('beneficiaryForm');
const pages = document.querySelectorAll('.form-page');
const nextBtn = document.querySelector('.btn-next');
const prevBtn = document.querySelector('.btn-prev');
const pageNumber = document.getElementById('currentPageNumber');
const globalValidationMessage = document.getElementById('formValidationMessage');
const profesionalInput = document.getElementById('profesionalAsignadoNombre');
const profesionalIdField = document.getElementById('profesionalAsignadoId');
const profesionalErrorDiv = document.getElementById('profesionalError');
const profesionalesList = document.getElementById('profesionales-list');
const successModal = document.getElementById('successModal');

// Definimos la URL de destino por defecto para la creación. 
const defaultSubmitUrl = 'guardar_beneficiarios.php';

// Verificamos si los elementos del formulario existen antes de ejecutar el código de paginación
if (form && pages.length > 0 && nextBtn && prevBtn) {
    let currentPage = 0;

    // Detectar si estamos en modo de visualización o edición/creación
    const isViewMode = window.location.pathname.includes('ver_beneficiarios.php');
    const isEditMode = window.location.pathname.includes('editar_beneficiarios.php');

    // --- CORRECCIÓN CRUCIAL DE LA URL DE DESTINO ---
    // Si NO estamos en modo visualización, nos aseguramos de que el action sea el correcto.
    if (isEditMode) {
        // Modo edición: Usamos la URL de actualización
        form.action = 'actualizar_beneficiarios.php';
    } else if (!isViewMode) {
        // Modo creación: Usamos la URL de guardar
        form.action = defaultSubmitUrl;
    }
    // Si es isViewMode, el form.action no importa ya que no se envía nada.


    /**
     * Valida los campos requeridos en la página actual.
     * @returns {boolean} - Verdadero si todos los campos son válidos.
     */
    function validatePage(page) {
        // Si es modo visualización, saltamos toda la validación
        if (isViewMode) {
            return true;
        }

        const requiredFields = page.querySelectorAll('[required]');
        let isValid = true;

        // 1. Validación de campos HTML (navegador)
        requiredFields.forEach(field => {
            if (!field.checkValidity()) {
                isValid = false;
            }
        });

        // 2. Validación de lógica de negocio (Profesional Asignado)
        if (page.dataset.page === '3' && profesionalInput) {
            isValid = validateProfesional(profesionalInput, profesionalesList, profesionalIdField, profesionalErrorDiv) && isValid;
        }

        // Muestra el mensaje de error si es necesario
        if (!isValid) {
            globalValidationMessage.textContent = "";
            // Muestra el mensaje de error del primer campo inválido
            for (let field of requiredFields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    break;
                }
            }
        } else {
            globalValidationMessage.textContent = "";
        }

        return isValid;
    }

    /**
     * Verifica si el nombre del profesional ingresado existe en la datalist
     */
    function validateProfesional(input, datalist, idField, errorDiv) {
        const inputValue = input.value.trim();
        if (!inputValue) {
            errorDiv.textContent = 'El profesional asignado es obligatorio.';
            idField.value = '';
            return false;
        }

        let profesionalEncontrado = false;
        let profesionalId = null;

        // Busca en las opciones de la datalist (generadas por PHP)
        for (const option of datalist.options) {
            if (option.value === inputValue) {
                profesionalEncontrado = true;
                // Recupera el ID del data-id
                profesionalId = option.dataset.id;
                break;
            }
        }

        if (profesionalEncontrado) {
            errorDiv.textContent = '';
            idField.value = profesionalId; // Asigna el ID real al campo oculto
            return true;
        } else {
            errorDiv.textContent = 'El profesional asignado no existe en la lista. Por favor, selecciona uno válido.';
            idField.value = '';
            return false;
        }
    }

    /**
     * Muestra el modal de éxito y redirige después de 3 segundos.
     */
    function showSuccessAndRedirect() {
        if (successModal) {
            // Cambiamos el texto del modal si estamos en modo edición
            const title = successModal.querySelector('.success-title');
            const message = successModal.querySelector('.modal-body p');

            if (isEditMode && title) {
                title.textContent = '¡Actualización Exitosa!';
            }
            if (isEditMode && message) {
                message.textContent = 'Los cambios del beneficiario se han guardado correctamente.';
            }

            successModal.style.display = 'flex';
            setTimeout(() => {
                // Redirige a la página de beneficiarios
                window.location.href = '../../modules/beneficiarios/index_beneficiarios.php';
            }, 3000);
        }
    }

    /**
     * Envía el formulario de forma asíncrona (AJAX)
     */
    async function submitForm() {
        if (!validatePage(pages[currentPage])) {
            return; // Detiene el envío si la última página no es válida
        }

        const formData = new FormData(form);

        // Muestra indicador de carga
        nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        nextBtn.disabled = true;

        try {
            // Usamos form.action para obtener la URL correcta (guardar_beneficiarios.php o actualizar_beneficiarios.php)
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Éxito: Muestra el modal de éxito
                showSuccessAndRedirect();
            } else {
                // Error: Muestra el mensaje de error retornado por PHP
                globalValidationMessage.textContent = result.message || 'Error desconocido al procesar el beneficiario.';
            }
        } catch (error) {
            globalValidationMessage.textContent = 'Error de conexión con el servidor.';
            console.error('Error al enviar formulario:', error);
        } finally {
            // Restaura el botón
            nextBtn.innerHTML = isEditMode ? 'Guardar Cambios <ion-icon name="checkmark-circle-outline"></ion-icon>' : 'Guardar <ion-icon name="checkmark-circle-outline"></ion-icon>';
            nextBtn.disabled = false;
        }
    }


    function showPage(index) {
        pages.forEach((page, i) => page.classList.toggle('is-active', i === index));
        pageNumber.textContent = index + 1;

        if (isViewMode) {
            // --- LÓGICA DE NAVEGACIÓN (VER) ---
            if (nextBtn) {
                if (index === pages.length - 1) {
                    // Ocultar si es la última página
                    nextBtn.style.display = 'none';
                } else {
                    // Mostrar y asignar la función de solo avanzar
                    nextBtn.style.display = 'flex';
                    nextBtn.innerHTML = 'Siguiente <ion-icon name="arrow-forward-outline"></ion-icon>';

                    nextBtn.onclick = function () {
                        currentPage++;
                        showPage(currentPage);
                    };
                }
            }
        } else {
            // --- LÓGICA DE CREACIÓN/EDICIÓN ---
            if (nextBtn) {
                nextBtn.style.display = 'flex';
                if (index === pages.length - 1) {
                    // Última página: Botón Guardar/Actualizar
                    nextBtn.innerHTML = isEditMode ? 'Guardar Cambios <ion-icon name="checkmark-circle-outline"></ion-icon>' : 'Guardar <ion-icon name="checkmark-circle-outline"></ion-icon>';
                    nextBtn.onclick = submitForm;
                } else {
                    // Páginas intermedias: Botón Siguiente con validación
                    nextBtn.innerHTML = 'Siguiente <ion-icon name="arrow-forward-outline"></ion-icon>';
                    nextBtn.onclick = function () {
                        const currentPageElement = pages[currentPage];
                        if (validatePage(currentPageElement)) {
                            currentPage++;
                            showPage(currentPage);
                        }
                    };
                }
            }
        }

        // Mostrar u ocultar botón Anterior (aplica a todos los modos)
        if (prevBtn) {
            prevBtn.style.display = index === 0 ? 'none' : 'flex';
        }
    }

    // Listener para el botón anterior (mantenido)
    prevBtn.addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            showPage(currentPage);
            globalValidationMessage.textContent = "";
        }
    });

    /**
     * Inicializa Flatpickr en todos los campos de fecha.
     */
    function initDatePickers() {
        // Solo inicializa Flatpickr si estamos en modo Edición o Creación
        if (!isViewMode && typeof flatpickr !== 'undefined') {
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                flatpickr(input, {
                    locale: {
                        weekdays: {
                            shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                            longhand: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
                        },
                        months: {
                            shorthand: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                            longhand: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                        },
                        firstDayOfWeek: 1,
                        rangeSeparator: " a ",
                        time_24hr: true,
                    },
                    dateFormat: 'Y-m-d',
                });
            });
        }
    }

    // Inicializar la paginación al cargar la página
    showPage(currentPage);
    initDatePickers();
}
