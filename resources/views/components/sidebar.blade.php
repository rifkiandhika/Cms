<style>
.treeview-menu li.active > a {
    background-color: #e9f3ff;
    color: #0d6efd;
    border-radius: 6px;
}
.sidebar-section-label {
    padding: 18px 20px 6px 20px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #9aa0ac !important;
    text-transform: uppercase;
    cursor: default;
    pointer-events: none;
}
.treeview-menu li > a {
    padding-left: 2.5rem;
    display: block;
}
</style>

<nav id="sidebar" class="sidebar-wrapper">

    {{-- Sidebar Profile --}}
    <div class="sidebar-profile">
        <img src="{{ asset('assets/images/user6.png') }}"
             class="img-shadow img-3x me-3 rounded-5"
             alt="User Profile">
        <div class="m-0">
            <h5 class="mb-1 profile-name text-nowrap text-truncate">
                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
            </h5>
            <p class="m-0 small profile-name text-nowrap text-truncate">
                {{ Auth::check() && Auth::user()->roles->first()
                    ? Auth::user()->roles->first()->name
                    : 'No Role' }}
            </p>
        </div>
    </div>

    <div class="sidebarMenuScroll">
        <ul class="sidebar-menu">

            {{-- Dashboard --}}
            <li class="{{ Request::is('/') || Request::is('dashboard') ? 'active current-page' : '' }}">
                <a href="{{ url('/dashboard') }}">
                    <i class="ri-home-6-line"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            {{-- ═══════════════════════════════
                 SECTION: OPERASIONAL
            ════════════════════════════════ --}}
            <li class="sidebar-section-label"><span>Operasional</span></li>

            {{-- Manajemen Supplier --}}
            <li class="treeview {{ Request::is('suppliers*') || Request::is('purchase-orders*') ? 'active' : '' }}">
                <a href="#!">
                    <i class="ri-folder-user-line"></i>
                    <span class="menu-text">Manajemen Supplier</span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('suppliers') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}">Supplier</a>
                    </li>
                    <li class="{{ Request::is('purchase-orders*') ? 'active' : '' }}">
                        <a href="{{ route('po.index') }}">Purchase Order</a>
                    </li>
                </ul>
            </li>

            {{-- Manajemen Gudang --}}
            <li class="treeview {{ Request::is('gudangs*') || Request::is('returs*') || Request::is('tagihan*') ? 'active' : '' }}">
                <a href="#!">
                    <i class="ri-store-2-line"></i>
                    <span class="menu-text">Manajemen Gudang</span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('gudangs') ? 'active' : '' }}">
                        <a href="{{ route('gudangs.index') }}">Gudang</a>
                    </li>
                    <li class="{{ Request::is('returs*') ? 'active' : '' }}">
                        <a href="{{ route('returs.index') }}">Retur</a>
                    </li>
                    <li class="{{ Request::is('tagihan') ? 'active' : '' }}">
                        <a href="{{ route('tagihan.index') }}">Tagihan PO</a>
                    </li>
                </ul>
            </li>

            {{-- Manajemen Supplier --}}
            <li class="treeview {{ Request::is('customers*')  ? 'active' : '' }}">
                <a href="#!">
                    <i class="ri-folder-user-line"></i>
                    <span class="menu-text">Manajemen Customer</span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('customers') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}">Customer</a>
                    </li>
                </ul>
            </li>

            {{-- ═══════════════════════════════
                 SECTION: MASTER DATA
            ════════════════════════════════ --}}
            <li class="sidebar-section-label"><span>Master Data</span></li>

            {{-- Data Master --}}
            <li class="treeview {{ Request::is('satuans*') || Request::is('jenis*') || Request::is('produks*') || Request::is('customers*') || Request::is('categories*') ? 'active' : '' }}">
                <a href="#!">
                    <i class="ri-database-line"></i>
                    <span class="menu-text">Master Data</span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('satuans') ? 'active' : '' }}">
                        <a href="{{ route('satuans.index') }}">Satuan</a>
                    </li>
                    <li class="{{ Request::is('jenis') ? 'active' : '' }}">
                        <a href="{{ route('jenis.index') }}">Jenis</a>
                    </li>
                    <li class="{{ Request::is('produks') ? 'active' : '' }}">
                        <a href="{{ route('produks.index') }}">Produk</a>
                    </li>
                    <li class="{{ Request::is('categories') ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}">Kategori</a>
                    </li>
                </ul>
            </li>

            {{-- SOP Perusahaan --}}
            <li class="{{ Request::is('sops*') ? 'active current-page' : '' }}">
                <a href="{{ route('sops.index') }}">
                    <i class="ri-book-open-line"></i>
                    <span class="menu-text">SOP Perusahaan</span>
                </a>
            </li>

            {{-- ═══════════════════════════════
                 SECTION: LAPORAN
            ════════════════════════════════ --}}
            <li class="sidebar-section-label"><span>Laporan</span></li>

            {{-- Laporan & Audit --}}
            <li class="treeview {{ Request::is('reports*') || Request::is('audits*') ? 'active' : '' }}">
                <a href="#!">
                    <i class="ri-file-chart-line"></i>
                    <span class="menu-text">Laporan & Audit</span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('reports/purchase-orders*') ? 'active' : '' }}">
                        <a href="{{ route('reports.purchase-orders.index') }}">Report PO</a>
                    </li>
                    <li class="{{ Request::is('reports/gudangs*') ? 'active' : '' }}">
                        <a href="{{ route('reports.gudangs.index') }}">Report Gudang</a>
                    </li>
                    <li class="{{ Request::is('audits') ? 'active' : '' }}">
                        <a href="{{ route('audits.index') }}">Audit</a>
                    </li>
                </ul>
            </li>

            {{-- ═══════════════════════════════
                 SECTION: MANAJEMEN
            ════════════════════════════════ --}}
            <li class="sidebar-section-label"><span>Manajemen</span></li>

            {{-- Manajemen Users --}}
            <li class="treeview {{ Request::is('users*') || Request::is('karyawans*') || Request::is('role-permissions*') ? 'active' : '' }}">
                <a href="#!">
                    <i class="ri-user-2-line"></i>
                    <span class="menu-text">Manajemen Users</span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('users') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">Users</a>
                    </li>
                    <li class="{{ Request::is('karyawans') ? 'active' : '' }}">
                        <a href="{{ route('karyawans.index') }}">Data Karyawan</a>
                    </li>
                    <li class="{{ Request::is('role-permissions') ? 'active' : '' }}">
                        <a href="{{ route('role-permissions.index') }}">Role Permission</a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>

    {{-- Sidebar Contact --}}
    <div class="sidebar-contact">
        <p class="fw-light mb-1 text-nowrap text-truncate">Emergency Contact</p>
        <h5 class="m-0 lh-1 text-nowrap text-truncate">0987654321</h5>
        <i class="ri-phone-line"></i>
    </div>

</nav>