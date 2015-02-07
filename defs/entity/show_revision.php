<?php
	$afterChangeRevisionId = R('history_id');
	$query = "SELECT * FROM ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} WHERE id = $afterChangeRevisionId";
	$db =& JFactory::getDBO();	
	$db->setQuery($query);
	$afterResult = $db->loadAssoc();
	$recordId = $afterResult['${tr.f_v($entity.prefix)}_id'];
	$afterRevision = $afterResult['revision'];
	if ($afterRevision > 1) {
		$query = "SELECT revision, timestamp, ip, username, user_id FROM ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = $recordId AND revision = " . ($afterRevision - 1);
		$db->setQuery($query);
		$result = $db->loadAssoc();
	}
?>
<style type="text/css">
div.form_view table {
	background: white;
}
div.form_view td {
	background: #EEE;
}
div.form_view td.meta {
	color: grey;
}
div.form_view td span.header {
	text-transform: capitalize;
}
div.form_view td.data_table_header {
	background: lightgrey;
}
</style>
<div class="form_view history">
	<table width="100%">
		<tr>
			<td width="20%" class="data_table_header"> 
			</td>
			<td width="40%" align="center" class="data_table_header">
				<span class="header"><?php echo JText::_('BEFORE') ?></span>
			</td>
			<td width="40%" align="center" class="data_table_header">
				<span class="header"><?php echo JText::_('AFTER') ?></span>
			</td>
		</tr>
#revisionField('revision')	
#revisionField('timestamp')	
#revisionField('ip')	
#revisionField('username')	
#revisionField('user_id')
#foreach ($property in $entity.properties)
#parseFileWithStandard("defs/dataTypes/php/showChanges/${tr.f_v($property.dataType.name)}" "defs/showChanges.php")			
#end
#foreach ($relation in $entity.childRelations)
		<tr>
			<td align="right" class="data_table_header">
				<?php echo JText::_('${tr.F_V($relation.parentEntity.name)}') ?> <?php echo JText::_('${tr.F_V($relation.name)}') ?>
			</td>
<?php
				$val = null;
				if ($afterRevision > 1) {
					$revision = $afterRevision - 1;
					$query = "
#if ($relation.name)					
SELECT ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id as val
#else
SELECT ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id as val
#end
FROM ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} 
WHERE ${tr.f_v($entity.prefix)}_id = $recordId
AND revision = (
	SELECT MAX(revision) 
	FROM ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} 
	WHERE revision <= $revision
	AND ${tr.f_v($entity.prefix)}_id = $recordId
#if ($relation.name)					
	AND ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id IS NOT NULL)
#else
	AND ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id IS NOT NULL)
#end
";
#selectResult()
					$val = $result[0];
				}
				$valTitleParent = new virgo${tr.FV($relation.parentEntity.name)}($val); 
				$valTitle = $valTitleParent->getVirgoTitle(); 
				$changed = false;
#if ($relation.name)				
				$newVal = $afterResult['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id'];
#else				
				$newVal = $afterResult['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id'];
#end				
## W przypadku relacji zasada jest latwa: pusta wartosc to 0 a NULL to brak zmiany.
				if (is_null($newVal)) {
					$newVal = $val;
				} else {
					$changed = true;
				}
				$newValTitleParent = new virgo${tr.FV($relation.parentEntity.name)}($newVal); 
				$newValTitle = $newValTitleParent->getVirgoTitle(); 
?>
			<td align="center">
						<input 
							class="inputbox readonly" size="57" 
							value="<?php echo htmlentities($val . " " . $valTitle, ENT_QUOTES, "UTF-8") ?>" 
							readonly="readonly"
						>
			</td>
			<td align="center">
						<input 
							class="inputbox readonly"  size="57"
							<?php echo $changed ? "style='color: red; background-color: #FF9; border: 1px solid red;'" : "" ?>
							value="<?php echo htmlentities($newVal . " " . $newValTitle, ENT_QUOTES, "UTF-8") ?>" 
							readonly="readonly"
						>
			</td>
		</tr>
#end
	</table>
</div>
#parseFile("defs/checkChanges.php")			
<input type="hidden" name="history_id" id="history_id">

