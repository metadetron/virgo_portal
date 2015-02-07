## <?php
			if (isset($this->${tr.f_v($entity.prefix)}_${tr.f_v($property.name)})) {
				$query .= " ? ,";
				$types .= "s";
				$values[] = $this->${tr.f_v($entity.prefix)}_${tr.f_v($property.name)};
			} else {
				$query .= " NULL ,";				
			}
## ?>
