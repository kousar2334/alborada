<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{


    /**
     * Will store new user
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function storeNewUser(Request $request): JsonResponse
    {
        $request->validate(
            [
                'name' => 'required|max:150',
                'email' => 'required|email|max:150|unique:users,email',
                'role' => 'required|exists:roles,id',
                'password' => 'required|confirmed|min:6|max:150'
            ]
        );
        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->image = $request['image'];
            $user->password = Hash::make($request['password']);
            $user->type = config('settings.user_type.admin');
            $user->status = config('settings.general_status.active');
            $user->save();
            $user->assignRole($request->role);

            DB::commit();
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User create failed'
                ]
            );
        } catch (\Error $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User create failed'
                ]
            );
        }
    }
    /**
     * will return user update form
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function editUser(Request $request): JsonResponse
    {
        $user = User::findOrFail($request['id']);
        $roles = Role::all();
        return response()->json([
            'success' => true,
            'data' => view('backend.modules.users.user_edit', ['roles' => $roles, 'user' => $user])->render()
        ]);
    }
    /**
     * Will update user info
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updateUser(Request $request): JsonResponse
    {
        $request->validate(
            [
                'name' => 'required|max:150',
                'email' => 'required|email|max:150|unique:users,email,' . $request['id'],
                'role' => 'required|exists:roles,id',
                'password' => 'nullable|min:6|confirmed',
            ]
        );

        try {
            DB::beginTransaction();
            $user = User::findOrFail($request['id']);

            //Password Update
            if ($request['password'] != null) {
                $user->password = Hash::make($request['password']);
            }


            $user->name = $request['name'];
            $user->image = $request['edit_image'];
            $user->email = $request['email'];
            $user->status = $request['status'];
            $user->save();
            $user->syncRoles($request->role);

            DB::commit();
            return response()->json(
                [
                    'success' => true,
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User create failed'
                ]
            );
        } catch (\Error $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User create failed'
                ]
            );
        }
    }
    /**
     * Will delete a user
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function deleteUser(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($request['id']);
            $user->delete();
            DB::commit();
            toastNotification('success', 'User Deleted successfully', 'Success');
            return to_route('admin.users.list');
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'User delete failed', 'Error');
            return redirect()->back();
        } catch (\Error $e) {
            DB::rollBack();
            toastNotification('error', 'User delete failed');
            return redirect()->back();
        }
    }


    /**
     * Will return users list
     */
    public function users(): View
    {
        $users = User::where('type', config('settings.user_type.admin'))->get();
        $roles = Role::all();
        return view('backend.modules.users.index', ['users' => $users, 'roles' => $roles]);
    }
    /**
     * Will return permissions list
     */
    public function permissions(): View
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        return view('backend.modules.users.permissions', ['permissions' => $permissions]);
    }

    /**
     * Will return roles list
     */
    public function roles(): View
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('created_at')->get()->groupBy(function ($data) {
            return $data->module;
        });
        return view('backend.modules.users.roles', ['roles' => $roles, 'permissions' => $permissions]);
    }
    /**
     * Will store new user role
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function storeNewRole(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|max:150'
        ]);
        if ($request['permission'] == null) {
            return response()->json([
                'success' => false,
                'message' => 'Please select permissions'
            ]);
        }
        try {
            DB::beginTransaction();
            $role = new Role();
            $role->name = $request['name'];
            $role->guard_name = 'web';
            $role->save();

            foreach ($request['permission'] as $permission) {
                $role->givePermissionTo($permission);
            }

            DB::commit();
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }

    /**
     * Edit Role
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function editRole(Request $request): JsonResponse
    {
        $role = Role::find($request['id']);
        $permissions = Permission::orderBy('created_at')->get()->groupBy(function ($data) {
            return $data->module;
        });
        return response()->json([
            'success' => true,
            'data' => view('backend.modules.users.role_edit', ['role' => $role, 'permissions' => $permissions])->render()
        ]);
    }

    /**
     * Will delete a role
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function deleteRole(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $role = Role::findOrFail($request['id']);
            $role->delete();
            DB::commit();
            toastNotification('success', 'Role Deleted successfully', 'Success');
            return to_route('admin.users.role.list');
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Role delete failed', 'Error');
            return redirect()->back();
        } catch (\Error $e) {
            DB::rollBack();
            toastNotification('error', 'Role delete failed');
            return redirect()->back();
        }
    }
    /**
     * Will update user role
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updateRole(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|max:150'
        ]);
        if ($request['permission'] == null) {
            return response()->json([
                'success' => false,
                'message' => 'Please select permissions'
            ]);
        }
        try {
            DB::beginTransaction();
            $role = Role::findOrFail($request['id']);
            $role->name = $request['name'];
            $role->save();

            $role->syncPermissions($request['permission']);

            DB::commit();
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
