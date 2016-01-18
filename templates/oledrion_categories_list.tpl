<!-- Created by Hervé Thouzard (http://www.herve-thouzard.com/) -->
<{if count($categories) > 0}>
	<table width="100%" cellspacing="0" class="tablefix">
  	<tr>
	    <td class="box_blue-clip_01"></td>
	    <td class="box_blue-clip_02"></td>
	    <td class="box_blue-clip_03"></td>
  	</tr>
  	<tr>
    	<td class="box_blue-clip_04"></td>
    	<td class="category-list">
			<table border='0' cellspacing='5' cellpadding='0' class='center oledrion_categorylist'>
			<tr>
				<{foreach item=category from=$categories}>
					<td class="top">
						<h2>
							<a href="<{$category.cat_url_rewrited}>" title="<{$category.cat_href_title}>">
							<{if $category.cat_imgurl != ''}><img src="<{$category.cat_full_imgurl}>" alt="<{$category.cat_href_title}>" /><{else}><img src="<{$smarty.const.OLEDRION_IMAGES_URL}>icon-cat.png" alt="<{$category.cat_href_title}>" /><{/if}>
							<{$category.cat_title}>
							</a>
						</h2>
	    			</td>
    				<{if $category.count is div by 3}>
	    				</tr><tr>
	    			<{/if}>
				<{/foreach}>
			</tr>
			</table>
			<{if isset($total_products_count) && $total_products_count > 0}>
				<div class="oledrion_totalproductscount"><{$total_products_count}></div>
			<{/if}>
		</td>
    	<td class="box_blue-clip_05"></td>
  	</tr>
  	<tr>
    	<td class="box_blue-clip_06"></td>
    	<td class="box_blue-clip_07"></td>
    	<td class="box_blue-clip_08"></td>
  	</tr>
	</table>
<{/if}>