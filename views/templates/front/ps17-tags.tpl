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
	<div class='blog_title'>{l s='Search for tag:' mod='xenforum'} "{$tags|escape:'htmlall':'UTF-8'}"</div>
<!-- Show forums and topics -->
{if $allposts == ''}
	<p class="error">({l s='No content here!' mod='xenforum'})</p>
{else}
    {include file='module:xenforum/views/templates/front/discussion_list.tpl' allposts=$allposts}
{/if}
<!--// Show forums and topics -->

    {include file='module:xenforum/views/templates/front/xf_footer.tpl'}
</div>
{/block}
