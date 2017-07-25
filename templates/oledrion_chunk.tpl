<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
<div class="featured-blocks-titles">
    <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png" alt="<{$title}>"><{$title}></h2>
</div>
<table class="oledrion_productindex">
    <{foreach item=product from=$products}>
        <tr>
            <td class="oledrion_productthumb"><{if $product.product_thumb_url}><a
                    href="<{$product.product_url_rewrited}>" title="<{$product.product_href_title}>"><img
                            src="<{$product.product_thumb_full_url}>" alt="<{$product.product_href_title}>"></a><{/if}>
            </td>
            <td class="oledrion_productssummary">
                <table width="100%" cellspacing="0">
                    <tr>
                        <td class="page-curl_01">
                            <div class="oledrion_producttitle">
                                <{$product.product_recommended_picture}><a href="<{$product.product_url_rewrited}>"
                                                                           title="<{$product.product_href_title}>"><{$product.product_title}></a>
                            </div>
                            <{if $product.product_joined_manufacturers != ''}>
                                <div class="oledrion_productauthor">
                                    <img src="<{$smarty.const.OLEDRION_IMAGES_URL}>author.png"
                                         alt="<{$product.product_joined_manufacturers}>"><{$smarty.const._OLEDRION_BY}> <{$product.product_joined_manufacturers}>
                                </div>
                            <{/if}>
                            <{if $mod_pref.use_price}>
                                <div class="oledrion_productprice">
                                    <{if $product.product_stock > 0 }><{$smarty.const._OLEDRION_PRICE}> <a
                                        href="<{$smarty.const.OLEDRION_URL}>caddy.php?op=addproduct&product_id=<{$product.product_id}>"
                                        title="<{$smarty.const._OLEDRION_ADD_TO_CART}>"><{if $product.product_discount_price_ttc != ''}>
                                        <s><{$product.product_price_ttc}></s>
                                        <{$product.product_discount_price_ttc}><{else}><{$product.product_price_ttc}><{/if}>
                                        <img src="<{$smarty.const.OLEDRION_IMAGES_URL}>cartadd.png"
                                             alt="<{$smarty.const._OLEDRION_ADD_TO_CART}>">
                                        </a><{else}><{$mod_pref.nostock_msg}><{/if}>
                                </div>
                            <{/if}>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    <{/foreach}>
</table>
