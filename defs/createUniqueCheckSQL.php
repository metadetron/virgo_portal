## <?php
			$uniqnessWhere = $uniqnessWhere . ' AND UPPER(${entity.prefix}_${tr.f_v($property.name)}) = UPPER(?) ';
			$types .= "s";
			$values[] = $this->${entity.prefix}_${tr.f_v($property.name)};
## ?>
