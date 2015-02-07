#macro (paramSelectRow)
	$result = $databaseHandler->queryPrepared($query, false, $types, $values);
	if ($result !== false) {
		$res = null;
		foreach ($result as $row) {
			$res = $row['ppr_value'];
		}
	} else {
		$res = null;
	}
#end
#macro (paramGetValues $name)
			$types = "s";
			$values = array('$name');
			$query = "SELECT ppr_value FROM prt_portlet_parameters WHERE ppr_pdf_id = {$pdfId} AND ppr_name = ? ";
#paramSelectRow()
			$pdf_$name = $res;
			$query = "SELECT ppr_value FROM prt_portlet_parameters WHERE ppr_pob_id = {$pobId} AND ppr_name = ? ";
#paramSelectRow()
			$pob_$name = $res;
#if ((($property && !$property.obligatory) || ($relation && $relation.childEntity.name == $entity.name && !$relation.obligatory)) && (($name.length() > 12 && $name.substring(0, 12) == "show_create_") || ($name.length() > 10 && $name.substring(0, 10) == "show_form_")))
			$values = array("${name}_obligatory");
			$query = "SELECT ppr_value FROM prt_portlet_parameters WHERE ppr_pob_id = {$pobId} AND ppr_name = ? ";
#paramSelectRow()
			$pob_${name}_obligatory = $res;
#end
#end
#macro (paramStoreValues $name $type)
			$postValue = $_POST['${type}_$name'];
			if (isset($postValue) && is_array($postValue)) {
				$postValue = implode(',', $postValue);
			}
			if (isset($_POST['pobId']) && $${type}_$name != $postValue) {
				if (!isset($postValue) || $postValue == "") {
					$types = "s";
					$values = array('$name');
					$query = "DELETE FROM prt_portlet_parameters WHERE ppr_${type}_id = {$${type}Id} AND ppr_name = ? ";
				} else {
					$types = "ss";
					if (isset($${type}_$name)) {
						$values = array($postValue, '$name');
						$query = "UPDATE prt_portlet_parameters SET ppr_value = ? WHERE ppr_${type}_id = {$${type}Id} AND ppr_name = ? ";
					} else {
						$values = array($name, $postValue);
						$query = "INSERT INTO prt_portlet_parameters (ppr_name, ppr_value, ppr_${type}_id) VALUES (?, ?, {$${type}Id})";
					}
				}
				$databaseHandler->queryPrepared($query, false, $types, $values);
##				$${type}_$name = $postValue; bo slashe w multi sie wykrzaczaly, a skoro nie ma tu walidacji to zawsze wartosci z POST sa takie same jak z bazy, czyz nie?
## zamiast tego po prostu wczytajmy jeszcze raz z bazy danych:
## to trzeba zmienic w przyszlosci i przetestowac, bo w sumie to paramGetValues wywoluje sie za duzo razy
#paramGetValues($name)
			}
#end
#macro (spacer $name)
<tr class="spacer"><td colspan="4" id="${tr.f_v($name)}">$name</td></tr>
#end
#macro (paramTextOne $name)
<input type="text" name="$name" value="<?php echo $${name} ?>"/>
#end
#macro (paramMultilineOne $name)
<textarea name="$name"><?php echo $${name} ?></textarea>
#end
#macro (paramText $name)
<?php
#paramGetValues($name)
#paramStoreValues($name 'pdf')
#if ($name != "ajax_max_label_list_size")
#paramStoreValues($name 'pob')
#end
?>
		<tr>
			<td>$name</td>
			<td>
#paramTextOne("pdf_$name")			
			</td>
			<td>
#if ($name != "ajax_max_label_list_size")
#paramTextOne("pob_$name")	
#end
			</td>
			<td>
				<input type="submit" value="Store"/>
			</td>			
		</tr>
#end
#macro (paramMultiline $name $hint)
<?php
#paramGetValues($name)
#paramStoreValues($name 'pdf')
#if ($name != "title_value" && $name != "extra_ajax_filter")
#paramStoreValues($name 'pob')
#end
?>
		<tr>
			<td title="$hint">$name</td>
			<td>
#paramMultilineOne("pdf_$name")			
			</td>
			<td>
#if ($name != "title_value" && $name != "extra_ajax_filter")
#paramMultilineOne("pob_$name")			
#end
			</td>
			<td>
				<input type="submit" value="Store"/>
			</td>			
		</tr>
#end
#macro (paramList1 $name $multiple)
#if ($multiple == "")
#set($nawiasy = "") 
#else
#set($nawiasy = "[]") 
#end
				<div style="max-width: 200px; overflow: auto;">
#if ((($property && !$property.obligatory) || ($relation && $relation.childEntity.name == $entity.name && !$relation.obligatory)) && (($name.length() > 16 && $name.substring(0, 16) == "pob_show_create_") || ($name.length() > 14 && $name.substring(0, 14) == "pob_show_form_")))
					Obl:<input type="checkbox" name="${name}_obligatory" value="1" <?php echo $${name}_obligatory == "1" ? " checked='checked' " : "" ?>/>
#end
<?php
	if (count($options) <= 100) {
?>				
					<select name="${name}${nawiasy}" $multiple>
#if ($property.obligatory)
#set ($emp = "!!!OBLIGATORY!!!")
#else
#set ($emp = "")
#end			
						<option value="" <?php echo !isset($${name}) ? "selected " : "" ?>>${emp}</option>
<?php
			foreach ($options as $key => $val) {
?>				
						<option value="<?php echo $key ?>" <?php echo isset($${name}) && strrpos(",".$${name}.",", ",".$key.",") !== false ? "selected " : "" ?>><?php echo $val ?></option>
<?php
			}
?>				
					</select>
<?php
	} else {
		/* podawanie z palca. na razie bez multi */
?>
	Za duzo rekordów, podaj ID: <input type="text" name="${name}" value="<?php echo $${name} ?>">
<?php
	}
?>				
				</div>
#end
#macro (paramList $name $multiple $hint)
<?php
#paramGetValues($name)
#paramStoreValues($name 'pdf')
#paramStoreValues($name 'pob')
#if ((($property && !$property.obligatory) || ($relation && $relation.childEntity.name == $entity.name && !$relation.obligatory)) && (($name.length() > 12 && $name.substring(0, 12) == "show_create_") || ($name.length() > 10 && $name.substring(0, 10) == "show_form_")))
#paramStoreValues("${name}_obligatory" 'pob')
#end
?>
		<tr>
			<td title="$hint">
#if ($property.obligatory)
				<strong>
#end
					$name
#if ($property.obligatory)
				</strong>
#end
			</td>
			<td>
#paramList1("pdf_$name" $multiple)
			</td>
			<td>
#paramList1("pob_$name" $multiple)
			</td>
			<td>
				<input type="submit" value="Store"/>
			</td>			
		</tr>
#end
#macro (paramBool $name $hint)
<?php
			$options = array();
			$options["0"] = "No";
			$options["1"] = "Yes";
?>
#paramList($name "" $hint)
#end
## #macro (setPermissions $action)
## var e = document.getElementById('${action}_<?php echo $id ?>_<?php echo $pobId ?>_' + this.value); e.checked = true;
## #end
## #macro (actionPermissions $action)
## <?php
## 				if (isset($_POST['pobId'])) {
## 					zapiszPozwolenieRole($pobId, '$action', $id, $name);
## 				}
## ?>
## 				<td class="action">
## 					<div class="action">$action</div>
## 					<table class="action">
## 						<tr>
## 							<td class="<?php echo perm($id, $pobId, '$action', NULL) ? 'neutral' : '' ?>"><input type="radio" name="${action}_<?php echo $id ?>_<?php echo $pobId ?>" id="${action}_<?php echo $id ?>_<?php echo $pobId ?>_" value="NULL" <?php echo perm($id, $pobId, '$action', NULL) ? " checked='checked' " : "" ?>/></td>
## 							<td class="<?php echo perm($id, $pobId, '$action', 1) ? 'allowed' : '' ?>"><input type="radio" name="${action}_<?php echo $id ?>_<?php echo $pobId ?>" id="${action}_<?php echo $id ?>_<?php echo $pobId ?>_1" value="1" <?php echo perm($id, $pobId, '$action', 1) ? " checked='checked' " : "" ?>/></td>
## 							<td class="<?php echo perm($id, $pobId, '$action', 0) ? 'blocked' : '' ?>"><input type="radio" name="${action}_<?php echo $id ?>_<?php echo $pobId ?>" id="${action}_<?php echo $id ?>_<?php echo $pobId ?>_0" value="0" <?php echo perm($id, $pobId, '$action', 0) ? " checked='checked' " : "" ?>/></td>
## 							<td width="100%"></td>				
## 						</tr>
## 					</table>
## 				</td>
## #end
#macro (getHintFromDB $token)
	$hintId = null;
	$hintText = null;
	$query = " SELECT trn_id, trn_text FROM prt_translations WHERE trn_token = ? AND trn_lng_id = " . $lngId;
	$result = $databaseHandler->queryPrepared($query, false, "s", array('$token'));
	foreach ($result as $res) {
		$hintId = $row[trn_id];
		$hintText = $row[trn_text];
	}
#end
#macro (hint $token)
<?php 
	if (isset($_POST['$token'])) {
		$newHint = $_POST['$token'];
		$newHint = trim($newHint);
		if ($newHint == "") {
			$newHint = null;
		}
		$query = "";
		if (isset($newHint)) {
#getHintFromDB($token)		
			if (isset($hintId)) {
				if ($newHint != $hintText) {
					$query = " UPDATE prt_translations SET trn_text = '" . $newHint . "' WHERE trn_id = " . $hintId;
				}
			} else {
					$query = " INSERT INTO prt_translations (trn_lng_id, trn_token, trn_text) VALUES (" . $lngId . ", '$token', '" . $newHint . "')";
			}
		} else {
#getHintFromDB($token)		
			if (isset($hintId)) {
				$query = " DELETE FROM  prt_translations WHERE trn_id = " . $hintId;			
			}		
		}
		if ($query != "") {
			$databaseHandler->query($query);
		}
	}
	$query = " SELECT trn_text FROM prt_translations WHERE trn_token = ? AND trn_lng_id = " . $lngId;
	$result = $databaseHandler->queryPrepared($query, false, "s", array('$token'));
	$text = null;
	foreach ($result as $row) {
		$text = $row[trn_text];
	}
?>
	<tr>
		<td>$token</td>
		<td colspan="2"><textarea cols="60" rows="4" name="$token"><?php echo $text ?></textarea></td>
		<td>
			<input type="submit" value="Store"/>
		</td>			
	</tr>
#end
#readParameters()
#foreach( $entity in $application.entities )
#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)})
#if (!$entity.external) 
$extraFilesInfo.clear()
----- meta\\${tr.f_v($application.name)}\\virgo${tr.FV($entity.name)}\\params.php -----
## w .htaccess musi byc: 
## RewriteCond %{REQUEST_URI} !.*/meta/.*/params\.php
<?php
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>virgo parameters</title>
	</head>
	<body>
<style type="text/css">
table, input, textarea {
	font-size: 10px;
}
table, input {
	font-family: Verdana;
}
textarea {
	font-family: Courier;
}
form {
	background-color: LightGoldenRodYellow;
	color: Black;
	padding: 10px;
	margin: 5px;
	border: 2px outset BurlyWood;
}
tr.spacer {
    background-color: Wheat;
    font-weight: bold;
}
th {
	background-color: White;
}
.checkbox {
	margin: 0px;
}
table.perm, div.perm {
	font-size: 6px;
	display: inline;
}
table.perm input, table.perm tr, table.perm td {
	padding: 0px;
	margin: 0px;
}
table.perm td.neutral {
	background-color: LightGrey;
}
table.perm td.allowed {
	background-color: LightGreen;
}
table.perm td.blocked {
	background-color: Red;
}
div.action {
  font-size: 8px;
  padding: 0px 1px;
}
div.quick_links a {
	font-size: 9px;
}
</style>
<div class="quick_links">
<a href="#action_permissions">Permissions</a>
<a href="#table_columns">Table columns</a>
<a href="#create_columns">Create columns</a>
<a href="#form_columns">Form columns</a>
<a href="#view_columns">View columns</a>
</div>
<form method="post">
<?php
//	require_once(dirname(__FILE__).'/../../../classes/DatabaseHandler/DatabaseHandler.php');
	$databaseHandler = new DatabaseHandler();
	
	function isa($roleId, $prtId, $action) {
		global $databaseHandler;
		$query = " SELECT prm_execute FROM prt_permissions WHERE prm_rle_id = {$roleId} AND prm_pob_id = {$prtId} AND prm_action = ?";
		$result = $databaseHandler->queryPrepared($query, false, "s", array($action));
		echo $databaseHandler->error() == "" ? "" : $databaseHandler->error() . " " . $query;
		if ($result !== false) {
			foreach ($result as $res) {
				return $res;
			}
		} else {
			return null;
		}
	}

	function actionPermissions($action, $id, $name, $pobId) {
				if (isset($_POST['pobId'])) {
					zapiszPozwolenieRole($pobId, $action, $id, $name);
				}
?>				
				<td class="action">
					<div class="action"><?php echo $action ?></div>
					<table class="action">
						<tr>
							<td class="<?php echo perm($id, $pobId, $action, NULL) ? 'neutral' : '' ?>"><input type="radio" name="<?php echo $action ?>_<?php echo $id ?>_<?php echo $pobId ?>" id="<?php echo $action ?>_<?php echo $id ?>_<?php echo $pobId ?>_" value="NULL" <?php echo perm($id, $pobId, $action, NULL) ? " checked='checked' " : "" ?>/></td>
							<td class="<?php echo perm($id, $pobId, $action, 1) ? 'allowed' : '' ?>"><input type="radio" name="<?php echo $action ?>_<?php echo $id ?>_<?php echo $pobId ?>" id="<?php echo $action ?>_<?php echo $id ?>_<?php echo $pobId ?>_1" value="1" <?php echo perm($id, $pobId, $action, 1) ? " checked='checked' " : "" ?>/></td>
							<td class="<?php echo perm($id, $pobId, $action, 0) ? 'blocked' : '' ?>"><input type="radio" name="<?php echo $action ?>_<?php echo $id ?>_<?php echo $pobId ?>" id="<?php echo $action ?>_<?php echo $id ?>_<?php echo $pobId ?>_0" value="0" <?php echo perm($id, $pobId, $action, 0) ? " checked='checked' " : "" ?>/></td>
							<td width="100%"></td>				
						</tr>
					</table>
				</td>
<?php
	}

	function setPermissions($action, $id, $pobId) {
?>		
var e = document.getElementById('<?php echo $action ?>_<?php echo $id ?>_<?php echo $pobId ?>_' + this.value); e.checked = true;		
<?php
	}
		
	function zapiszPozwolenieRole($id, $action, $roleId, $name) {
		global $databaseHandler;
		$query = " SELECT prm_execute FROM prt_permissions WHERE prm_rle_id = {$roleId} AND prm_pob_id = {$id} AND prm_action = ?";
		$result = $databaseHandler->queryPrepared($query, false, "s", array($action));
		echo $databaseHandler->error() == "" ? "" : $databaseHandler->error() . " " . $query;
		$row = null;
		foreach ($result as $row) {
			break;
		}
		$query = null;
		if ($_POST["{$action}_{$roleId}_{$id}"] == "NULL") {
			if (isset($row)) {	
				if (isset($row['prm_execute'])) {
					$query = " UPDATE prt_permissions SET prm_execute = NULL WHERE prm_rle_id = {$roleId} AND prm_pob_id = {$id} AND prm_action = ? ";
					$checkNull = true;
				}
			}
		} else {
			$val = $_POST["{$action}_{$roleId}_{$id}"];
			if (is_null($row)) {
				$query = " INSERT INTO prt_permissions (prm_execute, prm_rle_id, prm_pob_id, prm_action) VALUES ({$val}, {$roleId}, {$id}, ?) ";
			} else {
				if ($row['prm_execute'] != $val) {
					$query = " UPDATE prt_permissions SET prm_execute = {$val} WHERE prm_rle_id = {$roleId} AND prm_pob_id = {$id} AND prm_action = ? ";
				}
			}
		}
		if (isset($query)) {
			$databaseHandler->queryPrepared($query, false, "s", array($action));
			echo $databaseHandler->error() == "" ? "" : $databaseHandler->error() . " " . $query;
		}
	}

	function perm($roleId, $prtId, $action, $value) {
		$row = isa($roleId, $prtId, $action);
		if ($row) {
			if (is_null($row['prm_execute'])) {
				return !isset($value);
			} else {
				if (!isset($value)) {
					return false;
				} else {
					return $row['prm_execute'] == $value;
				}
			}
			
		} else {
			return !isset($value);
		}
	}	
	$newAction = $_POST['newAction'];
	if (isset($newAction) && trim($newAction) != "") {
		$query = " INSERT INTO prt_permissions (prm_rle_id, prm_pob_id, prm_action, prm_execute) SELECT rle_id, pob_id, '{$newAction}', NULL FROM prt_roles, prt_portlet_objects WHERE pob_id = {$pobId} ";
		$databaseHandler->query($query);
	}
	$codeActions = array();	
	$types = "";
#set ($actions = $actionPerEntity.getActions($entity.name))
#foreach( $tmpAction in $actions )
	$codeActions[] = "${tmpAction}";
	$codeActionStrings[] = "?";
	$types .= "s";
#end
	$codeActionsString = implode(", ", $codeActionStrings);
	$query = "
SELECT 
	DISTINCT prm_action 
FROM 
	prt_permissions 
WHERE 
	prm_pob_id = {$pobId} 
	AND prm_action IS NOT NULL 
	AND prm_action NOT IN ({$codeActionsString})
";
	
	$result = $databaseHandler->queryPrepared($query, false, $types, $codeActions);
	$extraActions = array();
	foreach ($result as $row) {
		$extraActions[] = $row[0];
	}
	$query = "
SELECT 
	pdf_alias,
	pdf_id 
FROM 
	prt_portlet_definitions
	LEFT OUTER JOIN prt_portlet_objects ON pob_pdf_id = pdf_id
WHERE pob_id = {$pobId} ";
#paramSelectRow()
	$alias = $row['pdf_alias'];
	$pdfId = $row['pdf_id']
?>
	<input type="hidden" name="pobId" value="<?php echo $pobId ?>">
	<table class="params">
		<tr>
			<th>Parameter name</th>
			<th>Portlet definition value</th>
			<th>Portlet object value</th>
			<th></th>
		</tr>
<?php
	switch ($alias) {
## #foreach( $entity in $application.entities )
## #if (!$entity.external)
		case 'virgo${tr.FV($entity.name)}':
?>
## ##############################################################
#spacer('Component mode')
<?php
			$options = array();
			$options["0"] = "CRUD Table";
			$options["1"] = "Only creation form";
			$options["7"] = "Only view data (record selection must be programmed)";
			$options["2"] = "Search by default";
			$options["5"] = "Only edition form (1 record/user)";
			$options["6"] = "Edit user data";
#set ($hasDate = "0")
#set ($hasValidityDate = "0")
#foreach( $property in $entity.properties )
#if ($property.dataType.name == "DATE" || $property.dataType.name == "DATETIME") 
#set ($hasDate = "1")
#if ($property.customProperty)
#getParamValue($property.customProperty "Validity")
#if ($virgoWartosc == "true")
#set ($hasValidityDate = "1")
#end
#end
#end
#end
#if ($hasDate == "1")
			$options["3"] = "Event callendar view";
#end
#set ($hasImage = "0")
#foreach( $property in $entity.properties )
#if ($property.dataType.name == "IMAGE") 
#set ($hasImage = "1")
#end
#end
#if ($hasImage == "1")
			$options["4"] = "Chessboard";
#end
?>
#paramList("form_only" "" "")
#if ($hasDate == "1")
<?php
			$options = array();
#foreach( $property in $entity.properties )
#if ($property.dataType.name == "DATE" || $property.dataType.name == "DATETIME")
			$options["${tr.f_v($property.name)}"] = "${tr.Fv($property.name)}";
#end
#end
?>
#paramList("event_column" "" "")
#end			
#if ($hasValidityDate == "1")
<?php
			$options = array();
			$options["1"] = "Yes, only in range";
			$options["0"] = "No, show all of them";
?>
#paramList("only_records_in_valid_range" "" "")
#end	
#if ($hasImage == "1")
<?php
			$options = array();
			$options["2"] = "2";
			$options["3"] = "3";
			$options["4"] = "4";
			$options["5"] = "5";
			$options["6"] = "6";
			$options["7"] = "7";
			$options["8"] = "8";
			$options["9"] = "9";
?>			
#paramList("chessboard_width" "" "")
#paramText("chessboard_image_size")		
#paramText("chessboard_cell_width")
#paramText("chessboard_cell_height")
#end
<?php
			$options = array();
			$options["0"] = "No, records from all users";
			$options["1"] = "Yes, logged in user created only";
?>	
#paramList("only_private_records" "" "")
<?php
			$options = array();
			$options["1"] = "Yes, old school Oracle Forms style";
			$options["0"] = "No. Allow contextless work";
?>	
#paramList("force_context_on_first_row" "" "")
<?php
			$options = array();
#foreach( $relation in $entity.childRelations )
			$query = <<<SQL
SELECT 
  pob_id,
  pdf_name,
  pob_custom_title,
  pge_title
FROM 
  prt_portlet_definitions
  LEFT OUTER JOIN prt_portlet_objects ON pob_pdf_id = pdf_id
  LEFT OUTER JOIN prt_portlet_locations ON plc_pob_id = pob_id
  LEFT OUTER JOIN prt_pages ON plc_pge_id = pge_id
WHERE 
  pdf_alias = ?
  AND pdf_namespace = ?
SQL;
			$result = $databaseHandler->queryPrepared($query, false, "ss", array('virgo${tr.FV($relation.parentEntity.name)}', '$application.name'));
			foreach ($result as $row) {
				$options[$row['pob_id']] = "{$row['pdf_name']} {$row['pob_custom_title']} [{$row['pge_title']}]";
			}
#end			
?>		
#paramList("parent_entity_pob_id" "multiple='multiple'" "")
<?php
			$options = array();
#foreach( $relation in $entity.childRelations )
#foreach( $grandRelation in $relation.parentEntity.childRelations )
			$query = <<<SQL
SELECT 
  pob_id,
  pdf_name,
  pob_custom_title,
  pge_title
FROM 
  prt_portlet_definitions
  LEFT OUTER JOIN prt_portlet_objects ON pob_pdf_id = pdf_id
  LEFT OUTER JOIN prt_portlet_locations ON plc_pob_id = pob_id
  LEFT OUTER JOIN prt_pages ON plc_pge_id = pge_id
WHERE 
  pdf_alias = ?
  AND pdf_namespace = ?
SQL;
			$result = $databaseHandler->queryPrepared($query, false, "ss", array('virgo${tr.FV($grandRelation.parentEntity.name)}', '$application.name'));
			foreach ($result as $row) {
				$options[$row['pob_id']] = "{$row['pdf_name']} {$row['pob_custom_title']} [{$row['pge_title']}]";
			}
#end
#end
?>		
#paramList("grandparent_entity_pob_id" "multiple='multiple'" "")
<?php
			$options = array();
			$options["E"] = "'Select parent' message";
			$options["C"] = "Chldren with empty parent";
			$options["G"] = "Filter by grandparent";
			$options["A"] = "Show all records";
			$options["H"] = "Hide content";
?>
#paramList("when_no_parent_selected" "" "")
#paramBool("master_mode" "")
<?php
			$options = array();
			$query = <<<SQL
SELECT 
  pob_id,
  pdf_name,
  pob_custom_title,
  pge_title
FROM 
  prt_portlet_definitions
  LEFT OUTER JOIN prt_portlet_objects ON pob_pdf_id = pdf_id
  LEFT OUTER JOIN prt_portlet_locations ON plc_pob_id = pob_id
  LEFT OUTER JOIN prt_pages ON plc_pge_id = pge_id
WHERE 
  pdf_alias = ?
  AND pdf_namespace = ?
  AND pob_id != {$pobId}
SQL;
			$result = $databaseHandler->queryPrepared($query, false, "ss", array('virgo${tr.FV($entity.name)}', '$application.name'));
			foreach ($result as $row) {
				$options[$row['pob_id']] = "{$row['pdf_name']} {$row['pob_custom_title']} [{$row['pge_title']}]";
			}
			$tmpOptions = $options;
?>		
#paramList("master_entity_pob_id" "" "")
#paramBool("filter_mode" "Komponent pokazuje tylko formularz do szukania (ale trzeba to ustawic w searchform only)") 
<?php
			$options = $tmpOptions;
?>
#paramList("filter_entity_pob_id" "" "Czytaj kryteria nie swoje tylko z podanego komponentu") 
<?php
			$options = array();
			$options["virgo"] = "Virgo generated";
			$options["template"] = "Inherit from template";
?>			
#paramList("css_usage" "" "")
<?php
			$options = array();
			$options["0"] = "Details button";
			$options["1"] = "Each field is a link";
?>			
#paramList("show_details_method" "" "")
#paramText("available_page_sizes")
#paramText("default_page_size")
<?php
			$options = array();
			$options["${tr.f_v($entity.prefix)}_id"] = "id";
#foreach( $property in $entity.properties )
## "multikolumnowe" wlasciwosci nie moga byc sortowane, albo inaczej: musza same wybrac po ktorej kolumnie bedzie sort 
#parseFileWithStandard("defs/dataTypes/php/sortProperty/${tr.f_v($property.dataType.name)}" "defs/sortProperty.php")
#end
#foreach( $relation in $entity.childRelations )
#if ($relation.name)								
			$options["${tr.f_v($relation.parentEntity.name)}_${tr.f_v($relation.name)}"] = "${tr.Fv($relation.parentEntity.name)} ${tr.Fv($relation.name)}";
#else							
			$options["${tr.f_v($relation.parentEntity.name)}"] = "${tr.Fv($relation.parentEntity.name)}";
#end
#end
?>
#paramList("default_sort_column" "" "")
<?php
			$options = array();
			$options["asc"] = "Ascending";
			$options["desc"] = "Descending";
?>
#paramList("default_sort_mode" "" "")
#paramBool("enable_record_duplication" "")
#paramBool("show_table_filter" "")
#if ($entity.versioned)
#paramBool("show_record_history" "")
#end
#paramBool("empty_values_search" "")
#paramMultiline("title_value" "What you put here will be added to '$ret = ' line, so end it with colon. Eg: $this->getNazwa() . ' (' . $this->getBank()->getVirgoTitle() . ')';")
<tr><td>log_level</td><td>TODO</td><td>TODO</td><td></td></tr>
#paramBool("under_construction" "")
#paramText("ajax_max_label_list_size")
#paramMultiline("extra_ajax_filter" "eg. ${tr.f_v($entity.prefix)}_abc_id in (select abc_id from...)) ") 
<tr><td>check_token</td><td>TODO</td><td>TODO</td><td></td></tr>
#paramText("under_construction_allowed_user")
#paramBool("show_project_name" "")
#foreach ($property	in $entity.properties)		
#if ($property.dataType.name == "IMAGE") 
#paramText("scale_image_to_width_${tr.f_v($property.name)}")
#paramText("scale_image_to_height_${tr.f_v($property.name)}")
#end		
#end		
#foreach ($property	in $entity.properties)		
#if ($property.dataType.name == "TEXT" || $property.dataType.name == "CODE") 
#paramText("short_text_size_${tr.f_v($property.name)}")
#end		
#end		
<?php
			$options = array();
			$options["freeserif"] = "Serif";
			$options["freesans"] = "Sans serif";
?>
#paramList("pdf_font_name" "" "")
#paramBool("pdf_include_bold_font" "")
#paramText("pdf_font_size")
#paramText("pdf_max_column_width")
<?php
			$options = array();
			$options["P"] = "Portrait";
			$options["L"] = "Landscape";
?>
#paramList("pdf_page_orientation" "" "")
#foreach( $property in $entity.properties )
#parseFile("defs/dataTypes/php/componentParameters/${tr.f_v($property.dataType.name)}")
#end
#foreach( $relation in $entity.parentRelations )
## sprawdz, czy te dzieci to przypadkiem nie jest tabela n:m ktora ma drugiego rodzica, ktory jest slownikiem
#set ($childrenRendered = false)
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
#set ($childrenRendered = true)
<?php
			$options = array();
			$options["select"] = "Option selection multiple";
			$options["checkbox"] = "Checkboxes";
?>
#paramList("n_m_children_input_${tr.f_v($relation.childEntity.name)}_${tr.f_v($relation.name)}" "" "")
#end
#end
#end
<?php
			$options = array();
			$options["false"] = "Forms in list";
			$options["true"] = "Forms in table";
			$options["float"] = "Floating fields";
			$options["float-grid"] = "Floating fields in grid";
?>
#paramList("forms_rendering" "" "")
<?php
			$options = array();
			$options["Select"] = "Select";
			$options["View"] = "View";
			$options["Form"] = "Edit";
// bo nie pyta!!!			$options["Delete"] = "Delete (seriously?)";
			$options["Custom"] = "Custom (fill field below)";
?>
#paramList("action_on_row_click" "" "")
#paramText("action_on_row_click_custom")
#paramList("action_on_row_double_click" "" "")
#paramText("action_on_row_double_click_custom")
<?php
			$options = array();
			$options["0"] = "No, show it";
			$options["1"] = "Yes, hide this useless stuff";
?>
#paramList("hide_audit" "" "") 
## <?php
##			$options = array();
##			$options["onclick"] = "Single click";
##			$options["ondblclick"] = "Double click";
## ?>
## #paramList("action_click_trigger" "" "")
#spacer('Action permissions')
</table>
<table class="params">
<?php
				$roles = array();
				$result = $databaseHandler->query(" SELECT rle_id, rle_name FROM prt_roles WHERE IFNULL(rle_virgo_deleted, 0) = 0 ");
				echo $databaseHandler->error();
				while($row = mysqli_fetch_row($result)) {
					$roles[$row[0]] = $row[1];
				}	
				mysqli_fetch_row($result);
				foreach ($roles as $id => $name) {
?>
<tr>
	<td>
						<?php echo $name ?>
	</td>
	<td colspan="4">
		<table class="perm" cellpadding="0" cellspacing="0">
			<tr>
<?php
#foreach( $tmpAction in $actions )
	actionPermissions("${tmpAction}", $id, $name, $pobId);
#end
##	actionPermissions("View", $id, $name, $pobId);
##	actionPermissions("Add", $id, $name, $pobId);
##	actionPermissions("Form", $id, $name, $pobId);
##	actionPermissions("Delete", $id, $name, $pobId);
##	actionPermissions("SearchForm", $id, $name, $pobId);
##	actionPermissions("Report", $id, $name, $pobId);
##	actionPermissions("Export", $id, $name, $pobId);
##	actionPermissions("UpdateTitle", $id, $name, $pobId);
##	actionPermissions("Upload", $id, $name, $pobId);
##	actionPermissions("EditSelected", $id, $name, $pobId);
##	actionPermissions("DeleteSelected", $id, $name, $pobId);
	foreach ($extraActions as $extraAction) {
		actionPermissions($extraAction, $id, $name, $pobId);
	}
?>
				<td>
					<select onclick="
<?php					
#foreach( $tmpAction in $actions.iterator() )
	setPermissions("${tmpAction}", $id, $pobId);
#end
##	setPermissions("View", $id, $pobId);
##	setPermissions("Add", $id, $pobId);
##	setPermissions("Form", $id, $pobId);
##	setPermissions("Delete", $id, $pobId);
##	setPermissions("SearchForm", $id, $pobId);
##	setPermissions("Report", $id, $pobId);
##	setPermissions("Export", $id, $pobId);
##	setPermissions("UpdateTitle", $id, $pobId);
##	setPermissions("Upload", $id, $pobId);
##	setPermissions("EditSelected", $id, $pobId);
##	setPermissions("DeleteSelected", $id, $pobId);
?>	
">
						<option value="">Set all to:</option>
						<option value="1">allowed</option>
						<option value="0">blocked</option>
					</select>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php
				}
?>				
</table>
<table><tr><td title="_ECA - extra create action... _EFA, _EVA, _ETA, _ERA, _ICA - instead create action... _IFA, _IVA, _ITA, _IRA">Dodaj akcję: <input type="text" name="newAction"></td></tr></table>
<table class="params">
#spacer('Custom forms')
<?php
			$options = array();
			$options["virgo_default"] = "virgo default";
			$options["virgo_entity"] = "entity default";
#foreach ($fileName in $util.getFiles("modules_project/insteadTable/${tr.f_v($entity.name)}/"))
			$options["$fileName"] = "$fileName";
#end	
?>
#paramList("table_form" "" "")
<?php
			$options = array();
			$options["virgo_default"] = "virgo default";
			$options["virgo_entity"] = "entity default";
#foreach ($fileName in $util.getFiles("modules_project/insteadCreate/${tr.f_v($entity.name)}/"))
			$options["$fileName"] = "$fileName";
#end	
?>
#paramList("create_form" "" "")
<?php
			$options = array();
			$options["virgo_default"] = "virgo default";
			$options["virgo_entity"] = "entity default";
#foreach ($fileName in $util.getFiles("modules_project/insteadForm/${tr.f_v($entity.name)}/"))
			$options["$fileName"] = "$fileName";
#end	
?>
#paramList("edit_form" "" "")
<?php
			$options = array();
			$options["virgo_default"] = "virgo default";
			$options["virgo_entity"] = "entity default";
#foreach ($fileName in $util.getFiles("modules_project/insteadView/${tr.f_v($entity.name)}/"))
			$options["$fileName"] = "$fileName";
#end	
?>
#paramList("view_form" "" "")
#spacer('Table columns')
#foreach( $property in $entity.properties )
#paramBool("show_table_${tr.f_v($property.name)}" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
<?php
			$options = array();
			$options["0"] = "No";
			$options["1"] = "Yes";
			$options["2"] = "Change";
?>
#paramList("show_table_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#paramBool("show_table_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}" "")
#end
#spacer('Create columns')
#foreach( $property in $entity.properties )
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["2"] = "Read";
			$options["1"] = "Change";
?>
#paramList("show_create_${tr.f_v($property.name)}" "" "")
#if ($property.dataType.name == 'DATE' || $property.dataType.name == 'DATETIME')
#paramBool("create_default_now_${tr.f_v($property.name)}" "")
#end
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["2"] = "Read";
			$options["1"] = "Change";
			$options["3"] = "Change Ajax";
?>
#paramList("show_create_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#if ($relation.parentEntity.external)
#set ($appPrefix = ${tr.f_v($relation.parentEntity.properties.get(1).name)})
#else
#set ($appPrefix = ${tr.f_v($application.prefix)})
#end
<?php
			$options = array();
			$options[-2] = "Encrypted id from request";
			$options[-1] = "record created by logged in user";
			$query = <<<SQL
SELECT 
  ${tr.f_v($relation.parentEntity.prefix)}_id,
  ${tr.f_v($relation.parentEntity.prefix)}_virgo_title
FROM 
  ${appPrefix}_${tr.f_v($relation.parentEntity.namePlural)}
SQL;
			$result = $databaseHandler->query($query);
			while ($row = mysqli_fetch_row($result)) {
				$options[$row[0]] = "{$row[1]}";
			}
			mysqli_free_result($result);			
?>
#paramList("create_default_value_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#paramBool("show_create_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}" "")
#set ($childrenRendered = false)
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
#set ($childrenRendered = true)
## tamta relacja: ${tr.Fv($relation.childEntity.namePlural)} ${tr.Fv($relation.name)}
#if ($secondParentRelation.parentEntity.external)
#set ($appPrefix = ${tr.f_v($secondParentRelation.parentEntity.properties.get(1).name)})
#else
#set ($appPrefix = ${tr.f_v($application.prefix)})
#end
<?php
			$options = array();
			$query = <<<SQL
SELECT 
  ${tr.f_v($secondParentRelation.parentEntity.prefix)}_id,
  ${tr.f_v($secondParentRelation.parentEntity.prefix)}_virgo_title
FROM 
  ${appPrefix}_${tr.f_v($secondParentRelation.parentEntity.namePlural)}
SQL;
			$result = $databaseHandler->query($query);
			while ($row = mysqli_fetch_row($result)) {
				$options[$row[0]] = "{$row[1]}";
			}
			mysqli_free_result($result);			
?>
#paramList("create_default_values_${tr.f_v($secondParentRelation.parentEntity.namePlural)}${tr.f_v($relation.name)}" "multiple='multiple'" "")
#end	
#end	
#end	
#spacer('Form columns')
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["2"] = "Read";
			$options["1"] = "Change";
?>
#foreach( $property in $entity.properties )
#paramList("show_form_${tr.f_v($property.name)}" "" "")
#end
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["2"] = "Read";
			$options["1"] = "Change";
			$options["3"] = "Change Ajax";
?>
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
#paramList("show_form_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#paramBool("show_form_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}" "")
#end
#spacer('View columns')
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
?>
#foreach( $property in $entity.properties )
#paramList("show_view_${tr.f_v($property.name)}" "" "")
#end
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
?>
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
#paramList("show_view_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#paramBool("show_view_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}" "")
#end	
#spacer('Search columns')
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
?>
#foreach( $property in $entity.properties )
#paramList("show_search_${tr.f_v($property.name)}" "" "")
#end
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
?>
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
#paramList("show_search_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#paramBool("show_search_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}" "")
#end	
#spacer('PDF columns')
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
			$options["2"] = "Count()";
			$options["3"] = "Sum()";
			$options["4"] = "Avg()";
?>
#foreach( $property in $entity.properties )
#paramList("show_pdf_${tr.f_v($property.name)}" "" "")
#end
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
?>
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
#paramList("show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#paramBool("show_pdf_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}" "")
#end	
#spacer('Export columns')
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
?>
#foreach( $property in $entity.properties )
#paramList("show_export_${tr.f_v($property.name)}" "" "")
#end
<?php
			$options = array();
			$options["0"] = "Hide";
			$options["1"] = "Show";
?>
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
#paramList("show_export_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#paramBool("show_export_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}" "")
#end	
#foreach( $relation in $entity.childRelations )
#if ($relation.parentEntity.external)
#set ($appPrefix = ${tr.f_v($relation.parentEntity.properties.get(1).name)})
#else
#set ($appPrefix = ${tr.f_v($application.prefix)})
#end
<?php
			$options = array();
#if (!$relation.obligatory)
			$options['empty'] = '(empty)';
#end
			$query = <<<SQL
SELECT 
  ${tr.f_v($relation.parentEntity.prefix)}_id,
  ${tr.f_v($relation.parentEntity.prefix)}_virgo_title
FROM 
  ${appPrefix}_${tr.f_v($relation.parentEntity.namePlural)}
SQL;
			$result = $databaseHandler->query($query);
			while ($row = mysqli_fetch_row($result)) {
				$options[$row[0]] = "{$row[1]}";
			}
			mysqli_free_result($result);			
?>
#paramList("limit_to_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "multiple='multiple'" "")
#end
#paramMultiline("custom_sql_condition" "Custom SQL condition (eg. ${tr.f_v($entity.prefix)}_abc_id in (select abc_id from...)) You can use classes $currentUser and $currentPage (eg. ${tr.f_v($entity.prefix)}_usr_created_id = {$user->getId()})")
#paramMultiline("custom_parent_query" "For more complex and flexible parent filtering than usual master/detail. The ? sign will be replaced with the value of the virgo_parent_id parameter set by some other portlet")
#spacer('Import setting')
<?php
			$options = array();
			$options["T"] = "All or nothing (break on first error)";
			$options["V"] = "Import valid records, ignore rest";
?>
#paramList("import_mode" "" "")
#paramText("field_separator")
#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'DATE')
#paramText("import_format_${tr.f_v($property.name)}")
#end
#end
#foreach( $relation in $entity.childRelations )
#if ($relation.parentEntity.external)
#set ($appPrefix = ${tr.f_v($relation.parentEntity.properties.get(1).name)})
#else
#set ($appPrefix = ${tr.f_v($application.prefix)})
#end
<?php
			$options = array();
			$options[-1] = "record created by logged in user";
			$query = <<<SQL
SELECT 
  ${tr.f_v($relation.parentEntity.prefix)}_id,
  ${tr.f_v($relation.parentEntity.prefix)}_virgo_title
FROM 
  ${appPrefix}_${tr.f_v($relation.parentEntity.namePlural)}
SQL;
			$result = $databaseHandler->query($query);
			while ($row = mysqli_fetch_row($result)) {
				$options[$row[0]] = "{$row[1]}";
			}
			mysqli_free_result($result);			
?>
#paramList("import_default_value_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}" "" "")
#end
<?php
	$where = " lng_default = 1 ";
	if (isset($_SESSION['portal_current_lang_id'])) {
		$where = " lng_id = " . $_SESSION['portal_current_lang_id'];
	}
	$query = <<<SQL
SELECT
	lng_id, lng_name
FROM
	prt_languages
WHERE 
	{$where}
SQL;
	$result = $databaseHandler->query($query);
	while ($row = mysqli_fetch_row($result)) {
		$lngId = $row[0];
		$lngName = $row[1];
	}
	mysqli_free_result($result);			
?>
#spacer('Hints (<?php echo $lngName ?>)')
#hint("HINT_${tr.f_v($entity.name)}")
#foreach( $property in $entity.properties )
#hint("HINT_${tr.f_v($entity.name)}_${tr.f_v($property.name)}")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.childRelations )
#hint("HINT_${tr.f_v($entity.name)}_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}")
#end
<tr><td></td><td></td><td></td><td></td></tr>
#foreach( $relation in $entity.parentRelations )
#hint("HINT_${tr.f_v($entity.name)}_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}")
#end	
<tr><td></td><td></td><td></td><td></td></tr>
#paramMultiline("portlet_css" "Style definitions limited to current portlet, eg: legend.my_form {display: none;} ")
## to pierwsze jest chyba bez sensu...:
#parseFile("defs/dataTypes/php/customParameters/${tr.f_v($entity.name)}.php")
#parseFile("modules_project/customParameters/${tr.f_v($entity.name)}.php")
<?php
			break;
## #end
## #end
	}
?>
	</table>
	<input type="submit" value="Store"/>
</form>
	</body>
</html>

#end
#end
#end
