import './stimulus_bootstrap.js';

// ── Dark / Light Mode ──────────────────────────────────────────────────────
function applyColorTheme() {
    const stored    = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark    = stored === 'dark' || (!stored && prefersDark);
    const html      = document.documentElement;

    if (isDark) {
        html.classList.add('dark', 'dark-theme', 'wa-dark');
        html.classList.remove('wa-light');
        html.setAttribute('data-theme', 'dark');
    } else {
        html.classList.remove('dark', 'dark-theme', 'wa-dark');
        html.classList.add('wa-light');
        html.setAttribute('data-theme', 'light');
    }
}

window.applyColorTheme = applyColorTheme;

window.darkMode = () => {
    localStorage.setItem('theme', 'dark');
    applyColorTheme();
};

window.lightMode = () => {
    localStorage.setItem('theme', 'light');
    applyColorTheme();
};

applyColorTheme();

window.updateThemeIcons = () => {
    const isDark = document.documentElement.classList.contains('dark');
    document.querySelectorAll('#theme-icon-sidebar, #theme-icon-topbar').forEach(el => {
        if (isDark) {
            el.className = el.className.replace('fa-moon', 'fa-sun');
        } else {
            el.className = el.className.replace('fa-sun', 'fa-moon');
        }
    });
};

document.addEventListener('turbo:render', () => { applyColorTheme(); updateThemeIcons(); });
document.addEventListener('DOMContentLoaded', updateThemeIcons);

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (!localStorage.getItem('theme')) {
        applyColorTheme();
    }
});
