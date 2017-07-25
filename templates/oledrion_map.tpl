<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->
    <div class='center'>
        <h2><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>folder_orange_open.png"
                 alt="<{$smarty.const._MI_OLEDRION_SMNAME4}>" width="32"
                 height="32"><{$smarty.const._MI_OLEDRION_SMNAME4}></h2>
    </div>

    <table width="100%" cellspacing="0">
        <tr>
            <td class="page-curl_01">
                <div class="oledrion_cat-map">
                    <ul>
                        <{foreach item=category from=$categories}>
                            <li><a href="<{$category.cat_url_rewrited}>"
                                   title="<{$category.cat_href_title}>"><{$category.cat_title}></a></li>
                        <{/foreach}>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <!--********* NOTIFICATION ***-->
    <{include file='db:system_notification_select.tpl'}>
    <!--******** /NOTIFICATION ***-->
</div>
