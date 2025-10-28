document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.querySelector(".toggle");
    const navigation = document.querySelector(".navigation");
    const main = document.querySelector(".main");

    if (toggle && navigation && main) {
        toggle.onclick = () => {
            navigation.classList.toggle("active");
            main.classList.toggle("active");
        };
    }
});


// Esperar a que el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
    const submitBtn = document.getElementById("submitIntervencionBtn");
    const form = document.getElementById("beneficiaryForm");

    if (!submitBtn || !form) return; // Evita errores si no está en la página correcta

    // ======== Al hacer clic en Guardar Intervencion ========
    submitBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const profesionalInput = document.getElementById("profesionalAsignadoNombre");
        const profesionalList = document.getElementById("profesionales-list");
        const profesionalIdField = document.getElementById("profesionalAsignadoId");
        const profesionalErrorDiv = document.getElementById("profesionalError");
        const globalValidationMessage = document.getElementById("formValidationMessage");

        // Limpiar mensaje de error anterior
        globalValidationMessage.textContent = "";
        globalValidationMessage.style.color = "";

        // Validar profesional
        let valid = validateProfesional(
            profesionalInput,
            profesionalList,
            profesionalIdField,
            profesionalErrorDiv
        );

        if (valid && form.checkValidity()) {
            await submitIntervencionFormAjax(form);
        } else {
            form.reportValidity();
            if (!valid) {
                globalValidationMessage.textContent = "Por favor, seleccione un profesional válido de la lista.";
                globalValidationMessage.style.color = "red";
            }
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
        errorDiv.style.color = "";
        return true;
    } else {
        idField.value = "";
        errorDiv.textContent = "Seleccione un profesional válido de la lista.";
        errorDiv.style.color = "red";
        return false;
    }
}


// ==================== ENVÍO DEL FORMULARIO POR AJAX ====================
async function submitIntervencionFormAjax(form) {
    const button = document.getElementById("submitIntervencionBtn");
    const globalValidationMessage = document.getElementById("formValidationMessage");
    const successModal = document.getElementById("successModal");

    const formData = new FormData(form);
    
    // ===== DEBUG: Mostrar datos del formulario en consola =====
    console.log("=== DATOS DEL FORMULARIO ENVIADOS ===");
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    console.log("======================================");
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: "POST",
            body: formData,
        });

        // Verificar si la respuesta es JSON válida
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("La respuesta del servidor no es JSON válida");
        }

        const result = await response.json();
        
        console.log("=== RESPUESTA DEL SERVIDOR ===");
        console.log(result);
        console.log("================================");

        if (result.success) {
            // Éxito: mostrar modal y redirigir
            successModal.style.display = "flex";

            // Redirige al historial del beneficiario
            const id = form.querySelector("[name=beneficiario_id]").value;
            setTimeout(() => {
                window.location.href = "historico_intervenciones.php?id=" + id;
            }, 3000);
        } else {
            // Error del servidor
            globalValidationMessage.textContent = result.message || "Error desconocido al guardar";
            globalValidationMessage.style.color = "red";
            globalValidationMessage.style.fontSize = "1.1em";
            globalValidationMessage.style.marginTop = "10px";
            console.error("Error del servidor:", result.message);
        }
    } catch (error) {
        // Error de red o JSON inválido
        globalValidationMessage.textContent = "Error al guardar la intervención: " + error.message;
        globalValidationMessage.style.color = "red";
        globalValidationMessage.style.fontSize = "1.1em";
        globalValidationMessage.style.marginTop = "10px";
        console.error("Error en la solicitud AJAX:", error);
    } finally {
        // Restaurar botón
        button.innerHTML = 'Guardar Intervención <ion-icon name="checkmark-circle-outline"></ion-icon>';
        button.disabled = false;
    }
}