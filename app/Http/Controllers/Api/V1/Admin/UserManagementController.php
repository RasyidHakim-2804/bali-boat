<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserManagementController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('admin'));
    }

    public function index()
    {
        // $this->permissionService->check('view');

        return UserResource::collection(
            QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts('name', 'id')
            ->paginate(25)
        );
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->is_active = $request->is_active;
        $user->save();

        return $this->successResponse();
    }
}
