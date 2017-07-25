<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $category.cat_advertisement != ''}>
        <div id="oledrion_publicite-category"><{$category.cat_advertisement}></div>
    <{elseif $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div>
    <{/if}>

    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->

    <!-- HEADER -->
    <div id="oledrion-logo">
        <{if $category.cat_imgurl != ''}>
            <img src="<{$category.cat_full_imgurl}>" alt="<{$category.cat_title}>">
        <{/if}>
    </div>

    <div class="oledrion-cat_description">
        <{if $category.cat_title != ''}>
            <table width="100%" cellspacing="0" class="tablefix">
                <tr>
                    <td class="box_blue-clip_01"></td>
                    <td class="box_blue-clip_02"></td>
                    <td class="box_blue-clip_03"></td>
                </tr>
                <tr>
                    <td class="box_blue-clip_04"></td>
                    <td class="oledrion_catdescription">
                        <{if isset($category) }>
                            <h2><{$category.cat_title}></h2>
                            <{if $category.cat_description != ''}><{$category.cat_description}><{/if}>
                        <{/if}>

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
    </div>
    <!-- /HEADER -->

    <{if count($subCategories) > 0}>
        <div class="oledrion-subCategories"><{include file="db:oledrion_categories_list.tpl" categories=$subCategories}></div>
    <{elseif count($motherCategories) > 0}>
        <div class="oledrion-motherCategories"><{include file="db:oledrion_categories_list.tpl" categories=$motherCategories}></div>
    <{/if}>

    <{if count($products) > 0}>
        <{if isset($pagenav) }>
            <div class="center pagenav"><{$pagenav}></div><{/if}>
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
        <{if isset($pagenav) }>
            <div class="center pagenav"><{$pagenav}></div><{/if}>
    <{else}>
        <h2><{$smarty.const._OLEDRION_SORRY_NO_PRODUCT}></h2>
    <{/if}>

    <{if count($chunk1) > 0 || count($chunk2) > 0 || count($chunk3) > 0 || count($chunk4) > 0}>
        <{if count($chunk1) > 0}>
            <{include file="db:oledrion_chunk.tpl" products=$chunk1 title=$chunk1Title}>
        <{/if}>
        <{if count($chunk2) > 0}>
            <{include file="db:oledrion_chunk.tpl" products=$chunk2 title=$chunk2Title}>
        <{/if}>
        <{if count($chunk3) > 0}>
            <{include file="db:oledrion_chunk.tpl" products=$chunk3 title=$chunk3Title}>
        <{/if}>
        <{if count($chunk4) > 0}>
            <{include file="db:oledrion_chunk.tpl" products=$chunk4 title=$chunk4Title}>
        <{/if}>
    <{/if}>


    <!-- CADDY & RSS -->
    <div id="oledrion_caddy" class="right">
        <a href="<{$smarty.const.OLEDRION_URL}>caddy.php" title="<{$smarty.const._OLEDRION_CART}>"><img
                    src="<{$smarty.const.OLEDRION_IMAGES_URL}>cart.png" alt="<{$smarty.const._OLEDRION_CART}>"></a>&nbsp;
        <{if $mod_pref.rss}>
            <a href="<{$smarty.const.OLEDRION_URL}>rss.php<{if $category.cat_cid > 0}>?cat_cid=<{$category.cat_cid}><{/if}>"
               title="<{$smarty.const._OLEDRION_RSS_FEED}>"><img
                        src="<{$smarty.const.OLEDRION_IMAGES_URL}>rss.gif" alt="<{$smarty.const._OLEDRION_RSS_FEED}>"></a>
        <{/if}>
    </div>
    <!-- /CADDY & RSS -->

    <!-- CATEGORY'S FOOTER -->
    <{if $category.cat_footer != ''}>
        <div class="oledrion_publicite">
            <{$category.cat_footer}>
        </div>
    <{/if}>
    <!--* /CATEGORY'S FOOTER -->

    <!--* NOTIFICATION *-->
    <{include file='db:system_notification_select.tpl'}>
    <!-- /NOTIFICATION *-->
</div>
