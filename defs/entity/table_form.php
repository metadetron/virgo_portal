		<script type="text/javascript">
			var ${tr.fV($entity.name)}ChildrenDivOpen = '';
			
			function childrenButtonClicked(clickedDivId) {
				var div = document.getElementById(clickedDivId);
				if (clickedDivId == ${tr.fV($entity.name)}ChildrenDivOpen) {
					div.style.display = 'none';
					${tr.fV($entity.name)}ChildrenDivOpen = '';
				} else {
					if (${tr.fV($entity.name)}ChildrenDivOpen != '') {
						document.getElementById(${tr.fV($entity.name)}ChildrenDivOpen).style.display = 'none';
					}
					div.style.display = 'block';
					${tr.fV($entity.name)}ChildrenDivOpen = clickedDivId;
				}
			}
			
			function copyIds(form) {
				var chcks = document.getElementsByTagName("input");
				var ids = form.ids;
				var firstOne = 1;
				for (i=0;i<chcks.length;i++) {
					if (chcks[i].name.match("^DELETE_\d*")) {
						if (chcks[i].checked == 1) {
							if (firstOne == 1) {
								firstOne = 0;
							} else {
								ids.value = ids.value + ",";
							}
							ids.value = ids.value + chcks[i].name.substring(7);
						}
					}
				}
				form.submit();
			}
			
			function nothingSelected(form) {
				var chcks = document.getElementsByTagName("input");
				for (i=0;i<chcks.length;i++) {
					if (chcks[i].name.match("^DELETE_\d*")) {
						if (chcks[i].checked == 1) {
							return false;
						}
					}
				}
				return true;
			}
			
			function checkAll(value) {
				var chcks = document.getElementsByTagName("input");
				for (i=0;i<chcks.length;i++) {
					if (chcks[i].name.match("^DELETE_\d*")) {
						chcks[i].checked = value;
					}
				}
			}
		</script>

## <?php		
## #foreach( $relation in $entity.parentRelations )
## #foreach( $subrelation in $relation.childEntity.childRelations )
##	include_once(/*$_SERVER[ "DOCUMENT_ROOT"]*/ PORTAL_PATH . "/components/com_${application.prefix}_${tr.f_v($subrelation.parentEntity.name)}/${application.prefix}_${tr.f_v($subrelation.parentEntity.name)}_class.php");
## #end	
## #end
## ?>
	<form method="post" style="display: inline;" action="" id="virgo_form_${tr.f_v($entity.name)}" name="virgo_form_${tr.f_v($entity.name)}" enctype="multipart/form-data">
#parse("defs/entity/params.php")
		<table class="data_table" cellpadding="0" cellspacing="0">
#parseFileWithStandard("modules_project/renderTableFormHeader/${tr.f_v($entity.name)}.php" "defs/entity/table_form_header.php")
<?php			
				$results${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->getRecordsToEdit();
				$idsToCorrect = $result${tr.FV($entity.name)}->getInvalidRecords();
				$index = 0;
#exectime_start("rows rendering")			
				foreach ($results${tr.FV($entity.name)} as $result${tr.FV($entity.name)}) {
					$index = $index + 1;
?>
#parseFileWithStandard("modules_project/renderTableFormRow/${tr.f_v($entity.name)}.php" "defs/entity/table_form_row.php")
<?php
				}
#exectime_end_echo("rows rendering")						
#foreach( $property in $entity.properties )
#exectime_echo($property.name)
#end
#exectime_echo("extra data")
?>		
				<tr>
					<td colspan="99" align="center">
						<input type="hidden" name="virgo_changed" value="N">
						<input type="hidden" name="virgo_validate_new" value="<?php echo R('virgo_validate_new', "N") ?>">
## #actionButtonSimple("StoreSelected" "'STORE'")
#actionButton("StoreSelected" "'STORE'" "" "" "" "" "this.form.virgo_changed.value = 'N';")

##						<input type="ubmi" class="button" value="'STORE'" onclick="this.form.virgo_action.value='StoreSelected'; this.form.id.value='<?php echo $result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id ?>'" onmousedown="this.className='button_pressed'" onmouseup="this.className='button'" onmouseout="this.className='button'">
<?php
#permissionRestrictedBlockBegin("add")
?>						
##						<div class="button_wrapper"><input type="button" class="button" value="<?php echo T('ADD') ?>" id="add_button" style="display: <?php echo R('virgo_validate_new', "N") == "N" ? 'inline' : 'none' ?>" onclick="this.form.virgo_validate_new.value='Y'; document.getElementById('remove_button').style.display='inline'; this.style.display='none';" onmousedown="this.className='button_pressed'" onmouseup="this.className='button'" onmouseout="this.className='button'"><div class="button_right" style="display: <?php echo R('virgo_validate_new', "N") == "N" ? 'none' : 'inline-block' ?>"></div></div>
##						<div class="button_wrapper"><input type="button" class="button" value="<?php echo T('REMOVE') ?>" id="remove_button" style="display: <?php echo R('virgo_validate_new', "N") == "N" ? 'none' : 'inline' ?>" onclick="this.form.virgo_validate_new.value='N'; document.getElementById('virgo_tr_id_0').style.display='none'; document.getElementById('add_button').style.display='inline'; this.style.display='none';" onmousedown="this.className='button_pressed'" onmouseup="this.className='button'" onmouseout="this.className='button'"><div class="button_right" style="display: <?php echo R('virgo_validate_new', "N") == "N" ? 'inline-block' : 'none' ?>"></div></div>
						<div id="add_button" class="button_wrapper" style="display: <?php echo R('virgo_validate_new', "N") == "N" ? 'inline-block' : 'none' ?>" ><input type="button" class="button" value="<?php echo T('ADD') ?>" onclick="this.form.virgo_validate_new.value='Y'; document.getElementById('virgo_tr_id_0').style.display='table-row'; document.getElementById('remove_button').style.display='inline-block'; document.getElementById('add_button').style.display='none';" onmousedown="this.className='button_pressed'" onmouseup="this.className='button'" onmouseout="this.className='button'"><div class="button_right"></div></div>
						<div id="remove_button" class="button_wrapper" style="display: <?php echo R('virgo_validate_new', "N") == "N" ? 'none' : 'inline-block' ?>" ><input type="button" class="button" value="<?php echo T('REMOVE') ?>" onclick="this.form.virgo_validate_new.value='N'; document.getElementById('virgo_tr_id_0').style.display='none'; document.getElementById('add_button').style.display='inline-block'; document.getElementById('remove_button').style.display='none';" onmousedown="this.className='button_pressed'" onmouseup="this.className='button'" onmouseout="this.className='button'"><div class="button_right"></div></div>
<?php
#permissionRestrictedBlockEnd()
?>						
#actionButtonSimple("Close" "'CLOSE'")
##						<input type="ubmi" class="button" value="'CLOSE'" onclick="this.form.virgo_action.value='Close'; if (document.getElementById('virgo_form_${tr.fV($entity.name)}').virgo_changed.value == 'T') return confirm('ARE_YOU_SURE_YOU_WANT_DISCARD');" onmousedown="this.className='button_pressed'" onmouseup="this.className='button'" onmouseout="this.className='button'">
					</td>
				</tr>
		</table>
	</form>
