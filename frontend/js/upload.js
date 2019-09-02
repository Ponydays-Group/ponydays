import Emitter from "./emitter"
import {ListElement, DraggableList} from "./dragndrop";

export class ImageUploadElement extends ListElement {
    filename = '';
    preview_src = '#';
    preview_needs_update = false;

    state_needs_update = false;
    state = 0;
    progress = 0;
    msg = '';

    xhr = null;
    loaded_data = null;

    constructor(filename) {
        super();
        this.filename = filename;
    }

    setPreview(preview_src) {
        this.preview_src = preview_src;
        this.preview_needs_update = true;
    }

    setProgress(progress) {
        this.progress = progress;
        this.state = 0;
        this.state_needs_update = true;
    }

    setDone(msg) {
        this.msg = msg;
        this.state = 1;
        this.state_needs_update = true;
    }

    setError(err) {
        this.msg = err;
        this.state = 2;
        this.state_needs_update = true;
    }

    update(el) {
        if(this.state_needs_update) {
            this.state_needs_update = false;
            const state_div = el.querySelector('.draglist-el-state');
            const state_text = state_div.querySelector('.draglist-el-state-text');
            const progress = state_div.querySelector('.progress');
            if(this.state === 0) {
                const progress_bar = state_div.querySelector('.progress-bar');
                progress.style = ``;
                progress_bar.style = `width: ${this.progress}%`;
                state_text.style = `display: none`;
            } else if(this.state === 1) {
                progress.style = `display: none`;
                state_text.style = `color: green`;
                state_text.setAttribute('title', this.msg);
                state_text.textContent = this.msg;
            } else if(this.state === 2) {
                progress.style = `display: none`;
                state_text.style = `color: red`;
                state_text.setAttribute('title', this.msg);
                state_text.textContent = this.msg;
            }
        }
        if(this.preview_needs_update) {
            this.preview_needs_update = false;
            const preview_img = el.querySelector('.draglist-el-preview img');
            preview_img.setAttribute('src', this.preview_src);
            this.preview_needs_update = false;
        }
    }

    render() {
        const el = document.createElement('DIV');
        el.style = 'height: 100%';

        el.innerHTML = `
            <div class="draglist-el-preview">
                <img src="${this.preview_src}" alt="">
            </div>
            <div class="draglist-el-info">
                <span class="draglist-el-info-line">
                    <span class="draglist-el-name">${this.filename}</span>
                    <span class="draglist-el-panel">
                        <span class="fa fa-pencil" style="cursor: pointer;" onclick="ls.upload.imageUploadListEdit(${this.element_id}); return false;"></span>
                        <span class="fa fa-close" style="margin-right: 3px; cursor: pointer;" onclick="ls.upload.imageUploadListRemove(${this.element_id}); return false;"></span>
                    </span>
                </span>
                <div class="draglist-el-state">
                    <span class="draglist-el-state-text" style="display: none"></span>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: ${this.progress}%"></div>
                    </div>
                </div>
            </div>`;

        return el;
    }

    destroy() {
        if(this.xhr) this.xhr.abort();
        if(this.preview_src) URL.revokeObjectURL(this.preview_src);
    }
}

export function openFileSelectDialog(files_cb, multiple=false) {
    const input_el = document.createElement('input');
    input_el.type = 'file';
    if(multiple) input_el.setAttribute('multiple', '');

    input_el.onchange = e => {
        files_cb(e.target.files);
    };
    input_el.click();
}

let imageUploadList = null;
let imageUploadMaxSize = 1048576;
Emitter.on('template_init_start', () => {
    const modal = document.getElementById('window_upload_img');
    if(modal) {
        imageUploadList = new DraggableList(modal.getElementsByClassName('draglist')[0]);
        imageUploadMaxSize = +document.getElementById('max-upload-size').getAttribute('data-value') * 1048576;
    }
});

export let imageUploadingState = new class {
    uploading_now = 0;

    increment() {
        this.uploading_now++;
        imageUploadingShowSubmit(false);
    }

    decrement() {
        this.uploading_now--;
        if(this.uploading_now < 0) this.uploading_now = 0;
        if(this.uploading_now == 0) {
            imageUploadingShowSubmit(true);
        }
    }
};

export function imageUploadingSubmitPressed() {
    if(imageUploadingState.uploading_now == 0 && !imageUploadList.empty()) {
        const title = document.getElementById('form-image-title').value;
        const align = document.getElementById('form-image-align').value;

        let result = '';
        imageUploadList.forEach(el => {
            if(el.state == 1) {
                result += '<img src="' + el.loaded_data + '"';
                if(title) result += ' title="' + title + '" alt="' + title + '"';
                if(align) result += ' align="' + align + '"';
                result += ' />\n';
            }
        });

        if($('#img_spoil').prop('checked')) {
            let s = prompt('Спойлер', 'Спойлер');
            if(s) {
                result = "<span class=\"spoiler\"><span class=\"spoiler-title spoiler-close\">" + s + "</span><span class=\"spoiler-body\">" + result + "</span></span>"
            }
        }

        $.markItUp({replaceWith: result});
        imageUploadList.clear();
        imageUploadingShowSubmit(false);
    }
    jQuery("#window_upload_img").jqmHide();
}

export function imageUploadingShowSubmit(show = true) {
    const submit_but = document.querySelector('#block_upload_img_content_pc_submit');
    if(show) {
        submit_but.classList.add('button-primary');
        submit_but.textContent = 'Вставить';
    } else {
        submit_but.classList.remove('button-primary');
        submit_but.textContent = 'Скрыть';
    }
}

export function imageUploadingCancelPressed() {
    jQuery("#window_upload_img").jqmHide();
    imageUploadList.clear();
    imageUploadingShowSubmit(false);
}

export function imageUploadingClear() {
    imageUploadList.clear();
    imageUploadingShowSubmit(false);
}

export function imageUploadingAdd() {
    openFileSelectDialog(files => {
        if(!files || !files[0]) return;
        for(let file of files) {
            if(file) {
                const img_el = new ImageUploadElement(file.name);
                imageUploadList.addElement(img_el);

                imageUploadStart(img_el, file);
            }
        }
    }, true);
}

export function imageUploadListEdit(id) {
    openFileSelectDialog(files => {
       if(!files || !files[0]) return;
       const img_el = new ImageUploadElement(files[0].name);
       imageUploadList.setElement(id, img_el);

       imageUploadStart(img_el, files[0]);
    });
}

export function imageUploadListRemove(id) {
    const img_el = imageUploadList.getElementById(id);
    if(img_el) {
        imageUploadList.removeElement(img_el);
        if(imageUploadList.empty()) imageUploadingShowSubmit(false);
    }
}

function imageUploadStart(img_el, file) {
    if(file.size > imageUploadMaxSize) {
        img_el.setError("Ошибка: превышено максимальное значение размера файла");
        return;
    }

    setTimeout(() => {
        const preview = URL.createObjectURL(file);
        img_el.setPreview(preview);
    }, 0);

    const formData = new FormData();
    formData.append('img_file[]', file, file.name);
    formData.append('just_url', '1');
    formData.append('security_ls_key', LIVESTREET_SECURITY_KEY);

    const xhr = new XMLHttpRequest();
    img_el.xhr = xhr;
    xhr.open('POST', aRouter["ajax"] + '/upload/image');
    xhr.responseType = 'json';

    xhr.onloadstart = e => {
        imageUploadingState.increment();
        img_el.setProgress(0);
        imageUploadList.updateElement(img_el);
    };
    xhr.upload.onprogress = e => {
        img_el.setProgress(e.loaded / e.total * 100);
        imageUploadList.updateElement(img_el);
    };
    xhr.upload.ontimeout = e => {
        img_el.setError("Ошибка: время вышло");
        imageUploadList.updateElement(img_el);
    };
    xhr.upload.onabort = e => {
        img_el.setError("Прервано");
        imageUploadList.updateElement(img_el);
    };
    xhr.upload.onerror = e => {
        img_el.setError("Ошибка: не найдено");
        imageUploadList.updateElement(img_el);
    };
    xhr.upload.onloadend = e => {
        imageUploadingState.decrement();
    };
    xhr.upload.onload = e => {
        img_el.setProgress(100);
        imageUploadList.updateElement(img_el);
    };
    xhr.onload = e => {
        const data = xhr.response;
        if(!data) {
            img_el.setError("Ошибка: изображение слишком тяжелое");
            imageUploadList.updateElement(img_el);
            return;
        }
        if(data.bStateError) {
            img_el.setError(data.sMsgTitle + ": " + data.sMsg);
            imageUploadList.updateElement(img_el);
        } else {
            if(!data.sText.length) {
                img_el.setError("Ошибка: сервер не отдал картинку");
                imageUploadList.updateElement(img_el);
                return;
            }
            img_el.loaded_data = data.sText;
            img_el.setDone("Завершено");
            imageUploadList.updateElement(img_el);
        }
    };
    xhr.send(formData);
}
