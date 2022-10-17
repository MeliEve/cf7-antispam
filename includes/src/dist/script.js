!function(){"use strict";window.onload=function(){if(!window.wpcf7)return;const t=cf7a_settings.prefix,n=cf7a_settings.version;function i(){if("maxTouchPoints"in navigator)i=navigator.maxTouchPoints>0;else if("msMaxTouchPoints"in navigator)i=navigator.msMaxTouchPoints>0;else{const e=window.matchMedia&&matchMedia("(pointer:coarse)");if(e&&"(pointer:coarse)"===e.media)i=!!e.matches;else if("orientation"in window)i=!0;else{const e=navigator.userAgent;i=/\b(BlackBerry|webOS|iPhone|IEMobile)\b/i.test(e)||/\b(Android|Windows Phone|iPad|iPod)\b/i.test(e)}}return i}wpcf7.cached=0===parseInt(cf7a_settings.disableReload)&&wpcf7.cached;const a=()=>{var e,t,n,a,o,r,d;const s=navigator.userAgent,l={timezone:null!==(e=Intl.DateTimeFormat().resolvedOptions().timeZone)&&void 0!==e?e:null,platform:null!==(t=navigator.platform)&&void 0!==t?t:null,hardware_concurrency:null!==(n=navigator.hardwareConcurrency)&&void 0!==n?n:null,screens:null!==(a=[screen.width,screen.height])&&void 0!==a?a:null,memory:null!==(o=navigator.deviceMemory)&&void 0!==o?o:null,user_agent:null!=s?s:null,app_version:null!==(r=navigator.appVersion)&&void 0!==r?r:null,webdriver:null!==(d=!1===navigator.webdriver)&&void 0!==d?d:null,session_storage:sessionStorage?1:null};return s.indexOf("Firefox")>-1?l.isFFox=!0:s.indexOf("SamsungBrowser")>-1?l.isSamsung=!0:s.indexOf("Opera")>-1||s.indexOf("OPR")>-1?l.isOpera=!0:s.indexOf("Trident")>-1?l.isIE=!0:s.indexOf("Edge")>-1?l.isIELegacy=!0:s.indexOf("Edg")>-1?l.isEdge=!0:s.indexOf("Chrome")>-1||s.indexOf("CriOS")>-1?l.isChrome=!0:s.indexOf("Safari")>-1||s.indexOf("GSA")>-1?l.isSafari=!0:l.isUnknown=!0,"boolean"==typeof navigator.standalone?l.isIos=!0:s.indexOf("Android")>-1&&(l.isAndroid=!0),(l.isIos||l.isAndroid)&&(l.touch=i()),l},o=function(e,n){let i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:t;const a=document.createElement("input");return a.setAttribute("type","hidden"),a.setAttribute("name",i+e),a.setAttribute("value","string"==typeof n?n:JSON.stringify(n)),a},r=document.querySelectorAll(".wpcf7");if(r.length){let i=0,d=0,s=0;for(const l of r){const r=l.querySelector("form > div"),c=r.querySelector("input[name="+t+"bot_fingerprint]"),u=r.querySelector("input[name="+t+"bot_fingerprint_extras]"),m=r.querySelector("input[name="+t+"_language]"),f=r.querySelector("input[name="+t+"append_on_submit]"),p=r.querySelector("input[name="+t+"version]");if(l.querySelector("form").getAttribute("autocomplete"))continue;p.setAttribute("value",n);const g=a();if(c)if(c.setAttribute("value",c.getAttribute("value").slice(0,5)),!f||g.isIos||g.isIE)for(const e in g)r.appendChild(o(e,g[e]));else{const e=l.querySelector("form");let n=new FormData(e.formData);e.addEventListener("formdata",(e=>{const i=e.formData;for(const e in g)i.append(t+e,g[e]);n=i}))}if(u){const n=function(e){const i=r.querySelector("input[name="+t+"activity]");i&&i.remove(),r.append(o("activity",s++)),s>3&&(document.body.removeEventListener("mouseup",n),document.body.removeEventListener("touchend",n),r.append(o("mouseclick_activity","passed")))};document.body.addEventListener("mouseup",n),document.body.addEventListener("touchend",n);const a=function(e){e.pageY>i&&(d+=1),i=e.pageY,d>3&&(document.removeEventListener("mousemove",a),r.append(o("mousemove_activity","passed")))};document.addEventListener("mousemove",a),(g.isIos||g.isAndroid)&&r.append(o("mousemove_activity","passed"));const l=document.createElement("div");l.id="hidden",r.append(l),String.prototype.hashCode=function(){let e,t,n=0;if(0===this.length)return n;for(e=0;e<this.length;e++)t=this.charCodeAt(e),n=(n<<5)-n+t,n|=0;return n};const c=document.createElement("div");c.id="webgl-vendor",l.append(c);const u=document.getElementById("webgl-vendor"),m=document.createElement("div");m.id="webgl-renderer",l.append(m);const f=document.getElementById("webgl-renderer"),p=document.createElement("canvas"),v=p.getContext("webgl")||p.getContext("webgl-experimental");if(v){const e=v.getExtension("WEBGL_debug_renderer_info");try{const t=v.getParameter(e.UNMASKED_VENDOR_WEBGL);u.innerHTML=t,"Brian Paul"===t||"Google Inc."===t?r.append(o("webgl","failed")):r.append(o("webgl","passed"))}catch(e){u.innerHTML="Error: "+e}try{const t=v.getParameter(e.UNMASKED_RENDERER_WEBGL);f.innerHTML=t,"Mesa OffScreen"===t||-1!==t.indexOf("Swift")?r.append(o("webgl_render","failed")):r.append(o("webgl_render","passed"))}catch(e){f.innerHTML="Error: "+e}}else r.append(o("webgl","failed")),r.append(o("webgl_render","failed"));const h=[],b=[];h[1]=document.createElement("div"),h[1].id="canvas1",h[2]=document.createElement("div"),h[2].id="canvas2",h[3]=document.createElement("div"),h[3].id="canvas3",b[3]=document.createElement("iframe"),b[3].id="canvas3-iframe",b[3].class="canvased",b[3].setAttribute("sandbox","allow-same-origin"),h[3].append(b[3]),h[4]=document.createElement("div"),h[4].id="canvas4",b[4]=document.createElement("iframe"),b[4].id="canvas4-iframe",b[4].class="canvased",b[4].setAttribute("sandbox","allow-same-origin"),h[4].append(b[4]),h[5]=document.createElement("div"),h[5].id="canvas5",b[5]=document.createElement("iframe"),b[5].id="canvas5-iframe",b[5].class="canvased",h[5].append(b[5]),h.forEach((function(e){l.appendChild(e)}));const w=function(t){let n,i=arguments.length>1&&void 0!==arguments[1]&&arguments[1],a=!0;const o="Bot test <canvas> 1.1",r=document.getElementById("canvas"+t),d=document.getElementById("canvas"+t+"-iframe");let s=i?d.contentDocument.createElement("canvas"):document.createElement("canvas");if(s.getContext){n=s.getContext("2d");try{s.setAttribute("width",220),s.setAttribute("height",30),n.textBaseline="top",n.font="14px 'Arial'",n.textBaseline="alphabetic",n.fillStyle="#f60",n.fillRect(53,1,62,20),n.fillStyle="#069",n.fillText(o,2,15),n.fillStyle="rgba(102, 204, 0, 0.7)",n.fillText(o,4,17)}catch(e){s=document.createElement("canvas"),n=s.getContext("2d"),void 0===n||"function"!=typeof s.getContext("2d").fillText?a=!1:(s.setAttribute("width",220),s.setAttribute("height",30),n.textBaseline="top",n.font="14px 'Arial'",n.textBaseline="alphabetic",n.fillStyle="#f60",n.fillRect(125,1,62,20),n.fillStyle="#069",n.fillText(o,2,15),n.fillStyle="rgba(102, 204, 0, 0.7)",n.fillText(o,4,17))}if(a&&"function"==typeof s.toDataURL){var l=s.toDataURL("image/png");try{if("boolean"==typeof l||void 0===l)throw e}catch(e){l=""}0===l.indexOf("data:image/png")||(a=!1)}else a=!1}else a=!1;if(a){const e=document.createElement("div");e.innerHTML="Hash: "+l.hashCode(),r.appendChild(s),r.appendChild(e)}else{const e=document.createElement("div");e.innerHTML="Canvas failed",r.appendChild(e)}};window.canvasCount=0,w("1"),w("2"),w("3",!0),w("4",!0),w("5",!0),l.remove()}m&&r.append(o("browser_language",window.navigator.languages.join()||window.navigator.language||window.navigator.browserLanguage||window.navigator.userLanguage))}}}}();