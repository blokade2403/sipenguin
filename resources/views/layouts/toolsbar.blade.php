 <!--begin::Toolbar-->
@php
    $toolbarSegments = request()->segments();
    $toolbarSectionTitle = trim(strip_tags($__env->yieldContent('title')));
    $toolbarCurrent = $title ?? ($toolbarSectionTitle !== ''
        ? $toolbarSectionTitle
        : ($toolbarSegments
            ? \Illuminate\Support\Str::title(str_replace(['-', '_'], ' ', last($toolbarSegments)))
            : 'Dashboard'));
@endphp
                    <div class="toolbar" id="kt_toolbar">
                        <!--begin::Container-->
                        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                            <!--begin::Page title-->
                            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                <!--begin::Title-->
                                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $toolbarCurrent }}</h1>
                                <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                                <nav aria-label="Breadcrumb" class="d-flex align-items-center">
                                    <ol class="breadcrumb breadcrumb-separatorless fw-semibold fs-8 my-0">
                                        <li class="breadcrumb-item"><a href="{{ \Illuminate\Support\Facades\Route::has('dashboard') ? route('dashboard') : url('/') }}" class="text-muted text-hover-primary">Dashboard</a></li>
                                        @if ($toolbarCurrent !== 'Dashboard')
                                            <li class="breadcrumb-item mx-2"><span class="bullet bg-gray-400 w-4px h-2px"></span></li>
                                            <li class="breadcrumb-item text-primary" aria-current="page">{{ $toolbarCurrent }}</li>
                                        @endif
                                    </ol>
                                </nav>
                                <!--end::Title-->
                            </div>
                            <!--end::Page title-->
                            <!--begin::Actions-->
                            <div class="d-flex align-items-center py-1">
                                <!--begin::Wrapper-->
                                <!--end::Wrapper-->
                                <!--begin::Button-->
                                <a href="#" class="btn btn-sm btn-primary" >{{ session('tahun_anggaran') }}</a>
                                <!--end::Button-->
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Toolbar-->
