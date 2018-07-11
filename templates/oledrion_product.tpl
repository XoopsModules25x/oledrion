<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if isset($mandatoryFieldsCount)}>
        <script type="text/javascript">
            function updatePrice() {
                var formContent = jQuery("#frmCart").serialize();
                jQuery('#oledrion_final_price_ttc').load('ajax.php', {
                    op: 'updatePrice',
                    product_id: <{$product.product_id}>,
                    formcontent: formContent
                });
            }

            jQuery().ready(function () {
                // Changement de prix
                jQuery('.oledrion_attribute').change(function () {
                    updatePrice();
                });
                jQuery('.oledrion_attribute').click(function () {
                    updatePrice();
                });
                updatePrice();

                // Required fields
                jQuery.validator.messages.required = "";

                jQuery("#frmCart").validate({
                    invalidHandler: function (e, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            var message = "<{$smarty.const._OLEDRION_VALIDATE_ERROR1}>";
                            jQuery("div.error span").html(message);
                            jQuery("div.error").show();
                        } else {
                            jQuery("div.error").hide();
                        }
                    }
                });
            });
        </script>
    <{/if}>

    <{if $product.product_category.cat_advertisement != ''}>
        <div id="oledrion_publicite-category"><{$product.product_category.cat_advertisement}></div>
    <{elseif $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div>
    <{/if}>

    <!--********* BREADCRUMB ******-->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!--********* /BREADCRUMB ******-->


    <!--**** PRODUCT INFORMATION *****-->
    <table cellspacing="0" width="100%" class="tablefix">
        <tr>
            <td class="view-product-shad1_01"></td>
            <td colspan="2" class="view-product-shad1_02"></td>
        </tr>
        <tr>
            <td class="view-product-shad1_03"></td>
            <td class="oledrion_productdescription">
                <table cellspacing="0">
                    <tr>
                        <td colspan="2" class="oledrion_producttitle_view-product">
                            <strong><{$product.product_recommended_picture}><{$product.product_title}></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="top width45">
                            <!--********* VOTES ******-->
                            <{if $canRateProducts}>
                                <div class="oledrion_rating">
                                    <div class="floatleft"><{$smarty.const._OLEDRION_RATINGC}> <{$product.product_rating_formated}>
                                        (<{$product.product_votes_count}>)
                                    </div>
                                    <div class="vote">
                                        <input value="<{$product.product_rating_formated}>" step="1"
                                               id="backing<{$product.product_id}>" type="range">
                                        <div class="floatleft rateit"
                                             data-url="<{$smarty.const.OLEDRION_URL}>ajax.php?op=rate"
                                             data-product="<{$product.product_id}>"
                                             data-rateit-backingfld="#backing<{$product.product_id}>"
                                             data-rateit-resetable="false" data-rateit-ispreset="true"
                                             data-rateit-min="0"
                                             data-rateit-max="10"></div>
                                    </div>
                                    <{if $userCanRate}>
                                        <script type="text/javascript">
                                            $('.vote .rateit').bind('rated reset', function (e) {
                                                var ri = $(this);
                                                var rating = ri.rateit('vote');
                                                var product_id = ri.data('product');
                                                var url = ri.data('url');
                                                ri.rateit('readonly', true);
                                                $.ajax({
                                                    url: url,
                                                    data: {product_id: product_id, rating: rating},
                                                    type: 'POST',
                                                    dataType: "json",
                                                    success: function (result) {
                                                        if (!result.status == 1) {
                                                            alert(result.message);
                                                        }
                                                    }
                                                });
                                            });
                                        </script>
                                    <{/if}>
                                    <div class="clear"></div>
                                </div>
                            <{/if}>
                            <!--********* /VOTES ******-->
                            <{if $product.product_thumb_full_url != ''}>
                                <div class="oledrion_productthumb-big">
                                    <img src="<{$product.product_image_full_url}>"
                                         alt="<{$product.product_href_title}>">
                                </div>
                            <{/if}>
                        </td>
                        <td class="top width45">
                            <{if $product.product_sku != ''}>
                            <div class="oledrion_productdescription">
                                <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_NUMBER}></span>: <{$product.product_sku}><{/if}> <{if $product.product_extraid != ''}> <{$smarty.const._OLEDRION_EXTRA_ID}>
                                : <{$product.product_extraid}>
                            </div>
                            <{/if}>
                            <{if $product_joined_manufacturers != ''}>
                                <div class="oledrion_productauthor_view-product">
                                    <img src="<{$smarty.const.OLEDRION_IMAGES_URL}>author.png" alt="<{$product_joined_manufacturers}>">
                                    <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_BY}></span>
                                    <{$product_joined_manufacturers}>
                                </div>
                            <{/if}>
                            <!-- Price box -->
                            <{if $mod_pref.use_price}>
                                <div class="oledrion_productprice_view-product">
                                    <div class="oledrion_view-product_price">
                                        <{if $product.product_stock ==  0 }>
                                            <{$mod_pref.nostock_msg}>
                                        <{elseif isset($product.product_attributes) && count($product.product_attributes) == 0 }>
                                            <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_PRICE}></span>
                                            :<span class="bold">
                                                   <{if $product.product_discount_price_ttc != ''}>
                                                       <s><{$product.product_price_ttc}> </s>
                                                       <{$product.product_discount_price_ttc}>
                                                   <{else}>
                                                       <{$product.product_price_ttc}>
                                                   <{/if}>
                                            </span>
                                            <a href="<{$smarty.const.OLEDRION_URL}>caddy.php?op=addproduct&product_id=<{$product.product_id}>"
                                               title="<{$smarty.const._OLEDRION_ADD_TO_CART}>"><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>cartadd.png" alt=""></a>
                                            <{if $product.product_ecotaxe != ''}>
                                                <div class="oledrion_view-product_price_ecotaxe">
                                                    <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_ECOTAXE}></span>
                                                    : <{$product.product_ecotaxe_formated}>
                                                </div>
                                            <{/if}>
                                        <{else}>
                                            <div class="oledrion_view-product_price_frmcart">
                                                <form method="post" name="frmCart" id="frmCart"
                                                      action="<{$smarty.const.OLEDRION_URL}>caddy.php?op=addproduct&product_id=<{$product.product_id}>">
                                                    <table class="tablefix">
                                                        <tr>
                                                            <td>
                                                                <{foreach item=attribute from=$product.product_attributes}>
                                                                    <div class="pad2"><{$attribute}></div>
                                                                <{/foreach}>
                                                            </td>
                                                            <td>
                                                                <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_PRICE}></span>
                                                                : <{if $product.product_discount_price_ttc != ''}>
                                                                    <s><{$product.product_price_ttc}> </s>
                                                                    <b><{$product.product_discount_price_ttc}></b>
                                                                <{else}>
                                                                    <{$product.product_price_ttc}>
                                                                <{/if}>
                                                                <span class="oledrion_final_price_ttc"><{$product.product_final_price_ttc_formated}></span>
                                                                <{if $product.product_ecotaxe != ''}>
                                                                    <div class="oledrion_view-product_price_ecotaxe"><span
                                                                                class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_ECOTAXE}></span>
                                                                        : <{$product.product_ecotaxe_formated}></div>
                                                                <{/if}>
                                                                <br><br>
                                                                <input type="image" src="<{$addToCartImage}>"
                                                                       id="buy_button"
                                                                       alt="<{$smarty.const._OLEDRION_ADD_TO_CART}>"
                                                                       title="<{$smarty.const._OLEDRION_ADD_TO_CART}>"
                                                                       value="<{$smarty.const._OLEDRION_ADD_TO_CART}>">
                                                                <div class="error" style="display: none;"><br>
                                                                    <img src="<{$smarty.const.OLEDRION_IMAGES_URL}>warning.png"
                                                                             width="24" height="24"
                                                                             style="float:left; margin: -5px 10px 0 0; ">
                                                                    <span></span>.<br clear="all">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form>
                                            </div>
                                        <{/if}>
                                    </div>
                                    <{if $product.product_shipping_price != 0}>
                                        <div class="oledrion_view-product_shipping-price">
                                            <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_SHIPPING_PRICE}></span>: <{$product.product_shipping_price_formated}>
                                        </div>
                                    <{/if}>
                                </div>
                            <{else}>
                                <br>
                            <{/if}>
                            <!-- /Price box -->
                            <div class="oledrion_productdate">
                                <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_DATE}></span>
                                : <{$product.product_date}>
                                <{if $product.product_delivery_time > 0}>
                                    <br>
                                    <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_DELIVERY_TIME}></span>
                                    : <{$product.product_delivery_time}> <{$smarty.const._OLEDRION_DAYS}>
                                <{/if}>
                            </div>
                            <div class="oledrion_productlangue">
                                <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_VENDOR}></span>: <{$product.product_vendor.vendor_name}>
                            </div>
                            <br>
                            <div class="oledrion_productproperty">
                                <{if $product.product_property1}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property1_title}></span>: <{$product.product_property1}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property2}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property2_title}></span>: <{$product.product_property2}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property3}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property3_title}></span>: <{$product.product_property3}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property4}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property4_title}></span>: <{$product.product_property4}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property5}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property5_title}></span>: <{$product.product_property5}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property6}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property6_title}></span>: <{$product.product_property6}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property7}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property7_title}></span>: <{$product.product_property7}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property8}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property8_title}></span>: <{$product.product_property8}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property9}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property9_title}></span>: <{$product.product_property9}>
                                    </div>
                                <{/if}>
                                <{if $product.product_property10}>
                                    <div class="oledrion_productdescription">
                                        <span class="oledrion_productdescription-contentTitles"><{$product.product_property10_title}></span>: <{$product.product_property10}>
                                    </div>
                                <{/if}>
                            </div>
                            <{if $product.product_attachment != ''}>
                                <a href="<{$smarty.const.OLEDRION_ATTACHED_FILES_URL}>/<{$product.product_attachment}>"
                                   target="_blank"><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>attach.gif"
                                                        alt="<{$smarty.const._OLEDRION_ATTACHED_FILE}>" width="9"
                                                        height="15"> <{$smarty.const._OLEDRION_ATTACHED_FILE}></a>
                            <{/if}>
                            <{if $currentUserId > 0}>
                                <br>
                                <a href="<{$smarty.const.OLEDRION_URL}>my-lists.php?op=addProduct&product_id=<{$product.product_id}>"><img
                                            src="<{$addToWishList}>"
                                            alt="<{$smarty.const._OLEDRION_ADD_TO_LIST}>"></a>
                            <{/if}>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'>
                            <!-- product summary and description-->
                            <{if $product.product_summary != '' || $product.product_description!= ''}>
                                <table width="100%" cellspacing="0" class="tablefix">
                                    <tr>
                                        <td class="box_blue-clip_01"></td>
                                        <td class="box_blue-clip_02"></td>
                                        <td class="box_blue-clip_03"></td>
                                    </tr>
                                    <tr>
                                        <td class="box_blue-clip_04"></td>
                                        <td class="oledrion_catdescription">
                                            <{if $product.product_summary != ''}>
                                                <div class="oledrion_productssummary_view-product">
                                                <h3><{$smarty.const._OLEDRION_SUMMARY}></h3><{$product.product_summary}>
                                                </div><{/if}>
                                            <{if $product.product_description!= ''}>
                                                <div class="oledrion_description_view-product">
                                                <h3><{$smarty.const._OLEDRION_DESCRIPTION}></h3><{$product.product_description}>
                                                </div><{/if}>
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
                            <!-- /product summary and description-->

                            <{if $product.product_width != '' || $product.product_weight != 0 || $product.product_url != '' || $product.product_url2 != '' || $product.product_url3 != ''}>
                                <div class="oledrion_otherinf">
                                    <h3><{$smarty.const._OLEDRION_OTHER_INFORMATIONS}></h3>
                                    <{if $product.product_width != ''}>
                                        <div>
                                        <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_FORMAT}></span>
                                        : <{$product.product_width}>
                                        x <{$product.product_length}> <{$product.product_unitmeasure1}></div><{/if}>
                                    <{if $product.product_weight != 0}>
                                        <div>
                                        <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_WEIGHT}></span>
                                        : <{$product.product_weight}> <{$product.product_unitmeasure2}></div><{/if}>
                                    <{if $product.product_url != ''}>
                                        <div><span
                                                class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_SITEURL}></span>:
                                        <a href="<{$product.product_url}>"
                                           target="_blank"><{$smarty.const._OLEDRION_URL}></a>
                                        </div><{/if}>
                                    <{if $product.product_url2 != ''}>
                                        <div><span
                                                class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_SITEURL}></span>:
                                        <a href="<{$product.product_url2}>"
                                           target="_blank"><{$smarty.const._OLEDRION_URL}></a>
                                        </div><{/if}>
                                    <{if $product.product_url3 != ''}>
                                        <div><span
                                                class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_SITEURL}></span>:
                                        <a href="<{$product.product_url3}>"
                                           target="_blank"><{$smarty.const._OLEDRION_URL}></a>
                                        </div><{/if}>
                                </div>
                            <{/if}>

                            <{if isset($product.attached_files) && count($product.attached_files) > 0}>
                                <div class="oledrion_otherinf">
                                    <h3><{$smarty.const._OLEDRION_ATTACHED_FILES}></h3>
                                    <{if $product.attached_mp3_count > 0}>
                                        <div>
                                            <span class="oledrion_productdescription-contentTitles"><{$smarty.const._OLEDRION_MUSIC}></span>
                                            <div class='left' id='DewPlayerContainer'></div>
                                        </div>
                                        <script type="text/javascript">
                                            var paramsDew = {
                                                wmode: "transparent"
                                            };

                                            jQuery().ready(function () {
                                                jQuery('#DewPlayerContainer').flash({
                                                    swf: '<{$smarty.const.OLEDRION_URL}>dewplayer/<{if $product.attached_mp3_count > 1}>dewplayer-multi.swf<{else}>dewplayer.swf<{/if}>',
                                                    hasVersion: 9,
                                                    height: <{$smarty.const.OLEDRION_DEWPLAYER_HEIGHT}>,
                                                    width: <{$smarty.const.OLEDRION_DEWPLAYER_WIDTH}>,
                                                    params: paramsDew,
                                                    flashvars: {
                                                        mp3: '<{$mp3FilesList}>'
                                                    }
                                                });
                                            });
                                        </script>
                                    <{/if}>
                                    <{if $product.attached_non_mp3_count > 0}>
                                        <br>
                                        <div>
                                            <{foreach item=attachedFile from=$product.attached_files}>
                                                <{if !$attachedFile.file_is_mp3}>
                                                    <span class="oledrion_productdescription-contentTitles"><a
                                                                href="javascript:openWithSelfMain('<{$smarty.const.OLEDRION_URL}>media.php?product_id=<{$product.product_id}>&type=attachment&file_id=<{$attachedFile.file_id}>', '',<{$smarty.const.OLEDRION_POPUP_MEDIA_WIDTH}>, <{$smarty.const.OLEDRION_POPUP_MEDIA_HEIGHT}>);"
                                                                rel="nofollow"><{$attachedFile.file_description}></a></span>
                                                    <br>
                                                <{/if}>
                                            <{/foreach}>
                                        </div>
                                    <{/if}>
                                </div>
                            <{/if}>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="view-product-shad2_03"></td>
        </tr>
        <tr>
            <td colspan="2" class="view-product-shad2_02"></td>
            <td class="view-product-shad2_01"></td>
        </tr>
    </table>
    <!--**** /product INFORMATIONS *****-->

    <!--***** RELATED PRODUCTS *****-->
    <{if isset($product_related_products) && count($product_related_products) > 0}>
        <div id="oledrion_related">
            <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                     alt="<{$smarty.const._OLEDRION_CART}>"><{$smarty.const._OLEDRION_RELATED_PRODUCTS}></h2>
            <table class='center oledrion_categorylist'>
                <tr>
                    <{foreach item=oneitem from=$product_related_products}>
                    <td class="center top">
                        <{include file="db:oledrion_product_box.tpl" product=$oneitem}>
                    </td>
                    <{if $columnsCount != 0 && $oneitem.count % $columnsCount == 0}>
                </tr>
                <tr>
                    <{/if}>
                    <{/foreach}>
                </tr>
            </table>
        </div>
    <{/if}>
    <!--***** /RELATED PRODUCTS *****-->

    <!--******** OTHER PRODUCTS *********-->
    <{if $showprevnextlink  || $summarylast > 0 || $summarycategory > 0 || $better_together > 0}>
        <div id="oledrion_otherproducts">
            <{if $previous_product_id != 0 || $next_product_id != 0}>
                <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                         alt="<{$smarty.const._OLEDRION_OTHER_PRODUCTS}>"><{$smarty.const._OLEDRION_OTHER_PRODUCTS}>
                </h2>
            <{/if}>
            <{if $previous_product_id != 0}>
                <br>
                <a href="<{$previous_product_url_rewrited}>" title="<{$previous_product_href_title}>"><img
                            src="<{$smarty.const.OLEDRION_IMAGES_URL}>go-previous.png"
                            alt="<{$previous_product_title}>"> <{$smarty.const._OLEDRION_PREVIOUS_PRODUCT}>
                    : <{$previous_product_title}></a>
            <{/if}>
            <{if $next_product_id != 0}>
                <br>
                <a href="<{$next_product_url_rewrited}>" title="<{$next_product_href_title}>"><img
                            src="<{$smarty.const.OLEDRION_IMAGES_URL}>go-next.png"
                            alt="<{$next_product_title}>"> <{$smarty.const._OLEDRION_NEXT_PRODUCT}>
                    : <{$next_product_title}></a>
            <{/if}>

            <{if $better_together > 0 && $bestwith}>
                <br>
                <img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-star.png" alt="<{$bestwith.product_title}>">
                <{$smarty.const._OLEDRION_BEST_WITH}>
                <a href="<{$bestwith.product_url_rewrited}>"
                   title="<{$bestwith.product_href_title}>"><{$bestwith.product_title}></a>
            <{/if}>

            <{if isset($product_all_categs) && count($product_all_categs) > 0}>
                <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                         alt="<{$smarty.const._OLEDRION_RECENT_CATEGS}>"><{$smarty.const._OLEDRION_RECENT_CATEGS}></h2>
                <table class='center oledrion_lastproducts'>
                    <tr>
                        <{foreach item=oneitem from=$product_all_categs}>
                        <td class="center top">
                            <{include file="db:oledrion_product_box.tpl" product=$oneitem}>
                        </td>
                        <{if $columnsCount != 0 && $oneitem.count % $columnsCount == 0}>
                    </tr>
                    <tr>
                        <{/if}>
                        <{/foreach}>
                    </tr>
                </table>
            <{/if}>

            <{if isset($product_current_categ) && count($product_current_categ) > 0}>
                <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                         alt="<{$smarty.const._OLEDRION_RECENT_CATEG}>"><{$smarty.const._OLEDRION_RECENT_CATEG}></h2>
                <table class='center oledrion_lastproducts'>
                    <tr>
                        <{foreach item=oneitem from=$product_current_categ}>
                        <td class="center top">
                            <{include file="db:oledrion_product_box.tpl" product=$oneitem}>
                        </td>
                        <{if $columnsCount != 0 && $oneitem.count % $columnsCount == 0}>
                    </tr>
                    <tr>
                        <{/if}>
                        <{/foreach}>
                    </tr>
                </table>
            <{/if}>
        </div>
    <{/if}>
    <!--******** /OTHER PRODUCTS *********-->

    <{* TAGS *}>
    <{if isset($tagbar) }>
        <br>
        <{include file="db:tag_bar.tpl"}>
    <{/if}>

    <!--********* CADDY ******-->
    <div id="oledrion_caddy" class="right">
        <br>
        <a href="<{$smarty.const.OLEDRION_URL}>caddy.php" title="<{$smarty.const._OLEDRION_CART}>"><img
                    src="<{$smarty.const.OLEDRION_IMAGES_URL}>cart.png" alt="<{$smarty.const._OLEDRION_CART}>"></a>&nbsp;
        <{if $mod_pref.rss}>
            <a href="<{$smarty.const.OLEDRION_URL}>rss.php" title="<{$smarty.const._OLEDRION_RSS_FEED}>"><img
                        src="<{$smarty.const.OLEDRION_IMAGES_URL}>rss.gif"
                        alt="<{$smarty.const._OLEDRION_RSS_FEED}>"></a>
            &nbsp;
        <{/if}>
        <a href="<{$baseurl}>&op=print" rel="nofollow" target="_blank"
           title="<{$smarty.const._OLEDRION_PRINT_VERSION}>"><img src="<{xoModuleIcons16 printer.png}>"
                                                                  alt="<{$smarty.const._OLEDRION_PRINT_VERSION}>"></a>&nbsp;
        <a href="<{$mail_link}>" rel="nofollow" target="_blank" title="<{$smarty.const._OLEDRION_TELLAFRIEND}>"><img
                    src="<{xoModuleIcons16 mail_forward.png}>"
                    alt="<{$smarty.const._OLEDRION_TELLAFRIEND}>"></a>&nbsp;
        <{if $mod_pref.isAdmin}>
            <a href="<{$smarty.const.OLEDRION_URL}>admin/index.php?op=products&action=edit&id=<{$product.product_id}>"
               target="_blank" title="<{$smarty.const._EDIT}>"><img
                        src="<{xoModuleIcons16 edit.png}>" alt="<{$smarty.const._EDIT}>"></a>
            &nbsp;
            <a href="<{$smarty.const.OLEDRION_URL}>admin/index.php?op=products&action=confdelete&id=<{$product.product_id}>"
               title="<{$smarty.const._DELETE}>"><img src="<{xoModuleIcons16 delete.png}>"
                                                      alt="<{$smarty.const._DELETE}>"></a>
        <{/if}>
        <{if $canChangeQuantity}><a href="<{$baseurl}>&stock=add" title="<{$ProductStockQuantity}>"><img
                    src="<{$smarty.const.OLEDRION_IMAGES_URL}>plus.gif" alt="<{$ProductStockQuantity}>">
            </a> <{if $product.product_stock -1 > 0}><a href="<{$baseurl}>&stock=substract"
                                                        title="<{$ProductStockQuantity}>"><img
                    src="<{$smarty.const.OLEDRION_IMAGES_URL}>minus.gif"
                    alt="<{$ProductStockQuantity}>"></a><{/if}><{/if}>
    </div>
    <br>
    <!--********* /CADDY ******-->

    <{* Pour afficher le pied de page de la catégorie du produit *}>
    <{* <{$product.product_category.cat_footer}> *}>

    <!--******** COMMENTS ***-->
    <div style="text-class: center; padding: 3px; margin:3px;">
        <{$commentsnav}>
        <{$lang_notice}>
    </div>

    <div style="margin:3px; padding: 3px;">
        <{if $comment_mode == "flat"}>
            <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
            <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
            <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    </div>
    <{include file='db:system_notification_select.tpl'}>
    <!--******** /COMMENTS ***-->
</div>
