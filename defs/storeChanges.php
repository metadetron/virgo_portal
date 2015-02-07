## <?php
				$colNames = $colNames . ", ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}";
				$values = $values . ", " . (is_null($objectToStore->get${tr.FV($property.name)}()) ? "null" : "'" . QE($objectToStore->get${tr.FV($property.name)}()) . "'");
## ?>
