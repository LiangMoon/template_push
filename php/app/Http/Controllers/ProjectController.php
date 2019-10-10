<?php

namespace App\Http\Controllers;

use App\Models\AccessTokenApi;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use Auth;
use Validator;

class ProjectController extends Controller
{
    private $pagesize = 10;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $oAccessTokenApi = new AccessTokenApi();
        $oAccounts = $oAccessTokenApi->getProjects();
        return view('admin.project.index', compact('oAccounts'));
    }

    public function addOrEditProject(Request $request)
    {
        $iId = $request->input('id');
        $oProject = AccessTokenApi::find($iId);
        if ($oProject) {
            return view('admin.project.createoredit', compact('oProject'));
        } else {
            return view('admin.project.createoredit');
        }
    }

    /**
     * 保存
     */
    public function saveProject(ProjectRequest $request)
    {
        $aProject = $request->all();
        if ($aProject['id']) {
            $oProject = AccessTokenApi::find($aProject['id']);
            $sFlag = $oProject->update($aProject);
        } else {
            $oAccessTokenApi = AccessTokenApi::create($aProject);
            $oManager = Auth::user();
            if($oManager->identity === 0){
                if($oManager->auth_ids){
                    $oManager->auth_ids .= ','.$oAccessTokenApi->id;
                }else{
                    $oManager->auth_ids = $oAccessTokenApi->id;
                }
                $sFlag = $oManager->save();
            }else{
                $sFlag = $oAccessTokenApi;
            }
        }
        if ($sFlag) {
            return redirect('/project')->with('success', '操作成功');
        } else {
            return redirect('/project')->with('error', '操作失败');
        }
    }

    /**
     * 删除
     */
    public function deleteProject(Request $request)
    {
        $iId = $request->input('id');
        $oProject = AccessTokenApi::find($iId);
        $oManagers = \App\Models\User::where('identity',0)->get();
        foreach ($oManagers as $value){
            if(strpos($value->auth_ids, $iId) !== false){
                $aAuthIds = explode(',', $value->auth_ids);
                if(count($aAuthIds) > 1){
                    if($iId == end($aAuthIds)){
                        $value->auth_ids = str_replace(",$iId",'',$value->auth_ids);
                    }else{
                        $value->auth_ids = str_replace("$iId,",'',$value->auth_ids);
                    }
                }else{
                    $value->auth_ids = null;
                }
                $value->save();
            }
        }
        if (is_null($oProject)) {
            return redirect('/project')->with('error', '数据不存在');
        }
        if (AccessTokenApi::destroy($iId)) {
            return redirect('/project')->with('success', '删除成功');
        } else {
            redirect()->back()->with('error', '删除失败');
        }
    }
}
