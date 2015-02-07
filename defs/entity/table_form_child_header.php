#############################################################################################
################################### a header row in a table #################################
			<tr class="data_table_header">
				<td>
					
				</td>
<?php
//		$acl = &JFactory::getACL();
//		$dataChangeRole = virgoSystemParameter::getValueByName("DATA_CHANGE_ROLE", "Author");
?>
#foreach( $property in $relationChild.childEntity.properties )
<?php
	if (false) { //$componentParams${tr.FV($relationChild.childEntity.name)}->get('show_table_${tr.f_v($property.name)}') == "1") {
?>
#parseFileWithStandard("modules_project/formColumnHeader/${tr.f_v($relationChild.childEntity.name)}/${tr.f_v($property.name)}.php" "defs/entity/table_form_column_header.php") 
<?php
	}
?>
#end
#parseFile("modules_project/extraColumnsHeader/${tr.f_v($relationChild.childEntity.name)}.php")
#foreach( $relation in $relationChild.childEntity.childRelations )
#if ($relation.parentEntity.name != $entity.name)
<?php
	if (false) { //$componentParams${tr.FV($relationChild.childEntity.name)}->get('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}') == "1" && ($masterComponentName != "${tr.f_v($relation.parentEntity.name)}" || is_null($contextId))) {
?>
				<td align="center" nowrap>${tr.Fv($relation.parentEntity.name)} ${tr.Fv($relation.name)}</td>
<?php
	}
?>
#end
#end
				<td></td>
				<td></td>
			</tr>
################################### a header row in a table #################################
#############################################################################################

