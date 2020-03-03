/* !!! WIP !!! */

let _tabId;

function generateUniqId() {
    return Math.random().toString(36).substr(2);
}

function getTabId() {
    if(!_tabId) initNewTab();
    return _tabId;
}

function initNewTab() {
    _tabId = generateUniqId();
    const tab_list = getTabList();
    tab_list.push(_tabId);
    setTabList(tab_list);
    makeCurrent(_tabId);
}

function makeCurrent(tab_id) {
    window.localStorage.setItem('current_tab', tab_id);
}

function becomeCurrent() { makeCurrent(getTabId()); }

export function isTabCurrent() {
    return window.localStorage.getItem('current_tab') === getTabId();
}

function getTabList() {
    const tabs = window.localStorage.getItem('tabs');
    if(tabs) {
        const tab_list = JSON.parse(tabs);
        if(!Array.isArray(tab_list)) {
            return [];
        }
        return tab_list;
    } else {
        return [];
    }
}

function setTabList(tab_list) {
    window.localStorage.setItem('tabs', JSON.stringify(tab_list));
}

window.onfocus = function() {
    becomeCurrent();
};

window.addEventListener('visibilitychange', function() {
    if(!document.hidden) becomeCurrent();
});
