<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->
    <div class='center'>
        <{if $welcome_msg != ''}>
            <div id="oledrion_welcome"><{$welcome_msg}></div><{/if}>
    </div>
    <h2><{$smarty.const._MI_OLEDRION_SMNAME6}></h2>
    <div class="left"><{$smarty.const._OLEDRION_CATALOG_HLP}></div>

    <table class="outer tablesorter" id="allProductsTable">
        <thead>
        <tr>
            <th><{$smarty.const._OLEDRION_TITLE}></th>
            <th><{$smarty.const._OLEDRION_CATEGORY}></th>
            <{if $mod_pref.use_price}>
                <th><{$smarty.const._OLEDRION_PRICE}></th><{/if}>
            <th><{$smarty.const._OLEDRION_ADD_TO_CART}></th>
        </tr>
        </thead>
        <tbody>
        <{foreach item=product from=$products}>
            <tr>
                <td><{$product.product_recommended_picture}><a href="<{$product.product_url_rewrited}>"
                                                               title="<{$product.product_href_title}>"><{$product.product_title}></a>
                </td>
                <td><{$product.product_category.cat_title}></td>
                <{if $mod_pref.use_price}>
                    <td class='right'><{if $product.product_discount_price_ttc != ''}>
                    <s><{$product.product_price_ttc}> </s>
                    <{$product.product_discount_price_ttc}>
                <{else}>
                    <{$product.product_price_ttc_long}>
                <{/if}></td><{/if}>
                <td class="center"><{if $product.product_stock > 0 }><a
                        href="<{$smarty.const.OLEDRION_URL}>caddy.php?op=addproduct&product_id=<{$product.product_id}>"
                        title="<{$smarty.const._OLEDRION_ADD_TO_CART}>"><img
                                src="<{$smarty.const.OLEDRION_IMAGES_URL}>cartadd.gif"
                                alt="<{$smarty.const._OLEDRION_ADD_TO_CART}>">
                        </a><{else}><{$mod_pref.nostock_msg}><{/if}></td>
            </tr>
        <{/foreach}>
        </tbody>
    </table>

    <script type="text/javascript">
        jQuery().ready(function () {
                    jQuery("#allProductsTable").tablesorter();
                }
        );
    </script>

    <{if $pagenav != ''}>
        <div class="center pagenav"><{$pagenav}></div>
    <{/if}>

    <{if $pdf_catalog == 1 }>
        <h3><{$smarty.const._OLEDRION_PDF_CATALOG}></h3>
        <form name="frmCatalog" id="frmCatalog" method="post" action="<{$smarty.const.OLEDRION_URL}>makepdf.php">
            <input type="radio" name="catalogFormat" id="catalogFormat" value="0"
                   checked="checked"><{$smarty.const._OLEDRION_PDF_CATALOG1}>
            <br><input type="radio" name="catalogFormat" id="catalogFormat"
                       value="1"><{$smarty.const._OLEDRION_PDF_CATALOG2}>
            <br><input type="submit" name="btnSubmit" id="btnSubmit" value="<{$smarty.const._OLEDRION_PDF_GETIT}>">
        </form>
    <{/if}>

    <{include file='db:system_notification_select.tpl'}>
</div>
