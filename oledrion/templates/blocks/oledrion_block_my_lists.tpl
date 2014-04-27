<div class="oledrion-block">
		<div class="oledrion-block-mylist">
				<ul>
						<{foreach item=list from=$block.my_lists}>
						<li>
								<span class="floatleft"><a href="<{$list.list_url_rewrited}>" title="<{$list.list_href_title}>"><{$list.list_title}></a></span>
								<span class="floatleft">( <{$list.list_formated_count}> )</span>
								<div class="clear"></div>
						</li>
						<{/foreach}>
				</ul>
		</div>
		<div class="center">
				<a href="<{$smarty.const.OLEDRION_URL}>my-lists.php" href="<{$smarty.const._MI_OLEDRION_SMNAME10}>"><{$smarty.const._MI_OLEDRION_SMNAME10}></a>
		</div>
</div>