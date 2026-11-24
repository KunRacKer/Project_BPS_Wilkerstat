(function () {
    'use strict';

    window.SIMAP = window.SIMAP || {};

    // Toast
    window.SIMAP.toast = function (type, message) {
        var container = document.getElementById('toastContainer');
        if (!container) {
            console.warn('Toast container not found.');
            return;
        }
        var colorMap = {
            success: 'text-bg-success',
            danger: 'text-bg-danger',
            warning: 'text-bg-warning',
            info: 'text-bg-primary',
            error: 'text-bg-danger'
        };
        var wrap = document.createElement('div');
        wrap.className = 'toast align-items-center border-0 ' + (colorMap[type] || 'text-bg-secondary');
        wrap.setAttribute('role', 'alert');
        wrap.innerHTML =
            '<div class="d-flex">' +
                '<div class="toast-body">' + (message || '') + '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '</div>';
        container.appendChild(wrap);
        var t = new bootstrap.Toast(wrap, { delay: 4000 });
        t.show();
        wrap.addEventListener('hidden.bs.toast', function () { wrap.remove(); });
    };

    // Theme Toggle
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('themeToggle');
        if (btn) {
            function applyIcon() {
                var current = document.documentElement.getAttribute('data-bs-theme') || 'light';
                btn.innerHTML = current === 'dark'
                    ? '<i class="bi bi-sun"></i>'
                    : '<i class="bi bi-moon-stars"></i>';
            }
            applyIcon();

            btn.addEventListener('click', function () {
                var current = document.documentElement.getAttribute('data-bs-theme') || 'light';
                var next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', next);
                localStorage.setItem('simap-theme', next);
                applyIcon();
            });
        }

        // Password toggle di halaman login
        var tgl = document.getElementById('togglePwd');
        if (tgl) {
            tgl.addEventListener('click', function () {
                var pwd = document.getElementById('password');
                if (!pwd) return;
                var isText = pwd.getAttribute('type') === 'text';
                pwd.setAttribute('type', isText ? 'password' : 'text');
                this.innerHTML = isText ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });
        }
    });
})();