#############################################################################################
################################### a single row in a table #################################
			<tr id="virgo_tr_id_<?php echo $result${tr.FV($relationChild.childEntity.name)}->${tr.f_v($relationChild.childEntity.prefix)}_id ?>" class="<?php echo ($index % 2 == 0 ? "data_table_even" : "data_table_odd") ?>"
			>
				<td>
<?php
	if (!is_null($result${tr.FV($relationChild.childEntity.name)}->${tr.f_v($relationChild.childEntity.prefix)}_id)) {
?>
					<input type="checkbox" class="checkbox" name="DELETE[]" value="<?php echo $result${tr.FV($relationChild.childEntity.name)}->${tr.f_v($relationChild.childEntity.prefix)}_id ?>">
					<?php echo JText::_('DELETE') ?>
<?php
	} else {
?>
					<input type="checkbox" class="checkbox" name="virgo_validate_new" <?php echo  strtoupper(R('virgo_validate_new')) == "ON" ? "checked" :"" ?>>
					<?php echo JText::_('ADD') ?>
<?php
	}
	$errorMessage = $idsToCorrect[is_null($result${tr.FV($relationChild.childEntity.name)}->${tr.f_v($relationChild.childEntity.prefix)}_id) ? 0 : $result${tr.FV($relationChild.childEntity.name)}->${tr.f_v($relationChild.childEntity.prefix)}_id];
	if (!is_null($errorMessage)) {
		$tmpId = $result${tr.FV($relationChild.childEntity.name)}->${tr.f_v($relationChild.childEntity.prefix)}_id;
		$result${tr.FV($relationChild.childEntity.name)} = new virgo${tr.FV($relationChild.childEntity.name)};
		$result${tr.FV($relationChild.childEntity.name)}->loadRecordFromRequest($tmpId);
	}
?>
				</td>
#set ($columnIndex = 0)
#foreach( $property in $relationChild.childEntity.properties )
<?php
	if (false) { //$componentParams${tr.FV($relationChild.childEntity.name)}->get('show_table_${tr.f_v($property.name)}') == "1") { 
?>
## jesli rekord jest bledny, to wartosc z requestu a nie z bazy danych
#parseFileWithStandard("modules_project/formColumnData/${tr.f_v($relationChild.childEntity.name)}/${tr.f_v($property.name)}.php" "defs/entity/table_form_column_data.php") 
<?php
	} else {
?> 
#parseFileWithStandard("defs/dataTypes/php/renderInputHidden/${tr.f_v($property.dataType.name)}" "defs/renderInputHidden.php") 
<?php
	}
?>
#set ($columnIndex = $columnIndex + 1)
#end
#parseFile("modules_project/extraColumnsData/${tr.f_v($relationChild.childEntity.name)}.php")
#foreach( $relationNr2 in $relationChild.childEntity.childRelations )
#if ($relationNr2.parentEntity.name != $entity.name)
<?php
	if (false) { //$componentParams${tr.FV($relationChild.childEntity.name)}->get('show_table_${tr.f_v($relationNr2.parentEntity.name)}${tr.f_v($relationNr2.name)}') == "1" && ($masterComponentName != "${tr.f_v($relationNr2.parentEntity.name)}" || is_null($contextId))) {
?>
## jesli rekord jest bledny, to wartosc z requestu a nie z bazy danych
				<td nowrap align="left" class="<?php echo ($index % 2 == 0 ? "data_table_even" : "data_table_odd") ?>">    
<?php
	$tabIndex = $index + sizeof($results${tr.FV($relationChild.childEntity.name)}) * ${columnIndex};
?>
#parentSelectForRelation($relationNr2 "form")					
    </td>
<?php
	} else {
?>
## #parentSelectHidden()					
<?php
	} 
?>
#set ($columnIndex = $columnIndex + 1)
#end
#end
				<td>
<?php
	if (!is_null($errorMessage)) {
?>
					<div class="error">
						<?php echo $errorMessage ?>
					</div>
<?php
	}
?>
				</td>
				<td nowrap class="actions" align="right">
				</td>
			</tr>
################################### a single row in a table #################################
#############################################################################################

