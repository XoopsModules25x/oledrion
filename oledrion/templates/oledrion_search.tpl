<div class="oledrion">
	<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
	<{if $mod_pref.advertisement != ''}><div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
	<!-- Breadcrumb -->
	<div class="breadcrumb"><{$breadcrumb}></div>
	<!-- /Breadcrumb -->
	<{if $search_results}>
		<h2><{$smarty.const._OLEDRION_SEARCHRESULTS}></h2>
		<table class="oledrion_productindex">
		<tr>
		<{foreach item=product from=$products}>
		      <td>
         <{include file="db:oledrion_product_box.html"}>
		      </td>
		    <{if $product.product_count is div by $columnsCount}>
	    		</tr><tr>
	    	<{/if}>		
		<{/foreach}>
		</tr>
		</table>
		<div class="clear"></div>
		<{if $pagenav !=''}><div class="center pagenav"><{$pagenav}></div><{/if}>
	<{/if}>
	<{$search_form}>
</div>