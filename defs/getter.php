#readSyncValues()
#if ($sync == "1") 
			return $this->_${entity.prefix}_${tr.f_v($property.name)};
#else
			return $this->${entity.prefix}_${tr.f_v($property.name)};
#end
