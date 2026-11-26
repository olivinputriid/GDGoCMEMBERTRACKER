/**
 * GDGoC UNSRI Admin Portal Interactive Controller
 */

function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach((el) => {
        const target = parseInt(el.dataset.counter || '0', 10);
        const suffix = el.dataset.suffix || '';
        const format = el.dataset.format === 'comma';
        if (Number.isNaN(target)) {
            return;
        }

        const duration = 650;
        const startTime = performance.now();

        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(1, elapsed / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(target * eased);
            el.textContent = (format ? current.toLocaleString('en-US') : String(current)) + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    });
}

function initAnimatedBars() {
    requestAnimationFrame(() => {
        document.querySelectorAll('[data-bar-width]').forEach((el) => {
            el.style.width = `${el.dataset.barWidth}%`;
        });
        document.querySelectorAll('[data-bar-height]').forEach((el) => {
            el.style.height = `${el.dataset.barHeight}%`;
        });
    });
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) {
        return;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeModal(modal) {
    if (!modal) {
        return;
    }
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (!document.querySelector('.portal-modal.flex')) {
        document.body.classList.remove('overflow-hidden');
    }
}

function initModals() {
    document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            openModal(trigger.dataset.modalOpen);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((btn) => {
        btn.addEventListener('click', () => {
            closeModal(btn.closest('.portal-modal'));
        });
    });

    document.querySelectorAll('.portal-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.portal-modal.flex').forEach(closeModal);
            document.querySelectorAll('[data-dropdown-panel]').forEach((panel) => panel.classList.add('hidden'));
        }
    });
}

function initDropdowns() {
    document.querySelectorAll('[data-dropdown-toggle]').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.stopPropagation();
            const targetId = btn.dataset.dropdownToggle;
            const panel = document.getElementById(targetId);
            if (!panel) {
                return;
            }
            const isHidden = panel.classList.contains('hidden');
            document.querySelectorAll('[data-dropdown-panel]').forEach((other) => {
                if (other.id !== targetId) {
                    other.classList.add('hidden');
                }
            });
            panel.classList.toggle('hidden', !isHidden);
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-dropdown-panel]') && !event.target.closest('[data-dropdown-toggle]')) {
            document.querySelectorAll('[data-dropdown-panel]').forEach((panel) => panel.classList.add('hidden'));
        }
    });
}

function initSidebarDrawer() {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('portal-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const closeBtn = document.getElementById('sidebar-close');

    if (!toggle || !sidebar || !backdrop) {
        return;
    }

    const closeDrawer = () => {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    };

    toggle.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    });

    backdrop.addEventListener('click', closeDrawer);
    if (closeBtn) {
        closeBtn.addEventListener('click', closeDrawer);
    }
}

function initGlobalSearch() {
    const input = document.getElementById('global-search-input');
    const resultsBox = document.getElementById('global-search-results');
    if (!input || !resultsBox) {
        return;
    }

    let timer = null;
    const searchUrl = input.dataset.searchUrl;

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const query = input.value.trim();

        if (query.length < 1) {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
            return;
        }

        timer = setTimeout(async () => {
            try {
                const res = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                    headers: { Accept: 'application/json' },
                });
                const data = await res.json();
                renderSearchResults(resultsBox, data);
            } catch {
                resultsBox.classList.add('hidden');
            }
        }, 200);
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('#global-search-wrapper')) {
            resultsBox.classList.add('hidden');
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderSearchResults(container, data) {
    const members = data.members || [];
    const activities = data.activities || [];

    if (members.length === 0 && activities.length === 0) {
        container.innerHTML = '<div class="px-4 py-3 text-xs text-slate-400">No matching members or activities found.</div>';
        container.classList.remove('hidden');
        return;
    }

    let html = '';
    if (members.length > 0) {
        html += '<div class="px-3 pt-2.5 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Members</div>';
        members.forEach((m) => {
            html += `
                <a href="${escapeHtml(m.url)}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                    <div>
                        <div class="text-sm font-semibold text-slate-800">${escapeHtml(m.name)}</div>
                        <div class="text-xs text-slate-400">${escapeHtml(m.subtitle)}</div>
                    </div>
                    <span class="text-xs font-bold text-blue-700">${escapeHtml(String(m.points))} pts</span>
                </a>
            `;
        });
    }

    if (activities.length > 0) {
        html += '<div class="px-3 pt-2.5 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-t border-slate-100 mt-1">Activities</div>';
        activities.forEach((a) => {
            html += `
                <a href="${escapeHtml(a.url)}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                    <div>
                        <div class="text-sm font-semibold text-slate-800">${escapeHtml(a.title)}</div>
                        <div class="text-xs text-slate-400">${escapeHtml(a.subtitle)}</div>
                    </div>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full ${a.status === 'Upcoming' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600'}">${escapeHtml(a.status)}</span>
                </a>
            `;
        });
    }

    container.innerHTML = `<div class="p-1.5">${html}</div>`;
    container.classList.remove('hidden');
}

window.showToast = function showToast(message, type = 'success') {
    const stack = document.getElementById('toast-stack');
    if (!stack) {
        return;
    }

    const colors = {
        success: 'bg-emerald-600 text-white',
        info: 'bg-blue-700 text-white',
        error: 'bg-red-600 text-white',
    };

    const toast = document.createElement('div');
    toast.className = `animate-toast-in pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-lg text-sm font-medium ${colors[type] || colors.success}`;
    toast.innerHTML = `<span>${escapeHtml(message)}</span>`;
    stack.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = 'all 0.25s ease';
        setTimeout(() => toast.remove(), 260);
    }, 3400);
};

window.openPortalModal = openModal;

document.addEventListener('DOMContentLoaded', () => {
    initCounters();
    initAnimatedBars();
    initModals();
    initDropdowns();
    initSidebarDrawer();
    initGlobalSearch();

    document.querySelectorAll('[data-auto-dismiss]').forEach((el) => {
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            el.style.transition = 'all 0.28s ease';
            setTimeout(() => el.remove(), 300);
        }, 3800);
    });
});
