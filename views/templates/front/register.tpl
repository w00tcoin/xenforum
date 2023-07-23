{*
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{capture name=path}
		<a href="{$xenforum->GetLink($page_url)|escape:'htmlall':'UTF-8'}">{$page_title|escape:'htmlall':'UTF-8'}</a>
		<span class="navigation-pipe"></span>{l s='Members' mod='xenforum'}
{/capture}

<div id="blogview" class="blogview">
    <div class="main_box membersSetting">
        {if (isset($customer) && !empty($customer->date_submited) && $customer->date_submited != '0000-00-00 00:00:00')}
        <p class="alert alert-warning">
            {l s='Thank you! Your request was sent.' mod='xenforum'}
            ({l s='You can also' mod='xenforum'}
            <a class="resubmit" href="javascript:toogleForm();" style="cursor:pointer;">{l s='re-submit' mod='xenforum'}</a>)
        </p>
        {/if}

        {if !empty($errors)}
        <p class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button>
            {foreach from=$errors item=error}
                {$error|escape:'htmlall':'UTF-8'}<br/>
            {/foreach}
        </p>
        {/if}
		<div class="main_title">{l s='Register to Forum' mod='xenforum'}</div>
		<div class="main_body">
            <form action="" method="post" id="psform" enctype="multipart/form-data">
                <fieldset class="twoColumn">
                    <dl class="ctrlUnit">
                        <dt><label for="nickname" class="required">{l s='Nickname:' mod='xenforum'}</label></dt>
                        <dd><input type="text" tabindex="1" class="inputName form-control grey" value="{(isset($customer->nickname)) ? $customer->nickname : $online->nickname|escape:'htmlall':'UTF-8'}" id="nickname" name="nickname" maxlength="45" >
                        </dd>
                    </dl>
                </fieldset>

                <fieldset>
                    <dl class="ctrlUnit">
                        <dt><label for="ctrl_license"><h4>{l s='Terms and Conditions' mod='xenforum'}</h4></label></dt>
                        <dd>
                        <!-- License agreement -->
                            <div style="height:200px; border:1px solid #ccc; margin-bottom:8px; padding:5px; background:#fff; overflow: auto; overflow-x:hidden; overflow-y:scroll;">
                                {$term_content|html_entity_decode nofilter}
                            </div>
                            <div>
                                <span class="inline"><input type="checkbox" id="approved_license" class="required" name="" value="1" /></span>
                                <span class="inline"><label for="approved_license"><strong>{l s='I agree to the above terms and conditions' mod='xenforum'}.</strong></label></span>
                            </div>
                        </dd>
                    </dl>
                </fieldset>

                <fieldset>
                    <div class="green_button center">
                            <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
                            <button type="submit" name="submit_register" id="btSubmit" class="btn btn-default button button-small" disabled="true">
                                <span><i class="icon-save"></i> {l s='Submit' mod='xenforum'}</span>
                            </button>
                    </div>
                </fieldset>
            </form>
		</div>
	</div>
</div>
