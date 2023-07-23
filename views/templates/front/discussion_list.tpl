{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<!-- Show discussion list -->
	<div class="post_loop discussionList">
		<dl class="sectionHeaders">
			<dt class="posterAvatar"><a><span>{l s='Sort by:' mod='xenforum'}</span></a></dt>
			<dd class="main">
				<a class="title"><span>{l s='Title' mod='xenforum'}</span></a>
				<a class="postDate"><span>{l s='Start Date' mod='xenforum'}</span></a>
			</dd>
			<dd class="stats">
				<a class="major"><span>{l s='Replies' mod='xenforum'}</span></a>
				<a class="minor"><span>{l s='Views' mod='xenforum'}</span></a>
			</dd>
			<dd class="lastPost"><a><span>{l s='Last Message ↓' mod='xenforum'}</span></a></dd>
		</dl>

		<ol class="discussionListItems">
			{foreach from=$allposts item=post}
				{assign var="options" value=null}
					{$options.id_xenforum = $post.id_xenforum}
					{$options.slug = $post.link_rewrite|escape:'htmlall':'UTF-8'}

				<li id="topic-{$post.id_xenforum|escape:'htmlall':'UTF-8'}" class="discussionListItem visible {if ($post.closed)} locked{/if}{if ($post.highlight)} unread{/if}">
					<div class="listBlock posterAvatar">
						<span class="avatarContainer">
							<a href="" class="avatar Av1s" data-avatarhtml="true"><img src="{$xenforum->checkAvatar('default_small', $post.id_author)|escape:'htmlall':'UTF-8'}" width="48" height="48" alt=""></a>
						</span>
					</div>

					<div class="listBlock main">
						<div class="titleText">
							{if ($post.closed)}
								<div class="iconKey">
									<span class="locked" title="Locked">Locked</span>
								</div>
							{/if}
							<div class="title">
								<a title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$xenforum->GetLink('xenforum_viewdetails',$options)|escape:'htmlall':'UTF-8'}">
									{$post.meta_title|escape:'htmlall':'UTF-8'}
								</a>
							</div>
							
							<div class="secondRow">
								<div class="posterDate muted">
										{assign var="MemOptions" value=null}
											{$MemOptions.id_customer = md5($post.id_author)}
									<a href="{$xenforum->GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" class="username" dir="auto" title="Topic starter">{$post.nickname|escape:'html':'UTF-8'}</a><span class="startDate">,
									<a class="faint"><abbr class="timeago" title="{$post.filter_created|escape:'htmlall':'UTF-8'}">{$post.created|escape:'htmlall':'UTF-8'}</abbr></a></span>
								</div>

								<div class="controls faint">
									{assign var="Newoptions" value=null}
										{$Newoptions.id_xenforum = $post.id_xenforum}
									
									{if ($user->checkPerm($id_online, '_edit_all_') || (($id_online == $post.id_author) && $user->checkPerm($id_online, '_edit_mine_')))}
										<a href="{$xenforum->GetLink('xenforum_topic_delete',$Newoptions)|escape:'htmlall':'UTF-8'}" class="EditControl deleteTopic" data-confirm="{l s='Are you sure to delete this item?' mod='xenforum'}">{l s='Delete' mod='xenforum'}</a>&nbsp;&nbsp;
									{/if}
									{if ($user->checkPerm($id_online, '_delete_all_') || (($id_online == $post.id_author) && $user->checkPerm($id_online, '_delete_mine_')))}
										<a href="{$xenforum->GetLink('xenforum_topic_edit',$Newoptions)|escape:'htmlall':'UTF-8'}" class="EditControl">{l s='Edit' mod='xenforum'}</a> 
									{/if}
								</div>
							</div>
						</div>
					</div>

					<div class="listBlock stats pairsJustified">
						<dl class="major"><dt>{l s='Replies:' mod='xenforum'}</dt> <dd>{$post.replies|escape:'htmlall':'UTF-8'}</dd></dl>
						<dl class="minor"><dt>{l s='Views:' mod='xenforum'}</dt> <dd>{$post.viewed|escape:'htmlall':'UTF-8'}</dd></dl>
					</div>

					<div class="listBlock lastPost">
						<dl class="lastPostInfo">
						{if isset($post.mess_id_post)}
									{assign var="AutOptions" value=null}
										{$AutOptions.id_customer = md5($post.mess_id_author)}
							<dt><a href="{xenforum::GetLink('xenforum_member', $AutOptions)|escape:'htmlall':'UTF-8'}" class="username" dir="auto">{$post.mess_author|escape:'htmlall':'UTF-8'}</a></dt>
							<dd class="muted"><abbr class="timeago" title="{$post.filter_mess_created|escape:'htmlall':'UTF-8'}">{$post.mess_created|escape:'htmlall':'UTF-8'}</abbr></dd>
						{else}
							<div class="noMessages muted">({l s='Empty' mod='xenforum'})</div>
						{/if}
						</dl>
					</div>
				</li>
			{/foreach}
		</ol>
	</div>	
	
    {if !empty($page_nums)}
	<div class="post_paging">
		<div class="sumary"><div class="results"> {l s='Page' mod='xenforum'} {$curent|escape:'htmlall':'UTF-8'} / {$total_pages|escape:'htmlall':'UTF-8'}</div></div>
        <ul>
            {assign var="options" value=null}
            {$options.page = 1}
            {$options.id_xenforum_cat = $id_xenforum_cat}
            {$options.slug = $category.link_rewrite|escape:'htmlall':'UTF-8'}
            {if (1 != $curent)}
                <li><a class="page-link" href="{$xenforum->GetLink('xenforum_category_pagination',$options)|escape:'htmlall':'UTF-8'}"><span class="page-active">{l s='« First' mod='xenforum'}</span></a></li>
            {else}
                <li><span class="page-disabled">{l s='« First' mod='xenforum'}</span></li>
            {/if}
            <li>...</li>

            {for $k=$start to ($start+$vision)}
                {assign var="options" value=null}
                {$options.page = $k}
                {$options.id_xenforum_cat = $id_xenforum_cat}
                {$options.slug = $category.link_rewrite|escape:'htmlall':'UTF-8'}
                {if $k == $curent}
                    <li><span class="page-current">{$k|escape:'htmlall':'UTF-8'}</span></li>
                {else}
                    <li><a class="page-link" href="{$xenforum->GetLink('xenforum_category_pagination',$options)|escape:'htmlall':'UTF-8'}"><span class="page-active">{$k|escape:'htmlall':'UTF-8'}</span></a></li>
                {/if}
            {/for}
            
            {assign var="options" value=null}
            {$options.page = $total_pages}
            {$options.id_xenforum_cat = $id_xenforum_cat}
            {$options.slug = $category.link_rewrite|escape:'htmlall':'UTF-8'}
            <li>...</li>
            {if ($total_pages != $curent)}
                <li><a class="page-link" href="{$xenforum->GetLink('xenforum_category_pagination',$options)|escape:'htmlall':'UTF-8'}"><span class="page-active">{l s='Last »' mod='xenforum'}</span></a></li>
            {else}
                <li><span class="page-disabled">{l s='Last »' mod='xenforum'}</span></li>
            {/if}
		</ul>
	</div>
	{/if}
<!--// Show discussion list -->