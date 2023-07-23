{**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<div class="block sidebar forum_block">
	<!-- block: forum_stats -->
	<div class="section">
		<div class="secondaryContent statsList" id="boardStats">
			<h3>{l s='Forum Statistics' mod='xenforum'}</h3>
			<div class="pairsJustified">
				<dl class="discussionCount"><dt>{l s='Discussions:' mod='xenforum'}</dt>
					<dd>{$totaltopic|escape:'htmlall':'UTF-8'}</dd></dl>
				<dl class="messageCount"><dt>{l s='Comments:' mod='xenforum'}</dt>
					<dd>{$totalcomment|escape:'htmlall':'UTF-8'}</dd></dl>
				<dl class="memberCount"><dt>{l s='Members:' mod='xenforum'}</dt>
					<dd>{$totaluser|escape:'htmlall':'UTF-8'}</dd></dl>
				{if ($totaluser)}
					<dl><dt>{l s='Latest Member:' mod='xenforum'}</dt>
						{assign var="MemOptions" value=null}
							{$MemOptions.id_customer = md5($last_id)}
					<dd><a href="{$xenforum->GetLink('xenforum_member',$MemOptions)|escape:'htmlall':'UTF-8'}" class="username" dir="auto">{$last_name|escape:'htmlall':'UTF-8'}</a></dd></dl>
				{/if}
			</div>
		</div>
	</div>
	<!-- end block: forum_stats -->
</div>
