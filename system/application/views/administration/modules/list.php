{{ if page:modules }}

	<dl>
		{{ page:modules }}
			<dt>{{ name }} {{ version }}</dt>
			<dd>
				<p>
					{{ description }}
				</p>
				<p>
					{{ if author_uri }}
						<a href="{{ author_uri }}" target="_blank" title="{{ author }}">{{ author }}</a>
					{{ else }}
						{{ author }}
					{{ endif }}
				</p>
			</dd>
		{{ /page:modules }}
	</dl>

{{ else }}

	No modules available

{{ endif }}