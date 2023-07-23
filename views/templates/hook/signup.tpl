{**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{if (!isset($id_online))}
<div class="block sidebar block-my-account">
		<div class="loginButton">
			<label for="LoginControl"><a rel="nofollow" href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" class="myButton">{l s='Log in now!' mod='xenforum'}</a></label>
		</div>
</div>
{elseif ($controller != 'member')}
<div class="block sidebar block-my-account">
	{assign var="MemOptions" value=null}
		{$MemOptions.id_customer = md5($id_online)}
	<div class="visitorPanel">
			<div class="leftAvatar">
				<a rel="nofollow" class="avatar Av1m" href="{$xenforum->GetLink('xenforum_member',$MemOptions)|escape:'htmlall':'UTF-8'}"><img src="{$xenforum->checkAvatar('default-medium', $id_online|escape:'htmlall':'UTF-8')}" width="96" height="96" alt="" /></a>
			</div>
			<div class="visitorText">
                <h2><a rel="nofollow" href="{$xenforum->GetLink('xenforum_member',$MemOptions)|escape:'htmlall':'UTF-8'}" class="username NoOverlay" dir="auto">{$currentuser.nickname|escape:'htmlall':'UTF-8'}{if ($unreads)} <span class="activityBar" title="{l s='You have %d notifications' sprintf=array($unreads) mod='xenforum'}">{$unreads|escape:'html'}</span>{/if}</a></h2>
				<p class="userBlurb">
					<span class="userTitle" style="color: {$currentuser.style|escape:'htmlall':'UTF-8'}" itemprop="title">{if ($currentuser.approved)}{$currentuser.title|escape:'htmlall':'UTF-8'}{else}{l s='(Unapproved)' mod='xenforum'}{/if}</span>
				</p>
				<div class="stats">
					<dl class="pairsJustified"><dt>{l s='Comments:' mod='xenforum'}</dt> <dd>{XenForumPost::getToltalByCust($id_online|escape:'htmlall':'UTF-8')}</dd></dl>
					<dl class="pairsJustified"><dt>{l s='Likes:' mod='xenforum'}</dt> <dd>{XenForumUser::getToltalByCust($id_online|escape:'htmlall':'UTF-8')}</dd></dl>
				</div>
			</div>
	</div>
</div>
{/if}