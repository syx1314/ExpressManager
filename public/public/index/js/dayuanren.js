//dayuanren.js web版本
//依赖jquery 1.5 以上或者zepto
var dyr = {
    debug: true,
    version: 1.0,
    //post 请求
    post: function (url, param, callback) {
        var aj = $.ajax({
            url: url,
            data: param,
            type: 'post',
            success: function (data, textStatus) {
                if (dyr.debug) dyr.print(data);
                callback(data);
            },
            error: function (XMLHttpRequest, textstatus) {
                dyr.print("Error:http " + XMLHttpRequest.status + ",msg " + textstatus + "  [" + url + "]")
            }
        });
    },
    //get请求
    get: function (url, callback) {
        var aj = $.ajax({
            url: url,
            type: 'get',
            success: function (data, textStatus) {
                if (dyr.debug) dyr.print(data);
                callback(data);
            },
            error: function (XMLHttpRequest, textstatus) {
                dyr.print("Error:http " + XMLHttpRequest.status + ",msg " + textstatus + "  [" + url + "]")
            }
        });
    },
    //上传文件
    //url 上传地址，form 表单 如：from  #formid .formclass，callback回调
    upload: function (url, form, callback) {
        var fd = new FormData($(form)[0]);
        $.ajax({
            type: "post",
            url: url, //上传到后台的地址
            data: fd,
            processData: false,
            contentType: false
        }).done(function (ret) {
            callback(ret);
        });
    },
    //post 请求 form 表单 如：
    postfrom: function (url, form, callback) {
        var fd = new FormData($(form)[0]);
        var aj = $.ajax({
            url: url,
            data: fd,
            type: 'post',
            processData: false,
            contentType: false,
            success: function (data, textStatus) {
                if (dyr.debug) dyr.print(data);
                callback(data);
            },
            error: function (XMLHttpRequest, textstatus) {
                dyr.print("Error:http " + XMLHttpRequest.status + ",msg " + textstatus + "  [" + url + "]")
            }
        });
    },
    //跳转到某页，isNewWin 是否使用新窗口打开
    jump: function (url, isNewWin) {
        var isNewWin = arguments[1] ? arguments[1] : false;
        if (isNewWin) {
            window.open(url);
        } else {
            window.location.href = url;
        }
    },
    //返回某页，page 自然数:-2 -1 1,isload:是否重新加载
    goback: function (page, isload) {
        var page = arguments[0] ? arguments[0] : -1;
        var isload = arguments[1] ? arguments[1] : false;
        window.history.go(page);
        if (isload) {
            location.reload();
        }
    },
    //打印控制台
    print: function (data) {
        console.log(data);
    },
    //判断是否为手机号
    ismobile: function (phone) {
        if (!(/^1[34578]\d{9}$/.test(phone))) {
            return false;
        } else {
            return true;
        }
    },
    //判断是否是email
    isemail: function (email) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (filter.test(email)) {
            return true;
        } else {
            return false;
        }
    },
    // 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
    // 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
    //dyr.timeformat(1506051966,"yyyy-MM-dd HH:mm:ss")
    timeformat: function (timestamp, fmt) {
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
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    },
    sms_init: function (callback) {
        if (localStorage.sendendtimestamp && localStorage.sendendtimestamp > Date.parse(new Date())) {
            var i = setInterval(function () {
                var syscd = (localStorage.sendendtimestamp - Date.parse(new Date())) / 1000;
                callback({state: 'wait', second: syscd});
                if (syscd <= 0) {
                    clearInterval(i);
                    callback({state: 'reset'});
                }
            }, 1000);
        } else {
            localStorage.sendendtimestamp = Date.parse(new Date());
        }
    },
    //创建短信工作
    sms_send: function (url, param, miao, callback) {
        if (localStorage.sendendtimestamp > Date.parse(new Date())) {
            callback({state: 'error', data: {code: 1, msg: '请稍后再点击'}});
            return;
        }
        var aj = $.ajax({
            url: url,
            data: param,
            type: 'post',
            success: function (ret, textStatus) {
                if (ret.code == 0) {
                    var i = setInterval(function () {
                        var syscd = (localStorage.sendendtimestamp - Date.parse(new Date())) / 1000;
                        callback({state: 'wait', second: syscd});
                        if (syscd <= 0) {
                            clearInterval(i);
                            callback({state: 'reset'});
                        }
                    }, 1000);
                    callback({state: 'send', data: ret});
                    callback({state: 'wait', second: miao});
                    localStorage.sendendtimestamp = Date.parse(new Date()) + miao * 1000;
                } else {
                    callback({state: 'error', data: ret});
                }
            },
            error: function (XMLHttpRequest, textstatus) {
                dyr.print("Error:http " + XMLHttpRequest.status + ",msg " + textstatus + "  [" + url + "]")
            }
        });
    },
    getCookie: function (c_name) {
        if (document.cookie.length > 0) {
            c_start = document.cookie.indexOf(c_name + "=");
            if (c_start != -1) {
                c_start = c_start + c_name.length + 1;
                c_end = document.cookie.indexOf(";", c_start);
                if (c_end == -1) c_end = document.cookie.length;
                var cookiestr = unescape(document.cookie.substring(c_start, c_end));
                if (cookiestr.indexOf("{")) {
                    cookiestr = cookiestr.substring(cookiestr.indexOf("{"));
                }
                try {
                    var obj = JSON.parse(cookiestr);
                    return obj;
                } catch (e) {
                    return cookiestr;
                }
            }
        }
        return ""
    }
}