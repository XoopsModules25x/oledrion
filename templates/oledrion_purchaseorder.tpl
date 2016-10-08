<!--******************-->
<!--***** WARNING ****-->
<!--******************-->
<{* ****** DON'T USE ANYHTING ELSE THAN JPEG OR PNG PICTURES OR THE PDF WILL NOT BE CREATED ****** *}>
<{* ****** This is a template for a PDF so you can't use all the html tags just this :      ****** *}>
<{* h1, h2, h3, h4, h5, h6, b, u, i, a, img, p, br, strong, em, font, blockquote, li, ul, ol, hr, td, th, tr, table, sup, sub, small *}>
<!--******************-->
<!--******************-->
<!--*** PURCHASE ORDER -->
<img src="<{$smarty.const.OLEDRION_IMAGES_URL}>pdf/purchase_order_header.jpg" alt="header"/>

<table border="0">
    <tr>
        <td><b><{$smarty.const._OLEDRION_TITLE}></b></td>
        <td width="50" class="center"><b><{$smarty.const._OLEDRION_PRICE}></b></td>
        <td width="50"><b><{$smarty.const._OLEDRION_QUANTITY}></b></td>
        <td width="50"><b><{$smarty.const._OLEDRION_TOTAL}></b></td>
    </tr>
    <{foreach item=product from=$products}>
        <tr>
            <td><{$product.product_title|strip_tags|wordwrap:100|nl2br}></td>
            <td width="50"
                class="right"><{if $product.product_discount_price_ttc != 0}><{$product.product_discount_price_ttc}><{else}><{$product.product_price_ttc}><{/if}></td>
            <td width="50">x</td>
            <td width="50" class="right">=</td>
        </tr>
    <{/foreach}>
</table>

<img src="<{$smarty.const.OLEDRION_IMAGES_URL}>pdf/purchase_order_footer.jpg" alt="footer"/>
<!--** /PURCHASE ORDER -->
