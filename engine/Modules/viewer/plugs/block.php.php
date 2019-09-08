<?php

function smarty_block_php($aParams,$sContent,&$oSmarty,&$bRepeat) {
    eval($sContent);
    return '';
}