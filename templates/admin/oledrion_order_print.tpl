<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <title><{if $xoops_pagetitle !=''}><{$xoops_pagetitle}> : <{/if}><{$xoops_sitename}></title>

    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>" />
    <meta name="keywords" content="<{$meta_keywords}>" />
    <meta name="description" content="<{$meta_description}>" />
    <meta name="author" content="voltan" />
    <meta name="copyright" content="<{$meta_copyright}>"/>
    <meta name="generator" content="Bluefish 2.2.3" />
    <meta name="robots" content="noindex,nofollow" />

    <link rel="shortcut icon" type="image/ico" href="<{$xoops_url}>/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_url}>/xoops.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_url}>/modules/oledrion/assets/css/print.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_url}>/language/persian/style.css" />

</head>
<body onload="window.print()">
<div id="xo-print">
	<h2 class="center">فاکتور اینترنتی</h2>
	<h3 class="center">لیلیوم رایحه خوش با هم بودن</h3>

	<div id="print-info">
		<div class="pad2"><span class="bold">شماره سفارش</span> : <{$order.cmd_id}></div>
		<div class="pad2"><span class="bold">تاریخ سفارش</span> : <{if $order.cmd_create > 0 }><{$order.cmd_create_date}><{else}><{$order.cmd_date|date_format:"%d/%m/%Y"}><{/if}></div>
		<div class="pad2"><span class="bold">نام و نام خانوادگی</span> : <{$order.cmd_firstname}> <{$order.cmd_lastname}></div>
		<div class="pad2"><span class="bold">آدرس</span> : <{$order.cmd_adress}></div>
		<{if $ask_vatnumber && trim($order.cmd_vat_number) != ''}>
			<div class="pad2"><{$smarty.const._OLEDRION_VAT_NUMBER}> : <{$order.cmd_vat_number}></div>
		<{/if}>
		<div class="pad2"><span class="bold">شهر</span> : <{$order.cmd_town}></div>
      <div class="pad2"><span class="bold">کد پستی</span> : <{$order.cmd_zip}></div>
		<div class="pad2"><span class="bold">تلفن</span> : <{$order.cmd_telephone}></div>
		<div class="pad2"><span class="bold">موبایل</span> : <{$order.cmd_mobile}></div>
		<div class="pad2"><span class="bold">ایمیل</span> : <{$order.cmd_email}></div>
		<{if $order.cmd_uname}>
		<div class="pad2"><span class="bold">نام کاربری</span> : <{$order.cmd_uname}></div>
		<{/if}>
		<{if $order.cmd_gift}>
		<div class="pad2"><span class="bold">کد کارت هدیه</span> : <{$order.cmd_gift}></div>
		<{/if}>
	</div>

   <div id="print-factor">
		<table class="center">
		<tr>
		   <th class="center">بارکد</th>
			<th class="center">نام محصول</th>
			<th class="center">شماره</th>
			<th class="center">حجم</th>
			<th class="center">فی واحد</th>
			<th class="center">فی تخفیف</th>
			<th class="center">تعداد</th>
			<th class="center">مبلغ</th>
		</tr>
		<{foreach item=product from=$products}>
		<{if count($product.product_attributes) > 0}>
		<tr>
			<td rowspan="<{math equation='x + y' x=$product.product_attributes_count y=1}>" class="center"><{$product.product_extraid}></td>
			<td rowspan="<{math equation='x + y' x=$product.product_attributes_count y=1}>" class="center"><{$product.product_cat_title}> <{$product.product_title}></td>
			<td rowspan="<{math equation='x + y' x=$product.product_attributes_count y=1}>" class="center"><{$product.product_id}></td>
			<td class="center" style="border-bottom-color: #fff;"></td>
			<td class="center" style="border-bottom-color: #fff;"></td>
			<td class="center" style="border-bottom-color: #fff;"></td>
			<td rowspan="<{math equation='x + y' x=$product.product_attributes_count y=1}>" class="center"><{$product.product_caddy.caddy_qte}></td>
			<td rowspan="<{math equation='x + y' x=$product.product_attributes_count y=1}>" class="center"><{$product.product_caddy.caddy_price_fordisplay}></td>
		</tr>
		<{foreach item=attribute from=$product.product_attributes}>
		<{foreach item=option from=$attribute.attribute_options}>
		<tr>
			<td class="center"><{$option.ca_attribute_name}></td>
			<td class="center"><{$option.ca_attribute_price_formated}></td>
         <td> - </td>
      </tr>
		<{/foreach}>
		<{/foreach}>
		<{else}>
		<tr>
			<td class="center"><{$product.product_extraid}></td>
			<td class="center"><{$product.product_joined_manufacturers}> <{$product.product_title}></td>
			<td class="center"><{$product.product_id}></td>
			<td class="center"><{$product.product_weight}> <{$product.product_unitmeasure1}></td>
			<td class="center"><{$product.product_price_ttc}></td>
			<td class="center"><{$product.product_caddy.caddy_price_t}></td>
			<td class="center"><{$product.product_caddy.caddy_qte}></td>
			<td class="center"><{$product.product_caddy.caddy_price_fordisplay}></td>
		</tr>
		<{/if}>
		<{/foreach}>
		<tr>
		   <td class="center bold">قابل پرداخت</td>
			<td colspan="6" >&nbsp;</td>
			<td class="center bold"><{$order.cmd_total_fordisplay}></td>
		</tr>
		</table>
	</div>

	<div id="print-check">
		<table>
			<tr>
				<td class="bold">کنترل موجودی</td>
				<td><span class="box"></span><span>موجودی تکمیل است</span></td>
				<td><span class="box"></span><span>موجودی تکمیل نمی باشد</span></td>
			</tr>
			<tr>
				<td class="bold">نحوه دریافت</td>
				<td><span class="box"></span><span>دریافت نقدی توسط پیک</span></td>
				<td><span class="box"></span><span>دریافت از طریق کارت به کارت</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><span class="box"></span><span>حواله انبار صادر گردید</span></td>
			</tr>
		</table>
		<div class="moreinfo"><span class="bold">زمان ارسال</span> :‌ </div>
		<div class="moreinfo"><span class="bold">سایر توضیحات</span> :‌ </div>
		<div class="moreinfo"><span class="bold">شماره کارت ( ۴ رقم )</span> :‌ </div>
		<div class="moreinfo"><span class="bold">شماره پیگیری</span> :‌ </div>
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
