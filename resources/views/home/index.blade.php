@extends('layout.master')

@section('title', "Home")

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <input type="button" class="btn btn-success" onclick="$.test()" value="Test" />
            </div>

        </div>

    </div>

@endsection

@section('css')

    <style>

    </style>

@endsection

@section('js_init')

    <script>

        $( document ).ready(function() {
            $.test = function () {

                $.ajax({
                    url: "https://testnet.binance.vision/api/v3/time",
                    type: "GET",
                    headers: {
                        "Access-Control-Allow-Origin":"*",
                        "X-MBX-APIKEY":"lwUdtCM7xq6MzuMjZE3a6LXaoMcJ1iS78K6cIweRfDA0ySaAkkJMNq6Q8iZfpMAs",
                    },
                    dataType: "JSON",
                    //data: {id : id},
                }).done(function(response) {
                    console.log(response);
                }).fail(function(jqXHR, textStatus) {
                    toastr.error( "Request failed: " + textStatus );
                });

            };
        });

    </script>

@endsection
