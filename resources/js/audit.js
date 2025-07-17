// Funcionalidades adicionales para la vista de auditoría
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-submit de filtros cuando cambian
    const filterInputs = document.querySelectorAll('.audit-filters select, .audit-filters input[type="date"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Solo auto-submit si hay un valor seleccionado
            if (this.value) {
                this.closest('form').submit();
            }
        });
    });

    // Tooltips personalizados
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = this.getAttribute('data-tooltip');
            if (tooltip) {
                showTooltip(this, tooltip);
            }
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });

    // Función para mostrar tooltip
    function showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
        
        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);
        
        element._tooltip = tooltip;
    }

    // Función para ocultar tooltip
    function hideTooltip() {
        const tooltip = document.querySelector('.custom-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Animaciones para las tarjetas de estadísticas
    const statsCards = document.querySelectorAll('.stats-card');
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('audit-fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    statsCards.forEach(card => {
        observer.observe(card);
    });

    // Función para exportar datos filtrados
    window.exportAuditData = function() {
        const currentUrl = new URL(window.location.href);
        const exportUrl = currentUrl.pathname.replace('/auditorias', '/auditorias/export');
        currentUrl.pathname = exportUrl;
        
        // Mostrar loading
        const exportBtn = document.querySelector('.export-btn');
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Exportando...';
        exportBtn.disabled = true;
        
        // Descargar archivo
        const link = document.createElement('a');
        link.href = currentUrl.toString();
        link.download = 'auditoria_' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Restaurar botón
        setTimeout(() => {
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        }, 2000);
    };

    // Función para limpiar filtros
    window.clearAuditFilters = function() {
        const form = document.querySelector('.audit-filters form');
        const inputs = form.querySelectorAll('select, input');
        
        inputs.forEach(input => {
            if (input.type === 'date') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        form.submit();
    };

    // Función para actualizar estadísticas en tiempo real
    window.refreshAuditStats = function() {
        const statsContainer = document.querySelector('.audit-stats');
        if (!statsContainer) return;
        
        fetch('/auditorias/stats')
            .then(response => response.json())
            .then(data => {
                // Actualizar estadísticas
                document.querySelector('.total-logs').textContent = data.total_logs;
                document.querySelector('.today-logs').textContent = data.today_logs;
                document.querySelector('.unique-users').textContent = data.unique_users;
                document.querySelector('.create-count').textContent = data.actions_count.create;
            })
            .catch(error => {
                console.error('Error actualizando estadísticas:', error);
            });
    };

    // Función para buscar en la tabla
    window.searchAuditTable = function() {
        const searchTerm = document.getElementById('audit-search').value.toLowerCase();
        const tableRows = document.querySelectorAll('.audit-table tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // Función para ordenar tabla
    window.sortAuditTable = function(column) {
        const table = document.querySelector('.audit-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.cells[column].textContent.trim();
            const bValue = b.cells[column].textContent.trim();
            
            // Intentar ordenar como fecha si es la columna de fecha
            if (column === 0) {
                const aDate = new Date(aValue.split(' ')[0].split('/').reverse().join('-'));
                const bDate = new Date(bValue.split(' ')[0].split('/').reverse().join('-'));
                return bDate - aDate; // Orden descendente por defecto
            }
            
            return aValue.localeCompare(bValue);
        });
        
        // Limpiar tabla
        rows.forEach(row => tbody.removeChild(row));
        
        // Reinsertar filas ordenadas
        rows.forEach(row => tbody.appendChild(row));
    };

    // Event listeners para funcionalidades adicionales
    document.addEventListener('click', function(e) {
        // Botón de limpiar filtros
        if (e.target.matches('.clear-filters-btn')) {
            e.preventDefault();
            clearAuditFilters();
        }
        
        // Botón de exportar
        if (e.target.matches('.export-btn')) {
            e.preventDefault();
            exportAuditData();
        }
        
        // Botón de actualizar estadísticas
        if (e.target.matches('.refresh-stats-btn')) {
            e.preventDefault();
            refreshAuditStats();
        }
    });

    // Búsqueda en tiempo real
    const searchInput = document.getElementById('audit-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchAuditTable();
        });
    }

    // Ordenamiento de columnas
    const sortableHeaders = document.querySelectorAll('.audit-table th[data-sortable]');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const columnIndex = Array.from(this.parentElement.children).indexOf(this);
            sortAuditTable(columnIndex);
        });
    });

    // Función para mostrar/ocultar detalles de cambios
    window.toggleAuditDetails = function(logId) {
        const detailsRow = document.querySelector(`#details-${logId}`);
        if (detailsRow) {
            detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
        }
    };

    // Función para resaltar cambios importantes
    window.highlightImportantChanges = function() {
        const changeCells = document.querySelectorAll('.audit-table td[data-changes]');
        changeCells.forEach(cell => {
            const changes = JSON.parse(cell.getAttribute('data-changes'));
            if (changes.length > 3) {
                cell.classList.add('text-warning');
                cell.setAttribute('data-tooltip', `${changes.length} cambios realizados`);
            }
        });
    };

    // Inicializar resaltado de cambios
    highlightImportantChanges();

    // Función para mostrar estadísticas en tiempo real
    window.showRealTimeStats = function() {
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach(card => {
            const valueElement = card.querySelector('.metric-value');
            const currentValue = parseInt(valueElement.textContent.replace(/,/g, ''));
            
            // Animación de contador
            let current = 0;
            const increment = currentValue / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= currentValue) {
                    current = currentValue;
                    clearInterval(timer);
                }
                valueElement.textContent = current.toLocaleString();
            }, 20);
        });
    };

    // Mostrar estadísticas animadas al cargar
    setTimeout(showRealTimeStats, 500);

    // Función para filtrar por rango de fechas
    window.filterByDateRange = function() {
        const startDate = document.getElementById('fecha_inicio').value;
        const endDate = document.getElementById('fecha_fin').value;
        
        if (startDate && endDate) {
            const form = document.querySelector('.audit-filters form');
            form.submit();
        }
    };

    // Event listeners para filtros de fecha
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            const startDate = document.getElementById('fecha_inicio').value;
            const endDate = document.getElementById('fecha_fin').value;
            
            if (startDate && endDate) {
                filterByDateRange();
            }
        });
    });

    // Función para mostrar información de usuario
    window.showUserInfo = function(userId) {
        // Aquí podrías hacer una petición AJAX para obtener más información del usuario
        console.log('Mostrando información del usuario:', userId);
    };

    // Función para generar reportes personalizados
    window.generateCustomReport = function() {
        const selectedFilters = {
            user_id: document.querySelector('select[name="user_id"]').value,
            action: document.querySelector('select[name="action"]').value,
            model_type: document.querySelector('select[name="model_type"]').value,
            fecha_inicio: document.querySelector('input[name="fecha_inicio"]').value,
            fecha_fin: document.querySelector('input[name="fecha_fin"]').value
        };
        
        // Filtrar valores vacíos
        const filteredFilters = Object.fromEntries(
            Object.entries(selectedFilters).filter(([key, value]) => value !== '')
        );
        
        // Generar URL con filtros
        const params = new URLSearchParams(filteredFilters);
        const exportUrl = `/auditorias/export?${params.toString()}`;
        
        // Descargar reporte
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = `reporte_auditoria_${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    // Función para mostrar gráficos interactivos
    window.showInteractiveCharts = function() {
        // Aquí podrías implementar gráficos interactivos con Chart.js o similar
        console.log('Mostrando gráficos interactivos');
    };

    // Inicializar funcionalidades adicionales
    console.log('Auditoría JavaScript cargado correctamente');
}); 