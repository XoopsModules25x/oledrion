<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/), Design XoopsDesign (http://www.xoopsdesign.com) -->
<{if $mod_pref.advertisement != ''}>
    <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
<!-- Breadcrumb -->
<div class="breadcrumb"><{$breadcrumb}></div>
<!-- /Breadcrumb -->

<{if $welcome_msg != ''}>
    <table width="100%" cellspacing="0">
        <tr>
            <td class="box_blue-clip_01"></td>
            <td class="box_blue-clip_02"></td>
            <td class="box_blue-clip_03"></td>
        </tr>
        <tr>
            <td class="box_blue-clip_04"></td>
            <td class="welcome-message">

                <div>
                    <div id="oledrion_welcome"><{$welcome_msg}></div>
                </div>

            </td>
            <td class="box_blue-clip_05"></td>
        </tr>
        <tr>
            <td class="box_blue-clip_06"></td>
            <td class="box_blue-clip_07"></td>
            <td class="box_blue-clip_08"></td>
        </tr>
    </table>
<{/if}>

<{if isset($products) && count($products) > 0 }>
    <div class="featured-blocks-titles"><h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                                                 alt="<{$smarty.const._OLEDRION_RECOMMENDED}>"><{$smarty.const._OLEDRION_RECOMMENDED}>
        </h2></div>
    <table class="oledrion_productindex">
        <tr>
            <{foreach item=product from=$products}>
            <td>
                <{include file="db:oledrion_product_box.tpl"}>
            </td>
            <{if $columnsCount != 0 && $product.product_count % $columnsCount == 0}>
        </tr>
        <tr>
            <{/if}>
            <{/foreach}>
        </tr>
    </table>
<{/if}>

<{if $pagenav}>
    <div class="center pagenav"><{$pagenav}></div>
<{/if}>
