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
{if (!isset($customer))}
	<p class="noContent">({l s='No content here!' mod='xenforum'})</p>
{else}
    {if ($id_member != $id_online && (bool)$customer->private)}
	<p class="noContent">({l s='Private member!' mod='xenforum'})</p>
    {else}

        {if isset($alreadySent)}
        <p class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>
            {l s='Your profiles has been successfully saved.' mod='xenforum'}</p>
        {else}
            {if !empty($errors)}
            <p class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button>
                {foreach from=$errors item=error}
                {$error|escape:'htmlall':'UTF-8'}<br/>
                {/foreach}
            </p>
            {/if}
        {/if}
        <div class="mainProfileColumn">
            <div class="visitorPanel">
				<a class="avatar Av1m quick-view" data-avatarhtml="true"><img src="{$xenforum->checkAvatar('default-medium', $customer->id_customer)|escape:'htmlall':'UTF-8'}" width="156" height="156" alt="" /></a>
				<div class="visitorText rpanel">
					<h2><a class="username NoOverlay" dir="auto">{$customer->nickname|escape:'htmlall':'UTF-8'}</a></h2>		
					<div class="stats">
						<p class="userBlurb">
							<span class="userTitle" style="color: {$customer->style|escape:'htmlall':'UTF-8'}" itemprop="title">{$title|escape:'htmlall':'UTF-8'}</span>
						</p>
						{if ($customer->is_staff)}
						<div class="userBanners">
							<em class="userBanner bannerStaff " itemprop="title"><span class="before"></span><strong>{l s='Staff Member' mod='xenforum'}</strong><span class="after"></span></em>
						</div>
						{/if}
					</div>
					{if ($customer->signature)}
					<div class="userQuote">
							<i class="icon icon-quote-left"></i>
								{$customer->signature|escape:'htmlall':'UTF-8'}
							<i class="icon icon-quote-right"></i>
					</div>
					{/if}

					<div class="infoBlock">
						<div class="secondaryContent pairsJustified">
								<dl><dt>{l s='Last Access:' mod='xenforum'}</dt>
									<dd>{if isset($customer->last_access) && $customer->last_access != '0000-00-00 00:00:00'}<abbr class="timeago" title="{$customer->filter_date_upd|escape:'htmlall':'UTF-8'}">{$customer->last_access|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='xenforum'}{/if}</abbr></dd></dl>
							<dl><dt>{l s='Joined:' mod='xenforum'}</dt>
								<dd>{$customer->date_add|escape:'htmlall':'UTF-8'|date_format:"%B %d, %Y"}</dd></dl>

							<dl><dt>{l s='Comments:' mod='xenforum'}</dt><dd>{XenForumPost::getToltalByCust($id_member)|escape:'htmlall':'UTF-8'}</dd></dl>

							<dl><dt>{l s='Received Likes:' mod='xenforum'}</dt><dd>{XenForumUser::getToltalByCust($id_member)|escape:'htmlall':'UTF-8'}</dd></dl>
						</div>
					</div>

					{if ($id_member == $id_online)}
					<div class="main_box listActivity">
						<div class="main_title"><i class="icon icon-bullhorn"></i> {l s='Notifications' mod='xenforum'} ({$total_unread|escape:'htmlall':'UTF-8'})</div>
						<div class="main_body">
                        {if empty($xen_notifications)}
							<p class="noContent">({l s='You don\'t have any notifications!' mod='xenforum'})</p>
						{else}
							<ol class="activity">
                            {foreach from=$xen_notifications item=notif}
								{assign var="options" value=null}
									{$options.id_xenforum = $notif.id_xenforum|escape:'htmlall':'UTF-8'}
									{$options.slug = $notif.link_rewrite|escape:'htmlall':'UTF-8'}
								{assign var="MemOptions" value=null}
									{$MemOptions.id_customer = md5($notif.id_visitor)|escape:'htmlall':'UTF-8'}
								{assign var="NotifOptions" value=null}
									{$NotifOptions.id = $notif.id_notification|escape:'htmlall':'UTF-8'}

								<li id="notification-{$notif.id_notification|escape:'htmlall':'UTF-8'}" data-id="{$notif.id_notification|escape:'htmlall':'UTF-8'}" class="notification {if (!$notif.readn)}unread{/if}">
									<a class="deleteNotif" href="{$xenforum->GetLink('xenforum_ajax_handle', $NotifOptions)|escape:'htmlall':'UTF-8'}" data-confirm="{l s='Are you sure to delete this item?' mod='xenforum'}"><i class="icon icon-times"></i></a>
									<span class="avatarMem">
										<a href="{$xenforum->GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" class="avatar Av1s" data-avatarhtml="true"><img src="{$xenforum->checkAvatar('default_small', $notif.id_visitor)|escape:'htmlall':'UTF-8'}" width="48" height="48" alt=""></a>
									</span>
									<div class="activityRight">
									<a href="{$xenforum->GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" title="{$notif.nickname|escape:'htmlall':'UTF-8'}">
										{$notif.nickname|escape:'htmlall':'UTF-8'}</a>
									{if ($notif.action == 'like')}
											{l s='likes your comment on' mod='xenforum'}
									{elseif ($notif.action == 'comment')}
											{l s='also comment on' mod='xenforum'}
									{/if}
										<a title="{$notif.meta_title|escape:'html':'UTF-8'}" href="{$xenforum->GetLink('xenforum_viewdetails',$options)|escape:'htmlall':'UTF-8'}">
												{$notif.meta_title|trim|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a>
										<span class="textItalic">"{$notif.comment|strip_tags|trim|truncate:45:'...'|escape:'html':'UTF-8'}"</span>
										<br />
										<span class="muted"><abbr class="timeago" title="{$notif.filter_date_add|escape:'htmlall':'UTF-8'}">{$notif.date_add|escape:'htmlall':'UTF-8'}</abbr></span>
									</div>
								</li>
							{/foreach}
							</ol>
						{/if}
						</div>
					</div>

					<div class="main_box membersSetting">
						<div class="main_title"><i class="icon icon-pencil-square"></i> {l s='Edit Profiles' mod='xenforum'}</div>
						<div class="main_body"><div class="main_rcolumn">
							<form action="" method="post" id="psform" class="xenForm" enctype="multipart/form-data">
							<fieldset>
								<dl class="ctrlUnit">
									<dt><label for="ctrl_file">{l s='Change avatar:' mod='xenforum'}</label></dt>
                                    <dd><input type="file" id="avatar" name="avatar" accept="image/*"/>
									</dd>
									<p class="hint">{l s='The image type must be %s, and size should be %s (square).' sprintf=['jpg', '156 x 156px'] mod='xenforum'}</p>
								</dl>

								<dl class="ctrlUnit" class="required">
									<dt><label for="nickname">{l s='Nickname:' mod='xenforum'}</label></dt>
									<dd><input type="text" tabindex="1" class="inputName form-control grey" value="{$customer->nickname|escape:'htmlall':'UTF-8'}" id="nickname" name="nickname" maxlength="45" >
									</dd>
								</dl>
								
								<dl class="ctrlUnit">
									<dt><label for="signature">{l s='Your quotes:' mod='xenforum'}</label></dt>
									<dd><input type="text" tabindex="2" class="form-control grey" value="{$customer->signature|escape:'htmlall':'UTF-8'}" id="signature" name="signature" maxlength="255" >
									</dd>
								</dl>

								<dl class="ctrlUnit">
									<dt><label for="city">{l s='City:' mod='xenforum'}</label></dt>
									<dd><input type="text" tabindex="3" class="form-control grey" value="{$customer->city|escape:'htmlall':'UTF-8'}" id="city" name="city" maxlength="45" >
									</dd>
								</dl>

								<dl class="ctrlUnit">
									<dt><label for="id_country">{l s='Country/Region:' mod='xenforum'}</label></dt>
									<dd><select name="id_country" id="id_country" class="">
											{foreach from=$countries item=country}
												<option value="{$country.id_country|escape:'htmlall':'UTF-8'}" {if $customer->id_country == $country.id_country}selected="selected"{/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										</select>
									</dd>
								</dl>

								<dl class="ctrlUnit">
									<dt><label for="notification">{l s='Get notifications via email:' mod='xenforum'}</label></dt>
									<dd id="notification">
										<input type="radio" class="grey" value="1" name="notification" id="notification_yes"{if $customer->notification == 1} checked{/if}>
											<label for="notification_yes" class="normal">{l s='Yes, ready' mod='xenforum'}</label> &nbsp;&nbsp;&nbsp;
										<input type="radio" class="grey" value="0" name="notification" id="notification_no"{if $customer->notification == 0} checked{/if}>
											<label for="notification_no" class="normal">{l s='No' mod='xenforum'}</label>
									</dd>
								</dl>

								<dl class="ctrlUnit">
									<dt><label for="private">{l s='Hide your profile' mod='xenforum'}</label></dt>
									<dd id="private">
										<input type="radio" class="grey" value="1" name="private" id="private_yes"{if $customer->private == 1} checked{/if}>
											<label for="private_yes" class="normal">{l s='Yes' mod='xenforum'}</label> &nbsp;&nbsp;&nbsp;
										<input type="radio" class="grey" value="0" name="private" id="private_no"{if $customer->private == 0} checked{/if}>
											<label for="private_no" class="normal">{l s='No' mod='xenforum'}</label>
									</dd>
								</dl>

								<div class="green_button center">
									<div class="submit">
										<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
										<button type="submit" name="update_member" id="update_member" class="btn btn-default button button-small">
											<span>{l s='Save' mod='xenforum'}</span>
										</button>
									</div>
								</div>
							</fieldset>

							</form>
						</div>
						</div>
					</div>
					{/if}

				</div>
            </div>
        </div>
    {/if}
{/if}

    {include file='module:xenforum/views/templates/front/xf_footer.tpl'}
</div>
{/block}
