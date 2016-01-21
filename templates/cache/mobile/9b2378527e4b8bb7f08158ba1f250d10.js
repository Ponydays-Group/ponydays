/*! jQuery v1.7.2 jquery.com | jquery.org/license */
(function(a,b){function cy(a){return f.isWindow(a)?a:a.nodeType===9?a.defaultView||a.parentWindow:!1}function cu(a){if(!cj[a]){var b=c.body,d=f("<"+a+">").appendTo(b),e=d.css("display");d.remove();if(e==="none"||e===""){ck||(ck=c.createElement("iframe"),ck.frameBorder=ck.width=ck.height=0),b.appendChild(ck);if(!cl||!ck.createElement)cl=(ck.contentWindow||ck.contentDocument).document,cl.write((f.support.boxModel?"<!doctype html>":"")+"<html><body>"),cl.close();d=cl.createElement(a),cl.body.appendChild(d),e=f.css(d,"display"),b.removeChild(ck)}cj[a]=e}return cj[a]}function ct(a,b){var c={};f.each(cp.concat.apply([],cp.slice(0,b)),function(){c[this]=a});return c}function cs(){cq=b}function cr(){setTimeout(cs,0);return cq=f.now()}function ci(){try{return new a.ActiveXObject("Microsoft.XMLHTTP")}catch(b){}}function ch(){try{return new a.XMLHttpRequest}catch(b){}}function cb(a,c){a.dataFilter&&(c=a.dataFilter(c,a.dataType));var d=a.dataTypes,e={},g,h,i=d.length,j,k=d[0],l,m,n,o,p;for(g=1;g<i;g++){if(g===1)for(h in a.converters)typeof h=="string"&&(e[h.toLowerCase()]=a.converters[h]);l=k,k=d[g];if(k==="*")k=l;else if(l!=="*"&&l!==k){m=l+" "+k,n=e[m]||e["* "+k];if(!n){p=b;for(o in e){j=o.split(" ");if(j[0]===l||j[0]==="*"){p=e[j[1]+" "+k];if(p){o=e[o],o===!0?n=p:p===!0&&(n=o);break}}}}!n&&!p&&f.error("No conversion from "+m.replace(" "," to ")),n!==!0&&(c=n?n(c):p(o(c)))}}return c}function ca(a,c,d){var e=a.contents,f=a.dataTypes,g=a.responseFields,h,i,j,k;for(i in g)i in d&&(c[g[i]]=d[i]);while(f[0]==="*")f.shift(),h===b&&(h=a.mimeType||c.getResponseHeader("content-type"));if(h)for(i in e)if(e[i]&&e[i].test(h)){f.unshift(i);break}if(f[0]in d)j=f[0];else{for(i in d){if(!f[0]||a.converters[i+" "+f[0]]){j=i;break}k||(k=i)}j=j||k}if(j){j!==f[0]&&f.unshift(j);return d[j]}}function b_(a,b,c,d){if(f.isArray(b))f.each(b,function(b,e){c||bD.test(a)?d(a,e):b_(a+"["+(typeof e=="object"?b:"")+"]",e,c,d)});else if(!c&&f.type(b)==="object")for(var e in b)b_(a+"["+e+"]",b[e],c,d);else d(a,b)}function b$(a,c){var d,e,g=f.ajaxSettings.flatOptions||{};for(d in c)c[d]!==b&&((g[d]?a:e||(e={}))[d]=c[d]);e&&f.extend(!0,a,e)}function bZ(a,c,d,e,f,g){f=f||c.dataTypes[0],g=g||{},g[f]=!0;var h=a[f],i=0,j=h?h.length:0,k=a===bS,l;for(;i<j&&(k||!l);i++)l=h[i](c,d,e),typeof l=="string"&&(!k||g[l]?l=b:(c.dataTypes.unshift(l),l=bZ(a,c,d,e,l,g)));(k||!l)&&!g["*"]&&(l=bZ(a,c,d,e,"*",g));return l}function bY(a){return function(b,c){typeof b!="string"&&(c=b,b="*");if(f.isFunction(c)){var d=b.toLowerCase().split(bO),e=0,g=d.length,h,i,j;for(;e<g;e++)h=d[e],j=/^\+/.test(h),j&&(h=h.substr(1)||"*"),i=a[h]=a[h]||[],i[j?"unshift":"push"](c)}}}function bB(a,b,c){var d=b==="width"?a.offsetWidth:a.offsetHeight,e=b==="width"?1:0,g=4;if(d>0){if(c!=="border")for(;e<g;e+=2)c||(d-=parseFloat(f.css(a,"padding"+bx[e]))||0),c==="margin"?d+=parseFloat(f.css(a,c+bx[e]))||0:d-=parseFloat(f.css(a,"border"+bx[e]+"Width"))||0;return d+"px"}d=by(a,b);if(d<0||d==null)d=a.style[b];if(bt.test(d))return d;d=parseFloat(d)||0;if(c)for(;e<g;e+=2)d+=parseFloat(f.css(a,"padding"+bx[e]))||0,c!=="padding"&&(d+=parseFloat(f.css(a,"border"+bx[e]+"Width"))||0),c==="margin"&&(d+=parseFloat(f.css(a,c+bx[e]))||0);return d+"px"}function bo(a){var b=c.createElement("div");bh.appendChild(b),b.innerHTML=a.outerHTML;return b.firstChild}function bn(a){var b=(a.nodeName||"").toLowerCase();b==="input"?bm(a):b!=="script"&&typeof a.getElementsByTagName!="undefined"&&f.grep(a.getElementsByTagName("input"),bm)}function bm(a){if(a.type==="checkbox"||a.type==="radio")a.defaultChecked=a.checked}function bl(a){return typeof a.getElementsByTagName!="undefined"?a.getElementsByTagName("*"):typeof a.querySelectorAll!="undefined"?a.querySelectorAll("*"):[]}function bk(a,b){var c;b.nodeType===1&&(b.clearAttributes&&b.clearAttributes(),b.mergeAttributes&&b.mergeAttributes(a),c=b.nodeName.toLowerCase(),c==="object"?b.outerHTML=a.outerHTML:c!=="input"||a.type!=="checkbox"&&a.type!=="radio"?c==="option"?b.selected=a.defaultSelected:c==="input"||c==="textarea"?b.defaultValue=a.defaultValue:c==="script"&&b.text!==a.text&&(b.text=a.text):(a.checked&&(b.defaultChecked=b.checked=a.checked),b.value!==a.value&&(b.value=a.value)),b.removeAttribute(f.expando),b.removeAttribute("_submit_attached"),b.removeAttribute("_change_attached"))}function bj(a,b){if(b.nodeType===1&&!!f.hasData(a)){var c,d,e,g=f._data(a),h=f._data(b,g),i=g.events;if(i){delete h.handle,h.events={};for(c in i)for(d=0,e=i[c].length;d<e;d++)f.event.add(b,c,i[c][d])}h.data&&(h.data=f.extend({},h.data))}}function bi(a,b){return f.nodeName(a,"table")?a.getElementsByTagName("tbody")[0]||a.appendChild(a.ownerDocument.createElement("tbody")):a}function U(a){var b=V.split("|"),c=a.createDocumentFragment();if(c.createElement)while(b.length)c.createElement(b.pop());return c}function T(a,b,c){b=b||0;if(f.isFunction(b))return f.grep(a,function(a,d){var e=!!b.call(a,d,a);return e===c});if(b.nodeType)return f.grep(a,function(a,d){return a===b===c});if(typeof b=="string"){var d=f.grep(a,function(a){return a.nodeType===1});if(O.test(b))return f.filter(b,d,!c);b=f.filter(b,d)}return f.grep(a,function(a,d){return f.inArray(a,b)>=0===c})}function S(a){return!a||!a.parentNode||a.parentNode.nodeType===11}function K(){return!0}function J(){return!1}function n(a,b,c){var d=b+"defer",e=b+"queue",g=b+"mark",h=f._data(a,d);h&&(c==="queue"||!f._data(a,e))&&(c==="mark"||!f._data(a,g))&&setTimeout(function(){!f._data(a,e)&&!f._data(a,g)&&(f.removeData(a,d,!0),h.fire())},0)}function m(a){for(var b in a){if(b==="data"&&f.isEmptyObject(a[b]))continue;if(b!=="toJSON")return!1}return!0}function l(a,c,d){if(d===b&&a.nodeType===1){var e="data-"+c.replace(k,"-$1").toLowerCase();d=a.getAttribute(e);if(typeof d=="string"){try{d=d==="true"?!0:d==="false"?!1:d==="null"?null:f.isNumeric(d)?+d:j.test(d)?f.parseJSON(d):d}catch(g){}f.data(a,c,d)}else d=b}return d}function h(a){var b=g[a]={},c,d;a=a.split(/\s+/);for(c=0,d=a.length;c<d;c++)b[a[c]]=!0;return b}var c=a.document,d=a.navigator,e=a.location,f=function(){function J(){if(!e.isReady){try{c.documentElement.doScroll("left")}catch(a){setTimeout(J,1);return}e.ready()}}var e=function(a,b){return new e.fn.init(a,b,h)},f=a.jQuery,g=a.$,h,i=/^(?:[^#<]*(<[\w\W]+>)[^>]*$|#([\w\-]*)$)/,j=/\S/,k=/^\s+/,l=/\s+$/,m=/^<(\w+)\s*\/?>(?:<\/\1>)?$/,n=/^[\],:{}\s]*$/,o=/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,p=/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,q=/(?:^|:|,)(?:\s*\[)+/g,r=/(webkit)[ \/]([\w.]+)/,s=/(opera)(?:.*version)?[ \/]([\w.]+)/,t=/(msie) ([\w.]+)/,u=/(mozilla)(?:.*? rv:([\w.]+))?/,v=/-([a-z]|[0-9])/ig,w=/^-ms-/,x=function(a,b){return(b+"").toUpperCase()},y=d.userAgent,z,A,B,C=Object.prototype.toString,D=Object.prototype.hasOwnProperty,E=Array.prototype.push,F=Array.prototype.slice,G=String.prototype.trim,H=Array.prototype.indexOf,I={};e.fn=e.prototype={constructor:e,init:function(a,d,f){var g,h,j,k;if(!a)return this;if(a.nodeType){this.context=this[0]=a,this.length=1;return this}if(a==="body"&&!d&&c.body){this.context=c,this[0]=c.body,this.selector=a,this.length=1;return this}if(typeof a=="string"){a.charAt(0)!=="<"||a.charAt(a.length-1)!==">"||a.length<3?g=i.exec(a):g=[null,a,null];if(g&&(g[1]||!d)){if(g[1]){d=d instanceof e?d[0]:d,k=d?d.ownerDocument||d:c,j=m.exec(a),j?e.isPlainObject(d)?(a=[c.createElement(j[1])],e.fn.attr.call(a,d,!0)):a=[k.createElement(j[1])]:(j=e.buildFragment([g[1]],[k]),a=(j.cacheable?e.clone(j.fragment):j.fragment).childNodes);return e.merge(this,a)}h=c.getElementById(g[2]);if(h&&h.parentNode){if(h.id!==g[2])return f.find(a);this.length=1,this[0]=h}this.context=c,this.selector=a;return this}return!d||d.jquery?(d||f).find(a):this.constructor(d).find(a)}if(e.isFunction(a))return f.ready(a);a.selector!==b&&(this.selector=a.selector,this.context=a.context);return e.makeArray(a,this)},selector:"",jquery:"1.7.2",length:0,size:function(){return this.length},toArray:function(){return F.call(this,0)},get:function(a){return a==null?this.toArray():a<0?this[this.length+a]:this[a]},pushStack:function(a,b,c){var d=this.constructor();e.isArray(a)?E.apply(d,a):e.merge(d,a),d.prevObject=this,d.context=this.context,b==="find"?d.selector=this.selector+(this.selector?" ":"")+c:b&&(d.selector=this.selector+"."+b+"("+c+")");return d},each:function(a,b){return e.each(this,a,b)},ready:function(a){e.bindReady(),A.add(a);return this},eq:function(a){a=+a;return a===-1?this.slice(a):this.slice(a,a+1)},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},slice:function(){return this.pushStack(F.apply(this,arguments),"slice",F.call(arguments).join(","))},map:function(a){return this.pushStack(e.map(this,function(b,c){return a.call(b,c,b)}))},end:function(){return this.prevObject||this.constructor(null)},push:E,sort:[].sort,splice:[].splice},e.fn.init.prototype=e.fn,e.extend=e.fn.extend=function(){var a,c,d,f,g,h,i=arguments[0]||{},j=1,k=arguments.length,l=!1;typeof i=="boolean"&&(l=i,i=arguments[1]||{},j=2),typeof i!="object"&&!e.isFunction(i)&&(i={}),k===j&&(i=this,--j);for(;j<k;j++)if((a=arguments[j])!=null)for(c in a){d=i[c],f=a[c];if(i===f)continue;l&&f&&(e.isPlainObject(f)||(g=e.isArray(f)))?(g?(g=!1,h=d&&e.isArray(d)?d:[]):h=d&&e.isPlainObject(d)?d:{},i[c]=e.extend(l,h,f)):f!==b&&(i[c]=f)}return i},e.extend({noConflict:function(b){a.$===e&&(a.$=g),b&&a.jQuery===e&&(a.jQuery=f);return e},isReady:!1,readyWait:1,holdReady:function(a){a?e.readyWait++:e.ready(!0)},ready:function(a){if(a===!0&&!--e.readyWait||a!==!0&&!e.isReady){if(!c.body)return setTimeout(e.ready,1);e.isReady=!0;if(a!==!0&&--e.readyWait>0)return;A.fireWith(c,[e]),e.fn.trigger&&e(c).trigger("ready").off("ready")}},bindReady:function(){if(!A){A=e.Callbacks("once memory");if(c.readyState==="complete")return setTimeout(e.ready,1);if(c.addEventListener)c.addEventListener("DOMContentLoaded",B,!1),a.addEventListener("load",e.ready,!1);else if(c.attachEvent){c.attachEvent("onreadystatechange",B),a.attachEvent("onload",e.ready);var b=!1;try{b=a.frameElement==null}catch(d){}c.documentElement.doScroll&&b&&J()}}},isFunction:function(a){return e.type(a)==="function"},isArray:Array.isArray||function(a){return e.type(a)==="array"},isWindow:function(a){return a!=null&&a==a.window},isNumeric:function(a){return!isNaN(parseFloat(a))&&isFinite(a)},type:function(a){return a==null?String(a):I[C.call(a)]||"object"},isPlainObject:function(a){if(!a||e.type(a)!=="object"||a.nodeType||e.isWindow(a))return!1;try{if(a.constructor&&!D.call(a,"constructor")&&!D.call(a.constructor.prototype,"isPrototypeOf"))return!1}catch(c){return!1}var d;for(d in a);return d===b||D.call(a,d)},isEmptyObject:function(a){for(var b in a)return!1;return!0},error:function(a){throw new Error(a)},parseJSON:function(b){if(typeof b!="string"||!b)return null;b=e.trim(b);if(a.JSON&&a.JSON.parse)return a.JSON.parse(b);if(n.test(b.replace(o,"@").replace(p,"]").replace(q,"")))return(new Function("return "+b))();e.error("Invalid JSON: "+b)},parseXML:function(c){if(typeof c!="string"||!c)return null;var d,f;try{a.DOMParser?(f=new DOMParser,d=f.parseFromString(c,"text/xml")):(d=new ActiveXObject("Microsoft.XMLDOM"),d.async="false",d.loadXML(c))}catch(g){d=b}(!d||!d.documentElement||d.getElementsByTagName("parsererror").length)&&e.error("Invalid XML: "+c);return d},noop:function(){},globalEval:function(b){b&&j.test(b)&&(a.execScript||function(b){a.eval.call(a,b)})(b)},camelCase:function(a){return a.replace(w,"ms-").replace(v,x)},nodeName:function(a,b){return a.nodeName&&a.nodeName.toUpperCase()===b.toUpperCase()},each:function(a,c,d){var f,g=0,h=a.length,i=h===b||e.isFunction(a);if(d){if(i){for(f in a)if(c.apply(a[f],d)===!1)break}else for(;g<h;)if(c.apply(a[g++],d)===!1)break}else if(i){for(f in a)if(c.call(a[f],f,a[f])===!1)break}else for(;g<h;)if(c.call(a[g],g,a[g++])===!1)break;return a},trim:G?function(a){return a==null?"":G.call(a)}:function(a){return a==null?"":(a+"").replace(k,"").replace(l,"")},makeArray:function(a,b){var c=b||[];if(a!=null){var d=e.type(a);a.length==null||d==="string"||d==="function"||d==="regexp"||e.isWindow(a)?E.call(c,a):e.merge(c,a)}return c},inArray:function(a,b,c){var d;if(b){if(H)return H.call(b,a,c);d=b.length,c=c?c<0?Math.max(0,d+c):c:0;for(;c<d;c++)if(c in b&&b[c]===a)return c}return-1},merge:function(a,c){var d=a.length,e=0;if(typeof c.length=="number")for(var f=c.length;e<f;e++)a[d++]=c[e];else while(c[e]!==b)a[d++]=c[e++];a.length=d;return a},grep:function(a,b,c){var d=[],e;c=!!c;for(var f=0,g=a.length;f<g;f++)e=!!b(a[f],f),c!==e&&d.push(a[f]);return d},map:function(a,c,d){var f,g,h=[],i=0,j=a.length,k=a instanceof e||j!==b&&typeof j=="number"&&(j>0&&a[0]&&a[j-1]||j===0||e.isArray(a));if(k)for(;i<j;i++)f=c(a[i],i,d),f!=null&&(h[h.length]=f);else for(g in a)f=c(a[g],g,d),f!=null&&(h[h.length]=f);return h.concat.apply([],h)},guid:1,proxy:function(a,c){if(typeof c=="string"){var d=a[c];c=a,a=d}if(!e.isFunction(a))return b;var f=F.call(arguments,2),g=function(){return a.apply(c,f.concat(F.call(arguments)))};g.guid=a.guid=a.guid||g.guid||e.guid++;return g},access:function(a,c,d,f,g,h,i){var j,k=d==null,l=0,m=a.length;if(d&&typeof d=="object"){for(l in d)e.access(a,c,l,d[l],1,h,f);g=1}else if(f!==b){j=i===b&&e.isFunction(f),k&&(j?(j=c,c=function(a,b,c){return j.call(e(a),c)}):(c.call(a,f),c=null));if(c)for(;l<m;l++)c(a[l],d,j?f.call(a[l],l,c(a[l],d)):f,i);g=1}return g?a:k?c.call(a):m?c(a[0],d):h},now:function(){return(new Date).getTime()},uaMatch:function(a){a=a.toLowerCase();var b=r.exec(a)||s.exec(a)||t.exec(a)||a.indexOf("compatible")<0&&u.exec(a)||[];return{browser:b[1]||"",version:b[2]||"0"}},sub:function(){function a(b,c){return new a.fn.init(b,c)}e.extend(!0,a,this),a.superclass=this,a.fn=a.prototype=this(),a.fn.constructor=a,a.sub=this.sub,a.fn.init=function(d,f){f&&f instanceof e&&!(f instanceof a)&&(f=a(f));return e.fn.init.call(this,d,f,b)},a.fn.init.prototype=a.fn;var b=a(c);return a},browser:{}}),e.each("Boolean Number String Function Array Date RegExp Object".split(" "),function(a,b){I["[object "+b+"]"]=b.toLowerCase()}),z=e.uaMatch(y),z.browser&&(e.browser[z.browser]=!0,e.browser.version=z.version),e.browser.webkit&&(e.browser.safari=!0),j.test(" ")&&(k=/^[\s\xA0]+/,l=/[\s\xA0]+$/),h=e(c),c.addEventListener?B=function(){c.removeEventListener("DOMContentLoaded",B,!1),e.ready()}:c.attachEvent&&(B=function(){c.readyState==="complete"&&(c.detachEvent("onreadystatechange",B),e.ready())});return e}(),g={};f.Callbacks=function(a){a=a?g[a]||h(a):{};var c=[],d=[],e,i,j,k,l,m,n=function(b){var d,e,g,h,i;for(d=0,e=b.length;d<e;d++)g=b[d],h=f.type(g),h==="array"?n(g):h==="function"&&(!a.unique||!p.has(g))&&c.push(g)},o=function(b,f){f=f||[],e=!a.memory||[b,f],i=!0,j=!0,m=k||0,k=0,l=c.length;for(;c&&m<l;m++)if(c[m].apply(b,f)===!1&&a.stopOnFalse){e=!0;break}j=!1,c&&(a.once?e===!0?p.disable():c=[]:d&&d.length&&(e=d.shift(),p.fireWith(e[0],e[1])))},p={add:function(){if(c){var a=c.length;n(arguments),j?l=c.length:e&&e!==!0&&(k=a,o(e[0],e[1]))}return this},remove:function(){if(c){var b=arguments,d=0,e=b.length;for(;d<e;d++)for(var f=0;f<c.length;f++)if(b[d]===c[f]){j&&f<=l&&(l--,f<=m&&m--),c.splice(f--,1);if(a.unique)break}}return this},has:function(a){if(c){var b=0,d=c.length;for(;b<d;b++)if(a===c[b])return!0}return!1},empty:function(){c=[];return this},disable:function(){c=d=e=b;return this},disabled:function(){return!c},lock:function(){d=b,(!e||e===!0)&&p.disable();return this},locked:function(){return!d},fireWith:function(b,c){d&&(j?a.once||d.push([b,c]):(!a.once||!e)&&o(b,c));return this},fire:function(){p.fireWith(this,arguments);return this},fired:function(){return!!i}};return p};var i=[].slice;f.extend({Deferred:function(a){var b=f.Callbacks("once memory"),c=f.Callbacks("once memory"),d=f.Callbacks("memory"),e="pending",g={resolve:b,reject:c,notify:d},h={done:b.add,fail:c.add,progress:d.add,state:function(){return e},isResolved:b.fired,isRejected:c.fired,then:function(a,b,c){i.done(a).fail(b).progress(c);return this},always:function(){i.done.apply(i,arguments).fail.apply(i,arguments);return this},pipe:function(a,b,c){return f.Deferred(function(d){f.each({done:[a,"resolve"],fail:[b,"reject"],progress:[c,"notify"]},function(a,b){var c=b[0],e=b[1],g;f.isFunction(c)?i[a](function(){g=c.apply(this,arguments),g&&f.isFunction(g.promise)?g.promise().then(d.resolve,d.reject,d.notify):d[e+"With"](this===i?d:this,[g])}):i[a](d[e])})}).promise()},promise:function(a){if(a==null)a=h;else for(var b in h)a[b]=h[b];return a}},i=h.promise({}),j;for(j in g)i[j]=g[j].fire,i[j+"With"]=g[j].fireWith;i.done(function(){e="resolved"},c.disable,d.lock).fail(function(){e="rejected"},b.disable,d.lock),a&&a.call(i,i);return i},when:function(a){function m(a){return function(b){e[a]=arguments.length>1?i.call(arguments,0):b,j.notifyWith(k,e)}}function l(a){return function(c){b[a]=arguments.length>1?i.call(arguments,0):c,--g||j.resolveWith(j,b)}}var b=i.call(arguments,0),c=0,d=b.length,e=Array(d),g=d,h=d,j=d<=1&&a&&f.isFunction(a.promise)?a:f.Deferred(),k=j.promise();if(d>1){for(;c<d;c++)b[c]&&b[c].promise&&f.isFunction(b[c].promise)?b[c].promise().then(l(c),j.reject,m(c)):--g;g||j.resolveWith(j,b)}else j!==a&&j.resolveWith(j,d?[a]:[]);return k}}),f.support=function(){var b,d,e,g,h,i,j,k,l,m,n,o,p=c.createElement("div"),q=c.documentElement;p.setAttribute("className","t"),p.innerHTML="   <link/><table></table><a href='/a' style='top:1px;float:left;opacity:.55;'>a</a><input type='checkbox'/>",d=p.getElementsByTagName("*"),e=p.getElementsByTagName("a")[0];if(!d||!d.length||!e)return{};g=c.createElement("select"),h=g.appendChild(c.createElement("option")),i=p.getElementsByTagName("input")[0],b={leadingWhitespace:p.firstChild.nodeType===3,tbody:!p.getElementsByTagName("tbody").length,htmlSerialize:!!p.getElementsByTagName("link").length,style:/top/.test(e.getAttribute("style")),hrefNormalized:e.getAttribute("href")==="/a",opacity:/^0.55/.test(e.style.opacity),cssFloat:!!e.style.cssFloat,checkOn:i.value==="on",optSelected:h.selected,getSetAttribute:p.className!=="t",enctype:!!c.createElement("form").enctype,html5Clone:c.createElement("nav").cloneNode(!0).outerHTML!=="<:nav></:nav>",submitBubbles:!0,changeBubbles:!0,focusinBubbles:!1,deleteExpando:!0,noCloneEvent:!0,inlineBlockNeedsLayout:!1,shrinkWrapBlocks:!1,reliableMarginRight:!0,pixelMargin:!0},f.boxModel=b.boxModel=c.compatMode==="CSS1Compat",i.checked=!0,b.noCloneChecked=i.cloneNode(!0).checked,g.disabled=!0,b.optDisabled=!h.disabled;try{delete p.test}catch(r){b.deleteExpando=!1}!p.addEventListener&&p.attachEvent&&p.fireEvent&&(p.attachEvent("onclick",function(){b.noCloneEvent=!1}),p.cloneNode(!0).fireEvent("onclick")),i=c.createElement("input"),i.value="t",i.setAttribute("type","radio"),b.radioValue=i.value==="t",i.setAttribute("checked","checked"),i.setAttribute("name","t"),p.appendChild(i),j=c.createDocumentFragment(),j.appendChild(p.lastChild),b.checkClone=j.cloneNode(!0).cloneNode(!0).lastChild.checked,b.appendChecked=i.checked,j.removeChild(i),j.appendChild(p);if(p.attachEvent)for(n in{submit:1,change:1,focusin:1})m="on"+n,o=m in p,o||(p.setAttribute(m,"return;"),o=typeof p[m]=="function"),b[n+"Bubbles"]=o;j.removeChild(p),j=g=h=p=i=null,f(function(){var d,e,g,h,i,j,l,m,n,q,r,s,t,u=c.getElementsByTagName("body")[0];!u||(m=1,t="padding:0;margin:0;border:",r="position:absolute;top:0;left:0;width:1px;height:1px;",s=t+"0;visibility:hidden;",n="style='"+r+t+"5px solid #000;",q="<div "+n+"display:block;'><div style='"+t+"0;display:block;overflow:hidden;'></div></div>"+"<table "+n+"' cellpadding='0' cellspacing='0'>"+"<tr><td></td></tr></table>",d=c.createElement("div"),d.style.cssText=s+"width:0;height:0;position:static;top:0;margin-top:"+m+"px",u.insertBefore(d,u.firstChild),p=c.createElement("div"),d.appendChild(p),p.innerHTML="<table><tr><td style='"+t+"0;display:none'></td><td>t</td></tr></table>",k=p.getElementsByTagName("td"),o=k[0].offsetHeight===0,k[0].style.display="",k[1].style.display="none",b.reliableHiddenOffsets=o&&k[0].offsetHeight===0,a.getComputedStyle&&(p.innerHTML="",l=c.createElement("div"),l.style.width="0",l.style.marginRight="0",p.style.width="2px",p.appendChild(l),b.reliableMarginRight=(parseInt((a.getComputedStyle(l,null)||{marginRight:0}).marginRight,10)||0)===0),typeof p.style.zoom!="undefined"&&(p.innerHTML="",p.style.width=p.style.padding="1px",p.style.border=0,p.style.overflow="hidden",p.style.display="inline",p.style.zoom=1,b.inlineBlockNeedsLayout=p.offsetWidth===3,p.style.display="block",p.style.overflow="visible",p.innerHTML="<div style='width:5px;'></div>",b.shrinkWrapBlocks=p.offsetWidth!==3),p.style.cssText=r+s,p.innerHTML=q,e=p.firstChild,g=e.firstChild,i=e.nextSibling.firstChild.firstChild,j={doesNotAddBorder:g.offsetTop!==5,doesAddBorderForTableAndCells:i.offsetTop===5},g.style.position="fixed",g.style.top="20px",j.fixedPosition=g.offsetTop===20||g.offsetTop===15,g.style.position=g.style.top="",e.style.overflow="hidden",e.style.position="relative",j.subtractsBorderForOverflowNotVisible=g.offsetTop===-5,j.doesNotIncludeMarginInBodyOffset=u.offsetTop!==m,a.getComputedStyle&&(p.style.marginTop="1%",b.pixelMargin=(a.getComputedStyle(p,null)||{marginTop:0}).marginTop!=="1%"),typeof d.style.zoom!="undefined"&&(d.style.zoom=1),u.removeChild(d),l=p=d=null,f.extend(b,j))});return b}();var j=/^(?:\{.*\}|\[.*\])$/,k=/([A-Z])/g;f.extend({cache:{},uuid:0,expando:"jQuery"+(f.fn.jquery+Math.random()).replace(/\D/g,""),noData:{embed:!0,object:"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000",applet:!0},hasData:function(a){a=a.nodeType?f.cache[a[f.expando]]:a[f.expando];return!!a&&!m(a)},data:function(a,c,d,e){if(!!f.acceptData(a)){var g,h,i,j=f.expando,k=typeof c=="string",l=a.nodeType,m=l?f.cache:a,n=l?a[j]:a[j]&&j,o=c==="events";if((!n||!m[n]||!o&&!e&&!m[n].data)&&k&&d===b)return;n||(l?a[j]=n=++f.uuid:n=j),m[n]||(m[n]={},l||(m[n].toJSON=f.noop));if(typeof c=="object"||typeof c=="function")e?m[n]=f.extend(m[n],c):m[n].data=f.extend(m[n].data,c);g=h=m[n],e||(h.data||(h.data={}),h=h.data),d!==b&&(h[f.camelCase(c)]=d);if(o&&!h[c])return g.events;k?(i=h[c],i==null&&(i=h[f.camelCase(c)])):i=h;return i}},removeData:function(a,b,c){if(!!f.acceptData(a)){var d,e,g,h=f.expando,i=a.nodeType,j=i?f.cache:a,k=i?a[h]:h;if(!j[k])return;if(b){d=c?j[k]:j[k].data;if(d){f.isArray(b)||(b in d?b=[b]:(b=f.camelCase(b),b in d?b=[b]:b=b.split(" ")));for(e=0,g=b.length;e<g;e++)delete d[b[e]];if(!(c?m:f.isEmptyObject)(d))return}}if(!c){delete j[k].data;if(!m(j[k]))return}f.support.deleteExpando||!j.setInterval?delete j[k]:j[k]=null,i&&(f.support.deleteExpando?delete a[h]:a.removeAttribute?a.removeAttribute(h):a[h]=null)}},_data:function(a,b,c){return f.data(a,b,c,!0)},acceptData:function(a){if(a.nodeName){var b=f.noData[a.nodeName.toLowerCase()];if(b)return b!==!0&&a.getAttribute("classid")===b}return!0}}),f.fn.extend({data:function(a,c){var d,e,g,h,i,j=this[0],k=0,m=null;if(a===b){if(this.length){m=f.data(j);if(j.nodeType===1&&!f._data(j,"parsedAttrs")){g=j.attributes;for(i=g.length;k<i;k++)h=g[k].name,h.indexOf("data-")===0&&(h=f.camelCase(h.substring(5)),l(j,h,m[h]));f._data(j,"parsedAttrs",!0)}}return m}if(typeof a=="object")return this.each(function(){f.data(this,a)});d=a.split(".",2),d[1]=d[1]?"."+d[1]:"",e=d[1]+"!";return f.access(this,function(c){if(c===b){m=this.triggerHandler("getData"+e,[d[0]]),m===b&&j&&(m=f.data(j,a),m=l(j,a,m));return m===b&&d[1]?this.data(d[0]):m}d[1]=c,this.each(function(){var b=f(this);b.triggerHandler("setData"+e,d),f.data(this,a,c),b.triggerHandler("changeData"+e,d)})},null,c,arguments.length>1,null,!1)},removeData:function(a){return this.each(function(){f.removeData(this,a)})}}),f.extend({_mark:function(a,b){a&&(b=(b||"fx")+"mark",f._data(a,b,(f._data(a,b)||0)+1))},_unmark:function(a,b,c){a!==!0&&(c=b,b=a,a=!1);if(b){c=c||"fx";var d=c+"mark",e=a?0:(f._data(b,d)||1)-1;e?f._data(b,d,e):(f.removeData(b,d,!0),n(b,c,"mark"))}},queue:function(a,b,c){var d;if(a){b=(b||"fx")+"queue",d=f._data(a,b),c&&(!d||f.isArray(c)?d=f._data(a,b,f.makeArray(c)):d.push(c));return d||[]}},dequeue:function(a,b){b=b||"fx";var c=f.queue(a,b),d=c.shift(),e={};d==="inprogress"&&(d=c.shift()),d&&(b==="fx"&&c.unshift("inprogress"),f._data(a,b+".run",e),d.call(a,function(){f.dequeue(a,b)},e)),c.length||(f.removeData(a,b+"queue "+b+".run",!0),n(a,b,"queue"))}}),f.fn.extend({queue:function(a,c){var d=2;typeof a!="string"&&(c=a,a="fx",d--);if(arguments.length<d)return f.queue(this[0],a);return c===b?this:this.each(function(){var b=f.queue(this,a,c);a==="fx"&&b[0]!=="inprogress"&&f.dequeue(this,a)})},dequeue:function(a){return this.each(function(){f.dequeue(this,a)})},delay:function(a,b){a=f.fx?f.fx.speeds[a]||a:a,b=b||"fx";return this.queue(b,function(b,c){var d=setTimeout(b,a);c.stop=function(){clearTimeout(d)}})},clearQueue:function(a){return this.queue(a||"fx",[])},promise:function(a,c){function m(){--h||d.resolveWith(e,[e])}typeof a!="string"&&(c=a,a=b),a=a||"fx";var d=f.Deferred(),e=this,g=e.length,h=1,i=a+"defer",j=a+"queue",k=a+"mark",l;while(g--)if(l=f.data(e[g],i,b,!0)||(f.data(e[g],j,b,!0)||f.data(e[g],k,b,!0))&&f.data(e[g],i,f.Callbacks("once memory"),!0))h++,l.add(m);m();return d.promise(c)}});var o=/[\n\t\r]/g,p=/\s+/,q=/\r/g,r=/^(?:button|input)$/i,s=/^(?:button|input|object|select|textarea)$/i,t=/^a(?:rea)?$/i,u=/^(?:autofocus|autoplay|async|checked|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped|selected)$/i,v=f.support.getSetAttribute,w,x,y;f.fn.extend({attr:function(a,b){return f.access(this,f.attr,a,b,arguments.length>1)},removeAttr:function(a){return this.each(function(){f.removeAttr(this,a)})},prop:function(a,b){return f.access(this,f.prop,a,b,arguments.length>1)},removeProp:function(a){a=f.propFix[a]||a;return this.each(function(){try{this[a]=b,delete this[a]}catch(c){}})},addClass:function(a){var b,c,d,e,g,h,i;if(f.isFunction(a))return this.each(function(b){f(this).addClass(a.call(this,b,this.className))});if(a&&typeof a=="string"){b=a.split(p);for(c=0,d=this.length;c<d;c++){e=this[c];if(e.nodeType===1)if(!e.className&&b.length===1)e.className=a;else{g=" "+e.className+" ";for(h=0,i=b.length;h<i;h++)~g.indexOf(" "+b[h]+" ")||(g+=b[h]+" ");e.className=f.trim(g)}}}return this},removeClass:function(a){var c,d,e,g,h,i,j;if(f.isFunction(a))return this.each(function(b){f(this).removeClass(a.call(this,b,this.className))});if(a&&typeof a=="string"||a===b){c=(a||"").split(p);for(d=0,e=this.length;d<e;d++){g=this[d];if(g.nodeType===1&&g.className)if(a){h=(" "+g.className+" ").replace(o," ");for(i=0,j=c.length;i<j;i++)h=h.replace(" "+c[i]+" "," ");g.className=f.trim(h)}else g.className=""}}return this},toggleClass:function(a,b){var c=typeof a,d=typeof b=="boolean";if(f.isFunction(a))return this.each(function(c){f(this).toggleClass(a.call(this,c,this.className,b),b)});return this.each(function(){if(c==="string"){var e,g=0,h=f(this),i=b,j=a.split(p);while(e=j[g++])i=d?i:!h.hasClass(e),h[i?"addClass":"removeClass"](e)}else if(c==="undefined"||c==="boolean")this.className&&f._data(this,"__className__",this.className),this.className=this.className||a===!1?"":f._data(this,"__className__")||""})},hasClass:function(a){var b=" "+a+" ",c=0,d=this.length;for(;c<d;c++)if(this[c].nodeType===1&&(" "+this[c].className+" ").replace(o," ").indexOf(b)>-1)return!0;return!1},val:function(a){var c,d,e,g=this[0];{if(!!arguments.length){e=f.isFunction(a);return this.each(function(d){var g=f(this),h;if(this.nodeType===1){e?h=a.call(this,d,g.val()):h=a,h==null?h="":typeof h=="number"?h+="":f.isArray(h)&&(h=f.map(h,function(a){return a==null?"":a+""})),c=f.valHooks[this.type]||f.valHooks[this.nodeName.toLowerCase()];if(!c||!("set"in c)||c.set(this,h,"value")===b)this.value=h}})}if(g){c=f.valHooks[g.type]||f.valHooks[g.nodeName.toLowerCase()];if(c&&"get"in c&&(d=c.get(g,"value"))!==b)return d;d=g.value;return typeof d=="string"?d.replace(q,""):d==null?"":d}}}}),f.extend({valHooks:{option:{get:function(a){var b=a.attributes.value;return!b||b.specified?a.value:a.text}},select:{get:function(a){var b,c,d,e,g=a.selectedIndex,h=[],i=a.options,j=a.type==="select-one";if(g<0)return null;c=j?g:0,d=j?g+1:i.length;for(;c<d;c++){e=i[c];if(e.selected&&(f.support.optDisabled?!e.disabled:e.getAttribute("disabled")===null)&&(!e.parentNode.disabled||!f.nodeName(e.parentNode,"optgroup"))){b=f(e).val();if(j)return b;h.push(b)}}if(j&&!h.length&&i.length)return f(i[g]).val();return h},set:function(a,b){var c=f.makeArray(b);f(a).find("option").each(function(){this.selected=f.inArray(f(this).val(),c)>=0}),c.length||(a.selectedIndex=-1);return c}}},attrFn:{val:!0,css:!0,html:!0,text:!0,data:!0,width:!0,height:!0,offset:!0},attr:function(a,c,d,e){var g,h,i,j=a.nodeType;if(!!a&&j!==3&&j!==8&&j!==2){if(e&&c in f.attrFn)return f(a)[c](d);if(typeof a.getAttribute=="undefined")return f.prop(a,c,d);i=j!==1||!f.isXMLDoc(a),i&&(c=c.toLowerCase(),h=f.attrHooks[c]||(u.test(c)?x:w));if(d!==b){if(d===null){f.removeAttr(a,c);return}if(h&&"set"in h&&i&&(g=h.set(a,d,c))!==b)return g;a.setAttribute(c,""+d);return d}if(h&&"get"in h&&i&&(g=h.get(a,c))!==null)return g;g=a.getAttribute(c);return g===null?b:g}},removeAttr:function(a,b){var c,d,e,g,h,i=0;if(b&&a.nodeType===1){d=b.toLowerCase().split(p),g=d.length;for(;i<g;i++)e=d[i],e&&(c=f.propFix[e]||e,h=u.test(e),h||f.attr(a,e,""),a.removeAttribute(v?e:c),h&&c in a&&(a[c]=!1))}},attrHooks:{type:{set:function(a,b){if(r.test(a.nodeName)&&a.parentNode)f.error("type property can't be changed");else if(!f.support.radioValue&&b==="radio"&&f.nodeName(a,"input")){var c=a.value;a.setAttribute("type",b),c&&(a.value=c);return b}}},value:{get:function(a,b){if(w&&f.nodeName(a,"button"))return w.get(a,b);return b in a?a.value:null},set:function(a,b,c){if(w&&f.nodeName(a,"button"))return w.set(a,b,c);a.value=b}}},propFix:{tabindex:"tabIndex",readonly:"readOnly","for":"htmlFor","class":"className",maxlength:"maxLength",cellspacing:"cellSpacing",cellpadding:"cellPadding",rowspan:"rowSpan",colspan:"colSpan",usemap:"useMap",frameborder:"frameBorder",contenteditable:"contentEditable"},prop:function(a,c,d){var e,g,h,i=a.nodeType;if(!!a&&i!==3&&i!==8&&i!==2){h=i!==1||!f.isXMLDoc(a),h&&(c=f.propFix[c]||c,g=f.propHooks[c]);return d!==b?g&&"set"in g&&(e=g.set(a,d,c))!==b?e:a[c]=d:g&&"get"in g&&(e=g.get(a,c))!==null?e:a[c]}},propHooks:{tabIndex:{get:function(a){var c=a.getAttributeNode("tabindex");return c&&c.specified?parseInt(c.value,10):s.test(a.nodeName)||t.test(a.nodeName)&&a.href?0:b}}}}),f.attrHooks.tabindex=f.propHooks.tabIndex,x={get:function(a,c){var d,e=f.prop(a,c);return e===!0||typeof e!="boolean"&&(d=a.getAttributeNode(c))&&d.nodeValue!==!1?c.toLowerCase():b},set:function(a,b,c){var d;b===!1?f.removeAttr(a,c):(d=f.propFix[c]||c,d in a&&(a[d]=!0),a.setAttribute(c,c.toLowerCase()));return c}},v||(y={name:!0,id:!0,coords:!0},w=f.valHooks.button={get:function(a,c){var d;d=a.getAttributeNode(c);return d&&(y[c]?d.nodeValue!=="":d.specified)?d.nodeValue:b},set:function(a,b,d){var e=a.getAttributeNode(d);e||(e=c.createAttribute(d),a.setAttributeNode(e));return e.nodeValue=b+""}},f.attrHooks.tabindex.set=w.set,f.each(["width","height"],function(a,b){f.attrHooks[b]=f.extend(f.attrHooks[b],{set:function(a,c){if(c===""){a.setAttribute(b,"auto");return c}}})}),f.attrHooks.contenteditable={get:w.get,set:function(a,b,c){b===""&&(b="false"),w.set(a,b,c)}}),f.support.hrefNormalized||f.each(["href","src","width","height"],function(a,c){f.attrHooks[c]=f.extend(f.attrHooks[c],{get:function(a){var d=a.getAttribute(c,2);return d===null?b:d}})}),f.support.style||(f.attrHooks.style={get:function(a){return a.style.cssText.toLowerCase()||b},set:function(a,b){return a.style.cssText=""+b}}),f.support.optSelected||(f.propHooks.selected=f.extend(f.propHooks.selected,{get:function(a){var b=a.parentNode;b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex);return null}})),f.support.enctype||(f.propFix.enctype="encoding"),f.support.checkOn||f.each(["radio","checkbox"],function(){f.valHooks[this]={get:function(a){return a.getAttribute("value")===null?"on":a.value}}}),f.each(["radio","checkbox"],function(){f.valHooks[this]=f.extend(f.valHooks[this],{set:function(a,b){if(f.isArray(b))return a.checked=f.inArray(f(a).val(),b)>=0}})});var z=/^(?:textarea|input|select)$/i,A=/^([^\.]*)?(?:\.(.+))?$/,B=/(?:^|\s)hover(\.\S+)?\b/,C=/^key/,D=/^(?:mouse|contextmenu)|click/,E=/^(?:focusinfocus|focusoutblur)$/,F=/^(\w*)(?:#([\w\-]+))?(?:\.([\w\-]+))?$/,G=function(
	a){var b=F.exec(a);b&&(b[1]=(b[1]||"").toLowerCase(),b[3]=b[3]&&new RegExp("(?:^|\\s)"+b[3]+"(?:\\s|$)"));return b},H=function(a,b){var c=a.attributes||{};return(!b[1]||a.nodeName.toLowerCase()===b[1])&&(!b[2]||(c.id||{}).value===b[2])&&(!b[3]||b[3].test((c["class"]||{}).value))},I=function(a){return f.event.special.hover?a:a.replace(B,"mouseenter$1 mouseleave$1")};f.event={add:function(a,c,d,e,g){var h,i,j,k,l,m,n,o,p,q,r,s;if(!(a.nodeType===3||a.nodeType===8||!c||!d||!(h=f._data(a)))){d.handler&&(p=d,d=p.handler,g=p.selector),d.guid||(d.guid=f.guid++),j=h.events,j||(h.events=j={}),i=h.handle,i||(h.handle=i=function(a){return typeof f!="undefined"&&(!a||f.event.triggered!==a.type)?f.event.dispatch.apply(i.elem,arguments):b},i.elem=a),c=f.trim(I(c)).split(" ");for(k=0;k<c.length;k++){l=A.exec(c[k])||[],m=l[1],n=(l[2]||"").split(".").sort(),s=f.event.special[m]||{},m=(g?s.delegateType:s.bindType)||m,s=f.event.special[m]||{},o=f.extend({type:m,origType:l[1],data:e,handler:d,guid:d.guid,selector:g,quick:g&&G(g),namespace:n.join(".")},p),r=j[m];if(!r){r=j[m]=[],r.delegateCount=0;if(!s.setup||s.setup.call(a,e,n,i)===!1)a.addEventListener?a.addEventListener(m,i,!1):a.attachEvent&&a.attachEvent("on"+m,i)}s.add&&(s.add.call(a,o),o.handler.guid||(o.handler.guid=d.guid)),g?r.splice(r.delegateCount++,0,o):r.push(o),f.event.global[m]=!0}a=null}},global:{},remove:function(a,b,c,d,e){var g=f.hasData(a)&&f._data(a),h,i,j,k,l,m,n,o,p,q,r,s;if(!!g&&!!(o=g.events)){b=f.trim(I(b||"")).split(" ");for(h=0;h<b.length;h++){i=A.exec(b[h])||[],j=k=i[1],l=i[2];if(!j){for(j in o)f.event.remove(a,j+b[h],c,d,!0);continue}p=f.event.special[j]||{},j=(d?p.delegateType:p.bindType)||j,r=o[j]||[],m=r.length,l=l?new RegExp("(^|\\.)"+l.split(".").sort().join("\\.(?:.*\\.)?")+"(\\.|$)"):null;for(n=0;n<r.length;n++)s=r[n],(e||k===s.origType)&&(!c||c.guid===s.guid)&&(!l||l.test(s.namespace))&&(!d||d===s.selector||d==="**"&&s.selector)&&(r.splice(n--,1),s.selector&&r.delegateCount--,p.remove&&p.remove.call(a,s));r.length===0&&m!==r.length&&((!p.teardown||p.teardown.call(a,l)===!1)&&f.removeEvent(a,j,g.handle),delete o[j])}f.isEmptyObject(o)&&(q=g.handle,q&&(q.elem=null),f.removeData(a,["events","handle"],!0))}},customEvent:{getData:!0,setData:!0,changeData:!0},trigger:function(c,d,e,g){if(!e||e.nodeType!==3&&e.nodeType!==8){var h=c.type||c,i=[],j,k,l,m,n,o,p,q,r,s;if(E.test(h+f.event.triggered))return;h.indexOf("!")>=0&&(h=h.slice(0,-1),k=!0),h.indexOf(".")>=0&&(i=h.split("."),h=i.shift(),i.sort());if((!e||f.event.customEvent[h])&&!f.event.global[h])return;c=typeof c=="object"?c[f.expando]?c:new f.Event(h,c):new f.Event(h),c.type=h,c.isTrigger=!0,c.exclusive=k,c.namespace=i.join("."),c.namespace_re=c.namespace?new RegExp("(^|\\.)"+i.join("\\.(?:.*\\.)?")+"(\\.|$)"):null,o=h.indexOf(":")<0?"on"+h:"";if(!e){j=f.cache;for(l in j)j[l].events&&j[l].events[h]&&f.event.trigger(c,d,j[l].handle.elem,!0);return}c.result=b,c.target||(c.target=e),d=d!=null?f.makeArray(d):[],d.unshift(c),p=f.event.special[h]||{};if(p.trigger&&p.trigger.apply(e,d)===!1)return;r=[[e,p.bindType||h]];if(!g&&!p.noBubble&&!f.isWindow(e)){s=p.delegateType||h,m=E.test(s+h)?e:e.parentNode,n=null;for(;m;m=m.parentNode)r.push([m,s]),n=m;n&&n===e.ownerDocument&&r.push([n.defaultView||n.parentWindow||a,s])}for(l=0;l<r.length&&!c.isPropagationStopped();l++)m=r[l][0],c.type=r[l][1],q=(f._data(m,"events")||{})[c.type]&&f._data(m,"handle"),q&&q.apply(m,d),q=o&&m[o],q&&f.acceptData(m)&&q.apply(m,d)===!1&&c.preventDefault();c.type=h,!g&&!c.isDefaultPrevented()&&(!p._default||p._default.apply(e.ownerDocument,d)===!1)&&(h!=="click"||!f.nodeName(e,"a"))&&f.acceptData(e)&&o&&e[h]&&(h!=="focus"&&h!=="blur"||c.target.offsetWidth!==0)&&!f.isWindow(e)&&(n=e[o],n&&(e[o]=null),f.event.triggered=h,e[h](),f.event.triggered=b,n&&(e[o]=n));return c.result}},dispatch:function(c){c=f.event.fix(c||a.event);var d=(f._data(this,"events")||{})[c.type]||[],e=d.delegateCount,g=[].slice.call(arguments,0),h=!c.exclusive&&!c.namespace,i=f.event.special[c.type]||{},j=[],k,l,m,n,o,p,q,r,s,t,u;g[0]=c,c.delegateTarget=this;if(!i.preDispatch||i.preDispatch.call(this,c)!==!1){if(e&&(!c.button||c.type!=="click")){n=f(this),n.context=this.ownerDocument||this;for(m=c.target;m!=this;m=m.parentNode||this)if(m.disabled!==!0){p={},r=[],n[0]=m;for(k=0;k<e;k++)s=d[k],t=s.selector,p[t]===b&&(p[t]=s.quick?H(m,s.quick):n.is(t)),p[t]&&r.push(s);r.length&&j.push({elem:m,matches:r})}}d.length>e&&j.push({elem:this,matches:d.slice(e)});for(k=0;k<j.length&&!c.isPropagationStopped();k++){q=j[k],c.currentTarget=q.elem;for(l=0;l<q.matches.length&&!c.isImmediatePropagationStopped();l++){s=q.matches[l];if(h||!c.namespace&&!s.namespace||c.namespace_re&&c.namespace_re.test(s.namespace))c.data=s.data,c.handleObj=s,o=((f.event.special[s.origType]||{}).handle||s.handler).apply(q.elem,g),o!==b&&(c.result=o,o===!1&&(c.preventDefault(),c.stopPropagation()))}}i.postDispatch&&i.postDispatch.call(this,c);return c.result}},props:"attrChange attrName relatedNode srcElement altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(a,b){a.which==null&&(a.which=b.charCode!=null?b.charCode:b.keyCode);return a}},mouseHooks:{props:"button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(a,d){var e,f,g,h=d.button,i=d.fromElement;a.pageX==null&&d.clientX!=null&&(e=a.target.ownerDocument||c,f=e.documentElement,g=e.body,a.pageX=d.clientX+(f&&f.scrollLeft||g&&g.scrollLeft||0)-(f&&f.clientLeft||g&&g.clientLeft||0),a.pageY=d.clientY+(f&&f.scrollTop||g&&g.scrollTop||0)-(f&&f.clientTop||g&&g.clientTop||0)),!a.relatedTarget&&i&&(a.relatedTarget=i===a.target?d.toElement:i),!a.which&&h!==b&&(a.which=h&1?1:h&2?3:h&4?2:0);return a}},fix:function(a){if(a[f.expando])return a;var d,e,g=a,h=f.event.fixHooks[a.type]||{},i=h.props?this.props.concat(h.props):this.props;a=f.Event(g);for(d=i.length;d;)e=i[--d],a[e]=g[e];a.target||(a.target=g.srcElement||c),a.target.nodeType===3&&(a.target=a.target.parentNode),a.metaKey===b&&(a.metaKey=a.ctrlKey);return h.filter?h.filter(a,g):a},special:{ready:{setup:f.bindReady},load:{noBubble:!0},focus:{delegateType:"focusin"},blur:{delegateType:"focusout"},beforeunload:{setup:function(a,b,c){f.isWindow(this)&&(this.onbeforeunload=c)},teardown:function(a,b){this.onbeforeunload===b&&(this.onbeforeunload=null)}}},simulate:function(a,b,c,d){var e=f.extend(new f.Event,c,{type:a,isSimulated:!0,originalEvent:{}});d?f.event.trigger(e,null,b):f.event.dispatch.call(b,e),e.isDefaultPrevented()&&c.preventDefault()}},f.event.handle=f.event.dispatch,f.removeEvent=c.removeEventListener?function(a,b,c){a.removeEventListener&&a.removeEventListener(b,c,!1)}:function(a,b,c){a.detachEvent&&a.detachEvent("on"+b,c)},f.Event=function(a,b){if(!(this instanceof f.Event))return new f.Event(a,b);a&&a.type?(this.originalEvent=a,this.type=a.type,this.isDefaultPrevented=a.defaultPrevented||a.returnValue===!1||a.getPreventDefault&&a.getPreventDefault()?K:J):this.type=a,b&&f.extend(this,b),this.timeStamp=a&&a.timeStamp||f.now(),this[f.expando]=!0},f.Event.prototype={preventDefault:function(){this.isDefaultPrevented=K;var a=this.originalEvent;!a||(a.preventDefault?a.preventDefault():a.returnValue=!1)},stopPropagation:function(){this.isPropagationStopped=K;var a=this.originalEvent;!a||(a.stopPropagation&&a.stopPropagation(),a.cancelBubble=!0)},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=K,this.stopPropagation()},isDefaultPrevented:J,isPropagationStopped:J,isImmediatePropagationStopped:J},f.each({mouseenter:"mouseover",mouseleave:"mouseout"},function(a,b){f.event.special[a]={delegateType:b,bindType:b,handle:function(a){var c=this,d=a.relatedTarget,e=a.handleObj,g=e.selector,h;if(!d||d!==c&&!f.contains(c,d))a.type=e.origType,h=e.handler.apply(this,arguments),a.type=b;return h}}}),f.support.submitBubbles||(f.event.special.submit={setup:function(){if(f.nodeName(this,"form"))return!1;f.event.add(this,"click._submit keypress._submit",function(a){var c=a.target,d=f.nodeName(c,"input")||f.nodeName(c,"button")?c.form:b;d&&!d._submit_attached&&(f.event.add(d,"submit._submit",function(a){a._submit_bubble=!0}),d._submit_attached=!0)})},postDispatch:function(a){a._submit_bubble&&(delete a._submit_bubble,this.parentNode&&!a.isTrigger&&f.event.simulate("submit",this.parentNode,a,!0))},teardown:function(){if(f.nodeName(this,"form"))return!1;f.event.remove(this,"._submit")}}),f.support.changeBubbles||(f.event.special.change={setup:function(){if(z.test(this.nodeName)){if(this.type==="checkbox"||this.type==="radio")f.event.add(this,"propertychange._change",function(a){a.originalEvent.propertyName==="checked"&&(this._just_changed=!0)}),f.event.add(this,"click._change",function(a){this._just_changed&&!a.isTrigger&&(this._just_changed=!1,f.event.simulate("change",this,a,!0))});return!1}f.event.add(this,"beforeactivate._change",function(a){var b=a.target;z.test(b.nodeName)&&!b._change_attached&&(f.event.add(b,"change._change",function(a){this.parentNode&&!a.isSimulated&&!a.isTrigger&&f.event.simulate("change",this.parentNode,a,!0)}),b._change_attached=!0)})},handle:function(a){var b=a.target;if(this!==b||a.isSimulated||a.isTrigger||b.type!=="radio"&&b.type!=="checkbox")return a.handleObj.handler.apply(this,arguments)},teardown:function(){f.event.remove(this,"._change");return z.test(this.nodeName)}}),f.support.focusinBubbles||f.each({focus:"focusin",blur:"focusout"},function(a,b){var d=0,e=function(a){f.event.simulate(b,a.target,f.event.fix(a),!0)};f.event.special[b]={setup:function(){d++===0&&c.addEventListener(a,e,!0)},teardown:function(){--d===0&&c.removeEventListener(a,e,!0)}}}),f.fn.extend({on:function(a,c,d,e,g){var h,i;if(typeof a=="object"){typeof c!="string"&&(d=d||c,c=b);for(i in a)this.on(i,c,d,a[i],g);return this}d==null&&e==null?(e=c,d=c=b):e==null&&(typeof c=="string"?(e=d,d=b):(e=d,d=c,c=b));if(e===!1)e=J;else if(!e)return this;g===1&&(h=e,e=function(a){f().off(a);return h.apply(this,arguments)},e.guid=h.guid||(h.guid=f.guid++));return this.each(function(){f.event.add(this,a,e,d,c)})},one:function(a,b,c,d){return this.on(a,b,c,d,1)},off:function(a,c,d){if(a&&a.preventDefault&&a.handleObj){var e=a.handleObj;f(a.delegateTarget).off(e.namespace?e.origType+"."+e.namespace:e.origType,e.selector,e.handler);return this}if(typeof a=="object"){for(var g in a)this.off(g,c,a[g]);return this}if(c===!1||typeof c=="function")d=c,c=b;d===!1&&(d=J);return this.each(function(){f.event.remove(this,a,d,c)})},bind:function(a,b,c){return this.on(a,null,b,c)},unbind:function(a,b){return this.off(a,null,b)},live:function(a,b,c){f(this.context).on(a,this.selector,b,c);return this},die:function(a,b){f(this.context).off(a,this.selector||"**",b);return this},delegate:function(a,b,c,d){return this.on(b,a,c,d)},undelegate:function(a,b,c){return arguments.length==1?this.off(a,"**"):this.off(b,a,c)},trigger:function(a,b){return this.each(function(){f.event.trigger(a,b,this)})},triggerHandler:function(a,b){if(this[0])return f.event.trigger(a,b,this[0],!0)},toggle:function(a){var b=arguments,c=a.guid||f.guid++,d=0,e=function(c){var e=(f._data(this,"lastToggle"+a.guid)||0)%d;f._data(this,"lastToggle"+a.guid,e+1),c.preventDefault();return b[e].apply(this,arguments)||!1};e.guid=c;while(d<b.length)b[d++].guid=c;return this.click(e)},hover:function(a,b){return this.mouseenter(a).mouseleave(b||a)}}),f.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(a,b){f.fn[b]=function(a,c){c==null&&(c=a,a=null);return arguments.length>0?this.on(b,null,a,c):this.trigger(b)},f.attrFn&&(f.attrFn[b]=!0),C.test(b)&&(f.event.fixHooks[b]=f.event.keyHooks),D.test(b)&&(f.event.fixHooks[b]=f.event.mouseHooks)}),function(){function x(a,b,c,e,f,g){for(var h=0,i=e.length;h<i;h++){var j=e[h];if(j){var k=!1;j=j[a];while(j){if(j[d]===c){k=e[j.sizset];break}if(j.nodeType===1){g||(j[d]=c,j.sizset=h);if(typeof b!="string"){if(j===b){k=!0;break}}else if(m.filter(b,[j]).length>0){k=j;break}}j=j[a]}e[h]=k}}}function w(a,b,c,e,f,g){for(var h=0,i=e.length;h<i;h++){var j=e[h];if(j){var k=!1;j=j[a];while(j){if(j[d]===c){k=e[j.sizset];break}j.nodeType===1&&!g&&(j[d]=c,j.sizset=h);if(j.nodeName.toLowerCase()===b){k=j;break}j=j[a]}e[h]=k}}}var a=/((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^\[\]]*\]|['"][^'"]*['"]|[^\[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,d="sizcache"+(Math.random()+"").replace(".",""),e=0,g=Object.prototype.toString,h=!1,i=!0,j=/\\/g,k=/\r\n/g,l=/\W/;[0,0].sort(function(){i=!1;return 0});var m=function(b,d,e,f){e=e||[],d=d||c;var h=d;if(d.nodeType!==1&&d.nodeType!==9)return[];if(!b||typeof b!="string")return e;var i,j,k,l,n,q,r,t,u=!0,v=m.isXML(d),w=[],x=b;do{a.exec(""),i=a.exec(x);if(i){x=i[3],w.push(i[1]);if(i[2]){l=i[3];break}}}while(i);if(w.length>1&&p.exec(b))if(w.length===2&&o.relative[w[0]])j=y(w[0]+w[1],d,f);else{j=o.relative[w[0]]?[d]:m(w.shift(),d);while(w.length)b=w.shift(),o.relative[b]&&(b+=w.shift()),j=y(b,j,f)}else{!f&&w.length>1&&d.nodeType===9&&!v&&o.match.ID.test(w[0])&&!o.match.ID.test(w[w.length-1])&&(n=m.find(w.shift(),d,v),d=n.expr?m.filter(n.expr,n.set)[0]:n.set[0]);if(d){n=f?{expr:w.pop(),set:s(f)}:m.find(w.pop(),w.length===1&&(w[0]==="~"||w[0]==="+")&&d.parentNode?d.parentNode:d,v),j=n.expr?m.filter(n.expr,n.set):n.set,w.length>0?k=s(j):u=!1;while(w.length)q=w.pop(),r=q,o.relative[q]?r=w.pop():q="",r==null&&(r=d),o.relative[q](k,r,v)}else k=w=[]}k||(k=j),k||m.error(q||b);if(g.call(k)==="[object Array]")if(!u)e.push.apply(e,k);else if(d&&d.nodeType===1)for(t=0;k[t]!=null;t++)k[t]&&(k[t]===!0||k[t].nodeType===1&&m.contains(d,k[t]))&&e.push(j[t]);else for(t=0;k[t]!=null;t++)k[t]&&k[t].nodeType===1&&e.push(j[t]);else s(k,e);l&&(m(l,h,e,f),m.uniqueSort(e));return e};m.uniqueSort=function(a){if(u){h=i,a.sort(u);if(h)for(var b=1;b<a.length;b++)a[b]===a[b-1]&&a.splice(b--,1)}return a},m.matches=function(a,b){return m(a,null,null,b)},m.matchesSelector=function(a,b){return m(b,null,null,[a]).length>0},m.find=function(a,b,c){var d,e,f,g,h,i;if(!a)return[];for(e=0,f=o.order.length;e<f;e++){h=o.order[e];if(g=o.leftMatch[h].exec(a)){i=g[1],g.splice(1,1);if(i.substr(i.length-1)!=="\\"){g[1]=(g[1]||"").replace(j,""),d=o.find[h](g,b,c);if(d!=null){a=a.replace(o.match[h],"");break}}}}d||(d=typeof b.getElementsByTagName!="undefined"?b.getElementsByTagName("*"):[]);return{set:d,expr:a}},m.filter=function(a,c,d,e){var f,g,h,i,j,k,l,n,p,q=a,r=[],s=c,t=c&&c[0]&&m.isXML(c[0]);while(a&&c.length){for(h in o.filter)if((f=o.leftMatch[h].exec(a))!=null&&f[2]){k=o.filter[h],l=f[1],g=!1,f.splice(1,1);if(l.substr(l.length-1)==="\\")continue;s===r&&(r=[]);if(o.preFilter[h]){f=o.preFilter[h](f,s,d,r,e,t);if(!f)g=i=!0;else if(f===!0)continue}if(f)for(n=0;(j=s[n])!=null;n++)j&&(i=k(j,f,n,s),p=e^i,d&&i!=null?p?g=!0:s[n]=!1:p&&(r.push(j),g=!0));if(i!==b){d||(s=r),a=a.replace(o.match[h],"");if(!g)return[];break}}if(a===q)if(g==null)m.error(a);else break;q=a}return s},m.error=function(a){throw new Error("Syntax error, unrecognized expression: "+a)};var n=m.getText=function(a){var b,c,d=a.nodeType,e="";if(d){if(d===1||d===9||d===11){if(typeof a.textContent=="string")return a.textContent;if(typeof a.innerText=="string")return a.innerText.replace(k,"");for(a=a.firstChild;a;a=a.nextSibling)e+=n(a)}else if(d===3||d===4)return a.nodeValue}else for(b=0;c=a[b];b++)c.nodeType!==8&&(e+=n(c));return e},o=m.selectors={order:["ID","NAME","TAG"],match:{ID:/#((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,CLASS:/\.((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,NAME:/\[name=['"]*((?:[\w\u00c0-\uFFFF\-]|\\.)+)['"]*\]/,ATTR:/\[\s*((?:[\w\u00c0-\uFFFF\-]|\\.)+)\s*(?:(\S?=)\s*(?:(['"])(.*?)\3|(#?(?:[\w\u00c0-\uFFFF\-]|\\.)*)|)|)\s*\]/,TAG:/^((?:[\w\u00c0-\uFFFF\*\-]|\\.)+)/,CHILD:/:(only|nth|last|first)-child(?:\(\s*(even|odd|(?:[+\-]?\d+|(?:[+\-]?\d*)?n\s*(?:[+\-]\s*\d+)?))\s*\))?/,POS:/:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^\-]|$)/,PSEUDO:/:((?:[\w\u00c0-\uFFFF\-]|\\.)+)(?:\((['"]?)((?:\([^\)]+\)|[^\(\)]*)+)\2\))?/},leftMatch:{},attrMap:{"class":"className","for":"htmlFor"},attrHandle:{href:function(a){return a.getAttribute("href")},type:function(a){return a.getAttribute("type")}},relative:{"+":function(a,b){var c=typeof b=="string",d=c&&!l.test(b),e=c&&!d;d&&(b=b.toLowerCase());for(var f=0,g=a.length,h;f<g;f++)if(h=a[f]){while((h=h.previousSibling)&&h.nodeType!==1);a[f]=e||h&&h.nodeName.toLowerCase()===b?h||!1:h===b}e&&m.filter(b,a,!0)},">":function(a,b){var c,d=typeof b=="string",e=0,f=a.length;if(d&&!l.test(b)){b=b.toLowerCase();for(;e<f;e++){c=a[e];if(c){var g=c.parentNode;a[e]=g.nodeName.toLowerCase()===b?g:!1}}}else{for(;e<f;e++)c=a[e],c&&(a[e]=d?c.parentNode:c.parentNode===b);d&&m.filter(b,a,!0)}},"":function(a,b,c){var d,f=e++,g=x;typeof b=="string"&&!l.test(b)&&(b=b.toLowerCase(),d=b,g=w),g("parentNode",b,f,a,d,c)},"~":function(a,b,c){var d,f=e++,g=x;typeof b=="string"&&!l.test(b)&&(b=b.toLowerCase(),d=b,g=w),g("previousSibling",b,f,a,d,c)}},find:{ID:function(a,b,c){if(typeof b.getElementById!="undefined"&&!c){var d=b.getElementById(a[1]);return d&&d.parentNode?[d]:[]}},NAME:function(a,b){if(typeof b.getElementsByName!="undefined"){var c=[],d=b.getElementsByName(a[1]);for(var e=0,f=d.length;e<f;e++)d[e].getAttribute("name")===a[1]&&c.push(d[e]);return c.length===0?null:c}},TAG:function(a,b){if(typeof b.getElementsByTagName!="undefined")return b.getElementsByTagName(a[1])}},preFilter:{CLASS:function(a,b,c,d,e,f){a=" "+a[1].replace(j,"")+" ";if(f)return a;for(var g=0,h;(h=b[g])!=null;g++)h&&(e^(h.className&&(" "+h.className+" ").replace(/[\t\n\r]/g," ").indexOf(a)>=0)?c||d.push(h):c&&(b[g]=!1));return!1},ID:function(a){return a[1].replace(j,"")},TAG:function(a,b){return a[1].replace(j,"").toLowerCase()},CHILD:function(a){if(a[1]==="nth"){a[2]||m.error(a[0]),a[2]=a[2].replace(/^\+|\s*/g,"");var b=/(-?)(\d*)(?:n([+\-]?\d*))?/.exec(a[2]==="even"&&"2n"||a[2]==="odd"&&"2n+1"||!/\D/.test(a[2])&&"0n+"+a[2]||a[2]);a[2]=b[1]+(b[2]||1)-0,a[3]=b[3]-0}else a[2]&&m.error(a[0]);a[0]=e++;return a},ATTR:function(a,b,c,d,e,f){var g=a[1]=a[1].replace(j,"");!f&&o.attrMap[g]&&(a[1]=o.attrMap[g]),a[4]=(a[4]||a[5]||"").replace(j,""),a[2]==="~="&&(a[4]=" "+a[4]+" ");return a},PSEUDO:function(b,c,d,e,f){if(b[1]==="not")if((a.exec(b[3])||"").length>1||/^\w/.test(b[3]))b[3]=m(b[3],null,null,c);else{var g=m.filter(b[3],c,d,!0^f);d||e.push.apply(e,g);return!1}else if(o.match.POS.test(b[0])||o.match.CHILD.test(b[0]))return!0;return b},POS:function(a){a.unshift(!0);return a}},filters:{enabled:function(a){return a.disabled===!1&&a.type!=="hidden"},disabled:function(a){return a.disabled===!0},checked:function(a){return a.checked===!0},selected:function(a){a.parentNode&&a.parentNode.selectedIndex;return a.selected===!0},parent:function(a){return!!a.firstChild},empty:function(a){return!a.firstChild},has:function(a,b,c){return!!m(c[3],a).length},header:function(a){return/h\d/i.test(a.nodeName)},text:function(a){var b=a.getAttribute("type"),c=a.type;return a.nodeName.toLowerCase()==="input"&&"text"===c&&(b===c||b===null)},radio:function(a){return a.nodeName.toLowerCase()==="input"&&"radio"===a.type},checkbox:function(a){return a.nodeName.toLowerCase()==="input"&&"checkbox"===a.type},file:function(a){return a.nodeName.toLowerCase()==="input"&&"file"===a.type},password:function(a){return a.nodeName.toLowerCase()==="input"&&"password"===a.type},submit:function(a){var b=a.nodeName.toLowerCase();return(b==="input"||b==="button")&&"submit"===a.type},image:function(a){return a.nodeName.toLowerCase()==="input"&&"image"===a.type},reset:function(a){var b=a.nodeName.toLowerCase();return(b==="input"||b==="button")&&"reset"===a.type},button:function(a){var b=a.nodeName.toLowerCase();return b==="input"&&"button"===a.type||b==="button"},input:function(a){return/input|select|textarea|button/i.test(a.nodeName)},focus:function(a){return a===a.ownerDocument.activeElement}},setFilters:{first:function(a,b){return b===0},last:function(a,b,c,d){return b===d.length-1},even:function(a,b){return b%2===0},odd:function(a,b){return b%2===1},lt:function(a,b,c){return b<c[3]-0},gt:function(a,b,c){return b>c[3]-0},nth:function(a,b,c){return c[3]-0===b},eq:function(a,b,c){return c[3]-0===b}},filter:{PSEUDO:function(a,b,c,d){var e=b[1],f=o.filters[e];if(f)return f(a,c,b,d);if(e==="contains")return(a.textContent||a.innerText||n([a])||"").indexOf(b[3])>=0;if(e==="not"){var g=b[3];for(var h=0,i=g.length;h<i;h++)if(g[h]===a)return!1;return!0}m.error(e)},CHILD:function(a,b){var c,e,f,g,h,i,j,k=b[1],l=a;switch(k){case"only":case"first":while(l=l.previousSibling)if(l.nodeType===1)return!1;if(k==="first")return!0;l=a;case"last":while(l=l.nextSibling)if(l.nodeType===1)return!1;return!0;case"nth":c=b[2],e=b[3];if(c===1&&e===0)return!0;f=b[0],g=a.parentNode;if(g&&(g[d]!==f||!a.nodeIndex)){i=0;for(l=g.firstChild;l;l=l.nextSibling)l.nodeType===1&&(l.nodeIndex=++i);g[d]=f}j=a.nodeIndex-e;return c===0?j===0:j%c===0&&j/c>=0}},ID:function(a,b){return a.nodeType===1&&a.getAttribute("id")===b},TAG:function(a,b){return b==="*"&&a.nodeType===1||!!a.nodeName&&a.nodeName.toLowerCase()===b},CLASS:function(a,b){return(" "+(a.className||a.getAttribute("class"))+" ").indexOf(b)>-1},ATTR:function(a,b){var c=b[1],d=m.attr?m.attr(a,c):o.attrHandle[c]?o.attrHandle[c](a):a[c]!=null?a[c]:a.getAttribute(c),e=d+"",f=b[2],g=b[4];return d==null?f==="!=":!f&&m.attr?d!=null:f==="="?e===g:f==="*="?e.indexOf(g)>=0:f==="~="?(" "+e+" ").indexOf(g)>=0:g?f==="!="?e!==g:f==="^="?e.indexOf(g)===0:f==="$="?e.substr(e.length-g.length)===g:f==="|="?e===g||e.substr(0,g.length+1)===g+"-":!1:e&&d!==!1},POS:function(a,b,c,d){var e=b[2],f=o.setFilters[e];if(f)return f(a,c,b,d)}}},p=o.match.POS,q=function(a,b){return"\\"+(b-0+1)};for(var r in o.match)o.match[r]=new RegExp(o.match[r].source+/(?![^\[]*\])(?![^\(]*\))/.source),o.leftMatch[r]=new RegExp(/(^(?:.|\r|\n)*?)/.source+o.match[r].source.replace(/\\(\d+)/g,q));o.match.globalPOS=p;var s=function(a,b){a=Array.prototype.slice.call(a,0);if(b){b.push.apply(b,a);return b}return a};try{Array.prototype.slice.call(c.documentElement.childNodes,0)[0].nodeType}catch(t){s=function(a,b){var c=0,d=b||[];if(g.call(a)==="[object Array]")Array.prototype.push.apply(d,a);else if(typeof a.length=="number")for(var e=a.length;c<e;c++)d.push(a[c]);else for(;a[c];c++)d.push(a[c]);return d}}var u,v;c.documentElement.compareDocumentPosition?u=function(a,b){if(a===b){h=!0;return 0}if(!a.compareDocumentPosition||!b.compareDocumentPosition)return a.compareDocumentPosition?-1:1;return a.compareDocumentPosition(b)&4?-1:1}:(u=function(a,b){if(a===b){h=!0;return 0}if(a.sourceIndex&&b.sourceIndex)return a.sourceIndex-b.sourceIndex;var c,d,e=[],f=[],g=a.parentNode,i=b.parentNode,j=g;if(g===i)return v(a,b);if(!g)return-1;if(!i)return 1;while(j)e.unshift(j),j=j.parentNode;j=i;while(j)f.unshift(j),j=j.parentNode;c=e.length,d=f.length;for(var k=0;k<c&&k<d;k++)if(e[k]!==f[k])return v(e[k],f[k]);return k===c?v(a,f[k],-1):v(e[k],b,1)},v=function(a,b,c){if(a===b)return c;var d=a.nextSibling;while(d){if(d===b)return-1;d=d.nextSibling}return 1}),function(){var a=c.createElement("div"),d="script"+(new Date).getTime(),e=c.documentElement;a.innerHTML="<a name='"+d+"'/>",e.insertBefore(a,e.firstChild),c.getElementById(d)&&(o.find.ID=function(a,c,d){if(typeof c.getElementById!="undefined"&&!d){var e=c.getElementById(a[1]);return e?e.id===a[1]||typeof e.getAttributeNode!="undefined"&&e.getAttributeNode("id").nodeValue===a[1]?[e]:b:[]}},o.filter.ID=function(a,b){var c=typeof a.getAttributeNode!="undefined"&&a.getAttributeNode("id");return a.nodeType===1&&c&&c.nodeValue===b}),e.removeChild(a),e=a=null}(),function(){var a=c.createElement("div");a.appendChild(c.createComment("")),a.getElementsByTagName("*").length>0&&(o.find.TAG=function(a,b){var c=b.getElementsByTagName(a[1]);if(a[1]==="*"){var d=[];for(var e=0;c[e];e++)c[e].nodeType===1&&d.push(c[e]);c=d}return c}),a.innerHTML="<a href='#'></a>",a.firstChild&&typeof a.firstChild.getAttribute!="undefined"&&a.firstChild.getAttribute("href")!=="#"&&(o.attrHandle.href=function(a){return a.getAttribute("href",2)}),a=null}(),c.querySelectorAll&&function(){var a=m,b=c.createElement("div"),d="__sizzle__";b.innerHTML="<p class='TEST'></p>";if(!b.querySelectorAll||b.querySelectorAll(".TEST").length!==0){m=function(b,e,f,g){e=e||c;if(!g&&!m.isXML(e)){var h=/^(\w+$)|^\.([\w\-]+$)|^#([\w\-]+$)/.exec(b);if(h&&(e.nodeType===1||e.nodeType===9)){if(h[1])return s(e.getElementsByTagName(b),f);if(h[2]&&o.find.CLASS&&e.getElementsByClassName)return s(e.getElementsByClassName(h[2]),f)}if(e.nodeType===9){if(b==="body"&&e.body)return s([e.body],f);if(h&&h[3]){var i=e.getElementById(h[3]);if(!i||!i.parentNode)return s([],f);if(i.id===h[3])return s([i],f)}try{return s(e.querySelectorAll(b),f)}catch(j){}}else if(e.nodeType===1&&e.nodeName.toLowerCase()!=="object"){var k=e,l=e.getAttribute("id"),n=l||d,p=e.parentNode,q=/^\s*[+~]/.test(b);l?n=n.replace(/'/g,"\\$&"):e.setAttribute("id",n),q&&p&&(e=e.parentNode);try{if(!q||p)return s(e.querySelectorAll("[id='"+n+"'] "+b),f)}catch(r){}finally{l||k.removeAttribute("id")}}}return a(b,e,f,g)};for(var e in a)m[e]=a[e];b=null}}(),function(){var a=c.documentElement,b=a.matchesSelector||a.mozMatchesSelector||a.webkitMatchesSelector||a.msMatchesSelector;if(b){var d=!b.call(c.createElement("div"),"div"),e=!1;try{b.call(c.documentElement,"[test!='']:sizzle")}catch(f){e=!0}m.matchesSelector=function(a,c){c=c.replace(/\=\s*([^'"\]]*)\s*\]/g,"='$1']");if(!m.isXML(a))try{if(e||!o.match.PSEUDO.test(c)&&!/!=/.test(c)){var f=b.call(a,c);if(f||!d||a.document&&a.document.nodeType!==11)return f}}catch(g){}return m(c,null,null,[a]).length>0}}}(),function(){var a=c.createElement("div");a.innerHTML="<div class='test e'></div><div class='test'></div>";if(!!a.getElementsByClassName&&a.getElementsByClassName("e").length!==0){a.lastChild.className="e";if(a.getElementsByClassName("e").length===1)return;o.order.splice(1,0,"CLASS"),o.find.CLASS=function(a,b,c){if(typeof b.getElementsByClassName!="undefined"&&!c)return b.getElementsByClassName(a[1])},a=null}}(),c.documentElement.contains?m.contains=function(a,b){return a!==b&&(a.contains?a.contains(b):!0)}:c.documentElement.compareDocumentPosition?m.contains=function(a,b){return!!(a.compareDocumentPosition(b)&16)}:m.contains=function(){return!1},m.isXML=function(a){var b=(a?a.ownerDocument||a:0).documentElement;return b?b.nodeName!=="HTML":!1};var y=function(a,b,c){var d,e=[],f="",g=b.nodeType?[b]:b;while(d=o.match.PSEUDO.exec(a))f+=d[0],a=a.replace(o.match.PSEUDO,"");a=o.relative[a]?a+"*":a;for(var h=0,i=g.length;h<i;h++)m(a,g[h],e,c);return m.filter(f,e)};m.attr=f.attr,m.selectors.attrMap={},f.find=m,f.expr=m.selectors,f.expr[":"]=f.expr.filters,f.unique=m.uniqueSort,f.text=m.getText,f.isXMLDoc=m.isXML,f.contains=m.contains}();var L=/Until$/,M=/^(?:parents|prevUntil|prevAll)/,N=/,/,O=/^.[^:#\[\.,]*$/,P=Array.prototype.slice,Q=f.expr.match.globalPOS,R={children:!0,contents:!0,next:!0,prev:!0};f.fn.extend({find:function(a){var b=this,c,d;if(typeof a!="string")return f(a).filter(function(){for(c=0,d=b.length;c<d;c++)if(f.contains(b[c],this))return!0});var e=this.pushStack("","find",a),g,h,i;for(c=0,d=this.length;c<d;c++){g=e.length,f.find(a,this[c],e);if(c>0)for(h=g;h<e.length;h++)for(i=0;i<g;i++)if(e[i]===e[h]){e.splice(h--,1);break}}return e},has:function(a){var b=f(a);return this.filter(function(){for(var a=0,c=b.length;a<c;a++)if(f.contains(this,b[a]))return!0})},not:function(a){return this.pushStack(T(this,a,!1),"not",a)},filter:function(a){return this.pushStack(T(this,a,!0),"filter",a)},is:function(a){return!!a&&(typeof a=="string"?Q.test(a)?f(a,this.context).index(this[0])>=0:f.filter(a,this).length>0:this.filter(a).length>0)},closest:function(a,b){var c=[],d,e,g=this[0];if(f.isArray(a)){var h=1;while(g&&g.ownerDocument&&g!==b){for(d=0;d<a.length;d++)f(g).is(a[d])&&c.push({selector:a[d],elem:g,level:h});g=g.parentNode,h++}return c}var i=Q.test(a)||typeof a!="string"?f(a,b||this.context):0;for(d=0,e=this.length;d<e;d++){g=this[d];while(g){if(i?i.index(g)>-1:f.find.matchesSelector(g,a)){c.push(g);break}g=g.parentNode;if(!g||!g.ownerDocument||g===b||g.nodeType===11)break}}c=c.length>1?f.unique(c):c;return this.pushStack(c,"closest",a)},index:function(a){if(!a)return this[0]&&this[0].parentNode?this.prevAll().length:-1;if(typeof a=="string")return f.inArray(this[0],f(a));return f.inArray(a.jquery?a[0]:a,this)},add:function(a,b){var c=typeof a=="string"?f(a,b):f.makeArray(a&&a.nodeType?[a]:a),d=f.merge(this.get(),c);return this.pushStack(S(c[0])||S(d[0])?d:f.unique(d))},andSelf:function(){return this.add(this.prevObject)}}),f.each({parent:function(a){var b=a.parentNode;return b&&b.nodeType!==11?b:null},parents:function(a){return f.dir(a,"parentNode")},parentsUntil:function(a,b,c){return f.dir(a,"parentNode",c)},next:function(a){return f.nth(a,2,"nextSibling")},prev:function(a){return f.nth(a,2,"previousSibling")},nextAll:function(a){return f.dir(a,"nextSibling")},prevAll:function(a){return f.dir(a,"previousSibling")},nextUntil:function(a,b,c){return f.dir(a,"nextSibling",c)},prevUntil:function(a,b,c){return f.dir(a,"previousSibling",c)},siblings:function(a){return f.sibling((a.parentNode||{}).firstChild,a)},children:function(a){return f.sibling(a.firstChild)},contents:function(a){return f.nodeName(a,"iframe")?a.contentDocument||a.contentWindow.document:f.makeArray(a.childNodes)}},function(a,b){f.fn[a]=function(c,d){var e=f.map(this,b,c);L.test(a)||(d=c),d&&typeof d=="string"&&(e=f.filter(d,e)),e=this.length>1&&!R[a]?f.unique(e):e,(this.length>1||N.test(d))&&M.test(a)&&(e=e.reverse());return this.pushStack(e,a,P.call(arguments).join(","))}}),f.extend({filter:function(a,b,c){c&&(a=":not("+a+")");return b.length===1?f.find.matchesSelector(b[0],a)?[b[0]]:[]:f.find.matches(a,b)},dir:function(a,c,d){var e=[],g=a[c];while(g&&g.nodeType!==9&&(d===b||g.nodeType!==1||!f(g).is(d)))g.nodeType===1&&e.push(g),g=g[c];return e},nth:function(a,b,c,d){b=b||1;var e=0;for(;a;a=a[c])if(a.nodeType===1&&++e===b)break;return a},sibling:function(a,b){var c=[];for(;a;a=a.nextSibling)a.nodeType===1&&a!==b&&c.push(a);return c}});var V="abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",W=/ jQuery\d+="(?:\d+|null)"/g,X=/^\s+/,Y=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/ig,Z=/<([\w:]+)/,$=/<tbody/i,_=/<|&#?\w+;/,ba=/<(?:script|style)/i,bb=/<(?:script|object|embed|option|style)/i,bc=new RegExp("<(?:"+V+")[\\s/>]","i"),bd=/checked\s*(?:[^=]|=\s*.checked.)/i,be=/\/(java|ecma)script/i,bf=/^\s*<!(?:\[CDATA\[|\-\-)/,bg={option:[1,"<select multiple='multiple'>","</select>"],legend:[1,"<fieldset>","</fieldset>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],area:[1,"<map>","</map>"],_default:[0,"",""]},bh=U(c);bg.optgroup=bg.option,bg.tbody=bg.tfoot=bg.colgroup=bg.caption=bg.thead,bg.th=bg.td,f.support.htmlSerialize||(bg._default=[1,"div<div>","</div>"]),f.fn.extend({text:function(a){return f.access(this,function(a){return a===b?f.text(this):this.empty().append((this[0]&&this[0].ownerDocument||c).createTextNode(a))},null,a,arguments.length)},wrapAll:function(a){if(f.isFunction(a))return this.each(function(b){f(this).wrapAll(a.call(this,b))});if(this[0]){var b=f(a,this[0].ownerDocument).eq(0).clone(!0);this[0].parentNode&&b.insertBefore(this[0]),b.map(function(){var a=this;while(a.firstChild&&a.firstChild.nodeType===1)a=a.firstChild;return a}).append(this)}return this},wrapInner:function(a){if(f.isFunction(a))return this.each(function(b){f(this).wrapInner(a.call(this,b))});return this.each(function(){var b=f(this),c=b.contents();c.length?c.wrapAll(a):b.append(a)})},wrap:function(a){var b=f.isFunction(a);return this.each(function(c){f(this).wrapAll(b?a.call(this,c):a)})},unwrap:function(){return this.parent().each(function(){f.nodeName(this,"body")||f(this).replaceWith(this.childNodes)}).end()},append:function(){return this.domManip(arguments,!0,function(a){this.nodeType===1&&this.appendChild(a)})},prepend:function(){return this.domManip(arguments,!0,function(a){this.nodeType===1&&this.insertBefore(a,this.firstChild)})},before:function(){if(this[0]&&this[0].parentNode)return this.domManip(arguments,!1,function(a){this.parentNode.insertBefore(a,this)});if(arguments.length){var a=f
	.clean(arguments);a.push.apply(a,this.toArray());return this.pushStack(a,"before",arguments)}},after:function(){if(this[0]&&this[0].parentNode)return this.domManip(arguments,!1,function(a){this.parentNode.insertBefore(a,this.nextSibling)});if(arguments.length){var a=this.pushStack(this,"after",arguments);a.push.apply(a,f.clean(arguments));return a}},remove:function(a,b){for(var c=0,d;(d=this[c])!=null;c++)if(!a||f.filter(a,[d]).length)!b&&d.nodeType===1&&(f.cleanData(d.getElementsByTagName("*")),f.cleanData([d])),d.parentNode&&d.parentNode.removeChild(d);return this},empty:function(){for(var a=0,b;(b=this[a])!=null;a++){b.nodeType===1&&f.cleanData(b.getElementsByTagName("*"));while(b.firstChild)b.removeChild(b.firstChild)}return this},clone:function(a,b){a=a==null?!1:a,b=b==null?a:b;return this.map(function(){return f.clone(this,a,b)})},html:function(a){return f.access(this,function(a){var c=this[0]||{},d=0,e=this.length;if(a===b)return c.nodeType===1?c.innerHTML.replace(W,""):null;if(typeof a=="string"&&!ba.test(a)&&(f.support.leadingWhitespace||!X.test(a))&&!bg[(Z.exec(a)||["",""])[1].toLowerCase()]){a=a.replace(Y,"<$1></$2>");try{for(;d<e;d++)c=this[d]||{},c.nodeType===1&&(f.cleanData(c.getElementsByTagName("*")),c.innerHTML=a);c=0}catch(g){}}c&&this.empty().append(a)},null,a,arguments.length)},replaceWith:function(a){if(this[0]&&this[0].parentNode){if(f.isFunction(a))return this.each(function(b){var c=f(this),d=c.html();c.replaceWith(a.call(this,b,d))});typeof a!="string"&&(a=f(a).detach());return this.each(function(){var b=this.nextSibling,c=this.parentNode;f(this).remove(),b?f(b).before(a):f(c).append(a)})}return this.length?this.pushStack(f(f.isFunction(a)?a():a),"replaceWith",a):this},detach:function(a){return this.remove(a,!0)},domManip:function(a,c,d){var e,g,h,i,j=a[0],k=[];if(!f.support.checkClone&&arguments.length===3&&typeof j=="string"&&bd.test(j))return this.each(function(){f(this).domManip(a,c,d,!0)});if(f.isFunction(j))return this.each(function(e){var g=f(this);a[0]=j.call(this,e,c?g.html():b),g.domManip(a,c,d)});if(this[0]){i=j&&j.parentNode,f.support.parentNode&&i&&i.nodeType===11&&i.childNodes.length===this.length?e={fragment:i}:e=f.buildFragment(a,this,k),h=e.fragment,h.childNodes.length===1?g=h=h.firstChild:g=h.firstChild;if(g){c=c&&f.nodeName(g,"tr");for(var l=0,m=this.length,n=m-1;l<m;l++)d.call(c?bi(this[l],g):this[l],e.cacheable||m>1&&l<n?f.clone(h,!0,!0):h)}k.length&&f.each(k,function(a,b){b.src?f.ajax({type:"GET",global:!1,url:b.src,async:!1,dataType:"script"}):f.globalEval((b.text||b.textContent||b.innerHTML||"").replace(bf,"/*$0*/")),b.parentNode&&b.parentNode.removeChild(b)})}return this}}),f.buildFragment=function(a,b,d){var e,g,h,i,j=a[0];b&&b[0]&&(i=b[0].ownerDocument||b[0]),i.createDocumentFragment||(i=c),a.length===1&&typeof j=="string"&&j.length<512&&i===c&&j.charAt(0)==="<"&&!bb.test(j)&&(f.support.checkClone||!bd.test(j))&&(f.support.html5Clone||!bc.test(j))&&(g=!0,h=f.fragments[j],h&&h!==1&&(e=h)),e||(e=i.createDocumentFragment(),f.clean(a,i,e,d)),g&&(f.fragments[j]=h?e:1);return{fragment:e,cacheable:g}},f.fragments={},f.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(a,b){f.fn[a]=function(c){var d=[],e=f(c),g=this.length===1&&this[0].parentNode;if(g&&g.nodeType===11&&g.childNodes.length===1&&e.length===1){e[b](this[0]);return this}for(var h=0,i=e.length;h<i;h++){var j=(h>0?this.clone(!0):this).get();f(e[h])[b](j),d=d.concat(j)}return this.pushStack(d,a,e.selector)}}),f.extend({clone:function(a,b,c){var d,e,g,h=f.support.html5Clone||f.isXMLDoc(a)||!bc.test("<"+a.nodeName+">")?a.cloneNode(!0):bo(a);if((!f.support.noCloneEvent||!f.support.noCloneChecked)&&(a.nodeType===1||a.nodeType===11)&&!f.isXMLDoc(a)){bk(a,h),d=bl(a),e=bl(h);for(g=0;d[g];++g)e[g]&&bk(d[g],e[g])}if(b){bj(a,h);if(c){d=bl(a),e=bl(h);for(g=0;d[g];++g)bj(d[g],e[g])}}d=e=null;return h},clean:function(a,b,d,e){var g,h,i,j=[];b=b||c,typeof b.createElement=="undefined"&&(b=b.ownerDocument||b[0]&&b[0].ownerDocument||c);for(var k=0,l;(l=a[k])!=null;k++){typeof l=="number"&&(l+="");if(!l)continue;if(typeof l=="string")if(!_.test(l))l=b.createTextNode(l);else{l=l.replace(Y,"<$1></$2>");var m=(Z.exec(l)||["",""])[1].toLowerCase(),n=bg[m]||bg._default,o=n[0],p=b.createElement("div"),q=bh.childNodes,r;b===c?bh.appendChild(p):U(b).appendChild(p),p.innerHTML=n[1]+l+n[2];while(o--)p=p.lastChild;if(!f.support.tbody){var s=$.test(l),t=m==="table"&&!s?p.firstChild&&p.firstChild.childNodes:n[1]==="<table>"&&!s?p.childNodes:[];for(i=t.length-1;i>=0;--i)f.nodeName(t[i],"tbody")&&!t[i].childNodes.length&&t[i].parentNode.removeChild(t[i])}!f.support.leadingWhitespace&&X.test(l)&&p.insertBefore(b.createTextNode(X.exec(l)[0]),p.firstChild),l=p.childNodes,p&&(p.parentNode.removeChild(p),q.length>0&&(r=q[q.length-1],r&&r.parentNode&&r.parentNode.removeChild(r)))}var u;if(!f.support.appendChecked)if(l[0]&&typeof (u=l.length)=="number")for(i=0;i<u;i++)bn(l[i]);else bn(l);l.nodeType?j.push(l):j=f.merge(j,l)}if(d){g=function(a){return!a.type||be.test(a.type)};for(k=0;j[k];k++){h=j[k];if(e&&f.nodeName(h,"script")&&(!h.type||be.test(h.type)))e.push(h.parentNode?h.parentNode.removeChild(h):h);else{if(h.nodeType===1){var v=f.grep(h.getElementsByTagName("script"),g);j.splice.apply(j,[k+1,0].concat(v))}d.appendChild(h)}}}return j},cleanData:function(a){var b,c,d=f.cache,e=f.event.special,g=f.support.deleteExpando;for(var h=0,i;(i=a[h])!=null;h++){if(i.nodeName&&f.noData[i.nodeName.toLowerCase()])continue;c=i[f.expando];if(c){b=d[c];if(b&&b.events){for(var j in b.events)e[j]?f.event.remove(i,j):f.removeEvent(i,j,b.handle);b.handle&&(b.handle.elem=null)}g?delete i[f.expando]:i.removeAttribute&&i.removeAttribute(f.expando),delete d[c]}}}});var bp=/alpha\([^)]*\)/i,bq=/opacity=([^)]*)/,br=/([A-Z]|^ms)/g,bs=/^[\-+]?(?:\d*\.)?\d+$/i,bt=/^-?(?:\d*\.)?\d+(?!px)[^\d\s]+$/i,bu=/^([\-+])=([\-+.\de]+)/,bv=/^margin/,bw={position:"absolute",visibility:"hidden",display:"block"},bx=["Top","Right","Bottom","Left"],by,bz,bA;f.fn.css=function(a,c){return f.access(this,function(a,c,d){return d!==b?f.style(a,c,d):f.css(a,c)},a,c,arguments.length>1)},f.extend({cssHooks:{opacity:{get:function(a,b){if(b){var c=by(a,"opacity");return c===""?"1":c}return a.style.opacity}}},cssNumber:{fillOpacity:!0,fontWeight:!0,lineHeight:!0,opacity:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":f.support.cssFloat?"cssFloat":"styleFloat"},style:function(a,c,d,e){if(!!a&&a.nodeType!==3&&a.nodeType!==8&&!!a.style){var g,h,i=f.camelCase(c),j=a.style,k=f.cssHooks[i];c=f.cssProps[i]||i;if(d===b){if(k&&"get"in k&&(g=k.get(a,!1,e))!==b)return g;return j[c]}h=typeof d,h==="string"&&(g=bu.exec(d))&&(d=+(g[1]+1)*+g[2]+parseFloat(f.css(a,c)),h="number");if(d==null||h==="number"&&isNaN(d))return;h==="number"&&!f.cssNumber[i]&&(d+="px");if(!k||!("set"in k)||(d=k.set(a,d))!==b)try{j[c]=d}catch(l){}}},css:function(a,c,d){var e,g;c=f.camelCase(c),g=f.cssHooks[c],c=f.cssProps[c]||c,c==="cssFloat"&&(c="float");if(g&&"get"in g&&(e=g.get(a,!0,d))!==b)return e;if(by)return by(a,c)},swap:function(a,b,c){var d={},e,f;for(f in b)d[f]=a.style[f],a.style[f]=b[f];e=c.call(a);for(f in b)a.style[f]=d[f];return e}}),f.curCSS=f.css,c.defaultView&&c.defaultView.getComputedStyle&&(bz=function(a,b){var c,d,e,g,h=a.style;b=b.replace(br,"-$1").toLowerCase(),(d=a.ownerDocument.defaultView)&&(e=d.getComputedStyle(a,null))&&(c=e.getPropertyValue(b),c===""&&!f.contains(a.ownerDocument.documentElement,a)&&(c=f.style(a,b))),!f.support.pixelMargin&&e&&bv.test(b)&&bt.test(c)&&(g=h.width,h.width=c,c=e.width,h.width=g);return c}),c.documentElement.currentStyle&&(bA=function(a,b){var c,d,e,f=a.currentStyle&&a.currentStyle[b],g=a.style;f==null&&g&&(e=g[b])&&(f=e),bt.test(f)&&(c=g.left,d=a.runtimeStyle&&a.runtimeStyle.left,d&&(a.runtimeStyle.left=a.currentStyle.left),g.left=b==="fontSize"?"1em":f,f=g.pixelLeft+"px",g.left=c,d&&(a.runtimeStyle.left=d));return f===""?"auto":f}),by=bz||bA,f.each(["height","width"],function(a,b){f.cssHooks[b]={get:function(a,c,d){if(c)return a.offsetWidth!==0?bB(a,b,d):f.swap(a,bw,function(){return bB(a,b,d)})},set:function(a,b){return bs.test(b)?b+"px":b}}}),f.support.opacity||(f.cssHooks.opacity={get:function(a,b){return bq.test((b&&a.currentStyle?a.currentStyle.filter:a.style.filter)||"")?parseFloat(RegExp.$1)/100+"":b?"1":""},set:function(a,b){var c=a.style,d=a.currentStyle,e=f.isNumeric(b)?"alpha(opacity="+b*100+")":"",g=d&&d.filter||c.filter||"";c.zoom=1;if(b>=1&&f.trim(g.replace(bp,""))===""){c.removeAttribute("filter");if(d&&!d.filter)return}c.filter=bp.test(g)?g.replace(bp,e):g+" "+e}}),f(function(){f.support.reliableMarginRight||(f.cssHooks.marginRight={get:function(a,b){return f.swap(a,{display:"inline-block"},function(){return b?by(a,"margin-right"):a.style.marginRight})}})}),f.expr&&f.expr.filters&&(f.expr.filters.hidden=function(a){var b=a.offsetWidth,c=a.offsetHeight;return b===0&&c===0||!f.support.reliableHiddenOffsets&&(a.style&&a.style.display||f.css(a,"display"))==="none"},f.expr.filters.visible=function(a){return!f.expr.filters.hidden(a)}),f.each({margin:"",padding:"",border:"Width"},function(a,b){f.cssHooks[a+b]={expand:function(c){var d,e=typeof c=="string"?c.split(" "):[c],f={};for(d=0;d<4;d++)f[a+bx[d]+b]=e[d]||e[d-2]||e[0];return f}}});var bC=/%20/g,bD=/\[\]$/,bE=/\r?\n/g,bF=/#.*$/,bG=/^(.*?):[ \t]*([^\r\n]*)\r?$/mg,bH=/^(?:color|date|datetime|datetime-local|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i,bI=/^(?:about|app|app\-storage|.+\-extension|file|res|widget):$/,bJ=/^(?:GET|HEAD)$/,bK=/^\/\//,bL=/\?/,bM=/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,bN=/^(?:select|textarea)/i,bO=/\s+/,bP=/([?&])_=[^&]*/,bQ=/^([\w\+\.\-]+:)(?:\/\/([^\/?#:]*)(?::(\d+))?)?/,bR=f.fn.load,bS={},bT={},bU,bV,bW=["*/"]+["*"];try{bU=e.href}catch(bX){bU=c.createElement("a"),bU.href="",bU=bU.href}bV=bQ.exec(bU.toLowerCase())||[],f.fn.extend({load:function(a,c,d){if(typeof a!="string"&&bR)return bR.apply(this,arguments);if(!this.length)return this;var e=a.indexOf(" ");if(e>=0){var g=a.slice(e,a.length);a=a.slice(0,e)}var h="GET";c&&(f.isFunction(c)?(d=c,c=b):typeof c=="object"&&(c=f.param(c,f.ajaxSettings.traditional),h="POST"));var i=this;f.ajax({url:a,type:h,dataType:"html",data:c,complete:function(a,b,c){c=a.responseText,a.isResolved()&&(a.done(function(a){c=a}),i.html(g?f("<div>").append(c.replace(bM,"")).find(g):c)),d&&i.each(d,[c,b,a])}});return this},serialize:function(){return f.param(this.serializeArray())},serializeArray:function(){return this.map(function(){return this.elements?f.makeArray(this.elements):this}).filter(function(){return this.name&&!this.disabled&&(this.checked||bN.test(this.nodeName)||bH.test(this.type))}).map(function(a,b){var c=f(this).val();return c==null?null:f.isArray(c)?f.map(c,function(a,c){return{name:b.name,value:a.replace(bE,"\r\n")}}):{name:b.name,value:c.replace(bE,"\r\n")}}).get()}}),f.each("ajaxStart ajaxStop ajaxComplete ajaxError ajaxSuccess ajaxSend".split(" "),function(a,b){f.fn[b]=function(a){return this.on(b,a)}}),f.each(["get","post"],function(a,c){f[c]=function(a,d,e,g){f.isFunction(d)&&(g=g||e,e=d,d=b);return f.ajax({type:c,url:a,data:d,success:e,dataType:g})}}),f.extend({getScript:function(a,c){return f.get(a,b,c,"script")},getJSON:function(a,b,c){return f.get(a,b,c,"json")},ajaxSetup:function(a,b){b?b$(a,f.ajaxSettings):(b=a,a=f.ajaxSettings),b$(a,b);return a},ajaxSettings:{url:bU,isLocal:bI.test(bV[1]),global:!0,type:"GET",contentType:"application/x-www-form-urlencoded; charset=UTF-8",processData:!0,async:!0,accepts:{xml:"application/xml, text/xml",html:"text/html",text:"text/plain",json:"application/json, text/javascript","*":bW},contents:{xml:/xml/,html:/html/,json:/json/},responseFields:{xml:"responseXML",text:"responseText"},converters:{"* text":a.String,"text html":!0,"text json":f.parseJSON,"text xml":f.parseXML},flatOptions:{context:!0,url:!0}},ajaxPrefilter:bY(bS),ajaxTransport:bY(bT),ajax:function(a,c){function w(a,c,l,m){if(s!==2){s=2,q&&clearTimeout(q),p=b,n=m||"",v.readyState=a>0?4:0;var o,r,u,w=c,x=l?ca(d,v,l):b,y,z;if(a>=200&&a<300||a===304){if(d.ifModified){if(y=v.getResponseHeader("Last-Modified"))f.lastModified[k]=y;if(z=v.getResponseHeader("Etag"))f.etag[k]=z}if(a===304)w="notmodified",o=!0;else try{r=cb(d,x),w="success",o=!0}catch(A){w="parsererror",u=A}}else{u=w;if(!w||a)w="error",a<0&&(a=0)}v.status=a,v.statusText=""+(c||w),o?h.resolveWith(e,[r,w,v]):h.rejectWith(e,[v,w,u]),v.statusCode(j),j=b,t&&g.trigger("ajax"+(o?"Success":"Error"),[v,d,o?r:u]),i.fireWith(e,[v,w]),t&&(g.trigger("ajaxComplete",[v,d]),--f.active||f.event.trigger("ajaxStop"))}}typeof a=="object"&&(c=a,a=b),c=c||{};var d=f.ajaxSetup({},c),e=d.context||d,g=e!==d&&(e.nodeType||e instanceof f)?f(e):f.event,h=f.Deferred(),i=f.Callbacks("once memory"),j=d.statusCode||{},k,l={},m={},n,o,p,q,r,s=0,t,u,v={readyState:0,setRequestHeader:function(a,b){if(!s){var c=a.toLowerCase();a=m[c]=m[c]||a,l[a]=b}return this},getAllResponseHeaders:function(){return s===2?n:null},getResponseHeader:function(a){var c;if(s===2){if(!o){o={};while(c=bG.exec(n))o[c[1].toLowerCase()]=c[2]}c=o[a.toLowerCase()]}return c===b?null:c},overrideMimeType:function(a){s||(d.mimeType=a);return this},abort:function(a){a=a||"abort",p&&p.abort(a),w(0,a);return this}};h.promise(v),v.success=v.done,v.error=v.fail,v.complete=i.add,v.statusCode=function(a){if(a){var b;if(s<2)for(b in a)j[b]=[j[b],a[b]];else b=a[v.status],v.then(b,b)}return this},d.url=((a||d.url)+"").replace(bF,"").replace(bK,bV[1]+"//"),d.dataTypes=f.trim(d.dataType||"*").toLowerCase().split(bO),d.crossDomain==null&&(r=bQ.exec(d.url.toLowerCase()),d.crossDomain=!(!r||r[1]==bV[1]&&r[2]==bV[2]&&(r[3]||(r[1]==="http:"?80:443))==(bV[3]||(bV[1]==="http:"?80:443)))),d.data&&d.processData&&typeof d.data!="string"&&(d.data=f.param(d.data,d.traditional)),bZ(bS,d,c,v);if(s===2)return!1;t=d.global,d.type=d.type.toUpperCase(),d.hasContent=!bJ.test(d.type),t&&f.active++===0&&f.event.trigger("ajaxStart");if(!d.hasContent){d.data&&(d.url+=(bL.test(d.url)?"&":"?")+d.data,delete d.data),k=d.url;if(d.cache===!1){var x=f.now(),y=d.url.replace(bP,"$1_="+x);d.url=y+(y===d.url?(bL.test(d.url)?"&":"?")+"_="+x:"")}}(d.data&&d.hasContent&&d.contentType!==!1||c.contentType)&&v.setRequestHeader("Content-Type",d.contentType),d.ifModified&&(k=k||d.url,f.lastModified[k]&&v.setRequestHeader("If-Modified-Since",f.lastModified[k]),f.etag[k]&&v.setRequestHeader("If-None-Match",f.etag[k])),v.setRequestHeader("Accept",d.dataTypes[0]&&d.accepts[d.dataTypes[0]]?d.accepts[d.dataTypes[0]]+(d.dataTypes[0]!=="*"?", "+bW+"; q=0.01":""):d.accepts["*"]);for(u in d.headers)v.setRequestHeader(u,d.headers[u]);if(d.beforeSend&&(d.beforeSend.call(e,v,d)===!1||s===2)){v.abort();return!1}for(u in{success:1,error:1,complete:1})v[u](d[u]);p=bZ(bT,d,c,v);if(!p)w(-1,"No Transport");else{v.readyState=1,t&&g.trigger("ajaxSend",[v,d]),d.async&&d.timeout>0&&(q=setTimeout(function(){v.abort("timeout")},d.timeout));try{s=1,p.send(l,w)}catch(z){if(s<2)w(-1,z);else throw z}}return v},param:function(a,c){var d=[],e=function(a,b){b=f.isFunction(b)?b():b,d[d.length]=encodeURIComponent(a)+"="+encodeURIComponent(b)};c===b&&(c=f.ajaxSettings.traditional);if(f.isArray(a)||a.jquery&&!f.isPlainObject(a))f.each(a,function(){e(this.name,this.value)});else for(var g in a)b_(g,a[g],c,e);return d.join("&").replace(bC,"+")}}),f.extend({active:0,lastModified:{},etag:{}});var cc=f.now(),cd=/(\=)\?(&|$)|\?\?/i;f.ajaxSetup({jsonp:"callback",jsonpCallback:function(){return f.expando+"_"+cc++}}),f.ajaxPrefilter("json jsonp",function(b,c,d){var e=typeof b.data=="string"&&/^application\/x\-www\-form\-urlencoded/.test(b.contentType);if(b.dataTypes[0]==="jsonp"||b.jsonp!==!1&&(cd.test(b.url)||e&&cd.test(b.data))){var g,h=b.jsonpCallback=f.isFunction(b.jsonpCallback)?b.jsonpCallback():b.jsonpCallback,i=a[h],j=b.url,k=b.data,l="$1"+h+"$2";b.jsonp!==!1&&(j=j.replace(cd,l),b.url===j&&(e&&(k=k.replace(cd,l)),b.data===k&&(j+=(/\?/.test(j)?"&":"?")+b.jsonp+"="+h))),b.url=j,b.data=k,a[h]=function(a){g=[a]},d.always(function(){a[h]=i,g&&f.isFunction(i)&&a[h](g[0])}),b.converters["script json"]=function(){g||f.error(h+" was not called");return g[0]},b.dataTypes[0]="json";return"script"}}),f.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/javascript|ecmascript/},converters:{"text script":function(a){f.globalEval(a);return a}}}),f.ajaxPrefilter("script",function(a){a.cache===b&&(a.cache=!1),a.crossDomain&&(a.type="GET",a.global=!1)}),f.ajaxTransport("script",function(a){if(a.crossDomain){var d,e=c.head||c.getElementsByTagName("head")[0]||c.documentElement;return{send:function(f,g){d=c.createElement("script"),d.async="async",a.scriptCharset&&(d.charset=a.scriptCharset),d.src=a.url,d.onload=d.onreadystatechange=function(a,c){if(c||!d.readyState||/loaded|complete/.test(d.readyState))d.onload=d.onreadystatechange=null,e&&d.parentNode&&e.removeChild(d),d=b,c||g(200,"success")},e.insertBefore(d,e.firstChild)},abort:function(){d&&d.onload(0,1)}}}});var ce=a.ActiveXObject?function(){for(var a in cg)cg[a](0,1)}:!1,cf=0,cg;f.ajaxSettings.xhr=a.ActiveXObject?function(){return!this.isLocal&&ch()||ci()}:ch,function(a){f.extend(f.support,{ajax:!!a,cors:!!a&&"withCredentials"in a})}(f.ajaxSettings.xhr()),f.support.ajax&&f.ajaxTransport(function(c){if(!c.crossDomain||f.support.cors){var d;return{send:function(e,g){var h=c.xhr(),i,j;c.username?h.open(c.type,c.url,c.async,c.username,c.password):h.open(c.type,c.url,c.async);if(c.xhrFields)for(j in c.xhrFields)h[j]=c.xhrFields[j];c.mimeType&&h.overrideMimeType&&h.overrideMimeType(c.mimeType),!c.crossDomain&&!e["X-Requested-With"]&&(e["X-Requested-With"]="XMLHttpRequest");try{for(j in e)h.setRequestHeader(j,e[j])}catch(k){}h.send(c.hasContent&&c.data||null),d=function(a,e){var j,k,l,m,n;try{if(d&&(e||h.readyState===4)){d=b,i&&(h.onreadystatechange=f.noop,ce&&delete cg[i]);if(e)h.readyState!==4&&h.abort();else{j=h.status,l=h.getAllResponseHeaders(),m={},n=h.responseXML,n&&n.documentElement&&(m.xml=n);try{m.text=h.responseText}catch(a){}try{k=h.statusText}catch(o){k=""}!j&&c.isLocal&&!c.crossDomain?j=m.text?200:404:j===1223&&(j=204)}}}catch(p){e||g(-1,p)}m&&g(j,k,m,l)},!c.async||h.readyState===4?d():(i=++cf,ce&&(cg||(cg={},f(a).unload(ce)),cg[i]=d),h.onreadystatechange=d)},abort:function(){d&&d(0,1)}}}});var cj={},ck,cl,cm=/^(?:toggle|show|hide)$/,cn=/^([+\-]=)?([\d+.\-]+)([a-z%]*)$/i,co,cp=[["height","marginTop","marginBottom","paddingTop","paddingBottom"],["width","marginLeft","marginRight","paddingLeft","paddingRight"],["opacity"]],cq;f.fn.extend({show:function(a,b,c){var d,e;if(a||a===0)return this.animate(ct("show",3),a,b,c);for(var g=0,h=this.length;g<h;g++)d=this[g],d.style&&(e=d.style.display,!f._data(d,"olddisplay")&&e==="none"&&(e=d.style.display=""),(e===""&&f.css(d,"display")==="none"||!f.contains(d.ownerDocument.documentElement,d))&&f._data(d,"olddisplay",cu(d.nodeName)));for(g=0;g<h;g++){d=this[g];if(d.style){e=d.style.display;if(e===""||e==="none")d.style.display=f._data(d,"olddisplay")||""}}return this},hide:function(a,b,c){if(a||a===0)return this.animate(ct("hide",3),a,b,c);var d,e,g=0,h=this.length;for(;g<h;g++)d=this[g],d.style&&(e=f.css(d,"display"),e!=="none"&&!f._data(d,"olddisplay")&&f._data(d,"olddisplay",e));for(g=0;g<h;g++)this[g].style&&(this[g].style.display="none");return this},_toggle:f.fn.toggle,toggle:function(a,b,c){var d=typeof a=="boolean";f.isFunction(a)&&f.isFunction(b)?this._toggle.apply(this,arguments):a==null||d?this.each(function(){var b=d?a:f(this).is(":hidden");f(this)[b?"show":"hide"]()}):this.animate(ct("toggle",3),a,b,c);return this},fadeTo:function(a,b,c,d){return this.filter(":hidden").css("opacity",0).show().end().animate({opacity:b},a,c,d)},animate:function(a,b,c,d){function g(){e.queue===!1&&f._mark(this);var b=f.extend({},e),c=this.nodeType===1,d=c&&f(this).is(":hidden"),g,h,i,j,k,l,m,n,o,p,q;b.animatedProperties={};for(i in a){g=f.camelCase(i),i!==g&&(a[g]=a[i],delete a[i]);if((k=f.cssHooks[g])&&"expand"in k){l=k.expand(a[g]),delete a[g];for(i in l)i in a||(a[i]=l[i])}}for(g in a){h=a[g],f.isArray(h)?(b.animatedProperties[g]=h[1],h=a[g]=h[0]):b.animatedProperties[g]=b.specialEasing&&b.specialEasing[g]||b.easing||"swing";if(h==="hide"&&d||h==="show"&&!d)return b.complete.call(this);c&&(g==="height"||g==="width")&&(b.overflow=[this.style.overflow,this.style.overflowX,this.style.overflowY],f.css(this,"display")==="inline"&&f.css(this,"float")==="none"&&(!f.support.inlineBlockNeedsLayout||cu(this.nodeName)==="inline"?this.style.display="inline-block":this.style.zoom=1))}b.overflow!=null&&(this.style.overflow="hidden");for(i in a)j=new f.fx(this,b,i),h=a[i],cm.test(h)?(q=f._data(this,"toggle"+i)||(h==="toggle"?d?"show":"hide":0),q?(f._data(this,"toggle"+i,q==="show"?"hide":"show"),j[q]()):j[h]()):(m=cn.exec(h),n=j.cur(),m?(o=parseFloat(m[2]),p=m[3]||(f.cssNumber[i]?"":"px"),p!=="px"&&(f.style(this,i,(o||1)+p),n=(o||1)/j.cur()*n,f.style(this,i,n+p)),m[1]&&(o=(m[1]==="-="?-1:1)*o+n),j.custom(n,o,p)):j.custom(n,h,""));return!0}var e=f.speed(b,c,d);if(f.isEmptyObject(a))return this.each(e.complete,[!1]);a=f.extend({},a);return e.queue===!1?this.each(g):this.queue(e.queue,g)},stop:function(a,c,d){typeof a!="string"&&(d=c,c=a,a=b),c&&a!==!1&&this.queue(a||"fx",[]);return this.each(function(){function h(a,b,c){var e=b[c];f.removeData(a,c,!0),e.stop(d)}var b,c=!1,e=f.timers,g=f._data(this);d||f._unmark(!0,this);if(a==null)for(b in g)g[b]&&g[b].stop&&b.indexOf(".run")===b.length-4&&h(this,g,b);else g[b=a+".run"]&&g[b].stop&&h(this,g,b);for(b=e.length;b--;)e[b].elem===this&&(a==null||e[b].queue===a)&&(d?e[b](!0):e[b].saveState(),c=!0,e.splice(b,1));(!d||!c)&&f.dequeue(this,a)})}}),f.each({slideDown:ct("show",1),slideUp:ct("hide",1),slideToggle:ct("toggle",1),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(a,b){f.fn[a]=function(a,c,d){return this.animate(b,a,c,d)}}),f.extend({speed:function(a,b,c){var d=a&&typeof a=="object"?f.extend({},a):{complete:c||!c&&b||f.isFunction(a)&&a,duration:a,easing:c&&b||b&&!f.isFunction(b)&&b};d.duration=f.fx.off?0:typeof d.duration=="number"?d.duration:d.duration in f.fx.speeds?f.fx.speeds[d.duration]:f.fx.speeds._default;if(d.queue==null||d.queue===!0)d.queue="fx";d.old=d.complete,d.complete=function(a){f.isFunction(d.old)&&d.old.call(this),d.queue?f.dequeue(this,d.queue):a!==!1&&f._unmark(this)};return d},easing:{linear:function(a){return a},swing:function(a){return-Math.cos(a*Math.PI)/2+.5}},timers:[],fx:function(a,b,c){this.options=b,this.elem=a,this.prop=c,b.orig=b.orig||{}}}),f.fx.prototype={update:function(){this.options.step&&this.options.step.call(this.elem,this.now,this),(f.fx.step[this.prop]||f.fx.step._default)(this)},cur:function(){if(this.elem[this.prop]!=null&&(!this.elem.style||this.elem.style[this.prop]==null))return this.elem[this.prop];var a,b=f.css(this.elem,this.prop);return isNaN(a=parseFloat(b))?!b||b==="auto"?0:b:a},custom:function(a,c,d){function h(a){return e.step(a)}var e=this,g=f.fx;this.startTime=cq||cr(),this.end=c,this.now=this.start=a,this.pos=this.state=0,this.unit=d||this.unit||(f.cssNumber[this.prop]?"":"px"),h.queue=this.options.queue,h.elem=this.elem,h.saveState=function(){f._data(e.elem,"fxshow"+e.prop)===b&&(e.options.hide?f._data(e.elem,"fxshow"+e.prop,e.start):e.options.show&&f._data(e.elem,"fxshow"+e.prop,e.end))},h()&&f.timers.push(h)&&!co&&(co=setInterval(g.tick,g.interval))},show:function(){var a=f._data(this.elem,"fxshow"+this.prop);this.options.orig[this.prop]=a||f.style(this.elem,this.prop),this.options.show=!0,a!==b?this.custom(this.cur(),a):this.custom(this.prop==="width"||this.prop==="height"?1:0,this.cur()),f(this.elem).show()},hide:function(){this.options.orig[this.prop]=f._data(this.elem,"fxshow"+this.prop)||f.style(this.elem,this.prop),this.options.hide=!0,this.custom(this.cur(),0)},step:function(a){var b,c,d,e=cq||cr(),g=!0,h=this.elem,i=this.options;if(a||e>=i.duration+this.startTime){this.now=this.end,this.pos=this.state=1,this.update(),i.animatedProperties[this.prop]=!0;for(b in i.animatedProperties)i.animatedProperties[b]!==!0&&(g=!1);if(g){i.overflow!=null&&!f.support.shrinkWrapBlocks&&f.each(["","X","Y"],function(a,b){h.style["overflow"+b]=i.overflow[a]}),i.hide&&f(h).hide();if(i.hide||i.show)for(b in i.animatedProperties)f.style(h,b,i.orig[b]),f.removeData(h,"fxshow"+b,!0),f.removeData(h,"toggle"+b,!0);d=i.complete,d&&(i.complete=!1,d.call(h))}return!1}i.duration==Infinity?this.now=e:(c=e-this.startTime,this.state=c/i.duration,this.pos=f.easing[i.animatedProperties[this.prop]](this.state,c,0,1,i.duration),this.now=this.start+(this.end-this.start)*this.pos),this.update();return!0}},f.extend(f.fx,{tick:function(){var a,b=f.timers,c=0;for(;c<b.length;c++)a=b[c],!a()&&b[c]===a&&b.splice(c--,1);b.length||f.fx.stop()},interval:13,stop:function(){clearInterval(co),co=null},speeds:{slow:600,fast:200,_default:400},step:{opacity:function(a){f.style(a.elem,"opacity",a.now)},_default:function(a){a.elem.style&&a.elem.style[a.prop]!=null?a.elem.style[a.prop]=a.now+a.unit:a.elem[a.prop]=a.now}}}),f.each(cp.concat.apply([],cp),function(a,b){b.indexOf("margin")&&(f.fx.step[b]=function(a){f.style(a.elem,b,Math.max(0,a.now)+a.unit)})}),f.expr&&f.expr.filters&&(f.expr.filters.animated=function(a){return f.grep(f.timers,function(b){return a===b.elem}).length});var cv,cw=/^t(?:able|d|h)$/i,cx=/^(?:body|html)$/i;"getBoundingClientRect"in c.documentElement?cv=function(a,b,c,d){try{d=a.getBoundingClientRect()}catch(e){}if(!d||!f.contains(c,a))return d?{top:d.top,left:d.left}:{top:0,left:0};var g=b.body,h=cy(b),i=c.clientTop||g.clientTop||0,j=c.clientLeft||g.clientLeft||0,k=h.pageYOffset||f.support.boxModel&&c.scrollTop||g.scrollTop,l=h.pageXOffset||f.support.boxModel&&c.scrollLeft||g.scrollLeft,m=d.top+k-i,n=d.left+l-j;return{top:m,left:n}}:cv=function(a,b,c){var d,e=a.offsetParent,g=a,h=b.body,i=b.defaultView,j=i?i.getComputedStyle(a,null):a.currentStyle,k=a.offsetTop,l=a.offsetLeft;while((a=a.parentNode)&&a!==h&&a!==c){if(f.support.fixedPosition&&j.position==="fixed")break;d=i?i.getComputedStyle(a,null):a.currentStyle,k-=a.scrollTop,l-=a.scrollLeft,a===e&&(k+=a.offsetTop,l+=a.offsetLeft,f.support.doesNotAddBorder&&(!f.support.doesAddBorderForTableAndCells||!cw.test(a.nodeName))&&(k+=parseFloat(d.borderTopWidth)||0,l+=parseFloat(d.borderLeftWidth)||0),g=e,e=a.offsetParent),f.support.subtractsBorderForOverflowNotVisible&&d.overflow!=="visible"&&(k+=parseFloat(d.borderTopWidth)||0,l+=parseFloat(d.borderLeftWidth)||0),j=d}if(j.position==="relative"||j.position==="static")k+=h.offsetTop,l+=h.offsetLeft;f.support.fixedPosition&&j.position==="fixed"&&(k+=Math.max(c.scrollTop,h.scrollTop),l+=Math.max(c.scrollLeft,h.scrollLeft));return{top:k,left:l}},f.fn.offset=function(a){if(arguments.length)return a===b?this:this.each(function(b){f.offset.setOffset(this,a,b)});var c=this[0],d=c&&c.ownerDocument;if(!d)return null;if(c===d.body)return f.offset.bodyOffset(c);return cv(c,d,d.documentElement)},f.offset={bodyOffset:function(a){var b=a.offsetTop,c=a.offsetLeft;f.support.doesNotIncludeMarginInBodyOffset&&(b+=parseFloat(f.css(a,"marginTop"))||0,c+=parseFloat(f.css(a,"marginLeft"))||0);return{top:b,left:c}},setOffset:function(a,b,c){var d=f.css(a,"position");d==="static"&&(a.style.position="relative");var e=f(a),g=e.offset(),h=f.css(a,"top"),i=f.css(a,"left"),j=(d==="absolute"||d==="fixed")&&f.inArray("auto",[h,i])>-1,k={},l={},m,n;j?(l=e.position(),m=l.top,n=l.left):(m=parseFloat(h)||0,n=parseFloat(i)||0),f.isFunction(b)&&(b=b.call(a,c,g)),b.top!=null&&(k.top=b.top-g.top+m),b.left!=null&&(k.left=b.left-g.left+n),"using"in b?b.using.call(a,k):e.css(k)}},f.fn.extend({position:function(){if(!this[0])return null;var a=this[0],b=this.offsetParent(),c=this.offset(),d=cx.test(b[0].nodeName)?{top:0,left:0}:b.offset();c.top-=parseFloat(f.css(a,"marginTop"))||0,c.left-=parseFloat(f.css(a,"marginLeft"))||0,d.top+=parseFloat(f.css(b[0],"borderTopWidth"))||0,d.left+=parseFloat(f.css(b[0],"borderLeftWidth"))||0;return{top:c.top-d.top,left:c.left-d.left}},offsetParent:function(){return this.map(function(){var a=this.offsetParent||c.body;while(a&&!cx.test(a.nodeName)&&f.css(a,"position")==="static")a=a.offsetParent;return a})}}),f.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(a,c){var d=/Y/.test(c);f.fn[a]=function(e){return f.access(this,function(a,e,g){var h=cy(a);if(g===b)return h?c in h?h[c]:f.support.boxModel&&h.document.documentElement[e]||h.document.body[e]:a[e];h?h.scrollTo(d?f(h).scrollLeft():g,d?g:f(h).scrollTop()):a[e]=g},a,e,arguments.length,null)}}),f.each({Height:"height",Width:"width"},function(a,c){var d="client"+a,e="scroll"+a,g="offset"+a;f.fn["inner"+a]=function(){var a=this[0];return a?a.style?parseFloat(f.css(a,c,"padding")):this[c]():null},f.fn["outer"+a]=function(a){var b=this[0];return b?b.style?parseFloat(f.css(b,c,a?"margin":"border")):this[c]():null},f.fn[c]=function(a){return f.access(this,function(a,c,h){var i,j,k,l;if(f.isWindow(a)){i=a.document,j=i.documentElement[d];return f.support.boxModel&&j||i.body&&i.body[d]||j}if(a.nodeType===9){i=a.documentElement;if(i[d]>=i[e])return i[d];return Math.max(a.body[e],i[e],a.body[g],i[g])}if(h===b){k=f.css(a,c),l=parseFloat(k);return f.isNumeric(l)?l:k}f(a).css(c,h)},c,a,arguments.length,null)}}),a.jQuery=a.$=f,typeof define=="function"&&define.amd&&define.amd.jQuery&&define("jquery",[],function(){return f})})(window);
/*!
 * jQuery UI 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI
 */(function(a,b){function d(b){return!a(b).parents().andSelf().filter(function(){return a.curCSS(this,"visibility")==="hidden"||a.expr.filters.hidden(this)}).length}function c(b,c){var e=b.nodeName.toLowerCase();if("area"===e){var f=b.parentNode,g=f.name,h;if(!b.href||!g||f.nodeName.toLowerCase()!=="map")return!1;h=a("img[usemap=#"+g+"]")[0];return!!h&&d(h)}return(/input|select|textarea|button|object/.test(e)?!b.disabled:"a"==e?b.href||c:c)&&d(b)}a.ui=a.ui||{};a.ui.version||(a.extend(a.ui,{version:"1.8.18",keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91}}),a.fn.extend({propAttr:a.fn.prop||a.fn.attr,_focus:a.fn.focus,focus:function(b,c){return typeof b=="number"?this.each(function(){var d=this;setTimeout(function(){a(d).focus(),c&&c.call(d)},b)}):this._focus.apply(this,arguments)},scrollParent:function(){var b;a.browser.msie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?b=this.parents().filter(function(){return/(relative|absolute|fixed)/.test(a.curCSS(this,"position",1))&&/(auto|scroll)/.test(a.curCSS(this,"overflow",1)+a.curCSS(this,"overflow-y",1)+a.curCSS(this,"overflow-x",1))}).eq(0):b=this.parents().filter(function(){return/(auto|scroll)/.test(a.curCSS(this,"overflow",1)+a.curCSS(this,"overflow-y",1)+a.curCSS(this,"overflow-x",1))}).eq(0);return/fixed/.test(this.css("position"))||!b.length?a(document):b},zIndex:function(c){if(c!==b)return this.css("zIndex",c);if(this.length){var d=a(this[0]),e,f;while(d.length&&d[0]!==document){e=d.css("position");if(e==="absolute"||e==="relative"||e==="fixed"){f=parseInt(d.css("zIndex"),10);if(!isNaN(f)&&f!==0)return f}d=d.parent()}}return 0},disableSelection:function(){return this.bind((a.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(a){a.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),a.each(["Width","Height"],function(c,d){function h(b,c,d,f){a.each(e,function(){c-=parseFloat(a.curCSS(b,"padding"+this,!0))||0,d&&(c-=parseFloat(a.curCSS(b,"border"+this+"Width",!0))||0),f&&(c-=parseFloat(a.curCSS(b,"margin"+this,!0))||0)});return c}var e=d==="Width"?["Left","Right"]:["Top","Bottom"],f=d.toLowerCase(),g={innerWidth:a.fn.innerWidth,innerHeight:a.fn.innerHeight,outerWidth:a.fn.outerWidth,outerHeight:a.fn.outerHeight};a.fn["inner"+d]=function(c){if(c===b)return g["inner"+d].call(this);return this.each(function(){a(this).css(f,h(this,c)+"px")})},a.fn["outer"+d]=function(b,c){if(typeof b!="number")return g["outer"+d].call(this,b);return this.each(function(){a(this).css(f,h(this,b,!0,c)+"px")})}}),a.extend(a.expr[":"],{data:function(b,c,d){return!!a.data(b,d[3])},focusable:function(b){return c(b,!isNaN(a.attr(b,"tabindex")))},tabbable:function(b){var d=a.attr(b,"tabindex"),e=isNaN(d);return(e||d>=0)&&c(b,!e)}}),a(function(){var b=document.body,c=b.appendChild(c=document.createElement("div"));c.offsetHeight,a.extend(c.style,{minHeight:"100px",height:"auto",padding:0,borderWidth:0}),a.support.minHeight=c.offsetHeight===100,a.support.selectstart="onselectstart"in c,b.removeChild(c).style.display="none"}),a.extend(a.ui,{plugin:{add:function(b,c,d){var e=a.ui[b].prototype;for(var f in d)e.plugins[f]=e.plugins[f]||[],e.plugins[f].push([c,d[f]])},call:function(a,b,c){var d=a.plugins[b];if(!!d&&!!a.element[0].parentNode)for(var e=0;e<d.length;e++)a.options[d[e][0]]&&d[e][1].apply(a.element,c)}},contains:function(a,b){return document.compareDocumentPosition?a.compareDocumentPosition(b)&16:a!==b&&a.contains(b)},hasScroll:function(b,c){if(a(b).css("overflow")==="hidden")return!1;var d=c&&c==="left"?"scrollLeft":"scrollTop",e=!1;if(b[d]>0)return!0;b[d]=1,e=b[d]>0,b[d]=0;return e},isOverAxis:function(a,b,c){return a>b&&a<b+c},isOver:function(b,c,d,e,f,g){return a.ui.isOverAxis(b,d,f)&&a.ui.isOverAxis(c,e,g)}}))})(jQuery);/*!
 * jQuery UI Widget 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Widget
 */(function(a,b){if(a.cleanData){var c=a.cleanData;a.cleanData=function(b){for(var d=0,e;(e=b[d])!=null;d++)try{a(e).triggerHandler("remove")}catch(f){}c(b)}}else{var d=a.fn.remove;a.fn.remove=function(b,c){return this.each(function(){c||(!b||a.filter(b,[this]).length)&&a("*",this).add([this]).each(function(){try{a(this).triggerHandler("remove")}catch(b){}});return d.call(a(this),b,c)})}}a.widget=function(b,c,d){var e=b.split(".")[0],f;b=b.split(".")[1],f=e+"-"+b,d||(d=c,c=a.Widget),a.expr[":"][f]=function(c){return!!a.data(c,b)},a[e]=a[e]||{},a[e][b]=function(a,b){arguments.length&&this._createWidget(a,b)};var g=new c;g.options=a.extend(!0,{},g.options),a[e][b].prototype=a.extend(!0,g,{namespace:e,widgetName:b,widgetEventPrefix:a[e][b].prototype.widgetEventPrefix||b,widgetBaseClass:f},d),a.widget.bridge(b,a[e][b])},a.widget.bridge=function(c,d){a.fn[c]=function(e){var f=typeof e=="string",g=Array.prototype.slice.call(arguments,1),h=this;e=!f&&g.length?a.extend.apply(null,[!0,e].concat(g)):e;if(f&&e.charAt(0)==="_")return h;f?this.each(function(){var d=a.data(this,c),f=d&&a.isFunction(d[e])?d[e].apply(d,g):d;if(f!==d&&f!==b){h=f;return!1}}):this.each(function(){var b=a.data(this,c);b?b.option(e||{})._init():a.data(this,c,new d(e,this))});return h}},a.Widget=function(a,b){arguments.length&&this._createWidget(a,b)},a.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",options:{disabled:!1},_createWidget:function(b,c){a.data(c,this.widgetName,this),this.element=a(c),this.options=a.extend(!0,{},this.options,this._getCreateOptions(),b);var d=this;this.element.bind("remove."+this.widgetName,function(){d.destroy()}),this._create(),this._trigger("create"),this._init()},_getCreateOptions:function(){return a.metadata&&a.metadata.get(this.element[0])[this.widgetName]},_create:function(){},_init:function(){},destroy:function(){this.element.unbind("."+this.widgetName).removeData(this.widgetName),this.widget().unbind("."+this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass+"-disabled "+"ui-state-disabled")},widget:function(){return this.element},option:function(c,d){var e=c;if(arguments.length===0)return a.extend({},this.options);if(typeof c=="string"){if(d===b)return this.options[c];e={},e[c]=d}this._setOptions(e);return this},_setOptions:function(b){var c=this;a.each(b,function(a,b){c._setOption(a,b)});return this},_setOption:function(a,b){this.options[a]=b,a==="disabled"&&this.widget()[b?"addClass":"removeClass"](this.widgetBaseClass+"-disabled"+" "+"ui-state-disabled").attr("aria-disabled",b);return this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_trigger:function(b,c,d){var e,f,g=this.options[b];d=d||{},c=a.Event(c),c.type=(b===this.widgetEventPrefix?b:this.widgetEventPrefix+b).toLowerCase(),c.target=this.element[0],f=c.originalEvent;if(f)for(e in f)e in c||(c[e]=f[e]);this.element.trigger(c,d);return!(a.isFunction(g)&&g.call(this.element[0],c,d)===!1||c.isDefaultPrevented())}}})(jQuery);/*!
 * jQuery UI Mouse 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Mouse
 *
 * Depends:
 *	jquery.ui.widget.js
 */(function(a,b){var c=!1;a(document).mouseup(function(a){c=!1}),a.widget("ui.mouse",{options:{cancel:":input,option",distance:1,delay:0},_mouseInit:function(){var b=this;this.element.bind("mousedown."+this.widgetName,function(a){return b._mouseDown(a)}).bind("click."+this.widgetName,function(c){if(!0===a.data(c.target,b.widgetName+".preventClickEvent")){a.removeData(c.target,b.widgetName+".preventClickEvent"),c.stopImmediatePropagation();return!1}}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName)},_mouseDown:function(b){if(!c){this._mouseStarted&&this._mouseUp(b),this._mouseDownEvent=b;var d=this,e=b.which==1,f=typeof this.options.cancel=="string"&&b.target.nodeName?a(b.target).closest(this.options.cancel).length:!1;if(!e||f||!this._mouseCapture(b))return!0;this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){d.mouseDelayMet=!0},this.options.delay));if(this._mouseDistanceMet(b)&&this._mouseDelayMet(b)){this._mouseStarted=this._mouseStart(b)!==!1;if(!this._mouseStarted){b.preventDefault();return!0}}!0===a.data(b.target,this.widgetName+".preventClickEvent")&&a.removeData(b.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(a){return d._mouseMove(a)},this._mouseUpDelegate=function(a){return d._mouseUp(a)},a(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),b.preventDefault(),c=!0;return!0}},_mouseMove:function(b){if(a.browser.msie&&!(document.documentMode>=9)&&!b.button)return this._mouseUp(b);if(this._mouseStarted){this._mouseDrag(b);return b.preventDefault()}this._mouseDistanceMet(b)&&this._mouseDelayMet(b)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,b)!==!1,this._mouseStarted?this._mouseDrag(b):this._mouseUp(b));return!this._mouseStarted},_mouseUp:function(b){a(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,b.target==this._mouseDownEvent.target&&a.data(b.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(b));return!1},_mouseDistanceMet:function(a){return Math.max(Math.abs(this._mouseDownEvent.pageX-a.pageX),Math.abs(this._mouseDownEvent.pageY-a.pageY))>=this.options.distance},_mouseDelayMet:function(a){return this.mouseDelayMet},_mouseStart:function(a){},_mouseDrag:function(a){},_mouseStop:function(a){},_mouseCapture:function(a){return!0}})})(jQuery);/*
 * jQuery UI Position 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Position
 */(function(a,b){a.ui=a.ui||{};var c=/left|center|right/,d=/top|center|bottom/,e="center",f={},g=a.fn.position,h=a.fn.offset;a.fn.position=function(b){if(!b||!b.of)return g.apply(this,arguments);b=a.extend({},b);var h=a(b.of),i=h[0],j=(b.collision||"flip").split(" "),k=b.offset?b.offset.split(" "):[0,0],l,m,n;i.nodeType===9?(l=h.width(),m=h.height(),n={top:0,left:0}):i.setTimeout?(l=h.width(),m=h.height(),n={top:h.scrollTop(),left:h.scrollLeft()}):i.preventDefault?(b.at="left top",l=m=0,n={top:b.of.pageY,left:b.of.pageX}):(l=h.outerWidth(),m=h.outerHeight(),n=h.offset()),a.each(["my","at"],function(){var a=(b[this]||"").split(" ");a.length===1&&(a=c.test(a[0])?a.concat([e]):d.test(a[0])?[e].concat(a):[e,e]),a[0]=c.test(a[0])?a[0]:e,a[1]=d.test(a[1])?a[1]:e,b[this]=a}),j.length===1&&(j[1]=j[0]),k[0]=parseInt(k[0],10)||0,k.length===1&&(k[1]=k[0]),k[1]=parseInt(k[1],10)||0,b.at[0]==="right"?n.left+=l:b.at[0]===e&&(n.left+=l/2),b.at[1]==="bottom"?n.top+=m:b.at[1]===e&&(n.top+=m/2),n.left+=k[0],n.top+=k[1];return this.each(function(){var c=a(this),d=c.outerWidth(),g=c.outerHeight(),h=parseInt(a.curCSS(this,"marginLeft",!0))||0,i=parseInt(a.curCSS(this,"marginTop",!0))||0,o=d+h+(parseInt(a.curCSS(this,"marginRight",!0))||0),p=g+i+(parseInt(a.curCSS(this,"marginBottom",!0))||0),q=a.extend({},n),r;b.my[0]==="right"?q.left-=d:b.my[0]===e&&(q.left-=d/2),b.my[1]==="bottom"?q.top-=g:b.my[1]===e&&(q.top-=g/2),f.fractions||(q.left=Math.round(q.left),q.top=Math.round(q.top)),r={left:q.left-h,top:q.top-i},a.each(["left","top"],function(c,e){a.ui.position[j[c]]&&a.ui.position[j[c]][e](q,{targetWidth:l,targetHeight:m,elemWidth:d,elemHeight:g,collisionPosition:r,collisionWidth:o,collisionHeight:p,offset:k,my:b.my,at:b.at})}),a.fn.bgiframe&&c.bgiframe(),c.offset(a.extend(q,{using:b.using}))})},a.ui.position={fit:{left:function(b,c){var d=a(window),e=c.collisionPosition.left+c.collisionWidth-d.width()-d.scrollLeft();b.left=e>0?b.left-e:Math.max(b.left-c.collisionPosition.left,b.left)},top:function(b,c){var d=a(window),e=c.collisionPosition.top+c.collisionHeight-d.height()-d.scrollTop();b.top=e>0?b.top-e:Math.max(b.top-c.collisionPosition.top,b.top)}},flip:{left:function(b,c){if(c.at[0]!==e){var d=a(window),f=c.collisionPosition.left+c.collisionWidth-d.width()-d.scrollLeft(),g=c.my[0]==="left"?-c.elemWidth:c.my[0]==="right"?c.elemWidth:0,h=c.at[0]==="left"?c.targetWidth:-c.targetWidth,i=-2*c.offset[0];b.left+=c.collisionPosition.left<0?g+h+i:f>0?g+h+i:0}},top:function(b,c){if(c.at[1]!==e){var d=a(window),f=c.collisionPosition.top+c.collisionHeight-d.height()-d.scrollTop(),g=c.my[1]==="top"?-c.elemHeight:c.my[1]==="bottom"?c.elemHeight:0,h=c.at[1]==="top"?c.targetHeight:-c.targetHeight,i=-2*c.offset[1];b.top+=c.collisionPosition.top<0?g+h+i:f>0?g+h+i:0}}}},a.offset.setOffset||(a.offset.setOffset=function(b,c){/static/.test(a.curCSS(b,"position"))&&(b.style.position="relative");var d=a(b),e=d.offset(),f=parseInt(a.curCSS(b,"top",!0),10)||0,g=parseInt(a.curCSS(b,"left",!0),10)||0,h={top:c.top-e.top+f,left:c.left-e.left+g};"using"in c?c.using.call(b,h):d.css(h)},a.fn.offset=function(b){var c=this[0];if(!c||!c.ownerDocument)return null;if(b)return this.each(function(){a.offset.setOffset(this,b)});return h.call(this)}),function(){var b=document.getElementsByTagName("body")[0],c=document.createElement("div"),d,e,g,h,i;d=document.createElement(b?"div":"body"),g={visibility:"hidden",width:0,height:0,border:0,margin:0,background:"none"},b&&a.extend(g,{position:"absolute",left:"-1000px",top:"-1000px"});for(var j in g)d.style[j]=g[j];d.appendChild(c),e=b||document.documentElement,e.insertBefore(d,e.firstChild),c.style.cssText="position: absolute; left: 10.7432222px; top: 10.432325px; height: 30px; width: 201px;",h=a(c).offset(function(a,b){return b}).offset(),d.innerHTML="",e.removeChild(d),i=h.top+h.left+(b?2e3:0),f.fractions=i>21&&i<22}()})(jQuery);/*
 * jQuery UI Autocomplete 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Autocomplete
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 *	jquery.ui.position.js
 */(function(a,b){var c=0;a.widget("ui.autocomplete",{options:{appendTo:"body",autoFocus:!1,delay:300,minLength:1,position:{my:"left top",at:"left bottom",collision:"none"},source:null},pending:0,_create:function(){var b=this,c=this.element[0].ownerDocument,d;this.element.addClass("ui-autocomplete-input").attr("autocomplete","off").attr({role:"textbox","aria-autocomplete":"list","aria-haspopup":"true"}).bind("keydown.autocomplete",function(c){if(!b.options.disabled&&!b.element.propAttr("readOnly")){d=!1;var e=a.ui.keyCode;switch(c.keyCode){case e.PAGE_UP:b._move("previousPage",c);break;case e.PAGE_DOWN:b._move("nextPage",c);break;case e.UP:b._move("previous",c),c.preventDefault();break;case e.DOWN:b._move("next",c),c.preventDefault();break;case e.ENTER:case e.NUMPAD_ENTER:b.menu.active&&(d=!0,c.preventDefault());case e.TAB:if(!b.menu.active)return;b.menu.select(c);break;case e.ESCAPE:b.element.val(b.term),b.close(c);break;default:clearTimeout(b.searching),b.searching=setTimeout(function(){b.term!=b.element.val()&&(b.selectedItem=null,b.search(null,c))},b.options.delay)}}}).bind("keypress.autocomplete",function(a){d&&(d=!1,a.preventDefault())}).bind("focus.autocomplete",function(){b.options.disabled||(b.selectedItem=null,b.previous=b.element.val())}).bind("blur.autocomplete",function(a){b.options.disabled||(clearTimeout(b.searching),b.closing=setTimeout(function(){b.close(a),b._change(a)},150))}),this._initSource(),this.response=function(){return b._response.apply(b,arguments)},this.menu=a("<ul></ul>").addClass("ui-autocomplete").appendTo(a(this.options.appendTo||"body",c)[0]).mousedown(function(c){var d=b.menu.element[0];a(c.target).closest(".ui-menu-item").length||setTimeout(function(){a(document).one("mousedown",function(c){c.target!==b.element[0]&&c.target!==d&&!a.ui.contains(d,c.target)&&b.close()})},1),setTimeout(function(){clearTimeout(b.closing)},13)}).menu({focus:function(a,c){var d=c.item.data("item.autocomplete");!1!==b._trigger("focus",a,{item:d})&&/^key/.test(a.originalEvent.type)&&b.element.val(d.value)},selected:function(a,d){var e=d.item.data("item.autocomplete"),f=b.previous;b.element[0]!==c.activeElement&&(b.element.focus(),b.previous=f,setTimeout(function(){b.previous=f,b.selectedItem=e},1)),!1!==b._trigger("select",a,{item:e})&&b.element.val(e.value),b.term=b.element.val(),b.close(a),b.selectedItem=e},blur:function(a,c){b.menu.element.is(":visible")&&b.element.val()!==b.term&&b.element.val(b.term)}}).zIndex(this.element.zIndex()+1).css({top:0,left:0}).hide().data("menu"),a.fn.bgiframe&&this.menu.element.bgiframe(),b.beforeunloadHandler=function(){b.element.removeAttr("autocomplete")},a(window).bind("beforeunload",b.beforeunloadHandler)},destroy:function(){this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete").removeAttr("role").removeAttr("aria-autocomplete").removeAttr("aria-haspopup"),this.menu.element.remove(),a(window).unbind("beforeunload",this.beforeunloadHandler),a.Widget.prototype.destroy.call(this)},_setOption:function(b,c){a.Widget.prototype._setOption.apply(this,arguments),b==="source"&&this._initSource(),b==="appendTo"&&this.menu.element.appendTo(a(c||"body",this.element[0].ownerDocument)[0]),b==="disabled"&&c&&this.xhr&&this.xhr.abort()},_initSource:function(){var b=this,d,e;a.isArray(this.options.source)?(d=this.options.source,this.source=function(b,c){c(a.ui.autocomplete.filter(d,b.term))}):typeof this.options.source=="string"?(e=this.options.source,this.source=function(d,f){b.xhr&&b.xhr.abort(),b.xhr=a.ajax({url:e,data:d,dataType:"json",context:{autocompleteRequest:++c},success:function(a,b){this.autocompleteRequest===c&&f(a)},error:function(){this.autocompleteRequest===c&&f([])}})}):this.source=this.options.source},search:function(a,b){a=a!=null?a:this.element.val(),this.term=this.element.val();if(a.length<this.options.minLength)return this.close(b);clearTimeout(this.closing);if(this._trigger("search",b)!==!1)return this._search(a)},_search:function(a){this.pending++,this.element.addClass("ui-autocomplete-loading"),this.source({term:a},this.response)},_response:function(a){!this.options.disabled&&a&&a.length?(a=this._normalize(a),this._suggest(a),this._trigger("open")):this.close(),this.pending--,this.pending||this.element.removeClass("ui-autocomplete-loading")},close:function(a){clearTimeout(this.closing),this.menu.element.is(":visible")&&(this.menu.element.hide(),this.menu.deactivate(),this._trigger("close",a))},_change:function(a){this.previous!==this.element.val()&&this._trigger("change",a,{item:this.selectedItem})},_normalize:function(b){if(b.length&&b[0].label&&b[0].value)return b;return a.map(b,function(b){if(typeof b=="string")return{label:b,value:b};return a.extend({label:b.label||b.value,value:b.value||b.label},b)})},_suggest:function(b){var c=this.menu.element.empty().zIndex(this.element.zIndex()+1);this._renderMenu(c,b),this.menu.deactivate(),this.menu.refresh(),c.show(),this._resizeMenu(),c.position(a.extend({of:this.element},this.options.position)),this.options.autoFocus&&this.menu.next(new a.Event("mouseover"))},_resizeMenu:function(){var a=this.menu.element;a.outerWidth(Math.max(a.width("").outerWidth()+1,this.element.outerWidth()))},_renderMenu:function(b,c){var d=this;a.each(c,function(a,c){d._renderItem(b,c)})},_renderItem:function(b,c){return a("<li></li>").data("item.autocomplete",c).append(a("<a></a>").text(c.label)).appendTo(b)},_move:function(a,b){if(!this.menu.element.is(":visible"))this.search(null,b);else{if(this.menu.first()&&/^previous/.test(a)||this.menu.last()&&/^next/.test(a)){this.element.val(this.term),this.menu.deactivate();return}this.menu[a](b)}},widget:function(){return this.menu.element}}),a.extend(a.ui.autocomplete,{escapeRegex:function(a){return a.replace(/[-[\]{}()*+?.,\\^$|#\s]/g,"\\$&")},filter:function(b,c){var d=new RegExp(a.ui.autocomplete.escapeRegex(c),"i");return a.grep(b,function(a){return d.test(a.label||a.value||a)})}})})(jQuery),function(a){a.widget("ui.menu",{_create:function(){var b=this;this.element.addClass("ui-menu ui-widget ui-widget-content ui-corner-all").attr({role:"listbox","aria-activedescendant":"ui-active-menuitem"}).click(function(c){!a(c.target).closest(".ui-menu-item a").length||(c.preventDefault(),b.select(c))}),this.refresh()},refresh:function(){var b=this,c=this.element.children("li:not(.ui-menu-item):has(a)").addClass("ui-menu-item").attr("role","menuitem");c.children("a").addClass("ui-corner-all").attr("tabindex",-1).mouseenter(function(c){b.activate(c,a(this).parent())}).mouseleave(function(){b.deactivate()})},activate:function(a,b){this.deactivate();if(this.hasScroll()){var c=b.offset().top-this.element.offset().top,d=this.element.scrollTop(),e=this.element.height();c<0?this.element.scrollTop(d+c):c>=e&&this.element.scrollTop(d+c-e+b.height())}this.active=b.eq(0).children("a").addClass("ui-state-hover").attr("id","ui-active-menuitem").end(),this._trigger("focus",a,{item:b})},deactivate:function(){!this.active||(this.active.children("a").removeClass("ui-state-hover").removeAttr("id"),this._trigger("blur"),this.active=null)},next:function(a){this.move("next",".ui-menu-item:first",a)},previous:function(a){this.move("prev",".ui-menu-item:last",a)},first:function(){return this.active&&!this.active.prevAll(".ui-menu-item").length},last:function(){return this.active&&!this.active.nextAll(".ui-menu-item").length},move:function(a,b,c){if(!this.active)this.activate(c,this.element.children(b));else{var d=this.active[a+"All"](".ui-menu-item").eq(0);d.length?this.activate(c,d):this.activate(c,this.element.children(b))}},nextPage:function(b){if(this.hasScroll()){if(!this.active||this.last()){this.activate(b,this.element.children(".ui-menu-item:first"));return}var c=this.active.offset().top,d=this.element.height(),e=this.element.children(".ui-menu-item").filter(function(){var b=a(this).offset().top-c-d+a(this).height();return b<10&&b>-10});e.length||(e=this.element.children(".ui-menu-item:last")),this.activate(b,e)}else this.activate(b,this.element.children(".ui-menu-item").filter(!this.active||this.last()?":first":":last"))},previousPage:function(b){if(this.hasScroll()){if(!this.active||this.first()){this.activate(b,this.element.children(".ui-menu-item:last"));return}var c=this.active.offset().top,d=this.element.height();result=this.element.children(".ui-menu-item").filter(function(){var b=a(this).offset().top-c+d-a(this).height();return b<10&&b>-10}),result.length||(result=this.element.children(".ui-menu-item:first")),this.activate(b,result)}else this.activate(b,this.element.children(".ui-menu-item").filter(!this.active||this.first()?":last":":first"))},hasScroll:function(){return this.element.height()<this.element[a.fn.prop?"prop":"attr"]("scrollHeight")},select:function(a){this._trigger("selected",a,{item:this.active})}})}(jQuery);/*
 * jQuery UI Datepicker 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Datepicker
 *
 * Depends:
 *	jquery.ui.core.js
 */(function($,undefined){function isArray(a){return a&&($.browser.safari&&typeof a=="object"&&a.length||a.constructor&&a.constructor.toString().match(/\Array\(\)/))}function extendRemove(a,b){$.extend(a,b);for(var c in b)if(b[c]==null||b[c]==undefined)a[c]=b[c];return a}function bindHover(a){var b="button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";return a.bind("mouseout",function(a){var c=$(a.target).closest(b);!c.length||c.removeClass("ui-state-hover ui-datepicker-prev-hover ui-datepicker-next-hover")}).bind("mouseover",function(c){var d=$(c.target).closest(b);!$.datepicker._isDisabledDatepicker(instActive.inline?a.parent()[0]:instActive.input[0])&&!!d.length&&(d.parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover"),d.addClass("ui-state-hover"),d.hasClass("ui-datepicker-prev")&&d.addClass("ui-datepicker-prev-hover"),d.hasClass("ui-datepicker-next")&&d.addClass("ui-datepicker-next-hover"))})}function Datepicker(){this.debug=!1,this._curInst=null,this._keyEvent=!1,this._disabledInputs=[],this._datepickerShowing=!1,this._inDialog=!1,this._mainDivId="ui-datepicker-div",this._inlineClass="ui-datepicker-inline",this._appendClass="ui-datepicker-append",this._triggerClass="ui-datepicker-trigger",this._dialogClass="ui-datepicker-dialog",this._disableClass="ui-datepicker-disabled",this._unselectableClass="ui-datepicker-unselectable",this._currentClass="ui-datepicker-current-day",this._dayOverClass="ui-datepicker-days-cell-over",this.regional=[],this.regional[""]={closeText:"Done",prevText:"Prev",nextText:"Next",currentText:"Today",monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"],monthNamesShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],dayNamesShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],dayNamesMin:["Su","Mo","Tu","We","Th","Fr","Sa"],weekHeader:"Wk",dateFormat:"mm/dd/yy",firstDay:0,isRTL:!1,showMonthAfterYear:!1,yearSuffix:""},this._defaults={showOn:"focus",showAnim:"fadeIn",showOptions:{},defaultDate:null,appendText:"",buttonText:"...",buttonImage:"",buttonImageOnly:!1,hideIfNoPrevNext:!1,navigationAsDateFormat:!1,gotoCurrent:!1,changeMonth:!1,changeYear:!1,yearRange:"c-10:c+10",showOtherMonths:!1,selectOtherMonths:!1,showWeek:!1,calculateWeek:this.iso8601Week,shortYearCutoff:"+10",minDate:null,maxDate:null,duration:"fast",beforeShowDay:null,beforeShow:null,onSelect:null,onChangeMonthYear:null,onClose:null,numberOfMonths:1,showCurrentAtPos:0,stepMonths:1,stepBigMonths:12,altField:"",altFormat:"",constrainInput:!0,showButtonPanel:!1,autoSize:!1,disabled:!1},$.extend(this._defaults,this.regional[""]),this.dpDiv=bindHover($('<div id="'+this._mainDivId+'" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>'))}$.extend($.ui,{datepicker:{version:"1.8.18"}});var PROP_NAME="datepicker",dpuuid=(new Date).getTime(),instActive;$.extend(Datepicker.prototype,{markerClassName:"hasDatepicker",maxRows:4,log:function(){this.debug&&console.log.apply("",arguments)},_widgetDatepicker:function(){return this.dpDiv},setDefaults:function(a){extendRemove(this._defaults,a||{});return this},_attachDatepicker:function(target,settings){var inlineSettings=null;for(var attrName in this._defaults){var attrValue=target.getAttribute("date:"+attrName);if(attrValue){inlineSettings=inlineSettings||{};try{inlineSettings[attrName]=eval(attrValue)}catch(err){inlineSettings[attrName]=attrValue}}}var nodeName=target.nodeName.toLowerCase(),inline=nodeName=="div"||nodeName=="span";target.id||(this.uuid+=1,target.id="dp"+this.uuid);var inst=this._newInst($(target),inline);inst.settings=$.extend({},settings||{},inlineSettings||{}),nodeName=="input"?this._connectDatepicker(target,inst):inline&&this._inlineDatepicker(target,inst)},_newInst:function(a,b){var c=a[0].id.replace(/([^A-Za-z0-9_-])/g,"\\\\$1");return{id:c,input:a,selectedDay:0,selectedMonth:0,selectedYear:0,drawMonth:0,drawYear:0,inline:b,dpDiv:b?bindHover($('<div class="'+this._inlineClass+' ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>')):this.dpDiv}},_connectDatepicker:function(a,b){var c=$(a);b.append=$([]),b.trigger=$([]);c.hasClass(this.markerClassName)||(this._attachments(c,b),c.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp).bind("setData.datepicker",function(a,c,d){b.settings[c]=d}).bind("getData.datepicker",function(a,c){return this._get(b,c)}),this._autoSize(b),$.data(a,PROP_NAME,b),b.settings.disabled&&this._disableDatepicker(a))},_attachments:function(a,b){var c=this._get(b,"appendText"),d=this._get(b,"isRTL");b.append&&b.append.remove(),c&&(b.append=$('<span class="'+this._appendClass+'">'+c+"</span>"),a[d?"before":"after"](b.append)),a.unbind("focus",this._showDatepicker),b.trigger&&b.trigger.remove();var e=this._get(b,"showOn");(e=="focus"||e=="both")&&a.focus(this._showDatepicker);if(e=="button"||e=="both"){var f=this._get(b,"buttonText"),g=this._get(b,"buttonImage");b.trigger=$(this._get(b,"buttonImageOnly")?$("<img/>").addClass(this._triggerClass).attr({src:g,alt:f,title:f}):$('<button type="button"></button>').addClass(this._triggerClass).html(g==""?f:$("<img/>").attr({src:g,alt:f,title:f}))),a[d?"before":"after"](b.trigger),b.trigger.click(function(){$.datepicker._datepickerShowing&&$.datepicker._lastInput==a[0]?$.datepicker._hideDatepicker():$.datepicker._datepickerShowing&&$.datepicker._lastInput!=a[0]?($.datepicker._hideDatepicker(),$.datepicker._showDatepicker(a[0])):$.datepicker._showDatepicker(a[0]);return!1})}},_autoSize:function(a){if(this._get(a,"autoSize")&&!a.inline){var b=new Date(2009,11,20),c=this._get(a,"dateFormat");if(c.match(/[DM]/)){var d=function(a){var b=0,c=0;for(var d=0;d<a.length;d++)a[d].length>b&&(b=a[d].length,c=d);return c};b.setMonth(d(this._get(a,c.match(/MM/)?"monthNames":"monthNamesShort"))),b.setDate(d(this._get(a,c.match(/DD/)?"dayNames":"dayNamesShort"))+20-b.getDay())}a.input.attr("size",this._formatDate(a,b).length)}},_inlineDatepicker:function(a,b){var c=$(a);c.hasClass(this.markerClassName)||(c.addClass(this.markerClassName).append(b.dpDiv).bind("setData.datepicker",function(a,c,d){b.settings[c]=d}).bind("getData.datepicker",function(a,c){return this._get(b,c)}),$.data(a,PROP_NAME,b),this._setDate(b,this._getDefaultDate(b),!0),this._updateDatepicker(b),this._updateAlternate(b),b.settings.disabled&&this._disableDatepicker(a),b.dpDiv.css("display","block"))},_dialogDatepicker:function(a,b,c,d,e){var f=this._dialogInst;if(!f){this.uuid+=1;var g="dp"+this.uuid;this._dialogInput=$('<input type="text" id="'+g+'" style="position: absolute; top: -100px; width: 0px; z-index: -10;"/>'),this._dialogInput.keydown(this._doKeyDown),$("body").append(this._dialogInput),f=this._dialogInst=this._newInst(this._dialogInput,!1),f.settings={},$.data(this._dialogInput[0],PROP_NAME,f)}extendRemove(f.settings,d||{}),b=b&&b.constructor==Date?this._formatDate(f,b):b,this._dialogInput.val(b),this._pos=e?e.length?e:[e.pageX,e.pageY]:null;if(!this._pos){var h=document.documentElement.clientWidth,i=document.documentElement.clientHeight,j=document.documentElement.scrollLeft||document.body.scrollLeft,k=document.documentElement.scrollTop||document.body.scrollTop;this._pos=[h/2-100+j,i/2-150+k]}this._dialogInput.css("left",this._pos[0]+20+"px").css("top",this._pos[1]+"px"),f.settings.onSelect=c,this._inDialog=!0,this.dpDiv.addClass(this._dialogClass),this._showDatepicker(this._dialogInput[0]),$.blockUI&&$.blockUI(this.dpDiv),$.data(this._dialogInput[0],PROP_NAME,f);return this},_destroyDatepicker:function(a){var b=$(a),c=$.data(a,PROP_NAME);if(!!b.hasClass(this.markerClassName)){var d=a.nodeName.toLowerCase();$.removeData(a,PROP_NAME),d=="input"?(c.append.remove(),c.trigger.remove(),b.removeClass(this.markerClassName).unbind("focus",this._showDatepicker).unbind("keydown",this._doKeyDown).unbind("keypress",this._doKeyPress).unbind("keyup",this._doKeyUp)):(d=="div"||d=="span")&&b.removeClass(this.markerClassName).empty()}},_enableDatepicker:function(a){var b=$(a),c=$.data(a,PROP_NAME);if(!!b.hasClass(this.markerClassName)){var d=a.nodeName.toLowerCase();if(d=="input")a.disabled=!1,c.trigger.filter("button").each(function(){this.disabled=!1}).end().filter("img").css({opacity:"1.0",cursor:""});else if(d=="div"||d=="span"){var e=b.children("."+this._inlineClass);e.children().removeClass("ui-state-disabled"),e.find("select.ui-datepicker-month, select.ui-datepicker-year").removeAttr("disabled")}this._disabledInputs=$.map(this._disabledInputs,function(b){return b==a?null:b})}},_disableDatepicker:function(a){var b=$(a),c=$.data(a,PROP_NAME);if(!!b.hasClass(this.markerClassName)){var d=a.nodeName.toLowerCase();if(d=="input")a.disabled=!0,c.trigger.filter("button").each(function(){this.disabled=!0}).end().filter("img").css({opacity:"0.5",cursor:"default"});else if(d=="div"||d=="span"){var e=b.children("."+this._inlineClass);e.children().addClass("ui-state-disabled"),e.find("select.ui-datepicker-month, select.ui-datepicker-year").attr("disabled","disabled")}this._disabledInputs=$.map(this._disabledInputs,function(b){return b==a?null:b}),this._disabledInputs[this._disabledInputs.length]=a}},_isDisabledDatepicker:function(a){if(!a)return!1;for(var b=0;b<this._disabledInputs.length;b++)if(this._disabledInputs[b]==a)return!0;return!1},_getInst:function(a){try{return $.data(a,PROP_NAME)}catch(b){throw"Missing instance data for this datepicker"}},_optionDatepicker:function(a,b,c){var d=this._getInst(a);if(arguments.length==2&&typeof b=="string")return b=="defaults"?$.extend({},$.datepicker._defaults):d?b=="all"?$.extend({},d.settings):this._get(d,b):null;var e=b||{};typeof b=="string"&&(e={},e[b]=c);if(d){this._curInst==d&&this._hideDatepicker();var f=this._getDateDatepicker(a,!0),g=this._getMinMaxDate(d,"min"),h=this._getMinMaxDate(d,"max");extendRemove(d.settings,e),g!==null&&e.dateFormat!==undefined&&e.minDate===undefined&&(d.settings.minDate=this._formatDate(d,g)),h!==null&&e.dateFormat!==undefined&&e.maxDate===undefined&&(d.settings.maxDate=this._formatDate(d,h)),this._attachments($(a),d),this._autoSize(d),this._setDate(d,f),this._updateAlternate(d),this._updateDatepicker(d)}},_changeDatepicker:function(a,b,c){this._optionDatepicker(a,b,c)},_refreshDatepicker:function(a){var b=this._getInst(a);b&&this._updateDatepicker(b)},_setDateDatepicker:function(a,b){var c=this._getInst(a);c&&(this._setDate(c,b),this._updateDatepicker(c),this._updateAlternate(c))},_getDateDatepicker:function(a,b){var c=this._getInst(a);c&&!c.inline&&this._setDateFromField(c,b);return c?this._getDate(c):null},_doKeyDown:function(a){var b=$.datepicker._getInst(a.target),c=!0,d=b.dpDiv.is(".ui-datepicker-rtl");b._keyEvent=!0;if($.datepicker._datepickerShowing)switch(a.keyCode){case 9:$.datepicker._hideDatepicker(),c=!1;break;case 13:var e=$("td."+$.datepicker._dayOverClass+":not(."+$.datepicker._currentClass+")",b.dpDiv);e[0]&&$.datepicker._selectDay(a.target,b.selectedMonth,b.selectedYear,e[0]);var f=$.datepicker._get(b,"onSelect");if(f){var g=$.datepicker._formatDate(b);f.apply(b.input?b.input[0]:null,[g,b])}else $.datepicker._hideDatepicker();return!1;case 27:$.datepicker._hideDatepicker();break;case 33:$.datepicker._adjustDate(a.target,a.ctrlKey?-$.datepicker._get(b,"stepBigMonths"):-$.datepicker._get(b,"stepMonths"),"M");break;case 34:$.datepicker._adjustDate(a.target,a.ctrlKey?+$.datepicker._get(b,"stepBigMonths"):+$.datepicker._get(b,"stepMonths"),"M");break;case 35:(a.ctrlKey||a.metaKey)&&$.datepicker._clearDate(a.target),c=a.ctrlKey||a.metaKey;break;case 36:(a.ctrlKey||a.metaKey)&&$.datepicker._gotoToday(a.target),c=a.ctrlKey||a.metaKey;break;case 37:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,d?1:-1,"D"),c=a.ctrlKey||a.metaKey,a.originalEvent.altKey&&$.datepicker._adjustDate(a.target,a.ctrlKey?-$.datepicker._get(b,"stepBigMonths"):-$.datepicker._get(b,"stepMonths"),"M");break;case 38:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,-7,"D"),c=a.ctrlKey||a.metaKey;break;case 39:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,d?-1:1,"D"),c=a.ctrlKey||a.metaKey,a.originalEvent.altKey&&$.datepicker._adjustDate(a.target,a.ctrlKey?+$.datepicker._get(b,"stepBigMonths"):+$.datepicker._get(b,"stepMonths"),"M");break;case 40:(a.ctrlKey||a.metaKey)&&$.datepicker._adjustDate(a.target,7,"D"),c=a.ctrlKey||a.metaKey;break;default:c=!1}else a.keyCode==36&&a.ctrlKey?$.datepicker._showDatepicker(this):c=!1;c&&(a.preventDefault(),a.stopPropagation())},_doKeyPress:function(a){var b=$.datepicker._getInst(a.target);if($.datepicker._get(b,"constrainInput")){var c=$.datepicker._possibleChars($.datepicker._get(b,"dateFormat")),d=String.fromCharCode(a.charCode==undefined?a.keyCode:a.charCode);return a.ctrlKey||a.metaKey||d<" "||!c||c.indexOf(d)>-1}},_doKeyUp:function(a){var b=$.datepicker._getInst(a.target);if(b.input.val()!=b.lastVal)try{var c=$.datepicker.parseDate($.datepicker._get(b,"dateFormat"),b.input?b.input.val():null,$.datepicker._getFormatConfig(b));c&&($.datepicker._setDateFromField(b),$.datepicker._updateAlternate(b),$.datepicker._updateDatepicker(b))}catch(a){$.datepicker.log(a)}return!0},_showDatepicker:function(a){a=a.target||a,a.nodeName.toLowerCase()!="input"&&(a=$("input",a.parentNode)[0]);if(!$.datepicker._isDisabledDatepicker(a)&&$.datepicker._lastInput!=a){var b=$.datepicker._getInst(a);$.datepicker._curInst&&$.datepicker._curInst!=b&&($.datepicker._curInst.dpDiv.stop(!0,!0),b&&$.datepicker._datepickerShowing&&$.datepicker._hideDatepicker($.datepicker._curInst.input[0]));var c=$.datepicker._get(b,"beforeShow"),d=c?c.apply(a,[a,b]):{};if(d===!1)return;extendRemove(b.settings,d),b.lastVal=null,$.datepicker._lastInput=a,$.datepicker._setDateFromField(b),$.datepicker._inDialog&&(a.value=""),$.datepicker._pos||($.datepicker._pos=$.datepicker._findPos(a),$.datepicker._pos[1]+=a.offsetHeight);var e=!1;$(a).parents().each(function(){e|=$(this).css("position")=="fixed";return!e}),e&&$.browser.opera&&($.datepicker._pos[0]-=document.documentElement.scrollLeft,$.datepicker._pos[1]-=document.documentElement.scrollTop);var f={left:$.datepicker._pos[0],top:$.datepicker._pos[1]};$.datepicker._pos=null,b.dpDiv.empty(),b.dpDiv.css({position:"absolute",display:"block",top:"-1000px"}),$.datepicker._updateDatepicker(b),f=$.datepicker._checkOffset(b,f,e),b.dpDiv.css({position:$.datepicker._inDialog&&$.blockUI?"static":e?"fixed":"absolute",display:"none",left:f.left+"px",top:f.top+"px"});if(!b.inline){var g=$.datepicker._get(b,"showAnim"),h=$.datepicker._get(b,"duration"),i=function(){var a=b.dpDiv.find("iframe.ui-datepicker-cover");if(!!a.length){var c=$.datepicker._getBorders(b.dpDiv);a.css({left:-c[0],top:-c[1],width:b.dpDiv.outerWidth(),height:b.dpDiv.outerHeight()})}};b.dpDiv.zIndex($(a).zIndex()+1),$.datepicker._datepickerShowing=!0,$.effects&&$.effects[g]?b.dpDiv.show(g,$.datepicker._get(b,"showOptions"),h,i):b.dpDiv[g||"show"](g?h:null,i),(!g||!h)&&i(),b.input.is(":visible")&&!b.input.is(":disabled")&&b.input.focus(),$.datepicker._curInst=b}}},_updateDatepicker:function(a){var b=this;b.maxRows=4;var c=$.datepicker._getBorders(a.dpDiv);instActive=a,a.dpDiv.empty().append(this._generateHTML(a));var d=a.dpDiv.find("iframe.ui-datepicker-cover");!d.length||d.css({left:-c[0],top:-c[1],width:a.dpDiv.outerWidth(),height:a.dpDiv.outerHeight()}),a.dpDiv.find("."+this._dayOverClass+" a").mouseover();var e=this._getNumberOfMonths(a),f=e[1],g=17;a.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width(""),f>1&&a.dpDiv.addClass("ui-datepicker-multi-"+f).css("width",g*f+"em"),a.dpDiv[(e[0]!=1||e[1]!=1?"add":"remove")+"Class"]("ui-datepicker-multi"),a.dpDiv[(this._get(a,"isRTL")?"add":"remove")+"Class"]("ui-datepicker-rtl"),a==$.datepicker._curInst&&$.datepicker._datepickerShowing&&a.input&&a.input.is(":visible")&&!a.input.is(":disabled")&&a.input[0]!=document.activeElement&&a.input.focus();if(a.yearshtml){var h=a.yearshtml;setTimeout(function(){h===a.yearshtml&&a.yearshtml&&a.dpDiv.find("select.ui-datepicker-year:first").replaceWith(a.yearshtml),h=a.yearshtml=null},0)}},_getBorders:function(a){var b=function(a){return{thin:1,medium:2,thick:3}[a]||a};return[parseFloat(b(a.css("border-left-width"))),parseFloat(b(a.css("border-top-width")))]},_checkOffset:function(a,b,c){var d=a.dpDiv.outerWidth(),e=a.dpDiv.outerHeight(),f=a.input?a.input.outerWidth():0,g=a.input?a.input.outerHeight():0,h=document.documentElement.clientWidth+$(document).scrollLeft(),i=document.documentElement.clientHeight+$(document).scrollTop();b.left-=this._get(a,"isRTL")?d-f:0,b.left-=c&&b.left==a.input.offset().left?$(document).scrollLeft():0,b.top-=c&&b.top==a.input.offset().top+g?$(document).scrollTop():0,b.left-=Math.min(b.left,b.left+d>h&&h>d?Math.abs(b.left+d-h):0),b.top-=Math.min(b.top,b.top+e>i&&i>e?Math.abs(e+g):0);return b},_findPos:function(a){var b=this._getInst(a),c=this._get(b,"isRTL");while(a&&(a.type=="hidden"||a.nodeType!=1||$.expr.filters.hidden(a)))a=a[c?"previousSibling":"nextSibling"];var d=$(a).offset();return[d.left,d.top]},_hideDatepicker:function(a){var b=this._curInst;if(!(!b||a&&b!=$.data(a,PROP_NAME))&&this._datepickerShowing){var c=this._get(b,"showAnim"),d=this._get(b,"duration"),e=this,f=function(){$.datepicker._tidyDialog(b),e._curInst=null};$.effects&&$.effects[c]?b.dpDiv.hide(c,$.datepicker._get(b,"showOptions"),d,f):b.dpDiv[c=="slideDown"?"slideUp":c=="fadeIn"?"fadeOut":"hide"](c?d:null,f),c||f(),this._datepickerShowing=!1;var g=this._get(b,"onClose");g&&g.apply(b.input?b.input[0]:null,[b.input?b.input.val():"",b]),this._lastInput=null,this._inDialog&&(this._dialogInput.css({position:"absolute",left:"0",top:"-100px"}),$.blockUI&&($.unblockUI(),$("body").append(this.dpDiv))),this._inDialog=!1}},_tidyDialog:function(a){a.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")},_checkExternalClick:function(a){if(!!$.datepicker._curInst){var b=$(a.target),c=$.datepicker._getInst(b[0]);(b[0].id!=$.datepicker._mainDivId&&b.parents("#"+$.datepicker._mainDivId).length==0&&!b.hasClass($.datepicker.markerClassName)&&!b.closest("."+$.datepicker._triggerClass).length&&$.datepicker._datepickerShowing&&(!$.datepicker._inDialog||!$.blockUI)||b.hasClass($.datepicker.markerClassName)&&$.datepicker._curInst!=c)&&$.datepicker._hideDatepicker()}},_adjustDate:function(a,b,c){var d=$(a),e=this._getInst(d[0]);this._isDisabledDatepicker(d[0])||(this._adjustInstDate(e,b+(c=="M"?this._get(e,"showCurrentAtPos"):0),c),this._updateDatepicker(e))},_gotoToday:function(a){var b=$(a),c=this._getInst(b[0]);if(this._get(c,"gotoCurrent")&&c.currentDay)c.selectedDay=c.currentDay,c.drawMonth=c.selectedMonth=c.currentMonth,c.drawYear=c.selectedYear=c.currentYear;else{var d=new Date;c.selectedDay=d.getDate(),c.drawMonth=c.selectedMonth=d.getMonth(),c.drawYear=c.selectedYear=d.getFullYear()}this._notifyChange(c),this._adjustDate(b)},_selectMonthYear:function(a,b,c){var d=$(a),e=this._getInst(d[0]);e["selected"+(c=="M"?"Month":"Year")]=e["draw"+(c=="M"?"Month":"Year")]=parseInt(b.options[b.selectedIndex].value,10),this._notifyChange(e),this._adjustDate(d)},_selectDay:function(a,b,c,d){var e=$(a);if(!$(d).hasClass(this._unselectableClass)&&!this._isDisabledDatepicker(e[0])){var f=this._getInst(e[0]);f.selectedDay=f.currentDay=$("a",d).html(),f.selectedMonth=f.currentMonth=b,f.selectedYear=f.currentYear=c,this._selectDate(a,this._formatDate(f,f.currentDay,f.currentMonth,f.currentYear))}},_clearDate:function(a){var b=$(a),c=this._getInst(b[0]);this._selectDate(b,"")},_selectDate:function(a,b){var c=$(a),d=this._getInst(c[0]);b=b!=null?b:this._formatDate(d),d.input&&d.input.val(b),this._updateAlternate(d);var e=this._get(d,"onSelect");e?e.apply(d.input?d.input[0]:null,[b,d]):d.input&&d.input.trigger("change"),d.inline?this._updateDatepicker(d):(this._hideDatepicker(),this._lastInput=d.input[0],typeof d.input[0]!="object"&&d.input.focus(),this._lastInput=null)},_updateAlternate:function(a){var b=this._get(a,"altField");if(b){var c=this._get(a,"altFormat")||this._get(a,"dateFormat"),d=this._getDate(a),e=this.formatDate(c,d,this._getFormatConfig(a));$(b).each(function(){$(this).val(e)})}},noWeekends:function(a){var b=a.getDay();return[b>0&&b<6,""]},iso8601Week:function(a){var b=new Date(a.getTime());b.setDate(b.getDate()+4-(b.getDay()||7));var c=b.getTime();b.setMonth(0),b.setDate(1);return Math.floor(Math.round((c-b)/864e5)/7)+1},parseDate:function(a,b,c){if(a==null||b==null)throw"Invalid arguments";b=typeof b=="object"?b.toString():b+"";if(b=="")return null;var d=(c?c.shortYearCutoff:null)||this._defaults.shortYearCutoff;d=typeof d!="string"?d:(new Date).getFullYear()%100+parseInt(d,10);var e=(c?c.dayNamesShort:null)||this._defaults.dayNamesShort,f=(c?c.dayNames:null)||this._defaults.dayNames,g=(c?c.monthNamesShort:null)||this._defaults.monthNamesShort,h=(c?c.monthNames:null)||this._defaults.monthNames,i=-1,j=-1,k=-1,l=-1,m=!1,n=function(b){var c=s+1<a.length&&a.charAt(s+1)==b;c&&s++;return c},o=function(a){var c=n(a),d=a=="@"?14:a=="!"?20:a=="y"&&c?4:a=="o"?3:2,e=new RegExp("^\\d{1,"+d+"}"),f=b.substring(r).match(e);if(!f)throw"Missing number at position "+r;r+=f[0].length;return parseInt(f[0],10)},p=function(a,c,d){var e=$.map(n(a)?d:c,function(a,b){return[[b,a]]}).sort(function(a,b){return-(a[1].length-b[1].length)}),f=-1;$.each(e,function(a,c){var d=c[1];if(b.substr(r,d.length).toLowerCase()==d.toLowerCase()){f=c[0],r+=d.length;return!1}});if(f!=-1)return f+1;throw"Unknown name at position "+r},q=function(){if(b.charAt(r)!=a.charAt(s))throw"Unexpected literal at position "+r;r++},r=0;for(var s=0;s<a.length;s++)if(m)a.charAt(s)=="'"&&!n("'")?m=!1:q();else switch(a.charAt(s)){case"d":k=o("d");break;case"D":p("D",e,f);break;case"o":l=o("o");break;case"m":j=o("m");break;case"M":j=p("M",g,h);break;case"y":i=o("y");break;case"@":var t=new Date(o("@"));i=t.getFullYear(),j=t.getMonth()+1,k=t.getDate();break;case"!":var t=new Date((o("!")-this._ticksTo1970)/1e4);i=t.getFullYear(),j=t.getMonth()+1,k=t.getDate();break;case"'":n("'")?q():m=!0;break;default:q()}if(r<b.length)throw"Extra/unparsed characters found in date: "+b.substring(r);i==-1?i=(new Date).getFullYear():i<100&&(i+=(new Date).getFullYear()-(new Date).getFullYear()%100+(i<=d?0:-100));if(l>-1){j=1,k=l;for(;;){var u=this._getDaysInMonth(i,j-1);if(k<=u)break;j++,k-=u}}var t=this._daylightSavingAdjust(new Date(i,j-1,k));if(t.getFullYear()!=i||t.getMonth()+1!=j||t.getDate()!=k)throw"Invalid date";return t},ATOM:"yy-mm-dd",COOKIE:"D, dd M yy",ISO_8601:"yy-mm-dd",RFC_822:"D, d M y",RFC_850:"DD, dd-M-y",RFC_1036:"D, d M y",RFC_1123:"D, d M yy",RFC_2822:"D, d M yy",RSS:"D, d M y",TICKS:"!",TIMESTAMP:"@",W3C:"yy-mm-dd",_ticksTo1970:(718685+Math.floor(492.5)-Math.floor(19.7)+Math.floor(4.925))*24*60*60*1e7,formatDate:function(a,b,c){if(!b)return"";var d=(c?c.dayNamesShort:null)||this._defaults.dayNamesShort,e=(c?c.dayNames:null)||this._defaults.dayNames,f=(c?c.monthNamesShort:null)||this._defaults.monthNamesShort,g=(c?c.monthNames:null)||this._defaults.monthNames,h=function(b){var c=m+1<a.length&&a.charAt(m+1)==b;c&&m++;return c},i=function(a,b,c){var d=""+b;if(h(a))while(d.length<c)d="0"+d;return d},j=function(a,b,c,d){return h(a)?d[b]:c[b]},k="",l=!1;if(b)for(var m=0;m<a.length;m++)if(l)a.charAt(m)=="'"&&!h("'")?l=!1:k+=a.charAt(m);else switch(a.charAt(m)){case"d":k+=i("d",b.getDate(),2);break;case"D":k+=j("D",b.getDay(),d,e);break;case"o":k+=i("o",Math.round(((new Date(b.getFullYear(),b.getMonth(),b.getDate())).getTime()-(new Date(b.getFullYear(),0,0)).getTime())/864e5),3);break;case"m":k+=i("m",b.getMonth()+1,2);break;case"M":k+=j("M",b.getMonth(),f,g);break;case"y":k+=h("y")?b.getFullYear():(b.getYear()%100<10?"0":"")+b.getYear()%100;break;case"@":k+=b.getTime();break;case"!":k+=b.getTime()*1e4+this._ticksTo1970;break;case"'":h("'")?k+="'":l=!0;break;default:k+=a.charAt(m)}return k},_possibleChars:function(a){var b="",c=!1,d=function(b){var c=e+1<a.length&&a.charAt(e+1)==b;c&&e++;return c};for(var e=0;e<a.length;e++)if(c)a.charAt(e)=="'"&&!d("'")?c=!1:b+=a.charAt(e);else switch(a.charAt(e)){case"d":case"m":case"y":case"@":b+="0123456789";break;case"D":case"M":return null;case"'":d("'")?b+="'":c=!0;break;default:b+=a.charAt(e)}return b},_get:function(a,b){return a.settings[b]!==undefined?a.settings[b]:this._defaults[b]},_setDateFromField:function(a,b){if(a.input.val()!=a.lastVal){var c=this._get(a,"dateFormat"),d=a.lastVal=a.input?a.input.val():null,e,f;e=f=this._getDefaultDate(a);var g=this._getFormatConfig(a);try{e=this.parseDate(c,d,g)||f}catch(h){this.log(h),d=b?"":d}a.selectedDay=e.getDate(),a.drawMonth=a.selectedMonth=e.getMonth(),a.drawYear=a.selectedYear=e.getFullYear(),a.currentDay=d?e.getDate():0,a.currentMonth=d?e.getMonth():0,a.currentYear=d?e.getFullYear():0,this._adjustInstDate(a)}},_getDefaultDate:function(a){return this._restrictMinMax(a,this._determineDate(a,this._get(a,"defaultDate"),new Date))},_determineDate:function(a,b,c){var d=function(a){var b=new Date;b.setDate(b.getDate()+a);return b},e=function(b){try{return $.datepicker.parseDate($.datepicker._get(a,"dateFormat"),b,$.datepicker._getFormatConfig(a))}catch(c){}var d=(b.toLowerCase().match(/^c/)?$.datepicker._getDate(a):null)||new Date,e=d.getFullYear(),f=d.getMonth(),g=d.getDate(),h=/([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,i=h.exec(b);while(i){switch(i[2]||"d"){case"d":case"D":g+=parseInt(i[1],10);break;case"w":case"W":g+=parseInt(i[1],10)*7;break;case"m":case"M":f+=parseInt(i[1],10),g=Math.min(g,$.datepicker._getDaysInMonth(e,f));break;case"y":case"Y":e+=parseInt(i[1],10),g=Math.min(g,$.datepicker._getDaysInMonth(e,f))}i=h.exec(b)}return new Date(e,f,g)},f=b==null||b===""?c:typeof b=="string"?e(b):typeof b=="number"?isNaN(b)?c:d(b):new Date(b.getTime());f=f&&f.toString()=="Invalid Date"?c:f,f&&(f.setHours(0),f.setMinutes(0),f.setSeconds(0),f.setMilliseconds(0));return this._daylightSavingAdjust(f)},_daylightSavingAdjust:function(a){if(!a)return null;a.setHours(a.getHours()>12?a.getHours()+2:0);return a},_setDate:function(a,b,c){var d=!b,e=a.selectedMonth,f=a.selectedYear,g=this._restrictMinMax(a,this._determineDate(a,b,new Date));a.selectedDay=a.currentDay=g.getDate(),a.drawMonth=a.selectedMonth=a.currentMonth=g.getMonth(),a.drawYear=a.selectedYear=a.currentYear=g.getFullYear(),(e!=a.selectedMonth||f!=a.selectedYear)&&!c&&this._notifyChange(a),this._adjustInstDate(a),a.input&&a.input.val(d?"":this._formatDate(a))},_getDate:function(a){var b=!a.currentYear||a.input&&a.input.val()==""?null:this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay));return b},_generateHTML:function(a){var b=new Date;b=this._daylightSavingAdjust(new Date(b.getFullYear(),b.getMonth(),b.getDate()));var c=this._get(a,"isRTL"),d=this._get(a,"showButtonPanel"),e=this._get(a,"hideIfNoPrevNext"),f=this._get(a,"navigationAsDateFormat"),g=this._getNumberOfMonths(a),h=this._get(a,"showCurrentAtPos"),i=this._get(a,"stepMonths"),j=g[0]!=1||g[1]!=1,k=this._daylightSavingAdjust(a.currentDay?new Date(a.currentYear,a.currentMonth,a.currentDay):new Date(9999,9,9)),l=this._getMinMaxDate(a,"min"),m=this._getMinMaxDate(a,"max"),n=a.drawMonth-h,o=a.drawYear;n<0&&(n+=12,o--);if(m){var p=this._daylightSavingAdjust(new Date(m.getFullYear(),m.getMonth()-g[0]*g[1]+1,m.getDate()));p=l&&p<l?l:p;while(this._daylightSavingAdjust(new Date(o,n,1))>p)n--,n<0&&(n=11,o--)}a.drawMonth=n,a.drawYear=o;var q=this._get(a,"prevText");q=f?this.formatDate(q,this._daylightSavingAdjust(new Date(o,n-i,1)),this._getFormatConfig(a)):q;var r=this._canAdjustMonth(a,-1,o,n)?'<a class="ui-datepicker-prev ui-corner-all" onclick="DP_jQuery_'+dpuuid+".datepicker._adjustDate('#"+a.id+"', -"+i+", 'M');\""+' title="'+q+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"e":"w")+'">'+q+"</span></a>":e?"":'<a class="ui-datepicker-prev ui-corner-all ui-state-disabled" title="'+q+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"e":"w")+'">'+q+"</span></a>",s=this._get(a,"nextText");s=f?this.formatDate(s,this._daylightSavingAdjust(new Date(o,n+i,1)),this._getFormatConfig(a)):s;var t=this._canAdjustMonth(a,1,o,n)?'<a class="ui-datepicker-next ui-corner-all" onclick="DP_jQuery_'+dpuuid+".datepicker._adjustDate('#"+a.id+"', +"+i+", 'M');\""+' title="'+s+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"w":"e")+'">'+s+"</span></a>":e?"":'<a class="ui-datepicker-next ui-corner-all ui-state-disabled" title="'+s+'"><span class="ui-icon ui-icon-circle-triangle-'+(c?"w":"e")+'">'+s+"</span></a>",u=this._get(a,"currentText"),v=this._get(a,"gotoCurrent")&&a.currentDay?k:b;u=f?this.formatDate(u,v,this._getFormatConfig(a)):u;var w=a.inline?"":'<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" onclick="DP_jQuery_'+dpuuid+'.datepicker._hideDatepicker();">'+this._get(a,"closeText")+"</button>",x=d?'<div class="ui-datepicker-buttonpane ui-widget-content">'+(c?w:"")+(this._isInRange(a,v)?'<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" onclick="DP_jQuery_'+dpuuid+".datepicker._gotoToday('#"+a.id+"');\""+">"+u+"</button>":"")+(c?"":w)+"</div>":"",y=parseInt(this._get(a,"firstDay"),10);y=isNaN(y)?0:y;var z=this._get(a,"showWeek"),A=this._get(a,"dayNames"),B=this._get(a,"dayNamesShort"),C=this._get(a,"dayNamesMin"),D=this._get(a,"monthNames"),E=this._get(a,"monthNamesShort"),F=this._get(a,"beforeShowDay"),G=this._get(a,"showOtherMonths"),H=this._get(a,"selectOtherMonths"),I=this._get(a,"calculateWeek")||this.iso8601Week,J=this._getDefaultDate(a),K="";for(var L=0;L<g[0];L++){var M="";this.maxRows=4;for(var N=0;N<g[1];N++){var O=this._daylightSavingAdjust(new Date(o,n,a.selectedDay)),P=" ui-corner-all",Q="";if(j){Q+='<div class="ui-datepicker-group';if(g[1]>1)switch(N){case 0:Q+=" ui-datepicker-group-first",P=" ui-corner-"+(c?"right":"left");break;case g[1]-1:Q+=" ui-datepicker-group-last",P=" ui-corner-"+(c?"left":"right");break;default:Q+=" ui-datepicker-group-middle",P=""}Q+='">'}Q+='<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix'+P+'">'+(/all|left/.test(P)&&L==0?c?t:r:"")+(/all|right/.test(P)&&L==0?c?r:t:"")+this._generateMonthYearHeader(a,n,o,l,m,L>0||N>0,D,E)+'</div><table class="ui-datepicker-calendar"><thead>'+"<tr>";var R=z?'<th class="ui-datepicker-week-col">'+this._get(a,"weekHeader")+"</th>":"";for(var S=0;S<7;S++){var T=(S+y)%7;R+="<th"+((S+y+6)%7>=5?' class="ui-datepicker-week-end"':"")+">"+'<span title="'+A[T]+'">'+C[T]+"</span></th>"}Q+=R+"</tr></thead><tbody>";var U=this._getDaysInMonth(o,n);o==a.selectedYear&&n==a.selectedMonth&&(a.selectedDay=Math.min(a.selectedDay,U));var V=(this._getFirstDayOfMonth(o,n)-y+7)%7,W=Math.ceil((V+U)/7),X=j?this.maxRows>W?this.maxRows:W:W;this.maxRows=X;var Y=this._daylightSavingAdjust(new Date(o,n,1-V));for(var Z=0;Z<X;Z++){Q+="<tr>";var _=z?'<td class="ui-datepicker-week-col">'+this._get(a,"calculateWeek")(Y)+"</td>":"";for(var S=0;S<7;S++){var ba=F?F.apply(a.input?a.input[0]:null,[Y]):[!0,""],bb=Y.getMonth()!=n,bc=bb&&!H||!ba[0]||l&&Y<l||m&&Y>m;_+='<td class="'+((S+y+6)%7>=5?" ui-datepicker-week-end":"")+(bb?" ui-datepicker-other-month":"")+(Y.getTime()==O.getTime()&&n==a.selectedMonth&&a._keyEvent||J.getTime()==Y.getTime()&&J.getTime()==O.getTime()?" "+this._dayOverClass:"")+(bc?" "+this._unselectableClass+" ui-state-disabled":"")+(bb&&!G?"":" "+ba[1]+(Y.getTime()==k.getTime()?" "+this._currentClass:"")+(Y.getTime()==b.getTime()?" ui-datepicker-today":""))+'"'+((!bb||G)&&ba[2]?' title="'+ba[2]+'"':"")+(bc?"":' onclick="DP_jQuery_'+dpuuid+".datepicker._selectDay('#"+a.id+"',"+Y.getMonth()+","+Y.getFullYear()+', this);return false;"')+">"+(bb&&!G?"&#xa0;":bc?'<span class="ui-state-default">'+Y.getDate()+"</span>":'<a class="ui-state-default'+(Y.getTime()==b.getTime()?" ui-state-highlight":"")+(Y.getTime()==k.getTime()?" ui-state-active":"")+(bb?" ui-priority-secondary":"")+'" href="#">'+Y.getDate()+"</a>")+"</td>",Y.setDate(Y.getDate()+1),Y=this._daylightSavingAdjust(Y)}Q+=_+"</tr>"}n++,n>11&&(n=0,o++),Q+="</tbody></table>"+(j?"</div>"+(g[0]>0&&N==g[1]-1?'<div class="ui-datepicker-row-break"></div>':""):""),M+=Q}K+=M}K+=x+($.browser.msie&&parseInt($.browser.version,10)<7&&!a.inline?'<iframe src="javascript:false;" class="ui-datepicker-cover" frameborder="0"></iframe>':""),
	a._keyEvent=!1;return K},_generateMonthYearHeader:function(a,b,c,d,e,f,g,h){var i=this._get(a,"changeMonth"),j=this._get(a,"changeYear"),k=this._get(a,"showMonthAfterYear"),l='<div class="ui-datepicker-title">',m="";if(f||!i)m+='<span class="ui-datepicker-month">'+g[b]+"</span>";else{var n=d&&d.getFullYear()==c,o=e&&e.getFullYear()==c;m+='<select class="ui-datepicker-month" onchange="DP_jQuery_'+dpuuid+".datepicker._selectMonthYear('#"+a.id+"', this, 'M');\" "+">";for(var p=0;p<12;p++)(!n||p>=d.getMonth())&&(!o||p<=e.getMonth())&&(m+='<option value="'+p+'"'+(p==b?' selected="selected"':"")+">"+h[p]+"</option>");m+="</select>"}k||(l+=m+(f||!i||!j?"&#xa0;":""));if(!a.yearshtml){a.yearshtml="";if(f||!j)l+='<span class="ui-datepicker-year">'+c+"</span>";else{var q=this._get(a,"yearRange").split(":"),r=(new Date).getFullYear(),s=function(a){var b=a.match(/c[+-].*/)?c+parseInt(a.substring(1),10):a.match(/[+-].*/)?r+parseInt(a,10):parseInt(a,10);return isNaN(b)?r:b},t=s(q[0]),u=Math.max(t,s(q[1]||""));t=d?Math.max(t,d.getFullYear()):t,u=e?Math.min(u,e.getFullYear()):u,a.yearshtml+='<select class="ui-datepicker-year" onchange="DP_jQuery_'+dpuuid+".datepicker._selectMonthYear('#"+a.id+"', this, 'Y');\" "+">";for(;t<=u;t++)a.yearshtml+='<option value="'+t+'"'+(t==c?' selected="selected"':"")+">"+t+"</option>";a.yearshtml+="</select>",l+=a.yearshtml,a.yearshtml=null}}l+=this._get(a,"yearSuffix"),k&&(l+=(f||!i||!j?"&#xa0;":"")+m),l+="</div>";return l},_adjustInstDate:function(a,b,c){var d=a.drawYear+(c=="Y"?b:0),e=a.drawMonth+(c=="M"?b:0),f=Math.min(a.selectedDay,this._getDaysInMonth(d,e))+(c=="D"?b:0),g=this._restrictMinMax(a,this._daylightSavingAdjust(new Date(d,e,f)));a.selectedDay=g.getDate(),a.drawMonth=a.selectedMonth=g.getMonth(),a.drawYear=a.selectedYear=g.getFullYear(),(c=="M"||c=="Y")&&this._notifyChange(a)},_restrictMinMax:function(a,b){var c=this._getMinMaxDate(a,"min"),d=this._getMinMaxDate(a,"max"),e=c&&b<c?c:b;e=d&&e>d?d:e;return e},_notifyChange:function(a){var b=this._get(a,"onChangeMonthYear");b&&b.apply(a.input?a.input[0]:null,[a.selectedYear,a.selectedMonth+1,a])},_getNumberOfMonths:function(a){var b=this._get(a,"numberOfMonths");return b==null?[1,1]:typeof b=="number"?[1,b]:b},_getMinMaxDate:function(a,b){return this._determineDate(a,this._get(a,b+"Date"),null)},_getDaysInMonth:function(a,b){return 32-this._daylightSavingAdjust(new Date(a,b,32)).getDate()},_getFirstDayOfMonth:function(a,b){return(new Date(a,b,1)).getDay()},_canAdjustMonth:function(a,b,c,d){var e=this._getNumberOfMonths(a),f=this._daylightSavingAdjust(new Date(c,d+(b<0?b:e[0]*e[1]),1));b<0&&f.setDate(this._getDaysInMonth(f.getFullYear(),f.getMonth()));return this._isInRange(a,f)},_isInRange:function(a,b){var c=this._getMinMaxDate(a,"min"),d=this._getMinMaxDate(a,"max");return(!c||b.getTime()>=c.getTime())&&(!d||b.getTime()<=d.getTime())},_getFormatConfig:function(a){var b=this._get(a,"shortYearCutoff");b=typeof b!="string"?b:(new Date).getFullYear()%100+parseInt(b,10);return{shortYearCutoff:b,dayNamesShort:this._get(a,"dayNamesShort"),dayNames:this._get(a,"dayNames"),monthNamesShort:this._get(a,"monthNamesShort"),monthNames:this._get(a,"monthNames")}},_formatDate:function(a,b,c,d){b||(a.currentDay=a.selectedDay,a.currentMonth=a.selectedMonth,a.currentYear=a.selectedYear);var e=b?typeof b=="object"?b:this._daylightSavingAdjust(new Date(d,c,b)):this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay));return this.formatDate(this._get(a,"dateFormat"),e,this._getFormatConfig(a))}}),$.fn.datepicker=function(a){if(!this.length)return this;$.datepicker.initialized||($(document).mousedown($.datepicker._checkExternalClick).find("body").append($.datepicker.dpDiv),$.datepicker.initialized=!0);var b=Array.prototype.slice.call(arguments,1);if(typeof a=="string"&&(a=="isDisabled"||a=="getDate"||a=="widget"))return $.datepicker["_"+a+"Datepicker"].apply($.datepicker,[this[0]].concat(b));if(a=="option"&&arguments.length==2&&typeof arguments[1]=="string")return $.datepicker["_"+a+"Datepicker"].apply($.datepicker,[this[0]].concat(b));return this.each(function(){typeof a=="string"?$.datepicker["_"+a+"Datepicker"].apply($.datepicker,[this].concat(b)):$.datepicker._attachDatepicker(this,a)})},$.datepicker=new Datepicker,$.datepicker.initialized=!1,$.datepicker.uuid=(new Date).getTime(),$.datepicker.version="1.8.18",window["DP_jQuery_"+dpuuid]=$})(jQuery);
(function($){$.extend({
	notifier: {
		options: {
			core:				"notifier",
			box_class: 			"n-box",
			notice_class: 		"n-notice",
			error_class: 		"n-error",
			close_class: 		"n-close",
			duration:			4000
		},
		notices:{},
		

		broadcast: function(title, message, type){
			this.core();
								
			var id = "notice-" + this.timestamp();
			
			// set notices object
			this.notices[id] = {id: id};
			notice = {
				id: id,
				ttl: title,
				msg: message
			}
			
			// box
			$("#" + this.options.core).append(this.box(notice).addClass(type));
		},
		
		
		notice: function(title, message){
			this.broadcast(title, message, this.options.notice_class);
		},
		
		
		error: function(title, message){
			this.broadcast(title, message, this.options.error_class);
		},
		

		core: function(){
			var core	= this.options.core;
			return $("#" + core).length == 0 ? $('body').append("<div id=\"" + core + "\"></div>") : $("#" + core);
		},
		

		box: function(notice){
			var box	= $("<div id=\"" + notice.id + "\" class=\"" + this.options.box_class + "\"></div>");				
			if (notice.ttl != null) box.append($("<h3></h3>").append(notice.ttl));
			box.append($("<p></p>").append(notice.msg));
			box.hide().show();
			this.life(box, notice.id);
			this.events(box, notice.id);		
			return box;
		},
					

		events: function(box, seed){
			$(box).bind(
				'click',
				function(){
					var seed = $(this).attr("id");
					$.notifier.destroy(seed, true);
				}
			)
			$(box).bind(
				'mouseover',
				function(){
					if($.notifier.notices[$(this).attr("id")].interval){				
						var seed = $(this).attr("id");
						$.notifier.destroy(seed)
					}
				}
			)			
			$(box).bind(
				'mouseout',
				function(){
					$.notifier.life(this, $(this).attr("id"));
				}
			)
		},
		

		life: function(box, seed){
			if(!this.notices[seed].duration){this.notices[seed].duration = this.options.duration}
			this.notices[seed].interval = {};
			this.notices[seed].interval	= setInterval(
				function(){
					(function(seed){
						$.notifier.destroy(seed, true)
					})
					(seed)
				},
				this.notices[seed].duration
			)
		},
		

		destroy: function(seed, remove){
			clearInterval($.notifier.notices[seed].interval);
			delete $.notifier.notices[seed].interval;
			if(remove == true){$("#" + seed).slideUp(250, function(){$(this).remove()});}
		},
		

		timestamp:function(){
			return new Date().getTime();
		}
	}
})})(jQuery);
/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */
;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);




jQuery.richArray={isin:function(a,b){a=jQuery.richArray.getArray(a);b=b||0;for(var i=0,len=a.length;i<len;++i){if(a[i]==b){return true}}return false},unique:function(a){a=jQuery.richArray.getArray(a);var b=[];for(var i=0,len=a.length;i<len;++i){if(!jQuery.richArray.isin(b,a[i])){b.push(a[i])}}return b},diff:function(a,b){a=jQuery.richArray.getArray(a);b=jQuery.richArray.getArray(b);var c=[];for(var i=0,len=a.length;i<len;++i){if(!jQuery.richArray.isin(b,a[i])){c.push(a[i])}}return c},intersect:function(a,b){a=jQuery.richArray.getArray(a);b=jQuery.richArray.getArray(b);var c=[];for(var i=0,len=a.length;i<len;++i){if(jQuery.richArray.isin(b,a[i])){c.push(a[i])}}return c},filter:function(a,b,c){a=jQuery.richArray.getArray(a);b=jQuery.richArray.getFunction(b);c=c||this;var d=[];for(var i=0,len=a.length;i<len;++i){if(b.call(c,a[i])){d.push(a[i])}}return d},map:function(a,b,c){a=jQuery.richArray.getArray(a);b=jQuery.richArray.getFunction(b);c=c||this;result=[];for(var i=0,len=a.length;i<len;++i){result.push(b.call(c,a[i]))}return result},sum:function(a,b){a=jQuery.richArray.getArray(a);b=b||0;for(var i=0,len=a.length;i<len;++i){b+=a[i]}return b},product:function(a,b){a=jQuery.richArray.getArray(a);b=b||1;for(var i=0,len=a.length;i<len;++i){b*=a[i]}return b},reduce:function(a){a=jQuery.richArray.getArray(a);if(1==a.length){return a[0]}return a},compact:function(a){var b=[];for(var i=0,len=a.length;i<len;++i){if(null!=a[i]){b.push(a[i])}}return b},without:function(a,b){var c=[];for(var i=0,len=a.length;i<len;++i){if(b!=a[i]){c.push(a[i])}}return c},getArray:function(a){if(!(a instanceof Array)){a=[]}return a},getFunction:function(a){if(!(a instanceof Function))a=new Function();return a}};
// ----------------------------------------------------------------------------
// markItUp! Universal MarkUp Engine, JQuery plugin
// v 1.1.12
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2007-2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
(function($) {
	$.fn.markItUp = function(settings, extraSettings) {
		var options, ctrlKey, shiftKey, altKey;
		ctrlKey = shiftKey = altKey = false;
	
		options = {	id:						'',
					nameSpace:				'',
					root:					'',
					previewInWindow:		'', // 'width=800, height=600, resizable=yes, scrollbars=yes'
					previewAutoRefresh:		true,
					previewPosition:		'after',
					previewTemplatePath:	'~/templates/preview.html',
					previewParser:			false,
					previewParserPath:		'',
					previewParserVar:		'data',
					resizeHandle:			true,
					beforeInsert:			'',
					afterInsert:			'',
					onEnter:				{},
					onShiftEnter:			{},
					onCtrlEnter:			{},
					onTab:					{},
					markupSet:			[	{ /* set */ } ]
				};
		$.extend(options, settings, extraSettings);

		// compute markItUp! path
		if (!options.root) {
			$('script').each(function(a, tag) {
				miuScript = $(tag).get(0).src.match(/(.*)jquery\.markitup(\.pack)?\.js$/);
				if (miuScript !== null) {
					options.root = miuScript[1];
				}
			});
		}

		return this.each(function() {
			var $$, textarea, levels, scrollPosition, caretPosition, caretOffset,
				clicked, hash, header, footer, previewWindow, template, iFrame, abort;
			$$ = $(this);
			textarea = this;
			levels = [];
			abort = false;
			scrollPosition = caretPosition = 0;
			caretOffset = -1;

			options.previewParserPath = localize(options.previewParserPath);
			options.previewTemplatePath = localize(options.previewTemplatePath);

			// apply the computed path to ~/
			function localize(data, inText) {
				if (inText) {
					return 	data.replace(/("|')~\//g, "$1"+options.root);
				}
				return 	data.replace(/^~\//, options.root);
			}

			// init and build editor
			function init() {
				id = ''; nameSpace = '';
				if (options.id) {
					id = 'id="'+options.id+'"';
				} else if ($$.attr("id")) {
					id = 'id="markItUp'+($$.attr("id").substr(0, 1).toUpperCase())+($$.attr("id").substr(1))+'"';

				}
				if (options.nameSpace) {
					nameSpace = 'class="'+options.nameSpace+'"';
				}
				$$.wrap('<div '+nameSpace+'></div>');
				$$.wrap('<div '+id+' class="markItUp"></div>');
				$$.wrap('<div class="markItUpContainer"></div>');
				$$.addClass("markItUpEditor");

				// add the header before the textarea
				header = $('<div class="markItUpHeader"></div>').insertBefore($$);
				$(dropMenus(options.markupSet)).appendTo(header);

				// add the footer after the textarea
				footer = $('<div class="markItUpFooter"></div>').insertAfter($$);

				// add the resize handle after textarea
				if (options.resizeHandle === true && $.browser.safari !== true) {
					resizeHandle = $('<div class="markItUpResizeHandle"></div>')
						.insertAfter($$)
						.bind("mousedown", function(e) {
							var h = $$.height(), y = e.clientY, mouseMove, mouseUp;
							mouseMove = function(e) {
								$$.css("height", Math.max(20, e.clientY+h-y)+"px");
								return false;
							};
							mouseUp = function(e) {
								$("html").unbind("mousemove", mouseMove).unbind("mouseup", mouseUp);
								return false;
							};
							$("html").bind("mousemove", mouseMove).bind("mouseup", mouseUp);
					});
					footer.append(resizeHandle);
				}

				// listen key events
				$$.keydown(keyPressed).keyup(keyPressed);
				
				// bind an event to catch external calls
				$$.bind("insertion", function(e, settings) {
					if (settings.target !== false) {
						get();
					}
					if (textarea === $.markItUp.focused) {
						markup(settings);
					}
				});

				// remember the last focus
				$$.focus(function() {
					$.markItUp.focused = this;
				});
			}

			// recursively build header with dropMenus from markupset
			function dropMenus(markupSet) {
				var ul = $('<ul></ul>'), i = 0;
				$('li:hover > ul', ul).css('display', 'block');
				$.each(markupSet, function() {
					var button = this, t = '', title, li, j;
					title = (button.key) ? (button.name||'')+' [Ctrl+'+button.key+']' : (button.name||'');
					key   = (button.key) ? 'accesskey="'+button.key+'"' : '';
					if (button.separator) {
						li = $('<li class="markItUpSeparator">'+(button.separator||'')+'</li>').appendTo(ul);
					} else {
						i++;
						for (j = levels.length -1; j >= 0; j--) {
							t += levels[j]+"-";
						}
						li = $('<li title="'+title+'" class="markItUpButton markItUpButton'+t+(i)+' '+(button.className||'')+'"><a href="" '+key+'></a></li>')
						.bind("contextmenu", function() { // prevent contextmenu on mac and allow ctrl+click
							return false;
						}).click(function() {
							return false;
						}).bind("focusin", function(){
                            //$$.focus();
						}).mouseup(function() {
							if (button.call) {
								eval(button.call)();
							}
							setTimeout(function() { markup(button) },1);
							return false;
						}).hover(function() {
								$('> ul', this).show();
								$(document).one('click', function() { // close dropmenu if click outside
										$('ul ul', header).hide();
									}
								);
							}, function() {
								$('> ul', this).hide();
							}
						).appendTo(ul);
						if (button.dropMenu) {
							levels.push(i);
							$(li).addClass('markItUpDropMenu').append(dropMenus(button.dropMenu));
						}
					}
				}); 
				levels.pop();
				return ul;
			}

			// markItUp! markups
			function magicMarkups(string) {
				if (string) {
					string = string.toString();
					string = string.replace(/\(\!\(([\s\S]*?)\)\!\)/g,
						function(x, a) {
							var b = a.split('|!|');
							if (altKey === true) {
								return (b[1] !== undefined) ? b[1] : b[0];
							} else {
								return (b[1] === undefined) ? "" : b[0];
							}
						}
					);
					// [![prompt]!], [![prompt:!:value]!]
					string = string.replace(/\[\!\[([\s\S]*?)\]\!\]/g,
						function(x, a) {
							var b = a.split(':!:');
							if (abort === true) {
								return false;
							}
							value = prompt(b[0], (b[1]) ? b[1] : '');
							if (value === null) {
								abort = true;
							}
							return value;
						}
					);
					return string;
				}
				return "";
			}

			// prepare action
			function prepare(action) {
				if ($.isFunction(action)) {
					action = action(hash);
				}
				return magicMarkups(action);
			}

			// build block to insert
			function build(string) {
				var openWith 			= prepare(clicked.openWith);
				var placeHolder 		= prepare(clicked.placeHolder);
				var replaceWith 		= prepare(clicked.replaceWith);
				var closeWith 			= prepare(clicked.closeWith);
				var openBlockWith 		= prepare(clicked.openBlockWith);
				var closeBlockWith 		= prepare(clicked.closeBlockWith);
				var multiline 			= clicked.multiline;
				
				if (replaceWith !== "") {
					block = openWith + replaceWith + closeWith;
				} else if (selection === '' && placeHolder !== '') {
					block = openWith + placeHolder + closeWith;
				} else {
					string = string || selection;
					
					var lines = [string], blocks = [];
					if (multiline===true) {
						lines = string.split(/\r?\n/);
					}
					
					for (var l=0; l < lines.length; l++) {
						line = lines[l];
						var trailingSpaces;
						if (trailingSpaces = line.match(/ *$/)) {
							blocks.push(openWith + line.replace(/ *$/g, '') + closeWith + trailingSpaces);
						} else {
							blocks.push(openWith + line + closeWith);
						}
					}
					
					block = blocks.join("\n");
				}

				block = openBlockWith + block + closeBlockWith;

				return {	block:block, 
							openWith:openWith, 
							replaceWith:replaceWith, 
							placeHolder:placeHolder,
							closeWith:closeWith
					};
			}

			// define markup to insert
			function markup(button) {
				var len, j, n, i;
				hash = clicked = button;
				get();
				$.extend(hash, {	line:"", 
						 			root:options.root,
									textarea:textarea, 
									selection:(selection||''), 
									selectionOuter:(selectionOuter||''), 
									caretPosition:caretPosition,
									ctrlKey:ctrlKey, 
									shiftKey:shiftKey, 
									altKey:altKey
								}
							);
				// callbacks before insertion
				prepare(options.beforeInsert);
				prepare(clicked.beforeInsert);
				if ((ctrlKey === true && shiftKey === true) || button.multiline === true) {
					prepare(clicked.beforeMultiInsert);
				}			
				$.extend(hash, { line:1 });

				if ((ctrlKey === true && shiftKey === true)) {
					lines = selection.split(/\r?\n/);
					for (j = 0, n = lines.length, i = 0; i < n; i++) {
						if ($.trim(lines[i]) !== '') {
							$.extend(hash, { line:++j, selection:lines[i] } );
							lines[i] = build(lines[i]).block;
						} else {
							lines[i] = "";
						}
					}
					string = { block:lines.join('\n')};
					start = caretPosition;
					len = string.block.length + (($.browser.opera) ? n-1 : 0);
				} else if (ctrlKey === true) {
					string = build(selection);
					start = caretPosition + string.openWith.length;
					len = string.block.length - string.openWith.length - string.closeWith.length;
					len = len - (string.block.match(/ $/) ? 1 : 0);
					len -= fixIeBug(string.block);
				} else if (shiftKey === true) {
					string = build(selection);
					start = caretPosition;
					len = string.block.length;
					len -= fixIeBug(string.block);
				} else {
					string = build(selection);
					start = caretPosition + string.block.length ;
					len = 0;
					start -= fixIeBug(string.block);
				}
				if ((selection === '' && string.replaceWith === '')) {
					caretOffset += fixOperaBug(string.block);
					
					start = caretPosition + string.openWith.length;
					len = string.block.length - string.openWith.length - string.closeWith.length;

					caretOffset = $$.val().substring(caretPosition,  $$.val().length).length;
					caretOffset -= fixOperaBug($$.val().substring(0, caretPosition));
				}
				$.extend(hash, { caretPosition:caretPosition, scrollPosition:scrollPosition } );

				if (string.block !== selection && abort === false) {
					insert(string.block);
					set(start, len);
				} else {
					caretOffset = -1;
				}
				get();

				$.extend(hash, { line:'', selection:selection });

				// callbacks after insertion
				if ((ctrlKey === true && shiftKey === true) || button.multiline === true) {
					prepare(clicked.afterMultiInsert);
				}
				prepare(clicked.afterInsert);
				prepare(options.afterInsert);

				// refresh preview if opened
				if (previewWindow && options.previewAutoRefresh) {
					refreshPreview(); 
				}
																									
				// reinit keyevent
				shiftKey = altKey = ctrlKey = abort = false;
			}

			// Substract linefeed in Opera
			function fixOperaBug(string) {
				if ($.browser.opera) {
					return string.length - string.replace(/\n*/g, '').length;
				}
				return 0;
			}
			// Substract linefeed in IE
			function fixIeBug(string) {
				if ($.browser.msie) {
					return string.length - string.replace(/\r*/g, '').length;
				}
				return 0;
			}
				
			// add markup
			function insert(block) {	
				if (document.selection) {
					var newSelection = document.selection.createRange();
					newSelection.text = block;
				} else {
					textarea.value =  textarea.value.substring(0, caretPosition)  + block + textarea.value.substring(caretPosition + selection.length, textarea.value.length);
				}
			}

			// set a selection
			function set(start, len) {
				if (textarea.createTextRange){
					// quick fix to make it work on Opera 9.5
					if ($.browser.opera && $.browser.version >= 9.5 && len == 0) {
						return false;
					}
					range = textarea.createTextRange();
					range.collapse(true);
					range.moveStart('character', start); 
					range.moveEnd('character', len); 
					range.select();
				} else if (textarea.setSelectionRange ){
					textarea.setSelectionRange(start, start + len);
				}
				textarea.scrollTop = scrollPosition;
				textarea.focus();
			}

			// get the selection
			function get() {
				// get the selection by outer text
				selectionOuter = '';
				if(window.getSelection){
					selectionOuter = window.getSelection().toString();
				} else if(window.document.selection){
					var sel = window.document.selection.createRange();
					selectionOuter = sel.text || sel;
					if(selectionOuter.toString) {
						selectionOuter = selectionOuter.toString();
					} else {
						selectionOuter='';
					}
				}
				textarea.focus();

				scrollPosition = textarea.scrollTop;
				if (document.selection) {
					selection = document.selection.createRange().text;
					if ($.browser.msie) { // ie
						var range = document.selection.createRange(), rangeCopy = range.duplicate();
						rangeCopy.moveToElementText(textarea);
						caretPosition = -1;
						while(rangeCopy.inRange(range)) {
							rangeCopy.moveStart('character');
							caretPosition ++;
						}
					} else { // opera
						caretPosition = textarea.selectionStart;
					}
				} else { // gecko & webkit
					caretPosition = textarea.selectionStart;

					selection = textarea.value.substring(caretPosition, textarea.selectionEnd);
				} 
				return selection;
			}

			// open preview window
			function preview() {
				if (!previewWindow || previewWindow.closed) {
					if (options.previewInWindow) {
						previewWindow = window.open('', 'preview', options.previewInWindow);
						$(window).unload(function() {
							previewWindow.close();
						});
					} else {
						iFrame = $('<iframe class="markItUpPreviewFrame"></iframe>');
						if (options.previewPosition == 'after') {
							iFrame.insertAfter(footer);
						} else {
							iFrame.insertBefore(header);
						}	
						previewWindow = iFrame[iFrame.length - 1].contentWindow || frame[iFrame.length - 1];
					}
				} else if (altKey === true) {
					if (iFrame) {
						iFrame.remove();
					} else {
						previewWindow.close();
					}
					previewWindow = iFrame = false;
				}
				if (!options.previewAutoRefresh) {
					refreshPreview(); 
				}
				if (options.previewInWindow) {
					previewWindow.focus();
				}
			}

			// refresh Preview window
			function refreshPreview() {
 				renderPreview();
			}

			function renderPreview() {		
				var phtml;
				if (options.previewParser && typeof options.previewParser === 'function') {
					var data = options.previewParser( $$.val() );
					writeInPreview( localize(data, 1) ); 
				} else if (options.previewParserPath !== '') {
					$.ajax({
						type: 'POST',
						dataType: 'text',
						global: false,
						url: options.previewParserPath,
						data: options.previewParserVar+'='+encodeURIComponent($$.val()),
						success: function(data) {
							writeInPreview( localize(data, 1) ); 
						}
					});
				} else {
					if (!template) {
						$.ajax({
							url: options.previewTemplatePath,
							dataType: 'text',
							global: false,
							success: function(data) {
								writeInPreview( localize(data, 1).replace(/<!-- content -->/g, $$.val()) );
							}
						});
					}
				}
				return false;
			}
			
			function writeInPreview(data) {
				if (previewWindow.document) {			
					try {
						sp = previewWindow.document.documentElement.scrollTop
					} catch(e) {
						sp = 0;
					}	
					previewWindow.document.open();
					previewWindow.document.write(data);
					previewWindow.document.close();
					previewWindow.document.documentElement.scrollTop = sp;
				}
			}
			
			// set keys pressed
			function keyPressed(e) { 
				shiftKey = e.shiftKey;
				altKey = e.altKey;
				ctrlKey = (!(e.altKey && e.ctrlKey)) ? (e.ctrlKey || e.metaKey) : false;

				if (e.type === 'keydown') {
					if (ctrlKey === true) {
						li = $('a[accesskey="'+String.fromCharCode(e.keyCode)+'"]', header).parent('li');
						if (li.length !== 0) {
							ctrlKey = false;
							setTimeout(function() {
								li.triggerHandler('mouseup');
							},1);
							return false;
						}
					}
					if (e.keyCode === 13 || e.keyCode === 10) { // Enter key
						if (ctrlKey === true) {  // Enter + Ctrl
							ctrlKey = false;
							markup(options.onCtrlEnter);
							return options.onCtrlEnter.keepDefault;
						} else if (shiftKey === true) { // Enter + Shift
							shiftKey = false;
							markup(options.onShiftEnter);
							return options.onShiftEnter.keepDefault;
						} else { // only Enter
							markup(options.onEnter);
							return options.onEnter.keepDefault;
						}
					}
					if (e.keyCode === 9) { // Tab key
						if (shiftKey == true || ctrlKey == true || altKey == true) {
							return false; 
						}
						if (caretOffset !== -1) {
							get();
							caretOffset = $$.val().length - caretOffset;
							set(caretOffset, 0);
							caretOffset = -1;
							return false;
						} else {
							markup(options.onTab);
							return options.onTab.keepDefault;
						}
					}
				}
			}

			init();
		});
	};

	$.fn.markItUpRemove = function() {
		return this.each(function() {
				var $$ = $(this).unbind().removeClass('markItUpEditor');
				$$.parent('div').parent('div.markItUp').parent('div').replaceWith($$);
			}
		);
	};

	$.markItUp = function(settings) {
		var options = { target:false };
		$.extend(options, settings);
		if (options.target) {
			return $(options.target).each(function() {
				$(this).focus();
				$(this).trigger('insertion', [options]);
			});
		} else {
			$('textarea').trigger('insertion', [options]);
		}
	};
})(jQuery);
;
/*!
 * jQuery Form Plugin
 * version: 2.83 (11-JUL-2011)
 * @requires jQuery v1.3.2 or later
 *
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
;(function($) {

/*
	Usage Note:
	-----------
	Do not use both ajaxSubmit and ajaxForm on the same form.  These
	functions are intended to be exclusive.  Use ajaxSubmit if you want
	to bind your own submit handler to the form.  For example,

	$(document).ready(function() {
		$('#myForm').bind('submit', function(e) {
			e.preventDefault(); // <-- important
			$(this).ajaxSubmit({
				target: '#output'
			});
		});
	});

	Use ajaxForm when you want the plugin to manage all the event binding
	for you.  For example,

	$(document).ready(function() {
		$('#myForm').ajaxForm({
			target: '#output'
		});
	});

	When using ajaxForm, the ajaxSubmit function will be invoked for you
	at the appropriate time.
*/

/**
 * ajaxSubmit() provides a mechanism for immediately submitting
 * an HTML form using AJAX.
 */
$.fn.ajaxSubmit = function(options) {
	// fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
	if (!this.length) {
		log('ajaxSubmit: skipping submit process - no element selected');
		return this;
	}
	
	var method, action, url, $form = this;

	if (typeof options == 'function') {
		options = { success: options };
	}

	method = this.attr('method');
	action = this.attr('action');
	url = (typeof action === 'string') ? $.trim(action) : '';
	url = url || window.location.href || '';
	if (url) {
		// clean url (don't include hash vaue)
		url = (url.match(/^([^#]+)/)||[])[1];
	}

	options = $.extend(true, {
		url:  url,
		success: $.ajaxSettings.success,
		type: method || 'GET',
		iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank'
	}, options);

	// hook for manipulating the form data before it is extracted;
	// convenient for use with rich editors like tinyMCE or FCKEditor
	var veto = {};
	this.trigger('form-pre-serialize', [this, options, veto]);
	if (veto.veto) {
		log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
		return this;
	}

	// provide opportunity to alter form data before it is serialized
	if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
		log('ajaxSubmit: submit aborted via beforeSerialize callback');
		return this;
	}

	var n,v,a = this.formToArray(options.semantic);
	if (options.data) {
		options.extraData = options.data;
		for (n in options.data) {
			if(options.data[n] instanceof Array) {
				for (var k in options.data[n]) {
					a.push( { name: n, value: options.data[n][k] } );
				}
			}
			else {
				v = options.data[n];
				v = $.isFunction(v) ? v() : v; // if value is fn, invoke it
				a.push( { name: n, value: v } );
			}
		}
	}

	// give pre-submit callback an opportunity to abort the submit
	if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
		log('ajaxSubmit: submit aborted via beforeSubmit callback');
		return this;
	}

	// fire vetoable 'validate' event
	this.trigger('form-submit-validate', [a, this, options, veto]);
	if (veto.veto) {
		log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
		return this;
	}

	var q = $.param(a);

	if (options.type.toUpperCase() == 'GET') {
		options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
		options.data = null;  // data is null for 'get'
	}
	else {
		options.data = q; // data is the query string for 'post'
	}

	var callbacks = [];
	if (options.resetForm) {
		callbacks.push(function() { $form.resetForm(); });
	}
	if (options.clearForm) {
		callbacks.push(function() { $form.clearForm(); });
	}

	// perform a load on the target only if dataType is not provided
	if (!options.dataType && options.target) {
		var oldSuccess = options.success || function(){};
		callbacks.push(function(data) {
			var fn = options.replaceTarget ? 'replaceWith' : 'html';
			$(options.target)[fn](data).each(oldSuccess, arguments);
		});
	}
	else if (options.success) {
		callbacks.push(options.success);
	}

	options.success = function(data, status, xhr) { // jQuery 1.4+ passes xhr as 3rd arg
		var context = options.context || options;   // jQuery 1.4+ supports scope context 
		for (var i=0, max=callbacks.length; i < max; i++) {
			callbacks[i].apply(context, [data, status, xhr || $form, $form]);
		}
	};

	// are there files to upload?
	var fileInputs = $('input:file', this).length > 0;
	var mp = 'multipart/form-data';
	var multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

	// options.iframe allows user to force iframe mode
	// 06-NOV-09: now defaulting to iframe mode if file input is detected
   if (options.iframe !== false && (fileInputs || options.iframe || multipart)) {
	   // hack to fix Safari hang (thanks to Tim Molendijk for this)
	   // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
	   if (options.closeKeepAlive) {
		   $.get(options.closeKeepAlive, function() { fileUpload(a); });
		}
	   else {
		   fileUpload(a);
		}
   }
   else {
		// IE7 massage (see issue 57)
		if ($.browser.msie && method == 'get') { 
			var ieMeth = $form[0].getAttribute('method');
			if (typeof ieMeth === 'string')
				options.type = ieMeth;
		}
		$.ajax(options);
   }

	// fire 'notify' event
	this.trigger('form-submit-notify', [this, options]);
	return this;


	// private function for handling file uploads (hat tip to YAHOO!)
	function fileUpload(a) {
		var form = $form[0], el, i, s, g, id, $io, io, xhr, sub, n, timedOut, timeoutHandle;
        var useProp = !!$.fn.prop;

        if (a) {
        	// ensure that every serialized input is still enabled
          	for (i=0; i < a.length; i++) {
                el = $(form[a[i].name]);
                el[ useProp ? 'prop' : 'attr' ]('disabled', false);
          	}
        }

		if ($(':input[name=submit],:input[id=submit]', form).length) {
			// if there is an input with a name or id of 'submit' then we won't be
			// able to invoke the submit fn on the form (at least not x-browser)
			alert('Error: Form elements must not have name or id of "submit".');
			return;
		}
		
		s = $.extend(true, {}, $.ajaxSettings, options);
		s.context = s.context || s;
		id = 'jqFormIO' + (new Date().getTime());
		if (s.iframeTarget) {
			$io = $(s.iframeTarget);
			n = $io.attr('name');
			if (n == null)
			 	$io.attr('name', id);
			else
				id = n;
		}
		else {
			$io = $('<iframe name="' + id + '" src="'+ s.iframeSrc +'" />');
			$io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });
		}
		io = $io[0];


		xhr = { // mock object
			aborted: 0,
			responseText: null,
			responseXML: null,
			status: 0,
			statusText: 'n/a',
			getAllResponseHeaders: function() {},
			getResponseHeader: function() {},
			setRequestHeader: function() {},
			abort: function(status) {
				var e = (status === 'timeout' ? 'timeout' : 'aborted');
				log('aborting upload... ' + e);
				this.aborted = 1;
				$io.attr('src', s.iframeSrc); // abort op in progress
				xhr.error = e;
				s.error && s.error.call(s.context, xhr, e, status);
				g && $.event.trigger("ajaxError", [xhr, s, e]);
				s.complete && s.complete.call(s.context, xhr, e);
			}
		};

		g = s.global;
		// trigger ajax global events so that activity/block indicators work like normal
		if (g && ! $.active++) {
			$.event.trigger("ajaxStart");
		}
		if (g) {
			$.event.trigger("ajaxSend", [xhr, s]);
		}

		if (s.beforeSend && s.beforeSend.call(s.context, xhr, s) === false) {
			if (s.global) {
				$.active--;
			}
			return;
		}
		if (xhr.aborted) {
			return;
		}

		// add submitting element to data if we know it
		sub = form.clk;
		if (sub) {
			n = sub.name;
			if (n && !sub.disabled) {
				s.extraData = s.extraData || {};
				s.extraData[n] = sub.value;
				if (sub.type == "image") {
					s.extraData[n+'.x'] = form.clk_x;
					s.extraData[n+'.y'] = form.clk_y;
				}
			}
		}
		
		var CLIENT_TIMEOUT_ABORT = 1;
		var SERVER_ABORT = 2;

		function getDoc(frame) {
			var doc = frame.contentWindow ? frame.contentWindow.document : frame.contentDocument ? frame.contentDocument : frame.document;
			return doc;
		}
		
		// take a breath so that pending repaints get some cpu time before the upload starts
		function doSubmit() {
			// make sure form attrs are set
			var t = $form.attr('target'), a = $form.attr('action');

			// update form attrs in IE friendly way
			form.setAttribute('target',id);
			if (!method) {
				form.setAttribute('method', 'POST');
			}
			if (a != s.url) {
				form.setAttribute('action', s.url);
			}

			// ie borks in some cases when setting encoding
			if (! s.skipEncodingOverride && (!method || /post/i.test(method))) {
				$form.attr({
					encoding: 'multipart/form-data',
					enctype:  'multipart/form-data'
				});
			}

			// support timout
			if (s.timeout) {
				timeoutHandle = setTimeout(function() { timedOut = true; cb(CLIENT_TIMEOUT_ABORT); }, s.timeout);
			}
			
			// look for server aborts
			function checkState() {
				try {
					var state = getDoc(io).readyState;
					log('state = ' + state);
					if (state.toLowerCase() == 'uninitialized')
						setTimeout(checkState,50);
				}
				catch(e) {
					log('Server abort: ' , e, ' (', e.name, ')');
					cb(SERVER_ABORT);
					timeoutHandle && clearTimeout(timeoutHandle);
					timeoutHandle = undefined;
				}
			}

			// add "extra" data to form if provided in options
			var extraInputs = [];
			try {
				if (s.extraData) {
					for (var n in s.extraData) {
						extraInputs.push(
							$('<input type="hidden" name="'+n+'" />').attr('value',s.extraData[n])
								.appendTo(form)[0]);
					}
				}

				if (!s.iframeTarget) {
					// add iframe to doc and submit the form
					$io.appendTo('body');
	                io.attachEvent ? io.attachEvent('onload', cb) : io.addEventListener('load', cb, false);
				}
				setTimeout(checkState,15);
				form.submit();
			}
			finally {
				// reset attrs and remove "extra" input elements
				form.setAttribute('action',a);
				if(t) {
					form.setAttribute('target', t);
				} else {
					$form.removeAttr('target');
				}
				$(extraInputs).remove();
			}
		}

		if (s.forceSync) {
			doSubmit();
		}
		else {
			setTimeout(doSubmit, 10); // this lets dom updates render
		}

		var data, doc, domCheckCount = 50, callbackProcessed;

		function cb(e) {
			if (xhr.aborted || callbackProcessed) {
				return;
			}
			try {
				doc = getDoc(io);
			}
			catch(ex) {
				log('cannot access response document: ', ex);
				e = SERVER_ABORT;
			}
			if (e === CLIENT_TIMEOUT_ABORT && xhr) {
				xhr.abort('timeout');
				return;
			}
			else if (e == SERVER_ABORT && xhr) {
				xhr.abort('server abort');
				return;
			}

			if (!doc || doc.location.href == s.iframeSrc) {
				// response not received yet
				if (!timedOut)
					return;
			}
            io.detachEvent ? io.detachEvent('onload', cb) : io.removeEventListener('load', cb, false);

			var status = 'success', errMsg;
			try {
				if (timedOut) {
					throw 'timeout';
				}

				var isXml = s.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
				log('isXml='+isXml);
				if (!isXml && window.opera && (doc.body == null || doc.body.innerHTML == '')) {
					if (--domCheckCount) {
						// in some browsers (Opera) the iframe DOM is not always traversable when
						// the onload callback fires, so we loop a bit to accommodate
						log('requeing onLoad callback, DOM not available');
						setTimeout(cb, 250);
						return;
					}
					// let this fall through because server response could be an empty document
					//log('Could not access iframe DOM after mutiple tries.');
					//throw 'DOMException: not available';
				}

				//log('response detected');
                var docRoot = doc.body ? doc.body : doc.documentElement;
                xhr.responseText = docRoot ? docRoot.innerHTML : null;
				xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
				if (isXml)
					s.dataType = 'xml';
				xhr.getResponseHeader = function(header){
					var headers = {'content-type': s.dataType};
					return headers[header];
				};
                // support for XHR 'status' & 'statusText' emulation :
                if (docRoot) {
                    xhr.status = Number( docRoot.getAttribute('status') ) || xhr.status;
                    xhr.statusText = docRoot.getAttribute('statusText') || xhr.statusText;
                }

				var dt = s.dataType || '';
				var scr = /(json|script|text)/.test(dt.toLowerCase());
				if (scr || s.textarea) {
					// see if user embedded response in textarea
					var ta = doc.getElementsByTagName('textarea')[0];
					if (ta) {
						xhr.responseText = ta.value;
                        // support for XHR 'status' & 'statusText' emulation :
                        xhr.status = Number( ta.getAttribute('status') ) || xhr.status;
                        xhr.statusText = ta.getAttribute('statusText') || xhr.statusText;
					}
					else if (scr) {
						// account for browsers injecting pre around json response
						var pre = doc.getElementsByTagName('pre')[0];
						var b = doc.getElementsByTagName('body')[0];
						if (pre) {
							xhr.responseText = pre.textContent ? pre.textContent : pre.innerHTML;
						}
						else if (b) {
							xhr.responseText = b.innerHTML;
						}
					}
				}
				else if (s.dataType == 'xml' && !xhr.responseXML && xhr.responseText != null) {
					xhr.responseXML = toXml(xhr.responseText);
				}

                try {
                    data = httpData(xhr, s.dataType, s);
                }
                catch (e) {
                    status = 'parsererror';
                    xhr.error = errMsg = (e || status);
                }
			}
			catch (e) {
				log('error caught: ',e);
				status = 'error';
                xhr.error = errMsg = (e || status);
			}

			if (xhr.aborted) {
				log('upload aborted');
				status = null;
			}

            if (xhr.status) { // we've set xhr.status
                status = (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) ? 'success' : 'error';
            }

			// ordering of these callbacks/triggers is odd, but that's how $.ajax does it
			if (status === 'success') {
				s.success && s.success.call(s.context, data, 'success', xhr);
				g && $.event.trigger("ajaxSuccess", [xhr, s]);
			}
            else if (status) {
				if (errMsg == undefined)
					errMsg = xhr.statusText;
				s.error && s.error.call(s.context, xhr, status, errMsg);
				g && $.event.trigger("ajaxError", [xhr, s, errMsg]);
            }

			g && $.event.trigger("ajaxComplete", [xhr, s]);

			if (g && ! --$.active) {
				$.event.trigger("ajaxStop");
			}

			s.complete && s.complete.call(s.context, xhr, status);

			callbackProcessed = true;
			if (s.timeout)
				clearTimeout(timeoutHandle);

			// clean up
			setTimeout(function() {
				if (!s.iframeTarget)
					$io.remove();
				xhr.responseXML = null;
			}, 100);
		}

		var toXml = $.parseXML || function(s, doc) { // use parseXML if available (jQuery 1.5+)
			if (window.ActiveXObject) {
				doc = new ActiveXObject('Microsoft.XMLDOM');
				doc.async = 'false';
				doc.loadXML(s);
			}
			else {
				doc = (new DOMParser()).parseFromString(s, 'text/xml');
			}
			return (doc && doc.documentElement && doc.documentElement.nodeName != 'parsererror') ? doc : null;
		};
		var parseJSON = $.parseJSON || function(s) {
			return window['eval']('(' + s + ')');
		};

		var httpData = function( xhr, type, s ) { // mostly lifted from jq1.4.4

			var ct = xhr.getResponseHeader('content-type') || '',
				xml = type === 'xml' || !type && ct.indexOf('xml') >= 0,
				data = xml ? xhr.responseXML : xhr.responseText;

			if (xml && data.documentElement.nodeName === 'parsererror') {
				$.error && $.error('parsererror');
			}
			if (s && s.dataFilter) {
				data = s.dataFilter(data, type);
			}
			if (typeof data === 'string') {
				if (type === 'json' || !type && ct.indexOf('json') >= 0) {
					data = parseJSON(data);
				} else if (type === "script" || !type && ct.indexOf("javascript") >= 0) {
					$.globalEval(data);
				}
			}
			return data;
		};
	}
};

/**
 * ajaxForm() provides a mechanism for fully automating form submission.
 *
 * The advantages of using this method instead of ajaxSubmit() are:
 *
 * 1: This method will include coordinates for <input type="image" /> elements (if the element
 *	is used to submit the form).
 * 2. This method will include the submit element's name/value data (for the element that was
 *	used to submit the form).
 * 3. This method binds the submit() method to the form for you.
 *
 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
 * passes the options argument along after properly binding events for submit elements and
 * the form itself.
 */
$.fn.ajaxForm = function(options) {
	// in jQuery 1.3+ we can fix mistakes with the ready state
	if (this.length === 0) {
		var o = { s: this.selector, c: this.context };
		if (!$.isReady && o.s) {
			log('DOM not ready, queuing ajaxForm');
			$(function() {
				$(o.s,o.c).ajaxForm(options);
			});
			return this;
		}
		// is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
		log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
		return this;
	}

	return this.ajaxFormUnbind().bind('submit.form-plugin', function(e) {
		if (!e.isDefaultPrevented()) { // if event has been canceled, don't proceed
			e.preventDefault();
			$(this).ajaxSubmit(options);
		}
	}).bind('click.form-plugin', function(e) {
		var target = e.target;
		var $el = $(target);
		if (!($el.is(":submit,input:image"))) {
			// is this a child element of the submit el?  (ex: a span within a button)
			var t = $el.closest(':submit');
			if (t.length == 0) {
				return;
			}
			target = t[0];
		}
		var form = this;
		form.clk = target;
		if (target.type == 'image') {
			if (e.offsetX != undefined) {
				form.clk_x = e.offsetX;
				form.clk_y = e.offsetY;
			} else if (typeof $.fn.offset == 'function') { // try to use dimensions plugin
				var offset = $el.offset();
				form.clk_x = e.pageX - offset.left;
				form.clk_y = e.pageY - offset.top;
			} else {
				form.clk_x = e.pageX - target.offsetLeft;
				form.clk_y = e.pageY - target.offsetTop;
			}
		}
		// clear form vars
		setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 100);
	});
};

// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
$.fn.ajaxFormUnbind = function() {
	return this.unbind('submit.form-plugin click.form-plugin');
};

/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example of
 * an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to the
 * ajaxSubmit() and ajaxForm() methods.
 */
$.fn.formToArray = function(semantic) {
	var a = [];
	if (this.length === 0) {
		return a;
	}

	var form = this[0];
	var els = semantic ? form.getElementsByTagName('*') : form.elements;
	if (!els) {
		return a;
	}

	var i,j,n,v,el,max,jmax;
	for(i=0, max=els.length; i < max; i++) {
		el = els[i];
		n = el.name;
		if (!n) {
			continue;
		}

		if (semantic && form.clk && el.type == "image") {
			// handle image inputs on the fly when semantic == true
			if(!el.disabled && form.clk == el) {
				a.push({name: n, value: $(el).val()});
				a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
			}
			continue;
		}

		v = $.fieldValue(el, true);
		if (v && v.constructor == Array) {
			for(j=0, jmax=v.length; j < jmax; j++) {
				a.push({name: n, value: v[j]});
			}
		}
		else if (v !== null && typeof v != 'undefined') {
			a.push({name: n, value: v});
		}
	}

	if (!semantic && form.clk) {
		// input type=='image' are not found in elements array! handle it here
		var $input = $(form.clk), input = $input[0];
		n = input.name;
		if (n && !input.disabled && input.type == 'image') {
			a.push({name: n, value: $input.val()});
			a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
		}
	}
	return a;
};

/**
 * Serializes form data into a 'submittable' string. This method will return a string
 * in the format: name1=value1&amp;name2=value2
 */
$.fn.formSerialize = function(semantic) {
	//hand off to jQuery.param for proper encoding
	return $.param(this.formToArray(semantic));
};

/**
 * Serializes all field elements in the jQuery object into a query string.
 * This method will return a string in the format: name1=value1&amp;name2=value2
 */
$.fn.fieldSerialize = function(successful) {
	var a = [];
	this.each(function() {
		var n = this.name;
		if (!n) {
			return;
		}
		var v = $.fieldValue(this, successful);
		if (v && v.constructor == Array) {
			for (var i=0,max=v.length; i < max; i++) {
				a.push({name: n, value: v[i]});
			}
		}
		else if (v !== null && typeof v != 'undefined') {
			a.push({name: this.name, value: v});
		}
	});
	//hand off to jQuery.param for proper encoding
	return $.param(a);
};

/**
 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
 *
 *  <form><fieldset>
 *	  <input name="A" type="text" />
 *	  <input name="A" type="text" />
 *	  <input name="B" type="checkbox" value="B1" />
 *	  <input name="B" type="checkbox" value="B2"/>
 *	  <input name="C" type="radio" value="C1" />
 *	  <input name="C" type="radio" value="C2" />
 *  </fieldset></form>
 *
 *  var v = $(':text').fieldValue();
 *  // if no values are entered into the text inputs
 *  v == ['','']
 *  // if values entered into the text inputs are 'foo' and 'bar'
 *  v == ['foo','bar']
 *
 *  var v = $(':checkbox').fieldValue();
 *  // if neither checkbox is checked
 *  v === undefined
 *  // if both checkboxes are checked
 *  v == ['B1', 'B2']
 *
 *  var v = $(':radio').fieldValue();
 *  // if neither radio is checked
 *  v === undefined
 *  // if first radio is checked
 *  v == ['C1']
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If this value is false the value(s)
 * for each element is returned.
 *
 * Note: This method *always* returns an array.  If no valid value can be determined the
 *	   array will be empty, otherwise it will contain one or more values.
 */
$.fn.fieldValue = function(successful) {
	for (var val=[], i=0, max=this.length; i < max; i++) {
		var el = this[i];
		var v = $.fieldValue(el, successful);
		if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length)) {
			continue;
		}
		v.constructor == Array ? $.merge(val, v) : val.push(v);
	}
	return val;
};

/**
 * Returns the value of the field element.
 */
$.fieldValue = function(el, successful) {
	var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
	if (successful === undefined) {
		successful = true;
	}

	if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
		(t == 'checkbox' || t == 'radio') && !el.checked ||
		(t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
		tag == 'select' && el.selectedIndex == -1)) {
			return null;
	}

	if (tag == 'select') {
		var index = el.selectedIndex;
		if (index < 0) {
			return null;
		}
		var a = [], ops = el.options;
		var one = (t == 'select-one');
		var max = (one ? index+1 : ops.length);
		for(var i=(one ? index : 0); i < max; i++) {
			var op = ops[i];
			if (op.selected) {
				var v = op.value;
				if (!v) { // extra pain for IE...
					v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
				}
				if (one) {
					return v;
				}
				a.push(v);
			}
		}
		return a;
	}
	return $(el).val();
};

/**
 * Clears the form data.  Takes the following actions on the form's input fields:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 */
$.fn.clearForm = function() {
	return this.each(function() {
		$('input,select,textarea', this).clearFields();
	});
};

/**
 * Clears the selected form elements.
 */
$.fn.clearFields = $.fn.clearInputs = function() {
	var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
	return this.each(function() {
		var t = this.type, tag = this.tagName.toLowerCase();
		if (re.test(t) || tag == 'textarea') {
			this.value = '';
		}
		else if (t == 'checkbox' || t == 'radio') {
			this.checked = false;
		}
		else if (tag == 'select') {
			this.selectedIndex = -1;
		}
	});
};

/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 */
$.fn.resetForm = function() {
	return this.each(function() {
		// guard against an input with the name of 'reset'
		// note that IE reports the reset function as an 'object'
		if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
			this.reset();
		}
	});
};

/**
 * Enables or disables any matching elements.
 */
$.fn.enable = function(b) {
	if (b === undefined) {
		b = true;
	}
	return this.each(function() {
		this.disabled = !b;
	});
};

/**
 * Checks/unchecks any matching checkboxes or radio buttons and
 * selects/deselects and matching option elements.
 */
$.fn.selected = function(select) {
	if (select === undefined) {
		select = true;
	}
	return this.each(function() {
		var t = this.type;
		if (t == 'checkbox' || t == 'radio') {
			this.checked = select;
		}
		else if (this.tagName.toLowerCase() == 'option') {
			var $sel = $(this).parent('select');
			if (select && $sel[0] && $sel[0].type == 'select-one') {
				// deselect all other options
				$sel.find('option').selected(false);
			}
			this.selected = select;
		}
	});
};

// helper fn for console logging
function log() {
	var msg = '[jquery.form] ' + Array.prototype.join.call(arguments,'');
	if (window.console && window.console.log) {
		window.console.log(msg);
	}
	else if (window.opera && window.opera.postError) {
		window.opera.postError(msg);
	}
};

})(jQuery);
;
/*
 *jQuery browser plugin detection 1.0.3
 * http://plugins.jquery.com/project/jqplugin
 * Checks for plugins / mimetypes supported in the browser extending the jQuery.browser object
 * Copyright (c) 2008 Leonardo Rossetti motw.leo@gmail.com
 * MIT License: http://www.opensource.org/licenses/mit-license.php
   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
   THE SOFTWARE.
*/
(function ($) {
	//$.getScript("http://java.com/js/deployJava.js");

	//checks if browser object exists
	if (typeof $.browser === "undefined" || !$.browser) {
		var browser = {};
		$.extend(browser);
	}
	var pluginList = {
		flash: {
			activex: ["ShockwaveFlash.ShockwaveFlash", "ShockwaveFlash.ShockwaveFlash.3", "ShockwaveFlash.ShockwaveFlash.4", "ShockwaveFlash.ShockwaveFlash.5", "ShockwaveFlash.ShockwaveFlash.6", "ShockwaveFlash.ShockwaveFlash.7"],
			plugin: /flash/gim
		},
		sl: {
			activex: ["AgControl.AgControl"],
			plugin: /silverlight/gim
		},
		pdf: {
			activex: ["acroPDF.PDF.1", "PDF.PdfCtrl.1", "PDF.PdfCtrl.4", "PDF.PdfCtrl.5", "PDF.PdfCtrl.6"],
			plugin: /adobe\s?acrobat/gim
		},
		qtime: {
			activex: ["QuickTime.QuickTime", "QuickTimeCheckObject.QuickTimeCheck.1", "QuickTime.QuickTime.4"],
			plugin: /quicktime/gim
		},
		wmp: {
			activex: ["WMPlayer.OCX", "MediaPlayer.MediaPlayer.1"],
			plugin: /(windows\smedia)|(Microsoft)/gim
		},
		shk: {
			activex: ["SWCtl.SWCtl", "SWCt1.SWCt1.7", "SWCt1.SWCt1.8", "SWCt1.SWCt1.9", "ShockwaveFlash.ShockwaveFlash.1"],
			plugin: /shockwave/gim
		},
		rp: {
			activex: ["RealPlayer", "rmocx.RealPlayer G2 Control.1"],
			plugin: /realplayer/gim
		}
	};
	var isSupported = function (p) {
		if (window.ActiveXObject) {
			$.browser[p] = false;
			
			for (i = 0; i < pluginList[p].activex.length; i++) {
				try {
					new ActiveXObject(pluginList[p].activex[i]);
					$.browser[p] = true;
				} catch (e) {}	
			}
		} else {
			$.each(navigator.plugins, function () {
				if (this.name.match(pluginList[p].plugin)) {
					$.browser[p] = true;
					return false;
				} else {
					$.browser[p] = false;
				}
			});
		}
	};
	
	$.each(pluginList, function (i, n) {
		isSupported(i);
	});
	//uses sun script to detect if java plugin is available
	/*
	$.getScript("http://java.com/js/deployJava.js", function () {
		if (deployJava.versionCheck("1.6.0+") || deployJava.versionCheck("1.4") || deployJava.versionCheck("1.5.0*")) {
			$.browser.java = true;
		} else {
			$.browser.java = false;
		}
	});
	*/
})(jQuery);
/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.cookie = function (key, value, options) {

    // key and at least value given, set cookie...
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);

        if (value === null || value === undefined) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }

        value = String(value);

        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : encodeURIComponent(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};
;
(function($){
	$.fn.serializeJSON=function() {
		var json = {};
		jQuery.map($(this).serializeArray(), function(n, i){
			json[n['name']] = n['value'];
		});
		return json;
	};
})(jQuery);
;(function($) {

jQuery.fn.choose = function(f) {
	$(this).bind('choose', f);
};


jQuery.fn.file = function(options) {
	var config = $.extend({
		name: 'file-upload'
	}, options);
	return this.each(function() {
		var btn = $(this);
		var pos = btn.offset();

		function update() {
			pos = btn.offset();
			file.css({
				'top': pos.top,
				'left': pos.left,
				'width': btn.width(),
				'height': btn.height()
			});
		}

		btn.mouseover(update);

		var hidden = $('<div></div>').css({
			'display': 'none'
		}).appendTo('body');

		var file = $('<div id="area-form-file-'+config.name+'"><form></form></div>').appendTo('body').css({
			'position': 'absolute',
			'overflow': 'hidden',
			'-moz-opacity': '0',
			'filter':  'alpha(opacity: 0)',
			'opacity': '0',
			'cursor': 'pointer',
			'z-index': '2'
		});

		var form = file.find('form');
		var input = form.find('input');

		function reset() {
			var input = $('<input type="file" multiple>').attr('name',config.name).css({'cursor':'pointer'}).appendTo(form);
			input.change(function(e) {
				input.unbind();
				input.detach();
				btn.trigger('choose', [input]);
				reset();
			});
		};

		reset();

		function placer(e) {
			form.css('margin-left', e.pageX - pos.left - offset.width);
			form.css('margin-top', e.pageY - pos.top - offset.height + 3);
		}

		function redirect(name) {
			file[name](function(e) {
				btn.trigger(name);
			});
		}

		file.mousemove(placer);
		btn.mousemove(placer);

		redirect('mouseover');
		redirect('mouseout');
		redirect('mousedown');
		redirect('mouseup');

		var offset = {
			width: file.width() - 25,
			height: file.height() / 2
		};

		update();
	});
};

})(jQuery);
/*! http://mths.be/placeholder v2.0.6 by @mathias */
;(function(g,i,d){var a='placeholder' in i.createElement('input'),e='placeholder' in i.createElement('textarea'),j=d.fn,c=d.valHooks,l,k;if(a&&e){k=j.placeholder=function(){return this};k.input=k.textarea=true}else{k=j.placeholder=function(){var m=this;m.filter((a?'textarea':':input')+'[placeholder]').not('.placeholder').bind({'focus.placeholder':b,'blur.placeholder':f}).data('placeholder-enabled',true).trigger('blur.placeholder');return m};k.input=a;k.textarea=e;l={get:function(n){var m=d(n);return m.data('placeholder-enabled')&&m.hasClass('placeholder')?'':n.value},set:function(n,o){var m=d(n);if(!m.data('placeholder-enabled')){return n.value=o}if(o==''){n.value=o;if(n!=i.activeElement){f.call(n)}}else{if(m.hasClass('placeholder')){b.call(n,true,o)||(n.value=o)}else{n.value=o}}return m}};a||(c.input=l);e||(c.textarea=l);d(function(){d(i).delegate('form','submit.placeholder',function(){var m=d('.placeholder',this).each(b);setTimeout(function(){m.each(f)},10)})});d(g).bind('beforeunload.placeholder',function(){d('.placeholder').each(function(){this.value=''})})}function h(n){var m={},o=/^jQuery\d+$/;d.each(n.attributes,function(q,p){if(p.specified&&!o.test(p.name)){m[p.name]=p.value}});return m}function b(o,p){var n=this,q=d(n),m;if(n.value==q.attr('placeholder')&&q.hasClass('placeholder')){m=n==i.activeElement;if(q.data('placeholder-password')){q=q.hide().next().show().attr('id',q.removeAttr('id').data('placeholder-id'));if(o===true){return q[0].value=p}q.focus()}else{n.value='';q.removeClass('placeholder')}m&&n.select()}}function f(){var r,m=this,q=d(m),n=q,p=this.id;if(m.value==''){if(m.type=='password'){if(!q.data('placeholder-textinput')){try{r=q.clone().attr({type:'text'})}catch(o){r=d('<input>').attr(d.extend(h(this),{type:'text'}))}r.removeAttr('name').data({'placeholder-password':true,'placeholder-id':p}).bind('focus.placeholder',b);q.data({'placeholder-textinput':r,'placeholder-id':p}).before(r)}q=q.removeAttr('id').hide().prev().attr('id',p).show()}q.addClass('placeholder');q[0].value=q.attr('placeholder')}else{q.removeClass('placeholder')}}}(this,document,jQuery));
/*
 * 	Character Count Plugin - jQuery plugin
 * 	Dynamic character count for text areas and input fields
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/7161/jquery-plugin-simplest-twitterlike-dynamic-character-count-for-textareas
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
(function($) {

	$.fn.charCount = function(options){
	  
		// default configuration properties
		var defaults = {	
			allowed: 140,		
			warning: 25,
			css: 'counter',
			counterElement: 'span',
			cssWarning: 'warning',
			cssExceeded: 'exceeded',
			counterText: ''
		}; 
			
		var options = $.extend(defaults, options); 
		
		function calculate(obj){
			var count = $(obj).val().length;
			var available = options.allowed - count;
			if(available <= options.warning && available >= 0){
				$(obj).next().addClass(options.cssWarning);
			} else {
				$(obj).next().removeClass(options.cssWarning);
			}
			if(available < 0){
				$(obj).next().addClass(options.cssExceeded);
			} else {
				$(obj).next().removeClass(options.cssExceeded);
			}
			$(obj).next().html(options.counterText + available);
		};
				
		this.each(function() {  			
			$(this).after('<'+ options.counterElement +' class="' + options.css + '">'+ options.counterText +'</'+ options.counterElement +'>');
			calculate(this);
			$(this).keyup(function(){calculate(this)});
			$(this).change(function(){calculate(this)});
		});
	  
	};

})(jQuery);
;
Function.prototype.bind = function(context) {
	var fn = this;
	if(jQuery.type(fn) != 'function'){
		throw new TypeError('Function.prototype.bind: call on non-function');
	};
	if(jQuery.type(context) == 'null'){
		throw new TypeError('Function.prototype.bind: cant be bound to null');
	};
	return function() {
		return fn.apply(context, arguments);
	};
};
String.prototype.tr = function(a,p) {
	var k;
	var p = typeof(p)=='string' ? p : '';
	var s = this;
	jQuery.each(a,function(k){
		var tk = p?p.split('/'):[];
		tk[tk.length] = k;
		var tp = tk.join('/');
		if(typeof(a[k])=='object'){
			s = s.tr(a[k],tp);
		}else{
			s = s.replace((new RegExp('%%'+tp+'%%', 'g')), a[k]);
		};
	});
	return s;
};


var ls = ls || {};

/**
 * Управление всплывающими сообщениями
 */
ls.msg = (function ($) {
	/**
	* Опции
	*/
	this.options = {
		class_notice: 'n-notice',
		class_error: 'n-error'
	};

	/**
	* Отображение информационного сообщения
	*/
	this.notice = function(title,msg){
		$.notifier.broadcast(title, msg, this.options.class_notice);
	};

	/**
	* Отображение сообщения об ошибке
	*/
	this.error = function(title,msg){
		$.notifier.broadcast(title, msg, this.options.class_error);
	};
    this.notify = function(title, body) {
        if (title && body) {
            // Давайте проверим, поддерживает ли браузер уведомления
            if (!("Notification" in window)) {
                alert("Ваш браузер не поддерживает HTML5 Notifications");
            }
            // Теперь давайте проверим есть ли у нас разрешение для отображения уведомления
             
            else if (Notification.permission === "granted") {
                // Если все в порядке, то создадим уведомление
                var notification1 = new Notification(title,{
                    'body': body,
                });
            }
            // В противном случае, мы должны спросить у пользователя разрешение
             
            else if (Notification.permission === 'default') {
                Notification.requestPermission(function(permission) {
                    
                    // Не зависимо от ответа, сохраняем его в настройках
                    if (!('permission' in Notification)) {
                        Notification.permission = permission;
                    }
                    // Если разрешение получено, то создадим уведомление
                    if (permission === "granted") {
                        var notification1 = new Notification(title,{
                            'body': body,
                        });
                    }
                }
                );
            }
        }
    }
	return this;
}).call(ls.msg || {},jQuery);


/**
* Доступ к языковым текстовкам (предварительно должны быть прогружены в шаблон)
*/
ls.lang = (function ($) {
	/**
	* Набор текстовок
	*/
	this.msgs = {};

	/**
	* Загрузка текстовок
	*/
	this.load = function(msgs){
		$.extend(true,this.msgs,msgs);
	};

	/**
	* Отображение сообщения об ошибке
	*/
	this.get = function(name,replace){
		if (this.msgs[name]) {
			var value=this.msgs[name];
			if (replace) {
				value = value.tr(replace);
			}
			return value;
		}
		return '';
	};

	return this;
}).call(ls.lang || {},jQuery);

/**
 * Методы таймера например, запуск функии через интервал
 */
ls.timer = (function ($) {

	this.aTimers={};

	/**
	 * Запуск метода через определенный период, поддерживает пролонгацию
	 */
	this.run = function(fMethod,sUniqKey,aParams,iTime){
		iTime=iTime || 1500;
		aParams=aParams || [];
		sUniqKey=sUniqKey || Math.random();

		if (this.aTimers[sUniqKey]) {
			clearTimeout(this.aTimers[sUniqKey]);
			this.aTimers[sUniqKey]=null;
		}
		var timeout = setTimeout(function(){
			clearTimeout(this.aTimers[sUniqKey]);
			this.aTimers[sUniqKey]=null;
			fMethod.apply(this,aParams);
		}.bind(this),iTime);
		this.aTimers[sUniqKey]=timeout;
	};

	return this;
}).call(ls.timer || {},jQuery);

/**
 * Функционал хранения js данных
 */
ls.registry = (function ($) {

	this.aData={};

	/**
	 * Сохранение
	 */
	this.set = function(sName,data){
		this.aData[sName]=data;
	};

	/**
	 * Получение
	 */
	this.get = function(sName){
		return this.aData[sName];
	};

	return this;
}).call(ls.registry || {},jQuery);

/**
* Flash загрузчик
*/
ls.swfupload = (function ($) {

	this.swfu = null;
	this.swfOptions = {};

	this.initOptions = function() {
		
		this.swfOptions = {
			// Backend Settings
			upload_url: aRouter['photoset']+"upload",
			post_params: {'SSID':SESSION_ID, 'security_ls_key': LIVESTREET_SECURITY_KEY},

			// File Upload Settings
			file_types : "*.jpg;*.jpe;*.jpeg;*.png;*.gif;*.JPG;*.JPE;*.JPEG;*.PNG;*.GIF",
			file_types_description : "Images",
			file_upload_limit : "0",

			// Event Handler Settings
			file_queue_error_handler : this.handlerFileQueueError,
			file_dialog_complete_handler : this.handlerFileDialogComplete,
			upload_progress_handler : this.handlerUploadProgress,
			upload_error_handler : this.handlerUploadError,
			upload_success_handler : this.handlerUploadSuccess,
			upload_complete_handler : this.handlerUploadComplete,

			// Button Settings
			button_placeholder_id : "start-upload",
			button_width: 122,
			button_height: 30,
			button_text : '<span class="button">'+ls.lang.get('topic_photoset_upload_choose')+'</span>',
			button_text_style : '.button { color: #1F8AB7; font-size: 14px; }',
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_text_left_padding: 6,
			button_text_top_padding: 3,
			button_cursor: SWFUpload.CURSOR.HAND,

			// Flash Settings
			flash_url : DIR_ROOT_ENGINE_LIB+'/external/swfupload/swfupload.swf',

			custom_settings : {
			},

			// Debug Settings
			debug: false
		};
		
		ls.hook.run('ls_swfupload_init_options_after',arguments,this.swfOptions);
		
	};

	this.loadSwf = function() {
		var f = {};
		
		f.onSwfobject = function(){
			if(window.swfobject && swfobject.swfupload){
				f.onSwfobjectSwfupload();
			}else{
				ls.debug('window.swfobject && swfobject.swfupload is undefined, load swfobject/plugin/swfupload.js');
				$.getScript(
					DIR_ROOT_ENGINE_LIB+'/external/swfobject/plugin/swfupload.js',
					f.onSwfobjectSwfupload
				);
			}
		}.bind(this);
		
		f.onSwfobjectSwfupload = function(){
			if(window.SWFUpload){
				f.onSwfupload();
			}else{
				ls.debug('window.SWFUpload is undefined, load swfupload/swfupload.js');
				$.getScript(
					DIR_ROOT_ENGINE_LIB+'/external/swfupload/swfupload.js',
					f.onSwfupload
				);
			}
		}.bind(this);
		
		f.onSwfupload = function(){
			this.initOptions();
			$(this).trigger('load');
		}.bind(this);
		
		
		(function(){
			if(window.swfobject){
				f.onSwfobject();
			}else{
				ls.debug('window.swfobject is undefined, load swfobject/swfobject.js');
				$.getScript(
					DIR_ROOT_ENGINE_LIB+'/external/swfobject/swfobject.js',
					f.onSwfobject
				);
			}
		}.bind(this))();
	};
	

	this.init = function(opt) {
		if (opt) {
			$.extend(true,this.swfOptions,opt);
		}
		this.swfu = new SWFUpload(this.swfOptions);
		return this.swfu;
	};

	this.handlerFileQueueError = function(file, errorCode, message) {
		$(this).trigger('eFileQueueError',[file, errorCode, message]);
	};

	this.handlerFileDialogComplete = function(numFilesSelected, numFilesQueued) {
		$(this).trigger('eFileDialogComplete',[numFilesSelected, numFilesQueued]);
		if (numFilesQueued>0) {
			this.startUpload();
		}
	};

	this.handlerUploadProgress = function(file, bytesLoaded) {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);
		$(this).trigger('eUploadProgress',[file, bytesLoaded, percent]);
	};

	this.handlerUploadError = function(file, errorCode, message) {
		$(this).trigger('eUploadError',[file, errorCode, message]);
	};

	this.handlerUploadSuccess = function(file, serverData) {
		$(this).trigger('eUploadSuccess',[file, serverData]);
	};

	this.handlerUploadComplete = function(file) {
		var next = this.getStats().files_queued;
		if (next > 0) {
			this.startUpload();
		}
		$(this).trigger('eUploadComplete',[file, next]);
	};

	return this;
}).call(ls.swfupload || {},jQuery);


/**
* Вспомогательные функции
*/
ls.tools = (function ($) {

	/**
	* Переводит первый символ в верхний регистр
	*/
	this.ucfirst = function(str) {
		var f = str.charAt(0).toUpperCase();
		return f + str.substr(1, str.length-1);
	};

	/**
	* Выделяет все chekbox с определенным css классом
	*/
	this.checkAll = function(cssclass, checkbox, invert) {
		$('.'+cssclass).each(function(index, item){
			if (invert) {
				$(item).attr('checked', !$(item).attr("checked"));
			} else {
				$(item).attr('checked', $(checkbox).attr("checked"));
			}
		});
	};

	/**
	* Предпросмотр
	*/
	this.textPreview = function(textId, save, divPreview) {
		var text =(BLOG_USE_TINYMCE) ? tinyMCE.activeEditor.getContent()  : $('#'+textId).val();
		var ajaxUrl = aRouter['ajax']+'preview/text/';
		var ajaxOptions = {text: text, save: save};
		ls.hook.marker('textPreviewAjaxBefore');
		ls.ajax(ajaxUrl, ajaxOptions, function(result){
			if (!result) {
				ls.msg.error('Error','Please try again later');
			}
			if (result.bStateError) {
				ls.msg.error(result.sMsgTitle||'Error',result.sMsg||'Please try again later');
			} else {
				if (!divPreview) {
					divPreview = 'text_preview';
				}
				var elementPreview = $('#'+divPreview);
				ls.hook.marker('textPreviewDisplayBefore');
				if (elementPreview.length) {
					elementPreview.html(result.sText);
					ls.hook.marker('textPreviewDisplayAfter');
				}
			}
		});
	};

	/**
	* Возвращает выделенный текст на странице
	*/
	this.getSelectedText = function(){
		var text = '';
		if(window.getSelection){
			text = window.getSelection().toString();
		} else if(window.document.selection){
			var sel = window.document.selection.createRange();
			text = sel.text || sel;
			if(text.toString) {
				text = text.toString();
			} else {
				text = '';
			}
		}
		return text;
	};


	return this;
}).call(ls.tools || {},jQuery);


/**
* Дополнительные функции
*/
ls = (function ($) {

	/**
	* Глобальные опции
	*/
	this.options = this.options || {};

	/**
	* Выполнение AJAX запроса, автоматически передает security key
	*/
	this.ajax = function(url,params,callback,more){
		more=more || {};
		params=params || {};
		params.security_ls_key=LIVESTREET_SECURITY_KEY;

		$.each(params,function(k,v){
			if (typeof(v) == "boolean") {
				params[k]=v ? 1 : 0;
			}
		});

		if (url.indexOf('http://')!=0 && url.indexOf('https://')!=0 && url.indexOf('/')!=0) {
			url=aRouter['ajax']+url+'/';
		}

		var ajaxOptions = {
			type: more.type || "POST",
			url: url,
			data: params,
			dataType: more.dataType || 'json',
			success: callback || function(){
				ls.debug("ajax success: ");
				ls.debug.apply(this,arguments);
			}.bind(this),
			error: more.error || function(msg){
				ls.debug("ajax error: ");
				ls.debug.apply(this,arguments);
			}.bind(this),
			complete: more.complete || function(msg){
				ls.debug("ajax complete: ");
				ls.debug.apply(this,arguments);
			}.bind(this)
		};
		
		ls.hook.run('ls_ajax_before', [ajaxOptions], this);

		return $.ajax(ajaxOptions);
	};

	/**
	* Выполнение AJAX отправки формы, включая загрузку файлов
	*/
	this.ajaxSubmit = function(url,form,callback,more) {
		more=more || {};
		if (typeof(form)=='string') {
			form=$('#'+form);
		}
		if (url.indexOf('http://')!=0 && url.indexOf('https://')!=0 && url.indexOf('/')!=0) {
			url=aRouter['ajax']+url+'/';
		}

		var options={
			type: 'POST',
			url: url,
			dataType: more.dataType || 'json',
			data: {security_ls_key: LIVESTREET_SECURITY_KEY},
			success: callback || function(){
				ls.debug("ajax success: ");
				ls.debug.apply(this,arguments);
			}.bind(this),
			error: more.error || function(){
				ls.debug("ajax error: ");
				ls.debug.apply(this,arguments);
			}.bind(this)

		};

		ls.hook.run('ls_ajaxsubmit_before', [options], this);
		
		form.ajaxSubmit(options);
	};

	/**
	* Загрузка изображения
	*/
	this.ajaxUploadImg = function(form, sToLoad) {
		ls.hook.marker('ajaxUploadImgBefore');
		ls.ajaxSubmit('upload/image/',form,function(data){
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				$.markItUp({replaceWith: data.sText} );
				$('#window_upload_img').find('input[type="text"], input[type="file"]').val('');
				$('#window_upload_img').jqmHide();
				ls.hook.marker('ajaxUploadImgAfter');
			}
		});
	};

	/**
	* Дебаг сообщений
	*/
	this.debug = function() {
		if (this.options.debug) {
			this.log.apply(this,arguments);
		}
	};

	/**
	* Лог сообщений
	*/
	this.log = function() {
		if (!$.browser.msie && window.console && window.console.log) {
			Function.prototype.bind.call(console.log, console).apply(console, arguments);
		} else {
			//alert(msg);
		}
	};

	return this;
}).call(ls || {},jQuery);



/**
* Автокомплитер
*/
ls.autocomplete = (function ($) {
	/**
	* Добавляет автокомплитер к полю ввода
	*/
	this.add = function(obj, sPath, multiple) {
		if (multiple) {
			obj.bind("keydown", function(event) {
				if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				source: function(request, response) {
					ls.ajax(sPath,{value: ls.autocomplete.extractLast(request.term)},function(data){
						response(data.aItems);
					});
				},
				search: function() {
					var term = ls.autocomplete.extractLast(this.value);
					if (term.length < 2) {
						return false;
					}
				},
				focus: function() {
					return false;
				},
				select: function(event, ui) {
					var terms = ls.autocomplete.split(this.value);
					terms.pop();
					terms.push(ui.item.value);
					terms.push("");
					this.value = terms.join(", ");
					return false;
				}
			});
		} else {
			obj.autocomplete({
				source: function(request, response) {
					ls.ajax(sPath,{value: ls.autocomplete.extractLast(request.term)},function(data){
						response(data.aItems);
					});
				}
			});
		}
	};

	this.split = function(val) {
		return val.split( /,\s*/ );
	};

	this.extractLast = function(term) {
		return ls.autocomplete.split(term).pop();
	};

	return this;
}).call(ls.autocomplete || {},jQuery);



/**
* Костыли для ИЕ
*/
ls.ie = (function ($) {

	// эмуляция border-sizing в IE
	this.bordersizing = function(inputs) {
		if ($('html').hasClass('ie7')) {
			if (!tinyMCE) $('textarea.mce-editor').addClass('markItUpEditor');
			
			inputs.each(function(i){
				var obj = $(this);
				if (obj.css('box-sizing') == 'border-box') {
					obj.css('width', '100%');
					obj.width(2 * obj.width() - obj.outerWidth());
				}
			});
		}
	};
	
	return this;
}).call(ls.ie || {},jQuery);



(ls.options || {}).debug=0;
;
var ls = ls || {};

/**
* Добавление в избранное
*/
ls.favourite = (function ($) {
	/**
	* Опции
	*/
	this.options = {
		active: 'active',
		type: {
			topic: {
				url: 			aRouter['ajax']+'favourite/topic/',
				targetName: 	'idTopic'
			},
			talk: {
				url: 			aRouter['ajax']+'favourite/talk/',
				targetName: 	'idTalk'
			},
			comment: {
				url: 			aRouter['ajax']+'favourite/comment/',
				targetName: 	'idComment'
			}
		}
	};

	/**
	* Переключение избранного
	*/
	this.toggle = function(idTarget, objFavourite, type) {
		if (!this.options.type[type]) { return false; }

		this.objFavourite = $(objFavourite);
		
		var params = {};
		params['type'] = !this.objFavourite.hasClass(this.options.active);
		params[this.options.type[type].targetName] = idTarget;
		
		ls.hook.marker('toggleBefore');
		ls.ajax(this.options.type[type].url, params, function(result) {
			$(this).trigger('toggle',[idTarget,objFavourite,type,params,result]);
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				ls.msg.notice(null, result.sMsg);
				this.objFavourite.removeClass(this.options.active);
				if (result.bState) {
					this.objFavourite.addClass(this.options.active);
					this.showTags(type,idTarget);
				} else {
					this.hideTags(type,idTarget);
				}
				ls.hook.run('ls_favourite_toggle_after',[idTarget,objFavourite,type,params,result],this);
			}
		}.bind(this));
		return false;
	};

	this.showEditTags = function(idTarget,type,obj) {
		var form=$('#favourite-form-tags');
		$('#favourite-form-tags-target-type').val(type);
		$('#favourite-form-tags-target-id').val(idTarget);
		var text='';
		var tags=$('.js-favourite-tags-'+$('#favourite-form-tags-target-type').val()+'-'+$('#favourite-form-tags-target-id').val());
		tags.find('.js-favourite-tag-user a').each(function(k,tag){
			if (text) {
				text=text+', '+$(tag).text();
			} else {
				text=$(tag).text();
			}
		});
		$('#favourite-form-tags-tags').val(text);
		//$(obj).parents('.js-favourite-insert-after-form').after(form);
		form.jqmShow();

		return false;
	};

	this.hideEditTags = function() {
		$('#favourite-form-tags').jqmHide();
		return false;
	};

	this.saveTags = function(form) {
		var url=aRouter['ajax']+'favourite/save-tags/';
		ls.hook.marker('saveTagsBefore');
		ls.ajaxSubmit(url, $(form), function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				this.hideEditTags();
				var type=$('#favourite-form-tags-target-type').val();
				var tags=$('.js-favourite-tags-'+type+'-'+$('#favourite-form-tags-target-id').val());
				tags.find('.js-favourite-tag-user').detach();
				var edit=tags.find('.js-favourite-tag-edit');
				$.each(result.aTags,function(k,v){
					edit.before('<li class="'+type+'-tags-user js-favourite-tag-user">, <a rel="tag" href="'+v.url+'">'+v.tag+'</a></li>');
				});

				ls.hook.run('ls_favourite_save_tags_after',[form,result],this);
			}
		}.bind(this));
		return false;
	};

	this.hideTags = function(targetType,targetId) {
		var tags=$('.js-favourite-tags-'+targetType+'-'+targetId);
		tags.find('.js-favourite-tag-user').detach();
		tags.find('.js-favourite-tag-edit').hide();
		this.hideEditTags();
	};

	this.showTags = function(targetType,targetId) {
		$('.js-favourite-tags-'+targetType+'-'+targetId).find('.js-favourite-tag-edit').show();
	};

	return this;
}).call(ls.favourite || {},jQuery);
var ls = ls || {};

/**
* Динамическая подгрузка блоков
*/
ls.blocks = (function ($) {
	/**
	* Опции
	*/
	this.options = {
		active: 'active',
		loader: DIR_STATIC_SKIN + '/images/loader.gif',
		type: {
			stream_comment: {
				url: aRouter['ajax']+'stream/comment/'
			},
			stream_topic: {
				url: aRouter['ajax']+'stream/topic/'
			},
			blogs_top: {
				url: aRouter['ajax']+'blogs/top/'
			},
			blogs_join: {
				url: aRouter['ajax']+'blogs/join/'
			},
			blogs_self: {
				url: aRouter['ajax']+'blogs/self/'
			}
		}
	};

	/**
	* Метод загрузки содержимого блока
	*/
	this.load = function(obj, block, params){
		var type = $(obj).data('type');
		ls.hook.marker('loadBefore');
		
		if(!type) return;
		type=block+'_'+type;
		
		params=$.extend(true,{},this.options.type[type].params || {},params || {});
		
		var content = $('.js-block-'+block+'-content');
		this.showProgress(content);

		$('.js-block-'+block+'-item').removeClass(this.options.active);
		$(obj).addClass(this.options.active);

		ls.ajax(this.options.type[type].url, params, function(result){
			var args = [content,result];
			ls.hook.marker('onLoadBefore');
			this.onLoad.apply(this,args);
		}.bind(this));
	};

	/**
	 * Переключает вкладки в блоке, без использования Ajax
	 * @param obj
	 * @param block
	 */
	this.switchTab = function(obj, block) {
		/**
		 * Если вкладку передаем как строчку - значение data-type
		 */
		if (typeof(obj)=='string') {
			$('.js-block-'+block+'-item').each(function(k,v){
				if ($(v).data('type')==obj) {
					obj=v;
					return;
				}
			});
		}
		/**
		 * Если не нашли такой вкладки
		 */
		if (typeof(obj)=='string') {
			return false;
		}

		$('.js-block-'+block+'-item').removeClass(this.options.active);
		$(obj).addClass(this.options.active);

		$('.js-block-'+block+'-content').hide();
		$('.js-block-'+block+'-content').each(function(k,v){
			if ($(v).data('type')==$(obj).data('type')) {
				$(v).show();
			}
		});
		ls.hook.run('ls_blocks_switch_tab_after',[obj, block],this);
		return true;
	};

	/**
	* Отображение процесса загрузки
	*/
	this.showProgress = function(content) {
		content.height(content.height());
		content.empty().css({'background': 'url(' + this.options.loader + ') no-repeat center top', 'min-height': 70});
	};

	/**
	* Обработка результатов загрузки
	*/
	this.onLoad = function(content,result) {
		$(this).trigger('loadSuccessful',arguments);
		content.empty().css({'background': 'none', 'height': 'auto', 'min-height': 0});
		if (result.bStateError) {
			ls.msg.error(null, result.sMsg);
		} else {
			content.html(result.sText);
			ls.hook.run('ls_block_onload_html_after',arguments,this);
		}
	};

	this.getCurrentItem = function(block) {
		if ($('.js-block-'+block+'-nav').is(':visible')) {
			return $('.js-block-'+block+'-nav').find('.js-block-'+block+'-item.'+this.options.active);
		} else {
			return $('.js-block-'+block+'-dropdown-items').find('.js-block-'+block+'-item.'+this.options.active);
		}
	};

	this.initSwitch = function(block) {
		$('.js-block-'+block+'-item').click(function(){
			ls.blocks.switchTab(this, block);
			return false;
		});
	};

	this.init = function(block,params) {
		params=params || {};
		$('.js-block-'+block+'-item').click(function(){
			ls.blocks.load(this, block);
			return false;
		});
		if (params.group_items) {
			this.initNavigation(block,params.group_min);
		}

		var $this=this;
		$('.js-block-'+block+'-update').click(function(){
			$(this).addClass('fa-spin');
			ls.blocks.load($this.getCurrentItem(block), block);
			setTimeout( function() { $(this).removeClass('fa-spin'); }.bind(this), 600 );
		});
	};

	this.initNavigation = function(block,count) {
		count=count || 3;
		if ($('.js-block-'+block+'-nav').find('li').length >= count) {
			$('.js-block-'+block+'-nav').hide();
			$('.js-block-'+block+'-dropdown').show();
			// Dropdown
			var trigger = $('.js-block-'+block+'-dropdown-trigger');
			var menu 	= $('.js-block-'+block+'-dropdown-items');

			menu.appendTo('body').css({'display': 'none'});
			trigger.click(function(){
				var pos = $(this).offset();
				menu.css({ 'left': pos.left, 'top': pos.top + 30, 'z-index': 2100 });
				menu.slideToggle();
				$(this).toggleClass('opened');
				return false;
			});
			menu.find('a').click(function(){
				trigger.removeClass('opened').find('a').text( $(this).text() );
				menu.slideToggle();
			});
			// Hide menu
			$(document).click(function(){
				trigger.removeClass('opened');
				menu.slideUp();
			});
			$('body').on('click', '.js-block-'+block+'-dropdown-trigger, .js-block-'+block+'-dropdown-items', function(e) {
				e.stopPropagation();
			});
		
			$(window).resize(function(){
				menu.css({ 'left': $('.js-block-'+block+'-dropdown-trigger').offset().left });
			});
		} else {
			// Transform nav to dropdown
			$('.js-block-'+block+'-nav').show();
			$('.js-block-'+block+'-dropdown').hide();
		}
		ls.hook.run('ls_blocks_init_navigation_after',[block,count],this);
	};

	return this;
}).call(ls.blocks || {},jQuery);
var ls = ls || {};

/**
* Функционал личных сообщений
*/
ls.talk = (function ($) {
	
	/**
	* Добавляет пользователя к переписке
	*/
	this.addToTalk = function(idTalk){
		var sUsers = $('#talk_speaker_add').val();
		if(!sUsers) return false;
		$('#talk_speaker_add').val('');
		
		var url = aRouter['talk']+'ajaxaddtalkuser/';
		var params = {users: sUsers, idTalk: idTalk};
		
		ls.hook.marker('addToTalkBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$.each(result.aUsers, function(index, item) {
					if(item.bStateError){
						ls.msg.notice(null, item.sMsg);
					} else {
						var list = $('#speaker_list');
						if(list.length == 0) {
							list = $('<ul class="list" id="speaker_list"></ul>');
							$('#speaker_list_block').append(list);
						}
						var listItem = $('<li id="speaker_item_'+item.sUserId+'_area"><a href="'+item.sUserLink+'" class="user">'+item.sUserLogin+'</a> - <a href="#" id="speaker_item_'+item.sUserId+'" class="delete">'+ls.lang.get('delete')+'</a></li>')
						list.append(listItem);
						ls.hook.run('ls_talk_add_to_talk_item_after',[idTalk,item],listItem);
					}
				});

				ls.hook.run('ls_talk_add_to_talk_after',[idTalk,result]);
			}
		});
		return false;
	};

	/**
	* Удаляет пользователя из переписки
	*/
	this.removeFromTalk = function(link, idTalk) {
		link = $(link);
		
		$('#'+link.attr('id')+'_area').fadeOut(500,function(){
			$(this).remove();
		});
		var idTarget = link.attr('id').replace('speaker_item_','');
		
		var url = aRouter['talk']+'ajaxdeletetalkuser/';
		var params = {idTarget: idTarget, idTalk: idTalk};

		ls.hook.marker('removeFromTalkBefore');
		ls.ajax(url, params, function(result) {
			if (!result) {
				ls.msg.error('Error','Please try again later');
				link.parent('li').show();
			}
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
				link.parent('li').show();
			}
			ls.hook.run('ls_talk_remove_from_talk_after',[idTalk,idTarget],link);
		});

		return false;
	};
	
	/**
	* Добавляет пользователя в черный список
	*/
	this.addToBlackList = function() {
		var sUsers = $('#talk_blacklist_add').val();
		if(!sUsers) return false;
		$('#talk_blacklist_add').val('');
		
		var url = aRouter['talk']+'ajaxaddtoblacklist/';
		var params = {users: sUsers};

		ls.hook.marker('addToBlackListBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$.each(result.aUsers, function(index, item) {
					if(item.bStateError){
						ls.msg.notice(null, item.sMsg);
					} else {
						var list = $('#black_list');
						if(list.length == 0) {
							list = $('<ul class="list" id="black_list"></ul>');
							$('#black_list_block').append(list);
						}
						var listItem = $('<li id="blacklist_item_'+item.sUserId+'_area"><a href="#" class="user">'+item.sUserLogin+'</a> - <a href="#" id="blacklist_item_'+item.sUserId+'" class="delete">'+ls.lang.get('delete')+'</a></li>');
						$('#black_list').append(listItem);
						ls.hook.run('ls_talk_add_to_black_list_item_after',[item],listItem);
					}
				});
				ls.hook.run('ls_talk_add_to_black_list_after',[result]);
			}
		});
		return false;
	};
	
	/**
	* Удаляет пользователя из черного списка
	*/
	this.removeFromBlackList = function(link) {
		link = $(link);
		
		$('#'+link.attr('id')+'_area').fadeOut(500,function(){
			$(this).remove();
		});
		var idTarget = link.attr('id').replace('blacklist_item_','');

		var url = aRouter['talk']+'ajaxdeletefromblacklist/';
		var params = {idTarget: idTarget};
		
		ls.hook.marker('removeFromBlackListBefore');
		ls.ajax(url, params, function(result) {
			if (!result) {
				ls.msg.error('Error','Please try again later');
				link.parent('li').show();
			}
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
				link.parent('li').show();
			}
			ls.hook.run('ls_talk_remove_from_black_list_after',[idTarget],link);
		});
		return false;
	};
	
	/**
	* Добавляет или удаляет друга из списка получателей
	*/
	this.toggleRecipient = function(login, add) {
		var to = $.map($('#talk_users').val().split(','), function(item, index){
			item = $.trim(item);
			return item != '' ? item : null;
		});
		if (add) { to.push(login); to = $.richArray.unique(to); } else { to = $.richArray.without(to, login); }
		$('#talk_users').val(to.join(', '));
	};

	/**
	 * Очищает поля фильтра
	 */
	this.clearFilter = function() {
		$('#block_talk_search_content').find('input[type="text"]').val('');
		$('#block_talk_search_content').find('input[type="checkbox"]').removeAttr("checked");
		return false;
	};

	/**
	 * Удаление списка писем
	 */
	this.removeTalks = function() {
		if ($('.form_talks_checkbox:checked').length == 0) {
			return false;
		}
		$('#form_talks_list_submit_del').val(1);
		$('#form_talks_list_submit_read').val(0);
		$('#form_talks_list').submit();
		return false;
	};

	/**
	 * Пометка о прочтении писем
	 */
	this.makeReadTalks = function() {
		if ($('.form_talks_checkbox:checked').length == 0) {
			return false;
		}
		$('#form_talks_list_submit_read').val(1);
		$('#form_talks_list_submit_del').val(0);
		$('#form_talks_list').submit();
		return false;
	};
	
	return this;
}).call(ls.talk || {},jQuery);
;
var ls = ls || {};

/**
* Голосование
*/
ls.vote = (function ($) {
	/**
	* Опции
	*/
	this.options = {
		classes: {
			voted: 		'voted',
			plus: 		'voted-up',
			minus:  	'voted-down',
			positive:	'vote-count-positive',
			negative:  	'vote-count-negative',
			voted_zero: 'voted-zero',
			zero: 	 	'vote-count-zero',
			not_voted:  'not-voted'
		},
		prefix_area: 'vote_area_',
		prefix_total: 'vote_total_',
		prefix_count: 'vote_count_',

		type: {
			comment: {
				url: aRouter['ajax']+'vote/comment/',
				targetName: 'idComment'
			},
			topic: {
				url: aRouter['ajax']+'vote/topic/',
				targetName: 'idTopic'
			},
			blog: {
				url: aRouter['ajax']+'vote/blog/',
				targetName: 'idBlog'
			},
			user: {
				url: aRouter['ajax']+'vote/user/',
				targetName: 'idUser'
			}
		}
	};

	this.vote = function(idTarget, objVote, value, type) {
		if (!this.options.type[type]) return false;

		objVote = $(objVote);
		var params = {};
		params['value'] = value;
		params[this.options.type[type].targetName] = idTarget;
		
		ls.hook.marker('voteBefore');
		ls.ajax(this.options.type[type].url, params, function(result) {
			var args = [idTarget, objVote, value, type, result];
			this.onVote.apply(this,args);
		}.bind(this));
		return false;
	}

	this.onVote = function(idTarget, objVote, value, type, result) {
		if (result.bStateError) {
			ls.msg.error(null, result.sMsg);
		} else {
			ls.msg.notice(null, result.sMsg);
			
			var divVoting = $('#'+this.options.prefix_area+type+'_'+idTarget);

			divVoting.addClass(this.options.classes.voted);

			if (value > 0) {
				divVoting.addClass(this.options.classes.plus);
				divVoting.removeClass(this.options.classes.minus);
			}
			if (value < 0) {
				divVoting.addClass(this.options.classes.minus);
				divVoting.removeClass(this.options.classes.plus);
			}
			if (value == 0) {
				divVoting.addClass(this.options.classes.voted_zero);
			}
			
			var divTotal = $('#'+this.options.prefix_total+type+'_'+idTarget);
			var divCount = $('#'+this.options.prefix_count+type+'_'+idTarget);
			
			if (divCount.length>0 && result.iCountVote) {
				divCount.text(parseInt(result.iCountVote));
			}

			result.iRating = parseFloat(result.iRating);

			divVoting.removeClass(this.options.classes.negative);
			divVoting.removeClass(this.options.classes.positive);
			divVoting.removeClass(this.options.classes.not_voted);
			divVoting.removeClass(this.options.classes.zero);

			if (result.iRating > 0) {
				divVoting.addClass(this.options.classes.positive);
				divTotal.text('+'+result.iRating);
			}else if (result.iRating < 0) {
				divVoting.addClass(this.options.classes.negative);
				divTotal.text(result.iRating);
			}else if (result.iRating == 0) {
				divVoting.addClass(this.options.classes.zero);
				divTotal.text(0);
			}

			var method='onVote'+ls.tools.ucfirst(type);
			if ($.type(this[method])=='function') {
				this[method].apply(this,[idTarget, objVote, value, type, result]);
			}

		}
		
		$(this).trigger('vote',[idTarget, objVote, value, type, result]);
	}



	this.onVoteUser = function(idTarget, objVote, value, type, result) {
		$('#user_skill_'+idTarget).text(result.iSkill);
	}

	return this;
}).call(ls.vote || {},jQuery);
var ls = ls || {};

/**
* Опросы
*/
ls.poll = (function ($) {
	
	/**
	* Голосование в опросе
	*/
	this.vote = function(idTopic, idAnswer) {
		var url = aRouter['ajax']+'vote/question/';
		var params = {idTopic: idTopic, idAnswer: idAnswer};
		ls.hook.marker('voteBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				ls.msg.notice(null, result.sMsg);
				var area = $('#topic_question_area_'+idTopic);
				ls.hook.marker('voteDisplayBefore');
				area.html(result.sText);
				ls.hook.run('ls_pool_vote_after',[idTopic, idAnswer,result],area);
			}
		});
	};

	/**
	* Добавляет вариант ответа
	*/
	this.addAnswer = function() {
		if($("#question_list li").length == 20) {
			ls.msg.error(null, ls.lang.get('topic_question_create_answers_error_max'));
			return false;
		}
		var newItem = $("#question_list li:first-child").clone();
		newItem.find('a').remove();
		var removeAnchor = $('<a href="#"/>').text(ls.lang.get('delete')).click(function(e){
			e.preventDefault();
			return this.removeAnswer(e.target);
		}.bind(this));
		newItem.appendTo("#question_list").append(removeAnchor);
		newItem.find('input').val('');
		ls.hook.run('ls_pool_add_answer_after',[removeAnchor],newItem);
	};
	
	/**
	* Удаляет вариант ответа
	*/
	this.removeAnswer = function(obj) {
		$(obj).parent("li").remove();
		return false;
	};

	this.switchResult = function(obj, iTopicId) {
		if ($('#poll-result-sort-'+iTopicId).css('display') == 'none') {
			$('#poll-result-original-'+iTopicId).hide();
			$('#poll-result-sort-'+iTopicId).show();
			$(obj).toggleClass('active');
		} else {
			$('#poll-result-sort-'+iTopicId).hide();
			$('#poll-result-original-'+iTopicId).show();
			$(obj).toggleClass('active');
		}
		return false;
	};
	
	return this;
}).call(ls.poll || {},jQuery);
var ls = ls || {};

/**
 * Подписка
 */
ls.subscribe = (function ($) {

	/**
	 * Подписка/отписка
	 */
	this.toggle = function(sTargetType, iTargetId, sMail, iValue) {
		var url = aRouter['subscribe']+'ajax-subscribe-toggle/';
		var params = {target_type: sTargetType, target_id: iTargetId, mail: sMail, value: iValue};
		ls.hook.marker('toggleBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				ls.msg.notice(null, result.sMsg);
				ls.hook.run('ls_subscribe_toggle_after',[sTargetType, iTargetId, sMail, iValue, result]);
			}
		});
		return false;
	}

	return this;
}).call(ls.subscribe || {},jQuery);
var ls = ls || {};

/**
 * Гео-объекты
 */
ls.geo = (function ($) {

	/**
	 * Инициализация селектов выбора гео-объекта
	 */
	this.initSelect = function() {
		$.each($('.js-geo-select'),function(k,v){
			$(v).find('.js-geo-country').bind('change',function(e){
				this.loadRegions($(e.target));
			}.bind(this));
			$(v).find('.js-geo-region').bind('change',function(e){
				this.loadCities($(e.target));
			}.bind(this));
		}.bind(this));
	};

	this.loadRegions = function($country) {
		$region=$country.parents('.js-geo-select').find('.js-geo-region');
		$city=$country.parents('.js-geo-select').find('.js-geo-city');
		$region.empty();
		$region.append('<option value="">'+ls.lang.get('geo_select_region')+'</option>');
		$city.empty();
		$city.hide();

		if (!$country.val()) {
			$region.hide();
			return;
		}

		var url = aRouter['ajax']+'geo/get/regions/';
		var params = {country: $country.val()};
		ls.hook.marker('loadRegionsBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$.each(result.aRegions,function(k,v){
					$region.append('<option value="'+v.id+'">'+v.name+'</option>');
				});
				$region.show();
				ls.hook.run('ls_geo_load_regions_after',[$country, result]);
			}
		});
	};

	this.loadCities = function($region) {
		$city=$region.parents('.js-geo-select').find('.js-geo-city');
		$city.empty();
		$city.append('<option value="">'+ls.lang.get('geo_select_city')+'</option>');

		if (!$region.val()) {
			$city.hide();
			return;
		}

		var url = aRouter['ajax']+'geo/get/cities/';
		var params = {region: $region.val()};
		ls.hook.marker('loadCitiesBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$.each(result.aCities,function(k,v){
					$city.append('<option value="'+v.id+'">'+v.name+'</option>');
				});
				$city.show();
				ls.hook.run('ls_geo_load_cities_after',[$region, result]);
			}
		});
	};



	return this;
}).call(ls.geo || {},jQuery);
var ls = ls || {};

/**
 * Стена пользователя
 */
ls.wall = (function ($) {

	this.options = {
		login: ''
	};

	this.iIdForReply=null;
	/**
	 * Добавление записи
	 */
	this.add = function(sText, iPid) {
		$('.js-button-wall-submit').attr('disabled',true);
		var url = aRouter['profile']+this.options.login+'/wall/add/';
		var params = {sText: sText, iPid: iPid};

		ls.hook.marker('addBefore');
		$('#wall-text').addClass('loader');
		ls.ajax(url, params, function(result) {
			$('.js-button-wall-submit').attr('disabled',false);
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$('.js-wall-reply-parent-text').val('');
				$('#wall-note-list-empty').hide();
				this.loadNew();
				ls.hook.run('ls_wall_add_after',[sText, iPid, result]);
			}
			$('#wall-text').removeClass('loader');
		}.bind(this));
		return false;
	};

	this.addReply = function(sText, iPid) {
		$('.js-button-wall-submit').attr('disabled',true);
		var url = aRouter['profile']+this.options.login+'/wall/add/';
		var params = {sText: sText, iPid: iPid};

		ls.hook.marker('addReplyBefore');
		$('#wall-reply-text-' + iPid).addClass('loader');
		ls.ajax(url, params, function(result) {
			$('.js-button-wall-submit').attr('disabled',false);
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$('.js-wall-reply-text').val('');
				this.loadReplyNew(iPid);
				ls.hook.run('ls_wall_addreply_after',[sText, iPid, result]);
			}
			$('#wall-reply-text-' + iPid).removeClass('loader');
		}.bind(this));
		return false;
	};

	this.load = function(iIdLess,iIdMore,callback) {
		var url = aRouter['profile']+this.options.login+'/wall/load/';
		var params = {iIdLess: iIdLess ? iIdLess : '', iIdMore: iIdMore ? iIdMore : ''};
		ls.hook.marker('loadBefore');
		ls.ajax(url, params, callback);
		return false;
	};

	this.loadReply = function(iIdLess,iIdMore,iPid,callback) {
		var url = aRouter['profile']+this.options.login+'/wall/load-reply/';
		var params = {iIdLess: iIdLess ? iIdLess : '', iIdMore: iIdMore ? iIdMore : '', iPid: iPid};
		ls.hook.marker('loadReplyBefore');
		ls.ajax(url, params, callback);
		return false;
	};

	this.loadNext = function() {
		var divLast=$('#wall-container').find('.js-wall-item:last');
		if (divLast.length) {
			var idLess=divLast.attr('id').replace('wall-item-','');
		} else {
			return false;
		}
		$('#wall-button-next').addClass('loader');
		this.load(idLess,'',function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				if (result.iCountWall) {
					$('#wall-container').append(result.sText);
				}
				var iCount=result.iCountWall-result.iCountWallReturn;
				if (iCount) {
					$('#wall-count-next').text(iCount);
				} else {
					$('#wall-button-next').detach();
				}
				ls.hook.run('ls_wall_loadnext_after',[idLess, result]);
			}
			$('#wall-button-next').removeClass('loader');
		}.bind(this));
		return false;
	};

	this.loadNew = function() {
		var divFirst=$('#wall-container').find('.js-wall-item:first');
		if (divFirst.length) {
			var idMore=divFirst.attr('id').replace('wall-item-','');
		} else {
			var idMore=-1;
		}
		this.load('',idMore,function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				if (result.iCountWall) {
					$('#wall-container').prepend(result.sText);
				}
				ls.hook.run('ls_wall_loadnew_after',[idMore, result]);
			}
		}.bind(this));
		return false;
	};

	this.loadReplyNew = function(iPid) {
		var divFirst=$('#wall-reply-container-'+iPid).find('.js-wall-reply-item::last');
		if (divFirst.length) {
			var idMore=divFirst.attr('id').replace('wall-reply-item-','');
		} else {
			var idMore=-1;
		}
		this.loadReply('',idMore,iPid,function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				if (result.iCountWall) {
					$('#wall-reply-container-'+iPid).append(result.sText);
				}
				ls.hook.run('ls_wall_loadreplynew_after',[iPid, idMore, result]);
			}
		}.bind(this));
		return false;
	};

	this.loadReplyNext = function(iPid) {
		var divLast=$('#wall-reply-container-'+iPid).find('.js-wall-reply-item:first');
		if (divLast.length) {
			var idLess=divLast.attr('id').replace('wall-reply-item-','');
		} else {
			return false;
		}
		$('#wall-reply-button-next-' + iPid).addClass('loader');
		this.loadReply(idLess,'',iPid,function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				if (result.iCountWall) {
					$('#wall-reply-container-'+iPid).prepend(result.sText);
				}
				var iCount=result.iCountWall-result.iCountWallReturn;
				if (iCount) {
					$('#wall-reply-count-next-'+iPid).text(iCount);
				} else {
					$('#wall-reply-button-next-'+iPid).detach();
				}
				ls.hook.run('ls_wall_loadreplynext_after',[iPid, idLess, result]);
			}
			$('#wall-reply-button-next-' + iPid).removeClass('loader');
		}.bind(this));
		return false;
	};

	this.toggleReply = function(iId) {
		$('#wall-item-' + iId + ' .wall-submit-reply').addClass('active').toggle().children('textarea').focus();
		return false;
	};

	this.expandReply = function(iId) {
		$('#wall-item-' + iId + ' .wall-submit-reply').addClass('active');
		return false;
	};

	this.init = function(opt) {
		if (opt) {
			$.extend(true,this.options,opt);
		}
		jQuery(function($){
			$(document).click(function(e) {
				if (e.which==1) {
					$('.wall-submit-reply.active').each(function(k,v){
						if (!$(v).find('.js-wall-reply-text').val()) {
							$(v).removeClass('active');
						}
					});
				}
			});

			$('body').on("click", ".wall-submit-reply, .link-dotted", function(e) {
				e.stopPropagation();
			});

			$('.js-wall-reply-text').bind('keyup', function(e) {
				key = e.keyCode || e.which;
				if(e.ctrlKey && (key == 13)) {
					var id=$(e.target).attr('id').replace('wall-reply-text-','');
					this.addReply($(e.target).val(), id);
					return false;
				}
			}.bind(this));
			$('.js-wall-reply-parent-text').bind('keyup', function(e) {
				key = e.keyCode || e.which;
				if(e.ctrlKey && (key == 13)) {
					this.add($(e.target).val(), 0);
					return false;
				}
			}.bind(this));
		}.bind(this));
	};

	this.remove = function(iId) {
		var url = aRouter['profile']+this.options.login+'/wall/remove/';
		var params = {iId: iId};
		ls.hook.marker('removeBefore');
		ls.ajax(url, params, function(result){
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$('#wall-item-'+iId).fadeOut('slow', function() {
					ls.hook.run('ls_wall_remove_item_fade',[iId, result],this);
				});
				$('#wall-reply-item-'+iId).fadeOut('slow', function() {
					ls.hook.run('ls_wall_remove_reply_item_fade',[iId, result],this);
				});
				ls.hook.run('ls_wall_remove_after',[iId, result]);
			}
		});
		return false;
	};

	return this;
}).call(ls.wall || {},jQuery);
var ls = ls || {};

ls.usernote =( function ($) {

	this.sText='';

	this.showForm = function(sText) {
		$('#usernote-button-add').hide();
		$('#usernote-note').hide();
		$('#usernote-form').show();
		if (this.sText) {
			$('#usernote-form-text').html(this.sText);
		} else {
			$('#usernote-form-text').val('');
		}
		$('#usernote-form-text').focus();
		return false;
	};

	this.hideForm = function() {
		$('#usernote-form').hide();
		if (this.sText) {
			this.showNote();
		} else {
			$('#usernote-button-add').show();
		}
		return false;
	};

	this.save = function(iUserId) {
		var url = aRouter['profile']+'ajax-note-save/';
		var params = {iUserId: iUserId, text: $('#usernote-form-text').val()};
		ls.hook.marker('saveBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				this.sText=result.sText;
				this.showNote();
				ls.hook.run('ls_usernote_save_after',[params, result]);
			}
		}.bind(this));
		return false;
	};

	this.showNote = function() {
		$('#usernote-form').hide();
		$('#usernote-note').show();
		$('#usernote-note-text').html(this.sText);
	};

	this.remove = function(iUserId) {
		var url = aRouter['profile']+'ajax-note-remove/';
		var params = {iUserId: iUserId};
		ls.hook.marker('removeBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$('#usernote-note').hide();
				$('#usernote-button-add').show();
				this.sText='';
				ls.hook.run('ls_usernote_remove_after',[params, result]);
			}
		}.bind(this));
		return false;
	};

	return this;
}).call(ls.usernote || {},jQuery);
var ls = ls || {};

ls.jdebug = false;

/**
* Обработка комментариев
*/
ls.comments = (function ($) {
	/**
	* Опции
	*/
	this.options = {
		type: {
			topic: {
				url_add: 		aRouter.blog+'ajaxaddcomment/',
				url_response: 	aRouter.blog+'ajaxresponsecomment/'
			},
			talk: {
				url_add: 		aRouter.talk+'ajaxaddcomment/',
				url_response: 	aRouter.talk+'ajaxresponsecomment/'
			}
		},
		classes: {
			form_loader: 'loader',
			comment_new: 'comment-new',
			comment_current: 'comment-current',
			comment_deleted: 'comment-deleted',
			comment_self: 'comment-self',
			comment: 'comment',
			comment_goto_parent: 'goto-comment-parent',
			comment_goto_child: 'goto-comment-child'
		},
		wysiwyg: null,
		folding: true,
        pageTitle: false // for comment count: original title here
	};

	this.iCurrentShowFormComment=0;
	this.iCurrentViewComment=null;
	this.aCommentNew=[];
	this.aCommentOld=[];

	// Добавляет комментарий
	this.add = function(formObj, targetId, targetType) {
		if (this.options.wysiwyg) {
			$('#'+formObj+' textarea').val(tinyMCE.activeEditor.getContent());
		}
		formObj = $('#'+formObj);

		$('#form_comment_text').addClass(this.options.classes.form_loader).attr('readonly',true);
		$('#comment-button-submit').attr('disabled', 'disabled');
		
		ls.ajax(this.options.type[targetType].url_add, formObj.serializeJSON(), function(result){
			$('#comment-button-submit').removeAttr('disabled');
			if (!result) {
				this.enableFormComment();
				ls.msg.error('Error','Please try again later');
				return;
			}
			if (result.bStateError) {
				this.enableFormComment();
				ls.msg.error(null,result.sMsg);
			} else {
				this.enableFormComment();
				$('#form_comment_text').val('');

				// Load new comments
				this.load(targetId, targetType, result.sCommentId, true);
				ls.hook.run('ls_comments_add_after',[formObj, targetId, targetType, result]);
			}
		}.bind(this));
	};


	// Активирует форму
	this.enableFormComment = function() {
		$('#form_comment_text').removeClass(this.options.classes.form_loader).attr('readonly',false);
	};


	// Показывает/скрывает форму комментирования
	this.toggleCommentForm = function(idComment, bNoFocus) {
		var reply=$('#reply');
		if(!reply.length){
			return;
		}
		$('#comment_preview_' + this.iCurrentShowFormComment).remove();

		if (this.iCurrentShowFormComment==idComment && reply.is(':visible')) {
			reply.hide();
			return;
		}
		if (this.options.wysiwyg) {
			tinyMCE.execCommand('mceRemoveControl',true,'form_comment_text');
		}
		reply.insertAfter('#comment_id_'+idComment).show();
		$('#form_comment_text').val('');
		$('#form_comment_reply').val(idComment);
		
		this.iCurrentShowFormComment = idComment;
		if (this.options.wysiwyg) {
			tinyMCE.execCommand('mceAddControl',true,'form_comment_text');
		}
		if (!bNoFocus) $('#form_comment_text').focus();
		
		if ($('html').hasClass('ie7')) {
			var inputs = $('input.input-text, textarea');
			ls.ie.bordersizing(inputs);
		}
	};


	// Подгружает новые комментарии
	this.load = function(idTarget, typeTarget, selfIdComment, bNotFlushNew) {	
        //if (this.options.loadMutex) return;
        //this.options.loadMutex = true;

		var idCommentLast = $("#comment_last_id").val();
		if(this.aCommentNew != []){
			this.aCommentOld = this.aCommentNew;
		}

		// Удаляем подсветку у комментариев
		if (!bNotFlushNew) { 
			$('.comment').each(function(index, item){ 
				$(item).removeClass(this.options.classes.comment_new+' '+this.options.classes.comment_current);
			}.bind(this)); 
		}

		objImg = $('#update-comments');
		objImg.addClass('fa-pulse');

		var params = { idCommentLast: idCommentLast, idTarget: idTarget, typeTarget: typeTarget };
		if (selfIdComment) { 
			params.selfIdComment = selfIdComment; 
		}
		if ($('#comment_use_paging').val()) { 
			params.bUsePaging = 1; 
		}

		ls.ajax(this.options.type[typeTarget].url_response, params, function(result) {
			objImg.removeClass('fa-pulse');

			if (!result) { ls.msg.error('Error','Please try again later'); }
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				var aCmt = result.aComments;
				if (aCmt.length > 0 && result.iMaxIdComment) { 
					$("#comment_last_id").val(result.iMaxIdComment);
					$('#count-comments').text(parseInt($('#count-comments').text())+aCmt.length);
					if (ls.blocks) {
						var curItemBlock=ls.blocks.getCurrentItem('stream');
						if (curItemBlock.data('type')=='comment') {
							ls.blocks.load(curItemBlock, 'stream');
						}
					}
				}
				var iCountOld=0;
				if (bNotFlushNew) {
					iCountOld=this.aCommentNew.length;
				} else {
					this.aCommentNew=[];
				}
				if (selfIdComment) { 
					this.toggleCommentForm(this.iCurrentShowFormComment, true); 
					this.setCountNewComment(aCmt.length-1+iCountOld);
				} else { 
					this.setCountNewComment(aCmt.length+iCountOld); 
				}

				$.each(aCmt, function(index, item) { 
					if (!document.getElementById('comment_id_' + item.id)) {
                        if (!(selfIdComment && selfIdComment==item.id)) {
						    this.aCommentNew.push(item.id);
    					}
    					this.inject(item.idParent, item.id, item.html); 
                    }
				}.bind(this));

				if (selfIdComment && $('#comment_id_'+selfIdComment).length) { 
					this.scrollToComment(selfIdComment);
				}
				this.checkFolding();
				this.aCommentNew = [];
				this.calcNewComments();
				ls.hook.run('ls_comments_load_after',[idTarget, typeTarget, selfIdComment, bNotFlushNew, result]);
			}
		}.bind(this));
	};

	this.turnBack = function(){
		this.aCommentNew += this.aCommentOld;
		this.aCommentNew.forEach(function(item, i){
			item.addClass(this.options.comment_new)
		})
		this.setCountNewComment(this.aCommentNew.length);

	}


	// Вставка комментария
	this.inject = function(idCommentParent, idComment, sHtml) {
		var newComment = $('<div>', {'class': 'comment-wrapper', id: 'comment_wrapper_id_'+idComment}).html(sHtml);
		if (idCommentParent) {
			// Уровень вложенности родителя
			var iCurrentTree = $('#comment_wrapper_id_'+idCommentParent).parentsUntil('#comments').length;
			if(iCurrentTree == ls.registry.get('comment_max_tree')) {
				// Определяем id предыдушего родителя
				var prevCommentParent = $('#comment_wrapper_id_'+idCommentParent).parent();
				idCommentParent = parseInt(prevCommentParent.attr('id').replace('comment_wrapper_id_',''));
			}
			$('#comment_wrapper_id_'+idCommentParent).append(newComment);
		} else {
			$('#comments').append(newComment);
		}
		ls.hook.run('ls_comment_inject_after',arguments,newComment);
	};


	// Удалить/восстановить комментарий
	this.toggle = function(obj, commentId) {
		var url = aRouter['ajax']+'comment/delete/';
		var params = { idComment: commentId };
		
		ls.hook.marker('toggleBefore');
		ls.ajax(url, params, function(result){
			if (!result) {
				ls.msg.error('Error','Please try again later');
			}
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				ls.msg.notice(null,result.sMsg);

				$('#comment_id_'+commentId).removeClass(this.options.classes.comment_self+' '+this.options.classes.comment_new+' '+this.options.classes.comment_deleted+' '+this.options.classes.comment_current);
				if (result.bState) {
					$('#comment_id_'+commentId).addClass(this.options.classes.comment_deleted);
				}
				$(obj).text(result.sTextToggle);
				ls.hook.run('ls_comments_toggle_after',[obj,commentId,result]);
			}
		}.bind(this));
	};


	// Предпросмотр комментария
	this.preview = function(divPreview) {
		if (this.options.wysiwyg) {
			$("#form_comment_text").val(tinyMCE.activeEditor.getContent());
		}
		if ($("#form_comment_text").val() == '') return;
		$("#comment_preview_" + this.iCurrentShowFormComment).remove();
		$('#reply').before('<div id="comment_preview_' + this.iCurrentShowFormComment +'" class="comment-preview text"></div>');
		ls.tools.textPreview('form_comment_text', false, 'comment_preview_' + this.iCurrentShowFormComment);
	};

	this.isCollapsed = function(el){
		if(el.closest(".collapsed")){
			return true;
		}
		return false;
	}



	// Устанавливает число новых комментариев
	this.setCountNewComment = function(count) {
        // TODO that will work good only if there are no any other title modificators!
        if (! this.options.pageTitle) this.options.pageTitle = document.title;
		if (count > 0) {
			$('#new_comments_counter').show().text(count);
            if (document.getElementById('autoload').checked) {
                document.title = '(' + count + ') ' + this.options.pageTitle;
            }
		} else {
			$('#new_comments_counter').text(0).hide();
            document.title = this.options.pageTitle;
		}
	};


	// Вычисляет кол-во новых комментариев
	this.calcNewComments = function() {
		var aCommentsNew = $('.'+this.options.classes.comment+'.'+this.options.classes.comment_new);
		$.each(aCommentsNew,function(k,v){
			console.log(this.isCollapsed(v));
		}.bind(this));
		count = aCommentsNew.length;
		$.each(aCommentsNew,function(k,v){
			if (!this.isCollapsed(v)){
			    this.aCommentNew.push(parseInt($(v).attr('id').replace('comment_id_','')));
		    } else {
		    	count--;
		    }
		}.bind(this));
		this.setCountNewComment(count);
	};


	// Переход к следующему комментарию
	this.goToNextComment = function() {
		if (this.aCommentNew[0]) {
			if ($('#comment_id_'+this.aCommentNew[0]).length) {
				this.scrollToComment(this.aCommentNew[0]);
				$('#comment_id_'+this.aCommentNew[0]).removeClass(this.options.classes.comment_new);
			}
			this.aCommentNew.shift();
		}
		this.setCountNewComment(this.aCommentNew.length);
	};


	// Прокрутка к комментарию
	this.scrollToComment = function(idComment) {
		$.scrollTo('#comment_id_'+idComment, 350, {offset: -250});
						
		if (this.iCurrentViewComment) {
			$('#comment_id_'+this.iCurrentViewComment).removeClass(this.options.classes.comment_current);
			$('#comment_id_'+this.iCurrentViewComment).removeClass(this.options.classes.comment_new);
		}				
		$('#comment_id_'+idComment).addClass(this.options.classes.comment_current);
		this.iCurrentViewComment=idComment;		
	};


	// Прокрутка к родительскому комментарию
	this.goToParentComment = function(id, pid) {
		thisObj = this;
		$('.'+this.options.classes.comment_goto_child).hide().find('a').unbind();

		$("#comment_id_"+pid).find('.'+this.options.classes.comment_goto_child).show().find("a").bind("click", function(){
			$(this).parent('.'+thisObj.options.classes.comment_goto_child).hide();
			thisObj.scrollToComment(id);
			return false;
		});
		this.scrollToComment(pid);
		return false;
	};


	// Сворачивание комментариев
	this.checkFolding = function() {
		//if(!this.options.folding){
		//	return false;
		//}
		$(".folding").each(function(index, element){
			if ($(element).parent(".comment").next(".comment-wrapper").length == 0) {
				$(element).hide();
			} else {
				$(element).show();
			}
		}).click(function(x){if(x.target.className=="folding fa fa-minus-square"){ls.comments.collapseComment(x.target)}else{ls.comments.expandComment(x.target)}});
		return false;
	};
	
	this.expandComment = function(folding) {
		$(folding).removeClass("fa-plus-square").addClass("fa-minus-square").parent().nextAll(".comment-wrapper").show().removeClass("collapsed");
	};
	
	this.collapseComment = function(folding) {
		$(folding).removeClass("fa-minus-square").addClass("fa-plus-square").parent().nextAll(".comment-wrapper").hide().addClass("collapsed");
	};

	this.expandCommentAll = function() {
		$.each($(".folding"),function(k,v){
			this.expandComment(v);
		}.bind(this));
	};
	
	this.collapseCommentAll = function() {
		$.each($(".folding"),function(k,v){
			this.collapseComment(v);
		}.bind(this));
	};
	
	


	this.init = function() {
		this.initEvent();
		this.calcNewComments();
		this.checkFolding();
		this.toggleCommentForm(this.iCurrentShowFormComment);
		
		if (typeof(this.options.wysiwyg)!='number') {
			this.options.wysiwyg = Boolean(BLOG_USE_TINYMCE && tinyMCE);
		}
		ls.hook.run('ls_comments_init_after',[],this);
	};
	
	this.initEvent = function() {
		$('#form_comment_text').bind('keyup', function(e) {
			key = e.keyCode || e.which;
			if(e.ctrlKey && (key == 13)) {
				$('#comment-button-submit').click();
				return false;
			}
		});
		
		if(this.options.folding){
			$(".folding").click(function(e){
				if ($(e.target).hasClass("folded")) {
					this.expandComment(e.target);
				} else {
					this.collapseComment(e.target);
				}
			}.bind(this));
		}
	};

	return this;
}).call(ls.comments || {},jQuery);
;
var ls = ls || {};

/**
* JS функционал для блогов
*/
ls.blog = (function ($) {
	
	/**
	* Вступить или покинуть блог
	*/
	this.toggleJoin = function(obj, idBlog){
		var url = aRouter['blog']+'ajaxblogjoin/';
		var params = {idBlog: idBlog};
		
		ls.hook.marker('toggleJoinBefore');
		ls.ajax(url,params,function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				obj = $(obj);
				ls.msg.notice(null, result.sMsg);
				
				var text = result.bState
					? ls.lang.get('blog_leave')
					: ls.lang.get('blog_join')
				;
				
				obj.empty().text(text);
				obj.toggleClass('active');
				
				$('#blog_user_count_'+idBlog).text(result.iCountUser);
				ls.hook.run('ls_blog_toggle_join_after',[idBlog,result],obj);
			}
		});
	};

	/**
	* Отправляет приглашение вступить в блог
	*/
	this.addInvite = function(idBlog) {
		var sUsers = $('#blog_admin_user_add').val();
		if(!sUsers) return false;
		$('#blog_admin_user_add').val('');
		
		var url = aRouter['blog']+'ajaxaddbloginvite/';
		var params = {users: sUsers, idBlog: idBlog};
		
		ls.hook.marker('addInviteBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$.each(result.aUsers, function(index, item) {
					if(item.bStateError){
						ls.msg.error(null, item.sMsg);
					} else {
						if($('#invited_list').length == 0) {
							$('#invited_list_block').append($('<ul class="list" id="invited_list"></ul>'));
						}
						var listItem = $('<li><a href="'+item.sUserWebPath+'" class="user">'+item.sUserLogin+'</a></li>');
						$('#invited_list').append(listItem);
						$('#blog-invite-empty').hide();
						ls.hook.run('ls_blog_add_invite_user_after',[idBlog,item],listItem);
					}
				});
				ls.hook.run('ls_blog_add_invite_after',[idBlog,sUsers,result]);
			}
		});
		
		return false;
	};

	/**
	* Повторно отправляет приглашение
	*/
	this.repeatInvite = function(idUser,idBlog) {
		var url = aRouter['blog']+'ajaxrebloginvite/';
		var params = {idUser: idUser, idBlog: idBlog};
		
		ls.hook.marker('repeatInviteBefore');
		ls.ajax(url, params, function(result){
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				ls.msg.notice(null, result.sMsg);
				ls.hook.run('ls_blog_repeat_invite_after',[idUser,idBlog,result]);
			}
		});
		
		return false;
	};

	/**
	 * Удаляет приглашение в блог
	 */
	this.removeInvite = function(idUser,idBlog) {
		var url = aRouter['blog']+'ajaxremovebloginvite/';
		var params = {idUser: idUser, idBlog: idBlog};

		ls.hook.marker('removeInviteBefore');
		ls.ajax(url, params, function(result){
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				$('#blog-invite-remove-item-'+idBlog+'-'+idUser).remove();
				ls.msg.notice(null, result.sMsg);
				if ($('#invited_list li').length == 0) $('#blog-invite-empty').show();
				ls.hook.run('ls_blog_remove_invite_after',[idUser,idBlog,result]);
			}
		});

		return false;
	};
	
	/**
	* Отображение информации о блоге
	*/
	this.loadInfo = function(idBlog) {
		var url = aRouter['blog']+'ajaxbloginfo/';
		var params = {idBlog: idBlog};
		
		ls.hook.marker('loadInfoBefore');
		ls.ajax(url, params, function(result){
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				var block = $('#block_blog_info');
				block.html(result.sText);
				ls.hook.run('ls_blog_load_info_after',[idBlog,result],block);
			}
		});
	};
	
	/**
	* Отображение информации о типе блога
	*/
	this.loadInfoType = function(type) {
		$('#blog_type_note').text(ls.lang.get('blog_create_type_' + type + '_notice'));
	};

	/**
	 * Поиск блогов
	 */
	this.searchBlogs = function(form) {
		var url = aRouter['blogs']+'ajax-search/';
		var inputSearch=$('#'+form).find('input');
		inputSearch.addClass('loader');

		ls.hook.marker('searchBlogsBefore');
		ls.ajaxSubmit(url, form, function(result){
			inputSearch.removeClass('loader');
			if (result.bStateError) {
				$('#blogs-list-search').hide();
				$('#blogs-list-original').show();
			} else {
				$('#blogs-list-original').hide();
				$('#blogs-list-search').html(result.sText).show();
				ls.hook.run('ls_blog_search_blogs_after',[form, result]);
			}
		});
	};

	/**
	 * Показать подробную информацию о блоге
	 */
	this.toggleInfo = function() {
		$('#blog-more-content').slideToggle();
		var more = $('#blog-more');
		more.toggleClass('expanded');
		
		if(more.hasClass('expanded')) {
			more.html(ls.lang.get('blog_fold_info'));
		} else {
			more.html(ls.lang.get('blog_expand_info'));
		}
		
		return false;
	};

	return this;
}).call(ls.blog || {},jQuery);
;
var ls = ls || {};

/**
 * Управление пользователями
 */
ls.user = (function ($) {

	this.jcropAvatar=null;
	this.jcropFoto=null;

	/**
	 * Добавление в друзья
	 */
	this.addFriend = function(obj, idUser, sAction){
		if(sAction != 'link' && sAction != 'accept') {
			var sText = $('#add_friend_text').val();
			$('#add_friend_form').children().each(function(i, item){$(item).attr('disabled','disabled')});
		} else {
			var sText='';
		}

		if(sAction == 'accept') {
			var url = aRouter.profile+'ajaxfriendaccept/';
		} else {
			var url = aRouter.profile+'ajaxfriendadd/';
		}

		var params = {idUser: idUser, userText: sText};

		ls.hook.marker('addFriendBefore');
		ls.ajax(url, params, function(result){
			$('#add_friend_form').children().each(function(i, item){$(item).removeAttr('disabled')});
			if (!result) {
				ls.msg.error('Error','Please try again later');
			}
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				ls.msg.notice(null,result.sMsg);
				$('#add_friend_form').jqmHide();
				$('#add_friend_item').remove();
				$('#profile_actions').prepend($(result.sToggleText));
				ls.hook.run('ls_user_add_friend_after', [idUser,sAction,result], obj);
			}
		});
		return false;
	};

	/**
	 * Удаление из друзей
	 */
	this.removeFriend = function(obj,idUser,sAction) {
		var url = aRouter.profile+'ajaxfrienddelete/';
		var params = {idUser: idUser,sAction: sAction};

		ls.hook.marker('removeFriendBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				ls.msg.notice(null,result.sMsg);
				$('#delete_friend_item').remove();
				$('#profile_actions').prepend($(result.sToggleText));
				ls.hook.run('ls_user_remove_friend_after', [idUser,sAction,result], obj);
			}
		});
		return false;
	};

	/**
	 * Загрузка временной аватарки
	 * @param form
	 * @param input
	 */
	this.uploadAvatar = function(form,input) {
		if (!form && input) {
			var form = $('<form method="post" enctype="multipart/form-data"></form>').css({
				'display': 'none'
			}).appendTo('body');
			var clone=input.clone(true);
			input.hide();
			clone.insertAfter(input);
			input.appendTo(form);
		}

		ls.ajaxSubmit(aRouter['settings']+'profile/upload-avatar/',form,function(data){
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				this.showResizeAvatar(data.sTmpFile);
			}
		}.bind(this));
	};

	/**
	 * Показывает форму для ресайза аватарки
	 * @param sImgFile
	 */
	this.showResizeAvatar = function(sImgFile) {
		if (this.jcropAvatar) {
			this.jcropAvatar.destroy();
		}
		$('#avatar-resize-original-img').attr('src',sImgFile+'?'+Math.random());
		$('#avatar-resize').jqmShow();
		var $this=this;
		$('#avatar-resize-original-img').Jcrop({
			aspectRatio: 1,
			minSize: [32,32]
		},function(){
			$this.jcropAvatar=this;
			this.setSelect([0,0,500,500]);
		});
	};

	/**
	 * Выполняет ресайз аватарки
	 */
	this.resizeAvatar = function() {
		if (!this.jcropAvatar) {
			return false;
		}
		var url = aRouter.settings+'profile/resize-avatar/';
		var params = {size: this.jcropAvatar.tellSelect()};

		ls.hook.marker('resizeAvatarBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#avatar-img').attr('src',result.sFile+'?'+Math.random());
				$('#avatar-resize').jqmHide();
				$('#avatar-remove').show();
				$('#avatar-upload').text(result.sTitleUpload);
				ls.hook.run('ls_user_resize_avatar_after', [params, result]);
			}
		});

		return false;
	};

	/**
	 * Удаление аватарки
	 */
	this.removeAvatar = function() {
		var url = aRouter.settings+'profile/remove-avatar/';
		var params = {};

		ls.hook.marker('removeAvatarBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#avatar-img').attr('src',result.sFile+'?'+Math.random());
				$('#avatar-remove').hide();
				$('#avatar-upload').text(result.sTitleUpload);
				ls.hook.run('ls_user_remove_avatar_after', [params, result]);
			}
		});

		return false;
	};

	/**
	 * Отмена ресайза аватарки, подчищаем временный данные
	 */
	this.cancelAvatar = function() {
		var url = aRouter.settings+'profile/cancel-avatar/';
		var params = {};

		ls.hook.marker('cancelAvatarBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#avatar-resize').jqmHide();
				ls.hook.run('ls_user_cancel_avatar_after', [params, result]);
			}
		});

		return false;
	};

	/**
	 * Загрузка временной фотки
	 * @param form
	 * @param input
	 */
	this.uploadFoto = function(form,input) {
		if (!form && input) {
			var form = $('<form method="post" enctype="multipart/form-data"></form>').css({
				'display': 'none'
			}).appendTo('body');
			var clone=input.clone(true);
			input.hide();
			clone.insertAfter(input);
			input.appendTo(form);
		}

		ls.ajaxSubmit(aRouter['settings']+'profile/upload-foto/',form,function(data){
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				this.showResizeFoto(data.sTmpFile);
			}
		}.bind(this));
	};

	/**
	 * Показывает форму для ресайза фотки
	 * @param sImgFile
	 */
	this.showResizeFoto = function(sImgFile) {
		if (this.jcropFoto) {
			this.jcropFoto.destroy();
		}
		$('#foto-resize-original-img').attr('src',sImgFile+'?'+Math.random());
		$('#foto-resize').jqmShow();
		var $this=this;
		$('#foto-resize-original-img').Jcrop({
			minSize: [32,32]
		},function(){
			$this.jcropFoto=this;
			this.setSelect([0,0,500,500]);
		});
	};

	/**
	 * Выполняет ресайз фотки
	 */
	this.resizeFoto = function() {
		if (!this.jcropFoto) {
			return false;
		}
		var url = aRouter.settings+'profile/resize-foto/';
		var params = {size: this.jcropFoto.tellSelect()};

		ls.hook.marker('resizeFotoBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#foto-img').attr('src',result.sFile+'?'+Math.random());
				$('#foto-resize').jqmHide();
				$('#foto-remove').show();
				$('#foto-upload').text(result.sTitleUpload);
				ls.hook.run('ls_user_resize_foto_after', [params, result]);
			}
		});

		return false;
	};

	/**
	 * Удаление фотки
	 */
	this.removeFoto = function() {
		var url = aRouter.settings+'profile/remove-foto/';
		var params = {};

		ls.hook.marker('removeFotoBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#foto-img').attr('src',result.sFile+'?'+Math.random());
				$('#foto-remove').hide();
				$('#foto-upload').text(result.sTitleUpload);
				ls.hook.run('ls_user_remove_foto_after', [params, result]);
			}
		});

		return false;
	};

	/**
	 * Отмена ресайза фотки, подчищаем временный данные
	 */
	this.cancelFoto = function() {
		var url = aRouter.settings+'profile/cancel-foto/';
		var params = {};

		ls.hook.marker('cancelFotoBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#foto-resize').jqmHide();
				ls.hook.run('ls_user_cancel_foto_after', [params, result]);
			}
		});

		return false;
	};

	/**
	 * Валидация полей формы при регистрации
	 * @param aFields
	 */
	this.validateRegistrationFields = function(aFields,sForm) {
		var url = aRouter.registration+'ajax-validate-fields/';
		var params = {fields: aFields};
		if (typeof(sForm)=='string') {
			sForm=$('#'+sForm);
		}

		ls.hook.marker('validateRegistrationFieldsBefore');
		ls.ajax(url, params, function(result) {
			if (!sForm) {
				sForm=$('body'); // поиск полей по всей странице
			}
			$.each(aFields,function(i,aField){
				if (result.aErrors && result.aErrors[aField.field][0]) {
					sForm.find('.validate-error-field-'+aField.field).removeClass('validate-error-hide').addClass('validate-error-show').text(result.aErrors[aField.field][0]);
					sForm.find('.validate-ok-field-'+aField.field).hide();
				} else {
					sForm.find('.validate-error-field-'+aField.field).removeClass('validate-error-show').addClass('validate-error-hide');
					sForm.find('.validate-ok-field-'+aField.field).show();
				}
			});
			ls.hook.run('ls_user_validate_registration_fields_after', [aFields, sForm, result]);
		});
	};

	/**
	 * Валидация конкретного поля формы
	 * @param sField
	 * @param sValue
	 * @param aParams
	 */
	this.validateRegistrationField = function(sField,sValue,sForm,aParams) {
		var aFields=[];
		aFields.push({field: sField, value: sValue, params: aParams || {}});
		this.validateRegistrationFields(aFields,sForm);
	};

	/**
	 * Ajax регистрация пользователя с проверкой полей формы
	 * @param form
	 */
	this.registration = function(form) {
		var url = aRouter.registration+'ajax-registration/';

		this.formLoader(form);
		ls.hook.marker('registrationBefore');
		ls.ajaxSubmit(url, form, function(result) {
			this.formLoader(form,true);
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				if (typeof(form)=='string') {
					form=$('#'+form);
				}
				form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');
				if (result.aErrors) {
					$.each(result.aErrors,function(sField,aErrors){
						if (aErrors[0]) {
							form.find('.validate-error-field-'+sField).removeClass('validate-error-hide').addClass('validate-error-show').text(aErrors[0]);
						}
					});
				} else {
					if (result.sMsg) {
						ls.msg.notice(null,result.sMsg);
					}
					if (result.sUrlRedirect) {
						window.location=result.sUrlRedirect;
					}
				}
				ls.hook.run('ls_user_registration_after', [form, result]);
			}
		}.bind(this));
	};

	/**
	 * Ajax авторизация пользователя с проверкой полей формы
	 * @param form
	 */
	this.login = function(form) {
		var url = aRouter.login+'ajax-login/';

		this.formLoader(form);
		ls.hook.marker('loginBefore');
		ls.ajaxSubmit(url, form, function(result) {
			this.formLoader(form,true);
			if (typeof(form)=='string') {
				form=$('#'+form);
			}
			form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');

			if (result.bStateError) {
				form.find('.validate-error-login').removeClass('validate-error-hide').addClass('validate-error-show').html(result.sMsg);
			} else {
				if (result.sMsg) {
					ls.msg.notice(null,result.sMsg);
				}
				if (result.sUrlRedirect) {
					window.location=result.sUrlRedirect;
				}
				ls.hook.run('ls_user_login_after', [form, result]);
			}
		}.bind(this));
	};

	/**
	 * Показывает лоадер в полях формы
	 * @param form
	 * @param bHide
	 */
	this.formLoader = function(form,bHide) {
		if (typeof(form)=='string') {
			form=$('#'+form);
		}
		form.find('input[type="text"], input[type="password"]').each(function(k,v){
			if (bHide) {
				$(v).removeClass('loader');
			} else {
				$(v).addClass('loader');
			}
		});
	};

	/**
	 * Ajax запрос на смену пароля
	 * @param form
	 */
	this.reminder = function(form) {
		var url = aRouter.login+'ajax-reminder/';

		this.formLoader(form);
		ls.hook.marker('reminderBefore');
		ls.ajaxSubmit(url, form, function(result) {
			this.formLoader(form,true);
			if (typeof(form)=='string') {
				form=$('#'+form);
			}
			form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');

			if (result.bStateError) {
				form.find('.validate-error-reminder').removeClass('validate-error-hide').addClass('validate-error-show').text(result.sMsg);
			} else {
				form.find('input').val('');
				if (result.sMsg) {
					ls.msg.notice(null,result.sMsg);
				}
				if (result.sUrlRedirect) {
					window.location=result.sUrlRedirect;
				}
				ls.hook.run('ls_user_reminder_after', [form, result]);
			}
		}.bind(this));
	};

	/**
	 * Ajax запрос на ссылку активации
	 * @param form
	 */
	this.reactivation = function(form) {
		var url = aRouter.login+'ajax-reactivation/';

		ls.hook.marker('reactivationBefore');
		ls.ajaxSubmit(url, form, function(result) {
			if (typeof(form)=='string') {
				form=$('#'+form);
			}
			form.find('.validate-error-show').removeClass('validate-error-show').addClass('validate-error-hide');

			if (result.bStateError) {
				form.find('.validate-error-reactivation').removeClass('validate-error-hide').addClass('validate-error-show').text(result.sMsg);
			} else {
				form.find('input').val('');
				if (result.sMsg) {
					ls.msg.notice(null,result.sMsg);
				}
				ls.hook.run('ls_user_reactivation_after', [form, result]);
			}
		});
	};

	/**
	 * Поиск пользователей
	 */
	this.searchUsers = function(form) {
		var url = aRouter['people']+'ajax-search/';
		var inputSearch=$('#'+form).find('input');
		inputSearch.addClass('loader');

		ls.hook.marker('searchUsersBefore');
		ls.ajaxSubmit(url, form, function(result){
			inputSearch.removeClass('loader');
			if (result.bStateError) {
				$('#users-list-search').hide();
				$('#users-list-original').show();
			} else {
				$('#users-list-original').hide();
				$('#users-list-search').html(result.sText).show();
				ls.hook.run('ls_user_search_users_after',[form, result]);
			}
		});
	};

	/**
	 * Поиск пользователей по началу логина
	 */
	this.searchUsersByPrefix = function(sPrefix,obj) {
		obj=$(obj);
		var url = aRouter['people']+'ajax-search/';
		var params = {user_login: sPrefix, isPrefix: 1};
		$('#search-user-login').addClass('loader');

		ls.hook.marker('searchUsersByPrefixBefore');
		ls.ajax(url, params, function(result){
			$('#search-user-login').removeClass('loader');
			$('#user-prefix-filter').find('.active').removeClass('active');
			obj.parent().addClass('active');
			if (result.bStateError) {
				$('#users-list-search').hide();
				$('#users-list-original').show();
			} else {
				$('#users-list-original').hide();
				$('#users-list-search').html(result.sText).show();
				ls.hook.run('ls_user_search_users_by_prefix_after',[sPrefix, obj, result]);
			}
		});
		return false;
	};

	/**
	 * Подписка
	 */
	this.followToggle = function(obj, iUserId) {
		if ($(obj).hasClass('followed')) {
			ls.stream.unsubscribe(iUserId);
			$(obj).toggleClass('followed').text(ls.lang.get('profile_user_follow'));
		} else {
			ls.stream.subscribe(iUserId);
			$(obj).toggleClass('followed').text(ls.lang.get('profile_user_unfollow'));
		}
		return false;
	};


	return this;
}).call(ls.user || {},jQuery);
var ls = ls || {};

ls.userfeed =( function ($) {
	this.isBusy = false;
	
	this.subscribe = function (sType, iId) {
		var url = aRouter['feed']+'subscribe/';
		var params = {'type':sType, 'id':iId};
		
		ls.hook.marker('subscribeBefore');
		ls.ajax(url, params, function(data) { 
			if (!data.bStateError) {
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_userfeed_subscribe_after',[sType, iId, data]);
			}
		});
	}
	
	this.unsubscribe = function (sType, iId) {
		var url = aRouter['feed']+'unsubscribe/';
		var params = {'type':sType, 'id':iId};
		
		ls.hook.marker('unsubscribeBefore');
		ls.ajax(url, params, function(data) { 
			if (!data.bStateError) {
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_userfeed_unsubscribe_after',[sType, iId, data]);
			}
		});
	}
	
	this.appendUser = function() {
		var sLogin = $('#userfeed_users_complete').val();
		if (!sLogin) return;
		
		var url = aRouter['feed']+'subscribeByLogin/';
		var params = {'login':sLogin};
		
		ls.hook.marker('appendUserBefore');
		ls.ajax(url, params, function(data) {
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				$('#userfeed_no_subscribed_users').remove();
				var checkbox = $('#usf_u_'+data.uid);
				if (checkbox.length) {
					if (checkbox.attr('checked')) {
						ls.msg.error(data.lang_error_title,data.lang_error_msg);
						return;
					} else {
						checkbox.attr('checked', 'on');
						ls.msg.notice(data.sMsgTitle,data.sMsg);
					}
				} else {
					var liElement=$('<li><input type="checkbox" class="userfeedUserCheckbox input-checkbox" id="usf_u_'+data.uid+'" checked="checked" onClick="if ($(this).get(\'checked\')) {ls.userfeed.subscribe(\'users\','+data.uid+')} else {ls.userfeed.unsubscribe(\'users\','+data.uid+')}" /><a href="'+data.user_web_path+'">'+data.user_login+'</a></li>');
					$('#userfeed_block_users_list').append(liElement);
					ls.msg.notice(data.sMsgTitle,data.sMsg);
				}
			}
		});
	}
	
	this.getMore = function () {
		if (this.isBusy) {
			return;
		}
		var lastId = $('#userfeed_last_id').val();
		if (!lastId) return;
		$('#userfeed_get_more').addClass('userfeed_loading');
		this.isBusy = true;
		
		var url = aRouter['feed']+'get_more/';
		var params = {'last_id':lastId};
		
		ls.hook.marker('getMoreBefore');
		ls.ajax(url, params, function(data) {
			if (!data.bStateError && data.topics_count) {
				$('#userfeed_loaded_topics').append(data.result);
				$('#userfeed_last_id').attr('value', data.iUserfeedLastId);
			}
			if (!data.topics_count) {
				$('#userfeed_get_more').hide();
			}
			$('#userfeed_get_more').removeClass('userfeed_loading');
			ls.hook.run('ls_userfeed_get_more_after',[lastId, data]);
			this.isBusy = false;
		}.bind(this));
	}
	
	return this;
}).call(ls.userfeed || {},jQuery);
var ls = ls || {};

ls.userfield =( function ($) {

	this.iCountMax = 2;

	this.showAddForm = function(){
		$('#user_fields_form_name').val('');
		$('#user_fields_form_title').val('');
		$('#user_fields_form_id').val('');
		$('#user_fields_form_pattern').val('');
		$('#user_fields_form_type').val('');
		$('#user_fields_form_action').val('add');
		$('#userfield_form').jqmShow(); 
	};
	
	this.showEditForm = function(id) {
		$('#user_fields_form_action').val('update');
		var name = $('#field_'+id+' .userfield_admin_name').text();
		var title = $('#field_'+id+' .userfield_admin_title').text();
		var pattern = $('#field_'+id+' .userfield_admin_pattern').text();
		var type = $('#field_'+id+' .userfield_admin_type').text();
		$('#user_fields_form_name').val(name);
		$('#user_fields_form_title').val(title);
		$('#user_fields_form_pattern').val(pattern);
		$('#user_fields_form_type').val(type);
		$('#user_fields_form_id').val(id);
		$('#userfield_form').jqmShow(); 
	};

	this.applyForm = function(){
		$('#userfield_form').jqmHide(); 
		if ($('#user_fields_form_action').val() == 'add') {
			this.addUserfield();
		} else if ($('#user_fields_form_action').val() == 'update')  {
			this.updateUserfield();
		}
	};

	this.addUserfield = function() {
		var name = $('#user_fields_form_name').val();
		var title = $('#user_fields_form_title').val();
		var pattern = $('#user_fields_form_pattern').val();
		var type = $('#user_fields_form_type').val();

		var url = aRouter['admin']+'userfields';
		var params = {'action':'add', 'name':name,  'title':title,  'pattern':pattern,  'type':type};
		
		ls.hook.marker('addUserfieldBefore');
		ls.ajax(url, params, function(data) { 
			if (!data.bStateError) {
				liElement = $('<li id="field_'+data.id+'"><span class="userfield_admin_name"></span > / <span class="userfield_admin_title"></span> / <span class="userfield_admin_pattern"></span> / <span class="userfield_admin_type"></span>'
					+'<div class="userfield-actions"><a class="icon-edit" href="javascript:ls.userfield.showEditForm('+data.id+')"></a> '
					+'<a class="icon-remove" href="javascript:ls.userfield.deleteUserfield('+data.id+')"></a></div>')
				;
				$('#user_field_list').append(liElement);
				$('#field_'+data.id+' .userfield_admin_name').text(name);
				$('#field_'+data.id+' .userfield_admin_title').text(title);
				$('#field_'+data.id+' .userfield_admin_pattern').text(pattern);
				$('#field_'+data.id+' .userfield_admin_type').text(type);
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_userfield_add_userfield_after',[params, data],liElement);
			} else {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			}
		});
	};

	this.updateUserfield = function() {
		var id = $('#user_fields_form_id').val();
		var name = $('#user_fields_form_name').val();
		var title = $('#user_fields_form_title').val();
		var pattern = $('#user_fields_form_pattern').val();
		var type = $('#user_fields_form_type').val();

		var url = aRouter['admin']+'userfields';
		var params = {'action':'update', 'id':id, 'name':name,  'title':title,  'pattern':pattern, 'type':type};
		
		ls.hook.marker('updateUserfieldBefore');
		ls.ajax(url, params, function(data) { 
			if (!data.bStateError) {
				$('#field_'+id+' .userfield_admin_name').text(name);
				$('#field_'+id+' .userfield_admin_title').text(title);
				$('#field_'+id+' .userfield_admin_pattern').text(pattern);
				$('#field_'+id+' .userfield_admin_type').text(type);
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_userfield_update_userfield_after',[params, data]);
			} else {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			}
		});
	};

	this.deleteUserfield = function(id) {
		if (!confirm(ls.lang.get('user_field_delete_confirm'))) {return;}
		
		var url = aRouter['admin']+'userfields';
		var params = {'action':'delete', 'id':id};
		
		ls.hook.marker('deleteUserfieldBefore');
		ls.ajax(url, params, function(data) { 
			if (!data.bStateError) {
				$('#field_'+id).remove();
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_userfield_update_userfield_after',[params, data]);
			} else {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			}
		});
	};

	this.addFormField = function() {
		var tpl=$('#profile_user_field_template').clone();
		/**
		 * Находим доступный тип контакта
		 */
		var value;
		tpl.find('select').find('option').each(function(k,v){
			if (this.getCountFormField($(v).val())<this.iCountMax) {
				value=$(v).val();
				return false;
			}
		}.bind(this));

		if (value) {
			tpl.find('select').val(value);
			$('#user-field-contact-contener').append(tpl.show());
		} else {
			ls.msg.error('',ls.lang.get('settings_profile_field_error_max',{count: this.iCountMax}));
		}
		return false;
	};

	this.changeFormField = function(obj) {
		var iCount=this.getCountFormField($(obj).val());
		if (iCount>this.iCountMax) {
			ls.msg.error('',ls.lang.get('settings_profile_field_error_max',{count: this.iCountMax}));
		}
	};

	this.getCountFormField = function(value) {
		var iCount=0;
		$('#user-field-contact-contener').find('select').each(function(k,v){
			if (value==$(v).val()) {
				iCount++;
			}
		});
		return iCount;
	};

	this.removeFormField = function(obj) {
		$(obj).parent('.js-user-field-item').detach();
		return false;
	};
	return this;
}).call(ls.userfield || {},jQuery);
var ls = ls || {};

ls.stream =( function ($) {
	this.isBusy = false;
	this.dateLast = null;

	this.subscribe = function (iTargetUserId) {
		var url = aRouter['stream']+'subscribe/';
		var params = {'id':iTargetUserId};

		ls.hook.marker('subscribeBefore');
		ls.ajax(url, params, function(data) {
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_stream_subscribe_after',[params,data]);
			}
		});
	};

	this.unsubscribe = function (iId) {
		var url = aRouter['stream']+'unsubscribe/';
		var params = {'id':iId};

		ls.hook.marker('unsubscribeBefore');
		ls.ajax(url, params, function(data) {
			if (!data.bStateError) {
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_stream_unsubscribe_after',[params,data]);
			}
		});
	};

	this.switchEventType = function (iType) {
		var url = aRouter['stream']+'switchEventType/';
		var params = {'type':iType};

		ls.hook.marker('switchEventTypeBefore');
		ls.ajax(url, params, function(data) {
			if (!data.bStateError) {
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_stream_switch_event_type_after',[params,data]);
			}
		});
	};

	this.appendUser = function() {
		var sLogin = $('#stream_users_complete').val();
		if (!sLogin) return;

		var url = aRouter['stream']+'subscribeByLogin/';
		var params = {'login':sLogin};

		ls.hook.marker('appendUserBefore');
		ls.ajax(url, params, function(data) {
			if (!data.bStateError) {
				$('#stream_no_subscribed_users').remove();
				var checkbox = $('#strm_u_'+data.uid);
				if (checkbox.length) {
					if (checkbox.attr('checked')) {
						ls.msg.error(ls.lang.get('error'),ls.lang.get('stream_subscribes_already_subscribed'));
					} else {
						checkbox.attr('checked', 'on');
						ls.msg.notice(data.sMsgTitle,data.sMsg);
					}
				} else {
					var liElement=$('<li><input type="checkbox" class="streamUserCheckbox input-checkbox" id="strm_u_'+data.uid+'" checked="checked" onClick="if ($(this).get(\'checked\')) {ls.stream.subscribe('+data.uid+')} else {ls.stream.unsubscribe('+data.uid+')}" /> <a href="'+data.user_web_path+'">'+data.user_login+'</a></li>');
					$('#stream_block_users_list').append(liElement);
					ls.msg.notice(data.sMsgTitle,data.sMsg);
				}
				ls.hook.run('ls_stream_append_user_after',[checkbox.length,data]);
			} else {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			}
		});
	};

	this.getMore = function () {
		if (this.isBusy) {
			return;
		}
		var lastId = $('#stream_last_id').val();
		if (!lastId) return;
		$('#stream_get_more').addClass('stream_loading');
		this.isBusy = true;

		var url = aRouter['stream']+'get_more/';
		var params = {'last_id':lastId,'date_last':this.dateLast};

		ls.hook.marker('getMoreBefore');
		ls.ajax(url, params, function(data) {
			if (!data.bStateError && data.events_count) {
				$('#stream-list').append(data.result);
				$('#stream_last_id').attr('value', data.iStreamLastId);
			}
			if (!data.events_count) {
				$('#stream_get_more').hide();
			}
			$('#stream_get_more').removeClass('stream_loading');
			ls.hook.run('ls_stream_get_more_after',[lastId, data]);
			this.isBusy = false;
		}.bind(this));
	};

	this.getMoreAll = function () {
		if (this.isBusy) {
			return;
		}
		var lastId = $('#stream_last_id').val();
		if (!lastId) return;
		$('#stream_get_more').addClass('stream_loading');
		this.isBusy = true;

		var url = aRouter['stream']+'get_more_all/';
		var params = {'last_id':lastId,'date_last':this.dateLast};

		ls.hook.marker('getMoreAllBefore');
		ls.ajax(url, params, function(data) {
			if (!data.bStateError && data.events_count) {
				$('#stream-list').append(data.result);
				$('#stream_last_id').attr('value', data.iStreamLastId);
			}
			if (!data.events_count) {
				$('#stream_get_more').hide();
			}
			$('#stream_get_more').removeClass('stream_loading');
			ls.hook.run('ls_stream_get_more_all_after',[lastId, data]);
			this.isBusy = false;
		}.bind(this));
	};

	this.getMoreByUser = function (iUserId) {
		if (this.isBusy) {
			return;
		}
		var lastId = $('#stream_last_id').val();
		if (!lastId) return;
		$('#stream_get_more').addClass('stream_loading');
		this.isBusy = true;

		var url = aRouter['stream']+'get_more_user/';
		var params = {'last_id':lastId, user_id: iUserId,'date_last':this.dateLast};

		ls.hook.marker('getMoreByUserBefore');
		ls.ajax(url, params, function(data) {
			if (!data.bStateError && data.events_count) {
				$('#stream-list').append(data.result);
				$('#stream_last_id').attr('value', data.iStreamLastId);
			}
			if (!data.events_count) {
				$('#stream_get_more').hide();
			}
			$('#stream_get_more').removeClass('stream_loading');
			ls.hook.run('ls_stream_get_more_by_user_after',[lastId, iUserId, data]);
			this.isBusy = false;
		}.bind(this));
	};

	return this;
}).call(ls.stream || {},jQuery);
var ls = ls || {};

ls.photoset =( function ($) {
	
	this.idLast=0;
	this.isLoading=false;
	this.swfu;
	
	this.initSwfUpload = function(opt) {
		opt=opt || {};
		opt.button_placeholder_id = 'photoset-start-upload';
		opt.post_params.ls_photoset_target_tmp = $.cookie('ls_photoset_target_tmp') ? $.cookie('ls_photoset_target_tmp') : 0;
		
		$(ls.swfupload).unbind('load').bind('load',function() {
			this.swfu = ls.swfupload.init(opt);

			$(this.swfu).bind('eUploadProgress',this.swfHandlerUploadProgress);
			$(this.swfu).bind('eFileDialogComplete',this.swfHandlerFileDialogComplete);
			$(this.swfu).bind('eUploadSuccess',this.swfHandlerUploadSuccess);
			$(this.swfu).bind('eUploadComplete',this.swfHandlerUploadComplete);
		}.bind(this));
		
		ls.swfupload.loadSwf();
	}
	
	this.swfHandlerUploadProgress = function(e, file, bytesLoaded, percent) {
		$('#photoset_photo_empty_progress').text(file.name+': '+( percent==100 ? 'resize..' : percent +'%'));
	}
	
	this.swfHandlerFileDialogComplete = function(e, numFilesSelected, numFilesQueued) {
		if (numFilesQueued>0) {
			ls.photoset.addPhotoEmpty();
		}
	}
	
	this.swfHandlerUploadSuccess = function(e, file, serverData) {
		ls.photoset.addPhoto(jQuery.parseJSON(serverData));
	}
	
	this.swfHandlerUploadComplete = function(e, file, next) {
		if (next>0) {
			ls.photoset.addPhotoEmpty();
		}
	}
	
	this.addPhotoEmpty = function() {
		template = '<li id="photoset_photo_empty"><img src="'+DIR_STATIC_SKIN + '/images/loader.gif'+'" alt="image" style="margin-left: 35px;margin-top: 20px;" />'
					+'<div id="photoset_photo_empty_progress" style="height: 60px;width: 350px;padding: 3px;border: 1px solid #DDDDDD;"></div><br /></li>';
		$('#swfu_images').append(template);
	}
	
	this.addPhoto = function(response) {
		$('#photoset_photo_empty').remove();
		if (!response.bStateError) {
			template = '<li id="photo_'+response.id+'"><img src="'+response.file+'" alt="image" />'
						+'<textarea onBlur="ls.photoset.setPreviewDescription('+response.id+', this.value)"></textarea><br />'
						+'<a href="javascript:ls.photoset.deletePhoto('+response.id+')" class="image-delete">'+ls.lang.get('topic_photoset_photo_delete')+'</a>'
						+'<span id="photo_preview_state_'+response.id+'" class="photo-preview-state"><a href="javascript:ls.photoset.setPreview('+response.id+')" class="mark-as-preview">'+ls.lang.get('topic_photoset_mark_as_preview')+'</a></span></li>';
			$('#swfu_images').append(template);
			ls.msg.notice(response.sMsgTitle,response.sMsg);
		} else {
			ls.msg.error(response.sMsgTitle,response.sMsg);
		}
		ls.photoset.closeForm();
	}

	this.deletePhoto = function(id)
	{
		if (!confirm(ls.lang.get('topic_photoset_photo_delete_confirm'))) {return;}
		ls.ajax(aRouter['photoset']+'deleteimage', {'id':id}, function(response){
			if (!response.bStateError) {
				$('#photo_'+id).remove();
				ls.msg.notice(response.sMsgTitle,response.sMsg);
			} else {
				ls.msg.error(response.sMsgTitle,response.sMsg);
			}
		});
	}

	this.setPreview =function(id)
	{
		$('#topic_main_photo').val(id);

		$('.marked-as-preview').each(function (index, el) {
			$(el).removeClass('marked-as-preview');
			tmpId = $(el).attr('id').slice($(el).attr('id').lastIndexOf('_')+1);
			$('#photo_preview_state_'+tmpId).html('<a href="javascript:ls.photoset.setPreview('+tmpId+')" class="mark-as-preview">'+ls.lang.get('topic_photoset_mark_as_preview')+'</a>');
		});
		$('#photo_'+id).addClass('marked-as-preview');
		$('#photo_preview_state_'+id).html(ls.lang.get('topic_photoset_is_preview'));
	}

	this.setPreviewDescription = function(id, text)
	{
		ls.ajax(aRouter['photoset']+'setimagedescription', {'id':id, 'text':text},  function(result){
			if (!result.bStateError) {

			} else {
				ls.msg.error('Error','Please try again later');
			}
		}
		)
	}

	this.getMore = function(topic_id)
	{
		if (this.isLoading) return;
		this.isLoading=true;
				
		ls.ajax(aRouter['photoset']+'getmore', {'topic_id':topic_id, 'last_id':this.idLast}, function(result){
			this.isLoading=false;
			if (!result.bStateError) {
				if (result.photos) {
					$.each(result.photos, function(index, photo) {
						var image = '<li><a class="photoset-image" href="'+photo.path+'" rel="[photoset]" title="'+photo.description+'"><img src="'+photo.path_thumb+'" alt="'+photo.description+'" /></a></li>';
						$('#topic-photo-images').append(image);
						this.idLast=photo.id;
						$('.photoset-image').unbind('click');
						$('.photoset-image').prettyPhoto({
							social_tools:'',
							show_title: false,
							slideshow:false,
							deeplinking: false
						});
					}.bind(this));
				}
				if (!result.bHaveNext || !result.photos) {
					$('#topic-photo-more').remove();
				}
			} else {
				ls.msg.error('Error','Please try again later');
			}
		}.bind(this));
	}

	this.upload = function()
	{
		ls.photoset.addPhotoEmpty();
		ls.ajaxSubmit(aRouter['photoset']+'upload/',$('#photoset-upload-form'),function(data){
			if (data.bStateError) {
				$('#photoset_photo_empty').remove();
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				ls.photoset.addPhoto(data);
			}
		});
		ls.photoset.closeForm();
	}

	this.closeForm = function()
	{
		$('#photoset-upload-form').jqmHide();
	}

	this.showForm = function()
	{
		var $select = $('#photoset-start-upload');
		if ($select.length) {
			var pos = $select.offset();
			w = $select.outerWidth();
			h = $select.outerHeight();
			t = pos.top + h - 30  + 'px';
			l = pos.left - 15 + 'px';
			$('#photoset-upload-form').css({'top':t,'left':l});
		}
		$('#photoset-upload-form').show();
	}
	
	this.showMainPhoto = function(id) {
		$('#photoset-main-preview-'+id).css('width',$('#photoset-main-image-'+id).outerWidth());
		$('#photoset-photo-count-'+id).show();
		$('#photoset-photo-desc-'+id).show();
	}
	
	return this;
}).call(ls.photoset || {},jQuery);
var ls = ls || {};
ls.toolbar = ls.toolbar || {};

/**
 * Функционал тул-бара (плавающая пимпа) списка топиков
 */
ls.toolbar.topic = (function ($) {

	this.iCurrentTopic=-1;

	this.init = function() {
		var vars = [], hash;
		var hashes = window.location.hash.replace('#','').split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}

		if (vars.goTopic!==undefined) {
			if (vars.goTopic=='last') {
				this.iCurrentTopic=$('.js-topic').length-2;
			} else {
				this.iCurrentTopic=parseInt(vars.goTopic)-1;
			}
			this.goNext();
		}
	};

	this.reset = function() {
		this.iCurrentTopic=-1;
	};

	/**
	 * Прокрутка следующему топику
	 */
	this.goNext = function() {
		this.iCurrentTopic++;
		var topic=$('.js-topic:eq('+this.iCurrentTopic+')');
		if (topic.length) {
			$.scrollTo(topic, 500);
		} else {
			this.iCurrentTopic=$('.js-topic').length-1;
			// переход на следующую страницу
			var page=$('.js-paging-next-page');
			if (page.length && page.attr('href')) {
				window.location=page.attr('href')+'#goTopic=0';
			}
		}

		return false;
	};

	/**
	 * Прокрутка предыдущему топику
	 */
	this.goPrev = function() {
		this.iCurrentTopic--;
		if (this.iCurrentTopic<0) {
			this.iCurrentTopic=0;
			// на предыдущую страницу
			var page=$('.js-paging-prev-page');
			if (page.length && page.attr('href')) {
				window.location=page.attr('href')+'#goTopic=last';
			}
		} else {
			var topic=$('.js-topic:eq('+this.iCurrentTopic+')');
			if (topic.length) {
				$.scrollTo(topic, 500);
			}
		}
		return false;
	};

	return this;
}).call(ls.toolbar.topic || {},jQuery);

/**
 * Функционал кнопки "UP"
 */
ls.toolbar.scroll = (function ($) {

	this.goUp = function() {
		ls.toolbar.topic.reset();
		$.scrollTo(0, 400);
		return false;
	};

	this.goDown = function() {
		ls.toolbar.topic.reset();
		$.scrollTo("#footer", 400);
		return false;
	};


	return this;
}).call(ls.toolbar.up || {},jQuery);
var ls = ls || {};

/**
* Различные настройки
*/
ls.settings = (function ($) {

	this.getMarkitup = function() {
		return {
			onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
			onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
			onTab:    		{keepDefault:false, replaceWith:'    '},
			markupSet:  [
				{name: "sup", className:'fa fa-superscript', openWith:'<sup>', closeWith:'</sup>' },
				{name: "sub", className:'fa fa-subscript', openWith:'<sub>', closeWith:'</sub>' },
				{name: "small", className:'fa fa-text-height', openWith:'<small>', closeWith:'</small>' },
				{separator:'---------------' },
				{name: "left", className:'fa fa-align-left', openWith:'<span class="left">', closeWith:'</span>' },
				{name: "center", className:'fa fa-align-center', openWith:'<span class="center">', closeWith:'</span>' },
				{name: "right", className:'fa fa-align-right', openWith:'<span class="right">', closeWith:'</span>' },
				{separator:'---------------' },
				{name: ls.lang.get('panel_b'), className:'fa fa-bold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
				{name: ls.lang.get('panel_i'), className:'fa fa-italic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
				{name: ls.lang.get('panel_s'), className:'fa fa-strikethrough', key:'S', openWith:'<s>', closeWith:'</s>' },
				{name: ls.lang.get('panel_u'), className:'fa fa-underline', key:'U', openWith:'<u>', closeWith:'</u>' },
				{name: ls.lang.get('panel_quote'), className:'fa fa-quote-left', key:'Q', replaceWith: function(m) { if (m.selectionOuter) return '<blockquote>'+m.selectionOuter+'</blockquote>'; else if (m.selection) return '<blockquote>'+m.selection+'</blockquote>'; else return '<blockquote></blockquote>' } },
				{name: ls.lang.get('panel_code'), className:'fa fa-code', openWith:'<code>', closeWith:'</code>' },
				{separator:'---------------' },
				{name: ls.lang.get('panel_list'), className:'fa fa-list-ul', openWith:'    <li>', closeWith:'</li>', multiline: true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>' },
				{name: ls.lang.get('panel_list'), className:'fa fa-list-ol', openWith:'    <li>', closeWith:'</li>', multiline: true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>' },
				{name: ls.lang.get('panel_list_li'), className:'fa fa-hashtag', openWith:'<li>', closeWith:'</li>' },
				{separator:'---------------' },
				{name: ls.lang.get('panel_image'), className:'fa fa-picture-o', key:'P', beforeInsert: function(h) { jQuery('#window_upload_img').jqmShow(); } },
				{name: ls.lang.get('panel_video'), className:'fa fa-video-camera', replaceWith:'<video>[!['+ls.lang.get('panel_video_promt')+':!:http://]!]</video>' },
				{name: ls.lang.get('panel_url'), className:'fa fa-link', key:'L', openWith:'<a target="_blank" href="[!['+ls.lang.get('panel_url_promt')+':!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },
				{name: ls.lang.get('panel_user'), className:'fa fa-user', replaceWith:'<ls user="[!['+ls.lang.get('panel_user_promt')+']!]" />' },
				{separator:'---------------' },
				{name: "Серый спойлер", className:'fa fa-h-square', key:'G', replaceWith: function(m) { if (m.selectionOuter) return '<span class="spoiler-gray">'+m.selectionOuter+'</span>'; else if (m.selection) return '<span class="spoiler-gray">'+m.selection+'</span>'; else return '<span class="spoiler-gray"></span>' } },
				{name: "Спойлер", className:'fa fa-caret-square-o-down', key:'R', replaceWith: function(m) { if (m.selectionOuter) return '<span class="spoiler"><span class="spoiler-title spoiler-close">' + prompt('Спойлер', "Спойлер") + '</span><span class="spoiler-body">\n'+m.selectionOuter+'\n</span></span>'; else if (m.selection) return '<span class="spoiler"><span class="spoiler-title spoiler-close">' + prompt('Спойлер', "Спойлер") + '</span><span class="spoiler-body">\n'+m.selection+'\n</span></span>'; else return '<span class="spoiler"><span class="spoiler-title spoiler-close">' + prompt('Спойлер', "Спойлер") + '</span><span class="spoiler-body">\nтекст спойлера\n</span></span>'} },
				{separator:'---------------' },
				{name: ls.lang.get('panel_clear_tags'), className:'fa fa-times', replaceWith: function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },
				{name: ls.lang.get('panel_cut'), className:'fa fa-cut', replaceWith: function(markitup) { if (markitup.selection) return '<cut name="'+markitup.selection+'">'; else return '<cut>' }}
			]
		}
	};

	this.getMarkitupComment = function() {
		return {
			onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
			onTab:    		{keepDefault:false, replaceWith:'    '},
			markupSet:  [
				{name: ls.lang.get('panel_b'), className:'fa fa-bold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
				{name: ls.lang.get('panel_i'), className:'fa fa-italic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
				{name: ls.lang.get('panel_s'), className:'fa fa-strikethrough', key:'S', openWith:'<s>', closeWith:'</s>' },
				{name: ls.lang.get('panel_u'), className:'fa fa-underline', key:'U', openWith:'<u>', closeWith:'</u>' },
				{name: "sup", className:'fa fa-superscript', openWith:'<sup>', closeWith:'</sup>' },
				{name: "sup", className:'fa fa-subscript', openWith:'<sub>', closeWith:'</sub>' },
				{name: "small", className:'fa fa-text-height', openWith:'<small>', closeWith:'</small>' },
				{separator:'---------------' },
				{name: "left", className:'fa fa-align-left', openWith:'<span class="left">', closeWith:'</span>' },
				{name: "center", className:'fa fa-align-center', openWith:'<span class="center">', closeWith:'</span>' },
				{name: "right", className:'fa fa-align-right', openWith:'<span class="right">', closeWith:'</span>' },
				{separator:'---------------' },
				{name: ls.lang.get('panel_quote'), className:'fa fa-quote-left', key:'Q', replaceWith: function(m) { if (m.selectionOuter) return '<blockquote>'+m.selectionOuter+'</blockquote>'; else if (m.selection) return '<blockquote>'+m.selection+'</blockquote>'; else return '<blockquote></blockquote>' } },
				{name: ls.lang.get('panel_code'), className:'fa fa-code', openWith:'<code>', closeWith:'</code>' },
				{name: ls.lang.get('panel_image'), className:'fa fa-picture-o', key:'P', beforeInsert: function(h) { jQuery('#window_upload_img').jqmShow(); } },
				{name: ls.lang.get('panel_url'), className:'fa fa-link', key:'L', openWith:'<a target="_blank" href="[!['+ls.lang.get('panel_url_promt')+':!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },
				{name: ls.lang.get('panel_video'), className:'fa fa-video-camera', replaceWith:'<video>[!['+ls.lang.get('panel_video_promt')+':!:http://]!]</video>' },
				{name: ls.lang.get('panel_user'), className:'fa fa-user', replaceWith:'<ls user="[!['+ls.lang.get('panel_user_promt')+']!]" />' },
				{separator:'---------------' },
				{name: "Серый спойлер", className:'fa fa-h-square', key:'G', replaceWith: function(m) { if (m.selectionOuter) return '<span class="spoiler-gray">'+m.selectionOuter+'</span>'; else if (m.selection) return '<span class="spoiler-gray">'+m.selection+'</span>'; else return '<span class="spoiler-gray"></span>' } },
				{name: "Спойлер", className:'fa fa-caret-square-o-down', key:'R', replaceWith: function(m) { var title = prompt('Спойлер', "Спойлер"); if(title) if (m.selectionOuter) return '<span class="spoiler"><span class="spoiler-title spoiler-close">' + title + '</span><span class="spoiler-body">\n'+m.selectionOuter+'\n</span></span>'; else if (m.selection) return '<span class="spoiler"><span class="spoiler-title spoiler-close">' + title + '</span><span class="spoiler-body">\n'+m.selection+'\n</span></span>'; else return '<span class="spoiler"><span class="spoiler-title spoiler-close">' + title + '</span><span class="spoiler-body">\nтекст спойлера\n</span></span>' } },
				{separator:'---------------' },
				{name: ls.lang.get('panel_clear_tags'), className:'fa fa-times', replaceWith: function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } }
			]
		}
	};

	this.getTinymce = function() {
		return {
			mode : 									"specific_textareas",
			editor_selector : 						"mce-editor",
			theme : 								"advanced",
			content_css : 							DIR_STATIC_SKIN + "/css/reset.css" + "," + DIR_STATIC_SKIN + "/css/tinymce.css?" + new Date().getTime(),
			theme_advanced_toolbar_location : 		"top",
			theme_advanced_toolbar_align : 			"left",
			theme_advanced_buttons1 : 				"lshselect,bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,|,lslink,unlink,lsvideo,lsimage,pagebreak,code",
			theme_advanced_buttons2 : 				"",
			theme_advanced_buttons3 : 				"",
			theme_advanced_statusbar_location : 	"bottom",
			theme_advanced_resizing : 				true,
			theme_advanced_resize_horizontal : 		0,
			theme_advanced_resizing_use_cookie : 	0,
			theme_advanced_path : 					false,
			object_resizing : 						true,
			force_br_newlines :						true,
			forced_root_block : 					'', // Needed for 3.x
			force_p_newlines : 						false,
			plugins : 								"lseditor,safari,inlinepopups,media,pagebreak,autoresize",
			convert_urls : 							false,
			extended_valid_elements : 				"embed[src|type|allowscriptaccess|allowfullscreen|width|height]",
			pagebreak_separator :					"<cut>",
			media_strict : 							false,
			language : 								TINYMCE_LANG,
			inline_styles:							false,
			formats : {
				underline : 	{inline : 'u', exact : true},
				strikethrough : {inline : 's', exact : true}
			}
		}
	};

	this.getTinymceComment = function() {
		return {
			mode : 									"textareas",
			theme : 								"advanced",
			content_css : 							DIR_STATIC_SKIN + "/css/reset.css" + "," + DIR_STATIC_SKIN + "/css/tinymce.css?" + new Date().getTime(),
			theme_advanced_toolbar_location : 		"top",
			theme_advanced_toolbar_align : 			"left",
			theme_advanced_buttons1 : 				"bold,italic,underline,strikethrough,lslink,lsquote",
			theme_advanced_buttons2 : 				"",
			theme_advanced_buttons3 : 				"",
			theme_advanced_statusbar_location : 	"bottom",
			theme_advanced_resizing : 				true,
			theme_advanced_resize_horizontal : 		0,
			theme_advanced_resizing_use_cookie : 	0,
			theme_advanced_path : 					false,
			object_resizing : 						true,
			force_br_newlines : 					true,
			forced_root_block : 					'', // Needed for 3.x
			force_p_newlines : 						false,
			plugins : 								"lseditor,safari,inlinepopups,media,pagebreak,autoresize",
			convert_urls : 							false,
			extended_valid_elements : 				"embed[src|type|allowscriptaccess|allowfullscreen|width|height]",
			pagebreak_separator :					"<cut>",
			media_strict : 							false,
			language : 								TINYMCE_LANG,
			inline_styles:							false,
			formats : {
				underline : 	{inline : 'u', exact : true},
				strikethrough : {inline : 's', exact : true}
			},
			setup : function(ed) {
				// Display an alert onclick
				ed.onKeyPress.add(function(ed, e) {
					key = e.keyCode || e.which;
					if(e.ctrlKey && (key == 13)) {
						$('#comment-button-submit').click();
						return false;
					}
				});
			}
		}
	};

	return this;
}).call(ls.settings || {},jQuery);
;
var ls = ls || {};

/**
 * Опросы
 */
ls.topic = (function ($) {

	this.preview = function(form, preview) {
		form=$('#'+form);
		preview=$('#'+preview);
		var url = aRouter['ajax']+'preview/topic/';
		ls.hook.marker('previewBefore');
		ls.ajaxSubmit(url, form, function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				preview.show().html(result.sText);
				ls.hook.run('ls_topic_preview_after',[form, preview, result]);
			}
		});
	};

	this.insertImageToEditor = function(sUrl,sAlign,sTitle) {
		sAlign=sAlign=='center' ? 'class="image-center"' : 'align="'+sAlign+'"';
		$.markItUp({replaceWith: '<img src="'+sUrl+'" title="'+sTitle+'" '+sAlign+' />'} );
		$('#window_upload_img').find('input[type="text"]').val('');
		$('#window_upload_img').jqmHide();
		return false;
	};

	return this;
}).call(ls.topic || {},jQuery);
/**
 * JavaScript-hooks
 *
 * Examples:
 *
 * - inject function call/code at top of function body
 * ls.hook.inject([ls.lang,'get'], function(){ls.msg.notice('lang debug');})});
 * ls.hook.inject([ls,'ajax'], 'alert(url)');
 *
 * - add and call hooks
 * ls.hook.add('somefunc_hook1_name', function(param1, param2){ ... });
 *
 * function someFunc(..params..){
 * 	//code
 * 	ls.hook.run('somefunc_hook1_name', [param1,param2], thisArg);
 * 	//code
 * }
 *
 * @author Sergey S Yaglov
 * @link http://livestreet.ru/profile/1d10t
 */
ls.hook = (function ($) {
	this.hooks = {};

	this.cloneFunc = function(func,as_text,no_def) {
		var f;
		if($.type(func)=='string'){
			eval('f = '+func+';');
		}else if($.type(func)=='array'){
			f = func[0][func[1]];
		}else{
			f = func;
		}
		if($.type(f)=='function'){
			var fbody = f.toString().replace(/^(function)([^\(]*)\(/gi, '$1 (');
			if(typeof as_text!='undefined' && as_text){
				if(typeof no_def!='undefined' && no_def){
					return fbody.replace(/^[^\{]*\{/gi, '').replace(/\}$/gi, '');
				}else{
					return fbody;
				}
			}
			return eval('('+fbody+')');
		}
		return function(){};
	};

	/**
	 * @param func functionName|object[parentObject,functionName] Name of function that will be modified
	 * @param funcInj function|string Function or code to be injected
	 * @param marker string
	 */
	this.inject = function(func,funcInj,marker) {
		var funcBody = ls.hook.cloneFunc(func, 1);
		var funcDefinition = ($.type(func)=='string'?func:($.type(func)=='array'?'func[0][func[1]]':'func'))+' = ';
		var replaceFrom = /\{/m;
		var replaceTo = '{ ';
		if($.type(marker) == 'string'){
			//replaceFrom = new RegExp('(\'\\*'+marker+'\\*\'[\r\n\t ]*;?)', 'm');
			replaceFrom = new RegExp('(ls\\.hook\\.marker\\(([\'"])'+marker+'(\\2)\\)[\\r\\n\\t ]*;?)','m');
			replaceTo = '$1';
		}
		if($.type(funcInj)=='function'){
			var funcInjName = 'funcInj'+Math.floor(Math.random()*1000000);
			eval('window["'+funcInjName+'"] = funcInj;');
			eval(funcDefinition + funcBody.replace(replaceFrom,replaceTo+funcInjName+'.apply(this, arguments); '));
		}else{
			eval(funcDefinition  + funcBody.replace(replaceFrom,replaceTo+funcInj+'; '));
		}
	};

	this.add = function(name,callback,priority) {
		var priority = priority || 0;
		if(typeof ls.hook.hooks[name] == 'undefined'){
			ls.hook.hooks[name] = [];
		}
		ls.hook.hooks[name].push({
			'callback': callback,
			'priority': priority
		});
	};

	this.run = function(name,params,o) {
		var params = params || [];
		var hooks = ls.hook.hooks;
		if(typeof hooks[name] != 'undefined'){
			hooks[name].sort(function(a,b){
				return a.priority > b.priority ?
					1
					: (a.priority < b.priority ? -1 : 0)
					;
			});
			$.each(hooks[name], function(i){
				var callback = hooks[name][i].callback;
				if($.type(callback) == 'function'){
					callback.apply(o, params);
				}else if($.type(callback) == 'array'){
					//console.log(callback);
					callback[0][callback[1]].apply(o, params);
				}else if($.type(callback) == 'string'){
					eval('(function(){'+callback+'}).apply(o, params);');
				}else{
					ls.debug('cant call hook "'+name+'"['+i+']');
				}
			});
		}
	};

	this.marker = function(name){
		// noop
	};

	return this;
}).call(ls.hook || {},jQuery);
jQuery(document).ready(function($){
	// Хук начала инициализации javascript-составляющих шаблона
	ls.hook.run('ls_template_init_start',[],window);
	
	$('html').removeClass('no-js');
	
	// Определение браузера
	if ($.browser.opera) {
		$('body').addClass('opera opera' + parseInt($.browser.version));
	}
	if ($.browser.mozilla) {
		$('body').addClass('mozilla mozilla' + parseInt($.browser.version));
	}
	if ($.browser.webkit) {
		$('body').addClass('webkit webkit' + parseInt($.browser.version));
	}
	if ($.browser.msie) {
		$('body').addClass('ie');
		if (parseInt($.browser.version) > 8) {
			$('body').addClass('ie' + parseInt($.browser.version));
		}
	}

	//$(".text").fitVids({customSelector: "iframe"});

	// Userbar
	ls.tools.showuserbar = function() {
		if ( ! $('#wrapper').hasClass('hidden')) {
			$('#userbar-trigger').toggleClass('active');

			// Костыль для андроида
			$('iframe').hide();

			$('#wrapper').addClass('hidden');
			//$('#wrapper').css('width', $(window).width());

			$('#userbar').addClass('show');
			//$('#userbar-inner').css('min-height', $(window).height());
		}
	};


	ls.tools.hideuserbar = function() {
		if ($('#wrapper').hasClass('hidden')) {
			$('#userbar-trigger').toggleClass('active');

			$('iframe').show();

			$('#wrapper').removeClass('hidden');
			//$('#wrapper').css('width', 'auto');

			$('#userbar').removeClass('show');
			//$('#userbar-inner').css('min-height', 'auto');
		}
	};


	$('#userbar-trigger').click(function(){
		if ($('#wrapper').hasClass('hidden')) {
			ls.tools.hideuserbar();
		} else {
			ls.tools.showuserbar();
		}
	});

	ls.tools.bindswipe = function() {
		$(document).swipe( {
			swipeLeft: function(event, direction, distance, duration, fingerCount) {
				ls.tools.hideuserbar();
			},
			swipeRight: function(event, direction, distance, duration, fingerCount) {
				ls.tools.showuserbar();
			},
			click: function(event, target) {
				return false;
			},
			excludedElements: $.fn.swipe.defaults.excludedElements+", #slider, li, i, .nav-foldable-trigger, #userbar-trigger",
			fallbackToMouseEvents: true,
			threshold: $(window).width() * .4
		});
	}

	if ($('#userbar').length > 0) {
		ls.tools.bindswipe();
	}

	$(window).resize(function () {
		if ($('#userbar').length > 0) {
			ls.tools.bindswipe();
		}
	});

	$(window).swipe( {
		swipeStatus:function(event, phase, direction, distance, fingerCount){
			if ($('#notifier').length > 0) $('#notifier').empty();
		},
		excludedElements: $.fn.swipe.defaults.excludedElements+", #slider, li, i, .nav-foldable-trigger, #userbar-trigger",
		triggerOnTouchEnd: false,
		allowPageScroll: 'vertical',
		fallbackToMouseEvents: false,
	});

	$('.swipegallery').swipegallery();

	ls.photoset.showForm = function() {
		$('#topic-photo-upload-input').appendTo('#photoset-wrapper');
		$('#topic-photo-upload-input').show();
	};

	ls.photoset.closeForm = function() {
		$('#topic-photo-upload-input').appendTo('#photoset-upload-form');
		$('#photoset-upload-form').hide();
	};

	ls.photoset.upload = function()
	{
		ls.photoset.closeForm();
		ls.photoset.addPhotoEmpty();
		ls.ajaxSubmit(aRouter['photoset']+'upload/',$('#photoset-upload-form'),function(data){
			if (data.bStateError) {
				$('#photoset_photo_empty').remove();
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				ls.photoset.addPhoto(data);
			}
		});
	};




	ls.user.showResizeAvatar = function(sImgFile) {
		// if (this.jcropAvatar) {
		// 	this.jcropAvatar.destroy();
		// }
		$('#avatar-resize-original-img').attr('src',sImgFile+'?'+Math.random());
		$('#avatar-resize').show();
		var $this=this;
		// $('#avatar-resize-original-img').Jcrop({
		// 	aspectRatio: 1,
		// 	minSize: [32,32]
		// },function(){
		// 	$this.jcropAvatar=this;
		// 	this.setSelect([0,0,500,500]);
		// });
	};

	/**
	 * Выполняет ресайз аватарки
	 */
	ls.user.resizeAvatar = function() {
		// if (!this.jcropAvatar) {
		// 	return false;
		// }
		var url = aRouter.settings+'profile/resize-avatar/';
		// var params = {size: this.jcropAvatar.tellSelect()};
		var params = {};

		ls.hook.marker('resizeAvatarBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#avatar-img').attr('src',result.sFile+'?'+Math.random());
				$('#avatar-resize').hide();
				$('#avatar-remove').show();
				$('#avatar-upload').text(result.sTitleUpload);
				ls.hook.run('ls_user_resize_avatar_after', [params, result]);
			}
		});

		return false;
	};
	/**
	 * Отмена ресайза аватарки, подчищаем временный данные
	 */
	ls.user.cancelAvatar = function() {
		var url = aRouter.settings+'profile/cancel-avatar/';
		var params = {};

		ls.hook.marker('cancelAvatarBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#avatar-resize').hide();
				ls.hook.run('ls_user_cancel_avatar_after', [params, result]);
			}
		});

		return false;
	};
	/**
	 * Загрузка временной фотки
	 * @param form
	 * @param input
	 */
	ls.user.uploadFoto = function(form,input) {
		if (!form && input) {
			var form = $('<form method="post" enctype="multipart/form-data"></form>').css({
				'display': 'none'
			}).appendTo('body');
			var clone=input.clone(true);
			input.hide();
			clone.insertAfter(input);
			input.appendTo(form);
		}

		ls.ajaxSubmit(aRouter['settings']+'profile/upload-foto/',form,function(data){
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				this.showResizeFoto(data.sTmpFile);
			}
		}.bind(this));
	};

	/**
	 * Показывает форму для ресайза фотки
	 * @param sImgFile
	 */
	ls.user.showResizeFoto = function(sImgFile) {
		// if (this.jcropFoto) {
		// 	this.jcropFoto.destroy();
		// }
		$('#foto-resize-original-img').attr('src',sImgFile+'?'+Math.random());
		$('#foto-resize').show();
		var $this=this;
		// $('#foto-resize-original-img').Jcrop({
		// 	minSize: [32,32]
		// },function(){
		// 	$this.jcropFoto=this;
		// 	this.setSelect([0,0,500,500]);
		// });
	};
	/**
	 * Выполняет ресайз фотки
	 */
	ls.user.resizeFoto = function() {
		// if (!this.jcropFoto) {
		// 	return false;
		// }
		var url = aRouter.settings+'profile/resize-foto/';
		// var params = {size: this.jcropFoto.tellSelect()};
		var params = {};

		ls.hook.marker('resizeFotoBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#foto-img').attr('src',result.sFile+'?'+Math.random());
				$('#foto-resize').hide();
				$('#foto-remove').show();
				$('#foto-upload').text(result.sTitleUpload);
				ls.hook.run('ls_user_resize_foto_after', [params, result]);
			}
		});

		return false;
	};


	/**
	 * Отмена ресайза фотки, подчищаем временный данные
	 */
	ls.user.cancelFoto = function() {
		var url = aRouter.settings+'profile/cancel-foto/';
		var params = {};

		ls.hook.marker('cancelFotoBefore');
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				$('#foto-resize').hide();
				ls.hook.run('ls_user_cancel_foto_after', [params, result]);
			}
		});

		return false;
	};






	// Vote
	ls.vote.onVote = function(idTarget, objVote, value, type, result) {
		if (result.bStateError) {
			ls.msg.error(null, result.sMsg);
		} else {
			ls.msg.notice(null, result.sMsg);
			
			var divVoting = $('#'+this.options.prefix_area+type+'_'+idTarget);
			var divTotal = $('#'+this.options.prefix_total+type+'_'+idTarget);
			var divCount = $('#'+this.options.prefix_count+type+'_'+idTarget);

			divTotal.addClass(this.options.classes.voted);

			if (value > 0) {
				divTotal.addClass(this.options.classes.plus);
			}
			if (value < 0) {
				divTotal.addClass(this.options.classes.minus);
			}
			if (value == 0) {
				divTotal.addClass(this.options.classes.voted_zero);
			}
			
			if (divCount.length>0 && result.iCountVote) {
				divCount.text(parseInt(result.iCountVote));
			}

			result.iRating = parseFloat(result.iRating);

			divTotal.removeClass(this.options.classes.negative);
			divTotal.removeClass(this.options.classes.positive);
			divTotal.removeClass(this.options.classes.not_voted);
			divTotal.removeClass(this.options.classes.zero);

			if (result.iRating > 0) {
				divTotal.addClass(this.options.classes.positive);
				divTotal.text('+'+result.iRating);
			}else if (result.iRating < 0) {
				divTotal.addClass(this.options.classes.negative);
				divTotal.text(result.iRating);
			}else if (result.iRating == 0) {
				divTotal.addClass(this.options.classes.zero);
				divTotal.text(0);
			}

			if (type == 'blog' || type == 'user') {
				$('#'+this.options.prefix_total+type+'_alt_'+idTarget).text(result.iRating);
				divTotal.empty();
			}

			var method='onVote'+ls.tools.ucfirst(type);
			if ($.type(this[method])=='function') {
				this[method].apply(this,[idTarget, objVote, value, type, result]);
			}

			divVoting.remove();
		}
		
		$(this).trigger('vote',[idTarget, objVote, value, type, result]);
	}


	// Add friend
	ls.user.addFriend = function(obj, idUser, sAction){
		if(sAction != 'link' && sAction != 'accept') {
			var sText = $('#add_friend_text').val();
			$('#add_friend_form').children().each(function(i, item){$(item).attr('disabled','disabled')});
		} else {
			var sText='';
		}

		if(sAction == 'accept') {
			var url = aRouter.profile+'ajaxfriendaccept/';
		} else {
			var url = aRouter.profile+'ajaxfriendadd/';
		}

		var params = {idUser: idUser, userText: sText};

		ls.hook.marker('addFriendBefore');
		ls.ajax(url, params, function(result){
			$('#add_friend_form').children().each(function(i, item){$(item).removeAttr('disabled')});
			if (!result) {
				ls.msg.error('Error','Please try again later');
			}
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				ls.msg.notice(null,result.sMsg);
				$('#add_friend_form').hide();
				$('#add_friend_item').remove();
				$('#profile_actions').prepend($(result.sToggleText));
				ls.hook.run('ls_user_add_friend_after', [idUser,sAction,result], obj);
			}
		});
		return false;
	};

	ls.favourite.showEditTags = function(idTarget,type,obj) {
		var form=$('#favourite-form-tags');
		$('#favourite-form-tags-target-type').val(type);
		$('#favourite-form-tags-target-id').val(idTarget);
		var text='';
		var tags=$('.js-favourite-tags-'+$('#favourite-form-tags-target-type').val()+'-'+$('#favourite-form-tags-target-id').val());
		tags.find('.js-favourite-tag-user a').each(function(k,tag){
			if (text) {
				text=text+', '+$(tag).text();
			} else {
				text=$(tag).text();
			}
		});
		$('#favourite-form-tags-tags').val(text);
		//$(obj).parents('.js-favourite-insert-after-form').after(form);
		form.insertAfter($(obj).parent().parent());
		form.slideToggle();

		return false;
	};

	ls.favourite.hideEditTags = function() {
		$('#favourite-form-tags').slideUp();
		return false;
	};


	ls.userfield.showAddForm = function(obj){
		$('#userfield_form').insertAfter($('#userfield_form_show'));
		$('#user_fields_form_name').val('');
		$('#user_fields_form_title').val('');
		$('#user_fields_form_id').val('');
		$('#user_fields_form_pattern').val('');
		$('#user_fields_form_type').val('');
		$('#user_fields_form_action').val('add');
		$('#userfield_form').slideDown(); 
	};
	
	ls.userfield.showEditForm = function(id, obj) {
		console.log($(obj));
		$('#userfield_form').insertAfter($(obj).parent().parent());
		$('#user_fields_form_action').val('update');
		var name = $('#field_'+id+' .userfield_admin_name').text();
		var title = $('#field_'+id+' .userfield_admin_title').text();
		var pattern = $('#field_'+id+' .userfield_admin_pattern').text();
		var type = $('#field_'+id+' .userfield_admin_type').text();
		$('#user_fields_form_name').val(name);
		$('#user_fields_form_title').val(title);
		$('#user_fields_form_pattern').val(pattern);
		$('#user_fields_form_type').val(type);
		$('#user_fields_form_id').val(id);
		$('#userfield_form').slideDown(); 
	};

	ls.userfield.applyForm = function(){
		$('#userfield_form').slideDown(); 
		if ($('#user_fields_form_action').val() == 'add') {
			this.addUserfield();
		} else if ($('#user_fields_form_action').val() == 'update')  {
			this.updateUserfield();
		}
	};

	ls.userfield.hideForm = function(){
		$('#userfield_form').slideUp();
		return false;
	};

	ls.userfield.addUserfield = function() {
		var name = $('#user_fields_form_name').val();
		var title = $('#user_fields_form_title').val();
		var pattern = $('#user_fields_form_pattern').val();
		var type = $('#user_fields_form_type').val();

		var url = aRouter['admin']+'userfields';
		var params = {'action':'add', 'name':name,  'title':title,  'pattern':pattern,  'type':type};
		
		ls.hook.marker('addUserfieldBefore');
		ls.ajax(url, params, function(data) { 
			if (!data.bStateError) {
				liElement = $('<div class="userfield-item" id="field_'+data.id+'"><span class="userfield_admin_name"></span > / <span class="userfield_admin_title"></span> / <span class="userfield_admin_pattern"></span> / <span class="userfield_admin_type"></span>'
					+'<div class="userfield-actions"><a class="icon-edit" href="javascript:ls.userfield.showEditForm('+data.id+')"></a> '
					+'<a class="icon-delete" href="javascript:ls.userfield.deleteUserfield('+data.id+')"></a></div>')
				;
				$('#user_field_list').append(liElement);
				$('#field_'+data.id+' .userfield_admin_name').text(name);
				$('#field_'+data.id+' .userfield_admin_title').text(title);
				$('#field_'+data.id+' .userfield_admin_pattern').text(pattern);
				$('#field_'+data.id+' .userfield_admin_type').text(type);
				ls.msg.notice(data.sMsgTitle,data.sMsg);
				ls.hook.run('ls_userfield_add_userfield_after',[params, data],liElement);
			} else {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			}
		});
	};


	ls.msg = (function ($) {
		/**
		* Опции
		*/
		this.options = {
			class_notice: 'n-notice',
			class_error: 'n-error'
		};

		/**
		* Отображение информационного сообщения
		*/
		this.notice = function(title,msg){
			this.init();
			$.notifier.broadcast(title, msg, this.options.class_notice);
		};

		/**
		* Отображение сообщения об ошибке
		*/
		this.error = function(title,msg){
			this.init();
			$.notifier.broadcast(title, msg, this.options.class_error);
		};

		this.init = function(){
			if ($('#notifier').length == 0) $('body').append('<div id="notifier" />');
			$('#notifier').width($(window).width());
			var doc = document.documentElement, body = document.body;
			$('#notifier').css('top', (doc && doc.scrollTop || body && body.scrollTop || 0));
		};

		return this;
	}).call(ls.msg || {},jQuery);

	$(window).resize(function () {
		$('#notifier').empty();
	});
	$(window).scroll(function () {
		$('#notifier').empty();
	});



	$('.js-registration-form-show').click(function(){
		if (ls.blocks.switchTab('registration','popup-login')) {
			$('#window_login_form').show();
		} else {
			window.location=aRouter.registration;
		}
		return false;
	});

	$('.js-login-form-show').click(function(){
		if (ls.blocks.switchTab('login','popup-login')) {
			$('#window_login_form').show();
		} else {
			window.location=aRouter.login;
		}
		return false;
	});
	
	// Datepicker
	 /**
	  * TODO: навесить языки на datepicker
	  */
	$('.date-picker').datepicker({ 
		dateFormat: 'dd.mm.yy',
		dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
		monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		firstDay: 1
	});
	
	
	// Поиск по тегам
	$('.js-tag-search-form').submit(function(){
		window.location = aRouter['tag']+encodeURIComponent($(this).find('.js-tag-search').val())+'/';
		return false;
	});
	
	
	// Автокомплит
	ls.autocomplete.add($(".autocomplete-tags-sep"), aRouter['ajax']+'autocompleter/tag/', true);
	ls.autocomplete.add($(".autocomplete-tags"), aRouter['ajax']+'autocompleter/tag/', false);
	ls.autocomplete.add($(".autocomplete-users-sep"), aRouter['ajax']+'autocompleter/user/', true);
	ls.autocomplete.add($(".autocomplete-users"), aRouter['ajax']+'autocompleter/user/', false);

	
	// Скролл
	$(window)._scrollable();
	
	// эмуляция border-sizing в IE
	var inputs = $('input.input-text, textarea');
	ls.ie.bordersizing(inputs);
	
	// эмуляция placeholder'ов в IE
	inputs.placeholder();

	// инизиализация блоков
	ls.blocks.init('stream',{group_items: true, group_min: 3});
	ls.blocks.init('blogs');
	ls.blocks.initSwitch('tags');
	ls.blocks.initSwitch('upload-img');
	ls.blocks.initSwitch('favourite-topic-tags');
	ls.blocks.initSwitch('popup-login');

	// комментарии
	ls.comments.options.folding = false;
	ls.comments.init();


	/**
	* Переключение избранного
	*/
	ls.favourite.toggle = function(idTarget, objFavourite, type) {
		if (!this.options.type[type]) { return false; }

		this.objFavourite = $(objFavourite);
		
		var params = {};
		params['type'] = !this.objFavourite.hasClass(this.options.active);
		params[this.options.type[type].targetName] = idTarget;
		
		ls.hook.marker('toggleBefore');
		ls.ajax(this.options.type[type].url, params, function(result) {
			$(this).trigger('toggle',[idTarget,objFavourite,type,params,result]);

			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				this.objFavourite.removeClass(this.options.active);
				if (result.bState) {
					this.objFavourite.addClass(this.options.active);
					this.showTags(type,idTarget);
				} else {
					this.hideTags(type,idTarget);
				}

				$(objFavourite).parent().toggleClass('active');
				$('#fav_count_'+type+'_'+idTarget).text((result.iCount>0) ? result.iCount : '');

				ls.hook.run('ls_favourite_toggle_after',[idTarget,objFavourite,type,params,result],this);
			}
		}.bind(this));
		return false;
	};

	ls.hook.add('ls_blog_toggle_join_after',function(idBlog,result){
		$(this).empty();
	});

	ls.hook.add('ls_wall_addreply_after',function(sText, iPid, result){
		$('#wall-reply-wrapper-'+iPid).show();
	});

	ls.favourite.hideEditTags = function() {
		$('#favourite-form-tags').hide();
		return false;
	};

	/****************
	 * TALK
	 */

	// Добавляем или удаляем друга из списка получателей
	$('#friends input:checkbox').change(function(){
		ls.talk.toggleRecipient($('#'+$(this).attr('id')+'_label').text(), $(this).attr('checked'));
	});

	// Добавляем всех друзей в список получателей
	$('#friend_check_all').click(function(){
		$('#friends input:checkbox').each(function(index, item){
			ls.talk.toggleRecipient($('#'+$(item).attr('id')+'_label').text(), true);
			$(item).attr('checked', true);
		});
		return false;
	});

	// Удаляем всех друзей из списка получателей
	$('#friend_uncheck_all').click(function(){
		$('#friends input:checkbox').each(function(index, item){
			ls.talk.toggleRecipient($('#'+$(item).attr('id')+'_label').text(), false);
			$(item).attr('checked', false);
		});
		return false;
	});

	// Удаляем пользователя из черного списка
	$("#black_list_block").delegate("a.delete", "click", function(){
		ls.talk.removeFromBlackList(this);
		return false;
	});

	// Удаляем пользователя из переписки
	$("#speaker_list_block").delegate("a.delete", "click", function(){
		ls.talk.removeFromTalk(this, $('#talk_id').val());
		return false;
	});


	// Help-tags link
	$('.js-tags-help-link').click(function(){
		var target=ls.registry.get('tags-help-target-id');
		if (!target || !$('#'+target).length) {
			return false;
		}
		target=$('#'+target);
		if ($(this).data('insert')) {
			var s=$(this).data('insert');
		} else {
			var s=$(this).text();
		}
		$.markItUp({target: target, replaceWith: s});
		return false;
	});
	
	
	// Фикс бага с z-index у встроенных видео
	$("iframe").each(function(){
		var ifr_source = $(this).attr('src');

		if(ifr_source) {
			var wmode = "wmode=opaque";
				
			if (ifr_source.indexOf('?') != -1) 
				$(this).attr('src',ifr_source+'&'+wmode);
			else 
				$(this).attr('src',ifr_source+'?'+wmode);
		}
	});


	ls.tools.slide = function(target, obj, deactivate_items) {
		if (deactivate_items) {
			obj.parent().children('li').not(obj).removeClass('active');
		}

		$('.slide-trigger').not(obj).removeClass('active');
		$('.slide').not(target).removeClass('active').hide();
		target.slideToggle(); 
		obj.toggleClass('active');
	}


	$('.nav-foldable').each(function(){
		$(this).wrap('<div class="nav-foldable-wrapper" />');

		var wrapper = $(this).parent();
		wrapper.prepend('<div class="nav-foldable-trigger inactive">' + ls.lang.get('nav_select_item') + '</div>');
		
		var trigger = wrapper.find('.nav-foldable-trigger');
		wrapper.find('ul').addClass('slide');

		if ($(this).hasClass('nav-foldable-primary')) {
			$(this).removeClass('nav-foldable-primary');
			trigger.addClass('nav-foldable-primary');
		}

		var active = wrapper.find('ul li.active');
		if (active.length > 0) {
			trigger.text(active.text()).removeClass('inactive');
		}
		
		trigger.click(function(){ 
			ls.tools.slide($(this).next('ul'), trigger);
		})
	});

	
	// Хук конца инициализации javascript-составляющих шаблона
	ls.hook.run('ls_template_init_end',[],window);
});

function adjustIframes()
{
  $('iframe').each(function(){
    var
    $this = $(this),
    proportion = $this.data( 'proportion' ),
    w = $this.attr('width'),
    actual_w = $this.width();
    
    if ( ! proportion )
    {
        proportion = $this.attr('height') / w;
        $this.data( 'proportion', proportion );
    }
  
    if ( actual_w != w )
    {
        $this.css( 'height', Math.round( actual_w * proportion ) + 'px' );
    }
  });
}

$(window).on('resize load',adjustIframes);
/*
* touchSwipe - jQuery Plugin
* https://github.com/mattbryson/TouchSwipe-Jquery-Plugin
* http://labs.skinkers.com/touchSwipe/
* http://plugins.jquery.com/project/touchSwipe
*
* Copyright (c) 2010 Matt Bryson (www.skinkers.com)
* Dual licensed under the MIT or GPL Version 2 licenses.
*
* $version: 1.5.1
*
* Changelog
* $Date: 2010-12-12 (Wed, 12 Dec 2010) $
* $version: 1.0.0 
* $version: 1.0.1 - removed multibyte comments
*
* $Date: 2011-21-02 (Mon, 21 Feb 2011) $
* $version: 1.1.0 	- added allowPageScroll property to allow swiping and scrolling of page
*					- changed handler signatures so one handler can be used for multiple events
* $Date: 2011-23-02 (Wed, 23 Feb 2011) $
* $version: 1.2.0 	- added click handler. This is fired if the user simply clicks and does not swipe. The event object and click target are passed to handler.
*					- If you use the http://code.google.com/p/jquery-ui-for-ipad-and-iphone/ plugin, you can also assign jQuery mouse events to children of a touchSwipe object.
* $version: 1.2.1 	- removed console log!
*
* $version: 1.2.2 	- Fixed bug where scope was not preserved in callback methods. 
*
* $Date: 2011-28-04 (Thurs, 28 April 2011) $
* $version: 1.2.4 	- Changed licence terms to be MIT or GPL inline with jQuery. Added check for support of touch events to stop non compatible browsers erroring.
*
* $Date: 2011-27-09 (Tues, 27 September 2011) $
* $version: 1.2.5 	- Added support for testing swipes with mouse on desktop browser (thanks to https://github.com/joelhy)
*
* $Date: 2012-14-05 (Mon, 14 May 2012) $
* $version: 1.2.6 	- Added timeThreshold between start and end touch, so user can ignore slow swipes (thanks to Mark Chase). Default is null, all swipes are detected
* 
* $Date: 2012-05-06 (Tues, 05 June 2012) $
* $version: 1.2.7 	- Changed time threshold to have null default for backwards compatibility. Added duration param passed back in events, and refactored how time is handled.
*
* $Date: 2012-05-06 (Tues, 05 June 2012) $
* $version: 1.2.8 	- Added the possibility to return a value like null or false in the trigger callback. In that way we can control when the touch start/move should take effect or not (simply by returning in some cases return null; or return false;) This effects the ontouchstart/ontouchmove event.
*
* $Date: 2012-06-06 (Wed, 06 June 2012) $
* $version: 1.3.0 	- Refactored whole plugin to allow for methods to be executed, as well as exposed defaults for user override. Added 'enable', 'disable', and 'destroy' methods
*
* $Date: 2012-05-06 (Fri, 05 June 2012) $
* $version: 1.3.1 	- Bug fixes  - bind() with false as last argument is no longer supported in jQuery 1.6, also, if you just click, the duration is now returned correctly.
*
* $Date: 2012-29-07 (Sun, 29 July 2012) $
* $version: 1.3.2	- Added fallbackToMouseEvents option to NOT capture mouse events on non touch devices.
* 			- Added "all" fingers value to the fingers property, so any combinatin of fingers triggers the swipe, allowing event handlers to check the finger count
*
* $Date: 2012-09-08 (Thurs, 9 Aug 2012) $
* $version: 1.3.3	- Code tidy prep for minified version
*
* $Date: 2012-04-10 (wed, 4 Oct 2012) $
* $version: 1.4.0	- Added pinch support, pinchIn and pinchOut
*
* $Date: 2012-11-10 (Thurs, 11 Oct 2012) $
* $version: 1.5.0	- Added excludedElements, a jquery selector that specifies child elements that do NOT trigger swipes. By default, this is one select that removes all form, input select, button and anchor elements.
*
* $Date: 2012-22-10 (Mon, 22 Oct 2012) $
* $version: 1.5.1	- Fixed bug with jQuery 1.8 and trailing comma in excludedElements
*
* A jQuery plugin to capture left, right, up and down swipes on touch devices.
* You can capture 2 finger or 1 finger swipes, set the threshold and define either a catch all handler, or individual direction handlers.
* Options: The defaults can be overridden by setting them in $.fn.swipe.defaults
* 		swipe 			Function 	A catch all handler that is triggered for all swipe directions. Handler is passed 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down" , the distance of the swipe, the duration of the swipe and the finger count.
* 		swipeLeft		Function 	A handler that is triggered for "left" swipes. Handler is passed 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down"  , the distance of the swipe, the duration of the swipe and the finger count.
* 		swipeRight		Function 	A handler that is triggered for "right" swipes. Handler is passed 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down"  , the distance of the swipe, the duration of the swipe and the finger count.
* 		swipeUp			Function 	A handler that is triggered for "up" swipes. Handler is passed 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down" , the distance of the swipe, the duration of the swipe and the finger count.
* 		swipeDown		Function 	A handler that is triggered for "down" swipes. Handler is passed 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down"  , the distance of the swipe, the duration of the swipe and the finger count.
*		swipeStatus 	Function 	A handler triggered for every phase of the swipe. Handler is passed 4 arguments: event : The original event object, phase:The current swipe phase, either "start", "move", "end" or "cancel". direction : The swipe direction, either "up?, "down?, "left " or "right?.distance : The distance of the swipe.Duration : The duration of the swipe :  The finger count
*		
* 		pinchIn			Function 	A handler triggered when the user pinch zooms inward. Handler is passed 
* 		pinchOut		Function 	A handler triggered when the user pinch zooms outward. Handler is passed
* 		pinchStatus		Function 	A handler triggered for every phase of a pinch. Handler is passed 4 arguments: event : The original event object, phase:The current swipe face, either "start", "move", "end" or "cancel". direction : The swipe direction, either "in" or "out". distance : The distance of the pinch, zoom: the pinch zoom level
* 		
* 		click			Function	A handler triggered when a user just clicks on the item, rather than swipes it. If they do not move, click is triggered, if they do move, it is not.
*
* 		fingers 		int 		Default 1. 	The number of fingers to trigger the swipe, 1 or 2.
* 		threshold 		int  		Default 75.	The number of pixels that the user must move their finger by before it is considered a swipe.
* 		maxTimeThreshold 	int  		Default null. Time, in milliseconds, between touchStart and touchEnd must NOT exceed in order to be considered a swipe.
*		triggerOnTouchEnd Boolean Default true If true, the swipe events are triggered when the touch end event is received (user releases finger).  If false, it will be triggered on reaching the threshold, and then cancel the touch event automatically.
*		allowPageScroll String Default "auto". How the browser handles page scrolls when the user is swiping on a touchSwipe object. 
*										"auto" : all undefined swipes will cause the page to scroll in that direction.
*										"none" : the page will not scroll when user swipes.
*										"horizontal" : will force page to scroll on horizontal swipes.
*										"vertical" : will force page to scroll on vertical swipes.
*		fallbackToMouseEvents 	Boolean		Default true	if true mouse events are used when run on a non touch device, false will stop swipes being triggered by mouse events on non tocuh devices
*
*		excludedElements	String 	jquery selector that specifies child elements that do NOT trigger swipes. By default, this is one select that removes all input, select, textarea, button and anchor elements as well as any .noSwipe classes.
*
* Methods: To be executed as strings, $el.swipe('disable');
*		disable		Will disable all touch events until enabled again
*		enable		Will re-enable the touch events
*		destroy		Will kill the plugin, and it must be re-instantiated if it needs to be used again
*
* This jQuery plugin will only run on devices running Mobile Webkit based browsers (iOS 2.0+, android 2.2+)
*/
(function ($) {

	//Constants
	var LEFT = "left",
		RIGHT = "right",
		UP = "up",
		DOWN = "down",
		IN = "in",
		OUT = "out",

		NONE = "none",
		AUTO = "auto",

		HORIZONTAL = "horizontal",
		VERTICAL = "vertical",

		ALL_FINGERS = "all",

		PHASE_START = "start",
		PHASE_MOVE = "move",
		PHASE_END = "end",
		PHASE_CANCEL = "cancel",

		SUPPORTS_TOUCH = 'ontouchstart' in window,

		PLUGIN_NS = 'TouchSwipe';



	// Default thresholds & swipe functions
	var defaults = {

		fingers: 1, 		// int - The number of fingers to trigger the swipe, 1 or 2. Default is 1.
		threshold: 75, 		// int - The number of pixels that the user must move their finger by before it is considered a swipe. Default is 75.

		maxTimeThreshold: null, // int - Time, in milliseconds, between touchStart and touchEnd must NOT exceed in order to be considered a swipe.

		swipe: null, 		// Function - A catch all handler that is triggered for all swipe directions. Accepts 2 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", and the finger count.
		swipeLeft: null, 	// Function - A handler that is triggered for "left" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeRight: null, 	// Function - A handler that is triggered for "right" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeUp: null, 		// Function - A handler that is triggered for "up" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeDown: null, 	// Function - A handler that is triggered for "down" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeStatus: null, 	// Function - A handler triggered for every phase of the swipe. Handler is passed 4 arguments: event : The original event object, phase:The current swipe phase, either "start, "move, "end or "cancel. direction : The swipe direction, either "up", "down", "left" or "right". distance : The distance of the swipe, fingerCount: The finger count.
		
		pinchIn:null,		// Function - A handler triggered for pinch in events. Handler is passed 4 arguments: event : The original event object, direction : The swipe direction, either "in" or "out". distance : The distance of the pinch, zoom: the pinch zoom level
		pinchOut:null,		// Function - A handler triggered for pinch in events. Handler is passed 4 arguments: event : The original event object, direction : The swipe direction, either "in" or "out". distance : The distance of the pinch, zoom: the pinch zoom level
		pinchStatus:null,	// Function - A handler triggered for every phase of a pinch. Handler is passed 4 arguments: event : The original event object, phase:The current swipe face, either "start", "move", "end" or "cancel". direction : The swipe direction, either "in" or "out". distance : The distance of the pinch, zoom: the pinch zoom level
		
		
		
		click: null, 		// Function	- A handler triggered when a user just clicks on the item, rather than swipes it. If they do not move, click is triggered, if they do move, it is not.
		
		
		triggerOnTouchEnd: true, // Boolean, if true, the swipe events are triggered when the touch end event is received (user releases finger).  If false, it will be triggered on reaching the threshold, and then cancel the touch event automatically.
		allowPageScroll: "auto", 	/* How the browser handles page scrolls when the user is swiping on a touchSwipe object. 
										"auto" : all undefined swipes will cause the page to scroll in that direction.
										"none" : the page will not scroll when user swipes.
										"horizontal" : will force page to scroll on horizontal swipes.
										"vertical" : will force page to scroll on vertical swipes.
									*/
		fallbackToMouseEvents: true,	//Boolean, if true mouse events are used when run on a non touch device, false will stop swipes being triggered by mouse events on non tocuh devices
		
		excludedElements:"button, input, select, textarea, a, .noSwipe" //a jquery selector that specifies child elements that do NOT trigger swipes. By default, this is one select that removes all form, input select, button and anchor elements.
	};



	/**
	* Main plugin entry point for jQuery
	* This allows us to pass options object for instantiation,
	* as well as execute methods by name as per jQuery plugin architecture
	*/
	$.fn.swipe = function (method) {
		var $this = $(this),
			plugin = $this.data(PLUGIN_NS);

		//Check if we are already instantiated and trying to execute a method	
		if (plugin && typeof method === 'string') {
			if (plugin[method]) {
				return plugin[method].apply(this, Array.prototype.slice.call(arguments, 1));
			} else {
				$.error('Method ' + method + ' does not exist on jQuery.swipe');
			}
		}
		//Else not instantiated and trying to pass init object (or nothing)
		else if (!plugin && (typeof method === 'object' || !method)) {
			return init.apply(this, arguments);
		}

		return $this;
	};

	//Expose our defaults so a user could override the plugin defaults
	$.fn.swipe.defaults = defaults;

	//Expose our phase constants - READ ONLY
	$.fn.swipe.phases = {
		PHASE_START: PHASE_START,
		PHASE_MOVE: PHASE_MOVE,
		PHASE_END: PHASE_END,
		PHASE_CANCEL: PHASE_CANCEL
	};

	//Expose our direction constants - READ ONLY
	$.fn.swipe.directions = {
		LEFT: LEFT,
		RIGHT: RIGHT,
		UP: UP,
		DOWN: DOWN,
		IN : IN,
		OUT: OUT
	};
	
	//Expose our page scroll directions - READ ONLY
	$.fn.swipe.pageScroll = {
		NONE: NONE,
		HORIZONTAL: HORIZONTAL,
		VERTICAL: VERTICAL,
		AUTO: AUTO
	};

	//EXPOSE our fingers values - READ ONLY
	$.fn.swipe.fingers = {
		ONE: 1,
		TWO: 2,
		THREE: 3,
		ALL: ALL_FINGERS
	};

	/**
	* Initialise the plugin for each DOM element matched
	* This creates a new instance of the main TouchSwipe class for each DOM element, and then 
	* saves a reference to that instance in the elements data property.
	*/
	function init(options) {
		//Prep and extend the options
		if (options && (options.allowPageScroll === undefined && (options.swipe !== undefined || options.swipeStatus !== undefined))) {
			options.allowPageScroll = NONE;
		}

		if (!options) {
			options = {};
		}

		//pass empty object so we dont modify the defaults
		options = $.extend({}, $.fn.swipe.defaults, options);

		//For each element instantiate the plugin
		return this.each(function () {
			var $this = $(this);

			//Check we havent already initialised the plugin
			var plugin = $this.data(PLUGIN_NS);

			if (!plugin) {
				plugin = new touchSwipe(this, options);
				$this.data(PLUGIN_NS, plugin);
			}
		});
	}

	/**
	* Main TouchSwipe Plugin Class
	*/
	function touchSwipe(element, options) {
		var useTouchEvents = (SUPPORTS_TOUCH || !options.fallbackToMouseEvents),
			START_EV = useTouchEvents ? 'touchstart' : 'mousedown',
			MOVE_EV = useTouchEvents ? 'touchmove' : 'mousemove',
			END_EV = useTouchEvents ? 'touchend' : 'mouseup',
			CANCEL_EV = 'touchcancel';

		var distance = 0;
		var direction = null;
		var duration = 0;
		var startTouchesDistance=0;
		var endTouchesDistance=0;
		var pinchZoom = 1;
		var pinchDirection=0;
		
		
		//jQuery wrapped element for this instance
		var $element = $(element);

		var phase = "start";

		var fingerCount = 0; 		// the current number of fingers being used.	

		//track mouse points / delta
		var fingerData=null;

		//track times
		var startTime = 0;
		var endTime = 0;

		// Add gestures to all swipable areas if supported
		try {
			$element.bind(START_EV, touchStart);
			$element.bind(CANCEL_EV, touchCancel);
		}
		catch (e) {
			$.error('events not supported ' + START_EV + ',' + CANCEL_EV + ' on jQuery.swipe');
		}

		//Public methods
		/**
		* re-enables the swipe plugin with the previous configuration
		*/
		this.enable = function () {
			$element.bind(START_EV, touchStart);
			$element.bind(CANCEL_EV, touchCancel);

			return $element;
		};

		/**
		* disables the swipe plugin
		*/
		this.disable = function () {
			removeListeners();
			return $element;
		};

		/**
		* Destroy the swipe plugin completely. To use any swipe methods, you must re initialise the plugin.
		*/
		this.destroy = function () {
			removeListeners();
			$element.data(PLUGIN_NS, null);
			return $element;
		};


		//Private methods
		/**
		* Event handler for a touch start event. 
		* Stops the default click event from triggering and stores where we touched
		*/
		function touchStart(event) {
			//If we already in a touch event (a finger already in use) then ignore subsequent ones..
			if( getTouchInProgress() )
				return;
			
			//Check if this element matches any in the excluded elements selectors,  or its parent is excluded, if so, DONT swipe
			if( $(event.target).closest( options.excludedElements, $element ).length>0 ) 
				return;
				
			//As we use Jquery bind for events, we need to target the original event object
			event = event.originalEvent;
			
			var ret,
				evt = SUPPORTS_TOUCH ? event.touches[0] : event;

			phase = PHASE_START;

			//If we support touches, get the finger count
			if (SUPPORTS_TOUCH) {
				// get the total number of fingers touching the screen
				fingerCount = event.touches.length;
			}
			//Else this is the desktop, so stop the browser from dragging the image
			else {
				event.preventDefault();
			}

			//clear vars..
			distance = 0;
			direction = null;
			pinchDirection=null;
			duration = 0;
			startTouchesDistance=0;
			endTouchesDistance=0;
			pinchZoom = 1;
			fingerData=createFingerData();

			
			// check the number of fingers is what we are looking for, or we are capturing pinches
			if (!SUPPORTS_TOUCH || (fingerCount === options.fingers || options.fingers === ALL_FINGERS) || hasPinches()) {
				// get the coordinates of the touch
				fingerData[0].start.x = fingerData[0].end.x = evt.pageX;
				fingerData[0].start.y = fingerData[0].end.y = evt.pageY;
				startTime = getTimeStamp();
				
				if(fingerCount==2) {
					//Keep track of the initial pinch distance, so we can calculate the diff later
					//Store second finger data as start
					fingerData[1].start.x = fingerData[1].end.x = event.touches[1].pageX;
					fingerData[1].start.y = fingerData[1].end.y = event.touches[1].pageY;
					
					startTouchesDistance = endTouchesDistance = calculateTouchesDistance(fingerData[0].start, fingerData[1].start);
				}
				
				if (options.swipeStatus || options.pinchStatus) {
					ret = triggerHandler(event, phase);
				}
			}
			else {
				//A touch with more or less than the fingers we are looking for, so cancel
				touchCancel(event);
				ret = false; // actualy cancel so we dont register event...
			}

			//If we have a return value from the users handler, then return and cancel
			if (ret === false) {
				phase = PHASE_CANCEL;
				triggerHandler(event, phase);
				return ret;
			}
			else {
				setTouchInProgress(true);
				$element.bind(MOVE_EV, touchMove);
				$element.bind(END_EV, touchEnd);
				
			}
		};

		/**
		* Event handler for a touch move event. 
		* If we change fingers during move, then cancel the event
		*/
		function touchMove(event) {
			//As we use Jquery bind for events, we need to target the original event object
			event = event.originalEvent;

			if (phase === PHASE_END || phase === PHASE_CANCEL)
				return;

			var ret,
				evt = SUPPORTS_TOUCH ? event.touches[0] : event;

			//Save the first finger data
			fingerData[0].end.x = SUPPORTS_TOUCH ? event.touches[0].pageX : evt.pageX;
			fingerData[0].end.y = SUPPORTS_TOUCH ? event.touches[0].pageY : evt.pageY;
			
			endTime = getTimeStamp();

			direction = calculateDirection(fingerData[0].start, fingerData[0].end);
			if (SUPPORTS_TOUCH) {
				fingerCount = event.touches.length;
			}

			phase = PHASE_MOVE;

			//If we have 2 fingers get Touches distance as well
			if(fingerCount==2) {
				//Keep track of the initial pinch distance, so we can calculate the diff later
				//We do this here as well as the start event, incase they start with 1 finger, and the press 2 fingers
				if(startTouchesDistance==0) {
					//Store second finger data as start
					fingerData[1].start.x = event.touches[1].pageX;
					fingerData[1].start.y = event.touches[1].pageY;
					
					startTouchesDistance = endTouchesDistance = calculateTouchesDistance(fingerData[0].start, fingerData[1].start);
				} else {
					//Store second finger data as end
					fingerData[1].end.x = event.touches[1].pageX;
					fingerData[1].end.y = event.touches[1].pageY;
					
					endTouchesDistance = calculateTouchesDistance(fingerData[0].end, fingerData[1].end);
					pinchDirection = calculatePinchDirection(fingerData[0].end, fingerData[1].end);
				}
				
				
				pinchZoom = calculatePinchZoom(startTouchesDistance, endTouchesDistance);
			}
			
			
			
			if ((fingerCount === options.fingers || options.fingers === ALL_FINGERS) || !SUPPORTS_TOUCH) {
				
				//Check if we need to prevent default evnet (page scroll / pinch zoom) or not
				validateDefaultEvent(event, direction);

				//Distance and duration are all off the main finger
				distance = calculateDistance(fingerData[0].start, fingerData[0].end);
				duration = calculateDuration(fingerData[0].start, fingerData[0].end);

				if (options.swipeStatus || options.pinchStatus) {
					ret = triggerHandler(event, phase);
				}

				//If we trigger whilst dragging, not on touch end, then calculate now...
				if (!options.triggerOnTouchEnd) {
					var cancel = !validateSwipeTime();

					// if the user swiped more than the minimum length, perform the appropriate action
					if (validateSwipeDistance() === true) {
						phase = PHASE_END;
						ret = triggerHandler(event, phase);
					} else if (cancel) {
						phase = PHASE_CANCEL;
						triggerHandler(event, phase);
					}
				}
			}
			else {
				phase = PHASE_CANCEL;
				triggerHandler(event, phase);
			}

			if (ret === false) {
				phase = PHASE_CANCEL;
				triggerHandler(event, phase);
			}
		}

		/**
		* Event handler for a touch end event. 
		* Calculate the direction and trigger events
		*/
		function touchEnd(event) {
			//As we use Jquery bind for events, we need to target the original event object
			event = event.originalEvent;

			//If we are still in a touch another finger is down, then dont cancel
			if(event.touches && event.touches.length>0)
				return true;
				 
			event.preventDefault();

			endTime = getTimeStamp();
			
			//If we have any touches distance data (they pinched at some point) get Touches distance as well
			if(startTouchesDistance!=0) {
				endTouchesDistance = calculateTouchesDistance(fingerData[0].end, fingerData[1].end);
				pinchZoom = calculatePinchZoom(startTouchesDistance, endTouchesDistance);
				pinchDirection = calculatePinchDirection(fingerData[0].end, fingerData[1].end);	
			}
			
			distance = calculateDistance(fingerData[0].start, fingerData[0].end);
			direction = calculateDirection(fingerData[0].start, fingerData[0].end);
			duration = calculateDuration();

			//If we trigger handlers at end of swipe OR, we trigger during, but they didnt trigger and we are still in the move phase
			if (options.triggerOnTouchEnd || (options.triggerOnTouchEnd === false && phase === PHASE_MOVE)) {
				phase = PHASE_END;

				// Validate the types of swipe we are looking for
				//Either we are listening for a pinch, and got one, or we are NOT listening so dont care.
				var hasValidPinchResult = didPinch() || !hasPinches();
				
				//The number of fingers we want were matched, or on desktop we ignore
				var hasCorrectFingerCount = ((fingerCount === options.fingers || options.fingers === ALL_FINGERS) || !SUPPORTS_TOUCH);

				//We have an end value for the finger
				var hasEndPoint = fingerData[0].end.x !== 0;
				
				//Check if the above conditions are met to make this swipe count...
				var isSwipe = (hasCorrectFingerCount && hasEndPoint && hasValidPinchResult);
				
				//If we are in a swipe, validate the time and distance...
				if (isSwipe) {
					var hasValidTime = validateSwipeTime();
					
					//Check the distance meets threshold settings
					var hasValidDistance = validateSwipeDistance();
					
					// if the user swiped more than the minimum length, perform the appropriate action
					// hasValidDistance is null when no distance is set 
					if ((hasValidDistance === true || hasValidDistance === null) && hasValidTime) {
						triggerHandler(event, phase);
					}
					else if (!hasValidTime || hasValidDistance === false) {
						phase = PHASE_CANCEL;
						triggerHandler(event, phase);
					}
				}
				else {
					phase = PHASE_CANCEL;
					triggerHandler(event, phase);
				}
			}
			else if (phase === PHASE_MOVE) {
				phase = PHASE_CANCEL;
				triggerHandler(event, phase);
			}

			$element.unbind(MOVE_EV, touchMove, false);
			$element.unbind(END_EV, touchEnd, false);
			
			setTouchInProgress(false);
		}

		/**
		* Event handler for a touch cancel event. 
		* Clears current vars
		*/
		function touchCancel() {
			// reset the variables back to default values
			fingerCount = 0;
			endTime = 0;
			startTime = 0;
			startTouchesDistance=0;
			endTouchesDistance=0;
			pinchZoom=1;
			setTouchInProgress(false);
		}


		/**
		* Trigger the relevant event handler
		* The handlers are passed the original event, the element that was swiped, and in the case of the catch all handler, the direction that was swiped, "left", "right", "up", or "down"
		*/
		function triggerHandler(event, phase) {
			var ret = undefined;

			//update status
			if (options.swipeStatus) {
				ret = options.swipeStatus.call($element, event, phase, direction || null, distance || 0, duration || 0, fingerCount);
			}
			
			if (options.pinchStatus && didPinch()) {
				ret = options.pinchStatus.call($element, event, phase, pinchDirection || null, endTouchesDistance || 0, duration || 0, fingerCount, pinchZoom);
			}

			if (phase === PHASE_CANCEL) {
				if (options.click && (fingerCount === 1 || !SUPPORTS_TOUCH) && (isNaN(distance) || distance === 0)) {
					ret = options.click.call($element, event, event.target);
				}
			}
			
			if (phase == PHASE_END) {
				//trigger catch all event handler
				if (options.swipe) {
					ret = options.swipe.call($element, event, direction, distance, duration, fingerCount);
				}
				//trigger direction specific event handlers	
				switch (direction) {
					case LEFT:
						if (options.swipeLeft) {
							ret = options.swipeLeft.call($element, event, direction, distance, duration, fingerCount);
						}
						break;

					case RIGHT:
						if (options.swipeRight) {
							ret = options.swipeRight.call($element, event, direction, distance, duration, fingerCount);
						}
						break;

					case UP:
						if (options.swipeUp) {
							ret = options.swipeUp.call($element, event, direction, distance, duration, fingerCount);
						}
						break;

					case DOWN:
						if (options.swipeDown) {
							ret = options.swipeDown.call($element, event, direction, distance, duration, fingerCount);
						}
						break;
				}
				
				
				switch (pinchDirection) {
					case IN:
						if (options.pinchIn) {
							ret = options.pinchIn.call($element, event, pinchDirection || null, endTouchesDistance || 0, duration || 0, fingerCount, pinchZoom);
						}
						break;
					
					case OUT:
						if (options.pinchOut) {
							ret = options.pinchOut.call($element, event, pinchDirection || null, endTouchesDistance || 0, duration || 0, fingerCount, pinchZoom);
						}
						break;	
				}
			}


			if (phase === PHASE_CANCEL || phase === PHASE_END) {
				//Manually trigger the cancel handler to clean up data
				touchCancel(event);
			}

			return ret;
		}


		/**
		* Checks the user has swipe far enough
		*/
		function validateSwipeDistance() {
			if (options.threshold !== null) {
				return distance >= options.threshold;
			}
			return null;
		}



		/**
		* Checks that the time taken to swipe meets the minimum / maximum requirements
		*/
		function validateSwipeTime() {
			var result;
			//If no time set, then return true

			if (options.maxTimeThreshold) {
				if (duration >= options.maxTimeThreshold) {
					result = false;
				} else {
					result = true;
				}
			}
			else {
				result = true;
			}

			return result;
		}


		/**
		* Checks direction of the swipe and the value allowPageScroll to see if we should allow or prevent the default behaviour from occurring.
		* This will essentially allow page scrolling or not when the user is swiping on a touchSwipe object.
		*/
		function validateDefaultEvent(event, direction) {
			if (options.allowPageScroll === NONE || hasPinches()) {
				event.preventDefault();
			} else {
				var auto = options.allowPageScroll === AUTO;

				switch (direction) {
					case LEFT:
						if ((options.swipeLeft && auto) || (!auto && options.allowPageScroll != HORIZONTAL)) {
							event.preventDefault();
						}
						break;

					case RIGHT:
						if ((options.swipeRight && auto) || (!auto && options.allowPageScroll != HORIZONTAL)) {
							event.preventDefault();
						}
						break;

					case UP:
						if ((options.swipeUp && auto) || (!auto && options.allowPageScroll != VERTICAL)) {
							event.preventDefault();
						}
						break;

					case DOWN:
						if ((options.swipeDown && auto) || (!auto && options.allowPageScroll != VERTICAL)) {
							event.preventDefault();
						}
						break;
				}
			}

		}


		/**
		* Calcualte the duration of the swipe
		*/
		function calculateDuration() {
			return endTime - startTime;
		}
		
		/**
		* Calculate the distance between 2 touches (pinch)
		*/
		function calculateTouchesDistance(startPoint, endPoint) {
			var diffX = Math.abs(startPoint.x - endPoint.x);
			var diffY = Math.abs(startPoint.y - endPoint.y);
				
			return Math.round(Math.sqrt(diffX*diffX+diffY*diffY));
		}
		
		/**
		* Calculate the zoom factor between the start and end distances
		*/
		function calculatePinchZoom(startDistance, endDistance) {
			var percent = (endDistance/startDistance) * 1;
			return percent.toFixed(2);
		}
		
		
		/**
		* Returns the pinch direction, either IN or OUT for the given points
		*/
		function calculatePinchDirection() {
			if(pinchZoom<1) {
				return OUT;
			}
			else {
				return IN;
			}
		}
		
		
		/**
		* Calculate the length / distance of the swipe
		* @param finger A finger object containing start and end points
		*/
		function calculateDistance(startPoint, endPoint) {
			return Math.round(Math.sqrt(Math.pow(endPoint.x - startPoint.x, 2) + Math.pow(endPoint.y - startPoint.y, 2)));
		}

		/**
		* Calcualte the angle of the swipe
		* @param finger A finger object containing start and end points
		*/
		function caluculateAngle(startPoint, endPoint) {
			var x = startPoint.x - endPoint.x;
			var y = endPoint.y - startPoint.y;
			var r = Math.atan2(y, x); //radians
			var angle = Math.round(r * 180 / Math.PI); //degrees

			//ensure value is positive
			if (angle < 0) {
				angle = 360 - Math.abs(angle);
			}

			return angle;
		}

		/**
		* Calcualte the direction of the swipe
		* This will also call caluculateAngle to get the latest angle of swipe
		* @param finger A finger object containing start and end points
		*/
		function calculateDirection(startPoint, endPoint ) {
			var angle = caluculateAngle(startPoint, endPoint);

			if ((angle <= 45) && (angle >= 0)) {
				return LEFT;
			} else if ((angle <= 360) && (angle >= 315)) {
				return LEFT;
			} else if ((angle >= 135) && (angle <= 225)) {
				return RIGHT;
			} else if ((angle > 45) && (angle < 135)) {
				return DOWN;
			} else {
				return UP;
			}
		}
		

		/**
		* Returns a MS time stamp of the current time
		*/
		function getTimeStamp() {
			var now = new Date();
			return now.getTime();
		}

		/**
		* Removes all listeners that were associated with the plugin
		*/
		function removeListeners() {
			$element.unbind(START_EV, touchStart);
			$element.unbind(CANCEL_EV, touchCancel);
			$element.unbind(MOVE_EV, touchMove);
			$element.unbind(END_EV, touchEnd);
			setTouchInProgress(false);
		}
		
		/**
		 * Returns true if any Pinch events have been registered
		 */
		function hasPinches() {
			return options.pinchStatus || options.pinchIn || options.pinchOut;
		}
		
		/**
		 * Returns true if we are detecting pinches, and have one
		 */
		function didPinch() {
			return pinchDirection && hasPinches();
		}
		

		
		/**
		* gets a data flag to indicate that a touch is in progress
		*/
		function getTouchInProgress() {
			return $element.data(PLUGIN_NS+'_intouch') === true ? true : false;
		}
		
		/**
		* Sets a data flag to indicate that a touch is in progress
		*/
		function setTouchInProgress(val) {
			val = val===true?true:false;
			$element.data(PLUGIN_NS+'_intouch', val);
		}
		
		function createFingerData() {
			var fingerData=[];
			for (var i=0; i<=5; i++) {
				fingerData.push({
					start:{ x: 0, y: 0 },
					end:{ x: 0, y: 0 },
					delta:{ x: 0, y: 0 }
				});
			}
			
			return fingerData;
		}

	}

})(jQuery);
/*
 * Swipe Gallery
 *
 * Copyright (c) 2012 Shakhov Denis <denis.shakhov@gmail.com>
 * 
 * $Version: 1.0
 */

(function($) {
	$.fn.swipegallery = function() {
		var obj = null;
		var container = null;
		var inner = null;
		var items = null;
		var counter = null;
		var index = 0;
		var items_count = 0;
		var item_width = 0;

		// Resize
		resize = function() {
			item_width = obj.width();
			items.width(item_width);
			container.width(items_count * item_width);
			inner.height(items.eq(index).find('.sg-item-inner').height());
			goToSlide(index);
		};

		// Update counter
		updateCounter = function() {
			counter.text(index + 1);
		};

		// Go to slide
		goToSlide = function(slide_index) {
			inner.addClass('loader');
			updateCounter();

			var img = items.eq(slide_index).find('img');

			if (img.attr('src') == "") {
				img.attr('src', img.data('original'));
			}

			if (img[0].complete) {
				update(slide_index);
			} else {
				img.load(function () {
					if (index == slide_index) update(slide_index);
				});
			}
		};

		// Update viewport
		update = function(slide_index) {
			inner.removeClass('loader');

			inner.height(items.eq(slide_index).css('display', 'table-cell').find('.sg-item-inner').height());
			container.css('marginLeft', -item_width * slide_index);
		};

		// Go to the next slide
		next = function() {
			index < items_count - 1 ? index++ : index = 0;
			goToSlide(index);
		};

		// Go to the previous slide
		prev = function() {
			index > 0 ? index-- : index = items_count - 1;
			goToSlide(index);
		};


		this.each(function () {
			// Init
			obj         = $(this);
			container   = obj.find('ul');
			inner       = obj.find('.sg-inner');
			counter     = obj.find('.sg-pos');

			// Move cover to front
			container.find('.sg-item-cover').prependTo(container);

			items       = container.find('li');
			items_count = items.length;
			
			resize();
			updateCounter();

			// Bind gestures
			inner.swipe({
				swipeLeft: function(event, direction, distance, duration, fingerCount) {
					next();
				},
				swipeRight: function(event, direction, distance, duration, fingerCount) {
					prev();
				}
			});

			// Button navigation
			obj.find('.sg-prev').click(function() {
				prev();
			});

			obj.find('.sg-next').click(function() {
				next();
			});

			// Resize
			$(window).resize(function () {
				resize();
			});
		});
	};
})(jQuery);
/*global jQuery */
/*jshint multistr:true browser:true */
/*!
* FitVids 1.0
*
* Copyright 2011, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
* Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
* Released under the WTFPL license - http://sam.zoy.org/wtfpl/
*
* Date: Thu Sept 01 18:00:00 2011 -0500
*/

(function( $ ){

  "use strict";

  $.fn.fitVids = function( options ) {
    var settings = {
      customSelector: null
    };

    var div = document.createElement('div'),
        ref = document.getElementsByTagName('base')[0] || document.getElementsByTagName('script')[0];

    div.className = 'fit-vids-style';
    div.innerHTML = '&shy;<style>         \
      .fluid-width-video-wrapper {        \
         width: 100%;                     \
         position: relative;              \
         padding: 0;                      \
      }                                   \
                                          \
      .fluid-width-video-wrapper iframe,  \
      .fluid-width-video-wrapper object,  \
      .fluid-width-video-wrapper embed {  \
         position: absolute;              \
         top: 0;                          \
         left: 0;                         \
         width: 100%;                     \
         height: 100%;                    \
      }                                   \
    </style>';

    ref.parentNode.insertBefore(div,ref);

    if ( options ) {
      $.extend( settings, options );
    }

    return this.each(function(){
      var selectors = [
        "iframe[src*='player.vimeo.com']",
        "iframe[src*='www.youtube.com']",
        "iframe[src*='www.youtube-nocookie.com']",
        "iframe[src*='www.kickstarter.com']",
        "object",
        "embed"
      ];

      if (settings.customSelector) {
        selectors.push(settings.customSelector);
      }

      var $allVideos = $(this).find(selectors.join(','));

      $allVideos.each(function(){
        var $this = $(this);
        if (this.tagName.toLowerCase() === 'embed' && $this.parent('object').length || $this.parent('.fluid-width-video-wrapper').length) { return; }
        var height = ( this.tagName.toLowerCase() === 'object' || ($this.attr('height') && !isNaN(parseInt($this.attr('height'), 10))) ) ? parseInt($this.attr('height'), 10) : $this.height(),
            width = !isNaN(parseInt($this.attr('width'), 10)) ? parseInt($this.attr('width'), 10) : $this.width(),
            aspectRatio = height / width;
        if(!$this.attr('id')){
          var videoID = 'fitvid' + Math.floor(Math.random()*999999);
          $this.attr('id', videoID);
        }
        $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css('padding-top', (aspectRatio * 100)+"%");
        $this.removeAttr('height').removeAttr('width');
      });
    });
  };
})( jQuery );
;
var ls = ls ||
{};

ls.comments = ls.comments ||
{};

/**
 * Обработка комментариев
 */
ls.comments = (function ($) {
    var that = ls.comments;

    this.superior = function (name) {
        var that = this;
        method = that[name];
        return function () {
            return method.apply(that, arguments);
        }
    }

    this.mytoggleCommentForm = function (idComment, bNoFocus) {
        if (typeof (this.sBStyle) != 'undefined')
            $('#comment-button-submit').css('display', this.sBStyle);
        if (typeof (this.cbsclick) != 'undefined') {
            $('#comment-button-submit').unbind('click');
            $('#comment-button-submit').attr('onclick', this.cbsclick);
        }

        var b = $('#comment-button-submit-edit');
        if (b.length)
            b.remove();

        b = $('#comment-button-history');
        if (b.length)
            b.remove();

        b = $('#comment-button-cancel');
        if (b.length)
            b.remove();

        this.super_toggleCommentForm(idComment, bNoFocus);
    }

    this.cancelEditComment = function (idComment) {
        var reply = $('#reply');
        if (!reply.length) {
            return;
        }

        reply.hide();
        this.setFormText('');
    }

    this.editComment = function (idComment) {
        var reply = $('#reply');
        if (!reply.length) {
            return;
        }

        if (!(this.iCurrentShowFormComment == idComment && reply.is(':visible'))) {
            var thisObj = this;
            $('#comment_content_id_' + idComment).addClass(thisObj.options.classes.form_loader);
            ls.ajax(aRouter.ajax + 'editcomment-getsource/',
                {
                    'idComment':idComment
                }, function (result) {
                    $('#comment_content_id_' + idComment).removeClass(thisObj.options.classes.form_loader);
                    if (!result) {
                        ls.msg.error('Error', 'Please try again later');
                        return;
                    }
                    if (result.bStateError) {
                        ls.msg.error(null, result.sMsg);
                    }
                    else {
                        thisObj.toggleCommentForm(idComment);
                        thisObj.sBStyle = $('#comment-button-submit').css('display');
                        var cbs = $('#comment-button-submit');
                        cbs.css('display', 'none');
                        thisObj.cbsclick = $('#comment-button-submit').attr('onclick');

                        $('#comment-button-submit').attr('onclick', "");
                        $('#comment-button-submit').bind('click', function () {
                            $('#comment-button-submit-edit').click();
                            return false;
                        });
                        if (result.bHasHistory)
                            cbs.after($(thisObj.options.history_button_code));

                        cbs.after($(thisObj.options.cancel_button_code));

                        cbs.after($(thisObj.options.edit_button_code));
                        ls.comments.setFormText(result.sCommentSource);

                        thisObj.enableFormComment();
                    }
                });
        }
        else {
            reply.hide();
            return;
        }
    }

    this.setFormText=function (sText)
    {
        if (this.options.wysiwyg) {
            tinyMCE.execCommand('mceRemoveControl', false, 'form_comment_text');
            $('#form_comment_text').val(sText);
            tinyMCE.execCommand('mceAddControl', true, 'form_comment_text');
        }
        else if (typeof($('#form_comment_text').getObject) == 'function') {
            $('#form_comment_text').destroyEditor();
            $('#form_comment_text').val(sText);
            $('#form_comment_text').redactor();
        }
        else
            $('#form_comment_text').val(sText);
    }

    this.edit = function (formObj, targetId, targetType) {
        if (this.options.wysiwyg) {
            $('#' + formObj + ' textarea').val(tinyMCE.activeEditor.getContent());
        }
        else
            if (typeof($('#form_comment_text').getObject) == 'function')
            {
                $('#' + formObj + ' textarea').val($('#form_comment_text').getCode());
            }
        formObj = $('#' + formObj);

        $('#form_comment_text').addClass(this.options.classes.form_loader).attr('readonly', true);
        $('#comment-button-submit').attr('disabled', 'disabled');

        var lData = formObj.serializeJSON();
        var idComment = lData.reply;

        ls.ajax(aRouter.ajax + 'editcomment-edit/', lData, function (result) {
            $('#comment-button-submit').removeAttr('disabled');
            if (!result) {
                this.enableFormComment();
                ls.msg.error('Error', 'Please try again later');
                return;
            }
            if (result.bStateError) {
                this.enableFormComment();
                ls.msg.error(null, result.sMsg);
            }
            else {
                if (result.sMsg)
                    ls.msg.notice(null, result.sMsg);

                ls.comments.enableFormComment();
                ls.comments.setFormText('');

                // Load new comments
                if (result.bEdited) {
                    $('#comment_content_id_' + idComment).html(result.sCommentText);
                }
                if (!result.bCanEditMore)
                    $('#comment_id_' + idComment).find('.editcomment_editlink').remove();
                this.load(targetId, targetType, idComment, true);
                if (ls.blocks) {
                    var curItemBlock = ls.blocks.getCurrentItem('stream');
                    if (curItemBlock.data('type') == 'comment') {
                        ls.blocks.load(curItemBlock, 'stream');
                    }
                }

                ls.hook.run('ls_comments_edit_after', [ formObj, targetId, targetType, result ]);
            }
        }.bind(this));
    }

    this.showHistory = function () {
        formObj = $('#form_comment');

        $('#form_comment_text').addClass(this.options.classes.form_loader).attr('readonly', true);
        $('#comment-button-submit-edit').attr('disabled', 'disabled');

        var lData = formObj.serializeJSON();
        lData.form_comment_text = '';
        var idComment = lData.reply;

        ls.ajax(aRouter.ajax + 'editcomment-gethistory/', lData, function (result) {
            $('#comment-button-submit-edit').removeAttr('disabled');
            if (!result) {
                this.enableFormComment();
                ls.msg.error('Error', 'Please try again later');
                return;
            }
            if (result.bStateError) {
                this.enableFormComment();
                ls.msg.error(null, result.sMsg);
            }
            else {
                if (result.sMsg)
                    ls.msg.notice(null, result.sMsg);

                this.enableFormComment();
                $('#editcomment-history-content').html(result.sContent);
                $('#modal-editcomment-history').jqmShow();
            }
        }.bind(this));
    }

    this.init_editcomment = function () {
        this.super_toggleCommentForm = that.superior("toggleCommentForm");
        ls.comments.toggleCommentForm = this.mytoggleCommentForm;
    }

    return this;
}).call(ls.comments ||
{}, jQuery);

jQuery(document).ready(function () {
    ls.comments.init_editcomment();
});
;
