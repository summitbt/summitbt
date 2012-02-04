<p>
	<a href="{{ site:url url='administration/link/new' }}" title="Create New Issue Link" class="positive button">Create New Issue Link</a>
</p>

{{ if page:link_results }}

	<div id="pagination-top">
		{{ page:pagination }}
	</div>

	<table>
		<tr>
			<th>Name</th>
			<th>Inbound</th>
			<th>Outbound</th>
			<th>Actions</th>
		</tr>
		{{ page:links }}
			<tr>
				<td>
					<a href="{{ site:url url='administration/link/edit' }}/{{ id }}" title="Edit {{ name }}">{{ name }}</a>
				</td>
				<td>{{ inward }}</td>
				<td>{{ outward }}</td>
				<td>
					<div class="button-group">
						<a href="{{ site:url url='administration/link/edit' }}/{{ id }}" title="Edit {{ name }}" class="button">Edit</a>
						<a href="{{ site:url url='administration/link/delete' }}/{{ id }}" title="Delete {{ name }}" class="negative button">Delete</a>
					</div>
				</td>
			</tr>
		{{ /page:links }}
	</table>

	<div id="pagination-bottom">
		{{ page:pagination }}
	</div>

{{ else }}

	No results were found

{{ endif }}