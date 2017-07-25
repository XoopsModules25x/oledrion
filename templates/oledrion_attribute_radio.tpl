<div class="attribute_radio">
    <label for="<{$name}>_<{$attribute_id}>"><{$attributeTitle}><{if $attribute_mandatory}> *<{/if}></label><br>
    <{foreach item=option from=$options}> <{* DON'T REMOVE THE class= ... *}>
        <input type='radio' name='<{$name}>_<{$attribute_id}>' id='<{$name}>_<{$attribute_id}>'
               value='<{$option.value}>_<{$option.counter}>'
               <{if in_array($option.value, $defaultValue)}>checked='checked'<{/if}>
               class="oledrion_attribute <{if $attribute_mandatory}>required<{/if}>">
        <{$option.name}> (<{$option.priceTTCFormated}>)<{$delimiter}>
    <{/foreach}>
    </select>
</div>
