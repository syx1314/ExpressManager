(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-agent-haibao"],{"21a0":function(t,i,n){"use strict";n.r(i);var e=n("3d6f"),a=n.n(e);for(var o in e)"default"!==o&&function(t){n.d(i,t,(function(){return e[t]}))}(o);i["default"]=a.a},"3d6f":function(t,i,n){"use strict";(function(t){n("d3b7"),n("3ca3"),n("ddb0"),Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var e={data:function(){return{canvasShow:!0,base64:"",width:750,height:1e3,config:{bgimg:"http://qiniu.dayuanren.cn/16195822008p3fZB.png",ctx:[{type:"img",left:40,top:20,size:20,src:"http://qiniu.dayuanren.cn/16195822008p3fZB.png"},{type:"text",left:50,top:50,fontsize:28,fontcolor:"#fff",text:"快和我一起加入"}]}}},onLoad:function(t){this.getConfig(t.id)},mounted:function(){},methods:{getConfig:function(i){var n=this;uni.showLoading({title:"加载中"}),this.$request.post("Customer/poster_config",{data:{id:i}}).then((function(t){uni.hideLoading(),0==t.data.errno?(n.config=t.data.data,n.base64=n.config.bgimg,n.init()):uni.showModal({title:"提示",content:t.data.errmsg,showCancel:!1,confirmText:"好的",success:function(t){uni.navigateBack({delta:1})}})})).catch((function(i){t.error("error:",i)}))},init:function(){var t=this;uni.showLoading({title:"生成中"});var i=this,n=new Promise((function(t,n){uni.getImageInfo({src:i.config.bgimg,success:function(i){t(i)}})}));Promise.all([n]).then((function(n){t.width=n[0].width,t.height=n[0].height,setTimeout((function(){i.makeCanvas(n[0])}),200)}))},makeCanvas:function(t){var i=this,n=t.width,e=t.height,a=(uni.createSelectorQuery().in(this),uni.createCanvasContext("myCanvas"));a.drawImage(this.config.bgimg,0,0,n,e,0,0,n,e);for(var o=0;o<this.config.ctx.length;o++){if("img"==this.config.ctx[o].type){var c=n*this.config.ctx[o].left/100,s=e*this.config.ctx[o].top/100,r=n*this.config.ctx[o].size/100;a.drawImage(this.config.ctx[o].src,c,s,r,r)}if("text"==this.config.ctx[o].type){c=n*this.config.ctx[o].left/100,s=e*this.config.ctx[o].top/100;var f=this.config.ctx[o].fontsize,u=this.config.ctx[o].fontcolor;a.setFillStyle(u),a.setFontSize(f),a.setTextAlign("center"),a.fillText(this.config.ctx[o].text,c,s)}}a.draw(!0,(function(){uni.hideLoading(),uni.canvasToTempFilePath({canvasId:"myCanvas",success:function(t){var n=t.tempFilePath;i.base64=n,i.canvasShow=!1},fail:function(){uni.showToast({title:"海报加载失败",duration:2e3})}})}))}}};i.default=e}).call(this,n("5a52")["default"])},"7a97":function(t,i,n){var e=n("bd5e");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var a=n("4f06").default;a("4ac84d8f",e,!0,{sourceMap:!1,shadowMode:!1})},a744:function(t,i,n){"use strict";var e=n("7a97"),a=n.n(e);a.a},bd5e:function(t,i,n){var e=n("24fb");i=e(!1),i.push([t.i,".page[data-v-b12e7c70]{display:flex;flex-direction:row;align-items:center;justify-content:center}#sss[data-v-b12e7c70]{position:absolute;\n\t/* width: 750rpx;\n\theight: 1006rpx; */top:%?-99999899?%;left:%?-99999899?%;z-index:9999}.immm[data-v-b12e7c70]{width:%?750?%;height:auto}",""]),t.exports=i},c940:function(t,i,n){"use strict";var e;n.d(i,"b",(function(){return a})),n.d(i,"c",(function(){return o})),n.d(i,"a",(function(){return e}));var a=function(){var t=this,i=t.$createElement,n=t._self._c||i;return n("v-uni-view",{staticClass:"page"},[n("v-uni-scroll-view",{attrs:{"scroll-y":!0}},[t.width>0?n("v-uni-canvas",{directives:[{name:"show",rawName:"v-show",value:t.canvasShow,expression:"canvasShow"}],style:{width:t.width+"px",height:t.height+"px"},attrs:{"canvas-id":"myCanvas",id:"sss"}}):t._e(),t.base64?n("img",{staticClass:"immm",attrs:{src:t.base64}}):t._e()],1)],1)},o=[]},e57e:function(t,i,n){"use strict";n.r(i);var e=n("c940"),a=n("21a0");for(var o in a)"default"!==o&&function(t){n.d(i,t,(function(){return a[t]}))}(o);n("a744");var c,s=n("f0c5"),r=Object(s["a"])(a["default"],e["b"],e["c"],!1,null,"b12e7c70",null,!1,e["a"],c);i["default"]=r.exports}}]);