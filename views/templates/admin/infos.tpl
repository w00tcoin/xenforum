{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<div class="alert alert-info">
<p><strong>{l s='Notice:' mod='xenforum'}</strong></p>
<p class="icon-cog" > 
	<a href="{$link_to_setting|escape:'htmlall':'UTF-8'}">
			{if isset($isconfig)}
				{l s='Forums management' mod='xenforum'}
			{else}
				{l s='Configure "Xen Forums" module' mod='xenforum'}
			{/if}
	</a>
</p>
<br />
<p class="icon-cog" > {l s='To add link to top menu in front-office: Go to' mod='xenforum'} <a href="{$link_to_blocktopmenu|escape:'htmlall':'UTF-8'}">{l s='Configure Menu module' mod='xenforum'}</a></p>
<br />
<p> {l s='Customize CSS: Shop dir' mod='xenforum'} » modules/xenforum/views/css/custom.css<br />
 {l s='Go to front-office to create your new topics' mod='xenforum'}.</p>
</div>

<div class="panel">
	<div class="form-wrapper xfInfo" style="text-align: center;">
        <a class="btn btn-info {if isset($isconfig)}active disabled{/if}" href="{$settings_url|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon-cogs"></i> {l s='Settings' mod='xenforum'}</a>
        <a class="btn btn-info {if isset($istree)}active disabled{/if}" href="{$tree_url|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon-sitemap"></i> {l s='Forums tree management' mod='xenforum'}</a>
        <a class="btn btn-info" href="{$topic_url|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon-newspaper-o"></i> {l s='Topics' mod='xenforum'}</a>
        <a class="btn btn-info" href="{$comment_url|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon-comments"></i> {l s='Comments' mod='xenforum'}</a>
        <a class="btn btn-info" href="{$user_url|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon-user"></i> {l s='Members' mod='xenforum'}</a>
        <a class="btn btn-info" href="{$permission_url|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon-users"></i> {l s='Groups and Permissions' mod='xenforum'}</a>
        <a class="btn btn-info" href="{$position_url|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon-arrows"></i> {l s='Posittions management' mod='xenforum'}</a>
        <div class="clearfix"></div>
	</div>
</div>
<style>
    .xfInfo .btn {
        margin-top: 2px;
        margin-bottom: 2px;
    }
</style>
