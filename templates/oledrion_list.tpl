<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->

    <h2><{$list.list_title}></h2>
    <div class="list_username">
        <{$smarty.const._OLEDRION_BY}> <a
                href="<{$xoops_url}>/userinfo.php?uid=<{$list.list_uid}>"><{$list.list_username}></a>
        <small>(<{$list.list_formated_date}>)</small>
    </div>
    <{if xoops_trim($list.list_description) != ''}>
        <div class="justify">
            <{$list.list_description}>
        </div>
    <{/if}>

    <{if count($products) > 0 }>
        <div class="featured-blocks-titles">
            <h3><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                     alt="<{$smarty.const._OLEDRION_RECOMMENDED}>"/><{$smarty.const._OLEDRION_PROD_IN_THIS_LIST}></h3>
        </div>
        <table class="oledrion_productindex">
            <tr>
                <{foreach item=product from=$products}>
                <td>
                    <{include file="db:oledrion_product_box.tpl"}>
                </td>
                <{if $product.product_count % $columnsCount == 0}>
            </tr>
            <tr>
                <{/if}>
                <{/foreach}>
            </tr>
        </table>
    <{/if}>

    <{if isset($otherUserLists) && count($otherUserLists) > 0 }>
        <div class="otherUserLists">
            <h4><{$smarty.const._OLEDRION_OTHER_LIST_FTUSER}></h4>
            <ul>
                <{foreach item=otherList from=$otherUserLists}>
                    <{if $otherList.list_id != $list.list_id }>
                        <li><a href="<{$otherList.list_url_rewrited}>"
                               title="<{$otherList.list_href_title}>"><{$otherList.list_title}></a>
                            <small>(<{$otherList.list_formated_date}>)</small>
                        </li>
                    <{/if}>
                <{/foreach}>
            </ul>
        </div>
    <{/if}>
</div>
