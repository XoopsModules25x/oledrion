<{$product.product_final_price_ttc_formated_long}>
<{if $product.product_vat_rate.vat_rate > 0 }>
	<div class="pad2"><{$smarty.const._OLEDRION_AMOUNT_WITHOUT_VAT}> : <{$product.product_final_price_ht_formated_long}></div>
	<div class="pad2"><{$smarty.const._OLEDRION_VAT}> : <{$product.product_vat_rate.vat_rate_formated}> %</div>
	<div class="pad2"><{$smarty.const._OLEDRION_VAT_AMOUNT}> : <{$product.product_vat_amount_formated_long}></div>
<{/if}>