(()=>{"use strict";var e,t,o={609:e=>{e.exports=window.React},0:e=>{e.exports=window.wc.blocksCheckout},656:e=>{e.exports=window.wc.blocksComponents},594:e=>{e.exports=window.wc.wcBlocksData},703:e=>{e.exports=window.wc.wcSettings},143:e=>{e.exports=window.wp.data},87:e=>{e.exports=window.wp.element},723:e=>{e.exports=window.wp.i18n}},r={};function n(e){var t=r[e];if(void 0!==t)return t.exports;var i=r[e]={exports:{}};return o[e](i,i.exports,n),i.exports}n.m=o,n.d=(e,t)=>{for(var o in t)n.o(t,o)&&!n.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},n.f={},n.e=e=>Promise.all(Object.keys(n.f).reduce(((t,o)=>(n.f[o](e,t),t)),[])),n.u=e=>e+".js",n.miniCssF=e=>{},n.miniCssF=e=>{},n.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),e={},t="contribuinte-checkout:",n.l=(o,r,i,c)=>{if(e[o])e[o].push(r);else{var a,s;if(void 0!==i)for(var l=document.getElementsByTagName("script"),u=0;u<l.length;u++){var p=l[u];if(p.getAttribute("src")==o||p.getAttribute("data-webpack")==t+i){a=p;break}}a||(s=!0,(a=document.createElement("script")).charset="utf-8",a.timeout=120,n.nc&&a.setAttribute("nonce",n.nc),a.setAttribute("data-webpack",t+i),a.src=o),e[o]=[r];var d=(t,r)=>{a.onerror=a.onload=null,clearTimeout(b);var n=e[o];if(delete e[o],a.parentNode&&a.parentNode.removeChild(a),n&&n.forEach((e=>e(r))),t)return t(r)},b=setTimeout(d.bind(null,void 0,{type:"timeout",target:a}),12e4);a.onerror=d.bind(null,a.onerror),a.onload=d.bind(null,a.onload),s&&document.head.appendChild(a)}},n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},(()=>{var e;n.g.importScripts&&(e=n.g.location+"");var t=n.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var o=t.getElementsByTagName("script");if(o.length)for(var r=o.length-1;r>-1&&(!e||!/^http(s?):/.test(e));)e=o[r--].src}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),n.p=e})(),(()=>{var e={241:0};n.f.j=(t,o)=>{var r=n.o(e,t)?e[t]:void 0;if(0!==r)if(r)o.push(r[2]);else{var i=new Promise(((o,n)=>r=e[t]=[o,n]));o.push(r[2]=i);var c=n.p+n.u(t),a=new Error;n.l(c,(o=>{if(n.o(e,t)&&(0!==(r=e[t])&&(e[t]=void 0),r)){var i=o&&("load"===o.type?"missing":o.type),c=o&&o.target&&o.target.src;a.message="Loading chunk "+t+" failed.\n("+i+": "+c+")",a.name="ChunkLoadError",a.type=i,a.request=c,r[1](a)}}),"chunk-"+t,t)}};var t=(t,o)=>{var r,i,[c,a,s]=o,l=0;if(c.some((t=>0!==e[t]))){for(r in a)n.o(a,r)&&(n.m[r]=a[r]);s&&s(n)}for(t&&t(o);l<c.length;l++)i=c[l],n.o(e,i)&&e[i]&&e[i][0](),e[i]=0},o=globalThis.webpackChunkcontribuinte_checkout=globalThis.webpackChunkcontribuinte_checkout||[];o.forEach(t.bind(null,0)),o.push=t.bind(null,o.push.bind(o))})(),(()=>{var e=n(0);const t=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"contribuinte-checkout/checkout-block","version":"0.1.0","title":"Checkout block","category":"contribuinte-checkout-category","description":"Add fiscal section to checkout.","supports":{"html":false,"align":false,"multiple":false,"reusable":false},"parent":["woocommerce/checkout-fields-block"],"attributes":{"lock":{"type":"object"},"showStepNumber":{"type":"boolean"},"sectionTitle":{"type":"string"},"sectionDescription":{"type":"string"},"inputLabel":{"type":"string"}},"textdomain":"contribuinte-checkout"}');var o=n(87);(0,e.registerCheckoutBlock)({metadata:t,component:(0,o.lazy)((()=>n.e(239).then(n.bind(n,239))))})})()})();