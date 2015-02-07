#parseFile("modules_project/beforeView/${tr.f_v($entity.name)}.php")
#set ($orderedFields = [])
#set ($orderedFieldsNames = [])
#parseFile("modules_project/fieldOrder/${tr.f_v($entity.name)}/view.vm")
#orderFields()
#foreach( $property in $orderedFields )
#if ($property.class.name == "com.metadetron.virgo.bean.Property")
<?php
	if (P('show_view_${tr.f_v($property.name)}', '1') == "1") {
?>
<?php
	if (!$formsInTable) {
?>
	<li
		class="${tr.f_v($property.name)}"
## #if ($property.dataType.name == "CODE" || $property.dataType.name == "TEXT")
## style="display: block;"
## #end
		<?php echo $floatingFields ? "style='display: inline-block; float: left;'" : '' ?>
	>
<?php
	} else {
?>
	<dt>
<?php
	}
?>
					<span align="right" nowrap class="fieldlabel">
#parseFileWithStandardText("modules_project/renderLabel/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "<?php echo T('${tr.F_V($property.name)}') ?>")
					</span>
<?php
	if (!$formsInTable) {
?>
<?php
	} else {
?>
	</dt><dd>
<?php
	}
?>
#renderDataView()
#parseFile("modules_project/afterField/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php")
<?php
	if (!$formsInTable) {
?>
	</li>
<?php
	} else {
?>
	</dd>
<?php
	}
?>
<?php
	}
?>
#elseif ($property.class.name == "com.metadetron.virgo.bean.Relation")
#set($relation = $property)
<?php
	if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($relation.parentEntity.name)}') && P('show_view_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', '1') == "1") { // && (isset($masterComponentName) && $masterComponentName != "${tr.f_v($relation.parentEntity.name)}" || is_null($contextId))) {
?>
<?php
	if (!$formsInTable) {
?>
	<li	class="${tr.f_v($relation.parentEntity.name)}"
<?php echo $floatingFields ? "style='display: inline-block; float: left;'" : '' ?>
	>
<?php
	} else {
?>
	<tr><td align="right">
<?php
	}
?>
					<span align="right" nowrap class="fieldlabel">${tr.Fv($relation.parentEntity.name)} ${tr.Fv($relation.name)}</span>
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
#renderParentDataViewOfCurrentRelation()
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
<?php
	}
?>
#elseif ($property.class.name == "java.lang.String")
#if ($property == "F:virgo_close")
</fieldset>
#else
<fieldset class="internal">
	<legend><?php echo T('$property.substring(2)') ?></legend>
#end
#end			
#end
#parseFile("modules_project/extraViewFields/${tr.f_v($entity.name)}.php")
#foreach( $relation in $entity.parentRelations )
<?php
	if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($relation.childEntity.name)}') && P('show_view_${tr.f_v($relation.childEntity.namePlural)}${tr.f_v($relation.name)}', '0') == "1") {
?>
#getChildrenData()
<?php
	if (!$formsInTable) {
?>
	<li	class="${tr.f_v($relation.parentEntity.name)}"
<?php echo $floatingFields ? "style='display: inline-block; float: left;'" : '' ?>
	>
<?php
	} else {
?>
	<tr><td align="right">
<?php
	}
?>
					<span align="right" nowrap class="fieldlabel">
						${tr.Fv($relation.childEntity.namePlural)} ${tr.Fv($relation.name)}
					</span>
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
					<span class="inputbox readonly">
#renderChildrenData('true')
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
<?php
	}
?>
#end
#parseFile("modules_project/afterView/${tr.f_v($entity.name)}.php")

