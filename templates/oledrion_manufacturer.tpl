<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->

    <div class='center'>
        <{if $manufacturer.manu_email != '' }>
            <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>author_32x32.png"
                     alt="<{$manufacturer.manu_name}> <{$manufacturer.manu_commercialname}>" width="32" height="32"> <a
                        href="mailto:<{$manufacturer.manu_email}>"><{$manufacturer.manu_name}> <{$manufacturer.manu_commercialname}></a>
            </h2>
        <{else}>
            <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>author_32x32.png"
                     alt="<{$manufacturer.manu_name}> <{$manufacturer.manu_commercialname}>" width="32"
                     height="32"> <{$manufacturer.manu_name}> <{$manufacturer.manu_commercialname}></h2>
        <{/if}>

        <{if $manufacturer.manu_url != '' }>
            <div class="oledrion_authorurl">
                <img src="<{$smarty.const.OLEDRION_IMAGES_URL}>url.png" alt="<{$manufacturer.manu_url}>" width="16"
                     height="16"> <{$smarty.const._OLEDRION_SITEURL}> : <a
                        href="<{$manufacturer.manu_url}>" title="<{$manufacturer.manu_url}>"
                        target="_blank"><{$manufacturer.manu_url}></a>
            </div>
        <{/if}>
    </div>

    <div class="oledrion_authorbio">
        <table width="100%" cellspacing="0">
            <tr>
                <td class="page-curl_01">
                    <h3><{$smarty.const._OLEDRION_MANUFACTURER_INF}></h3>
                    <{$manufacturer.manu_bio}>
                    <div class="oledrion_authorphotos">
                        <{if $manufacturer.manu_photo1 != '' }>
                            <div class="oledrion_authorphotos_img"><img src="<{$manufacturer.manu_photo1_url}>" alt="">
                            </div><{/if}>
                        <{if $manufacturer.manu_photo2 != '' }>
                            <div class="oledrion_authorphotos_img"><img src="<{$manufacturer.manu_photo2_url}>" alt="">
                            </div><{/if}>
                        <{if $manufacturer.manu_photo3 != '' }>
                            <div class="oledrion_authorphotos_img"><img src="<{$manufacturer.manu_photo3_url}>" alt="">
                            </div><{/if}>
                        <{if $manufacturer.manu_photo4 != '' }>
                            <div class="oledrion_authorphotos_img"><img src="<{$manufacturer.manu_photo4_url}>" alt="">
                            </div><{/if}>
                        <{if $manufacturer.manu_photo5 != '' }>
                            <div class="oledrion_authorphotos_img"><img src="<{$manufacturer.manu_photo5_url}>" alt="">
                            </div><{/if}>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <{if isset($products) && count($products) > 0}>
        <div id="oledrion_related">
            <h3><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-product-person.png"
                     alt="<{$smarty.const._MI_PRODUCTSBYTHISMANUFACTURER}>"><{$smarty.const._MI_PRODUCTSBYTHISMANUFACTURER}>
            </h3>
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
            <{if $pagenav != ''}>
                <div class="center pagenav"><{$pagenav}></div>
            <{/if}>
        </div>
    <{/if}>
</div>
