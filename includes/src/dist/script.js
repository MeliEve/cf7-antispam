!function(){"use strict";window.onload=function(){if(!window.wpcf7)return;const e=cf7a_settings.prefix,t=cf7a_settings.version;wpcf7.cached=0===parseInt(cf7a_settings.disableReload)&&wpcf7.cached;const n=()=>{var e,t,n,i,o,a,r;const d=window.navigator.userAgent,s={timezone:null!==(e=Intl.DateTimeFormat().resolvedOptions().timeZone)&&void 0!==e?e:null,platform:null!==(t=window.navigator.platform)&&void 0!==t?t:null,hardware_concurrency:null!==(n=window.navigator.hardwareConcurrency)&&void 0!==n?n:null,screens:null!==(i=[window.screen.width,window.screen.height])&&void 0!==i?i:null,memory:null!==(o=window.navigator.deviceMemory)&&void 0!==o?o:null,user_agent:null!=d?d:null,app_version:null!==(a=window.navigator.appVersion)&&void 0!==a?a:null,webdriver:null!==(r=!1===window.navigator.webdriver)&&void 0!==r?r:null,session_storage:window.sessionStorage?1:null};return d.indexOf("Firefox")>-1?s.isFFox=!0:d.indexOf("SamsungBrowser")>-1?s.isSamsung=!0:d.indexOf("Opera")>-1||d.indexOf("OPR")>-1?s.isOpera=!0:d.indexOf("Trident")>-1?s.isIE=!0:d.indexOf("Edge")>-1?s.isIELegacy=!0:d.indexOf("Edg")>-1?s.isEdge=!0:d.indexOf("Chrome")>-1||d.indexOf("CriOS")>-1?s.isChrome=!0:d.indexOf("Safari")>-1||d.indexOf("GSA")>-1?s.isSafari=!0:s.isUnknown=!0,"boolean"==typeof window.navigator.standalone?s.isIos=!0:d.indexOf("Android")>-1&&(s.isAndroid=!0),(s.isIos||s.isAndroid)&&(s.touch=function(){let e;if("maxTouchPoints"in window.navigator)e=window.navigator.maxTouchPoints>0;else if("msMaxTouchPoints"in window.navigator)e=window.navigator.msMaxTouchPoints>0;else{const t=window.matchMedia&&window.matchMedia("(pointer:coarse)");if(t&&"(pointer:coarse)"===t.media)e=!!t.matches;else if("orientation"in window)e=!0;else{const t=window.navigator.userAgent;e=/\b(BlackBerry|webOS|iPhone|IEMobile)\b/i.test(t)||/\b(Android|Windows Phone|iPad|iPod)\b/i.test(t)}}return e}()),s},i=function(t,n){let i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:e;const o=document.createElement("input");return o.setAttribute("type","hidden"),o.setAttribute("name",i+t),o.setAttribute("value","string"==typeof n?n:JSON.stringify(n)),o},o=document.querySelectorAll(".wpcf7");if(o.length){let a=0,r=0,d=0;for(const s of o){const o=s.querySelector("form > div"),l=o.querySelector("input[name="+e+"bot_fingerprint]"),c=o.querySelector("input[name="+e+"bot_fingerprint_extras]"),u=o.querySelector("input[name="+e+"_language]"),m=o.querySelector("input[name="+e+"append_on_submit]"),f=o.querySelector("input[name="+e+"version]");if(s.querySelector("form").getAttribute("autocomplete"))continue;f.setAttribute("value",t);const p=n();if(l)if(l.setAttribute("value",l.getAttribute("value").slice(0,5)),!m||p.isIos||p.isIE)for(const e in p)o.appendChild(i(e,p[e]));else{const t=s.querySelector("form");let n=new FormData(t.formData);t.addEventListener("formdata",(t=>{const i=t.formData;for(const t in p)i.append(e+t,p[t]);n=i}))}if(c){const t=function(n){const a=o.querySelector("input[name="+e+"activity]");a&&a.remove(),o.append(i("activity",d++)),d>3&&(document.body.removeEventListener("mouseup",t),document.body.removeEventListener("touchend",t),o.append(i("mouseclick_activity","passed")))};document.body.addEventListener("mouseup",t),document.body.addEventListener("touchend",t);const n=function(e){e.pageY>a&&(r+=1),a=e.pageY,r>3&&(document.removeEventListener("mousemove",n),o.append(i("mousemove_activity","passed")))};document.addEventListener("mousemove",n),(p.isIos||p.isAndroid)&&o.append(i("mousemove_activity","passed"));const s=document.createElement("div");s.id="hidden",o.append(s),String.prototype.hashCode=function(){let e,t,n=0;if(0===this.length)return n;for(e=0;e<this.length;e++)t=this.charCodeAt(e),n=(n<<5)-n+t,n|=0;return n};const l=document.createElement("div");l.id="webgl-vendor",s.append(l);const c=document.getElementById("webgl-vendor"),u=document.createElement("div");u.id="webgl-renderer",s.append(u);const m=document.getElementById("webgl-renderer"),f=document.createElement("canvas"),g=f.getContext("webgl")||f.getContext("webgl-experimental");if(g){const e=g.getExtension("WEBGL_debug_renderer_info");try{const t=g.getParameter(e.UNMASKED_VENDOR_WEBGL);c.innerHTML=t,"Brian Paul"===t||"Google Inc."===t?o.append(i("webgl","failed")):o.append(i("webgl","passed"))}catch(e){c.innerHTML="Error: "+e}try{const t=g.getParameter(e.UNMASKED_RENDERER_WEBGL);m.innerHTML=t,"Mesa OffScreen"===t||-1!==t.indexOf("Swift")?o.append(i("webgl_render","failed")):o.append(i("webgl_render","passed"))}catch(e){m.innerHTML="Error: "+e}}else o.append(i("webgl","failed")),o.append(i("webgl_render","failed"));const v=[],w=[];v[1]=document.createElement("div"),v[1].id="canvas1",v[2]=document.createElement("div"),v[2].id="canvas2",v[3]=document.createElement("div"),v[3].id="canvas3",w[3]=document.createElement("iframe"),w[3].id="canvas3-iframe",w[3].class="canvased",w[3].setAttribute("sandbox","allow-same-origin"),v[3].append(w[3]),v[4]=document.createElement("div"),v[4].id="canvas4",w[4]=document.createElement("iframe"),w[4].id="canvas4-iframe",w[4].class="canvased",w[4].setAttribute("sandbox","allow-same-origin"),v[4].append(w[4]),v[5]=document.createElement("div"),v[5].id="canvas5",w[5]=document.createElement("iframe"),w[5].id="canvas5-iframe",w[5].class="canvased",v[5].append(w[5]),v.forEach((function(e){s.appendChild(e)}));const h=function(e){let t,n,i=arguments.length>1&&void 0!==arguments[1]&&arguments[1],o=!0;const a="Bot test <canvas> 1.1",r=document.getElementById("canvas"+e),d=document.getElementById("canvas"+e+"-iframe");let s=i?d.contentDocument.createElement("canvas"):document.createElement("canvas");if(s.getContext){n=s.getContext("2d");try{s.setAttribute("width",220),s.setAttribute("height",30),n.textBaseline="top",n.font="14px 'Arial'",n.textBaseline="alphabetic",n.fillStyle="#f60",n.fillRect(53,1,62,20),n.fillStyle="#069",n.fillText(a,2,15),n.fillStyle="rgba(102, 204, 0, 0.7)",n.fillText(a,4,17)}catch(e){s=document.createElement("canvas"),n=s.getContext("2d"),void 0===n||"function"!=typeof s.getContext("2d").fillText?o=!1:(s.setAttribute("width",220),s.setAttribute("height",30),n.textBaseline="top",n.font="14px 'Arial'",n.textBaseline="alphabetic",n.fillStyle="#f60",n.fillRect(125,1,62,20),n.fillStyle="#069",n.fillText(a,2,15),n.fillStyle="rgba(102, 204, 0, 0.7)",n.fillText(a,4,17))}if(o&&"function"==typeof s.toDataURL){t=s.toDataURL("image/png");try{if("boolean"==typeof t||void 0===t)throw new Error("Unable to load image")}catch(e){t=""}0===t.indexOf("data:image/png")||(o=!1)}else o=!1}else o=!1;if(o){const e=document.createElement("div");e.innerHTML="Hash: "+t.hashCode(),r.appendChild(s),r.appendChild(e)}else{const e=document.createElement("div");e.innerHTML="Canvas failed",r.appendChild(e)}};window.canvasCount=0,h("1"),h("2"),h("3",!0),h("4",!0),h("5",!0),s.remove()}u&&o.append(i("browser_language",window.navigator.languages.join()||window.navigator.language||window.navigator.browserLanguage||window.navigator.userLanguage))}}}}();