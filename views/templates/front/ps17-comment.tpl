{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<div id="commentview" class="commentview">
{if $all_comments == ''}
	<p class="noContent">({l s='No comment here!' mod='xenforum'})</p>
{else}
	<ol class="messageList" id="messageList">
	{foreach from=$all_comments key=k item=comment}
	<li id="post-{$comment.id_xenforum_post|escape:'htmlall':'UTF-8'}" class="message">
		<div class="messageUserInfo" itemscope="itemscope" itemtype="http://data-vocabulary.org/Person">	
			<div class="messageUserBlock ">
					{assign var="MemOptions" value=null}
						{$MemOptions.id_customer = md5($comment.id_author)|escape:'htmlall':'UTF-8'}
				<div class="avatarHolder">
					<span class="helper"></span>
					<a rel="nofollow" href="{$xenforum->GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" class="avatar Av2m" data-avatarhtml="true"><img src="{$xenforum->checkAvatar('default-medium', $comment.id_author)|escape:'htmlall':'UTF-8'}" width="96" height="96" alt="{$comment.nickname|escape:'htmlall'}"></a>
					
					<!-- slot: message_user_info_avatar -->
				</div>

				<h5 class="userText">
					<a rel="nofollow" href="{$xenforum->GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" class="username" dir="auto" itemprop="name">{$comment.nickname|escape:'htmlall':'UTF-8'}</a>
					<em class="userTitle"  style="color: {$comment.style|escape:'htmlall':'UTF-8'}" itemprop="title">{if ($comment.override_title)}{$comment.override_title|escape:'htmlall':'UTF-8'}{else}{$comment.title|escape:'htmlall'}{/if}</em>
					{if ($comment.is_staff|escape:'intval')}
						<em class="userBanner bannerStaff wrapped" itemprop="role"><span class="before"></span><strong>{l s='Staff Member' mod='xenforum'}</strong><span class="after"></span></em>
					{/if}
					<!-- slot: message_user_info_text -->
					{if ($comment.city != null && $comment.city != '')}
						<em class="userLocation" itemprop="region"><i class="icon-map-marker"></i> {$comment.city|escape:'htmlall':'UTF-8'}</em>
					{/if}
				</h5>
			</div>
		</div>

		<div class="messageInfo primaryContent">
			<div class="messageContent messageText">
				<article>
                    {$comment.comment|stripslashes nofilter}
				</article>
			</div>
			
			<div class="messageMeta ToggleTriggerAnchor">
				<div class="privateControls">
					<span class="item muted">
						{assign var="MemOptions" value=null}
							{$MemOptions.id_customer = md5($comment.id_author)|escape:'htmlall':'UTF-8'}
						<span class="authorEnd"><a href="{$xenforum->GetLink('xenforum_member', $MemOptions)|escape:'htmlall':'UTF-8'}" class="username author" dir="auto">{$comment.nickname|escape:'htmlall':'UTF-8'}</a>,</span>
						<abbr class="timeago" title="{$comment.filter_created|escape:'html':'UTF-8'}">{$comment.created|escape:'html':'UTF-8'}</abbr>
					</span>
						{assign var="ComOptions" value=null}
							{$ComOptions.id = $comment.id_xenforum_post|escape:'htmlall':'UTF-8'}
					{if ($user->checkPerm($id_customer, '_edit_all_') || (($id_customer == $comment.id_author) && $user->checkPerm($id_customer, '_edit_mine_')))}
					<a href="{$xenforum->GetLink('xenforum_editpost', $ComOptions)|escape:'htmlall':'UTF-8'}" class="item control edit" ><i class="icon icon-edit"></i> <span></span>{l s='Edit' mod='xenforum'}</a>
                    {/if}
                    {if ($user->checkPerm($id_customer, '_delete_all_') || (($id_customer == $comment.id_author) && $user->checkPerm($id_customer, '_delete_mine_')))}
                    <a href="javascript:void(0);" rel="{$xenforum->GetLink('xenforum_ajax_handle', $ComOptions)|escape:'htmlall':'UTF-8'}" class="item control delete" data-confirm="{l s='Are you sure to delete this item?' mod='xenforum'}"><i class="icon icon-times"></i> <span></span>{l s='Delete' mod='xenforum'}</a>
                    {/if}
					{if ($id_customer)}
					<a rel="{$xenforum->GetLink('xenforum_ajax_handle', $ComOptions)|escape:'htmlall':'UTF-8'}" href="#report_box" class="item control report-topic" ><i class="icon icon-flag-o"></i> <span></span>{l s='Report' mod='xenforum'}</a>
						{if $comment.reports && $user->checkPerm($id_customer, '_edit_all_')}
							<div class="tool-tips">{$comment.reports|escape:'htmlall':'UTF-8'}</div>
						{/if}
					{/if}
				</div>

				<div class="publicControls">
					<a href="#post-{$comment.id_xenforum_post|escape:'htmlall':'UTF-8'}" title="Permalink" class="item muted postNumber">#{$comment.id_xenforum_post|escape:'htmlall'}</a>
					{if ($id_customer && ($like_myself || ($id_customer != $comment.id_author)))}
					<a href="{$xenforum->GetLink('xenforum_ajax_handle', $ComOptions)|escape:'htmlall':'UTF-8'}" class="LikeLink item control {if ($user->checkUserLiked($comment.id_xenforum_post, $id_customer))}{l s='unlike' mod='xenforum'}{else}{l s='like' mod='xenforum'}{/if}" data-container="#likes-post-{$comment.id_xenforum_post|escape:'htmlall':'UTF-8'}"><span></span><span class="LikeLabel">{if ($user->checkUserLiked($comment.id_xenforum_post, $id_customer))}<i class="icon icon-thumbs-o-down"></i> {l s='Unlike' mod='xenforum'}{else}<i class="icon icon-thumbs-o-up"></i> {l s='Like' mod='xenforum'}{/if}</span></a>
					{/if}
					{if ($user->checkPerm($id_customer, '_create_comment_'))}
					<a href="{$xenforum->GetLink('xenforum_ajax_handle', $ComOptions)|escape:'htmlall':'UTF-8'}" class="ReplyQuote item control reply" title="{l s='Reply, quoting this message' mod='xenforum'}"><span><i class="icon icon-reply-all"></i> {l s='Reply' mod='xenforum'}</span>
						</a>
					{/if}
					</div>
			</div>

			<div id="likes-post-{$comment.id_xenforum_post|escape:'htmlall':'UTF-8'}">
				{$user->listUserLiked($comment.id_xenforum_post, $id_customer|escape:'htmlall':'UTF-8')}
			</div>
		</div>
	</li>

	{if $k == 0 && !empty($related_products)}
		<!-- Related Products -->
		<li class="xenforum_related message">
		<h4 class="related-heading"><i class="icon icon-link"></i> {l s='Related Products' mod='xenforum'}</h4>
			<div class="mainList" style="padding: 0 5px;">
				{include file='module:xenforum/views/templates/front/ps17-related_products.tpl' products=$related_products}
			</div>
		</li>
		<!--end Related Products -->
	{/if}
	{/foreach}
{/if}

    {if !empty($page_nums)}
	<div class="post_paging">
		<div class="sumary"><div class="results"> {l s='Page' mod='xenforum'} {$curent|escape:'htmlall':'UTF-8'} / {$total_pages|escape:'htmlall':'UTF-8'}</div></div>
        <ul>
            {assign var="options" value=null}
            {$options.page = 1}
            {$options.id_xenforum = $id_xenforum}
            {$options.slug = $topic.link_rewrite|escape:'htmlall':'UTF-8'}
            {if (1 != $curent)}
            <li><a class="page-link" href="{$xenforum->GetLink('xenforum_post_pagination',$options)|escape:'htmlall':'UTF-8'}"><span class="page-active">{l s='« First' mod='xenforum'}</span></a></li>
            {else}
            <li><span class="page-disabled">{l s='« First' mod='xenforum'}</span></li>
            {/if}
            <li>...</li>

            {for $k=$start to ($start+$vision)}
                {assign var="options" value=null}
                {$options.page = $k}
                {$options.id_xenforum = $id_xenforum}
                {$options.slug = $topic.link_rewrite|escape:'htmlall':'UTF-8'}
                {if $k == $curent}
                    <li><span class="page-current">{$k|escape:'htmlall':'UTF-8'}</span></li>
                {else}
                    <li><a class="page-link" href="{$xenforum->GetLink('xenforum_post_pagination',$options)|escape:'htmlall':'UTF-8'}"><span class="page-active">{$k|escape:'htmlall':'UTF-8'}</span></a></li>
                {/if}
            {/for}
            
            {assign var="options" value=null}
            {$options.page = $total_pages}
            {$options.id_xenforum = $id_xenforum}
            {$options.slug = $topic.link_rewrite|escape:'htmlall':'UTF-8'}
            <li>...</li>
            {if ($total_pages != $curent)}
            <li><a class="page-link" href="{$xenforum->GetLink('xenforum_post_pagination',$options)|escape:'htmlall':'UTF-8'}"><span class="page-active">{l s='Last »' mod='xenforum'}</span></a></li>
            {else}
                <li><span class="page-disabled">{l s='Last »' mod='xenforum'}</span></li>
            {/if}
		</ul>
	</div>
	{/if}


{if ($comment_closed)}
	<p class="muted alignRight alertFooter">(<i class="icon-lock"></i> {l s='Locked' mod='xenforum'})</p>
{elseif (!($id_customer))}
	<p class="muted alignRight alertFooter">({l s='You must log in or sign up to post here' mod='xenforum'})</p>
{elseif ($user->checkPerm($id_customer, '_create_comment_'))}
		<li class="message" data-author="" style="list-style: none;">
		<div class="messageUserInfo" itemscope="itemscope" itemtype="http://data-vocabulary.org/Person">	
		<div class="messageUserBlock ">
			
				<div class="avatarHolder">
					<span class="helper"></span>
					<a href="" class="avatar Av2m" data-avatarhtml="true"><img src="{$xenforum->checkAvatar('default-medium', $id_customer)|escape:'htmlall':'UTF-8'}" width="96" height="96" alt="vothien"></a>
					
					<!-- slot: message_user_info_avatar -->
				</div>

			<span class="arrow"><span></span></span>
		</div>
		</div>

			<div class="messageInfo primaryContent">
				<div id="xenforum_form" class="xenforum_form">

					{if isset($alreadySent)}
						<p class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>
							{l s='Your comment has been successfully sent.' mod='xenforum'}</p>
					{else}
						{if isset($error)}
							<p class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button>
								{$error|escape:'htmlall':'UTF-8'}</p>
						{/if}
					{/if}

					<form action="" method="post" id="psform">
						<textarea tabindex="5" class="inputContent form-control grey rteXF" rows="8" cols="80" name="comment" id="comment">{$retrieve_comment}</textarea>
						<div class="green_button right">
							<div class="submit">
								<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
								<button type="submit" name="add_comment" id="add_comment" class="btn btn-default button button-small">
									<span>{l s='SEND' mod='xenforum'}<i class="icon-chevron-right right"></i></span>
                                </button>
                                {if ($user->checkPerm($id_online, '_allowed_upload_'))}
                                <button type="button" id="add_attachment" class="btn btn-default button button-small">
                                    <span>{l s='Add Images' mod='xenforum'}</span>
                                </button>
                                <div style="display:none;">
                                    {assign var="Options" value=null}
                                    {$Options.id = $id_online|escape:'htmlall':'UTF-8'}
                                    <input type="file" name="add_attachment" data-max-size="2048" accept="image/*" rel="{$xenforum->GetLink('xenforum_ajax_handle', $Options)|escape:'htmlall':'UTF-8'}">
                                </div>
                                {/if}
							</div>
						</div>
                        {if ($user->checkPerm($id_online, '_allowed_upload_'))}
                        <div class="twoColumn" id="store_attachment" style="display:none;">
                            <dt><label>{l s='Attachments:' mod='xenforum'}</label></dt>
                            <dd id="attachment_wrap">
                                <div class="attachment" id="attachment_templ" style="display:none;">
                                    <div class="attachment-img">
                                        <img src="">
                                    </div>
                                    <div class="attachment-desc">
                                        <div class="attachment-name" style="margin-bottom:10px;"><a href="" target="_blank"></a></div>
                                        <div>
                                            <input type="hidden" name="attachments[]" value="" />
                                            <button type="button" class="btn btn-xs btn-primary insert-attachment">{l s='Insert into editor' mod='xenforum'}</button>
                                            <button type="button" class="btn btn-xs btn-danger delete" rel="{$xenforum->GetLink('xenforum_ajax_handle', $Options)|escape:'htmlall':'UTF-8'}">{l s='Delete' mod='xenforum'}</button>
                                        </div>
                                    </div>
                                </div>
                                {if !empty($attachments)}
                                {foreach from=$attachments item=attachment}
                                <div class="attachment">
                                    <div class="attachment-img">
                                        <img src="{$attachment.path|escape:'htmlall':'UTF-8'}">
                                    </div>
                                    <div class="attachment-desc">
                                        <div class="attachment-name" style="margin-bottom:10px;"><a href="{$attachment.path|escape:'htmlall':'UTF-8'}" target="_blank">{$attachment.name|escape:'htmlall':'UTF-8'}</a></div>
                                        <div>
                                            <input type="hidden" name="attachments[]" value="{$attachment.id|escape:'htmlall':'UTF-8'}" />
                                            <button type="button" class="btn btn-xs btn-primary insert-attachment">{l s='Insert into editor' mod='xenforum'}</button>
                                            <button type="button" class="btn btn-xs btn-danger delete" rel="{$xenforum->GetLink('xenforum_ajax_handle', $Options)|escape:'htmlall':'UTF-8'}">{l s='Delete' mod='xenforum'}</button>
                                        </div>
                                    </div>
                                </div>
                                {/foreach}
                                {/if}
                            </dd>
                        </div>
                        {/if}
					</form>
				</div>
			</div>
		</li>
{/if}
</ol>

{if (!$comment_closed && $fb_comment_on && $fb_app_id != '')}
    <div class="fb-comments" xid="{$id_xenforum|escape:'htmlall':'UTF-8'}" data-numposts="20" data-width="100%" data-colorscheme="light"></div>
{/if}
</div>

<!-- Fancybox -->
<div style="display:none">
	<div id="report_box">
		<form id="report_box_form" action="#">
			<h2 class="title">{l s='Report Post' mod='xenforum'}</h2>
			<div class="new_comment_form_content">
				<h2>{l s='Write your reason' mod='xenforum'}</h2>
				<div id="report_box_form_error" class="error" style="display:none;padding:15px 25px">
					<ul>{l s='Please enter a reason for reporting this message.' mod='xenforum'}</ul>
				</div>

				<label for="reason">{l s='Your reason' mod='xenforum'}<sup class="required">*</sup></label>
				<textarea id="reason" name="reason"></textarea>

				<div id="new_comment_form_footer">
					<input name="url" type="hidden" value="" />
					<p class="fl required"><sup>*</sup> {l s='Required fields' mod='xenforum'}</p>
					<p class="fr">
						<button id="submitReport" name="submitReport" type="submit">{l s='Send' mod='xenforum'}</button>&nbsp;
						{l s='or' mod='xenforum'}&nbsp;<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='xenforum'}</a>
					</p>
					<div class="clearfix"></div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End fancybox -->


<script type="text/javascript">
var alert_report_thankyou = "{l s='Thank you for report this message' mod='xenforum'}";
var id_xenforum_post = "0";
var _confirm_delete_ = "{l s='Are you sure you want to delete this attachment?' mod='xenforum'}";
var tinymce_lang_link = "{$tinymce_lang_link}";
var str_unlike = "<i class='icon icon-thumbs-o-down'></i> {l s='Unlike' mod='xenforum'}";
var str_like = "<i class='icon icon-thumbs-o-up'></i> {l s='Like' mod='xenforum'}";

var slide_pager = false,
slide_infiniteLoop = false,
slide_auto = true,
slide_hideControlOnEnd = false,
slide_slideWidth = {$homeSize.width|escape:'htmlall':'UTF-8'} + 0,
slide_slideMargin = 41;
var fb_app_id = "{$fb_app_id|escape:'htmlall':'UTF-8'}";

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId="+fb_app_id;
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

</script>
