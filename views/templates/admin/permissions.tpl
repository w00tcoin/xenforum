{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{if (isset($on_submit) && ($on_submit == true))}
	<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			Successful update
	</div>
{elseif (isset($on_submit) && ($on_submit == false))}
	<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			Error
	</div>
{/if}
<form id="module_form" class="defaultForm form-horizontal" action="" method="post" novalidate>
	<input type="hidden" name="submitBlockCart" value="1" />
	<div class="panel" id="fieldset_0">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='Edit Permissions:' mod='xenforum'} {$group_name|escape:'htmlall':'UTF-8'}
	</div>
	<div class="form-wrapper">

	{foreach from=$rules_list item=permission}
		<div class="form-group">
			<label class="control-label col-lg-3">{$permission.name|escape:'htmlall':'UTF-8'}</label>
			<div class="col-lg-3">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="{$permission.rule|escape:'htmlall':'UTF-8'}" id="{$permission.rule|escape:'htmlall':'UTF-8'}_on" value="1" {if ($permission.valid)}checked="checked"{/if} {if ($is_admin)}disabled="disabled"{/if}/>
                            <label  for="{$permission.rule|escape:'htmlall':'UTF-8'}_on">Yes</label>
                    <input type="radio" name="{$permission.rule|escape:'htmlall':'UTF-8'}" id="{$permission.rule|escape:'htmlall':'UTF-8'}_off" value="0" {if (!$permission.valid)}checked="checked"{/if} {if ($is_admin)}disabled="disabled"{/if}/>
                            <label  for="{$permission.rule|escape:'htmlall':'UTF-8'}_off">No</label>
                            <a class="slide-button btn"></a>
                </span>
            </div>
            <span class="control-label control-right col-lg-6"{if (!$permission.valid)} style="display:none;"{/if}>
                {if in_array($permission.rule, array('_create_topic_', '_view_private_')) }
                <button href="#tree_box" class="edit-popup" {if ($is_admin)}disabled="disabled"{/if}>{l s='edit params' mod='xenforum'}</button>
                    <input type="hidden" name="{$permission.rule|escape:'htmlall':'UTF-8'}_validation" class="validation" placeholder="{l s='Related forums...' mod='xenforum'}" value="{(isset($permission.validation)) ? $permission.validation : ''|escape:'htmlall':'UTF-8'}">
                {/if}
            </span>
		</div>
	{/foreach}
	</div>
    
    <div class="clearfix"></div>
    <!-- Fancybox -->
    <div style="display:none">
        <div id="tree_box" class="form-wrapper tree-validations">
            <div class="tree-header"><i class="icon icon-edit"></i> {l s='Forums related to this feature:' mod='xenforum'}</div>
            <div class="tree-sub">
                <input type="checkbox" class="forums-all" name="forums[all]" id="all_forum" rel="all" value="1" />
                <label for="all_forum">{l s='All forums' mod='xenforum'}</label>
            </div> 
            <table cellpadding="10px" align="center" style="min-width:300px;">
                <tbody>
                {* define the function *}
                {function name=tree level=0}
                        {foreach $data as $entry}
                            <tr class="tree-row level-{$level|escape:'htmlall':'UTF-8'}{($entry.closed)?' closed':''|escape:'htmlall':'UTF-8'}{($entry.private)?' private':''|escape:'htmlall':'UTF-8'}" level="{$level|escape:'htmlall':'UTF-8'}">
                                <td class="tree-label" style="padding-left: {$level * 20|escape:'htmlall':'UTF-8'}px;padding-right: 20px;" align="left">
                                    <span class="itemLabel">{$entry.meta_title|escape:'htmlall':'UTF-8'} {if ($entry.closed)}{l s='(closed)' mod='xenforum'}{/if}</span>
                                </td>
                                <td class="tree-action">
                                   <span data-id="{$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}" class="itemTitle">
                                       <input
                                              type="checkbox"
                                              class="forums-{$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}"
                                              name="forums[{$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}]"
                                              rel="{$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}"
                                              value="1"
                                        />
                                   </span>
                                </td>
                            </tr>
                            {if !empty($entry.child_categories)}
                                {tree data=$entry.child_categories level=$level+1}
                            {/if}
                        {/foreach}
                {/function}

                {* run the array through the function *}
                {tree data=$categories}
                </tbody>
            </table>
            <div class="tree-footer close-popup">
                <button name="submitRelateds">{l s='Save' mod='xenforum'}</button>
            </div>
        </div>
	</div>
    
	<!-- /.form-wrapper -->
	<div class="panel-footer">
			<button type="submit" value="1"	id="module_form_submit_btn" name="submitXenForum" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='xenforum'}
			</button>
			<a href="{$link_back|escape:'htmlall':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i> {l s='Back' mod='xenforum'}
			</a>
	</div>
</div>
</form>

<script type="text/javascript">
{literal}
    $('.form-group').find("input[type=radio]").on("change", function() {
        toggleSettings($(this));
    });

    function toggleSettings(rule) {
        //var rule = $("[name=_create_topic_]:checked");
        if (rule.val() == 1) {
            rule.closest('.form-group').find(".control-right").show();
        } else {
            rule.closest('.form-group').find(".control-right").hide();
        }
    }

    $("#all_forum").on("change", function() {
        toggleAll();
    });

    //$(".tree-validations table input[readonly=readonly]").on("click", function() {
    //    return false;
    //});

    function toggleAll() {
        var _all = $("#all_forum").is(":checked");
        
        if (_all) {
            $(".tree-validations table input[type=checkbox]").attr("disabled", "disabled");
            $(".tree-validations table").addClass("disabled");
        } else {
            $(".tree-validations table input[type=checkbox]").removeAttr("disabled");
            $(".tree-validations table input[type=checkbox]").prop("disabled", false);
            $(".tree-validations table").removeClass("disabled");
        }
    }

    $('.edit-popup').fancybox({
        hideOnContentClick: false,
        showCloseButton: true,
        afterLoad: function(obj) {
            obj.content.find('input').removeAttr('checked');
            obj.content.find('input').removeAttr('disabled');
            obj.content.find('table').removeClass("disabled");
            var rel = $(this.element).parent('span').find('input.validation').val();
            var valid = rel.split(',');
            $.each(valid, function(i, forum) {
                if (obj.content.find('input.forums-'+forum).length) {
                    obj.content.find('input.forums-'+forum).attr('checked', 'checked');
                }
                if (forum == 'all') {
                    obj.content.find('input.forums-'+forum).trigger('change');
                }
            });
        },
        afterClose: function(obj) {
            var res = [];
            $.each(obj.content.find('input:checked').not(':disabled'), function(i, item) {
                res.push($(this).attr('rel'));
            });
            var rel = $(this.element).parent('span').find('input.validation').val(res.join(','));
        }
    });

$('.close-popup').click(function(e) {
        // Kill default behaviour
        e.preventDefault();
        $.fancybox.close();
        return false;
    });

{/literal}
</script>