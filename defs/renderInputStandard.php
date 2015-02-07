#getParamValue($property.customProperty "Set")
#if ($virgoWartosc != "")
						<select
							class="inputbox" 
							id="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>" 
							name="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>"
						>
#if (!$property.obligatory)
<?php
	if (P('show_form_${tr.f_v($property.name)}_obligatory', "0") == "0") {
?>
							<option value=""></option>
<?php
	}
?>							
#end
#foreach($number in $virgoWartosc.split("#"))
#set ($sTmpCount = 0)
#foreach($sNumber in $number.split("-"))
#if ($sTmpCount == 1)
## w przyszlosci dodac do languages te etykietki!
#set ($sOptionLabel = ${tr.F_V($sNumber)})
#else
#set ($sOptionValue = $sNumber)
#set ($sOptionLabel = $sNumber)
#end
#set ($sTmpCount = $sTmpCount + 1)
#end
<option value="$sOptionValue" <?php echo $result${tr.FV($property.entity.name)}->get${tr.FV($property.name)}() == $sOptionValue ? " selected='selected' " : "" ?>><?php echo T('$sOptionLabel') ?></option>
#end
						</select>
#else
#if ($property.entity.dictionary)
#set ($fieldSize = 30)
#else
#set ($fieldSize = 50)
#end
#getParamValue($property.customProperty "FieldSize")
#if ($virgoWartosc != "")
#set ($fieldSize = $virgoWartosc)
#end
#set ($inputType = "text")
#if ($property.dataType.name == 'INTEGER' || $property.dataType.name == 'DECIMAL')
#set ($inputType = "number")
#end
						<input 
							class="inputbox #if ($property.obligatory) obligatory #else <?php echo P('show_form_${tr.f_v($property.name)}_obligatory', "0") == "1" ? " obligatory " : "" ?> #end #if ($property.size < 11) short #elseif ($property.size < 2000) medium #else long #end #if ($property.dataType.name == 'EMAIL' || $property.dataType.name == 'URL')
 medium #end" 
							type="$inputType"
							id="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>" 
							name="${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>"
#if ($property.dataType.name == 'VARCHAR' || $property.dataType.name == 'EMAIL' || $property.dataType.name == 'URL')
##							style="border: yellow 1 solid;" 
#if ($property.size && $property.size > 0)
							maxlength="$property.size"
#if ($property.size < 50 && $property.size > 0)
#if ($fieldSize == 50)
							size="$property.size" 
#else
							size="$fieldSize" 
#end							
#else
							size="$fieldSize" 
#end
#else
							size="$fieldSize" 
#end
#else
							style="border: yellow 1 solid; text-align: right;" 
## bylo 10 wstawilem 15 zeby sie miescila data z godzina							
							size="15"
#end
							value="<?php echo htmlentities($result${tr.FV($property.entity.name)}->get${tr.FV($property.name)}(), ENT_QUOTES, "UTF-8") ?>" 
							onchange="this.form.virgo_changed.value='T'"
#if ($property.entity.name == 'system parameter' && $property.name == 'name')
<?php
	if (!is_null($result${tr.FV($property.entity.name)}->getId())) {
?>
							readonly="readonly"
<?php
	}
?>
#end							
<?php
	if (isset($tabIndex) && $tabIndex > 0) {
?>
							tabindex="<?php echo $tabIndex ?>"
<?php
	}
	$hint = TE('HINT_${tr.F_V($property.entity.name)}_${tr.F_V($property.name)}');
	if (isset($hint)) {
?>
							title="<?php echo $hint ?>"
<?php
	}
?>
						>
<?php
	if (isset($hint)) {
?>						
<script type="text/javascript">
#set ($hash = '#')
$('${hash}${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>').qtip({position: {
		my: 'left center',
		at: 'right center'
	}});
</script>						
<?php
	}
?>						
#end						
#if (!$firstFieldName || $firstFieldName == "")
#set ($firstFieldName = "${tr.fV($property.entity.prefix)}_${tr.fV($property.name)}_<?php echo $result${tr.FV($property.entity.name)}->getId() ?>")
#end
