				<th align="center" valign="middle" rowspan="#if($fieldset == "0") $defaultRowspan#else 1#end"><!-- bylo: nowrap -->
#if ($fieldset == "0")				
<?php
	$oc = $result${tr.FV($entity.name)}->getOrderColumn(); 
	$om = $result${tr.FV($entity.name)}->getOrderMode();
	$pagingAscHTML = '<div style="display: inline;" class="button_paging">&#x21E7;</div>';
	$pagingDescHTML = '<div style="display: inline;" class="button_paging">&#x21E9;</div>';
?>
						<span onclick="var form = document.getElementById('portlet_form_<?php echo $this->getId() ?>');
							form.portlet_action.value='ChangeOrder';
#if ($relation.name)								
#setFormFieldValue("'virgo_order_column'" "'${tr.f_v($relation.parentEntity.name)}_${tr.f_v($relation.name)}'")
#else							
#setFormFieldValue("'virgo_order_column'" "'${tr.f_v($relation.parentEntity.name)}'")
#end							
#setFormFieldValue("'virgo_order_mode'" "'<?php echo ($om == 'asc' ? 'desc' : 'asc') ?>'")
							<?php echo JSFS() ?>
							 " style="white-space: normal; cursor: pointer;" class="data_table_header">
<?php
	$customLabel = TE('${tr.F_V($relation.parentEntity.name)}_${tr.F_V($relation.name)}'); 
	echo !is_null($customLabel) ? $customLabel : T('${tr.F_V($relation.parentEntity.name)}') . '&nbsp;' . T('${tr.F_V($relation.name)}')
?>
#if ($relation.name)								
							<?php echo ($oc == "${tr.f_v($relation.parentEntity.name)}_${tr.f_v($relation.name)}" ? ($om == "" ? $pagingDescHTML : ($om == "asc") ? $pagingDescHTML : $pagingAscHTML) : "") ?> 
#else							
							<?php echo ($oc == "${tr.f_v($relation.parentEntity.name)}" ? ($om == "" ? $pagingDescHTML : ($om == "asc") ? $pagingDescHTML : $pagingAscHTML) : "") ?> 
#end							
						</span>
#else
						<span style="white-space: normal;" class="data_table_header"><?php echo T('$fieldset') ?></span>
#end						
<?php
	if (P('show_table_filter', '0') == '1') {
		$parentsCount = virgo${tr.FV($relation.parentEntity.name)}::getVirgoListStatic('', true);
		if ($parentsCount < 51) {
			$parents = virgo${tr.FV($relation.parentEntity.name)}::getVirgoListStatic();
			$parentFilter = virgo${tr.FV($entity.name)}::getLocalSessionValue('VirgoFilter${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', null);
?>
						<select 
							name="virgo_filter_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}"
							class="virgo_filter"
							onChange="<?php echo JSFS(null, "Submit", true, array(), false, "SetVirgoTableFilter") ?>"
						>
							<option value=""></option>
							<option value="empty" <?php echo $parentFilter == "empty" ? " selected='selected' " : "" ?>>(empty)</option>
<?php
			foreach ($parents as $key => $value) {
?>							
							<option value="<?php echo $key ?>" <?php echo $parentFilter == $key ? " selected='selected' " : "" ?>><?php echo $value ?></option>
<?php
			}
?>							
						</select>
<?php		
		} else {
			$parentFilter = virgo${tr.FV($entity.name)}::getLocalSessionValue('VirgoFilterTitle${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', null);
?>
						<input
							name="virgo_filter_title_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}"
							class="virgo_filter"
							onChange="<?php echo JSFS(null, "Submit", true, array(), false, "SetVirgoTableFilter") ?>"
							value="<?php echo $parentFilter ?>"
						/>
<?php			
		}
	}
?>

				</th>
			
