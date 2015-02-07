						<input 
							class="inputbox readonly" 
							id="${tr.fV($property.name)}" 
							name="${tr.fV($property.name)}"
#if ($property.dataType.name == 'VARCHAR' || $property.dataType.name == 'DATE' || $property.dataType.name == 'DATETIME')
							style="border: yellow 1 solid;" 
#else
							style="border: yellow 1 solid; text-align: right;"
#end
							value="<?php echo htmlentities($result${tr.FV($entity.name)}->get${tr.FV($property.name)}(), ENT_QUOTES, "UTF-8") ?>" 
							readonly="readonly"
						>

