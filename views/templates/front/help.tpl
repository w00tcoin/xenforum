{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
		<a href="{$xenforum->GetLink($page_url)|escape:'htmlall':'UTF-8'}">{$page_title|escape:'htmlall':'UTF-8'}</a>
		<span class="navigation-pipe"></span>{l s='Helps' mod='xenforum'}
{/capture}

<div id="blogview" class="blogview">
	<div class="blog_title">{l s='Helps' mod='xenforum'}</div>
{if $help_content != ''}
	<div class="helpContent">
        {$help_content|html_entity_decode nofilter}
	</div>

{/if}
</div>
