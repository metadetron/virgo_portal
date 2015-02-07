<?php
	$showBasic = false;
	$showImport = false;
	$showSelected = false;
	$anythingRendered = false;
?>
		<span class="operations_tabs"><span id="table_operations" style="text-align: center;" class="table_footer_buttons"<?php echo P('form_only') == "4" ? " style='background-color: #FFFFFF;'" : "" ?>>
#if ($extendedtableOperations == 'TRUE')		
					<span id="control_basic_<?php echo $this->getId() ?>" class="operations_tab_current" onclick="showOperations('basic', <?php echo $this->getId() ?>);">#text("'OPERATIONS_BASIC'")</span>
					<span id="control_import_<?php echo $this->getId() ?>" class="operations_tab" onclick="showOperations('import', <?php echo $this->getId() ?>);">#text("'OPERATIONS_IMPORT'")</span>
					<span id="control_selected_<?php echo $this->getId() ?>" class="operations_tab" onclick="showOperations('selected', <?php echo $this->getId() ?>);">#text("'OPERATIONS_SELECTED'")</span>
#end					
					<div id="operations_basic_<?php echo $this->getId() ?>" class="operations">
		</span>
#parseFile("modules_project/extraEntityActionsBefore/${tr.f_v($entity.name)}.php")
#if ($entity.name != 'system log')		
<?php
PROFILE('TABLE_ACTIONS_ADD');
#permissionRestrictedBlockBegin("add")
?>
#actionButtonSimple("Add" "'ADD'")
<?php
#permissionRestrictedBlockEnd()
PROFILE('TABLE_ACTIONS_ADD');
PROFILE('TABLE_ACTIONS_SEARCH');
#permissionRestrictedBlockBegin("SearchForm")
?>
#actionButtonSimple("SearchForm" "'SEARCH'")
<?php
#permissionRestrictedBlockEnd()
PROFILE('TABLE_ACTIONS_SEARCH');
#if ($extendedtableOperations != 'TRUE')		
PROFILE('TABLE_ACTIONS_DELETE');
#permissionRestrictedBlockBegin("delete")
?>
#actionButton("DeleteSelected" "'DELETE_SELECTED'" "" "!nothingSelected(this.form, <?php echo $this->getId() ?>)" "'NOTHING_SELECTED'" "<?php echo T(${DQ}ARE_YOU_SURE_YOU_WANT_REMOVE${DQ}, T(${DQ}${tr.F_V($entity.namePlural)}${DQ}), ${DQ}${DQ}) ?>" "copyIds(form, <?php echo $this->getId() ?>);") 
<?php
#permissionRestrictedBlockEnd()
PROFILE('TABLE_ACTIONS_DELETE');
#end
?>
<?php
PROFILE('TABLE_ACTIONS_REPORT');
#permissionRestrictedBlockBegin("report")
?>
#actionButtonSimpleDownload("Report" "'REPORT'")
<?php
#permissionRestrictedBlockEnd()
PROFILE('TABLE_ACTIONS_REPORT');
PROFILE('TABLE_ACTIONS_EXPORT');
#permissionRestrictedBlockBegin("export")
?>
#actionButtonSimpleDownload("Export" "'CSV'")
<?php
#permissionRestrictedBlockEnd()
PROFILE('TABLE_ACTIONS_EXPORT_WUT?');
#permissionRestrictedBlockBegin("add")
#permissionRestrictedBlockBegin("form")
#permissionRestrictedBlockBegin("delete")
?>
#actionButtonSimpleDownload("Offline" "'EXCEL'")
<?php
#permissionRestrictedBlockEnd()
#permissionRestrictedBlockEnd()
#permissionRestrictedBlockEnd()
PROFILE('TABLE_ACTIONS_EXPORT_WUT?');
?>
#getStatusEntityWithWorkflow()
#if ($statusEntity)
#actionButtonSimple("ShowStatusLog" "'Log'")
#end
#parseFile("modules_project/extraEntityActions/${tr.f_v($entity.name)}.php")
<?php
	if (true) { //$user->username == "metaadmin" && $user->get( 'gid' ) == 25) {
?>
#actionButtonSimple("UpdateTitle" "'UPDATE_TITLE'")
<?php
	}
			$actions = virgoRole::getExtraActions('ET');
PROFILE('TABLE_ACTIONS_ET');
			foreach ($actions as $action) {
?>
#actionButton("${dollar}action" "$action" "" "!nothingSelected(this.form, <?php echo $this->getId() ?>)" "'NOTHING_SELECTED'" "" "copyIds(form, <?php echo $this->getId() ?>);") 
<?php						
			}
PROFILE('TABLE_ACTIONS_ET');
?>
					</div>
					<div id="operations_import_<?php echo $this->getId() ?>" style="display: none;" class="operations">
<?php
#permissionRestrictedBlockBegin("upload")
?>
			<input type="file" name="virgo_upload_file">
<?php
PROFILE('TABLE_ACTIONS_UPLOAD');
				$separatorString = ","; //$componentParams->get('import_separator');
				if ($separatorString == "") {
?>
			<input name="field_separator_in_import" size="1"
<?php
					$sessionSeparator = virgo${tr.FV($entity.name)}::getImportFieldSeparator();
					if (!is_null($sessionSeparator)) {
?>
						value="<?php $sessionSeparator ?>"
<?php
					}
?>
			>
<?php
				} else {
##					$separators = split("X", $separatorString);
					$separators = preg_split("/X/", $separatorString);
					if (sizeof($separators) == 1) {
?>
			<input type="hidden" name="field_separator_in_import" value="<?php echo $separators[0] ?>">
<?php
					} else {
?>
			<select name="field_separator_in_import">
<?php
						$sessionSeparator = virgo${tr.FV($entity.name)}::getImportFieldSeparator();
						foreach ($separators as $separator) {
?>
				<option value="<?php echo $separator ?>" 
<?php
							if ($sessionSeparator == $separator) {
?>
					selected="selected"
<?php
							}
?>
				><?php echo $separator ?></option>
<?php
						}
?>
			</select>
<?php						
					}
				}
PROFILE('TABLE_ACTIONS_UPLOAD');				
PROFILE('TABLE_ACTIONS_RESZTA');
?>
#actionButtonSimple("Upload" "'UPLOAD'")
#parseFile("modules_project/extraUploadActions/${tr.f_v($entity.name)}.php")
<?php
	if ($buttonRendered) {
		$showImport = true;
	}
#permissionRestrictedBlockEnd()
?>
					</div>
#if ($extendedtableOperations == 'TRUE')		
<?php
	if (!$showImport) {
?>
<script type="text/javascript">
	document.getElementById('control_import_<?php echo $this->getId() ?>').style.display='none';
</script>
<?php
	}
?>
#end					<div id="operations_selected_<?php echo $this->getId() ?>" style="display: none;"  class="operations">
#if ($entity.name != 'system parameter')							
			<input type="hidden" name="ids" value="">
<?php
#permissionRestrictedBlockBegin("edit")
?>
#actionButton("EditSelected" "'EDIT_SELECTED'" "" "!nothingSelected(this.form, <?php echo $this->getId() ?>)" "'NOTHING_SELECTED'" "" "copyIds(form, <?php echo $this->getId() ?>);") 
<?php
	if ($buttonRendered) {
		$showSelected = true;
	}
#permissionRestrictedBlockEnd()
#permissionRestrictedBlockBegin("delete")
?>
#actionButton("DeleteSelected" "'DELETE_SELECTED'" "" "!nothingSelected(this.form, <?php echo $this->getId() ?>)" "'NOTHING_SELECTED'" "<?php echo T('ARE_YOU_SURE_YOU_WANT_REMOVE', T('${tr.F_V($entity.namePlural)}'), ${DQ}${DQ}) ?>" "copyIds(form, <?php echo $this->getId() ?>);") 
<?php
	if ($buttonRendered) {
		$showSelected = true;
	}
#permissionRestrictedBlockEnd()
?>
#end		
#end		





#parseFile("modules_project/extraEntityActionsSelected/${tr.f_v($entity.name)}.php")
					</div>
		</span>
#if ($extendedtableOperations == 'TRUE')		
<?php
	if (!$showSelected) {
?>
<script type="text/javascript">
	document.getElementById('control_selected_<?php echo $this->getId() ?>').style.display='none';
</script>
<?php
	}
?>
<?php
	if (!$showImport && !$showSelected) {
?>
<script type="text/javascript">
	document.getElementById('control_basic_<?php echo $this->getId() ?>').style.display='none';
</script>
<?php
	}
	if (!$anythingRendered) {
?>
<script type="text/javascript">
	document.getElementById('operations_basic_<?php echo $this->getId() ?>').style.display='none';
</script>
<?php		
	}
PROFILE('TABLE_ACTIONS_RESZTA');
?>
#end

