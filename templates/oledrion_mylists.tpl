<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->
    <div class="pad2">
        <h2><{$smarty.const._MI_OLEDRION_SMNAME10}></h2>
    </div>

    <{if $op == 'default'}>
        <table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>
            <tr>
                <th class='center'><{$smarty.const._AM_OLEDRION_TITLE}></th>
                <th class='center'><{$smarty.const._AM_OLEDRION_TYPE}></th>
                <th class='center'><{$smarty.const._AM_OLEDRION_DATE}></th>
                <th class='center'><{$smarty.const._AM_OLEDRION_ACTION}></th>
            </tr>
            <{foreach item=list from=$lists}>
                <tr class="<{cycle values='even,odd'}>">
                    <td class='left'><a href="<{$list.list_url_rewrited}>" target="_blank"
                                        title="<{$list.list_href_title}>"><{$list.list_title}></a></td>
                    <td class='center'><{$list.list_type_description}></td>
                    <td class='center'><{$list.list_formated_date}></td>
                    <td class='center'><a href="<{$baseurl}>?op=edit&list_id=<{$list.list_id}>"><img
                                    src="<{xoModuleIcons16 edit.png}>" alt="<{$smarty.const._EDIT}>"
                                    title="<{$smarty.const._EDIT}>"></a>
                        <a href="<{$baseurl}>?op=delete&list_id=<{$list.list_id}>"><img
                                    src="<{xoModuleIcons16 delete.png}>" alt="<{$smarty.const._DELETE}>"
                                    title="<{$smarty.const._DELETE}>"></a>
                    </td>
                </tr>
            <{/foreach}>
            <tr class="<{cycle values="even,odd"}>">
                <td colspan="4" class="center">
                    <div class="frmAddList">
                        <form method="post" action="<{$baseurl}>" name="frmAddList" id="frmAddList">
                            <{securityToken}><{*//mb*}>
                            <input type="hidden" name="op" id="op" value="addList">
                            <input type="submit" name="btngo" id="btngo" value="<{$smarty.const._OLEDRION_CREATE_NEW_LIST}>">
                        </form>
                    </div>
                </td>
            </tr>
        </table>
    <{elseif $op == 'edit' || $op == 'addList'}>
        <{$form}>
    <{elseif $op == 'addProduct'}>
        <{if $userListsCount == 0}>
            <{$form}>
        <{else}>
            <h3>
                <a href="<{$product.product_url_rewrited}>"><{$product.product_title}></a>, <{$smarty.const._OLEDRION_WHAT_TO_DO}>
            </h3>
            <{if $message != ''}>
                <div class="oledrion_message"><{$message}></div><{/if}>
            <form method="post" action="<{$baseurl}>" name="frmSelectAction" id="frmSelectAction">
                <{securityToken}><{*//mb*}>
                <input type="hidden" name="op" id="op" value="addProductToList">
                <input type="hidden" name="product_id" id="product_id" value="<{$product_id}>">
                <{foreach item=list key=listKey from=$lists}>
                    <input type="radio" name="list_id" id="list_id" <{if $listKey == 0}>checked='checked'<{/if}>
                           value="<{$list.list_id}>"><{$smarty.const._OLEDRION_ADD_TO}> <{$list.list_title}>
                    <br>
                <{/foreach}>
                <input type="radio" name="list_id" id="list_id" value="0"><{$smarty.const._OLEDRION_CREATE_NEW_LIST}>
                <br>
                <br><input type="submit" name="btnGo" id="btnGo" value="<{$smarty.const._GO}>">
            </form>
        <{/if}>
    <{/if}>
</div>
