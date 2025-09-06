<?php

namespace App\Http\Controllers;

use App\Models\Account;

use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            $validated = $request->validate([
                'user_name' => 'required|string|min:3|max:60|unique:accounts',
                'password' => 'required|string|min:3|max:60',
            ], [
                'user_name.required' => 'Vui lòng nhập tên tài khoản',
                'user_name.min' => 'Tên tài khoản phải có nhất 3 ký tự',
                'user_name.max' => 'Ten tài khoản chỉ được giới hạn 60 ký tự',
                'user_name.unique' => 'Tên tài khoản đã tồn tại',

                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có nhất 3 ký tự',
                'password.max' => 'Mật khấu chỉ được giới hạn 60 ký tự',
            ]);

            $user = Account::create([
                'user_name' => $validated['user_name'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công',
                'token_type' => 'bearer',
                'access_token' => 'Bearer ' . JWTAuth::fromUser($user),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        }
         catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký tài khoản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|min:3|max:60',
            'password' => 'required|string|min:3|max:60',
        ], [
            'user_name.required' => 'Vui lòng nhập tên tài khoản',
            'user_name.min' => 'Tên tài khoản phải có nhất 3 ký tự',
            'user_name.max' => 'Ten tài khoản chỉ được giới hạn 60 ký tự',

            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có nhất 3 ký tự',
            'password.max' => 'Mật khấu chỉ được giới hạn 60 ký tự',
        ]);
        
        try {
            if (!$token = JWTAuth::attempt($validated)) {
                return response()->json(['success' => false, 'message' => 'Sai tên tài khoản hoặc mật khẩu'], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'token_type' => 'bearer',
                'access_token' => 'Bearer ' . $token,
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng nhập',
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng nhập',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        try {
            auth('api')->logout();

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất tài khoản thành công'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Phiên đăng nhập đã hết hạn'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['success' => false, 'message' => 'Phiên đăng nhập không hợp lệ'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy phiên đăng nhập'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng xuất tài khoản',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function me()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => JWTAuth::user()
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['success' => false, 'message' => 'Phiên đăng nhập không hợp lệ'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy phiên đăng nhập'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin tài khoản',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
