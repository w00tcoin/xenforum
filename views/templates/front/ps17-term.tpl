{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{extends file='module:xenforum/views/templates/front/ps7-layout.tpl'}

{block name='page_content'}
<div id="blogview" class="blogview">

	<div class="blog_title">{l s='Terms of Service and Rules' mod='xenforum'}</div>
{if $term_content != ''}
	<div class="helpContent">
			{$term_content|html_entity_decode nofilter}
	</div>

{/if}
</div>
{/block}
