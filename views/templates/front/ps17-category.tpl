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

    {include file='module:xenforum/views/templates/front/childrenforums_list.tpl' category=$category}
    {if (!$allposts)}
	   <p class="error">({l s='No content here!' mod='xenforum'})</p>
    {else}
        {include file='module:xenforum/views/templates/front/discussion_list.tpl' allposts=$allposts}
	{/if}

    {include file='module:xenforum/views/templates/front/xf_footer.tpl'}
</div>
{/block}
