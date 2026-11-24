<?php
/** @var string $icon */ 
/** @var string $title */ 
/** @var string $desc */ 
$icon = $icon ?? 'inbox';
$title = $title ?? 'Tidak Ada Data';
$desc = $desc ?? '';
?>
<div class="text-center py-5">
    <div class="mb-3 text-secondary opacity-50">
        <i class="bi bi-<?= e($icon) ?> display-5"></i>
    </div>
    <h6 class="fw-semibold"><?= e($title) ?></h6>
    <p class="text-secondary small mb-0"><?= e($desc) ?></p>
</div>