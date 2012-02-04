<p>
	<a href="{{ site:url url='administration/type/new' }}" title="Create New Issue Type" class="positive button">Create New Issue Type</a>
</p>

{{ if page:type_results }}

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
		{{ page:types }}
			<tr>
				<td>
					<a href="{{ site:url url='administration/type/edit/{{ id }}' }}" title="Edit {{ name }}">{{ name }}</a>
				</td>
				<td>{{ description }}</td>
				<td class="a-center"><img src="{{ site:base url='uploads/icons/{{ icon }}' }}" alt="{{ name }}" /></td>
				<td>
					<div class="button-group">
						<a href="{{ site:url url='administration/type/edit/{{ id }}' }}/" title="Edit {{ name }}" class="button">Edit</a>
						<a href="{{ site:url url='administration/type/delete/{{ id }}' }}" title="Delete {{ name }}" class="negative button">Delete</a>
					</div>
				</td>
			</tr>
		{{ /page:types }}
	</table>

	<div id="pagination-bottom">
		{{ page:pagination }}
	</div>

{{ else }}

	No results were found

{{ endif }}