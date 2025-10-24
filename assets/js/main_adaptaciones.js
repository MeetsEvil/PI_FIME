// ==================== SCRIPT PRINCIPAL PARA CREAR / EDITAR ADAPTACIONES ====================

// Esperar a que el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
    const submitBtn = document.getElementById("submitAdaptacionBtn");
    const form = document.getElementById("beneficiaryForm");

    if (!submitBtn || !form) return; // Evita errores si no está en la página correcta

    // ======== Al hacer clic en Guardar Adaptación ========
    submitBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const profesionalInput = document.getElementById("profesionalAsignadoNombre");
        const profesionalList = document.getElementById("profesionales-list");
        const profesionalIdField = document.getElementById("profesionalAsignadoId");
        const profesionalErrorDiv = document.getElementById("profesionalError");
        const globalValidationMessage = document.getElementById("formValidationMessage");

        // Validar profesional
        let valid = validateProfesional(
            profesionalInput,
            profesionalList,
            profesionalIdField,
            profesionalErrorDiv
        );

        if (valid && form.checkValidity()) {
            await submitAdaptacionFormAjax(form);
        } else {
            form.reportValidity();
        }
    });
});


// ==================== VALIDACIÓN DE PROFESIONAL ====================
function validateProfesional(input, list, idField, errorDiv) {
    const selected = Array.from(list.options).find(
        (opt) => opt.value.trim() === input.value.trim()
    );

    if (selected) {
        idField.value = selected.dataset.id;
        errorDiv.textContent = "";
        return true;
    } else {
        idField.value = "";
        errorDiv.textContent = "Seleccione un profesional válido de la lista.";
        return false;
    }
}


// ==================== ENVÍO DEL FORMULARIO POR AJAX ====================
async function submitAdaptacionFormAjax(form) {
    const button = document.getElementById("submitAdaptacionBtn");
    const globalValidationMessage = document.getElementById("formValidationMessage");
    const successModal = document.getElementById("successModal");

    const formData = new FormData(form);
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: "POST",
            body: formData,
        });

        const result = await response.json();

        if (result.success) {
            successModal.style.display = "flex";

            // Redirige al historial del beneficiario
            const id = form.querySelector("[name=beneficiario_id]").value;
            setTimeout(() => {
                window.location.href = "historico_adaptaciones.php?id=" + id;
            }, 3000);
        } else {
            globalValidationMessage.textContent = result.message;
        }
    } catch (error) {
        globalValidationMessage.textContent = "Error al guardar la adaptación.";
        console.error("Error en la solicitud AJAX:", error);
    } finally {
        button.innerHTML =
            'Guardar Adaptación <ion-icon name="checkmark-circle-outline"></ion-icon>';
        button.disabled = false;
    }
}
