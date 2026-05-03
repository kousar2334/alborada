<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\MemberPasswordResetRequest;

class MemberController extends Controller
{
    /**
     * Will return member list
     */
    public function memberList(Request $request): View
    {
        $query = User::with(['ads' => function ($q) {
            $q->select('id', 'user_id');
        }])
            ->where('type', config('settings.user_type.member'));

        if ($request->has('join_date') && $request['join_date'] != null) {
            $date_range = explode(' to ', $request['join_date']);
            if (sizeof($date_range) > 1) {
                $query = $query->whereBetween('created_at', $date_range);
            }
        }

        if ($request->has('status') && $request['status'] != null) {
            $query = $query->where('status', $request['status']);
        }

        if ($request->has('search') && $request['search'] != null) {
            $query = $query->where('name', 'like', '%' . $request['search'] . '%')
                ->orWhere('email', 'like', '%' . $request['search'] . '%')
                ->orWhere('uid', 'like', '%' . $request['search'] . '%')
                ->orWhere('phone', 'like', '%' . $request['search'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            $members = $query->orderBy('id', 'DESC')
                ->paginate($query->get()->count())
                ->withQueryString();
        } else {
            $members = $query->orderBy('id', 'DESC')
                ->paginate($per_page)
                ->withQueryString();
        }

        return view('backend.modules.members.index', ['members' => $members]);
    }
    /**
     * Will delete a member
     */
    public function memberDelete(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $member = User::findOrFail($request['id']);
            if ($member->user_type == config('settings.user_type.member')) {
                $member->delete();
                DB::commit();
            } else {
                abort(404);
            }
            toastNotification('success', 'Member deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Delete Failed', 'Error');
            return redirect()->back();
        }
    }
    /**
     * Will reset member password
     */
    public function memberPasswordReset(MemberPasswordResetRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $member = User::find($request['id']);
            if ($member != null) {
                $member->password = Hash::make($request['password']);
                $member->save();
                DB::commit();
                return response()->json([
                    'success' => true
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        } catch (\Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        }
    }
    /**
     * Will load member edit form
     */
    public function memberEdit(Request $request): JsonResponse
    {
        $member = User::where('id', $request['id'])->where('type', config('settings.user_type.member'))->first();

        if ($member != null) {
            return response()->json([
                'success' => true,
                'data' => view('backend.modules.members.edit', ['member' => $member])->render()
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }
    /**
     * Will update member
     */
    public function memberUpdate(MemberRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $member = User::where('id', $request['id'])->first();
            if ($member != null) {
                $member->name = $request['name'];
                $member->email = $request['email'];
                $member->status = $request['status'];
                $member->phone = $request['phone'];
                $member->image = $request['edit_image'];
                $member->save();
                DB::commit();
                return response()->json([
                    'success' => true,
                ]);
            }
            DB::commit();
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will store new member
     */
    public function memberStore(MemberRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $member = new User();
            $member->name = $request['name'];
            $member->email = $request['email'];
            $member->phone = $request['phone'];
            $member->image = $request['image'];
            $member->type = config('settings.user_type.member');
            $member->password = Hash::make($request['password']);
            $member->status = $request['status'];
            $member->save();
            DB::commit();
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        }
    }
}
