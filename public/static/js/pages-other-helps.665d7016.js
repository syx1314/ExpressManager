(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-other-helps"],{"0534":function(t,i){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUBAMAAAB/pwA+AAAAJFBMVEUAAAC3t7e1tbW2tra2tra3t7e2tra1tbW2tra2tra2tra3t7dcOL9nAAAADHRSTlMADc8SyVvSfHRsZVG5HsDOAAAAQklEQVQI12OgADAKwJlCjnBB9SIBmGADhyJUUJOBYRJE2GgBAwMXWJg5FUQGgYTNAkBMVpAh3hAdW4DYAMJkJtd9AJVhBlsUaQejAAAAAElFTkSuQmCC"},"06a8":function(t,i,e){var n=e("daef");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=e("4f06").default;a("23bdb1cc",n,!0,{sourceMap:!1,shadowMode:!1})},4215:function(t,i,e){var n=e("42f0");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=e("4f06").default;a("c8250e5e",n,!0,{sourceMap:!1,shadowMode:!1})},"42f0":function(t,i,e){var n=e("24fb");i=n(!1),i.push([t.i,".boxs[data-v-29db1a81]{width:%?650?%;background-color:#fff;border-radius:%?24?%;min-height:%?20?%;padding-bottom:%?30?%;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;position:relative;box-sizing:border-box}.title[data-v-29db1a81]{line-height:%?100?%;font-size:%?34?%;font-weight:600}.topbg[data-v-29db1a81]{width:100%;border-radius:%?24?% %?24?% 0 0;height:%?200?%}.close_ico[data-v-29db1a81]{position:absolute;right:10px;top:10px;width:%?30?%;height:%?30?%;z-index:999}.content[data-v-29db1a81]{max-height:60vh;min-height:%?300?%;width:%?610?%;overflow-y:scroll;margin-top:%?20?%}.btns[data-v-29db1a81]{width:%?610?%;display:flex;flex-direction:row;justify-content:space-around;align-items:center;margin-top:%?40?%}.btns .btn[data-v-29db1a81]{background-color:#0d8eea;color:#fff;height:%?80?%;line-height:%?80?%;text-align:center;padding-left:%?90?%;padding-right:%?90?%;border-radius:%?40?%;font-size:%?30?%}",""]),t.exports=i},"6bb5":function(t,i,e){"use strict";e.d(i,"b",(function(){return a})),e.d(i,"c",(function(){return o})),e.d(i,"a",(function(){return n}));var n={uniPopup:e("2885").default},a=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("uni-popup",{ref:"popref",attrs:{type:"center"}},[e("v-uni-view",{staticClass:"boxs"},[t.info.litpic?[e("v-uni-image",{staticClass:"close_ico",attrs:{src:"/static/close_w.png"},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.closePop.apply(void 0,arguments)}}}),e("v-uni-image",{staticClass:"topbg",attrs:{src:t.info.litpic,mode:"aspectFill"}})]:[e("v-uni-image",{staticClass:"close_ico",attrs:{src:"/static/close_g.png"},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.closePop.apply(void 0,arguments)}}}),e("v-uni-view",{staticClass:"title"},[t._v(t._s(t.info.title))])],e("v-uni-scroll-view",{staticClass:"content",attrs:{"scroll-y":"true"}},[e("div",{staticClass:"richbox",domProps:{innerHTML:t._s(t.info.body)}})]),t.btntxt?e("v-uni-view",{staticClass:"btns"},[e("v-uni-view",{staticClass:"btn",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.closePop.apply(void 0,arguments)}}},[t._v(t._s(t.btntxt))])],1):t._e()],2)],1)},o=[]},"6d7b":function(t,i,e){"use strict";e.r(i);var n=e("b687"),a=e("8c7d");for(var o in a)"default"!==o&&function(t){e.d(i,t,(function(){return a[t]}))}(o);e("71d8");var s,r=e("f0c5"),c=Object(r["a"])(a["default"],n["b"],n["c"],!1,null,"1443d166",null,!1,n["a"],s);i["default"]=c.exports},"71d8":function(t,i,e){"use strict";var n=e("06a8"),a=e.n(n);a.a},8974:function(t,i,e){"use strict";(function(t){e("a9e3"),Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var n={data:function(){return{info:{}}},props:{docid:{type:Number},btntxt:{type:String,default:"知道了"}},mounted:function(){},onShow:function(){},methods:{openPop:function(t){this.getDoc(),this.$refs.popref.open()},closePop:function(){this.$refs.popref.close()},getDoc:function(i){var e=this;uni.showLoading({title:"请稍后"}),this.$request.post("open/get_doc",{data:{id:this.docid}}).then((function(t){uni.hideLoading(),0==t.data.errno?e.info=t.data.data:(e.toast("内容未找到"),e.$refs.popref.close())})).catch((function(i){t.error("error:",i)}))}}};i.default=n}).call(this,e("5a52")["default"])},"8c7d":function(t,i,e){"use strict";e.r(i);var n=e("ec00"),a=e.n(n);for(var o in n)"default"!==o&&function(t){e.d(i,t,(function(){return n[t]}))}(o);i["default"]=a.a},9531:function(t,i,e){"use strict";e.r(i);var n=e("8974"),a=e.n(n);for(var o in n)"default"!==o&&function(t){e.d(i,t,(function(){return n[t]}))}(o);i["default"]=a.a},b687:function(t,i,e){"use strict";var n;e.d(i,"b",(function(){return a})),e.d(i,"c",(function(){return o})),e.d(i,"a",(function(){return n}));var a=function(){var t=this,i=t.$createElement,n=t._self._c||i;return n("v-uni-view",[n("v-uni-view",{staticClass:"content"},[n("v-uni-view",{staticClass:"titles"},[t._v("帮助中心")]),t._l(t.lists,(function(i,a){return n("v-uni-view",{key:a,staticClass:"uls",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.shows(a)}}},[n("v-uni-view",{staticClass:"ulis"},[n("v-uni-view",{staticClass:"lis"},[t._v(t._s(i.title))]),n("v-uni-image",{class:[i.type?"":"rotates"],attrs:{src:e("0534"),mode:""}})],1),n("v-uni-view",{staticClass:"instructions",class:[a==t.showindex?"":"isOpen"]},[t._v(t._s(i.content))])],1)})),n("v-uni-view",{staticClass:"serbtn",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.openKefu()}}},[n("v-uni-image",{attrs:{src:"/static/ic_kefu.png"}}),t._v("在线客服")],1),n("doc-box",{ref:"docbox",attrs:{docid:6,btntxt:""}})],2)],1)},o=[]},c625:function(t,i,e){"use strict";var n=e("4215"),a=e.n(n);a.a},daef:function(t,i,e){var n=e("24fb");i=n(!1),i.push([t.i,".isOpen[data-v-1443d166]{display:none}.content[data-v-1443d166]{padding:0 %?30?% %?120?% %?40?%}.titles[data-v-1443d166]{height:%?150?%;line-height:%?150?%;font-size:%?40?%;font-weight:900;font-family:SimHei;color:#000}.uls[data-v-1443d166]{border-bottom:1px solid #d9d9d9;width:%?670?%}.ulis[data-v-1443d166]{height:%?110?%;width:%?670?%;display:flex;justify-content:space-between;align-items:center}.lis[data-v-1443d166]{width:%?630?%;font-size:%?30?%;color:#000;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-family:SimSun}.uls uni-image[data-v-1443d166]{display:block;width:%?30?%;height:%?30?%}.uls .rotates[data-v-1443d166]{-webkit-transform:rotate(180deg);transform:rotate(180deg)}.instructions[data-v-1443d166]{padding:%?0?% %?0?% %?20?% 0;width:%?670?%;height:auto;font-size:%?26?%;color:#a0a0a0;line-height:%?38?%;margin-top:%?0?%}.public-number[data-v-1443d166]{position:fixed;left:0;bottom:%?0?%;display:flex;justify-content:center;align-items:center;background-color:#fff;width:%?750?%;height:%?120?%}.btns[data-v-1443d166]{width:%?526?%;height:%?80?%;text-align:center;line-height:%?80?%;color:#fff;background-color:#528fce;border-radius:%?10?%}.advertising[data-v-1443d166]{width:%?670?%;height:%?270?%;margin:%?30?% auto 0}.advertising uni-image[data-v-1443d166]{display:block;width:100%;height:100%}.serbtn[data-v-1443d166]{width:%?750?%;height:%?100?%;position:fixed;left:0;bottom:0;line-height:%?100?%;text-align:center;background-color:#fff;box-shadow:0 0 3px 0 #aaa;font-size:%?30?%;display:flex;flex-direction:row;align-items:center;justify-content:center}.serbtn>uni-image[data-v-1443d166]{width:%?40?%;height:%?40?%;margin-right:%?20?%}",""]),t.exports=i},ec00:function(t,i,e){"use strict";(function(t){var n=e("4ea4");Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var a=n(e("fe06")),o={components:{DocBox:a.default},data:function(){return{showindex:-1,lists:[]}},onLoad:function(){this.getHelp()},computed:{},methods:{openKefu:function(){this.$refs.docbox.openPop()},shows:function(t){this.showindex=t},getHelp:function(){var i=this;this.$request.post("open/helptxt",{data:{}}).then((function(e){t.log(e),0==e.data.errno&&(i.lists=e.data.data)})).catch((function(i){t.error("error:",i)}))}}};i.default=o}).call(this,e("5a52")["default"])},fe06:function(t,i,e){"use strict";e.r(i);var n=e("6bb5"),a=e("9531");for(var o in a)"default"!==o&&function(t){e.d(i,t,(function(){return a[t]}))}(o);e("c625");var s,r=e("f0c5"),c=Object(r["a"])(a["default"],n["b"],n["c"],!1,null,"29db1a81",null,!1,n["a"],s);i["default"]=c.exports}}]);