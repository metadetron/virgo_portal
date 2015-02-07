----- ${application.name}_create.sql -----
#if (!$singleEntity)
#parseFile("modules_project/instalSql/beforeInstal.sql")
#end
#foreach( $entity in $application.entities )
#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
## INSERT INTO `prt_portlet_definitions` (`pdf_namespace`, `pdf_alias`, `pdf_virgo_state`, `pdf_virgo_title`, `pdf_name`, `pdf_author`, `pdf_version`, `pdf_date_created`, `pdf_date_modified`, `pdf_usr_created_id`, `pdf_usr_modified_id`) VALUES
## ('${tr.f_v($application.name)}', 'virgo${tr.FV($entity.name)}', NULL, '${tr.Fv($entity.name)}', '${tr.Fv($entity.name)}', 'Grzegorz Świerczynski', '${application.versionMain}.${application.version}', '0000-00-00 00:00:00', NULL, 0, NULL);
-- 
-- Struktura tabeli dla  `${application.prefix}_${tr.f_v($entity.namePlural)}`
-- 

#if ($entity.custom)


#elseif ($entity.view)

CREATE OR REPLACE VIEW `${application.prefix}_${tr.f_v($entity.namePlural)}` AS
#parse("modules_project/views/${tr.f_v($entity.name)}.sql")
;

#else

#createTable()

#end

#if ($entity.versioned)
#createHistoryTable()
#end

#end
#end
#if (!$singleEntity)
#parseFile("modules_project/instalSql/afterInstal.sql")
#end

drop procedure if exists addNewColumns;
delimiter //
create procedure addNewColumns()
begin
    declare continue handler for 1060 begin end;
#foreach( $entity in $application.entities )
#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)})
#set ($prevCol = "${tr.f_v($entity.prefix)}_id")
#foreach( $property in $entity.properties )
#set ($definition = "NOT_IMPLEMENTED-IN-ALTER") 
#if (${tr.f_v($property.dataType.name)} == "varchar")
#set ($definition = "VARCHAR($property.size)")
#elseif (${tr.f_v($property.dataType.name)} == "integer")
#set ($definition = "INT(11)")
#elseif (${tr.f_v($property.dataType.name)} == "decimal")
#set ($definition = "decimal(10,2)")
#elseif (${tr.f_v($property.dataType.name)} == "bool")
#set ($definition = "boolean")
#elseif (${tr.f_v($property.dataType.name)} == "text")
#set ($definition = "mediumtext")
#elseif (${tr.f_v($property.dataType.name)} == "date")
#set ($definition = "DATE")
#elseif (${tr.f_v($property.dataType.name)} == "datetime")
#set ($definition = "DATETIME")
#elseif (${tr.f_v($property.dataType.name)} == "email")
#set ($definition = "VARCHAR(255)")
#elseif (${tr.f_v($property.dataType.name)} == "code")
#set ($definition = "LONGTEXT")
#elseif (${tr.f_v($property.dataType.name)} == "password")
#set ($definition = "VARCHAR(2000)")
#elseif (${tr.f_v($property.dataType.name)} == "url")
#set ($definition = "VARCHAR(255)")
#elseif (${tr.f_v($property.dataType.name)} == "color")
#set ($definition = "VARCHAR(20)")
#end
#if ($definition == "NOT_IMPLEMENTED-IN-ALTER") -- #end ALTER TABLE `${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}` ADD COLUMN `${entity.prefix}_${tr.f_v($property.name)}` $definition AFTER $prevCol ;
#if ($definition != "NOT_IMPLEMENTED-IN-ALTER")
#set ($prevCol = "${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}")
#end
#end
#foreach( $relation in $entity.childRelations )
#if ($relation.name && $relation.name != "null")
ALTER TABLE `${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}` ADD COLUMN `${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id` int(11) default NULL AFTER $prevCol ;
#set ($prevCol = "${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id")
#else
ALTER TABLE `${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}` ADD COLUMN `${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id` int(11) default NULL AFTER $prevCol ;
#set ($prevCol = "${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id")
#end
#end
#end
#end
end//
delimiter ;
call addNewColumns();

