function getUrlPath() {
    return window.location.origin + window.location.pathname;
}

/*后台删除数据功能*/
function del(id) {
    swal({
            title: '您确定要删除吗？',
            text: '你将无法恢复它！',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF0000',
            cancelButtonColor: '#9999FF',
            confirmButtonText: '确定',
            closeOnConfirm: false
        },
        function (isConfirm) {
            if (isConfirm) {
                swal({
                        title: "点击ok后您将删除成功！",
                        text: "若没有点击ok，将不会删除信息。",
                        type: "success"
                    },
                    function () {
                        $("#destory_" + id).submit();
                    })
            }
            else {
                swal({
                    title: "已取消",
                    text: "您取消了删除操作！",
                    type: "error"
                })
            }
        })
}
