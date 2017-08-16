<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <div class="breadcrumb"><{$breadcrumb}></div>

    <div class="center"><h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>add-to-basket.png"
                                 alt="<{$smarty.const._MI_OLEDRION_SMNAME1}>"><{$smarty.const._MI_OLEDRION_SMNAME1}>
        </h2></div>

    <{if $emptyCart}>
        <{$smarty.const._OLEDRION_CART_IS_EMPTY}>
        <{if $isCartExists}>
            <div class="CartExists"><a
                        href="<{$smarty.const.OLEDRION_URL}>caddy.php?op=reload"><{$smarty.const._OLEDRION_RELOAD_PERSISTENT}></a>
            </div>
        <{/if}>
    <{else}>
        <form method="post" name="frmUpdate" id="frmUpdate" action="<{$smarty.const.OLEDRION_URL}>caddy.php"
              style="margin:0; padding:0; border: 0; display: inline;">
            <{securityToken}><{*//mb*}>
            <table cellspacing="0" id="oledrion_caddy">
                <tr>
                    <th><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_ITEMS}></span></th>
                    <th><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_UNIT_PRICE}></span></th>
                    <th><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_UNIT_PRICE2}></span></th>
                    <th><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_QUANTITY}></span></th>
                    <th><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_CART1}></span></th>
                    <th><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_CART2}></span></th>
                    <th><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_SHIPPING_PRICE}></span></th>
                    <th colspan="2"><span class="oledrion_caddy-titles"><{$smarty.const._OLEDRION_PRICE}></span></th>
                </tr>
                <{foreach item=product from=$caddieProducts}>
                    <tr>
                        <td>
                            <div class="oledrion_producttitle">
                                <a href="<{$product.product_url_rewrited}>"
                                   title="<{$product.product_href_title}>"><{$product.product_title}><{if $product.reduction != ''}>
                                        <sup
                                                style="color: #FF0000;"><{$product.number}></sup><{/if}></a>
                            </div>
                            <div class="oledrion_productauthor"><{if $product.manufacturersJoinList != ''}><{$smarty.const._OLEDRION_BY}> <{$product.manufacturersJoinList}><{/if}></div>
                        </td>
                        <td>
                            <div class="oledrion_productprice right"><{$product.unitBasePriceFormated}></div>
                        </td>
                        <td>
                            <div class="oledrion_productprice right"><{$product.discountedPriceFormated}></div>
                        </td>
                        <td class="center"><input type="text" name="qty_<{$product.number}>"
                                                  id="qty_<{$product.number}>" value="<{$product.product_qty}>"
                                                  size="3"></td>
                        <td>
                            <div class="oledrion_productprice right"><{$product.discountedPriceWithQuantityFormated}></div>
                        </td>
                        <td class='right'><{$product.vatRate}></td>
                        <td>
                            <div class="oledrion_productprice right"><{$product.discountedShippingFormated}></div>
                        </td>
                        <td>
                            <div class="oledrion_productprice right"><{$product.totalPriceFormated}></div>
                        </td>
                        <td>
                            <a href="<{$smarty.const.OLEDRION_URL}>caddy.php?op=delete&product_id=<{$product.number}>" <{$confirm_delete_item}>
                               title="<{$smarty.const._OLEDRION_REMOVE_ITEM}>"><img
                                        src="<{$smarty.const.OLEDRION_IMAGES_URL}>cartdelete.png"
                                        alt="<{$smarty.const._OLEDRION_REMOVE_ITEM}>"></td>
                    </tr>
                    <{if isset($product.attributes) && count($product.attributes) > 0}>
                        <tr>
                            <td colspan="10">
                                <ul>
                                    <{foreach item=attribute from=$product.attributes}>
                                        <li><{$attribute.attribute_title}>
                                            <{foreach item=option from=$attribute.attribute_options}>
                                                <div class="attribute_options"><{$option.option_name}>
                                                    : <{$option.option_price_ht_formated}>
                                                    (<{$option.option_vat_formated}>
                                                    ), <{$option.option_ttc_formated}></div>
                                            <{/foreach}>
                                        </li>
                                    <{/foreach}>
                                </ul>
                            </td>
                        </tr>
                    <{/if}>
                <{/foreach}>
                <tr class="oledrion_carttotal">
                    <td colspan="4"><h3><{$smarty.const._OLEDRION_TOTAL}></h3></td>
                    <td class="oledrion_productprice right middle"><{$commandAmount}></td>
                    <td class="oledrion_productprice right middle"><{$vatAmount}></td>
                    <td class="oledrion_productprice right middle"><{$shippingAmount}></td>
                    <td colspan="2" class="oledrion_productprice right middle"><{$commandAmountTTC}></td>
                </tr>
                <tr>
                    <td colspan="8">
                        <{$smarty.const._OLEDRION_QTE_MODIFIED}>
                        <input type="hidden" name="op" id="op" value="update">
                        <input type="submit" name="btnUpdate" id="btnUpdate"
                               value="<{$smarty.const._OLEDRION_UPDATE}>">
        </form>
        <form method="post" name="frmEmpty" id="frmEmpty"
              action="<{$smarty.const.OLEDRION_URL}>caddy.php" <{$confEmpty}>
              style="margin:0; padding:0; border: 0; display: inline;">
            <{securityToken}><{*//mb*}>
            <input type="hidden" name="op" id="op" value="empty">
            <input type="submit" name="btnEmpty" id="btnEmpty" value="<{$smarty.const._OLEDRION_EMPTY_CART}>">
        </form>
        <form method="post" name="frmGoOn" id="frmGoOn" action="<{$goOn}>"
              style="margin:0; padding:0; border: 0; display: inline;">
            <input type="submit" name="btnGoOn" id="btnGoOn" value="<{$smarty.const._OLEDRION_GO_ON}>">
        </form>
        </td>
        <td colspan="2" class="center">
            <{if $showOrderButton}>
                <form method="post" name="frmCheckout" id="frmCheckout"
                      action="<{$smarty.const.OLEDRION_URL}>checkout.php"
                      style="margin:0; padding:0; border: 0; display: inline;">
                    <input type="submit" name="btnCheckout" id="btnCheckout"
                           value="<{$smarty.const._OLEDRION_CHECKOUT}>">
                </form>
            <{/if}>
        </td>
        </tr>
        </table>

        <{if $showRegistredOnly && trim($restrict_orders_text) != ''}>
            <div class="oledrion_alert">
                <{$restrict_orders_text}>
            </div>
        <{/if}>

        <{if $discountsCount > 0}>
            <div class="oledrion_discounts">
                <h3><{$smarty.const._OLEDRION_CART4}></h3>
                <ul>
                    <{foreach item=product from=$caddieProducts}>
                        <{if $product.reduction != ''}>
                            <li class="oledrion_discount-description">
                            <sup style="color: #FF0000;"><{$product.number}></sup>
                            <{$product.reduction}></li><{/if}>
                    <{/foreach}>
                </ul>

                <{if isset($discountsDescription) && count($discountsDescription) > 0}>
                    <ul>
                        <{foreach item=discount from=$discountsDescription}>
                            <li class="oledrion_discount-description"><{$discount}></li>
                        <{/foreach}>
                    </ul>
                <{/if}>
            </div>
        <{/if}>
    <{/if}>
    <table class="oledrion_step_body oledrion_step_body_width">
        <tr>
            <td class="oledrion_step_active">
                <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_1}></span>
            </td>
            <td class="oledrion_step_img">
                <img src="<{$step2}>" alt="">
            </td>
            <td class="oledrion_step">
                <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_2}></span>
            </td>
            <td class="oledrion_step_img">
                <img src="<{$step1}>" alt="">
            </td>
            <td class="oledrion_step">
                <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_3}></span>
            </td>
        </tr>
    </table>
</div>
