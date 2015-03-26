<script type="text/template" data-grid="main" data-template="filters">

	<% _.each(filters, function(f) { %>

		<button class="btn btn-default btn-sm">

			<span><i class="fa fa-trash-o"></i></span>

			<% if (f.from !== undefined && f.to !== undefined) { %>

				<% if (/[0-9]{4}-[0-9]{2}-[0-9]{2}/g.test(f.from) && /[0-9]{4}-[0-9]{2}-[0-9]{2}/g.test(f.to)) { %>

					<%= f.label %> <em><%= moment(f.from).format('MMM DD, YYYY') %> - <%= moment(f.to).format('MMM DD, YYYY') %></em>

				<% } else { %>

					<%= f.label %> <em><%= f.from %> - <%= f.to %></em>

				<% } %>

			<% } else if (f.col_mask !== undefined && f.val_mask !== undefined) { %>

				<%= f.col_mask %> <em><%= f.val_mask %></em>

			<% } else { %>

				<% if (f.column === 'all') { %>

					<%= f.value %>

				<% } else { %>

					<%= f.value %> {{{ trans('common.in') }}} <em><%= f.column %></em>

				<% } %>

			<% } %>

		</button>

	<% }); %>

</script>
