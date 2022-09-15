@extends('layouts.admin.master')

@section('content')
    <div class="row">


        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Sales</span>
                    <span class="info-box-number">{{ $totalSales }}</span>
                </div>

            </div>

        </div>


        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Todays Sale</span>
                    <span class="info-box-number">{{ $todaysSales }}</span>
                </div>

            </div>

        </div>


        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-hamburger"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Top Sellling Item</span>
                    <span class="info-box-number">
                            {{ $topSoldItem }}
                    </span>
                </div>
            </div>

        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Customer</span>
                    <span class="info-box-number">{{ $totalCustomers }}</span>
                </div>

            </div>

        </div>




    </div>
    <div class="row">
        <section class="col-lg-6">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Sales Last 30 days
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="sales1" width="200" height="100"></canvas>
                </div>

        </section>
        <section class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Selling Items</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        <table class="table">
                            <thead>
                                <th>Item Image</th>
                                <th>Item Name</th>
                                <th>Item Price</th>
                            </thead>
                            <tbody>
                                @foreach ($topSoldItems as $cartItem)
                                    <tr>
                                        <td> <img src="{{ $cartItem->item->image() }}" alt="Product Image"
                                                class="img-size-50">
                                        </td>
                                        <td>{{ $cartItem->item->name }}</td>
                                        <td>{{ $cartItem->item->price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <tr>

                        </tr>



                    </ul>
                </div>


                <div class="card-footer text-center">
                    <a href="{{ route('admin.items.index') }}" class="uppercase">View All Items</a>
                </div>

            </div>
        </section>

    </div>
@endsection
@section('js')
    <script>
        $.ajax({
            type: "POST",
            url: '{{ route('admin.dashboard.getSalesData') }}',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(dataResult) {
                const ctx = document.getElementById('sales1').getContext('2d');
                console.log(Object.values(dataResult.salesData));

                var sales1 = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(dataResult.salesData),
                        datasets: [{
                            label: 'Total Sales',
                            data: Object.values(dataResult.salesData),
                            borderColor: 'rgba(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 1

                        }]
                    },
                    options: {
                        responsive: true,

                        scales: {

                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
