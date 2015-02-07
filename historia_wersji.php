#if (!$singleEntity || $singleEntity == ${tr.f_v($entity.name)}) 
----- historia_wersji_virgo.html -----
## parsowanie versions wstawia puste linie, wiec komentujemy je w HTML
<!--
#parse("defs/versions.php")
-->
<ul>
#foreach ($version in $versions.versions)
<li>
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
</li>
#end
</ul>
#end
