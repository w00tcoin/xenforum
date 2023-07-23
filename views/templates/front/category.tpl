{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
	{if $category.meta_title == ''}
		{$page_title|escape:'htmlall':'UTF-8'}
	{else}
		<a href="{$xenforum->GetLink($page_url)|escape:'htmlall':'UTF-8'}">{$page_title|escape:'htmlall':'UTF-8'}</a>

        {foreach from=$nested_categories item=nested}
            {if $nested.id_xenforum_cat != $id_xenforum_cat}
                {assign var="catOptions" value=null}
                    {$catOptions.id_xenforum_cat = $nested.id_xenforum_cat}
                    {$catOptions.slug = $nested.link_rewrite}
                &nbsp;&nbsp;&nbsp;
                <a href="{$xenforum->GetLink('xenforum_category',$catOptions)|escape:'htmlall':'UTF-8'}">{$nested.meta_title|escape:'htmlall':'UTF-8'}</a>
            {else}
                <span class="navigation-pipe"></span>{$category.meta_title|escape:'htmlall':'UTF-8'}
            {/if}
        {/foreach}
	{/if}
{/capture}

<div id="blogview" class="blogview">
	<div class="topCtrl">
	{if ($category.closed)}
		<p class="muted">(<i class="icon-lock"></i> {l s='Locked' mod='xenforum'})</p>
	{else}
		{if ($user->checkPerm($id_online, '_create_topic_', $category.id_xenforum_cat))}
			{assign var="catOptions" value=null}
				{$catOptions.id_xenforum_cat = $id_xenforum_cat}
				{$catOptions.slug = $category.link_rewrite}
					<a href="{$xenforum->GetLink('xenforum_topic_create',$catOptions)|escape:'htmlall':'UTF-8'}" class="notif topCreate">
						<span><i class="icon icon-plus"></i>&nbsp;{l s='New Topic' mod='xenforum'}</span>
					</a>
		{/if}
	{/if}
	</div>

	<div class='blog_title'>{$category.meta_title|escape:'htmlall':'UTF-8'}</div>
	{if ($category.description)}
		<div class='blog_header'>{$category.description}</div>
	{/if}

    {include file="./childrenforums_list.tpl" category=$category}
    {if (!$allposts)}
	   <p class="error">({l s='No content here!' mod='xenforum'})</p>
    {else}
        {include file="./discussion_list.tpl" allposts=$allposts}
	{/if}

    {include file="./xf_footer.tpl"}
</div>
