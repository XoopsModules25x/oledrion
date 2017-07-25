<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <title><{if $xoops_pagetitle !=''}><{$xoops_pagetitle}> : <{/if}><{$xoops_sitename}></title>

    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>">
    <meta name="keywords" content="<{$meta_keywords}>">
    <meta name="description" content="<{$meta_description}>">
    <meta name="author" content="<{$xoops_meta_author}>">
    <meta name="copyright" content="<{$meta_copyright}>">
    <meta name="generator" content="XOOPS">
    <meta name="robots" content="noindex,nofollow">

    <link rel="shortcut icon" type="image/ico" href="<{$xoops_url}>/favicon.ico">
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_url}>/xoops.css">
    <link rel="stylesheet" type="text/css" media="all" href="<{$smarty.const.OLEDRION_URL}>/assets/css/print.css">
    <link rel="stylesheet" type="text/css" media="all" href="<{$localstyle}>">

</head>
<!--  onload="window.print()" -->
<body onload="window.print()">
<div id="xo-print">
    <div id="xo-print-content">
        <div class="item">
            <div class="itemTitle spacer"><{$product.product_title}></div>
            <div class="itemBody">
                <{if $product.product_thumb_full_url != ''}>
                    <img class="left" src="<{$product.product_image_full_url}>" alt="<{$product.product_href_title}>">
                <{/if}>
                <{if $product.product_summary != ''}>
                    <div class="itemText spacer txtjustify"><{$product.product_summary}></div><{/if}>
                <{if $product.product_description != ''}>
                    <div class="itemText spacer txtjustify"><{$product.product_description}></div><{/if}>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
