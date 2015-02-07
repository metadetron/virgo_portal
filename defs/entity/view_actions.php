#if ($entity.name != "wpis dziennika")			
<?php
#permissionRestrictedBlockBegin("form")
#if ($entity.dictionary)
	if ($result${tr.FV($entity.name)}->isDeletedVirgo()) {
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
	if ($result${tr.FV($entity.name)}->isDeletedVirgo()) {
?>#actionDisabled("'DELETE'")<?php
	} else {
#end
?>#actionButtonConfirmation("Delete" "'DELETE'" "<?php echo T(${DQ}ARE_YOU_SURE_YOU_WANT_REMOVE${DQ}, T(${DQ}${tr.F_V($entity.name)}${DQ}), ${DQ}\\'${DQ}.$result${tr.FV($entity.name)}->getVirgoTitle().${DQ}\\'${DQ}) ?>")
<?php
#if ($entity.dictionary)
	}
#end
#permissionRestrictedBlockEnd()
?>
#end
#parseFile("modules_project/extraViewActions/${tr.f_v($entity.name)}.php")
<?php						
	if (P('form_only') != "1" && P('form_only') != "7") {
?>
#actionButtonSimple("Close" "'CLOSE'")
<?php						
	}
?>

