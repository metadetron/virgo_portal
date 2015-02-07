## <?php
#virgoTitlePropertyStandard($entity)
		$ret = $this->${entity.prefix}_${tr.f_v($virgoTitleProperty)};
		$len = strlen($ret);
		if ($len > 30) {
			$ret = substr($ret, 0, 20) . "... (" . $len . ") " . substr($ret, $len - 7, 7);
		}
		return $ret;
## ?>
