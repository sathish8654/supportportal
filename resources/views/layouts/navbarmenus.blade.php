    @if (Auth::check() && Auth::user()->hasVerifiedEmail() && (Auth::user()->role_id === 2))


        <li class="nav-item dropdown">
            @if (Auth::user()->hasMenuPermission('Users'))
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                {{ __('Users') }}
            </a>
            @endif
            <ul class="dropdown-menu " style="position: absolute;">
                @if (Auth::user()->hasMenuPermission('Users', 'userlist'))
                <li><a class="dropdown-item" href="{{ route('userlist') }}">User List</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Users', 'userimei'))
                <li><a class="dropdown-item" href="{{ route('userimei') }}">User IMEI</a></li>@endif
                @if (Auth::user()->hasMenuPermission('Users', 'userDevice'))
                <li><a class="dropdown-item" href="{{ route('userDevice') }}">User Device</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Users', 'usersim'))
                <li><a class="dropdown-item" href="{{ route('usersim') }}">User SIM</a></li>@endif
                @if (Auth::user()->hasMenuPermission('Users', 'usersimPayment'))
                <li><a class="dropdown-item" href="{{ route('usersimPayment') }}">SIM Payment</a></li>@endif

            </ul>
        </li>


          <li class="nav-item dropdown">
            @if (Auth::user()->hasMenuPermission('Parts'))
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                {{ __('Parts') }}
            </a>
            @endif
            <ul class="dropdown-menu " style="position: absolute;">
                @if (Auth::user()->hasMenuPermission('Parts', 'partsUsage'))
                <li><a class="dropdown-item" href="{{ route('partsUsage') }}">Parts Usage</a></li> @endif
                         @if (Auth::user()->hasMenuPermission('Parts', 'parts'))
                    <li><a class="dropdown-item" href="{{ route('parts') }}">Add Parts</a></li>
                @endif

            </ul>
        </li>

        <li class="nav-item dropdown">
            @if (Auth::user()->hasMenuPermission('Products'))
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                {{ __('Products') }}
            </a>
            @endif
            <ul class="dropdown-menu " style="position: absolute;">
                @if (Auth::user()->hasMenuPermission('Products', 'productCreate'))
                <li><a class="dropdown-item" href="{{ route('productCreate') }}">Create</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productBatch'))
                <li><a class="dropdown-item" href="{{ route('productBatch') }}">Batch</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productProcess'))
                <li><a class="dropdown-item" href="{{ route('productProcess') }}">Process</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productQc'))
                <li><a class="dropdown-item" href="{{ route('productQc') }}">QC/Testing</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productGenSerial'))
                <li><a class="dropdown-item" href="{{ route('productGenSerial') }}">Genrate Serial</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productReady'))
                <li><a class="dropdown-item" href="{{ route('productReady') }}">Ready</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productSold'))
                <li><a class="dropdown-item" href="{{ route('productSold') }}">Sold</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productDefective'))
                <li><a class="dropdown-item" href="{{ route('productDefective') }}">Defective</a></li> @endif
                @if (Auth::user()->hasMenuPermission('Products', 'productSpecification'))
                <li><a class="dropdown-item" href="{{ route('productSpecification') }}">Product Specifiaction</a></li> @endif
                 
            </ul>
        </li>
        <li class="nav-item dropdown">
            @if (Auth::user()->hasMenuPermission('Manage'))
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    Manage
                </a>
            @endif
            <ul class="dropdown-menu " style="position: absolute;">
                @if (Auth::user()->hasMenuPermission('Manage', 'userAdmins'))
                    <li><a class="dropdown-item" href="{{ route('userAdmins') }}">Admins</a></li>
                @endif
                @if (Auth::user()->hasMenuPermission('Manage', 'category'))
                    <!-- <li><a class="dropdown-item" href="{{ route('category') }}">Category</a></li> -->
                @endif
                @if (Auth::user()->hasMenuPermission('Manage', 'vendor'))
                    <li><a class="dropdown-item" href="{{ route('vendor') }}">Addresses</a></li>
                @endif
                @if (Auth::user()->hasMenuPermission('Manage', 'userAddImei'))
                <li><a class="dropdown-item" href="{{ route('userAddImei') }}">IMEI</a></li>
                @endif
                @if (Auth::user()->hasMenuPermission('Manage', 'firmware.index'))
                    <li><a class="dropdown-item" href="{{ route('firmware.index') }}">Firmware</a></li>
                @endif
                @if (Auth::user()->hasMenuPermission('Products', 'category'))
                    <li><a class="dropdown-item" href="{{ route('category') }}">Manage Category</a></li>
                @endif

            </ul>
        </li>



        <li class="nav-item dropdown">
            @if (Auth::user()->hasMenuPermission('Tickets'))
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    Tickets
                </a>
            @endif
            <ul class="dropdown-menu " style="position: absolute;">
                @if (Auth::user()->hasMenuPermission('Tickets', 'createTicket'))
                    <li><a class="dropdown-item" href="{{ route('createTicket') }}">Create</a></li>
                @endif
                @if (Auth::user()->hasMenuPermission('Tickets', 'viewTickets'))
                    <li><a class="dropdown-item" href="{{ route('viewTickets') }}">Open</a></li>
                @endif
                @if (Auth::user()->hasMenuPermission('Tickets', 'processTickets'))
                    <li><a class="dropdown-item" href="{{ route('processTickets') }}">Process</a>
                    </li>
                @endif
                @if (Auth::user()->hasMenuPermission('Tickets', 'qcTickets'))
                    <li><a class="dropdown-item" href="{{ route('qcTickets') }}">QC</a></li>
                @endif
                @if (Auth::user()->hasMenuPermission('Tickets', 'closedTickets'))
                    <li><a class="dropdown-item" href="{{ route('closedTickets') }}">Closed</a>
                    </li>
                @endif
                @if (Auth::user()->hasMenuPermission('Tickets', 'deliveredTickets'))
                    <li><a class="dropdown-item" href="{{ route('deliveredTickets') }}">Delivered</a></li>
                @endif
                <!-- ////////////////////////////////////// -->
                @if (Auth::user()->hasMenuPermission('Tickets', 'allLogTickets'))
                    <li><a class="dropdown-item" href="{{ route('allLogTickets') }}">All Tickets Log</a>
                    </li>
                @endif

            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                Sales
            </a>
            <ul class="dropdown-menu " style="position: absolute;">
                <li><a class="dropdown-item" href="{{ route('leads.index') }}">Leads</a></li>
                <li><a class="dropdown-item" href="{{ route('quotations.index') }}">Quotations</a></li>
                <li><a class="dropdown-item" href="{{ route('orders.index') }}">Orders</a></li>
                <li><a class="dropdown-item" href="{{ route('installations.index') }}">Installations</a></li>
            </ul>
        </li>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                @if (Auth::user()->hasMenuPermission('Print'))
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        Print
                    </a>
                @endif
                <ul class="dropdown-menu " style="position: absolute;">
                    @if (Auth::user()->hasMenuPermission('Print', 'printAddress'))
                        <li><a class="dropdown-item" href="{{ route('printAddress') }}">Print Address</a></li>
                    @endif
                    @if (Auth::user()->hasMenuPermission('Print', 'printTickets'))
                        <li><a class="dropdown-item" href="{{ route('printTickets') }}">Print Tickets</a></li>
                    @endif
                </ul>
            </li>
        </ul>
<div class="text-center " style="margin:auto">
<a type="button" class="btn btn-outline-success btn-sm float-right" href="{{ route('search') }}">Search</a>
</div>


    @else
        {{-- show logged out navbar --}}
    @endif
