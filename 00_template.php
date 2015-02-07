#parseFile("modules_project/language.vm")
#readParameters()
#set ($hash = '#')
#set ($dollar = '$')
#foreach( $entity in $application.entities )
#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
$extraFilesInfo.clear()
#if (!$entity.external)
----- portlets\\${tr.f_v($application.name)}\\virgo${tr.FV($entity.name)}\\content.php -----
#set ($calendar_rendered = 'false')
<?php
	defined( '_INDEX_PORTAL' ) or die( 'Access denied.' );
	if (preg_match("/.*.metadetron.com/i", $_SERVER["SERVER_NAME"])) {
	    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	} 
	ini_set('display_errors', 1);

	use portal\virgoRole;
	use portal\virgoPage;
	use portal\virgoUser;
	use portal\virgoPortletObject;
#if	($application.name != 'portal' || $entity.name != 'role')
#if	($application.name != 'portal' || $entity.name != 'page')
#if	($application.name != 'portal' || $entity.name != 'user')
#if	($application.name != 'portal' || $entity.name != 'portlet object')
	use ${tr.f_v($application.name)}\virgo${tr.FV($entity.name)};
#end
#end
#end
#end

//	setlocale(LC_ALL, '$messages.LOCALE');
##	include_once("controller.php"); 
#foreach( $relation in $entity.childRelations )
#if ($relation.parentEntity.external)
#set ($appName = ${tr.f_v($relation.parentEntity.properties.get(0).name)})
#else
#set ($appName = ${tr.f_v($application.name)})
#end
//	if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php')) require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php');
#end			
	$componentParams = null; //&JComponentHelper::getParams('com_${application.prefix}_${tr.f_v($entity.name)}');
	$underConstruction = false; //$componentParams->get('under_construction');
	$underConstructionAllowedUser = ''; //$componentParams->get('under_construction_allowed_user');
	$context = array();
##  Wazna latka bezpieczenstwa!
##  Ale to nie tak! Tu trzeba znalezc w tabeli menu to ID i sprawdzic prawa!!! Bo ktos mogl Id tez z palca wpisac!

	if (false) { //$underConstruction == "1" && (is_null($user) || ($user->username != "metaadmin" && $user->username != $underConstructionAllowedUser))) {
?>
		<div style="color: #FF6666; font-size: 2em; margin: 100px; text-align: center;">
			Komponent w trakcie przebudowy.
		</div>
<?php
	} else {
		if (false) { //$underConstruction == "1" && !is_null($user) && ($user->username == "metaadmin" || $user->username == $underConstructionAllowedUser)) {
?>
		<div style="background-color: #FFFF00; border: 1px dashed; color: #111111; font-family: monospace; font-size: 1.2em; font-weight: bold; margin: 0; padding: 2px; text-align: center;">
			Komponent w trakcie przebudowy.
		</div>
<?php
		}
#set ($doneImage = 0)
#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'IMAGE' && $doneImage == 0)
#set ($doneImage = 1)
	$imageProperty = R('virgo_show_image_property');
	$imageId = R('virgo_show_image_id');
	$imageMaxSize = R('virgo_max_image_size');
	if (intval($imageMaxSize) == 0) {
		$imageMaxSize = 50;
	}
	if (!is_null($imageProperty) && !is_null($imageId)) {
		$thumbnail = R('virgo_thumbnail');
		$tmp_${entity.prefix} = new virgo${tr.FV($entity.name)}();
#if ($entity.view)		
		$tmp_${entity.prefix}->load($imageId);
#else
		$tmp_${entity.prefix}->load((int)$imageId);
#end		
		$imagePropertyFullName = "${entity.prefix}_" . $imageProperty . "_virgo_blob";
		if (!isset($tmp_${entity.prefix}->$imagePropertyFullName)) {
			$noBroken = R('virgo_nobroken');
			if ($noBroken != "yes") { 
				if ($thumbnail != 'true') {
					$imageMaxSize = 200;
				}
				$my_img = imagecreate( $imageMaxSize, $imageMaxSize );
				$background = imagecolorallocate( $my_img, 240, 240, 240 );
				$line_colour = imagecolorallocate( $my_img, 200, 200, 200 );
				$text_colour = imagecolorallocate( $my_img, 150, 150, 150 );
				imagesetthickness ( $my_img, 1 );
				imageline( $my_img, 0, 0, $imageMaxSize, $imageMaxSize, $line_colour );
				imageline( $my_img, $imageMaxSize, 0, 0, $imageMaxSize, $line_colour );
				imagestring( $my_img, 3, ($imageMaxSize / 2) - 40, ($imageMaxSize / 2) - 10,
#textPHP("IMAGE_MISSING")				
					, $text_colour );
				header('Content-Length: '.strlen($tmp_wsz->$imagePropertyFullName));
				header('Content-Type: image/jpeg');
				imagejpeg( $my_img );
				imagecolordeallocate( $line_color );
				imagecolordeallocate( $background );
				imagecolordeallocate( $text_color );
				imagedestroy( $my_img );
			}
		} else {
			$thumbnailWidth = ($thumbnail == 'true' ? $imageMaxSize : "");
			$thumbnailHeight = ($thumbnail == 'true' ? $imageMaxSize : "");
			header('Location: ' . virgo${tr.FV($entity.name)}::getCachedImagePath($imageId, $imageProperty, $thumbnailWidth, $thumbnailHeight));
		}
		exit();			
	}
#end
#end
?>
<?php
#set ($doneAudio = 0)
#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'AUDIO' && $doneAudio == 0)
#set ($doneAudio = 1)
	$imageProperty = R('virgo_play_audio_property');
	$imageId = R('virgo_play_audio_id');
	$imageMaxSize = R('virgo_max_audio_size');
	if (intval($imageMaxSize) == 0) {
		$imageMaxSize = 50;
	}
	if (!is_null($imageProperty) && !is_null($imageId)) {
		$tmp_${entity.prefix} = new virgo${tr.FV($entity.name)}();
#if ($entity.view)		
		$tmp_${entity.prefix}->load($imageId);
#else
		$tmp_${entity.prefix}->load((int)$imageId);
#end		
		$imagePropertyFullName = "${entity.prefix}_" . $imageProperty . "_virgo_blob";
		if (!isset($tmp_${entity.prefix}->$imagePropertyFullName)) {
			exit();
		}
		header('Content-Length: '.strlen($tmp_${entity.prefix}->$imagePropertyFullName));
		header('Content-Type: audio/mpeg');
		echo $tmp_${entity.prefix}->$imagePropertyFullName;
		exit();			
	}
#end
#end
?>
<?php
#set ($doneFile = 0)
#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'FILE' && $doneFile == 0)
#set ($doneFile = 1)
	$imageProperty = R('virgo_download_file_property');
	$imageId = R('virgo_download_file_id');
	if (!is_null($imageProperty) && !is_null($imageId)) {
		$tmp_${entity.prefix} = new virgo${tr.FV($entity.name)}();
#if ($entity.view)		
		$tmp_${entity.prefix}->load($imageId);
#else
		$tmp_${entity.prefix}->load((int)$imageId);
#end		
		$imagePropertyFullName = "${entity.prefix}_" . $imageProperty . "_virgo_blob";
		$imagePropertyFileName = "${entity.prefix}_" . $imageProperty . "_virgo_file_name";
		if (!isset($tmp_${entity.prefix}->$imagePropertyFullName)) {
			exit();
		}
		header('Content-Length: '.strlen($tmp_${entity.prefix}->$imagePropertyFullName));
		header('Content-Type: application/download');
		header('Content-transfer-encodig: binary');
		header('Content-disposition: attachment; filename="' . $tmp_${entity.prefix}->$imagePropertyFileName . '"');
		
		echo $tmp_${entity.prefix}->$imagePropertyFullName;
		exit();			
	}
#end
#end
?>
<?php
	$live_site = "localhost";
	if (false) { //$componentParams->get('css_usage') == "virgo" || $componentParams->get('css_usage') == "") {
?>
<link rel="stylesheet" href="<?php echo $live_site ?>/components/com_${application.prefix}_${tr.f_v($entity.name)}/${tr.f_v($application.name)}.css" type="text/css" /> 
<?php
	}
?>
<?php
	if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.f_v($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($entity.name)}'.DIRECTORY_SEPARATOR.'${tr.f_v($application.prefix)}_${tr.f_v($entity.prefix)}.css')) {
?>
<link rel="stylesheet" href="<?php echo $_SESSION['portal_url'] ?>/portlets/${tr.f_v($application.name)}/virgo${tr.FV($entity.name)}/${tr.f_v($application.prefix)}_${tr.f_v($entity.prefix)}.css" type="text/css" /> 
<?php
	}
?>
## <link rel="stylesheet" href="<?php echo $live_site ?>/media/system/css/calendar-jos.css" type="text/css" /> 
## <SCRIPT SRC="<?php echo $live_site ?>/components/com_${application.prefix}_${tr.f_v($entity.name)}/boxover.js"></SCRIPT>
<style>
## co to jest? To przeszkadza! Jest konflikt nazw divow?
## div#content {
##  padding: 0px 20px;
## }

td#component {
    background-color: inherit !important;
    border: inherit !important;
    color: inherit !important;
    padding: inherit !important;
    text-shadow: inherit !important;
    font-size: inherit !important;
  }
</style>
<div class="virgo_container_${tr.f_v($application.name)} virgo_container_entity_${tr.f_v($entity.name)}" style="border: none;">
	<div class="virgo_scrollable">
<?php
			}
#createDataStructureHashes()
#prepareTabsHashes()	
			$result${tr.FV($entity.name)} = virgo${tr.FV($entity.name)}::createGuiAware();
			$contextId = $result${tr.FV($entity.name)}->getContextId();
			if (isset($contextId)) {
				if (virgo${tr.FV($entity.name)}::getDisplayMode() != "CREATE" || R('portlet_action') == "Duplicate") {
					$result${tr.FV($entity.name)}->load($contextId);
				}
			}
?>
<?php
			if (false) { //$componentParams->get('show_project_name') == "1") {
?>
		<div id="virgo_project">
<?php
  $parentMenu = null; //$menu->getItem($menuItem->parent);
?>
			$application.name: <?php echo is_null($parentMenu) ? $mainframe->getPageTitle() : $parentMenu->name . " -> " . $mainframe->getPageTitle() ?>
			<span id="virgo_project_version">
				${application.versionMain}.${application.version} 
			</span>
#parse("defs/version.php")			
			<span class="virgo_connected_as">
<?php
				if (is_null($user->username)) {
?>
#text("'NOT_LOGGED_IN'")
<?php
				} else {
?>
#text("'LOGGED_IN_AS'")
					<span class="virgo_username"><?php echo $user->username ?></span>.
<?php
				}
?>
			</span>
		</div>
<?php
			}
	$masterPobId = P('master_entity_pob_id');
	if (isset($masterPobId)) {
		$portletObject = new virgoPortletObject($masterPobId);
		$className = $portletObject->getPortletDefinition()->getAlias();
		if ($className == "virgo${tr.FV($entity.name)}") {
			$masterObject = new $className();
			$tmpId = $masterObject->getRemoteContextId($masterPobId);
			if (isset($tmpId)) {
				$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}($tmpId);
				virgo${tr.FV($entity.name)}::setDisplayMode("FORM");
			} else {
				$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
				virgo${tr.FV($entity.name)}::setDisplayMode("CREATE");
			}
		}
	} else {
## To powodowalo Fatal error: Allowed memory size of 67108864 bytes exhausted jak dużo rekordów, wiec zmieniono
		if (P('form_only', "0") == "5") {
			if (is_null($result${tr.FV($entity.name)}->getId())) { 
## ale jesli to jest zapisywanie i error wyskoczyl? To id nie bedzie, ale nie nalezy kasowac wartosci pol! 
				if (P('only_private_records', "0") == "1") {
					$allPrivateRecords = $result${tr.FV($entity.name)}->selectAll();
					if (sizeof($allPrivateRecords) > 0) {
## biore pierwszego!					
						$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}($allPrivateRecords[0]['${tr.f_v($entity.prefix)}_id']);
						$result${tr.FV($entity.name)}->putInContext(false);
					} else {
						$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
					}
				} else {
					$customSQL = P('custom_sql_condition');
					if (isset($customSQL) && trim($customSQL) != '') {
						$currentUser = virgoUser::getUser();
						$currentPage = virgoPage::getCurrentPage();
						eval("\$customSQL = \"$customSQL\";");
						$records = $result${tr.FV($entity.name)}->selectAll($customSQL);
						if (sizeof($records) > 0) {
## biore pierwszego!					
							$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}($records[0]['${tr.f_v($entity.prefix)}_id']);
							$result${tr.FV($entity.name)}->putInContext(false);
						} else {
							$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
						}
					} else {
						$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
					}
				}
			}
		} elseif (P('form_only', "0") == "6") {
			$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}(virgoUser::getUserId());
			$result${tr.FV($entity.name)}->putInContext(false);
		}
	}
?>
<?php
		if (isset($includeError) && $includeError == 1) {
			$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
		}
?>
<?php
	$${tr.fV($entity.name)}DisplayMode = virgo${tr.FV($entity.name)}::getDisplayMode();
## 16.01.2013: to nie moze byc tak, jak ponizej, bo to jest zbyt niebezpieczne, ze np. pokazemy tabele a w niej rekordy, ktorych on nie powinien widziec...
##	if (($${tr.fV($entity.name)}DisplayMode == "VIEW" || $${tr.fV($entity.name)}DisplayMode == "FORM") && is_null($result${tr.FV($entity.name)}->getId())) {
##		$${tr.fV($entity.name)}DisplayMode = "TABLE";
##	}
//	if ($${tr.fV($entity.name)}DisplayMode == "" || $${tr.fV($entity.name)}DisplayMode == "TABLE") {
//		$result${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->portletActionForm();
//	}
?>
#parseFile("modules_project/customResult/${tr.f_v($entity.name)}.php")
		<div class="form">
#if ($entity.custom)
#parse("modules_project/customPage/${tr.f_v($entity.name)}.php")
#else
#renderContext()
#parse("defs/virgo.php")
#end
		</div>
	</div>
##	<div id="virgo_powered" onclick="document.getElementById('extraFilesInfo_${application.prefix}_${tr.f_v($entity.name)}').style.display='block';">
## #text("'POWERED_BY'")					
##		<a href="http://www.metadetron.com" target="_blank">METADETRON</a>
##	</div>
</div>
<div style="display: none; background-color:#FFFFFF; border:1px solid #000000; font-size:10px; margin:10px 0; padding:10px;"; id="extraFilesInfo_${application.prefix}_${tr.f_v($entity.name)}" style="font-size: 12px; " onclick="document.getElementById('extraFilesInfo_${application.prefix}_${tr.f_v($entity.name)}').style.display='none';">
<table><tr><td valign="top">
<table collspacing="2" collpadding="1">
	<tr>
		<td colspan="2">
			<b>Web:</b>
		</td>
	</tr>
#foreach( $fileName in $extraFilesInfo.keySet() )
	<tr>
		<td align="right">
			$fileName
		</td>
		<td align="left">
			$extraFilesInfo.get($fileName)
		</td>
	</tr>
#end
</table>
</td>
<td valign="top">
<table>
	<tr>
		<td colspan="2">
			<b>Bean:</b>
		</td>
	</tr>
<?php
	$infos = virgo${tr.FV($entity.name)}::getExtraFilesInfo();
	foreach ($infos as $fileName => $date) {
?>
	<tr>
		<td align="right">
			<?php echo $fileName ?>
		</td>
		<td align="left">
			<?php echo $date ?>
		</td>
	</tr>
<?php
	}
?>
</table>
</td></tr></table>
</div>
<?php 
		if ($underConstruction == "1" && !is_null($user) && $user->username == "admin") {
?>
		<div style="background-color: #FFFF00; border: 1px dashed; color: #111111; font-family: monospace; font-size: 1.2em; font-weight: bold; margin: 0; padding: 2px; text-align: center;">
			Komponent w trakcie przebudowy.
		</div>
<?php
	}
?>
#end
#end
#end

