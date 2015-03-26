/**
 * Part of the Platform Content extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Content extension
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

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
        Extension.Index
            .listeners()
            .datePicker()
            .dataGrid()
        ;
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

        return this;
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

            if (filter[0] === 'date' && filter[1] !== undefined && filter[2] !== undefined)
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
                opens: 'left',
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

        $('.daterangepicker_start_input').attr('data-grid', 'main');

        $('.daterangepicker_end_input').attr('data-grid', 'main');

        $('input[name="daterangepicker_start"]')
            .attr('data-format', 'MM/DD/YYYY')
            .attr('data-range-start', '')
            .attr('data-range-filter', 'date')
        ;

        $('input[name="daterangepicker_end"]')
            .attr('data-format', 'MM/DD/YYYY')
            .attr('data-range-end', '')
            .attr('data-range-filter', 'date')
        ;

        return this;
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
                        if (filter.column === 'date' && filter.from !== undefined && filter.to !== undefined)
                        {
                            $('[data-grid-calendar]').val('');
                        }
                    });
                }
            },
            callback: function(data)
            {
                $('[data-grid-checkbox-all]').prop('checked', false);

                $('[data-action]').prop('disabled', true);

                Extension.Index
                    .bulkStatus()
                    .exporterStatus(data)
                ;
            }
        };

        Extension.Index.Grid = $.datagrid('main', '#data-grid', '#data-grid_pagination', '#data-grid_applied', config);

        return this;
    };

    // Handle Data Grid checkboxes
    Extension.Index.checkboxes = function(event)
    {
        event.stopPropagation();

        var type = $(this).attr('data-grid-checkbox');

        if (type === 'all')
        {
            $('[data-grid-checkbox]').not(this).not('[data-grid-checkbox][disabled]').prop('checked', this.checked);

            $('[data-grid-row]').not('[data-grid-row][disabled]').not(this).toggleClass('active', this.checked);
        }

        $(this).parents('[data-grid-row]').not('[data-grid-row][disabled]').toggleClass('active');

        Extension.Index.bulkStatus();
    };

    // Handle Data Grid row checking
    Extension.Index.checkRow = function()
    {
        if ($(this).find('[data-grid-checkbox]').prop('disabled')) return false;

        $(this).toggleClass('active');

        var checkbox = $(this).find('[data-grid-checkbox]');

        checkbox.prop('checked', ! checkbox.prop('checked'));

        Extension.Index.bulkStatus();
    };

    Extension.Index.bulkStatus = function()
    {
        var rows = $('[data-grid-checkbox]').not('[data-grid-checkbox="all"]').not('[data-grid-checkbox][disabled]').length;

        var checked = $('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]').not('[data-grid-checkbox][disabled]').length;

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

        return this;
    };

    Extension.Index.exporterStatus = function(grid)
    {
        $('[data-grid-exporter]').closest('li').toggleClass('disabled', grid.pagination.filtered == 0);

        return this;
    };

    // Handle Data Grid bulk actions
    Extension.Index.bulkActions = function(event)
    {
        event.preventDefault();

        var url = window.location.origin + window.location.pathname;

        var action = $(this).data('grid-bulk-action') ? $(this).data('grid-bulk-action') : 'delete';

        var rows = $.map($('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]'), function(event)
        {
            return event.value;
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

    // Job done, lets run.
    Extension.Index.init();

})(window, document, jQuery);
