<?php

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/admin', function () {
    return redirect('/home');
});

Auth::routes();

/* 推送 */
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/template/all', 'HomeController@templateAll')->name('template.all');
Route::get('/push-page', 'HomeController@getPushPage');
Route::post('/upload-excel', 'HomeController@uploadExcel');//上传表格
Route::post('/test-send-template', 'HomeController@testSendTemplate');//测试推送模板消息
Route::post('/send-template', 'HomeController@postSendTemplate');//正式推送模板消息
Route::get('/download-records', 'HomeController@downloadRecords');//导出推送结果记录

/** 分配项目权限 */
Route::get('assign-auth', 'AuthController@assignAuth');
Route::get('owned-auth', 'AuthController@ownedAuth');
Route::post('assign-auth', 'AuthController@assignAuth');

/* 项目管理 */
Route::get('/project', 'ProjectController@index');
Route::get('/addoredit-project', 'ProjectController@addOrEditProject');
Route::post('/save-project', 'ProjectController@saveProject');
Route::post('/delete-project', 'ProjectController@deleteProject');

/** 获取微信公众号的关注用户 */
Route::get('fans', 'HomeController@fans');