<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMenuPermission
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $routeName = $request->route()->getName();

        // Map route names to [Menu, Submenu]
        $routeMap = [
            'userlist' => ['Users', 'userlist'],
            'userDevice' => ['Users', 'userDevice'],
            'userimei' => ['Users', 'userimei'],
            'usersim' => ['Users', 'usersim'],
            'usersimPayment' => ['Users', 'usersimPayment'],
            
            'partsUsage' => ['Parts', 'partsUsage'],
            'parts' => ['Parts', 'parts'],
            
            'productCreate' => ['Products', 'productCreate'],
            'productBatch' => ['Products', 'productBatch'],
            'productProcess' => ['Products', 'productProcess'],
            'productQc' => ['Products', 'productQc'],
            'productGenSerial' => ['Products', 'productGenSerial'],
            'productReady' => ['Products', 'productReady'],
            'productSold' => ['Products', 'productSold'],
            'productDefective' => ['Products', 'productDefective'],
            'category' => ['Products', 'category'],
            'productSpecification' => ['Products', 'productSpecification'],
            
            'userAdmins' => ['Manage', 'userAdmins'],
            'vendor' => ['Manage', 'vendor'],
            'userAddImei' => ['Manage', 'userAddImei'],
            'firmware.index' => ['Manage', 'firmware.index'],
            
            'createTicket' => ['Tickets', 'createTicket'],
            'viewTickets' => ['Tickets', 'viewTickets'],
            'processTickets' => ['Tickets', 'processTickets'],
            'qcTickets' => ['Tickets', 'qcTickets'],
            'closedTickets' => ['Tickets', 'closedTickets'],
            'deliveredTickets' => ['Tickets', 'deliveredTickets'],
            'allLogTickets' => ['Tickets', 'allLogTickets'],
            
            'printAddress' => ['Print', 'printAddress'],
            'printTickets' => ['Print', 'printTickets'],
        ];

        if (isset($routeMap[$routeName])) {
            list($menu, $submenu) = $routeMap[$routeName];
            
            if ($routeName === 'category') {
                if (!$user->hasMenuPermission('Products', 'category') && !$user->hasMenuPermission('Manage', 'category')) {
                    return redirect()->route('dashboard')->with('error', 'You do not have access to this page.');
                }
            } else {
                if (!$user->hasMenuPermission($menu, $submenu)) {
                    return redirect()->route('dashboard')->with('error', 'You do not have access to this page.');
                }
            }
        }

        return $next($request);
    }
}
