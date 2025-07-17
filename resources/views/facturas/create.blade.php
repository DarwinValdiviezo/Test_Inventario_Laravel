@extends('layouts.app')

@section('title', 'Nueva Factura')
@section('page-title', 'Nueva Factura')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('facturas.index') }}"><i class="bx bx-receipt"></i> Facturas</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="bx bx-plus-circle"></i> Nueva Factura</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="bx bx-receipt me-2"></i> Crear Nueva Factura</h3>
                    <a href="{{ route('facturas.index') }}" class="btn btn-outline-light btn-sm"><i class="bx bx-arrow-back me-1"></i> Volver</a>
                </div>
                <form method="POST" action="{{ route('facturas.store') }}" autocomplete="off" id="facturaForm">
                    @csrf
                    <div class="card-body">
                        <!-- Card Cliente -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center gap-2">
                                <i class="bx bx-user text-primary"></i>
                                <span class="fw-bold">Cliente <span class="text-danger">*</span></span>
                                <span class="ms-auto" data-bs-toggle="tooltip" title="Solo clientes activos con stock disponible"><i class="bx bx-info-circle text-info"></i></span>
                            </div>
                            <div class="card-body">
                                <select name="cliente_id" id="cliente_id" class="form-select form-select-lg select2 @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">Seleccione un cliente</option>
                                    @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} - {{ $cliente->email }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback d-block mt-2">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <!-- Card Productos -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center gap-2 justify-content-between">
                                <div><i class="bx bx-box text-primary"></i> <span class="fw-bold">Productos</span></div>
                                <button type="button" class="btn btn-success btn-sm" id="agregarProducto"><i class="bx bx-plus-circle me-1"></i> Agregar Producto</button>
                            </div>
                            <div class="card-body">
                                <div id="productosContainer"></div>
                                @error('productos')
                                    <div class="alert alert-danger mt-2"><i class="bx bx-error-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Card Forma de Pago -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center gap-2">
                                <i class="bx bx-credit-card text-primary"></i>
                                <span class="fw-bold">Forma de Pago</span>
                                <span class="ms-auto" data-bs-toggle="tooltip" title="La forma de pago se incluirá en la factura electrónica"><i class="bx bx-info-circle text-info"></i></span>
                            </div>
                            <div class="card-body">
                                <select name="forma_pago" id="forma_pago" class="form-select form-select-lg select2">
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TARJETA">Tarjeta de Crédito/Débito</option>
                                    <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                                    <option value="CHEQUE">Cheque</option>
                                    <option value="OTROS">Otros</option>
                                </select>
                            </div>
                        </div>
                        <!-- Card Resumen -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center gap-2">
                                <i class="bx bx-calculator text-primary"></i>
                                <span class="fw-bold">Resumen</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3 col-6">
                                        <div class="bg-white rounded shadow-sm p-3 text-center">
                                            <span class="text-muted small">Total de Productos</span>
                                            <div class="fw-bold fs-4" id="totalProductos">0</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="bg-white rounded shadow-sm p-3 text-center">
                                            <span class="text-muted small">Subtotal</span>
                                            <div class="fw-bold fs-5" id="subtotal">$0.00</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="bg-white rounded shadow-sm p-3 text-center">
                                            <span class="text-muted small">IVA (15%)</span>
                                            <div class="fw-bold fs-5 text-primary" id="iva">$0.00</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="bg-white rounded shadow-sm p-3 text-center">
                                            <span class="text-muted small">Total a Pagar</span>
                                            <div class="fw-bold fs-4 text-success" id="totalPagar">$0.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-4 mb-0 d-flex align-items-center gap-2">
                                    <i class="bx bx-error-alt"></i>
                                    <div><strong>Importante:</strong> Al crear la factura, el stock de los productos se actualizará automáticamente y se generarán los datos SRI.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="text-muted small"><i class="bx bx-info-circle me-1"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-info btn-lg" id="btnVistaPrevia" disabled><i class="bx bx-show me-1"></i> Vista Previa</button>
                            <a href="{{ route('facturas.index') }}" class="btn btn-secondary btn-lg"><i class="bx bx-x me-1"></i> Cancelar</a>
                            <button type="submit" class="btn btn-primary btn-lg" id="btnGuardar" disabled><i class="bx bx-check me-1"></i> Crear Factura</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template para producto -->
<template id="productoTemplate">
    <div class="producto-item card mb-3 border-0 shadow-sm" data-index="__INDEX__">
        <div class="card-body row align-items-end g-2">
            <div class="col-md-5">
                <label class="form-label fw-bold">Producto <span class="text-danger">*</span></label>
                <select name="productos[__INDEX__][producto_id]" class="form-select producto-select select2" required>
                    <option value="">Seleccione un producto</option>
                    @foreach($productos as $producto)
                    <option value="{{ $producto->id }}" 
                            data-precio="{{ $producto->precio }}" 
                            data-stock="{{ $producto->stock }}"
                            data-nombre="{{ $producto->nombre }}"
                            data-descripcion="{{ $producto->descripcion }}">
                        {{ $producto->nombre }} - Stock: {{ $producto->stock }} - ${{ number_format($producto->precio, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Cantidad <span class="text-danger">*</span></label>
                <input type="number" name="productos[__INDEX__][cantidad]" class="form-control cantidad-input" min="1" max="9999" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Precio Unit.</label>
                <input type="text" class="form-control precio-unitario" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Subtotal</label>
                <input type="text" class="form-control subtotal" readonly>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm eliminar-producto w-100"><i class="bx bx-trash"></i></button>
            </div>
        </div>
    </div>
</template>

<!-- Modal Vista Previa -->
<div class="modal fade" id="modalVistaPrevia" tabindex="-1" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-show display-6"></i>
                    <h5 class="modal-title mb-0" id="modalVistaPreviaLabel">Vista Previa de Factura</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <div id="vistaPreviaContent" class="p-4"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bx bx-x"></i> Cerrar</button>
                <button type="button" class="btn btn-success" id="btnDescargarPDF"><i class="bx bx-file me-1"></i> Descargar PDF</button>
            </div>
        </div>
    </div>
</div>

<style>
.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.form-select-lg {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.2s ease-in-out;
}

.form-select-lg:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: translateY(-1px);
}

.form-label {
    color: #495057;
    font-size: 0.95rem;
}

.btn-lg {
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s ease-in-out;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.breadcrumb {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem 1rem;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.producto-item {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.2s ease-in-out;
}

.producto-item:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.1);
}

.precio-unitario, .subtotal {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #495057;
}

/* Estilos para la vista previa */
.vista-previa-factura {
    font-family: 'Arial', sans-serif;
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.vista-previa-header {
    text-align: center;
    border-bottom: 3px solid #007bff;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.vista-previa-company {
    font-size: 28px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
}

.vista-previa-subtitle {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}

.vista-previa-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin: 10px 0;
}

.vista-previa-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    gap: 20px;
}

.vista-previa-section {
    flex: 1;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.vista-previa-section h3 {
    font-size: 16px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 15px;
    border-bottom: 1px solid #007bff;
    padding-bottom: 5px;
}

.vista-previa-item {
    margin-bottom: 8px;
    font-size: 12px;
    display: flex;
    justify-content: space-between;
}

.vista-previa-label {
    font-weight: bold;
    color: #555;
    min-width: 120px;
}

.vista-previa-value {
    color: #333;
    text-align: right;
    word-break: break-all;
}

.vista-previa-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    overflow: hidden;
}

.vista-previa-table th {
    background-color: #007bff;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    font-size: 12px;
}

.vista-previa-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 11px;
}

.vista-previa-table tr:nth-child(even) {
    background-color: #f8f9fa;
}

.vista-previa-table tfoot tr {
    background-color: #f8f9fa;
    font-weight: bold;
}

.vista-previa-table tfoot tr:last-child {
    background-color: #28a745;
    color: white;
    font-size: 14px;
}

.vista-previa-total {
    text-align: right;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #007bff;
}

.vista-previa-amount {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

.vista-previa-details {
    margin-top: 30px;
    padding: 15px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.vista-previa-details-header {
    font-size: 16px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 15px;
    border-bottom: 1px solid #007bff;
    padding-bottom: 5px;
    display: flex;
    align-items: center;
}

.vista-previa-details-header i {
    margin-right: 8px;
}

@media (max-width: 768px) {
    .card-footer .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-lg {
        width: 100%;
    }
    
    .producto-item .row {
        margin-bottom: 1rem;
    }
    
    .producto-item .col-md-2,
    .producto-item .col-md-4 {
        margin-bottom: 1rem;
    }
    
    .vista-previa-info {
        flex-direction: column;
    }
    
    .vista-previa-section {
        margin-bottom: 20px;
    }
}
</style>

<script>
// Verificar que QRious esté disponible antes de ejecutar el código
if (typeof QRious === 'undefined') {
    console.error('QRious no está disponible al cargar la página');
    // Intentar cargar QRious dinámicamente
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js';
    script.onload = function() {
        console.log('QRious cargado dinámicamente');
        initFacturaScript();
    };
    script.onerror = function() {
        console.error('Error al cargar QRious dinámicamente');
        alert('Error: No se pudo cargar la librería QRious. Por favor, verifica tu conexión a internet y recarga la página.');
    };
    document.head.appendChild(script);
} else {
    initFacturaScript();
}

function initFacturaScript() {
    console.log('Inicializando script de facturas...');
    console.log('QRious disponible:', typeof QRious !== 'undefined');
    
document.addEventListener('DOMContentLoaded', function() {
    let productoIndex = 0;
    const productosContainer = document.getElementById('productosContainer');
    const template = document.getElementById('productoTemplate');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnVistaPrevia = document.getElementById('btnVistaPrevia');
    
    // Función para agregar producto
    function agregarProducto() {
        const nuevoProducto = template.innerHTML.replace(/__INDEX__/g, productoIndex);
        productosContainer.insertAdjacentHTML('beforeend', nuevoProducto);
        
        const nuevoItem = productosContainer.lastElementChild;
        configurarEventosProducto(nuevoItem);
        productoIndex++;
        actualizarResumen();
        validarFormulario();
    }
    
    // Función para configurar eventos de un producto
    function configurarEventosProducto(item) {
        const select = item.querySelector('.producto-select');
        const cantidadInput = item.querySelector('.cantidad-input');
        const precioUnitario = item.querySelector('.precio-unitario');
        const subtotal = item.querySelector('.subtotal');
        const btnEliminar = item.querySelector('.eliminar-producto');
        
        // Evento para cambio de producto
        select.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (option.value) {
                const precio = parseFloat(option.dataset.precio);
                const stock = parseInt(option.dataset.stock);
                
                precioUnitario.value = '$' + precio.toFixed(2);
                cantidadInput.max = stock;
                cantidadInput.placeholder = 'Máx: ' + stock;
                
                if (cantidadInput.value) {
                    calcularSubtotal();
                }
            } else {
                precioUnitario.value = '';
                subtotal.value = '';
                cantidadInput.max = '';
                cantidadInput.placeholder = '';
            }
            validarFormulario();
        });
        
        // Evento para cambio de cantidad
        cantidadInput.addEventListener('input', function() {
            calcularSubtotal();
            validarFormulario();
        });
        
        // Función para calcular subtotal
        function calcularSubtotal() {
            const cantidad = parseInt(cantidadInput.value) || 0;
            const precio = parseFloat(select.options[select.selectedIndex].dataset.precio) || 0;
            const subtotalValue = cantidad * precio;
            subtotal.value = '$' + subtotalValue.toFixed(2);
            actualizarResumen();
        }
        
        // Evento para eliminar producto
        btnEliminar.addEventListener('click', function() {
            item.remove();
            actualizarResumen();
            validarFormulario();
        });
    }
    
    // Función para actualizar resumen
    function actualizarResumen() {
        const productos = productosContainer.querySelectorAll('.producto-item');
        const totalProductos = productos.length;
        let subtotal = 0;
        
        productos.forEach(producto => {
            const subtotalProducto = producto.querySelector('.subtotal').value;
            if (subtotalProducto) {
                subtotal += parseFloat(subtotalProducto.replace('$', '').replace(',', ''));
            }
        });
        
        // Calcular IVA (15%)
        const iva = subtotal * 0.15;
        const total = subtotal + iva;
        
        document.getElementById('totalProductos').textContent = totalProductos;
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('iva').textContent = '$' + iva.toFixed(2);
        document.getElementById('totalPagar').textContent = '$' + total.toFixed(2);
    }
    
    // Función para validar formulario
    function validarFormulario() {
        const cliente = document.getElementById('cliente_id').value;
        const productos = productosContainer.querySelectorAll('.producto-item');
        let productosValidos = 0;
        
        productos.forEach(producto => {
            const select = producto.querySelector('.producto-select');
            const cantidad = producto.querySelector('.cantidad-input').value;
            
            if (select.value && cantidad && parseInt(cantidad) > 0) {
                productosValidos++;
            }
        });
        
        const formularioValido = cliente && productosValidos > 0;
        btnGuardar.disabled = !formularioValido;
        btnVistaPrevia.disabled = !formularioValido;
    }
    
    // Función para generar vista previa
    function generarVistaPrevia() {
        const clienteSelect = document.getElementById('cliente_id');
        const clienteOption = clienteSelect.options[clienteSelect.selectedIndex];
        const clienteNombre = clienteOption.text.split(' - ')[0];
        const clienteEmail = clienteOption.text.split(' - ')[1];
        
        const formaPago = document.getElementById('forma_pago').value;
        const productos = productosContainer.querySelectorAll('.producto-item');
        let subtotal = 0;
        let productosHTML = '';
        
        productos.forEach((producto, index) => {
            const select = producto.querySelector('.producto-select');
            const cantidad = producto.querySelector('.cantidad-input').value;
            const subtotalProducto = producto.querySelector('.subtotal').value;
            
            if (select.value && cantidad && parseInt(cantidad) > 0) {
                const option = select.options[select.selectedIndex];
                const nombre = option.dataset.nombre;
                const descripcion = option.dataset.descripcion || '';
                const precio = parseFloat(option.dataset.precio);
                const subtotalValue = parseFloat(subtotalProducto.replace('$', ''));
                subtotal += subtotalValue;
                
                productosHTML += `
                    <tr>
                        <td style="text-align: center; font-weight: bold;">${index + 1}</td>
                        <td>
                            <div style="font-weight: bold;">${nombre}</div>
                            ${descripcion ? `<div style="font-size: 12px; color: #666;">${descripcion}</div>` : ''}
                        </td>
                        <td style="text-align: center;">
                            <span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: bold;">${cantidad}</span>
                        </td>
                        <td style="text-align: center; font-weight: bold; color: #28a745;">$${precio.toFixed(2)}</td>
                        <td style="text-align: center; font-weight: bold; color: #007bff;">$${subtotalValue.toFixed(2)}</td>
                    </tr>
                `;
            }
        });
        
        // Calcular IVA y total
        const iva = subtotal * 0.15;
        const total = subtotal + iva;
        
        const fechaActual = new Date().toLocaleDateString('es-ES');
        const horaActual = new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
        
        // Generar número secuencial simulado
        const numeroSecuencial = '001-001-' + String(Date.now()).slice(-9).padStart(9, '0');
        const cua = fechaActual.replace(/\//g, '') + '-1728167857001-' + numeroSecuencial.replace(/-/g, '');
        
        const qrData = JSON.stringify({
            ruc: '1728167857001',
            ambiente: 'PRODUCCION',
            tipoEmision: 'NORMAL',
            numeroSecuencial: numeroSecuencial,
            fechaEmision: fechaActual,
            horaEmision: horaActual,
            formaPago: formaPago,
            subtotal: subtotal.toFixed(2),
            iva: iva.toFixed(2),
            total: total.toFixed(2),
            numeroAutorizacion: '1728167857001-1728167857001-1728167857001',
            codigoControl: '1728167857001-1728167857001-1728167857001'
        });
        // Verificar que QRious esté disponible
        if (typeof QRious === 'undefined') {
            console.error('QRious no está disponible');
            alert('Error: La librería QRious no se cargó correctamente. Por favor, recarga la página.');
            return;
        }
        
        const qr = new QRious({
            value: qrData,
            size: 160,
            background: 'white',
            foreground: '#007bff',
            level: 'H'
        });

        const vistaPreviaHTML = `
<div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; gap: 20px;">
    <div style="flex: 1; min-width: 180px; max-width: 220px; display: flex; flex-direction: column; align-items: center;">
        <h4 style="color: #007bff; font-size: 13px; font-weight: bold; margin-bottom: 10px;">Código QR</h4>
        <div style="background: #fff; border: 2px solid #007bff; border-radius: 8px; padding: 10px; margin-bottom: 5px;">
            <img id="qr-preview-img" src="${qr.toDataURL()}" alt="QR Factura SRI" style="width: 140px; height: 140px; display: block;" />
        </div>
        <span style="font-size: 10px; color: #666;">Escanee para verificar autenticidad</span>
    </div>
    <div style="flex: 2; min-width: 220px; background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px;">
        <div style="margin-bottom: 7px;"><span style="color: #888; font-size: 11px;">Secuencial:</span> <span style="font-weight: bold; font-size: 15px; letter-spacing: 1px;">${numeroSecuencial}</span></div>
        <div style="margin-bottom: 7px;"><span style="color: #888; font-size: 11px;">CUA:</span> <span style="font-weight: bold; font-size: 13px;">${cua}</span></div>
        <div style="margin-bottom: 7px;"><span style="color: #888; font-size: 11px;">Ambiente:</span> <span style="font-weight: bold; color: #007bff;">PRODUCCION</span></div>
        <div style="margin-bottom: 7px;"><span style="color: #888; font-size: 11px;">Estado:</span> <span style="font-weight: bold; color: #28a745;">AUTORIZADO</span></div>
        <div style="margin-bottom: 7px;"><span style="color: #888; font-size: 11px;">Fecha Emisión:</span> <span style="font-weight: bold;">${fechaActual}</span></div>
        <div style="margin-bottom: 7px;"><span style="color: #888; font-size: 11px;">Hora:</span> <span style="font-weight: bold;">${horaActual}</span></div>
        <div><span style="color: #888; font-size: 11px;">Forma de Pago:</span> <span style="font-weight: bold;">${formaPago}</span></div>
    </div>
    <div style="flex: 1.5; min-width: 200px; display: flex; flex-direction: column; align-items: center;">
        <h4 style="color: #007bff; font-size: 13px; font-weight: bold; margin-bottom: 10px;">Firma Digital</h4>
        <div style="width: 100%; background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px;">
            <span style="display: inline-block; background: #28a745; color: #fff; font-size: 12px; font-weight: bold; border-radius: 5px; padding: 2px 10px; margin-bottom: 5px;"><i style="margin-right: 4px;" class="bx bx-shield-check"></i>FIRMA VÁLIDA</span>
            <span style="color: #28a745; font-size: 10px; display: block;">La firma digital es válida</span>
            <div style="font-size: 9px; word-break: break-all; background: #f8f9fa; padding: 6px; border-radius: 3px; border: 1px solid #dee2e6; margin-top: 7px;">
                <strong>Firma Digital:</strong><br>
                Simulación de firma digital para vista previa
            </div>
        </div>
    </div>
</div>
<hr style="border-top: 2px dashed #dee2e6; margin: 20px 0;">
<div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; margin-bottom: 10px;">
    <h6 style="color: #007bff; font-size: 12px; font-weight: bold; margin-bottom: 8px;"><i class="bx bx-qr-code me-2"></i>Contenido del Código QR</h6>
    <div style="overflow-x: auto;">
        <code style="font-size: 10px; word-break: break-all; white-space: pre; color: #b30059;">${qrData}</code>
    </div>
</div>
<div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px; margin: 10px 0 0 0; font-size: 10px; color: #856404; text-align: center;">
    <strong>AVISO:</strong> Esta factura electrónica será generada por el Sistema de Rentas Internas del Ecuador. La firma digital y el código QR garantizarán la autenticidad e integridad del documento. Cualquier modificación invalidará la factura.
</div>
`;
        document.getElementById('vistaPreviaContent').innerHTML = vistaPreviaHTML;
    }
    
    // Función para descargar PDF de vista previa
    function descargarPDFVistaPrevia() {
        const formData = new FormData();
        const clienteId = document.getElementById('cliente_id').value;
        const productos = [];
        
        // Recopilar datos de productos
        productosContainer.querySelectorAll('.producto-item').forEach(producto => {
            const select = producto.querySelector('.producto-select');
            const cantidad = producto.querySelector('.cantidad-input').value;
            
            if (select.value && cantidad && parseInt(cantidad) > 0) {
                productos.push({
                    producto_id: select.value,
                    cantidad: parseInt(cantidad)
                });
            }
        });
        
        formData.append('cliente_id', clienteId);
        formData.append('productos', JSON.stringify(productos));
        
        // Mostrar indicador de carga
        const btnDescargar = document.getElementById('btnDescargarPDF');
        const originalText = btnDescargar.innerHTML;
        btnDescargar.innerHTML = '<i class="bx bx-hourglass-split"></i> Generando PDF...';
        btnDescargar.disabled = true;
        
        // Enviar solicitud
        fetch('{{ route("facturas.preview-pdf") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Error al generar PDF');
        })
        .then(blob => {
            // Crear enlace de descarga
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'factura-preview.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al generar el PDF. Por favor, inténtalo de nuevo.');
        })
        .finally(() => {
            // Restaurar botón
            btnDescargar.innerHTML = originalText;
            btnDescargar.disabled = false;
        });
    }
    
    // Eventos del formulario
    document.getElementById('agregarProducto').addEventListener('click', agregarProducto);
    document.getElementById('cliente_id').addEventListener('change', validarFormulario);
    btnVistaPrevia.addEventListener('click', function() {
        generarVistaPrevia();
        new bootstrap.Modal(document.getElementById('modalVistaPrevia')).show();
    });
    
    // Evento para descargar PDF desde vista previa
    document.getElementById('btnDescargarPDF').addEventListener('click', descargarPDFVistaPrevia);
    
    // Agregar primer producto automáticamente
    agregarProducto();
    
    // Validación del formulario antes de enviar
    document.getElementById('facturaForm').addEventListener('submit', function(e) {
        const productos = productosContainer.querySelectorAll('.producto-item');
        let productosValidos = 0;
        
        productos.forEach(producto => {
            const select = producto.querySelector('.producto-select');
            const cantidad = producto.querySelector('.cantidad-input').value;
            
            if (select.value && cantidad && parseInt(cantidad) > 0) {
                productosValidos++;
            }
        });
        
        if (productosValidos === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto a la factura.');
            return false;
        }
    });
});
}
</script>
@endsection
