{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
		{$page_title|escape:'htmlall':'UTF-8'}
{/capture}

<div id="blogview" class="blogview">
	<p class="error">
		{if !$id_online}
			{l s='Private Area! Please' mod='xenforum'}
			<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Log in' mod='xenforum'}</a>
		{else}
			({l s='Private Area! You need more permission to access.' mod='xenforum'})
		{/if}
	</p>
	{include file="./xf_footer.tpl"}
</div>