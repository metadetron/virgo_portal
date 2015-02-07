## parsowanie versions wstawia puste linie, wiec komentujemy je w HTML
<!--
#parse("defs/versions.php")
-->
<span 
	title="header=[<?php echo JText::_('VERSIONS') ?>]cssheader=[tooltip_header]cssbody=[tooltip_body]body=
[
#set ($counter = 0)
#foreach ($version in $versions.versions)
#if ($counter == 0) 
#set ($current = $version.revision)
#end
#set ($counter = $counter + 1)
#if ($counter <= 10)
$version.revision -
#if ($version.type == "T")
<b>
#elseif ($version.type == "B")
<i>
#elseif ($version.type == "R")
<i><b>
#end
$version.description
#if ($version.type == "T")
</b>
#elseif ($version.type == "B")
</i>
#elseif ($version.type == "R")
</b></i>
#end
<br>
#end
#end
#if ($counter > 10)
...<br>
#end
]">$current</span>
