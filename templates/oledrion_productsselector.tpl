<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-type" content="text/html; charset=<{$smarty.const._CHARSET}>" />
	<meta name="author" content="voltan" />
	<meta http-equiv="content-language" content="<{$xoops_langcode}>" />
	<meta name="generator" content="Bluefish 2.2.3" />
	<title><{$smarty.const._AM_OLEDRION_SEARCH}></title>
	<link rel="stylesheet" type="text/css" href="<{$theme_set}>" />
	<style>
		.selectLists {
			width: 170px; 
			max-width: 170px;
		}
	</style>
</head>
<body>
<form name="productsSelector" id="productsSelector" method="post" action="<{$baseurl}>">
<fieldset>
<legend><{$smarty.const._AM_OLEDRION_SEARCH}></legend>
<input type="hidden" name="op" id="op" value="search" />
<input type="hidden" name="mutipleSelect" id="mutipleSelect" value="<{$mutipleSelect}>" />
<input type="hidden" name="callerName" id="callerName" value="<{$callerName}>" />
<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>
<tr class="<{cycle values='even,odd'}>">
	<td><select name='searchField' id='searchField' class="selectLists"><{html_options selected=$searchFieldSelected options=$searchField}></select></td>
	<td><select name='searchCriteria' id='searchCriteria' class="selectLists"><{html_options selected=$searchCriteriaSelected options=$searchCriteria}></select></td>
	<td><input type='text' name='searchText' id='searchText' size='45' maxlength='255' value="<{$searchTextValue}>" /></td>
</tr>
<tr class="<{cycle values='even,odd'}>">
	<td><{$smarty.const._AM_OLEDRION_DISCOUNT_VENDOR}><br /><select name='searchVendor' id='searchVendor' class="selectLists"><{html_options selected=$searchVendorSelected options=$searchVendor}></select></td>
	<td><{$smarty.const._OLEDRION_CATEGORY}><br /><{$searchCategory}></td>
	<td>&nbsp;<input type='submit' name='btnSearch' id='btnSearch' value="<{$smarty.const._AM_OLEDRION_SEARCH}>" /> <input type="button" name="btnClose" id="btnClose" onClick="javascript:window.close();" value="<{$smarty.const._AM_OLEDRION_CLOSE_WINDOW}>" /><br />
		<{if isset($productsCount)}>
			<div style="float: right; font-size: 10px"><{$productsCount}> <{$smarty.const._MI_OLEDRION_ADMENU4}></div>
		<{/if}>
	</td>
</tr>
</table>
</fieldset>
</form>

<{if isset($products)}>
	<form name="productsList" id="productsList">
	<{if isset($pagenav)}>
		<div class='right'><{$pagenav}></div>
	<{/if}>
	<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>
	<tr>
		<th class='center'><{$smarty.const._AM_OLEDRION_ID}></th>
		<th class='center'><{$smarty.const._OLEDRION_TITLE}></th>
		<th class='center'><{$smarty.const._AM_OLEDRION_SELECT}></th>
	</tr>
	<{foreach item=product from=$products}>
	<tr class="<{cycle values='even,odd'}>">
		<td class='center'><{$product.product_id}></td>
		<td class='left'><a href="<{$product.product_url_rewrited}>" target="_blank"><{$product.product_title}></a></td>
		<td class='center'>
			<{if $mutipleSelect}>
				<span style="cursor: pointer; text-decoration: underline" onClick="addValuesToCaller(<{$product.product_id}>, '<{$product.product_title_javascript}>')"><{$smarty.const._AM_OLEDRION_ADD}></span>
			<{else}>
				<span style="cursor: pointer; text-decoration: underline" onClick="replaceValueFromCaller(<{$product.product_id}>, '<{$product.product_href_title|escape:"quotes"}>')"><{$smarty.const._AM_OLEDRION_REPLACE}></span>
			<{/if}>	
		</td> 
	</tr>
	<{/foreach}>
	</table>
	<{if isset($pagenav)}>
		<div class="center pagenav"><{$pagenav}></div>
	<{/if}>	
	</form>
<{/if}>

<script type="text/javascript">
	function addValuesToCaller(optionValue, optionText)
	{
	  	var elOptNew = window.opener.document.createElement('option');
	  	elOptNew.text = optionText;
	  	elOptNew.value = optionValue;
	  	var elSel = window.opener.document.getElementById('<{$callerName}>');
	  	try {
		    elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
	  	}
	  	catch(ex) {
		    elSel.add(elOptNew); // IE only
	  	}
	}

	function removeCallerContent()
	{
	  var elSel = window.opener.document.getElementById('<{$callerName}>');
	  var i;
	  for (i = elSel.length - 1; i>=0; i--) {
		elSel.remove(i);
	  }
	}
	
	function replaceValueFromCaller(optionValue, optionText)
	{
		removeCallerContent();
		addValuesToCaller(optionValue, optionText);
	}
</script>
</body>
</html>