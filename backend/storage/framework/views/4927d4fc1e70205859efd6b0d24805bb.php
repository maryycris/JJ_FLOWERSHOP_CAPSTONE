
<?php $__env->startSection('content'); ?>
<div class="mx-auto" style="max-width: 400px;">
    <h4 class="fw-bold mb-3"><i class="bi bi-key"></i> Reset Password</h4>
    <form method="POST" action="<?php echo e(route('password.update')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="token" value="<?php echo e($token); ?>">
        <input type="hidden" name="email" value="<?php echo e($email); ?>">
        <div class="mb-3">
            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="New Password">
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-danger small"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm New Password">
        </div>
        <div class="d-grid mb-2">
            <button type="submit" class="btn btn-success">Reset Password</button>
        </div>
        <div class="mb-2">
            <a href="<?php echo e(route('login')); ?>" class="small">Back to Login</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.mobile_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\frontend\resources\views/auth/passwords/reset.blade.php ENDPATH**/ ?>