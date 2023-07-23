{**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<!-- Block XENFORUM -->
<div id="xenforum_block_right" class="block forum_block sidebar">
	<div class="block_content list secondaryContent">
	<h3>{l s='Latest Posts' mod='xenforum'}</h3>
	{if $all_latest == ''}
		<p>({l s='No content here!' mod='xenforum'})</p>
	{else}
		<ol class="clearfix">
		{foreach from=$all_latest item=post name=myLoop}
			{assign var="options" value=null}
				{$options.id_xenforum = $post.id_xenforum}
				{$options.slug = $post.link_rewrite|escape:'htmlall':'UTF-8'}
				<li>
					<a title="{$post.meta_title|escape:'html':'UTF-8'}" href="{$xenforum->GetLink('xenforum_viewdetails',$options)|escape:'htmlall':'UTF-8'}">
						{$post.meta_title|trim|truncate:60:'...'|escape:'html':'UTF-8'}
					</a><br/>
					<span>{$post.comment|strip_tags|trim|truncate:150:'...'|escape:'html':'UTF-8'}</span>
					<div class="bottomMessages">
						<abbr class="timeago" title="{$post.filter_created|escape:'html':'UTF-8'}">{$post.created|escape:'html':'UTF-8'}</abbr>,
						{assign var="Options" value=null}
							{$Options.id_customer = md5($post.id_author)}
						<a href="{$xenforum->GetLink('xenforum_member', $Options)|escape:'htmlall':'UTF-8'}" title="{$post.nickname|escape:'htmlall':'UTF-8'}">
							{$post.nickname|escape:'htmlall':'UTF-8'}
						</a>
					</div>
				</li>
		{/foreach}
		</ol>
	{/if}
	</div>
</div>
<!-- Block XENFORUM -->