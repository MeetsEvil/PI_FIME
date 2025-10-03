// Menu Toggle (Mantenido)
let toggle = document.querySelector(".toggle");
let navigation = document.querySelector(".navigation");
let main = document.querySelector(".main");

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

let currentPage = 0;

/**
 * Valida los campos requeridos en la página actual.
 * @returns {boolean} - Verdadero si todos los campos son válidos.
 */
function validatePage(page) {
    const requiredFields = page.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.checkValidity()) {
            isValid = false;
        }
    });

    if (page.dataset.page === '3' && profesionalInput) {
        isValid = validateProfesional(profesionalInput, profesionalesList, profesionalIdField, profesionalErrorDiv) && isValid;
    }
    
    if (!isValid) {
        globalValidationMessage.textContent = "";
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
        errorDiv.textContent = '';
        idField.value = '';
        return false; 
    }

    let profesionalEncontrado = false;
    let profesionalId = null;

    for (const option of datalist.options) {
        if (option.value === inputValue) {
            profesionalEncontrado = true;
            profesionalId = option.dataset.id; 
            break;
        }
    }

    if (profesionalEncontrado) {
        errorDiv.textContent = '';
        idField.value = profesionalId;
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


function showPage(index) {
    pages.forEach((page, i) => page.classList.toggle('is-active', i === index));
    pageNumber.textContent = index + 1;

    // Cambiar botón Siguiente/Guardar
    if (index === pages.length - 1) {
        nextBtn.innerHTML = 'Guardar <ion-icon name="checkmark-circle-outline"></ion-icon>';
        nextBtn.type = "button";
    } else {
        nextBtn.innerHTML = 'Siguiente <ion-icon name="arrow-forward-outline"></ion-icon>';
        nextBtn.type = "button";
    }

    // Mostrar u ocultar botón Anterior
    prevBtn.style.display = index === 0 ? 'none' : 'flex';
}

nextBtn.addEventListener('click', () => {
    const currentPageElement = pages[currentPage];
    
    if (currentPage < pages.length - 1) {
        if (validatePage(currentPageElement)) {
            currentPage++;
            showPage(currentPage);
        }
    } else {
        if (validatePage(currentPageElement)) {
            // Llama a la función para mostrar el modal de éxito y redirigir
            showSuccessAndRedirect();
        }
    }
});

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