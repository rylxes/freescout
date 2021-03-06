@extends('layouts.app')

@section('title', __('System'))

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    <div class="sidebar-title">
        {{ __('System') }}
    </div>
    <ul class="sidebar-menu">
        <li><a href="#app"><i class="glyphicon glyphicon-menu-right"></i> {{ __('App') }}</a></li>
        <li><a href="#server"><i class="glyphicon glyphicon-menu-right"></i> {{ __('Server') }}</a></li>
        <li><a href="#php"><i class="glyphicon glyphicon-menu-right"></i> PHP</a></li>
        <li><a href="#tasks"><i class="glyphicon glyphicon-menu-right"></i> {{ __('Cron Commands') }}</a></li>
        <li><a href="#jobs"><i class="glyphicon glyphicon-menu-right"></i> {{ __('Background Jobs') }}</a></li>
    </ul>
@endsection

@section('content')
<div class="container">

    <h3 id="app">{{ __('App') }}</h3>

    <table class="table table-dark-header table-bordered table-responsive">
        <tbody>
            <tr>
                <th>{{ __('Date & Time') }}</th>
                <td class="table-main-col">{{ App\User::dateFormat(new Illuminate\Support\Carbon()) }}</td>
            </tr>
            <tr>
                <th>{{ __('Timezone') }}</th>
                <td class="table-main-col">{{ \Config::get('app.timezone') }} (GMT{{ date('O') }})</td>
            </tr>
        </tbody>
    </table>

    <h3 id="server" class="margin-top-40">{{ __('Server') }}</h3>

    <table class="table table-dark-header table-bordered table-responsive">
        <tbody>
            <tr>
                <th>{{ __('Name') }}</th>
                <td class="table-main-col">@if (!empty($_SERVER['SERVER_SOFTWARE'])){{ $_SERVER['SERVER_SOFTWARE'] }}@else ? @endif</td>
            </tr>
        </tbody>
    </table>

    <h3 id="php" class="margin-top-40">PHP</h3>
    <table class="table table-dark-header table-bordered table-responsive">
        <tbody>
            <tr>
                <th>{{ __('Version') }}</th>
                <td class="table-main-col">PHP {{ phpversion() }}</td>
            </tr>
        </tbody>
    </table>
    <p>
        {{ __('Required PHP extensions:') }}
    </p>
    <table class="table table-dark-header table-bordered table-responsive">
        <tbody>
            @foreach ($php_extensions as $extension_name => $extension_status)
                <tr>
                    <th>{{ $extension_name }}</th>
                    <td class="table-main-col">
                        @if ($extension_status)
                            <strong class="text-success">OK</strong>
                        @else
                            <strong class="text-danger">{{ __('Not found') }}</strong>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 id="cron" class="margin-top-40">Cron Commands</h3>
    <p>
        {!! __('Commands are launched by <i>schedule:run</i> cron command. Make sure that you have the following line in your crontab:') !!}<br/>
        <code>* * * * * php /var/www/html/artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
    </p>
    <table class="table table-dark-header table-bordered table-responsive">
        <tbody>
            @foreach ($commands as $command)
                <tr>
                    <th>{{ $command['name'] }}</th>
                    <td class="table-main-col">
                        <strong class="text-@if ($command['status'] == "success"){{ 'success' }}@else{{ 'danger' }}@endif">{{ $command['status_text'] }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 id="jobs" class="margin-top-40">{{ __('Background Jobs') }}</h3>
    <table class="table table-dark-header table-bordered table-responsive">
        <tbody>
            <tr>
                <th>{{ __('Queued Jobs') }}</th>
                <td class="table-main-col">
                    <p>
                        {{ __('Total') }}: <strong>{{ count($queued_jobs)}}</strong>
                    </p>
                    @foreach ($queued_jobs as $job)
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th colspan="2">{{ $loop->index+1 }}. {{ json_decode($job->payload, true)['displayName'] }}</th>
                                </tr>
                                <tr>
                                    <td>{{ __('Queue') }}</td>
                                    <td>{{ $job->queue }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Attempts') }}</td>
                                    <td>
                                        @if ($job->attempts > 0)<strong class="text-danger">@endif
                                            {{ $job->attempts }}
                                        @if ($job->attempts > 0)</strong>@endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('Created At') }}</td>
                                    <td>{{  App\User::dateFormat($job->created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>{{ __('Failed Jobs') }}</th>
                <td>
                    <p>
                        {{ __('Total') }}:  <strong @if (count($failed_jobs) > 0) class="text-danger" @endif >{{ count($failed_jobs) }}</strong>
                    </p>
                    @foreach ($failed_jobs as $job)
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th colspan="2">{{ $loop->index+1 }}. {{ json_decode($job->payload, true)['displayName'] }}</th>
                                </tr>
                                <tr>
                                    <td>{{ __('Queue') }}</td>
                                    <td>{{ $job->queue }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Failed At') }}</td>
                                    <td>{{  App\User::dateFormat($job->failed_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>

</div>
@endsection