<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
<{if $mod_pref.advertisement != ''}>
    <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
<{if $global_advert != ''}>
    <div class='global_advert center'><{$global_advert}></div><{/if}>
<!-- Breadcrumb -->
<div class="breadcrumb"><{$breadcrumb}></div>
<!-- /Breadcrumb -->
<h2><{$smarty.const._OLEDRION_CGV}></h2>
<div class="pad2"><{$cgv_msg}></div>
