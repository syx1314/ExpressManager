(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-other-doc"],{"0e94":function(t,n,e){"use strict";var i;e.d(n,"b",(function(){return a})),e.d(n,"c",(function(){return o})),e.d(n,"a",(function(){return i}));var a=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("v-uni-view",{staticStyle:{padding:"20rpx"}},[e("v-uni-rich-text",{attrs:{nodes:t.info.body}})],1)},o=[]},"6c4a":function(t,n,e){"use strict";e.r(n);var i=e("aa8c"),a=e.n(i);for(var o in i)"default"!==o&&function(t){e.d(n,t,(function(){return i[t]}))}(o);n["default"]=a.a},aa8c:function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var i={data:function(){return{info:{}}},onLoad:function(t){this.getDoc(t.id)},mounted:function(){},methods:{getDoc:function(t){var n=this;uni.showLoading({title:"请稍后"}),this.$request.post("open/get_doc",{data:{id:t}}).then((function(t){uni.hideLoading(),0==t.data.errno&&(n.info=t.data.data,uni.setNavigationBarTitle({title:n.info.title}))})).catch((function(t){console.error("error:",t)}))}}};n.default=i},c1fe:function(t,n,e){"use strict";e.r(n);var i=e("0e94"),a=e("6c4a");for(var o in a)"default"!==o&&function(t){e.d(n,t,(function(){return a[t]}))}(o);var r,u=e("f0c5"),c=Object(u["a"])(a["default"],i["b"],i["c"],!1,null,"ae0f32a0",null,!1,i["a"],r);n["default"]=c.exports}}]);