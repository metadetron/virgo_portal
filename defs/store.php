## <?php
			if (isset($this->${tr.f_v($entity.prefix)}_${tr.f_v($property.name)})) {
				$query .= " ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} = ? ,";
				$types .= "s";
				$values[] = $this->${tr.f_v($entity.prefix)}_${tr.f_v($property.name)};
			} else {
				$query .= " ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} = NULL ,";				
			}
## ?>
