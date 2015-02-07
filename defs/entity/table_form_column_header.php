				<td align="center" valign="middle"><!-- bylo: nowrap -->
#if ($property.dataType.name == 'IMAGE')
					<input type="button" class="button data_table_header" value="${tr.Fv($property.name)}"/>
#else				
						<span style="white-space: normal;" class="data_table_header">
							${tr.Fv($property.name)}
#if ($property.obligatory)
*
#end							
						</span>
#end					
				</td>

