<?php
	if (P('show_form_${tr.f_v($property.name)}', "1") == "1" || P('show_form_${tr.f_v($property.name)}', "1") == "2") {
?>
<?php
	if (!$formsInTable) {
?>
	<li
## #if ($property.dataType.name == "CODE" || $property.dataType.name == "TEXT")
## style="display: block;"
## #end
		<?php echo $floatingFields ? "style='display: inline-block; float: left;'" : '' ?>
	>
<?php
	} else {
?>
	<tr><td align="right">
<?php
	}
?>
					<label 
						nowrap="nowrap"
						class="fieldlabel #if ($property.obligatory) obligatory #else <?php echo P('show_form_${tr.f_v($property.name)}_obligatory', "0") == "1" ? " obligatory " : "" ?> #end  ${tr.f_v($property.name)} ${tr.f_v($property.dataType.name)}" 
						for="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>"
					>#renderLabel($property false "form")</label>
<?php
	if (!$formsInTable) {
?>
<?php
	} else {
?>
	</td><td>
<?php
	}
?>
					<span align="left" nowrap>
<?php
	if (P('show_form_${tr.f_v($property.name)}', "1") == "2") {
?>
#renderDataView()
<?php
	} else {
?>
#renderField($property false "form")
<?php
	}
?>
#parseFile("modules_project/rightAfterFieldForm/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php")
					</span>
<?php
	if (!$formsInTable) {
?>
	</li>
<?php
	} else {
?>
	</td></tr>
<?php
	}
?>
## <?php
## 	} else {
## ?>
## #parseFileWithStandard("defs/dataTypes/php/renderInputHidden/${tr.f_v($property.dataType.name)}" "defs/renderInputHidden.php") 
<?php	
	}
?>

