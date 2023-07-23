{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{extends file='module:xenforum/views/templates/front/ps7-layout.tpl'}

{block name='page_content'}
<div id="blogview" class="blogview">
{if $allforums == ''}
	<p class="error">({l s='No forums here!' mod='xenforum'})</p>
{else}
		<div class="blog_title">{$page_title|escape:'htmlall':'UTF-8'}</div>
	{if $page_header != ''}
        <div class="blog_header">{$page_header nofilter}</div>
	{/if}

	{foreach from=$allforums item=category}
        {include file='module:xenforum/views/templates/front/childrenforums_list.tpl' category=$category}
    
        {if (!empty($category['allposts']))}
            {include file='module:xenforum/views/templates/front/discussion_list.tpl' allposts=$category['allposts']}
        {/if}
	{/foreach}
{/if}

    {include file='module:xenforum/views/templates/front/xf_footer.tpl'}
</div>
{/block}
