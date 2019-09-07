export class ListElement {
    element_id = 0;

    created() {}

    render() {
        return document.createTextNode('');
    }

    update(el) {}

    destroy() {}
}

export class DraggableList {
    list_element = null;

    last_id = 0;
    elements = new Map();

    dragged_element = null;

    dnd_file_handler = null;
    dnd_placeholder = null;

    dnd_cur_over_el = null;
    dnd_cur_over_dir = 0;

    constructor(el) {
        this.dnd_placeholder = document.createElement('LI');
        this.dnd_placeholder.classList.add('draglist-placeholder');
        this.list_element = el;
        let ref = this;
        this.list_element.addEventListener('dragover', function(e) {
            if(e.stopPropagation) e.stopPropagation();
            if(e.preventDefault) e.preventDefault();
            ref._removePlaceholder();
            ref.list_element.appendChild(ref.dnd_placeholder);
            e.dataTransfer.dropEffect = 'move';
            return false;
        });
        this.list_element.addEventListener('dragleave', function(e) {
            if(e.stopPropagation) e.stopPropagation();
            if(e.preventDefault) e.preventDefault();
            ref._removePlaceholder();
            return false;
        });
        this.list_element.addEventListener('drop', function(e) {
            ref._handleEmptyDrop(this, e);
        }, false);
        this.dnd_placeholder.addEventListener('dragover', function(e) {
            if(e.stopPropagation) e.stopPropagation();
            e.preventDefault();
        }, false);
        this.dnd_placeholder.addEventListener('drop', function(e) {
            ref._handleDrop(this, e);
        }, false);
        this.list_element.classList.add('draglist');
        this._initEmptyText();
    }

    setFileHandler(handler) {
        this.dnd_file_handler = handler;
    }

    addElement(element) {
        const li = document.createElement('DIV');
        li.classList.add('draglist-el');
        li.setAttribute('draggable', 'true');
        li.setAttribute('data-id', ++this.last_id);
        element.element_id = this.last_id;
        const ch = element.render();
        li.appendChild(ch);
        if(this.elements.size == 0) {
            while(this.list_element.firstChild) this.list_element.removeChild(this.list_element.firstChild);
        }
        this.list_element.appendChild(li);
        this._addHandlers(li);
        this.elements.set(this.last_id, element);
        element.created();
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

    updateElement(element) {
        const el = this.getDomElement(element.element_id);
        if(el) element.update(el);
    }

    getElementById(id) {
        return this.elements.get(id);
    }

    removeElement(element) {
        const dom_element = this.getDomElement(element.element_id);
        if(dom_element) {
            element.destroy();
            this.list_element.removeChild(dom_element.parentNode);
            this.elements.delete(element.element_id);
            if(this.elements.size == 0) {
                this._initEmptyText();
            }
        }
    }

    clear() {
        for(let [_, el] of this.elements) el.destroy();
        this.elements.clear();
        while(this.list_element.firstChild) {
            this.list_element.removeChild(this.list_element.firstChild);
        }
        this._initEmptyText();
    }

    empty() {
        return this.elements.size == 0;
    }

    _initEmptyText() {
        const li = document.createElement('DIV');
        li.classList.add('draglist-empty');
        li.appendChild(document.createTextNode('Здесь ничего нет. Добавьте побольше файлов.'));
        this.list_element.appendChild(li);
    }

    _addHandlers(el) {
        let ref = this;
        el.addEventListener('dragstart', function(e) {
            ref._handleDragStart(this, e);
        }, false);
        el.addEventListener('dragenter', function(e) {
            ref._handleDragEnter(this, e);
        }, false);
        el.addEventListener('dragover', function(e) {
            ref._handleDragOver(this, e);
        }, false);
        el.addEventListener('dragleave', function(e) {
            ref._handleDragLeave(this, e);
        }, false);
        el.addEventListener('drop', function(e) {
            ref._handleDrop(this, e);
        }, false);
        el.addEventListener('dragend', function(e) {
            ref._handleDragEnd(this, e);
        }, false);
    }

    _removePlaceholder() {
        if(this.list_element.contains(this.dnd_placeholder)) this.list_element.removeChild(this.dnd_placeholder);
    }

    _handleDragStart(el, e) {
        this.dragged_element = el;

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', el.outerHTML);

        el.classList.add('dnd-dragged');
    }

    _handleDragEnter(el, e) {
        if(e.stopPropagation) e.stopPropagation();
    }

    _handleDragOver(el, e) {
        if(e.stopPropagation) e.stopPropagation();
        if(e.preventDefault) e.preventDefault();
        const rect = el.getBoundingClientRect();
        const ycenter = rect.top + el.offsetHeight / 2;

        if(ycenter <= e.clientY) {
            if(el != this.dnd_cur_over_el || this.dnd_cur_over_dir != 0) {
                this._removePlaceholder();
                el.insertAdjacentElement('afterend', this.dnd_placeholder);
                this.dnd_cur_over_el = el;
                this.dnd_cur_over_dir = 0;
            }
        } else {
            if(el != this.dnd_cur_over_el || this.dnd_cur_over_dir != 1) {
                this._removePlaceholder();
                el.insertAdjacentElement('beforebegin', this.dnd_placeholder);
                this.dnd_cur_over_el = el;
                this.dnd_cur_over_dir = 1;
            }
        }

        e.dataTransfer.dropEffect = 'move';

        return false;
    }

    _handleDragLeave(el, e) {
        if(e.stopPropagation) e.stopPropagation();
    }

    _handleDrop(el, e) {
        if(e.stopPropagation) e.stopPropagation();
        e.preventDefault();
        if(this.list_element.contains(this.dnd_placeholder)) {
            let isFile = false;
            const stringCallback = dropHtml => {
                if(dropHtml) {
                    if(el != this.dragged_element) {
                        this.dnd_placeholder.insertAdjacentHTML('beforebegin', dropHtml);
                        let dropEl = this.dnd_placeholder.previousSibling;
                        this._addHandlers(dropEl);
                        this.list_element.removeChild(this.dragged_element);
                    }
                }
            };
            const fileCallback = file => {
                isFile = true;
                const dropElement = this.dnd_file_handler(file);
                this.addElement(dropElement);
                const dropEl = this.getDomElement(dropElement.element_id).parentNode;
                this.list_element.removeChild(dropEl);
                this.dnd_placeholder.insertAdjacentElement('beforebegin', dropEl);
                this._addHandlers(dropEl);
            };
            if(e.dataTransfer.items) {
                for(let i = 0; i < e.dataTransfer.items.length; i++) {
                    if(e.dataTransfer.items[i].kind === 'file' && this.dnd_file_handler) {
                        const file = e.dataTransfer.items[i].getAsFile();
                        fileCallback(file);
                    }
                }
            } else {
                for(let i = 0; i < e.dataTransfer.files.length; i++) {
                    const file = e.dataTransfer.files[i];
                    fileCallback(file);
                }
            }
            if(!isFile) {
                stringCallback(e.dataTransfer.getData('text/html'));
            }
        }
        this._removePlaceholder();
        el.classList.remove('dnd-dragged');
        return false;
    }

    _handleDragEnd(el, e) {
        this.dragged_element.classList.remove('dnd-dragged');
    }

    _handleEmptyDrop(el, e) {
        if(e.stopPropagation) e.stopPropagation();
        e.preventDefault();
        let isFile = false;
        const stringCallback = dropHtml => {
            if(dropHtml) {
                if(el != this.dragged_element) {
                    this.list_element.insertAdjacentHTML('beforeend', dropHtml);
                    let dropEl = this.list_element.lastChild;
                    this._addHandlers(dropEl);
                    this.list_element.removeChild(this.dragged_element);
                }
            }
        };
        const fileCallback = file => {
            isFile = true;
            const dropElement = this.dnd_file_handler(file);
            this.addElement(dropElement);
        };
        if(e.dataTransfer.items) {
            for(let i = 0; i < e.dataTransfer.items.length; i++) {
                if(e.dataTransfer.items[i].kind === 'file' && this.dnd_file_handler) {
                    const file = e.dataTransfer.items[i].getAsFile();
                    fileCallback(file);
                }
            }
        } else {
            for(let i = 0; i < e.dataTransfer.files.length; i++) {
                const file = e.dataTransfer.files[i];
                fileCallback(file);
            }
        }
        if(!isFile) {
            stringCallback(e.dataTransfer.getData('text/html'));
        }
        this._removePlaceholder();
        el.classList.remove('dnd-dragged');
        return false;
    }
}
