#############################################################################################
################################### a header row in a table #################################
			<tr class="data_table_header">
<?php
//		$acl = &JFactory::getACL();
//		$dataChangeRole = virgoSystemParameter::getValueByName("DATA_CHANGE_ROLE", "Author");
?>
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
<?php
	if (P('show_table_${tr.f_v($property.name)}', "$obl") == "1") {
?>
#parseFileWithStandard("modules_project/formColumnHeader/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "defs/entity/table_form_column_header.php") 
<?php
	}
?>
#end
#parseFile("modules_project/extraColumnsHeader/${tr.f_v($entity.name)}.php")
#foreach( $relation in $entity.childRelations )
<?php
	if (P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1" /* && ($masterComponentName != "${tr.f_v($relation.parentEntity.name)}" || is_null($contextId)) */) {
?>
				<td align="center" nowrap>${tr.Fv($relation.parentEntity.name)} ${tr.Fv($relation.name)}</td>
<?php
	}
?>
#end
				<td></td>
				<td></td>
			</tr>
################################### a header row in a table #################################
#############################################################################################

