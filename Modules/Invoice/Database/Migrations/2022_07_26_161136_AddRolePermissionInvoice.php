<?php

use Illuminate\Database\Migrations\Migration;

class AddRolePermissionInvoice extends Migration
{

    public function up()
    {
        $routes = [
            [
                'name' => 'Invoice',
                'route' => 'invoice',
                'parent_route' => null,
                'type' => 1,
                'module' => 'Invoice'
            ], [
                'name' => 'Pending Offline Payment',
                'route' => 'invoice.offline-payment',
                'parent_route' => 'invoice',
                'type' => 2,
                'module' => 'Invoice'

            ], [
                'name' => 'Approve',
                'route' => 'invoice.offline-payment.approve',
                'parent_route' => 'invoice.offline-payment',
                'type' => 3,
                'module' => 'Invoice'

            ],

            [
                'name' => 'Delete',
                'route' => 'invoice.offline-payment.destroy',
                'parent_route' => 'invoice.offline-payment',
                'type' => 3,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Invoice',
                'route' => 'invoice.index',
                'parent_route' => 'invoice',
                'type' => 2,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Add',
                'route' => 'invoice.create',
                'parent_route' => 'invoice.index',
                'type' => 3,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Edit',
                'route' => 'invoice.edit',
                'parent_route' => 'invoice.index',
                'type' => 3,
                'module' => 'Invoice'

            ],
            [
                'name' => 'View',
                'route' => 'invoice.show',
                'parent_route' => 'invoice.index',
                'type' => 3,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Delete',
                'route' => 'invoice.destroy',
                'parent_route' => 'invoice.index',
                'type' => 3,
                'module' => 'Invoice'

            ],


            [
                'name' => 'Settings',
                'route' => 'invoice.settings.index',
                'parent_route' => 'invoice',
                'type' => 2,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Printed certificate',
                'route' => 'prc',
                'parent_route' => null,
                'type' => 1,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Order List',
                'route' => 'prc.order.index',
                'parent_route' => 'prc',
                'type' => 2,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Print',
                'route' => 'prc.certificate.pdfPrint',
                'parent_route' => 'prc.order.index',
                'type' => 3,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Status change',
                'route' => 'prc.certificate.shipped',
                'parent_route' => 'prc.order.index',
                'type' => 3,
                'module' => 'Invoice'

            ],
            [
                'name' => 'Settings',
                'route' => 'prc.settings.index',
                'parent_route' => 'prc',
                'type' => 2,
                'module' => 'Invoice'

            ], [
                'name' => 'My Invoice',
                'route' => 'myInvoice',
                'parent_route' => null,
                'type' => 1,
                'module' => 'Invoice',
                'backend' => 0
            ],

        ];
        if (function_exists('permissionUpdateOrCreate')) {
            permissionUpdateOrCreate($routes);
        }
    }


    public function down()
    {
        //
    }
}
