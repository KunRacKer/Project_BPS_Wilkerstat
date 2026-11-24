<?php
/**
 * Pemakaian: 
 * View::partial('components.form-group', [
 *    'name' => 'full_name',
 *    'label' => 'Nama Lengkap',
 *    'value' => $user['full_name'] ?? '',
 *    'required' => true,
 *    'type' => 'text',
 *    'placeholder' => 'Masukkan nama...'
 * ])
 */
$name = $name ?? '';
$id = $id ?? 'input_' . $name;
$label = $label ?? '';
$type = $type ?? 'text';
$value = $value ?? '';
$placeholder = $placeholder ?? '';
$required = $required ?? false;
$help = $help ?? '';
$error = $error ?? '';

$inputClass = 'form-control';
if ($error) {
    $inputClass .= ' is-invalid';
}
?>
<div class="mb-3">
    <?php if ($label): ?>
        <label for="<?= e($id) ?>" class="form-label small fw-semibold">
            <?= e($label) ?>
            <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
        </label>
    <?php endif; ?>
    
    <?php if ($type === 'textarea'): ?>
        <textarea class="<?= e($inputClass) ?>" id="<?= e($id) ?>" name="<?= e($name) ?>" 
                  rows="3" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>><?= e($value) ?></textarea>
    <?php else: ?>
        <input type="<?= e($type) ?>" class="<?= e($inputClass) ?>" id="<?= e($id) ?>" name="<?= e($name) ?>" 
               value="<?= e($value) ?>" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="invalid-feedback"><?= e($error) ?></div>
    <?php elseif ($help): ?>
        <div class="form-text"><?= e($help) ?></div>
    <?php endif; ?>
</div>