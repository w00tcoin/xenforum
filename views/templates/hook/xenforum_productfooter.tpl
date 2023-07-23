{**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

{if !empty($topics)}
<!-- Block XENFORUM -->
<section class="page-product-box">
	<h3 class="page-product-heading">{l s='Related Topics' mod='xenforum'}</h3>
    <div id="xenforum-related-topics" class="block related-topics">
        <ul class="articles_list grid clearfix">
        {foreach from=$topics item=topic name=null}
            <li class="item article-box">
                {assign var="options" value=null}
                {$options.id_xenforum = $topic.id_xenforum}
                {$options.slug = $topic.link_rewrite|escape:'htmlall':'UTF-8'}
                <i class="icon-chevron-sign-right"></i>
                <a title="{$topic.meta_title|escape:'htmlall':'UTF-8'}" href="{$xenforum->getLink('xenforum_viewdetails',$options)|escape:'htmlall':'UTF-8'}">
                    {$topic.meta_title|escape:'htmlall':'UTF-8'}
                    &nbsp;(<span title="{$topic.created|escape:'htmlall':'UTF-8'}">{$topic.created|escape:'htmlall':'UTF-8'})
                </a>
            </li>
        {/foreach}
        </ul>
    </div>
</section>
<!-- Block XENFORUM -->
{/if}