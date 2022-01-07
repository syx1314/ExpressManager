/*
 * gitee : https://gitee.com/monksoul/LayX
 * author : 百小僧
 * version : v1.0.0 beta
 * create time : 2018.04.30
 */

; !function (over, win) {
    "use strict";

    var utils = {
        Event: {
            on: function (eventName, callback) {
                if (!this[eventName]) {
                    this[eventName] = [];
                }
                this[eventName].push(callback);
            },
            emit: function (eventName) {
                var that = this;
                var params = arguments.length > 1 ? Array.prototype.slice.call(arguments, 1) : [];
                if (that[eventName]) {
                    Array.prototype.forEach.call(that[eventName], function (arg) {
                        arg.apply(self, params);
                    });
                }
            }
        },
        extend: function (target, ...sources) {
            sources.forEach(source => {
                let descriptors = Object.keys(source).reduce((descriptors, key) => {
                    descriptors[key] = Object.getOwnPropertyDescriptor(source, key);
                    return descriptors;
                }, {});

                Object.getOwnPropertySymbols(source).forEach(sym => {
                    let descriptor = Object.getOwnPropertyDescriptor(source, sym);
                    if (descriptor.enumerable) {
                        descriptors[sym] = descriptor;
                    }
                });
                Object.defineProperties(target, descriptors);
            });
            return target;
        },
        getNodeByClassName: function (node, className) {
            if (node === document.body) {
                return null;
            }
            var cls = node.classList;
            if (cls.contains(className)) {
                return node;
            }
            else {
                return arguments.callee(node.parentNode, className);
            }
        },
        getElementById: function (id, pEle) {
            return (pEle ? pEle : document).getElementById(id);
        },
        getElementBySelector: function (selector, pEle) {
            return (pEle ? pEle : document).querySelector(selector);
        },
        isArray: function (o) {
            return Object.prototype.toString.call(o) == '[object Array]';
        },
        InsertAfter: function (html, pEle) {
            (pEle ? pEle : document.body).lastElementChild.insertAdjacentHTML('afterend', html);
        },
        isFunction: function (func) {
            return func && Object.prototype.toString.call(func) === '[object Function]';
        },
        getCoords: function (el) {
            var
                box = el.getBoundingClientRect(),
                doc = el.ownerDocument,
                body = doc.body,
                html = doc.documentElement,
                clientTop = html.clientTop || body.clientTop || 0,
                clientLeft = html.clientLeft || body.clientLeft || 0,
                top = Math.round(box.top) + (self.pageYOffset || html.scrollTop || body.scrollTop) - clientTop,
                left = Math.round(box.left) + (self.pageXOffset || html.scrollLeft || body.scrollLeft) - clientLeft;
            return { 'top': top - 1, 'left': left - 1 };
        }
    };
    var defaults = {
        id: 'layx',
        title: 'Layx',
        width: 800,
        height: 500,
        screen: 'center',
        content: ['iframe', 'about:blank'],
        move: true,
        moveLock: { x: false, y: false, limit: false },
        resize: true,
        min: true,
        max: true,
        minWidth: 100,
        minHeight: 100,
        events: {
            load: function (layxContainer) {
            },
            destroy: function (iframeContext, layxContainer) {
            },
            maxOrNormal: function (iframeContext, layxContainerStatus, layxContainer) {
            },
            min: function (iframeContext, layxContainer) {
            }
        }
    };

    // drag
    var Drag = function (canMove, el, layxContainer) {
        var
            options = arguments[3] || {},
            container = options.container || document.documentElement,
            limit = false || options.limit,
            lockX = false || options.lockX,
            lockY = false || options.lockY,

            maskLayer = utils.getElementBySelector(".layx-mask", layxContainer);

        var drag = function (e) {
            e = e || window.event;

            var button = e.button || e.which;
            if (button == 1 && e.shiftKey == false) {
                Drag.isMove = true;

                var
                    layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal",
                    layxContainerArea = eval("(" + layxContainer.getAttribute("data-area") + ")"),
                    maxOrNormalMenu = utils.getElementBySelector(".layx-max-normal", layxContainer),

                    _left = e.clientX - el.offset_x,
                    _top = e.clientY - el.offset_y;

                if (limit) {
                    var _right = _left + layxContainer.offsetWidth,
                        _bottom = _top + layxContainer.offsetHeight,
                        _cCoords = utils.getCoords(container),
                        _cLeft = _cCoords.left,
                        _cTop = _cCoords.top,
                        _cRight = _cLeft + container.clientWidth,
                        _cBottom = _cTop + container.clientHeight;
                    _left = Math.max(_left, _cLeft);
                    _top = Math.max(_top, _cTop);
                    if (_right > _cRight) {
                        _left = _cRight - layxContainer.offsetWidth;
                    }
                    if (_bottom > _cBottom) {
                        _top = _cBottom - layxContainer.offsetHeight;
                    }
                }

                if (lockX) _left = el.lockX;
                if (lockY) _top = el.lockY;

                _top = _top > 0 ? _top : 0;

                layxContainer.style.top = _top + "px";
                if (layxContainerStatus === "max") {
                    layxContainer.style.setProperty('width', layxContainerArea[0] + 'px');
                    layxContainer.style.setProperty('height', layxContainerArea[1] + 'px');
                    layxContainer.setAttribute("data-statu", "normal");

                    if (maxOrNormalMenu) {
                        maxOrNormalMenu.classList.remove("layx-icon-max");
                        maxOrNormalMenu.classList.add("layx-icon-normal");
                    }

                    if (e.clientX < layxContainerArea[0] / 2) {
                        layxContainer.style.left = 0;
                    }
                    else if (e.clientX > layxContainerArea[0] / 2 && e.clientX < container.clientWidth - layxContainerArea[0]) {
                        layxContainer.style.left = e.clientX - layxContainerArea[0] / 2 + "px";
                    }
                    else if (container.clientWidth - e.clientX < layxContainerArea[0] / 2) {
                        layxContainer.style.left = container.clientWidth - layxContainerArea[0] + "px";
                    }
                    else if (container.clientWidth - e.clientX > layxContainerArea[0] / 2 && e.clientX >= container.clientWidth - layxContainerArea[0]) {
                        layxContainer.style.left = e.clientX - layxContainerArea[0] / 2 + "px";
                    }

                    el.offset_x = e.clientX - layxContainer.offsetLeft;

                }
                else layxContainer.style.left = _left + "px";
            }
        }

        var dragend = function (e) {
            e = e || window.event;

            document.onmouseup = null;
            document.onmousemove = null;

            if (Drag.isMove) {
                var
                    layxContainerArea = eval("(" + layxContainer.getAttribute("data-area") + ")"),
                    cTop = Number(layxContainer.style.top.replace("px", "")),
                    cLeft = Number(layxContainer.style.left.replace("px", ""));

                maskLayer.style.setProperty("visibility", "hidden");

                layxContainerArea[2] = cTop;
                layxContainerArea[3] = cLeft;
                layxContainer.setAttribute("data-area", "[" + layxContainerArea + "]");

                if (cTop == 0) {
                    utils.Event.emit(core.name + "-" + "maxOrNormal", layxContainer);
                }
            }
            Drag.isMove = false;

            // store window statu
            utils.Event.emit(core.name + `-` + "updateStatus", layxContainer);
        }

        var dragstart = function (e) {
            e = e || window.event;

            var layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal";
            if (layxContainerStatus !== "min" && canMove) {
                maskLayer.style.setProperty("visibility", "visible");
                var layxContainerArea = eval("(" + layxContainer.getAttribute("data-area") + ")");

                if (container.clientWidth < layxContainerArea[0]) {
                    limit = false;
                    lockX = false;
                    lockY = false;
                }
                if (lockX) el.lockX = utils.getCoords(el).left;
                if (lockY) el.lockY = utils.getCoords(el).top;

                if (/a/[-1] == 'a') {
                    el.offset_x = e.layerX;
                    el.offset_y = e.layerY;
                } else {
                    el.offset_x = e.offsetX;
                    el.offset_y = e.offsetY;
                }

                el.offset_x = el.offset_x + el.offsetLeft + 1;
                el.offset_y = el.offset_y + el.offsetTop + 1;

                document.onmouseup = dragend;
                document.onmousemove = drag;

                layxContainer.style.zIndex = ++core.zIndex;
            }

            e.stopPropagation();
            return false;
        };

        Drag.isMove = false;
        el.onmousedown = dragstart;
    }

    // resize

    var Resize = function (canResize, layxContainer, resizeEle, minWidth, minHeight, isLeft, isTop, lockX, lockY) {
        var maskLayer = utils.getElementBySelector(".layx-mask", layxContainer);

        var drag = function (e) {
            e = e || window.event;

            var button = e.button || e.which;
            if (button == 1 && e.shiftKey == false) {
                var iL = e.clientX - resizeEle.disX;
                var iT = e.clientY - resizeEle.disY;

                var maxW = isLeft ? (resizeEle.iParentWidth + resizeEle.iParentLeft - 2) : (document.documentElement.clientWidth - layxContainer.offsetLeft - 2);
                var maxH = isTop ? (resizeEle.iParentHeight + resizeEle.iParentTop - 2) : (document.documentElement.clientHeight - layxContainer.offsetTop - 2);

                var iW = isLeft ? resizeEle.iParentWidth - iL - 2 : resizeEle.offsetWidth + iL;
                var iH = isTop ? resizeEle.iParentHeight - iT - 2 : resizeEle.offsetHeight + iT;

                isLeft && (layxContainer.style.left = resizeEle.iParentLeft + iL + "px");
                isTop && (layxContainer.style.top = resizeEle.iParentTop + iT + "px");

                iW < minWidth && (iW = minWidth);
                iW > maxW && (iW = maxW);
                lockX || (layxContainer.style.width = iW + "px");

                iH < minHeight && (iH = minHeight);
                iH > maxH && (iH = maxH);
                lockY || (layxContainer.style.height = iH + "px");

                if (isLeft && iW == minWidth) {
                    isLeft && (layxContainer.style.left = resizeEle.iParentLeft + resizeEle.iParentWidth - minWidth + "px");
                }
                if (isLeft && iW == maxW) {
                    isLeft && (layxContainer.style.left = "0px");
                }

                if (isTop && iH == minHeight) {
                    isTop && (layxContainer.style.top = resizeEle.iParentTop + resizeEle.iParentHeight - minHeight + "px");
                }
                if (isTop && iH == maxH) {
                    isTop && (layxContainer.style.top = "0px");
                }
            }
        }

        var dragend = function (e) {
            e = e || window.event;

            document.onmouseup = null;
            document.onmousemove = null;

            maskLayer.style.setProperty("visibility", "hidden");

            var cWidth = Number(layxContainer.style.width.replace("px", ""));
            var cHeight = Number(layxContainer.style.height.replace("px", ""));
            var cTop = Number(layxContainer.style.top.replace("px", ""));
            var cLeft = Number(layxContainer.style.left.replace("px", ""));
            layxContainer.setAttribute("data-area", "[" + [cWidth, cHeight, cTop, cLeft] + "]");

            // store window statu
            utils.Event.emit(core.name + `-` + "updateStatus", layxContainer);
        }

        var dragstart = function (e) {

            e = e || window.event;
            var layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal";

            if (layxContainerStatus === "normal" && canResize) {
                maskLayer.style.setProperty("visibility", "visible");


                resizeEle.disX = e.clientX - resizeEle.offsetLeft;
                resizeEle.disY = e.clientY - resizeEle.offsetTop;

                resizeEle.iParentTop = layxContainer.offsetTop;
                resizeEle.iParentLeft = layxContainer.offsetLeft;

                resizeEle.iParentWidth = layxContainer.offsetWidth;
                resizeEle.iParentHeight = layxContainer.offsetHeight;

                document.onmouseup = dragend;
                document.onmousemove = drag;
            }

            e.stopPropagation();
            return false;
        }
        resizeEle.onmousedown = dragstart;
    }

    var methods = {
        screenTransform: function (screen, width, height) {
            var
                visibleWidth = document.documentElement.clientWidth,
                visibleHeight = document.documentElement.clientHeight;

            if (utils.isArray(screen)) {
                return { top: screen[0], left: screen[1] };
            }
            else {
                switch (screen.toString()) {
                    // 中间
                    case "center":
                    default:
                        return { top: Math.ceil((visibleHeight - height - 2) / 2), left: Math.ceil((visibleWidth - width - 2) / 2) };
                        break;
                    // 左上
                    case "lt":
                        return { top: 0, left: 0 };
                        break;
                    // 右上
                    case "rt":
                        return { top: 0, left: visibleWidth - width - 2 };
                        break;
                    // 左下
                    case "lb":
                        return { top: visibleHeight - height - 2, left: 0 };
                        break;
                    // 右下
                    case "rb":
                        return { top: visibleHeight - height - 2, left: visibleWidth - width - 2 };
                        break;
                }
            }
        },
        init: function (options) {
            var
                that = this,
                config = utils.extend({}, defaults, options || {}),
                layxPrefix = core.name + `-`,
                layxId = layxPrefix + config.id,
                pos = that.screenTransform(config.screen, config.width, config.height);

            // html
            var layxHtml = `<div class="layx-iframe" data-type="` + config.content[0] + `" ` + (config.content[0] === "iframe" ? ' data-content="' + config.content[1] + '" ' : '') + ` id="` + layxId + `" style="height:` + config.height + `px;width:` + config.width + `px;z-index:` + (++core.zIndex) + `;top:` + pos.top + `px;left:` + pos.left + `px;">
        <div class="layx-iframe-title layx-flex-row">
            <div class="layx-title-icons"></div>
            <div class="layx-title-label layx-flex-item" title="`+ config.title + `">` + config.title + `</div>
            <div class="layx-title-tools">`+ (config.min === true ? `<a class="layx-title-tool-item layx-min layx-iconfont layx-icon-min"></a>` : ``) + `
                `+ (config.max === true ? `<a class="layx-title-tool-item layx-max-normal layx-iconfont layx-icon-normal"></a>` : ``) + `
                <a class="layx-title-tool-item layx-destroy layx-iconfont layx-icon-destroy"></a>
            </div>
        </div>
        <div class="layx-body">
            <div class="layx-mask"></div>
        </div>
        <div class="layx-drag" `+ (config.resize ? '' : ' style="visibility:hidden;" ') + `>
            <div class="layx-drag-top"></div>
            <div class="layx-drag-bottom"></div>
            <div class="layx-drag-left"></div>
            <div class="layx-drag-right"></div>
            <div class="layx-drag-left-top"></div>
            <div class="layx-drag-right-top"></div>
            <div class="layx-drag-left-bottom"></div>
            <div class="layx-drag-right-bottom"></div>
        </div>
    </div>`;

            utils.InsertAfter(layxHtml);
            core.zIndex++;

            var layxContainer = utils.getElementById(layxId);
            layxContainer.onclick = function () {
                utils.Event.emit(layxPrefix + "setTop", layxContainer);
            }

            that.recoreLayxContainerArea(layxContainer);
            // create iframe
            var iframeContext = that.createIframe(utils.getElementBySelector(".layx-body", layxContainer), config.content[1], function () {
                if (utils.isFunction(config.events.load)) {
                    config.events.load(layxContainer);
                }
            });

            // store window statu
            core.windows[layxId] = {};
            core.windows[layxId]["createDate"] = new Date();
            utils.Event.emit(layxPrefix + "updateStatus", layxContainer);

            // destroy click
            var destroyMenu = utils.getElementBySelector(".layx-icon-destroy", layxContainer);
            destroyMenu.onclick = function (e) {
                e = e || window.event;
                var result;
                if (utils.isFunction(config.events.destroy)) {
                    result = config.events.destroy(iframeContext, layxContainer);
                }
                if (result !== false) {
                    that.destroyIframe(iframeContext);
                    utils.Event.emit(layxPrefix + "destroy", layxContainer);
                }

                e.stopPropagation();
            };

            // max/normal click
            var maxOrNormalMenu = utils.getElementBySelector(".layx-max-normal", layxContainer);
            if (maxOrNormalMenu) {
                maxOrNormalMenu.onclick = function (e) {
                    e = e || window.event;
                    var result;
                    if (utils.isFunction(config.events.maxOrNormal)) {
                        var layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal";
                        result = config.events.maxOrNormal(iframeContext, layxContainerStatus, layxContainer);
                    }
                    if (result !== false) {
                        utils.Event.emit(layxPrefix + "maxOrNormal", layxContainer);
                    }

                    e.stopPropagation();
                };
            }

            // titlelabel dbclick
            var titleLabel = utils.getElementBySelector(".layx-title-label", layxContainer);
            titleLabel.ondblclick = function (e) {
                e = e || window.event;
                utils.Event.emit(layxPrefix + "maxOrNormal", layxContainer);

                e.stopPropagation();
            };
            // move
            new Drag(config.move, titleLabel, layxContainer, { limit: config.moveLock.limit, lockX: config.moveLock.x, lockY: config.moveLock.y });

            // min click
            var minMenu = utils.getElementBySelector(".layx-min", layxContainer);
            if (minMenu) {
                minMenu.onclick = function (e) {
                    e = e || window.event;
                    var result, that = this;

                    if (!that.classList.contains("layx-icon-max")) {
                        if (utils.isFunction(config.events.min)) {
                            result = config.events.min(iframeContext, layxContainer);
                        }
                    }
                    else {
                        if (utils.isFunction(config.events.maxOrNormal)) {
                            var layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal";
                            result = config.events.maxOrNormal(iframeContext, layxContainerStatus, layxContainer);
                        }
                    }

                    if (result !== false) {
                        utils.Event.emit(layxPrefix + "min", layxContainer);
                    }

                    e.stopPropagation();
                };
            }

            // resize
            var
                dragLeft = utils.getElementBySelector('.layx-drag-left', layxContainer),
                dragTop = utils.getElementBySelector('.layx-drag-top', layxContainer),
                dragRight = utils.getElementBySelector('.layx-drag-right', layxContainer),
                dragBottom = utils.getElementBySelector('.layx-drag-bottom', layxContainer),

                dragLeftTop = utils.getElementBySelector('.layx-drag-left-top', layxContainer),
                dragRightTop = utils.getElementBySelector('.layx-drag-right-top', layxContainer),
                dragRightBotom = utils.getElementBySelector('.layx-drag-right-bottom', layxContainer),
                dragLeftBottom = utils.getElementBySelector('.layx-drag-left-bottom', layxContainer);

            new Resize(config.resize, layxContainer, dragLeft, config.minWidth, config.minHeight, true, false, false, true);
            new Resize(config.resize, layxContainer, dragTop, config.minWidth, config.minHeight, false, true, true, false);
            new Resize(config.resize, layxContainer, dragRight, config.minWidth, config.minHeight, false, false, false, true);
            new Resize(config.resize, layxContainer, dragBottom, config.minWidth, config.minHeight, false, false, true, false);

            new Resize(config.resize, layxContainer, dragLeftTop, config.minWidth, config.minHeight, true, true, false, false);
            new Resize(config.resize, layxContainer, dragRightTop, config.minWidth, config.minHeight, false, true, false, false);
            new Resize(config.resize, layxContainer, dragRightBotom, config.minWidth, config.minHeight, false, false, false, false);
            new Resize(config.resize, layxContainer, dragLeftBottom, config.minWidth, config.minHeight, true, false, false, false);

            return layxContainer;
        },
        createIframe: function (pEle, src, onload) {
            var iframe = document.createElement("iframe");

            iframe.classList.add("layx-webcontent");
            iframe.setAttribute("allowtransparency", true);
            iframe.setAttribute("frameborder", 0);
            iframe.setAttribute("scrolling", "auto");

            if (onload && Object.prototype.toString.call(onload) === '[object Function]') {
                if (iframe.attachEvent) {
                    iframe.attachEvent('onload', onload);
                } else if (iframe.addEventListener) {
                    iframe.addEventListener('load', onload);
                } else {
                    iframe.onload = onload;
                }
            }
            iframe.src = src;
            pEle.appendChild(iframe);
            return iframe;
        },
        destroyIframe: function (iframe) {
            iframe.src = 'about:blank';
            try {
                iframe.contentWindow.document.write('');
                iframe.contentWindow.document.clear();
            } catch (e) { }
            iframe.parentNode.removeChild(iframe);
        },
        changeLayxContainerArea: function (layxContainer, areaArray, type) {
            type = type || "size&position";

            if (type.indexOf("size") > -1) {
                layxContainer.style.setProperty('width', areaArray[0] === "auto" ? areaArray[0] : areaArray[0] + 'px');
                layxContainer.style.setProperty('height', areaArray[1] === "auto" ? areaArray[1] : areaArray[1] + 'px');
            }
            if (type.indexOf("position") > -1) {
                layxContainer.style.setProperty('top', areaArray[2] === "auto" ? areaArray[2] : areaArray[2] + 'px');
                layxContainer.style.setProperty('left', areaArray[3] === "auto" ? areaArray[3] : areaArray[3] + 'px');
            }

            layxContainer.style.setProperty('bottom', "auto");
        },
        recoreLayxContainerArea: function (layxContainer) {
            if (!layxContainer.getAttribute("data-area")) {
                layxContainer.setAttribute("data-area", "[" + [layxContainer.offsetWidth - 2, layxContainer.offsetHeight - 2, layxContainer.offsetTop, layxContainer.offsetLeft] + "]");
            }
        },
        layxContainerMinManager: function () {
            var minCount = 0,
                minWidth = 200,
                minHeight = 30,
                padding = 10,
                visibleWidth = document.documentElement.clientWidth,
                lineMaxCount = Math.floor(visibleWidth / minWidth);
            var windows = core.windows;
            if (windows) {
                for (var id in windows) {
                    if (windows[id].status === "min") {
                        methods.changeLayxContainerArea(windows[id].container, [minWidth, minHeight, 'auto', (minCount % lineMaxCount) * (minWidth + padding) + padding]);
                        windows[id].container.style.setProperty('bottom', Math.floor(minCount / lineMaxCount) * (minHeight + padding) + padding + "px");

                        minCount++;
                    }
                }
            }
        }
    };

    var core = {
        v: '1.0.0 beta',
        name: 'layx',
        open: function (options) {
            var that = this;
            var layxId = that.name + '-' + options.id;
            var windowInfo = that.windows[layxId];

            if (windowInfo) {
                var status = windowInfo.status;
                switch (status) {
                    case "min":
                        utils.getElementBySelector(".layx-min", windowInfo.container).click();
                        break;
                }
                windowInfo.container.click();
            }
            else {
                methods.init(options);
            }
        },
        windows: {},
        zIndex: 19920527
    };

    // events
    var eventHandle = function () {
        var layxPrefix = core.name + `-`;
        utils.Event.on(layxPrefix + "destroy", function (layxContainer) {
            var id = layxContainer.getAttribute("id");
            delete core.windows[id];
            layxContainer.parentNode.removeChild(layxContainer);
        });

        utils.Event.on(layxPrefix + "maxOrNormal", function (layxContainer) {
            var
                visibleWidth = document.documentElement.clientWidth,
                visibleHeight = document.documentElement.clientHeight,

                dragContainer = utils.getElementBySelector(".layx-drag", layxContainer),
                maskLayer = utils.getElementBySelector(".layx-mask", layxContainer),

                layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal",
                layxContainerArea = eval("(" + layxContainer.getAttribute("data-area") + ")"),

                maxOrNormalMenu = utils.getElementBySelector(".layx-max-normal", layxContainer),
                minMenu = utils.getElementBySelector(".layx-min", layxContainer);

            if (layxContainerStatus === "normal" || layxContainerStatus === "min") {
                methods.changeLayxContainerArea(layxContainer, [visibleWidth - 2, visibleHeight - 2, 0, 0]);
                layxContainer.setAttribute("data-statu", "max");

                if (maxOrNormalMenu) {
                    maxOrNormalMenu.classList.remove("layx-icon-normal");
                    maxOrNormalMenu.classList.add("layx-icon-max");
                }
                if (minMenu) {
                    if (!minMenu.classList.contains("layx-icon-min")) {
                        minMenu.classList.remove("layx-icon-max");
                        minMenu.classList.add("layx-icon-min");
                    }
                }
                dragContainer.style.setProperty("visibility", "hidden");
            }
            else if (layxContainerStatus === "max") {
                methods.changeLayxContainerArea(layxContainer, layxContainerArea);
                layxContainer.setAttribute("data-statu", "normal");

                if (maxOrNormalMenu) {
                    maxOrNormalMenu.classList.remove("layx-icon-max");
                    maxOrNormalMenu.classList.add("layx-icon-normal");
                }
                dragContainer.style.setProperty("visibility", "visible");
            }

            maskLayer.style.setProperty("visibility", "hidden");

            // store window statu
            utils.Event.emit(layxPrefix + "updateStatus", layxContainer);
        });

        utils.Event.on(layxPrefix + "min", function (layxContainer) {
            var
                visibleWidth = document.documentElement.clientWidth,
                visibleHeight = document.documentElement.clientHeight,

                layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal",
                layxContainerArea = eval("(" + layxContainer.getAttribute("data-area") + ")"),

                dragContainer = utils.getElementBySelector(".layx-drag", layxContainer),

                maxOrNormalMenu = utils.getElementBySelector(".layx-max-normal", layxContainer),
                minMenu = utils.getElementBySelector(".layx-min", layxContainer);

            if (maxOrNormalMenu) {
                if (!maxOrNormalMenu.classList.contains("layx-icon-normal")) {
                    maxOrNormalMenu.classList.remove("layx-icon-max");
                    maxOrNormalMenu.classList.add("layx-icon-normal");
                }
            }

            if (layxContainerStatus === "max") {
                if (minMenu) {
                    minMenu.setAttribute("data-area", "[" + [visibleWidth, visibleHeight, 0, 0] + "]");
                }
            }

            if (minMenu) {
                if (minMenu.classList.contains("layx-icon-max")) {
                    var minStoreArea = minMenu.getAttribute("data-area");
                    layxContainerArea = minStoreArea ? eval("(" + minStoreArea + ")") : layxContainerArea;
                    methods.changeLayxContainerArea(layxContainer, layxContainerArea);

                    if (minStoreArea) {
                        layxContainer.setAttribute("data-statu", "max");
                        if (maxOrNormalMenu) {
                            maxOrNormalMenu.classList.remove("layx-icon-normal");
                            maxOrNormalMenu.classList.add("layx-icon-max");
                        }
                        minMenu.removeAttribute("data-area")
                    }
                    else {
                        dragContainer.style.setProperty("visibility", "visible");
                        layxContainer.setAttribute("data-statu", "normal");
                    }

                    minMenu.classList.remove("layx-icon-max");
                    minMenu.classList.add("layx-icon-min");

                    // store window statu
                    utils.Event.emit(layxPrefix + "updateStatus", layxContainer);
                }
                else {
                    dragContainer.style.setProperty("visibility", "hidden");
                    layxContainer.setAttribute("data-statu", "min");

                    // store window statu
                    utils.Event.emit(layxPrefix + "updateStatus", layxContainer);

                    minMenu.classList.remove("layx-icon-min");
                    minMenu.classList.add("layx-icon-max");

                    methods.layxContainerMinManager();
                }
            }
        });

        utils.Event.on(layxPrefix + "updateStatus", function (layxContainer) {
            var
                id = layxContainer.getAttribute("id"),
                title = utils.getElementBySelector(".layx-title-label", layxContainer).getAttribute("title"),
                layxContainerStatus = layxContainer.getAttribute("data-statu") || "normal",
                layxContainerZindex = layxContainer.style.zIndex,
                layxContainerType = layxContainer.getAttribute("data-type"),
                layxContainerContent = layxContainerType === "iframe" ? layxContainer.getAttribute("data-content") : utils.getElementBySelector(".layx-body", layxContainer).lastElementChild.innerHTML,
                context = layxContainerType === "iframe" ? utils.getElementBySelector(".layx-webcontent", layxContainer).contentWindow : window,
                createDate = core.windows[id].createDate;

            core.windows[id] = {
                id: id,
                title: title,
                status: layxContainerStatus,
                area: { width: Number(layxContainer.style.width.replace("px", "")), height: Number(layxContainer.style.height.replace("px", "")), top: Number(layxContainer.style.top.replace("px", "")), left: Number(layxContainer.style.left.replace("px", "")) },
                zIndex: layxContainerZindex,
                type: layxContainerType,
                content: layxContainerContent,
                container: layxContainer,
                context: context,
                updateDate: new Date(),
                createDate: createDate
            };
        });

        utils.Event.on(layxPrefix + "setTop", function (layxContainer) {
            var maskLayer = utils.getElementBySelector(".layx-mask", layxContainer);
            maskLayer.style.setProperty("visibility", "hidden");
            layxContainer.style.zIndex = ++top.layx.zIndex;

            if (core.windows[layxContainer.getAttribute("id")]) {
                // store window statu
                utils.Event.emit(layxPrefix + "updateStatus", layxContainer);
            }
        });

    }();

    win.layx = core;
    over["layx"] = core;
    over["layx"]["app"] = core;

    window.onresize = function () {
        methods.layxContainerMinManager();
    }
}(top, window);