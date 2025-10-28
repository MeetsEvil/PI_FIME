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


// main_editar_diagnosticos.js (reemplaza/pega)
$(document).ready(function () {
    const $btn = $('#updateDiagnosisBtn');
    const $form = $('#editDiagnosisForm');

    // Cuando guardes: usa FormData para mayor robustez
    $btn.on('click', function (e) {
        e.preventDefault();

        // Intentar asegurar que el profesional seleccionado tenga su ID
        const profNombre = $('#profesionalAsignadoNombre').val().trim();
        if (profNombre) {
            const $opt = $('#profesionales-list option').filter(function () {
                return $(this).val().trim() === profNombre;
            }).first();

            if ($opt.length) {
                $('#profesionalAsignadoId').val($opt.data('id'));
                $('#profesionalError').text('');
            } else {
                // Si no coincide, avisar pero permitir enviar si quieres (aquí lo evitamos)
                $('#profesionalAsignadoId').val('');
                $('#profesionalError').text('Seleccione un profesional válido de la lista.');
                return; // evita enviar si profesional inválido
            }
        } else {
            $('#profesionalError').text('El profesional es obligatorio.');
            return;
        }

        const formElement = $form[0];
        const formData = new FormData(formElement);

        // Debug: imprime en consola lo que se enviará
        console.log('Enviando actualizar_diagnostico.php con:', Array.from(formData.entries()));

        $.ajax({
            url: 'actualizar_diagnostico.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                $('#formValidationMessage').text('').css('color', '');
            },
            success: function (response) {
                console.log('Respuesta del servidor:', response);
                if (response && response.success) {
                    $('#successModal').fadeIn(200).css('display', 'flex');
                    setTimeout(function () {
                        const beneficiarioId = $form.find('input[name="beneficiario_id"]').val();
                        window.location.href = 'historico_diagnosticos.php?id=' + beneficiarioId;
                    }, 2000);
                } else {
                    const msg = (response && (response.error || response.message)) ? (response.error || response.message) : 'Respuesta inesperada del servidor.';
                    $('#formValidationMessage').text('Error al actualizar: ' + msg).css('color', 'red');
                }
            },
            error: function (xhr, status, error) {
                // Muestra info útil para depurar
                console.error('AJAX error:', status, error);
                console.error('XHR responseText:', xhr.responseText);
                let serverText = xhr.responseText || error;
                $('#formValidationMessage').text('Error de conexión/servidor: ' + serverText).css('color', 'red');
            },
            complete: function () {
                $btn.prop('disabled', false).html('Guardar Cambios <ion-icon name="checkmark-circle-outline"></ion-icon>');
            }
        });
    });

    // Mantenemos la lógica de sincronizar el id del profesional al escribir (útil si no usamos el bloqueo anterior)
    $('#profesionalAsignadoNombre').on('input', function () {
        const nombre = $(this).val().trim();
        const option = $('#profesionales-list option').filter(function () {
            return $(this).val().trim() === nombre;
        }).first();

        if (option.length) {
            $('#profesionalAsignadoId').val(option.data('id'));
            $('#profesionalError').text('');
        } else {
            $('#profesionalAsignadoId').val('');
            $('#profesionalError').text('Seleccione un profesional válido de la lista.');
        }
    });
});
