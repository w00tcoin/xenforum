{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<!-- Show forums and topics -->
{if (($category) && (!isset($id_xenforum_cat) || !empty($category.child_categories)))}
        {assign var="catOptions" value=null}
            {$catOptions.id_xenforum_cat = $category.id_xenforum_cat}
            {$catOptions.slug = $category.link_rewrite|escape:'htmlall':'UTF-8'}
		<div class="post_loop node">
            {if (!isset($id_xenforum_cat))}
			<div class="post_header">
                <div class="post_title">
					<i class="icon icon-chevron-sign-right"></i>
					<a title="{$category.meta_title|escape:'html':'UTF-8'}" href="{$xenforum->GetLink('xenforum_category',$catOptions)|escape:'htmlall':'UTF-8'}">
						{$category.meta_title|escape:'htmlall':'UTF-8'}
					</a>
                </div>
                {if (!$category.closed)}
                    {if ($user->checkPerm($id_online, '_create_topic_', $category.id_xenforum_cat))}
                            <div class="topCtrl">
                                <a href="{$xenforum->GetLink('xenforum_topic_create',$catOptions)|escape:'htmlall':'UTF-8'}" class="notif topCreate">
                                    <span><i class="icon icon-plus"></i>&nbsp;{l s='New Topic' mod='xenforum'}</span>
                                </a>
                            </div>
                    {/if}
                {/if}
			</div>
            {/if}

			{if (!empty($category.child_categories))}
				<ol class="post_child_loop nodeList">
				{foreach from=$category.child_categories item=ch_category}
				{assign var="catOptions" value=null}
					{$catOptions.id_xenforum_cat = $ch_category.id_xenforum_cat}
					{$catOptions.slug = $ch_category.link_rewrite|escape:'htmlall':'UTF-8'}
					<li class="categoryForum">
					<div class="nodeInfo forumNodeInfo primaryContent unread">
						<span class="nodeIcon" title="Unread messages"></span>
						<div class="nodeText">
								<div class="nodeTitle"><a title="{$ch_category.meta_title|escape:'html':'UTF-8'}" href="{$xenforum->GetLink('xenforum_category',$catOptions)|escape:'htmlall':'UTF-8'}">
									{$ch_category.meta_title|escape:'html':'UTF-8'}
								</a></div>
								<div class="nodeStats pairsInline">
									<dl><dt>{l s='Discussions:' mod='xenforum'}</dt> <dd>{$ch_category.total|escape:'htmlall':'UTF-8'}</dd></dl>
                                    <dl><dt>{l s='Messages:' mod='xenforum'}</dt> <dd>{$ch_category.total_comment|escape:'htmlall':'UTF-8'}</dd></dl>
								</div>
									
						</div>
						<div class="nodeLastPost">
							{if ($ch_category.last_post)}
								{assign var="AutOptions" value=null}
									{$AutOptions.id_customer = md5($ch_category.last_post['id_author'])}
								<div class="lastPostCell">
									<span class="avatarHome">
										<a href="{$xenforum->GetLink('xenforum_member', $AutOptions)|escape:'htmlall':'UTF-8'}" class="avatar Av1s" data-avatarhtml="true"><img src="{$xenforum->checkAvatar('default_small', $ch_category.last_post['id_author'])|escape:'htmlall':'UTF-8'}" width="48" height="48" alt=""></a>
									</span>
								</div>

								<div class="lastPostCell">
									{assign var="options" value=null}
										{$options.id_xenforum = $ch_category.last_post['id_xenforum']}
										{$options.slug = $ch_category.last_post['link_rewrite']|escape:'htmlall':'UTF-8'}
									<span class="lastThreadTitle"><span>{l s='Latest:' mod='xenforum'}</span> <a href="{$xenforum->GetLink('xenforum_viewdetails',$options)|escape:'htmlall':'UTF-8'}" title="">{$ch_category.last_post['meta_title']|escape:'htmlall':'UTF-8'}</a></span>
									<span class="lastThreadMeta">
										<span class="lastThreadUser"><a href="{$xenforum->GetLink('xenforum_member', $AutOptions)|escape:'htmlall':'UTF-8'}" class="username" dir="auto">{$ch_category.last_post['nickname']|escape:'htmlall':'UTF-8'}</a>,</span>
										<abbr class="timeago muted lastThreadDate" title="{$ch_category.last_post['filter_created']|escape:'htmlall':'UTF-8'}">{$ch_category.last_post['created']|escape:'htmlall':'UTF-8'}</abbr>
									</span>
								</div>
							{else}
								<p class="noMessages muted">&nbsp;&nbsp;({l s='Contains no message' mod='xenforum'})</p>
							{/if}
						</div>
					</div>
					<li>
				{/foreach}
				</ol>
			{/if}
		</div>
{/if}