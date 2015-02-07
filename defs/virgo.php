#renderToken()
<?php
	if ($${tr.fV($entity.name)}DisplayMode != "TABLE") {
		$tmpAction = R('portlet_action');
		if (
			$tmpAction == "Store" 
			|| $tmpAction == "Apply"
			|| $tmpAction == "StoreAndClear"
			|| $tmpAction == "BackFromParent"
#foreach( $relationToRender in $entity.childRelations )
#if (!$relationToRender.parentEntity.dictionary)
#if ($relationToRender.obligatory)
#set ($backupRelation = $relation)
#set ($backupRelationToRender = $relationToRender)
#set ($includeBackFromParet = 'true')		
			|| $tmpAction == "StoreNew${tr.FV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}"
#set ($relation = $backupRelation)
#end
#end
#end
		) {
## skad mamy wiedziec, czy zapis sie powiodl?
## contextId w przypadku edycji chyba nie pomoze (bo nawet jak sie zapis nie udal, to czemu mialby tracic kontekst?)
## mamy tylko te zmienna $ret, ale ona jest wspolna dla wszystkich portletow. Ale z drugiej strony, tylko jeden mogl byc aktualnie zapisywany, wiec jesli akcja jest jego (jak to sprawdzic? invoked_portlet_object_id) to po nim rozpoznamy		
##			if (/* P('form_only', "0") != "1" || */ is_null($contextId)) { ## zakomentowano, bo sie apply krzaczylo (tzn. po poprawnym apply czytal z requestu zamiast z bazy)			
			$invokedPortletId = R('invoked_portlet_object_id');
## esli to nie jest ajax, to ta wartosc jest nullem. Wtedy nie wiadomo kto wywolal? Od tego chyba jest legacy_invoked_portlet_object_id
			if (is_null($invokedPortletId) || trim($invokedPortletId) == "") {
				$invokedPortletId = R('legacy_invoked_portlet_object_id');
			}
			$pob = $result${tr.FV($entity.name)}->getMyPortletObject();
			$reloadFromRequest = $pob->getPortletSessionValue('reload_from_request', '0');
			if (isset($invokedPortletId) && $invokedPortletId == $_SESSION['current_portlet_object_id'] && isset($reloadFromRequest) && $reloadFromRequest == "1") { 
				$pob->setPortletSessionValue('reload_from_request', '0');
				$result${tr.FV($entity.name)}->loadFromRequest();
			} else {
				if (P('form_only', "0") == "1" && isset($contextId)) {
## komunikat typu 'Dziekujemy za utworzenie konta...
					if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.fV($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($entity.name)}'.DIRECTORY_SEPARATOR.'create_store_message.php')) {
						require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.fV($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($entity.name)}'.DIRECTORY_SEPARATOR.'create_store_message.php');
						$${tr.fV($entity.name)}DisplayMode = "-empty-";
					}
				}
			}
		}
	}
if (!$result${tr.FV($entity.name)}->hideContentDueToNoParentSelected()) {
#if ($isCustomEntity == 'true')
?>
#parseFile("modules_project/customPage/${tr.f_v($entity.name)}.php")
<?php
#else
	$formsInTable = (P('forms_rendering', "false") == "true");
	if (!$formsInTable) {
		$floatingFields = (P('forms_rendering', "false") == "float" || P('forms_rendering', "false") == "float-grid");
		$floatingGridFields = (P('forms_rendering', "false") == "float-grid");
	}
/* MILESTONE 1.1 Form */
	$tabIndex = 1;
	$parentAjaxRendered = "0";
	if ($${tr.fV($entity.name)}DisplayMode == "FORM") {
?>
#preventExit()
	<div class="form_edit">
#set ($firstFieldName = "") 		
#parseFile("modules_project/beforeForm/${tr.f_v($entity.name)}.php")
			<fieldset class="form">
				<legend>
#text("'${tr.F_V($entity.name)}'")				
:</legend>
<?php
	if (!$formsInTable) {
?>
				<ul <?php echo $floatingGridFields ? "class='float_grid_fields'" : "" ?>>
<?php
	} else {
?>
				<table>
<?php
	}
?>
#parseFile("modules_project/editExtraFieldsBefore/${tr.f_v($entity.name)}.php")
<?php
	$editForm = P('edit_form', "virgo_default");
	if (is_null($editForm) || trim($editForm) == "" || $editForm == "virgo_default") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#parseFile("defs/entity/form.php")
<?php
	} elseif ($editForm == "virgo_entity") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#parseFile("modules_project/insteadForm/${tr.f_v($entity.name)}.php")
#foreach ($fileName in $util.getFiles("modules_project/insteadForm/${tr.f_v($entity.name)}/"))
<?php
	} elseif ($editForm == "$fileName") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#parseFile("modules_project/insteadForm/${tr.f_v($entity.name)}/$fileName")
#end	
<?php
	}
?>
#parseFile("modules_project/editExtraFieldsAfter/${tr.f_v($entity.name)}.php")
<?php
	if (!$formsInTable) {
?>
				</ul>
<?php
	} else {
?>
				</table>
<?php
	}
?>
#foreach ($relationChild in $entity.parentRelations)
#if (!$relationChild.childEntity.dictionary && !$relationChild.childEntity.weak)
<?php 
	if (false) { //$componentParams->get('show_form_${tr.f_v($relationChild.childEntity.namePlural)}${tr.f_v($relationChild.name)}') == "1") {
?>
#parseFileWithStandard("modules_project/insteadTableFormChild/${tr.f_v($entity.name)}.php" "defs/entity/table_form_child.php")
<?php
	}
?>
#end
#end
			</fieldset>
#parseFile("modules_project/afterForm/${tr.f_v($entity.name)}.php")				
#getStatusEntityWithWorkflow()
#if ($statusEntity)
#viewLifecycleHistory($statusEntity)
#end
#parse("defs/entity/audit.php")
				<div class="buttons form-actions">
#parse("defs/entity/params.php")
						<input type="hidden" name="virgo_changed" value="N">
#parseFileWithStandard("modules_project/insteadFormActions/${tr.f_v($entity.name)}.php" "defs/entity/form_actions.php")
#parseFile("modules_project/extraFormActions/${tr.f_v($entity.name)}.php")
				</div>
#if ($firstFieldName && $firstFieldName != "") 		
<script type="text/javascript" language="JavaScript">
// document.forms['portlet_form_<?php echo $this->getId() ?>'].elements['$firstFieldName'].focus();
</script>
#end
	</div>
<?php
/* MILESTONE 1.2 Create */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "CREATE") {
?>
#preventExit()
	<div class="form_edit">
#set ($firstFieldName = "") 		
#parseFile("modules_project/beforeCreate/${tr.f_v($entity.name)}.php")
			<fieldset class="form">
				<legend>
#text("'${tr.F_V($entity.name)}'")				
:</legend>
<?php
	if (!$formsInTable) {
?>
				<ul>
<?php
	} else {
?>
				<table>
<?php
	}
?>
#parseFile("modules_project/editCreateExtraFieldsBefore/${tr.f_v($entity.name)}.php")
<?php
	$createForm = P('create_form', "virgo_default");
	if (is_null($createForm) || trim($createForm) == "" || $createForm == "virgo_default") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#parseFile("defs/entity/create.php")
<?php
	} elseif ($createForm == "virgo_entity") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#parseFileWithStandard("modules_project/insteadCreate/${tr.f_v($entity.name)}.php" "defs/entity/create.php")
#foreach ($fileName in $util.getFiles("modules_project/insteadCreate/${tr.f_v($entity.name)}/"))
<?php
	} elseif ($createForm == "$fileName") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#parseFile("modules_project/insteadCreate/${tr.f_v($entity.name)}/$fileName")
#end	
<?php
	}
?>
#parseFile("modules_project/editCreateExtraFieldsAfter/${tr.f_v($entity.name)}.php")
<?php
	if (!$formsInTable) {
?>
				</ul>
<?php
	} else {
?>
				</table>
<?php
	}
?>
			</fieldset>
#parseFile("modules_project/afterCreate/${tr.f_v($entity.name)}.php")							
#parse("defs/entity/audit.php")
				<div class="buttons form-actions">
#parse("defs/entity/params.php")
						<input type="hidden" name="virgo_changed" value="N">
#parseFile("modules_project/extraCreateActions/${tr.f_v($entity.name)}.php")
#parseFileWithStandard("modules_project/insteadCreateActions/${tr.f_v($entity.name)}.php" "defs/entity/create_actions.php")
				</div>
#if ($firstFieldName && $firstFieldName != "") 		
<script type="text/javascript" language="JavaScript">
// document.forms['portlet_form_<?php echo $this->getId() ?>'].elements['$firstFieldName'].focus();
</script>
#end
	</div>
<?php
/* MILESTONE 1.3 Search */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "SEARCH") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
	<div class="form_edit form_search">
			<fieldset class="form">
				<legend>
#text("'${tr.F_V($entity.name)}'")				
:</legend>
				<ul>
#parseFileWithStandard("modules_project/insteadSearch/${tr.f_v($entity.name)}.php" "defs/entity/search.php")
				</ul>
			</fieldset>
## #parse("defs/entity/audit.php")
				<div class="buttons form-actions">
#parse("defs/entity/params.php")
#parseFileWithStandard("modules_project/insteadSearchActions/${tr.f_v($entity.name)}.php" "defs/entity/search_actions.php")
#actionButtonSimple("Clear" "'CLEAR'")<?php						
	if (P('form_only', "0") != "2") {
?>#actionButtonSimple("Close" "'CLOSE'")
<?php						
	}
?>
				</div>
	</div>
<?php
/* MILESTONE 1.4 View */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "VIEW") {
?>
	<div class="form_view">
<?php
	$editForm = P('view_form', "virgo_default");
	if (is_null($editForm) || trim($editForm) == "" || $editForm == "virgo_default") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
			<fieldset class="form">
				<legend>
#text("'${tr.F_V($entity.name)}'")				
:</legend>
<?php
	if (!$formsInTable) {
?>
				<ul>
<?php
	} else {
?>
				<dl class="dl-horizontal">
<?php
	}
?>
#parseFile("defs/entity/view.php")
<?php
	if (!$formsInTable) {
?>
				</ul>
<?php
	} else {
?>
				</dl>
<?php
	}
?>
			</fieldset>
<?php
	} elseif ($editForm == "virgo_entity") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
			<fieldset class="form">
				<legend>
#text("'${tr.F_V($entity.name)}'")				
:</legend>
				<ul>
#parseFile("modules_project/insteadView/${tr.f_v($entity.name)}.php")
				</ul>
			</fieldset>
#foreach ($fileName in $util.getFiles("modules_project/insteadView/${tr.f_v($entity.name)}/"))
<?php
	} elseif ($editForm == "$fileName") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#parseFile("modules_project/insteadView/${tr.f_v($entity.name)}/$fileName")
#end	
<?php
	}
?>
#getStatusEntityWithWorkflow()
#if ($statusEntity)
#viewLifecycleHistory($statusEntity)
#end
#parse("defs/entity/audit.php")
				<div class="buttons form-actions">
#parse("defs/entity/params.php")
## #parseFile("modules_project/extraViewActions/${tr.f_v($entity.name)}.php")
#parseFileWithStandard("modules_project/insteadViewActions/${tr.f_v($entity.name)}.php" "defs/entity/view_actions.php")
				</div>
	</div>
<?php
/* MILESTONE 1.5 Table */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "TABLE") {
PROFILE('TABLE');
		if (P('form_only') == "3") {
?>
#parseFile("defs/entity/calendar_view.php")
<?php
		} else {
?>
#parseFile("defs/entity/table.php")
<?php
		}
PROFILE('TABLE');
/* MILESTONE 1.6 TableForm */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "TABLE_FORM") {
#set ($calendar_rendered = 'false')
#set ($tinemce_initialized = 'false')
?>
#preventExit()
#parseFileWithStandard("modules_project/insteadTableForm/${tr.f_v($entity.name)}.php" "defs/entity/table_form.php")
#parseFile("modules_project/afterListTable/${tr.f_v($entity.name)}.php")
#if ($entity.versioned)
<?php
/* MILESTONE 1.7 ShowHistory */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "SHOW_HISTORY") {
?>		
#parseFile("defs/entity/show_history.php")
				<div class="buttons form-actions">
#parse("defs/entity/params.php")
#actionButtonSimple("Close" "CLOSE")
				</div>
<?php
/* MILESTONE 1.8 ShowRevision */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "SHOW_REVISION") {
?>		
#parseFile("defs/entity/show_revision.php")
				<div class="buttons form-actions">
#parse("defs/entity/params.php")
<?php
	if (!is_null($previousHistoryEntryId)) {
?>
#actionButton("ShowRevision" "'PREV'" "" "" "" "" "this.form.history_id.value='<?php echo $previousHistoryEntryId ?>';")
<?php
	} else {
?>#actionDisabled('PREV')<?php						
	}
?>
#actionButton("ShowHistory" "'CLOSE'" "<?php echo $recordId ?>" "" "" "" "")
<?php
	if (!is_null($nextHistoryEntryId)) {
?>
#actionButton("ShowRevision" "'NEXT'" "" "" "" "" "this.form.history_id.value='<?php echo $nextHistoryEntryId ?>';")
<?php
	} else {
?>#actionDisabled('NEXT')<?php						
	}
?>
				</div>
#end
<?php
#getStatusEntityWithWorkflow()
#if ($statusEntity)
/* MILESTONE 1.9 ShowStatusLog */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "STATUS_LOG") {
		$selected = R('selected_status_' . $_SESSION['current_portlet_object_id']);
		if (isset($selected) && $selected != "") {
			$where = ' WHERE next.${tr.f_v($statusEntity.prefix)}_id = ' . $selected;
		} else {
			$where = "";
		}
		$query = "
SELECT 
	next.change_timestamp time, 
	ent.${tr.f_v($entity.prefix)}_virgo_title element, 
	${tr.f_v($statusEntity.prefix)}p.${tr.f_v($statusEntity.prefix)}_virgo_title old_status, 
	${tr.f_v($statusEntity.prefix)}n.${tr.f_v($statusEntity.prefix)}_virgo_title new_status, 
	next.username user
FROM 
	${tr.f_v($application.prefix)}_${tr.f_v($statusEntity.name)}_history next
	LEFT OUTER JOIN ${tr.f_v($application.prefix)}_${tr.f_v($statusEntity.name)}_history prev ON prev.${tr.f_v($entity.prefix)}_id = next.${tr.f_v($entity.prefix)}_id AND prev.${tr.f_v($statusEntity.prefix)}_id != next.${tr.f_v($statusEntity.prefix)}_id
	LEFT OUTER JOIN ${tr.f_v($application.prefix)}_${tr.f_v($statusEntity.namePlural)} ${tr.f_v($statusEntity.prefix)}p ON ${tr.f_v($statusEntity.prefix)}p.${tr.f_v($statusEntity.prefix)}_id = prev.${tr.f_v($statusEntity.prefix)}_id
	LEFT OUTER JOIN ${tr.f_v($application.prefix)}_${tr.f_v($statusEntity.namePlural)} ${tr.f_v($statusEntity.prefix)}n ON ${tr.f_v($statusEntity.prefix)}n.${tr.f_v($statusEntity.prefix)}_id = next.${tr.f_v($statusEntity.prefix)}_id
	LEFT OUTER JOIN ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ent ON ent.${tr.f_v($entity.prefix)}_id = next.${tr.f_v($entity.prefix)}_id
{$where}	
ORDER BY next.change_timestamp DESC
";
		$rows = QR($query);
?>
		<table cellspacing="1">
			<tr style="background-color: #CCC">
				<td></td>
				<td></td>
				<td></td>
				<td>
					<select name="selected_status_<?php echo $_SESSION['current_portlet_object_id'] ?>" onchange="this.form.submit()">
						<option></option>
<?php
		$statusList = ${tr.f_v($application.name)}\virgo${tr.FV($statusEntity.name)}::getVirgoList();
		foreach ($statusList as $id => $value) {
?>
						<option value="<?php echo $id ?>" <?php echo $id == $selected ? 'selected' : '' ?>><?php echo $value ?></option>
<?php
		}
?>
					</select>
				</td>
				<td></td>
			</tr>
<?php
		$parzysty = 1;
		foreach ($rows as $row) {
			$parzysty = !$parzysty; 
?>
			<tr style="background-color: #<?php echo $parzysty ? 'FFF' : 'EEE' ?>;">
				<td style="width: 105px;"><?php echo $row['time'] ?></td>
				<td><?php echo $row['element'] ?></td>
				<td><?php echo $row['old_status'] ?></td>
				<td><?php echo $row['new_status'] ?></td>
				<td><?php echo $row['user'] ?></td>
			</tr>
<?php
		}
?>
		</table>
#actionButtonSimple("Close" "'CLOSE'")
<?php
#end
## dodanie nowego parenta
## tylko dla wymaganych relacji i dodajemy go minimalnie, czyli tylko wymagane pola
#foreach( $relationToRender in $entity.childRelations )
#if (!$relationToRender.parentEntity.dictionary)
#if ($relationToRender.obligatory)
#set ($backupRelation = $relation)
#set ($backupRelationToRender = $relationToRender)
/* MILESTONE 1.10 AddNewParents */
	} elseif ($${tr.fV($entity.name)}DisplayMode == "ADD_NEW_PARENT_${tr.F_V($relationToRender.parentEntity.name)}${relationToRender.u}${tr.F_V($relationToRender.name)}") {
## #set ($entityBackup = $entity)
#set ($entityParent = $relationToRender.parentEntity)
		$result${tr.FV($entityParent.name)} = ${tr.f_v($application.name)}\virgo${tr.FV($entityParent.name)}::createGuiAware();
		$result${tr.FV($entityParent.name)}->loadFromRequest();
?>
<fieldset>
	<label>Dodaj nowy rekord ${tr.Fv($entityParent.name)}</label>
#foreach($property in $entityParent.properties )
#parseFileWithStandard("modules_project/renderCreateProperty/${tr.f_v($entityParent.name)}/${tr.f_v($property.name)}.php" "defs/entity/create_property.php")
#end
#set ($alreadyAddingOtherParent = 'true')
#foreach($relation in $entityParent.childRelations)
#renderParentSelect("create")
#end
#set ($alreadyAddingOtherParent = 'false')
<input type="hidden" name="calling_view" value="<?php echo R('calling_view') ?>">
</fieldset>
#actionButtonSimple("StoreNew${tr.FV($entityParent.name)}${tr.FV($backupRelationToRender.name)}" "'STORE'")
#actionButtonSimple("BackFromParent" "'CLOSE'")
## #set ($entity = $entityBackup)
<?php
	$pobId = $_SESSION['current_portlet_object_id'];
	$childId = R('${tr.f_v($entity.prefix)}_id_' . $pobId);
?>
<input 
	type="hidden" 
	name="${tr.f_v($entity.prefix)}_id_<?php echo $pobId ?>" 
	value="<?php echo $childId ?>"
/>
#foreach($property in $entity.properties )
<input 
	type="hidden" 
	name="${tr.f_v($entity.prefix)}_${tr.fV($property.name)}_<?php echo $childId ?>" 
	value="<?php echo R('${tr.f_v($entity.prefix)}_${tr.fV($property.name)}_' . $childId) ?>"
/>
#end
#foreach($relation in $entity.childRelations)
<input 
	type="hidden" 
	name="${tr.f_v($entity.prefix)}_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}_<?php echo $childId ?>" 
	value="<?php echo R('${tr.f_v($entity.prefix)}_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}_' . $childId) ?>"
/>
#end
<?php		
#set ($relation = $backupRelation)
#end
#end
#end
	} else {
		$virgoShowReturn = true;
?>
		<div class="<?php echo $${tr.fV($entity.name)}DisplayMode ?>">
<?php
#parseFile("modules_project/customDisplayModes/${tr.f_v($entity.name)}.php")
?>
			<div class="buttons form-actions">
## #parse("defs/entity/params.php")
<?php
				if ($virgoShowReturn) {
?>
#actionButtonSimple("Close" "'CLOSE'")
<?php
				}
?>
			</div>
		</div>
<?php
	}
## if (!$result${tr.FV($entity.name)}->hideContentDueToNoParentSelected()) 
} 
#end	
?>


