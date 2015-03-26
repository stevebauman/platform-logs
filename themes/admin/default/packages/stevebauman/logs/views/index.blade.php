@extends('layouts/default')

{{-- Page title --}}
@section('title')
    @parent
    Logs
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/css/daterangepicker-bs3.css', 'style') }}

{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('index', 'stevebauman/logs::js/index.js', 'platform') }}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/js/daterangepicker.js', 'jquery') }}

{{-- Page --}}
@section('page')

    {{-- Grid --}}
    <section class="panel panel-default panel-grid">

        {{-- Grid: Header --}}
        <header class="panel-heading">

            <nav class="navbar navbar-default navbar-actions">

                <div class="container-fluid">

                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <span class="navbar-brand">Logs</span>
                    </div>

                    {{-- Grid: Actions --}}
                    <div class="collapse navbar-collapse" id="actions">

                        <ul class="nav navbar-nav navbar-left">

                            <li class="danger disabled">
                                <a data-grid-bulk-action="delete" data-toggle="tooltip" data-target="modal-confirm" data-original-title="{{{ trans('action.bulk.delete') }}}">
                                    <i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.delete') }}}</span>
                                </a>
                            </li>

                            <li class="disabled">
                                <a data-grid-bulk-action="read" data-toggle="tooltip" data-original-title="Mark Selected As Read">
                                    <i class="fa fa-eye"></i> <span class="visible-xs-inline">Mark Selected As Read</span>
                                </a>
                            </li>

                            <li class="dropdown disabled">
                                <a href="#" data-grid-exporter class="dropdown-toggle tip" data-toggle="dropdown" role="button" aria-expanded="false" data-original-title="{{{ trans('action.export') }}}">
                                    <i class="fa fa-download"></i> <span class="visible-xs-inline">{{{ trans('action.export') }}}</span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-download="pdf"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                                    <li><a data-download="csv"><i class="fa fa-file-excel-o"></i> CSV</a></li>
                                    <li><a data-download="json"><i class="fa fa-file-code-o"></i> JSON</a></li>
                                </ul>
                            </li>

                        </ul>

                        {{-- Grid: Filters --}}
                        <form class="navbar-form navbar-right" method="post" accept-charset="utf-8" data-search data-grid="main" role="form">

                            <div class="input-group">

							<span class="input-group-btn">

								<button class="btn btn-default" type="button" disabled>
                                    {{{ trans('common.filters') }}}
                                </button>

								<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

								<ul class="dropdown-menu" role="menu">

                                    <li>
                                        <a data-grid="main" data-filter="include_read:1" data-label="include_read::Show Read">
                                            <i class="fa fa-eye"></i> Show Read
                                        </a>
                                    </li>

                                    <li class="divider"></li>

                                    <li>
                                        <a data-grid-calendar-preset="day">
                                            <i class="fa fa-calendar"></i> {{{ trans('date.day') }}}
                                        </a>
                                    </li>

                                    <li>
                                        <a data-grid-calendar-preset="week">
                                            <i class="fa fa-calendar"></i> {{{ trans('date.week') }}}
                                        </a>
                                    </li>

                                    <li>
                                        <a data-grid-calendar-preset="month">
                                            <i class="fa fa-calendar"></i> {{{ trans('date.month') }}}
                                        </a>
                                    </li>

                                </ul>

								<button class="btn btn-default hidden-xs" type="button" data-grid-calendar data-range-filter="date">
                                    <i class="fa fa-calendar"></i>
                                </button>

							</span>

                                <input class="form-control" name="filter" type="text" placeholder="{{{ trans('common.search') }}}">

							<span class="input-group-btn">

								<button class="btn btn-default" type="submit">
                                    <span class="fa fa-search"></span>
                                </button>

								<button class="btn btn-default" data-grid="main" data-reset>
                                    <i class="fa fa-refresh fa-sm"></i>
                                </button>

							</span>

                            </div>

                        </form>

                    </div>

                </div>

            </nav>

        </header>

        <div class="panel-body">

            {{-- Grid: Applied Filters --}}
            <div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">

                <div id="data-grid_applied" class="btn-group" data-grid="main"></div>

            </div>

        </div>

        {{-- Grid: Table --}}
        <div class="table-responsive">

            <table id="data-grid" class="table table-hover" data-source="{{ route('admin.logs.grid') }}" data-grid="main">
                <thead>
                    <tr>
                        <th><input disabled data-grid-checkbox="all" type="checkbox"></th>
                        <th class="sortable" data-sort="level">Level</th>
                        <th class="sortable" data-sort="date">Date</th>
                        <th class="sortable hidden-xs" data-sort="header">Header</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>

        <footer class="panel-footer clearfix">

            {{-- Grid: Pagination --}}
            <div id="data-grid_pagination" data-grid="main"></div>

        </footer>

        {{-- Grid: templates --}}
        @include('stevebauman/logs::grid/index/results')
        @include('stevebauman/logs::grid/index/pagination')
        @include('stevebauman/logs::grid/index/filters')
        @include('stevebauman/logs::grid/index/no_results')

    </section>

@stop
