#set ($rowAction = 'TRUE')

#renderToken()
## #actionButtonSimple("Select" "'SELECT'")
<?php
	if (P('master_mode', "0") == "0") {
#permissionRestrictedBlockBegin("view")
?>
<?php						
		if (P('show_details_method') != "1") {
?>#actionButtonSimple("View" "'VIEW'")
<?php			
		}
?>
<?php
#permissionRestrictedBlockEnd()
		if ($this->canEdit()) {
?>
#if ($entity.name != "wpis dziennika")			
<?php
#permissionRestrictedBlockBegin("form")
#if ($entity.dictionary)
		if ($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_virgo_deleted']) {
?>#actionDisabled("'EDIT'")<?php
		} else {
#end
?>#actionButtonSimple("Form" "'EDIT'")
<?php			
#if ($entity.dictionary)
		}
#end
?>
<?php
#permissionRestrictedBlockEnd()
?>
<?php
#permissionRestrictedBlockBegin("delete")
#set ($DQ = '"') 
#if ($entity.dictionary)
		if ($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_virgo_deleted']) {
?>#actionDisabled("'DELETE'")<?php
		} else {
#end
?>#actionButtonConfirmation("Delete" "'DELETE'" "<?php echo T(${DQ}ARE_YOU_SURE_YOU_WANT_REMOVE${DQ}, T(${DQ}${tr.F_V($entity.name)}${DQ}), ${DQ}\\'${DQ}.rawurlencode($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_virgo_title']).${DQ}\\'${DQ}) ?>")
<?php
#if ($entity.dictionary)
		}
#end
#permissionRestrictedBlockEnd()
?>
#getStatusEntityWithWorkflow()
#if ($statusEntity)
<?php
#permissionRestrictedBlockBegin("form")
## po zoptymalizowaniu juz nie ma kolumny z id parent a w selekcie :-( Wiec trzeba tu ladowac osobno
			## ale najpierw sprobujmy, moze jednak w tabeli jest ta kolumna, wiec w tablicy tez bedzie
			if (isset($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id'])) {
				$parent${tr.FV($statusEntity.name)} = ${tr.f_v($application.name)}\virgo${tr.FV($statusEntity.name)}::getById($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id']);
			} else {
				$tmp${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}(); 
				$tmpRes = $tmp${tr.FV($entity.name)}->select('', 'all', '', '', $where = '', $queryString = 'SELECT ${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id AS id FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = ' . $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id']);
				$parent${tr.FV($statusEntity.name)} = ${tr.f_v($application.name)}\virgo${tr.FV($statusEntity.name)}::getById($tmpRes[0]['id']);
			}
			$results${tr.FV($statusEntity.name)}WorkflowNext = $parent${tr.FV($statusEntity.name)}->get${tr.FV($statusEntity.name)}WorkflowsNext(); ## czy to jest zmiana raz w tą raz w tą stronę nieskończona? Nie, kończymy ją. OFICJALNIE ma być tak: "task status workflows"! czyli domyślnie
##			if (!is_null($results${tr.FV($statusEntity.name)}WorkflowNext) && count($results${tr.FV($statusEntity.name)}WorkflowNext) > 0) {
##				$workflowImage = "triangle.png";
##			} else {
##				$workflowImage = "triangle_empty.png";
##			}
?>
 <div 
	class="parent inlineBlock"
>
<div class="button_wrapper dropdown"><input type="submit" class="button btn btn-mini" style="cursor: pointer;" onclick="changeDisplay('workflow_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>'); return false;" value="<?php echo T('CHANGE_STATUS') ?> &nabla;"/><div class="button_right"></div></div>
<div id="workflow_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>" class="child" style="display: none; position: absolute; box-shadow: 1px 2px 2px black; border-radius: 0px; z-index: 999; ">
		<table cellspacing="0">
<?php
			foreach ($results${tr.FV($statusEntity.name)}WorkflowNext as $result${tr.FV($statusEntity.name)}WorkflowNext) {
?>
## #actionButton("VirgoChange${tr.FV($statusEntity.name)}${tr.FV($relation.name)}" "$result${tr.FV($statusEntity.name)}WorkflowNext->get${tr.FV($statusEntity.name)}Prev()->getVirgoTitle()" "" "" "" "" "this.form.componentName.value='<?php echo $result${tr.FV($statusEntity.name)}['${tr.f_v($statusEntity.prefix)}_id'] ?>';")
			<tr>
			<td style="border: none; background-color: white !important;">
<input 
	type="submit" 
	style="border: none;"
	value="<?php echo $result${tr.FV($statusEntity.name)}WorkflowNext->get${tr.FV($statusEntity.name)}Prev()->getVirgoTitle() ?>" 
	onclick="this.form.portlet_action.value='VirgoChange${tr.FV($statusEntity.name)}${tr.FV($relation.name)}';this.form.${tr.fV($entity.prefix)}_id_<?php echo $this->getId() ?>.value='<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>';this.form.virgo_parent_id.value='<?php echo $result${tr.FV($statusEntity.name)}WorkflowNext->get${tr.FV($statusEntity.name)}Prev()->getId() ?>';"
>
				</td>
			</tr>
<?php
			}
?>
		</table>
	</div> 
</div>
<?php
#permissionRestrictedBlockEnd()
?>
#end
<?php 
		} ## $this->canEdit()
## koniec warunku 'master mode'
	} 
		if ($this->canEdit()) {
?>
#if ($entity.name != "wpis dziennika")			
<?php
	if (P('enable_record_duplication', "0") == "1") {
#permissionRestrictedBlockBegin("add")
?>#actionButtonSimple("Duplicate" "'DUPLICATE'")
<?php			
	}
#end
?>
<?php
#permissionRestrictedBlockEnd()
?>
#if ($entity.versioned)
<?php
	if (false) { //$componentParams->get('show_record_history') == "0") {
?>
#actionButtonSimple("ShowHistory" "'HISTORY'")
<?php
	}
?>
#end
#parseFile("modules_project/extraPublicActions/${tr.f_v($entity.name)}.php")
#end
#set ($displayOrderPropertyName = "")
#foreach( $property in $entity.properties )
#if ($property.name == 'display order' || ${tr.f_v($property.name)} == 'kolejnosc_wyswietlania' || ${tr.f_v($property.name)} == 'order' || ${tr.f_v($property.name)} == 'kolejnosc')
#set ($displayOrderPropertyName = $property.name)
#end
#end
#if ($displayOrderPropertyName != "")
<?php
#permissionRestrictedBlockBegin("form")
	if ($virgoOrderColumn == "${tr.f_v($entity.prefix)}_${tr.f_v($displayOrderPropertyName)}") {
?>	
<input type="hidden" name="virgo_swap_down_with_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>" id="virgo_swap_down_with_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>" value="">
<td>
<?php
		if (isset($virgoPreviousId)) {
?>
<input type="hidden" name="virgo_swap_up_with_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>" value="<?php echo $virgoPreviousId ?>">
<script type="text/javascript">
var f = document.getElementById("virgo_swap_down_with_<?php echo $virgoPreviousId ?>");
f.value = <?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>;
f = document.getElementById("arrow_down_<?php echo $virgoPreviousId ?>");
f.style.display = 'block';
</script>
#actionButtonSimple("VirgoUp" "'↑'")
<?php
		}
?>
</td>
<td>
<div id="arrow_down_<?php echo $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] ?>" style="display: none;">
#actionButtonSimple("VirgoDown" "'↓'")
</div>
</td>
<?php		
		$virgoPreviousId = $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'];
	}
#permissionRestrictedBlockEnd()
?>
#end
<?php
			$actions = virgoRole::getExtraActions('ER');
			foreach ($actions as $action) {
?>
#actionButtonSimple("${dollar}action" "$action")
<?php						
			}
		} ## $this->canEdit()
?>
#set ($rowAction = 'FALSE')
