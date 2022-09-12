<li class="nav-item ">
    <a href="{{ url('/dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
        <i class="nav-icon fa-fw nav-icon fa fa-bars"></i>
        <p>Dashboard</p>
    </a>
</li>
@can('user_list')
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
            <li class="nav-item">
                <a href="{{ url('/admin/users') }}" class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                    <i class="nav-icon fa-fw nav-icon fas fa-user"></i>
                    <p>Users</p>
                </a>
            </li>
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
            <i class="nav-icon fa-fw nav-icon fa fa-bread-slice"></i>
            <p>Food Items</p>
        </a>
    </li>
@endcan
@canany('order_list')
    <li
        class="nav-item has-treeview {{ Request::is('admin/orders*') ? 'menu-open' : '' }}   ">
        <a href="#"
            class="nav-link {{ Request::is('admin/orders*') ? 'active' : '' }} ">
            <i class="nav-icon fas fa-list-ol"></i>
            <p>
                Orders Management
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview ">
            @can('order_list')
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ Request::is('admin/orders*') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fas fa-bars"></i>
                        <p>Orders</p>
                    </a>
                </li>
            @endcan
        </ul>
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
@can('customer_list')
    <li class="nav-item ">
        <a href="{{ url('/admin/customers') }}" class="nav-link {{ Request::is('admin/customers*') ? 'active' : '' }}">
            <i class="nav-icon fa-fw nav-icon fa fa-users"></i>
            <p>Customers</p>
        </a>
    </li>
@endcan
@canany('report_list')
    <li
        class="nav-item has-treeview {{ Request::is('admin/reports*') ? 'menu-open' : '' }}   ">
        <a href="#"
            class="nav-link {{ Request::is('admin/reports*') ? 'active' : '' }} ">
            <i class="nav-icon fas fa-list-ol"></i>
            <p>
                Reports
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview ">
            @can('report_list')
                <li class="nav-item">
                    <a href="{{ route('admin.reports.sales.index') }}" class="nav-link {{ Request::is('admin/reports/sales') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fas fa-bars"></i>
                        <p>Sales Report</p>
                    </a>
                </li>
            @endcan
        </ul>
         <ul class="nav nav-treeview ">
            @can('report_list')
                <li class="nav-item">
                    <a href="{{ route('admin.reports.item_sales.index') }}" class="nav-link {{ Request::is('admin/reports/item-sales') ? 'active' : '' }}">
                        <i class="nav-icon fa-fw nav-icon fas fa-book"></i>
                        <p>Item Sales Report</p>
                    </a>
                </li>
            @endcan
        </ul>
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