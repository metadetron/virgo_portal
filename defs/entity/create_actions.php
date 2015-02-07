#readParameters()
<?php
	$actions = virgoRole::getExtraActions('IC');
	if (isset($actions) && count($actions) > 0) {
		foreach ($actions as $action) {
?>
#actionButtonSimple("${dollar}action" "$action")
<?php						
		}
	} else {
?>
#actionButton("Store" "'STORE'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
<?php						
		if ((!isset($masterPobId) || trim($masterPobId) == "") && P('form_only', "0") != "1" && P('form_only') != "5") {
?>
#if ($showStoreAndClear == 'TRUE')
#actionButton("StoreAndClear" "'STORE_AND_CLEAR'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
#end
#if ($showApply == 'TRUE')
<?php 
	if ($this->canExecute("Form")) {
?>
#actionButton("Apply" "'APPLY'" "" "" "" "" "this.form.virgo_changed.value = 'N';")
<?php 
	}
?>
#end
#actionButton("Close" "'CLOSE'" "" "!(this.form.virgo_changed.value == 'T')" "'ARE_YOU_SURE_YOU_WANT_DISCARD'" "" "this.form.virgo_changed.value = 'N';")
<?php						
		}
		$actions = virgoRole::getExtraActions('EC');
		foreach ($actions as $action) {
?>
#actionButtonSimple("${dollar}action" "$action")
<?php						
		}
	}
?>

