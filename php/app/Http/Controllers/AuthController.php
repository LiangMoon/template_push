<?php

namespace App\Http\Controllers;

use App\Models\AccessTokenApi;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /** 查询一个用户拥有的项目权限 */
    public function ownedAuth(Request $request)
    {
        $iManagerId = $request->input('managerId');
        $sAuthIds = User::where('id',$iManagerId)->value('auth_ids');
        return $sAuthIds;
    }

    /** 分配项目权限 */
    public function assignAuth(Request $request)
    {
        if($request->isMethod("GET")){
            $oUsers = User::where('identity',0)->get();
            $oProjects = AccessTokenApi::all();
            return view('admin.assign_auth',compact('oUsers','oProjects'));
        }else{
            $iManagerId = $request->input('manager_id');
            $aAuthIds = $request->input('auth_id');
            if(empty($aAuthIds)){
                $sAuthIds = null;
            }else{
                $sAuthIds = implode(',', $aAuthIds);
            }
            $bFlag = User::find($iManagerId)->update(['auth_ids' => $sAuthIds]);
            if($bFlag){
                return back()->with('success','操作成功');
            }else{
                return back()->with('error','操作失败');
            }
        }
    }
}
