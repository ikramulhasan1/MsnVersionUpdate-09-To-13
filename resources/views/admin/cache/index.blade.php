@extends('admin.layouts.master')
@section('title', $title)
@section('content')

<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    @include('admin.inc.breadcrumb')
    <!-- end page title -->

    <div class="row">
        <div class="col-12">

            {{-- Status + master switch --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4 class="header-title mb-1">Page Caching Status</h4>
                            @if($enabled)
                                <span class="badge badge-success" style="font-size: 14px;">চালু আছে (ON)</span>
                            @else
                                <span class="badge badge-danger" style="font-size: 14px;">বন্ধ আছে (OFF)</span>
                            @endif
                        </div>
                        <div class="mt-2 mt-md-0">
                            @if($enabled)
                                <form action="{{ route('admin.cache.disable') }}" method="POST" class="d-inline" onsubmit="return confirm('Page caching বন্ধ করলে প্রতিটা রিকোয়েস্ট সরাসরি Laravel থেকে যাবে (ধীর হবে)। আপনি কি নিশ্চিত?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">Cache বন্ধ করুন</button>
                                </form>
                            @else
                                <form action="{{ route('admin.cache.enable') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Cache চালু করুন</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        চালু থাকলে ভিজিটররা pre-generated static HTML পাবেন (super fast, ৫০-১০০ms এর কাছাকাছি)।
                        বন্ধ করলে প্রতিটা রিকোয়েস্ট Laravel দিয়ে normal ভাবে প্রসেস হবে — শুধু debugging/develop করার সময় বন্ধ রাখুন।
                    </p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h2 class="mb-0">{{ $stats['count'] }}</h2>
                            <p class="text-muted mb-0">Cached পেজের সংখ্যা</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h2 class="mb-0">{{ $stats['size_human'] }}</h2>
                            <p class="text-muted mb-0">মোট Cache সাইজ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h2 class="mb-0">
                                {{ isset($stats['files'][0]) ? $stats['files'][0]['modified']->diffForHumans() : '—' }}
                            </h2>
                            <p class="text-muted mb-0">সর্বশেষ cache আপডেট</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Cache Control</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <form action="{{ route('admin.cache.clear-all') }}" method="POST" onsubmit="return confirm('পুরো ওয়েবসাইটের cache মুছে ফেলা হবে। আপনি কি নিশ্চিত?');">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash-alt"></i> পুরো সাইট Clear করুন (Hard Flush)
                                </button>
                            </form>
                        </div>

                        <div class="col-md-4 mb-3">
                            <form action="{{ route('admin.cache.clear-home') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-home"></i> শুধু হোমপেজ Clear করুন
                                </button>
                            </form>
                        </div>

                        <div class="col-md-4 mb-3">
                            <form action="{{ route('admin.cache.clear-framework') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-sync"></i> Laravel Framework Cache Clear করুন
                                </button>
                            </form>
                            <small class="text-muted">(view/config/route cache — deploy করার পর ব্যবহার করুন)</small>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('admin.cache.clear-path') }}" method="POST" class="form-inline">
                        @csrf
                        <label class="mr-2 mb-0">নির্দিষ্ট পেজের cache Clear করুন:</label>
                        <input type="text" name="path" class="form-control mr-2" placeholder="/about অথবা /service/web-design" style="min-width: 300px;" required>
                        <button type="submit" class="btn btn-secondary">Clear করুন</button>
                    </form>
                </div>
            </div>

            {{-- Cached files list --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Cached পেজের তালিকা</h4>
                </div>
                <div class="card-body">
                    @if(count($stats['files']) === 0)
                        <p class="text-muted mb-0">এখনো কোনো পেজ cache হয়নি। ভিজিটররা পেজ ভিজিট করা শুরু করলে এখানে দেখা যাবে।</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-dark">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>পেজ (path)</th>
                                        <th>সাইজ</th>
                                        <th>শেষ আপডেট</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['files'] as $key => $file)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>/{{ Str::replaceLast('/index.html', '', Str::replaceLast('.html', '', $file['path'])) }}</td>
                                        <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                        <td>{{ $file['modified']->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
