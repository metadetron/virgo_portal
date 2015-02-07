#set ($orderedFields = [])
#set ($orderedFieldsNames = [])
#parseFile("modules_project/fieldOrder/${tr.f_v($entity.name)}/form.vm")
#orderFields()
#foreach( $property in $orderedFields )
#if ($property.class.name == "com.metadetron.virgo.bean.Property")
#parseFileWithStandard("modules_project/renderProperty/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php" "defs/entity/form_property.php")
#parseFile("modules_project/afterFieldForm/${tr.f_v($entity.name)}/${tr.f_v($property.name)}.php")
#elseif ($property.class.name == "com.metadetron.virgo.bean.Relation")
#set ($relation = $property)
#renderParentSelect("form")
#elseif ($property.class.name == "java.lang.String")
#if ($property == "F:virgo_close")
</fieldset>
#else
<fieldset class="internal">
	<legend><?php echo T('$property.substring(2)') ?></legend>
#end
#end			
#end
#parseFile("modules_project/extraFormFields/${tr.f_v($entity.name)}.php")
#foreach( $relation in $entity.childRelations )
#foreach ($parentRelation2 in $entity.childRelations)
#if ($relation.parentEntity.name != $parentRelation2.parentEntity.name)
#foreach ($childRelation2 in $relation.parentEntity.parentRelations)
#if ($parentRelation2.parentEntity.name == $childRelation2.childEntity.name && $parentRelation2.parentEntity.name != $entity.name && $childRelation2.childEntity.name != $relation.parentEntity.name)
#if (!$parentRelation2.parentEntity.dictionary)
<?php
#if ($entity.name != $relation.parentEntity.name && !$relation.name)
	if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($childRelation2.childEntity.name)}') && P('show_${type}_${tr.f_v($relation.parentEntity.name)}', "1") == "1" && ((P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "1" || P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "2" || P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "3") && !isset($context["${tr.f_v($childRelation2.childEntity.prefix)}"]))) {
#else	
	if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($childRelation2.childEntity.name)}') && P('show_${type}_${tr.f_v($relation.parentEntity.name)}', "1") == "1" && ((P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "1" || P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "2" || P('show_${type}_${tr.f_v($childRelation2.childEntity.name)}${tr.f_v($childRelation2.name)}', "1") == "3"))) {
#end
?>
<script type="text/javascript">
<?php
	$tmpListId = null;
#if ($relation.name)					
	$tmpListId = $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id;
#else
	$tmpListId = $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id;
#end
	if (!is_null($tmpListId)) {
?>
	update${tr.FV($childRelation2.childEntity.name)}(<?php echo $tmpListId ?>, '${tr.fV($entity.prefix)}_${tr.fV($childRelation2.childEntity.name)}${tr.FV($relation.name)}_<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>', <?php echo isset($result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_${tr.f_v($parentRelation2.parentEntity.prefix)}_id) ? $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_${tr.f_v($parentRelation2.parentEntity.prefix)}_id : 'null' ?>); 
<?php
	}
?>
</script>
#if ($entity.name != $relation.parentEntity.name && !$relation.name)
<?php
	}
?>
#end
#end
#end
#end
#end
#end			
#end

#renderNMList('form')

