{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<div id="xenforum_notif_wrapper" style="display:none;">
	<li id="forum_messages_notif" class="dropdown" data-type="customer_message">
		<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
			<i class="icon-comments"></i>
			<span id="forum_messages_notif_number_wrapper" class="notifs_badge{if (!$total_notif)} hide{/if}">
				<span id="forum_messages_notif_value" >{$total_notif|escape:'htmlall':'UTF-8'}</span>
			</span>
		</a>
		<div class="dropdown-menu notifs_dropdown">
			<section id="forum_messages_notif_wrapper" class="notifs_panel">
				<div class="notifs_panel_header">
					{if ($total_notif)}
						&nbsp;&nbsp;
                        <span class="notif bgred" title="{l s='Numbers of hidden topics | comments and unapproved users' mod='xenforum'}">
							{l s='hidden:' mod='xenforum'} {$hidden_post|escape:'htmlall':'UTF-8'} {l s='topics' mod='xenforum'} | {$hidden_com|escape:'htmlall':'UTF-8'} {l s='comments' mod='xenforum'} | {$hidden_user|escape:'htmlall':'UTF-8'} {l s='users' mod='xenforum'}
						</span>{/if}
					<h3>{l s='Forums:: Latest Comments' mod='xenforum'}</h3>
				</div>
				<div id="list_forum_messages_notif" class="list_notif">

					{if $all_latest == ''}
						<span class="no_notifs">
							{l s='No new comments have been posted on your forums.' mod='xenforum'}
						</span>
					{else}
						{foreach from=$all_latest item=post name=myLoop}
							{assign var="options" value=null}
								{$options.id_xenforum = $post.id_xenforum|escape:'htmlall':'UTF-8'}
								{$options.slug = $post.link_rewrite|escape:'htmlall':'UTF-8'}
							{assign var="MemOptions" value=null}
								{$MemOptions.id_customer = $post.id_author}
								<div class="comments-list">
									{$post.comment|strip_tags|trim|truncate:100:'...'|escape:'htmlall':'UTF-8'}
									<br/>
								</div>
									<a class="staticColor" target="_blank" style="padding-left:10px;" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$xenforum->GetLink('xenforum_viewdetails',$options)|escape:'htmlall':'UTF-8'}">
										{$post.nickname|escape:'htmlall':'UTF-8'}, <span class="muted"><abbr class="timeago" title="{$post.filter_created|escape:'htmlall':'UTF-8'}">{$post.created|escape:'htmlall':'UTF-8'}</abbr></span>
									</a>
								
						{/foreach}
					{/if}
				</div>
				<div class="notifs_panel_footer"> 
					<a href="{$link_to_setting|escape:'html':'UTF-8'}">{l s='Settings' mod='xenforum'}</a> |
					<a href="{$link_to_post|escape:'html':'UTF-8'}">{l s='Comments Management' mod='xenforum'}</a> |
					<a href="{$link_to_forum|escape:'html':'UTF-8'}" target="_blank">{l s='Forums' mod='xenforum'}</a>
				</div>
			</section>
		</div>
	</li>
</div>
<style>
{literal}
	.muted {font-size:.9em;color:#999;}
	.comments-list {padding:0 10px;}
		.notif {color:white;font-size:.85em;font-weight:500;padding:1px 5px;background:black;border-radius:15px;}
		.red {color:red;} .blue {color:blue;}
		.bgred {background:red;color:white;}
		.bgblue {background:blue;color:white;}
		.bggray {background:gray;color:white;}
{/literal}
</style>
<script type="text/javascript">
{literal}
	$(document).ready(function(){
		$('#header_notifs_icon_wrapper').append($(xenforum_notif_wrapper).html());

	});
{/literal}
</script>
