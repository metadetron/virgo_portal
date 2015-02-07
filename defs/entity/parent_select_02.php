<?php
## $limit_... powinien pozwolic na zawezenie listy wyboru. Czyli powinien dostarczac to, co 
## pojawi sie w SQL-u po WHERE abc_id IN ('')
//		$limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} = $componentParams->get('limit_to_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}');
		$limit_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)} = null;
#parseFile("modules_project/customListLimit/${tr.f_v($relationToRender.childEntity.name)}/${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}.php")
		$tmpId = null;
## UWAGA: Tylko dla relacji BEZ nazwy! Bo jak jest kilka relacji do tej samej encji to sie burzylo - ma wybrac tylko ta bez nazwy		
#if ($relationToRender.childEntity.name != $relationToRender.parentEntity.name && !$relationToRender.name)		
#getParentsInContext()
		if (isset($parentsInContext["${tr.f_v($application.name)}\\virgo${tr.FV($relationToRender.parentEntity.name)}"])) {
			$tmpId = $parentsInContext["${tr.f_v($application.name)}\\virgo${tr.FV($relationToRender.parentEntity.name)}"];
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
#else
			if (isset($result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id)) {
				$parentId = $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id;
#end
				$parent${tr.FV($relationToRender.parentEntity.name)} = new ${tr.f_v($application.name)}\virgo${tr.FV($relationToRender.parentEntity.name)}();
				$parentValue = $parent${tr.FV($relationToRender.parentEntity.name)}->lookup($parentId);
			} else {
				$parentId = null;
				$parentValue = null;
			}
?>
						<input type="hidden" id="${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" name="${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" value="<?php echo $parentId ?>">
						<span class="inputbox readonly"><?php echo isset($parentValue) ? $parentValue : '' ?></span>
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
	if (childrenList.tagName.toUpperCase() == "SELECT") {
		if (childrenList) {
			var arrayToShow = array${tr.FV($relationToRender.parentEntity.name)}2${tr.FV($childRelation2.childEntity.name)}[parentId];
			displayArray${tr.FV($childRelation2.childEntity.name)}(childrenList, arrayToShow, selectedValue);
		}
	}
}
</script>
#end
#end
#end
#end
#end
<?php
	$hint = TE('HINT_${tr.F_V($relationToRender.childEntity.name)}_${tr.F_V($relationToRender.name)}${tr.F_V($relationToRender.parentEntity.name)}');
?>
################################ select a nie AJAX ################################
<?php
	$parent${tr.FV($relationToRender.parentEntity.name)} = new ${tr.f_v($application.name)}\virgo${tr.FV($relationToRender.parentEntity.name)}();
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
	$showAjax${relationToRender.childEntity.prefix} = P('show_${type}_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}', "1") == "3" || $parentCount > 100;
	if (!$showAjax${relationToRender.childEntity.prefix}) {
?>
    						<select 
							class="inputbox #if ($relationToRender.obligatory) obligatory #else <?php echo P('show_${type}_${tr.f_v($relationToRender.parentEntity.name)}${tr.f_v($relationToRender.name)}_obligatory') == "1" ? " obligatory " : "" ?> #end" 
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
			} else {
#if ($relationToRender.name)		
				$parentId = $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_${tr.f_v($relationToRender.name)}_id;
#else
				$parentId = $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.prefix)}_id;
#end
				$parent${tr.FV($relationToRender.parentEntity.name)} = new ${tr.f_v($application.name)}\virgo${tr.FV($relationToRender.parentEntity.name)}();
				$parentValue = $parent${tr.FV($relationToRender.parentEntity.name)}->lookup($parentId);
?>
<?php
	if ($parentAjaxRendered == "0") {
		$parentAjaxRendered = "1";
?>
<style type="text/css">
input.locked  {
  font-weight: bold;
  background-color: #DDD;
}
.ui-autocomplete-loading {
    background: white url('/media/icons/executing.gif') right center no-repeat;
}
</style>
<?php
	}
?>
	<input type="hidden" id="${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" name="${tr.f_v($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" value="<?php echo $parentId ?>"/> 
	<input 
		type="text"
		class="inputbox dropdown <?php echo !is_null($parentId) && trim($parentId) != '' && $parentId != '0' ? 'locked' : '' ?> #if ($relationToRender.obligatory) obligatory #end" 
		id="${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_dropdown_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" 
		autocomplete="off" 
		value="<?php echo $parentValue ?>" 
<?php
	if (isset($hint)) {
?>
							title="<?php echo $hint ?>"
<?php
	}
?>
<?php
	if (isset($tabIndex) && $tabIndex > 0) {
?>
							tabindex="<?php echo $tabIndex ?>"
<?php
	}
?>							
	/>
#set ($hash = '#')
#set ($dollar = '$')
<script type="text/javascript">
$(function() {
        $( "${hash}${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_dropdown_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>" ).autocomplete({
            delay: 0,
            source: function( request, response ) {
                ${dollar}.ajax({
                    url: "?",
                    dataType: "json",
                    data: {
			virgo_matching_labels_entity: "${tr.FV($relationToRender.parentEntity.name)}",
			virgo_field_name: "${tr.f_v($relationToRender.parentEntity.name)}",
			virgo_matching_labels_namespace: "${tr.f_v($application.name)}",
			virgo_match: request.term,
			<?php echo getTokenName(virgoUser::getUserId()) ?>: "<?php echo getTokenValue(virgoUser::getUserId()) ?>"
                    },
                    success: function( data ) {
                        response( ${dollar}.map( data, function( item ) {
                            return {
                                label: item.title,
                                value: item.id
                            }
                        }));
                    }
                });
            },
            minLength: 0,
            select: function( event, ui ) {
				if (ui.item.value != '') {
					$('${hash}${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').val(ui.item.value);
				  	$('${hash}${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_dropdown_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').val(ui.item.label);
				  	$('${hash}${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_dropdown_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').addClass("locked");
				}
				event.stopImmediatePropagation();
				event.preventDefault();
            },
            focus: function( event, ui ) {
				if (ui.item.value != '') {
					$('${hash}${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_dropdown_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').val(ui.item.label);
				}
				event.stopImmediatePropagation();
				event.preventDefault();
            },
            open: function() {
				$('${hash}${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').val('');
				$('${hash}${tr.f_v($relationToRender.childEntity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_dropdown_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').removeClass("locked");		
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    });
</script>
<?php			
#if (!$firstFieldName || $firstFieldName == "")
#set ($firstFieldName = "${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.name)}${tr.FV($relationToRender.name)}_dropdown_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>")
#end
			}
?>
################################ AJAX/select koniec ################################
## dodanie nowego parenta
## tylko dla wymaganych relacji i dodajemy go minimalnie, czyli tylko wymagane pola
#if (!$relationToRender.parentEntity.dictionary)
#if ($relationToRender.obligatory)
#set ($backupRelation = $relation)
#set ($backupRelationToRender = $relationToRender)
<?php
	$result${tr.FV($relationToRender.parentEntity.name)} = new ${tr.f_v($application.name)}\virgo${tr.FV($relationToRender.parentEntity.name)}(); 
	$result${tr.FV($relationToRender.parentEntity.name)}->loadFromRequest();
?>
<fieldset
	id="create_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>"
	class="create_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}"
##	style="display: <?php echo R('create_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_' . $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id) == '1' ? 'inline-block' : 'none' ?>; margin: 0px; <?php echo $floatingFields ? " float: left;" : '' ?>"
	style="display: none; margin: 0px; <?php echo $floatingFields ? " float: left;" : '' ?>"
>
<input 
	type="hidden" 
	id="check_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>"
	name="create_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>"
##	value="<?php echo R('create_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_' . $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id) == '1' ? '1' : '0' ?>"
	value="0"
> 	
##	<ul>
<?php
	if ($formsInTable) {
?>
	<table>
<?php
	}
?>
#foreach( $relation in $relationToRender.parentEntity.childRelations )
## tylko slowniki, zeby nie bylo rekurencji
#if ($relation.parentEntity.dictionary)
#if ($relation.obligatory || $showFullCreateParentForm == "TRUE")
#renderParentSelect("create")
#end
#end
#end
#set ($relationToRender = $backupRelationToRender)
#foreach( $property in $relationToRender.parentEntity.properties )
## 
#if ($property.obligatory || $showFullCreateParentForm == "TRUE")
		<li <?php echo $floatingFields ? "style='display: inline-block; float: left;'" : '' ?>>
					<label 
						nowrap="nowrap"
						class="fieldlabel #if ($property.obligatory) obligatory #else <?php echo P('show_form_${tr.f_v($property.name)}_obligatory', "0") == "1" ? " obligatory " : "" ?> #end  ${tr.f_v($property.name)} ${tr.f_v($property.dataType.name)}" 
						for="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->${tr.f_v($property.entity.prefix)}_id ?>"
					>#renderLabel($property false "create")</label>
#renderField($property false "create")
		</li>
#end
#end
## #foreach( $relation in $relationToRender.parentEntity.childRelations )
## #if (!$relation.parentEntity.dictionary)
## #renderParentSelect("create")
## #end
## #end
<?php
	if ($formsInTable) {
?>
	</table>
<?php
	}
?>
##	</ul>
</fieldset>
<li <?php echo $floatingFields ? "style='display: inline-block; float: left;'" : '' ?>>
<label>&nbsp;</label>
<input 
	type="button" 
	value="#text("'Wprowadź nowy'") #text("'${tr.fV($relationToRender.parentEntity.name)}'")" 
	class="button btn"
	onclick="
			$('#create_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').toggle();
			$('label[for=\'${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>\']').toggle();
#set ($hash = '#')
<?php
	if ($showAjax${relationToRender.childEntity.prefix}) {
		$dropdown = "dropdown_";
	} else {
		$dropdown = "";
	}
?> 
			$('${hash}${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $dropdown ?><?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').toggle();
			$('#check_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').val($('#create_${tr.fV($relationToRender.childEntity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_<?php echo $result${tr.FV($relationToRender.childEntity.name)}->${tr.f_v($relationToRender.childEntity.prefix)}_id ?>').css('display') == 'none' ? 0 : 1);
		"
>
</li>
#set ($relation = $backupRelation)
#end
#end
<?php
		} 
?>
<?php
	if (isset($hint)) {
?>						
<script type="text/javascript">
#set ($hash = '#')
$('${hash}${tr.f_v($entity.prefix)}_${tr.f_v($relationToRender.parentEntity.name)}_dropdown_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>').qtip({position: {
		my: 'left center',
		at: 'right center'
	}});
</script>						
<?php
	}
?>						

