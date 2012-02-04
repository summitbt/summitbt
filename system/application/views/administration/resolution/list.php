<p>
	<a href="{{ site:url url='administration/resolution/new' }}" title="Create New Issue Resolution" class="positive button">Create New Issue Resolution</a>
</p>

{{ if page:resolution_results }}

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
		{{ page:resolutions }}
			<tr>
				<td>
					<a href="{{ site:url url='administration/resolution/edit' }}/{{ id }}" title="Edit {{ name }}">{{ name }}</a>
				</td>
				<td>{{ description }}</td>
				<td>{{ icon }}</td>
				<td>
					<div class="button-group">
						<a href="{{ site:url url='administration/resolution/edit' }}/{{ id }}" title="Edit {{ name }}" class="button">Edit</a>
						<a href="{{ site:url url='administration/resolution/delete' }}/{{ id }}" title="Delete {{ name }}" class="negative button">Delete</a>
					</div>
				</td>
			</tr>
		{{ /page:resolutions }}
	</table>

	<div id="pagination-bottom">
		{{ page:pagination }}
	</div>

{{ else }}

	No results were found

{{ endif }}