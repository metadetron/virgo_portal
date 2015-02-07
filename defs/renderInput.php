<?php
	if (!isset($result${tr.FV($property.entity.name)})) {
		$result${tr.FV($property.entity.name)} = new ${tr.f_v($application.name)}\virgo${tr.FV($property.entity.name)}();
	}
?>
#parseFileWithStandard("defs/dataTypes/php/renderInput/${tr.f_v($property.dataType.name)}" "defs/renderInputStandard.php")

