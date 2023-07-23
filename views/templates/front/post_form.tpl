{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<dl class="ctrlUnit fullWidth">
    <dt></dt>
    <dd>
        <textarea name="comment" id="comment" rows="20" cols="50" class="inputContent form-control rteXF autoload_rte">{$comment}</textarea>
    </dd>
</dl>

<div class="green_button center"> <!--http://local.dev/ps1619/modules/xenforum/uploads/3_1480863854_blog-icon.png-->
    <div class="submit">
        <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
        <button type="submit" name="edit_post" id="edit_post" class="btn btn-default button button-small">
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
<dl class="twoColumn" id="store_attachment" {if empty($attachments)}style="display:none;"{/if}>
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
</dl>
{/if}

<script type="text/javascript">
var id_xenforum_post = "{if isset($id_xenforum_post)}{$id_xenforum_post|escape:'htmlall':'UTF-8'}{else}0{/if}";
var tinymce_lang_link = "{$tinymce_lang_link}";
var _confirm_delete_ = "{l s='Are you sure you want to delete this attachment?' mod='xenforum'}";
</script>
