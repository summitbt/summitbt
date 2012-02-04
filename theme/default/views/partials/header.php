{{ theme:partial file='head' }}
<body class="{{ page:bodyclass }}">

<div id="wrapper">

	<header id="header">
		<div class="header">

			<div class="site">
				<span class="sitelogo">
					{{ if site:logo }}
						<a href="{{ site:url }}" title="{{ site:name }}"><img src="{{ site:logo }}" alt="{{ site:name }}" /></a>
					{{ else }}
						<a href="{{ site:url }}" title="{{ site:name }}"><img src="{{ site:logo_summit }}" alt="{{ site:name }}" /></a>
					{{ endif }}
				</span>

				<h1 class="sitename">
					<a href="{{ site:url }}" title="{{ site:name }}">{{ site:name }}</a>
				</h1>
			</div>

			<nav id="navigation">
				{{ if user:logged_in }}
					{{ site:menu:primary }}
				{{ endif }}
			</nav>

			<ul class="meta">
				<li><span id="loading"></span></li>

				{{ if {can action="issues_create"} }}
					<li><a href="{{ site:url url='issue/new' }}">Create Issue</a></li>
				{{ endif }}

				{{ if user:logged_in }}
					<li>{{ site:menu:account }}</li>
				{{ endif }}

				<li>
					<a href="{{ site:url url='search' }}">Search issues</a>
					<div class="quick-search">
						{{ page:form:quick_search }}
					</div>
				</li>
			</ul>

		</div>
	</header>