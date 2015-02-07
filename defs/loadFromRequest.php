## <?php
	$tmpValue = null;
	$tmpValue = R('${tr.fV($entity.prefix)}_${tr.fV($property.name)}_' . $this->${entity.prefix}_id);
	if (!is_null($tmpValue)) {
#if ($property.dataType.name != 'TEXT' && $property.dataType.name != 'CODE')
		$tmpValue = trim($tmpValue);
#end		
#if ($property.dataType.name == 'VARCHAR')
		$tmpValue = preg_replace('/\s+/', ' ', $tmpValue);
#end		
		if ($tmpValue == "") {
#readSyncValues()
#if ($sync == "1") 
			$this->_${entity.prefix}_${tr.f_v($property.name)} = null;
#else
			$this->${entity.prefix}_${tr.f_v($property.name)} = null;
#end
		} else {
#readSyncValues()
#if ($sync == "1") 
			$this->_${entity.prefix}_${tr.f_v($property.name)} = $tmpValue;
#else
			$this->${entity.prefix}_${tr.f_v($property.name)} = $tmpValue;
#end
		}
	}
## ?>
