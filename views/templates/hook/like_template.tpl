{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{if !empty($result)}
	<div class="likesSummary secondaryContent">
		<span class="LikeText">
			{assign var="k" value=1}
			{foreach from=$result item=like}
				{if ($like.id_user == $id_user)}{l s='You' mod='xenforum'}{else}
					
					{assign var="MemOptions" value=null}
						{$MemOptions.id_customer = md5($like.id_user)|escape:'htmlall':'UTF-8'}
					<a href="{$xenforum->GetLink('xenforum_member', $MemOptions)}" class="username" dir="auto">{$like.nickname}</a>{/if}{if (($k == $max && $totalLike > $max) || ($k == $totalLike - 1))} {l s='and' mod='xenforum'}{elseif ($k < $totalLike - 1)},{/if}{if ($k == $max && $totalLike > $max)} {$totalLike - $k} {l s='others' mod='xenforum'}{break}{/if}{assign var="k" value=$k + 1}
			{/foreach}
			{l s='likes this.' mod='xenforum'}

		</span>
	</div>
{/if}