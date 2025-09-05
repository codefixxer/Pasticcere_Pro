<div class="navbar-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                </button>
            </div>
        </div>
       
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" data-theme-toggle
                    class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

                @php
                // Ensure notifications are passed from the controller
                $notifications = \App\Models\Notification::with('news')
                                                          ->where('is_read', false)
                                                          ->where('user_id', Auth::id())
                                                          ->latest()
                                                          ->get();
                @endphp
                
                <div class="dropdown">
                    <button type="button"
                        class="btn btn-light rounded-circle d-flex justify-content-center align-items-center position-relative {{ $notifications->count() > 0 ? 'blink' : '' }}"
                        data-bs-toggle="dropdown">
                        <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                
                        @if($notifications->count())
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $notifications->count() }}
                            </span>
                        @endif
                    </button>
                
                    <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 380px;">
                        <div class="px-4 py-3 bg-primary-light text-white rounded-top">
                            <h6 class="mb-0">Notifiche</h6>
                            <small class="badge bg-dark">{{ $notifications->count() }} Non lette</small>
                        </div>
                        <div class="max-h-400px overflow-auto p-3">
                            @foreach($notifications as $n)
                                <a href="{{ route('notifications.markAsRead', $n->id) }}"
                                    class="px-3 py-2 d-flex align-items-center gap-3 mb-2 text-decoration-none text-dark hover-bg-light rounded">
                                    <span class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="bitcoin-icons:verify-outline" class="text-xl"></iconify-icon>
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-1">{{ $n->news->title }}</h6>
                                        <p class="mb-0 text-muted text-truncate" style="max-width: 200px;">
                                            {{ Str::limit($n->news->content, 100) }}...
                                        </p>
                                    </div>
                                    <span class="text-sm text-secondary-light">{{ $n->created_at->diffForHumans() }}</span>
                                </a>
                            @endforeach
                        </div>
                
                        <div class="text-center py-3 border-top">
                            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm px-4 py-2 rounded-pill">
                                    Segna tutte come lette
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                        data-bs-toggle="dropdown">
                        @php
                            
                                    $user = Auth::user()->load('roles');

                        @endphp
             @if(!empty(Storage::url('photos/'.$user->photo )))
    <img src="{{ Storage::url('photos/'.$user->photo) }}"
         alt="Profile Photo"
         class="w-40-px h-40-px object-fit-cover rounded-circle">
@else
    <img src="{{ asset('assets/images/asset/profile.jpg') }}"
         alt="Default Profile"
         class="w-40-px h-40-px object-fit-cover rounded-circle">
@endif

                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div
                            class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-2">{{ auth()->user()->name }}</h6>
                                <span class="text-secondary-light fw-medium text-sm">Admin</span>
                            </div>
                            <button type="button" class="hover-text-danger">
                                <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                            </button>
                        </div>
                        <ul class="to-top-list">
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="{{ route('profile') }}">
                                    <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon> Il mio profilo</a>
                            </li>
                 
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3"
                                    href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                    Esci
                                </a>
                            </li>
                            
                            <!-- Hidden Logout Form -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            
                        </ul>
                    </div>
                </div><!-- Profile dropdown end -->
            </div>
        </div>
    </div>
</div>
