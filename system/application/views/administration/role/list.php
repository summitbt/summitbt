<p>
	<a href="{{ site:url url='administration/role/new' }}" title="Create New Role" class="positive button">Create New Role</a>
</p>

{{ if page:role_results }}

	<div id="pagination-top">
		{{ page:pagination }}
	</div>

	<table>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Actions</th>
		</tr>
		{{ page:roles }}
			<tr>
				<td>
					<a href="{{ site:url url='administration/role/edit' }}/{{ id }}" title="Edit {{ name }}">{{ name }}</a>
				</td>
				<td>{{ description }}</td>
				<td>
					<div class="button-group">
						<a href="{{ site:url url='administration/role/edit' }}/{{ id }}" title="Edit {{ name }}" class="button">Edit</a>
						<a href="{{ site:url url='administration/role/delete' }}/{{ id }}" title="Delete {{ name }}" class="negative button">Delete</a>
					</div>
				</td>
			</tr>
		{{ /page:roles }}
	</table>

	<div id="pagination-bottom">
		{{ page:pagination }}
	</div>

{{ else }}

	No results were found

{{ endif }}