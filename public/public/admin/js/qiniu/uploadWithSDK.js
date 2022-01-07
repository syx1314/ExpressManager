/**
 * 七牛文件上传方法
 * @param option
 * liaoqiang
 */
function qiniuUpload(option) {
    if (option.file && option.file.name) {
        $.post(option.tokenApi, {filename: option.file.name}, function (result) {
            if (result.errno == 0) {
                var config = {
                    useCdnDomain: true,
                    disableStatisticsReport: false,
                    retryCount: 6,
                    region: null// qiniu.region.z2 //选择上传服务器
                };
                var putExtra = {
                    fname: option.file.name,
                    params: {},
                    mimeType: null
                };
                uploadWithSDK(option, result.data.token, result.data.filename, putExtra, config, result.data.domain, result.data.prefix);
            } else {
                alert(result.errmsg);
            }
        });
    }else{
        typeof option.error === "function" ? option.error({code: 400, message: '未选择文件'}) : false;
    }

}
/**
 * 七牛文件分片上传
 * @param option
 * @param token
 * @param key
 * @param putExtra
 * @param config
 * @param domain
 * @param prefix
 * liaoqiang
 */
function uploadWithSDK(option, token, key, putExtra, config, domain, prefix) {
    // 切换tab后进行一些css操作
    var file = option.file;
    // eslint-disable-next-line
    // var finishedAttr = [];
    // eslint-disable-next-line
    // var compareChunks = [];
    var htmltitle = document.title;
    var observable;
    if (file && file.name) {
        key = key == "" ? file.name : key;
        // 设置next,error,complete对应的操作，分别处理相应的进度信息，错误信息，以及完成后的操作
        var error = function (err) {
            // console.log(err);
            document.title = htmltitle;
            if (err) {
                typeof option.error === "function" ? option.error(err) : false;
            } else {
                typeof option.error === "function" ? option.error({code: 400, message: '上传出现错误了'}) : false;
            }
        };

        var complete = function (res) {
            document.title = htmltitle;
            res.domain = domain;
            res.type = file.type;
            res.size = parseFloat(file.size / 1024 / 1024).toFixed(2);
            res.url = prefix + '://' + domain + '/' + res.key;
            typeof option.complete === "function" ? option.complete(res) : false;
        };

        var next = function (response) {
            // var chunks = response.chunks || [];
            var total = response.total;
            document.title = htmltitle + '   上传中..' + total.percent.toFixed(2) + '%';
            typeof option.next === "function" ? option.next(response) : false;
        };

        var subObject = {
            next: next,
            error: error,
            complete: complete
        };
        var subscription;
        // 调用sdk上传接口获得相应的observable，控制上传和暂停
        observable = qiniu.upload(file, key, token, putExtra, config);
        subscription = observable.subscribe(subObject);
        // subscription.unsubscribe();
    } else {
        typeof option.error === "function" ? option.error({code: 400, message: '未选择文件'}) : false;
    }

}
