<div class="oledrion">
    <!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
    <{if $mod_pref.advertisement != ''}>
        <div id="oledrion_publicite"><{$mod_pref.advertisement}></div><{/if}>
    <!-- Breadcrumb -->
    <div class="breadcrumb"><{$breadcrumb}></div>
    <!-- /Breadcrumb -->
    <div class="oledrion_productdescription">
        <{if $text != '' }>
            <div class="pad2">
                <{if is_array($text)}>
                    <{foreach item=line from=$text}>
                        <div class="pad2"><{$line}></div>
                    <{/foreach}>
                <{else}>
                    <{$text}>
                <{/if}>
            </div>
        <{/if}>
        <div class="clear"></div>
        <{$form}>
        <{if $op == 'confirm'}>
            <div class="center bold"><{$smarty.const._OLEDRION_DETAILS_EMAIL}></div>
            <table class="oledrion_step_body">
                <tr>
                    <td class="oledrion_step_active">
                        <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_1}></span>
                    </td>
                    <td class="oledrion_step_img">
                        <img src="<{$step3}>">
                    </td>
                    <td class="oledrion_step_active">
                        <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_2}></span>
                    </td>
                    <td class="oledrion_step_img">
                        <img src="<{$step3}>">
                    </td>
                    <td class="oledrion_step_active">
                        <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_3}></span>
                    </td>
                </tr>
            </table>
        <{else}>
            <div class="center bold"><{$smarty.const._OLEDRION_REQUIRED}></div>
            <table class="oledrion_step_body">
                <tr>
                    <td class="oledrion_step_active">
                        <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_1}></span>
                    </td>
                    <td class="oledrion_step_img">
                        <img src="<{$step3}>">
                    </td>
                    <td class="oledrion_step_active">
                        <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_2}></span>
                    </td>
                    <td class="oledrion_step_img">
                        <img src="<{$step2}>">
                    </td>
                    <td class="oledrion_step">
                        <span class="oledrion_step_text"><{$smarty.const._OLEDRION_STEP_3}></span>
                    </td>
                </tr>
            </table>
        <{/if}>
    </div>
</div>
