<?php
	$menu =& JSite::getMenu();
	$currentItem = $menu->getActive();
## tylko jesli aktualna pozycja menu nie nalezy do typu menu w pozycji menu (na gorze strony)
	$query = "
SELECT 
	COUNT(*) as ilosc 
FROM 
	jos_modules modul_w_pozycji_menu
WHERE 
	modul_w_pozycji_menu.position = 'menu'
	AND modul_w_pozycji_menu.published = 1
	AND SUBSTRING(modul_w_pozycji_menu.params, 10, INSTR(modul_w_pozycji_menu.params, '\n') - 10) = '" . $currentItem->menutype . "'
	";
#selectResult()
	if ($result[0] == 0) {
?>
<form id="virgo_tabs_form" action="/index.php">
<div id="tabs">
<ul class="menu top">
#parse("defs/versions.php")
#set ($counter = 0)
#foreach ($version in $versions.versions)
#if ($counter == 0) 
#set ($current = $version.revision)
#end
#set ($counter = $counter + 1) 
#end
<?php
		$items = $menu->getItems("menutype", $currentItem->menutype);
		$currentTabDisplayed = false;
		$entity2menu = array();
		foreach ($items as $item) { 
			$entity2menu[$item->link] = $item->id;
		}
		foreach ($items as $item) { 
			$entityName = substr($item->link, 25);
			$clickable = true;
			if (!is_null($parents[$entityName])) {
				foreach ($parents[$entityName] as $parent) {
					if ($clickable) {
						if (isset($entity2menu["index.php?option=com_${application.prefix}_" . $parent])) {
							$tmpItem = $entity2menu["index.php?option=com_${application.prefix}_" . $parent];
							$tmpClassName = "virgo" . $entityNames[$parent];
							$tmpClass = new $tmpClassName();
							$tmpCtxId = $tmpClass->getRemoteContextId($tmpItem);
							if (is_null($tmpCtxId)) {
								$clickable = false;
								break;
							}
						}
					}
				}
			}
?>
			<li <?php echo $item->id == $currentItem->id ? 'class="active inlineBlock" id="current"' : '' ?> class="inlineBlock <?php echo $clickable ? '' : 'disabled' ?>"
<?php
			if ($clickable) {
?>			
			 onclick="var tmpForm = document.getElementById('virgo_tabs_form'); tmpForm.action='<?php echo $live_site ?>/index.php/<?php echo $item->alias ?>'; tmpForm.submit();"
<?php
			}
?>			
			 >
				<span title="virgo version: $current">
<?php echo $item->name ?>				
				</span>
			</li>
<?php		
			if ($item->id == $currentItem->id) {
				$currentTabDisplayed = true;
			}
		}
?>
</ul>
</div>
</form>
<?php
	}
?>
