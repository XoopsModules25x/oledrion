<div class="oledrion">
	<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
	<{if $mod_pref.advertisement != ''}><div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
	<!-- Breadcrumb -->
	<div class="breadcrumb"><{$breadcrumb}></div>
	<!-- /Breadcrumb -->
	<{if $global_advert != ''}>
		<div class='global_advert center'><{$global_advert}></div>
	<{/if}>
   <div class="pad2">
	<{if $success }>
		<h3><{$smarty.const._OLEDRION_THANK_YOU}></h3>
		<h4><{$smarty.const._OLEDRION_TRANSACTION_FINSIHED}></h4>
	<{else}>
		<h3><{$smarty.const._OLEDRION_GATEWAY_FAILED}></h3>
	<{/if}>
	</div>
	<div class="pad2"><a href="<{$smarty.const.OLEDRION_URL}>"><{$smarty.const._OLEDRION_CONTINUE_SHOPPING}></a></div>
</div>