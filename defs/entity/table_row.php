#############################################################################################
################################### a single row in a table #################################
<?php
			if (P('form_only') != "4" || (P('chessboard_width') != 0 && $index % P('chessboard_width') == 1)) {
				if (is_null($firstRowId)) {
					$firstRowId = $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'];
				}
				$displayClass = ' displayClass ';
				$tmpContextId = virgo${tr.FV($entity.name)}::getContextId();
				if (is_null($tmpContextId)) {
					$forceContextOnFirstRow = P('force_context_on_first_row', "1");
					if ($forceContextOnFirstRow == "1") {
						virgo${tr.FV($entity.name)}::setContextId($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'], false);
						$tmpContextId = $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'];
					}
				}
				if (isset($tmpContextId) && $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] == $tmpContextId) {
					if (P('form_only') != "4") {
						$contextClass = ' contextClass ';
					} else {
						$contextClass = '';
					}
					$contextRowIdInTable = $tmpContextId;
				} else {
					$contextClass = '';
				}
?>
			<tr 
				id="<?php echo $this->getId() ?>_<?php echo isset($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id']) ? $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] : "" ?>" 
				class="<?php echo (P('form_only') == "4" ? "data_table_chessboard" : ($index % 2 == 0 ? "data_table_even" : "data_table_odd")) ?> <?php echo $contextClass ?>
## #renderStandardTooltip()
#parseFileWithStandardText("modules_project/displayClass/${tr.f_v($entity.name)}.php" ' <? echo $displayClass ?> ')
#if ($entity.dictionary)
${tr.f_v($entity.name)}_<?php echo isset($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id']) ? $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id'] : "" ?>
#end
#foreach( $relation in $entity.childRelations )
#if ($relation.parentEntity.dictionary)
<?php
				if (class_exists('${tr.f_v($application.name)}\virgo${tr.FV($relation.parentEntity.name)}') && P('show_view_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
?>
#if ($relation.name)
 ${tr.f_v($relation.parentEntity.name)}_<?php echo isset($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id']) ? $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id'] : "" ?>
#else
 ${tr.f_v($relation.parentEntity.name)}_<?php echo isset($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id']) ? $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id'] : "" ?>
#end
<?php
				}
?>
#end
#end
			" 
			>
<?php
			} 
?>
<?php
			if (P('form_only') == "4") {
				$width = P('chessboard_cell_width');
				$height = P('chessboard_cell_height');
				if ($width != "0") {
					$styleWidth = " width: " . $width . "px; ";
				}
				if ($height != "0") {
					$styleHeight = " height: " . $height . "px; ";
				}
				$style = "";
				if (!is_null($styleWidth) || !is_null($styleHeight)) {
					$style = " style='" . $styleWidth . $styleHeight . "' ";
				}
?>
				<td 
					class="chessboard" 
					valign="top" 
					<?php echo $style ?>
				>
#parseFileWithStandard("modules_project/renderTableCell/${tr.f_v($entity.name)}.php" "defs/entity/table_cell.php")
				</td>
<?php
			} else {
?>
#parse("defs/entity/table_cell.php") 
<?php
			}
?>
<?php
			if (P('form_only') != "4" || (P('chessboard_width') != 0 && $index % P('chessboard_width') == 0)) {
?>
			</tr>
<?php
			} 
?>
################################### a single row in a table #################################
#############################################################################################

