{*
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<form id="xenforum_tree_form" class="defaultForm form-horizontal AdminXenForumCategory" action="" method="post" novalidate="">
<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='Tree management' mod='xenforum'}
	</div>
	<div class="form-wrapper tree-management">
        {* define the function *}
        {function name=tree level=0}
            {if $level == 0}
            <ol class="sortable ui-sortable mjs-nestedSortable-branch mjs-nestedSortable-expanded">
            {else}
            <ol class="">
            {/if}
                {foreach $data as $entry}
                    <li style="display: list-item;" class="{if !empty($entry.child_categories)}mjs-nestedSortable-branch mjs-nestedSortable-expanded{else}mjs-nestedSortable-leaf{/if} level{$level|escape:'htmlall':'UTF-8'}" id="menuItem_{$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}">
                        <div class="menuDiv{($entry.closed == 1) ? ' closed': ''|escape:'htmlall':'UTF-8'}{($entry.private == 1)? ' private': ''|escape:'htmlall':'UTF-8'}">
                           <span title="{l s='Show/hide children' mod='xenforum'}" class="disclose ui-icon ui-icon-minusthick"></span>
                           <span data-id="{$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}" class="itemTitle">
                                <a href="{$url_update|escape:'htmlall':'UTF-8'}&id_xenforum_cat={$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}">{$entry.meta_title|escape:'htmlall':'UTF-8'}</a>
                           </span>
                           <a href="{$url_delete|escape:'htmlall':'UTF-8'}&id_xenforum_cat={$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}" onclick="{literal}if (confirm('{/literal}{l s='Delete selected item?' mod='xenforum'}{literal}\n {/literal}{$entry.meta_title|escape:'htmlall':'UTF-8'}{literal}')){return true;}else{event.stopPropagation(); event.preventDefault();};{/literal}">
                                <span title="{l s='Delete' mod='xenforum'}" data-id="{$entry.id_xenforum_cat|escape:'htmlall':'UTF-8'}" class="deleteMenu ui-icon ui-icon-closethick">
                                </span>
                           </a>
                        </div>
                        {if !empty($entry.child_categories)}
                            {tree data=$entry.child_categories level=$level+1}
                        {/if}
                    </li>
                {/foreach}
            </ol>
        {/function}

        {* run the array through the function *}
        {tree data=$categories}
	</div>
	<div class="panel-footer">
		<input type="hidden" id="categories" name="allcategories">
		<button type="submit" name="submitReOrderTree" id="submitReOrderTree" disabled="disabled" class="btn btn-default pull-right" value="1"><i class="process-icon-save"></i> {l s='Save' mod='xenforum'}</button>
	</div>
</div>
</form>

<script type="text/javascript">
{literal}
    $(document).ready(function(){
        var ns = $('ol.sortable').nestedSortable({
            forcePlaceholderSize: true,
            handle: 'div',
            helper:	'clone',
            items: 'li',
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            maxLevels: 5,
            isTree: true,
            expandOnHover: 700,
            startCollapsed: false,
            change: function(event, ui){
                // console.log('Relocated item', event, ui);
                $("#submitReOrderTree").removeAttr("disabled");
            }
        });

        $('.disclose').on('click', function() {
            $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
            $(this).toggleClass('ui-icon-plusthick').toggleClass('ui-icon-minusthick');
        });

        /* $('.deleteMenu').click(function(){
            var id = $(this).attr('data-id');
            $('#menuItem_'+id).remove();
        }); */

		$("#xenforum_tree_form").submit(function(e){
			arraied = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
			$("input[name=allcategories]").val(JSON.stringify(arraied));
		});
    });
{/literal}
</script>