{**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<!-- MODULE XenForum -->
{assign var="MemOptions" value=null}
    {$MemOptions.id_customer = md5($id_online)}
{if $smarty.const._PS_VERSION_|@addcslashes:'\'' < '1.7'}
<li class="lnk_forums">
	<a 	href="{xenforum::GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" title="{l s='Forums' mod='xenforum'}">
		<i class="icon-comments"></i>
        <span>{l s='My Forum Profiles' mod='xenforum'}{if ($unreads)}<div class="activityBar" title="{l s='You have %d notifications' sprintf=array($unreads) mod='xenforum'}">{$unreads|escape:'htmlall':'UTF-8'}</div>{/if}</span>
	</a>
</li>
{else}
<a 	href="{xenforum::GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="lnk_forums" title="{l s='Forums' mod='xenforum'}">
	<span class="link-item">
	<i class="material-icons">comment</i>
	<span>{l s='My Forum Profiles' mod='xenforum'}</span>
	</span>
</a>
{/if}

<style>
{literal}
	.lnk_forums a {color:#0087BF !important;}
	.lnk_forums div {display:inline;}
{/literal}
</style>
<!-- END : MODULE XenForum -->