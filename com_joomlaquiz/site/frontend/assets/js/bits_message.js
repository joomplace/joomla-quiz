///////////////////////////////////////////////////////
/*          ShowMessage(msg_obj,stat,mes)            */
/**  !! BITS_mycommon.js required !! (getObj support)*/
///////////////////////////////////////////////////////
var nSteps1=0;
var sR1=0;var sG1=0;var sB1=0;var eR1=0;var eG1=0;var eB1=0;
var dR1=0;var dG1=0;var dB1=0;var cR1=0;var cG1=0;var cB1=0;
var cStep1=0;
var timerID1=55;
var fd_startR = 250;var fd_startG = 250;var fd_startB = 250;
var fd_endR = 200;var fd_endG = 0;var fd_endB = 0;
function startFadeDec_mes(fade_obj,suffix,to_exec,ch_prop,startR, startG, startB, endR, endG, endB, nSteps) {
	cR1=sR1=parseInt(startR,10);cG1=sG1=parseInt(startG,10);cB1=sB1=parseInt(startB,10);
	eR1=parseInt(endR,10);eG1=parseInt(endG,10);eB1=parseInt(endB,10);
	nSteps1=parseInt(nSteps,10);cStep1=0;
	dR1=(eR1-sR1)/nSteps1;dG1=(eG1-sG1)/nSteps1;dB1=(eB1-sB1)/nSteps1;
	fade_mes(fade_obj,suffix,to_exec,ch_prop);
}
function fade_mes(fade_obj,suffix,to_exec,ch_prop) {
	cStep1++;
	if (cStep1<=nSteps1) {
		var hR=dToH(cR1);var hG=dToH(cG1);var hB=dToH(cB1);
		var color="#"+hR+""+hG+""+hB+"";
		eval('jq_getObj(\''+fade_obj+suffix+'\').style.'+ch_prop+' = color');
		cR1+=dR1;cG1+=dG1;cB1+=dB1;
	  	timerID=setTimeout("fade_mes('"+fade_obj+"', '"+suffix+"', '"+to_exec+"', '"+ch_prop+"')",20);
	}
	else {
		var hR=dToH(eR1);var hG=dToH(eG1);var hB=dToH(eB1);
		var color="#"+hR+""+hG+""+hB+"";
		eval('jq_getObj(\''+fade_obj+suffix+'\').style.'+ch_prop+' = color');
		if (to_exec != '') {eval(to_exec);}
	}
}
function dToH(dN) {
	dN=Math.floor(dN);
	var dStr=""+dN;
	for (var i=0; i<dStr.length; i++) {
		if (dStr.charAt(i)>='0' && dStr.charAt(i)<='9'){;}
		else {return decNum;}
	}
	var result=dN;
	var remainder="";
	var hexNum="";
	var hexAlphabet=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
	while (result>0) {
		result=Math.floor(dN/16);
		remainder=dN%16;
		dN=result;
		hexNum=""+hexAlphabet[remainder]+""+hexNum;
	};
	if (hexNum.length==1) hexNum="0"+hexNum;
	else if (hexNum.length==0) hexNum="00";
	return hexNum;
}   

function ShowMessage(msg_obj,stat,mes) { 
	var mes_span = jq_getObj(msg_obj);
	if (stat == 1) {
		mes_span.innerHTML = mes;
		mes_span.style.display = "block";
		mes_span.style.visibility = "visible";		
		startFadeDec_mes(msg_obj,'','','color',fd_startR,fd_startG,fd_startB,fd_endR,fd_endG,fd_endB,30);
	} else {
		mes_span.innerHTML = '<!-- x -->';
		mes_span.style.display = "none";
		mes_span.style.visibility = "hidden";		
	}
}