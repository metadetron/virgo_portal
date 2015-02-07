#if ($singleEntity)
----- ${application.name}_rename_table.sql -----
#foreach( $entity in $application.entities )
#if ($singleEntity == ${tr.f_v($entity.name)})



#end
#end
#end


