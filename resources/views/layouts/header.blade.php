 <style>
     .approval-notification-menu { width: min(375px, calc(100vw - 24px)); border-radius: 16px !important; }
     .approval-notification-item { border: 1px solid #edf0f5; background: #fff; text-decoration: none; transition: border-color .15s ease, background-color .15s ease, transform .15s ease; }
     .approval-notification-item:hover { border-color: #cbdcff; background: #f6f9ff; transform: translateY(-1px); }
     .header-user-initials { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 2px solid #fff; border-radius: 13px; color: #fff; background: linear-gradient(145deg, #3987f6, #1749a5); box-shadow: 0 4px 12px rgba(36, 99, 211, .2); font-size: 13px; font-weight: 800; letter-spacing: .04em; }
     .header-user-initials.header-user-initials-lg { width: 50px; height: 50px; border-radius: 16px; font-size: 16px; }
 </style>
 @php
     $headerUser = auth()->user();
     $headerName = trim($headerUser?->nama ?: $headerUser?->username ?: 'User');
     $headerNameParts = preg_split('/\s+/u', $headerName, -1, PREG_SPLIT_NO_EMPTY);
     $headerInitials = mb_strtoupper(mb_substr($headerNameParts[0] ?? 'U', 0, 1) . (count($headerNameParts) > 1 ? mb_substr(end($headerNameParts), 0, 1) : ''));
 @endphp
 <!--begin::Header-->
 <div id="kt_header" style="" class="header align-items-stretch">
     <!--begin::Container-->
     <div class="container-fluid d-flex align-items-stretch justify-content-between">
         <!--begin::Aside mobile toggle-->
         <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
             <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                 id="kt_aside_mobile_toggle">
                 <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                 <span class="svg-icon svg-icon-2x mt-1">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none">
                         <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                             fill="black" />
                         <path opacity="0.3"
                             d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                             fill="black" />
                     </svg>
                 </span>
                 <!--end::Svg Icon-->
             </div>
         </div>
         <!--end::Aside mobile toggle-->
         <!--begin::Mobile logo-->
         <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
             <a href="../../demo1/dist/index.html" class="d-lg-none">
                 <img alt="Logo" src="assets/media/logos/logo-2.svg" class="h-30px" />
             </a>
         </div>
         <!--end::Mobile logo-->
         <!--begin::Wrapper-->
         <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
             <!--begin::Navbar-->
             <div class="d-flex align-items-stretch" id="kt_header_nav">
                 <!--begin::Menu wrapper-->
                 <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu"
                     data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                     data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end"
                     data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true"
                     data-kt-swapper-mode="prepend"
                     data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                     <!--begin::Menu-->
                     <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch"
                         id="#kt_header_menu" data-kt-menu="true">
                        
                     </div>
                     <!--end::Menu-->
                 </div>
                 <!--end::Menu wrapper-->
             </div>
             <!--end::Navbar-->
             <!--begin::Topbar-->
             <div class="d-flex align-items-stretch flex-shrink-0">
                 <!--begin::Toolbar wrapper-->
                 <div class="d-flex align-items-stretch flex-shrink-0">
                     <!--begin::Notifications-->
                     <div class="d-flex align-items-center ms-1 ms-lg-3">
                         <!--begin::Menu- wrapper-->
                         <div class="btn btn-icon btn-active-light-primary position-relative w-30px h-30px w-md-40px h-md-40px rounded-circle"
                             data-kt-menu-trigger="click" data-kt-menu-attach="parent"
                             data-kt-menu-placement="bottom-end" aria-label="Notifikasi approval" title="Notifikasi approval">
                             <!--begin::Svg Icon | path: icons/duotune/general/gen022.svg-->
                             <i class="fas fa-bell fs-3 text-gray-700"></i>
                             <!--end::Svg Icon-->
                             @if (($approvalNotificationTotal ?? 0) > 0)
                                 <span class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-danger fs-9">{{ $approvalNotificationTotal > 99 ? '99+' : $approvalNotificationTotal }}</span>
                             @endif
                         </div>
                         <!--begin::Menu-->
                         <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px approval-notification-menu shadow-lg border-0 overflow-hidden"
                             data-kt-menu="true">
                             <!--begin::Heading-->
                             <div class="px-6 py-5 bg-light-primary border-bottom">
                                 <div class="d-flex align-items-center justify-content-between">
                                     <div>
                                         <h3 class="fw-bold text-gray-900 mb-1">Notifikasi Approval</h3>
                                         <div class="text-muted fs-7">Daftar tugas yang menunggu tindakan Anda</div>
                                     </div>
                                     <span class="badge badge-primary rounded-pill px-3 py-2">{{ $approvalNotificationTotal ?? 0 }}</span>
                                 </div>
                             </div>
                             <!--end::Heading-->
                             <div class="approval-notification-list p-4">
                                 @forelse ($approvalNotifications ?? [] as $notification)
                                     <a href="{{ $notification['url'] }}" class="approval-notification-item d-flex align-items-center gap-3 p-4 mb-2 rounded-3 text-gray-800 text-hover-primary">
                                         <span class="symbol symbol-45px symbol-circle flex-shrink-0"><span class="symbol-label bg-light-primary"><i class="ki-duotone {{ $notification['icon'] }} fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i></span></span>
                                         <span class="d-flex flex-column flex-grow-1 min-w-0">
                                             <span class="fw-bold text-gray-900 text-hover-primary">{{ $notification['label'] }}</span>
                                             <span class="text-muted fs-7 mt-1">{{ $notification['count'] }} tugas menunggu</span>
                                         </span>
                                         <span class="badge badge-light-primary rounded-pill px-3 py-2">{{ $notification['count'] }}</span>
                                     </a>
                                 @empty
                                     <div class="text-center py-8 px-4">
                                         <span class="symbol symbol-60px symbol-circle mx-auto mb-4"><span class="symbol-label bg-light-success"><i class="ki-duotone ki-check-circle fs-2x text-success"><span class="path1"></span><span class="path2"></span></i></span></span>
                                         <div class="fw-bold text-gray-800 mb-1">Semua sudah diperiksa</div>
                                         <div class="text-muted fs-7">Tidak ada tugas approval yang menunggu.</div>
                                     </div>
                                 @endforelse
                             </div>

                         </div>
                         <!--end::Menu-->
                         <!--end::Menu wrapper-->
                     </div>
                     <!--end::Notifications-->
                    
                     <!--begin::User-->
                     <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                         <!--begin::Menu wrapper-->
                         <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click"
                             data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                             <span class="header-user-initials" aria-label="Inisial {{ $headerName }}">{{ $headerInitials }}</span>
                         </div>
                         <!--begin::Menu-->
                         <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                             data-kt-menu="true">
                             <!--begin::Menu item-->
                             <div class="menu-item px-3">
                                 <div class="menu-content d-flex align-items-center px-3">
                                     <!--begin::Avatar-->
                                     <div class="symbol symbol-50px me-5">
                                         <span class="header-user-initials header-user-initials-lg" aria-label="Inisial {{ $headerName }}">{{ $headerInitials }}</span>
                                     </div>
                                     <!--end::Avatar-->
                                     <!--begin::Username-->
                                     <div class="d-flex flex-column">
                                         <div class="fw-bolder d-flex align-items-center fs-5">
                                             {{ $headerUser?->nama ?? $headerUser?->username ?? 'Pengguna' }}
                                         </div>
                                         <a href="{{ route('profile.edit') }}"
                                             class="fw-bold text-muted text-hover-primary fs-7">{{ $headerUser?->email ?? $headerUser?->username }}</a>
                                     </div>
                                     <!--end::Username-->
                                 </div>
                             </div>
                             <!--end::Menu item-->
                             <!--begin::Menu separator-->
                             <div class="separator my-2"></div>
                             <!--end::Menu separator-->
                             <!--begin::Menu item-->
                             <div class="menu-item px-5">
                                 <a href="{{ route('profile.edit') }}" class="menu-link px-5">Profil Saya</a>
                             </div>
                             <!--begin::Menu separator-->
                             <div class="separator my-2"></div>
                             <!--end::Menu separator-->
                             <!--end::Menu item-->
                             <!--begin::Menu item-->
                             <div class="menu-item px-5">
                                 <form method="POST" action="{{ route('logout') }}">
                                     @csrf
                                     <button type="submit"
                                         class="menu-link px-5 border-0 bg-transparent w-100 text-start">
                                         Sign Out
                                     </button>
                                 </form>
                             </div>
                             <!--end::Menu item-->
                             <!--begin::Menu separator-->
                             <div class="separator my-2"></div>
                             <!--end::Menu separator-->
                             <!--begin::Menu item-->
                             <div class="menu-item px-5">
                                 <div class="menu-content px-5">
                                     <label
                                         class="form-check form-switch form-check-custom form-check-solid pulse pulse-success"
                                         for="kt_user_menu_dark_mode_toggle">
                                         <input class="form-check-input w-30px h-20px" type="checkbox" value="1"
                                             name="mode" id="kt_user_menu_dark_mode_toggle"
                                             data-kt-url="../../demo1/dist/index.html" />
                                         <span class="pulse-ring ms-n1"></span>
                                         <span class="form-check-label text-gray-600 fs-7">Dark
                                             Mode</span>
                                     </label>
                                 </div>
                             </div>
                             <!--end::Menu item-->
                         </div>
                         <!--end::Menu-->
                         <!--end::Menu wrapper-->
                     </div>
                     <!--end::User -->
                     <!--begin::Heaeder menu toggle-->
                     <div class="d-flex align-items-center d-lg-none ms-2 me-n3" title="Show header menu">
                         <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                             id="kt_header_menu_mobile_toggle">
                             <!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->
                             <span class="svg-icon svg-icon-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none">
                                     <path
                                         d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z"
                                         fill="black" />
                                     <path opacity="0.3"
                                         d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z"
                                         fill="black" />
                                 </svg>
                             </span>
                             <!--end::Svg Icon-->
                         </div>
                     </div>
                     <!--end::Heaeder menu toggle-->
                 </div>
                 <!--end::Toolbar wrapper-->
             </div>
             <!--end::Topbar-->
         </div>
         <!--end::Wrapper-->
     </div>
     <!--end::Container-->
 </div>
 <!--end::Header-->
