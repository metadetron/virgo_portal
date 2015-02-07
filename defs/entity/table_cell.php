<?php
			if (P('form_only') == "4") {
?>
					<ul> 
<?php
			}
?>
#if ($entity.name != 'system log' && $entity.name != 'system parameter')					
<?php
$showSelectAll = FALSE;
#permissionRestrictedBlockBegin("delete")
$showSelectAll = TRUE;
?>
<?php
			if (P('form_only') != "4") {
?>
			<td>
<?php
			} else {
?>
			<li>
<?php
			}
#if ($entity.dictionary)
	if (P('master_mode', "0") != "1") {
			echo  $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'];
	}
			if (P('master_mode', "0") != "1" && !$result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_virgo_deleted']) {
#end
?>
				<input type="checkbox" class="checkbox" style="float: right;" name="DELETE_<?php echo $this->getId() ?>_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>">
<?php
#if ($entity.dictionary)
			}
#end
			if (P('form_only') != "4") {
?>
			</td>
<?php
			} else {
?>
			</li>
<?php
			}
?>
<?php
#permissionRestrictedBlockEnd()
?>
<?php
if (! $showSelectAll) {
#permissionRestrictedBlockBegin("delete")
?>
<?php
			if (P('form_only') != "4") {
?>
			<td>
<?php
			} else {
?>
			<li>
<?php
			}
#if ($entity.dictionary)
			if (P('master_mode', "0") != "1" && !$result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_virgo_deleted']) {
#end
?>
				<input type="checkbox" class="checkbox" name="DELETE_<?php echo $this->getId() ?>_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>">
<?php
#if ($entity.dictionary)
			}
#end
			if (P('form_only') != "4") {
?>
			</td>
<?php
			} else {
?>
			</li>
<?php
			}
?>
<?php
#permissionRestrictedBlockEnd()
}
?>
#end
## #set ($orderedFields = [])
## #parseFile("modules_project/fieldOrder/${tr.f_v($entity.name)}/table.vm")
## #orderFields()
#foreach( $property in $orderedFields )
#if ($property.class.name == "com.metadetron.virgo.bean.Property")
<?php
#exectime_start($property.name)
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
	if (P('show_table_${tr.f_v($property.name)}', "$obl") == "1") {
PROFILE('render_data_table_${tr.f_v($property.name)}');
?>
#parseFileWithStandard("modules_project/columnData/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "defs/entity/table_column_data.php") 
<?php
PROFILE('render_data_table_${tr.f_v($property.name)}');
	}
#exectime_end($property.name)				
?>
#elseif ($property.class.name == "com.metadetron.virgo.bean.Relation")
#set ($relation = $property)
<?php
##                                                                                                                                                                ## UWAGA: Tylko dla relacji BEZ nazwy! Bo jak jest kilka relacji do tej samej encji to sie burzylo - ma wybrac tylko ta bez nazwy		
	if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($relation.parentEntity.name)}') && P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") != "0" #if (!$relation.name) && !isset($parentsInContext["${tr.f_v($application.name)}\\virgo${tr.FV($relation.parentEntity.name)}"]) #end ) {
?>
<?php
			if (P('form_only') != "4") {
?>
				<td 
##
##					nowrap 
					align="left" 
					class="<?php echo ($index % 2 == 0 ? 'data_table_even' : 'data_table_odd') ?> <?php echo P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1" ? " selectable " : " " ?> "
				>    
<?php
			} else {
?>
				<li class="${tr.f_v($relation.parentEntity.name)}#if($relation.name)_#end${tr.f_v($relation.name)}">
<?php
			} 
?>
    <span 
#if ($entity.dictionary)    
	style="<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_virgo_deleted'] ? "text-decoration: line-through;" : "" ?>"
#end    	
    	class="<?php echo $displayClass ?>"
    >
#detailsLinkStart()		
<?php 
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
	if (P('show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
		if (isset($result${tr.FV($entity.name)}['${tr.f_v($relation.parentEntity.name)}${underscore}${tr.f_v($relation.name)}'])) {
			echo $result${tr.FV($entity.name)}['${tr.f_v($relation.parentEntity.name)}${underscore}${tr.f_v($relation.name)}'];
		}
	} else {
//		echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id'];
?>
		<select class="inline" onchange="
this.form.portlet_action.value='VirgoSet${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}';
#setFormFieldValue("'${tr.fV($entity.prefix)}_id_<?php echo $this->getId() ?>'" "'<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>'")
#setFormFieldValue("'${tr.fV($entity.prefix)}_${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}_id_<?php echo $this->getId() ?>'" "this.options[this.selectedIndex].value")
<?php
					if (isset($ajax) && !$ajax) {
?>					
						form.submit();
<?php
					} else {
						echo JSFS(null, 'Submit', true, array());
					}
?>					
		">
#if (!$relation.obligatory)
			<option></option>
#end
<?php
		foreach ($tmpLookup${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} as $value => $label) {
?>
			<option value="<?php echo $value ?>" <?php echo $value == $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id'] ? " selected " : "" ?>><?php echo $label ?></option>
<?php			
		}
?>
		</select>
<?php
	}
?>
#detailsLinkEnd()		
    </span>
<?php
			if (P('form_only') != "4") {
?>
				</td>    
<?php
			} else {
?>
				</li>    
<?php
			}
?>
<?php
	}
?>
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
## w ponizszych 4 liniach powinna byc tez obsluga widoku kalendarza a nie tylko tabeli!
<td>
#getChildrenData()
#renderChildrenData('true')
</td>
#end
#end
<?php
	}
?>
#end
#parseFile("modules_project/renderExtraColumn/${tr.f_v($entity.name)}_3.php")
<?php
#exectime_start("extra data")
?>
#parseFile("modules_project/extraColumnsData/${tr.f_v($entity.name)}.php")
<?php
#exectime_end("extra data")
?>
<?php
			if (P('form_only') != "4") {
?>
				<td nowrap class="actions" align="left">
<?php
			} else {
?>
				<li>
<?php
			}
?>
#parseFileWithStandard("modules_project/insteadActions/${tr.f_v($entity.name)}.php" "defs/entity/actions.php")
<?php
			if (P('form_only') != "4") {
?>
				</td>
<?php
			} else {
?>
				</li>
<?php
			}
?>
<?php
			if (P('form_only') == "4") {
?>
					</ul> 
<?php
			}
?>

