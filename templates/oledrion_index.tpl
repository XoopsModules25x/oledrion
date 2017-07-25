<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->

    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>

    <!-- CATEGORIES LIST -->
    <{include file="db:oledrion_categories_list.tpl" categories=$categories}>
    <!-- /CATEGORIES LIST -->

    <!--  CADDY & RSS  -->
    <div id="oledrion_caddy" class="right">
        <a href="<{$smarty.const.OLEDRION_URL}>caddy.php" title="<{$smarty.const._OLEDRION_CART}>"><img
                    src="<{$smarty.const.OLEDRION_IMAGES_URL}>cart.png" alt="<{$smarty.const._OLEDRION_CART}>"></a>&nbsp;
        <{if $mod_pref.rss}>
            <a href="<{$smarty.const.OLEDRION_URL}>rss.php" title="<{$smarty.const._OLEDRION_RSS_FEED}>"><img
                        src="<{$smarty.const.OLEDRION_IMAGES_URL}>rss.gif"
                        alt="<{$smarty.const._OLEDRION_RSS_FEED}>"></a>
        <{/if}>
    </div>
    <!--  /CADDY & RSS  -->

    <!--  RECENT PRODUCTS  -->
    <{if count($products) > 0}>
        <div class="featured-blocks-titles">
            <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                     alt="<{$smarty.const._OLEDRION_CART}>"><{$smarty.const._OLEDRION_MOST_RECENT}></h2>
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
    <!-- /RECENT PRODUCTS -->
    <{if $pagenav != ''}>
        <div class="center pagenav"><{$pagenav}></div>
    <{/if}>
    <!--  NOTIFICATION -->
    <{include file='db:system_notification_select.tpl'}>
    <!--  /NOTIFICATION -->
</div>
