#readSyncValues()
#if ($sync == "1") 
			$this->_${entity.prefix}_${tr.f_v($property.name)} = $val;
#else
			$this->${entity.prefix}_${tr.f_v($property.name)} = $val;
#end
