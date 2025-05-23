@extends('layouts.app')

@section('title', 'BANK IDP')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>LIST IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Behavior IDP</a></div>
                    <div class="breadcrumb-item"><a href="#">BANK IDP</a></div>
                    <div class="breadcrumb-item">Detail IDP</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">BANK IDP</h2>
                <p class="section-lead">Bank IDP berisi kumpulan IDP untuk karyawan.</p>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                        <article class="article">
                            <div class="article-header">
                                <div class="article-image"
                                    data-background="{{ asset('img/news/img08.jpg') }}">
                                </div>
                                <div class="article-title">
                                    <h2><a href="#">Excepteur sint occaecat cupidatat non proident</a></h2>
                                </div>
                            </div>
                            <div class="article-details">
                                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse
                                    cillum dolore eu fugiat nulla pariatur. </p>
                                <div class="article-cta">
                                    <a href="#"
                                        class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                        <article class="article">
                            <div class="article-header">
                                <div class="article-image"
                                    data-background="{{ asset('img/news/img04.jpg') }}">
                                </div>
                                <div class="article-title">
                                    <h2><a href="#">Excepteur sint occaecat cupidatat non proident</a></h2>
                                </div>
                            </div>
                            <div class="article-details">
                                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse
                                    cillum dolore eu fugiat nulla pariatur. </p>
                                <div class="article-cta">
                                    <a href="#"
                                        class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                        <article class="article">
                            <div class="article-header">
                                <div class="article-image"
                                    data-background="{{ asset('img/news/img09.jpg') }}">
                                </div>
                                <div class="article-title">
                                    <h2><a href="#">Excepteur sint occaecat cupidatat non proident</a></h2>
                                </div>
                            </div>
                            <div class="article-details">
                                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse
                                    cillum dolore eu fugiat nulla pariatur. </p>
                                <div class="article-cta">
                                    <a href="#"
                                        class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                        <article class="article">
                            <div class="article-header">
                                <div class="article-image"
                                    data-background="{{ asset('img/news/img12.jpg') }}">
                                </div>
                                <div class="article-title">
                                    <h2><a href="#">Excepteur sint occaecat cupidatat non proident</a></h2>
                                </div>
                            </div>
                            <div class="article-details">
                                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse
                                    cillum dolore eu fugiat nulla pariatur. </p>
                                <div class="article-cta">
                                    <a href="#"
                                        class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
