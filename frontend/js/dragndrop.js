import * as Lang from './lang';

export class Draggable {
    currentElement = null;

    addHandlers(el) {
        let ref = this;
        el.addEventListener('dragstart', function(e) { ref._handleDragStart(this, e); }, false);
        el.addEventListener('dragenter', function(e) { Draggable._handleDragEnter(this, e); }, false);
        el.addEventListener('dragover',  function(e) { Draggable._handleDragOver(this, e); }, false);
        el.addEventListener('dragleave', function(e) { Draggable._handleDragLeave(this, e); }, false);
        el.addEventListener('drop',      function(e) { ref._handleDrop(this, e); }, false);
        el.addEventListener('dragend',   function(e) { Draggable._handleDragEnd(this, e); }, false);
    }

    _handleDragStart(el, e) {
        this.currentElement = el;

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', el.outerHTML);

        el.classList.add('dnd-dragged');
    }

    static _handleDragEnter(el, e) {}

    static _handleDragOver(el, e) {
        if(e.preventDefault) e.preventDefault();
        el.classList.add('dnd-over');

        e.dataTransfer.dropEffect = 'move';

        return false;
    }

    static _handleDragLeave(el, e) {
        el.classList.remove('dnd-over');
    }

    _handleDrop(el, e) {
        if(e.stopPropagation) e.stopPropagation();
        if(this.currentElement != el) {
            el.parentNode.removeChild(this.currentElement);
            let dropHtml = e.dataTransfer.getData('text/html');
            el.insertAdjacentHTML('beforebegin', dropHtml);
            let dropEl = el.previousSibling;
            this.addHandlers(dropEl);
        }
        el.classList.remove('dnd-over');
        el.classList.remove('dnd-dragged');
        return false;
    }

    static _handleDragEnd(el, e) {
        el.classList.remove('dnd-over');
        el.classList.remove('dnd-dragged');
    }
}

export class ListElement {
    element_id = 0;

    update(el) {}

    render() {
        return document.createTextNode('');
    }

    destroy() {}
}

export class DraggableList {
    draggable = new Draggable();
    list_element = null;

    last_id = 0;
    elements = new Map();

    constructor(el) {
        this.list_element = el;
        this.list_element.classList.add('draglist');
        this._initPlaceholderText();
    }

    addElement(list_element) {
        const li = document.createElement('LI');
        li.classList.add('draglist-el');
        li.setAttribute('draggable', 'true');
        li.setAttribute('data-id', ++this.last_id);
        list_element.element_id = this.last_id;
        const ch = list_element.render();
        li.appendChild(ch);
        if(this.elements.size == 0) {
            while(this.list_element.firstChild) this.list_element.removeChild(this.list_element.firstChild);
        }
        this.list_element.appendChild(li);
        this.draggable.addHandlers(li);
        this.elements.set(this.last_id, list_element);
    }

    getDomElement(id) {
        const parent = this.list_element.querySelector(`[data-id='${id}']`);
        return parent ? parent.children[0] : null;
    }

    forEach(fn) {
        const children = Array.from(this.list_element.children);
        for(let child of children) {
            if(child && child.getAttribute('data-id')) {
                fn(this.elements.get(+child.getAttribute('data-id')));
            }
        }
    }

    getElementList() {
        const list = [];
        this.forEach(el => list.push(el));
        return list;
    }

    setElement(id, element) {
        const el = this.getDomElement(id);
        if(el) {
            const li = el.parentElement;
            this.elements.get(id).destroy();
            li.removeChild(el);
            element.element_id = id;
            const ch = element.render();
            li.appendChild(ch);
            this.elements.set(id, element);
        }
    }

    updateElement(list_element) {
        const el = this.getDomElement(list_element.element_id);
        if(el) list_element.update(el);
    }

    getElementById(id) {
        return this.elements.get(id);
    }

    removeElement(list_element) {
        const dom_element = this.getDomElement(list_element.element_id);
        if(dom_element) {
            list_element.destroy();
            this.list_element.removeChild(dom_element.parentNode);
            this.elements.delete(list_element.element_id);
            if(this.elements.size == 0) {
                this._initPlaceholderText();
            }
        }
    }

    clear() {
        for(let [_, el] of this.elements) el.destroy();
        this.elements.clear();
        while(this.list_element.firstChild) {
            this.list_element.removeChild(this.list_element.firstChild);
        }
        this._initPlaceholderText();
    }

    empty() {
        return this.elements.size == 0;
    }

    _initPlaceholderText() {
        const li = document.createElement('LI');
        li.classList.add('draglist-placeholder');
        li.appendChild(document.createTextNode('Здесь ничего нет. Добавьте побольше файлов.'));
        this.list_element.appendChild(li);
    }
}
