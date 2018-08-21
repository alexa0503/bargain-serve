<?php

namespace App\Http\Controllers\Api\Administrator;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\Administrator\Administrator as AdministratorResource;
use Overtrue\LaravelWeChat\Facade as EasyWeChat;

class AuthController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:6',
        ]);
        $credentials = request(['name', 'password']);
        
        $validator->after(function ($validator) use($credentials) {
            if (!auth('admin')->validate($credentials)) {
                $validator->errors()->add('password', '错误的用户名或者密码');
            }
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $token = auth('admin')->attempt($credentials);
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $admin = auth('admin')->user();
        if( $admin->shop_id && !file_exists(base_path('public/codes/shops/').$admin->shop_id.'.png') ){
            $mini_program = EasyWeChat::MiniProgram();
            $response = $mini_program->app_code->getUnlimit($admin->shop_id,[
                'page'=>'pages/index/index',
                'width'=>800,
                'auto_color'=>false,
                'is_hyaline'=>true,
                'line_color'=>(object)['r'=>0,'g'=>0,'b'=>0],
            ]);
            $response->saveAs(base_path('public/codes/shops/'), $admin->shop_id.'.png');
        }
        return new AdministratorResource($admin);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admin')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        return response()->json([]);
    }
    
    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $rules = [
            'password' => [
                'required',
                'regex:/.{6,}/'
            ],
            'repeatPassword' => 'required',
        ];
        $messages = [
            'password.required' => '请输入密码',
            'password.regex' => '密码不能少于六位数',
            'repeatPassword.*' => '请输入密码重复密码',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->after(function ($validator) use($request){
            if( $request->input('password') != $request->input('repeatPassword') ){
                $validator->errors()->add('repeatPassword', '两次输入的密码不一致');
            }
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        $admin = auth('admin')->user();
        \DB::table('administrators')->where('id', $admin->id)->update([
            'password' => bcrypt($request->input('password'))
        ]);
        return response()->json([]);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('admin')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 24 * 60
        ]);
    }
}