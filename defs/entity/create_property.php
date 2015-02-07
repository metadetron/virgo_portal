<?php
	if (P('show_create_${tr.f_v($property.name)}', "1") == "1" || P('show_create_${tr.f_v($property.name)}', "1") == "2") {
?>
<?php
	if (!$formsInTable) {
?>
	<li
## #if ($property.dataType.name == "CODE" || $property.dataType.name == "TEXT")
##  style="display: block;"
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
					<label nowrap class="fieldlabel #if ($property.obligatory) obligatory #else <?php echo P('show_create_${tr.f_v($property.name)}_obligatory', "0") == "1" ? " obligatory " : "" ?> #end" for="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>">
#renderLabel($property false "create")						
					</label>
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
## Sytuacja z kalendarzem jest inna, bo przycisk Add jest jednoczesnie data, czyli owa data powinna sie juz ustawic
	$virgoSelectedDay = R('virgo_selected_day');
	if (S($virgoSelectedDay)) {
		if (P('form_only') == "3") {
			if (P('event_column') == "${tr.f_v($property.name)}") {
				$result${tr.FV($entity.name)}->set${tr.FV($property.name)}($virgoSelectedDay);
			}
		}
	}
	if (P('show_create_${tr.f_v($property.name)}', "1") == "2") {
?>
#renderDataView()
<?php
	} else {
?>
#renderField($property false "create")
<?php
	}
?>
#parseFile("modules_project/rightAfterFieldCreate/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php")
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

