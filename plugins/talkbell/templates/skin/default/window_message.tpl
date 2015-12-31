<script type="text/javascript" src="{$sTWPTalkbell}/js/soundmanager2.js"></script>
{literal}
<script language="JavaScript" type="text/javascript">

function doMany() {
    ls.ajax(aRouter['talkbell'], {security_ls_key:LIVESTREET_SECURITY_KEY}, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            if (!result.bStError) {
                if (result.bSc) {
                    var aSc = result.aHtmlSc;
                    for (var i = 0; i < aSc.length; i++) {
                        console.log(aSc[i]);
                        console.log(jQuery(aSc[i]).text());
                        ls.msg.notify('У вас новое сообщение', "К письму " + jQuery(aSc[i]).text());
                    }
                }
                if (result.bSt) {
                    var aSt = result.aHtmlSt;
                    for (var i = 0; i < aSt.length; i++) {
                        ls.msg.notice('', aSt[i]);
                    }
                }
            }
        }
    });
    return false;
}

setInterval(doMany, 20000)


</script>
{/literal}
