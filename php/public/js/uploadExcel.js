/* 上传excel文件*/
function saveExcel(id) {
    $.ajaxFileUpload({
        url: '/upload-excel',
        type: 'post',
        secureuri: false,
        fileElementId: 'excel-upload',//文件上传空间的id属性
        dataType: 'json',//返回值类型 :一般设置为json
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        data: data,
        success: function (data, status) {
            if (data.err) {
                swal(data.err);
                return false;
            }
            swal(data.file_name + '上传成功！');
        }
    })
}

// 移除excel文件的字段
function removeExcel(id) {
    swal(id);
    $('#' + id).val('');
}