<?php
	$recordHistory = $result${tr.FV($entity.name)}->getVirgoRecordHistory();
?>
	<input type="hidden" name="history_id" id="history_id">
	<table class="history data_table">
		<tr class="data_table_header">
			<td align="center">
<?php
				echo JText::_('REVISION');
?>
			</td>
			<td align="center">
<?php
				echo JText::_('IP');
?>
			</td>
			<td align="center">
<?php
				echo JText::_('USERNAME');
?>
			</td>
			<td align="center">
<?php
				echo JText::_('USER_ID');
?>
			</td>
			<td align="center">
<?php
				echo JText::_('TIMESTAMP');
?>
			</td>
			<td>
			</td>
		</tr>
<?php
	foreach ($recordHistory as $recordHistoryEntry) {
?>
		<tr>
			<td align="center">
<?php
				echo $recordHistoryEntry->revision;
?>
			</td>
			<td align="center">
<?php
				echo $recordHistoryEntry->ip;
?>
			</td>
			<td align="center">
<?php
				echo $recordHistoryEntry->username;
?>
			</td>
			<td align="center">
<?php
				echo $recordHistoryEntry->user_id;
?>
			</td>
			<td align="center">
<?php
				echo $recordHistoryEntry->timestamp;
?>
			</td>
			<td class="actions">
#actionButton("ShowRevision" "'CHANGE_DETAILS'" "" "" "" "" "this.form.history_id.value='<?php echo $recordHistoryEntry->id ?>';")
			</td>
		</tr>
<?php
	}
?>
	</table>

