@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
               <table class="table table-striped main-data-table">
                    <thead>
                        <tr>
                            <th class="text-center main-table-cell" >Index</th>
                            <th class="text-center main-table-cell toggle hidden" >Key</th>
                            <th class="text-center main-table-cell" >Original</th>
                            <th class="text-center main-table-cell" >Translation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data[$language] as $key => $element)
                        @if ($element['visible'])
                        <tr >
                            <td class="main-table-cell" >
                                {{$loop->index+1}}
                            </td>
                            <td class="main-table-cell toggle hidden" >
                                {{$key}}
                            </td>
                            <td class="main-table-cell">
                                <div class="form-control">{{$data['English'][$key]['translation']}}</div>
                            </td>
                            <td class="main-table-cell">
                                <div class="form-control translation" id="{{$key}}" contenteditable="true">{{$element['translation']}}</div>
                            </td>
                        </tr>
                        @endif
                        @empty
                           No data available
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<button class="btn btn-warning btn-key btn-show-keys ">Show keys</button>
<button class="btn btn-success btn-key btn-hide-keys hidden">Hide keys</button>
@endsection
