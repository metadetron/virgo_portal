<?php
	$query = "SELECT id FROM ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = $recordId AND revision = " . ($afterRevision - 1);
#selectResult()
	if (sizeof($result) > 0) {
		$previousHistoryEntryId = $result[0];
	} else {
		$previousHistoryEntryId = null;
	}
	$query = "SELECT id FROM ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = $recordId AND revision = " . ($afterRevision + 1);
#selectResult()
	if (sizeof($result) > 0) {
		$nextHistoryEntryId = $result[0];
	} else {
		$nextHistoryEntryId = null;
	}
?>

