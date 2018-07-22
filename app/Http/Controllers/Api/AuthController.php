<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
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
        if (null == $request->input('code')) {
            return response()
                ->json(['ret' => 1001, 'errMsg' => '无效请求'],422);
        }
        $mini_program = EasyWeChat::MiniProgram();
        $return = $mini_program->auth->session($request->input('code'));
        if ($return && isset($return['errcode'])) {
            return response(['errMsg' => $return['errmsg'], 'ret' => 1001], 422);
        }
        $user = User::where('openid', $return['openid'])->first();
        if (null == $user) {
            $user = new User;
            $user->openid = $return['openid'];
        }
        $user->session_key = $return['session_key'];
        $user->save();

        $token = auth('api')->login($user);
        return $this->respondWithToken($token);

        // $token = [
        //     'token' => auth('api')->login($user),
        //     'token_type' => 'bearer',
        //     'expires_in' => auth('api')->factory()->getTTL() * 60,
        // ];
        // $user->token = $token;
        // return $this->respondWithToken($user);
    }
    # 小程序用户信息更新
    public function update(Request $request)
    {
        $user = auth('api')->user();
        if (!$user->session_key || !$request->input('iv') || !$request->input('encryptedData')) {
            return response()->json(['ret' => 1001, 'errMsg' => '参数不全'], 422);
        }
        $mini_program = EasyWeChat::MiniProgram();
        $wechat_user = $mini_program->encryptor->decryptData($user->session_key, $request->input('iv'), $request->input('encryptedData'));
        if ($wechat_user) {
            $data = [
                'openid' => $wechat_user['openId'],
                'city' => $wechat_user['city'],
                'country' => $wechat_user['country'],
                'gender' => $wechat_user['gender'],
                'nickname' => $wechat_user['nickName'],
                'province' => $wechat_user['province'],
                'unionid' => isset($wechat_user['unionId']) ? $wechat_user['unionId'] : null,
                'avatar' => $wechat_user['avatarUrl'],
            ];
        }

        User::where('openid', $user->openid)
            ->update($data);
        $updated_user = User::find($user->id);
        auth('api')->login($updated_user);
        return response()->json($updated_user);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
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
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
