<script type="text/template" data-grid="main" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-grid-row>
			<td><input data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>"></td>
			<td><%= r.level %></td>
			<td><a href="<%= r.show_url %>"><%= r.date %></a></td>
			<td><%= r.header %></td>
		</tr>

	<% }); %>

</script>