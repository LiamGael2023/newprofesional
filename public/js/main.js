/**
 * Sistema de Gestión de Colegio Profesional
 * JavaScript Principal
 */

// Confirmación de eliminación
function confirmarEliminacion(mensaje) {
    return confirm(mensaje || '¿Está seguro de eliminar este registro?');
}

// Función para anular
function confirmarAnulacion(mensaje) {
    return confirm(mensaje || '¿Está seguro de anular este registro?');
}

// Auto-hide alerts después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// Validación de formularios
function validarFormulario(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const required = form.querySelectorAll('[required]');
    let valid = true;

    required.forEach(function(field) {
        if (!field.value.trim()) {
            field.style.borderColor = 'red';
            valid = false;
        } else {
            field.style.borderColor = '';
        }
    });

    return valid;
}

// Formatear moneda
function formatMoney(amount) {
    return 'S/ ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Calcular totales en tablas
function calcularTotales(className) {
    const elements = document.querySelectorAll('.' + className);
    let total = 0;

    elements.forEach(function(el) {
        const value = parseFloat(el.textContent || el.value || 0);
        total += value;
    });

    return total;
}
