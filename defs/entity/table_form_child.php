		<script type="text/javascript">
			function checkAll(value) {
				var chcks = document.getElementsByTagName("input");
				for (i=0;i<chcks.length;i++) {
					if (chcks[i].name.match("^DELETE_\d*")) {
						chcks[i].checked = value;
					}
				}
			}
		</script>

<?php
//	$componentParams${tr.FV($relationChild.childEntity.name)} = &JComponentHelper::getParams('com_${tr.f_v($application.prefix)}_${tr.f_v($relationChild.childEntity.name)}');
#foreach( $relation in $relationChild.childEntity.parentRelations )
#foreach( $subrelation in $relationChild.childEntity.childRelations )
	include_once(/*$_SERVER[ "DOCUMENT_ROOT"]*/ PORTAL_PATH . "/components/com_${application.prefix}_${tr.f_v($subrelation.parentEntity.name)}/${application.prefix}_${tr.f_v($subrelation.parentEntity.name)}_class.php");
#end	
#end
?>
		<table class="data_table" cellpadding="0" cellspacing="0">
#parseFileWithStandard("modules_project/renderTableFormChildHeader/${tr.f_v($relationChild.childEntity.name)}.php" "defs/entity/table_form_child_header.php")
<?php			
				$tmp${tr.FV($relationChild.childEntity.name)} = new ${tr.f_v($application.name)}\virgo${tr.FV($relationChild.childEntity.name)}();
#if ($relationChild.name)
#set ($kreska = "_")
#else
#set ($kreska = "")
#end
				$results${tr.FV($relationChild.childEntity.name)} = $tmp${tr.FV($relationChild.childEntity.name)}->selectAll(' ${tr.f_v($relationChild.childEntity.prefix)}_${tr.f_v($entity.prefix)}_${tr.f_v($relationChild.name)}${kreska}id = ' . $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id);
				$idsToCorrect = $tmp${tr.FV($relationChild.childEntity.name)}->getInvalidRecords();
				$index = 0;
				foreach ($results${tr.FV($relationChild.childEntity.name)} as $result${tr.FV($relationChild.childEntity.name)}) {
					$index = $index + 1;
?>
#parseFileWithStandard("modules_project/renderTableFormChildRow/${tr.f_v($relationChild.childEntity.name)}.php" "defs/entity/table_form_child_row.php")
<?php
				}
				$result${tr.FV($relationChild.childEntity.name)} = array();
?>		
#parseFileWithStandard("modules_project/renderTableFormChildRow/${tr.f_v($relationChild.childEntity.name)}.php" "defs/entity/table_form_child_row.php")
			</table>
<?php
				$tmp${tr.FV($relationChild.childEntity.name)}->setInvalidRecords(null);
?>
