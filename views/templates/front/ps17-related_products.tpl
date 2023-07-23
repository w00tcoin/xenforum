{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<div id="products" class="products row">
{foreach from=$products item="product"}
	{include file='module:xenforum/views/templates/front/miniatures/product.tpl' product=$product}
{/foreach}
</div>