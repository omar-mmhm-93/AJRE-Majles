{{-- This file is used for menu items by any Backpack v7 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-dropdown title="User Management" icon="la la-users">
    <x-backpack::menu-dropdown-item title="Users" icon="" :link="backpack_url('users')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="" :link="backpack_url('roles')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="" :link="backpack_url('permissions')" />
    <x-backpack::menu-dropdown-item title="Departments" icon="" :link="backpack_url('department')" />

    @if(backpack_user()?->can('sync_ldap'))
        <x-backpack::menu-dropdown-item title="LDAP" icon="" :link="backpack_url('ldap')" />
    @endif
</x-backpack::menu-dropdown>


<x-backpack::menu-dropdown title="Post Management" icon="las la-newspaper">
    <x-backpack::menu-dropdown-item title="Posts" icon="" :link="backpack_url('posts')" />
    <x-backpack::menu-dropdown-item title="Comments" icon="" :link="backpack_url('comments')" />
    <x-backpack::menu-dropdown-item title="Likes" icon="" :link="backpack_url('likes')" />
</x-backpack::menu-dropdown>