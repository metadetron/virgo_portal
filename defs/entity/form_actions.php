#readParameters()
#actionButton("Store" "'STORE'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
#if ($doubleTabs == 'FALSE')
<?php						
	if (!isset($masterPobId) && P('form_only', "0") != "1" && P('form_only', "0") != "5" && P('form_only', "0") != "6") {
?>
#if ($showStoreAndClear == 'TRUE')
#actionButton("StoreAndClear" "'STORE_AND_CLEAR'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
#end
#if ($showApply == 'TRUE')
#actionButton("Apply" "'APPLY'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
#end
<?php						
	}
?>
<?php
#permissionRestrictedBlockBegin("delete")
#set ($DQ = '"') 
#if ($entity.dictionary)
	if ($result${tr.FV($entity.name)}->isDeletedVirgo()) {
?>#actionDisabled("'DELETE'")<?php
	} else {
#end
?>#actionButtonConfirmation("Delete" "'DELETE'" "<?php echo T(${DQ}ARE_YOU_SURE_YOU_WANT_REMOVE${DQ}, T(${DQ}${tr.F_V($entity.name)}${DQ}), ${DQ}\\'${DQ}.rawurlencode($result${tr.FV($entity.name)}->getVirgoTitle()).${DQ}\\'${DQ}) ?>")
<?php
#if ($entity.dictionary)
	}
#end
#permissionRestrictedBlockEnd()
?>
<?php						
	if (!isset($masterPobId) && P('form_only') != "1" && P('form_only') != "5") {
?>
#actionButton("Close" "'CLOSE'" "" "!(this.form.virgo_changed.value == 'T')" "'ARE_YOU_SURE_YOU_WANT_DISCARD'" "" "this.form.virgo_changed.value = 'N';")
<?php						
	}
?>
#else
<?php						
	if (!isset($masterPobId) && P('form_only') != "1" && P('form_only') != "5") {
?>
#if ($showStoreAndClear == 'TRUE')
#actionButton("StoreAndClear" "'STORE_AND_CLEAR'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
#end
#if ($showApply == 'TRUE')
#actionButton("Apply" "'APPLY'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
#end
#actionButton("Close" "'CLOSE'" "" "!(this.form.virgo_changed.value == 'T')" "'ARE_YOU_SURE_YOU_WANT_DISCARD'" "" "this.form.virgo_changed.value = 'N';")
<?php						
	}
?>
#end
