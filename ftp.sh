#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
----- full_update_${application.name}.sh -----
#parse("modules_project/ftp.sh") 
cd output
## echo Sending files for component com_${application.prefix}_${tr.f_v($entity.name)} 
ftp -p -n -v $HOST <<EOD
quote USER $USERNAME
quote PASS $PASSWORD
#set ($first = "1")
#foreach( $entity in $application.entities )
#if ($first == "1")
cd ${rootDirName}/components/com_${application.prefix}_${tr.f_v($entity.name)}
lcd ${application.prefix}_${tr.f_v($entity.name)}
#set ($first = "0")
#else
cd ../com_${application.prefix}_${tr.f_v($entity.name)}
lcd ../${application.prefix}_${tr.f_v($entity.name)}
#end
put ${application.prefix}_${tr.f_v($entity.name)}_class.php
put ContentAppender${tr.FV($entity.name)}.php
put ContentAppenderCSV${tr.FV($entity.name)}.php
put ${application.prefix}_${tr.f_v($entity.name)}.php
put ${tr.f_v($application.name)}.css
cd ../../administrator/components/com_${application.prefix}_${tr.f_v($entity.name)}
put config.xml
cd ../../../components/com_${application.prefix}_${tr.f_v($entity.name)}
#end
quit
EOD
cd ..
exit 0

----- code_update_${application.name}.sh -----
#parse("modules_project/ftp.sh") 
cd output
## echo Sending files for component com_${application.prefix}_${tr.f_v($entity.name)} 
ftp -p -n -v $HOST <<EOD
quote USER $USERNAME
quote PASS $PASSWORD
#set ($first = "1")
#foreach( $entity in $application.entities )
#if ($first == "1")
cd ${rootDirName}/components/com_${application.prefix}_${tr.f_v($entity.name)}
lcd ${application.prefix}_${tr.f_v($entity.name)}
#set ($first = "0")
#else
cd ../com_${application.prefix}_${tr.f_v($entity.name)}
lcd ../${application.prefix}_${tr.f_v($entity.name)}
#end
put ${application.prefix}_${tr.f_v($entity.name)}_class.php
put ${application.prefix}_${tr.f_v($entity.name)}.php
#end
quit
EOD
cd ..
exit 0

----- lang_update_${application.name}.sh -----
#parse("modules_project/ftp.sh") 
cd output
## echo Sending files for component com_${application.prefix}_${tr.f_v($entity.name)} 
ftp -p -n -v $HOST <<EOD
quote USER $USERNAME
quote PASS $PASSWORD
#foreach($lang in ["pl-PL", "en-GB"])
cd ${rootDirName}/language/${lang}
#foreach( $entity in $application.entities )
lcd ${application.prefix}_${tr.f_v($entity.name)}/languages
put ${lang}.com_${application.prefix}_${tr.f_v($entity.name)}.ini
lcd ../..
#end
cd ../../../../../..
#end
quit
EOD
cd ..
exit 0
#end

