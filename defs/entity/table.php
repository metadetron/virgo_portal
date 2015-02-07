<?php
PROFILE('table_01');
?>
		<script type="text/javascript">
## wiele z tego kodu powinno byc tylko raz dla wszystkich portletow wyswietlane...		
			var ${tr.fV($entity.name)}ChildrenDivOpen = '';
			
			function childrenButtonClicked(clickedDivId) {
				var div = document.getElementById(clickedDivId);
				if (clickedDivId == ${tr.fV($entity.name)}ChildrenDivOpen) {
					div.style.display = 'none';
					${tr.fV($entity.name)}ChildrenDivOpen = '';
				} else {
					if (${tr.fV($entity.name)}ChildrenDivOpen != '') {
						document.getElementById(${tr.fV($entity.name)}ChildrenDivOpen).style.display = 'none';
					}
					div.style.display = 'block';
					${tr.fV($entity.name)}ChildrenDivOpen = clickedDivId;
				}
			}
			
			function copyIds(form, pobId) {
				var chcks = document.getElementsByTagName("input");
				var ids = form.ids;
				ids.value = '';
				var firstOne = 1;
				for (i=0;i<chcks.length;i++) {
					if (isOurCheckbox(chcks[i].name, pobId)) {
						if (chcks[i].checked == 1) {
							if (firstOne == 1) {
								firstOne = 0;
							} else {
								ids.value = ids.value + ",";
							}
							ids.value = ids.value + chcks[i].name.substring(chcks[i].name.lastIndexOf("_")+1);
						}
					}
				}
## A po co tu to bylo???				
##				<?php echo JSFS() ?>
			}
			
			function isOurCheckbox(name, pobId) {
				return name.match("^DELETE_" + pobId + "_\d*");
			}
			
			function nothingSelected(form, pobId) {
				var chcks = document.getElementsByTagName("input");
				for (i=0;i<chcks.length;i++) {
					if (isOurCheckbox(chcks[i].name, pobId)) {
						if (chcks[i].checked == 1) {
							return false;
						}
					}
				}
				return true;
			}
			
			function checkAll(value, pobId) {
				var chcks = document.getElementsByTagName("input");
				for (i=0;i<chcks.length;i++) {
					if (isOurCheckbox(chcks[i].name, pobId)) {
						chcks[i].checked = value;
					}
				}
			}
			
#if ($entity.dictionary)
<?php
	if (P('master_mode', "0") != "1") {
?>
#end
			if (typeof portletCurrentTab === 'undefined') {
				var portletCurrentTab = new Array();
				function showOperations(tab, pobId) {
					var div = document.getElementById("operations_" + portletCurrentTab[pobId] + "_" + pobId);
					var span = document.getElementById("control_" + portletCurrentTab[pobId] + "_" + pobId);
					div.style.display = 'none';
					span.className = 'operations_tab';
					portletCurrentTab[pobId] = tab;
					div = document.getElementById("operations_" + portletCurrentTab[pobId] + "_" + pobId);
					span = document.getElementById("control_" + portletCurrentTab[pobId] + "_" + pobId);
					div.style.display = 'block';
					span.className = 'operations_tab_current';
				}
				function changeDisplay(id) {
					var div = document.getElementById(id);
					div.style.display = div.style.display == "none" ? "inline-block" : "none";
				}
			}
			portletCurrentTab[<?php echo $this->getId() ?>] = "basic";
#if ($entity.dictionary)
<?php
	}
?>
#end
			
		</script>
<?php		
PROFILE('table_01');
PROFILE('table_02');
#foreach( $relation in $entity.parentRelations )
#foreach( $subrelation in $relation.childEntity.childRelations )
	if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.f_v($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($subrelation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php')) require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.f_v($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($subrelation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php');
#end	
#end
			$showPage = $result${tr.FV($entity.name)}->getShowPage(); 
			$showRows = $result${tr.FV($entity.name)}->getShowRows(); 
?>
#parse("defs/entity/params.php")
#parseFile("modules_project/beforeTable/${tr.f_v($entity.name)}.php")
		<input type="hidden" name="virgo_parent_id" id="virgo_parent_id"/>
<?php
			$actionOnRowClick = P('action_on_row_click', 'Select');
			if ($actionOnRowClick == "Custom") {
				$actionOnRowClick = P('action_on_row_click_custom', 'Select');
			}
			$actionOnRowDoubleClick = P('action_on_row_double_click', 'View');
			if ($actionOnRowDoubleClick == "Custom") {
				$actionOnRowDoubleClick = P('action_on_row_click_custom', 'View');
			}
			$hint = TE('HINT_${tr.F_V($property.entity.name)}');
			if (isset($hint)) {
?>
				<p><br/><?php echo $hint ?><br/><br/></p>
<?php
			}

?>
<?php echo P('master_mode', "0") == "1" ? "<div class='well sidebar-nav'>" : "" ?>
		<table class="data_table table table-striped table-hover table-condensed <?php echo P('form_only') == "4" ? "chessboard" : "" ?> <?php echo P('master_mode', "0") == "1" ? "nav nav-list" : "" ?>" cellpadding="0" cellspacing="<?php echo P('form_only') == "4"  ? "5" : "0" ?>" border="0">
<?php
			if (P('master_mode', "0") == "1") {
?>			
				<tr><td colspan="99" class="nav-header"><?php echo T('${tr.Fv($entity.namePlural)}') ?></td></tr>
<?php
			}
?>			
<?php
PROFILE('table_02');
#exectime_start("main select")			
			$virgoOrderColumn = $result${tr.FV($entity.name)}->getOrderColumn();
			$virgoOrderMode = $result${tr.FV($entity.name)}->getOrderMode();
			$resultCount = -1;
			$filterApplied = false;
			$results${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->getTableData($resultCount, $filterApplied);
#exectime_end_echo("main select")			
PROFILE('table_03');

			if (P('form_only') != "4") {
?>		
#if ($entity.dictionary)
<?php
	if (P('master_mode', "0") != "1") {
?>
#end
#parseFileWithStandard("modules_project/renderTableHeader/${tr.f_v($entity.name)}.php" "defs/entity/table_header.php")
#if ($entity.dictionary)
<?php
	}
?>
#end
<?php
			}
			if ($resultCount != 0) {
				if (((int)$showRows) * (((int)$showPage)-1) == $resultCount ) {
					$showPage = ((int)$showPage)-1;
					$result${tr.FV($entity.name)}->setShowPage($showPage);
				}
				$index = 0;
PROFILE('table_04');
#exectime_start("rows rendering")
				$contextRowIdInTable = null;
				$firstRowId = null;
				foreach ($results${tr.FV($entity.name)} as $result${tr.FV($entity.name)}) {
					$index = $index + 1;
?>
<?php
## eksperymentalnie:
$fileNameToInclude = PORTAL_PATH . "/portlets/${tr.f_v($application.name)}/virgo${tr.FV($entity.name)}/modules/renderTableRow_{$_SESSION['current_portlet_object_id']}.php";
if (!file_exists($fileNameToInclude) || !is_readable($fileNameToInclude)) {
	$fileNameToInclude = PORTAL_PATH . "/portlets/${tr.f_v($application.name)}/modules/renderTableRow.php";
} 
if (!file_exists($fileNameToInclude) || !is_readable($fileNameToInclude)) {
?>
#parseFileWithStandard("modules_project/renderTableRow/${tr.f_v($entity.name)}.php" "defs/entity/table_row.php")
<?php
} else {
	include($fileNameToInclude);
}
?>
<?php
				}
	if (is_null($contextRowIdInTable)) {
## jednak pierwszy rekord musi byc kontekstem 		
		$forceContextOnFirstRow = P('force_context_on_first_row', "1");
		if ($forceContextOnFirstRow == "1") {
			virgo${tr.FV($entity.name)}::setContextId($firstRowId, false);
			if (P('form_only') != "4") {
?>
<script type="text/javascript">
		${dollar}('form#portlet_form_<?php echo $this->getId() ?> table.data_table tr#<?php echo $this->getId() ?>_<?php echo $firstRowId ?>').addClass("contextClass");
</script>
<?php
			}
		}
	}				
				unset($result${tr.FV($entity.name)});
				unset($results${tr.FV($entity.name)});
				if (isset($contextIdOwn) && trim($contextIdOwn) != "") {
					if ($contextIdConfirmed == false) {
						$tmp${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
						$tmpCount = $tmp${tr.FV($entity.name)}->getAllRecordCount(' ${tr.f_v($entity.prefix)}_id = ' . $contextIdOwn);
						if ($tmpCount == 0) {
							virgo${tr.FV($entity.name)}::clearRemoteContextId($tabModeEditMenu);
						}
					}
				}
#exectime_end_echo("rows rendering")						
#foreach( $property in $entity.properties )
#exectime_echo($property.name)
#end
#exectime_echo("extra data")
?>			
<?php
PROFILE('table_05');
				if ($showRows == 'all') {
					$pageCount = 1;
				} else {
					$pageCount = (int)($resultCount / $showRows) + 1;
					if ($resultCount % $showRows == 0) {
						$pageCount = $pageCount - 1;
					}
				}
PROFILE('table_05');
PROFILE('table_06_1');
?>
## nie tylko dla slownikow! #if ($entity.dictionary)
<?php
	if (P('master_mode', "0") != "1") {
?>
## #end
<?php
		if (P('default_page_size', "20") != 'all' || P('available_page_sizes', "5,10,20,50,all") != "all") {
?>
			<tr class="table_footer">
				<td colspan="99" nowrap="nowrap">
						<table cellspacing="0" cellpadding="0" width="100%">
							<tr class="table_footer">
<?php
$showSelectAll = FALSE;
#permissionRestrictedBlockBegin("form")
$showSelectAll = TRUE;
?>
								<td nowrap="nowrap" width="33%" align="left" class="select_all">
#text("'SELECT_ALL'")
									<input type="checkbox" class="checkbox" onclick="checkAll(this.checked, <?php echo $this->getId() ?>)">
								</td>
<?php
#permissionRestrictedBlockEnd()
?>
<?php
if (! $showSelectAll) {
#permissionRestrictedBlockBegin("delete")
?>
#text("'SELECT_ALL'")
								<td nowrap="nowrap" width="33%" align="left" class="select_all">
									<input type="checkbox" class="checkbox" onclick="checkAll(this.checked, <?php echo $this->getId() ?>)">
								</td>
<?php
#permissionRestrictedBlockEnd()
}
?>
								<td nowrap="nowrap" width="34%" align="center" class="select_page">
									<input class="button_paging<?php echo ($showPage == 1 ? '_disabled' : '') ?>" src="<?php echo $_SESSION['portal_url'] ?>/media/icons/go-first.png" type="image" value="&#x21E4" onClick="this.form.action='';this.form.target='';this.form.virgo_show_page.value='1'; this.form.portlet_action.value='ChangePaging'; <?php echo JSFS() ?>" <?php echo ($showPage == 1 ? 'disabled="disabled"' : '') ?><?php echo ($showPage > 1 ? ' onmousedown="this.className=\'button_paging_pressed\'" onmouseup="this.className=\'button_paging\'" onmouseout="this.className=\'button_paging\'"' : '') ?>>
									<input class="button_paging<?php echo ($showPage == 1 ? '_disabled' : '') ?>" src="<?php echo $_SESSION['portal_url'] ?>/media/icons/go-previous.png" type="image" value="&#x2190" onClick="this.form.action='';this.form.target='';this.form.virgo_show_page.value='<?php echo ($showPage - 1) ?>'; this.form.portlet_action.value='ChangePaging'; <?php echo JSFS() ?>" <?php echo ($showPage == 1 ? 'disabled="disabled"' : '') ?><?php echo ($showPage > 1 ? ' onmousedown="this.className=\'button_paging_pressed\'" onmouseup="this.className=\'button_paging\'" onmouseout="this.className=\'button_paging\'"' : '') ?>>
#text("'PAGE'") 
<?php
PROFILE('table_06_1');
PROFILE('table_06_2');
	if ($pageCount > 100) {
?>
		<input class="inputbox" size="5" value="<?php echo $showPage ?>" name="showPageGui" onChange="this.form.action='';this.form.target='';this.form.virgo_show_page.value=this.value; this.form.portlet_action.value='ChangePaging'; <?php echo JSFS() ?>">
<?php
	} else {
?>
									<select class="inputbox" name="showPageGui" onChange="this.form.action='';this.form.target='';this.form.virgo_show_page.value=this.value; this.form.portlet_action.value='ChangePaging'; <?php echo JSFS() ?>">
<?php
				$tmpPageIndex = 1;
				while ($tmpPageIndex <= $pageCount) {
		
?>
										<option value="<?php echo ($tmpPageIndex) ?>" <?php echo ($tmpPageIndex == $showPage ? "selected='selected'" : "") ?>><?php echo ($tmpPageIndex) ?></option>
<?php
					$tmpPageIndex = $tmpPageIndex + 1;
				}
?>
									</select>
<?php
	}
?>
#text("'OF'") 
									<?php echo ($pageCount) ?>
									<input type="hidden" name="virgo_show_page" value="<?php echo ($showPage) ?>">
									<input class="button_paging<?php echo ($showPage == $pageCount ? '_disabled' : '') ?>" src="<?php echo $_SESSION['portal_url'] ?>/media/icons/go-next.png" type="image" value="&#x2192" onClick="this.form.action='';this.form.target='';this.form.virgo_show_page.value='<?php echo ($showPage + 1) ?>'; this.form.portlet_action.value='ChangePaging'; <?php echo JSFS() ?>" <?php echo ($showPage == $pageCount ? 'disabled="disabled"' : '') ?><?php echo ($showPage > 1 ? ' onmousedown="this.className=\'button_paging_pressed\'" onmouseup="this.className=\'button_paging\'" onmouseout="this.className=\'button_paging\'"' : '') ?>>
									<input class="button_paging<?php echo ($showPage == $pageCount ? '_disabled' : '') ?>" src="<?php echo $_SESSION['portal_url'] ?>/media/icons/go-last.png" type="image" value="&#x21E5" onClick="this.form.action='';this.form.target='';this.form.virgo_show_page.value='<?php echo ($pageCount) ?>'; this.form.portlet_action.value='ChangePaging'; <?php echo JSFS() ?>" <?php echo ($showPage == $pageCount ? 'disabled="disabled"' : '') ?><?php echo ($showPage > 1 ? ' onmousedown="this.className=\'button_paging_pressed\'" onmouseup="this.className=\'button_paging\'" onmouseout="this.className=\'button_paging\'"' : '') ?>>
								</td>
								<td width="33%" nowrap="nowrap" align="right" class="show_rows">#text("'SHOW_ROWS'") (<?php echo $resultCount ?>):&nbsp;#showRowsSelect()
								</td>
							</tr>
						</table>
				</td>
			</tr>
<?php
		}
?>
## nie tylko dla slownikow! #if ($entity.dictionary)
<?php
	}
PROFILE('table_06_2');
PROFILE('table_07');
?>
## #end
<?php
			} else {
?>
				<tr>
					<td colspan="99">
						<div class="message"><?php echo ($filterApplied ? #textPHP("NO_RESULTS") : #textPHP("NO_DATA_TO_SHOW")) ?></div>
					</td>
				</tr>
<?php
			}
?>
		</table>
<?php echo P('master_mode', "0") == "1" ? "</div>" : "" ?>		
#onClickContextChange()
#parseFile("modules_project/afterTable/${tr.f_v($entity.name)}.php")
<?php
PROFILE('table_07');
PROFILE('table_08');
?>
#if ($entity.dictionary)
<?php
	if (P('master_mode', "0") != "1") {
?>
#end		
#parseFileWithStandard("modules_project/insteadTableActions/${tr.f_v($entity.name)}.php" "defs/entity/table_actions.php")
#if ($entity.dictionary)
<?php
	}
?>
#end		
<?php
PROFILE('table_08');
?>
