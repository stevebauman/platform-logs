var Extension;

;(function(window, document, $, undefined)
{

	'use strict';

	Extension = Extension || {
		Index: {},
	};

	// Initialize functions
	Extension.Index.init = function()
	{
		Extension.Index.datePicker();
		Extension.Index.dataGrid();
		Extension.Index.listeners();
	};

	// Add Listeners
	Extension.Index.listeners = function()
	{
		Platform.Cache.$body
			.on('click', '[data-grid-row]', Extension.Index.checkRow)
			.on('click', '[data-grid-row] a', Extension.Index.titleClick)
			.on('click', '[data-grid-checkbox]', Extension.Index.checkboxes)
			.on('click', '#modal-confirm a.confirm', Extension.Index.bulkActions)
			.on('click', '[data-grid-calendar-preset]', Extension.Index.calendarPresets)
			.on('click', '[data-grid-bulk-action]:not([data-grid-bulk-action="delete"])', Extension.Index.bulkActions)
		;
	};

	// Handle Data Grid checkboxes
	Extension.Index.checkboxes = function(event)
	{
		event.stopPropagation();

		var type = $(this).attr('data-grid-checkbox');

		if (type === 'all')
		{
			$('[data-grid-checkbox]').not(this).prop('checked', this.checked);

			$('[data-grid-row]').not(this).toggleClass('active', this.checked);
		}

		$(this).parents('[data-grid-row]').toggleClass('active');

		Extension.Index.bulkStatus();
	};

	// Handle Data Grid row checking
	Extension.Index.checkRow = function()
	{
		$(this).toggleClass('active');

		var checkbox = $(this).find('[data-grid-checkbox]');

		checkbox.prop('checked', ! checkbox.prop('checked'));

		Extension.Index.bulkStatus();
	};

	Extension.Index.bulkStatus = function()
	{
		var rows = $('[data-grid-checkbox]').not('[data-grid-checkbox="all"]').length;

		var checked = $('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]').length;

		$('[data-grid-bulk-action]').closest('li').toggleClass('disabled', ! checked);

		if (checked > 0)
		{
			$('[data-grid-bulk-action="delete"]').attr('data-modal', true);
		}
		else
		{
			$('[data-grid-bulk-action="delete"]').removeAttr('data-modal');
		}

		$('[data-grid-checkbox="all"]')
			.prop('disabled', rows < 1)
			.prop('checked', rows < 1 ? false : rows === checked)
		;
	};

	// Handle Data Grid bulk actions
	Extension.Index.bulkActions = function(event)
	{
		event.preventDefault();

		var url = window.location.origin + window.location.pathname;

		var action = $(this).data('grid-bulk-action') ? $(this).data('grid-bulk-action') : 'delete';

		var rows = $.map($('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]'), function(event)
		{
			return +event.value;
		});

		if (rows.length > 0)
		{
			$.ajax({
				type: 'POST',
				url: url,
				data: {
					action : action,
					rows   : rows
				},
				success: function(response)
				{
					Extension.Index.Grid.refresh();
				}
			});
		}
	};

	// Handle Data Grid calendar
	Extension.Index.calendarPresets = function(event)
	{
		event.preventDefault();

		var start, end;

		switch ($(this).data('grid-calendar-preset'))
		{
			case 'day':
				start = end = moment().subtract(1, 'day').startOf('day').format('MM/DD/YYYY');
			break;

			case 'week':
				start = moment().startOf('week').format('MM/DD/YYYY');
				end   = moment().endOf('week').format('MM/DD/YYYY');
			break;

			case 'month':
				start = moment().startOf('month').format('MM/DD/YYYY');
				end   = moment().endOf('month').format('MM/DD/YYYY');
			break;

			default:
		}

		$('input[name="daterangepicker_start"]').val(start);

		$('input[name="daterangepicker_end"]').val(end);

		$('.range_inputs .applyBtn').trigger('click');
	};

	// Ignore row selection on title click
	Extension.Index.titleClick = function(event)
	{
		event.stopPropagation();
	};

	// Date range picker initialization
	Extension.Index.datePicker = function()
	{
		var startDate, endDate, config, filter;

		var filters = _.compact(
			String(window.location.hash.slice(3)).split('/').splice(2)
		);

		config = {
			opens: 'left'
		};

		_.each(filters, function(route)
		{
			filter = route.split(':');

			if (filter[0] === 'created_at' && filter[1] !== undefined && filter[2] !== undefined)
			{
				startDate = moment(filter[1]);

				endDate = moment(filter[2]);
			}
		});

		if (startDate && endDate)
		{
			$('[data-grid-calendar]').val(
				startDate.format('MM/DD/YYYY') + ' - ' + endDate.format('MM/DD/YYYY')
				);

			config = {
				startDate: startDate,
				endDate: endDate,
				opens: 'left'
			};
		}

		Platform.Cache.$body.on('click', '.range_inputs .applyBtn', function()
		{
			$('input[name="daterangepicker_start"]').trigger('change');

			$('[data-grid-calendar]').val(
				moment($('input[name="daterangepicker_start"]').val()).format('MM/DD/YYYY') + ' - ' + moment($('input[name="daterangepicker_end"]').val()).format('MM/DD/YYYY')
			);
		});

		Extension.Index.datePicker = $('[data-grid-calendar]').daterangepicker(config, function(start, end, label)
		{
			$('input[name="daterangepicker_start"]').trigger('change');
		});

		$('.daterangepicker_start_input').attr('data-grid', 'logs');

		$('.daterangepicker_end_input').attr('data-grid', 'logs');

		$('input[name="daterangepicker_start"]')
			.attr('data-format', 'MM/DD/YYYY')
			.attr('data-range-start', '')
			.attr('data-range-filter', 'created_at')
		;

		$('input[name="daterangepicker_end"]')
			.attr('data-format', 'MM/DD/YYYY')
			.attr('data-range-end', '')
			.attr('data-range-filter', 'created_at')
		;
	};

	// Data Grid initialization
	Extension.Index.dataGrid = function()
	{
		var config = {
			scroll: '#data-grid',
			events: {
				removing: function(dg)
				{
					_.each(dg.applied_filters, function(filter)
					{
						if (filter.column === 'created_at' && filter.from !== undefined && filter.to !== undefined)
						{
							$('[data-grid-calendar]').val('');
						}
					});
				}
			},
			callback: function()
			{
				$('[data-grid-checkbox-all]').prop('checked', false);

				$('[data-action]').prop('disabled', true);

				Extension.Index.bulkStatus();
			}
		};

		Extension.Index.Grid = $.datagrid('logs', '#data-grid', '#data-grid_pagination', '#data-grid_applied', config);
	};

	// Job done, lets run.
	Extension.Index.init();

})(window, document, jQuery);
