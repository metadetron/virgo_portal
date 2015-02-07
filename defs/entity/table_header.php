#############################################################################################
################################### a header row in a table #################################
			<tr class="data_table_header">
<?php
//		$acl = &JFactory::getACL();
//		$dataChangeRole = virgoSystemParameter::getValueByName("DATA_CHANGE_ROLE", "Author");
?>
#if ($entity.name != 'system log' && $entity.name != 'system parameter')
## poki co nie ma edit selected...
## <?php
## $showSelectAll = FALSE;
## #permissionRestrictedBlockBegin("form")
## $showSelectAll = TRUE;
## ?>
##			<th rowspan="2">
##			</th>
## <?php
## #permissionRestrictedBlockEnd()
## ?>
<?php
## if (! $showSelectAll) {
#permissionRestrictedBlockBegin("delete")
?>
			<th rowspan="2">
			</th>
<?php
#permissionRestrictedBlockEnd()
##}
?>
#end
#set ($orderedFields = [])
#set ($orderedFieldsNames = [])
#parseFile("modules_project/fieldOrder/${tr.f_v($entity.name)}/table.vm")
#orderFields()
#set ($defaultRowspan = "2")
#set ($fieldset = "0")
<?php
#getParentsInContext()
?>
#foreach( $property in $orderedFields )
#if ($property.class.name == "com.metadetron.virgo.bean.Property")
<?php
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
	if (P('show_table_${tr.f_v($property.name)}', "$obl") == "1") {
?>
#if ($fieldset == "0")
#parseFileWithStandard("modules_project/columnHeader/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "defs/entity/table_column_header.php") 
#else
<?php
	$fieldsetCount = $fieldsetCount + 1;
?>
#end
<?php
	}
?>
#elseif ($property.class.name == "com.metadetron.virgo.bean.Relation")
#set ($relation = $property)
<?php
##                                                                                                                                                                                                                                                                                                                  ## UWAGA: Tylko dla relacji BEZ nazwy! Bo jak jest kilka relacji do tej samej encji to sie burzylo - ma wybrac tylko ta bez nazwy		
if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.f_v($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php') && P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") != "0" #if (!$relation.name) && !isset($parentsInContext['${tr.f_v($application.name)}\\virgo${tr.FV($relation.parentEntity.name)}']) #end ) {
	if (P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "2") {
		$tmpLookup${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} = virgo${tr.FV($relation.parentEntity.name)}::getVirgoListStatic();
?>
<input name='${tr.fV($entity.prefix)}_${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}_id_<?php echo $this->getId() ?>' id='${tr.fV($entity.prefix)}_${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}_id_<?php echo $this->getId() ?>' type="hidden"/>
<?php		
	}
?>
#if ($fieldset == "0")
#parseFileWithStandard("modules_project/formColumnHeader/${tr.f_v($entity.name)}/${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}.php" "defs/entity/table_form_parent_header.php") 
#else
<?php
	$fieldsetCount = $fieldsetCount + 1;
?>
#end
<?php
	}
?>
#elseif ($property.class.name == "java.lang.String")
#if ($property == "F:virgo_close")
<?php
	if ($fieldsetCount > 0) {
?>

				<td align="center" valign="middle" rowspan="1" colspan="<?php echo $fieldsetCount ?>" class="groupCell"><!-- bylo: nowrap -->
					<span style="white-space: normal;" class="data_table_header"><?php echo T('$fieldset') ?></span>
				</td>
<?php
	}
?>
#set ($fieldset = "0")
#else
<?php
	$fieldsetCount = 0;
?>
#set ($fieldset = $property.substring(2))
#end
#end
#end
#foreach( $relation in $entity.parentRelations )
<?php
	if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($relation.childEntity.name)}') && P('show_table_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}', '0') == "1") {
?>
## sprawdz, czy te dzieci to przypadkiem nie jest tabela n:m ktora ma drugiego rodzica -----USUNIETO TEN WARUNEK:", ktory jest slownikiem"
#set ($childrenRendered = false)
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
#set ($childrenRendered = true)
## w ponizszych liniach powinna byc tez obsluga widoku kalendarza a nie tylko tabeli!
				<th align="center" valign="middle" rowspan="#if($fieldset == "0") $defaultRowspan#else 1#end"><!-- bylo: nowrap -->
						<span style="white-space: normal; cursor: pointer;" class="data_table_header">
#text("'${tr.F_V($relation.parentEntity.name)}'")
&nbsp;
#text("'${tr.F_V($relation.name)}'")							
#if ($relation.name)								
							<?php echo ($oc == "${tr.f_v($relation.parentEntity.name)}_${tr.f_v($relation.name)}" ? ($om == "" ? $pagingDescHTML : ($om == "asc") ? $pagingDescHTML : $pagingAscHTML) : "") ?> 
#else							
							<?php echo ($oc == "${tr.f_v($relation.parentEntity.name)}" ? ($om == "" ? $pagingDescHTML : ($om == "asc") ? $pagingDescHTML : $pagingAscHTML) : "") ?> 
#end							
						</span>
				</th>			
#end
#end
<?php
	}
?>
#end
#parseFile("modules_project/renderExtraColumn/${tr.f_v($entity.name)}_2.php")
#parseFile("modules_project/extraColumnsHeader/${tr.f_v($entity.name)}.php")
## #foreach( $relation in $entity.childRelations )
## #end
				<th rowspan="2"></th>
			</tr>
			<tr class="data_table_header">
#set ($defaultRowspan = "1")
#set ($fieldset = "0")
#foreach( $property in $orderedFields )
	#if ($property.class.name == "com.metadetron.virgo.bean.Property")
		#if ($fieldset != "0")
<?php
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
	if (P('show_table_${tr.f_v($property.name)}', "$obl") == "1") {
?>
			#set ($originalFieldset = $fieldset)		
			#set ($fieldset = "0")
			#parseFileWithStandard("modules_project/columnHeader/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "defs/entity/table_column_header.php") 
			#set ($fieldset = $originalFieldset)		
<?php
	}
?>
		#end
	#elseif ($property.class.name == "com.metadetron.virgo.bean.Relation")
		#set ($relation = $property)
		#if ($fieldset != "0")
<?php
if (P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") { // && !in_array("${relation.parentEntity.prefix}${tr.f_v($relation.name)}", $hideColumnFromContextInTable)) {
?>
			#set ($originalFieldset = $fieldset)		
			#set ($fieldset = "0")
			#parseFileWithStandard("modules_project/formColumnHeader/${tr.f_v($entity.name)}/${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}.php" "defs/entity/table_form_parent_header.php") 
			#set ($fieldset = $originalFieldset)		
<?php
	}
?>
		#end
	#elseif ($property.class.name == "java.lang.String")
		#if ($property == "F:virgo_close")
			#set ($fieldset = "0")
		#else
			#set ($fieldset = $property.substring(2))
		#end
	#end
#end
## na razie dzieci n:m obok funkcjonalnosci "orderFields"

			</tr>
################################### a header row in a table #################################
#############################################################################################

