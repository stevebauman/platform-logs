@extends('layouts/default')

{{-- Page title --}}
@section('title')
    @parent
    Logs
@stop

{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}

{{-- Page --}}
@section('page')
    <section class="panel panel-default">

        <form id="content-form" action="{{ request()->fullUrl() }}" role="form" method="post" accept-char="UTF-8">

        <input type="hidden" name="_token" value="{{ csrf_token() }}">

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

                        <a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.logs.index') }}" data-toggle="tooltip" data-original-title="Go Back">
                            <i class="fa fa-reply"></i> <span class="visible-xs-inline">Go Back</span>
                        </a>

                        <span class="navbar-brand">Viewing Log Entry <small>{{ ucfirst($entry->level) }} on {{ $entry->date }}</small></span>
                    </div>

                    {{-- Form: Actions --}}
                    <div class="collapse navbar-collapse" id="actions">
                        <ul class="nav navbar-nav navbar-right">

                            <li>
                                <a href="{{ route('admin.logs.destroy', $entry->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
                                    <i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.logs.read', array($entry->id)) }}" class="tip" data-toggle="tooltip" data-original-title="Mark Read">
                                    <i class="fa fa-eye"></i> <span class="visible-xs-inline">Mark Read</span>
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>

            </nav>
        </header>

        <div class="panel-body">

            <dl>
                <dt>Level:</dt>
                <dd>{{ ucfirst($entry->level) }}</dd>

                <p></p>

                <dt>Header:</dt>
                <dd>
                    <pre>{{ $entry->header }}</pre>
                </dd>

                <p></p>

                <dt>Stack:</dt>
                <dd>
                    @if(strlen($entry->stack) > 1)
                        <pre>{{ $entry->stack }}</pre>
                    @else
                        <em>No stack trace to display</em>
                    @endif
                </dd>

                <p></p>

            </dl>

        </div>

        </form>

    </section>
@stop