<table cellspacing="5">
	{{foreach this}}
	<tr>
		<td>{[getName()]}</td>
		<td>{[getVersion()]}</td>
		<td>{[getDescription()]}</td>
	</tr>
	<tr>
		<td colspan="3">
			<table>
				{{foreach getModuleItems()}}
				<tr>
					<td>{[getName()]}</td>
					<td>{[getDescription()]}</td>
				</tr>
				{{end}}
			</table>
		</td>
	</tr>
	{{end}}
</table>