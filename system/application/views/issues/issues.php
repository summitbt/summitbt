{{ if page:num_results == 0 }}

	<p>{{ page:no_results }}</p>

{{ else }}

	<table>
		<tr>
			<th>Key</th>
			<th>Summary</th>
			<th>Assignee</th>
			<th>Reporter</th>
			<th>Priority</th>
			<th>Status</th>
			<th>Resolution</th>
			<th>Version</th>
			<th>Component</th>
			<th>Created</th>
			<th>Updated</th>
			<th>Due</th>
		</tr>
		{{ page:issues }}
			<tr>
				<td>{{ code }}</td>
				<td><a href="project/{{ project_code }}/issue/{{ code }}" title="View {{ code }}">{{ summary }}</a></td>
				<td><a href="user/{{ assignee_username }}" title="View {{ assignee_first }} {{ assignee_last }}">{{ assignee_first }} {{ assignee_last }}</a></td>
				<td><a href="user/{{ reporter_username }}" title="View {{ reporter_first }} {{ reporter_last }}">{{ reporter_first }} {{ reporter_last }}</a></td>
				<td>{{ priority }}</td>
				<td>{{ status }}</td>
				<td>{{ resolution }}</td>
				<td>{{ version }}</td>
				<td>{{ component }}</td>
				<td>{{ date_created }}</td>
				<td>{{ date_updated }}</td>
				<td>{{ date_due }}</td>
			</tr>
		{{ /page:issues }}
	</table>

{{ endif }}