	<footer id="footer">
		<div class="footer">

			{{ if user:logged_in }}
				{{ site:menu:secondary }}
			{{ endif }}

			<div class="footnote">
				Proudly powered by <a href="{{ site:url url='about' }}">Summit</a>
			</div>

		</div>
	</footer>

</div>

{{ page:foot:scripts }}

<!-- {{ page:rendered }} -->

</body>
</html>