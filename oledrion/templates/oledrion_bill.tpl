<div class="oledrion">
	<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
	<h2><{$smarty.const._OLEDRION_BILL}></h2>
	<div class="oledrion_otherinf">
		<{$smarty.const._DATE}> : <{$order.cmd_date|date_format:"%d/%m/%Y"}>
		<div class="pad2"><{$smarty.const._OLEDRION_COMMAND}> : <{$order.cmd_id}></div>
		<div class="pad2"><{$order.cmd_firstname}> <{$order.cmd_lastname}></div>
		<div class="pad2"><{$order.cmd_adress}></div>
		<{if $ask_vatnumber && trim($order.cmd_vat_number) != ''}>
			<div class="pad2"><{$smarty.const._OLEDRION_VAT_NUMBER}> : <{$order.cmd_vat_number}></div>
		<{/if}>
		<div class="pad2"><{$order.cmd_zip}> <{$order.cmd_town}></div>
		<div class="pad2"><{$order.cmd_country_label}></div>
		<div class="pad2"><{$order.cmd_telephone}> - <{$order.cmd_email}></div>
		<{if $order.cmd_gift}>
		<div class="pad2"><{$smarty.const._OLEDRION_GIFT}> : <{$order.cmd_gift}></div>
		<{/if}>
	</div>

	<{if $order.cmd_track}>
	<div class="oledrion_otherinf">
		<div class="pad2"><strong><{$smarty.const._OLEDRION_TRACK}></strong> : <{$order.cmd_track}></div>
		<p>برای مشاهده مکان بسته خود به وب سایت <a href="http://tntsearch.post.ir/">tntsearch.post.ir</a> مراجعه نمایید و با زدن کد رهگیری خود از مکان بسته با خبر شوید.</p>
	</div>
	<{/if}>

	<table id="oledrion_caddy" width="90%">
	<tr>
		<th class="center"><{$smarty.const._OLEDRION_ITEMS}></th>
		<th class="center"><{$smarty.const._OLEDRION_QUANTITY}></th>
		<th class="center"><{$smarty.const._OLEDRION_PRICE}></th>
		<th class="center"><{$smarty.const._OLEDRION_SHIPPING_PRICE}></th>
	</tr>
	<{foreach item=product from=$products}>
	<tr>
		<td>
			<div class="oledrion_producttitle"><{$product.product_recommended_picture}><a href="<{$product.product_url_rewrited}>" title="<{$product.product_href_title}>"><{$product.product_title}></a></div>
			<div class="oledrion_productauthor"><{if $product.product_joined_manufacturers != ''}><{$smarty.const._OLEDRION_BY}> <{$product.product_joined_manufacturers}><{/if}></div>
		</td>
		<td class="right"><div class="oledrion_productprice"><{$product.product_caddy.caddy_qte}></div></td>
		<td class="right"><div class="oledrion_productprice"><{$product.product_caddy.caddy_price_fordisplay}></div></td>
		<td class="right"><{$product.product_caddy.caddy_shipping_fordisplay}></td>
	</tr>
	<{if count($product.product_attributes) > 0}>
	<tr>
		<td colspan='4'>
			<ul>
			<{foreach item=attribute from=$product.product_attributes}>
				<li><{$attribute.attribute_title}>
				<{foreach item=option from=$attribute.attribute_options}>
					<div class="attribute_options"><{$option.ca_attribute_name}> : <{$option.ca_attribute_price_formated}></div>
				<{/foreach}>
				</li>			
			<{/foreach}>
			</ul>
		</td>
	</tr>
	<{/if}>
	<{/foreach}>
	<tr class="oledrion_carttotal">
		<td><h3><{$smarty.const._OLEDRION_TOTAL}><h3></td>
		<td>&nbsp;</td>
		<td class="right"><div class="oledrion_productprice"><{$order.cmd_total_fordisplay}></td>
		<td class="right"><{$order.cmd_shipping_fordisplay}></td>
	</tr>
	</table>
	
	<{if $order.cmd_text_fordisplay != ''}>
		<div class="oledrion_discounts">
			<h4><{$smarty.const._OLEDRION_CART4}></h4>
				<{$order.cmd_text_fordisplay}>
		</div>
	<{/if}>
	
	<div id="oledrion_caddy" class="right">
		<a href="<{$printurl}>" rel="nofollow" target="_blank" title="<{$smarty.const._OLEDRION_PRINT_VERSION}>"><img src="<{xoModuleIcons16 printer.png}>" alt="<{$smarty.const._OLEDRION_PRINT_VERSION}>" /></a>&nbsp;
	</div>
</div>