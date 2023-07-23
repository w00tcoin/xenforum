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
<span class="navigation-pipe"></span>{l s='Create topic' mod='xenforum'}
{/capture}

<div id="blogview" class="blogview">
{if !($id_xenforum_cat)}
	<p class="error">{l s='Error!' mod='xenforum'}</p>
{elseif (!$id_online)}
	<p class="error">({l s='Please log in or sign up!' mod='xenforum'})</p>
{elseif ((!$closed) && $user->checkPerm($id_online, '_create_topic_', $id_xenforum_cat))}
	{if isset($alreadySent)}
		<p class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>
			{l s='Your topic has been successfully sent.' mod='xenforum'}
			<a href="{$alreadySent|escape:'htmlall':'UTF-8'}">{l s='Read now' mod='xenforum'} <i class="icon icon-external-link"></i></a></p>
	{else}
		{if !empty($errors)}
        <p class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button>
            {foreach from=$errors item=error}
            {$error|escape:'htmlall':'UTF-8'}<br/>
            {/foreach}
        </p>
		{/if}
	{/if}

	<div class='blog_title'>{l s='Create topic' mod='xenforum'}&nbsp;&nbsp;<span id="createTopic"></span></div>

	<div class="discussionList sectionMain">
	<div class="post_rcolumn">
		<form action="" method="post" id="psform" class="xenForm">
		<fieldset>
			<dl class="ctrlUnit fullWidth surplusLabel">
				<dt><label for="ctrl_title_topic_create"></label></dt>
                <dd><input type="text" tabindex="1" class="inputName form-control grey copyTitle2friendlyURL" id="meta_title" name="meta_title" value="{$title|escape:'htmlall':'UTF-8'}" placeholder="{l s='Topic Title...' mod='xenforum'}" maxlength="125" autofocus="true" >
				</dd>
			</dl>

            {include file="./post_form.tpl"}
		</fieldset>

        <fieldset>
            <dl class="ctrlUnit fullWidth surplusLabel" {if !($user->checkPerm($id_online, '_edit_link_rewrite_'))}style="display:none;"{/if}>
                <!-- <dt><label for="ctrl_link_topic_rewrite"></label></dt>-->
                <dd><input type="text" id="link_rewrite" name="link_rewrite" value="{$link_rewrite|escape:'htmlall':'UTF-8'}" placeholder="{l s='Link rewrite...' mod='xenforum'}" class="inputName form-control">
                </dd>
            </dl>
            
            {if ($user->checkPerm($id_online, '_edit_related_products_'))}
            <dl class="ctrlUnit fullWidth surplusLabel">
                <!-- <dt><label for="ctrl_related_product"></label></dt>-->
                <dd><input type="text" id="ctrl_related_product" name="products" value="" placeholder="{l s='ID of related products...' mod='xenforum'}" class="inputName form-control">
                    <p class="hint">{l s='Use commas to separate ids.' mod='xenforum'}</p>
                </dd>
            </dl>
            {/if}
                
			<dl class="ctrlUnit fullWidth surplusLabel">
                <!-- <dt><label for="ctrl_tags"></label></dt>-->
                <dd><input type="text" tabindex="3" class="inputName form-control grey" value="{$tags|escape:'htmlall':'UTF-8'}" id="tags" name="tags" placeholder="{l s='Tags...' mod='xenforum'}" maxlength="125">
				<p class="hint">{l s='Use commas to separate items.' mod='xenforum'}</p>
				</dd>
            </dl>
		</fieldset>
		
        {if ($user->checkPerm($id_online, '_close_topic_') || $user->checkPerm($id_online, '_highlight_topic_'))}
		<fieldset>
		<dl class="twoColumn">
			<dt><label>{l s='Set Status:' mod='xenforum'}</label></dt>
			<dd>
                <ul>
                    {if ($user->checkPerm($id_online, '_close_topic_'))}
					<li>
						<label for="ctrl_discussion_open"><span><input type="checkbox" name="closed" value="1" id="closed" /></span> <span>{l s='Closed' mod='xenforum'}</span></label>
						<p class="hint">{l s='People may reply to this topic' mod='xenforum'}</p>
                    </li>
                    {/if}
                    {if ($user->checkPerm($id_online, '_highlight_topic_'))}
					<li>
						<label for="ctrl_sticky"><span><input type="checkbox" name="highlight" value="1" id="ctrl_sticky" /></span> <span>{l s='Highlight' mod='xenforum'}</span></label>
						<p class="hint">{l s='Highlight topics appear at the top of the first page of the list of topics in their parent forum' mod='xenforum'}</p>
                    </li>
                    {/if}
				</ul>
			</dd>
		</dl>
        </fieldset>
        {/if}
		<!-- slot: after_options -->

		</form>
	</div>
	</div>
{/if}

	{include file="./xf_footer.tpl"}
</div>