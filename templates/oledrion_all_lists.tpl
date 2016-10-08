<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <{if $global_advert != ''}>
        <div id="oledrion_publicite"><{$global_advert}></div><{/if}>
    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->

    <h2><{$smarty.const._MI_OLEDRION_SMNAME11}></h2>
    <div id="directory-nav" style="margin-top: 15px; margin-bottom: 15px;"></div>
    <ul id="directory">
        <{foreach item=list from=$lists}>
            <li>
                <a href="<{$list.list_url_rewrited}>"
                   title="<{$list.list_href_title}>"><{$list.list_title}></a> <{$smarty.const._OLEDRION_BY}> <a
                        href="<{$xoops_url}>/userinfo.php?uid=<{$list.list_uid}>"><{$list.list_username}></a>
                <small>(<{$list.list_formated_date}>), <{$list.list_formated_count}></small>
            </li>
        <{/foreach}>
    </ul>

    <{if $pagenav != ''}>
        <div class="center pagenav"><{$pagenav}></div>
    <{/if}>
</div>
