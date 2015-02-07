#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
----- ${application.name}_copy.sh -----
#parse("modules_project/copy_local.sh") 
## echo Sending files for component com_${application.prefix}_${tr.f_v($entity.name)} 
#set ($first = "1")
#foreach( $entity in $application.entities )
cp output/${application.prefix}_${tr.f_v($entity.name)}/${application.prefix}_${tr.f_v($entity.name)}_class.php ${rootDirName}/components/com_${application.prefix}_${tr.f_v($entity.name)}
cp output/${application.prefix}_${tr.f_v($entity.name)}/${application.prefix}_${tr.f_v($entity.name)}.php ${rootDirName}/components/com_${application.prefix}_${tr.f_v($entity.name)}
cp output/${application.prefix}_${tr.f_v($entity.name)}/ContentAppender${tr.FV($entity.name)}.php ${rootDirName}/components/com_${application.prefix}_${tr.f_v($entity.name)}
cp output/${application.prefix}_${tr.f_v($entity.name)}/ContentAppenderCSV${tr.FV($entity.name)}.php ${rootDirName}/components/com_${application.prefix}_${tr.f_v($entity.name)}
cp output/${application.prefix}_${tr.f_v($entity.name)}/${tr.f_v($application.name)}.css ${rootDirName}/components/com_${application.prefix}_${tr.f_v($entity.name)}
#end
#end

