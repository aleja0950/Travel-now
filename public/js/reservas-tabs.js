(function () {
    const tabsRoot = document.getElementById('reservas-tabs');

    if (!tabsRoot) {
        return;
    }

    const tabs = tabsRoot.querySelectorAll('[data-tab]');
    const panels = {
        upcoming: document.getElementById('upcomingTab'),
        past: document.getElementById('pastTab'),
        rejected: document.getElementById('rejectedTab')
    };

    function activarTab(tab) {
        tabs.forEach(function (item) {
            item.classList.toggle('active', item.dataset.tab === tab);
        });

        Object.keys(panels).forEach(function (key) {
            const panel = panels[key];
            if (!panel) {
                return;
            }

            panel.classList.toggle('tab-panel-hidden', key !== tab);
        });
    }

    tabs.forEach(function (item) {
        item.addEventListener('click', function () {
            activarTab(item.dataset.tab);
        });
    });
})();
