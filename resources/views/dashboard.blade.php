@extends('layouts.app')

@section('content')
    <div class="page-heading">
        <h3>Profile Statistics</h3>
    </div>
    <div class="page-content">
        <!-- Recruitment Status Alert -->
        @if(Auth::user()->recruitment_status)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-light-primary">
                        <div class="card-body">
                            <h4 class="card-title text-primary">Status Pendaftaran Anda</h4>
                            <hr>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5>Status: <span class="badge {{ str_contains(Auth::user()->recruitment_status, 'Tidak') ? 'bg-danger' : (str_contains(Auth::user()->recruitment_status, 'Masih') ? 'bg-warning' : 'bg-success') }} fs-5">{{ Auth::user()->recruitment_status }}</span></h5>
                                    <p class="mb-0 mt-2">
                                        Jarak ke Sekolah: <strong>{{ number_format(Auth::user()->distance_to_school, 2) }} km</strong>
                                    </p>
                                </div>
                                <i class="bi bi-info-circle-fill text-primary" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <section class="row">
            <div class="col-12 col-lg-12">
                <div class="row">
                    <!-- User Statistics -->
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon purple mb-2">
                                            <i class="bi bi-people-fill text-white fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total User</h6>
                                        <h6 class="font-extrabold mb-0">{{ $userCount ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- News Statistics -->
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon blue mb-2">
                                            <i class="bi bi-newspaper text-white fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Berita</h6>
                                        <h6 class="font-extrabold mb-0">{{ $newsCount ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Latest News Section -->
                 <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Berita & Pengumuman Terbaru</h4>
                            </div>
                            <div class="card-body">
                                @if(isset($latestNews) && $latestNews->count() > 0)
                                    <div class="list-group">
                                        @foreach($latestNews as $news)
                                            <a href="#" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">{{ $news->title }}</h5>
                                                    <small class="text-muted">{{ $news->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-1">{!! Str::limit(strip_tags($news->content), 150) !!}</p>
                                                <small class="text-primary">Baca selengkapnya...</small>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center p-4">
                                        <p class="text-muted">Belum ada berita terbaru.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
