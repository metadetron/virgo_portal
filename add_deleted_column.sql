----- add_deleted_column.sql -----
#foreach( $entity in $application.entities )
#if (!$singleEntity) 
#if ($entity.dictionary)
ALTER TABLE ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ADD COLUMN ${tr.f_v($entity.prefix)}_virgo_deleted boolean;
#end
#end
#end


