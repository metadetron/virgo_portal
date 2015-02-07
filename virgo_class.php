#*
	Actions:
		- standard
			* edit
			* view
			* store
			* close
	Display Modes:
		- standard
			* TABLE
			* FORM
			* VIEW
*#
#readParameters()
#foreach( $entity in $application.entities )
#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)})
#if (!$entity.external) 
$extraFilesInfo.clear()
----- portlets\\${tr.f_v($application.name)}\\virgo${tr.FV($entity.name)}\\controller.php -----
<?php
/**
* Module ${tr.Fv($entity.name)}
* @package ${tr.Fv($application.name)}
* @author Grzegorz Swierczynski
* @copyright METADETRON (C) 2012 All rights reserved.
*/	
	namespace ${tr.f_v($application.name)};	
	use portal\virgoUser;
	use portal\virgoPage;
	use portal\virgoPortletObject;

	defined( '_INDEX_PORTAL' ) or die( 'Access denied.' );
	
#foreach( $relation in $entity.childRelations )
#if ($relation.parentEntity.external)
#set ($appName = ${tr.f_v($relation.parentEntity.properties.get(0).name)})
#else
#set ($appName = ${tr.f_v($application.name)})
#end
## jednak sprobujmy bez file_exists, bo to za duzo trwa? if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php')) require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php');
//	require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.parentEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php');
#end			
#foreach( $relation in $entity.parentRelations )
#if ($relation.childEntity.external)
#set ($appName = ${tr.f_v($relation.childEntity.properties.get(0).name)})
#else
#set ($appName = ${tr.f_v($application.name)})
#end
## jednak sprobujmy bez file_exists, bo to za duzo trwa? if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.childEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php')) require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.childEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php');
//	require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'$appName'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.childEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php');
#end

	class virgo${tr.FV($entity.name)} {

		#if ($hideSetters == "TRUE") private #else var #end $${entity.prefix}_id = null;
#foreach( $property in $entity.properties )
#parseFileWithStandard("defs/dataTypes/php/defineMember/${tr.f_v($property.dataType.name)}" "defs/defineMember.php")
#end			
#foreach( $relation in $entity.childRelations )
#if ($relation.name)
		#if ($hideSetters == "TRUE") private #else var #end $${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id = null;
#else			
		#if ($hideSetters == "TRUE") private #else var #end $${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_id = null;
#end			
#end

#set ($dollar = '$')
#foreach( $relation in $entity.parentRelations )
## sprawdz, czy te dzieci to przypadkiem nie jest tabela n:m ktora ma drugiego rodzica, ktory jest slownikiem
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
		#if ($hideSetters == "TRUE") private #else var #end  ${dollar}_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray = null;
		#if ($hideSetters == "TRUE") private #else var #end  ${dollar}_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToDeleteArray = null;
#end
#end
#end
		#if ($hideSetters == "TRUE") private #else var #end  $${entity.prefix}_date_created = null;
		#if ($hideSetters == "TRUE") private #else var #end  $${entity.prefix}_usr_created_id = null;
		#if ($hideSetters == "TRUE") private #else var #end  $${entity.prefix}_date_modified = null;
		#if ($hideSetters == "TRUE") private #else var #end  $${entity.prefix}_usr_modified_id = null;
		#if ($hideSetters == "TRUE") private #else var #end  $${entity.prefix}_virgo_title = null;
#if ($entity.dictionary)		
		#if ($hideSetters == "TRUE") private #else var #end  $${entity.prefix}_virgo_deleted = null;
#end
		
		#if ($hideSetters == "TRUE") private #else var #end  $internalLog = null;		
				
		#if ($hideSetters == "TRUE") private #end function __construct($loadId = null) {
#foreach( $property in $entity.properties )
#if ($property.defaultValue)
			$tmpRet = '$ret = ' . $property.defaultValue . ';';
			eval($tmpRet);
			$this->${entity.prefix}_${tr.f_v($property.name)} = $ret;
#end
#if ($property.dataType.name == 'DATE' || $property.dataType.name == 'DATETIME')
#if ($property.customProperty)
#getParamValue($property.customProperty "CurrentDate")
#if ($virgoWartosc == "True")
#if ($property.dataType.name == 'DATE')
			$this->${entity.prefix}_${tr.f_v($property.name)} = date("Y-m-d");
#end
#if ($property.dataType.name == 'DATETIME')
			$this->${entity.prefix}_${tr.f_v($property.name)} = date("Y-m-d H:i:s");
#end
#end
#end
#end
#end			

## initial state
#getStatusEntityWithWorkflow()
#if ($statusEntity)
			if (is_null($this->${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id)) {
#set ($displayOrderPropertyName = "id")
#foreach( $property in $statusEntity.properties )
#if ($property.name == 'display order' || ${tr.f_v($property.name)} == 'kolejnosc_wyswietlania' || ${tr.f_v($property.name)} == 'order' || ${tr.f_v($property.name)} == 'kolejnosc')
#set ($displayOrderPropertyName = $property.name)
#end
#end
				$tmpIds = virgo${tr.FV($statusEntity.name)}::selectAllAsIdsStatic(' NOT EXISTS (SELECT * FROM ${tr.f_v($application.prefix)}_${tr.f_v($statusEntity.name)}_workflows WHERE ${tr.f_v($statusWorkflowEntity.prefix)}_${tr.f_v($statusEntity.prefix)}_prev_id = ${tr.f_v($statusEntity.prefix)}_id ) ', ' ${tr.f_v($statusEntity.prefix)}_${tr.f_v($displayOrderPropertyName)} ASC ');
				foreach ($tmpIds as $tmpId) {
					$this->set${tr.FV($statusEntity.prefix)}Id($tmpId);
					break;
				}
			}
#end

#parseFile("modules_project/defaultValues/${tr.f_v($entity.name)}.php")			
			if (!is_null($loadId) && (is_int($loadId) || is_string($loadId))) {
				$this->load($loadId);
#set ($synchronizeUser = "0")					
#foreach ($property	in $entity.properties)
#readSyncValues()
#if ($sync == "1") 
#set ($synchronizeUser = "1")
#end
#end
#if ($synchronizeUser == "1")
				$tmpUser = new JUser($this->${entity.prefix}_usr_created_id);
#end
#foreach ($property in $entity.properties)				
#getParamValue($property.customProperty "SyncUsername")
#if ($virgoWartosc != "")
				$this->set${tr.FV($property.name)}($tmpUser->username);
#end				
#getParamValue($property.customProperty "SyncEmail")
#if ($virgoWartosc != "")
				$this->set${tr.FV($property.name)}($tmpUser->email);
#end				
#getParamValue($property.customProperty "SyncPassword")
#if ($virgoWartosc != "")
				$this->set${tr.FV($property.name)}($tmpUser->password);
#end				
#end
			}
		}

		public function isDeletedVirgo() {
			return $this->${entity.prefix}_virgo_deleted;
		}

		static public function createGuiAware() {
			$ret = new virgo${tr.FV($entity.name)}();
## tuttaj trzeba przeczytac co jest w contekscie parentow i grandparentow i ustawic odpowiednie pola
			$parentsInContext = self::getParentsInContext();
			foreach ($parentsInContext as $className => $parentInfo) {
				if (isset($parentInfo['contextId'])) {
					$setter = "set".strtoupper($parentInfo['prefix'])."Id";
					if (method_exists($ret, $setter)) {
						call_user_func_array(array($ret, $setter), array($parentInfo['contextId']));
					}					
				}
			}
			return $ret;
		}

		public function __clone() {
        	$this->${entity.prefix}_id = null;
		    $this->${entity.prefix}_date_created = null;
		    $this->${entity.prefix}_usr_created_id = null;
		    $this->${entity.prefix}_date_modified = null;
		    $this->${entity.prefix}_usr_modified_id = null;
		    $this->${entity.prefix}_virgo_title = null;
    	}		
		
		function log($message, $level = "INFO") {
			L($message, '', $level);
		}
		
		function logFatal($message) {
			$this->log($message, "FATAL");
		}
		
		function logError($message) {
			$this->log($message, "ERROR");
		}

		function logWarn($message) {
			$this->log($message, "WARN");
		}
		
		function logInfo($message) {
			$this->log($message, "INFO");
		}
		
		function logDebug($message) {
			$this->log($message, "DEBUG");
		}
		
		function logTrace($message) {
			$this->log($message, "TRACE");
		}
		
		function isLogOn($level) {
			return false;
		}
		
		function isFatal() {
			return $this->isLogOn("FATAL");
		}
		
		function isError() {
			return $this->isLogOn("ERROR");
		}
		
		function isWarn() {
			return $this->isLogOn("WARN");
		}
		
		function isInfo() {
			return $this->isLogOn("INFO");
		}
		
		function isDebug() {
			return $this->isLogOn("DEBUG");
		}
		
		function isTrace() {
			return $this->isLogOn("TRACE");
		}
		
		function getId() {
			return $this->${entity.prefix}_id;
		}

#foreach( $property in $entity.properties )
		function get${tr.FV($property.name)}() {
#parseFileWithStandard("defs/dataTypes/php/getter/${tr.f_v($property.dataType.name)}" "defs/getter.php")
		}
		
		#if ($hideSetters == "TRUE") private #end function set${tr.FV($property.name)}($val) {
#parseFileWithStandard("defs/dataTypes/php/setter/${tr.f_v($property.dataType.name)}" "defs/setter.php")
		}
#end			

#foreach( $relation in $entity.childRelations )
		function get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Id() {
#if ($relation.name)
			return $this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id;
#else			
			return $this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_id;
#end			
		}
		
		#if ($hideSetters == "TRUE") private #end function set${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Id($val) {
#if ($relation.name)
			$this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id = $val;
#else			
			$this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_id = $val;
#end			
		}
#end

		function getDateCreated() {
			return $this->${entity.prefix}_date_created;
		}
		function getUsrCreatedId() {
			return $this->${entity.prefix}_usr_created_id;
		}
		function getDateModified() {
			return $this->${entity.prefix}_date_modified;
		}
		function getUsrModifiedId() {
			return $this->${entity.prefix}_usr_modified_id;
		}


## for backward compability only:
#foreach( $relation in $entity.childRelations )
		function get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id() {
			return $this->get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Id();
		}
		
		#if ($hideSetters == "TRUE") private #end function set${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id($val) {
			$this->set${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Id($val);
		}
#end

#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'IMAGE')
		static function get${tr.FV($property.name)}ImageUrlStatic($tmpId, $width = 0) {
			if ($width > 0) {
				$width = "&virgo_media_width=" . $width;
			} else {
				$width = "";
			}
			$portal = \portal\virgoPortal::getCurrentPortal();
			return $portal->getPortalUrl()."?virgo_media=true&virgo_media_table_name=${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}&virgo_media_table_prefix=${tr.f_v($entity.prefix)}&virgo_media_property_name=${tr.f_v($property.name)}{$width}&virgo_media_row_id={$tmpId}&" . getTokenName($tmpId) . "=" . getTokenValue($tmpId);
		}
## ostatni argument: czasami lepiej pokazac cos w stylu "brak obrazka" a to juz chyba bardziej kwestia cacheowania czy kodu w index.php
		function get${tr.FV($property.name)}ImageUrl($width = 0, $emptyIfNoImage = true) {
			if (is_null($this->${entity.prefix}_${tr.f_v($property.name)}_virgo_blob) && $emptyIfNoImage) {
				return "";
			}
			return virgo${tr.FV($entity.name)}::get${tr.FV($property.name)}ImageUrlStatic($this->${entity.prefix}_id, $width);
		}
#elseif ($property.dataType.name == 'FILE')
		static function get${tr.FV($property.name)}FileUrlStatic($tmpId) {
			$ret = "";
			$ret .= $_SESSION['portal_url'];
			$ret .= "?virgo_media=true";
			$ret .= "&virgo_media_type=file";
			$ret .= "&virgo_media_table_name=${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}";
			$ret .= "&virgo_media_table_prefix=${tr.f_v($entity.prefix)}";
			$ret .= "&virgo_media_property_name=${tr.f_v($property.name)}";
			$ret .= "&virgo_media_row_id=" . $tmpId;
			$ret .= "&" . getTokenName($tmpId) . "=" . getTokenValue($tmpId);
			return $ret;
		}
		function get${tr.FV($property.name)}FileUrl() {
			if (!is_null($this->getId())) {
				if (!is_null($this->${entity.prefix}_${tr.f_v($property.name)}_virgo_blob)) {
					return virgo${tr.FV($entity.name)}::get${tr.FV($property.name)}FileUrlStatic($this->${entity.prefix}_id);
				}
			}
			return "";
		}
#elseif ($property.dataType.name == 'HTML')		
		static function get${tr.FV($property.name)}PdfUrlStatic($tmpId) {
			$ret = "";
			$ret .= $_SESSION['portal_url'];
			$ret .= "?virgo_media=true";
			$ret .= "&virgo_media_type=html2pdf";
			$ret .= "&virgo_media_table_name=${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}";
			$ret .= "&virgo_media_table_prefix=${tr.f_v($entity.prefix)}";
			$ret .= "&virgo_media_property_name=${tr.f_v($property.name)}";
			$ret .= "&virgo_media_row_id=" . $tmpId;
			$ret .= "&" . getTokenName($tmpId) . "=" . getTokenValue($tmpId);
			return $ret;
		}
		function get${tr.FV($property.name)}PdfUrl() {
			if (!is_null($this->getId())) {
				if (!is_null($this->${entity.prefix}_${tr.f_v($property.name)})) {
					return virgo${tr.FV($entity.name)}::get${tr.FV($property.name)}PdfUrlStatic($this->${entity.prefix}_id);
				}
			}
			return "";
		}
#elseif ($property.dataType.name == 'TEXT')		
		function get${tr.FV($property.name)}Snippet($wordCount) {
			if (is_null($this->get${tr.FV($property.name)}()) || trim($this->get${tr.FV($property.name)}()) == "") {
				return "";
			}
		  	return implode( 
			    '', 
		    	array_slice( 
		      		preg_split(
			        	'/([\s,\.;\?\!]+)/', 
		        		$this->get${tr.FV($property.name)}(), 
		        		$wordCount*2+1, 
		        		PREG_SPLIT_DELIM_CAPTURE
		      		),
		      		0,
		      		$wordCount*2-1
		    	)
		  	)."...";
		}
#end
#end		
		function loadRecordFromRequest($rowId) {
			$this->${entity.prefix}_id = $rowId;
#foreach( $property in $entity.properties )
#parseFileWithStandard("defs/dataTypes/php/loadFromRequest/${tr.f_v($property.dataType.name)}" "defs/loadFromRequest.php")
#end			
#foreach( $relation in $entity.childRelations )
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
			$this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id = strval(R('${tr.fV($entity.prefix)}_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}_' . $this->${entity.prefix}_id));
#end
#foreach( $relation in $entity.parentRelations )
## sprawdz, czy te dzieci to przypadkiem nie jest tabela n:m ktora ma drugiego rodzica, ktory jest slownikiem
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
			$tmp_ids = R('${tr.fV($entity.prefix)}_${tr.fV($relation.childEntity.name)}${tr.FV($relation.name)}_' . $this->${entity.prefix}_id, null); ##, 'default', 'ARRAY');
			if (is_null($tmp_ids)) {
				$tmp_ids = array();
			}
			if (is_array($tmp_ids)) { 
## Algorytm: wez wszystkie dotychczasowe polaczenia i po kolei sprawdzaj, czy istnieja w tmp_ids. Jesli nie, to usuwamy, a jak tak, to usuwamy z tmp_ids.
## Na koniec to, co zostalo w tmp_ids wstawiamy do polaczen
## Uwaga, tuttaj tylko wypelnienie tymczasowych tablic, reszta w store()!
				$this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray = $tmp_ids;
				$this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToDeleteArray = array();
				$currentConnections = $this->get${tr.FV($weakEntityRelation.childEntity.namePlural)}${tr.FV($weakEntityRelation.name)}();
				foreach ($currentConnections as $currentConnection) {
					if (in_array($currentConnection->get${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id(), $tmp_ids)) {
						foreach($this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray as $key => $value) {
							if ($value == $currentConnection->get${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id()) {
								unset($this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray[$key]);
							}
						}
						$this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray = array_values($this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray);
					} else {
						$this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToDeleteArray[] = $currentConnection->get${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id();
					}
				}
			}
#end
#end
#end
##			$this->${entity.prefix}_usr_created_id = R('usr_created_id', null, 'default', 'none', JREQUEST_ALLOWRAW);
##			$this->${entity.prefix}_date_created = R('date_created', null, 'default', 'none', JREQUEST_ALLOWRAW);
##			$this->${entity.prefix}_usr_modified_id = R('usr_modified_id', null, 'default', 'none', JREQUEST_ALLOWRAW);
##			$this->${entity.prefix}_date_modified = R('date_modified', null, 'default', 'none', JREQUEST_ALLOWRAW);
#parseFile("modules_project/extraFieldsFromRequest/${tr.f_v($entity.name)}.php")
		}
		
		function loadFromRequest($rowId = null) {
			if (is_null($rowId)) {
				$rowId = R('${tr.fV($entity.prefix)}_id_' . $_SESSION['current_portlet_object_id']);
			}
			if (is_null($rowId) || trim($rowId) == "") {
				$rowId = "";
			} else {
				$rowId = intval($rowId);
				$this->load((int)$rowId);
#set ($synchronizeUser = "0")					
#foreach ($property	in $entity.properties)
#readSyncValues()
#if ($sync == "1") 
#set ($synchronizeUser = "1")
#end
#end
#if ($synchronizeUser == "1")
				$tmpUser = new JUser($this->${entity.prefix}_usr_created_id);
#end
#foreach ($property in $entity.properties)				
#getParamValue($property.customProperty "SyncUsername")
#if ($virgoWartosc != "")
				$this->set${tr.FV($property.name)}($tmpUser->username);
#end				
#getParamValue($property.customProperty "SyncEmail")
#if ($virgoWartosc != "")
				$this->set${tr.FV($property.name)}($tmpUser->email);
#end				
#getParamValue($property.customProperty "SyncPassword")
#if ($virgoWartosc != "")
				$this->set${tr.FV($property.name)}($tmpUser->password);
#end				
#end
			}
			$this->loadRecordFromRequest($rowId);
		}		

		static function loadSearchFromRequest() {
			$criteria${tr.FV($entity.name)} = array();	
#foreach( $property in $entity.properties )
			$criteriaField${tr.FV($entity.name)} = array();	
			$isNull${tr.FV($entity.name)} = R('virgo_search_${tr.fV($property.name)}_is_null');
			
			$criteriaField${tr.FV($entity.name)}["is_null"] = 0;
			if ($isNull${tr.FV($entity.name)} == "not_null") {
				$criteriaField${tr.FV($entity.name)}["is_null"] = 1;
			} elseif ($isNull${tr.FV($entity.name)} == "null") {
				$criteriaField${tr.FV($entity.name)}["is_null"] = 2;
			}
			
			$dataTypeCriteria = array();
			$isSet = false;
#parseFile("defs/dataTypes/php/loadSearchFromRequest/${tr.f_v($property.dataType.name)}")
//			if ($isSet) {
			$criteriaField${tr.FV($entity.name)}["value"] = $dataTypeCriteria;
//			}
			$criteria${tr.FV($entity.name)}["${tr.f_v($property.name)}"] = $criteriaField${tr.FV($entity.name)};
#end
#foreach( $relation in $entity.childRelations )
			$criteriaParent = array();	
			$isNull = R('virgo_search_${tr.fV($relation.parentEntity.name)}${tr.fV($relation.name)}_is_null');
			$criteriaParent["is_null"] = 0;
			if ($isNull == "not_null") {
				$criteriaParent["is_null"] = 1;
			} elseif ($isNull == "null") {
				$criteriaParent["is_null"] = 2;
			}
##			$parentIds = R('virgo_search_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}', array(), 'default', 'array');
			$parent = R('virgo_search_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}', null, 'default', 'none');
			if (!is_null($parent) && count($parent) > 0) {
#if ($relation.parentEntity.dictionary)				
				$criteriaParent["ids"] = $parent;
#else
				$criteriaParent["value"] = $parent;
#end
			}
			$criteria${tr.FV($entity.name)}["${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}"] = $criteriaParent;
#end
#foreach( $relation in $entity.parentRelations )
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
			$parent = R('virgo_search_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($relation.name)}', null, 'default', 'none');
			if (!is_null($parent) && count($parent) > 0) {
				$criteriaParent["ids"] = $parent;
				$criteria${tr.FV($entity.name)}["${tr.f_v($secondParentRelation.parentEntity.name)}${tr.f_v($relation.name)}"] = $criteriaParent;
			}
#end
#end
#end
#parseFile("modules_project/extraSearchFieldsFromRequest/${tr.f_v($entity.name)}.php")
			self::setCriteria($criteria${tr.FV($entity.name)});
		}

## te metode wola inny portlet, ktory ustawia parametr sesji 'custom_parent_id' i aplikuje go do 
## parametru portletu 'custom_parent_query'
		static function portletActionSetCustomParent() {
			$customParentId = R('virgo_parent_id');
			self::setLocalSessionValue('CustomParentId', $customParentId);
			return 0;
		}

		static function portletActionSetVirgoTableFilter() {
#foreach( $property in $entity.properties )
			$tableFilter = R('virgo_filter_${tr.f_v($property.name)}');
			if (S($tableFilter)) {
				self::setLocalSessionValue('VirgoFilter${tr.FV($property.name)}', $tableFilter);
			} else {
				self::setLocalSessionValue('VirgoFilter${tr.FV($property.name)}', null);
			}
#end
#foreach( $relation in $entity.childRelations )
			$parentFilter = R('virgo_filter_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}');
			if (S($parentFilter)) {
				self::setLocalSessionValue('VirgoFilter${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', $parentFilter);
			} else {
				self::setLocalSessionValue('VirgoFilter${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', null);
			}
			$parentFilter = R('virgo_filter_title_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}');
			if (S($parentFilter)) {
				self::setLocalSessionValue('VirgoFilterTitle${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', $parentFilter);
			} else {
				self::setLocalSessionValue('VirgoFilterTitle${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', null);
			}
#end
		}

## ----  tu wstawiamy to, co bylo w content.php a dotyczylo wybierania danych do tabelki  ----
		function showApplicableOnlyRecords(&$filterApplied, &$types, &$values) {
			$whereClause${tr.FV($entity.name)} = ' 1 = 1 ';
			if (P('form_only') == "3") {
				$pob = self::getMyPortletObject();
				$selectedMonth = $pob->getPortletSessionValue('selected_month', date("m"));
				$selectedYear = $pob->getPortletSessionValue('selected_year', date("Y"));
				$daysInfo = $pob->getPortletSessionValue('days_info', array());
				$tmpDay = getdate(mktime(0, 0, 0, $selectedMonth, 1, $selectedYear));
				$firstDay = $tmpDay;
				if ((int)$tmpDay["mon"] == 12) {
					$lastDay = getdate(strtotime($tmpDay["year"]+1 . "-" .  1 . "-" . (((int)$tmpDay["mday"])-1)));
				} else {
					$lastDay = getdate(strtotime($tmpDay["year"] . "-" .  (((int)$tmpDay["mon"])+1) . "-" . (((int)$tmpDay["mday"])-1)));
				}
				$eventColumn = "${tr.f_v($entity.prefix)}_" . P('event_column');
				$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " AND " . $eventColumn . " BETWEEN ? AND ? ";
				$types .= "ss";
				$values[] = $firstDay["year"] . "-" . (str_pad($firstDay["mon"], 2, "0", STR_PAD_LEFT)) . "-" . (str_pad($firstDay["mday"], 2, "0", STR_PAD_LEFT));
				$values[] = $lastDay["year"] . "-" . (str_pad($lastDay["mon"], 2, "0", STR_PAD_LEFT)) . "-" . (str_pad($lastDay["mday"], 2, "0", STR_PAD_LEFT));
			}
			$parentContextInfos = $this->getParentsInContext();
			foreach ($parentContextInfos as $parentContextInfo) {
				$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . ' AND ' . $parentContextInfo['condition'];
			}
#limitToConfiguredParents()			
#fillUpFilterCriteria()			
			$filterApplied = ($filter != "");
			$customSQL = P('custom_sql_condition');
			$currentUser = virgoUser::getUser();
			$currentPage = virgoPage::getCurrentPage();
			if (isset($customSQL) && trim($customSQL) != '') {
				$user = virgoUser::getUser();
				eval("\$customSQL = \"$customSQL\";");
				$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " AND " . $customSQL . " ";
			}
			$customParentQuery = P('custom_parent_query');
			if (S($customParentQuery)) {
				$customParentId = self::getLocalSessionValue('CustomParentId', null);
				if (S($customParentId)) {
					$customParentQuery = str_replace("?", $customParentId, $customParentQuery);
					$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " AND " . $customParentQuery . " ";
				}
			}
## filtry nad kolumnami
			if (P("show_table_filter", '0') == 1) {
#foreach( $property in $entity.properties )
				$tableFilter = self::getLocalSessionValue('VirgoFilter${tr.FV($property.name)}', null);
				if (S($tableFilter)) {
					$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " AND ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} LIKE '%{$tableFilter}%' ";
				}
#end
#foreach( $relation in $entity.childRelations )
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end				
				$parentFilter = self::getLocalSessionValue('VirgoFilter${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', null);
				if (S($parentFilter)) {
					if ($parentFilter == "empty") {
						$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " AND ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id IS NULL ";
					} else {
						$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " AND ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id = $parentFilter ";
					}
				}
				$parentFilter = self::getLocalSessionValue('VirgoFilterTitle${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}', null);
				if (S($parentFilter)) {
					$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " AND ${tr.f_v($application.prefix)}_${tr.f_v($relation.parentEntity.namePlural)}_parent.${tr.f_v($relation.parentEntity.prefix)}_virgo_title LIKE '%{$parentFilter}%' ";
				}				
#end
			}
			return $whereClause${tr.FV($entity.name)};
		}

## co jest wejsciem tego kodu? numer strony?
## co jest wynikiem tego kodu? rekordy z tabeli z danej strony plus ilosc wszystkich
		function getTableData(&$resultCount, &$filterApplied) {
			$types = "";
			$values = array();
			$filterApplied = false;
			$whereClause${tr.FV($entity.name)} = $this->showApplicableOnlyRecords($filterApplied, $types, $values);
#set ($tableDisplayMode = "table")
#parseFileWithStandard("modules_project/tableSelect/${tr.f_v($entity.name)}.php" "defs/entity/table_select.php")
			$showPage = $this->getShowPage(); 
			$showRows = $this->getShowRows(); 
			$virgoOrderColumn = $this->getOrderColumn();
			$virgoOrderMode = $this->getOrderMode();

			$extraCondition = "";
#parseFile("modules_project/extraCondition/${tr.f_v($entity.name)}.php") 
			if (trim($extraCondition) != "") {
				$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . " " . $extraCondition;
			}

			$resultCount = $this->getAllRecordCount($whereClause${tr.FV($entity.name)}, $queryString, $types, $values);

			return $this->select(
					$showPage, 
					$showRows, 
					$virgoOrderColumn, 
					$virgoOrderMode, 
					$whereClause${tr.FV($entity.name)},
					$queryString,
					$types,
					$values);

		}
## ---- tu wstawilismy to, co bylo w content.php a dotyczylo wybierania danych do tabelki ----
		
		static function internalSelect($query, $column = null, $types = null, $values = null) {
			return QPR($query, $types, $values, false, true, $column);
		}
		
		function selectAll($where = '', $orderBy = '', $types = null, $values = null) {
			$query = "SELECT * "
			. "\n FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}"
			;
			$componentParams = null;
## to ma byc sprawdzane tylko dla tej klasy a nie aktualnego poba!
## na razie moze zrobmy, ze nie dotyczy slownikow? Ale to chyba i tak trzeba bedzie zmienic...
#if (!$entity.dictionary && !$entity.weak)
			if (P('only_private_records', "0") == "1") {
				$privateCondition = " ${tr.f_v($entity.prefix)}_usr_created_id = " . virgoUser::getUserId() . " ";
				if ($where == '') {
					$where = $privateCondition;
				} else {
					$where = $where . " AND " . $privateCondition;
				}
			}			
#end
			if (false) { //$componentParams->get('only_records_in_valid_range') == "1") {
				$rangeCondition = "";
#foreach ($property in $entity.properties)
#if ($property.dataType.name == "DATE")
#if ($property.customProperty)
#getParamValue($property.customProperty "Validity")
#if ($virgoWartosc == "true")
#if ($property.name.endsWith(" od") || $property.name.endsWith(" from"))
				$rangeCondition = $rangeCondition . " ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} <= curdate()) ";
#end ## od-from
#if ($property.name.endsWith(" do") || $property.name.endsWith(" to"))
				if ($rangeCondition == "") {
					$rangeCondition = " ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} >= curdate()) ";
				} else {
					$rangeCondition = " ( " . $rangeCondition . " AND ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} >= curdate()) ) ";
				}
#end ## do-to
#end ## virgoWartosc
#end ## property.custmoProperty
#end ## dataType == "DATE"
#end ## foreach property			
				if ($rangeCondition != "") {
					if ($where == '') {
						$where = $rangeCondition;
					} else {
						$where = $where . " AND " . $rangeCondition;
					}
				}
			}			
			if ($where != '') {
				$query = $query . " WHERE " . $where . " ";
			}
			if ($orderBy != '') {
				$query = $query . " ORDER BY  " . $orderBy . " ";
			}
			return self::internalSelect($query, null, $types, $values);
		}
		
		function select($showPage, $showRows, $orderColumn, $orderMode, $where = '', $queryString = '', $types = null, $values = null) {
			$componentParams = null;
## to ma byc sprawdzane tylko dla tej klasy a nie aktualnego poba!
## na razie moze zrobmy, ze nie dotyczy slownikow? Ale to chyba i tak trzeba bedzie zmienic...
#if (!$entity.dictionary && !$entity.weak)
			if (P('only_private_records', "0") == "1") {
				$privateCondition = " ${tr.f_v($entity.prefix)}_usr_created_id = ? ";
				if ($where == '') {
					$where = $privateCondition;
				} else {
					$where = $where . " AND " . $privateCondition;
				}
				$types .= "i";
				$values[] = virgoUser::getUserId();
			}			
#end			
			if (false) { //$componentParams->get('only_records_in_valid_range') == "1") {
				$rangeCondition = "";
#foreach ($property in $entity.properties)
#if ($property.dataType.name == "DATE")
#if ($property.customProperty)
#getParamValue($property.customProperty "Validity")
#if ($virgoWartosc == "true")
#if ($property.name.endsWith(" od") || $property.name.endsWith(" from"))
				$rangeCondition = $rangeCondition . " ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} <= curdate()) ";
#end ## od-from
#if ($property.name.endsWith(" do") || $property.name.endsWith(" to"))
				if ($rangeCondition == "") {
					$rangeCondition = " ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} >= curdate()) ";
				} else {
					$rangeCondition = " ( " . $rangeCondition . " AND ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} >= curdate()) ) ";
				}
#end ## do-to
#end ## virgoWartosc
#end ## property.custmoProperty
#end ## dataType == "DATE"
#end ## foreach property			
				if ($rangeCondition != "") {
					if ($where == '') {
						$where = $rangeCondition;
					} else {
						$where = $where . " AND " . $rangeCondition;
					}
				}
			}			
			if ($queryString == '') {
				$query = "SELECT * "
				. "\n FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}"
				;
				if ($where != '') { 
					$query = $query . " WHERE " . $where . " ";
				}			
			} else {
				if ($where != '') { 
## Tra la la. A co, jeśli w $queryString już jest WHERE? A co więcej, już jest np. GROUP BY itp?
					$query = "SELECT * FROM ( " . $queryString . (strpos(strtoupper($queryString), "WHERE") === false ? " WHERE " : " AND ") . $where . " ) as ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ";
				} else {			
					$query = "SELECT * FROM ( " . $queryString . " ) as ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ";
				}
			}
			if ($orderColumn != null) {
##					$query = $query . "\n ORDER BY " . (is_array($orderColumn) ? implode(",", $orderColumn) : $orderColumn) . " $orderMode";
				$query = $query . "\n ORDER BY $orderColumn $orderMode, ${tr.f_v($entity.prefix)}_id $orderMode";
			}
			if ($showRows != 'all') {
				$query = $query . "\n LIMIT " . ($showPage - 1) * $showRows. ", " . $showRows;
			}
			return self::internalSelect($query, null, $types, $values);
		}

		function getAllRecordCount($where = '', $queryString = '', $types = null, $values = null) {
			$componentParams = null;
## to ma byc sprawdzane tylko dla tej klasy a nie aktualnego poba!
## na razie moze zrobmy, ze nie dotyczy slownikow? Ale to chyba i tak trzeba bedzie zmienic...
#if (!$entity.dictionary && !$entity.weak)
			if (P('only_private_records', "0") == "1") {
				$privateCondition = " ${tr.f_v($entity.prefix)}_usr_created_id = ? ";
				if ($where == '') {
					$where = $privateCondition;
				} else {
					$where = $where . " AND " . $privateCondition;
				}
				$types .= "i";
				$values[] = virgoUser::getUserId();
			}
#end			
			if (false) { //$componentParams->get('only_records_in_valid_range') == "1") {
				$rangeCondition = "";
#foreach ($property in $entity.properties)
#if ($property.dataType.name == "DATE")
#if ($property.customProperty)
#getParamValue($property.customProperty "Validity")
#if ($virgoWartosc == "true")
#if ($property.name.endsWith(" od") || $property.name.endsWith(" from"))
				$rangeCondition = $rangeCondition . " ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} <= curdate()) ";
#end ## od-from
#if ($property.name.endsWith(" do") || $property.name.endsWith(" to"))
				if ($rangeCondition == "") {
					$rangeCondition = " ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} >= curdate()) ";
				} else {
					$rangeCondition = " ( " . $rangeCondition . " AND ( ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} IS NULL OR ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} >= curdate()) ) ";
				}
#end ## do-to
#end ## virgoWartosc
#end ## property.custmoProperty
#end ## dataType == "DATE"
#end ## foreach property			
				if ($rangeCondition != "") {
					if ($where == '') {
						$where = $rangeCondition;
					} else {
						$where = $where . " AND " . $rangeCondition;
					}
				}
			}			
			if ($queryString == '') {
				$query = "SELECT COUNT(${entity.prefix}_id) cnt FROM ${tr.f_v($entity.namePlural)}";
				if ($where != '') {
					$query = $query . " WHERE " . $where . " ";
				}	
			} else {
				if ($where != '') {
					$query = "SELECT COUNT(*) as cnt FROM (" . $queryString . " WHERE " . $where . ") as ${tr.f_v($entity.namePlural)} ";
				} else {
					$query = "SELECT COUNT(*) as cnt FROM (" . $queryString . ") as ${tr.f_v($entity.namePlural)} ";
				}
			}
			
			$results = self::internalSelect($query, null, $types, $values);
			return $results[0]['cnt'];
		}

		static function getById($id) {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->load($id);
			return $instance;
		}
		
		function load($id, $fetchUsernames = false) {
## zanim zaladujesz, sprawdz, czy dostep jest autoryzowany (czy token jest prawidlowy)
## PRZENIESIONE DO setContextId
##			if (!A($id)) {
##				L('Access denied', '', 'ERROR');
##				return;
##			}
			if (!is_null($id) && trim($id) != "") {
				$query = "SELECT * FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = {$id}";
				$results = QR($query);
				if (!isset($results)) {
					L('Invalid query', $query, 'ERROR');
				} else {
					if (count($results) > 0) {
						$row = $results[0];
						$this->${tr.f_v($entity.prefix)}_id = $row['${tr.f_v($entity.prefix)}_id'];
#foreach ($property in $entity.properties)
#parseFileWithStandardText("defs/dataTypes/php/load/${tr.f_v($property.dataType.name)}" "$this->${tr.fV($entity.prefix)}_${tr.f_v($property.name)} = $row['${tr.fV($entity.prefix)}_${tr.f_v($property.name)}'];")		
#end
#foreach ($relation in $entity.childRelations)
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end				
						$this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id = $row['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id'];
#end
						if ($fetchUsernames) {
							if ($row['${tr.f_v($entity.prefix)}_date_created']) {
								if ($row['${tr.f_v($entity.prefix)}_usr_created_id'] == 0) {
									$createdBy = "anonymous";
								} else {
									$createdByUser = new virgoUser((int)$row['${tr.f_v($entity.prefix)}_usr_created_id']);
									$createdBy = $createdByUser->getUsername();
								}
							} else {
								$createdBy = "";
							}
							if ($row['${tr.f_v($entity.prefix)}_date_modified']) {
								if ($row['${tr.f_v($entity.prefix)}_usr_modified_id'] == $row['${tr.f_v($entity.prefix)}_usr_created_id']) {
									$modifiedBy = $createdBy;
								} else {
									if ($row['${tr.f_v($entity.prefix)}_usr_modified_id'] == 0) {
										$modifiedBy = "anonymous";
									} else {
										$modifiedByUser = new virgoUser((int)$row['${tr.f_v($entity.prefix)}_usr_modified_id']);
										$modifiedBy = $modifiedByUser->getUsername();
									}
								}
							} else {
								$modifiedBy = "";
							}
						}
						$this->${tr.f_v($entity.prefix)}_date_created = $row['${tr.f_v($entity.prefix)}_date_created'];
						$this->${tr.f_v($entity.prefix)}_usr_created_id = $fetchUsernames ? $createdBy : $row['${tr.f_v($entity.prefix)}_usr_created_id'];
						$this->${tr.f_v($entity.prefix)}_date_modified = $row['${tr.f_v($entity.prefix)}_date_modified'];
						$this->${tr.f_v($entity.prefix)}_usr_modified_id = $fetchUsernames ? $modifiedBy : $row['${tr.f_v($entity.prefix)}_usr_modified_id'];
						$this->${tr.f_v($entity.prefix)}_virgo_title = $row['${tr.f_v($entity.prefix)}_virgo_title'];
					}
				}
			}
		}

## to jest mylaca nazwa! moze jednak na wszelki wypadek zrobic tuttaj this->store()?		
		function changeOwnershipAndStore($userId) {
			$query = " UPDATE ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} SET ${entity.prefix}_usr_created_id = {$userId} WHERE ${entity.prefix}_id = {$this->getId()} ";
			$result = Q($query);
			if (!$result) {
				L(mysqli_error(), $query, 'ERROR');
			}
			$this->${tr.f_v($entity.prefix)}_usr_created_id = $userId;
		}
		
		static function getRecordCreatedBy($userId) {
			$rets = virgo${tr.FV($entity.name)}::selectAllAsObjectsStatic('${entity.prefix}_usr_created_id = ' . $userId);
			foreach ($rets as $ret) {
				return $ret;
			}
			return null;
		}

		function getCreatorUserId() {
			return $this->${tr.f_v($entity.prefix)}_usr_created_id;
		}

		function getCreatorUsername() {
			$createdByUser = new virgoUser($this->getCreatorUserId());
			return $createdByUser->getUsername();
		}

		function getVirgoCreationDate() {
			return $this->${tr.f_v($entity.prefix)}_date_created;
		}

		function loadFromDB() {
			$this->load(self::loadIdFromRequest());
		}
		
		static function loadIdFromRequest($id = null) {
#if ($entity.view)		
			return is_null($id) ? R('${tr.fV($entity.prefix)}_id_' . $_SESSION['current_portlet_object_id']) : $id;
#else
			return intval(is_null($id) ? R('${tr.fV($entity.prefix)}_id_' . $_SESSION['current_portlet_object_id']) : $id);
#end			
##					$eventColumn = "${tr.f_v($entity.prefix)}_" . $componentParams->get('event_column');
##					$this->$eventColumn = R($eventColumn . "_0");
		}
		
		static function lookup($lookup_id) {
			if ($lookup_id) {
				$tmp_${entity.prefix} = new virgo${tr.FV($entity.name)}();
				$tmp_${entity.prefix}->load((int)$lookup_id);
				return $tmp_${entity.prefix}->getVirgoTitle();
##				return $tmp_${entity.prefix}->${entity.prefix}_name;
			}
		} 
				
## Uzywam w AjaxParentSelect
		function printVirgoListMatchedInternal($match, $fieldName) {
			if (A(virgoUser::getUserId())) {
				$match = QE($match);
				$extraAjaxFilter = PD('extra_ajax_filter', 'virgo${tr.FV($entity.name)}');
				if (S($extraAjaxFilter)) {
					$extraAjaxFilter = " AND ({$extraAjaxFilter}) ";
				} else {
					$extraAjaxFilter = "";
				}
				$resultsLabels = $this->getVirgoList(" ${tr.f_v($entity.prefix)}_virgo_title LIKE '%{$match}%' {$extraAjaxFilter} ", false, true);
				$sizeOf = sizeof($resultsLabels);
				$maxListLabelSize = PD('ajax_max_label_list_size', 'virgo${tr.FV($entity.name)}', "10");
				if ($sizeOf > $maxListLabelSize) {
##				echo "<li class='inputbox'>" . T('RESULTS') . ": $sizeOf.</li>";
					echo json_encode(array(array("id" => '', "title" => T('RESULTS') . ": $sizeOf.")));
					return;
				}
				echo json_encode($resultsLabels);
##				while(list($id, $label)=each($resultsLabels)) {
##				echo "<li class='inputbox' id='${tr.f_v($entity.name)}_title_$id' onClick='selectedLabel(\"$fieldName\", $id)'>$label</li>";
##				}
			}
		}

		function printVirgoListMatched($match, $fieldName) {
#parseFileWithStandardText("modules_project/virgoListMatched/${tr.f_v($entity.name)}.php" "$this->printVirgoListMatchedInternal($match, $fieldName);")		
		}
		
		static function getVirgoListSize($where = '') {
			return self::getVirgoList($where = '', true);
		}

		static function getVirgoList($where = '', $sizeOnly = false, $hash = false) {
			$query = " SELECT ";
			if ($sizeOnly) {
				$query = $query . " COUNT(*) AS CNT ";
			} else {
				$query = $query . " ${tr.f_v($entity.prefix)}_id as id, ${tr.f_v($entity.prefix)}_virgo_title as title ";
			}
			$query = $query . " FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ";
			$componentParams = null;
			$tmpQuery = " WHERE 1 = 1 ";
## to ma byc sprawdzane tylko dla tej klasy a nie aktualnego poba!
## na razie moze zrobmy, ze nie dotyczy slownikow? Ale to chyba i tak trzeba bedzie zmienic...
#if (!$entity.dictionary && !$entity.weak)
			if (P('only_private_records', "0", 'virgo${tr.FV($entity.name)}', '${tr.f_v($application.name)}') == "1") {
				$privateCondition = " ${tr.f_v($entity.prefix)}_usr_created_id = " . virgoUser::getUserId() . " ";
				$tmpQuery = " WHERE " . $privateCondition . " ";
			}
#end			
			$query = $query . $tmpQuery;
#if ($entity.dictionary)
			$query = $query . " AND (${tr.f_v($entity.prefix)}_virgo_deleted IS NULL OR ${tr.f_v($entity.prefix)}_virgo_deleted = 0) ";
#end
			if ($where != '') {
				$query = $query . " AND " . $where;
			}
#set ($displayOrderPropertyName = "")
#foreach( $property in $entity.properties )
#if ($property.name == 'display order' || ${tr.f_v($property.name)} == 'kolejnosc_wyswietlania' || ${tr.f_v($property.name)} == 'order' || ${tr.f_v($property.name)} == 'kolejnosc')
#set ($displayOrderPropertyName = $property.name)
#end
#end
			if (!$sizeOnly) {
#if ($displayOrderPropertyName != "")
				$query = $query . " ORDER BY ${tr.f_v($entity.prefix)}_${tr.f_v($displayOrderPropertyName)} ";
#else			
				$query = $query . " ORDER BY ${tr.f_v($entity.prefix)}_virgo_title ";
#end
			}
			if ($sizeOnly) {
#selectResult()
				return $result[0];
			} else {						
#selectRows()
				if ($hash) {
					return $rows;
				}
				$res${tr.FV($entity.name)} = array();
				foreach ($rows as $row) {
					$res${tr.FV($entity.name)}[$row['id']] = $row['title'];
				}
				return $res${tr.FV($entity.name)};
			}
		}

		static function getVirgoListStatic($where = '', $sizeOnly = false, $hash = false) {
			$static${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
			return $static${tr.FV($entity.name)}->getVirgoList($where, $sizeOnly, $hash);
		}
		
#foreach( $relation in $entity.childRelations )
		static function get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Static($parentId) {
			return virgo${tr.FV($relation.parentEntity.name)}::getById($parentId);
		}
		
		function get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}() {
#if ($relation.name)
			return virgo${tr.FV($entity.name)}::get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Static($this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id);
#else			
			return virgo${tr.FV($entity.name)}::get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Static($this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_id);
#end						
		}
#end

#foreach( $relation in $entity.parentRelations )
		static function get${tr.FV($relation.childEntity.namePlural)}${tr.FV($relation.name)}Static($parentId, $orderBy = '', $extraWhere = null) {
			$res${tr.FV($relation.childEntity.name)} = array();
			if (!file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.f_v($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($relation.childEntity.name)}'.DIRECTORY_SEPARATOR.'controller.php')) {
				return $res${tr.FV($relation.childEntity.name)};
			}
			if (is_null($parentId) || trim($parentId) == "") {
				return $res${tr.FV($relation.childEntity.name)};
			}
			if (isset($extraWhere)) {
				$extraWhere = " AND ($extraWhere) ";
			} else {
				$extraWhere = "";
			}
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end		
			$results${tr.FV($relation.childEntity.name)}${tr.FV($relation.name)} = virgo${tr.FV($relation.childEntity.name)}::selectAll('${tr.f_v($relation.childEntity.prefix)}_${tr.f_v($entity.prefix)}${underscore}${tr.f_v($relation.name)}_id = ' . $parentId . $extraWhere, $orderBy);
			foreach ($results${tr.FV($relation.childEntity.name)}${tr.FV($relation.name)} as $result${tr.FV($relation.childEntity.name)}${tr.FV($relation.name)}) {
				$tmp${tr.FV($relation.childEntity.name)}${tr.FV($relation.name)} = virgo${tr.FV($relation.childEntity.name)}::getById($result${tr.FV($relation.childEntity.name)}${tr.FV($relation.name)}['${tr.f_v($relation.childEntity.prefix)}_id']); 
				array_push($res${tr.FV($relation.childEntity.name)}, $tmp${tr.FV($relation.childEntity.name)}${tr.FV($relation.name)});
			}
			return $res${tr.FV($relation.childEntity.name)};
		}

		function get${tr.FV($relation.childEntity.namePlural)}${tr.FV($relation.name)}($orderBy = '', $extraWhere = null) {
			return virgo${tr.FV($entity.name)}::get${tr.FV($relation.childEntity.namePlural)}${tr.FV($relation.name)}Static($this->getId(), $orderBy, $extraWhere);
		}
#end

		function validateObject($virgoOld) {
#foreach( $property in $entity.properties )
#if ($property.obligatory)
			if (
#parseFileWithStandardText("defs/dataTypes/php/nullValueCondition/${tr.f_v($property.dataType.name)}" "(is_null($this->get${tr.FV($property.name)}()) || trim($this->get${tr.FV($property.name)}()) == '')")		
			) {
				return T('FIELD_OBLIGATORY', '${tr.F_V($property.name)}');
			}			
#else
			$tmpMode = (is_null($this->getId()) || trim($this->getId()) == "" ? "create" : "form");
			if (P('show_'.$tmpMode.'_${tr.f_v($property.name)}_obligatory', "0") == "1") {
				if (
#parseFileWithStandardText("defs/dataTypes/php/nullValueCondition/${tr.f_v($property.dataType.name)}" "(is_null($this->get${tr.FV($property.name)}()) || trim($this->get${tr.FV($property.name)}()) == '')")		
				) {
					return T('FIELD_OBLIGATORY', '${tr.F_V($property.name)}');
				}			
			}
#end
#end			
#foreach( $relation in $entity.childRelations )
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
#if (!$relation.obligatory)
			$tmpMode = (is_null($this->getId()) ? "create" : "form");
			if (P('show_'.$tmpMode.'_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}_obligatory', "0") == "1") {
#end			
				if (is_null($this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id) || trim($this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id) == "") {
					if (R('create_${tr.fV($relation.childEntity.prefix)}_${tr.fV($relation.parentEntity.name)}${tr.FV($relation.name)}_' . $this->${entity.prefix}_id) == "1") { 
## daj mu jeszcze szanse sie zapisac z nowym rodzicem
## #set ($showStandardObligatoryMessage = "true")
#if (!$relation.parentEntity.dictionary)
#if ($relation.obligatory)
## #set ($showStandardObligatoryMessage = "false")
						$parent = new virgo${tr.FV($relation.parentEntity.name)}();
						$parent->loadFromRequest();
						$res = $parent->store();
						if ($res != "") {
							return $res;
						} else {
							$this->${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id = $parent->getId();
						}
#end
#end
					} else {
## #if ($showStandardObligatoryMessage == "true")
						return T('FIELD_OBLIGATORY', '${tr.F_V($relation.parentEntity.name)}', '${tr.F_V($relation.name)}');
## #end				
					}
#if (!$relation.obligatory)
				}
#end				
			}			
#end ## foreach 
#parseFile("defs/dataTypes/php/preValidation/${tr.f_v($entity.name)}")		
#foreach( $property in $entity.properties )
#parseFile("defs/dataTypes/php/validation/${tr.f_v($property.dataType.name)}")		
#end
#foreach( $property in $entity.properties )
#set ($min = "")
#set ($max = "")
#if ($property.dataType.name == "INTEGER")
#getParamValue($property.customProperty "Min")
#if ($virgoWartosc != "")
#set ($min = $virgoWartosc)
#end
#getParamValue($property.customProperty "Max")
#if ($virgoWartosc != "")
#set ($max = $virgoWartosc)
#end
#set ($min = $wartosc2)
#set ($max = $wartosc1)
#if ($min && $min.trim() != "")
			if (!is_null($this->${entity.prefix}_${tr.f_v($property.name)}) && trim($this->${entity.prefix}_${tr.f_v($property.name)}) != "") {
				if (((int)$this->${entity.prefix}_${tr.f_v($property.name)}) < $min) {
					return T('VALUE_MUST_BE_GREATER_THAN', '${tr.F_V($property.name)}', "$min");
				}
			}
#end
#if ($max && $max.trim() != '')
			if (!is_null($this->${entity.prefix}_${tr.f_v($property.name)}) && trim($this->${entity.prefix}_${tr.f_v($property.name)}) != "") {
				if (((int)$this->${entity.prefix}_${tr.f_v($property.name)}) > $max) {
					return T('VALUE_MUST_BE_LESSER_THAN', '${tr.F_V($property.name)}', "$max");
				}
			}
#end
##
#end
#end
#foreach( $property in $entity.properties )
#if ($property.pattern)
			if (!is_null($this->${entity.prefix}_${tr.f_v($property.name)}) && trim($this->${entity.prefix}_${tr.f_v($property.name)}) != "") {
				if (preg_match('/^${property.pattern}$/', $this->${entity.prefix}_${tr.f_v($property.name)}) == 0) {
					return T('FIELD_MUST_MATCH_PATTERN', '${tr.F_V($property.name)}', "^${property.pattern}$");
				}
			}
#end
#end			
##
## UNIKALNOSC NA KOLUMNACH NIEOBOWIAZKOWYCH
## W gsh wyplynal taki problem z unikalnoscia PESELa. Chca, zeby byl unikalny, ale 
## tylko wtedy, gdy jest podany. Czyli moze byc wiele NULLi. A zatem
##
## W tabeli:
##		PESEL
##		1234
##		NULL
##		ABC
## 
## Wstawiamy: '1234'. Select pojdzie " WHERE pesel = '1234' ", zwroci rekord i nie pozwoli zapisac. 
## Wstawiamy: NULL. Select w ogole nie powinien isc. 
##
## Super. A co gdy klucz jest na kilku kolumnach? Np. Imie i Nazwisko. I obie sa nieobowiazkowe?
##
## W tabeli: 
##		IMIE		NAZWISKO
## 		Adam		Malysz
##		NULL		Kowalski
##		Jan 		Tomaszewski
##		NULL		NULL
##
## Wstawiamy: 'Adam' 'Malysz'. Select: " WHERE imie = 'Adam' AND nazwisko = 'Malysz' ", zwroci rekord i nie pozwoli.
## Wstawiamy: NULL 'Malysz'. Co powinien zrobic? Pozwolic? A zatem select: " WHERE imie IS NULL AND nazwisko = 'Malysz' "?
## Wstawiamy: NULL	NULL. Co powinien zrobic? W ogole nie sprawdzac unikalnosci. No to moze w poprzednim przypadku tez nie powonien?
## 
## KONKLUZJA: Jesli w jednym z pol z klucza jest NULL, to w ogole nie sprawdzamy unikalnosci. I KROPKA (Radłów, 16 sierpnia 2012). 
		$types = "";
		$values = array();
		$skipUniquenessCheck = false;
#foreach( $uniqueKey in $entity.uniqueKeys )
		$uniqnessWhere = " 1 = 1 ";
		if (!is_null($this->${entity.prefix}_id) && $this->${entity.prefix}_id != 0) {
			$uniqnessWhere = " ${entity.prefix}_id != " . $this->${entity.prefix}_id . " ";			
		}
#foreach( $columnSet in $uniqueKey.columnSets ) ########## foreach #############
		if (!$skipUniquenessCheck) {
#if ($columnSet.property) ###### wlasciwosc a nie relacja #########
#set ($property = $columnSet.property)
#if (!$property.obligatory)
			if (
#parseFileWithStandardText("defs/dataTypes/php/nullValueCondition/${tr.f_v($property.dataType.name)}" "(is_null($this->get${tr.FV($property.name)}()) || trim($this->get${tr.FV($property.name)}()) == '')")		
			) {
				$skipUniquenessCheck = true;
			}
#end
			if (!$skipUniquenessCheck) {
#parseFileWithStandard("defs/dataTypes/php/createUniqueCheckSQL/${tr.f_v($columnSet.property.dataType.name)}" "defs/createUniqueCheckSQL.php") 
			}
#else ###### relacja a nie wlasciwosc #########
#set ($relation = $columnSet.relation)
#if (!$relation.obligatory)
			if (is_null($this->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id()) || trim($this->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id()) == '') {
				$skipUniquenessCheck = true;
			}
#end
			if (!$skipUniquenessCheck) { 
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
				$uniqnessWhere = $uniqnessWhere . ' AND ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}${underscore}id = ? ';
				$types .= "i";
				$values[] = $this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}${underscore}id;
			}
#end ###### relacja czy wlasciwosc #########	
		}	
#end ########## foreach #############
		if (!$skipUniquenessCheck) {	
			$query = " SELECT COUNT(*) FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ";
			$query = $query . " WHERE " . $uniqnessWhere;
			$result = QPL($query, $types, $values);
			if ($result[0] > 0) {
				$valeus = array();
				$colNames = array();
#foreach( $columnSet in $uniqueKey.columnSets )
#if ($columnSet.property)
				$colNames[] = T('${tr.F_V($columnSet.property.name)}');
				$values[] = $this->${entity.prefix}_${tr.f_v($property.name)}; 
#else
				$colNames[] = T('${tr.F_V($columnSet.relation.parentEntity.name)}');
#if ($columnSet.relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
				$values[] = virgo${tr.FV($columnSet.relation.parentEntity.name)}::lookup($this->${tr.f_v($entity.prefix)}_${tr.f_v($columnSet.relation.parentEntity.prefix)}_${tr.f_v($columnSet.relation.name)}${underscore}id); 
#end				
#end
				return T('UNIQNESS_FAILED', '${tr.F_V($entity.name)}', implode(', ', $colNames), implode(', ', $values));
			}
		}
#end			
#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'EMAIL')
			if (!is_null($this->get${tr.FV($property.name)}()) && trim($this->get${tr.FV($property.name)}()) != "") {
				if (preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/', strtoupper($this->get${tr.FV($property.name)}())) == 0) {
					return T('FIELD_MUST_BE_EMAIL', '${tr.F_V($property.name)}');
				}
			}
#end
#end	
#foreach ($property in $entity.properties)
#getParamValue($property.customProperty "SyncPassword")
#if ($virgoWartosc != "")
			$tmpPassword1 = R('${tr.fV($entity.prefix)}_${tr.fV($property.name)}_' . $this->${entity.prefix}_id, null);
			$tmpPassword2 = R('${tr.fV($entity.prefix)}_${tr.fV($property.name)}2_' . $this->${entity.prefix}_id, null);
			if ($tmpPassword1 != $tmpPassword2) {
				return T('PASSWORDS_DO_NOT_MATCH');
			}
#end
#end
## czy zmiana statusu zgodna z workflowem
#getStatusEntityWithWorkflow()
#if ($statusEntity)
			$virgo = $this;
## na razie nie sprawdzamy tych z pustym statusem poprzednim 			
		if (isset($virgoOld) && $virgoOld->${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id != $virgo->${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id) {
			if (!virgo${tr.FV($statusEntity.name)}Workflow::workflowTransitionAllowed($virgo->${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id, $virgoOld->${tr.f_v($entity.prefix)}_${tr.f_v($statusEntity.prefix)}_id)) {
				return T('STATUS_CHANGE_OUT_OF_WORKFLOW');
			}
		}
#end		
#parseFileWithStandardText("modules_project/validate/${tr.f_v($entity.name)}.php" '			return "";')		
		}

#set ($entityBackup = $entity)		
#foreach( $entity in $application.entities )
#getStatusEntityWithWorkflow()
#if ($statusEntity)
#set ($samaNazwa = ${tr.FV($statusEntity.name)})		
#set ($entityWithWorkflowSuffix = "${samaNazwa}Workflow")		
#if (${tr.FV($entityBackup.name)} == $entityWithWorkflowSuffix)
		static function workflowTransitionAllowed($fromId, $toId) {
			$tmpCheck = new virgo${tr.FV($entityBackup.name)}();
## 2 nastepne linijki sa pozornie na odwrot (prev -> virgo, next -> virgoOld, ale to zalezy jak sie te relacje czyta)			
			$tmpCheck->set${tr.FV($statusEntity.prefix)}PrevId($fromId);
			$tmpCheck->set${tr.FV($statusEntity.prefix)}NextId($toId);
			$existingArray = $tmpCheck->existingConnections();
			return (count($existingArray) > 0);
		} 
#end
#end		
#end
#set ($entity = $entityBackup)		
				
		function beforeStore($virgoOld) {
#parseFileWithStandardText("modules_project/beforeStore/${tr.f_v($entity.name)}.php" '			return "";')		
		}		
		
		function afterStore($virgoOld) {
#parseFileWithStandardText("modules_project/afterStore/${tr.f_v($entity.name)}.php" '			return "";')		
		}
		
		function beforeDelete() {
#parseFileWithStandardText("modules_project/beforeDelete/${tr.f_v($entity.name)}.php" '			return "";')		
		}

		function afterDelete() {
#parseFileWithStandardText("modules_project/afterDelete/${tr.f_v($entity.name)}.php" '			return "";')		
		}
		
		function getCurrentRevision() {
			$query = "SELECT IFNULL(MAX(revision), 0) as revision FROM  ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = " . $this->getId();
#selectResult()
			return $result[0];
		}
		
		function storeRecordChange($virgoOld, $user) {
			$ip = $_SERVER['REMOTE_ADDR'];
			$username = $user->getUsername();
			$user_id = $user->getId();
			$new_revision = $this->getCurrentRevision() + 1;
			if ($new_revision == 1 && !is_null($virgoOld)) {
				$colNames = "";
				$values = "";
				$objectToStore = $virgoOld;
#foreach ($property in $entity.properties)
#parseFileWithStandard("defs/dataTypes/php/storeChanges/${tr.f_v($property.dataType.name)}" "defs/storeChanges.php")
#end
#foreach ($relation in $entity.childRelations)
#if ($relation.name)
				$colNames = $colNames . ", ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id";
#else
				$colNames = $colNames . ", ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id";
#end
				$values = $values . ", " . (is_null($objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id()) || $objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id() == "" ? "null" : $objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id());
#end
				$query = "INSERT INTO ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} (revision, ip, username, user_id, timestamp, ${tr.f_v($entity.prefix)}_id" . $colNames . ") VALUES ($new_revision, '$ip', '$username', $user_id, '" . date('Y-m-d H:i:s') . "', " . $this->getId() . $values . ") ";
				Q($query);
				$new_revision = 2;
			}
## Na razie tylko typy proste!!! TODO	
			$colNames = "";
			$values = "";
			$objectToStore = $this;
			$nullifiedProperties = "";
#foreach ($property in $entity.properties)
			if (is_null($virgoOld) || $virgoOld->get${tr.FV($property.name)}() != $objectToStore->get${tr.FV($property.name)}()) {
				if (is_null($objectToStore->get${tr.FV($property.name)}())) {
					$nullifiedProperties = $nullifiedProperties . "${tr.f_v($property.name)},";
				} else {
#parseFileWithStandard("defs/dataTypes/php/storeChanges/${tr.f_v($property.dataType.name)}" "defs/storeChanges.php")
				}
			}
#end
			if ($nullifiedProperties != "") {
				$colNames = $colNames . ", nullified_properties";
				$values = $values . ", '" . $nullifiedProperties . "'";
			}
#foreach ($relation in $entity.childRelations)
			if (is_null($virgoOld) || ($virgoOld->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id() != $objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id() && ($virgoOld->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id() != 0 || $objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id() != ""))) { 
#if ($relation.name)
				$colNames = $colNames . ", ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id";
#else
				$colNames = $colNames . ", ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id";
#end
				$values = $values . ", " . (is_null($objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id()) ? "null" : ($objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id() == "" ? "0" : $objectToStore->get${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id()));
			}
#end
			$query = "INSERT INTO ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} (revision, ip, username, user_id, timestamp, ${tr.f_v($entity.prefix)}_id" . $colNames . ") VALUES ($new_revision, '$ip', '$username', $user_id, '" . date('Y-m-d H:i:s') . "', " . $this->getId() . $values . ") ";
			Q($query);
		}
		
		function virgoTitleColumnExists() {
			$exists = false;
			$columns = Q("SHOW COLUMNS FROM ${application.prefix}_${tr.f_v($entity.namePlural)}");
			if (isset($columns) && $columns !== false) {
				foreach ($columns as $c){
					if($c['Field'] == '${tr.f_v($entity.prefix)}_virgo_title'){
						return true;
					}
				}
			}
			return false;
		}
		
		function addVirgoTitleColumn() {
			$query = "ALTER TABLE ${application.prefix}_${tr.f_v($entity.namePlural)} ADD COLUMN (${tr.f_v($entity.prefix)}_virgo_title VARCHAR(255));";
			Q($query);
			$this->fillVirgoTitles();
		}
		
		var $_error = null;
		
		function getError() {
			return $this->_error;
		}
		
		function parentStore($updateNulls = false, $log = false) {
			$types = "";
			$values = array();
			if (isset($this->${tr.f_v($entity.prefix)}_id) && $this->${tr.f_v($entity.prefix)}_id != "") {
				$query = "UPDATE ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} SET ";
#foreach ($property in $entity.properties)
#parseFileWithStandard("defs/dataTypes/php/store/${tr.f_v($property.dataType.name)}" "defs/store.php")		
#end
#foreach ($relation in $entity.childRelations)
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end 				
				if (isset($this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id) && trim($this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id) != "") {
					$query = $query . " ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id = ? , ";
					$types = $types . "i";
					$values[] = $this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id;
				} else {
					$query = $query . " ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id = NULL, ";
				}
#end
#if ($entity.dictionary)
				if (isset($this->${entity.prefix}_virgo_deleted)) {
					$query = $query . " ${tr.f_v($entity.prefix)}_virgo_deleted = ? , ";
					$types = $types . "i";
					$values[] = $this->${entity.prefix}_virgo_deleted;
				} else {
					$query = $query . " ${tr.f_v($entity.prefix)}_virgo_deleted = NULL , ";
				}
#end
				$query = $query . " ${tr.f_v($entity.prefix)}_virgo_title = ? , ";
				$types = $types . "s";
				$values[] = $this->getVirgoTitle();

				$query = $query . " ${tr.f_v($entity.prefix)}_date_modified = ? , ";
				$types = $types . "s";
				$values[] = $this->${tr.f_v($entity.prefix)}_date_modified;

				$query = $query . " ${tr.f_v($entity.prefix)}_usr_modified_id = ? ";
				$types = $types . "i";
				$values[] = $this->${tr.f_v($entity.prefix)}_usr_modified_id;

				$query = $query . " WHERE ${tr.f_v($entity.prefix)}_id = ? ";
				$types = $types . "i";
				$values[] = $this->${tr.f_v($entity.prefix)}_id;
			} else {
				$query = "INSERT INTO ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ( ";
#foreach ($property in $entity.properties)
#parseFileWithStandard("defs/dataTypes/php/storeInsertColumns/${tr.f_v($property.dataType.name)}" "defs/storeInsertColumns.php")		
#end
#foreach ($relation in $entity.childRelations)
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end 				
				$query = $query . " ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id, ";
#end
				$query = $query . " ${tr.f_v($entity.prefix)}_virgo_title, ${tr.f_v($entity.prefix)}_date_created, ${tr.f_v($entity.prefix)}_usr_created_id) VALUES ( ";
#foreach ($property in $entity.properties)
#parseFileWithStandard("defs/dataTypes/php/storeInsertValues/${tr.f_v($property.dataType.name)}" "defs/storeInsertValues.php")		
#end
#foreach ($relation in $entity.childRelations)
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end 				
				if (isset($this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id) && trim($this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id) != "") {
					$query = $query . " ? , ";
					$types = $types . "i";
					$values[] = $this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id;
				} else {
					$query = $query . " NULL, ";
				}
#end
				$query = $query . " ? , ? , ? ) ";
				$types = $types . "ssi";
				$values[] = $this->getVirgoTitle();
				$values[] = $this->${tr.f_v($entity.prefix)}_date_created;
				$values[] = $this->${tr.f_v($entity.prefix)}_usr_created_id;
			}
			$results = Q($query, $log, true, $types, $values);
			if (!$results) {
				$this->_error = QER();
				L('Invalid query ' . $query, $this->_error, 'FATAL');
				return false;
			} else {
				if (!isset($this->${tr.f_v($entity.prefix)}_id) || $this->${tr.f_v($entity.prefix)}_id == "") {
					$this->${tr.f_v($entity.prefix)}_id = QID();
				}
				if ($log) {
					L("$entity.name stored successfully", "id = {$this->${tr.f_v($entity.prefix)}_id}", "TRACE");
				}
				return true;
			}
		}
		
#foreach( $relation in $entity.parentRelations )
## sprawdz, czy te dzieci to przypadkiem nie jest tabela n:m ktora ma drugiego rodzica, ktory jest slownikiem
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)

#if ($weakEntityRelation.name)
#set ($kreska1 = "_")
#else
#set ($kreska1 = "")
#end
#if ($secondParentRelation.name)
#set ($kreska2 = "_")
#else
#set ($kreska2 = "")
#end
		static function add${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Static($thisId, $id) {
			$query = " SELECT COUNT(${tr.f_v($weakEntityRelation.childEntity.prefix)}_id) AS cnt FROM ${tr.f_v($application.prefix)}_${tr.f_v($weakEntityRelation.childEntity.namePlural)} WHERE ${tr.f_v($weakEntityRelation.childEntity.prefix)}_${tr.f_v($entity.prefix)}_${tr.f_v($weakEntityRelation.name)}${kreska1}id = {$thisId} AND ${tr.f_v($weakEntityRelation.childEntity.prefix)}_${tr.f_v($secondParentRelation.parentEntity.prefix)}_${tr.f_v($secondParentRelation.name)}${kreska2}id = {$id} ";
			$res = Q1($query);
			if ($res == 0) {
				$new${tr.FV($weakEntityRelation.childEntity.name)} = new virgo${tr.FV($weakEntityRelation.childEntity.name)}();
				$new${tr.FV($weakEntityRelation.childEntity.name)}->set${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id($id);
				$new${tr.FV($weakEntityRelation.childEntity.name)}->set${tr.FV($entity.name)}${tr.FV($relation.name)}Id($thisId);
				return $new${tr.FV($weakEntityRelation.childEntity.name)}->store();
			}			
			return "";
		}
		
		function add${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}($id) {
			return virgo${tr.FV($entity.name)}::add${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Static($this->getId(), $id);
		}
		
		static function remove${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Static($thisId, $id) {
			$query = " SELECT ${tr.f_v($weakEntityRelation.childEntity.prefix)}_id AS id FROM ${tr.f_v($application.prefix)}_${tr.f_v($weakEntityRelation.childEntity.namePlural)} WHERE ${tr.f_v($weakEntityRelation.childEntity.prefix)}_${tr.f_v($entity.prefix)}_${tr.f_v($weakEntityRelation.name)}${kreska1}id = {$thisId} AND ${tr.f_v($weakEntityRelation.childEntity.prefix)}_${tr.f_v($secondParentRelation.parentEntity.prefix)}_${tr.f_v($secondParentRelation.name)}${kreska2}id = {$id} ";
			$res = QR($query);
			foreach ($res as $re) {
				$new${tr.FV($weakEntityRelation.childEntity.name)} = new virgo${tr.FV($weakEntityRelation.childEntity.name)}($re['id']);
				return $new${tr.FV($weakEntityRelation.childEntity.name)}->delete();
			}			
			return "";
		}
		
		function remove${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}($id) {
			return virgo${tr.FV($entity.name)}::remove${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Static($this->getId(), $id);
		}
#end
#end
#end		
		
		function store($log = false) {
			$virgoOld = null;
			if ($this->${entity.prefix}_id) {
				$virgoOld = new virgo${tr.FV($entity.name)}($this->${entity.prefix}_id);
			}
			$validationMessageText = $this->beforeStore($virgoOld);
			if (!is_null($validationMessageText) && trim($validationMessageText) != "") {
				$this->logWarn('Before store failed for id = ' . $this->getId() . ": " . $validationMessageText);
				return trim($validationMessageText);				
			} else {
				$validationMessageText = $this->validateObject($virgoOld);
				if (!is_null($validationMessageText) && trim($validationMessageText) != "") {
					$this->logWarn('Validation failed for id = ' . $this->getId() . ": " . $validationMessageText);
					return trim($validationMessageText);				
				} else {
					$userId = virgoUser::getUserId();			
					if ($this->${entity.prefix}_id) {			
						$this->${entity.prefix}_date_modified = date("Y-m-d H:i:s");
						$this->${entity.prefix}_usr_modified_id = $userId;
					} else {
						$this->${entity.prefix}_date_created = date("Y-m-d H:i:s");
						$this->${entity.prefix}_usr_created_id = $userId;
					}
					$this->${entity.prefix}_virgo_title = $this->getVirgoTitle(); 
## no to w koncu zapisujemy te nulle czy nie?			
## jesli nie, to jak zmazac wartosc z pola i zapisac?
					if (!$this->parentStore(true, $log)) {
## czy problemem jest brak kolumny xxx_virgo_title?
						$exists = $this->virgoTitleColumnExists();
						if(!$exists){
							$this->addVirgoTitleColumn();
							if (!$this->parentStore(true, $log)) {
								$error = $this->getError();
								$this->logFatal('Error storing "$entity.name" with id = ' . $this->getId() . ": " . $error);
							return 'A problem ocurred while saving the record in the database. Please contact site Administrator.';
							}
						} else {
							$error = $this->getError();
							$this->logFatal('Error storing "$entity.name" with id = ' . $this->getId() . ": " . $error);
							return 'A problem ocurred while saving the record in the database. Please contact site Administrator.';
						}
					}
#foreach( $relation in $entity.parentRelations )
## sprawdz, czy te dzieci to przypadkiem nie jest tabela n:m ktora ma drugiego rodzica, ktory jest slownikiem
#if ($relation.childEntity.weak)
#findSecondParentForNMRelation()
#if ($secondParentRelation)
					if (!is_null($this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray)) {
						foreach ($this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToAddArray as $${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id) {
							$ret = $this->add${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}((int)$${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id);
							if ($ret != "") {
								L($ret, '', 'ERROR');
							}
						}
					}
					if (!is_null($this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToDeleteArray)) {
						foreach ($this->_${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}IdsToDeleteArray as $${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id) {
							$ret = $this->remove${tr.FV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}((int)$${tr.fV($secondParentRelation.parentEntity.name)}${tr.FV($secondParentRelation.name)}Id);
							if ($ret != "") {
								L($ret, '', 'ERROR');
							}
						}
					}
#end
#end
#end
#if ($entity.versioned)
					$user = virgoUser::getUser();
					$this->storeRecordChange($virgoOld, $user);
#end
#set ($synchronizeUser = "0")					
#foreach ($property	in $entity.properties)
#readSyncValues()
#if ($sync == "1") 
#set ($synchronizeUser = "1")
#end
#end
#if ($synchronizeUser == "1") 
## trzeba zsynchronizowac z userami Joomli
					if (is_null($virgoOld)) {
						jimport('joomla.user.helper');
						jimport('joomla.application.component.controller');
						include_once(PORTAL_PATH . "/components/com_user/controller.php"); 
						$newUser = new JUser();
#foreach ($property	in $entity.properties)
#getParamValue($property.customProperty "SyncUsername")
#if ($virgoWartosc != "")
						$tmpUsername = $this->get${tr.FV($property.name)}();
#end
#end
#foreach ($property	in $entity.properties)
#getParamValue($property.customProperty "SyncEmail")
#if ($virgoWartosc != "")
						$tmpEmail = $this->get${tr.FV($property.name)}();
#end
#end
#foreach ($property	in $entity.properties)
#getParamValue($property.customProperty "SyncPassword")
#if ($virgoWartosc != "")
						$tmpPassword = $this->get${tr.FV($property.name)}();
#end
#end
						if ((is_null($tmpPassword) || trim($tmpPassword) == "")) {
							$tmpPassword = $this->generatePassword(9, 7);
						}
						$array = array(
							'name' => $tmpUsername,
							'username' => $tmpUsername, 
							'email' => $tmpEmail, 
							'password' => $tmpPassword, 
							'password2' => $tmpPassword
						);
						if (!$newUser->bind($array)) {
							return $newUser->getError();
						}
						$newUser->set('usertype', "Registered");
						$newUser->set('gid', 18);
//						$now =& JFactory::getDate();
						$newUser->set( 'registerDate', $now->toMySQL() );
						$componentParams = null;
						$userActivation = "0"; //$componentParams->get('user_activation');
						if ($userActivation == "1") {
							$usersConfig 	= null;
							$useractivation = $usersConfig->get( 'useractivation' );
							if ($useractivation == '1')	{
								jimport('joomla.user.helper');
								$newUser->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
								$newUser->set('block', '1');
							}					
						}
						if ( !$newUser->save() ) {
							return $newUser->getError();
						}
						$this->${entity.prefix}_usr_created_id = $newUser->id;
						parent::store(false);
						UserController::_sendMail($newUser, $tmpPassword, 2);
					} else {
						$tmpUser = new JUser($this->${entity.prefix}_usr_created_id);
#foreach ($property	in $entity.properties)
#getParamValue($property.customProperty "SyncUsername")
#if ($virgoWartosc != "")
						$tmpUsername = $this->get${tr.FV($property.name)}();
#end
#end
#foreach ($property	in $entity.properties)
#getParamValue($property.customProperty "SyncEmail")
#if ($virgoWartosc != "")
						$tmpEmail = $this->get${tr.FV($property.name)}();
#end
#end
#foreach ($property	in $entity.properties)
#getParamValue($property.customProperty "SyncPassword")
#if ($virgoWartosc != "")
						$tmpPassword = $this->get${tr.FV($property.name)}();
#end
#end
						$array = array();
						if ($tmpUser->username != $tmpUsername) {
							$array['username'] = $tmpUsername;
							$array['name'] = $tmpUsername;
						}
						if ($tmpUser->email != $tmpEmail) {
							$array['email'] = $tmpEmail;
						}
						if ($tmpUser->password != md5($tmpPassword) && $tmpUser->password != $tmpPassword) {
							$array['password'] = $tmpPassword;
							$array['password2'] = $tmpPassword;
						}
						if (sizeof($array) > 0) {
							if (!$tmpUser->bind($array)) {
								return $tmpUser->getError();
							}
							if ( !$tmpUser->save() ) {
								return $tmpUser->getError();
							}
						}
					}
#end
## jednal afterStore musi byc przed triggerami, bo czasami te triggery korzystaja z pol automatycznych wyliczanych w afterStore					
					$ret = $this->afterStore($virgoOld);
					if (isset($ret) && $ret != "") {
						return $ret;
					}

#getStatusEntityWithWorkflow()
#if ($statusEntity)
					$virgo = $this;
## #insertLifecycleHistory($statusEntity)
#end
				}
			}
			return "";
		}

#getStatusEntityWithWorkflow()
#if ($statusEntity)
		static function portletActionShowStatusLog() {
			self::setDisplayMode("STATUS_LOG");
		}
#end		
		
		static function portletActionVirgoDefault() {
			$ret = 0;
#parseFile("modules_project/defaultAction/${tr.f_v($entity.name)}.php")
			return $ret;
		}

		function parentDelete() {
			$query = "DELETE FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = {$this->${tr.f_v($entity.prefix)}_id}";
			$results = Q($query);
			if (!$results) {
				L('Invalid query', $query, 'ERROR');
			}
		}

		function delete() {
			$this->beforeDelete();
#if (!$entity.dictionary)
#foreach( $relation in $entity.parentRelations )
			$list = $this->get${tr.FV($relation.childEntity.namePlural)}${tr.FV($relation.name)}();
			if (sizeof($list) > 0) {
#if ($relation.identifying)
				foreach ($list as $childRecord) {
					$childRecord->delete();
				}
#else
				$name = $this->getVirgoTitle();
				if (!is_null($name) && trim($name) != "") {
					$name = "'" . $name . "' ";
				}
				return T('CANT_DELETE_PARENT', '${tr.F_V($entity.name)}', '${tr.F_V($relation.childEntity.name)}', $name);
#end
			}
#end
#end
			self::removeFromContext();
#if ($entity.dictionary)
			$this->${entity.prefix}_virgo_deleted = true;
			$this->${entity.prefix}_date_modified = date("Y-m-d H:i:s");
			$userId = virgoUser::getUserId();
			$this->${entity.prefix}_usr_modified_id = $userId;
			$this->parentStore(true);
#else
			$this->parentDelete();
#end
#set ($synchronizeUser = "0")					
#foreach ($property	in $entity.properties)
#readSyncValues()
#if ($sync == "1") 
#set ($synchronizeUser = "1")
#end
#end
#if ($synchronizeUser == "1")
			$tmpUser = new JUser($this->${entity.prefix}_usr_created_id);
			if ( !$tmpUser->delete() ) {
				return $tmpUser->getError();
			}
#end
			$this->afterDelete();
			return "";
		}
		
## safer, when huge amount of big objects may be selected. In this case use 
## selectAllAsIds and construct each object one by one to avoid "Allowed memory 
## size of ... bytes exhausted (tried to allocate ... bytes)"   		
		static public function selectAllAsIdsStatic($where = '', $idsNotArrayOfIds = false) {
			$tmp = new virgo${tr.FV($entity.name)}();
			return $tmp->selectAllAsIds($where, $idsNotArrayOfIds);
		}
		
		public function selectAllAsIds($where = '', $idsNotArrayOfIds = false) {
			$query = "SELECT ${tr.f_v($entity.prefix)}_id as id FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}";
			if ($where != '') {
				$query = $query . " WHERE $where ";
			}
			$orderBy = "";
			if (property_exists($this, '${tr.f_v($entity.prefix)}_order_column')) {
				$orderBy = " ORDER BY ${tr.f_v($entity.prefix)}_order_column ASC ";
			} 
			if (property_exists($this, '${tr.f_v($entity.prefix)}_kolejnosc_wyswietlania')) {
				$orderBy = " ORDER BY ${tr.f_v($entity.prefix)}_kolejnosc_wyswietlania ASC ";
			} 
			$query = $query . $orderBy;
			return self::internalSelect($query, $idsNotArrayOfIds ? 'id' : null);
		}		
		
		static public function selectAllAsObjectsStatic($where = '') {
			$tmp = new virgo${tr.FV($entity.name)}();
			return $tmp->selectAllAsObjects($where);
		}
		
		public function selectAllAsObjects($where = '') {
			$results =  $this->selectAllAsIds($where);
			$ret = array(); 
			foreach ($results as $result) {
				$tmpObj = new virgo${tr.FV($entity.name)}($result['id']);
				$ret[] = $tmpObj;
			}
			return $ret;
		}
		
		function fillVirgoTitles() {
//			$results = $this->selectAllAsObjects();
//			foreach ($results as $result) {
//				$title = ...getEscaped($result->getVirgoTitle());
//				$id = $result->getId();
//				$query = " UPDATE ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} SET ${tr.f_v($entity.prefix)}_virgo_title = '$title' WHERE ${tr.f_v($entity.prefix)}_id = $id";
//				if (!mysqli_query($query)) {
//					JError::raiseWarning( 0, "FILL VIRGO TITLES: " . mysqli_error() . " QUERY: " . $query);
//				}
//			}
		}
## tu w zasadzie powinien byc warunek: jesli slownik lub jesli kolumna ma klucz unikalny!
## zrobione na kluczu unikalnym. To ponizej trzymane tylko dla kompatybilnosci
## #if ($entity.dictionary)		
#foreach( $property in $entity.properties )
#if ( $property.name == 'token' || $property.name == 'name' || $property.name == 'symbol' || $property.name == 'nazwa' || $property.name == 'kod' || $property.name == 'code')
		static function getBy${tr.FV($property.name)}Static($token) {
			$tmpStatic = new virgo${tr.FV($entity.name)}();
			$tmpId = $tmpStatic->getIdBy${tr.FV($property.name)}($token);
			$tmpStatic->load($tmpId);
			return $tmpStatic;
		}
		
		static function getIdBy${tr.FV($property.name)}Static($token) {
			$tmpStatic = new virgo${tr.FV($entity.name)}();
			return $tmpStatic->getIdBy${tr.FV($property.name)}($token);
		}
		
		function getIdBy${tr.FV($property.name)}($token) {
			$res = $this->selectAll(" ${entity.prefix}_${tr.f_v($property.name)} = ?", "", "s", array($token));
			foreach ($res as $r) {
				return $r['${entity.prefix}_id'];
			}
			return null;
		}
#end
#end		
## #end		
		static function getIdByVirgoTitleStatic($token) {
			$tmpStatic = new virgo${tr.FV($entity.name)}();
			return $tmpStatic->getIdByVirgoTitle($token);
		}
		
		function getIdByVirgoTitle($token) {
			$res = $this->selectAll(" ${entity.prefix}_virgo_title = '" . $token . "'");
			foreach ($res as $r) {
				return $r['${entity.prefix}_id'];
			}
			return null;
		}

#if ($entity.name == 'system parameter')
		function getValueByNameNonStatic($token) {
			$res = $this->selectAll(" ${entity.prefix}_name = '" . $token . "'");
			foreach ($res as $r) {
				return $r['${entity.prefix}_value'];
			}
			return null;
		}
		
		static function getValueByName($token, $default = null) {
			$sysParam = new virgo${tr.FV($entity.name)}();
			$ret = $sysParam->getValueByNameNonStatic($token);
			if (is_null($ret)) {
				return $default;
			}
			return $ret;
		}
#end
#if ($entity.name == 'system log')
		private static function log($level, $message) { 
//			$user =& JFactory::getUser();			
			$log = new virgoSystemLog();
			$log->slg_date_time = date("Y-m-d H:i:s");
			$log->slg_level = $level;
			$log->slg_username = $user->username;
			$log->slg_message = $message;
			$log->store();
		}
		
		static function logInfo($message) {
			$logLevel = virgoSystemLog::getLogLevel();
			if ($logLevel == "D" || $logLevel == "I") { 
				virgoSystemLog::log("I", $message);
			}
		}
		
		static function logDebug($message) {
			$logLevel = virgoSystemLog::getLogLevel();			
			if ($logLevel == "D") { 
				virgoSystemLog::log("D", $message);
			}
		}
		
		private static function getLogLevel() {
/*			$systemParameter = new virgoSystemParameter();
			$paramValue = $systemParameter->getValueByName("LOG_LEVEL");
			if (is_null($paramValue) || trim($paramValue == "")) {
				$paramValue = "I";
			}*/
			$componentParams = null;
			return "I"; //$componentParams->get('log_level');			
		}
		
#end


		private static function setSessionValue($namespace, $name, $value) {
			$_SESSION[$namespace . "-" . $name] = $value;
		}

		private static function getSessionValue($namespace, $name, $default) {
			if (isset($_SESSION[$namespace . "-" . $name])) {
				return $_SESSION[$namespace . "-" . $name];
			}
			virgo${tr.FV($entity.name)}::setSessionValue($namespace, $name, $default);
			return $default;
		}
		
		static function setRemoteSessionValue($name, $value, $menuItem) {
			virgo${tr.FV($entity.name)}::setSessionValue('${tr.FV($application.name)}_${tr.FV($entity.name)}-' . $menuItem, $name, $value);
		}
		
		static function getRemoteSessionValue($name, $default, $menuItem) {
			return virgo${tr.FV($entity.name)}::getSessionValue('${tr.FV($application.name)}_${tr.FV($entity.name)}-' . $menuItem, $name, $default);
		}
		
		static function setLocalSessionValue($name, $value) {
			virgo${tr.FV($entity.name)}::setRemoteSessionValue($name, $value, $_SESSION['current_portlet_object_id']);
		}
		
		static function getLocalSessionValue($name, $default) {
			return virgo${tr.FV($entity.name)}::getRemoteSessionValue($name, $default, $_SESSION['current_portlet_object_id']);
		}

		private static function setGlobalSessionValue($name, $value) {
			virgo${tr.FV($entity.name)}::setSessionValue('GLOBAL', $name, $value);
		}
		
		private static function getGlobalSessionValue($name, $default) {
			return virgo${tr.FV($entity.name)}::getSessionValue('GLOBAL', $name, $default);
		}
		
/*		static function isDebug() {
			$session = &JFactory::getSession();
			$debug = $session->get("VIRGO_DEBUG_MODE");
			return ($debug == "ON");

		} */
		
		static function getComponentByMenuId($menu, $masteritemid) {
			$masteritem = $menu->getItem($masteritemid);
			$masterComponentName = str_replace("index.php?option=", "", $masteritem->link);
			$masterComponentName = substr($masterComponentName, 8);
			return $masterComponentName;
		}
		
		static function putInContextStatic($id, $verifyToken = true, $pobId = null) {
			$context = self::getGlobalSessionValue('VIRGO_CONTEXT_usuniete', array());
			$context['${tr.f_v($entity.prefix)}_id'] = $id;
			self::setGlobalSessionValue('VIRGO_CONTEXT_usuniete', $context);
			self::setContextId($id, $verifyToken, $pobId);
## tu bylo sporo kodu ustawiajacego konteksty w roznych przypadkach. Sprawdzic w poprzedniej wersji pliku			
			$query = "SELECT ppr_pob_id, pdf_namespace, pdf_alias FROM prt_portlet_parameters, prt_portlet_objects, prt_portlet_definitions WHERE ppr_name = ? AND pob_id = ppr_pob_id AND pdf_id = pob_pdf_id AND ppr_value = ? ";
			$rows = QPR($query, "si", array('parent_entity_pob_id', isset($pobId) ? $pobId : $_SESSION['current_portlet_object_id']));
			foreach ($rows as $row) {
				$className = $row['pdf_namespace'] . '\\' . $row['pdf_alias'];
	 			if (class_exists($className)) {
	 				$className::removeFromContext($row['ppr_pob_id']);
	 				$className::setShowPage(1);
	 				$className::setDisplayMode("TABLE");
	 			}
			}
		}		
		
		function putInContext($verifyToken = true, $pobId = null) {
			self::putInContextStatic($this->getId(), $verifyToken, $pobId);
		}		

		static function removeFromContext($pobId = null) {
			$context = self::getGlobalSessionValue('VIRGO_CONTEXT_usuniete', array());
			$context['${tr.f_v($entity.prefix)}_id'] = null;
			virgo${tr.FV($entity.name)}::setGlobalSessionValue('VIRGO_CONTEXT_usuniete', $context);
			if (is_null($pobId)) {
				self::setContextId(null);
			} else {
				self::setRemoteContextId(null, $pobId);				
			}
## Zmiana podejscia. Zamiast usuwac "bezmyslnie" sprawdzmy tylko kto jest skonfigurowany na czytanie z tego parenta
## 
## #foreach( $relation in $entity.parentRelations )
## #if ($relation.childEntity.name != $entity.name) 
## 			if (class_exists('virgo${tr.FV($relation.childEntity.name)}')) {
## 				$tmpChild = new virgo${tr.FV($relation.childEntity.name)}();
## 				$tmpChild->removeFromContext();
## 				$tmpChild->setDisplayMode("TABLE");
## 			}
## #end			
## #end
			$query = "SELECT ppr_pob_id, pdf_namespace, pdf_alias FROM prt_portlet_parameters, prt_portlet_objects, prt_portlet_definitions WHERE ppr_name = ? AND pob_id = ppr_pob_id AND pdf_id = pob_pdf_id AND ppr_value = ? ";
			$rows = QPR($query, "si", array('parent_entity_pob_id', isset($pobId) ? $pobId : $_SESSION['current_portlet_object_id']));
			foreach ($rows as $row) {
				$className = $row['pdf_namespace'] . '\\' . $row['pdf_alias'];
	 			if (class_exists($className)) {
	 				$className::removeFromContext($row['ppr_pob_id']);
	 				$className::setShowPage(1);
	 				$className::setDisplayMode("TABLE");
	 			}
			}
		}
		
		static function portletActionRemoveFromContext() {
			$classToRemove = R('virgo_remove_class');			
			$classToRemove::removeFromContext();
## Powinien pamietac jakie to bylo menu id
##			$tmpChild->setDisplayMode("TABLE");
			return 0;
		}

		static function setRecordSet($criteria) {
			virgo${tr.FV($entity.name)}::setLocalSessionValue('VIRGO_RECORD_SET', $criteria);
		}
		
		static function getRecordSet() {
			return virgo${tr.FV($entity.name)}::getLocalSessionValue('VIRGO_RECORD_SET', array());
		}		
		
		static function setInvalidRecords($invalidRecords) {
			virgo${tr.FV($entity.name)}::setLocalSessionValue('VIRGO_INVALID_RECORDS', $invalidRecords);
		}
		
		static function getInvalidRecords() {
			return virgo${tr.FV($entity.name)}::getLocalSessionValue('VIRGO_INVALID_RECORDS', array());
		}
		
		static function setCriteria($criteria) {
			virgo${tr.FV($entity.name)}::setLocalSessionValue('VIRGO_CRITERIA', $criteria);
		}
		
		static function getCriteria() {
			if (P('filter_entity_pob_id', '') == '') {
				return virgo${tr.FV($entity.name)}::getLocalSessionValue('VIRGO_CRITERIA', null);
			} else {
				$filterPobId = P('filter_entity_pob_id', '');
				return virgo${tr.FV($entity.name)}::getRemoteSessionValue('VIRGO_CRITERIA', null, $filterPobId); 
			}
		}		

		static function setDisplayMode($mode) {
			virgo${tr.FV($entity.name)}::setLocalSessionValue('DisplayMode', $mode);
		}

		static function setRemoteDisplayMode($mode, $menuItem) { 
			virgo${tr.FV($entity.name)}::setRemoteSessionValue('DisplayMode', $mode, $menuItem);
		}
		
		static function setContextId($id, $verifyToken = true, $pobId = null) {
			if ($verifyToken && isset($id)) {
				if (!A($id)) {
					L('Access denied', '', 'ERROR');
					return;
				}
			}
			virgo${tr.FV($entity.name)}::setRemoteContextId($id, (isset($pobId) ? $pobId : $_SESSION['current_portlet_object_id']));
		}
		
		static function getContextId() {
			return virgo${tr.FV($entity.name)}::getRemoteContextId($_SESSION['current_portlet_object_id']);
		}
		
		static function getRemoteContextId($menuItem) {
			return virgo${tr.FV($entity.name)}::getRemoteSessionValue('ContextId', null, $menuItem);
		}
		
		static function clearRemoteContextId($menuItem) {
			virgo${tr.FV($entity.name)}::setRemoteContextId(null, $menuItem);
		}
		
		static function setRemoteContextId($contextId, $menuItem) {
			virgo${tr.FV($entity.name)}::setRemoteSessionValue('ContextId', $contextId, $menuItem);
#foreach( $relation in $entity.parentRelations ) 
## Przypadek:
## zamówienie ---E pozycja zamówienia ---E	pobranie zapasu
##                  zakup promocyjny  ---E
## 
## W takich przypadkach encje pozycja zamówienia i zakup promocyjny należy 
## zsynchronizować, czyli jeśli jedna jest wstawiana go kontekstu, to druga powinna 
## z niego wyjść!
#if ($relation.childEntity.name != $entity.name)
#foreach( $relation2 in $relation.childEntity.childRelations )
#if ($relation2.parentEntity.name != $relation.childEntity.name)
#set ($suspectedEntity = $relation2.parentEntity)
#foreach( $relation3 in $suspectedEntity.childRelations )
#if ($relation3.parentEntity.name != $suspectedEntity.name)
#if (!$relation3.parentEntity.dictionary)
#foreach( $relation4 in $relation3.parentEntity.parentRelations )
#if ($relation4.childEntity.name != $relation3.parentEntity.name) 
#if ($relation4.childEntity.name == $entity.name && $suspectedEntity.name != $entity.name)
## sprawdz, czy jestesmy w tabie, razem z tabela z ta encja!
			if (!is_null($contextId)) {
				$currentItem = null; //$menu->getActive();
##				$items = $menu->getItems("menutype", "$currentItem->menutype");
##				$firstFound = false;
##				foreach ($items as $item) {
##					$component = virgo${tr.FV($entity.name)}::getComponentByMenuId($menu, $item->id);
##					if ($component == "${tr.f_v($suspectedEntity.name)}") {
##						if (!$firstFound) {
##							$firstFound = true;
##						} else {
##							virgo${tr.FV($suspectedEntity.name)}::setRemoteContextId(null, $item->id);
##							break;
##						}
##					}
##				}
			}
#end
#end
#end
#end
#end
#end
#end
#end
#end
#end
		}		

## zeby mozna bylo wstrzyknac jakies specjalne sterowanie ekranami
## wstrzykiwany kod musi przypisac zwracany mode zmiennej $result
		static function getCustomDisplayMode() {
			$result = null;
#parseFile("modules_project/customDisplayMode/${tr.f_v($entity.name)}.php")
			return $result;
		}
		
		static function getDisplayMode() {
			$ret = self::getCustomDisplayMode();
			if (isset($ret)) {
				return $ret;
			}
			$componentParams = null; 
			if (P('form_only', "0") == "1") {
				return "CREATE";
			} elseif (P('form_only', "0") == "5" || P('form_only', "0") == "6") { ##// || (trim($componentParams->get('master_list_menu_item')) != "" && $componentParams->get('master_list_menu_item') != "0")) {
				return "FORM";
			} elseif (P('form_only', "0") == "7") {
				return "VIEW";
			} else {
				$defaultMode = "";
				if (P('form_only', "0") == 2) {
					$defaultMode = 'SEARCH';
				} else {
					$defaultMode = 'TABLE';
				}
				return virgo${tr.FV($entity.name)}::getLocalSessionValue('DisplayMode', $defaultMode);
			}
		}
		
		static function setOrderColumn($column) {
			virgo${tr.FV($entity.name)}::setLocalSessionValue('OrderColumn', $column);
		}

		static function getOrderColumn() {
			$componentParams = null; 
#foreach( $property in $entity.properties )
#if ($property.name == 'display order' || ${tr.f_v($property.name)} == 'kolejnosc_wyswietlania' || ${tr.f_v($property.name)} == 'order' || ${tr.f_v($property.name)} == 'kolejnosc')
#set ($displayOrderPropertyName = $property.name)
#end
#end			
#if ($displayOrderPropertyName != "")
			$defaultOrderColumn = P('default_sort_column', '${tr.f_v($entity.prefix)}_${tr.f_v($displayOrderPropertyName)}');
#else			
			$defaultOrderColumn = P('default_sort_column');
#end
			if (is_null($defaultOrderColumn) || trim($defaultOrderColumn) == "") {
				$defaultOrderColumn = "${tr.f_v($entity.prefix)}_id";
			}
			return virgo${tr.FV($entity.name)}::getLocalSessionValue('OrderColumn', $defaultOrderColumn); 
		}		

		static function setOrderMode($mode) {
			virgo${tr.FV($entity.name)}::setLocalSessionValue('OrderMode', $mode);
		}

		static function getOrderMode() {
			$componentParams = null; 
			$defaultOrderMode = P('default_sort_mode');
			if (is_null($defaultOrderMode) || trim($defaultOrderMode) == "") {
				$defaultOrderMode = "asc";
			}
			return virgo${tr.FV($entity.name)}::getLocalSessionValue('OrderMode', $defaultOrderMode);
		}
		
		static function setShowRows($rows) {
			virgo${tr.FV($entity.name)}::setLocalSessionValue('ShowRows', $rows);
		}

		static function getShowRows() {
/*			$systemParameter = new virgoSystemParameter();
			$paramValue = $systemParameter->getValueByName("DEFAULT_PAGE_SIZE");
			if (is_null($paramValue) || trim($paramValue) == "" || ((int)$paramValue) == 0) {
				$paramValue = 20;
			}*/
			$componentParams = null; 
			$paramValue = P('default_page_size', "20");			
			return virgo${tr.FV($entity.name)}::getLocalSessionValue('ShowRows', $paramValue); 
		}
		
#getterSetterSession("ShowPage" "1")

#getterSetterSession("ImportFieldSeparator" "null")		
				
		function internalActionStore($closeForm, $showOKMessage = true, $showError = true) {
#if ($application.name != "framework" || ${tr.f_v($entity.name)} != "uzytkownik")
			$permissionToCheck = "";
			$creating = false;
			if ($this->${entity.prefix}_id) {
				$permissionToCheck = "form";
			} else {
				$permissionToCheck = "add";
				$creating = true;
			}
#permissionRestrictedBlockBeginClass($permissionToCheck)
				$errorMessage = $this->store();
				if ($errorMessage == "") {
					if ($showOKMessage) {
						L(T('STORED_CORRECTLY', '${tr.F_V($entity.name)}'), '', 'INFO');
					}
					if ($closeForm) {
##						self::removeFromContext();
						self::setDisplayMode("TABLE");
					}
					$componentParams = null; 
					if ($creating && false) { //$componentParams->get('send_email') == "1") {
						$email = $componentParams->get('send_email_address');
						$subject = $componentParams->get('send_email_subject');
						$body = $componentParams->get('send_email_body');
						$fieldValue = $componentParams->get('send_email_field_value');
						$from	= $config->getValue('mailfrom');
						$fromname= $config->getValue('fromname');
						$fieldValues = '';						
#foreach( $property in $entity.properties )
						$fieldValues = $fieldValues . T($fieldValue, '${tr.fv($property.name)}', $this->${entity.prefix}_${tr.f_v($property.name)});
#end
#foreach( $relation in $entity.childRelations )
						$parent${tr.FV($relation.parentEntity.name)} = new virgo${tr.FV($relation.parentEntity.name)}();
#if ($relation.name)
						$fieldValues = $fieldValues . T($fieldValue, '$relation.parentEntity.name $relation.name', $parent${tr.FV($relation.parentEntity.name)}->lookup($this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id));
#else
						$fieldValues = $fieldValues . T($fieldValue, '$relation.parentEntity.name', $parent${tr.FV($relation.parentEntity.name)}->lookup($this->${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_id));
#end
#end
						$username = '';
						if ($this->${entity.prefix}_usr_created_id == 0) {
							$username = "anonymous";
						} else {
							$createdByUser =& JUser::getInstance((int)$this->${entity.prefix}_usr_created_id);
							$username = $createdByUser->username;
						}						
						$fieldValues = $fieldValues . T($fieldValue, 'User created', $username);
						$fieldValues = $fieldValues . T($fieldValue, 'Creation date', $this->${entity.prefix}_date_created);
						$fieldValues = $fieldValues . T($fieldValue, 'Client IP', $_SERVER[REMOTE_ADDR]);
						$body = T($body, $fieldValues);
						if (!JUtility::sendMail($from, $fromname, $email, $subject, $body)) {
							L('Email not sent', '', 'ERROR');
						}
					}
					return "";
				} else {
					if ($showError) {
						L($errorMessage, '', 'ERROR');
					}
					return $errorMessage;
				}
			} else {
				if ($showOKMessage) {
					L(T('NO_PERMISSION'), '', 'ERROR');
				}
				return T('NO_PERMISSION');
#permissionRestrictedBlockEnd()
#else
					return ".";
#end
		}
		
		static function portletActionStore($showOKMessage = true, &$id = null) {
			$originalDisplayMode = self::getDisplayMode();
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromRequest();
			$oldId = $instance->getId();
			if ($oldId == "") {
				$oldId = null;
			}
## do specjalnych formularzy typu "zaloz konto w naszym serwisie" - jesli jest custom message to nie pokazuj juz naszego			
			if (is_null($oldId)) {
				if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'${tr.fV($application.name)}'.DIRECTORY_SEPARATOR.'virgo${tr.FV($entity.name)}'.DIRECTORY_SEPARATOR.'create_store_message.php')) {
					$showOKMessage = false;
				}
			}
			$errorMessage = $instance->internalActionStore(R('keep_form_open', "false") != "true", $showOKMessage);
			if ($errorMessage == "" && !isset($oldId)) {
##				if (P('form_only', "0") != "1") { Bo "create only" nie wiedzial kiedy ma kasowac pola
				$instance->putInContext(isset($oldId));
				$masterEntityPobId = P('master_entity_pob_id', '');
				if ($masterEntityPobId != "") {
					$instance->putInContext(false, $masterEntityPobId);
				}
##				} else {
##					self::removeFromContext();
##				}
##			} else {
## sprobujmy to wylaczyc...				self::removeFromContext();
			}
##			if ($errorMessage == "") {
##				if (trim($componentParams->get('master_list_menu_item')) == "" || $componentParams->get('master_list_menu_item') == "0") {
##					return new virgo${tr.FV($entity.name)}();
##				}
##			}
			$currentItem = null; //$menu->getActive();
			$ret = null;
			$componentParams = null;
			if ($errorMessage == "") { ## && !strstr($currentItem->menutype, "-tabs-") && $componentParams->get('form_only') != "5") {  <------ Sprawdzic, jak dziala teraz "form only"!
##				jesli to nie w trybie tabs, to puste pola zwroc
				$ret = 0;
				if (isset($id)) {
					$id = $instance->getId();
				}
			} else {
				$ret = -1;
			}
#foreach ($relationChild in $entity.parentRelations)
#if (!$relationChild.childEntity.dictionary && !$relationChild.childEntity.weak)
			if (false) { //$componentParams->get('show_form_${tr.f_v($relationChild.childEntity.namePlural)}${tr.f_v($relationChild.name)}') == "1") {
				$tmp${tr.FV($relationChild.childEntity.name)} = new virgo${tr.FV($relationChild.childEntity.name)}();
				$delete${tr.FV($relationChild.childEntity.name)} = R('DELETE');
				if (sizeof($delete${tr.FV($relationChild.childEntity.name)}) > 0) {
					virgo${tr.FV($relationChild.childEntity.name)}::multipleDelete($delete${tr.FV($relationChild.childEntity.name)});
				}
#if ($relationChild.name)
#set ($kreska = "_")
#else
#set ($kreska = "")
#end
				$resIds = $tmp${tr.FV($relationChild.childEntity.name)}->select(null, 'all', null, null, ' ${tr.f_v($relationChild.childEntity.prefix)}_${tr.f_v($entity.prefix)}_${tr.f_v($relationChild.name)}${kreska}id = ' . $instance->getId(), ' SELECT ${tr.f_v($relationChild.childEntity.prefix)}_id FROM ${tr.f_v($application.prefix)}_${tr.f_v($relationChild.childEntity.namePlural)} ');
				$resIdsString = "";
				foreach ($resIds as $resId) {
					if ($resIdsString != "") {
						$resIdsString = $resIdsString . ",";
					}
					$resIdsString = $resIdsString . $resId->${tr.f_v($relationChild.childEntity.prefix)}_id;
//					JRequest::setVar('${tr.f_v($relationChild.childEntity.prefix)}_${tr.f_v($entity.name)}_' . $resId->${tr.f_v($relationChild.childEntity.prefix)}_id, $this->getId());
				} 
//				JRequest::setVar('${tr.f_v($relationChild.childEntity.prefix)}_${tr.f_v($entity.name)}_', $instance->getId());
				$tmp${tr.FV($relationChild.childEntity.name)}->setRecordSet($resIdsString);
				if (!$tmp${tr.FV($relationChild.childEntity.name)}->portletActionStoreSelected()) {
					$ret = -1;
					self::setDisplayMode($originalDisplayMode); 
				}
			}
#end
#end
################### n:m dodawanie zbiorowe (poczatek)#################### CZY TO NIE JEST JUZ GDZIES ZROBIONE???
## ## Dla wszystkich dzieci, ktore sa tabelami n:m
## #foreach( $relation2 in $entity.parentRelations )
## #if ($relation2.childEntity.weak)
## ## Dla wszystkich jego rodzicow, ktorzy nie sa aktualna encja inie sa slownikami:
## #foreach( $relation in $relation2.childEntity.childRelations )
## #if ($relation.parentEntity.name != $entity.name && !$relation.parentEntity.dictionary)
## 			$ids = R('n_m_${tr.f_v($relation.parentEntity.name)}');
## 			if (is_null($ids)) {
## 				$ids = array();
## 			}
## 			foreach ($this->get${tr.FV($relation2.childEntity.namePlural)}() as $child) {
## 				$aktualneId = $child->get${tr.FV($relation.parentEntity.name)}Id();
## 				if (!in_array($aktualneId, $ids)) {
## 					$child->delete();
## 				} else {
## 					$key = array_search($aktualneId, $ids);
## 					unset($ids[$key]);
## 				}
## 			}
## 			foreach ($ids as $id) {
## 				$tmp = new virgo${tr.FV($relation2.childEntity.name)}();
## 				$tmp->set${tr.FV($relation.parentEntity.name)}Id($id);
## 				$tmp->set${tr.FV($entity.name)}Id($this->getId());
## 				$tmp->store();
## 			}
## #end
## #end
## #end
## #end
################### n:m dodawanie zbiorowe (koniec)  ####################
			if ($ret == -1) {
				$pob = self::getMyPortletObject();
				$pob->setPortletSessionValue('reload_from_request', '1');				
			}
			return $ret;
		}
		
#if ($showStoreAndClear == 'TRUE')
		static function portletActionStoreAndClear() {
			$originalDisplayMode = $this->getDisplayMode();
			$ret = $this->portletActionStore(true);
			if ($ret == 0) {
				self::removeFromContext();
				self::setDisplayMode($originalDisplayMode);
			}
			return $ret;
		}
#end
		
		
		static function portletActionApply() {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromRequest();
			$oldId = $instance->getId();
			if ($oldId == "") {
				$oldId = null;
			}
			$errorMessage = $instance->internalActionStore(false);
			if ($errorMessage == "") {
				$instance->putInContext(isset($oldId));
			}
			if ($errorMessage == "" && is_null($oldId)) {
				self::setDisplayMode("FORM");
			}
			if ($errorMessage == "") {
				return 0;
			} else {
				$pob = self::getMyPortletObject();
				$pob->setPortletSessionValue('reload_from_request', '1');				
				return -1;
			}
		}

		static function portletActionSelect($verifyToken = true, $pobId = null) {
#if ($entity.view)		
			$tmpId = R('${tr.fV($entity.prefix)}_id_' . $_SESSION['current_portlet_object_id']);
#else
			$tmpId = intval(R('${tr.fV($entity.prefix)}_id_' . $_SESSION['current_portlet_object_id']));
#end			
			$oldContextId = self::getContextId();
			if (isset($oldContextId) && $oldContextId == $tmpId) {
				self::removeFromContext();
			} else {
				self::putInContextStatic($tmpId, $verifyToken, $pobId);
			}
			return 0;
		}
		
##  legacy_invoked_portlet_object_id=211
## &portlet_action=SelectAndSetParent
## &parent_pob_id=210
## &pdk_id_211=6
## &ktg_id_211=3

		static function portletActionSelectAndSetParent() {
			$parentPobId = R('parent_pob_id');
			$parentPortletObject = new virgoPortletObject($parentPobId);
			$className = $parentPortletObject->getPortletDefinition()->getAlias();
			if (!class_exists($className)) {
				require_once(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.$portletObject->getPortletDefinition()->getNamespace().DIRECTORY_SEPARATOR.$portletObject->getPortletDefinition()->getAlias().DIRECTORY_SEPARATOR.'controller.php');
			}
			$class = new $className();
			$class->portletActionSelect(false, $parentPobId);
			return self::portletActionSelect(false);
		}
		
		static function portletActionSelectJson() {
			self::portletActionSelect(false);
			return virgo${tr.FV($entity.name)}::getContextId();
		}
		
		static function portletActionView() {
			self::putInContextStatic(self::loadIdFromRequest());
##			$this->setContextId($this->${tr.f_v($entity.prefix)}_id);
			self::setDisplayMode("VIEW");
#parseFile("modules_project/onView/${tr.f_v($entity.name)}.php")		 
			return 0;
		}
		
		static function portletActionClearView() {
			$this->setCriteria(array());
			return $this->portletActionView();
		}
		

		static function portletActionClose() {
## sprobujmy to wylaczyc...			self::removeFromContext();
			self::setDisplayMode("TABLE");
			return 0;
		}
		
		static function portletActionForm() {
			$tmpId = self::loadIdFromRequest();
			self::putInContextStatic($tmpId);
			if ($tmpId) {
				$permissionToCheck = "form";
			} else {
				$permissionToCheck = "add";
			}
#permissionRestrictedBlockBeginClass($permissionToCheck)
##			$this->setContextId($this->${tr.f_v($entity.prefix)}_id);
			self::setDisplayMode("FORM");
#permissionRestrictedBlockEnd()
			return 0;
		}
		
		static function portletActionDuplicate() {
			self::putInContextStatic(self::loadIdFromRequest());
			$this->${entity.prefix}_id = null;
			$this->${entity.prefix}_date_created = null;
			$this->${entity.prefix}_usr_created_id = null;
			$this->${entity.prefix}_date_modified = null;
			$this->${entity.prefix}_usr_modified_id = null;
			$this->${entity.prefix}_virgo_title = null;
#if ($entity.dictionary)		
			$this->${entity.prefix}_virgo_deleted = null;
#end
			
			self::setDisplayMode("CREATE");
			return 0;
		}
		
		static function portletActionShowHistory() {
			self::putInContextStatic(self::loadIdFromRequest());
			self::setDisplayMode("SHOW_HISTORY");
			return 0;
		}
		
		static function portletActionShowRevision() {
			self::setDisplayMode("SHOW_REVISION");
			return 0;
		}
		
		static function portletActionCustomMode() {
			$customMode = R('componentName');
			if (!is_null($customMode) && trim($customMode) != "") {			
				self::putInContextStatic(self::loadIdFromRequest());
				self::setDisplayMode($customMode);
			}
			return 0;
		}

#foreach( $relation in $entity.childRelations )
		static function portletActionShowFor${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}() {
			$parentId = R('${tr.f_v($relation.parentEntity.prefix)}_id');
			$menuItem = R('virgo_menu_item');
			if (!is_null($parentId) && trim($parentId) != "") {
				$parent = new virgo${tr.FV($relation.parentEntity.name)}($parentId);
				$parent->setRemoteContextId($parentId, $menuItem);
				self::setShowPage(1);
			}
			self::setDisplayMode("TABLE");
			return 0;
		}
#end

#if ($entity.weak)
		function existingConnections() {
			$where = "";
#set ($counter = 0)			
#foreach ($relation in $entity.childRelations)
			if (is_null($this->get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Id())) {
				L('Missing ${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Id', '', 'ERROR');
			}
#set ($counter = $counter + 1)
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
			$where = $where . "#if ($counter > 1) AND#end ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}${underscore}id = {$this->get${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}Id()}";

#end
			return virgo${tr.FV($entity.name)}::selectAllAsObjectsStatic($where);
		}

		function storeIfNotExists() {
			$res = $this->existingConnections();
			if (count($res) == 0) {
				LE($this->store());
				return $this;
			}
			return $res[0];
		}
#end

		static function portletActionAdd() {
#permissionRestrictedBlockBeginClass("add")
			self::removeFromContext();
			self::setDisplayMode("CREATE");
//			$ret = new virgo${tr.FV($entity.name)}();
## #parseFile("modules_project/fillOnAdd/${tr.f_v($entity.name)}.php")			
//			return $ret;
#permissionRestrictedBlockEnd()
			return 0;
		}

		static function portletActionSearchForm() {
## #permissionRestrictedBlockBeginClass("search")
			self::setDisplayMode("SEARCH"); 
			self::setShowPage(1);
			return 0;
## #permissionRestrictedBlockEnd()
		}

		static function portletActionSearch() {
## #permissionRestrictedBlockBeginClass("search")
			self::loadSearchFromRequest();
			if (P('filter_mode', '0') == '0') {
				self::setDisplayMode("TABLE");
			}
			return 0;
## #permissionRestrictedBlockEnd()
		}
		
		static function portletActionClear() {
			$this->setCriteria(array());
			// self::setDisplayMode("TABLE");
			return 0;
		}

		static function portletActionRemoveCriterium() { 
			$column = R('virgo_filter_column');
			$criteria = self::getCriteria();
			unset($criteria[$column]);
			self::setCriteria($criteria);
			self::setDisplayMode("TABLE");
			return 0;
		}
		
		static function portletActionDelete() {
#permissionRestrictedBlockBeginClass("delete")
##				$this->loadIdFromRequest(); to jednak musi byc ladowanie wszystkich pol po to, zeby ewentualnie afterDelete mogl jeszcze je przeczytac
				$instance = new virgo${tr.FV($entity.name)}();
				$instance->loadFromDB();
				$res = $instance->delete();
				if ($res == "") {
					$masterEntityPobId = P('master_entity_pob_id', '');
					if ($masterEntityPobId == "") { ## bylo != czyli chyba zle!
						self::setDisplayMode("TABLE");
						virgo${tr.FV($entity.name)}::setRemoteContextId(null, $masterEntityPobId);
					}				
					L(T('DELETED_CORRECTLY', '${tr.F_V($entity.name)}'), '', 'INFO');
					return 0;
					
				} else {
##					self::setDisplayMode("TABLE"); moze byc wywolany tez z innych screenow (np. view, edit)
					L($res, '', 'ERROR');
					return -1;
				}
#permissionRestrictedBlockEnd()
		}
		
#set ($displayOrderPropertyName = "")
#foreach( $property in $entity.properties )
#if ($property.name == 'display order' || ${tr.f_v($property.name)} == 'kolejnosc_wyswietlania' || ${tr.f_v($property.name)} == 'order' || ${tr.f_v($property.name)} == 'kolejnosc')
#set ($displayOrderPropertyName = $property.name)
#end
#end
#if ($displayOrderPropertyName != "")
		static function portletActionVirgoUp() {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromDB();
			$idToSwapWith = R('virgo_swap_up_with_' . $instance->getId());
			$objectToSwapWith = new virgo${tr.FV($entity.name)}($idToSwapWith);
			$val1 = $instance->get${tr.FV($displayOrderPropertyName)}();
			$val2 = $objectToSwapWith->get${tr.FV($displayOrderPropertyName)}();
			if (is_null($val1)) {
				$val1 = 1;
			}
			if (is_null($val2)) {
				$val2 = 1;
			}
			if ($val1 == $val2) {
				$val1 = $val2 + 1;
			}
			$objectToSwapWith->set${tr.FV($displayOrderPropertyName)}($val1);
			$instance->set${tr.FV($displayOrderPropertyName)}($val2);
			$objectToSwapWith->store(false);
			$instance->store(false);
			$instance->putInContext();
			return 0;
		}
		
		static function portletActionVirgoDown() {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromDB();
			$idToSwapWith = R('virgo_swap_down_with_' . $instance->getId());
			$objectToSwapWith = new virgo${tr.FV($entity.name)}($idToSwapWith);
			$val1 = $instance->get${tr.FV($displayOrderPropertyName)}();
			$val2 = $objectToSwapWith->get${tr.FV($displayOrderPropertyName)}();
			if (is_null($val1)) {
				$val1 = 1;
			}
			if (is_null($val2)) {
				$val2 = 1;
			}
			if ($val1 == $val2) {
				$val1 = $val2 - 1;
			}
			$objectToSwapWith->set${tr.FV($displayOrderPropertyName)}($val1);
			$instance->set${tr.FV($displayOrderPropertyName)}($val2);
			$objectToSwapWith->store(false);
			$instance->store(false);
			$instance->putInContext();
			return 0;
		}
#end
		
#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'BOOL')
		static function portletActionVirgoSet${tr.FV($property.name)}True() {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromDB();
			$instance->set${tr.FV($property.name)}(1);
			$ret = $instance->store();
			if ($ret == "") {
				return 0;
			} else {
				L($ret, '', 'ERROR');
				return -1;
			}
		}
		
		static function portletActionVirgoSet${tr.FV($property.name)}False() {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromDB();
			$instance->set${tr.FV($property.name)}(0);
			$ret = $instance->store();
			if ($ret == "") {
				return 0;
			} else {
				L($ret, '', 'ERROR');
				return -1;
			}
		}

		function is${tr.FV($property.name)}() {
			return $this->get${tr.FV($property.name)}() == 1;
		}
#end
#if ($property.dataType.name == 'HTML')
		static function portletActionVirgo${tr.FV($property.name)}AsPdf() {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromDB();
			$instance->generate${tr.FV($property.name)}AsPdf();
		}
		function generate${tr.FV($property.name)}AsPdf() {
			require_once(PORTAL_PATH.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'tcpdf'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'eng.php');
			require_once(PORTAL_PATH.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'tcpdf'.DIRECTORY_SEPARATOR.'tcpdf.php');
			ini_set('display_errors', '0');
			$pdf = new FOOTEREDPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetCreator(null);
			$pdf->SetTitle('');
			$pdf->SetSubject(null);
			$pdf->SetKeywords(null);
			$pdf->setImageScale(1);
			$font = 'freeserif';
			$fontBoldVariant = 'B';
##			$pdf->setFooterFont(array($font, '', 8));
##			$pdf->setFooterData(E('TRESC_PODZIEKOWANIA_STOPKA', $P), 'L');
			$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			$pdf->SetFont($font, '', 10);
			$pdf->SetFont('freeserif', '', 13);
			$pdf->AddPage();
			$pdf->writeHTML($this->get${tr.FV($property.name)}(), true, false, true, false, '');
			$pdf->Output('${tr.FV($entity.name)}_${tr.FV($property.name)}_' . $this->getId() . '.pdf', 'I'); ## a nie powinno byc 'D' zamiast 'I'?
			ini_set('display_errors', '1');
			return 0;			
		}
#end
#end
		
		
		static function portletActionEditSelected() {
			$idsToDeleteString = R('ids');
			$idsToDeleteString = $idsToDeleteString . ",0";
			$this->setRecordSet($idsToDeleteString);
			$this->setInvalidRecords(array());
			self::setDisplayMode("TABLE_FORM");
			return 0;
		}		
		
		function getRecordsToEdit() {
			$idsToEditString = $this->getRecordSet();
			$idsToEdit = preg_split("/,/", $idsToEditString);
			$idsToCorrect = $this->getInvalidRecords();
			$results = array();
			foreach ($idsToEdit as $idToEdit) {
				$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
				if (isset($idsToCorrect[$idToEdit])) {
					$result${tr.FV($entity.name)}->loadRecordFromRequest($idToEdit);
				} else {
					$idToEditInt = (int)trim($idToEdit);
					if ($idToEditInt != 0) {
						$result${tr.FV($entity.name)}->load($idToEditInt);
					} else {
						$result${tr.FV($entity.name)}->${tr.f_v($entity.prefix)}_id = 0;
					}
				}
				$results[] = $result${tr.FV($entity.name)};
			}
			return $results;
		}
		
		static function portletActionStoreSelected() {
			$validateNew = R('virgo_validate_new'); 
			$idsToStoreString = $this->getRecordSet();
			$idsToStore = preg_split("/,/", $idsToStoreString);
## WTF? Po co to jest?			
##			if ($idsToStoreString != "") {
##				$idsToStore[] = "";
##			}
			$results = array();
			$errors = 0;
			$idsToCorrect = array();
			foreach ($idsToStore as $idToStore) {
				$result = new virgo${tr.FV($entity.name)}();
				$result->loadFromRequest($idToStore);
				if ($result->${tr.f_v($entity.prefix)}_id != 0 || strtoupper($validateNew) == "Y") {
					if ($result->${tr.f_v($entity.prefix)}_id == 0) {
						$result->${tr.f_v($entity.prefix)}_id = null;
					}
					$errorMessage = $result->internalActionStore(false, false);				
					if ($errorMessage != "") {
						$errors = $errors + 1;
						if (is_null($result->${tr.f_v($entity.prefix)}_id)) {
							$result->${tr.f_v($entity.prefix)}_id = 0;
						}
						$idsToCorrect[$result->${tr.f_v($entity.prefix)}_id] = $errorMessage;
					}
				}
			}
			if ($errors == 0) {
				if (sizeof($idsToStore) > 1 || strtoupper($validateNew) == "Y") {
					L(T('STORED_CORRECTLY', '${tr.F_V($entity.namePlural)}'), '', 'INFO');
				}
				self::setDisplayMode("TABLE");
				return 0;
			} else {
				L(T('INVALID_RECORDS', $errors), '', 'ERROR');
				$this->setInvalidRecords($idsToCorrect);
				return -1;
			}
		}

		static function multipleDelete($idsToDelete) {
			$errorOcurred = 0;
			$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
			foreach ($idsToDelete as $idToDelete) {
				$result${tr.FV($entity.name)}->load((int)trim($idToDelete));
				$res = $result${tr.FV($entity.name)}->delete();
				if (!is_null($res) && trim($res) != "") {
					L($res, '', 'ERROR');
					$errorOcurred = 1;
				}
			}
			if ($errorOcurred == 0) {
				L(T('DELETED_CORRECTLY', '${tr.F_V($entity.namePlural)}'), '', 'INFO');			
				self::setDisplayMode("TABLE");
				return 0;
			} else {
				return -1;
			}
		}

## generalnie to to virgowe zaznaczanie jest zle zrobione, bo niepotrzebna jest ta konwersja do stringa i z powrotem. 
## checkboxy powinny miec nazwe z [] i wtedy nie trzeba sie juz z tym bawic 
		static function getSelectedIds($name = 'ids') {
			$idsString = R($name);
			if (trim($idsString) == "") {
				return array();
			}
			return preg_split("/,/", $idsString);			
		}
		
		static function portletActionDeleteSelected() {
			$idsToDelete = self::getSelectedIds();
			return self::multipleDelete($idsToDelete);
		}

		static function portletActionChangeOrder() {
			$column = R('virgo_order_column');
			$mode = R('virgo_order_mode');
			self::setOrderColumn($column);
			self::setOrderMode($mode);
			return 0;
		}
		
		static function portletActionChangePaging() {
			$showPage = R('virgo_show_page');
			if(preg_match('/^\d+$/',$showPage)) {
				if ((int)$showPage > 0) {
					self::setShowPage($showPage);
				}
			}			
			$showRows = R('virgo_show_rows');
			self::setShowRows($showRows);
			return 0;
		}
				
		function getVirgoTitleNull() {
#parseFileWithStandard("modules_project/virgoTitle/${tr.f_v($entity.name)}.php" "defs/entity/virgoTitle.php")
		}

		function getVirgoTitle() {
			$componentParams = null;
			$paramTitleSource = PD('title_value', 'virgo${tr.FV($entity.name)}');
			if (!is_null($paramTitleSource) && trim($paramTitleSource) != "") {
				$paramTitleSource = '$ret = ' . $paramTitleSource;
				eval($paramTitleSource);
				return $ret;
			} else {
				$ret = $this->getVirgoTitleNull();
				if (is_null($ret)) return "";
				return $ret;
			}
		}
		
		function formatMessage($message, $args) {
			$index = 1;
			foreach ($args as &$value) {
				$message = str_replace("$" . $index, $value, $message);
				$index = $index + 1;
			}
			unset($value);
			return $message;
		}
		
		static function getExtraFilesInfo() {
			$ret = array();
#foreach( $fileName in $extraFilesInfo.keySet() )
    			$ret["$fileName"] = "$extraFilesInfo.get($fileName)";
#end
			return $ret;
		}
		
		function updateTitle() {
			$val = $this->getVirgoTitle(); 
			if (!is_null($val) && trim($val) != "") {
				$query = "UPDATE ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} SET ${tr.f_v($entity.prefix)}_virgo_title = ? WHERE ${tr.f_v($entity.prefix)}_id = " . $this->getId();
				Q($query, false, true, "s", array($val));
			}
		}
		
		static function portletActionUpdateTitle() {
			$query = "SELECT ${tr.f_v($entity.prefix)}_id AS id FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} ";
#selectRows()
			foreach ($rows as $row) {
				$tmp = new virgo${tr.FV($entity.name)}($row['id']);
				$tmp->updateTitle();
			}
			L("Title updated", '', 'INFO');
			return 0;
		}
		
#if ($entity.versioned)
		function getVirgoRecordHistory() {
			if (is_null($this->getId()) || $this->getId() == "") {
				return array();
			}
			$query = "SELECT id, revision, timestamp, ip, username, user_id FROM ${tr.f_v($application.prefix)}_history_${tr.f_v($entity.namePlural)} WHERE ${tr.f_v($entity.prefix)}_id = " . $this->getId() . " ORDER BY revision DESC ";
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			if (is_null($rows)) {
				if ($db->getErrorNum() == 1146) {
					$this->createHistoryTable();
					L(T('HISTORY_TABLE_JUST_CREATED'), '', 'ERROR');
				} else {
					L($db->getErrorMsg(), '', 'ERROR');
				}
				return array();
			} else {
				return $rows;
			}
		}
#end

		function hideContentDueToNoParentSelected() {			
			$whenNoParentSelected = P("when_no_parent_selected", "E");
			if ($whenNoParentSelected == "H") {
				$parentPobIds = PN('parent_entity_pob_id');
				foreach ($parentPobIds as $parentPobId) {
					$portletObject = new virgoPortletObject($parentPobId);
					$className = $portletObject->getPortletDefinition()->getNamespace()."\\".$portletObject->getPortletDefinition()->getAlias();
					$tmpContextId = $className::getRemoteContextId($parentPobId);
					if (isset($tmpContextId)) {
						return false;
					}
				}
;				return true;
			} else {
				return false;
			}
		}

## co ta funkcja ma załatwić?
## mianowicie dla danego portletu ma zwrocic:
##   - liste wszystkich rekordow, od ktorych jest uzalezniona, czyli parentow i grandparentow, a konkretnie:
##      - prefix
##      - nazwe
##      - wartosc z kontekstu
##      - warunek do WHERE'a
		static function getParentsInContext() {
			if (!isset($_REQUEST['_virgo_parents_in_context_' . $_SESSION['current_portlet_object_id']])) {
				$ret = array();
				$parentPobIds = PN('parent_entity_pob_id');
				$class2prefix = array();
#foreach( $relation in $entity.childRelations )
				$class2prefix["${tr.f_v($application.name)}\\virgo${tr.FV($relation.parentEntity.name)}"] = "${tr.f_v($relation.parentEntity.prefix)}";
				$class2prefix2 = array();
#foreach( $grandRelation in $relation.parentEntity.childRelations )
				$class2prefix2["${tr.f_v($application.name)}\\virgo${tr.FV($grandRelation.parentEntity.name)}"] = "${tr.f_v($grandRelation.parentEntity.prefix)}";
#end
				$class2parentPrefix["${tr.f_v($application.name)}\\virgo${tr.FV($relation.parentEntity.name)}"] = $class2prefix2;
#end		
				$whenNoParentSelected = P("when_no_parent_selected", "E");			
				foreach ($parentPobIds as $parentPobId) {
					$grandparentAdded = false;				
					$parentInfo = self::getEntityInfoByPobId($parentPobId, $class2prefix);
					if (isset($parentInfo['value'])) {
						$parentInfo['condition'] = '${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${entity.prefix}_' . $parentInfo['prefix'] . '_id = ' . $parentInfo['contextId'];
					} else {
						if ($whenNoParentSelected == "C") {
							$parentInfo['condition'] = '${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${entity.prefix}_' . $parentInfo['prefix'] . '_id IS NULL';
						} elseif ($whenNoParentSelected == "A") {
							$parentInfo['condition'] = ' 1 ';
						} elseif ($whenNoParentSelected == "G") {
							$grandparentPobIds = PN('grandparent_entity_pob_id');
							foreach ($grandparentPobIds as $grandparentPobId) {
								$class2parent2 = $class2parentPrefix[$parentInfo['className']];
								$grandparentInfo = self::getEntityInfoByPobId($grandparentPobId, $class2parent2);
								if (isset($class2prefix2[$grandparentInfo['className']])) {
									if (isset($grandparentInfo['value'])) {
										$parentClassName = $parentInfo['className'];
										$tmp = new $parentClassName();
										$grandparentInfo['condition'] = '${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${entity.prefix}_' . $parentInfo['prefix'] . '_id IN (SELECT ' . $parentInfo['prefix'] . '_id FROM ${tr.f_v($application.prefix)}_' . $tmp->getPlural() . ' WHERE ' . $parentInfo['prefix'] . '_' . $grandparentInfo['prefix'] . '_id = ' . $grandparentInfo['contextId'] . ') ';
									} else {
										$grandparentInfo['condition'] = ' 1 ';
									}
									$grandparentAdded = true;
									$ret[$grandparentInfo['className']] = $grandparentInfo;
									break;
								}
							}
							if (!$grandparentAdded) {
								$parentInfo['condition'] = '${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}.${entity.prefix}_' . $parentInfo['prefix'] . '_id IS NULL';
							}
						} else {
							if ($whenNoParentSelected != "H") {
								L(T('PLEASE_SELECT_PARENT'), '', 'INFO');
							}
							$parentInfo['condition'] = ' 0 ';
						}
					}
					if (!$grandparentAdded) {
						$ret[$parentInfo['className']] = $parentInfo;
					}
				}
				$_REQUEST['_virgo_parents_in_context_' . $_SESSION['current_portlet_object_id']] = $ret;
			} else {
				$ret = $_REQUEST['_virgo_parents_in_context_' . $_SESSION['current_portlet_object_id']];
			}
			return $ret;
		}
		
		## $class2prefix to lista parentow aktualnej encji
		static function getEntityInfoByPobId($parentPobId, $class2prefix) {
			$ret = array();
			$portletObject = new virgoPortletObject($parentPobId);
			$ret['name'] = $portletObject->getPortletDefinition()->getName();
			$className = $portletObject->getPortletDefinition()->getNamespace().'\\'.$portletObject->getPortletDefinition()->getAlias();
			if (!isset($class2prefix[$className])) {
				L('Zle skonfigurowany komponent: jego rodzicem jest niby portlet o ID ' . $parentPobId . ' ale on pokazuje klase ' . $className . ', ktora NIE JEST rodzicem klasy virgo${tr.FV($entity.name)}!', '', 'ERROR');
				return array();
			}
			$ret['prefix'] = $class2prefix[$className];
			$ret['className'] = $className;
			$tmpContextId = $className::getRemoteContextId($parentPobId);
			$ret['contextId'] = $tmpContextId;
			if (isset($tmpContextId) && $tmpContextId != "") {
				$ret['value'] =  "" . $className::lookup($tmpContextId);
			}
			return $ret;
		}

		static function portletActionReport() {
			require_once(PORTAL_PATH.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'tcpdf'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'eng.php');
			require_once(PORTAL_PATH.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'tcpdf'.DIRECTORY_SEPARATOR.'tcpdf.php');
			ini_set('display_errors', '0');
			$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$user = virgoUser::getUser();
			$pdf->SetCreator(null);
			$pdf->SetTitle('${tr.Fv($entity.namePlural)} report');
			$pdf->SetSubject(null);
			$pdf->SetKeywords(null);
			$pdf->setImageScale(1);

			$pob = self::getMyPortletObject();
			$reportTitle = $pob->getCustomTitle();
			if (is_null($reportTitle)) {
				$reportTitle = T('${tr.F_V($entity.namePlural)}');
			}
			$pdf->setHeaderData('', 0, $reportTitle, date ("Y.m.d H:i:s"));

			$font = P('pdf_font_name', 'freeserif');
			$includeBold = P('pdf_include_bold_font', '0');
			$fontBoldVariant = ($includeBold == "0" || is_null($includeBold) || trim($includeBold) == "") ? '' : 'B';

			$pdf->setHeaderFont(array($font, '', 10));
			$pdf->setFooterFont(array($font, '', 8));

			$fontSize = (float)P('pdf_font_size', '10');
			$pdf->SetFont($font, '', $fontSize);
			$columnsNumber = 0;	
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
				$columnsNumber = $columnsNumber + 1;
			}
#end				
#foreach( $relation in $entity.childRelations )
			if (P('show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
				$columnsNumber = $columnsNumber + 1;
			}
#end
			$colNr = $columnsNumber;
			$colHeight = 5;
			$maxWidth = (int)P('pdf_max_column_width', '70');
			$minWidth = array();
			$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
#################### PDF Table formatting ##################		
			$pdf->SetFont($font, $fontBoldVariant, $fontSize);
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
				$titleWords = preg_split("/[ ]+/", '${tr.Fv($property.name)}');
				$minWidth['$property.name'] = 6;
				foreach ($titleWords as $titleWord) {
					$tmpLen = $pdf->GetStringWidth($titleWord) + 6;
					if ($tmpLen > $minWidth['$property.name']) {
						$minWidth['$property.name'] = min($tmpLen, $maxWidth);
					}
				}
			}
#end				
#foreach( $relation in $entity.childRelations )
			if (P('show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
				$minWidth['$relation.parentEntity.name $relation.name'] = 6; 
				$titleWords = preg_split("/[ ]+/", '$relation.parentEntity.name $relation.name');
				foreach ($titleWords as $titleWord) {
					$tmpLen = $pdf->GetStringWidth($titleWord) + 6;
					if ($tmpLen > $minWidth['$relation.parentEntity.name $relation.name']) {
						$minWidth['$relation.parentEntity.name $relation.name'] = min($tmpLen, $maxWidth);
					}
				}
			}
#end
			$pdf->SetFont($font, '', $fontSize);
			$pdf->AliasNbPages();
			$orientation = P('pdf_page_orientation', 'P');
			$pdf->AddPage($orientation);
			$pdf->SetFont($font, $fontBoldVariant, $fontSize + 4);
			$pdf->MultiCell(0, 1, $reportTitle, '', 'C', 0, 0);
			$pdf->Ln();
			$whereClause${tr.FV($entity.name)} = " 1 = 1 ";				
#################### virgo Context ####################
		$parentContextInfos = self::getParentsInContext();
		foreach ($parentContextInfos as $parentContextInfo) {
			$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . ' AND ' . $parentContextInfo['condition'];
			$pdf->SetFont($font, $fontBoldVariant, $fontSize + 2);
			$pdf->MultiCell(30, 100, $parentContextInfo['name'] . ":", '', 'R', 0, 0);
			$pdf->SetFont($font, '', $fontSize + 2);
			$pdf->MultiCell(0, 1, $parentContextInfo['value'], '', 'L', 0, 0);
			$pdf->Ln();			
		}
		$pdf->Ln();
########################################################
#################### virgo filter ######################
			$criteria${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->getCriteria();
#foreach( $property in $entity.properties )
			$fieldCriteria${tr.FV($property.name)} = $criteria${tr.FV($entity.name)}["${tr.f_v($property.name)}"];
			if ($fieldCriteria${tr.FV($property.name)}["is_null"] == 1) {
				$pdf->SetFont($font, $fontBoldVariant, $fontSize + 2);
				$pdf->MultiCell(60, 100, '${tr.Fv($property.name)}', '', 'R', 0, 0);
				$pdf->SetFont($font, '', $fontSize + 2);
				$pdf->MultiCell(0, 1, "EMPTY_VALUE", '', 'L', 0, 0);
				$pdf->Ln();
			} else {
				$dataTypeCriteria = $fieldCriteria${tr.FV($property.name)}["value"];
				$renderCriteria = "";
#parseFile("defs/dataTypes/php/showCondition/${tr.f_v($property.dataType.name)}")
				if ($renderCriteria != "") {
					$pdf->SetFont($font, $fontBoldVariant, $fontSize + 2);
					$pdf->MultiCell(60, 100, '${tr.Fv($property.name)}', '', 'R', 0, 0);
					$pdf->SetFont($font, '', $fontSize + 2);
					$pdf->MultiCell(0, 1, $renderCriteria, '', 'L', 0, 0);
					$pdf->Ln();
				}
			}
#end
## ??? #parseFile("modules_project/extraRenderCriteria/${tr.f_v($entity.name)}.php")
#foreach( $relation in $entity.childRelations )
			$parentCriteria = $criteria${tr.FV($entity.name)}["${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}"];
			if ($parentCriteria["is_null"] == 1) {
				$pdf->SetFont($font, $fontBoldVariant, $fontSize + 2);
				$pdf->MultiCell(60, 100, '${tr.Fv($relation.parentEntity.name)}', '', 'R', 0, 0);
				$pdf->SetFont($font, '', $fontSize + 2);
				$pdf->MultiCell(0, 1, "$messages.EMPTY_VALUE", '', 'L', 0, 0);
				$pdf->Ln();
			} else {
		  		$parentIds = $parentCriteria["ids"];
				if (!is_null($parentIds) && is_array($parentIds)) {
					$parentLookups = array();
					foreach ($parentIds as $parentId) {
						$parentLookups[] = virgo${tr.FV($relation.parentEntity.name)}::lookup($parentId);
					}
					$pdf->SetFont($font, $fontBoldVariant, $fontSize + 2);
					$pdf->MultiCell(60, 100, '${tr.Fv($relation.parentEntity.name)}', '', 'R', 0, 0);
					$pdf->SetFont($font, '', $fontSize + 2);
					$pdf->MultiCell(0, 1, implode(", ", $parentLookups), '', 'L', 0, 0);
					$pdf->Ln();
				}
			}
#end
			$pdf->Ln();
##			$whereClause${tr.FV($entity.name)} = " 1 = 1 ";
			$hideColumnFromContextInTable = array();
########################################################
########################################################################
## Pobranie danych z bazy                                             ##
########################################################################
#showApplicableOnlyRecords()
#parseFile("modules_project/beforeSelect/${tr.f_v($entity.name)}.php")
#set ($tableDisplayMode = "pdf")
#parseFileWithStandard("modules_project/tableSelect/${tr.f_v($entity.name)}.php" "defs/entity/table_select.php")
##		$results${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->select('', 'all', $result${tr.FV($entity.name)}->getOrderColumn(), $result${tr.FV($entity.name)}->getOrderMode(), $whereClause${tr.FV($entity.name)});
#parseFile("modules_project/beforeTablePdf/${tr.f_v($entity.name)}.php")
		$results${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->select(
			'', 
			'all', 
			$result${tr.FV($entity.name)}->getOrderColumn(), 
			$result${tr.FV($entity.name)}->getOrderMode(), 
			$whereClause${tr.FV($entity.name)},
			$queryString);
		
		foreach ($results${tr.FV($entity.name)} as $result${tr.FV($entity.name)}) {
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end

			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
#if ($property.dataType.name == 'IMAGE')
			$tmpLen = $imageSizeX;
#else
			$tmpLen = $pdf->GetStringWidth(trim('' . $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}'])) + 6;
#end
				if ($tmpLen > $minWidth['$property.name']) {
					$minWidth['$property.name'] = min($tmpLen, $maxWidth);
				}
			}
#end				
#foreach( $relation in $entity.childRelations )
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
			if (P('show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
			$parentValue = trim(virgo${tr.FV($relation.parentEntity.name)}::lookup($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}${underscore}${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id']));
			$tmpLen = $pdf->GetStringWidth(trim($parentValue)) + 6;
				if ($tmpLen > $minWidth['$relation.parentEntity.name $relation.name']) {
					$minWidth['$relation.parentEntity.name $relation.name'] = min($tmpLen, $maxWidth);
				}
			}
#end
		}
#############################################################
##		potrzebne toto?		
##		$tableWidth = 0;
##		foreach ($minWidth as $tmpWidth) {
##			$tableWidth = $tableWidth + $tmpWidth;  
##		}
##		potrzebne toto?		
##		if ($tableWidth < 180) {
##			$pdf->DefOrientation='P';
##			$pdf->wPt=$pdf->fwPt;
##			$pdf->hPt=$pdf->fhPt;
##		}
		$maxLn = 1;
#################### virgo search criteria ############
//		$criteria${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->getCriteria();
		if (is_null($criteria${tr.FV($entity.name)}) || sizeof($criteria${tr.FV($entity.name)}) == 0 || $countTmp == 0) {
		} else {
			
		}
########################################################

		$pdf->SetFont($font, $fontBoldVariant, $fontSize);
		$pdf->Cell(1, $colHeight, '');
## WTF? Co to tu robi?		
## #parseFile("modules_project/beforeCreateForm/${tr.f_v($entity.name)}.php")
#set ($orderedFields = [])
#parseFile("modules_project/fieldOrder/${tr.f_v($entity.name)}/create.vm")
#orderFields()
#foreach( $property in $orderedFields )
#if ($property.class.name == "com.metadetron.virgo.bean.Property")		
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
		$tmpLn = $pdf->MultiCell($minWidth['$property.name'], $colHeight, T('${tr.F_V($property.name)}'), 'T', 'C', 0, 0);
		if ($tmpLn > $maxLn) {
			$maxLn = $tmpLn;
		}
			}
#else
#set ($relation = $property)
			if (P('show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
		$tmpLn = $pdf->MultiCell($minWidth['$relation.parentEntity.name $relation.name'], $colHeight, T('${tr.F_V($relation.parentEntity.name)}') . ' ' . T('${tr.F_V($relation.name)}'), 'T', 'C', 0, 0); 
		if ($tmpLn > $maxLn) {
			$maxLn = $tmpLn;
		}
			}
#end
#end
##		$pdf->Cell(1, $colHeight, '');		
##		$results${tr.FV($entity.name)} = $result${tr.FV($entity.name)}Object->select('', 'all', $_POST["orderColumn"], $_POST["orderMode"], $whereClause);
##		$pdf->ln($maxLn * 2);
## 		$pdf->SetFont($font, '', 1);
## 		$pdf->Cell(1, $colHeight, '');
## #foreach( $property in $orderedFields )
## #if ($property.class.name == "com.metadetron.virgo.bean.Property")		
## 			if ($componentParams->get('show_pdf_${tr.f_v($property.name)}') != "0") {
## 		$tmpLn = $pdf->MultiCell($minWidth['$property.name'], 1, '', 'TB', 'C', 0, 0);
## 			}
## #else
## #set ($relation = $property)
## 			if ($componentParams->get('show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}') == "1") {
## 		$tmpLn = $pdf->MultiCell($minWidth['$relation.parentEntity.name $relation.name'], 1, '', 'TB', 'C', 0, 0);
## 			}
## #end
## #end

## 		$pdf->Cell(1, $colHeight, '');		
## 		$pdf->Ln();
		for ($iTmp = 0; $iTmp < $maxLn; $iTmp++) {
			$dummyText .= " 
";
		}
		$pdf->MultiCell(4, $colHeight, $dummyText, '0', 'L', 0, 1); 
		$pdf->SetFont($font, '', $fontSize);
		$counts = array();
		$sums = array();
		$avgCounts = array();
		$avgSums = array();
		$pdf->SetDrawColor(200);
		foreach ($results${tr.FV($entity.name)} as $result${tr.FV($entity.name)}) {
			$maxLn = 1;
			$pdf->Cell(1, $colHeight, ''); //, 0, 1);
#foreach( $property in $orderedFields )
#if ($property.class.name == "com.metadetron.virgo.bean.Property")		
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
#renderPDF($entity $property "'T'")
				if (P('show_pdf_${tr.f_v($property.name)}', "1") == "2") {
					## na razie nie liczy typow co sie zapisuja w wielu kolumnach
					if (!is_null($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}'])) {
						$tmpCount = (float)$counts["${tr.f_v($property.name)}"];
						if (is_null($tmpCount)) {
							$tmpCount = 1;
						} else {
							$tmpCount++;
						}
						$counts["${tr.f_v($property.name)}"] = $tmpCount;
					}
				}
				if (P('show_pdf_${tr.f_v($property.name)}', "1") == "3") {
					## na razie nie liczy typow co sie zapisuja w wielu kolumnach
					if (!is_null($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}'])) {
						$tmpSum = (float)$sums["${tr.f_v($property.name)}"];
						if (is_null($tmpSum)) {
							$tmpSum = 0;
						} else {
							$tmpSum = $tmpSum + $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}'];
						}
						$sums["${tr.f_v($property.name)}"] = $tmpSum;
					}
				}
				if (P('show_pdf_${tr.f_v($property.name)}', "1") == "4") {
					## na razie nie liczy typow co sie zapisuja w wielu kolumnach
					if (!is_null($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}'])) {
						$tmpCount = (float)$avgCounts["${tr.f_v($property.name)}"];
						$tmpSum = (float)$avgSums["${tr.f_v($property.name)}"];
						if (is_null($tmpCount)) {
							$tmpCount = 1;
						} else {
							$tmpCount++;
						}
						$avgCounts["${tr.f_v($property.name)}"] = $tmpCount;
						if (is_null($tmpSum)) {
							$tmpSum = 0;
						} else {
							$tmpSum = $tmpSum + $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($property.name)}'];
						}
						$avgSums["${tr.f_v($property.name)}"] = $tmpSum;
					}
				}
			}
			if ($tmpLn > $maxLn) {
				$maxLn = $tmpLn;
			}			
#else
#set ($relation = $property)
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end 
			if (P('show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
			$parentValue = virgo${tr.FV($relation.parentEntity.name)}::lookup($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id']);
			$tmpLn = $pdf->MultiCell($minWidth['$relation.parentEntity.name $relation.name'], $colHeight, trim($parentValue), 'T', 'L', 0, 0); 
			if ($tmpLn > $maxLn) {
				$maxLn = $tmpLn;
			}
			}
#end
#end
####### wazne, zeby sie multiline nie zerzygaly na nowych stronach			
			$dummyText = "";
			for ($iTmp = 0; $iTmp < $maxLn; $iTmp++) {
				$dummyText .= " 
";
			}
			$pdf->MultiCell(4, $colHeight, $dummyText, '0', 'L', 0, 1); 
#######			
//			$pdf->Cell(1, $colHeight, '', 0, 0); //, 0, 1);
//			$pdf->ln(50); //$maxLn * ($fontSize - 1) / 2);
		}
## 		$pdf->SetDrawColor(0);		
## #foreach( $property in $orderedFields )
##   #if ($property.class.name == "com.metadetron.virgo.bean.Property")		
##     #if ($property.obligatory)
##       #set ($obl = '1')
##     #else
##       #set ($obl = '0')
##     #end
## 			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
## 		$pdf->MultiCell($minWidth['$property.name'], 1, '', 'T', 'L', 0, 0);
## 			}
##   #else
##     #set ($relation = $property)
## 			if (P('show_pdf_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
## 		$pdf->MultiCell($minWidth['$relation.parentEntity.name $relation.name'], 1, '', 'T', 'L', 0, 0);
## 			}
##   #end
## #end
## 		$pdf->Ln();
## 		$pdf->SetFont($font, $fontBoldVariant, $fontSize);
		if (sizeof($counts) > 0) {
			$pdf->Cell(1, $colHeight, ''); //, 0, 1);
			$tmpWidth = 0;
			$labelDone = false;
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
				$aggrVal = '';
				$tmpWidth = $tmpWidth + $minWidth['$property.name'];
				if (P('show_pdf_${tr.f_v($property.name)}', "1") == "2") {
					if (!$labelDone) {
						$aggrVal = $aggrVal . T('COUNT') . ": ";
						$labelDone = true;
					}
					$aggrVal = $aggrVal . $counts["${tr.f_v($property.name)}"];
					$pdf->MultiCell($tmpWidth, $colHeight, $aggrVal, 0, 'R', 0, 0);
					$tmpWidth = 0;
				}
			}
#end
		}
		$pdf->Ln();
		if (sizeof($sums) > 0) {
			$pdf->Cell(1, $colHeight, ''); //, 0, 1);
			$tmpWidth = 0;
			$labelDone = false;
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
				$aggrVal = '';
				$tmpWidth = $tmpWidth + $minWidth['$property.name'];
				if (P('show_pdf_${tr.f_v($property.name)}', "1") == "3") {
					if (!$labelDone) {
						$aggrVal = $aggrVal . T('SUM') . ": ";
						$labelDone = true;
					}
					$aggrVal = $aggrVal . number_format($sums["${tr.f_v($property.name)}"], 2, ',', ' ');
					$pdf->MultiCell($tmpWidth, $colHeight, $aggrVal, 0, 'R', 0, 0);
					$tmpWidth = 0;
				}
			}
#end
		}
		$pdf->Ln();
		if (sizeof($avgCounts) > 0 && sizeof($avgSums) > 0) {
			$pdf->Cell(1, $colHeight, ''); //, 0, 1);
			$tmpWidth = 0;
			$labelDone = false;
#foreach( $property in $entity.properties )
#if ($property.obligatory)
#set ($obl = '1')
#else
#set ($obl = '0')
#end
			if (P('show_pdf_${tr.f_v($property.name)}', "$obl") != "0") {
				$aggrVal = '';
				$tmpWidth = $tmpWidth + $minWidth['$property.name'];
				if (P('show_pdf_${tr.f_v($property.name)}', "1") == "4") {
					if (!$labelDone) {
						$aggrVal = $aggrVal . T('AVERAGE') . ": ";
						$labelDone = true;
					}
					$aggrVal = $aggrVal . ($avgCounts["${tr.f_v($property.name)}"] == 0 ? "-" : $avgSums["${tr.f_v($property.name)}"] / $avgCounts["${tr.f_v($property.name)}"]);
					$pdf->MultiCell($tmpWidth, $colHeight, $aggrVal, 0, 'R', 0, 0);
					$tmpWidth = 0;

				}
			}
#end
		}
		$pdf->Ln();
		$pdf->SetFont($font, '', $fontSize);
		$pdf->Output($reportTitle. '_' . date ("Ymd_His") . '.pdf', 'I'); ## a nie powinno byc 'D' zamiast 'I'?
		ini_set('display_errors', '1');
		return 0;
			}
		
##		static function portletActionCsv() { w liverze trzeba export, bo inaczej wszystkie prawa trzeba zmieniac
		static function portletActionExport() {
			$data = "";
			$pob = self::getMyPortletObject();
			$reportTitle = $pob->getCustomTitle();
			if (is_null($reportTitle)) {
				$reportTitle = T('${tr.F_V($entity.namePlural)}');
			}
			$reportTitle = $reportTitle . ".csv";
			$separator = P('export_separator', ",");
			$stringDelimeter = P('export_string_delimeter', "\"");
			$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
			$whereClause${tr.FV($entity.name)} = " 1 = 1 ";
#################### virgo Context ####################
			$parentContextInfos = self::getParentsInContext();
			foreach ($parentContextInfos as $parentContextInfo) {
				$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . ' AND ' . $parentContextInfo['condition'];
			}
########################################################
#foreach( $property in $entity.properties )
#if ($property.obligatory || $showByDefault == '1')
#set ($obl = '1')
#else
#set ($obl = '0')
#end
				if (P('show_export_${tr.f_v($property.name)}', "$obl") != "0") {
					$data = $data . $stringDelimeter .'${tr.Fv($property.name)}' . $stringDelimeter . $separator;
				}
#end				
#foreach( $relation in $entity.childRelations )
				if (P('show_export_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") != "0") {
					$data = $data . $stringDelimeter . '${tr.Fv($relation.parentEntity.name)} ${tr.Fv($relation.name)}' . $stringDelimeter . $separator;
				}
#end
			$data = $data . "\n";
			$hideColumnFromContextInTable = array();
#parseFile("modules_project/beforeSelect/${tr.f_v($entity.name)}.php")
#set ($tableDisplayMode = "export")					
#parseFileWithStandard("modules_project/tableSelect/${tr.f_v($entity.name)}.php" "defs/entity/table_select.php")
			$results${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->select(
				'', 
				'all', 
				$result${tr.FV($entity.name)}->getOrderColumn(), 
				$result${tr.FV($entity.name)}->getOrderMode(), 
				$whereClause${tr.FV($entity.name)},
				$queryString);
			foreach ($results${tr.FV($entity.name)} as $result${tr.FV($entity.name)}) {
#foreach( $property in $entity.properties )
#if ($property.obligatory || $showByDefault == '1')
#set ($obl = '1')
#else
#set ($obl = '0')
#end
				if (P('show_export_${tr.f_v($property.name)}', "$obl") != "0") {
#renderCSV($entity $property)
				}
#end				
#foreach( $relation in $entity.childRelations )
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
				if (P('show_export_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") != "0") {
					$parentValue = virgo${tr.FV($relation.parentEntity.name)}::lookup($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id']);
					$data = $data . $stringDelimeter . $parentValue . $stringDelimeter . $separator;
				}
#end
				$data = $data . "\n"; 
			}
			D($data, $reportTitle, "text/csv"); 
		}
				
		static function portletActionOffline() {
			require_once(PORTAL_PATH.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php');		
			require_once(PORTAL_PATH.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Writer'.DIRECTORY_SEPARATOR.'Excel2007.php');		
			$pob = self::getMyPortletObject();
			$reportTitle = $pob->getCustomTitle();
			if (is_null($reportTitle)) {
				$reportTitle = T('${tr.F_V($entity.namePlural)}');
			}
#set ($tableDisplayMode = "export")		
#set ($showByDefault = "1")
			$objPHPExcel = new \PHPExcel();
			$objPHPExcel->getProperties()->setCreator("virgo by METADETRON");
			$objPHPExcel->getProperties()->setLastModifiedBy("");
			$objPHPExcel->getProperties()->setTitle($reportTitle);
			$objPHPExcel->getProperties()->setSubject("");
			$objPHPExcel->getProperties()->setDescription("virgo generated Excel Sheet for offline data edition");
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getProtection()->setPassword('virgo');
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			$objPHPExcel->getActiveSheet()->setTitle($reportTitle);
			$result${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
			$whereClause${tr.FV($entity.name)} = " 1 = 1 ";
#################### virgo Context ####################
			$parentContextInfos = self::getParentsInContext();
			foreach ($parentContextInfos as $parentContextInfo) {
				$whereClause${tr.FV($entity.name)} = $whereClause${tr.FV($entity.name)} . ' AND ' . $parentContextInfo['condition'];
			}
########################################################
				$headerStyle = array('font' => array('bold' => true));
				$idStyle = array('font' => array('bold' => true));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'ID');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->applyFromArray($headerStyle);
				$kolumna = 0;
#foreach( $property in $entity.properties )
				if (P('show_export_${tr.f_v($property.name)}', "1") == "1") {
					$kolumna = $kolumna + 1;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($kolumna, 1, '${tr.Fv($property.name)}');
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($kolumna, 1)->applyFromArray($headerStyle);
				}
#end				
#foreach( $relation in $entity.childRelations )
				if (P('show_export_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
					$kolumna = $kolumna + 1;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($kolumna, 1, '${tr.Fv($relation.parentEntity.name)} ${tr.Fv($relation.name)}');
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($kolumna, 1)->applyFromArray($headerStyle);					
					
					$parentList = virgo${tr.FV($relation.parentEntity.name)}::getVirgoList();
					$formula${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} = ""; 
					foreach ($parentList as $id => $key) {
						if ($formula${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} != "") {
							$formula${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} = $formula${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} . ',';
						}
						$formula${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} = $formula${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} . $key;
					}
				}
#end
			$iloscKolumn = $kolumna;
			$hideColumnFromContextInTable = array();
#parseFile("modules_project/beforeSelect/${tr.f_v($entity.name)}.php")
#parseFileWithStandard("modules_project/tableSelect/${tr.f_v($entity.name)}.php" "defs/entity/table_select.php")
			$results${tr.FV($entity.name)} = $result${tr.FV($entity.name)}->select(
				'', 
				'all', 
				$result${tr.FV($entity.name)}->getOrderColumn(), 
				$result${tr.FV($entity.name)}->getOrderMode(), 
				$whereClause${tr.FV($entity.name)},
				$queryString);
			$index = 1;
			foreach ($results${tr.FV($entity.name)} as $result${tr.FV($entity.name)}) {
				$index = $index + 1;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $index, $result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_id']);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $index)->getFont()->getColor()->applyFromArray(array("rgb" => 'BBBBBB'));
				$kolumna = 0;
#foreach( $property in $entity.properties )
				if (P('show_export_${tr.f_v($property.name)}', "1") == "1") {
					$kolumna = $kolumna + 1;
#renderExcel($entity $property $kolumna)
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($kolumna, $index)->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				}
#end				
#foreach( $relation in $entity.childRelations )
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
				if (P('show_export_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}', "1") == "1") {
					$kolumna = $kolumna + 1;
					$parentValue = virgo${tr.FV($relation.parentEntity.name)}::lookup($result${tr.FV($entity.name)}['${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}${underscore}${tr.f_v($relation.name)}_id']);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($kolumna, $index, $parentValue);
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($kolumna, $index)->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
					$objValidation = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($kolumna, $index)->getDataValidation();
					$objValidation->setType( \PHPExcel_Cell_DataValidation::TYPE_LIST );
					$objValidation->setErrorStyle( \PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle('Input error');
					$objValidation->setError('Value is not in list.');
					$objValidation->setPromptTitle('Pick from list');
					$objValidation->setPrompt('Please pick a value from the drop-down list.');
					$objValidation->setFormula1('"' . $formula${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)} . '"');
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($kolumna, $index)->setDataValidation($objValidation);					
				}
#end
			}
		    for($i = 1; $i <= $iloscKolumn; $i++) {
		        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
		    }
		    $objPHPExcel->getActiveSheet()->calculateColumnWidths();
			$objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
			
			header('Content-Type: application/vnd.ms-excel');
			if (headers_sent()) {
				echo 'Some data has already been output to browser';
			}
			header('Content-Disposition: attachment; filename="' . $reportTitle . '.xlsx";');
//			header('Content-Length: '.strlen($data));		
			$objWriter->save("php://output");			
			exit();
		}		
		
		static function portletActionUpload() {
			$userfile = $_FILES['virgo_upload_file'];
			if ( $userfile['error'] || $userfile['size'] < 1 ) {
				L("$messages.FILE_NOT_UPLOADED", '', 'ERROR');		 
			} else {
// PATH_TXTUPL is not reliable? Lets use $config->getValue('tmp_path') instead
//				$componentParams = null;
//				$separatorString = $componentParams->get('field_separator_in_import');
				$separatorString = P('field_separator');
				if (is_null($separatorString) || $separatorString == "") {
					$separatorString = ",";
				} elseif ($separatorString == "TAB") {
					$separatorString = "\t";
				}
				$this->setImportFieldSeparator($separatorString);
				$tmp_dest = PORTAL_PATH.DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.'tmp_upload_'.date("YmdHis").'.txt';
				$tmp_src   = $userfile['tmp_name'];
//				$user =& JFactory::getUser();
				if ( move_uploaded_file($tmp_src, $tmp_dest ) ) {
					$fh = fopen($tmp_dest, 'r');
					$firstLine = fgets($fh);
					$columns = split($separatorString, $firstLine);
					$propertyColumnHash = array();
					$propertyDateFormatHash = array();
					$propertyClassHash = array();
#foreach( $property in $entity.properties )
					$propertyColumnHash['$property.name'] = '${entity.prefix}_${tr.f_v($property.name)}';
					$propertyColumnHash['${tr.f_v($property.name)}'] = '${entity.prefix}_${tr.f_v($property.name)}';
#if ($property.dataType.name == 'DATE')
					$dateFormat = P('import_format_${tr.f_v($property.name)}');
					if (isset($dateFormat)) {
						$propertyDateFormatHash['${entity.prefix}_${tr.f_v($property.name)}'] = $dateFormat;
					}
#end
#end
#foreach( $relation in $entity.childRelations )
					$propertyClassHash['$relation.parentEntity.name'] = '${tr.FV($relation.parentEntity.name)}';
					$propertyClassHash['${tr.f_v($relation.parentEntity.name)}'] = '${tr.FV($relation.parentEntity.name)}';
#if ($relation.name)
					$propertyColumnHash['$relation.parentEntity.name $relation.name'] = '${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id';
					$propertyColumnHash['${tr.f_v($relation.parentEntity.name)} ${tr.f_v($relation.name)}'] = '${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}_id';
#else
					$propertyColumnHash['$relation.parentEntity.name'] = '${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_id';
					$propertyColumnHash['${tr.f_v($relation.parentEntity.name)}'] = '${entity.prefix}_${tr.f_v($relation.parentEntity.prefix)}_id';
#end
#end
					$importMode = P('import_mode', 'T');
					$recordsOK = 0;
					$recordsError = 0;
					while(!feof($fh)) {
						$import${tr.FV($entity.name)} = new virgo${tr.FV($entity.name)}();
						$line = fgets($fh);
						if (is_null($line) || trim($line) == "") {
						} else {
							$values = split($separatorString, $line);
							$index = 0;
							foreach ($values as $value) {
								$value = trim($value);
								if (isset($columns[$index]) && trim($columns[$index]) != "VIRGO_IGNORE") {
									$fieldName = $propertyColumnHash[trim($columns[$index])];
									if (substr($fieldName, strlen($fieldName) - 3) == "_id") {
										$className = 'virgo' . $propertyClassHash[trim($columns[$index])];
										$parent = new $className();
										$value = $parent->getIdByVirgoTitle($value);
									}
									if (is_null($fieldName)) {
##									JError::raiseWarning( 0, $this->formatMessage('${messages.parse("PROPERTY_NOT_FOUND", ["${tr.Fv($entity.name)}"])}', array(trim($columns[$index]))));
										L(T('PROPERTY_NOT_FOUND', T('${tr.F_V($entity.name)}'), $columns[$index]), '', 'ERROR');
										return;
									} else {
										if (isset($propertyDateFormatHash[$fieldName])) {
											$dateFormat = $propertyDateFormatHash[$fieldName];
											if (version_compare(PHP_VERSION, '5.3.0') >= 0) {												
												$dateInfo = date_parse_from_format($dateFormat, $value);
												$value = $dateInfo['day'] . '.' . $dateInfo['month'] . '.' . $dateFormat['year'];

											}
											$value = date(DATE_FORMAT, strtotime($value));
										}
										$import${tr.FV($entity.name)}->$fieldName = $value;
									}
								}
								$index = $index + 1;
							}
#foreach( $relation in $entity.childRelations )
$defaultValue = P('import_default_value_${tr.f_v($relation.parentEntity.name)}${tr.f_v($relation.name)}');
if (isset($defaultValue) && trim($defaultValue) != "") {
#calculateDefaultValue()	
	$import${tr.FV($entity.name)}->set${tr.Fv($relation.parentEntity.prefix)}${tr.Fv($relation.name)}Id($defaultValue);
}
#end
							$errorMessage = $import${tr.FV($entity.name)}->store();
							if ($errorMessage != "") {
								if ($importMode == "T") {
									L($errorMessage, '', 'ERROR');
									fclose($fh);
									unset($propertyColumnHash);
									unset($propertyClassHash); 
									return -1;
								} else {
									$recordsError++;
									L('Error on import: ' . $errorMessage, '', 'WARN');
								}
							} else {
								$recordsOK++;
							}
						}
					}
					fclose($fh);
					unset($propertyColumnHash);
					unset($propertyClassHash); 
					L(T('VALUES_UPLOADED', $recordsOK, $recordsError), '', 'INFO'); 
					return 0;
				}
			}
		}
		
## TODO: to jest chyba metoda do usuniecia, bo teraz jej role przejal portletActionVirgoSet...()

#foreach( $relation in $entity.childRelations )
#if ($entity.name != $relation.parentEntity.name)
#if ($relation.parentEntity.dictionary)
		static function portletActionVirgoChange${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}() {
			$instance = new virgo${tr.FV($entity.name)}();
			$instance->loadFromDB();
			self::portletActionSelect(true, $instance->getId());
			$parentId = R('virgo_parent_id');
			$parent = virgo${tr.FV($relation.parentEntity.name)}::getById($parentId);
			$title = $parent->getVirgoTitle();
			if (!is_null($title) && trim($title) != "") {
				$instance->set${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id($parentId);
				$errorMessage = $instance->store();
				if ($errorMessage == "") {
					L(T('PARENT_SET', T('${tr.F_V($relation.parentEntity.name)}'), $title), '', 'INFO');
					return 0;
				} else {
					L($errorMessage, '', 'ERROR');
					return -1;
				}
			} else {
				L("$messages.PARENT_NOT_FOUND", '', 'ERROR');
				return -1;
			}
		}
#end
#end
#end

################### n:m dodawanie zbiorowe (poczatek)####################
## Dla wszystkich dzieci, ktore sa tabelami n:m
#foreach( $relation2 in $entity.parentRelations )
#if ($relation2.childEntity.weak)
## Dla wszystkich jego rodzicow, ktorzy nie sa aktualna encja inie sa slownikami:
#foreach( $relation in $relation2.childEntity.childRelations )
#if ($relation.parentEntity.name != $entity.name && !$relation.parentEntity.dictionary)
		static function portletActionAddSelectedToNMRecord${tr.FV($relation.parentEntity.name)}() {
			$${tr.fV($relation.parentEntity.name)}Id = R('${tr.fV($relation.childEntity.prefix)}_${tr.fV($relation.parentEntity.name)}_');
			$idsToDeleteString = R('ids');
			$idsToDelete = split(",", $idsToDeleteString);
			foreach ($idsToDelete as $idToDelete) {
				$new${tr.FV($relation2.childEntity.name)} = new virgo${tr.FV($relation2.childEntity.name)}();
				$new${tr.FV($relation2.childEntity.name)}->set${tr.FV($entity.prefix)}Id($idToDelete);
				$new${tr.FV($relation2.childEntity.name)}->set${tr.FV($relation.parentEntity.prefix)}Id($${tr.fV($relation.parentEntity.name)}Id);
				$errorMessage = $new${tr.FV($relation2.childEntity.name)}->store();
				if ($errorMessage != "") {
					L($errorMessage, '', 'ERROR');
					return -1;
				}

			}
			self::setDisplayMode("TABLE");
			return 0;
		}
## #parentSelect()
## #actionButton("AddSelectedToNMRecord" "$messages.ADD_SELECTED" "" "!nothingSelected(this.form)" "$messages.NOTHING_SELECTED" "" "copyIds(form);") 
#end
#end
#end
#end
################### n:m dodawanie zbiorowe (koniec)  ####################

#set ($hasDate = "0")
#foreach( $property in $entity.properties )
#if ($property.dataType.name == "DATE" || $property.dataType.name == "DATETIME") 
#set ($hasDate = "1")
#end
#end
#if ($hasDate == "1")
		static function portletActionPreviousYear() {
			$pob = self::getMyPortletObject();
			$selectedYear = $pob->getPortletSessionValue('selected_year', date("Y"));
			$pob->setPortletSessionValue('selected_year', $selectedYear-1);
		}

		static function portletActionPreviousMonth() {
			$pob = self::getMyPortletObject();
			$selectedMonth = $pob->getPortletSessionValue('selected_month', date("m"));
			$selectedMonth = $selectedMonth - 1;
			if ($selectedMonth == 0) {
				$selectedMonth = 12;
				$pob->portletActionPreviousYear();
			}
			$pob->setPortletSessionValue('selected_month', $selectedMonth);
		}

		static function portletActionNextYear() {
			$pob = self::getMyPortletObject();
			$selectedYear = $pob->getPortletSessionValue('selected_year', date("Y"));
			$pob->setPortletSessionValue('selected_year', $selectedYear+1);
		}

		static function portletActionNextMonth() {			
			$pob = self::getMyPortletObject();
			$selectedMonth = $pob->getPortletSessionValue('selected_month', date("m"));
			$selectedMonth = $selectedMonth + 1;
			if ($selectedMonth == 13) {
				$selectedMonth = 1;
				$pob->portletActionNextYear();
			}
			$pob->setPortletSessionValue('selected_month', $selectedMonth);
		}

		static function portletActionCurrentMonth() {
			$pob = self::getMyPortletObject();
			$pob->setPortletSessionValue('selected_month', date("m"));
			$pob->setPortletSessionValue('selected_year', date("Y"));
		}

		static function portletActionSetMonth() {
			$pob = self::getMyPortletObject();
			$selectedMonth = R('virgo_cal_selected_month');
			$pob->setPortletSessionValue('selected_month', $selectedMonth);
		}

		static function portletActionSetYear() {
			$pob = self::getMyPortletObject();
			$selectedYear = R('virgo_cal_selected_year');
			$pob->setPortletSessionValue('selected_year', $selectedYear);
		}
#end		

#foreach( $relation in $entity.childRelations )
		static function portletActionVirgoSet${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}() {
			$this->loadFromDB();
			$parentId = R('${tr.fV($entity.prefix)}_${tr.FV($relation.parentEntity.name)}${tr.FV($relation.name)}_id_' . $_SESSION['current_portlet_object_id']);
			$this->set${tr.FV($relation.parentEntity.prefix)}${tr.FV($relation.name)}Id($parentId);
			$ret = $this->store();
			if ($ret == "") {
				return 0;
			} else {
				return -1;
			}
		}
#end

## dodanie nowego parenta
## tylko dla wymaganych relacji i dodajemy go minimalnie, czyli tylko wymagane pola
#foreach( $relationToRender in $entity.childRelations )
#if (!$relationToRender.parentEntity.dictionary)
#if ($relationToRender.obligatory)
#set ($backupRelation = $relation)
#set ($backupRelationToRender = $relationToRender)
#set ($includeBackFromParet = 'true')		
		static function portletActionAdd${tr.FV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}() {
			self::setDisplayMode("ADD_NEW_PARENT_${tr.F_V($relationToRender.parentEntity.name)}${relationToRender.u}${tr.F_V($relationToRender.name)}");
		}

		static function portletActionStoreNew${tr.FV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}() {
			$id = -1;
			if (virgo${tr.FV($relationToRender.parentEntity.name)}::portletActionStore(true, $id) == -1) {
				self::setDisplayMode("ADD_NEW_PARENT_${tr.F_V($relationToRender.parentEntity.name)}${relationToRender.u}${tr.F_V($relationToRender.name)}");
				$pob = self::getMyPortletObject();
				$pob->setPortletSessionValue('reload_from_request', '1');				
			} else {
				$tmpId = self::loadIdFromRequest();
				$_POST['${tr.f_v($entity.prefix)}_${tr.fV($relationToRender.parentEntity.name)}${tr.FV($relationToRender.name)}_' . ($tmpId == 0 ? '' : $tmpId)] = $id;
				self::portletActionBackFromParent();
			}
		}
#set ($relation = $backupRelation)
#end
#end
#end

#if ($includeBackFromParet == 'true')
		static function portletActionBackFromParent() {
			$calligView = strtoupper(R('calling_view'));
			self::setDisplayMode($calligView);
			$pob = self::getMyPortletObject();
			$pob->setPortletSessionValue('reload_from_request', '1');				
		}
#end
#parseFile("modules_project/extraMethods/${tr.f_v($entity.name)}.php")

#parseFile("modules_project/customActions/${tr.f_v($entity.name)}.php")

		static function createTable() {
			$query =  <<<SELECT
#createTable()
SELECT;
			if (!Q($query)) {
				L("Probably ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} table already exists.", '', 'FATAL');
				L("Error ocurred, please contact site Administrator.", '', 'ERROR');
 				return false;
 			}
 			return true;
 		}

#if ($entity.versioned)
		function createHistoryTable() {
			$query =  <<<SELECT
#createHistoryTable()
SELECT;
			if (!Q($query)) {
				L(QER(), '', 'ERROR');
 				return false;
 			}
 			return true;
		}
#end

		static function onInstall($pobId, $title) {
#parseFile("modules_project/onInstall/${tr.f_v($entity.name)}.php")			
		}

#set ($doneImage = 0)
#foreach( $property in $entity.properties )
#if ($property.dataType.name == 'IMAGE' && $doneImage == 0)
#set ($doneImage = 1)
		static function getCacheFileName($id, $imageProperty, $sizeX, $sizeY) {
			return "" . $id . "-" . $imageProperty . "-" . $sizeX . "x" . $sizeY . ".jpg";
		}

## zwraca url do podania np. w Location:		
		static function getCachedImagePath($id, $imageProperty, $thumbnailWidth, $thumbnailHeight) {
			if (!file_exists(JPATH_BASE . "/tmp/image_cache/${tr.f_v($entity.name)}")) {
				if (!file_exists(JPATH_BASE . "/tmp/image_cache")) {
					if (!file_exists(JPATH_BASE . "/tmp")) {
						mkdir(JPATH_BASE . "/tmp");
					}
					mkdir(JPATH_BASE . "/tmp/image_cache");
				}
				mkdir(JPATH_BASE . "/tmp/image_cache/${tr.f_v($entity.name)}");
			}
			$cacheFile = "/tmp/image_cache/${tr.f_v($entity.name)}/" . virgo${tr.FV($entity.name)}::getCacheFileName($id, $imageProperty, $thumbnailWidth, $thumbnailHeight);
			$cacheFileName = JPATH_BASE . $cacheFile;
			if (!file_exists($cacheFileName)) {
				$src_image = imagecreatefromstring($tmp_${entity.prefix}->$imagePropertyFullName);
				$imageX = imagesx($src_image);
				$imageY = imagesy($src_image);
				$factorX = $imageX / $thumbnailWidth;
				$factorY = $imageY / $thumbnailHeight;
				$factor = max($factorX, $factorY);
				$thumbnailWidth = $imageX / $factor;
				$thumbnailHeight = $imageY / $factor;
				$dst_image = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
				imagecopyresized($dst_image, $src_image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $imageX, $imageY);
				imagejpeg($dst_image, $cacheFileName, 85);
				imagedestroy($dst_image);
			}
			return $cacheFile;
		}
#end
#end
#set ($dollar = '$')
#foreach( $uniqueKey in $entity.uniqueKeys )
## gorzej jak bedzie encja z wlasciwoscia o tej samej nazwie co parent :-/ Co chyba wcale nie byloby az takie dziwne...
		static function getIdByKey#set($indexUnq=0)#foreach( $columnSet in $uniqueKey.columnSets )#if ($indexUnq == 1)And#else#set($indexUnq = 1)#end${tr.FV($columnSet.property.name)}${tr.FV($columnSet.relation.parentEntity.name)}${tr.FV($columnSet.relation.name)}#end(#set($indexUnq=0)#foreach( $columnSet in $uniqueKey.columnSets )#if ($indexUnq == 1), #else#set($indexUnq = 1)#end${dollar}${tr.fV($columnSet.property.name)}${tr.fV($columnSet.relation.parentEntity.name)}${tr.fV($columnSet.relation.name)}#end) {
			$query = " SELECT ${tr.f_v($entity.prefix)}_id FROM ${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)} WHERE 1 ";
#foreach( $columnSet in $uniqueKey.columnSets )
#if ($columnSet.property)			
#set ($property = $columnSet.property)
			$query .= " AND ${tr.f_v($entity.prefix)}_${tr.f_v($property.name)} = '{${dollar}${tr.fV($columnSet.property.name)}}' ";
#else
#set ($relation = $columnSet.relation)
#if ($relation.name)
#set ($underscore = "_")
#else
#set ($underscore = "")
#end
			$query .= " AND ${tr.f_v($entity.prefix)}_${tr.f_v($relation.parentEntity.prefix)}_${tr.f_v($relation.name)}${underscore}id = {${dollar}${tr.fV($columnSet.relation.parentEntity.name)}${tr.fV($columnSet.relation.name)}} ";
#end			
#end	
			$rows = QR($query);
			foreach ($rows as $row) {
				return $row['${tr.f_v($entity.prefix)}_id'];
			}
			return null;
		}

		static function getByKey#set($indexUnq=0)#foreach( $columnSet in $uniqueKey.columnSets )#if ($indexUnq == 1)And#else#set($indexUnq = 1)#end${tr.FV($columnSet.property.name)}${tr.FV($columnSet.relation.parentEntity.name)}${tr.FV($columnSet.relation.name)}#end(#set($indexUnq=0)#foreach( $columnSet in $uniqueKey.columnSets )#if ($indexUnq == 1), #else#set($indexUnq = 1)#end${dollar}${tr.fV($columnSet.property.name)}${tr.fV($columnSet.relation.parentEntity.name)}${tr.fV($columnSet.relation.name)}#end) {
			$id = self::getIdByKey#set($indexUnq=0)#foreach( $columnSet in $uniqueKey.columnSets )#if ($indexUnq == 1)And#else#set($indexUnq = 1)#end${tr.FV($columnSet.property.name)}${tr.FV($columnSet.relation.parentEntity.name)}${tr.FV($columnSet.relation.name)}#end(#set($indexUnq=0)#foreach( $columnSet in $uniqueKey.columnSets )#if ($indexUnq == 1), #else#set($indexUnq = 1)#end${dollar}${tr.fV($columnSet.property.name)}${tr.fV($columnSet.relation.parentEntity.name)}${tr.fV($columnSet.relation.name)}#end);
			$ret = new virgo${tr.FV($entity.name)}();
			if (isset($id)) {
				$ret->load($id);
			}
			return $ret;
		}
#end

		static function token2Id($token, $extraLimit = null) {
			if (S($token)) {
				$ids = self::selectAllAsIdsStatic($extraLimit, true);
				foreach ($ids as $id) {
					if (getTokenValue($id) == $token) {
						return $id;
					}
				}
			}
			return null;
		}

		static function getMyPortletObject() {
			if (file_exists(PORTAL_PATH.DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'portal'.DIRECTORY_SEPARATOR.'virgoPortletObject'.DIRECTORY_SEPARATOR.'controller.php')) {
				require_once(PORTAL_PATH .DIRECTORY_SEPARATOR.'portlets'.DIRECTORY_SEPARATOR.'portal'.DIRECTORY_SEPARATOR.'virgoPortletObject'.DIRECTORY_SEPARATOR.'controller.php');
				$pobId = $_SESSION['current_portlet_object_id'];
				return new virgoPortletObject($pobId);
			}
			return null;
		}
		
## metody na styku portletu i struktury projektu (kiedy to nie kod portletów 
## generowanych, lecz innych, np. portal_core, chce skorzystać z wiedzy o 
## strukturze danych):
		static function getPrefix() {
			return "${tr.f_v($entity.prefix)}";
		}
		
		static function getPlural() {
			return "${tr.f_v($entity.namePlural)}";
		}
		
		static function isDictionary() {
#if ($entity.dictionary)		
			return true;
#else
			return false;
#end
		}

		static function getParents() {
			$ret = array();
#foreach( $relation in $entity.childRelations )
			$ret[] = "virgo${tr.FV($relation.parentEntity.name)}";
#end						
			return $ret;
		}

		static function getChildren() {
#foreach( $relation in $entity.parentRelations )
			$ret[] = "virgo${tr.FV($relation.childEntity.name)}";
#end						
		}
		
## czasami tabeli nie ma tylko widok lub wrecz nic
		static function getVirgoTableType() {
			global $virgoConfig;
			$query = " SELECT table_type FROM information_schema.tables WHERE table_schema = ? AND table_name = ? ";
			$rows = QPR($query, "ss", array($virgoConfig[VIRGO_PORTAL_INSTANCE]['database_name'], '${tr.f_v($application.prefix)}_${tr.f_v($entity.namePlural)}'));
			foreach ($rows as $row) {
				return $row['table_type'];
			}
			return "";
		}
		
		static function getStructureVersion() {
			return "${application.versionMain}.${application.version}" . 
#parseFileWithStandardText("modules_project/extensionVersion.php" "''") 
			;
		}
		
		static function getVirgoVersion() {
			return
#parseFile("defs/virgoVersion.php")
			;
		}
		
## UWAGA: Jak sie zmieni w index.php albo w classes jakas funkcja, z ktorej korzystaja komponenty
##        virgo, to trzeba zmienic na gorze index.php wersje o major do przodu!
##	  Jak sie zmieni w modules_project portalu to zmieniamy wersje portal.xml nawet, jesli w 
##        zasadzie struktura sie nie zmienila  
## zwraca:
##   1 - OK
##   0 - may need regeneration
##  -1 - requires regeneration
		static function checkCompatibility() {
## metoda odpowiada na pytanie: jaka byla aktualna wersja index.php (portalu) w 
## momencie generowania komponentu
			$virgoVersion = virgo${tr.FV($entity.name)}::getVirgoVersion();
			if ($virgoVersion == INDEX_VERSION) {
				return 1;
			}
			$virgoVersionNumber = substr($virgoVersion, 0, strpos($virgoVersion, "."));
			$portalVersionNumber = substr(INDEX_VERSION, 0, strpos(INDEX_VERSION, "."));
			if ($virgoVersionNumber == $portalVersionNumber) {
				return 0;
			}
			return -1;
		}

#*
		jest juz taka metoda :-/
		static function getParentsInContext() {
			if (!isset($_REQUEST['_virgo_parents_in_context_' . $_SESSION['current_portlet_object_id']])) {
				$parentsInContext = array();
				$parentPobIds = PN('parent_entity_pob_id');
				foreach ($parentPobIds as $parentPobId) {
					$portletObject = new portal\virgoPortletObject($parentPobId);
					$className = $portletObject->getPortletDefinition()->getNamespace().'\\'.$portletObject->getPortletDefinition()->getAlias();
					$tmp2Id = $className::getRemoteContextId($parentPobId);
					if (isset($tmp2Id)) {
						$parentsInContext[$className] = $tmp2Id;
					}
				}
				$_REQUEST['_virgo_parents_in_context_' . $_SESSION['current_portlet_object_id'] = $parentsInContext];
			} else {
				$parentsInContext = $_REQUEST['_virgo_parents_in_context_' . $_SESSION['current_portlet_object_id'];
			}
			return $parentsInContext;
		} 
*#

		static function getParentInContext($parentName) {
			$parentsInContext = self::getParentsInContext();
			if (isset($parentsInContext[$parentName])) {
				$parentInfo = $parentsInContext[$parentName];
				if (isset($parentInfo['contextId'])) {
					return $parentInfo['contextId'];
				} else {
					return null;
				}
			} else {
				return null;
			}
		}

## uzywac zamiast widokow, zwlaszcza tych group by, bo to tylko spowalnia, zamiast selektywnie wybierac...
## niech te metody beda statyczne, zeby mozna je bylo wolac ze statycznego kontekstu
		/****************** database selects ******************/
#parseFile("modules_project/selects/${tr.f_v($entity.name)}.php")					
		
	}
	
	
#end
#end
#end
