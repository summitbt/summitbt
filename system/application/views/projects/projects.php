<table>
	<tr>
		<th>Project</th>
		<th>Key</th>
		<th>Project Lead</th>
		<th>URL</th>
	</tr>
	{{ page:projects }}
		<tr>
			<td><a href="{{ site:url url='project/{{code}}' }}" title="{{ name }}">{{ name }}</a></td>
			<td>{{ code }}</td>
			<td><a href="{{ site:url url='user/{{lead_username}}' }}" title="{{ lead_first_name }} {{ lead_last_name }}">{{ lead_first_name }} {{ lead_last_name }}</a></td>
			<td>{{ url }}</td>
		</tr>
	{{ /page:projects }}

</table>