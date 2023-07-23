{**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<div class="variant-links">
  {foreach from=$variants item=variant}
    <a href="{$variant.url}"
       class="{$variant.type|escape:'htmlall':'UTF-8'}"
       title="{$variant.name|escape:'htmlall':'UTF-8'}"
       {*
          TODO:
            put color in a data attribute for use with attr() as soon as browsers support it,
            see https://developer.mozilla.org/en/docs/Web/CSS/attr
        *}
      {if $variant.html_color_code} style="background-color: {$variant.html_color_code|escape:'htmlall':'UTF-8'}" {/if}
      {if $variant.texture} style="background-image: url({$variant.texture})" {/if}
    ><span class="sr-only">{$variant.name|escape:'htmlall':'UTF-8'}</span></a>
  {/foreach}
  <span class="js-count count"></span>
</div>
