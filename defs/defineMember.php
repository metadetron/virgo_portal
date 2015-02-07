#readSyncValues()
#if ($sync == "1") 
		#if ($hideSetters == "TRUE") private #else var #end $_${entity.prefix}_${tr.f_v($property.name)} = null;
#else
		#if ($hideSetters == "TRUE") private #else var #end $${entity.prefix}_${tr.f_v($property.name)} = null;
#end

