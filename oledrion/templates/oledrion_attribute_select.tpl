<div class="attribute_select">
	<label for="<{$name}>_<{$attribute_id}>"><{$attributeTitle}><{if $attribute_mandatory}> *<{/if}></label><br />
	<select name='<{$name}>_<{$attribute_id}>[]' id='<{$name}>_<{$attribute_id}>[]' <{$multiple}> size='<{$size}>' class="oledrion_attribute <{if $attribute_mandatory}>required<{/if}>"> <{* DON'T REMOVE THE class= ... *}>
		<{foreach item=option from=$options}>
			<option <{if in_array($option.value, $defaultValue)}>selected='selected'<{/if}> value='<{$option.value}>_<{$option.counter}>'><{$option.name}> (<{$option.priceTTCFormated}>)</option> 
		<{/foreach}>
	</select>
</div>