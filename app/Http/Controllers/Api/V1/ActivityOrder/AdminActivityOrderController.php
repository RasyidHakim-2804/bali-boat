<?php

namespace App\Http\Controllers\Api\V1\ActivityOrder;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityOrder\AdminActivityOrderResource;
use App\Http\Resources\ActivityOrder\AdminListActivityOrderResource;
use App\Models\ActivityOrder;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class AdminActivityOrderController extends Controller
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

        return AdminListActivityOrderResource::collection(
            QueryBuilder::for(ActivityOrder::class)
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

    public function show(ActivityOrder $order)
    {
        // $this->permissionService->check('order');

        $order = new AdminActivityOrderResource(ActivityOrder::where('id', $order->id)->with('details', 'forms', 'payment')->first());

        return $this->successGet($order);
    }

    public function destroy(ActivityOrder $order)
    {
        // $this->permissionService->check('order');

        $order->delete();

        return $this->successStore();
    }
}
