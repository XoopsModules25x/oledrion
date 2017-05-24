<!--******************-->
<!--***** WARNING ****-->
<!--******************-->
<{* ****** DON'T USE ANYHTING ELSE THAN JPEG OR PNG PICTURES OR THE PDF WILL NOT BE CREATED ****** *}>
<{* ****** This is a template for a PDF so you can't use all the html tags just this :      ****** *}>
<{* h1, h2, h3, h4, h5, h6, b, u, i, a, img, p, br, strong, em, font, blockquote, li, ul, ol, hr, td, th, tr, table, sup, sub, small *}>
<!--******************-->
<!--******************-->
<img src="<{$smarty.const.OLEDRION_IMAGES_URL}>pdf/header_first_page.jpg" alt=""/>


<!--**** LIST PRODUCTS **-->
<{foreach item=product from=$products}>
    <h1><{$product.product_title|strip_tags}></h1>
    <br>
    <{$product.product_summary|strip_tags|wordwrap:120|nl2br}>
    <{if $details == 1 }>
        <br>
        <{$product.product_description|strip_tags|wordwrap:120|nl2br}>
    <{/if}>
    <br>
    <br>
    <a href="<{$product.product_url_rewrited}>"
       title="<{$smarty.const._OLEDRION_READ_MORE}> <{$product.product_href_title}>"><{if $product.product_joined_manufacturers != ''}><{$smarty.const._OLEDRION_BY|capitalize}> <{$product.product_joined_manufacturers|strip_tags|wordwrap:100|nl2br}><{/if}> <{if $mod_pref.use_price}>- <{if $product.product_stock > 0 }><{$smarty.const._OLEDRION_PRICE}> <{if $product.product_discount_price_ttc != 0}><{$product.product_discount_price_ttc}><{else}><{$product.product_price_ttc}><{/if}></a><{else}><{$mod_pref.nostock_msg}><{/if}><{/if}>
    <br>
    <br>
    <br>
    <br>
<{/foreach}>
</table>
<!--**** /LIST products **-->

