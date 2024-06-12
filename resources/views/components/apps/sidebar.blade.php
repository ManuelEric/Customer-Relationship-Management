<nav class="sidebar sidebar-offcanvas position-md-fixed h-75 overflow-auto pt-3 pe-1" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a @class([
                'nav-link',
                'bg-secondary text-white' => Request::is('dashboard'),
            ]) href="{{ url('dashboard') }}">
                <i class="bi bi-speedometer2 mx-2"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item nav-category">Pages</li>

        @foreach ($menus as $key => $menu)
            <li class="nav-item">
                @php
                    $key = $key == 'Users' ? 'User' : $key;
                @endphp
                <a class="nav-link  {{ Request::is(strtolower($key) . '*') ? 'text-primary' : '' }}"
                    data-bs-toggle="collapse" href="#{{ strtolower($key) }}" aria-expanded="false"
                    aria-controls="{{ strtolower($key) }}">
                    <i class="{{ $menu[0]['icon'] }} mx-2"></i>
                    <span class="menu-title">{{ $key }}</span>
                    <i class="menu-arrow bi bi-arrow-right"></i>
                </a>
                <div @class(['collapse', 'show' => Request::is(strtolower($key) . '*')]) id="{{ strtolower($key) }}">
                    <ul class="nav flex-column sub-menu bg-secondary p-0" style="list-style-type: none;">
                        @foreach ($menu as $key2 => $submenu)
                            @php
                                $submenu_link = $submenu['submenu_link'];
                                $explode = explode('/', $submenu_link);
                                $length = count($explode);
                            @endphp
                            @if ($position = strpos($submenu['submenu_link'], '?'))
                                @php
                                    $submenu_link = substr($submenu['submenu_link'], 0, $position);
                                @endphp
                            @endif

                            @php
                                
                                /* Removing the ? meaning GET params */
                                $submenu_active = $submenu['submenu_link'];
                                if ( $questionmark_pos = strpos($submenu_active, '?') ) {
                                    $submenu_active = substr($submenu_active, 0, $questionmark_pos);
                                }
                                
                                /* Extract only 2 words from 'invoice/corporate-program/needed' into 'invoice/corporate-program' */
                                $url = explode("/", $submenu_active);
                                if ( count($url) > 2) {
                                    $submenu_active = $url[0].'/'.$url[1];
                                }
                                
                                
                            @endphp
                            <li class="p-0">
                                <a @class([
                                    'nav-link',
                                    'py-1',
                                    'm-0',
                                    'ps-5',
                                    'border-bottom',
                                    'rounded-0',
                                    'active bg-info' => Request::is($submenu_active . '*'),
                                    'text-white',
                                ]) href="{{ url($submenu['submenu_link']) }}">
                                    <i class="bi bi-dash me-2"></i>
                                    {{ $submenu['submenu_name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        @endforeach

        @if($isSalesAdmin || $isSuperAdmin)
            <li class="nav-item">
                <a href="{{ url('import') }}"
                    class="nav-link {{ Request::is('import') ? 'bg-secondary text-white' : '' }}">
                    <i class="bi bi-upload mx-2"></i>
                    <span class="menu-title">Import</span>
                </a>
            </li>
        @endif
        
        @if ($isSuperAdmin)
            <li class="nav-item">
                <a href="{{ url('request-sign?type=invoice') }}"
                    class="nav-link {{ Request::is('request-sign') ? 'bg-secondary text-white' : '' }}">
                    <i class="bi bi-pencil mx-2"></i>
                    <span class="menu-title">Request Sign</span>
                </a>
            </li>
            <li class="nav-item nav-category">Settings</li>
            <li class="nav-item">
                <a href="{{ url('menus') }}"
                    class="nav-link {{ Request::is('menus') ? 'bg-secondary text-white' : '' }}">
                    <i class="bi bi-list mx-2"></i>
                    <span class="menu-title">Menus</span>
                </a>
            </li>
        @endif
    </ul>
</nav>