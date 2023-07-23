{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
		<a href="{$xenforum->GetLink($page_url)|escape:'htmlall':'UTF-8'}">{$page_title|escape:'htmlall':'UTF-8'}</a>
		<span class="navigation-pipe"></span>{l s='Tags' mod='xenforum'} "{$tags|escape:'htmlall':'UTF-8'}"
{/capture}

<div id="blogview" class="blogview">
	<div class='blog_title'>{l s='Search for tag:' mod='xenforum'} "{$tags|escape:'htmlall':'UTF-8'}"</div>
<!-- Show forums and topics -->
{if $allposts == ''}
	<p class="error">({l s='No content here!' mod='xenforum'})</p>
{else}
    {include file="./discussion_list.tpl" allposts=$allposts}
{/if}
<!--// Show forums and topics -->

    {include file="./xf_footer.tpl"}
</div>
