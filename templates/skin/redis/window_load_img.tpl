<div class="modal modal-image-upload" id="window_upload_img">
    <header class="modal-header">
        <h3>{$aLang.uploadimg}</h3>
        <a href="#" class="close jqmClose material-icons">close</a>
    </header>

    <div class="modal-content">
        <ul class="nav nav-pills nav-pills-tabs">
            <li class="active js-block-upload-img-item" data-type="pc"><a href="#">{$aLang.uploadimg_from_pc}</a></li>
            <li class="js-block-upload-img-item" data-type="link"><a href="#">{$aLang.uploadimg_from_link}</a></li>
        </ul>

        <div id="block_upload_img_content_pc" class="tab-content js-block-upload-img-content" data-type="pc">
            <div class="file-upload-list">
                <span>{$aLang.uploadimg_files}:</span>
                <span id="max-upload-size" style="float: right" data-value="{max_upload_size}">Максимальный размер файла: {max_upload_size}МБ</span>
                <div class="draglist"></div>
                <div class="file-upload-list-controls">
                    <button class="button" onclick="ls.upload.imageUploadingAdd(); return false;">{$aLang.uploadimg_add}</button>
                    <button class="button button-red" onclick="ls.upload.imageUploadingClear(); return false;" style="float: right">{$aLang.uploadimg_clear}</button>
                </div>
            </div>

            <div style="margin: 10px 0 5px">
                <div style="width: 70%; display: inline-block">
                    <label for="form-image-title">{$aLang.uploadimg_title}:</label>
                    <input type="text" name="title" id="form-image-title" value="" class="input-text input-width-full"/>
                </div>

                <div style="width: 30%; display: inline-block; float: right">
                    <label for="form-image-align">{$aLang.uploadimg_align}:</label>
                    <select name="align" id="form-image-align" class="input-width-full">
                        <option value="">{$aLang.uploadimg_align_no}</option>
                        <option value="left">{$aLang.uploadimg_align_left}</option>
                        <option value="right">{$aLang.uploadimg_align_right}</option>
                        <option value="center">{$aLang.uploadimg_align_center}</option>
                    </select>
                </div>
            </div>

            <span class="checkbox">
                <span>
                    <input type="checkbox" tabindex="" name="img_spoil" id="img_spoil" checked>
                    <label for="img_spoil">Спойлер</label>
                </span>
            </span>

            <button type="submit" id="block_upload_img_content_pc_submit" class="button" onclick="ls.upload.imageUploadingSubmitPressed(); return false;">{$aLang.uploadimg_hide}</button>
            <button type="submit" class="button" style="float: right" onclick="ls.upload.imageUploadingCancelPressed(); return false;">{$aLang.uploadimg_cancel}</button>
        </div>

        <form method="POST" action="" enctype="multipart/form-data" id="block_upload_img_content_link"
              onsubmit="return false;" style="display: none;" class="tab-content js-block-upload-img-content"
              data-type="link">
            <p>
                <label for="img_url">{$aLang.uploadimg_url}:</label>
                <input type="text" name="img_url" id="img_url" value="http://" class="input-text input-width-full"/>
            </p>

            <p>
                <label for="form-image-url-align">{$aLang.uploadimg_align}:</label>
                <select name="align" id="form-image-url-align" class="input-width-full">
                    <option value="">{$aLang.uploadimg_align_no}</option>
                    <option value="left">{$aLang.uploadimg_align_left}</option>
                    <option value="right">{$aLang.uploadimg_align_right}</option>
                    <option value="center">{$aLang.uploadimg_align_center}</option>
                </select>
            </p>

            <span class="checkbox">
                <span>
                    <input type="checkbox" tabindex="" name="img_url_spoil" id="img_url_spoil" checked>
                    <label for="img_url_spoil">Спойлер</label>
                </span>
            </span>

            <p><label for="form-image-url-title">{$aLang.uploadimg_title}:</label>
                <input type="text" name="title" id="form-image-url-title" value="" class="input-text input-width-full"/>
            </p>

            {hook run="uploadimg_link_additional"}

            <button type="submit" id="block_upload_img_content_link_submit" class="button button-primary"
                    onclick="ls.ajax.ajaxUploadImg('block_upload_img_content_link','{$sToLoad}', '#img_url_spoil');">{$aLang.uploadimg_link_submit_load}</button>
            {$aLang.or_}
            <button type="submit" class="button button-primary"
                    onclick="ls.topic.insertImageToEditor(jQuery('#img_url').val(), $('#img_url_spoil').prop('checked'), jQuery('#form-image-url-align').val(),jQuery('#form-image-url-title').val());">{$aLang.uploadimg_link_submit_paste}</button>
            <button type="submit" class="button jqmClose">{$aLang.uploadimg_cancel}</button>
        </form>
    </div>
</div>
