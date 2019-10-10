<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class AccessTokenApi extends Model
{
    protected $table = 'access_token_api';
    protected $guarded = ['id'];
    public $timestamps = true;

    /** 获取当前用户有权管理的项目 */
    public function getProjects()
    {
        $oManager = Auth::user();
        if($oManager->identity === 0){
            $aAuthIds = explode(',', $oManager->auth_ids);
            $oAccounts = self::whereIn('id', $aAuthIds)->get();
        }else{
            $oAccounts = self::all();
        }
        return $oAccounts;
    }
}
