<aside class="toolbar">
	<section class="toolbar-despoil">
        <a href="#" title="spoil/despoil" onclick="despoil(); return false;">
    	    <i class="fa fa-eye-slash"></i>
    	</a>
    </section>
    <section class="toolbar-widemode">
        <a href="#" title="widemode" onclick="widemode(); return false;">
    	    <i class="fa fa-arrows-h"></i>
    	</a>
    </section>
	{include file='blocks.tpl' group='toolbar'}
	    <section class="toolbar-talk" {if $iUserCurrentCountTalkNew}style="display: block;"{/if}>
    	    <a href="{router page='talk'}" title="+{$iUserCurrentCountTalkNew}">
    		    <i class="fa fa-envelope"></i>
        	</a>
        </section>
</aside>