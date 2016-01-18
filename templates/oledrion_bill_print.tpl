<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <title><{if $xoops_pagetitle !=''}><{$xoops_pagetitle}> : <{/if}><{$xoops_sitename}></title>

    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>" />
    <meta name="keywords" content="<{$meta_keywords}>" />
    <meta name="description" content="<{$meta_description}>" />
    <meta name="author" content="voltan" />
    <meta name="copyright" content="<{$meta_copyright}>"/>
    <meta name="generator" content="Bluefish 2.2.4" />
    <meta name="robots" content="noindex,nofollow" />

    <link rel="shortcut icon" type="image/ico" href="<{$xoops_url}>/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_url}>/xoops.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_url}>/modules/oledrion/assets/css/print.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_url}>/language/persian/style.css" />

</head>
<body onload="window.print()">
<div id="xo-print">
	<h2 class="center"><{$smarty.const._OLEDRION_BILL}></h2>

	<div id="print-info">
		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_ORDERID}></span> : <{$order.cmd_id}></div>
		<div class="pad2"><span class="bold"><{$smarty.const._DATE}></span> : <{if $order.cmd_create > 0 }><{$order.cmd_create_date}><{else}><{$order.cmd_date|date_format:"%d/%m/%Y"}><{/if}></div>
		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_FIRSTNAME}></span> : <{$order.cmd_firstname}> <{$order.cmd_lastname}></div>
		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_STREET}></span> : <{$order.cmd_adress}></div>
		<{if $ask_vatnumber && trim($order.cmd_vat_number) != ''}>
			<div class="pad2"><{$smarty.const._OLEDRION_VAT_NUMBER}> : <{$order.cmd_vat_number}></div>
		<{/if}>
		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_CITY}></span> : <{$order.cmd_town}></div>

      <div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_CP}></span> : <{$order.cmd_zip}></div>
		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_PHONE}></span> : <{$order.cmd_telephone}></div>
		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_MOBILE}></span> : <{$order.cmd_mobile}></div>

		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_EMAIL}></span> : <{$order.cmd_email}></div>
		<{if $order.cmd_uname}>
		<div class="pad2"><span class="bold"><{$smarty.const._OLEDRION_UNAME}></span> : <{$order.cmd_uname}></div>
		<{/if}>
	</div>

   <div id="print-factor">
		<table class="center" width="90%">
			<tr>
				<th class="center"><{$smarty.const._OLEDRION_ITEMS}></th>
				<th class="center"><{$smarty.const._OLEDRION_QUANTITY}></th>
				<th class="center"><{$smarty.const._OLEDRION_PRICE}></th>
				<th class="center"><{$smarty.const._OLEDRION_SHIPPING_PRICE}></th>
			</tr>
			<{foreach item=product from=$products}>
			<tr>
				<td class="center">
					<div class="oledrion_producttitle"><{$product.product_title}></div>
					<!-- <div class="oledrion_productauthor"><{if $product.product_joined_manufacturers != ''}><{$smarty.const._OLEDRION_BY}> <{$product.product_joined_manufacturers}><{/if}></div> -->
				</td>
				<td class="center"<div class="oledrion_productprice"><{$product.product_caddy.caddy_qte}></div></td>
				<td class="center"><div class="oledrion_productprice"><{$product.product_caddy.caddy_price_fordisplay}></div></td>
				<td class="center"><{$product.product_caddy.caddy_shipping_fordisplay}></td>
			</tr>
			<{if count($product.product_attributes) > 0}>
			<tr>
				<td colspan='4' class="center">
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
			<tr class="center">
				<td class="center"><h3><{$smarty.const._OLEDRION_TOTAL}><h3></td>
				<td class="center">&nbsp;</td>
				<td class="center"><div class="oledrion_productprice"><{$order.cmd_total_fordisplay}></td>
				<td class="center"><{$order.cmd_shipping_fordisplay}></td>
			</tr>
		</table>
	</div>
	<{if $order.cmd_text_fordisplay != ''}>
	<div class="oledrion_discounts">
		<h4><{$smarty.const._OLEDRION_CART4}></h4>
			<{$order.cmd_text_fordisplay}>
	</div>
	<{/if}>
</div>
</body>
</html>
