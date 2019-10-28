<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'],
    function () use ($router) {

        $router->post('auth/login', ['uses' => 'Auth\AuthController@userAuthenticate']);
        $router->post('users/verify', ['uses' => 'UserController@verifyUser']);
        $router->post('users/verification-code', ['uses' => 'UserController@getVerificationCode']);
        $router->post('cities', ['uses' => 'HomeController@districtList']);
        $router->post('areas/{id}', ['uses' => 'HomeController@areaList']);
        $router->post('companies/all', ['uses' => 'HomeController@CompanyList']);
        $router->post('users/mr/add', ['uses' => 'MrController@add']);
        $router->post('users/pharmacy-mr', ['uses' => 'MrController@addMR']);

        $router->group(['middleware' => 'jwt.auth'],
            function () use ($router) {
                $router->get('users', ['uses' => 'UserController@showAllUsers']);
                $router->post('users', ['uses' => 'UserController@create']);

                /** Users */
                $router->post('users/{id}', ['uses' => 'UserController@update']);

                /** MRs */
                $router->post('mrs/{id}', ['uses' => 'MrController@update']);
                $router->get('mrs', ['uses' => 'MrController@index']);
                $router->get('orders/smart-mr', ['uses' => 'MrController@smartMrOrderList']);

                /** Home */
                $router->get('data-sync', ['uses' => 'HomeController@dataSync']);
                $router->get('sale-data-sync', ['uses' => 'HomeController@saleDataSync']);
                /* for mr light */
                $router->get('summary', ['uses' => 'HomeController@summary']);

                /** Dashboard */
                $router->get('dashboard/summary', ['uses' => 'DashboardController@summary']);
                $router->get('dashboard/statistics', ['uses' => 'DashboardController@getStatistics']);

                /** Medicine */
                $router->get('medicines/search', ['uses' => 'MedicineController@search']);
                $router->get('medicines/searchFromInventory', ['uses' => 'MedicineController@searchMedicineFromInventory']);
                $router->post('medicines/company', ['uses' => 'MedicineController@searchByCompany']);
                $router->get('companies', ['uses' => 'CompanyController@index']); // only name of all companies
                $router->get('companies/inventory', ['uses' => 'CompanyController@getCompaniesByInventory']); // only name of all companies
                $router->get('company-list', ['uses' => 'CompanyController@companyList']);

                /** Carts */
                $router->post('carts/add-to-cart', ['uses' => 'CartController@addToCart']);
                $router->get('carts/{token}', ['uses' => 'CartController@view']);
                $router->get('carts/{token}/check', ['uses' => 'CartController@tokenCheck']);
                $router->post('carts/delete-item', ['uses' => 'CartController@deleteItem']);
                $router->post('carts/quantity-update', ['uses' => 'CartController@quantityUpdate']);
                $router->post('carts/price-update', ['uses' => 'CartController@priceUpdate']);

                /** Report Sale */
                $router->get('sale/report', ['uses' => 'SaleController@saleReport']);
                $router->get('sale/due/report', ['uses' => 'SaleController@saleDueReport']);

                /** Sale Order */
                $router->post('orders/sale', ['uses' => 'SaleController@create']);
                $router->post('orders/sale/delete-item', ['uses' => 'SaleController@deleteItem']);
                $router->post('orders/sale/return-item', ['uses' => 'SaleController@update']);
                $router->post('orders/sale/upload-image', ['uses' => 'SaleController@uploadimage']);
                $router->get('sale/{saleId}', ['uses' => 'SaleController@view']);
                $router->get('sales', ['uses' => 'SaleController@index']);
                $router->get('sales/report', ['uses' => 'SaleController@report']);
                $router->get('sales/due', ['uses' => 'SaleController@saleDueList']);
                $router->post('sales/payout', ['uses' => 'SaleController@payout']);
                $router->post('sales/discount', ['uses' => 'SaleController@discount']);
                $router->get('reports/sale/latest', ['uses' => 'SaleController@latestSale']);
                $router->get('medicines/search/sale', ['uses' => 'MedicineController@searchByPharmacy']);
                $router->post('medicines/batch', ['uses' => 'MedicineController@batchList']);
                $router->post('medicines/quantity', ['uses' => 'MedicineController@getAvailableQuantity']);

                /** Purchase Order */
                $router->get('orders/latest', ['uses' => 'OrderController@latestPurchase']);
                $router->post('orders', ['uses' => 'OrderController@create']); // unused
                $router->post('orders/manual', ['uses' => 'OrderController@manualOrder']);
                $router->post('orders/purchase/manual', ['uses' => 'OrderController@manualPurchase']);
                $router->get('orders', ['uses' => 'OrderController@index']);
                $router->get('orders/items', ['uses' => 'OrderController@orderItems']);
                $router->get('orders/{token}', ['uses' => 'OrderController@view']);
                $router->get('orders/{orderId}/details', ['uses' => 'OrderController@details']);
                $router->post('orders/update', ['uses' => 'OrderController@update']);
                $router->post('orders/update-status', ['uses' => 'OrderController@statusUpdate']);
                $router->post('orders/delete-item', ['uses' => 'OrderController@deleteItem']);
                $router->get('orders/check-is-last-item/{item_id}', ['uses' => 'OrderController@checkIsLastItem']); // unused

                /** Purchase Order list for report */
                $router->get('purchase-report', ['uses' => 'OrderController@purchaseReport']);
                $router->get('purchase-report/filter', ['uses' => 'OrderController@purchaseFilter']);

                $router->post('purchase/save', ['uses' => 'OrderController@purchaseSave']);
                $router->post('purchase/due/save', ['uses' => 'OrderController@purchaseDueSave']);
                $router->post('purchase/item/details/update', ['uses' => 'OrderController@purchaseItemDetailsUpdate']);
                $router->post('lowStockQty/update', ['uses' => 'OrderController@lowStockQtyupdate']);
                $router->post('purchase/item/delete', ['uses' => 'OrderController@purchaseItemDetailsDelete']);

                $router->post('purchase/previous/details', ['uses' => 'OrderController@previousPurchaseDetails']);
                //purchase/list
                $router->get('purchase/list', ['uses' => 'OrderController@purchaseList']);
                $router->get('master/purchase/list', ['uses' => 'OrderController@masterPurchaseList']);
                $router->post('master/purchase/list/filter', ['uses' => 'OrderController@masterPurchaseListFilter']);
                $router->post('purchase/item/filter', ['uses' => 'OrderController@purchaseListFilter']);
                $router->get('purchase/due/list', ['uses' => 'OrderController@purchaseDueList']);

                $router->get('purchase/details/{orderId}', ['uses' => 'OrderController@purchaseDetails']);


                /** Sales List for report */
                $router->get('sales-report', ['uses' => 'OrderController@salesReport']);
                $router->get('sales-report/filter', ['uses' => 'OrderController@saleFilter']);

                /** MR Connection */
                $router->post('mr-connection', ['uses' => 'UserController@mrConnection']);

                /** Order/Reports */
                $router->get('reports/purchase-manual', ['uses' => 'OrderController@manualPurchaseList']);
                $router->get('reports/orders', ['uses' => 'OrderController@getOrderList']);
                $router->get('reports/ordersFilter', ['uses' => 'OrderController@orderFilterList']);
                $router->get('orders/orders/{order_id}', ['uses' => 'OrderController@getOrderDetails']);
                $router->get('reports/orders/items', ['uses' => 'OrderController@getItemList']);
                $router->post('order/fillReceive', ['uses' => 'OrderController@fullReceive']);
                $router->post('order/updateinfo', ['uses' => 'OrderController@orderUpdate']);

                /** Items  */
                $router->post('item/receive', ['uses' => 'OrderController@receiveItem']);

                /** Inventory   */
                $router->get('inventory/list', ['uses' => 'OrderController@productList']);
                $router->get('reports/inventory', ['uses' => 'OrderController@inventoryList']);
                $router->get('reports/inventoryFilter', ['uses' => 'OrderController@inventoryFilter']);
                $router->post('inventory/damages', ['uses' => 'OrderController@receiveDamageItem']);
                $router->get('reports/inventory/damagesList', ['uses' => 'OrderController@damagesList']);

                //Products
                $router->get('type/search', ['uses' => 'OrderController@typeSearch']);
                $router->post('product/save', ['uses' => 'OrderController@productSave']);
                $router->get('product/list', ['uses' => 'OrderController@userAddedProductList']);

                //inventory/list
                $router->get('inventory/listFilter', ['uses' => 'OrderController@inventoryListFilter']);

                /** Notification List */
                $router->get('notification/list', ['uses' => 'HomeController@getNotificationList']);
                $router->get('notification/list/all', ['uses' => 'HomeController@getAllNotificationList']);
                $router->post('notification/generateLowStockNotification', ['uses' => 'HomeController@generateLowStockNotification']);

                /** sales/persons/list */
                $router->get('sales/persons/list', ['uses' => 'HomeController@getSalePersonsList']);

            }
        );

    }
);
/** Script for database migration */
$router->get('medicine-scripe', ['uses' => 'TestController@medicineScript']);
$router->get('medicine-type', ['uses' => 'TestController@medicineTypeScript']);
$router->get('test', ['uses' => 'TestController@test']);

$router->post('orders/sync/data', ['uses' => 'HomeController@awsData']);

$router->post('data_sync', ['uses' => 'HomeController@dataSyncToDB']);
$router->post('sale_data_sync', ['uses' => 'HomeController@saleDataSyncToDB']);
$router->post('sync-data-to-server', ['uses' => 'HomeController@syncDataToServer']);

$router->get('companyScript', ['uses' => 'HomeController@companyScript']);
$router->get('api/orders/{id}/pdf', ['uses' => 'OrderController@downloadPDF']);

/** Notification open url for checking */

$router->get('notification/generate', ['uses' => 'HomeController@generateNotification']);
$router->get('test', ['uses' => 'UserController@test']);

/** Insert Consumer products */
$router->get('insertconsumerproducts', ['uses' => 'OrderController@insertconsumerproducts']);
$router->get('purchaseReportToExcels',  ['uses' => 'OrderController@purchaseReportToExcels']);
