document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // TEMPORIZADOR DE INACTIVIDAD (15 minutos)
    // ============================================
    let tiempoInactividad = 0;
    const limiteInactividad = 15 * 60; // 15 minutos en segundos

    function reiniciarTemporizador() {
        tiempoInactividad = 0;
    }

    function verificarInactividad() {
        tiempoInactividad++;
        if (tiempoInactividad >= limiteInactividad) {
            // Cerrar sesión automáticamente
            window.location.href = 'logout.php';
        }
    }

    // Eventos que reinician el temporizador
    document.addEventListener('click', reiniciarTemporizador);
    document.addEventListener('keypress', reiniciarTemporizador);
    document.addEventListener('mousemove', reiniciarTemporizador);
    document.addEventListener('scroll', reiniciarTemporizador);

    // Verificar inactividad cada segundo
    setInterval(verificarInactividad, 1000);

    // ============================================
    // VALIDACIÓN DE FORMULARIOS
    // ============================================
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                const errorElement = field.parentElement.querySelector('.error-message');
                if (errorElement) errorElement.remove();
                field.classList.remove('is-invalid');
                
                const value = field.value.trim();
                if (value === '') {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    const error = document.createElement('small');
                    error.className = 'error-message text-danger';
                    error.textContent = 'Este campo es obligatorio';
                    field.parentElement.appendChild(error);
                }
                
                if (field.type === 'email' && value !== '') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        
                        const error = document.createElement('small');
                        error.className = 'error-message text-danger';
                        error.textContent = 'Ingrese un correo electrónico válido';
                        field.parentElement.appendChild(error);
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                const firstError = this.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    });
    
    // ============================================
    // CONFIRMACIÓN DE ELIMINACIÓN
    // ============================================
    document.querySelectorAll('.btn-delete, .delete-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de que desea eliminar este registro?')) {
                e.preventDefault();
            }
        });
    });
    
    // ============================================
    // AUTO-CERRAR ALERTAS
    // ============================================
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // ============================================
    // FILTROS EN TABLAS
    // ============================================
    const searchInput = document.querySelector('#table-search');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const table = document.querySelector('.table tbody');
            if (!table) return;
            
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
    
    // ============================================
    // SELECT CON BÚSQUEDA
    // ============================================
    document.querySelectorAll('.select-search').forEach(select => {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control mb-1';
        searchInput.placeholder = 'Buscar...';
        searchInput.style.marginBottom = '5px';
        
        select.parentElement.insertBefore(searchInput, select);
        
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const options = select.querySelectorAll('option');
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });
    
    // ============================================
    // MASCARAS PARA CAMPOS
    // ============================================
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            this.value = value;
        });
    });
    
    console.log('Sistema cargado correctamente');
});