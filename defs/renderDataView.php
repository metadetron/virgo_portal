## NIE ZMIENIAJ TEGO NA span BO WTEDY BEDA GUBIONE WARTOSCI W PRZYPADKU,
## GDY NA FORMULARZU JEST POLE readonly!!!
## Dlatego dodaje hiddena
## No... ale chyba hiddena dodałem z wujowym name :-/
						<span class="inputbox readonly uneditable-input">
#if ($property.dataType.name == 'DECIMAL')			
							<?php echo number_format($result${tr.FV($entity.name)}->get${tr.FV($property.name)}(), 2, ',', ' ') ?>
#else				
							<?php echo htmlentities($result${tr.FV($entity.name)}->get${tr.FV($property.name)}(), ENT_QUOTES, "UTF-8") ?> 
#end
						&nbsp;</span>
						<input type="hidden" id="${tr.fV($property.name)}" name="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>" value="<?php echo htmlentities($result${tr.FV($entity.name)}->get${tr.FV($property.name)}(), ENT_QUOTES, "UTF-8") ?>"> 

