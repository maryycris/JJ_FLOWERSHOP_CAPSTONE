@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- Header with Tabs and Workflow -->
                    <div class="p-3" style="border-bottom:1px solid #e6f0e6;">
                        <!-- Tabs -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="nav nav-tabs border-0" id="orderTabs" role="tablist">
                                <button class="nav-link active bg-light" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab" aria-controls="new" aria-selected="true">
                                    New
                                </button>
                                <button class="nav-link" id="quotations-tab" data-bs-toggle="tab" data-bs-target="#quotations" type="button" role="tab" aria-controls="quotations" aria-selected="false">
                                    Quotations
                                </button>
                            </div>
                            
                            <!-- Workflow Indicator -->
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-secondary text-white px-2 py-1">Quotation</div>
                                    <i class="bi bi-chevron-right mx-1"></i>
                                    <div class="badge bg-secondary text-white px-2 py-1">Quotation Sent</div>
                                    <i class="bi bi-chevron-right mx-1"></i>
                                    <div class="badge bg-success text-white px-2 py-1">Sales Order</div>
                                </div>
                                
                                <!-- Delivery Icon -->
                                <div class="d-flex flex-column align-items-center ms-3">
                                    <i class="bi bi-truck text-dark" style="font-size: 24px;"></i>
                                    <small class="text-muted">Deliver</small>
                                    <span class="badge bg-primary">1</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-success" id="createInvoiceBtn">
                                <i class="bi bi-file-earmark-text me-1"></i>Create Invoice
                            </button>
                            <button class="btn btn-outline-secondary" id="sendEmailBtn">
                                <i class="bi bi-envelope me-1"></i>Send by Email
                            </button>
                            <button class="btn btn-outline-secondary" id="cancelBtn">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content px-3 pt-2 pb-3" id="orderTabsContent">
                        <!-- New Tab -->
                        <div class="tab-pane fade show active" id="new" role="tabpanel" aria-labelledby="new-tab">
                            <!-- Customer and Order Details Grid -->
                            <div class="row g-0 border mb-3" style="border-color:#d9ecd9 !important;">
                                <div class="col-md-2" style="background:#e6f5e6;">
                                    <div class="p-3 fw-semibold" style="border-bottom:1px solid #d9ecd9;">Customer</div>
                                    <div class="p-3 small">
                                        <div class="fw-semibold mb-1">{{ $order->user->name ?? 'Walk-in Customer' }}</div>
                                        @if($order->delivery)
                                            <div>{{ $order->delivery->delivery_address }}</div>
                                            <div>{{ $order->delivery->recipient_phone }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3" style="background:#e6f5e6;">
                                    <div class="p-3 fw-semibold" style="border-bottom:1px solid #d9ecd9;">Invoice Address</div>
                                    <div class="p-3">
                                        <input type="text" class="form-control form-control-sm" id="invoiceAddress" placeholder="Enter invoice address">
                                    </div>
                                </div>
                                <div class="col-md-3" style="background:#e6f5e6;">
                                    <div class="p-3 fw-semibold" style="border-bottom:1px solid #d9ecd9;">Delivery Address</div>
                                    <div class="p-3">
                                        <input type="text" class="form-control form-control-sm" id="deliveryAddress" value="{{ $order->delivery->delivery_address ?? '' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="background:#e6f5e6;">
                                    <div class="p-3 fw-semibold" style="border-bottom:1px solid #d9ecd9;">Order Date</div>
                                    <div class="p-3">
                                        <input type="date" class="form-control form-control-sm" id="orderDate" value="{{ $order->created_at->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-2" style="background:#e6f5e6;">
                                    <div class="p-3 fw-semibold" style="border-bottom:1px solid #d9ecd9;">Price list</div>
                                    <div class="p-3">
                                        <select class="form-select form-select-sm" id="priceList">
                                            <option value="standard">Standard</option>
                                            <option value="wholesale">Wholesale</option>
                                            <option value="retail">Retail</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Line Section -->
                            <div class="mt-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Order Line</div>
                                    <button class="btn btn-success btn-sm" id="addOrderBtn">
                                        <i class="bi bi-plus-circle me-1"></i>Add Order
                                    </button>
                                </div>
                                <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                    <table class="table mb-0" id="orderLineTable">
                                        <thead style="background:#e6f5e6;">
                                            <tr>
                                                <th style="width:20%">Product</th>
                                                <th style="width:25%">Description</th>
                                                <th style="width:10%">Quantity</th>
                                                <th style="width:10%">Delivered</th>
                                                <th style="width:10%">UoM</th>
                                                <th style="width:15%">Unit Price</th>
                                                <th style="width:10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderLineBody">
                                            @foreach($order->products as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->description }}</td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm quantity-input" value="{{ $product->pivot->quantity }}" min="1">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm delivered-input" value="{{ $product->pivot->quantity }}" min="0">
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm">
                                                            <option value="pcs">PCS</option>
                                                            <option value="dozen">Dozen</option>
                                                            <option value="box">Box</option>
                                                        </select>
                                                    </td>
                                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="removeOrderLine(this)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-2 fw-semibold">Total: ₱<span id="totalPrice">{{ number_format($order->total_price, 2) }}</span></div>
                            </div>
                        </div>
                        
                        <!-- Quotations Tab -->
                        <div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="quotations-tab">
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No quotations available</h5>
                                <p class="text-muted">Quotations will appear here when created</p>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeTabs();
    initializeEventListeners();
    initializeFormValidation();
    loadProductOptions();
    initializeTooltips();
});

function initializeTabs() {
    // Initialize Bootstrap tabs
    const triggerTabList = [].slice.call(document.querySelectorAll('#orderTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
}

function initializeEventListeners() {
    // Add Order functionality
    document.getElementById('addOrderBtn').addEventListener('click', function() {
        addNewOrderLine();
    });

    // Create Invoice functionality
    document.getElementById('createInvoiceBtn').addEventListener('click', function() {
        createInvoice();
    });

    // Send Email functionality
    document.getElementById('sendEmailBtn').addEventListener('click', function() {
        sendInvoiceByEmail();
    });

    // Cancel functionality
    document.getElementById('cancelBtn').addEventListener('click', function() {
        cancelOrder();
    });

    // Calculate total when quantities change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('delivered-input') || e.target.classList.contains('unit-price-input')) {
            calculateTotal();
        }
    });

    // Auto-save functionality
    document.addEventListener('input', function(e) {
        if (e.target.matches('input, select, textarea')) {
            autoSaveForm();
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S to save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            createInvoice();
        }
        // Ctrl+N to add new order line
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            addNewOrderLine();
        }
        // Escape to cancel
        if (e.key === 'Escape') {
            cancelOrder();
        }
    });
}

function initializeFormValidation() {
    // Real-time validation for all inputs
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

function initializeTooltips() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
}

function loadProductOptions() {
    // Load products from server (simulated)
    const productSelects = document.querySelectorAll('.product-select');
    productSelects.forEach(select => {
        if (select.children.length <= 1) {
            // Add more product options
            const products = [
                { value: 'winter', text: 'Winter' },
                { value: 'spring', text: 'Spring Bouquet' },
                { value: 'summer', text: 'Summer Collection' },
                { value: 'autumn', text: 'Autumn Mix' },
                { value: 'roses', text: 'Red Roses' },
                { value: 'tulips', text: 'Tulips' },
                { value: 'lilies', text: 'Lilies' },
                { value: 'orchids', text: 'Orchids' }
            ];
            
            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.value;
                option.textContent = product.text;
                select.appendChild(option);
            });
        }
    });
}

function addNewOrderLine() {
    const tbody = document.getElementById('orderLineBody');
    const newRow = document.createElement('tr');
    const rowId = 'row_' + Date.now();
    newRow.id = rowId;
    newRow.innerHTML = `
        <td>
            <select class="form-select form-select-sm product-select" onchange="onProductChange(this)" data-row-id="${rowId}">
                <option value="">Select Product</option>
                <option value="winter">Winter</option>
                <option value="spring">Spring Bouquet</option>
                <option value="summer">Summer Collection</option>
                <option value="autumn">Autumn Mix</option>
                <option value="roses">Red Roses</option>
                <option value="tulips">Tulips</option>
                <option value="lilies">Lilies</option>
                <option value="orchids">Orchids</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm description-input" placeholder="Enter description" data-row-id="${rowId}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm quantity-input" value="1" min="1" onchange="validateQuantity(this)" data-row-id="${rowId}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm delivered-input" value="0" min="0" onchange="validateDelivered(this)" data-row-id="${rowId}">
        </td>
        <td>
            <select class="form-select form-select-sm uom-select" data-row-id="${rowId}">
                <option value="pcs">PCS</option>
                <option value="dozen">Dozen</option>
                <option value="box">Box</option>
                <option value="kg">KG</option>
                <option value="g">G</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm unit-price-input" value="0" min="0" step="0.01" onchange="calculateRowTotal(this)" data-row-id="${rowId}">
        </td>
        <td>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-success" onclick="duplicateOrderLine('${rowId}')" title="Duplicate">
                    <i class="bi bi-files"></i>
                </button>
                <button type="button" class="btn btn-outline-warning" onclick="editOrderLine('${rowId}')" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="removeOrderLine(this)" title="Remove">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    `;
    tbody.appendChild(newRow);
    
    // Add row animation
    newRow.style.opacity = '0';
    newRow.style.transform = 'translateY(-10px)';
    setTimeout(() => {
        newRow.style.transition = 'all 0.3s ease';
        newRow.style.opacity = '1';
        newRow.style.transform = 'translateY(0)';
    }, 10);
    
    // Focus on product select
    newRow.querySelector('.product-select').focus();
    
    // Show success message
    showToast('New order line added', 'success');
}

function removeOrderLine(button) {
    const row = button.closest('tr');
    
    // Add removal animation
    row.style.transition = 'all 0.3s ease';
    row.style.opacity = '0';
    row.style.transform = 'translateX(-100%)';
    
    setTimeout(() => {
        row.remove();
        calculateTotal();
        showToast('Order line removed', 'info');
    }, 300);
}

function duplicateOrderLine(rowId) {
    const originalRow = document.getElementById(rowId);
    if (!originalRow) return;
    
    const tbody = document.getElementById('orderLineBody');
    const newRow = originalRow.cloneNode(true);
    const newRowId = 'row_' + Date.now();
    
    newRow.id = newRowId;
    
    // Update all data attributes
    newRow.querySelectorAll('[data-row-id]').forEach(el => {
        el.setAttribute('data-row-id', newRowId);
    });
    
    // Clear values
    newRow.querySelectorAll('input').forEach(input => {
        if (input.type === 'number') {
            input.value = input.value || '0';
        } else {
            input.value = '';
        }
    });
    
    // Insert after original row
    originalRow.parentNode.insertBefore(newRow, originalRow.nextSibling);
    
    showToast('Order line duplicated', 'success');
}

function editOrderLine(rowId) {
    const row = document.getElementById(rowId);
    if (!row) return;
    
    const product = row.querySelector('.product-select').value;
    const description = row.querySelector('.description-input').value;
    const quantity = row.querySelector('.quantity-input').value;
    const delivered = row.querySelector('.delivered-input').value;
    const uom = row.querySelector('.uom-select').value;
    const unitPrice = row.querySelector('.unit-price-input').value;
    
    Swal.fire({
        title: 'Edit Order Line',
        html: `
            <div class="mb-3">
                <label class="form-label">Product</label>
                <select class="form-select" id="editProduct">
                    <option value="winter" ${product === 'winter' ? 'selected' : ''}>Winter</option>
                    <option value="spring" ${product === 'spring' ? 'selected' : ''}>Spring Bouquet</option>
                    <option value="summer" ${product === 'summer' ? 'selected' : ''}>Summer Collection</option>
                    <option value="autumn" ${product === 'autumn' ? 'selected' : ''}>Autumn Mix</option>
                    <option value="roses" ${product === 'roses' ? 'selected' : ''}>Red Roses</option>
                    <option value="tulips" ${product === 'tulips' ? 'selected' : ''}>Tulips</option>
                    <option value="lilies" ${product === 'lilies' ? 'selected' : ''}>Lilies</option>
                    <option value="orchids" ${product === 'orchids' ? 'selected' : ''}>Orchids</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" class="form-control" id="editDescription" value="${description}">
            </div>
            <div class="row">
                <div class="col-6">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="editQuantity" value="${quantity}" min="1">
                </div>
                <div class="col-6">
                    <label class="form-label">Delivered</label>
                    <input type="number" class="form-control" id="editDelivered" value="${delivered}" min="0">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-6">
                    <label class="form-label">UoM</label>
                    <select class="form-select" id="editUom">
                        <option value="pcs" ${uom === 'pcs' ? 'selected' : ''}>PCS</option>
                        <option value="dozen" ${uom === 'dozen' ? 'selected' : ''}>Dozen</option>
                        <option value="box" ${uom === 'box' ? 'selected' : ''}>Box</option>
                        <option value="kg" ${uom === 'kg' ? 'selected' : ''}>KG</option>
                        <option value="g" ${uom === 'g' ? 'selected' : ''}>G</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Unit Price</label>
                    <input type="number" class="form-control" id="editUnitPrice" value="${unitPrice}" min="0" step="0.01">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const newProduct = document.getElementById('editProduct').value;
            const newDescription = document.getElementById('editDescription').value;
            const newQuantity = document.getElementById('editQuantity').value;
            const newDelivered = document.getElementById('editDelivered').value;
            const newUom = document.getElementById('editUom').value;
            const newUnitPrice = document.getElementById('editUnitPrice').value;
            
            if (!newProduct || !newQuantity || !newUnitPrice) {
                Swal.showValidationMessage('Please fill in all required fields');
            }
            
            return { newProduct, newDescription, newQuantity, newDelivered, newUom, newUnitPrice };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Update the row with new values
            row.querySelector('.product-select').value = result.value.newProduct;
            row.querySelector('.description-input').value = result.value.newDescription;
            row.querySelector('.quantity-input').value = result.value.newQuantity;
            row.querySelector('.delivered-input').value = result.value.newDelivered;
            row.querySelector('.uom-select').value = result.value.newUom;
            row.querySelector('.unit-price-input').value = result.value.newUnitPrice;
            
            calculateTotal();
            showToast('Order line updated', 'success');
        }
    });
}

function onProductChange(select) {
    const rowId = select.getAttribute('data-row-id');
    const row = document.getElementById(rowId);
    const descriptionInput = row.querySelector('.description-input');
    const unitPriceInput = row.querySelector('.unit-price-input');
    
    // Auto-fill description and price based on product
    const productData = {
        'winter': { description: 'Winter seasonal bouquet', price: 25.00 },
        'spring': { description: 'Spring fresh flowers', price: 30.00 },
        'summer': { description: 'Summer bright collection', price: 35.00 },
        'autumn': { description: 'Autumn warm tones', price: 28.00 },
        'roses': { description: 'Premium red roses', price: 50.00 },
        'tulips': { description: 'Colorful tulips', price: 20.00 },
        'lilies': { description: 'Elegant lilies', price: 40.00 },
        'orchids': { description: 'Exotic orchids', price: 60.00 }
    };
    
    const selectedProduct = select.value;
    if (productData[selectedProduct]) {
        descriptionInput.value = productData[selectedProduct].description;
        unitPriceInput.value = productData[selectedProduct].price;
        calculateRowTotal(unitPriceInput);
    }
}

function validateQuantity(input) {
    const value = parseInt(input.value);
    if (value < 1) {
        input.value = 1;
        showToast('Quantity must be at least 1', 'warning');
    }
    calculateRowTotal(input);
}

function validateDelivered(input) {
    const row = input.closest('tr');
    const quantityInput = row.querySelector('.quantity-input');
    const quantity = parseInt(quantityInput.value) || 0;
    const delivered = parseInt(input.value) || 0;
    
    if (delivered > quantity) {
        input.value = quantity;
        showToast('Delivered cannot exceed quantity', 'warning');
    }
}

function calculateRowTotal(input) {
    const row = input.closest('tr');
    const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
    const total = quantity * unitPrice;
    
    // Add total display to row if it doesn't exist
    let totalCell = row.querySelector('.row-total');
    if (!totalCell) {
        const actionsCell = row.querySelector('td:last-child');
        const totalCellHtml = `<td class="row-total text-end fw-bold">₱${total.toFixed(2)}</td>`;
        actionsCell.insertAdjacentHTML('beforebegin', totalCellHtml);
    } else {
        totalCell.textContent = `₱${total.toFixed(2)}`;
    }
    
    calculateTotal();
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.getAttribute('name') || field.getAttribute('id') || 'field';
    
    // Remove existing validation classes
    field.classList.remove('is-valid', 'is-invalid');
    
    if (field.hasAttribute('required') && !value) {
        field.classList.add('is-invalid');
        showFieldError(field, `${fieldName} is required`);
        return false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        field.classList.add('is-invalid');
        showFieldError(field, 'Please enter a valid email address');
        return false;
    }
    
    if (field.type === 'number' && value) {
        const numValue = parseFloat(value);
        const min = parseFloat(field.getAttribute('min'));
        const max = parseFloat(field.getAttribute('max'));
        
        if (min !== null && numValue < min) {
            field.classList.add('is-invalid');
            showFieldError(field, `Value must be at least ${min}`);
            return false;
        }
        
        if (max !== null && numValue > max) {
            field.classList.add('is-invalid');
            showFieldError(field, `Value must be at most ${max}`);
            return false;
        }
    }
    
    field.classList.add('is-valid');
    clearFieldError(field);
    return true;
}

function showFieldError(field, message) {
    clearFieldError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    const existingError = field.parentNode.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function autoSaveForm() {
    // Debounce auto-save to avoid too many requests
    clearTimeout(window.autoSaveTimeout);
    window.autoSaveTimeout = setTimeout(() => {
        const formData = collectFormData();
        localStorage.setItem('invoice_draft', JSON.stringify(formData));
        showToast('Draft saved', 'info');
    }, 2000);
}

function collectFormData() {
    return {
        invoiceAddress: document.getElementById('invoiceAddress').value,
        deliveryAddress: document.getElementById('deliveryAddress').value,
        orderDate: document.getElementById('orderDate').value,
        priceList: document.getElementById('priceList').value,
        orderLines: Array.from(document.querySelectorAll('#orderLineBody tr')).map(row => ({
            product: row.querySelector('.product-select')?.value,
            description: row.querySelector('.description-input')?.value,
            quantity: row.querySelector('.quantity-input')?.value,
            delivered: row.querySelector('.delivered-input')?.value,
            uom: row.querySelector('.uom-select')?.value,
            unitPrice: row.querySelector('.unit-price-input')?.value
        }))
    };
}

function loadDraft() {
    const draft = localStorage.getItem('invoice_draft');
    if (draft) {
        try {
            const data = JSON.parse(draft);
            document.getElementById('invoiceAddress').value = data.invoiceAddress || '';
            document.getElementById('deliveryAddress').value = data.deliveryAddress || '';
            document.getElementById('orderDate').value = data.orderDate || '';
            document.getElementById('priceList').value = data.priceList || 'standard';
            
            // Load order lines
            if (data.orderLines && data.orderLines.length > 0) {
                // Clear existing rows
                document.getElementById('orderLineBody').innerHTML = '';
                
                data.orderLines.forEach(line => {
                    addNewOrderLine();
                    const lastRow = document.querySelector('#orderLineBody tr:last-child');
                    if (lastRow) {
                        lastRow.querySelector('.product-select').value = line.product || '';
                        lastRow.querySelector('.description-input').value = line.description || '';
                        lastRow.querySelector('.quantity-input').value = line.quantity || '1';
                        lastRow.querySelector('.delivered-input').value = line.delivered || '0';
                        lastRow.querySelector('.uom-select').value = line.uom || 'pcs';
                        lastRow.querySelector('.unit-price-input').value = line.unitPrice || '0';
                    }
                });
            }
            
            calculateTotal();
            showToast('Draft loaded', 'success');
        } catch (e) {
            console.error('Error loading draft:', e);
        }
    }
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to page
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function calculateTotal() {
    const rows = document.querySelectorAll('#orderLineBody tr');
    let total = 0;
    let itemCount = 0;
    
    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input')?.value || 0);
        const unitPrice = parseFloat(row.querySelector('.unit-price-input')?.value || 0);
        const rowTotal = quantity * unitPrice;
        total += rowTotal;
        
        if (quantity > 0 && unitPrice > 0) {
            itemCount++;
        }
    });
    
    document.getElementById('totalPrice').textContent = total.toFixed(2);
    
    // Update item count in header
    const itemCountElement = document.querySelector('.delivery-count');
    if (itemCountElement) {
        itemCountElement.textContent = itemCount;
    }
    
    // Update total display with formatting
    const totalElement = document.getElementById('totalPrice');
    if (total > 0) {
        totalElement.innerHTML = `<span class="text-success fw-bold">₱${total.toFixed(2)}</span>`;
    } else {
        totalElement.innerHTML = '<span class="text-muted">₱0.00</span>';
    }
}

function createInvoice() {
    // Validate form before creating invoice
    if (!validateForm()) {
        showToast('Please fix validation errors before creating invoice', 'error');
        return;
    }
    
    // Show loading state
    const btn = document.getElementById('createInvoiceBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating Invoice...';

    // Collect form data
    const formData = collectFormData();
    
    // Show confirmation dialog
    Swal.fire({
        title: 'Create Invoice?',
        html: `
            <div class="text-start">
                <p><strong>Invoice Address:</strong> ${formData.invoiceAddress || 'Not specified'}</p>
                <p><strong>Delivery Address:</strong> ${formData.deliveryAddress || 'Not specified'}</p>
                <p><strong>Order Date:</strong> ${formData.orderDate || 'Not specified'}</p>
                <p><strong>Price List:</strong> ${formData.priceList}</p>
                <p><strong>Total Items:</strong> ${formData.orderLines.length}</p>
                <p><strong>Total Amount:</strong> ₱${calculateTotalAmount()}</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Yes, Create Invoice',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            // Simulate API call
            setTimeout(() => {
                // Clear draft
                localStorage.removeItem('invoice_draft');
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Invoice Created!',
                    text: 'The invoice has been created successfully.',
                    timer: 2000
                }).then(() => {
                    // Redirect to validate page
                    window.location.href = '{{ route("admin.orders.walkin.validate", $order->id) }}';
                });
            }, 1500);
        } else {
            // Reset button if cancelled
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}

function sendInvoiceByEmail() {
    // Validate form before sending email
    if (!validateForm()) {
        showToast('Please fix validation errors before sending email', 'error');
        return;
    }
    
    const formData = collectFormData();
    
    Swal.fire({
        title: 'Send Invoice by Email',
        html: `
            <div class="mb-3">
                <label class="form-label">Recipient Email</label>
                <input type="email" class="form-control" id="recipientEmail" placeholder="customer@example.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" class="form-control" id="emailSubject" value="Invoice #{{ sprintf('%05d', $order->id) }} - J'J Flower Shop" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea class="form-control" id="emailMessage" rows="3" placeholder="Optional message...">Dear Customer,

Please find attached your invoice for order #{{ sprintf('%05d', $order->id) }}.

Thank you for choosing J'J Flower Shop!

Best regards,
J'J Flower Shop Team</textarea>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="includePdf" checked>
                    <label class="form-check-label" for="includePdf">
                        Include PDF attachment
                    </label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send Email',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const email = document.getElementById('recipientEmail').value;
            const subject = document.getElementById('emailSubject').value;
            const message = document.getElementById('emailMessage').value;
            const includePdf = document.getElementById('includePdf').checked;
            
            if (!email) {
                Swal.showValidationMessage('Please enter an email address');
            }
            if (!subject) {
                Swal.showValidationMessage('Please enter a subject');
            }
            
            return { email, subject, message, includePdf };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Sending Email...',
                text: 'Please wait while we send the invoice.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Simulate API call
            setTimeout(() => {
                console.log('Sending invoice to:', result.value);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: `Invoice has been sent to ${result.value.email}`,
                    timer: 3000
                });
            }, 2000);
        }
    });
}

function cancelOrder() {
    Swal.fire({
        title: 'Cancel Order',
        html: `
            <div class="text-start">
                <p>Are you sure you want to cancel this order?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All order data will be lost.
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason for cancellation (optional)</label>
                    <select class="form-select" id="cancelReason">
                        <option value="">Select reason...</option>
                        <option value="customer_request">Customer Request</option>
                        <option value="out_of_stock">Out of Stock</option>
                        <option value="delivery_issue">Delivery Issue</option>
                        <option value="payment_issue">Payment Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3" id="otherReasonDiv" style="display: none;">
                    <label class="form-label">Please specify</label>
                    <textarea class="form-control" id="otherReason" rows="2" placeholder="Enter reason..."></textarea>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel Order',
        cancelButtonText: 'No, Keep Order',
        confirmButtonColor: '#dc3545',
        preConfirm: () => {
            const reason = document.getElementById('cancelReason').value;
            const otherReason = document.getElementById('otherReason').value;
            
            if (reason === 'other' && !otherReason.trim()) {
                Swal.showValidationMessage('Please specify the reason for cancellation');
            }
            
            return { reason, otherReason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Cancelling Order...',
                text: 'Please wait while we cancel the order.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Simulate API call
            setTimeout(() => {
                console.log('Cancelling order with reason:', result.value);
                
                // Clear draft
                localStorage.removeItem('invoice_draft');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Order Cancelled',
                    text: 'The order has been cancelled successfully.',
                    timer: 2000
                }).then(() => {
                    window.location.href = '{{ route("admin.orders.index", ["type" => "walkin"]) }}';
                });
            }, 1500);
        }
    });
    
    // Show/hide other reason field
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'cancelReason') {
            const otherReasonDiv = document.getElementById('otherReasonDiv');
            if (e.target.value === 'other') {
                otherReasonDiv.style.display = 'block';
            } else {
                otherReasonDiv.style.display = 'none';
            }
        }
    });
}

// Additional utility functions
function validateForm() {
    let isValid = true;
    const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    // Check if at least one order line exists
    const orderLines = document.querySelectorAll('#orderLineBody tr');
    if (orderLines.length === 0) {
        showToast('Please add at least one order line', 'warning');
        isValid = false;
    }
    
    // Check if all order lines have valid data
    orderLines.forEach(row => {
        const product = row.querySelector('.product-select')?.value;
        const quantity = row.querySelector('.quantity-input')?.value;
        const unitPrice = row.querySelector('.unit-price-input')?.value;
        
        if (!product || !quantity || !unitPrice) {
            showToast('Please fill in all order line details', 'warning');
            isValid = false;
        }
    });
    
    return isValid;
}

function calculateTotalAmount() {
    const rows = document.querySelectorAll('#orderLineBody tr');
    let total = 0;
    
    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input')?.value || 0);
        const unitPrice = parseFloat(row.querySelector('.unit-price-input')?.value || 0);
        total += quantity * unitPrice;
    });
    
    return total.toFixed(2);
}

function exportToPDF() {
    Swal.fire({
        title: 'Export to PDF',
        text: 'Generating PDF...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: 'PDF Generated!',
            text: 'Your invoice PDF has been generated successfully.',
            timer: 2000
        });
    }, 2000);
}

function printInvoice() {
    window.print();
}

function saveAsDraft() {
    const formData = collectFormData();
    localStorage.setItem('invoice_draft', JSON.stringify(formData));
    showToast('Draft saved successfully', 'success');
}

function loadDraftData() {
    const draft = localStorage.getItem('invoice_draft');
    if (draft) {
        Swal.fire({
            title: 'Load Draft?',
            text: 'A saved draft was found. Do you want to load it?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Load Draft',
            cancelButtonText: 'No, Start Fresh'
        }).then((result) => {
            if (result.isConfirmed) {
                loadDraft();
            }
        });
    }
}

// Initialize draft loading on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDraftData();
});
</script>
@endsection
