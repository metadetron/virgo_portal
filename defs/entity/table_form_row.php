#############################################################################################
################################### a single row in a table #################################
			<tr id="virgo_tr_id_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>" class="<?php echo ($index % 2 == 0 ? "data_table_even" : "data_table_odd") ?>"
## #renderStandardTooltip()
## #parseFileWithStandardText("modules_project/displayClass/${tr.f_v($entity.name)}.php" '$displayClass = "";') ??? co to jest?
<?php
	if ($result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id == 0 && R('virgo_validate_new', "N") == "N") {
?>
		style="display: none;"
<?php
	}
?>
			>
#set ($columnIndex = 0)
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
<?php
#exectime_start($property.name)				
	if (P('show_table_${tr.f_v($property.name)}', "$obl") == "1") { 
?>
#parseFileWithStandard("modules_project/formColumnData/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "defs/entity/table_form_column_data.php") 
<?php
#exectime_end($property.name)				
	} else {
?> 
#parseFileWithStandard("defs/dataTypes/php/renderInputHidden/${tr.f_v($property.dataType.name)}" "defs/renderInputHidden.php") 
<?php
	}
?>
#set ($columnIndex = $columnIndex + 1)
#end
<?php
#exectime_start("extra data")
?>
#parseFile("modules_project/extraColumnsData/${tr.f_v($entity.name)}.php")
<?php
#exectime_end("extra data")
?>
#foreach( $relation in $entity.childRelations )
<?php
	if (P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1"/* && ($masterComponentName != "${tr.f_v($relation.parentEntity.name)}" || is_null($contextId)) */) {
?>
				<td nowrap align="left" class="<?php echo ($index % 2 == 0 ? "data_table_even" : "data_table_odd") ?>">    
<?php
	$tabIndex = $index + sizeof($results${tr.FV($entity.name)}) * ${columnIndex};
?>
#parentSelect("form")					
    </td>
<?php
	} else {
?>
#parentSelectHidden()					
<?php
	} 
?>
#set ($columnIndex = $columnIndex + 1)
#end
				<td>
<?php
	if (isset($idsToCorrect[$result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id])) {
		$errorMessage = $idsToCorrect[$result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id];
?>
					<div class="error">
						<?php echo $errorMessage ?>
					</div>
<?php
	}
?>
				</td>
				<td nowrap class="actions" align="right">
#*
<?php
	if (false) { //$componentParams->get('show_children_view') == "0") {
?>
# *
	************************************************************* Children Tables BEGIN ******************************************************
* #
#foreach( $relation in $entity.parentRelations )
<?php
		if (P('show_table_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}', "1") == "1") {
?>
#getChildrenData()
					<input type="button" class="button<?php echo ($size == 0 ? '_disabled' : '') ?>" <?php echo ($size == 0 ? 'disabled="disabled"' : '') ?> value="${tr.Fv($relation.childEntity.name)}" onclick="childrenButtonClicked('${tr.fV($relation.childEntity.name)}${tr.fV($relation.name)}_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>');" title="<?php echo $size ?>">
<?php
		}					
?>
#end
				</td>
			</tr>
			<tr>
				<td colspan="99">
#foreach( $relation in $entity.parentRelations )
<?php
		if (P('show_table_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}', "1") == "1") {
?>
#renderChildrenData('false')
<?php
		}
?>
#end
# *
	************************************************************* Children Tables END ******************************************************
* #			
<?php
	}
?>
*#
				</td>
			</tr>
################################### a single row in a table #################################
#############################################################################################

