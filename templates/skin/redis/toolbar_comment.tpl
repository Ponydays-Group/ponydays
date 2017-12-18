
    {assign var=aPagingCmt value=$params.aPagingCmt}
    <div class="rightbar-item delim-top" id="prevcomment">
        <a href="#" id="prev_new" class="update-comments disabled"
           onclick="ls.comments.goToPrevComment(); return false;" title="Предыдущий комментарий"><i id="go-back"
                                                                                                       class="material-icons">keyboard_arrow_up</i></a>
    </div>
    <div class="rightbar-item" id="update" style="{if $aPagingCmt and $aPagingCmt.iCountPage > 1}display: none;{/if}">
        <a href="#" class="update-comments"
           onclick="ls.comments.load({$params.iTargetId},'{$params.sTargetType}'); return false;"><i
                    id="update-comments" class="material-icons">autorenew</i><span class="new-comments"
                                                                                   id="new_comments_counter"
                                                                                   style="display: none;"
                                                                                   title="{$aLang.comment_count_new}"></span></a>

        <input type="hidden" id="comment_last_id" value="{$params.iMaxIdComment}"/>
        <input type="hidden" id="comment_use_paging" value="{if $aPagingCmt and $aPagingCmt.iCountPage>1}1{/if}"/>
    </div>
    <div class="rightbar-item">
        <a href="#" id="next_new" class="update-comments disabled"
           onclick="ls.comments.goToNextComment(); return false;" title="Следующий новый комментарий"><i id="go-back"
                                                                                                         class="material-icons">keyboard_arrow_down</i></a>
    </div>
        <script>
        function autoload() {
            if (document.getElementById('autoload').checked) {
                ls.comments.load({$params.iTargetId}, '{$params.sTargetType}', null, true)
            }
        }
        console.log(setInterval(autoload, 10000))
    </script>
