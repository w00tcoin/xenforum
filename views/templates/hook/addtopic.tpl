{**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{if (isset($id_online) && $user->checkPerm($id_online, '_create_topic_'))}
<div class="block sidebar block-my-account">
		<div class="loginButton">
			<label for="LoginControl"><a href="#add_topic" class="open-add-topic myButton">{l s='New Topic' mod='xenforum'}</a></label>
		</div>
</div>

<!-- Fancybox -->
<div style="display: none;">
	<div id="add_topic">
    {if $new_allforums == ''}
        <p class="error">({l s='No forums!' mod='xenforum'})</p>
    {elseif ($private && (!$user->checkPerm($id_online, '_view_all_')))}
        <p class="error">({l s='Private Forums! You need more permission to access.' mod='xenforum'})</p>
    {else}
        {foreach from=$new_allforums item=category}
        {assign var="catOptions" value=null}
            {$catOptions.id_xenforum_cat = $category.id_xenforum_cat}
            {$catOptions.slug = $category.link_rewrite|escape:'htmlall':'UTF-8'}
            <div class="post_loop node">
                <div class="post_header">
                    <div class="post_title">
                        <i class="icon icon-chevron-sign-right"></i>
                        <a title="{$category.meta_title|escape:'html':'UTF-8'}" href="{$xenforum->GetLink('xenforum_category',$catOptions)|escape:'htmlall':'UTF-8'}">
                            {$category.meta_title|escape:'html':'UTF-8'}
                        </a>
                    </div>
                    {if (!$category.closed && $user->checkPerm($id_online, '_create_topic_', $category.id_xenforum_cat))}
                        <div class="topCtrl">
                            <a href="{$xenforum->GetLink('xenforum_topic_create',$catOptions)|escape:'htmlall':'UTF-8'}" class="notif topCreate">
                                <span><i class="icon icon-plus"></i>&nbsp;{l s='New Topic' mod='xenforum'}</span>
                            </a>
                        </div>
                    {/if}
                </div>

                {if $category.child_categories != ''}
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
                            <div class="nodeLastPost" style="text-align: right;width: 150px;border:0;background:none;">
                                {if ($ch_category.closed)}
                                    <p class="muted">(<i class="icon-lock"></i> {l s='Locked' mod='xenforum'})</p>
                                {else}
                                    {if ($user->checkPerm($id_online, '_create_topic_', $ch_category.id_xenforum_cat))}
                                        <p class="noMessages">&nbsp;
                                            <a href="{$xenforum->GetLink('xenforum_topic_create',$catOptions)|escape:'htmlall':'UTF-8'}" class="notif topCreate">
                                                <span><i class="icon icon-plus"></i>&nbsp;{l s='New Topic' mod='xenforum'}</span>
                                            </a>
                                        </p>
                                   {/if}
                                {/if}
                            </div>
                        </div>
                        <li>
                    {/foreach}
                    </ol>
                {/if}
            </div>
        {/foreach}
    {/if}

	</div>
</div>
<!-- End fancybox -->
{/if}