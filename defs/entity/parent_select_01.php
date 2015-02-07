<?php
## $limit_... powinien pozwolic na zawezenie listy wyboru. Czyli powinien dostarczac to, co 
## pojawi sie w SQL-u po WHERE abc_id IN ('')
//		$limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} = $componentParams->get('limit_to_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}');
#parseFile("modules_project/customListLimit/${tr.f_v($relationToRender.childEntity.name)}/${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}.php")
		$tmpId = null;
#if ($relationToRender.childEntity.name != $relationToRender.parentEntity.name)		
#getParentsInContext()
		if (isset($parentsInContext["virgo${tr.FV($relationToRender.parentEntity.name)}"])) {
			$tmpId = $parentsInContext["virgo${tr.FV($relationToRender.parentEntity.name)}"];
		}
#end		
		$readOnly = "";
		if (isset($tmpId) || P('show_${type}_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}', "1") == "2") {
			if (!is_null($tmpId)) {
#if ($relationToRender.name)		
## ???				if (isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id)) {
## ???					if ($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id == 0) {
						$result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_${tr.f_v($relationToRender.name)}_id = $tmpId;
## ???					}
## ???				}
#else
## ???				if (isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id)) {
## ???					if ($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id == 0) {
						$result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id = $tmpId;
## ???					}
## ???				}
#end
			}
#if ($relationToRender.name)		
			if (isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_${tr.f_v($relationToRender.name)}_id)) {
				$parentId = $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_${tr.f_v($relationToRender.name)}_id;
			}
#else
			if (isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id)) {
				$parentId = $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id;
			}
#end
			$parent${tr.FV($relationToRender.parentEntity.name)} = new virgo${tr.FV($relationToRender.parentEntity.name)}();
			$parentValue = $parent${tr.FV($relationToRender.parentEntity.name)}->lookup($parentId);
?>
						<input type="hidden" id="${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" name="${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" value="<?php echo $parentId ?>">
						<input class="inputbox readonly" id="${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}Dummy" name="${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}Dummy" size="30" value="<?php echo isset($parentValue) ? $parentValue : '' ?>" readonly="readonly">
<?php
		} else {
?>
## szukamy czy aktualna encja nie jest przypadkiem naszym wnukiem
## a jesli jest, to dla encji posredniej (jesli to nie slownik!) zrob funkcje w Javascript
## ktora znajdzie i zsynchronizuje jej liste z nasza
#foreach ($parentRelation2 in $relationToRender.childEntity.childRelations)
#if ($relationToRender.parentEntity.name != $parentRelation2.parentEntity.name)
#foreach ($childRelation2 in $relationToRender.parentEntity.parentRelations)
#if ($parentRelation2.parentEntity.name == $childRelation2.childEntity.name && $parentRelation2.parentEntity.name != $relationToRender.childEntity.name && $childRelation2.childEntity.name != $relationToRender.parentEntity.name)
#if (!$parentRelation2.parentEntity.dictionary)
<script type="text/javascript">
var array${tr.FV($relationToRender.parentEntity.name)}2${tr.FV($childRelation2.childEntity.name)} = new Array();

<?php
##	$query = " SELECT DISTINCT adr_kln_id AS parent_id FROM gsh_adresy "; // plus ograniczenia skonfigurowane
	$query = " SELECT DISTINCT ${tr.f_v($childRelation2.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}#if($childRelation2.name)_${tr.f_v($childRelation2.name)}#end${E}_id AS parent_id FROM ${tr.f_v($application.prefix)}_${tr.f_v($childRelation2.childEntity.namePlural)} "; // plus ograniczenia skonfigurowane
#selectRows()
	$rowsParent = $rows;
	foreach ($rowsParent as $rowParent) {
		if (!is_null($rowParent['parent_id'])) {
?>
	var array${tr.FV($relationToRender.parentEntity.name)}<?php echo $rowParent['parent_id'] ?>= new Array();	
<?php
##		$query = " SELECT adr_id AS id, adr_virgo_title AS value FROM gsh_adresy WHERE adr_kln_id = " . $rowParent['parent_id']; // plus ograniczenia skonfigurowane
			$query = " SELECT ${tr.f_v($childRelation2.childEntity.prefix)}_id AS id, ${tr.f_v($childRelation2.childEntity.prefix)}_virgo_title AS value FROM ${tr.f_v($application.prefix)}_${tr.f_v($childRelation2.childEntity.namePlural)} WHERE ${tr.f_v($childRelation2.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}#if($childRelation2.name)_${tr.f_v($childRelation2.name)}#end${E}_id = " . $rowParent['parent_id']; // plus ograniczenia skonfigurowane
#selectRows()
			foreach ($rows as $row) {
?>
	array${tr.FV($relationToRender.parentEntity.name)}<?php echo $rowParent['parent_id'] ?>['<?php echo $row["id"] ?>'] = '<?php echo $row["value"] ?>';	
<?php
			}
?>
	array${tr.FV($relationToRender.parentEntity.name)}2${tr.FV($childRelation2.childEntity.name)}[<?php echo $rowParent['parent_id'] ?>] = array${tr.FV($relationToRender.parentEntity.name)}<?php echo $rowParent['parent_id'] ?>;
		
<?php
		}
	}
?>

function addOption${tr.FV($childRelation2.childEntity.name)}(select, optionText, optionValue, selectedValue) {
	var newOption = document.createElement('option');
	newOption.text = optionText;
	newOption.value = optionValue;
	if (optionValue == selectedValue) {
		newOption.selected = true;
	}
	try {
		select.add(newOption, null);
	} catch (ex) {
		select.add(newOption);
	}
}

function displayArray${tr.FV($childRelation2.childEntity.name)}(select, arrayToShow, selectedValue) {
	select.length = 0;
	addOption${tr.FV($childRelation2.childEntity.name)}(select, '', '', selectedValue);
	for (var id in arrayToShow){ 
		addOption${tr.FV($childRelation2.childEntity.name)}(select, arrayToShow[id], id, selectedValue);
	} 
}

function update${tr.FV($childRelation2.childEntity.name)}(parentId, childrenListId, selectedValue) {
	// alert('test: ' + parentId + ' '  + childrenListId);
	var childrenList = document.getElementById(childrenListId);
	if (childrenList) {
		var arrayToShow = array${tr.FV($relationToRender.parentEntity.name)}2${tr.FV($childRelation2.childEntity.name)}[parentId];
		displayArray${tr.FV($childRelation2.childEntity.name)}(childrenList, arrayToShow, selectedValue);
	}
}
</script>
#end
#end
#end
#end
#end
<?php
	$hint = T('HINT_${tr.F_V($relationToRender.childEntity.name)}_${tr.F_V($relationToRender.name)}${tr.F_V($relationToRender.parentEntity.name)}');
?>
################################ select a nie AJAX ################################
<?php
	$limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} = null;
	$parent${tr.FV($relationToRender.parentEntity.name)} = new virgo${tr.FV($relationToRender.parentEntity.name)}();
	$whereList = "";
	if (!is_null($limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}) && trim($limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}) != "") {
		$whereList = $whereList . " ${tr.f_v($relationToRender.parentEntity.prefix)}_id ";
		if (trim($limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}) == "page_title") {
			$limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} = "SELECT ${tr.f_v($relationToRender.parentEntity.prefix)}_id FROM ${tr.f_v($application.prefix)}_${tr.f_v($relationToRender.parentEntity.namePlural)} WHERE ${tr.f_v($relationToRender.parentEntity.prefix)}_" . $limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} . " = '" . $mainframe->getPageTitle() . "'";
		}
		eval("\$limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} = \"$limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}\";");
		$whereList = $whereList . " IN (" . $limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} . ") ";
	}						
	$parentCount = $parent${tr.FV($relationToRender.parentEntity.name)}->getVirgoListSize($whereList);

	if (P('show_${type}_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}', "1") == "1" && $parentCount <= 100) {
?>
    						<select 
							class="inputbox #if ($relationToRender.obligatory) obligatory #end" 
							id="${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id) ? $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id : '' ?>" 
							name="${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id) ? $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id : '' ?>" 
<?php
	if (isset($hint)) {
?>
							title="<?php echo $hint ?>"
<?php
	}
?>
							onchange="this.form.virgo_changed.value='T';
#foreach ($parentRelation2 in $relationToRender.childEntity.childRelations)
#if ($relationToRender.parentEntity.name != $parentRelation2.parentEntity.name)
#foreach ($childRelation2 in $relationToRender.parentEntity.parentRelations)
#if ($parentRelation2.parentEntity.name == $childRelation2.childEntity.name && $parentRelation2.parentEntity.name != $relationToRender.childEntity.name && $childRelation2.childEntity.name != $relationToRender.parentEntity.name)
#if (!$parentRelation2.parentEntity.dictionary)
## dodany if, bo probowal wolac update nawet jak nie bylo docelowej listy na formularzu
<?php
	if (P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "1") {
?>
								update${tr.FV($childRelation2.childEntity.name)}(this.options[this.selectedIndex].value, '${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($childRelation2.childEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>', <?php echo !isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($parentRelation2.parentEntity.prefix)}_id) ? "null" : $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($parentRelation2.parentEntity.prefix)}_id ?>);
<?php
	}
?>
#end
#end
#end
#end
#end
							"
<?php
	if (isset($tabIndex) && $tabIndex > 0) {
?>
							tabindex="<?php echo $tabIndex ?>"
<?php
	}
?>							
						>
<?php
			if (is_null($limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}) || trim($limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}) == "") {
?>
    							<option value=""></option>
<?php
			}
?>
<?php
			$results${tr.FV($relationToRender.parentEntity.name)} = $parent${tr.FV($relationToRender.parentEntity.name)}->getVirgoList($whereList);
			while(list($id, $label)=each($results${tr.FV($relationToRender.parentEntity.name)})) {
?>	
							<option value="<?php echo $id ?>" 
<?php 
#if ($relationToRender.name)					
				echo (isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_${tr.f_v($relationToRender.name)}_id) && $id == $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_${tr.f_v($relationToRender.name)}_id ? "selected='selected'" : "");
#else
				echo (isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id) && $id == $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id ? "selected='selected'" : "");
#end
?>
							>
								<?php echo $label ?>
							</option>
<?php
			} 
?>
    						</select>
################################ AJAX a nie select ################################
<?php
#if (!$firstFieldName || $firstFieldName == "")
#set ($firstFieldName = "${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.name)}_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>")
#end
			} elseif (P('show_${type}_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}', "1") == "3" || $parentCount > 100) {
#if ($relationToRender.name)		
				$parentId = $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_${tr.f_v($relationToRender.name)}_id;
#else
				$parentId = $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id;
#end
				$parent${tr.FV($relationToRender.parentEntity.name)} = new virgo${tr.FV($relationToRender.parentEntity.name)}();
				$parentValue = $parent${tr.FV($relationToRender.parentEntity.name)}->lookup($parentId);
?>
<?php
	if ($parentAjaxRendered == "0") {
		$parentAjaxRendered = "1";
?>
		<script type="text/javascript">
function getXMLHttpRequest() {
	var ua = navigator.userAgent.toLowerCase();
	if (!window.ActiveXObject) {
		return new XMLHttpRequest();
	} else if (ua.indexOf('msie 5') == -1) {
		return new ActiveXObject("Msxml2.XMLHTTP");
	} else {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
}		
		
var xmlhttpArray = new Array();
function getAjaxToTargetParent(url, targetId, inputField) {
	xmlhttpArray[targetId] = getXMLHttpRequest();
	xmlhttpArray[targetId].onreadystatechange=function() {
		if (xmlhttpArray[targetId].readyState==4 && xmlhttpArray[targetId].status==200) {
			var ret = xmlhttpArray[targetId].responseText;
			ret = ret.replace(/^\s+|\s+$/g,"");
			document.getElementById(targetId).innerHTML = ret;
			document.getElementById(targetId).style.display = ret.length > 0 ? 'block' : 'none';
			inputField.style.backgroundImage='none';
		}
	}
	xmlhttpArray[targetId].open("GET", url + "&timestamp=" + new Date().getTime(), true);
	xmlhttpArray[targetId].send();
}
function selectedLabel(entity, id) {
	document.getElementById('${tr.f_v($entity.prefix)}_' + entity + '_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>').value=id;
	var dropdownItem = document.getElementById('${tr.f_v($entity.prefix)}_' + entity + '_dropdown_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>');
	dropdownItem.value=document.getElementById(entity + '_title_' + id).innerHTML;
	dropdownItem.className='inputbox dropdown locked';
##	document.getElementById('virgo_form_${tr.f_v($entity.name)}').virgo_changed.value='T';
	document.getElementById('lista_' + entity).style.display='none';
#foreach ($parentRelation2 in $relationToRender.childEntity.childRelations)
#if ($relationToRender.parentEntity.name != $parentRelation2.parentEntity.name)
#foreach ($childRelation2 in $relationToRender.parentEntity.parentRelations)
#if ($parentRelation2.parentEntity.name == $childRelation2.childEntity.name && $parentRelation2.parentEntity.name != $relationToRender.childEntity.name && $childRelation2.childEntity.name != $relationToRender.parentEntity.name)
#if (!$parentRelation2.parentEntity.dictionary)
## dodany if, bo probowal wolac update nawet jak nie bylo docelowej listy na formularzu
<?php
	if (P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "1") {
?>
								update${tr.FV($childRelation2.childEntity.name)}(id, '${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($childRelation2.childEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>', <?php echo !isset($result${tr.FV($relationToRender.childEntity.name)}['${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($parentRelation2.parentEntity.prefix)}_id']) ? "null" : $result${tr.FV($relationToRender.childEntity.name)}['${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($parentRelation2.parentEntity.prefix)}_id'] ?>);
<?php
	}
?>
#end
#end
#end
#end
#end
}
function showDropdown(entity, className, inputField) {
	document.getElementById('${tr.f_v($entity.prefix)}_' + entity + '_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>').value='';
	document.getElementById('${tr.f_v($entity.prefix)}_' + entity + '_dropdown_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>').className='inputbox dropdown';
	inputField.style.backgroundImage='url(/media/icons/executing.gif)';
	getAjaxToTargetParent('?virgo_matching_labels_entity=' + className + '&virgo_field_name=' + entity + '&virgo_matching_labels_namespace=${tr.f_v($application.name)}&virgo_match=' + inputField.value + '&token=1', 'lista_' + entity, inputField);
}
		</script>
<style type="text/css">
div.dropdownHolder {
  position: relative;
  display: inline;
}
div.dropdownHolder ul.dropdown {
  width: 100%;
  position: absolute;
  border: 1px solid #999;
  display: none;
## top zalezy od wielkosci fontu albo linii  
  top: 16px; 
  right: 0px;
  padding: 0px;
  z-index: 999;
  border-radius: 0px;
  cursor: pointer;
  margin: 0px;
}
div.dropdownHolder ul.dropdown li {
  display: block !important;
  margin: 0px !important;
  background-color: #FFF;
  border: none !important;
}
div.dropdownHolder ul.dropdown li:hover {
  background-color: #CCC;
  border: none !important;
}
div.dropdownHolder input.dropdown  {
## /*  background-image: url('/components/com_${tr.f_v($application.prefix)}_${tr.f_v($entity.name)}/icons/executing.gif'); */
  background-repeat: no-repeat;
  background-position: right;
  width: 300px;
}
div.dropdownHolder input.locked  {
  font-weight: bold;
  background-color: #DDD;
}
</style>
<?php
	}
?>
<div class="dropdownHolder">
	<input type="hidden" id="${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.name)}_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>" name="${tr.f_v($entity.prefix)}_${tr.fV($relation.parentEntity.name)}_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>" value="<?php echo $parentId ?>"/> 
	<input 
		class="inputbox dropdown <?php echo !is_null($parentId) && trim($parentId) != '' && $parentId != '0' ? 'locked' : '' ?> #if ($relationToRender.obligatory) obligatory #end" 
		id="${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.name)}_dropdown_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>" 
		onkeyup="showDropdown('${tr.f_v($relation.parentEntity.name)}', '${tr.FV($relation.parentEntity.name)}', this)" 
		autocomplete="off" 
		value="<?php echo $parentValue ?>" 
		onclick="document.getElementById('lista_${tr.f_v($relation.parentEntity.name)}').style.display='none';"
<?php
	if (isset($hint)) {
?>
							title="<?php echo $hint ?>"
<?php
	}
?>
	/>
	<ul class="dropdown" id='lista_${tr.f_v($relation.parentEntity.name)}'>
	</ul>
</div>
<?php			
#if (!$firstFieldName || $firstFieldName == "")
#set ($firstFieldName = "${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.name)}_dropdown_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>")
#end
			}
?>
################################ AJAX/select koniec ################################
<?php
		} 
?>
<?php
	if (isset($hint)) {
?>						
<script type="text/javascript">
#set ($hash = '#')
$('${hash}${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.name)}_dropdown_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>').qtip({position: {
		my: 'left center',
		at: 'right center'
	}});
</script>						
<?php
	}
?>						

