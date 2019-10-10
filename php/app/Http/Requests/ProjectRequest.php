<?php

namespace App\Http\Requests;

class ProjectRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required | max:50',
            'desc' => 'required',
            'url' => 'required',
        ];
    }

    public function messages()
    {
        return ['required' => ':attribute 为必填项',
            'max' => ':attribute 长度不符合要求',
            'integer' => ':attribute 格式不符合要求',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '项目名称',
            'desc' => '项目描述',
            'url' => '项目token链接',
        ];
    }
}
