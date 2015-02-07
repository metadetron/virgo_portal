		<tr>
			<td align="right" class="data_table_header">
				<?php echo JText::_('${tr.F_V($property.name)}') ?>
			</td>
<?php
				$val = null;
				if ($afterRevision > 1) {
					$revision = $afterRevision - 1;
					$query = "
SELECT 
  ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} as val 
FROM 
  ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} 
WHERE 
  ${tr.f_v($entity.prefix)}_id = $recordId 
  AND revision = ( 
      SELECT 
        MAX(revision) 
      FROM 
          ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)}
      WHERE 
        revision <= $revision
        AND ${tr.f_v($entity.prefix)}_id = $recordId 
        AND (
          ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NOT NULL 
          OR INSTR(nullified_properties, '${tr.f_v($property.name)}') > 0
        )
  ) 	
";
#selectResult()
					$val = $result[0];
				}
				$changed = false;
				$newVal = $afterResult['${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}'];
				if (is_null($newVal)) {
					if (strstr($afterResult['nullified_properties'], "${tr.f_v($property.name)}")) {
						$changed = true;
					} else {
						$newVal = $val;
					}
				} else {
					$changed = true;
				}
?>
			<td align="center">
						<input 
							class="inputbox readonly" size="57" 
							value="<?php echo htmlentities($val, ENT_QUOTES, "UTF-8") ?>" 
							readonly="readonly"
						>
			</td>
			<td align="center">
						<input 
							class="inputbox readonly" size="57"
							<?php echo $changed ? "style='color: red; background-color: #FF9; border: 1px solid red;'" : "" ?>
							value="<?php echo htmlentities($newVal, ENT_QUOTES, "UTF-8") ?>" 
							readonly="readonly"
						>
			</td>
		</tr>

