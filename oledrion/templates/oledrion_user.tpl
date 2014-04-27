<div class="oledrion">
	<table>
		<tr>
			<th><{$smarty.const._OLEDRION_USER_ID}></th>
			<th><{$smarty.const._OLEDRION_USER_DATA}></th>
			<th><{$smarty.const._OLEDRION_USER_NAME}></th>
			<th><{$smarty.const._OLEDRION_USER_TOTAL}></th>
			<th><{$smarty.const._OLEDRION_USER_STATE}></th>
			<th><{$smarty.const._OLEDRION_USER_ACTION}></th>
		</tr>
        <{foreach item=item from=$list}>
		<tr class="odd">
			<td><{$item.cmd_id}></td>
			<td><{$item.cmd_create_date}></td>
			<td><{$item.cmd_firstname}> <{$item.cmd_lastname}></td>
			<td><{$item.cmd_total_fordisplay}></td>
			<td><{$item.cmd_state_title}></td>
			<td><a href="<{$item.cmd_url}>"><{$smarty.const._OLEDRION_USER_VIEW}></a></td>
		</tr>
        <{/foreach}>
	</table>
</div>