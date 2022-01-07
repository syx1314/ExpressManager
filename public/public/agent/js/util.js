function timeFormat(timestamp) {
    var fmt = arguments[1] ? arguments[1] : "yyyy-MM-dd hh:mm:ss";
    if (!timestamp) return "";
    var cdate = new Date();
    if (timestamp.toString().length == 10) {
        cdate.setTime(timestamp * 1000);
    } else {
        cdate.setTime(timestamp);
    }
    var o = {
        "M+": cdate.getMonth() + 1, //月份
        "d+": cdate.getDate(), //日
        "h+": cdate.getHours(), //小时
        "m+": cdate.getMinutes(), //分
        "s+": cdate.getSeconds(), //秒
        "q+": Math.floor((cdate.getMonth() + 3) / 3), //季度
        "S": cdate.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (cdate.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" +
            o[k]).substr(("" + o[k]).length)));
    return fmt;
}

function shuiYinHeadimg(url) {
    return url + "?imageView2/1/w/100/h/100/format/jpg/q/75|imageslim";
}