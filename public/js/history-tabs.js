(function () {
    const tabsRoot = document.getElementById('history-tabs');

    if (!tabsRoot) {
        return;
    }

    const tabs = tabsRoot.querySelectorAll('[data-tab]');
    const panels = {
        completed: document.getElementById('completedTab'),
        upcoming: document.getElementById('upcomingTab'),
        reverted: document.getElementById('revertedTab'),
        gross: document.getElementById('grossTab')
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
