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



// main_editar_intervenciones.js
$(document).ready(function () {
    const $btn = $('#updateIntervencionBtn');
    const $form = $('#editIntervencionForm');

    // Cuando se presione el botón de guardar
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
                // Si no coincide, avisar y evitar envío
                $('#profesionalAsignadoId').val('');
                $('#profesionalError').text('Seleccione un profesional válido de la lista.');
                $('#profesionalError').css('color', 'red');
                return;
            }
        } else {
            $('#profesionalError').text('El profesional es obligatorio.');
            $('#profesionalError').css('color', 'red');
            return;
        }

        const formElement = $form[0];
        const formData = new FormData(formElement);

        // Debug: imprime en consola lo que se enviará
        console.log('=== DATOS A ACTUALIZAR ===');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        console.log('==========================');

        $.ajax({
            url: 'actualizar_intervencion.php',
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
                console.log('=== RESPUESTA DEL SERVIDOR ===');
                console.log(response);
                console.log('===============================');
                
                if (response && response.success) {
                    $('#successModal').fadeIn(200).css('display', 'flex');
                    setTimeout(function () {
                        const beneficiarioId = $form.find('input[name="beneficiario_id"]').val();
                        window.location.href = 'historico_intervenciones.php?id=' + beneficiarioId;
                    }, 2000);
                } else {
                    const msg = (response && (response.error || response.message)) 
                        ? (response.error || response.message) 
                        : 'Respuesta inesperada del servidor.';
                    $('#formValidationMessage').text('Error al actualizar: ' + msg).css('color', 'red');
                }
            },
            error: function (xhr, status, error) {
                console.error('=== ERROR AJAX ===');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('==================');
                
                let serverText = xhr.responseText || error;
                $('#formValidationMessage').text('Error de conexión/servidor: ' + serverText).css('color', 'red');
            },
            complete: function () {
                $btn.prop('disabled', false).html('Guardar Cambios <ion-icon name="checkmark-circle-outline"></ion-icon>');
            }
        });
    });

    // Sincronizar el ID del profesional al escribir en el input
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
            if (nombre.length > 0) {
                $('#profesionalError').text('Seleccione un profesional válido de la lista.');
                $('#profesionalError').css('color', 'red');
            }
        }
    });
});