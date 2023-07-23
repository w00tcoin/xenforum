{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
		{$page_title|escape:'htmlall':'UTF-8'}
{/capture}

<div id="blogview" class="blogview">
{if $allforums == ''}
	<p class="error">({l s='No forums here!' mod='xenforum'})</p>
{else}
		<div class="blog_title">{$page_title|escape:'htmlall':'UTF-8'}</div>
	{if $page_header != ''}
		<div class="blog_header">{$page_header}</div>
	{/if}

	{foreach from=$allforums item=category}
        {include file="./childrenforums_list.tpl" category=$category}
    
        {if (!empty($category['allposts']))}
            {include file="./discussion_list.tpl" allposts=$category['allposts']}
        {/if}
	{/foreach}
{/if}

	{include file="./xf_footer.tpl"}
</div>