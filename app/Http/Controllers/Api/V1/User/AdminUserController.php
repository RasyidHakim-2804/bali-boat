<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminUserController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = (new AdminPermissionService('user'));
    }

    public function index()
    {
        $this->permissionService->check('view');

        return UserResource::collection(
            QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts('name', 'id')
            ->where('email_verified_at', '!=', null)
            ->paginate(25)
        );
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->permissionService->check('full');

        $user->is_active = $request->is_active;
        $user->save();

        return $this->successResponse();
    }
}
