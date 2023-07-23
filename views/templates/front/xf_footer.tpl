{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

	<p class="muted alignRight alertFooter">
		{if !$id_online}
			<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Log in | Sign up' mod='xenforum'}</a><br />
		{/if}
		<a href="{$link->getPageLink('xenforum_term')|escape:'htmlall':'UTF-8'}"><i class="icon icon-legal"></i> {l s='Tems & Conditions!' mod='xenforum'}</a><br />
		<a href="{$link->getPageLink('xenforum_help')|escape:'htmlall':'UTF-8'}"><i class="icon icon-life-ring"></i> {l s='Help!' mod='xenforum'}</a>
	</p>
