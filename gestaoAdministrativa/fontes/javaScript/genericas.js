/**
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/**
* Funções
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28583 $
$Name$
$Author: diogo.zarpelon $
$Date: 2008-03-17 11:16:39 -0300 (Seg, 17 Mar 2008) $

Casos de uso: uc-01.01.00
*/

/**
*
*/
function validaTecla( tecla ){
    var retorno = false;
    if ( navigator.appName == "Netscape" ){
        switch(tecla){
            //backspace
            case 8: retorno = true; break;
            //tab
            case 9: retorno = true; break;
            //enter
            case 13: retorno = true; break;
            //capslock
            case 20: retorno = true; break;
            //esc
            case 27: retorno = true; break;
            //pagup
            case 33: retorno = true; break;
            //pagdown
            case 34: retorno = true; break;
            //end
            case 35: retorno = true; break;
            //home
            case 36: retorno = true; break;
            //esquerda
            case 37: retorno = true; break;
            //cima
            case 38: retorno = true; break;
            //direita
            case 39: retorno = true; break;
            //baixo
            case 40: retorno = true; break;
            //insert
            case 45: retorno = true; break;
            //delete
            case 46: retorno = true; break;
        }
    } else {
        switch(tecla){
            //backspace
            case 8: retorno = true; break;
        }
    }
    return retorno;
}

/**
* Filtra a string informada, retirando todos caracteres não alfanuméricos.
*/
function filtraAlfaNumerico( campo ){
  var expReg = new RegExp("[^a-zA-Z0-9]","g");
  var inCont = 0;
  var novoCampo = "";
  var tmpCampo;
  while(campo.length > inCont ){
     tmpCampo = campo.substr(inCont, 1);
     if( !expReg.test(tmpCampo) ){
        novoCampo += tmpCampo;
     }
     inCont++;
  }
  return novoCampo;
}

/**
* Retira os zeros a esquerda do valor da moeda.
*/
function removeZerosEsquerda( valor ){
    while( valor.substr(0,1) == "0" ){
        valor = valor.substr(1, valor.length );
    }
    return valor;
}

/**
* Retira os epaços a esquerda do valor da string.
*/
function lTrim( valor ){
    while ( valor.charAt(0) == " " ){
        valor = valor.substr(1, valor.length );
    }
    return valor;
}

/**
* Retira os espaços a direita do valor da string.
*/
function rTrim( valor ){
    while ( valor.charAt(valor.length-1) == " " ){
        valor = valor.substr(1, valor.length );
    }
    return valor;
}

/**
* Retira os espaços a direita e esquerda do valor da string.
*/
function trim( valor ){
    valor = lTrim( valor );
    return valor;
}

/**
* Separa um inteir nos milhares.
*/
function inteiroParaMilhar( valor ){
    if( valor != "0" ){
        var expReg = new RegExp("[^0-9,\-]","g");
        valor = valor.replace(expReg, '');
        valor = removeZerosEsquerda( valor );
        var tamanho = valor.length;
        var pos = tamanho - 3;
        var milhar = "";
        var cont = 0;
        while( pos > 0 && tamanho > 3 ){
            valor = valor.substr(0, pos)+"."+valor.substr(pos, tamanho);
            tamanho = valor.length;
            pos = pos - 3;
        }
    }
    return valor;
}

/**
* Preenche um campo comforme o valor setado em outro.
*/
function preencheCampo( selecionado, preenchido, sessao ){
    var iIndice = 0;
    var formulario = selecionado.form.name;
    var d = eval("document."+formulario);
    var iIndex;
    if( selecionado.type == "select-one" && selecionado.value.toUpperCase() == "XXX" ){
        preenchido.value = "";
        return true;

    }else{
        preenchido.value = selecionado.value;

        if( preenchido.type == "select-one" &&  preenchido.value != selecionado.value ){
            alertaAviso("@Valor inválido. ("+selecionado.value+")",'form','erro',sessao);
            preenchido.selectedIndex = 0;
            selecionado.value = '';
            return false;

        }else{
                for(var iCont = 1 ; iCont < d.elements.length ; iCont++){
                    if( d.elements[iCont].name == selecionado.name ){
                        break;
                    }
                }
                if( selecionado.type == "select-one" ){
                    iIndex = iCont+1;
                }else{
                    iIndex = iCont+2;
                }
                if( ( d.elements.length - iIndex ) > 0  ){
                    d.elements[iIndex].focus();
                }
                return true;
        }
    }
    return true;
}

/**
* Preenche um campo comforme o valor setado em outro.
*/
function preencheCampoMensagem( selecionado, preenchido, sessao, mensagem ){
    var iIndice = 0;
    var formulario = selecionado.form.name;
    var d = eval("document."+formulario);
    var iIndex;
    if( selecionado.type == "select-one" && selecionado.value.toUpperCase() == "XXX" ){
        preenchido.value = "";
        return true;

    }else{
        preenchido.value = selecionado.value;

        if( preenchido.type == "select-one" &&  preenchido.value != selecionado.value ){
            alertaAviso("@"+mensagem+". ("+selecionado.value+")",'form','erro',sessao);
            preenchido.selectedIndex = 0;
            selecionado.value = '';
            return false;

        }else{
                for(var iCont = 1 ; iCont < d.elements.length ; iCont++){
                    if( d.elements[iCont].name == selecionado.name ){
                        break;
                    }
                }
                if( selecionado.type == "select-one" ){
                    iIndex = iCont+1;
                }else{
                    iIndex = iCont+2;
                }
                if( ( d.elements.length - iIndex ) > 0  ){
                    d.elements[iIndex].focus();
                }
                return true;
        }
    }
    return true;
} 

/**
* Faz o chamada de alerta.
*/
function alertaAviso(objeto,tipo,chamada,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(15,8);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/index/mensagem.php?'+sessaoid+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    mudaTelaMensagem(sArq);
}

/**
* Chamada de um popup.
*/
function alertaAvisoPopUpPrincipal(objeto,tipo,chamada,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(15,8);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/index/mensagem.php?'+sessaoid+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    mudaTelaMensagemPopUpPrincipal(sArq);
}

/**
* Chamada de uma iframe.
*/
function alertaAvisoTelaPrincipal(objeto,tipo,chamada,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(15,8);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/index/mensagem.php?'+sessaoid+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    mudaTelaMensagemTelaPrincipal(sArq);
}

/**
* Carrega o frame telaMensagem com a página informada.
*/
function mudaTelaMensagem(sPag){
    parent.frames["telaMensagem"].location.replace(sPag);
}

/**
* Carrega o frame telaMensagem com a página informada quando chamado por um popup.
*/
function mudaTelaMensagemPopUpPrincipal(sPag){
    window.opener.parent.frames["telaMensagem"].location.replace(sPag);
}

/**
* Carrega o frame telaMensagem com a página informada quando chamado por uma iframe.
*/
function mudaTelaMensagemTelaPrincipal(sPag){
    parent.parent.frames["telaMensagem"].location.replace(sPag);
}

/**
* Carrega o frame oculto com a página informada.
*/

function mudaFrameOculto(sPag){
    parent.frames["oculto"].location.replace(sPag);
}

/**
* Ordena o select em ordem alfabética
*/function sortByText(a,b){
	if ((a.text+"") < (b.text+"")) { return -1; }
	if ((a.text+"") > (b.text+"")) { return 1; }
    return 0;
}  

/**
* Ordena o select em ordem numérica
*/
function sortByValue(a,b){
    return (a.value - b.value);
} 

/**
* Executa a função que ordena as opções do select
*/
function sortSelect(obj,ordenacao){
    var o = new Array();
    for (var i=0; i<obj.options.length; i++){
        o[o.length] = new Option(obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected);
    }
    
    if (ordenacao == "text")
      o = o.sort(sortByText);

    if (ordenacao == "value")
      o = o.sort(sortByValue);
		
    for (var i=0; i<o.length; i++){
        obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
    }
}

/**
* Envia o valor de um select para outro, ordenando no final
*/
function passaItem(objDe,objPara,acao,ordenacao){      

    objDe 	  = eval(objDe);
    objPara   = eval(objPara);

    if (acao=='tudo'){
        for (var i = 0; i < objDe.length; i++){
          if(!(objDe.options[i].disabled)){        
            valor = objDe.options[i].value;
            texto = objDe.options[i].text;
            var temp = document.createElement('option');
            temp.text = objDe.options[i].text;
            temp.value = objDe.options[i].value;
            temp.title = temp.text;
            destino = objPara.length;
            objPara.options[destino] = temp;
            objDe.options[i] = null;
            i = i - 1;
            tooltip.init();
          }
        }
    }
    if (acao=='selecao'){
        for (var i = 0; i < objDe.length; i++){
            if (objDe.options[i].selected==true){
				valor = objDe.options[i].value;
                texto = objDe.options[i].text;
                var temp = document.createElement('option');
                temp.text = objDe.options[i].text;
                temp.value = objDe.options[i].value;
                temp.title = temp.text;
                destino = objPara.length;
                objPara.options[destino] = temp;
                objDe.options[i] = null;
                i = i - 1;
                tooltip.init();
            }
        }
    }

	// Caso não seja passado o parâmetro para ordenar, ordena default por texto. 
    if (ordenacao == "")
      ordenacao = "text";
    
    // Executa função que ordena o combo com base na ordenação setada (text ou value).
    sortSelect(objPara, ordenacao);
    
	return;
}

/**
* Seleciona todos elementos do combo (select).
*/
function selecionaTodosSelect(obCampo){
    for(var inCount=0; inCount<obCampo.length; inCount++){
        obCampo.options[ inCount ].selected = true;
    }
}

/**
* Abre popUp de pesquisa.
*/
function abrePopUp(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height,namepopup){
    if (width == '') {
        width = 800;
    }
    if (height == '') {
        height = 550;
    }
    var x = 0;
    var y = 0;
    var sessaoid = sessao.substr(15,8);
    var sArq = ''+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&tipoBusca='+tipodebusca;
    var sAux = "prcgm"+ sessaoid +" = window.open(sArq,'prcgm"+ sessaoid +namepopup +"','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/**
* Abre popUp de documentos anexados.
*/
function abrePopUpAnexo(arquivo,anexo,sessao,width,height){
    if (width == '') {
        width = 800;
    }
    if (height == '') {
        height = 550;
    }
    var x = 0;
    var y = 0;
    var sessaoid = sessao.substr(15,8);
    var sArq = arquivo+'?'+sessaoid+'&anexo='+anexo;
    var sAux = "anexo"+ sessaoid +" = window.open(sArq,'anexo"+ sessaoid +"','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/**
* Faz o chamada de alerta de Qqestão nos novos módulos.
*/
function alertaQuestao(pagina,tipo,sessao){
    var x = 350;
    var y = 200;
    var valor = 'teste';
    var chave = 1;
    var sessaoid = sessao.substr(15,8);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/popups/alerta/alerta.php?'+sessaoid+'&tipo='+tipo+'&chamada=sn&chave='+chave+'&valor='+valor+'&pagQuestao='+pagina;
    var wVolta=false;
    var sAux = "msgc"+ sessaoid +" = window.open(sArq,'msgc"+ sessaoid +"','width=400px,height=230px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/**
*
*/
function alertaQuestaoPopUp(pagina,tipo,sessao){
    var x = 350;
    var y = 200;
    var valor = 'teste';
    var chave = 1;
    var sessaoid = sessao.substr(15,8);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/popups/alerta/alertaGenerico.php?'+sessaoid+'&tipo='+tipo+'&chamada=pp&chave='+chave+'&valor='+valor+'&pagQuestao='+pagina;
    var wVolta=false;
    var sAux = "msgc"+ sessaoid +" = window.open(sArq,'msgc"+ sessaoid +"','width=350px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/**
*
*/
function buscaValoresLocalizacao(stActionAnterior, stActionPosterior, stCampo){
    var targetTmp = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'buscaValoresNiveis';
    document.frm.action = stActionAnterior + '&stSelecionado=' + stCampo;
    document.frm.submit();
    document.frm.target = targetTmp;
    document.frm.action = stActionPosterior;
}

/**
*
*/
function buscaValorComboComposto(tipoBusca, stActionAnterior, stActionPosterior, stCampo, targetPosterior, sessao){
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = stActionAnterior + '&stSelecionado=' + stCampo;
    document.frm.submit();
    if( targetPosterior == '' ){
        targetPosterior = 'telePrincipal';
    }
    document.frm.target = targetPosterior;
    document.frm.action = stActionPosterior + '?<?=$sessao->id;?>';
}

/**
*
*/
function buscaValor(tipoBusca,actionAnterior,actionPosterior,targetPosterior,sessao){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = actionAnterior + '?' + sessao;
    document.frm.submit();
    if( targetPosterior == '' ){
        targetPosterior = 'telaPrincipal';
    }
    document.frm.target = targetPosterior;
    document.frm.action = actionPosterior + '?' + sessao;
}

function buscaDados(tipoBusca,actionAnterior,actionPosterior,targetPosterior){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    if( actionAnterior.indexOf('?') == -1 ){
        document.frm.action = actionAnterior + '?<?=$sessao->id;?>';
    }else{
        document.frm.action = actionAnterior;
    }
    document.frm.submit();
    if( targetPosterior == '' ){
        targetPosterior = 'telaPrincipal';
    }
    document.frm.target = targetPosterior;
    if( actionAnterior.indexOf('?') == -1 ){
        document.frm.target = actionPosterior + '?<?=$sessao->id;?>';
    }else{
        document.frm.action = actionPosterior;
    }
}

/**
*
*/
function incluiZerosAEsquerda(str,tamanho,campo,permiteZero) {
    var retorno;
    retorno = str;
    if ( (str.length > 0) && (str.length < tamanho) && ( (toFloat(str) != 0) || permiteZero ) ) {
        for (i=(tamanho - str.length) ; i>0 ; i--) {
            retorno = '0' + retorno;
        }
        campo.value = retorno;
    }
    if (toFloat(str) == 0 && !permiteZero) {
        return "";
    } else {
        return str;
    }
}

/**
*
*/
function toUpperCase(obThis){
    obThis.value = obThis.value.toUpperCase();
}

/**
*
*/
function toLowerCase(obThis){
    obThis.value = obThis.value.toLowerCase();
}

/**
* Descrição: Recupera o option de um determinado VALUE em uma combo.
*/ 
function recuperaOption( campo , valor ){
    var option = 0;
    for (iCount = campo.options.length-1; iCount > 0; iCount--){
        if( campo.options[iCount].value == valor ){
            option = iCount;
            break;
        }
    }
    return option;
}

/**
*
*/
function validaValorMaximo(campo, maxValue, Decimais) {
    var valorCampo = "";
    var valorMax = "";
    for( var i = 0; i < campo.value.length; i++ ){
        if( campo.value[i] == "," ){
          valorCampo += ".";
        }else if( campo.value[i] != "." ){
          valorCampo += campo.value[i];
        }
    }
    for( var i = 0; i < maxValue.length; i++ ){
        if( maxValue[i] == "." ){
            valorMax += "";        
        }
        else if( maxValue[i] == "," ){
            valorMax += ".";
        }else{
            valorMax += maxValue[i];
        }
    }
    valorCampo = parseFloat( valorCampo );
    valorMax   = parseFloat( valorMax );
    if ( valorCampo > valorMax ) {
        return false;
    } else {
        return true;
    }  
}

/**
*
*/
function validaValorMinimo(campo, minValue, Decimais) {
  var valorCampo = 0;
  var valorMin = 0;

  for( var i = 0; i < campo.value.length; i++ ){
        if( campo.value[i] == "," ){
          valorCampo += ".";
        }else if( campo.value[i] != "." ){
          valorCampo += campo.value[i];
        }
  }

  for( var i = 0; i < minValue.length; i++ ){
        if( minValue[i] == "," ){
            valorMin += ".";
        }else{
            valorMin += minValue[i];
        }
  }

  valorCampo = parseFloat( valorCampo );
  valorMin   = parseFloat( valorMin );

  if ( valorCampo < valorMin ) {
    return false;
  } else {
    return true;
  }  
}

/**
*
*/
function selecionaValorCampo( campo ){
    campo.focus();
    campo.select();
}

/**
*
*/
function validaMinLength( campo, tamanho ){
   if( campo.value.length < tamanho && campo.value.length > 0 ){
       campo.value = '';
       return false;
   }else{
       return true;
   }
}

/**
*
*/
function alfaNumerico( campo, evento ){
    var expRegular = new RegExp("[0-9a-zA-Z]","g");
    var teclaPressionada;
    var caracter;
    if ( navigator.appName == "Netscape" ){
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    caracter = String.fromCharCode( teclaPressionada );
    if( !validaTecla( evento.keyCode ) ){
        if( caracter.search(expRegular) == -1 )
            return false;
        else
            return true;
    }
}

/**
*
*/
function removeEspacosExtras( campo,evento ){
    campo.value = campo.value.replace(/\s+/gm, " "); 
    campo.value = campo.value.replace(/^\s*|\s*$/g,"");
}

/**
*
*/
function removeAcentos( campo, evento ){
   var Acentos = "áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ";
   var Traducao ="aaaaaAAAAeeeeiIoooOOOuuUUcC";
   var Posic, Carac;
   var TempLog = "";
   stCampo = campo.value;
   for (var i=0; i < stCampo.length; i++){
       Carac = stCampo.charAt (i);
       Posic  = Acentos.indexOf (Carac);
       if (Posic > -1)
          TempLog += Traducao.charAt (Posic);
       else
          TempLog += Campo.charAt (i);
   }
   campo.value = TempLog;
}

/**
*
*/
function Cancelar ( stLocation, stTarget ){
    document.frm.target = stTarget;
    window.location = stLocation;
}

/**
*
*/
function validaExpressao( campo, evento, expressao ){
    var expRegular = new RegExp(expressao,"g");
    var teclaPressionada;
    var caracter;
    if ( navigator.appName == "Netscape" ){
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    caracter = String.fromCharCode( teclaPressionada );
    if( !validaTecla( evento.keyCode ) ){
        if( caracter.search(expRegular) == -1 )
            return false;
        else
            return true;
    }
}

/**
*
*/
function validaExpressaoInteira( campo, expressao ){
    var i;
    var flag = "ok";
    var expRegular = new RegExp(expressao,"g");
    if (campo.value != "") {
        for(i=0;i<campo.value.length;i++) {
            caracter = campo.value.charAt(i);
            if  ( caracter.search(expRegular) == -1 ) {
                flag = "nok";
            }
        }
        if (flag == "ok")
            return true;
        else
            return false;
    } else {
        return true;
    }
}

/**
* Valida a presenca de caracteres invalidos no conteudo do campo.
*/
function validaCaracteres( campo, palavra , mascara ){
    var CharTest,Teste;
    stCampo = campo.value;
    for (var i=0; i < stCampo.length; i++){
        CharTest = stCampo.charAt(i);
        if( mascara == 'true' ){
            var expRegular = new RegExp("[0-9a-zA-Z]","ig");
            Teste = expRegular.test(CharTest);
            if( Teste ){
                if( !validaCaracterMascara( palavra, CharTest, i ) ){
                    return false;
                }
            } else {
                if( palavra.indexOf(CharTest) < 0 ){
                    return false;
                }
            }
        } else {
            if( palavra.indexOf(CharTest) < 0 ){
                return false;
            }
        }
    }
    return true;
}

/**
* Retira o caracter especial digitada no campo.
*/
function retiraCaracteresEspeciais(obj){
    objValue    = obj.value;
    for (var i = 0; i < objValue.length; i++ ){
        letra = objValue.charAt(i);
        if(letra == "~" || letra == "´" || letra == "`" || letra == "¨" || letra == "^"){
	        obj.value = objValue.substring(0,i) + objValue.substring(i+1, objValue.length);
	    }
	}
}

/**
* Valida o limite de caracteres em campos TextArea.
*/
function validaMaxCaracter(campo,limite,evento,blur){
    var key = '';
    var strCheck = '';
    var whichCode = (window.Event) ? evento.which : evento.keyCode;
    if(blur){
        campo.value = campo.value.substring(0, limite);
        return true;
    }
    if (campo.value.length > limite){
            campo.value = campo.value.substring(0, limite);
            return true;
    }
}

/**
* Muda o item selecionadoem selects que contiverem chave no começo do id do select.
*/
function muda_selects(chave){
    var count;
    var id;
    var texto;
 
    for(i=0; i < document.frm.elements.length; i++){
	   texto = document.frm.elements[i].id;
	   if (texto.match(chave)==chave){
	           document.getElementById(texto).selectedIndex = document.getElementById('mestre').selectedIndex;
	           count++;
	       }
	}
}

/**
* Funções para bloquear e liberar uso do mouse e teclado no frames.
*/
function BloqueiaFrames(boPrincipal,boMenu){
    loadingModal(boPrincipal, boMenu, '');
    /*
    if  (boMenu == true){
            parent.frames[1].document.body.scrollTop=0;
            parent.frames[1].document.getElementById('fundo_carregando').style.visibility='visible';
    }
    if  (boPrincipal==true)   {
            parent.frames[2].document.getElementById('layerFormulario').style.visibility='hidden'; 
            parent.frames[2].document.getElementById('layerFormulario').disabled = true;
            parent.frames[2].document.getElementById('BOTAO').style.visibility='hidden'; 
            parent.frames[2].document.body.scrollTop=0;
            parent.frames[2].document.getElementById('fundo_carregando').style.visibility='visible';            
    }
    */
}



/**
*
*/
function LiberaFrames(boPrincipal,boMenu){
    removePopUp();
    /*
    if  (boMenu == true)      {
            parent.frames[1].document.getElementById('fundo_carregando').style.visibility='hidden';
        }
    if  (boPrincipal==true)   {
            parent.frames[2].document.getElementById('layerFormulario').style.visibility='visible'; 
            parent.frames[2].document.getElementById('BOTAO').style.visibility='visible'; 
            parent.frames[2].document.getElementById('fundo_carregando').style.visibility='hidden';
        }
    */
}

function BloqueiaBotoesFrame( ) {
 parent.frames[2].document.getElementById('layerFormulario').style.visibility='hidden'; 
 parent.frames[2].document.getElementById('layerFormulario').disabled = true;
 parent.frames[2].document.getElementById('BOTAO').style.visibility='hidden'; 
 parent.frames[2].document.body.scrollTop=0;
 parent.frames[2].document.getElementById('fundo_carregando').style.visibility='visible'; 

 for (i=0;i<document.frm.elements.length;i++)
       if ( document.frm.elements[i].type == 'button' ){ 
             document.frm.elements[i].disabled = true;
       } 


}

function LiberaBotoesFrames( ){
   d = window.parent.frames["telaPrincipal"].document; 

   for (i=0;i<d.frm.elements.length;i++)
            d.frm.elements[i].disabled = false;

   parent.frames[2].document.getElementById('layerFormulario').style.visibility='visible'; 
   parent.frames[2].document.getElementById('BOTAO').style.visibility='visible'; 
   parent.frames[2].document.getElementById('fundo_carregando').style.visibility='hidden';

}

/**
* Funções para Recuperar valor descrição do componente BuscaInner.
*/
function buscaValorBscInner( stPaginaBusca, stNomForm, stNomCampoCod, stIdCampoDesc , stTipoBusca ) {
    var f = eval('document.'+stNomForm);
    var stAction = f.action;
    var stTarget = f.target;
    f.target = 'oculto';
    f.action = stPaginaBusca+'&stCtrl=buscaPopup'+'&stNomForm='+stNomForm+'&stNomCampoCod='+stNomCampoCod+'&stIdCampoDesc='+stIdCampoDesc+'&stTipoBusca='+stTipoBusca;
    f.submit();
    f.action = stAction;
    f.target = stTarget;
}

function redirecionaPagina( stPaginaBusca, stNomForm, stTipo ) {

    var f = eval('document.'+stNomForm);
    var stAction = f.action;
    var stTarget = f.target;
    f.target = 'oculto';
    f.action = stPaginaBusca+'&stCtrl='+stTipo;
    f.submit();
    f.action = stAction;
    f.target = stTarget;
}

/**
*
*/
function retornaValorBscInner( stNomCampoCod, stIdCampoDesc, stNomForm, stValorInner ) {
    var d = 'parent.frames["telaPrincipal"].document';
    var f = d+'.'+stNomForm;
    var campoCod        = eval( f + '.'+stNomCampoCod                      );
    var campoHidden     = eval( f + '.Hdn'+stNomCampoCod                   );
    var campoDesc       = eval( d + '.getElementById("'+stIdCampoDesc+'")' );
    if( campoDesc )
        var campoDescHidden = eval( f + '.'+stIdCampoDesc );
    if( stValorInner != '' ) {
        campoHidden.value     = campoCod.value;
        if( eval( campoDesc ) != null ) {
            campoDesc.innerHTML   = stValorInner;
            campoDescHidden.value = stValorInner;
        }
    } else {
        campoCod.value        = '';
        campoHidden.value     = '';
        if( eval( campoDesc ) != null ) {
            campoDesc.innerHTML   = '&nbsp;';
            campoDescHidden.value = '';
        }
    }
}

/**
*
*/
function exibeHistorico( stArquivoHistorico ){
 window.open( stArquivoHistorico,'historico','fullscreen=no,width=640,height=480,resizable=1,scrollbars=1,left=0,top=0');
}

/**
*
*/
function tipoBusca( stCampoTipo, stCampoBusca, stCampoHdn, inOrigem ){
   if( inOrigem == 1 ){
      var stForm = stCampoTipo.form.name;
      stCampoBusca = eval( 'document.'+ stForm + '.' + stCampoBusca );
   }else if( inOrigem == 2 ){
      var stForm =  stCampoBusca.form.name;
      stCampoTipo = eval( 'document.'+ stForm + '.' + stCampoTipo );
   }
   var stCampo = eval(  'document.'+ stForm + '.' + stCampoHdn );
   stCampo.value = "";
   if( stCampoTipo.value == 'inicio' ){
        stCampo.value = stCampoBusca.value + "%";
   }else if( stCampoTipo.value == 'final' ){
       stCampo.value = "%" + stCampoBusca.value;
   }else if( stCampoTipo.value == 'contem' ){
       stCampo.value = "%" + stCampoBusca.value + "%";
   }else if (stCampoTipo.value == 'exata'){
        stCampo.value = stCampoBusca.value;
   }
}

/**
* Funções para validar a placa de um carro e verificar os caracteres digitados em tempo de digitação.
*/
function mascaraPlacaVeiculo(campo){
    testaLetra = new RegExp (/^[A-Z]$/i);
    testaNumero = new RegExp (/^[0-9]$/);
    testaSeparador = new RegExp (/\-/);
    campo.value = campo.value.toUpperCase();
    campoFormatado = campo.value;
    if (!testaLetra.test(campoFormatado[0])){
        campoFormatado = "";
    }
    if (!testaLetra.test(campoFormatado[1])){
        campoFormatado = campoFormatado.slice(0,1);
    }
    if (!testaLetra.test(campoFormatado[2])){
        campoFormatado = campoFormatado.slice(0,2);
    }
    if (campoFormatado.length >= 4) {
        if ( campoFormatado[3] != "-" ){
            complemento = campoFormatado[3];
            campoFormatado = campoFormatado.slice(0,3) + "-"+complemento;
        }
    }
    if (!testaNumero.test(campoFormatado[4])){
        campoFormatado = campoFormatado.slice(0,4);
    }
    if (!testaNumero.test(campoFormatado[5])){
        campoFormatado = campoFormatado.slice(0,5);
    }
    if (!testaNumero.test(campoFormatado[6])){
        campoFormatado = campoFormatado.slice(0,6);
    }
    if (!testaNumero.test(campoFormatado[7])){
        campoFormatado = campoFormatado.slice(0,7);
    }
    campo.value = campoFormatado;
}

/**
* Valida a placa quando o focus é perdido e retorna true ou false.
*/
function verificaPlacaVeiculo(campo){
    placa = new RegExp(/^[A-Z]{3}\-[0-9]{4}$/)
    if (!placa.test(campo.value)){
//   alertaAviso("@Placa inválida. ("+campo.value+")",'form','erro','<?=$sessao->id?>');
     campo.value = '';
     return(true);
     setTimeout('document.forms[0].elements["'+campo.name+'"].focus()',10);
   }
 return(false);
}

/*
* 
*/
function executa(){
   return true;
}


function validaCampoPeriodo(dt1,dt2){
    var hoje = new Date();
    var ano = hoje.getYear();
    if(ano >= 50 && ano <= 99)
        ano = 1900 + ano
    else
        ano = 2000 + ano;

    var pos1 = dt1.indexOf("/",0)
    var dd = dt1.substring(0,pos1)
    pos2 = dt1.indexOf("/", pos1 + 1)
    var mm = dt1.substring(pos1 + 1,pos2)
    var aa = dt1.substring(pos2 + 1,10)

        if(aa.length < 4)
            if(ano > 1999)
            aa = (2000 + parseInt(aa,10))
        else
            aa = (1900 + parseInt(aa,10));
    var data1 = new Date(parseInt(aa,10),parseInt(mm,10) - 1, parseInt(dd,10));
    var pos1 = dt2.indexOf("/",0)
    var dd = dt2.substring(0,pos1)
    pos2 = dt2.indexOf("/", pos1 + 1)
    var mm = dt2.substring(pos1 + 1,pos2)
    var aa = dt2.substring(pos2 + 1,10)
    if(aa.length < 4)
        if(ano > 80 && ano <= 99)
            aa = (1900 + parseInt(aa,10))
        else
            aa = (2000 + parseInt(aa,10));
    var data2 = new Date(parseInt(aa,10),parseInt(mm,10) - 1,parseInt(dd,10));

   if(data1 <= data2){
        return true;
   } else{
        return false;
   }
}




/**Funções utilizadas pelo componente ISelectMultiploRegSubCarEsp
*
*/
function selecionarSelectMultiploRegSubCarEsp(stCampo,boSelected) {
    i = 0;
    while (eval('document.frm.'+stCampo+'[i]')) {
        eval('document.frm.'+stCampo+'[i].selected = '+boSelected+';');
        i++;
    }
}
/**Funções utilizadas pelo componente ISelectMultiploRegSubCarEsp
*
*/

/**Funções utilizadas pelo componente IFiltroAssentamentoMultiplo
*
*/
function selecionarFiltroAssentamentoMultiplo(stCampo,boSelected) {
    i = 0;
    while (eval('document.frm.'+stCampo+'[i]')) {
        eval('document.frm.'+stCampo+'[i].selected = '+boSelected+';');
        i++;
    }
}
/**Funções utilizadas pelo componente IFiltroAssentamentoMultiplo
*
*/

function procuraFocaCampo(){
 var objeto =  new Array("hidden");
 var focado = false;
   if(document.forms[0]){
       for(x=0;x<document.forms[0].elements.length;x++){
           for(z=0;z<objeto.length;z++){
               if(document.forms[0].elements[x].type != objeto[z]){   
                   if(!(document.forms[0].elements[x].disabled == true | document.forms[0].elements[x].readOnly == true)){ 
                       focado = true;
                   }
               }
           }
           if(focado){
               document.forms[0].elements[x].focus();
               break;
           }
       }
   }
}

function focaCampo(elemento, evento, funcao){
  if(elemento.addEventListener){
    elemento.addEventListener(evento, funcao, "undefined");
  }
}

focaCampo(this,"load",function(){
                                  procuraFocaCampo();
                                });

function parseToFloat(stValor){
    var stSaida = '';
    for( var i = 0; i < stValor.length; i++ ){
        if( stValor[i] == "," ){
          stSaida += ".";
        }else if( stValor[i] != "." ){
          stSaida += stValor[i];
        }
    }
    return parseFloat(stSaida);
}

function parseToMoeda(num,decimais) {
    nuVl = 1;
    for(i = 0; i < decimais; i++ ){
        nuVl *= 10;
    }
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
        num = "0";
    num = Math.floor(num*nuVl+0.50000000001);
    cents = num%nuVl;
    num = Math.floor(num/nuVl).toString();
    if(cents<10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
        num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));
    return num + ',' + cents;
}


/**
 * htmlEntities
 *
 * Convert all applicable characters to HTML entities
 *
 * object string
 * return string
 *
 * example:
 *   test = 'äöü'
 *   test.htmlEntities() //returns '&auml;&ouml;&uuml;'
 */

String.prototype.htmlEntities = function()
{
  var chars = new Array ('&','à','á','â','ã','ä','å','æ','ç','è','é',
                         'ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô',
                         'õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ','À',
                         'Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë',
                         'Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö',
                         'Ø','Ù','Ú','Û','Ü','Ý','Þ','','\"','ß','<',
                         '>','¢','£','¤','¥','¦','§','¨','©','ª','«',
                         '¬','­','®','¯','°','±','²','³','´','µ','¶',
                         '·','¸','¹','º','»','¼','½','¾');

  var entities = new Array ('amp','agrave','aacute','acirc','atilde','auml','aring',
                            'aelig','ccedil','egrave','eacute','ecirc','euml','igrave',
                            'iacute','icirc','iuml','eth','ntilde','ograve','oacute',
                            'ocirc','otilde','ouml','oslash','ugrave','uacute','ucirc',
                            'uuml','yacute','thorn','yuml','Agrave','Aacute','Acirc',
                            'Atilde','Auml','Aring','AElig','Ccedil','Egrave','Eacute',
                            'Ecirc','Euml','Igrave','Iacute','Icirc','Iuml','ETH','Ntilde',
                            'Ograve','Oacute','Ocirc','Otilde','Ouml','Oslash','Ugrave',
                            'Uacute','Ucirc','Uuml','Yacute','THORN','euro','quot','szlig',
                            'lt','gt','cent','pound','curren','yen','brvbar','sect','uml',
                            'copy','ordf','laquo','not','shy','reg','macr','deg','plusmn',
                            'sup2','sup3','acute','micro','para','middot','cedil','sup1',
                            'ordm','raquo','frac14','frac12','frac34');

  newString = this;
  for (var i = 0; i < chars.length; i++)
  {
    myRegExp = new RegExp();
    myRegExp.compile(chars[i],'g')
    newString = newString.replace (myRegExp, '&' + entities[i] + ';');
  }
  return newString;
}


/**
 * strPad
 *
 * Pad a string to a certain length with another string
 *
 * This functions returns the input string padded on the left, the right, or both sides
 * to the specified padding length. If the optional argument pad_string is not supplied,
 * the output is padded with spaces, otherwise it is padded with characters from pad_string
 * up to the limit.
 *
 * The optional argument pad_type can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH.
 * If pad_type is not specified it is assumed to be STR_PAD_RIGHT.
 *
 * If the value of pad_length is negative or less than the length of the input string,
 * no padding takes place.
 *
 * object string
 * return string
 *
 * examples:
 *   var input = 'foo';
 *   input.strPad(9);                      // returns "foo      "
 *   input.strPad(9, "*+", STR_PAD_LEFT);  // returns "*+*+*+foo"
 *   input.strPad(9, "*", STR_PAD_BOTH);   // returns "***foo***"
 *   input.strPad(9 , "*********");        // returns "foo******"
 */

var STR_PAD_LEFT  = 0;
var STR_PAD_RIGHT = 1;
var STR_PAD_BOTH  = 2;

String.prototype.strPad = function(pad_length, pad_string, pad_type)
{
  /* Helper variables */
  var num_pad_chars   = pad_length - this.length;/* Number of padding characters */
  var result          = '';                       /* Resulting string */
  var pad_str_val     = ' ';
  var pad_str_len     = 1;                        /* Length of the padding string */
  var pad_type_val    = STR_PAD_RIGHT;            /* The padding type value */
  var i               = 0;
  var left_pad        = 0;
  var right_pad       = 0;
  var error           = false;
  var error_msg       = '';
  var output           = this;

  if (arguments.length < 2 || arguments.length > 4)
  {
    error     = true;
    error_msg = "Wrong parameter count.";
  }


  else if(isNaN(arguments[0]) == true)
  {
    error     = true;
    error_msg = "Padding length must be an integer.";
  }
  /* Setup the padding string values if specified. */
  if (arguments.length > 2)
  {
    if (pad_string.length == 0)
    {
      error     = true;
      error_msg = "Padding string cannot be empty.";
    }
    pad_str_val = pad_string;
    pad_str_len = pad_string.length;

    if (arguments.length > 3)
    {
      pad_type_val = pad_type;
      if (pad_type_val < STR_PAD_LEFT || pad_type_val > STR_PAD_BOTH)
      {
        error     = true;
        error_msg = "Padding type has to be STR_PAD_LEFT, STR_PAD_RIGHT, or STR_PAD_BOTH."
      }
    }
  }

  if(error) throw error_msg;

  if(num_pad_chars > 0 && !error)
  {
    /* We need to figure out the left/right padding lengths. */
    switch (pad_type_val)
    {
      case STR_PAD_RIGHT:
        left_pad  = 0;
        right_pad = num_pad_chars;
        break;

      case STR_PAD_LEFT:
        left_pad  = num_pad_chars;
        right_pad = 0;
        break;

      case STR_PAD_BOTH:
        left_pad  = Math.floor(num_pad_chars / 2);
        right_pad = num_pad_chars - left_pad;
        break;
    }

    for(i = 0; i < left_pad; i++)
    {
      output = pad_str_val.substr(0,num_pad_chars) + output;
    }

    for(i = 0; i < right_pad; i++)
    {
      output += pad_str_val.substr(0,num_pad_chars);
    }
  }

  return output;
}

function selectMultiploToString( obSelectMultiplo ) {
    var i;
    var stLink = '';
    for( i=0; i<obSelectMultiplo.length; i++ ) {
        stLink += '&'+obSelectMultiplo.name+'='+obSelectMultiplo[i].value;
    }
    return stLink;
}

function voltaProcesso(){
    var stPagLimpaSessao  = '<?=CAM_GA_PROT_INSTANCIAS;?>processo/OCManterProcesso.php?<?=$sessao->id;?>&stCtrl=voltarProcesso';
    parent.frames['oculto'].location.replace(stPagLimpaSessao);
}

/*****
 *
 * funcao criada para montar ou reverter o label
 *
*****/
function setLabel( id, reverter )
{
    if( reverter == true )
    {
        window.parent.frames['telaPrincipal'].document.getElementById(id).style.display = 'block';
        window.parent.frames["telaPrincipal"].document.getElementById(id+'_label').style.display = 'none';
    }
    else
    {
        window.parent.frames["telaPrincipal"].document.getElementById(id).style.display = 'none';
        window.parent.frames["telaPrincipal"].document.getElementById(id+'_label').style.display = 'block';
    }
}

function selecionaValoresAtributos(linkPagina, acao, idComponentes, nomeAtributos)                                                      
{                                                                                   
    var i = 0;
    var j = 0;                                                                        
    var link = '';
     
    var arrayComponentes = idComponentes.split(' | ');    
    var nomeAtributo = nomeAtributos.split(' | ');
        
    for(j = 0; j < arrayComponentes.length; j++) {
        if(arrayComponentes[j] != '') {

            var selectSelecionados = document.getElementById(arrayComponentes[j]);                                 
            for(i = 0; i < selectSelecionados.length; i++)                                    
            {                                                                                 
               link += '&stAtributo['+ nomeAtributo[j] +'_Selecionados]['+ i +'][valor]='+ selectSelecionados.options[i].value;
               link += '&stAtributo['+ nomeAtributo[j] +'_Selecionados]['+ i +'][texto]='+ selectSelecionados.options[i].text; 
            }

            if(selectSelecionados.length == 0) {
                link += '&stAtributo['+ nomeAtributo[j] +'_Selecionados]['+ i +'][valor]=';
            }

        }
    }
    ajaxJavaScript(linkPagina+link,acao);   
}

function removeConfirmPopUp()
{
    removePopUp();
}

function removePopUp()
{
    if (typeof jq == 'undefined') {
        var jq = window.parent.frames["telaPrincipal"].jQuery;
    }
    jq("input:button").each(function(){
        this.disabled = false;
    });

    jq("input#Ok").removeAttr('readonly');
    for(i=1;i<4;i++){
        jq('div#containerPopUp',parent.frames[i].document).each(function(){
                                                                    jq(this).remove();
                                                               });
            jq('html',parent.frames[i].document).css({'overflow':'auto'});
    }   
}

function confirmPopUp(stTitle,stText,stMethodSim)
{
    removePopUp();
    if (typeof jq == 'undefined') {
       var jq = window.parent.frames["telaPrincipal"].jQuery;
    } 

    stHTMLFrames = '<div id="containerPopUp">&nbsp;</div>';

    stHTML = '    <div id="showPopUp">';
    stHTML = stHTML + '        <h3>'+stTitle+'</h3>';
    stHTML = stHTML + '        <h4>Confirmação</h4>';
    stHTML = stHTML + '        <p>'+stText+'</p>';
    stHTML = stHTML + '        <input type="button" value="Sim" id="btPopUpSim" name="btPopUpSim" onclick="javascript:removeConfirmPopUp();'+stMethodSim+';"; />'; 
    stHTML = stHTML + '        <input type="button" value="Não" id="btPopUpNao" name="btPopUpNao" onclick="removeConfirmPopUp();" />';
    stHTML = stHTML + '    </div>';

    var containerCSS = { 'width':'100%',  
                         'height': '1999px',
                         'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                         'position':'absolute',
                         'left':'0',
                         'top':'0' };
    
    for(i=1;i<4;i++){
        jq('html',parent.frames[i].document).append(stHTMLFrames);
        jq('html',parent.frames[i].document).css({'overflow':'hidden'});
        jq('div#containerPopUp', parent.frames[i].document).css(containerCSS);
    }

    jq('div#containerPopUp',parent.frames[2].document).html(stHTML);
    jq('#btPopUpSim').focus();

}

function alertPopUp(stTitle,stText,stMethod)
{
    removePopUp();
    if (typeof jq == 'undefined') {
       var jq = window.parent.frames["telaPrincipal"].jQuery;
    } 
    stHTMLFrames = '<div id="containerPopUp">&nbsp;</div>';

    stHTML = '    <div id="showPopUp">';
    stHTML = stHTML + '        <h3>'+stTitle+'</h3>';
    stHTML = stHTML + '        <h4 class="alert">Alerta</h4>';
    stHTML = stHTML + '        <p>'+stText+'</p>';
    stHTML = stHTML + '        <input type="button" value="Ok" id="btPopUpOk" name="btPopUpOk" onclick="removeConfirmPopUp();' + stMethod + '" />';
    stHTML = stHTML + '    </div>';

    var containerCSS = { 'width':'100%',  
                         'height': '1999px',
                         'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                         'position':'absolute',
                         'left':'0',
                         'top':'0' };
    
    for(i=1;i<4;i++){
        jq('html',parent.frames[i].document).append(stHTMLFrames);
        jq('html',parent.frames[i].document).css({'overflow':'hidden'});
        jq('div#containerPopUp', parent.frames[i].document).css(containerCSS);
    }

    jq('div#containerPopUp',parent.frames[2].document).html(stHTML);
    jq('#btPopUpOk').focus();

}

function loadingModal(boPrincipal, boMenu, stText)
{
    removePopUp();
    if (typeof jq == 'undefined') {
       var jq = window.parent.frames["telaPrincipal"].jQuery;
    } 
    jq("input:button").each(function(){
        this.disabled = true;
    });
    stHTMLFrames = '<div id="containerPopUp">&nbsp;</div>';

    stHTML = '    <div id="showLoading">';

    if(stText == ''){
        stText = 'Carregando...';
    }

    stHTML = stHTML + '<h5>'+stText+'</h5>';
    stHTML = stHTML + ' <img src = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/loading_modal.gif" id="loadingModal" />';
    stHTML = stHTML + '    </div>';

    var containerCSS = { 'width':'100%',  
                         'height': '1999px',
                         'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                         'position':'fixed',
                         'left':'0',
                         'top':'0' };
   
    //Aplica o modal no frame de mensagens
    jq('html',parent.frames[3].document).append(stHTMLFrames);
    jq('div#containerPopUp', parent.frames[3].document).css(containerCSS);
    jq('html',parent.frames[3].document).css({'overflow':'hidden'});
    //Aplica o modal no frame principal
    if(boPrincipal == true){
        jq('html',parent.frames[2].document).append(stHTMLFrames);
        jq('div#containerPopUp', parent.frames[2].document).css(containerCSS);
        jq('html',parent.frames[2].document).css({'overflow':'hidden'});
    }
    //Aplica o modal no frame do menu
    if(boMenu == true){
        jq('html',parent.frames[1].document).append(stHTMLFrames);
        jq('div#containerPopUp', parent.frames[1].document).css(containerCSS);
        jq('html',parent.frames[1].document).css({'overflow':'hidden'});
    }

    jq('div#containerPopUp',parent.frames[2].document).html(stHTML);
    jq('#btPopUpOk').focus();

}

function imagemPopUp(stTitle,stText, stRotulo, stMethod)
{
        stHTMLFrames = '<div id="containerImagemPopUp"></div>';

        stHTML = '    <div id="showPopUp">';
        stHTML = stHTML + '        <a id="fechar" href="" target="_blank" onclick="removeConfirmImagemPopUp();return false;">Fechar[X]</a>';
        stHTML = stHTML + '        <h3>'+stTitle+'</h3>';
        stHTML = stHTML + '        <p><img src="'+stText+'"></p>';
        if(stRotulo != ''){
        stHTML = stHTML + '        <input type="button" value="'+stRotulo+'" id="btMetodo" name="btMetodo" onclick="'+stMethod+'" />';
        }
        stHTML = stHTML + '    </div>';

        var containerCSS = { 'width':'100%',  
                             'height': '1999px',
                             'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                             'position':'absolute',
                             'left':'0',
                             'top':'0' };
    
        for(i=1;i<4;i++){
            jq('html',parent.frames[i].document).append(stHTMLFrames);
            jq('html',parent.frames[i].document).css({'overflow':'hidden'});
            jq('div#containerImagemPopUp', parent.frames[i].document).css(containerCSS);
        }

        jq('div#containerImagemPopUp',parent.frames[2].document).html(stHTML);
        jq('#btPopUpOk').focus();
}

function removeConfirmImagemPopUp()
{
    for(i=1;i<4;i++){
        jq('div#containerImagemPopUp',parent.frames[i].document).remove();
            jq('html',parent.frames[i].document).css({'overflow':'auto'});
    }   
}

/***********************************************************
* Funções utilizadas pelo seletorAno (Componente do GRH).
***********************************************************/
function increaseSelectorYear(inIdElemento) {                                            
   jQuery('#'+inIdElemento).val(parseInt(jQuery('#'+inIdElemento).val())+1);                
}                                                                                    

function decreaseSelectorYear(inIdElemento, inAnoInicioPerMov) {                                               
    if (parseInt(jQuery('#'+inIdElemento).val()) > inAnoInicioPerMov) {
        jQuery('#'+inIdElemento).val(parseInt(jQuery('#'+inIdElemento).val())-1);                
    }
}                                                                                    

function checkSelectorDate(obData, inAnoInicioPerMov) {        
    if (obData.value.length < 4) {                                                   
        var obDate = new Date();                                                     
        obData.value = obDate.getFullYear();                                         
    } else {
        if (obData.value < inAnoInicioPerMov) {
           obData.value = inAnoInicioPerMov;     
        }    
    } 
}                                                                                     
