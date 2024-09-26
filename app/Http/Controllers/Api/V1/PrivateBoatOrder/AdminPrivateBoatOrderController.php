<?php

namespace App\Http\Controllers\Api\V1\PrivateBoatOrder;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivateBoatOrder\AdminPrivateBoatOrderResource;
use App\Http\Resources\PrivateBoatOrder\AdminListPrivateBoatOrderResource;
use App\Models\PrivateBoatOrder;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class AdminPrivateBoatOrderController extends Controller
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

        return AdminListPrivateBoatOrderResource::collection(
            QueryBuilder::for(PrivateBoatOrder::class)
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

    public function show(PrivateBoatOrder $order)
    {
        // $this->permissionService->check('order');

        $order = new AdminPrivateBoatOrderResource(PrivateBoatOrder::where('id', $order->id)->with('details', 'forms', 'payment')->first());

        return $this->successGet($order);
    }

    public function destroy(PrivateBoatOrder $order)
    {
        // $this->permissionService->check('order');

        $order->delete();

        return $this->successStore();
    }
}
