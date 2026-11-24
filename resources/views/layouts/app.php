<?php
/** @var string $content */
/** @var string $title */
use App\Support\Session;
$appName = \App\Support\Config::get('app.name', 'SIMAP Wilkerstat');
$user = Session::get('_auth');
$title = $title ?? $appName;

$flashSuccess = Session::pullFlash('success');
$flashError   = Session::pullFlash('error');
$flashInfo    = Session::pullFlash('info');
$flashWarning = Session::pullFlash('warning');
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> &middot; <?= e($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
    <script>
        (function () {
            var t = localStorage.getItem('simap-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>
</head>
<body>
<?= \App\Support\View::partial('components.topbar', ['user' => $user]) ?>

<main class="container-fluid px-3 px-md-4 py-4 app-main">
    <div class="container-xxl px-0">
        <?= $content ?>
    </div>
</main>

<?= \App\Support\View::partial('components.toast') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
<?php if ($flashSuccess || $flashError || $flashInfo || $flashWarning): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($flashSuccess): ?>window.SIMAP.toast('success', <?= json_encode($flashSuccess, JSON_UNESCAPED_UNICODE) ?>);<?php endif; ?>
    <?php if ($flashError): ?>window.SIMAP.toast('danger',  <?= json_encode($flashError,  JSON_UNESCAPED_UNICODE) ?>);<?php endif; ?>
    <?php if ($flashWarning): ?>window.SIMAP.toast('warning', <?= json_encode($flashWarning, JSON_UNESCAPED_UNICODE) ?>);<?php endif; ?>
    <?php if ($flashInfo): ?>window.SIMAP.toast('info',    <?= json_encode($flashInfo,    JSON_UNESCAPED_UNICODE) ?>);<?php endif; ?>
});
</script>
<?php endif; ?>
</body>
</html>