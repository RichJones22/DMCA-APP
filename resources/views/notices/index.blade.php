@extends('app')

@section('content')
    <h1 class="page-heading">Your Notices</h1>

    <table class="table table-striped table-bordered">
        <head>
            <th>This Content:</th>
            <th>Accessible Here:</th>
            <th>Is Infringing Upon My Work Here:</th>
            <th>Notice Sent:</th>
            <th>Content Removed:</th>
        </head>

        <body>
            @foreach ($notices as $notice)
                <tr>
                    <td>{{ $notice->infringing_title }}</td>
                    <td>{!! link_to($notice->infringing_link) !!}</td>
                    <td>{!! link_to($notice->original_link) !!}</td>
                    <td>{{ $notice->created_at->diffForHumans() }}</td>
                    <td>
                        {!! Form::open(['data-remote', 'method' => 'PATCH', 'url' => 'notices/' . $notice->id]) !!}
                            <div class="form-group">
                                {!! Form::checkbox('content_removed', $notice->content_removed, $notice->content_removed, ['data-click-submits-form']) !!}
                            </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </body>
    </table>

    @unless(count($notices))
        <p class="text-center">You haven't sent any DMCA notices yet!</p>
    @endunless
@endsection