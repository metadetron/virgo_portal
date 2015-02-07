<?php
		if (isset($result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id)) {
			$result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id = null;
		}
		$parentAjaxRendered = "0";
?>
#parseFile("modules_project/beforeCreateForm/${tr.f_v($entity.name)}.php")
<?php
	$tmpAction = R('portlet_action');
	if ($tmpAction != "Store" && $tmpAction != "Apply" && $tmpAction != "StoreAndClear" && $tmpAction != "BackFromParent") {

#foreach( $relation in $entity.childRelations )
$defaultValue = P('create_default_value_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}');
if (isset($defaultValue) && trim($defaultValue) != "") {
#calculateDefaultValue()	
	$result${tr.FV($entity.name)}->set${tr.Fv($relation.parentEntity.prefix)}${tr.Fv($relation.name)}Id($defaultValue);
}
#end
	}
?>
#set ($orderedFields = [])
#set ($orderedFieldsNames = [])
#parseFile("modules_project/fieldOrder/${tr.f_v($entity.name)}/create.vm")
#orderFields()
#foreach( $property in $orderedFields )
#if ($property.class.name == "com.metadetron.virgo.bean.Property")
#if ($property.dataType.name == 'DATE' || $property.dataType.name == 'DATETIME')
<?php
if (is_null($result${tr.FV($entity.name)}->get${tr.FV($property.name)}()) && P('create_default_now_${tr.f_v($property.name)}') == '1') {
	$result${tr.FV($entity.name)}->set${tr.FV($property.name)}(date(${property.dataType.name}_FORMAT));
}
?>
#end
#parseFileWithStandard("modules_project/renderCreateProperty/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "defs/entity/create_property.php")
#parseFile("modules_project/afterFieldCreate/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php")
#elseif ($property.class.name == "com.metadetron.virgo.bean.Relation")
#set ($relation = $property)
#renderParentSelect("create")
#elseif ($property.class.name == "java.lang.String")
#if ($property == "F:virgo_close")
</fieldset>
#else
<fieldset class="internal">
	<legend><?php echo T('$property.substring(2)') ?></legend>
#end
#end			
#end
#parseFile("modules_project/extraCreateFields/${tr.f_v($entity.name)}.php")

#renderNMList('create')


