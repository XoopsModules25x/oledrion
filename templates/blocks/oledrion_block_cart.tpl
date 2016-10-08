<div class="oledrion-block">
    <div class="oledrion_carttotal">
        <ul>
            <{foreach item=product from=$block.block_caddieProducts}>
                <li><a href="<{$product.product_url_rewrited}>"
                       title="<{$product.product_href_title}>"><{$product.product_title}></a></li>
            <{/foreach}>
        </ul>
    </div>
    <div class="oledrion_carttotal center"><{$smarty.const._OLEDRION_TOTAL}> <{$block.block_commandAmountTTC}> <{$block.block_money_short}></div>
    <div class="oledrion_carttotal center">
        <a href="<{$xoops_url}>/modules/oledrion/caddy.php"><{$smarty.const._MB_OLEDRION_CART}></a>
    </div>
</div>
