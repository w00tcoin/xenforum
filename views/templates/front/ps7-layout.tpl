{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{extends file='page.tpl'}

{block name='head_seo_title'}{$meta_title|escape:'htmlall':'UTF-8'} - {$page.meta.title|escape:'htmlall':'UTF-8'}{/block}
{block name='head_seo_description'}{$meta_description|escape:'htmlall':'UTF-8'|default:''} {$page.meta.description|escape:'htmlall':'UTF-8'}{/block}
{block name='head_seo_keywords'}{$meta_keywords|escape:'htmlall':'UTF-8'} {$page.meta.keywords|escape:'htmlall':'UTF-8'}{/block}

{block name="left_column"}
{if strpos($layout, 'both') !== false || strpos($layout, 'left') !== false}
	<div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
		{if $xenforum_show_lcolumn}
			{hook h='displayXenForumLeft'}
		{else}
			{hook h="displayLeftColumn"}
		{/if}
	</div>
{/if}
{/block}

{block name="right_column"}
{if strpos($layout, 'both') !== false || strpos($layout, 'right') !== false}
	<div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
		{if $xenforum_show_rcolumn}
			{hook h='displayXenForumRight'}
		{else}
			{hook h="displayRightColumn"}
		{/if}
	</div>
{/if}
{/block}
