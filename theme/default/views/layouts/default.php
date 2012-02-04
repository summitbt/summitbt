{{ theme:partial file='header' }}

	<section id="content">
		<div class="content">
			{{ if ! {is type="dashboard"} AND page:breadcrumbs != "" }}
				<div id="breadcrumbs">
					{{ page:breadcrumbs }}
				</div>
			{{ endif }}

			{{ page:messages }}

			{{ page:content }}
		</div>
	</section>

{{ theme:partial file='footer' }}