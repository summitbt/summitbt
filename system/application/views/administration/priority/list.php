<p>
	<a href="{{ site:url url='administration/priority/new' }}" title="Create New Issue Priority" class="positive button">Create New Issue Priority</a>
</p>

{{ if page:priority_results }}

	<div id="pagination-top">
		{{ page:pagination }}
	</div>

	<table>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Color</th>
			<th>Icon</th>
			<th>Actions</th>
		</tr>
		{{ page:priorities }}
			<tr>
				<td>
					<a href="{{ site:url url='administration/priority/edit/{{ id }}' }}" title="Edit {{ name }}">{{ name }}</a>
				</td>
				<td>{{ description }}</td>
				<td>{{ color }}</td>
				<td class="a-center"><img src="{{ site:base url='uploads/icons/{{ icon }}' }}" alt="{{ name }}" /></td>
				<td>
					<div class="button-group">
						<a href="{{ site:url url='administration/priority/edit/{{ id }}' }}" title="Edit {{ name }}" class="button">Edit</a>
						<a href="{{ site:url url='administration/priority/delete{{ id }}' }}/" title="Delete {{ name }}" class="negative button">Delete</a>
					</div>
				</td>
			</tr>
		{{ /page:priorities }}
	</table>

	<div id="pagination-bottom">
		{{ page:pagination }}
	</div>

{{ else }}

	No results were found

{{ endif }}