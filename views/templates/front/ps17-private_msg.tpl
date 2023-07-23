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
	<p class="error">
		{if !$id_online}
			{l s='Private Area! Please' mod='xenforum'}
			<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Log in' mod='xenforum'}</a>
		{else}
			({l s='Private Area! You need more permission to access.' mod='xenforum'})
		{/if}
	</p>
    {include file='module:xenforum/views/templates/front/xf_footer.tpl'}
</div>
{/block}