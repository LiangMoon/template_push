<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushLog extends Model
{
    protected $table = 'push_logs';
    protected $guarded = ['id'];
    public $timestamps = true;

    public static function addPushLog($iUserId, $iProjectId, $sCsvPath, $sTemplateId)
    {
        $oPushLog = new PushLog();
        $oPushLog->user_id = $iUserId;
        $oPushLog->project_id = $iProjectId;
        $oPushLog->file_csv_path = $sCsvPath;
        $oPushLog->template_id = $sTemplateId;
        $oPushLog->save();
    }
}
