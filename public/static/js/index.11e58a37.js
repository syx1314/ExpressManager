(function (e) {
    function n(n) {
        for (var a, i, s = n[0], c = n[1], u = n[2], l = 0, g = []; l < s.length; l++) i = s[l], Object.prototype.hasOwnProperty.call(o, i) && o[i] && g.push(o[i][0]), o[i] = 0;
        for (a in c) Object.prototype.hasOwnProperty.call(c, a) && (e[a] = c[a]);
        p && p(n);
        while (g.length) g.shift()();
        return r.push.apply(r, u || []), t()
    }

    function t() {
        for (var e, n = 0; n < r.length; n++) {
            for (var t = r[n], a = !0, i = 1; i < t.length; i++) {
                var c = t[i];
                0 !== o[c] && (a = !1)
            }
            a && (r.splice(n--, 1), e = s(s.s = t[0]))
        }
        return e
    }

    var a = {}, o = {index: 0}, r = [];

    function i(e) {
        return s.p + "static/js/" + ({
            "pages-agent-balance": "pages-agent-balance",
            "pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record": "pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record",
            "pages-agent-balancelog": "pages-agent-balancelog",
            "pages-agent-rebateorder": "pages-agent-rebateorder",
            "pages-agent-tixianlog": "pages-agent-tixianlog",
            "pages-agent-yaoqinlog": "pages-agent-yaoqinlog",
            "pages-index-record": "pages-index-record",
            "pages-agent-haibao": "pages-agent-haibao",
            "pages-agent-index": "pages-agent-index",
            "pages-agent-links": "pages-agent-links",
            "pages-agent-lirun": "pages-agent-lirun",
            "pages-agent-poster": "pages-agent-poster",
            "pages-index-index": "pages-index-index",
            "pages-login-h5reg": "pages-login-h5reg",
            "pages-login-loginpwd": "pages-login-loginpwd",
            "pages-login-oauth": "pages-login-oauth",
            "pages-my-apply": "pages-my-apply",
            "pages-my-my": "pages-my-my",
            "pages-other-city": "pages-other-city",
            "pages-other-doc": "pages-other-doc",
            "pages-other-helps": "pages-other-helps",
            "pages-other-tagline": "pages-other-tagline"
        }[e] || e) + "." + {
            "pages-agent-balance": "9a5a4809",
            "pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record": "45f50343",
            "pages-agent-balancelog": "b39d32b0",
            "pages-agent-rebateorder": "1be65e89",
            "pages-agent-tixianlog": "bce92646",
            "pages-agent-yaoqinlog": "6f2ecfee",
            "pages-index-record": "9067ed52",
            "pages-agent-haibao": "80b7e24d",
            "pages-agent-index": "3e719fbb",
            "pages-agent-links": "7d8d0d42",
            "pages-agent-lirun": "cdb941af",
            "pages-agent-poster": "62ab0474",
            "pages-index-index": "78153b34",
            "pages-login-h5reg": "91676ef3",
            "pages-login-loginpwd": "7a434cae",
            "pages-login-oauth": "0a97d416",
            "pages-my-apply": "c1c642e1",
            "pages-my-my": "00340874",
            "pages-other-city": "92ddfa59",
            "pages-other-doc": "bbaf1628",
            "pages-other-helps": "e73e8d3d",
            "pages-other-tagline": "4d400eca"
        }[e] + ".js"
    }

    function s(n) {
        if (a[n]) return a[n].exports;
        var t = a[n] = {i: n, l: !1, exports: {}};
        return e[n].call(t.exports, t, t.exports, s), t.l = !0, t.exports
    }

    s.e = function (e) {
        var n = [], t = o[e];
        if (0 !== t) if (t) n.push(t[2]); else {
            var a = new Promise((function (n, a) {
                t = o[e] = [n, a]
            }));
            n.push(t[2] = a);
            var r, c = document.createElement("script");
            c.charset = "utf-8", c.timeout = 120, s.nc && c.setAttribute("nonce", s.nc), c.src = i(e);
            var u = new Error;
            r = function (n) {
                c.onerror = c.onload = null, clearTimeout(l);
                var t = o[e];
                if (0 !== t) {
                    if (t) {
                        var a = n && ("load" === n.type ? "missing" : n.type), r = n && n.target && n.target.src;
                        u.message = "Loading chunk " + e + " failed.\n(" + a + ": " + r + ")", u.name = "ChunkLoadError", u.type = a, u.request = r, t[1](u)
                    }
                    o[e] = void 0
                }
            };
            var l = setTimeout((function () {
                r({type: "timeout", target: c})
            }), 12e4);
            c.onerror = c.onload = r, document.head.appendChild(c)
        }
        return Promise.all(n)
    }, s.m = e, s.c = a, s.d = function (e, n, t) {
        s.o(e, n) || Object.defineProperty(e, n, {enumerable: !0, get: t})
    }, s.r = function (e) {
        "undefined" !== typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(e, "__esModule", {value: !0})
    }, s.t = function (e, n) {
        if (1 & n && (e = s(e)), 8 & n) return e;
        if (4 & n && "object" === typeof e && e && e.__esModule) return e;
        var t = Object.create(null);
        if (s.r(t), Object.defineProperty(t, "default", {
            enumerable: !0,
            value: e
        }), 2 & n && "string" != typeof e) for (var a in e) s.d(t, a, function (n) {
            return e[n]
        }.bind(null, a));
        return t
    }, s.n = function (e) {
        var n = e && e.__esModule ? function () {
            return e["default"]
        } : function () {
            return e
        };
        return s.d(n, "a", n), n
    }, s.o = function (e, n) {
        return Object.prototype.hasOwnProperty.call(e, n)
    }, s.p = "/", s.oe = function (e) {
        throw console.error(e), e
    };
    var c = window["webpackJsonp"] = window["webpackJsonp"] || [], u = c.push.bind(c);
    c.push = n, c = c.slice();
    for (var l = 0; l < c.length; l++) n(c[l]);
    var p = u;
    r.push([0, "chunk-vendors"]), t()
})({
    0: function (e, n, t) {
        e.exports = t("019f")
    }, "019f": function (e, n, t) {
        "use strict";
        var a = t("4ea4"), o = a(t("5530"));
        t("e260"), t("e6cf"), t("cca6"), t("a79d"), t("8588"), t("06b9");
        var r = a(t("e143")), i = a(t("7a86")), s = a(t("d065")), c = a(t("58cb")), u = a(t("17a6")), l = a(t("8b5b"));
        r.default.config.productionTip = !1, r.default.component("LoginBox", l.default), i.default.mpType = "app", r.default.use(s.default), r.default.use(c.default), r.default.use(u.default);
        var p = new r.default((0, o.default)({}, i.default));
        p.$mount()
    }, "026b": function (e, n, t) {
        "use strict";
        (function (e) {
            var a = t("4ea4");
            Object.defineProperty(n, "__esModule", {value: !0}), n.default = void 0;
            var o = a(t("ad09")), r = {
                data: function () {
                    return {canIUseGetUserProfile: !1}
                }, mounted: function () {
                    var e = this;
                    uni.$on("login", (function () {
                        e.login()
                    })), uni.$on("loginSus", (function () {
                        uni.reLaunch({url: "/pages/index/index"})
                    }))
                }, methods: {
                    open: function (e) {
                        this.$refs.popref.open()
                    }, closePop: function () {
                        uni.$emit("loginSus", {}), this.setLogining(!1), this.$refs.popref.close()
                    }, login: function () {
                        e.log("登录");
                        var n = this;
                        this.isLogining() || (e.log("开始登录"), uni.showLoading({title: "加载中"}), this.setLogining(!0), uni.login({
                            provider: "weixin",
                            success: function (t) {
                                n.$request.post("Applet/login", {
                                    data: {
                                        code: t.code,
                                        vi: o.default.getVid()
                                    }
                                }).then((function (e) {
                                    uni.hideLoading(), 0 == e.data.errno ? (o.default.setToken(e.data.data.access_token), n.setUserinfo(e.data.data.customer), 0 == e.data.data.customer.is_mp_auth ? n.$refs.popref.open() : (n.setLogining(!1), uni.$emit("loginSus", e.data.data.customer))) : (n.setLogining(!1), uni.showToast({
                                        title: e.data.errmsg,
                                        icon: "none",
                                        duration: 2e3
                                    }))
                                })).catch((function (n) {
                                    e.error("error:", n)
                                }))
                            }
                        }))
                    }, getUserProfile: function () {
                        var n = this;
                        uni.getUserProfile({
                            desc: "获取头像昵称用于账号注册", success: function (t) {
                                e.log(t), n.closePop(), n.$request.post("customer/saveinfo", {
                                    data: {
                                        headimg: t.userInfo.avatarUrl,
                                        username: t.userInfo.nickName,
                                        sex: 2 == t.userInfo.gender ? 2 : 1,
                                        is_mp_auth: 1
                                    }
                                }).then((function (e) {
                                    uni.showToast({title: e.data.errmsg, icon: "none", duration: 2e3})
                                })).catch((function (n) {
                                    e.error("error:", n)
                                }))
                            }, fail: function (e) {
                                n.closePop()
                            }
                        })
                    }, getUserInfo: function (n) {
                        var t = this;
                        uni.login({
                            provider: "weixin", success: function (a) {
                                t.$request.post("Applet/save_user_info", {
                                    data: {
                                        code: a.code,
                                        encryptedData: n.detail.encryptedData,
                                        iv: n.detail.iv
                                    }
                                }).then((function (e) {
                                    t.setLogining(!1), uni.hideLoading(), 0 == e.data.errno ? t.closePop() : uni.showToast({
                                        title: e.data.errmsg,
                                        icon: "none",
                                        duration: 2e3
                                    })
                                })).catch((function (n) {
                                    e.error("error:", n)
                                }))
                            }
                        })
                    }
                }
            };
            n.default = r
        }).call(this, t("5a52")["default"])
    }, "0553": function (e, n, t) {
        "use strict";
        var a = t("9a0f"), o = t.n(a);
        o.a
    }, "06c7": function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("6a3e"), o = t("733c");
        for (var r in o) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return o[e]
            }))
        }(r);
        t("6920");
        var i, s = t("f0c5"),
            c = Object(s["a"])(o["default"], a["b"], a["c"], !1, null, "64cecef0", null, !1, a["a"], i);
        n["default"] = c.exports
    }, "0c78": function (e, n, t) {
        "use strict";
        var a;
        t.d(n, "b", (function () {
            return o
        })), t.d(n, "c", (function () {
            return r
        })), t.d(n, "a", (function () {
            return a
        }));
        var o = function () {
            var e = this, n = e.$createElement, t = e._self._c || n;
            return t("App", {attrs: {keepAliveInclude: e.keepAliveInclude}})
        }, r = []
    }, "17a6": function (e, n, t) {
        "use strict";
        Object.defineProperty(n, "__esModule", {value: !0}), n.default = void 0;
        var a = {
            install: function (e, n) {
                e.prototype.setUserinfo = function (e) {
                    return uni.setStorageSync("userinfo", JSON.stringify(e)), e
                }, e.prototype.getUserinfo = function (e) {
                    e = uni.getStorageSync("userinfo") ? JSON.parse(uni.getStorageSync("userinfo")) : {};
                    return e
                }, e.prototype.setHomepageurl = function (e) {
                    return sessionStorage.setItem("homepageurl", sessionStorage.getItem("homepageurl") ? sessionStorage.getItem("homepageurl") : e), e
                }, e.prototype.getHomepageurl = function () {
                    var e = sessionStorage.getItem("homepageurl") || "";
                    return e
                }, e.prototype.setLogining = function (e) {
                    return uni.setStorageSync("islogining", e), e
                }, e.prototype.isLogining = function () {
                    return !!uni.getStorageSync("islogining")
                }, e.prototype.setLocCity = function (e) {
                    return uni.setStorageSync("ClientLocCity", JSON.stringify(e)), e
                }, e.prototype.getLocCity = function () {
                    var e = uni.getStorageSync("ClientLocCity") ? JSON.parse(uni.getStorageSync("ClientLocCity")) : {};
                    return e
                }
            }
        };
        n.default = a
    }, "198f": function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("e2ed"), o = t("a0f1");
        for (var r in o) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return o[e]
            }))
        }(r);
        t("927b");
        var i, s = t("f0c5"),
            c = Object(s["a"])(o["default"], a["b"], a["c"], !1, null, "016ede03", null, !1, a["a"], i);
        n["default"] = c.exports
    }, "32fa": function (e, n, t) {
        var a = t("24fb");
        n = a(!1), n.push([e.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n/*每个页面公共css */uni-page-body{background-color:#fff}body.?%PAGE?%{background-color:#fff}", ""]), e.exports = n
    }, "49b6": function (e, n, t) {
        var a = t("32fa");
        "string" === typeof a && (a = [[e.i, a, ""]]), a.locals && (e.exports = a.locals);
        var o = t("4f06").default;
        o("451312fa", a, !0, {sourceMap: !1, shadowMode: !1})
    }, 5072: function (e, n, t) {
        "use strict";
        (function (e) {
            var a = t("4ea4");
            Object.defineProperty(n, "__esModule", {value: !0}), n.default = void 0;
            var o = a(t("ad09")), r = {
                onLaunch: function (n) {
                    this.setLogining(!1), this.setHomepageurl(window.location.href), n.query.appid && o.default.setAppid(n.query.appid), n.query.vi && o.default.setVid(n.query.vi), e.log(n), e.log("App Launch")
                }, onShow: function () {
                    e.log("App Show")
                }, onHide: function () {
                    e.log("App Hide")
                }
            };
            n.default = r
        }).call(this, t("5a52")["default"])
    }, "58cb": function (e, n, t) {
        "use strict";
        (function (e) {
            var a = t("4ea4");
            t("caad"), t("c975"), t("d3b7"), t("acd8"), t("4d63"), t("ac1f"), t("25f0"), t("5319"), t("841c"), t("1276"), t("498a"), Object.defineProperty(n, "__esModule", {value: !0}), n.default = void 0;
            var o = a(t("e9d6")), r = {
                install: function (n, t) {
                    n.prototype.websiteName = "充值平台", n.prototype.timeFormat = function (e) {
                        var n = arguments[1] ? arguments[1] : "yyyy-MM-dd hh:mm:ss";
                        if (!e) return "";
                        var t = new Date;
                        10 == e.toString().length ? t.setTime(1e3 * e) : t.setTime(e);
                        var a = {
                            "M+": t.getMonth() + 1,
                            "d+": t.getDate(),
                            "h+": t.getHours(),
                            "m+": t.getMinutes(),
                            "s+": t.getSeconds(),
                            "q+": Math.floor((t.getMonth() + 3) / 3),
                            S: t.getMilliseconds()
                        };
                        for (var o in /(y+)/.test(n) && (n = n.replace(RegExp.$1, (t.getFullYear() + "").substr(4 - RegExp.$1.length))), a) new RegExp("(" + o + ")").test(n) && (n = n.replace(RegExp.$1, 1 == RegExp.$1.length ? a[o] : ("00" + a[o]).substr(("" + a[o]).length)));
                        return n
                    }, n.prototype.strToTime = function (e) {
                        e = e.substring(0, 19), e = e.replace(/-/g, "/");
                        var n = new Date(e).getTime() / 1e3;
                        return isNaN(n) ? 0 : n
                    }, n.prototype.timestamp = function (e) {
                        return (new Date).getTime() / 1e3
                    }, n.prototype.GetUrlParame = function (n) {
                        var t = window.location.search;
                        if (e.log(t), t.indexOf(n) > -1) {
                            var a = "";
                            return a = t.substring(t.indexOf(n), t.length), a.indexOf("&") > -1 ? (a = a.substring(0, a.indexOf("&")), a = a.replace(n + "=", ""), a) : ""
                        }
                    }, n.prototype.toast = function (e) {
                        uni.showToast({title: e, icon: "none", duration: 2e3})
                    }, n.prototype.showLoading = function (e) {
                        uni.showLoading({title: "加载中"})
                    }, n.prototype.navigateTo = function (e) {
                        uni.navigateTo({url: e})
                    }, n.prototype.openH5Url = function (e) {
                        e && (window.location.href = e)
                    }, n.prototype.moneyFloat = function (e) {
                        e = parseFloat(e);
                        isNaN(e) && (e = 0), e = Math.round(100 * e) / 100;
                        var n = e.toString().split(".");
                        return 1 == n.length ? (e = e.toString() + ".00", e) : n.length > 1 ? (n[1].length < 2 && (e = e.toString() + "0"), e) : void 0
                    }, n.prototype.mobileFormat = function (e) {
                        var n = e.replace(/\s/g, "").replace(/(\d{3})(\d{0,4})(\d{0,4})/, "$1 $2 $3").replace(/\s+/g, " ");
                        return [3, 7].includes(e.replace(/\s/g, "").length) || (n = n.trim()), n
                    }, n.prototype.requestPayment = function (e, t) {
                        o.default.chooseWXPay({
                            timestamp: e.timeStamp + "",
                            nonceStr: e.nonceStr,
                            package: e.package,
                            signType: e.signType,
                            paySign: e.sign,
                            success: function (e) {
                                "chooseWXPay:ok" == e.errMsg ? t({status: !0}) : t({status: !1})
                            },
                            cancel: function (a) {
                                uni.showModal({
                                    title: "提示",
                                    content: "您确定要取消支付？",
                                    cancelText: "取消支付",
                                    confirmText: "继续支付",
                                    success: function (a) {
                                        a.confirm ? n.prototype.requestPayment(e, t) : a.cancel && (uni.showToast({
                                            title: "已取消支付",
                                            icon: "none"
                                        }), t({status: !1}))
                                    }
                                })
                            },
                            fail: function (e) {
                                t({status: !1})
                            }
                        })
                    }
                }
            };
            n.default = r
        }).call(this, t("5a52")["default"])
    }, 6059: function (e, n, t) {
        var a = t("dc8b");
        "string" === typeof a && (a = [[e.i, a, ""]]), a.locals && (e.exports = a.locals);
        var o = t("4f06").default;
        o("38e7b675", a, !0, {sourceMap: !1, shadowMode: !1})
    }, "63e3": function (e, n, t) {
        var a = t("8feb");
        "string" === typeof a && (a = [[e.i, a, ""]]), a.locals && (e.exports = a.locals);
        var o = t("4f06").default;
        o("55ed66d8", a, !0, {sourceMap: !1, shadowMode: !1})
    }, "665c": function (e, n, t) {
        var a = t("24fb");
        n = a(!1), n.push([e.i, ".login-boxs[data-v-12445a6e]{width:%?500?%;background-color:#fff;padding-top:%?30?%;border-radius:%?12?%;min-height:%?20?%;padding-bottom:%?30?%;display:flex;flex-direction:column;align-items:center;justify-content:flex-start}.login-boxs .title[data-v-12445a6e]{font-size:%?32?%;width:%?400?%;text-align:center;margin-top:%?20?%;font-weight:600}.login-boxs .content[data-v-12445a6e]{font-size:%?28?%;width:%?400?%;margin-top:%?30?%}.login-boxs .btns[data-v-12445a6e]{display:flex;flex-direction:row;justify-content:space-between;width:%?420?%;margin-top:%?50?%}.login-boxs .btns .btn[data-v-12445a6e]{width:%?180?%;height:%?50?%;text-align:center;line-height:%?50?%;color:#0d89eb;font-size:%?26?%;border-radius:%?25?%;box-shadow:0 0 0 1px #46a3ec}.login-boxs .btns .activebt[data-v-12445a6e]{box-shadow:0 0 0 0 #46a3ec;background:linear-gradient(270deg,#46a3ec,#0d89eb);color:#fff}", ""]), e.exports = n
    }, 6920: function (e, n, t) {
        "use strict";
        var a = t("63e3"), o = t.n(a);
        o.a
    }, "6a3e": function (e, n, t) {
        "use strict";
        t.d(n, "b", (function () {
            return o
        })), t.d(n, "c", (function () {
            return r
        })), t.d(n, "a", (function () {
            return a
        }));
        var a = {uniTransition: t("198f").default}, o = function () {
            var e = this, n = e.$createElement, t = e._self._c || n;
            return e.showPopup ? t("v-uni-view", {
                staticClass: "uni-popup", on: {
                    touchmove: function (n) {
                        n.stopPropagation(), n.preventDefault(), arguments[0] = n = e.$handleEvent(n), e.clear.apply(void 0, arguments)
                    }
                }
            }, [t("uni-transition", {
                attrs: {
                    "mode-class": ["fade"],
                    styles: e.maskClass,
                    duration: e.duration,
                    show: e.showTrans
                }, on: {
                    click: function (n) {
                        arguments[0] = n = e.$handleEvent(n), e.onTap.apply(void 0, arguments)
                    }
                }
            }), t("uni-transition", {
                attrs: {
                    "mode-class": e.ani,
                    styles: e.transClass,
                    duration: e.duration,
                    show: e.showTrans
                }, on: {
                    click: function (n) {
                        arguments[0] = n = e.$handleEvent(n), e.onTap.apply(void 0, arguments)
                    }
                }
            }, [t("v-uni-view", {
                staticClass: "uni-popup__wrapper-box", on: {
                    click: function (n) {
                        n.stopPropagation(), arguments[0] = n = e.$handleEvent(n), e.clear.apply(void 0, arguments)
                    }
                }
            }, [e._t("default")], 2)], 1)], 1) : e._e()
        }, r = []
    }, "6d54": function (e, n, t) {
        "use strict";
        var a = t("49b6"), o = t.n(a);
        o.a
    }, "733c": function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("a0f7"), o = t.n(a);
        for (var r in a) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return a[e]
            }))
        }(r);
        n["default"] = o.a
    }, "7a86": function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("0c78"), o = t("e673");
        for (var r in o) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return o[e]
            }))
        }(r);
        t("6d54");
        var i, s = t("f0c5"), c = Object(s["a"])(o["default"], a["b"], a["c"], !1, null, null, null, !1, a["a"], i);
        n["default"] = c.exports
    }, 8588: function (e, n, t) {
        "use strict";
        (function (e) {
            var n = t("4ea4");
            t("13d5"), t("d3b7"), t("ac1f"), t("5319"), t("ddb0");
            var a = n(t("e143")), o = {
                keys: function () {
                    return []
                }
            };
            e["____8A89990____"] = !0, delete e["____8A89990____"], e.__uniConfig = {
                tabBar: {
                    color: "#7A7E83",
                    selectedColor: "#0d89eb",
                    backgroundColor: "#ffffff",
                    borderStyle: "black",
                    list: [{
                        pagePath: "pages/index/index",
                        iconPath: "static/tabbar/tab_icon11.png",
                        selectedIconPath: "static/tabbar/tab_icon10.png",
                        text: "充值",
                        redDot: !1,
                        badge: ""
                    }, {
                        pagePath: "pages/my/my",
                        iconPath: "static/tabbar/tab_icon21.png",
                        selectedIconPath: "static/tabbar/tab_icon20.png",
                        text: "我的",
                        redDot: !1,
                        badge: ""
                    }],
                    position: "bottom"
                },
                globalStyle: {
                    navigationBarTextStyle: "black",
                    navigationBarTitleText: "uni-app",
                    navigationBarBackgroundColor: "#F8F8F8",
                    backgroundColor: "#F8F8F8"
                }
            }, e.__uniConfig.compilerVersion = "3.2.9", e.__uniConfig.router = {
                mode: "hash",
                base: "/"
            }, e.__uniConfig.publicPath = "/", e.__uniConfig["async"] = {
                loading: "AsyncLoading",
                error: "AsyncError",
                delay: 200,
                timeout: 6e4
            }, e.__uniConfig.debug = !1, e.__uniConfig.networkTimeout = {
                request: 6e4,
                connectSocket: 6e4,
                uploadFile: 6e4,
                downloadFile: 6e4
            }, e.__uniConfig.sdkConfigs = {}, e.__uniConfig.qqMapKey = "XVXBZ-NDMC4-JOGUS-XGIEE-QVHDZ-AMFV2", e.__uniConfig.locale = "", e.__uniConfig.fallbackLocale = void 0, e.__uniConfig.locales = o.keys().reduce((function (e, n) {
                var t = n.replace(/\.\/(uni-app.)?(.*).json/, "$2"), a = o(n);
                return Object.assign(e[t] || (e[t] = {}), a.common || a), e
            }), {}), e.__uniConfig.nvue = {"flex-direction": "column"}, e.__uniConfig.__webpack_chunk_load__ = t.e, a.default.component("pages-index-index", (function (e) {
                var n = {
                    component: t.e("pages-index-index").then(function () {
                        return e(t("55df"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-login-oauth", (function (e) {
                var n = {
                    component: t.e("pages-login-oauth").then(function () {
                        return e(t("470d"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-login-loginpwd", (function (e) {
                var n = {
                    component: t.e("pages-login-loginpwd").then(function () {
                        return e(t("398d"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-login-h5reg", (function (e) {
                var n = {
                    component: t.e("pages-login-h5reg").then(function () {
                        return e(t("4c6b"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-my-my", (function (e) {
                var n = {
                    component: t.e("pages-my-my").then(function () {
                        return e(t("ae63"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-my-apply", (function (e) {
                var n = {
                    component: t.e("pages-my-apply").then(function () {
                        return e(t("b5d3"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-index-record", (function (e) {
                var n = {
                    component: Promise.all([t.e("pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record"), t.e("pages-index-record")]).then(function () {
                        return e(t("ca6c"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-other-helps", (function (e) {
                var n = {
                    component: t.e("pages-other-helps").then(function () {
                        return e(t("6f04"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-balance", (function (e) {
                var n = {
                    component: t.e("pages-agent-balance").then(function () {
                        return e(t("1113"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-balancelog", (function (e) {
                var n = {
                    component: Promise.all([t.e("pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record"), t.e("pages-agent-balancelog")]).then(function () {
                        return e(t("2b16"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-yaoqinlog", (function (e) {
                var n = {
                    component: Promise.all([t.e("pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record"), t.e("pages-agent-yaoqinlog")]).then(function () {
                        return e(t("d52f"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-poster", (function (e) {
                var n = {
                    component: t.e("pages-agent-poster").then(function () {
                        return e(t("c4bf"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-haibao", (function (e) {
                var n = {
                    component: t.e("pages-agent-haibao").then(function () {
                        return e(t("e57e"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-index", (function (e) {
                var n = {
                    component: t.e("pages-agent-index").then(function () {
                        return e(t("767a"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-other-doc", (function (e) {
                var n = {
                    component: t.e("pages-other-doc").then(function () {
                        return e(t("c1fe"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-other-tagline", (function (e) {
                var n = {
                    component: t.e("pages-other-tagline").then(function () {
                        return e(t("21c0"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-links", (function (e) {
                var n = {
                    component: t.e("pages-agent-links").then(function () {
                        return e(t("7d4c"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-tixianlog", (function (e) {
                var n = {
                    component: Promise.all([t.e("pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record"), t.e("pages-agent-tixianlog")]).then(function () {
                        return e(t("90d6"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-rebateorder", (function (e) {
                var n = {
                    component: Promise.all([t.e("pages-agent-balancelog~pages-agent-rebateorder~pages-agent-tixianlog~pages-agent-yaoqinlog~pages-index-record"), t.e("pages-agent-rebateorder")]).then(function () {
                        return e(t("258a"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-agent-lirun", (function (e) {
                var n = {
                    component: t.e("pages-agent-lirun").then(function () {
                        return e(t("bdc5"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), a.default.component("pages-other-city", (function (e) {
                var n = {
                    component: t.e("pages-other-city").then(function () {
                        return e(t("a606"))
                    }.bind(null, t)).catch(t.oe),
                    delay: __uniConfig["async"].delay,
                    timeout: __uniConfig["async"].timeout
                };
                return __uniConfig["async"]["loading"] && (n.loading = {
                    name: "SystemAsyncLoading",
                    render: function (e) {
                        return e(__uniConfig["async"]["loading"])
                    }
                }), __uniConfig["async"]["error"] && (n.error = {
                    name: "SystemAsyncError", render: function (e) {
                        return e(__uniConfig["async"]["error"])
                    }
                }), n
            })), e.__uniRoutes = [{
                path: "/",
                alias: "/pages/index/index",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({
                                isQuit: !0,
                                isEntry: !0,
                                isTabBar: !0,
                                tabBarIndex: 0
                            }, __uniConfig.globalStyle, {navigationBarTitleText: "充值", navigationStyle: "custom"})
                        }, [e("pages-index-index", {slot: "page"})])
                    }
                },
                meta: {
                    id: 1,
                    name: "pages-index-index",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/index/index",
                    isQuit: !0,
                    isEntry: !0,
                    isTabBar: !0,
                    tabBarIndex: 0,
                    windowTop: 0
                }
            }, {
                path: "/pages/login/oauth",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "微信授权中",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-login-oauth", {slot: "page"})])
                    }
                },
                meta: {name: "pages-login-oauth", isNVue: !1, maxWidth: 0, pagePath: "pages/login/oauth", windowTop: 0}
            }, {
                path: "/pages/login/loginpwd",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "登录",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-login-loginpwd", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-login-loginpwd",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/login/loginpwd",
                    windowTop: 0
                }
            }, {
                path: "/pages/login/h5reg",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "注册",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-login-h5reg", {slot: "page"})])
                    }
                },
                meta: {name: "pages-login-h5reg", isNVue: !1, maxWidth: 0, pagePath: "pages/login/h5reg", windowTop: 0}
            }, {
                path: "/pages/my/my",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({
                                isQuit: !0,
                                isTabBar: !0,
                                tabBarIndex: 1
                            }, __uniConfig.globalStyle, {navigationBarTitleText: "我的", navigationStyle: "custom"})
                        }, [e("pages-my-my", {slot: "page"})])
                    }
                },
                meta: {
                    id: 2,
                    name: "pages-my-my",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/my/my",
                    isQuit: !0,
                    isTabBar: !0,
                    tabBarIndex: 1,
                    windowTop: 0
                }
            }, {
                path: "/pages/my/apply", component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "升级代理",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-my-apply", {slot: "page"})])
                    }
                }, meta: {name: "pages-my-apply", isNVue: !1, maxWidth: 0, pagePath: "pages/my/apply", windowTop: 0}
            }, {
                path: "/pages/index/record",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "充值记录",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-index-record", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-index-record",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/index/record",
                    windowTop: 0
                }
            }, {
                path: "/pages/other/helps",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "客服帮助",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-other-helps", {slot: "page"})])
                    }
                },
                meta: {name: "pages-other-helps", isNVue: !1, maxWidth: 0, pagePath: "pages/other/helps", windowTop: 0}
            }, {
                path: "/pages/agent/balance",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "余额",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-balance", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-agent-balance",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/agent/balance",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/balancelog",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "余额日志",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-balancelog", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-agent-balancelog",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/agent/balancelog",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/yaoqinlog",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "邀请记录",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-yaoqinlog", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-agent-yaoqinlog",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/agent/yaoqinlog",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/poster",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "二维码",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-poster", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-agent-poster",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/agent/poster",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/haibao",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "二维码",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-haibao", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-agent-haibao",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/agent/haibao",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/index",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "店铺",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-index", {slot: "page"})])
                    }
                },
                meta: {name: "pages-agent-index", isNVue: !1, maxWidth: 0, pagePath: "pages/agent/index", windowTop: 0}
            }, {
                path: "/pages/other/doc", component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "文档",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-other-doc", {slot: "page"})])
                    }
                }, meta: {name: "pages-other-doc", isNVue: !1, maxWidth: 0, pagePath: "pages/other/doc", windowTop: 0}
            }, {
                path: "/pages/other/tagline",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "宣传语",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-other-tagline", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-other-tagline",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/other/tagline",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/links",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "推广链接",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-links", {slot: "page"})])
                    }
                },
                meta: {name: "pages-agent-links", isNVue: !1, maxWidth: 0, pagePath: "pages/agent/links", windowTop: 0}
            }, {
                path: "/pages/agent/tixianlog",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "提现记录",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-tixianlog", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-agent-tixianlog",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/agent/tixianlog",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/rebateorder",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "返利订单",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-rebateorder", {slot: "page"})])
                    }
                },
                meta: {
                    name: "pages-agent-rebateorder",
                    isNVue: !1,
                    maxWidth: 0,
                    pagePath: "pages/agent/rebateorder",
                    windowTop: 0
                }
            }, {
                path: "/pages/agent/lirun",
                component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "利润设置",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-agent-lirun", {slot: "page"})])
                    }
                },
                meta: {name: "pages-agent-lirun", isNVue: !1, maxWidth: 0, pagePath: "pages/agent/lirun", windowTop: 0}
            }, {
                path: "/pages/other/city", component: {
                    render: function (e) {
                        return e("Page", {
                            props: Object.assign({}, __uniConfig.globalStyle, {
                                navigationBarTitleText: "城市",
                                navigationStyle: "custom"
                            })
                        }, [e("pages-other-city", {slot: "page"})])
                    }
                }, meta: {name: "pages-other-city", isNVue: !1, maxWidth: 0, pagePath: "pages/other/city", windowTop: 0}
            }, {
                path: "/preview-image", component: {
                    render: function (e) {
                        return e("Page", {props: {navigationStyle: "custom"}}, [e("system-preview-image", {slot: "page"})])
                    }
                }, meta: {name: "preview-image", pagePath: "/preview-image"}
            }, {
                path: "/choose-location", component: {
                    render: function (e) {
                        return e("Page", {props: {navigationStyle: "custom"}}, [e("system-choose-location", {slot: "page"})])
                    }
                }, meta: {name: "choose-location", pagePath: "/choose-location"}
            }, {
                path: "/open-location", component: {
                    render: function (e) {
                        return e("Page", {props: {navigationStyle: "custom"}}, [e("system-open-location", {slot: "page"})])
                    }
                }, meta: {name: "open-location", pagePath: "/open-location"}
            }], e.UniApp && new e.UniApp
        }).call(this, t("c8ba"))
    }, "8b5b": function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("d8c5"), o = t("f024");
        for (var r in o) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return o[e]
            }))
        }(r);
        t("0553");
        var i, s = t("f0c5"),
            c = Object(s["a"])(o["default"], a["b"], a["c"], !1, null, "12445a6e", null, !1, a["a"], i);
        n["default"] = c.exports
    }, "8feb": function (e, n, t) {
        var a = t("24fb");
        n = a(!1), n.push([e.i, '@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.uni-popup[data-v-64cecef0]{position:fixed;top:var(--window-top);bottom:0;left:0;right:0;z-index:99}.uni-popup__mask[data-v-64cecef0]{position:absolute;top:0;bottom:0;left:0;right:0;background-color:rgba(0,0,0,.4);opacity:0}.mask-ani[data-v-64cecef0]{transition-property:opacity;transition-duration:.2s}.uni-top-mask[data-v-64cecef0]{opacity:1}.uni-bottom-mask[data-v-64cecef0]{opacity:1}.uni-center-mask[data-v-64cecef0]{opacity:1}.uni-popup__wrapper[data-v-64cecef0]{display:block;position:absolute}.top[data-v-64cecef0]{top:0;left:0;right:0;-webkit-transform:translateY(-500px);transform:translateY(-500px)}.bottom[data-v-64cecef0]{bottom:0;left:0;right:0;-webkit-transform:translateY(500px);transform:translateY(500px)}.center[data-v-64cecef0]{display:flex;flex-direction:column;bottom:0;left:0;right:0;top:0;justify-content:center;align-items:center;-webkit-transform:scale(1.2);transform:scale(1.2);opacity:0}.uni-popup__wrapper-box[data-v-64cecef0]{display:block;position:relative}.content-ani[data-v-64cecef0]{transition-property:opacity,-webkit-transform;transition-property:transform,opacity;transition-property:transform,opacity,-webkit-transform;transition-duration:.2s}.uni-top-content[data-v-64cecef0]{-webkit-transform:translateY(0);transform:translateY(0)}.uni-bottom-content[data-v-64cecef0]{-webkit-transform:translateY(0);transform:translateY(0)}.uni-center-content[data-v-64cecef0]{-webkit-transform:scale(1);transform:scale(1);opacity:1}', ""]), e.exports = n
    }, "927b": function (e, n, t) {
        "use strict";
        var a = t("6059"), o = t.n(a);
        o.a
    }, "9a0f": function (e, n, t) {
        var a = t("665c");
        "string" === typeof a && (a = [[e.i, a, ""]]), a.locals && (e.exports = a.locals);
        var o = t("4f06").default;
        o("4ea4174e", a, !0, {sourceMap: !1, shadowMode: !1})
    }, a0a2: function (e, n, t) {
        "use strict";
        var a = t("4ea4");
        t("4160"), t("a9e3"), t("ac1f"), t("5319"), t("159b"), Object.defineProperty(n, "__esModule", {value: !0}), n.default = void 0;
        var o = a(t("5530")), r = {
            name: "uniTransition",
            props: {
                show: {type: Boolean, default: !1}, modeClass: {
                    type: Array, default: function () {
                        return []
                    }
                }, duration: {type: Number, default: 300}, styles: {
                    type: Object, default: function () {
                        return {}
                    }
                }
            },
            data: function () {
                return {isShow: !1, transform: "", ani: {in: "", active: ""}}
            },
            watch: {
                show: {
                    handler: function (e) {
                        e ? this.open() : this.close()
                    }, immediate: !0
                }
            },
            computed: {
                stylesObject: function () {
                    var e = (0, o.default)((0, o.default)({}, this.styles), {}, {"transition-duration": this.duration / 1e3 + "s"}),
                        n = "";
                    for (var t in e) {
                        var a = this.toLine(t);
                        n += a + ":" + e[t] + ";"
                    }
                    return n
                }
            },
            created: function () {
            },
            methods: {
                change: function () {
                    this.$emit("click", {detail: this.isShow})
                }, open: function () {
                    var e = this;
                    for (var n in clearTimeout(this.timer), this.isShow = !0, this.transform = "", this.ani.in = "", this.getTranfrom(!1)) "opacity" === n ? this.ani.in = "fade-in" : this.transform += "".concat(this.getTranfrom(!1)[n], " ");
                    this.$nextTick((function () {
                        setTimeout((function () {
                            e._animation(!0)
                        }), 50)
                    }))
                }, close: function (e) {
                    clearTimeout(this.timer), this._animation(!1)
                }, _animation: function (e) {
                    var n = this, t = this.getTranfrom(e);
                    for (var a in this.transform = "", t) "opacity" === a ? this.ani.in = "fade-".concat(e ? "out" : "in") : this.transform += "".concat(t[a], " ");
                    this.timer = setTimeout((function () {
                        e || (n.isShow = !1), n.$emit("change", {detail: n.isShow})
                    }), this.duration)
                }, getTranfrom: function (e) {
                    var n = {transform: ""};
                    return this.modeClass.forEach((function (t) {
                        switch (t) {
                            case"fade":
                                n.opacity = e ? 1 : 0;
                                break;
                            case"slide-top":
                                n.transform += "translateY(".concat(e ? "0" : "-100%", ") ");
                                break;
                            case"slide-right":
                                n.transform += "translateX(".concat(e ? "0" : "100%", ") ");
                                break;
                            case"slide-bottom":
                                n.transform += "translateY(".concat(e ? "0" : "100%", ") ");
                                break;
                            case"slide-left":
                                n.transform += "translateX(".concat(e ? "0" : "-100%", ") ");
                                break;
                            case"zoom-in":
                                n.transform += "scale(".concat(e ? 1 : .8, ") ");
                                break;
                            case"zoom-out":
                                n.transform += "scale(".concat(e ? 1 : 1.2, ") ");
                                break
                        }
                    })), n
                }, _modeClassArr: function (e) {
                    var n = this.modeClass;
                    if ("string" !== typeof n) {
                        var t = "";
                        return n.forEach((function (n) {
                            t += n + "-" + e + ","
                        })), t.substr(0, t.length - 1)
                    }
                    return n + "-" + e
                }, toLine: function (e) {
                    return e.replace(/([A-Z])/g, "-$1").toLowerCase()
                }
            }
        };
        n.default = r
    }, a0f1: function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("a0a2"), o = t.n(a);
        for (var r in a) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return a[e]
            }))
        }(r);
        n["default"] = o.a
    }, a0f7: function (e, n, t) {
        "use strict";
        var a = t("4ea4");
        Object.defineProperty(n, "__esModule", {value: !0}), n.default = void 0;
        var o = a(t("198f")), r = {
            name: "UniPopup",
            components: {uniTransition: o.default},
            props: {
                animation: {type: Boolean, default: !0},
                type: {type: String, default: "center"},
                maskClick: {type: Boolean, default: !0}
            },
            data: function () {
                return {
                    duration: 300,
                    ani: [],
                    showPopup: !1,
                    showTrans: !1,
                    maskClass: {
                        position: "fixed",
                        bottom: 0,
                        top: 0,
                        left: 0,
                        right: 0,
                        backgroundColor: "rgba(0, 0, 0, 0.4)"
                    },
                    transClass: {position: "fixed", left: 0, right: 0}
                }
            },
            watch: {
                type: {
                    handler: function (e) {
                        switch (this.type) {
                            case"top":
                                this.ani = ["slide-top"], this.transClass = {position: "fixed", left: 0, right: 0};
                                break;
                            case"bottom":
                                this.ani = ["slide-bottom"], this.transClass = {
                                    position: "fixed",
                                    left: 0,
                                    right: 0,
                                    bottom: 0
                                };
                                break;
                            case"center":
                                this.ani = ["zoom-out", "fade"], this.transClass = {
                                    position: "fixed",
                                    display: "flex",
                                    flexDirection: "column",
                                    bottom: 0,
                                    left: 0,
                                    right: 0,
                                    top: 0,
                                    justifyContent: "center",
                                    alignItems: "center"
                                };
                                break
                        }
                    }, immediate: !0
                }
            },
            created: function () {
                this.animation ? this.duration = 300 : this.duration = 0
            },
            methods: {
                clear: function (e) {
                    e.stopPropagation()
                }, open: function () {
                    var e = this;
                    this.showPopup = !0, this.$nextTick((function () {
                        clearTimeout(e.timer), e.timer = setTimeout((function () {
                            e.showTrans = !0
                        }), 50), setTimeout((function () {
                            e.$emit("transed", {show: !0})
                        }), e.duration)
                    })), this.$emit("change", {show: !0})
                }, close: function (e) {
                    var n = this;
                    this.showTrans = !1, this.$nextTick((function () {
                        clearTimeout(n.timer), n.timer = setTimeout((function () {
                            n.$emit("change", {show: !1}), n.showPopup = !1, setTimeout((function () {
                                n.$emit("transed", {show: !1})
                            }), n.duration)
                        }), 300)
                    }))
                }, onTap: function () {
                    this.maskClick && this.close()
                }
            }
        };
        n.default = r
    }, ad09: function (e, n, t) {
        "use strict";
        var a = t("4ea4");
        t("c975");
        var o = a(t("b606")), r = "https://recharge2.dayuanren.cn/api.php/", i = "wx6019ca946aa1c2d2", s = function () {
            var e = r;
            return e = document.location.protocol + "//" + window.location.hostname + "/api.php/", e
        }, c = function (e) {
            return uni.setStorageSync("wx_appid", e), e
        }, u = function () {
            var e = uni.getStorageSync("wx_appid") || i;
            return e
        }, l = function (e) {
            return uni.setStorageSync("token_" + u(), e), e
        }, p = function (e) {
            var n = uni.getStorageSync("token_" + u()) || "";
            return n
        }, g = function (e) {
            return !(!uni.getStorageSync("userinfo") || !uni.getStorageSync("token_" + u()))
        }, d = function (e) {
            return uni.setStorageSync("vid_" + u(), e), e
        }, f = function (e) {
            var n = uni.getStorageSync("vid_" + u()) || "";
            return n
        }, m = function () {
            return y() ? 1 : 2
        }, y = function () {
            var e = navigator.userAgent.toLowerCase(), n = -1 != e.indexOf("micromessenger");
            return !!n
        }, h = function () {
            return "https:" == document.location.protocol ? "https" : "http"
        }, v = function (e) {
            return uni.setStorageSync("share_title", e), !0
        }, _ = function (e) {
            var n = uni.getStorageSync("userinfo") ? JSON.parse(uni.getStorageSync("userinfo")) : {}, t = {};
            n && (t["vi"] = n.id);
            var a = "/pages/index/index?" + o.default.stringify(t), r = uni.getStorageSync("share_title") || "";
            return {title: r, path: a}
        }, b = function (e) {
            var n = uni.getStorageSync("userinfo") ? JSON.parse(uni.getStorageSync("userinfo")) : {}, t = {};
            n && (t["vi"] = n.id);
            var a = "/pages/index/index?" + o.default.stringify(t), r = uni.getStorageSync("share_title") || "";
            return {title: r, query: a}
        };
        e.exports = {
            getApiurl: s,
            setAppid: c,
            getAppid: u,
            setToken: l,
            getToken: p,
            islogin: g,
            setVid: d,
            getVid: f,
            getClientType: m,
            isWeixinH5: y,
            getHttpProtocol: h,
            setShareData: v,
            getShareAppMessage: _,
            getShareTimeline: b
        }
    }, b606: function (e, n, t) {
        var a;
        t("99af"), t("4de4"), t("4160"), t("c975"), t("13d5"), t("fb6a"), t("4e82"), t("b64b"), t("d3b7"), t("e25e"), t("ac1f"), t("25f0"), t("5319"), t("1276"), t("159b"), function (n) {
            e.exports = n()
        }((function () {
            return function () {
                function e(n, t, o) {
                    function r(s, c) {
                        if (!t[s]) {
                            if (!n[s]) {
                                var u = "function" == typeof a && a;
                                if (!c && u) return a(s, !0);
                                if (i) return i(s, !0);
                                var l = new Error("Cannot find module '" + s + "'");
                                throw l.code = "MODULE_NOT_FOUND", l
                            }
                            var p = t[s] = {exports: {}};
                            n[s][0].call(p.exports, (function (e) {
                                var t = n[s][1][e];
                                return r(t || e)
                            }), p, p.exports, e, n, t, o)
                        }
                        return t[s].exports
                    }

                    for (var i = "function" == typeof a && a, s = 0; s < o.length; s++) r(o[s]);
                    return r
                }

                return e
            }()({
                1: [function (e, n, t) {
                    "use strict";
                    var a = String.prototype.replace, o = /%20/g;
                    n.exports = {
                        default: "RFC3986", formatters: {
                            RFC1738: function (e) {
                                return a.call(e, o, "+")
                            }, RFC3986: function (e) {
                                return e
                            }
                        }, RFC1738: "RFC1738", RFC3986: "RFC3986"
                    }
                }, {}], 2: [function (e, n, t) {
                    "use strict";
                    var a = e("./stringify"), o = e("./parse"), r = e("./formats");
                    n.exports = {formats: r, parse: o, stringify: a}
                }, {"./formats": 1, "./parse": 3, "./stringify": 4}], 3: [function (e, n, t) {
                    "use strict";
                    var a = e("./utils"), o = Object.prototype.hasOwnProperty, r = {
                        allowDots: !1,
                        allowPrototypes: !1,
                        arrayLimit: 20,
                        decoder: a.decode,
                        delimiter: "&",
                        depth: 5,
                        parameterLimit: 1e3,
                        plainObjects: !1,
                        strictNullHandling: !1
                    }, i = function (e, n) {
                        for (var t = {}, a = n.ignoreQueryPrefix ? e.replace(/^\?/, "") : e, i = n.parameterLimit === 1 / 0 ? void 0 : n.parameterLimit, s = a.split(n.delimiter, i), c = 0; c < s.length; ++c) {
                            var u, l, p = s[c], g = p.indexOf("]="), d = -1 === g ? p.indexOf("=") : g + 1;
                            -1 === d ? (u = n.decoder(p, r.decoder), l = n.strictNullHandling ? null : "") : (u = n.decoder(p.slice(0, d), r.decoder), l = n.decoder(p.slice(d + 1), r.decoder)), o.call(t, u) ? t[u] = [].concat(t[u]).concat(l) : t[u] = l
                        }
                        return t
                    }, s = function (e, n, t) {
                        for (var a = n, o = e.length - 1; o >= 0; --o) {
                            var r, i = e[o];
                            if ("[]" === i) r = [], r = r.concat(a); else {
                                r = t.plainObjects ? Object.create(null) : {};
                                var s = "[" === i.charAt(0) && "]" === i.charAt(i.length - 1) ? i.slice(1, -1) : i,
                                    c = parseInt(s, 10);
                                !isNaN(c) && i !== s && String(c) === s && c >= 0 && t.parseArrays && c <= t.arrayLimit ? (r = [], r[c] = a) : r[s] = a
                            }
                            a = r
                        }
                        return a
                    }, c = function (e, n, t) {
                        if (e) {
                            var a = t.allowDots ? e.replace(/\.([^.[]+)/g, "[$1]") : e, r = /(\[[^[\]]*])/,
                                i = /(\[[^[\]]*])/g, c = r.exec(a), u = c ? a.slice(0, c.index) : a, l = [];
                            if (u) {
                                if (!t.plainObjects && o.call(Object.prototype, u) && !t.allowPrototypes) return;
                                l.push(u)
                            }
                            var p = 0;
                            while (null !== (c = i.exec(a)) && p < t.depth) {
                                if (p += 1, !t.plainObjects && o.call(Object.prototype, c[1].slice(1, -1)) && !t.allowPrototypes) return;
                                l.push(c[1])
                            }
                            return c && l.push("[" + a.slice(c.index) + "]"), s(l, n, t)
                        }
                    };
                    n.exports = function (e, n) {
                        var t = n ? a.assign({}, n) : {};
                        if (null !== t.decoder && void 0 !== t.decoder && "function" !== typeof t.decoder) throw new TypeError("Decoder has to be a function.");
                        if (t.ignoreQueryPrefix = !0 === t.ignoreQueryPrefix, t.delimiter = "string" === typeof t.delimiter || a.isRegExp(t.delimiter) ? t.delimiter : r.delimiter, t.depth = "number" === typeof t.depth ? t.depth : r.depth, t.arrayLimit = "number" === typeof t.arrayLimit ? t.arrayLimit : r.arrayLimit, t.parseArrays = !1 !== t.parseArrays, t.decoder = "function" === typeof t.decoder ? t.decoder : r.decoder, t.allowDots = "boolean" === typeof t.allowDots ? t.allowDots : r.allowDots, t.plainObjects = "boolean" === typeof t.plainObjects ? t.plainObjects : r.plainObjects, t.allowPrototypes = "boolean" === typeof t.allowPrototypes ? t.allowPrototypes : r.allowPrototypes, t.parameterLimit = "number" === typeof t.parameterLimit ? t.parameterLimit : r.parameterLimit, t.strictNullHandling = "boolean" === typeof t.strictNullHandling ? t.strictNullHandling : r.strictNullHandling, "" === e || null === e || "undefined" === typeof e) return t.plainObjects ? Object.create(null) : {};
                        for (var o = "string" === typeof e ? i(e, t) : e, s = t.plainObjects ? Object.create(null) : {}, u = Object.keys(o), l = 0; l < u.length; ++l) {
                            var p = u[l], g = c(p, o[p], t);
                            s = a.merge(s, g, t)
                        }
                        return a.compact(s)
                    }
                }, {"./utils": 5}], 4: [function (e, n, t) {
                    "use strict";
                    var a = e("./utils"), o = e("./formats"), r = {
                        brackets: function (e) {
                            return e + "[]"
                        }, indices: function (e, n) {
                            return e + "[" + n + "]"
                        }, repeat: function (e) {
                            return e
                        }
                    }, i = Date.prototype.toISOString, s = {
                        delimiter: "&",
                        encode: !0,
                        encoder: a.encode,
                        encodeValuesOnly: !1,
                        serializeDate: function (e) {
                            return i.call(e)
                        },
                        skipNulls: !1,
                        strictNullHandling: !1
                    }, c = function e(n, t, o, r, i, c, u, l, p, g, d, f) {
                        var m = n;
                        if ("function" === typeof u) m = u(t, m); else if (m instanceof Date) m = g(m); else if (null === m) {
                            if (r) return c && !f ? c(t, s.encoder) : t;
                            m = ""
                        }
                        if ("string" === typeof m || "number" === typeof m || "boolean" === typeof m || a.isBuffer(m)) {
                            if (c) {
                                var y = f ? t : c(t, s.encoder);
                                return [d(y) + "=" + d(c(m, s.encoder))]
                            }
                            return [d(t) + "=" + d(String(m))]
                        }
                        var h, v = [];
                        if ("undefined" === typeof m) return v;
                        if (Array.isArray(u)) h = u; else {
                            var _ = Object.keys(m);
                            h = l ? _.sort(l) : _
                        }
                        for (var b = 0; b < h.length; ++b) {
                            var w = h[b];
                            i && null === m[w] || (v = Array.isArray(m) ? v.concat(e(m[w], o(t, w), o, r, i, c, u, l, p, g, d, f)) : v.concat(e(m[w], t + (p ? "." + w : "[" + w + "]"), o, r, i, c, u, l, p, g, d, f)))
                        }
                        return v
                    };
                    n.exports = function (e, n) {
                        var t = e, i = n ? a.assign({}, n) : {};
                        if (null !== i.encoder && void 0 !== i.encoder && "function" !== typeof i.encoder) throw new TypeError("Encoder has to be a function.");
                        var u = "undefined" === typeof i.delimiter ? s.delimiter : i.delimiter,
                            l = "boolean" === typeof i.strictNullHandling ? i.strictNullHandling : s.strictNullHandling,
                            p = "boolean" === typeof i.skipNulls ? i.skipNulls : s.skipNulls,
                            g = "boolean" === typeof i.encode ? i.encode : s.encode,
                            d = "function" === typeof i.encoder ? i.encoder : s.encoder,
                            f = "function" === typeof i.sort ? i.sort : null,
                            m = "undefined" !== typeof i.allowDots && i.allowDots,
                            y = "function" === typeof i.serializeDate ? i.serializeDate : s.serializeDate,
                            h = "boolean" === typeof i.encodeValuesOnly ? i.encodeValuesOnly : s.encodeValuesOnly;
                        if ("undefined" === typeof i.format) i.format = o["default"]; else if (!Object.prototype.hasOwnProperty.call(o.formatters, i.format)) throw new TypeError("Unknown format option provided.");
                        var v, _, b = o.formatters[i.format];
                        "function" === typeof i.filter ? (_ = i.filter, t = _("", t)) : Array.isArray(i.filter) && (_ = i.filter, v = _);
                        var w, S = [];
                        if ("object" !== typeof t || null === t) return "";
                        w = i.arrayFormat in r ? i.arrayFormat : "indices" in i ? i.indices ? "indices" : "repeat" : "indices";
                        var x = r[w];
                        v || (v = Object.keys(t)), f && v.sort(f);
                        for (var C = 0; C < v.length; ++C) {
                            var T = v[C];
                            p && null === t[T] || (S = S.concat(c(t[T], T, x, l, p, g ? d : null, _, f, m, y, b, h)))
                        }
                        var k = S.join(u), P = !0 === i.addQueryPrefix ? "?" : "";
                        return k.length > 0 ? P + k : ""
                    }
                }, {"./formats": 1, "./utils": 5}], 5: [function (e, n, t) {
                    "use strict";
                    var a = Object.prototype.hasOwnProperty, o = function () {
                        for (var e = [], n = 0; n < 256; ++n) e.push("%" + ((n < 16 ? "0" : "") + n.toString(16)).toUpperCase());
                        return e
                    }(), r = function (e) {
                        var n;
                        while (e.length) {
                            var t = e.pop();
                            if (n = t.obj[t.prop], Array.isArray(n)) {
                                for (var a = [], o = 0; o < n.length; ++o) "undefined" !== typeof n[o] && a.push(n[o]);
                                t.obj[t.prop] = a
                            }
                        }
                        return n
                    }, i = function (e, n) {
                        for (var t = n && n.plainObjects ? Object.create(null) : {}, a = 0; a < e.length; ++a) "undefined" !== typeof e[a] && (t[a] = e[a]);
                        return t
                    }, s = function e(n, t, o) {
                        if (!t) return n;
                        if ("object" !== typeof t) {
                            if (Array.isArray(n)) n.push(t); else {
                                if ("object" !== typeof n) return [n, t];
                                (o.plainObjects || o.allowPrototypes || !a.call(Object.prototype, t)) && (n[t] = !0)
                            }
                            return n
                        }
                        if ("object" !== typeof n) return [n].concat(t);
                        var r = n;
                        return Array.isArray(n) && !Array.isArray(t) && (r = i(n, o)), Array.isArray(n) && Array.isArray(t) ? (t.forEach((function (t, r) {
                            a.call(n, r) ? n[r] && "object" === typeof n[r] ? n[r] = e(n[r], t, o) : n.push(t) : n[r] = t
                        })), n) : Object.keys(t).reduce((function (n, r) {
                            var i = t[r];
                            return a.call(n, r) ? n[r] = e(n[r], i, o) : n[r] = i, n
                        }), r)
                    }, c = function (e, n) {
                        return Object.keys(n).reduce((function (e, t) {
                            return e[t] = n[t], e
                        }), e)
                    }, u = function (e) {
                        try {
                            return decodeURIComponent(e.replace(/\+/g, " "))
                        } catch (n) {
                            return e
                        }
                    }, l = function (e) {
                        if (0 === e.length) return e;
                        for (var n = "string" === typeof e ? e : String(e), t = "", a = 0; a < n.length; ++a) {
                            var r = n.charCodeAt(a);
                            45 === r || 46 === r || 95 === r || 126 === r || r >= 48 && r <= 57 || r >= 65 && r <= 90 || r >= 97 && r <= 122 ? t += n.charAt(a) : r < 128 ? t += o[r] : r < 2048 ? t += o[192 | r >> 6] + o[128 | 63 & r] : r < 55296 || r >= 57344 ? t += o[224 | r >> 12] + o[128 | r >> 6 & 63] + o[128 | 63 & r] : (a += 1, r = 65536 + ((1023 & r) << 10 | 1023 & n.charCodeAt(a)), t += o[240 | r >> 18] + o[128 | r >> 12 & 63] + o[128 | r >> 6 & 63] + o[128 | 63 & r])
                        }
                        return t
                    }, p = function (e) {
                        for (var n = [{
                            obj: {o: e},
                            prop: "o"
                        }], t = [], a = 0; a < n.length; ++a) for (var o = n[a], i = o.obj[o.prop], s = Object.keys(i), c = 0; c < s.length; ++c) {
                            var u = s[c], l = i[u];
                            "object" === typeof l && null !== l && -1 === t.indexOf(l) && (n.push({
                                obj: i,
                                prop: u
                            }), t.push(l))
                        }
                        return r(n)
                    }, g = function (e) {
                        return "[object RegExp]" === Object.prototype.toString.call(e)
                    }, d = function (e) {
                        return null !== e && "undefined" !== typeof e && !!(e.constructor && e.constructor.isBuffer && e.constructor.isBuffer(e))
                    };
                    n.exports = {
                        arrayToObject: i,
                        assign: c,
                        compact: p,
                        decode: u,
                        encode: l,
                        isBuffer: d,
                        isRegExp: g,
                        merge: s
                    }
                }, {}]
            }, {}, [2])(2)
        }))
    }, d065: function (e, n, t) {
        "use strict";
        var a = t("4ea4"), o = a(t("e143")), r = a(t("d5dc")), i = a(t("ad09"));
        r.default.setConfig({
            baseUrl: "",
            dataType: "json",
            responseType: "text",
            header: {"Content-Type": "application/json;charset=utf-8"}
        }), r.default.interceptors.request((function (e) {
            return e.baseUrl = i.default.getApiurl(), e.header.Authorization = i.default.getToken(), e.header.Client = i.default.getClientType(), e.header.Appid = i.default.getAppid(), e
        })), r.default.interceptors.response((function (e) {
            if ("request:fail" != e.errMsg) {
                if (-1 == e.data.errno) {
                    var n = i.default.getClientType();
                    switch (n) {
                        case 1:
                            return void uni.reLaunch({url: "/pages/login/oauth"});
                        case 2:
                            return void uni.reLaunch({url: "/pages/login/oauth"});
                        case 3:
                            return void uni.$emit("login");
                        default:
                    }
                }
                return e
            }
            uni.showToast({title: "网络开小差了", icon: "none", duration: 2e3})
        })), o.default.prototype.$request = r.default, e.exports = {request: r.default}
    }, d5dc: function (e, n, t) {
        "use strict";
        (function (e, a) {
            var o = t("4ea4");
            t("b64b"), t("d3b7"), t("25f0"), t("8a79"), t("2ca0"), Object.defineProperty(n, "__esModule", {value: !0}), n.default = void 0, t("96cf");
            var r = o(t("1da1")), i = o(t("d4ec")), s = o(t("bee2")), c = function () {
                function e() {
                    var n = this, t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {};
                    (0, i.default)(this, e), this.config = {}, this.config.baseUrl = t.baseUrl ? t.baseUrl : "", this.config.dataType = t.dataType ? t.dataType : "json", this.config.responseType = t.responseType ? t.responseType : "text", this.config.header = t.header ? t.header : {}, this.reqInterceptors = null, this.resInterceptors = null, this.interceptors = {
                        request: function (e) {
                            n.reqInterceptors = e
                        }, response: function (e) {
                            n.resInterceptors = e
                        }
                    }
                }

                return (0, s.default)(e, [{
                    key: "get", value: function () {
                        var e = (0, r.default)(regeneratorRuntime.mark((function e(n) {
                            var t, a = arguments;
                            return regeneratorRuntime.wrap((function (e) {
                                while (1) switch (e.prev = e.next) {
                                    case 0:
                                        return t = a.length > 1 && void 0 !== a[1] ? a[1] : {}, e.abrupt("return", this._request("get", n, t));
                                    case 2:
                                    case"end":
                                        return e.stop()
                                }
                            }), e, this)
                        })));

                        function n(n) {
                            return e.apply(this, arguments)
                        }

                        return n
                    }()
                }, {
                    key: "post", value: function () {
                        var e = (0, r.default)(regeneratorRuntime.mark((function e(n) {
                            var t, a = arguments;
                            return regeneratorRuntime.wrap((function (e) {
                                while (1) switch (e.prev = e.next) {
                                    case 0:
                                        return t = a.length > 1 && void 0 !== a[1] ? a[1] : {}, e.abrupt("return", this._request("post", n, t));
                                    case 2:
                                    case"end":
                                        return e.stop()
                                }
                            }), e, this)
                        })));

                        function n(n) {
                            return e.apply(this, arguments)
                        }

                        return n
                    }()
                }, {
                    key: "put", value: function () {
                        var e = (0, r.default)(regeneratorRuntime.mark((function e(n) {
                            var t, a = arguments;
                            return regeneratorRuntime.wrap((function (e) {
                                while (1) switch (e.prev = e.next) {
                                    case 0:
                                        return t = a.length > 1 && void 0 !== a[1] ? a[1] : {}, e.abrupt("return", this._request("put", n, t));
                                    case 2:
                                    case"end":
                                        return e.stop()
                                }
                            }), e, this)
                        })));

                        function n(n) {
                            return e.apply(this, arguments)
                        }

                        return n
                    }()
                }, {
                    key: "delete", value: function () {
                        var e = (0, r.default)(regeneratorRuntime.mark((function e(n) {
                            var t, a = arguments;
                            return regeneratorRuntime.wrap((function (e) {
                                while (1) switch (e.prev = e.next) {
                                    case 0:
                                        return t = a.length > 1 && void 0 !== a[1] ? a[1] : {}, e.abrupt("return", this._request("delete", n, t));
                                    case 2:
                                    case"end":
                                        return e.stop()
                                }
                            }), e, this)
                        })));

                        function n(n) {
                            return e.apply(this, arguments)
                        }

                        return n
                    }()
                }, {
                    key: "setConfig", value: function () {
                        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {};
                        this.config = this._deepCopy(this._merge(this.config, e))
                    }
                }, {
                    key: "getConfig", value: function () {
                        return this.config
                    }
                }, {
                    key: "_request", value: function (e, n, t) {
                        var a = this, o = this._deepCopy(this._merge(this.config, t)), i = {};
                        if (this.reqInterceptors && "function" === typeof this.reqInterceptors) {
                            var s = this.reqInterceptors(o);
                            if ("[object Promise]" === Object.prototype.toString.call(s)) return s;
                            i = this._deepCopy(s)
                        } else i = this._deepCopy(o);
                        var c = this._formatUrl(i.baseUrl, n);
                        return new Promise((function (n, t) {
                            uni.request({
                                url: c,
                                method: e,
                                data: i.data ? i.data : {},
                                header: i.header,
                                dataType: i.dataType,
                                responseType: i.responseType,
                                complete: function (e) {
                                    return (0, r.default)(regeneratorRuntime.mark((function o() {
                                        var r, i, s;
                                        return regeneratorRuntime.wrap((function (o) {
                                            while (1) switch (o.prev = o.next) {
                                                case 0:
                                                    if (r = e, !a.resInterceptors || "function" !== typeof a.resInterceptors) {
                                                        o.next = 22;
                                                        break
                                                    }
                                                    if (i = a.resInterceptors(r), i) {
                                                        o.next = 8;
                                                        break
                                                    }
                                                    return t("返回值已被您拦截！"), o.abrupt("return");
                                                case 8:
                                                    if ("[object Promise]" !== Object.prototype.toString.call(i)) {
                                                        o.next = 21;
                                                        break
                                                    }
                                                    return o.prev = 9, o.next = 12, i;
                                                case 12:
                                                    s = o.sent, n(s), o.next = 19;
                                                    break;
                                                case 16:
                                                    o.prev = 16, o.t0 = o["catch"](9), t(o.t0);
                                                case 19:
                                                    o.next = 22;
                                                    break;
                                                case 21:
                                                    r = i;
                                                case 22:
                                                    n(r);
                                                case 23:
                                                case"end":
                                                    return o.stop()
                                            }
                                        }), o, null, [[9, 16]])
                                    })))()
                                }
                            })
                        }))
                    }
                }, {
                    key: "_formatUrl", value: function (e, n) {
                        if (!e) return n;
                        var t = "", a = e.endsWith("/"), o = n.startsWith("/");
                        return t = a && o ? e + n.substring(1) : a || o ? e + n : e + "/" + n, t
                    }
                }, {
                    key: "_merge", value: function (e, n) {
                        var t = this._deepCopy(e);
                        if (!n || !Object.keys(n).length) return t;
                        for (var a in n) if ("header" !== a) t[a] = n[a]; else if ("[object Object]" === Object.prototype.toString.call(n[a])) for (var o in n[a]) t[a][o] = n[a][o];
                        return t
                    }
                }, {
                    key: "_deepCopy", value: function (e) {
                        var n = Array.isArray(e) ? [] : {};
                        for (var t in e) e.hasOwnProperty(t) && ("object" === typeof e[t] ? n[t] = this._deepCopy(e[t]) : n[t] = e[t]);
                        return n
                    }
                }]), e
            }();
            a.$request || (a.$request = new c);
            var u = a.$request;
            n.default = u
        }).call(this, t("5a52")["default"], t("c8ba"))
    }, d8c5: function (e, n, t) {
        "use strict";
        t.d(n, "b", (function () {
            return o
        })), t.d(n, "c", (function () {
            return r
        })), t.d(n, "a", (function () {
            return a
        }));
        var a = {uniPopup: t("06c7").default}, o = function () {
            var e = this, n = e.$createElement, t = e._self._c || n;
            return t("uni-popup", {
                ref: "popref",
                attrs: {type: "center"}
            }, [t("v-uni-view", {staticClass: "login-boxs"}, [t("v-uni-view", {staticClass: "title"}, [e._v("您还未登录")]), t("v-uni-view", {staticClass: "content"}, [e._v("为了完整的体验所有功能，请您先授权登录")]), t("v-uni-view", {staticClass: "btns"}, [t("v-uni-view", {
                staticClass: "btn",
                on: {
                    click: function (n) {
                        arguments[0] = n = e.$handleEvent(n), e.closePop.apply(void 0, arguments)
                    }
                }
            }, [e._v("暂不登录")]), e.canIUseGetUserProfile ? t("v-uni-button", {
                staticClass: "btn activebt",
                on: {
                    click: function (n) {
                        arguments[0] = n = e.$handleEvent(n), e.getUserProfile.apply(void 0, arguments)
                    }
                }
            }, [e._v("立即登录")]) : t("v-uni-button", {
                staticClass: "btn activebt",
                attrs: {"open-type": "getUserInfo"},
                on: {
                    getuserinfo: function (n) {
                        arguments[0] = n = e.$handleEvent(n), e.getUserInfo.apply(void 0, arguments)
                    }
                }
            }, [e._v("立即登录")])], 1)], 1)], 1)
        }, r = []
    }, dc8b: function (e, n, t) {
        var a = t("24fb");
        n = a(!1), n.push([e.i, ".uni-transition[data-v-016ede03]{transition-timing-function:ease;transition-duration:.3s;transition-property:opacity,-webkit-transform;transition-property:transform,opacity;transition-property:transform,opacity,-webkit-transform}.fade-in[data-v-016ede03]{opacity:0}.fade-active[data-v-016ede03]{opacity:1}.slide-top-in[data-v-016ede03]{\n\t/* transition-property: transform, opacity; */-webkit-transform:translateY(-100%);transform:translateY(-100%)}.slide-top-active[data-v-016ede03]{-webkit-transform:translateY(0);transform:translateY(0)\n\t/* opacity: 1; */}.slide-right-in[data-v-016ede03]{-webkit-transform:translateX(100%);transform:translateX(100%)}.slide-right-active[data-v-016ede03]{-webkit-transform:translateX(0);transform:translateX(0)}.slide-bottom-in[data-v-016ede03]{-webkit-transform:translateY(100%);transform:translateY(100%)}.slide-bottom-active[data-v-016ede03]{-webkit-transform:translateY(0);transform:translateY(0)}.slide-left-in[data-v-016ede03]{-webkit-transform:translateX(-100%);transform:translateX(-100%)}.slide-left-active[data-v-016ede03]{-webkit-transform:translateX(0);transform:translateX(0);opacity:1}.zoom-in-in[data-v-016ede03]{-webkit-transform:scale(.8);transform:scale(.8)}.zoom-out-active[data-v-016ede03]{-webkit-transform:scale(1);transform:scale(1)}.zoom-out-in[data-v-016ede03]{-webkit-transform:scale(1.2);transform:scale(1.2)}", ""]), e.exports = n
    }, e2ed: function (e, n, t) {
        "use strict";
        var a;
        t.d(n, "b", (function () {
            return o
        })), t.d(n, "c", (function () {
            return r
        })), t.d(n, "a", (function () {
            return a
        }));
        var o = function () {
            var e = this, n = e.$createElement, t = e._self._c || n;
            return e.isShow ? t("v-uni-view", {
                ref: "ani",
                staticClass: "uni-transition",
                class: [e.ani.in],
                style: "transform:" + e.transform + ";" + e.stylesObject,
                on: {
                    click: function (n) {
                        arguments[0] = n = e.$handleEvent(n), e.change.apply(void 0, arguments)
                    }
                }
            }, [e._t("default")], 2) : e._e()
        }, r = []
    }, e673: function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("5072"), o = t.n(a);
        for (var r in a) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return a[e]
            }))
        }(r);
        n["default"] = o.a
    }, e9d6: function (e, n, t) {
        (function (n) {
            t("c975"), t("ac1f"), t("466d"), t("5319"), t("1276");
            var a = t("9523");
            !function (n, t) {
                e.exports = t(n)
            }(window, (function (e, t) {
                function o(n, t, a) {
                    e.WeixinJSBridge ? WeixinJSBridge.invoke(n, i(t), (function (e) {
                        u(n, e, a)
                    })) : g(n, a)
                }

                function r(n, t, a) {
                    e.WeixinJSBridge ? WeixinJSBridge.on(n, (function (e) {
                        a && a.trigger && a.trigger(e), u(n, e, t)
                    })) : g(n, a || t)
                }

                function i(e) {
                    return e = e || {}, e.appId = M.appId, e.verifyAppId = M.appId, e.verifySignType = "sha1", e.verifyTimestamp = M.timestamp + "", e.verifyNonceStr = M.nonceStr, e.verifySignature = M.signature, e
                }

                function s(e) {
                    return {
                        timeStamp: e.timestamp + "",
                        nonceStr: e.nonceStr,
                        package: e.package,
                        paySign: e.paySign,
                        signType: e.signType || "SHA1"
                    }
                }

                function c(e) {
                    return e.postalCode = e.addressPostalCode, delete e.addressPostalCode, e.provinceName = e.proviceFirstStageName, delete e.proviceFirstStageName, e.cityName = e.addressCitySecondStageName, delete e.addressCitySecondStageName, e.countryName = e.addressCountiesThirdStageName, delete e.addressCountiesThirdStageName, e.detailInfo = e.addressDetailInfo, delete e.addressDetailInfo, e
                }

                function u(e, n, t) {
                    "openEnterpriseChat" == e && (n.errCode = n.err_code), delete n.err_code, delete n.err_desc, delete n.err_detail;
                    var a = n.errMsg;
                    a || (a = n.err_msg, delete n.err_msg, a = l(e, a), n.errMsg = a), (t = t || {})._complete && (t._complete(n), delete t._complete), a = n.errMsg || "", M.debug && !t.isInnerInvoke && alert(JSON.stringify(n));
                    var o = a.indexOf(":");
                    switch (a.substring(o + 1)) {
                        case"ok":
                            t.success && t.success(n);
                            break;
                        case"cancel":
                            t.cancel && t.cancel(n);
                            break;
                        default:
                            t.fail && t.fail(n)
                    }
                    t.complete && t.complete(n)
                }

                function l(e, n) {
                    var t = e, a = b[t];
                    a && (t = a);
                    var o = "ok";
                    if (n) {
                        var r = n.indexOf(":");
                        "confirm" == (o = n.substring(r + 1)) && (o = "ok"), "failed" == o && (o = "fail"), -1 != o.indexOf("failed_") && (o = o.substring(7)), -1 != o.indexOf("fail_") && (o = o.substring(5)), "access denied" != (o = (o = o.replace(/_/g, " ")).toLowerCase()) && "no permission to execute" != o || (o = "permission denied"), "config" == t && "function not exist" == o && (o = "ok"), "" == o && (o = "fail")
                    }
                    return t + ":" + o
                }

                function p(e) {
                    if (e) {
                        for (var n = 0, t = e.length; n < t; ++n) {
                            var a = e[n], o = _[a];
                            o && (e[n] = o)
                        }
                        return e
                    }
                }

                function g(e, t) {
                    if (!(!M.debug || t && t.isInnerInvoke)) {
                        var a = b[e];
                        a && (e = a), t && t._complete && delete t._complete, n.log('"' + e + '",', t || "")
                    }
                }

                function d(e) {
                    if (!(T || k || M.debug || I < "6.0.2" || L.systemType < 0)) {
                        var n = new Image;
                        L.appId = M.appId, L.initTime = j.initEndTime - j.initStartTime, L.preVerifyTime = j.preVerifyEndTime - j.preVerifyStartTime, q.getNetworkType({
                            isInnerInvoke: !0,
                            success: function (e) {
                                L.networkType = e.networkType;
                                var t = "https://open.weixin.qq.com/sdk/report?v=" + L.version + "&o=" + L.isPreVerifyOk + "&s=" + L.systemType + "&c=" + L.clientVersion + "&a=" + L.appId + "&n=" + L.networkType + "&i=" + L.initTime + "&p=" + L.preVerifyTime + "&u=" + L.url;
                                n.src = t
                            }
                        })
                    }
                }

                function f() {
                    return (new Date).getTime()
                }

                function m(n) {
                    P && (e.WeixinJSBridge ? n() : w.addEventListener && w.addEventListener("WeixinJSBridgeReady", n, !1))
                }

                function y() {
                    q.invoke || (q.invoke = function (n, t, a) {
                        e.WeixinJSBridge && WeixinJSBridge.invoke(n, i(t), a)
                    }, q.on = function (n, t) {
                        e.WeixinJSBridge && WeixinJSBridge.on(n, t)
                    })
                }

                function h(e) {
                    if ("string" == typeof e && e.length > 0) {
                        var n = e.split("?")[0], t = e.split("?")[1];
                        return n += ".html", void 0 !== t ? n + "?" + t : n
                    }
                }

                if (!e.jWeixin) {
                    var v, _ = {
                            config: "preVerifyJSAPI",
                            onMenuShareTimeline: "menu:share:timeline",
                            onMenuShareAppMessage: "menu:share:appmessage",
                            onMenuShareQQ: "menu:share:qq",
                            onMenuShareWeibo: "menu:share:weiboApp",
                            onMenuShareQZone: "menu:share:QZone",
                            previewImage: "imagePreview",
                            getLocation: "geoLocation",
                            openProductSpecificView: "openProductViewWithPid",
                            addCard: "batchAddCard",
                            openCard: "batchViewCard",
                            chooseWXPay: "getBrandWCPayRequest",
                            openEnterpriseRedPacket: "getRecevieBizHongBaoRequest",
                            startSearchBeacons: "startMonitoringBeacons",
                            stopSearchBeacons: "stopMonitoringBeacons",
                            onSearchBeacons: "onBeaconsInRange",
                            consumeAndShareCard: "consumedShareCard",
                            openAddress: "editAddress"
                        }, b = function () {
                            var e = {};
                            for (var n in _) e[_[n]] = n;
                            return e
                        }(), w = e.document, S = w.title, x = navigator.userAgent.toLowerCase(),
                        C = navigator.platform.toLowerCase(), T = !(!C.match("mac") && !C.match("win")),
                        k = -1 != x.indexOf("wxdebugger"), P = -1 != x.indexOf("micromessenger"),
                        A = -1 != x.indexOf("android"), O = -1 != x.indexOf("iphone") || -1 != x.indexOf("ipad"),
                        I = function () {
                            var e = x.match(/micromessenger\/(\d+\.\d+\.\d+)/) || x.match(/micromessenger\/(\d+\.\d+)/);
                            return e ? e[1] : ""
                        }(), j = {initStartTime: f(), initEndTime: 0, preVerifyStartTime: 0, preVerifyEndTime: 0}, L = {
                            version: 1,
                            appId: "",
                            initTime: 0,
                            preVerifyTime: 0,
                            networkType: "",
                            isPreVerifyOk: 1,
                            systemType: O ? 1 : A ? 2 : -1,
                            clientVersion: I,
                            url: encodeURIComponent(location.href)
                        }, M = {}, E = {_completes: []}, N = {state: 0, data: {}};
                    m((function () {
                        j.initEndTime = f()
                    }));
                    var B = !1, V = [], q = (v = {
                        config: function (e) {
                            M = e, g("config", e);
                            var n = !1 !== M.check;
                            m((function () {
                                if (n) o(_.config, {verifyJsApiList: p(M.jsApiList)}, function () {
                                    E._complete = function (e) {
                                        j.preVerifyEndTime = f(), N.state = 1, N.data = e
                                    }, E.success = function (e) {
                                        L.isPreVerifyOk = 0
                                    }, E.fail = function (e) {
                                        E._fail ? E._fail(e) : N.state = -1
                                    };
                                    var e = E._completes;
                                    return e.push((function () {
                                        d()
                                    })), E.complete = function (n) {
                                        for (var t = 0, a = e.length; t < a; ++t) e[t]();
                                        E._completes = []
                                    }, E
                                }()), j.preVerifyStartTime = f(); else {
                                    N.state = 1;
                                    for (var e = E._completes, t = 0, a = e.length; t < a; ++t) e[t]();
                                    E._completes = []
                                }
                            })), y()
                        }, ready: function (e) {
                            0 != N.state ? e() : (E._completes.push(e), !P && M.debug && e())
                        }, error: function (e) {
                            I < "6.0.2" || (-1 == N.state ? e(N.data) : E._fail = e)
                        }, checkJsApi: function (e) {
                            var n = function (e) {
                                var n = e.checkResult;
                                for (var t in n) {
                                    var a = b[t];
                                    a && (n[a] = n[t], delete n[t])
                                }
                                return e
                            };
                            o("checkJsApi", {jsApiList: p(e.jsApiList)}, (e._complete = function (e) {
                                if (A) {
                                    var t = e.checkResult;
                                    t && (e.checkResult = JSON.parse(t))
                                }
                                e = n(e)
                            }, e))
                        }, onMenuShareTimeline: function (e) {
                            r(_.onMenuShareTimeline, {
                                complete: function () {
                                    o("shareTimeline", {
                                        title: e.title || S,
                                        desc: e.title || S,
                                        img_url: e.imgUrl || "",
                                        link: e.link || location.href,
                                        type: e.type || "link",
                                        data_url: e.dataUrl || ""
                                    }, e)
                                }
                            }, e)
                        }, onMenuShareAppMessage: function (e) {
                            r(_.onMenuShareAppMessage, {
                                complete: function (n) {
                                    "favorite" === n.scene ? o("sendAppMessage", {
                                        title: e.title || S,
                                        desc: e.desc || "",
                                        link: e.link || location.href,
                                        img_url: e.imgUrl || "",
                                        type: e.type || "link",
                                        data_url: e.dataUrl || ""
                                    }) : o("sendAppMessage", {
                                        title: e.title || S,
                                        desc: e.desc || "",
                                        link: e.link || location.href,
                                        img_url: e.imgUrl || "",
                                        type: e.type || "link",
                                        data_url: e.dataUrl || ""
                                    }, e)
                                }
                            }, e)
                        }, onMenuShareQQ: function (e) {
                            r(_.onMenuShareQQ, {
                                complete: function () {
                                    o("shareQQ", {
                                        title: e.title || S,
                                        desc: e.desc || "",
                                        img_url: e.imgUrl || "",
                                        link: e.link || location.href
                                    }, e)
                                }
                            }, e)
                        }, onMenuShareWeibo: function (e) {
                            r(_.onMenuShareWeibo, {
                                complete: function () {
                                    o("shareWeiboApp", {
                                        title: e.title || S,
                                        desc: e.desc || "",
                                        img_url: e.imgUrl || "",
                                        link: e.link || location.href
                                    }, e)
                                }
                            }, e)
                        }, onMenuShareQZone: function (e) {
                            r(_.onMenuShareQZone, {
                                complete: function () {
                                    o("shareQZone", {
                                        title: e.title || S,
                                        desc: e.desc || "",
                                        img_url: e.imgUrl || "",
                                        link: e.link || location.href
                                    }, e)
                                }
                            }, e)
                        }, updateTimelineShareData: function (e) {
                            o("updateTimelineShareData", {title: e.title, link: e.link, imgUrl: e.imgUrl}, e)
                        }, updateAppMessageShareData: function (e) {
                            o("updateAppMessageShareData", {
                                title: e.title,
                                desc: e.desc,
                                link: e.link,
                                imgUrl: e.imgUrl
                            }, e)
                        }, startRecord: function (e) {
                            o("startRecord", {}, e)
                        }, stopRecord: function (e) {
                            o("stopRecord", {}, e)
                        }, onVoiceRecordEnd: function (e) {
                            r("onVoiceRecordEnd", e)
                        }, playVoice: function (e) {
                            o("playVoice", {localId: e.localId}, e)
                        }, pauseVoice: function (e) {
                            o("pauseVoice", {localId: e.localId}, e)
                        }, stopVoice: function (e) {
                            o("stopVoice", {localId: e.localId}, e)
                        }, onVoicePlayEnd: function (e) {
                            r("onVoicePlayEnd", e)
                        }, uploadVoice: function (e) {
                            o("uploadVoice", {
                                localId: e.localId,
                                isShowProgressTips: 0 == e.isShowProgressTips ? 0 : 1
                            }, e)
                        }, downloadVoice: function (e) {
                            o("downloadVoice", {
                                serverId: e.serverId,
                                isShowProgressTips: 0 == e.isShowProgressTips ? 0 : 1
                            }, e)
                        }, translateVoice: function (e) {
                            o("translateVoice", {
                                localId: e.localId,
                                isShowProgressTips: 0 == e.isShowProgressTips ? 0 : 1
                            }, e)
                        }, chooseImage: function (e) {
                            o("chooseImage", {
                                scene: "1|2",
                                count: e.count || 9,
                                sizeType: e.sizeType || ["original", "compressed"],
                                sourceType: e.sourceType || ["album", "camera"]
                            }, (e._complete = function (e) {
                                if (A) {
                                    var n = e.localIds;
                                    try {
                                        n && (e.localIds = JSON.parse(n))
                                    } catch (e) {
                                    }
                                }
                            }, e))
                        }, getLocation: function (e) {
                        }, previewImage: function (e) {
                            o(_.previewImage, {current: e.current, urls: e.urls}, e)
                        }, uploadImage: function (e) {
                            o("uploadImage", {
                                localId: e.localId,
                                isShowProgressTips: 0 == e.isShowProgressTips ? 0 : 1
                            }, e)
                        }, downloadImage: function (e) {
                            o("downloadImage", {
                                serverId: e.serverId,
                                isShowProgressTips: 0 == e.isShowProgressTips ? 0 : 1
                            }, e)
                        }, getLocalImgData: function (e) {
                            !1 === B ? (B = !0, o("getLocalImgData", {localId: e.localId}, (e._complete = function (e) {
                                if (B = !1, V.length > 0) {
                                    var n = V.shift();
                                    wx.getLocalImgData(n)
                                }
                            }, e))) : V.push(e)
                        }, getNetworkType: function (e) {
                            var n = function (e) {
                                var n = e.errMsg;
                                e.errMsg = "getNetworkType:ok";
                                var t = e.subtype;
                                if (delete e.subtype, t) e.networkType = t; else {
                                    var a = n.indexOf(":"), o = n.substring(a + 1);
                                    switch (o) {
                                        case"wifi":
                                        case"edge":
                                        case"wwan":
                                            e.networkType = o;
                                            break;
                                        default:
                                            e.errMsg = "getNetworkType:fail"
                                    }
                                }
                                return e
                            };
                            o("getNetworkType", {}, (e._complete = function (e) {
                                e = n(e)
                            }, e))
                        }, openLocation: function (e) {
                            o("openLocation", {
                                latitude: e.latitude,
                                longitude: e.longitude,
                                name: e.name || "",
                                address: e.address || "",
                                scale: e.scale || 28,
                                infoUrl: e.infoUrl || ""
                            }, e)
                        }
                    }, a(v, "getLocation", (function (e) {
                        e = e || {}, o(_.getLocation, {type: e.type || "wgs84"}, (e._complete = function (e) {
                            delete e.type
                        }, e))
                    })), a(v, "hideOptionMenu", (function (e) {
                        o("hideOptionMenu", {}, e)
                    })), a(v, "showOptionMenu", (function (e) {
                        o("showOptionMenu", {}, e)
                    })), a(v, "closeWindow", (function (e) {
                        o("closeWindow", {}, e = e || {})
                    })), a(v, "hideMenuItems", (function (e) {
                        o("hideMenuItems", {menuList: e.menuList}, e)
                    })), a(v, "showMenuItems", (function (e) {
                        o("showMenuItems", {menuList: e.menuList}, e)
                    })), a(v, "hideAllNonBaseMenuItem", (function (e) {
                        o("hideAllNonBaseMenuItem", {}, e)
                    })), a(v, "showAllNonBaseMenuItem", (function (e) {
                        o("showAllNonBaseMenuItem", {}, e)
                    })), a(v, "scanQRCode", (function (e) {
                        o("scanQRCode", {
                            needResult: (e = e || {}).needResult || 0,
                            scanType: e.scanType || ["qrCode", "barCode"]
                        }, (e._complete = function (e) {
                            if (O) {
                                var n = e.resultStr;
                                if (n) {
                                    var t = JSON.parse(n);
                                    e.resultStr = t && t.scan_code && t.scan_code.scan_result
                                }
                            }
                        }, e))
                    })), a(v, "openAddress", (function (e) {
                        o(_.openAddress, {}, (e._complete = function (e) {
                            e = c(e)
                        }, e))
                    })), a(v, "openProductSpecificView", (function (e) {
                        o(_.openProductSpecificView, {
                            pid: e.productId,
                            view_type: e.viewType || 0,
                            ext_info: e.extInfo
                        }, e)
                    })), a(v, "addCard", (function (e) {
                        for (var n = e.cardList, t = [], a = 0, r = n.length; a < r; ++a) {
                            var i = n[a], s = {card_id: i.cardId, card_ext: i.cardExt};
                            t.push(s)
                        }
                        o(_.addCard, {card_list: t}, (e._complete = function (e) {
                            var n = e.card_list;
                            if (n) {
                                for (var t = 0, a = (n = JSON.parse(n)).length; t < a; ++t) {
                                    var o = n[t];
                                    o.cardId = o.card_id, o.cardExt = o.card_ext, o.isSuccess = !!o.is_succ, delete o.card_id, delete o.card_ext, delete o.is_succ
                                }
                                e.cardList = n, delete e.card_list
                            }
                        }, e))
                    })), a(v, "chooseCard", (function (e) {
                        o("chooseCard", {
                            app_id: M.appId,
                            location_id: e.shopId || "",
                            sign_type: e.signType || "SHA1",
                            card_id: e.cardId || "",
                            card_type: e.cardType || "",
                            card_sign: e.cardSign,
                            time_stamp: e.timestamp + "",
                            nonce_str: e.nonceStr
                        }, (e._complete = function (e) {
                            e.cardList = e.choose_card_info, delete e.choose_card_info
                        }, e))
                    })), a(v, "openCard", (function (e) {
                        for (var n = e.cardList, t = [], a = 0, r = n.length; a < r; ++a) {
                            var i = n[a], s = {card_id: i.cardId, code: i.code};
                            t.push(s)
                        }
                        o(_.openCard, {card_list: t}, e)
                    })), a(v, "consumeAndShareCard", (function (e) {
                        o(_.consumeAndShareCard, {consumedCardId: e.cardId, consumedCode: e.code}, e)
                    })), a(v, "chooseWXPay", (function (e) {
                        o(_.chooseWXPay, s(e), e)
                    })), a(v, "openEnterpriseRedPacket", (function (e) {
                        o(_.openEnterpriseRedPacket, s(e), e)
                    })), a(v, "startSearchBeacons", (function (e) {
                        o(_.startSearchBeacons, {ticket: e.ticket}, e)
                    })), a(v, "stopSearchBeacons", (function (e) {
                        o(_.stopSearchBeacons, {}, e)
                    })), a(v, "onSearchBeacons", (function (e) {
                        r(_.onSearchBeacons, e)
                    })), a(v, "openEnterpriseChat", (function (e) {
                        o("openEnterpriseChat", {useridlist: e.userIds, chatname: e.groupName}, e)
                    })), a(v, "launchMiniProgram", (function (e) {
                        o("launchMiniProgram", {
                            targetAppId: e.targetAppId,
                            path: h(e.path),
                            envVersion: e.envVersion
                        }, e)
                    })), a(v, "miniProgram", {
                        navigateBack: function (e) {
                            e = e || {}, m((function () {
                                o("invokeMiniProgramAPI", {name: "navigateBack", arg: {delta: e.delta || 1}}, e)
                            }))
                        }, navigateTo: function (e) {
                            m((function () {
                                o("invokeMiniProgramAPI", {name: "navigateTo", arg: {url: e.url}}, e)
                            }))
                        }, redirectTo: function (e) {
                            m((function () {
                                o("invokeMiniProgramAPI", {name: "redirectTo", arg: {url: e.url}}, e)
                            }))
                        }, switchTab: function (e) {
                            m((function () {
                                o("invokeMiniProgramAPI", {name: "switchTab", arg: {url: e.url}}, e)
                            }))
                        }, reLaunch: function (e) {
                            m((function () {
                                o("invokeMiniProgramAPI", {name: "reLaunch", arg: {url: e.url}}, e)
                            }))
                        }, postMessage: function (e) {
                            m((function () {
                                o("invokeMiniProgramAPI", {name: "postMessage", arg: e.data || {}}, e)
                            }))
                        }, getEnv: function (n) {
                            m((function () {
                                n({miniprogram: "miniprogram" === e.__wxjs_environment})
                            }))
                        }
                    }), v), U = 1, R = {};
                    return w.addEventListener("error", (function (e) {
                        if (!A) {
                            var n = e.target, t = n.tagName, a = n.src;
                            if (("IMG" == t || "VIDEO" == t || "AUDIO" == t || "SOURCE" == t) && -1 != a.indexOf("wxlocalresource://")) {
                                e.preventDefault(), e.stopPropagation();
                                var o = n["wx-id"];
                                if (o || (o = U++, n["wx-id"] = o), R[o]) return;
                                R[o] = !0, wx.ready((function () {
                                    wx.getLocalImgData({
                                        localId: a, success: function (e) {
                                            n.src = e.localData
                                        }
                                    })
                                }))
                            }
                        }
                    }), !0), w.addEventListener("load", (function (e) {
                        if (!A) {
                            var n = e.target, t = n.tagName;
                            if (n.src, "IMG" == t || "VIDEO" == t || "AUDIO" == t || "SOURCE" == t) {
                                var a = n["wx-id"];
                                a && (R[a] = !1)
                            }
                        }
                    }), !0), t && (e.wx = e.jWeixin = q), q
                }
            }))
        }).call(this, t("5a52")["default"])
    }, f024: function (e, n, t) {
        "use strict";
        t.r(n);
        var a = t("026b"), o = t.n(a);
        for (var r in a) "default" !== r && function (e) {
            t.d(n, e, (function () {
                return a[e]
            }))
        }(r);
        n["default"] = o.a
    }
});
