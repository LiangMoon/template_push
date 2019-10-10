//第验证手机号码
function is_mobile(mobile){
    return strlen ( mobile ) == 11 && preg_match ( "/^1[0-9]{10}$/", mobile );
}

// 邮箱校验
function is_email(email) {
    return strlen ( email ) > 6 && preg_match ( "/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", email );
}

// url正则校验
function is_url(url) {
    //判断URL地址的正则表达式为:http(s)?://([\w-]+\.)+[\w-]+(/[\w- ./?%&=]*)?
    //下面的代码中应用了转义字符"\"输出一个字符"/"
    var Expression=/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
    var objExp=new RegExp(Expression);
    if(objExp.test(url) != true){
        $("#error_urlMsg").html('*网址格式不正确！请重新输入');
        return false;
    }
    return true;
}

// 非空校验函数
function is_null(val) {
    var str = val.replace(/(^\s*)|(\s*$)/g, '');//去除空格;
    if (str == '' || str == undefined || str == null) {
        return true;
    } else {
        return false;
    }
}

//判断是否为数组且至少有一个元素;
function isArray(arr){
    if(!$.isArray(arr)){
        return false;
    }else if(arr.length<1){
        return false;
    }else{
        return true;
    }
}

// 校验输入的数据是否为json格式
function is_json(val) {
    try {
        if (typeof JSON.parse(val) == "object") {
            return true;
        }
    } catch(e) {
        console.log(e);
        return false;
    }
}

//倒计时js分享
function getTimer(oldtime){
    var now=new Date();
    var end=new Date(oldtime).getTime();//兼容ios手机和android手机
    var s=parseInt((end-now)/1000);
    if(s>0){
        var d=parseInt(s/(3600*24));//s/(一天的总秒数),再取整

        //获得s中不足一天的秒数,再/一小时的总秒数,再取整
        var h=parseInt(s%(3600*24)/3600);

        //获得s中不足1小时的秒数,再/一分钟的总秒数,再取整
        var m=parseInt(s%3600/60);
        var s=s%60;//获得s中不足1分钟的秒数
        if(s<10){
            s="0"+s;
        }
        if(d>0){
            $(".days").css('opacity','1');
            $(".days").html(d);
        }else{
            $("b").css('display','none');
        }
        $(".hours").html(h);
        $(".minutes").html(m);
        $(".second").html(s);
    }else{
        $(".timer").html("直播活动已结束");
    }
}
setTimeout(function(){
    getTimer('dt');
});
setInterval(function(){
    getTimer('dt');
},1000);

//ios移动端软键盘收起后，页面内容留白不下滑
$("input").blur(function () {
    setTimeout(function () {
        window.scrollTo({top: 0, left: 0, behavior: "smooth"});
    }, 100);
});

/*
 * This function used to extract url parameter by name.
 *
 * @param {string} name This parameter is always needed
 * @param {string} href This parameter is always needed
 *
 * @return
 *          value : the value of name, if name present
 *          ""    : empty value, if name present with empty value or no value
 *          null  : absent name
 *
 * Note:
 *       if a parameter is present several times (?foo=lorem&foo=ipsum),
 *       you will get the first value (lorem). There is no standard about this
 *       and usages vary, see for example this question: Authoritative position
 *       of duplicate HTTP GET query keys (https://stackoverflow.com/questions/1746507/authoritative-position-of-duplicate-http-get-query-keys).
 *
 *       The function is case-sensitive, if you prefer case-insensitive
 *       parameter name, add 'i' modifier to RegExp
 *
 * Source: https://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
 */
function getParameterByName(name, href) {
    if (!href) href = window.location.href;
    var name_value = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name_value + '(=([^&#]*)|&|#|$)');
    var results = regex.exec(href);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}


