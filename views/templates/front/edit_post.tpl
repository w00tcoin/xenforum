{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
		<a href="{$xenforum->GetLink($page_url)|escape:'htmlall':'UTF-8'}">{$page_title|escape:'htmlall':'UTF-8'}</a>
		{if ($id_xenforum)}
			{assign var="options" value=null}
				{$options.id_xenforum = $id_xenforum|escape:'htmlall':'UTF-8'}
				{$options.slug = $link_rewrite|escape:'htmlall':'UTF-8'}
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$xenforum->GetLink('xenforum_viewdetails', $options)|escape:'htmlall':'UTF-8'}">{$meta_title|escape:'htmlall':'UTF-8'}</a>
	{/if}
		<span class="navigation-pipe"></span>{l s='Edit' mod='xenforum'}
{/capture}

<div id="blogview" class="blogview">
{if !($id_xenforum_post)}
	<p class="error">{l s='Error!' mod='xenforum'}</p>
{elseif (!$id_customer)}
	<p class="error">({l s='Please log in or sign up!' mod='xenforum'})</p>
{elseif ($user->checkPerm($id_customer, '_edit_all_') || (($id_customer == $id_author) && $user->checkPerm($id_customer, '_edit_mine_')))}
    {if !empty($errors)}
    <p class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {foreach from=$errors item=error}
        {$error|escape:'htmlall':'UTF-8'}<br/>
        {/foreach}
    </p>
    {/if}

	<div class='blog_title'>{l s='Edit Post by' mod='xenforum'}&nbsp;{$nickname|escape:'htmlall':'UTF-8'}</div>

	<div class="discussionList sectionMain">
	<div class="post_rcolumn">
		<form action="" method="post" id="psform" class="xenForm">
		<fieldset>

            {include file="./post_form.tpl"}
		</fieldset>
		
		<fieldset>			
		<dl class="twoColumn">
			<dt><label>{l s='Set Status:' mod='xenforum'}</label></dt>
			<dd>
				<ul>
					<li>
						<label for="ctrl_post_active"><span><input type="checkbox" name="active" value="1" id="active" {if ($active)}checked="checked"{/if}/></span> <span>{l s='Active' mod='xenforum'}</span></label>
						<p class="hint">{l s='Active this mesage' mod='xenforum'}</p>
					</li>
				</ul>
			</dd>
		</dl>
		</fieldset>	
		</form>
	</div>
	</div>
{/if}

	{include file="./xf_footer.tpl"}
</div>
