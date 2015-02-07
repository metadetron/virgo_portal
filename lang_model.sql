----- lang_model.sql -----
#foreach( $entity in $application.entities )
## #if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
INSERT INTO prt_translations (trn_lng_id, trn_token, trn_text) SELECT lng_id, '${tr.F_V($entity.name)}', '${tr.Fv($entity.name)}' FROM prt_languages WHERE lng_default = 1;
INSERT INTO prt_translations (trn_lng_id, trn_token, trn_text) SELECT lng_id, '${tr.F_V($entity.namePlural)}', '${tr.Fv($entity.namePlural)}' FROM prt_languages WHERE lng_default = 1;
#foreach( $property in $entity.properties )
INSERT INTO prt_translations (trn_lng_id, trn_token, trn_text) SELECT lng_id, '${tr.F_V($property.name)}', '${tr.Fv($property.name)}' FROM prt_languages WHERE lng_default = 1;
#end

#foreach( $relation in $entity.parentRelations )
#if ($relation.name)
INSERT INTO prt_translations (trn_lng_id, trn_token, trn_text) SELECT lng_id, '${tr.F_V($relation.name)}', '${tr.Fv($relation.name)}' FROM prt_languages WHERE lng_default = 1;
#end
#end
## #end
#end

