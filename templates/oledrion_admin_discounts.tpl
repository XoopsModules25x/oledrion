<{if $action == 'default'}>

<{elseif $action == 'edit'}>
    <form method="post" action="<{$baseurl}>" name="frmdiscount" id="frmdiscount">
        <input type='hidden' name='op' id='op' value='discounts'/>
        <input type='hidden' name='action' id='action' value='saveedit'/>
        <input type='hidden' name='disc_id' id='disc_id' value='<{$discount.disc_id}>'/>
        <div align="center"><h3><{$formTitle}></h3>
            <table width='100%' class='outer' cellspacing='1'>
                <tr>
                    <th colspan="2"
                        align="center"><{$smarty.const._AM_OLEDRION_DISCOUNT_INFORMATION}></th> <{* Informations sur la réduction *}>
                </tr>
                <tr>
                    <td class='head'><label for="disc_title"><{$smarty.const._AM_OLEDRION_DISCOUNT_TITLE}></label></td>
                    <td class='even'><input type="text" name="disc_title" id="disc_title"
                                            value="<{$discount.disc_title}>" size="50" maxlength="255"/></td>
                </tr>
                <tr>
                    <td class='head top'><label
                                for="disc_description"><{$smarty.const._AM_OLEDRION_DISCOUNT_DESCRIPTION}></label></td>
                    <td class='even'><{$editor}></td>
                </tr>
                <tr>
                    <td class='head'><label for="disc_pediod"><{$smarty.const._AM_OLEDRION_DISCOUNT_PERIOD}></label>
                    </td>
                    <td class='even'><input type="checkbox" name="disc_pediod" id="disc_pediod"
                                            value="1" <{$discount.disc_pediod_checked}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_PERFROM}> <{$discount.disc_date_from}> <{$smarty.const._AM_OLEDRION_DISCOUNT_PERTO}> <{$discount.disc_date_to}>
                    </td>
                </tr>

                <tr>    <{* A qui ou selon quoi appliquer la réduction ? *}>
                    <th colspan="2" align="center"><{$smarty.const._AM_OLEDRION_DISCOUNT_WHOWHAT}></th>
                </tr>
                <tr>
                    <td class='head'><label for="disc_group"><{$smarty.const._AM_OLEDRION_DISCOUNT_XOOPS_GROUP}></label>
                    </td>
                    <td class='even'><select name="disc_group"
                                             id="disc_group"><{html_options options=$disc_groups_options selected=$disc_groups_selected}></select>
                    </td>
                </tr>
                <tr>
                    <td class='head'><label for="disc_cat_cid"><{$smarty.const._AM_OLEDRION_DISCOUNT_CATEGORY}></label>
                    </td>
                    <td class='even'><{$discount.disc_cat_cid_select}></td>
                </tr>
                <tr>
                    <td class='head'><label for="disc_vendor_id"><{$smarty.const._AM_OLEDRION_DISCOUNT_VENDOR}></label>
                    </td>
                    <td class='even'><select name="disc_vendor_id"
                                             id="disc_vendor_id"><{html_options options=$disc_vendor_id_options selected=$disc_vendor_id_selected}></select>
                    </td>
                </tr>
                <tr>
                    <td class='head'><label
                                for="disc_product_id"><{$smarty.const._AM_OLEDRION_DISCOUNT_PRODUCT}></label><br><span
                                class='xoops-form-element-help'><{$smarty.const._AM_OLEDRION_DISCOUNT_HELP1}></span>
                    </td>
                    <td class='even'>
                        <{$disc_product_id}><!-- <select name="disc_product_id" id="disc_product_id"><{html_options options=$disc_product_id_options selected=$disc_product_id_selected}></select> --></td>
                </tr>
                <tr>
                    <td class='head' colspan="2"><span
                                class='xoops-form-element-help'><{$smarty.const._AM_OLEDRION_DISCOUNT_HELP2}>
                            <br><{$smarty.const._AM_OLEDRION_DISCOUNT_HELP3}>
                            <br><{$smarty.const._AM_OLEDRION_DISCOUNT_HELP4}>
                            <br><{$smarty.const._AM_OLEDRION_DISCOUNT_HELP5}>
                            <br><{$smarty.const._AM_OLEDRION_DISCOUNT_HELP6}></span></td>
                </tr>

                <tr>    <{* Réduction sur le prix du produit ou le montant de la commande *}>
                    <th colspan="2" align="center"><{$smarty.const._AM_OLEDRION_DISCOUNT_REDUCTION_PRICE}></th>
                </tr>
                <tr>
                    <td class='head top'><{$smarty.const._AM_OLEDRION_DISCOUNT_REDUCTION_TYPE}></td>
                    <td class='even'>
                        <table border="0">
                            <tr>
                                <td>
                                    <input type="radio" name="disc_price_type" id="disc_price_type"
                                           value="1" <{$discount.disc_price_type_checked1}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_DEGRESSIV}>
                                    <table border="0">
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_price_degress_l1qty1"
                                                                                                             id="disc_price_degress_l1qty1"
                                                                                                             value="<{$discount.disc_price_degress_l1qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_price_degress_l1qty2"
                                                       id="disc_price_degress_l1qty2"
                                                       value="<{$discount.disc_price_degress_l1qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_price_degress_l1total"
                                                       id="disc_price_degress_l1total"
                                                       value="<{$discount.disc_price_degress_l1total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_price_degress_l2qty1"
                                                                                                             id="disc_price_degress_l2qty1"
                                                                                                             value="<{$discount.disc_price_degress_l2qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_price_degress_l2qty2"
                                                       id="disc_price_degress_l2qty2"
                                                       value="<{$discount.disc_price_degress_l2qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_price_degress_l2total"
                                                       id="disc_price_degress_l2total"
                                                       value="<{$discount.disc_price_degress_l2total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_price_degress_l3qty1"
                                                                                                             id="disc_price_degress_l3qty1"
                                                                                                             value="<{$discount.disc_price_degress_l3qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_price_degress_l3qty2"
                                                       id="disc_price_degress_l3qty2"
                                                       value="<{$discount.disc_price_degress_l3qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_price_degress_l3total"
                                                       id="disc_price_degress_l3total"
                                                       value="<{$discount.disc_price_degress_l3total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_price_degress_l4qty1"
                                                                                                             id="disc_price_degress_l4qty1"
                                                                                                             value="<{$discount.disc_price_degress_l4qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_price_degress_l4qty2"
                                                       id="disc_price_degress_l4qty2"
                                                       value="<{$discount.disc_price_degress_l4qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_price_degress_l4total"
                                                       id="disc_price_degress_l4total"
                                                       value="<{$discount.disc_price_degress_l4total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_price_degress_l5qty1"
                                                                                                             id="disc_price_degress_l5qty1"
                                                                                                             value="<{$discount.disc_price_degress_l5qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_price_degress_l5qty2"
                                                       id="disc_price_degress_l5qty2"
                                                       value="<{$discount.disc_price_degress_l5qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_price_degress_l5total"
                                                       id="disc_price_degress_l5total"
                                                       value="<{$discount.disc_price_degress_l5total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" name="disc_price_type" id="disc_price_type"
                                           value="2" <{$discount.disc_price_type_checked2}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_AMOUNT_PERCENT}>
                                    <table border="0">
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><input type="text" name="disc_price_amount_amount"
                                                       id="disc_price_amount_amount"
                                                       value="<{$discount.disc_price_amount_amount}>" size="5"
                                                       maxlength="10"/> <input type="radio"
                                                                               name="disc_price_amount_type"
                                                                               id="disc_price_amount_type"
                                                                               value="1" <{$discount.disc_price_amount_type_checked1}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_PERCENT}>
                                                <input
                                                        type="radio" name="disc_price_amount_type"
                                                        id="disc_price_amount_type"
                                                        value="2" <{$discount.disc_price_amount_type_checked2}> /> <{$currencyName}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td>
                                                <{$smarty.const._AM_OLEDRION_DISCOUNT_ON}> <input type="radio"
                                                                                                  name="disc_price_amount_on"
                                                                                                  id="disc_price_amount_on"
                                                                                                  value="1" <{$discount.disc_price_amount_on_checked1}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_THE_PRODUCT}>
                                                <input type="radio" name="disc_price_amount_on"
                                                       id="disc_price_amount_on"
                                                       value="2" <{$discount.disc_price_amount_on_checked2}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_THE_CART}>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class='head top'><{$smarty.const._AM_OLEDRION_DISCOUNT_IN_WHICH_CASE}></td>
                    <td class='even'>
                        <input type="radio" name="disc_price_case" id="disc_price_case"
                               value="1" <{$discount.disc_price_case_checked1}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_ALL_CASES}>
                        <br>
                        <input type="radio" name="disc_price_case" id="disc_price_case"
                               value="2" <{$discount.disc_price_case_checked2}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_FIRST_PURCHASE}>
                        <br>
                        <input type="radio" name="disc_price_case" id="disc_price_case"
                               value="3" <{$discount.disc_price_case_checked3}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_NEVER_BOUGHT}>
                        <br>
                        <input type="radio" name="disc_price_case" id="disc_price_case"
                               value="4" <{$discount.disc_price_case_checked4}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_IS}>
                        <select
                                name="disc_price_case_qty_cond"
                                id="disc_price_case_qty_cond"><{html_options options=$disc_price_case_qty_cond_options selected=$disc_price_case_qty_cond_selected}></select>
                        <input type="text"
                               name="disc_price_case_qty_value"
                               id="disc_price_case_qty_value"
                               value="<{$discount.disc_price_case_qty_value}>"
                               size="3"
                               maxlength="5"/>
                    </td>
                </tr>
                <tr>
                    <th colspan="2"
                        align="center"><{$smarty.const._AM_OLEDRION_DISCOUNT_SHIPPING_REDUCTIONS}></th> <{* Réductions sur les frais de port *}>
                </tr>
                <tr>
                    <td class='head top'><{$smarty.const._AM_OLEDRION_DISCOUNT_SHIPPINGS_ARE}></td>
                    <td class='even'>
                        <table border="0">
                            <tr>
                                <td><input type="radio" name="disc_shipping_type" id="disc_shipping_type"
                                           value="1" <{$discount.disc_shipping_type_checked1}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_FULL_PAY}>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" name="disc_shipping_type" id="disc_shipping_type"
                                           value="2" <{$discount.disc_shipping_type_checked2}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_SHIPPING_FREE}>
                                    <table border="0">
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_ORDER_OVER}> <input type="text"
                                                                                                          name="disc_shipping_free_morethan"
                                                                                                          id="disc_shipping_free_morethan"
                                                                                                          value="<{$discount.disc_shipping_free_morethan}>"
                                                                                                          size="5"
                                                                                                          maxlength="10"/> <{$currencyName}>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" name="disc_shipping_type" id="disc_shipping_type"
                                           value="3" <{$discount.disc_shipping_type_checked3}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_REDUCED_FOR}>
                                    <table border="0">
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><input type="text" name="disc_shipping_reduce_amount"
                                                       id="disc_shipping_reduce_amount"
                                                       value="<{$discount.disc_shipping_reduce_amount}>" size="5"
                                                       maxlength="10"/> <{$currencyName}> <{$smarty.const._AM_OLEDRION_DISCOUNT_REDUCED_IF}>
                                                <input type="text" name="disc_shipping_reduce_cartamount"
                                                       id="disc_shipping_reduce_cartamount"
                                                       value="<{$discount.disc_shipping_reduce_cartamount}>"
                                                       size="5" maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" name="disc_shipping_type" id="disc_shipping_type"
                                           value="4" <{$discount.disc_shipping_type_checked4}> /> <{$smarty.const._AM_OLEDRION_DISCOUNT_DEGRESSIV}>
                                    <table border="0">
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_shipping_degress_l1qty1"
                                                                                                             id="disc_shipping_degress_l1qty1"
                                                                                                             value="<{$discount.disc_shipping_degress_l1qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_shipping_degress_l1qty2"
                                                       id="disc_shipping_degress_l1qty2"
                                                       value="<{$discount.disc_shipping_degress_l1qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_shipping_degress_l1total"
                                                       id="disc_shipping_degress_l1total"
                                                       value="<{$discount.disc_shipping_degress_l1total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_shipping_degress_l2qty1"
                                                                                                             id="disc_shipping_degress_l2qty1"
                                                                                                             value="<{$discount.disc_shipping_degress_l2qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_shipping_degress_l2qty2"
                                                       id="disc_shipping_degress_l2qty2"
                                                       value="<{$discount.disc_shipping_degress_l2qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_shipping_degress_l2total"
                                                       id="disc_shipping_degress_l2total"
                                                       value="<{$discount.disc_shipping_degress_l2total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_shipping_degress_l3qty1"
                                                                                                             id="disc_shipping_degress_l3qty1"
                                                                                                             value="<{$discount.disc_shipping_degress_l3qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_shipping_degress_l3qty2"
                                                       id="disc_shipping_degress_l3qty2"
                                                       value="<{$discount.disc_shipping_degress_l3qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_shipping_degress_l3total"
                                                       id="disc_shipping_degress_l3total"
                                                       value="<{$discount.disc_shipping_degress_l3total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_shipping_degress_l4qty1"
                                                                                                             id="disc_shipping_degress_l4qty1"
                                                                                                             value="<{$discount.disc_shipping_degress_l4qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_shipping_degress_l4qty2"
                                                       id="disc_shipping_degress_l4qty2"
                                                       value="<{$discount.disc_shipping_degress_l4qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_shipping_degress_l4total"
                                                       id="disc_shipping_degress_l4total"
                                                       value="<{$discount.disc_shipping_degress_l4total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                        <tr>
                                            <td width="5%">&nbsp;</td>
                                            <td><{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_FROM}> <input type="text"
                                                                                                             name="disc_shipping_degress_l5qty1"
                                                                                                             id="disc_shipping_degress_l5qty1"
                                                                                                             value="<{$discount.disc_shipping_degress_l5qty1}>"
                                                                                                             size="3"
                                                                                                             maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_TO}>
                                                <input type="text"
                                                       name="disc_shipping_degress_l5qty2"
                                                       id="disc_shipping_degress_l5qty2"
                                                       value="<{$discount.disc_shipping_degress_l5qty2}>"
                                                       size="3"
                                                       maxlength="5"/> <{$smarty.const._AM_OLEDRION_DISCOUNT_QUANTITY_INCLUDED}>
                                                <input type="text" name="disc_shipping_degress_l5total"
                                                       id="disc_shipping_degress_l5total"
                                                       value="<{$discount.disc_shipping_degress_l5total}>" size="5"
                                                       maxlength="10"/> <{$currencyName}></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class='head'>&nbsp;</td>
                    <td class='even'><input type="submit" name="btngo" id="btngo" value="<{$label_submit}>"/></td>
                </tr>
            </table>
    </form>
<{/if}>
