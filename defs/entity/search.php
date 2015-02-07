<?php
	$criteria${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->getCriteria();
?>
#foreach( $property in $entity.properties )
<?php
	if (P('show_search_${tr.f_v($property.name)}', "1") == "1") {

		if (isset($criteria${tr.FV($entity.name)}["${tr.f_v($property.name)}"])) {
			$fieldCriteria${tr.FV($property.name)} = $criteria${tr.FV($entity.name)}["${tr.f_v($property.name)}"];
			$dataTypeCriteria = $fieldCriteria${tr.FV($property.name)}["value"];
		}
?>
	<li
		<?php echo (!$formsInTable && $floatingFields) ? "style='display: inline-block; float: left;'" : '' ?>
	>
		<label align="right" nowrap class="fieldlabel"
			<?php echo (!$formsInTable && $floatingFields) ? "style='width: inherit;'" : '' ?>
		>
#parseFileWithStandardText("modules_project/renderLabel/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "<?php echo T('${tr.F_V($property.name)}') ?>")
		</label>
		<span align="left" nowrap>
#set ($maxSize = $property.size)					
#parseFile("defs/dataTypes/php/renderSearch/${tr.f_v($property.dataType.name)}")
		</span>
<?php
	if (P('empty_values_search', "0") == "1") {
?>
		
		<span class="virgo_search_empty_values" align="left" nowrap style="font-size: 0.7em;">
##		#text("'EMPTY_VALUE'")
##			<input type="checkbox" 
##				style="border: yellow 1 solid;" 
##				class="inputbox checkbox"
##				id="virgo_search_${tr.fV($property.name)}_is_null" 
##				name="virgo_search_${tr.fV($property.name)}_is_null"
##<?php
##		if ($fieldCriteria${tr.FV($property.name)}["is_null"] == 1) {
##?>
##				checked="checked"
##<?php
##		}
##?>
##			/>
			#text("'VALUE'") 
			<select 
				id="virgo_search_${tr.fV($property.name)}_is_null" 
				name="virgo_search_${tr.fV($property.name)}_is_null"
			>
				<option value=""
<?php
		if (isset($fieldCriteria${tr.FV($property.name)}) && $fieldCriteria${tr.FV($property.name)}["is_null"] == 0) {
?>
				selected="selected"
<?php
		}
?>
				></option>
				<option value="not_null"
<?php
		if (isset($fieldCriteria${tr.FV($property.name)}) && $fieldCriteria${tr.FV($property.name)}["is_null"] == 1) {
?>
				selected="selected"
<?php
		}
?>
				>#text("'NOT_EMPTY_VALUE'")</option>
				<option value="null"
<?php
		if (isset($fieldCriteria${tr.FV($property.name)}) && $fieldCriteria${tr.FV($property.name)}["is_null"] == 2) {
?>
				selected="selected"
<?php
		}
?>
				>#text("'EMPTY_VALUE'")</option>
			</select>
		</span>
<?php
	}
?>
	</li>
<?php
	}
?>
#end
#parseFile("modules_project/extraSearchFields/${tr.f_v($entity.name)}.php")
<?php
	$context = null; //$session->get('GLOBAL-VIRGO_CONTEXT_usuniete');
?>	
#foreach( $relation in $entity.childRelations )
<?php
	if (P('show_search_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', '1') == "1") {
		if (isset($criteria${tr.FV($entity.name)}["${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}"])) {
			$fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} = $criteria${tr.FV($entity.name)}["${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}"];
		}
?>
				<li
					<?php echo (!$formsInTable && $floatingFields) ? "style='display: inline-block; float: left;'" : '' ?>
				>
	
				<label align="right" nowrap class="fieldlabel"
					<?php echo (!$formsInTable && $floatingFields) ? "style='width: inherit;'" : '' ?>				
				>#text("'${tr.F_V($relation.parentEntity.name)}'") #text("'${tr.F_V($relation.name)}'")</label>
## ### SLOWNIK ### ##
#if ($relation.parentEntity.dictionary)
<?php
	$ids = (isset($fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["ids"]) ? $fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["ids"] : array());
	$results${tr.FV($relation.parentEntity.name)} = ${tr.f_v($application.name)}\virgo${tr.FV($relation.parentEntity.name)}::getVirgoList();
	$maxListboxSize = 10;
?>
#if ($relation.name)					
    <select class="inputbox " id="virgo_search_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}[]" name="virgo_search_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}[]" multiple
#else
    <select class="inputbox " id="virgo_search_${tr.fV($relation.parentEntity.name)}[]" name="virgo_search_${tr.fV($relation.parentEntity.name)}[]" multiple
#end
<?php
	if (sizeof($results${tr.FV($relation.parentEntity.name)}) > $maxListboxSize) {
		echo "size=" . $maxListboxSize;
	}
?>	
    >
<?php
	while(list($id, $label)=each($results${tr.FV($relation.parentEntity.name)})) {
?>	
<option value="<?php echo $id ?>" 
<?php 
	echo (is_array($ids) && in_array($id, $ids) ? "selected='selected'" : "");
?>><?php echo $label ?>
</option>
<?php
	}
?>
    </select>
#else    
## ### NIESLOWNIK ### ##
<?php
	$value = isset($fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["value"]) ? $fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["value"] : null;
?>
#if ($relation.name)					
    <input type="text" class="inputbox " id="virgo_search_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}" name="virgo_search_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}" value="<?php echo $value ?>">
#else
    <input type="text" class="inputbox " id="virgo_search_${tr.fV($relation.parentEntity.name)}" name="virgo_search_${tr.fV($relation.parentEntity.name)}" value="<?php echo $value ?>">
#end
#end
## ### ####### ### ##				
############# A tutaj dla encji, ktore nie sa slownikami mozliwosc wprowadzenia nowego rekordu parenta
## #if ($relation.parentEntity.dictionary)
## 	<!-- -->
## #else
## 	<br/>
## 	<!-- tutaj -->
## #set ($tmpEntity = $entity)
## #set ($entity = $relation.parentEntity)
## #renderForm($relation.parentEntity.name)	
## #set ($entity = $tmpEntity)
## #end
############# 
</span>
<?php
	if (P('empty_values_search', '0') == "1") {
?>
					<span align="left" nowrap>
##						<input type="checkbox" 
##							style="border: yellow 1 solid;" 
##							class="inputbox checkbox" 
##							id="virgo_search_${tr.fV($relation.parentEntity.name)}${tr.fV($relation.name)}_is_null" 
##							name="virgo_search_${tr.fV($relation.parentEntity.name)}${tr.fV($relation.name)}_is_null"
##<?php
##	if (isset($fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}) && $fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["is_null"] == 1) {
##?>
##							checked="checked"
##<?php
##	}
##?>
##						/>
			#text("'VALUE'") 
			<select 
				id="virgo_search_${tr.fV($property.name)}_is_null" 
				name="virgo_search_${tr.fV($property.name)}_is_null"
			>
				<option value=""
<?php
		if (isset($fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}) && $fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["is_null"] == 0) {
?>
				selected="selected"
<?php
		}
?>
				></option>
				<option value="not_null"
<?php
		if (isset($fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}) && $fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["is_null"] == 1) {
?>
				selected="selected"
<?php
		}
?>
				>#text("'NOT_EMPTY_VALUE'")</option>
				<option value="null"
<?php
		if (isset($fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}) && $fieldCriteria${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}["is_null"] == 2) {
?>
				selected="selected"
<?php
		}
?>
				>#text("'EMPTY_VALUE'")</option>
			</select>						
					</span>
<?php
	}
?>
				</li>
<?php
	}
?>
#end		
#foreach( $relation in $entity.parentRelations )
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
<?php
	if (P('show_search_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}') == "1") {
		if (isset($criteria${tr.FV($entity.name)}["${tr.f_v($secondParentRelation.parentEntity.name)}${tr.f_v($relation.name)}"])) {
			$parentIds = $criteria${tr.FV($entity.name)}["${tr.f_v($secondParentRelation.parentEntity.name)}${tr.f_v($relation.name)}"];
		}
		if (isset($parentIds) && isset($parentIds['ids'])) {
			$selectedIds = $parentIds['ids'];
		}
?>
				<li
					style="vertical-align: top;
					<?php echo (!$formsInTable && $floatingFields) ? "display: inline-block; float: left;" : '' ?>
					"
				>
					<label align="right" nowrap class="fieldlabel"
						<?php echo (!$formsInTable && $floatingFields) ? "style='width: inherit;'" : '' ?>				
					>${tr.Fv($secondParentRelation.parentEntity.namePlural)}</label>
<?php
		$ids = virgo${tr.FV($secondParentRelation.parentEntity.name)}::selectAllAsIdsStatic();
		if (count($ids) < 50) {
			$idAndName = "virgo_search_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($relation.name)}[]";
?>
					<select class="inputbox " multiple='multiple' id="<?php echo $idAndName ?>" name="<?php echo $idAndName ?>">
<?php
			foreach ($ids as $id) {
				$obj = new virgo${tr.FV($secondParentRelation.parentEntity.name)}($id['id']);
?>
					<option value="<?php echo $obj->getId() ?>"
<?php
						echo (isset($selectedIds) && is_array($selectedIds) && in_array($obj->getId(), $selectedIds) ? "selected='selected'" : "");
?>
					><?php echo $obj->getVirgoTitle() ?></option>
<?php
			}
?>
					</select>
<?php
		} else {
?>
			Too many rows: <?php echo count($ids) ?> 
<?php
		}
?>
				</li>
<?php
	}
?>
#end
#else
<?php
	if (P('show_search_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}') == "1") {
?>
#getChildrenData()
				<li
					<?php echo (!$formsInTable && $floatingFields) ? "style='display: inline-block; float: left;'" : '' ?>
				>
					<label align="right" valign="top" nowrap class="fieldlabel"
						<?php echo (!$formsInTable && $floatingFields) ? "style='width: inherit;'" : '' ?>
					>
						${tr.Fv($relation.childEntity.namePlural)}
					</label>
#renderChildrenData('true')
				</li>
<?php
	}
?>
#end
#end
<?php
	unset($criteria${tr.FV($entity.name)});
?>
