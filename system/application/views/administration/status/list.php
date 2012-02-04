<p>
	<a href="{{ site:url url='administration/status/new' }}" title="Create New Issue Status" class="positive button">Create New Issue Status</a>
</p>

{{ if page:status_results }}

	<div id="pagination-top">
		{{ page:pagination }}
	</div>

	<table>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Icon</th>
			<th>Actions</th>
		</tr>
		{{ page:statuses }}
			<tr>
				<td>
					<a href="{{ site:url url='administration/status/edit' }}/{{ id }}" title="Edit {{ name }}">{{ name }}</a>
				</td>
				<td>{{ description }}</td>
				<td class="a-center"><img src="{{ site:base url='uploads/icons/{{ icon }}' }}" alt="{{ name }}" /></td>
				<td>
					<div class="button-group">
						<a href="{{ site:url url='administration/status/edit' }}/{{ id }}" title="Edit {{ name }}" class="button">Edit</a>
						<a href="{{ site:url url='administration/status/delete' }}/{{ id }}" title="Delete {{ name }}" class="negative button">Delete</a>
					</div>
				</td>
			</tr>
		{{ /page:statuses }}
	</table>

	<div id="pagination-bottom">
		{{ page:pagination }}
	</div>

{{ else }}

	No results were found

{{ endif }}