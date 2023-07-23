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
	<div class="post_loop">
        <div class="post_header">
		      <div class="post_title">
				<i class="icon-chevron-sign-right"></i>
						{$topic.meta_title|stripslashes|escape:'htmlall':'UTF-8'}
		      </div>
		</div>
		<div class="post_rcolumn">
			<div class="post_desc">
				{if ($id_xenforum_cat)}
					{assign var="catOptions" value=null}
						{$catOptions.id_xenforum_cat = $id_xenforum_cat}
						{$catOptions.slug = $cat_link_rewrite}
					<i class="icon icon-folder"></i>&nbsp;<a href="{$xenforum->GetLink('xenforum_category',$catOptions)|escape:'htmlall':'UTF-8'}">{$title_category|escape:'htmlall':'UTF-8'}</a>&nbsp;
				{/if}
				{if ($topic.nickname)}
					{assign var="AutOptions" value=null}
						{$AutOptions.id_customer = md5($topic.id_author)}
					<i class="icon icon-user"></i>&nbsp;{l s='Started by' mod='xenforum'} <a href="{$xenforum->GetLink('xenforum_member', $AutOptions)|escape:'htmlall':'UTF-8'}"><span>{$topic.nickname|escape:'html':'UTF-8'}</span></a>&nbsp;
				{/if}
				<i class="icon icon-time"></i>&nbsp;<abbr class="timeago" title="{$filter_created|escape:'htmlall':'UTF-8'}">{$created|escape:'htmlall':'UTF-8'}</abbr>&nbsp;
				<i class="icon icon-comments"></i>&nbsp;{l s='Comments:' mod='xenforum'} {$total_comments|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;
				{if ($topic.viewed)}
					<i class="icon icon-eye-open"></i>&nbsp;{l s='Viewed:' mod='xenforum'} {$topic.viewed|escape:'htmlall':'UTF-8'}
				{/if}

				{assign var="Newoptions" value=null}
					{$Newoptions.id_xenforum = $id_xenforum}

					{if ($user->checkPerm($id_online, '_edit_all_') || (($id_online == $topic.id_author) && $user->checkPerm($id_online, '_edit_mine_')))}
						&nbsp;&nbsp;<a href="{$xenforum->GetLink('xenforum_topic_edit',$Newoptions)|escape:'htmlall':'UTF-8'}" class="EditControl">{l s='Edit' mod='xenforum'}</a> 
					{/if}
					{if ($user->checkPerm($id_online, '_delete_all_') || (($id_online == $topic.id_author) && $user->checkPerm($id_online, '_delete_mine_')))}
						&nbsp;&nbsp;<a href="{$xenforum->GetLink('xenforum_topic_delete',$Newoptions)|escape:'htmlall':'UTF-8'}" class="EditControl deleteTopic" data-confirm="{l s='Are you sure to delete this item?' mod='xenforum'}">{l s='Delete' mod='xenforum'}</a>&nbsp;
					{/if}
			</div>

			{if ($all_tags != '')}
			<div class="post_tags">
				<i class="icon icon-tags"></i>{l s='Tags:' mod='xenforum'}
				{assign var="i" value=1}
				{foreach from=$all_tags item=tag}
					{assign var="options" value=null}
						{$options.tags = $tag|replace:' ':'+'|escape:'htmlall':'UTF-8'}
					<a title="{$tag|escape:'htmlall':'UTF-8'}" href="{$xenforum->GetLink('xenforum_tags',$options)}"><span class="notif bggreen">{$tag|escape:'html':'UTF-8'}</span></a>{if $i < $count_tags}&nbsp;{/if}{assign var="i" value=$i+1}
				{/foreach}
			</div>
			{/if}

			<div class="post_sharing">
				<p class="socialsharing_product list-inline no-print">
					<button data-type="twitter" type="button" class="btn btn-default btn-twitter">
						<i class="icon-twitter"></i> {l s='Tweet' mod='xenforum'}
					</button>
					<button data-type="facebook" type="button" class="btn btn-default btn-facebook">
						<i class="icon-facebook"></i> {l s='Share' mod='xenforum'}
					</button>
					<button data-type="google-plus" type="button" class="btn btn-default btn-google-plus">
						<i class="icon-google-plus"></i> {l s='Google+' mod='xenforum'}
					</button>
				</p>
			</div>			

            {include file='module:xenforum/views/templates/front/ps17-comment.tpl'}
		</div>
	</div>

	{if ($all_tags != '')}
	<div class="post_tags">
		{l s='Tags:' mod='xenforum'}
		{assign var="i" value=1}
		{foreach from=$all_tags item=tag}
			{assign var="options" value=null}
				{$options.tags = $tag|replace:' ':'+'|escape:'htmlall':'UTF-8'}
			<a title="{$tag|escape:'html':'UTF-8'}" href="{$xenforum->GetLink('xenforum_tags',$options)}"><span class="notif txtgray"><i class="icon icon-tags"></i>&nbsp;{$tag|escape:'html':'UTF-8'}</span></a>&nbsp;
		{/foreach}
	</div>
	{/if}

    {include file='module:xenforum/views/templates/front/xf_footer.tpl'}
</div>
<script type="text/javascript">
{literal}
	sharing_name = '{/literal}{$sharing_name|escape:"htmlall":"UTF-8"}{literal}';
	sharing_url = '{/literal}{$sharing_url|escape:"htmlall":"UTF-8"}{literal}';
	sharing_img = '{/literal}{$sharing_img|escape:"htmlall":"UTF-8"}{literal}';
{/literal}
</script>
{/block}