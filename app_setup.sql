#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
----- app_setup.sql -----
#foreach( $entity in $application.entities )
#if (!$entity.dictionary)
#set ($workflow = "FALSE")
#foreach( $relation in $entity.childRelations )
#if ($relation.parentEntity.dictionary)
#foreach( $relation2 in $relation.parentEntity.childRelations )
#if ($relation2.parentEntity.name == $relation.parentEntity.name)
#set ($workflow = "TRUE")
#end
#end
#end
#end

#set ($count = 0)
#foreach( $relation in $entity.childRelations )
#if ($relation.parentEntity.dictionary)
#set ($count = $count + 0)
#else
#set ($count = $count + 1)
#end
#end

#if ($workflow == "FALSE" && $count == 0)
-- menu for entity $entity.name
## utworz nowe menu (tabs)
INSERT INTO `jos_menu_types` (`menutype`, `title`, `description`) 
VALUES ('${tr.fV($entity.namePlural)}-tabs-0', '${tr.Fv($entity.name)}', '${tr.Fv($entity.name)} - autogenerated');

## utworz nowy modul z tym menu
INSERT INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) 
VALUES ('${tr.Fv($entity.name)}', '', 0, 'tabs', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 1, 'menutype=${tr.fV($entity.namePlural)}-tabs-0\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, '');

#set ($tabNumber = 0)

## dodawaj taby
INSERT INTO jos_menu (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) 
SELECT '${tr.fV($entity.namePlural)}-tabs-0', 'Lista', '${tr.fV($entity.namePlural)}-lista', 'index.php?option=com_${tr.f_v($application.prefix)}_${tr.f_v($entity.name)}', 'component', '1', '0', jos_components.id, '1', '1', '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0' 
FROM jos_components
WHERE jos_components.name = '${tr.Fv($application.name)} ${tr.Fv($entity.name)}';

INSERT INTO `jos_menu` (`menutype`, 
`name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) 
SELECT (select substr(params, instr(params, 'menutype=') + 9, instr(params, 'menustyle=') - 11)  from jos_modules where position = 'menu' and published = 1 and access = 0 and module like '%menu%'), 
'${tr.Fv($entity.namePlural)}', '${tr.fV($entity.namePlural)}-lista-alias', concat('index.php?Itemid=', jos_menu.id), 'menulink', 1, 0, 0, 0, 99, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, concat(concat('menu_item=', jos_menu.id), '\n\n'), 0, 0, 0
FROM jos_menu
WHERE jos_menu.alias = '${tr.fV($entity.namePlural)}-lista';

INSERT INTO jos_menu (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) 
SELECT '${tr.fV($entity.namePlural)}-tabs-0', 'Dane podstawowe', '${tr.fV($entity.name)}-dane-podstawowe', 'index.php?option=com_${tr.f_v($application.prefix)}_${tr.f_v($entity.name)}', 'component', '1', '0', jos_components.id, '1', '2', '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0' 
FROM jos_components
WHERE jos_components.name = '${tr.Fv($application.name)} ${tr.Fv($entity.name)}';

#*
-- (6) optional
INSERT INTO jos_templates_menu (template, menuid, client_id)
SELECT 'virgo', jos_menu.id, 0
from jos_menu 
where jos_menu.alias in ('${tr.fV($entity.namePlural)}-lista', '${tr.fV($entity.name)}-dane-podstawowe');

-- (7) optional
INSERT INTO jos_modules_menu (moduleid, menuid)
select jos_modules.id, jos_menu.id 
from jos_modules, jos_menu 
where jos_modules.position = 'menu' 
AND jos_modules.published = 1 
AND jos_modules.access = 0 
AND jos_modules.module LIKE '%menu%'
AND jos_menu.alias in ('${tr.fV($entity.namePlural)}-lista', '${tr.fV($entity.name)}-dane-podstawowe');
*#

INSERT INTO jos_modules_menu (moduleid,	menuid)
select jos_modules.id, jos_menu.id 
from jos_modules, jos_menu 
where jos_modules.title = '${tr.Fv($entity.name)}'
and jos_menu.alias in ('${tr.fV($entity.namePlural)}-lista', '${tr.fV($entity.name)}-dane-podstawowe');

#foreach( $descendant in $entity.allDescendants )

#set ($tabNumber = $tabNumber + 1)

#set ($menuOrder1 = $tabNumber * 2 + 1)
#set ($menuOrder2 = $tabNumber * 2 + 2)

INSERT INTO jos_menu (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) 
SELECT '${tr.fV($entity.namePlural)}-tabs-0', '${tr.Fv($descendant.namePlural)}', '${tr.fV($entity.name)}-${tr.fV($descendant.namePlural)}-lista', 'index.php?option=com_${tr.f_v($application.prefix)}_${tr.f_v($descendant.name)}', 'component', '1', '0', jos_components.id, '1', '$menuOrder1', '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0' 
FROM jos_components
WHERE jos_components.name = '${tr.Fv($application.name)} ${tr.Fv($descendant.name)}';

INSERT INTO jos_menu (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) 
SELECT '${tr.fV($entity.namePlural)}-tabs-0', '${tr.Fv($descendant.name)}', '${tr.fV($entity.name)}-${tr.fV($descendant.name)}-dane-podstawowe', 'index.php?option=com_${tr.f_v($application.prefix)}_${tr.f_v($descendant.name)}', 'component', '1', '0', jos_components.id, '1', '$menuOrder2', '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0' 
FROM jos_components
WHERE jos_components.name = '${tr.Fv($application.name)} ${tr.Fv($descendant.name)}';

-- (6) optional
INSERT INTO jos_templates_menu (template, menuid, client_id)
SELECT 'virgo', jos_menu.id, 0
from jos_menu 
where jos_menu.alias in ('${tr.fV($descendant.namePlural)}-lista', '${tr.fV($descendant.name)}-dane-podstawowe');

-- (7) optional
INSERT INTO jos_modules_menu (moduleid, menuid)
select jos_modules.id, jos_menu.id 
from jos_modules, jos_menu
where jos_modules.position = 'menu' 
AND jos_modules.published = 1 
AND jos_modules.access = 0 
AND jos_modules.module LIKE '%menu%'
AND jos_menu.alias in ('${tr.fV($descendant.namePlural)}-lista', '${tr.fV($descendant.name)}-dane-podstawowe');

INSERT INTO jos_modules_menu (moduleid,	menuid)
select jos_modules.id, jos_menu.id 
from jos_modules, jos_menu 
where jos_modules.title = '${tr.Fv($entity.name)}'
and jos_menu.alias in ('${tr.fV($descendant.namePlural)}-lista', '${tr.fV($descendant.name)}-dane-podstawowe');
#end


#end

#end
#end
#end

-- dictionaries
#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
## utworz nowe menu (tabs)
INSERT INTO `jos_menu_types` (`menutype`, `title`, `description`) 
VALUES ('dictionaries-virgo-0', '$messages.DICTIONARIES', '$messages.DICTIONARIES - autogenerated');

## utworz nowy modul z tym menu
INSERT INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) 
VALUES ('$messages.DICTIONARIES', '', 0, 'tabs', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 1, 'menutype=dictionaries-virgo-0\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, '');

#set ($tabNumber = 0)

## dodawaj taby
#foreach( $entity in $application.entities )
#if ($entity.dictionary)
#if ($tabNumber == 0)
#set ($firstDictionary = "${tr.fV($entity.namePlural)}-dictionary")
#end
INSERT INTO jos_menu (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) 
SELECT 'dictionaries-virgo-0', '${tr.Fv($entity.namePlural)}', '${tr.fV($entity.namePlural)}-dictionary', 'index.php?option=com_${tr.f_v($application.prefix)}_${tr.f_v($entity.name)}', 'component', '1', '0', jos_components.id, '1', '1', '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0' 
FROM jos_components
WHERE jos_components.name = '${tr.Fv($application.name)} ${tr.Fv($entity.name)}';
#set ($tabNumber = $tabNumber + 1)
#end
#end

INSERT INTO `jos_menu` (`menutype`, 
`name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) 
SELECT (select substr(params, instr(params, 'menutype=') + 9, instr(params, 'menustyle=') - 11)  from jos_modules where position = 'menu' and published = 1 and access = 0 and module like '%menu%'), 
'$messages.DICTIONARIES', 'dictionaries-virgo-alias', concat('index.php?Itemid=', jos_menu.id), 'menulink', 1, 0, 0, 0, 99, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, concat(concat('menu_item=', jos_menu.id), '\n\n'), 0, 0, 0
FROM jos_menu
WHERE jos_menu.alias = '$firstDictionary';
#end
