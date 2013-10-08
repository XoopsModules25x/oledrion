jQuery().ready(function() {
	var attribute_id_Value = jQuery('#attribute_id').val();
	jQuery('#ajaxOptions').load('index.php', {op: 'attributes', action: 'ajaxoptions', attribute_id: attribute_id_Value});

	jQuery('img.btnremove').live("click", function() {
		if(confirm(confirmDelete)) {
			var optionId = jQuery(this).attr('id').replace('btnremove-','');
			var formContent = jQuery("#frmattributes").serialize();
			jQuery('#ajaxOptions').load('index.php', {op: 'attributes', action: 'ajaxoptions', subaction: 'delete', option: optionId, attribute_id: attribute_id_Value, formcontent: formContent});
		}
	});

	jQuery('img.btnUp').live("click", function() {
		var optionId = jQuery(this).attr('id').replace('btnUp-','');
		var formContent = jQuery("#frmattributes").serialize();
		jQuery('#ajaxOptions').load('index.php', {op: 'attributes', action: 'ajaxoptions', subaction: 'up', option: optionId, attribute_id: attribute_id_Value, formcontent: formContent});
	});

	jQuery('#attribute_type').change(function() {
		attributeParameters();
	});
	attributeParameters();

	jQuery('img.btnDown').live("click", function() {
		var optionId = jQuery(this).attr('id').replace('btnDown-','');
		var formContent = jQuery("#frmattributes").serialize();
		jQuery('#ajaxOptions').load('index.php', {op: 'attributes', action: 'ajaxoptions', subaction: 'down', option: optionId, attribute_id: attribute_id_Value, formcontent: formContent});
	});

	jQuery('#bntAdd').live("click", function() {
		var formContent = jQuery("#frmattributes").serialize();
		jQuery('#ajaxOptions').load('index.php', {op: 'attributes', action: 'ajaxoptions', subaction: 'add', attribute_id: attribute_id_Value, formcontent: formContent});
 });
});

function attributeParameters()
{
	var attributeType = jQuery('#attribute_type').val();
	if(attributeType == 3) {
		jQuery('#attributeParametersSelect').show();
		jQuery('#attributeParametersCheckbox').hide();
	} else {
		jQuery('#attributeParametersSelect').hide();
		jQuery('#attributeParametersCheckbox').show();
	}
}
