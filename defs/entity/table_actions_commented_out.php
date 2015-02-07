Po DELETE_SELECTED
#*
################## n:m dodawanie zbiorowe (poczatek)#################### WYLACZONE NA RAZIE!!!
## Dla wszystkich dzieci, ktore sa tabelami n:m
<table>
#foreach( $relation2 in $entity.parentRelations )
#if ($relation2.childEntity.weak)
## Dla wszystkich jego rodzicow, ktorzy nie sa aktualna encja inie sa slownikami:
#foreach( $relation in $relation2.childEntity.childRelations )
#if ($relation.parentEntity.name != $entity.name && !$relation.parentEntity.dictionary)
	<tr>
		<td align="right">
			${tr.Fv($relation.parentEntity.name)}:
		</td>
		<td>
#parentSelect("form")
		</td>
		<td>
#actionButton("AddSelectedToNMRecord${tr.FV($relation.parentEntity.name)}" "'ADD_SELECTED'" "" "!nothingSelected(this.form, <?php echo $this->getId() ?>)" "'NOTHING_SELECTED'" "" "copyIds(this.form, <?php echo $this->getId() ?>);") 
<?php
	if ($buttonRendered) {
		$showSelected = true;
	}
?>
		</td>
	</tr>
#end
#end
#end
#end
</table>
################### n:m dodawanie zbiorowe (koniec)  ################### 
*#

