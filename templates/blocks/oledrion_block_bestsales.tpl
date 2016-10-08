<div class="oledrion-block">
    <table border="0" class="oledrion_productindex">
        <{foreach item=product from=$block.block_products}>
            <tr>
                <td class="oledrion_productthumb"><{if $product.product_thumb_url}><a
                        href="<{$product.product_url_rewrited}>" title="<{$product.product_href_title}>"><img
                                src="<{$product.product_thumb_full_url}>" alt="<{$product.product_href_title}>"
                                border="0"/></a><{/if}></td>
                <td class="oledrion_productssummary">
                    <table width="100%" cellspacing="0">
                        <tr>
                            <td class="page-curl_01">
                                <div class="oledrion_producttitle"><{$product.product_recommended_picture}><a
                                            href="<{$product.product_url_rewrited}>"
                                            title="<{$product.product_href_title}>"><{$product.product_title}></a></div>
                                <div class="oledrion_productauthor"><{if $product.product_joined_manufacturers != ''}>
                                        <img src="<{$smarty.const.OLEDRION_IMAGES_URL}>author.png" alt=""
                                             border="0" /><{$smarty.const._OLEDRION_BY}> <{$product.product_joined_manufacturers}><{/if}>
                                </div>
                                <div class="oledrion_productprice">
                                    <br><{if $product.product_stock > 0 }><{$smarty.const._OLEDRION_PRICE}> <a
                                    href="<{$smarty.const.OLEDRION_URL}>caddy.php?op=addproduct&product_id=<{$product.product_id}>"
                                    title="<{$smarty.const._OLEDRION_ADD_TO_CART}>"><{if $product.product_discount_price_ttc != ''}>
                                        <s><{$product.product_price_ttc}></s>
                                        <{$product.product_discount_price_ttc}><{else}><{$product.product_price_ttc}><{/if}>
                                    <img
                                            src="<{$smarty.const.OLEDRION_IMAGES_URL}>cartadd.png"
                                            alt="<{$smarty.const._OLEDRION_ADD_TO_CART}>" border="0"/>
                                    </a><{else}><{$block.nostock_msg}><{/if}>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        <{/foreach}>
    </table>
</div>
