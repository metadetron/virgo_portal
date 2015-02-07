<?php
if (P('hide_audit', '0') == '0') {
	$showFieldset = 0;
	if ($result${tr.FV($entity.name)}->getDateCreated()) {
		if ($result${tr.FV($entity.name)}->getUsrCreatedId() == 0) {
			$username = "anonymous";
		} else {
			$createdByUser = new virgoUser((int)$result${tr.FV($entity.name)}->getUsrCreatedId());
			$username = $createdByUser->getUsername();
		}
		$createdOn = $result${tr.FV($entity.name)}->getDateCreated();
		$showFieldset = 1;
?>
	 <fieldset class="audit">
		<legend>#text("'AUDIT_DATA'"):</legend>
			<ul>
				<li>
					<span align="right" nowrap class="fieldlabel">#text("'CREATED_BY'")</span>
					<span class="readonly"><b><?php echo $username ?></b></span>
					<input type="hidden" id="usr_created_id" name="usr_created_id" value="<?php echo $result${tr.FV($entity.name)}->getUsrCreatedId() ?>"	>
				</li>
				<li>
					<span align="right" nowrap class="fieldlabel">#text("'CREATION_DATE'")</span>
					<span class="readonly"><i><?php echo $createdOn ?></i></span>
					<input type="hidden" id="date_created" name="date_created" value="<?php echo $result${tr.FV($entity.name)}->getDateCreated() ?>"	>
				</li>
<?php
	}
?>
<?php
	if ($result${tr.FV($entity.name)}->getDateModified()) {
		if ($result${tr.FV($entity.name)}->getUsrModifiedId() == 0) {
			$username = "anonymous";
		} else {
			$modifiedByUser = new virgoUser((int)$result${tr.FV($entity.name)}->getUsrModifiedId());
			$username = $modifiedByUser->getUsername();
		}
		$modifiedOn = $result${tr.FV($entity.name)}->getDateModified();
?>
				<li>
					<span align="right" nowrap class="fieldlabel">#text("'MODIFIED_BY'")</span>
					<span class="readonly"><b><?php echo $username ?></b></span>
					<input type="hidden" id="usr_modified_id" name="usr_modified_id" value="<?php echo $result${tr.FV($entity.name)}->getUsrModifiedId() ?>"	>
				</li>
				<li>
					<span align="right" nowrap class="fieldlabel">#text("'MODIFICATION_DATE'")</span>
					<span class="readonly"><i><?php echo $modifiedOn ?></i></span>
					<input type="hidden" id="date_modified" name="date_modified" value="<?php echo $result${tr.FV($entity.name)}->getDateModified() ?>"	>
				</li>
<?php
	}
	if ($showFieldset == 1) {
?>
		</ul>
</fieldset>
<?php
	}
}
?>

