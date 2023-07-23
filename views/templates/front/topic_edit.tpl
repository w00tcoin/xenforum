{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
<a href="{$xenforum->GetLink($page_url)|escape:'htmlall':'UTF-8'}">{$page_title|escape:'htmlall':'UTF-8'}</a>
{foreach from=$nested_categories item=nested}
    {assign var="catOptions" value=null}
        {$catOptions.id_xenforum_cat = $nested.id_xenforum_cat}
        {$catOptions.slug = $nested.link_rewrite}
    &nbsp;&nbsp;&nbsp;
    <a href="{$xenforum->GetLink('xenforum_category',$catOptions)|escape:'htmlall':'UTF-8'}">{$nested.meta_title|escape:'htmlall':'UTF-8'}</a>
{/foreach}

{if ($id_xenforum)}
    {assign var="options" value=null}
        {$options.id_xenforum = $id_xenforum|escape:'htmlall':'UTF-8'}
        {$options.slug = $link_rewrite|escape:'htmlall':'UTF-8'}
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$xenforum->GetLink('xenforum_viewdetails', $options)|escape:'htmlall':'UTF-8'}">{$meta_title|escape:'htmlall':'UTF-8'}</a>
{/if}
<span class="navigation-pipe"></span>{l s='Edit' mod='xenforum'}
{/capture}

<div id="blogview" class="blogview">
{if (!$id_xenforum)}
	<p class="error">{l s='Error!' mod='xenforum'}</p>
{elseif (!$id_online)}
	<p class="error">({l s='Please log in or sign up!' mod='xenforum'})</p>
{elseif ($user->checkPerm($id_online, '_edit_all_') || (($id_online == $id_author) && $user->checkPerm($id_online, '_edit_mine_')))}
    {if !empty($errors)}
    <p class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button>
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

			<dl class="ctrlUnit fullWidth surplusLabel">
				<dt><label for="ctrl_title_topic_create">{l s='Topic title:' mod='xenforum'}</label></dt>
				<dd><input type="text" tabindex="1" class="inputName form-control grey copyTitle2friendlyURL" value="{$title|escape:'htmlall':'UTF-8'}" id="meta_title" name="meta_title" placeholder="{l s='Thread Title...' mod='xenforum'}" maxlength="125" autofocus="true" >
				</dd>
			</dl>
			<dl class="ctrlUnit fullWidth surplusLabel" {if !($user->checkPerm($id_online, '_edit_link_rewrite_'))}style="display:none;"{/if}>
				<dt><label for="ctrl_link_topic_create">{l s='Link rewrite:' mod='xenforum'}</label></dt>
				<dd><input type="text" id="link_rewrite" name="link_rewrite" value="{$link_rewrite|escape:'htmlall':'UTF-8'}" placeholder="{l s='Link rewrite...' mod='xenforum'}" class="inputName form-control">
				</dd>
			</dl>

            {if ($user->checkPerm($id_online, '_edit_related_products_'))}
            <dl class="ctrlUnit fullWidth surplusLabel">
                <dt><label for="ctrl_related_product">{l s='Related products:' mod='xenforum'}</label></dt>
                <dd><input type="text" id="ctrl_related_product" name="products" value="{$products|escape:'htmlall':'UTF-8'}" placeholder="{l s='ID of related products...' mod='xenforum'}" class="inputName form-control">
                    <p class="hint">{l s='Use commas to separate ids.' mod='xenforum'}</p>
                </dd>
            </dl>
            {/if}

			<dl class="ctrlUnit fullWidth surplusLabel">
				<dt><label for="ctrl_tags">{l s='Tags:' mod='xenforum'}</label></dt>
				<dd><input type="text" tabindex="3" class="inputName form-control grey" value="{$tags|escape:'htmlall':'UTF-8'}" id="tags" name="tags" placeholder="{l s='Tags...' mod='xenforum'}" maxlength="125">
				<p class="hint">{l s='Use commas to separate items.' mod='xenforum'}</p>
				</dd>
			</dl>

		
			<div class="green_button center">
				<div class="submit">
					<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
					<button type="submit" name="edit_post" id="add_new_post" class="btn btn-default button button-small">
						<span>{l s='SAVE' mod='xenforum'}<i class="icon-chevron-right right"></i></span>
					</button>
				</div>
			</div>
		</fieldset>
		
        {if ($user->checkPerm($id_online, '_close_topic_') || $user->checkPerm($id_online, '_highlight_topic_'))}
		<fieldset>			
		<dl class="twoColumn">
			<dt><label>{l s='Set Status:' mod='xenforum'}</label></dt>
			<dd>
                <ul>
                    {if ($user->checkPerm($id_online, '_close_topic_'))}
					<li>
						<label for="ctrl_discussion_open"><span><input type="checkbox" name="closed" value="1" id="closed" {if ($closed)}checked="checked"{/if}/></span> <span>{l s='Closed' mod='xenforum'}</span></label>
						<p class="hint">{l s='People may reply to this topic' mod='xenforum'}</p>
                    </li>
                    {/if}
                    {if ($user->checkPerm($id_online, '_highlight_topic_'))}
					<li>
						<label for="ctrl_sticky"><span><input type="checkbox" name="highlight" value="1" id="ctrl_sticky" {if ($highlight)}checked="checked"{/if}/></span> <span>{l s='Highlight' mod='xenforum'}</span></label>
						<p class="hint">{l s='Highlight topics appear at the top of the first page of the list of topics in their parent forum' mod='xenforum'}</p>
                    </li>
                    {/if}
				</ul>
			</dd>
		</dl>
        </fieldset>
        {/if}
		
		</form>
	</div>
	</div>
{/if}

	{include file="./xf_footer.tpl"}
</div>