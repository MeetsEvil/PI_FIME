// main_usuarios.js - Manejo de formulario de usuarios con paginación

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('usuarioForm');
    const pages = document.querySelectorAll('.form-page');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const validationMessage = document.getElementById('formValidationMessage');
    const passwordError = document.getElementById('passwordError');
    
    let currentPage = 1;
    const totalPages = pages.length;

    // Función para mostrar la página actual
    function showPage(pageNumber) {
        pages.forEach((page, index) => {
            if (index + 1 === pageNumber) {
                page.classList.add('is-active');
            } else {
                page.classList.remove('is-active');
            }
        });

        // Actualizar número de página
        const pageNumberElement = document.getElementById('currentPageNumber');
        if (pageNumberElement) {
            pageNumberElement.textContent = pageNumber;
        }

        // Actualizar botones
        if (pageNumber === 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'inline-flex';
            submitBtn.style.display = 'none';
        } else if (pageNumber === totalPages) {
            prevBtn.style.display = 'inline-flex';
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-flex';
        } else {
            prevBtn.style.display = 'inline-flex';
            nextBtn.style.display = 'inline-flex';
            submitBtn.style.display = 'none';
        }

        currentPage = pageNumber;
    }

    // Función para validar la página actual
    function validateCurrentPage() {
        const currentPageElement = document.querySelector(`.form-page[data-page="${currentPage}"]`);
        const inputs = currentPageElement.querySelectorAll('input[required], select[required]');
        let isValid = true;
        let firstInvalidField = null;

        inputs.forEach(input => {
            // Validar que el campo no esté vacío
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#e74c3c';
                if (!firstInvalidField) firstInvalidField = input;
            } else {
                // Validar pattern si existe
                if (input.hasAttribute('pattern')) {
                    const pattern = new RegExp(input.getAttribute('pattern'));
                    if (!pattern.test(input.value)) {
                        isValid = false;
                        input.style.borderColor = '#e74c3c';
                        if (!firstInvalidField) firstInvalidField = input;
                    } else {
                        input.style.borderColor = '';
                    }
                } else {
                    input.style.borderColor = '';
                }
            }
        });

        // Validación especial para página 2: contraseñas
        if (currentPage === 2) {
            const password = document.querySelector('input[name="contrasena"]');
            const confirmPassword = document.querySelector('input[name="confirmar_contrasena"]');
            const isEditMode = window.isEditMode || false;
            
            if (password && confirmPassword) {
                // En modo edición, las contraseñas son opcionales
                if (isEditMode) {
                    // Solo validar si se ingresó alguna contraseña
                    if (password.value || confirmPassword.value) {
                        if (password.value !== confirmPassword.value) {
                            isValid = false;
                            passwordError.textContent = 'Las contraseñas no coinciden';
                            passwordError.style.display = 'block';
                            password.style.borderColor = '#e74c3c';
                            confirmPassword.style.borderColor = '#e74c3c';
                        } else if (password.value.length < 4) {
                            isValid = false;
                            passwordError.textContent = 'La contraseña debe tener al menos 4 caracteres';
                            passwordError.style.display = 'block';
                            password.style.borderColor = '#e74c3c';
                        } else {
                            passwordError.style.display = 'none';
                            password.style.borderColor = '';
                            confirmPassword.style.borderColor = '';
                        }
                    } else {
                        // No se ingresó contraseña, está bien
                        passwordError.style.display = 'none';
                        password.style.borderColor = '';
                        confirmPassword.style.borderColor = '';
                    }
                } else {
                    // En modo creación, las contraseñas son obligatorias
                    if (password.value !== confirmPassword.value) {
                        isValid = false;
                        passwordError.textContent = 'Las contraseñas no coinciden';
                        passwordError.style.display = 'block';
                        password.style.borderColor = '#e74c3c';
                        confirmPassword.style.borderColor = '#e74c3c';
                    } else if (password.value.length < 4) {
                        isValid = false;
                        passwordError.textContent = 'La contraseña debe tener al menos 4 caracteres';
                        passwordError.style.display = 'block';
                        password.style.borderColor = '#e74c3c';
                    } else {
                        passwordError.style.display = 'none';
                        password.style.borderColor = '';
                        confirmPassword.style.borderColor = '';
                    }
                }
            }
        }

        if (!isValid) {
            validationMessage.textContent = 'Por favor, completa todos los campos requeridos correctamente.';
            validationMessage.style.display = 'block';
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
        } else {
            validationMessage.style.display = 'none';
        }

        return isValid;
    }

    // Botón Siguiente
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateCurrentPage()) {
                if (currentPage < totalPages) {
                    showPage(currentPage + 1);
                }
            }
        });
    }

    // Botón Anterior
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                showPage(currentPage - 1);
            }
        });
    }

    // Botón Guardar
    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            if (validateCurrentPage()) {
                submitForm();
            }
        });
    }

    // Función para enviar el formulario
    function submitForm() {
        const formData = new FormData(form);
        
        // Determinar si estamos en modo edición o creación
        const isEditMode = window.isEditMode || false;
        const actionUrl = isEditMode ? 'actualizar_usuario.php' : 'guardar_usuario.php';
        const buttonText = isEditMode ? 'Actualizar Usuario' : 'Guardar Usuario';

        // Mostrar indicador de carga
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar modal de éxito
                const successModal = document.getElementById('successModal');
                successModal.style.display = 'flex';

                // Redirigir después de 3 segundos
                setTimeout(() => {
                    window.location.href = 'index_usuarios.php';
                }, 3000);
            } else {
                // Mostrar error
                validationMessage.textContent = data.message || 'Error al guardar el usuario. Intenta nuevamente.';
                validationMessage.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = buttonText + ' <ion-icon name="checkmark-circle-outline"></ion-icon>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            validationMessage.textContent = 'Error de conexión. Intenta nuevamente.';
            validationMessage.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = buttonText + ' <ion-icon name="checkmark-circle-outline"></ion-icon>';
        });
    }

    // Limpiar estilos de error al escribir
    const allInputs = form.querySelectorAll('input, select');
    allInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            validationMessage.style.display = 'none';
        });
    });

    // Inicializar mostrando la primera página
    showPage(1);
});
