@extends('layout.master')

@section('title', "Home")

@section('content')

    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr>
                        <td>Günlük Toplam Satın Alma Miktarı</td>
                        <td>{{number_format($totalDailyBuy, 8)}}</td>
                    </tr>

                    <tr>
                        <td>Günlük Toplam Satış Miktarı</td>
                        <td>{{number_format($totalDailySell, 8)}}</td>
                    </tr>

                    <tr>
                        <td>Kâr</td>
                        <td>{{number_format($totalDailySell-$totalDailyBuy, 8)}}</td>
                    </tr>
                </table>


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

        });

    </script>

@endsection
