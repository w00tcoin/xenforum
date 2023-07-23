{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<form id="xenforum_position_form" class="defaultForm form-horizontal AdminXenForumPosition" action="" method="post" novalidate="">
<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='Positions management' mod='xenforum'}
	</div>
	<div class="form-wrapper">
        <ul id="sortable1" class="connectedSortable">
            <li class="ui-state-default ui-state-disabled">{l s='(Left panel)' mod='xenforum'}</li>
            {if ($left_blocks)}
                {foreach $left_blocks as $value}
                    <li data-id="{$value.id|escape:'htmlall':'UTF-8'}" data-name="{$value.block|escape:'htmlall':'UTF-8'}" class="ui-state-default">
                        <a href="{$url_update|escape:'htmlall':'UTF-8'}&id_xenforum_position={$value.id|escape:'htmlall':'UTF-8'}">{$value.block|escape:'htmlall':'UTF-8'}</a>
                    </li>
                {/foreach}
            {/if}
        </ul>

        <ul id="sortable2" class="connectedSortable">
            <li class="ui-state-default ui-state-disabled">{l s='(Unallocated blocks)' mod='xenforum'}</li>
            {if ($hidden_blocks)}
                {foreach $hidden_blocks as $value}
                    <li data-id="{$value.id|escape:'htmlall':'UTF-8'}" data-name="{$value.block|escape:'htmlall':'UTF-8'}" class="ui-state-default">
                        <a href="{$url_update|escape:'htmlall':'UTF-8'}&id_xenforum_position={$value.id|escape:'htmlall':'UTF-8'}">{$value.block|escape:'htmlall':'UTF-8'}</a>
                    </li>
                {/foreach}
            {/if}
        </ul>

        <ul id="sortable3" class="connectedSortable">
            <li class="ui-state-default ui-state-disabled">{l s='(Right panel)' mod='xenforum'}</li>
            {if ($right_blocks)}
                {foreach $right_blocks as $value}
                    <li data-id="{$value.id|escape:'htmlall':'UTF-8'}" data-name="{$value.block|escape:'htmlall':'UTF-8'}" class="ui-state-default">
                        <a href="{$url_update|escape:'htmlall':'UTF-8'}&id_xenforum_position={$value.id|escape:'htmlall':'UTF-8'}">{$value.block|escape:'htmlall':'UTF-8'}</a>
                    </li>
                {/foreach}
            {/if}
        </ul>

        <div class="clearfix"></div>
	</div>
	<div class="panel-footer clearfix">
		<input type="hidden" name="sortable1">
		<input type="hidden" name="sortable2">
		<input type="hidden" name="sortable3">
		<button type="submit" name="submitReOrderPositions" id="submitReOrderTree" class="btn btn-default pull-right" value="1"><i class="process-icon-save"></i> {l s='Save' mod='xenforum'}</button>
	</div>
</div>
</form>

<script type="text/javascript">
{literal}
    $(function() {
        $("#sortable1, #sortable2, #sortable3").sortable({
            items: "li:not(.ui-state-disabled)",
            connectWith: ".connectedSortable",
            placeholder: "ui-state-highlight"
        }).disableSelection();

		$("#xenforum_position_form").submit(function(e){
			left_blocks = $("#sortable1").sortable("toArray", {attribute: "data-id"});
			right_blocks = $("#sortable3").sortable("toArray", {attribute: "data-id"});
			unallocated_blocks = $("#sortable2").sortable("toArray", {attribute: "data-id"});
			$("input[name=sortable1]").val(JSON.stringify(left_blocks));
			$("input[name=sortable3]").val(JSON.stringify(right_blocks));
			$("input[name=sortable2]").val(JSON.stringify(unallocated_blocks));
            return true;
		});
    });
{/literal}
</script>