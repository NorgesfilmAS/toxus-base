(function(i){i.extend(i.inputmask.defaults.aliases,{Regex:{mask:"r",greedy:!1,repeat:"*",regex:null,regexTokens:null,tokenizer:/\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\]+|./g,quantifierFilter:/[0-9]+[^,]/,definitions:{r:{validator:function(e,j,k,m,f){function i(){var c={isQuantifier:!1,matches:[],isGroup:!1},b,a=[];for(f.regexTokens=[];b=
f.tokenizer.exec(f.regex);)switch(b=b[0],b.charAt(0)){case "[":case "\\":!0!==c.isGroup&&(c={isQuantifier:!1,matches:[],isGroup:!1},f.regexTokens.push(c));0<a.length?a[a.length-1].matches.push(b):c.matches.push(b);break;case "(":c={isQuantifier:!1,matches:[],isGroup:!0};a.push(c);break;case ")":b=a.pop();0<a.length?a[a.length-1].matches.push(b):(c=b,f.regexTokens.push(c));break;case "{":b={isQuantifier:!0,matches:[b],isGroup:!1};0<a.length?a[a.length-1].matches.push(b):c.matches.push(b);break;default:0<
a.length?a[a.length-1].matches.push(b):c.matches.push(b)}}function n(c,b){var a=!1;b&&(d+="(",l++);for(var e=0;e<c.matches.length;e++){var g=c.matches[e];if(!0==g.isGroup)a=n(g,!0);else if(!0==g.isQuantifier){for(var g=g.matches[0],a=f.quantifierFilter.exec(g)[0].replace("}",""),a=d+"{1,"+a+"}",h=0;h<l;h++)a+=")";a=RegExp("^"+a+"$");a=a.test(o);d+=g}else{d+=g;a=d.replace(/\|$/,"");for(h=0;h<l;h++)a+=")";a=RegExp("^"+a+"$");a=a.test(o)}if(a)break}b&&(d+=")",l--);return a}null==f.regexTokens&&i();var m=
j.slice(),d="",j=!1,l=0;m.splice(k,0,e);for(var o=m.join(""),e=0;e<f.regexTokens.length&&!(k=f.regexTokens[e],j=n(k,d,k.isGroup));e++);return j},cardinality:1}}}})})(jQuery);
