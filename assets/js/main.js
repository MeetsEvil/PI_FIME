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


// ==================== LÓGICA DE PAGINACIÓN Y VALIDACIÓN ====================

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
const submitTargetUrl = 'guardar_beneficiarios.php'; // Define el script de PHP que procesará los datos

let currentPage = 0;

/**
 * Valida los campos requeridos en la página actual.
 * @returns {boolean} - Verdadero si todos los campos son válidos.
 */
function validatePage(page) {
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
    successModal.style.display = 'flex';
    setTimeout(() => {
        // Redirige a la página de beneficiarios
        window.location.href = '../../modules/beneficiarios/index_beneficiarios.php'; 
    }, 3000); 
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
        const response = await fetch(submitTargetUrl, {
            method: 'POST',
            body: formData
        });

        // El script PHP debe devolver un texto JSON con { success: true/false, message: "..." }
        const result = await response.json(); 

        if (result.success) {
            // Éxito: Muestra el modal de éxito
            showSuccessAndRedirect();
        } else {
            // Error: Muestra el mensaje de error retornado por PHP
            globalValidationMessage.textContent = result.message || 'Error desconocido al registrar el beneficiario.';
        }
    } catch (error) {
        globalValidationMessage.textContent = 'Error de conexión con el servidor. Inténtalo de nuevo.';
        console.error('Error al enviar formulario:', error);
    } finally {
        // Restaura el botón
        nextBtn.innerHTML = 'Guardar <ion-icon name="checkmark-circle-outline"></ion-icon>';
        nextBtn.disabled = false;
    }
}


function showPage(index) {
    pages.forEach((page, i) => page.classList.toggle('is-active', i === index));
    pageNumber.textContent = index + 1;

    // Cambiar botón Siguiente/Guardar
    if (index === pages.length - 1) {
        nextBtn.innerHTML = 'Guardar <ion-icon name="checkmark-circle-outline"></ion-icon>';
        nextBtn.onclick = submitForm; // Asigna la función de envío al botón
    } else {
        nextBtn.innerHTML = 'Siguiente <ion-icon name="arrow-forward-outline"></ion-icon>';
        // Asigna la función de paginación al botón
        nextBtn.onclick = function() {
            const currentPageElement = pages[currentPage];
            if (validatePage(currentPageElement)) {
                currentPage++;
                showPage(currentPage);
            }
        };
    }

    // Mostrar u ocultar botón Anterior
    prevBtn.style.display = index === 0 ? 'none' : 'flex';
}

// Listener para el botón anterior (mantenido)
prevBtn.addEventListener('click', () => {
    if (currentPage > 0) {
        currentPage--;
        showPage(currentPage);
        globalValidationMessage.textContent = "";
    }
});

// Inicializar la paginación al cargar la página
if (pages.length > 0) {
    showPage(currentPage);
}
