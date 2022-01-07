$(function () {
    //toastr参数配置
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "0",
        "hideDuration": "0",
        "timeOut": "1000",
        "extendedTimeOut": "0",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }


    var checkAll = $('input.check-all');  //全选的input
    var checkboxs = $('input.ids'); //所有单选的input

    checkAll.on('ifChecked ifUnchecked', function (event) {
        if (event.type == 'ifChecked') {
            checkboxs.iCheck('check');
        } else {
            checkboxs.iCheck('uncheck');
        }
    });

    checkboxs.on('ifChanged', function (event) {
        if (checkboxs.filter(':checked').length == checkboxs.length) {
            checkAll.prop('checked', true);
        } else {
            checkAll.prop('checked', false);
        }
        checkAll.iCheck('update');
    })

    //ajax get请求
    $('.ajax-get').click(function () {
        var target;
        var that = this;
        if ($(this).hasClass('confirm')) {
            if (!confirm('确认要执行该操作吗?')) {
                return false;
            }
        }
        if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
            var load_index = layer.load(1, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });
            $.get(target).success(function (data) {
                layer.close(load_index);
                // var waittime = data.wait ? data.wait * 1000 : 2000;
                var waittime = 1000;
                if (data.errno == 0) {
                    layer.msg(data.errmsg, {icon: 6, time: waittime}, function () {
                        if (!$(that).hasClass('no-refresh')) {
                            if (data.data.url) {
                                location.href = data.data.url;
                            } else {
                                location.reload();
                            }
                        }
                    });
                } else {
                    layer.msg(data.errmsg, {icon: 5, time: waittime}, function () {
                        if (data.data.url) {
                            location.href = data.data.url;
                        }
                    });
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function () {
        var target, query, form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm = false;
        if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
            form = $('.' + target_form);
            if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
                form = $('.hide-data');
                query = form.serialize();
            } else if (form.get(0) == undefined) {
                return false;
            } else if (form.get(0).nodeName == 'FORM') {
                if ($(this).hasClass('confirm')) {
                    if (!confirm('确认要执行该操作吗?')) {
                        return false;
                    }
                }
                if ($(this).attr('url') !== undefined) {
                    target = $(this).attr('url');
                } else {
                    target = form.get(0).action;
                }
                query = form.serialize();
            } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                form.each(function (k, v) {
                    if (v.type == 'checkbox' && v.checked == true) {
                        nead_confirm = true;
                    }
                })
                if (nead_confirm && $(this).hasClass('confirm')) {
                    if (!confirm('确认要执行该操作吗?')) {
                        return false;
                    }
                }
                query = form.serialize();
            } else {
                if ($(this).hasClass('confirm')) {
                    if (!confirm('确认要执行该操作吗?')) {
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete', 'off').prop('disabled', true);
            var load_index = layer.load(1, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });
            $.post(target, query).success(function (data) {
                layer.close(load_index);
                $(that).removeClass('disabled').prop('disabled', false);
                // var waittime = data.wait ? data.wait * 1000 : 2000;
                var waittime = 1000;

                if (data.errno == 0) {
                    layer.msg(data.errmsg, {icon: 6, time: waittime}, function () {
                        if (!$(that).hasClass('no-close')) {
                            location.reload();
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        } else {
                            if (!$(that).hasClass('no-refresh')) {
                                if (data.data.url) {
                                    location.href = data.data.url;
                                } else {
                                    location.reload();
                                }
                            }
                        }
                    });
                } else {
                    layer.msg(data.errmsg, {icon: 5, time: waittime}, function () {
                        if (data.data.url) {
                            location.href = data.data.url;
                        }
                    });
                }
            });
        }
        return false;
    });

    /**顶部警告栏*/
    $('body').prepend("<div class='alert alert-success' id='top-alert' style='position: fixed;left: 2.5%;top:10px;width: 95%;display: none;z-index: 99999'>  </div>");
    var top_alert = $('#top-alert');

    window.updateAlert = function (text, classs, wait) {
        wait = wait ? wait : 2;
        if (classs) {
            top_alert.removeClass('alert-danger alert-success').addClass(classs);
        } else {
            top_alert.removeClass('alert-danger alert-success').addClass("alert-danger");
        }
        top_alert.text(text);
        top_alert.slideDown(200);
        window.setTimeout(function () {
            top_alert.hide();
        }, wait * 1000);

    };

    $('.open-window').click(function () {
        var that = $(this);
        var url = $(this).attr('href');
        var title = $(this).attr('title');

        layer.open({
            type: 2,
            title: title,
            shadeClose: true,
            shade: false,
            maxmin: true, //开启最大化最小化按钮
            area: ['80%', '90%'],
            content: url,
            success: function (layero, index) {
                // console.log(layero, index);
            },
            end: function () {
                if (!$(that).hasClass('no-refresh')) {
                    window.location.reload();
                }
            }
        });
        return false;
    });

    $('.open-reload').click(function () {
        var index = layer.load(1, {
            shade: [0.1, '#fff'] //0.1透明度的白色背景
        });
        window.location.reload();
    });
    $('.open-img-window').click(function () {
        var url = $(this).data('url');
        var title = $(this).data('title');
        var name = $(this).data('name');
        var maxcount = parseInt($(this).data('max'));
        if (!url) {
            return layer.msg('请设置标签的data-url属性');
        }
        if (!name) {
            return layer.msg('请设置标签的data-name属性');
        }
        layer.open({
            type: 2,
            title: title ? title : '选择图片',
            fixed: false, //不固定
            maxmin: true,
            moveOut: false,//true  可以拖出窗外  false 只能在窗内拖
            anim: 5,//出场动画 isOutAnim bool 关闭动画
            offset: 'auto',
            shade: 0,//遮罩
            resize: true,//是否允许拉伸
            move: '.layui-layer-title',
            area: ['50%', '80%'],
            content: url + "?name=" + name + "&maxcount=" + maxcount
        });
        return false;
    });
    $('.file-upload').click(function () {
        $($(this).attr('for')).trigger("click");
    });
    $(".show-big-img").click(function () {
        var src = $(this).data('url');
        var title = $(this).attr('title');
        layer.photos({
            photos: {
                "title": title, //相册标题
                "id": 123, //相册id
                "start": 0, //初始显示的图片序号，默认0
                "data": [   //相册包含的图片，数组格式
                    {
                        "alt": title,
                        "pid": 666, //图片id
                        "src": src, //原图地址
                        "thumb": "" //缩略图地址
                    }
                ]
            }
            , anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机
        });
    });

    $("#search,#excel").click(function () {
        var url = $(this).attr('url');
        var query = $('.input-groups').find('input').serialize();
        var queryta = $('.input-groups').find('textarea').serialize();
        var select = $('.input-groups').find('select').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
        query = query.replace(/^&/g, '');
        if (url.indexOf('?') > 0) {
            url += '&' + query;
        } else {
            url += '?' + query;
        }
        if (url.indexOf('?') > 0) {
            url += '&' + select;
        } else {
            url += '?' + select;
        }
        if (url.indexOf('?') > 0) {
            url += '&' + queryta;
        } else {
            url += '?' + queryta;
        }
        window.location.href = url;
    });
    //回车搜索
    $("[name=key]").keyup(function (e) {
        if (e.keyCode === 13) {
            $("#search").click();
            return false;
        }
    });
    $(".serach_selects").change(function () {
        $("#search").click();
        return false;
    });
})