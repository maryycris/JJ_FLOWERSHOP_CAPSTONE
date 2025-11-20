<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['currentPage' => 1, 'totalPages' => 1, 'baseUrl' => '', 'queryParams' => []]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['currentPage' => 1, 'totalPages' => 1, 'baseUrl' => '', 'queryParams' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $maxVisiblePages = 5;
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
    
    if ($endPage - $startPage + 1 < $maxVisiblePages) {
        $startPage = max(1, $endPage - $maxVisiblePages + 1);
    }
?>

<?php if($totalPages > 1): ?>
<div class="pagination-container d-flex justify-content-center mt-4 mb-4">
    <nav aria-label="Product pagination">
        <ul class="pagination pagination-custom mb-0">
            <!-- Previous Button -->
            <li class="page-item <?php echo e($currentPage <= 1 ? 'disabled' : ''); ?>">
                <a class="page-link" href="<?php echo e($currentPage > 1 ? $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $currentPage - 1])) : '#'); ?>" 
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            
            <!-- First Page (if not visible) -->
            <?php if($startPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => 1]))); ?>">1</a>
                </li>
                <?php if($startPage > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Page Numbers -->
            <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?php echo e($i == $currentPage ? 'active' : ''); ?>">
                    <a class="page-link" href="<?php echo e($baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $i]))); ?>"><?php echo e($i); ?></a>
                </li>
            <?php endfor; ?>
            
            <!-- Last Page (if not visible) -->
            <?php if($endPage < $totalPages): ?>
                <?php if($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $totalPages]))); ?>"><?php echo e($totalPages); ?></a>
                </li>
            <?php endif; ?>
            
            <!-- Next Button -->
            <li class="page-item <?php echo e($currentPage >= $totalPages ? 'disabled' : ''); ?>">
                <a class="page-link" href="<?php echo e($currentPage < $totalPages ? $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $currentPage + 1])) : '#'); ?>" 
                   aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
.pagination-custom .page-link {
    color: #7bb47b;
    background-color: #fff;
    border: 1px solid #e6f4ea;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination-custom .page-link:hover {
    color: #fff;
    background-color: #7bb47b;
    border-color: #7bb47b;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(123, 180, 123, 0.3);
}

.pagination-custom .page-item.active .page-link {
    color: #fff;
    background-color: #7bb47b;
    border-color: #7bb47b;
    box-shadow: 0 2px 8px rgba(123, 180, 123, 0.4);
}

.pagination-custom .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.pagination-custom .page-item.disabled .page-link:hover {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    transform: none;
    box-shadow: none;
}

.pagination-container {
    background: rgba(255, 255, 255, 0.9);
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin: 1rem 0;
}
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\frontend\resources\views/components/pagination.blade.php ENDPATH**/ ?>