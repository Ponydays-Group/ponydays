<aside class="toolbar">
	{include file='blocks.tpl' group='toolbar'}
	    <section class="toolbar-talk" {if $iUserCurrentCountTalkNew}style="display: block;"{/if}>
    	    <a href="{router page='talk'}" title="+{$iUserCurrentCountTalkNew}">
    		    <i class="fa fa-envelope"></i>
        	</a>
        </section>
</aside>