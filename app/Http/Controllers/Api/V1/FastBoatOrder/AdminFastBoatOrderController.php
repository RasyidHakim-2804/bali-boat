<?php

namespace App\Http\Controllers\Api\V1\FastBoatOrder;

use App\Http\Controllers\Controller;
use App\Http\Resources\FastBoatOrder\AdminFastBoatOrderResource;
use App\Http\Resources\FastBoatOrder\AdminListFastBoatOrderResource;
use App\Models\FastBoatOrder;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class AdminFastBoatOrderController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    // public function __construct()
    // {
    //     $this->permissionService = (new AdminPermissionService('order'));
    // }

    public function index()
    {
        // $this->permissionService->check('order');

        return AdminListFastBoatOrderResource::collection(
            QueryBuilder::for(FastBoatOrder::class)
            ->allowedFilters([
                AllowedFilter::scope('from_date'),
                AllowedFilter::scope('to_date'),
                AllowedFilter::scope('search'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts('id')
            ->with('details')
            ->orderBy('id', 'DESC')
            ->paginate(25)
        );
    }

    public function show(FastBoatOrder $order)
    {
        // $this->permissionService->check('order');

        $order = new AdminFastBoatOrderResource(FastBoatOrder::where('id', $order->id)->with('details', 'forms', 'payment')->first());

        return $this->successGet($order);
    }

    public function destroy(FastBoatOrder $order)
    {
        // $this->permissionService->check('order');

        $order->delete();

        return $this->successStore();
    }
}
