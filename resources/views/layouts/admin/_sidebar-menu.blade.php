<li class="nav-item ">
    <a href="{{ url('/dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
        <i class="nav-icon fa-fw nav-icon fa fa-bars"></i>
        <p>Dashboard</p>
    </a>
</li>
@canany(['user_list','role_list'])
    <li
        class="nav-item has-treeview {{ Request::is('admin/users*') ? 'menu-open' : '' }}  {{ Request::is('admin/roles*') ? 'menu-open' : '' }}  ">
        <a href="#"
            class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }} {{ Request::is('admin/roles*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-cog"></i>
            <p>
                User Management
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview ">
            @can('user_list')
                <li class="nav-item">
                    <a href="{{ url('/admin/users') }}" class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fas fa-user"></i>
                        <p>Users</p>
                    </a>
                </li>
            @endcan
            @can('role_list')
                <li class="nav-item ">
                    <a href="{{ url('/admin/roles') }}" class="nav-link {{ Request::is('admin/roles*') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fas fa-tag"></i>
                        <p>Roles</p>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan
@can('category_list')
    <li class="nav-item ">
        <a href="{{ url('/admin/categories') }}" class="nav-link {{ Request::is('admin/categories*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-utensils"></i>
            <p>Menu Categories</p>
        </a>
    </li>
@endcan
@can('item_list')
    <li class="nav-item ">
        <a href="{{ url('/admin/items') }}" class="nav-link {{ Request::is('admin/items*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-coffee"></i>
            <p>Food Items</p>
        </a>
    </li>
@endcan
@can('order_list')
    <li class="nav-item">
        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ Request::is('admin/orders*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fas fa-clipboard"></i>
            <p>Orders</p>
        </a>
    </li>
@endcan
@can('order_create')
    <li class="nav-item">
        <a href="{{ route('admin.pos') }}" class="nav-link {{ Request::is('admin/pos*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>POS</p>
        </a>
    </li>
@endcan

@can('status_list')
    <li class="nav-item ">
        <a href="{{ url('/admin/statuses') }}" class="nav-link {{ Request::is('admin/statuses*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-cubes"></i>
            <p>Status</p>
        </a>
    </li>
@endcan
@canany(['patient_list', 'staff_list', 'customer_list', 'patient_discharge'])
    <li
        class="nav-item has-treeview {{ Request::is('admin/staffs*') ? 'menu-open' : '' }} {{ Request::is('admin/patients*') ? 'menu-open' : '' }} {{ Request::is('admin/patient-discharge-payments*') ? 'menu-open' : '' }}   {{ Request::is('admin/customers*') ? 'menu-open' : '' }} ">
        <a href="#"
            class="nav-link {{ Request::is('admin/staffs*') ? 'active' : '' }}  {{ Request::is('admin/patients*') ? 'active' : '' }} {{ Request::is('admin/patient-discharge-payments*') ? 'active' : '' }}  {{ Request::is('admin/customers*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-hospital-user"></i>
            <p>
                Customer and Staff
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview ">
            @can('customer_list')
                <li class="nav-item ">
                    <a href="{{ url('/admin/customers') }}"
                        class="nav-link {{ Request::is('admin/customers*') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fa fa-user"></i>
                        <p>Walking Customer</p>
                    </a>
                </li>
            @endcan
            @can('patient_list')
                <li class="nav-item ">
                    <a href="{{ url('/admin/patients') }}"
                        class="nav-link {{ Request::is('admin/patients*') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fa fa-bed"></i>
                        <p>Patients</p>
                    </a>
                </li>
            @endcan
            @can('patient_discharge')
                <li class="nav-item ">
                    <a href="{{ url('/admin/patient-discharge-payments') }}"
                        class="nav-link {{ Request::is('admin/patient-discharge-payments*') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fas fa-sign-out-alt"></i>
                        <p>Patients Discharge Payment</p>
                    </a>
                </li>
            @endcan
            @can('staff_list')
                <li class="nav-item ">
                    <a href="{{ url('/admin/staffs') }}" class="nav-link {{ Request::is('admin/staffs*') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fa fa-user-md"></i>
                        <p>Staffs</p>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan
@can('report_list')
    <li class="nav-item has-treeview {{ Request::is('admin/reports*') ? 'menu-open' : '' }}   ">
        <a href="#" class="nav-link {{ Request::is('admin/reports*') ? 'active' : '' }} ">
            <i class="nav-icon fas fa-list-ol"></i>
            <p>
                Reports
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview ">
            <li class="nav-item">
                <a href="{{ route('admin.reports.sales.index') }}"
                    class="nav-link {{ Request::is('admin/reports/sales') ? 'active' : '' }}">
                    <i class="nav-icon fa-fw nav-icon fas fa-bars"></i>
                    <p>Sales Report</p>
                </a>
            </li>
        </ul>
        <ul class="nav nav-treeview ">
            <li class="nav-item">
                <a href="{{ route('admin.reports.item_sales.index') }}"
                    class="nav-link {{ Request::is('admin/reports/item-sales') ? 'active' : '' }}">
                    <i class="nav-icon fa-fw nav-icon fas fa-book"></i>
                    <p>Item Sales Report</p>
                </a>
            </li>
        </ul>
    </li>
@endcan
{{-- @can('order_list')
    <li class="nav-item ">
        <a href="{{ url('/admin/kot') }}" class="nav-link {{ Request::is('admin/kot*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-list-alt"></i>
            <p>KOT</p>
        </a>
    </li>
@endcan --}}
@can('coupon_list')
    <li class="nav-item ">
        <a href="{{ url('/admin/coupons') }}" class="nav-link {{ Request::is('admin/coupons*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-ticket-alt"></i>
            <p>Coupons</p>
        </a>
    </li>
@endcan
@can('department_list')
    <li class="nav-item ">
        <a href="{{ url('/admin/departments') }}"
            class="nav-link {{ Request::is('admin/departments*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-building"></i>
            <p>Departments</p>
        </a>
    </li>
@endcan
@can('setting_create')
    <li class="nav-item ">
        <a href="{{ url('/admin/settings') }}" class="nav-link {{ Request::is('admin/settings*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-cogs"></i>
            <p>Settings</p>
        </a>
    </li>
@endcan
