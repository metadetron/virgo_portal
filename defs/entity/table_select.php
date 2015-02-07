## Tutaj nie ma zadnych warunkow, jest tylko zbieranie kolumn!
## <?php
################## nowy select, z virgotitles parentow ####################
			$queryString = " SELECT ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_id, ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_virgo_title ";
#if ($entity.dictionary)			
			$queryString = $queryString . " ,${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_virgo_deleted ";
#end			
## #set ($first = 'false') 			
#foreach( $property in $entity.properties )
#if ($property.name == 'display order' || ${tr.f_v($property.name)} == 'kolejnosc_wyswietlania' || ${tr.f_v($property.name)} == 'order' || ${tr.f_v($property.name)} == 'kolejnosc')
#set ($displayOrderPropertyName = $property.name)
#end
#end			
#if ($displayOrderPropertyName != "")
			$defaultOrderColumn = P('default_sort_column', '${tr.f_v($entity.prefix)}_${tr.f_v($displayOrderPropertyName)}');
#else			
			$defaultOrderColumn = P('default_sort_column');
#end
			$orderColumnNotDisplayed = "";
#foreach( $property in $entity.properties )
#if ($property.obligatory || $showByDefault == '1')
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_${tableDisplayMode}_${tr.f_v($property.name)}', "$obl") != "0") {
#if ($util.fileExists("defs/dataTypes/php/select/${tr.f_v($property.dataType.name)}", $extraFilesInfo)) 
#parse("defs/dataTypes/php/select/${tr.f_v($property.dataType.name)}")
#else
#renderColumn1("${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}" "${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}")
#end
#set ($first = 'false') 			
			} else {
				if ($defaultOrderColumn == "${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}") {
					$orderColumnNotDisplayed = " ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} ";
				}
			}
#end
#parseFile("modules_project/renderExtraColumn/${tr.f_v($entity.name)}_1.php")
#foreach( $relation in $entity.childRelations )
			if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($relation.parentEntity.name)}') && P('show_${tableDisplayMode}_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") != "0") { // */ && !in_array("${relation.parentEntity.prefix}${tr.f_v($relation.name)}", $hideColumnFromContextInTable)) {
## #if ($first == 'false')
## 				$queryString = $queryString . ", ";
## #end
#if ($relation.name)
				$queryString = $queryString . ", ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id as ${tr.f_v($entity.prefix)}_${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id ";
				$queryString = $queryString . ", ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_${tr.f_v($relation.name)}.${tr.f_v($relation.parentEntity.prefix)}_virgo_title as ${tr.f_v($relation.parentEntity.name)}_${tr.f_v($relation.name)} ";
#else
				$queryString = $queryString . ", ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id as ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id ";
				$queryString = $queryString . ", ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_parent.${tr.f_v($relation.parentEntity.prefix)}_virgo_title as `${tr.f_v($relation.parentEntity.name)}` ";
#end
#set ($first = 'false') 			
			} else {
#if ($relation.name)								
				if ($defaultOrderColumn == "${tr.f_v($relation.parentEntity.name)}_${tr.f_v($relation.name)}") {
#else							
				if ($defaultOrderColumn == "${tr.f_v($relation.parentEntity.name)}") {
#end
#if ($relation.name)
					$orderColumnNotDisplayed = " ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_${tr.f_v($relation.name)}.${tr.f_v($relation.parentEntity.prefix)}_virgo_title ";
#else
					$orderColumnNotDisplayed = " ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_parent.${tr.f_v($relation.parentEntity.prefix)}_virgo_title ";
#end					
				}
			}
#end
			if ($orderColumnNotDisplayed != "") {
				$queryString = $queryString . ", " . $orderColumnNotDisplayed;
			}
			$queryString = $queryString . " FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ";
#foreach( $relation in $entity.childRelations )
			if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($relation.parentEntity.name)}')) {
#if ($relation.parentEntity.external)
#set ($appPrefix = ${tr.f_v($relation.parentEntity.properties.get(1).name)})
#else
#set ($appPrefix = ${tr.f_v($application.prefix)})
#end
#if ($relation.name)
				$queryString = $queryString . " LEFT OUTER JOIN ${appPrefix}_${tr.f_v($relation.parentEntity.namePlural)} AS ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_${tr.f_v($relation.name)} ON (${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id = ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_${tr.f_v($relation.name)}.${tr.f_v($relation.parentEntity.prefix)}_id) ";
#else
				$queryString = $queryString . " LEFT OUTER JOIN ${appPrefix}_${tr.f_v($relation.parentEntity.namePlural)} AS ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_parent ON (${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id = ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_parent.${tr.f_v($relation.parentEntity.prefix)}_id) ";
#end			
			}
#end

#set ($syncronizeUser = "0") 
#foreach( $property in $entity.properties )
#readSyncValues()
#if ($sync == "1") 
#set ($syncronizeUser = "1") 
#end
#end
#if ($syncronizeUser == "1") 
			$queryString = $queryString . " LEFT OUTER JOIN jos_users ON jos_users.id = ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${tr.f_v($entity.prefix)}_usr_created_id ";
#end
##			echo $queryString;
## TODO: Zoptymalizowac order by i limit metoda z ksiazki Wysocewydajne MySQL ze strony 213 u gory
###########################################################################
## ?>
