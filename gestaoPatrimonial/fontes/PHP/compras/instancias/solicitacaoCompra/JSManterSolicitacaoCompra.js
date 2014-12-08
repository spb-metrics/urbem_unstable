<script type="text/javascript">
/*
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
</script>
<?php
/**
 * Arquivo JavaScript utilizado na Consulta de Inclusão de Solicitação 
 * Data de Criação   : 11/09/2006


 * @author Analista      : Cleisson Barboza 
 * @author Desenvolvedor : Rodrigo 

 * @ignore
 
 * Casos de uso: uc-03.04.01
  
 $Id: JSManterSolicitacaoCompra.js 59612 2014-09-02 12:00:51Z gelson $ 

 */
?>
<script type="text/javascript">

   function comparaValor(obj){
      var objetoHdn = new Object();
      objetoHdn = eval("document.frm."+obj.name.replace("nu","nuHdn"));
      if (objetoHdn.value == obj.value){
         if (obj.name.indexOf("nuQt")>=0){
            var valor    = new Object();
            var valorHdn = new Object();
            valor    = eval("document.frm."+obj.name.replace("nuQt","nuVl"));
            valorHdn = eval("document.frm."+valor.name.replace("nuVl","nuHdnVl"));
            valor.value = valorHdn.value;
         } else if(obj.name.indexOf("nuVl")>=0){
            var quantidade    = new Object();
            var quantidadeHdn = new Object();
            quantidade    = eval("document.frm."+obj.name.replace("nuVl","nuQt"));
            quantidadeHdn = eval("document.frm."+quantidade.name.replace("nuQt","nuHdnQt"));
            quantidade.value = quantidadeHdn.value;
         }
      }
   }

   function calculaTotal(obj){
      var qtdAnular   = new Object();
      var cmp         = obj.name.split('_');
      qtdAnular.value = obj.value;
      var qtdPendente = eval("document.forms[0].nuHdnQtTotalAnulada_"+cmp[1]);
      var vlrPendente = eval("document.forms[0].nuHdnVlTotalAnulada_"+cmp[1]);
      var vlrAnulada  = eval("document.forms[0].nuVlTotalAnulada_"+cmp[1]);
  
      var calculo = parseFloat(vlrPendente.value)/parseFloat(qtdPendente.value);
      calculo = parseToMoeda(calculo * parseFloat(qtdAnular.value));
      vlrAnulada.value = parseToMoeda(parseFloat(calculo));
   }

   function anularSolicitacao(inIndice){
      var erro     = new Boolean(false);
      var campo    = new String();
      var mensagem = "";
      var cmp      = new Number(0);
      var qtAnula  = new Number(0);
      var vlAnula  = new Number(0);
      campo  = document.frm.stMotivo.value.length;
      vcampo = document.frm.stMotivo.value;

      if(campo == 0 | vcampo == '0'){
        erro     = true;
        mensagem = "@O campo Motivo é obrigatório";
      }

      if(erro==true){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
      }else{
         document.frm.target = 'oculto';
         document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>&stCtrl=anular';
         document.frm.submit();
      }
   }

   function saldoCentroItem(vlr){
      var caminho = new String();
      var link    = '<?=CAM_GP_ALM_PROCESSAMENTO?>OCCentroCustoUsuario.php?<?=Sessao::getId()?>&usuario=1';
      caminho=link+'&nomCampoUnidade=inCodCentroCusto&stNomCampoCod=inCodCentroCusto&stIdCampoDesc=stNomCentroCusto&stNomForm=frm';
      if (document.frm.inCodItem.value != ""){
        ajaxJavaScript(caminho+'&inCodigo='+vlr+'&inCodItem='+document.frm.inCodItem.value,'buscaPopup');
        return true;
      } else {
        jQuery('#inCodCentroCusto').val('');
        alertaAviso("@O campo Item é obrigatório para o saldo em estoque.",'form','erro','<?=Sessao::getId()?>');
        return false;
      }
   }
  
   function excluirListaItens(inIndice){
      ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'excluirListaItens');
   }
  
   function alterarListaItens(inIndice){
      ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice+'&alterar=true','montaDotacao');
      ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice+'&alterar=true','alterarListaItens');
   }
  
   function LimparOutraSolicitacao(){
      document.frm.stExercicioSolicitacao.value                   = "<?=Sessao::getExercicio()?>";
      document.frm.inCodEntidadeSolicitacao.value                 = "";
      document.frm.inCodSolicitacao.value                         = "";
      document.frm.stNomEntidadeSolicitacao.options.selectedIndex = 0;
      document.getElementById('stNomSolicitacao').innerHTML = '&nbsp;';
   }
  
   function LimparItensSolicitacao(){
      document.frm.inCodItem.value                         = "";
      if( document.frm.inCodUnidadeMedida ){
           document.frm.inCodUnidadeMedida.value                = "";
      }
      document.frm.inCodCentroCusto.value                  = "";
      //  document.frm.inCodDespesa.value                      = "";
      document.frm.HdninCodCentroCusto.value               = "";
      //document.frm.HdninCodDespesa.value                   = "";
      document.frm.nuVlUnitario.value                      = "";
      document.frm.nuQuantidade.value                      = "";
      document.frm.nuVlTotal.value                         = "";
      if (document.frm.nuVlTotalReservado){
           document.frm.nuVlTotalReservado.value                = "0,00";
      }
      if( document.frm.stNomUnidade ){
           document.frm.stNomUnidade.value                      = "";
      }
      document.frm.stNomCentroCusto.value                  = "";
      document.frm.stComplemento.value                     = "";
      //document.frm.stNomDespesa.value                      = "";
      document.getElementById("stNomItem").innerHTML       = "&nbsp;";
      if( document.getElementById("stUnidadeMedida") ){
           document.getElementById("stUnidadeMedida").innerHTML = "&nbsp;";
      }
      //document.getElementById("stNomDespesa").innerHTML    = "&nbsp;";
      document.getElementById("stNomCentroCusto").innerHTML= "&nbsp;";
      document.getElementById("lblSaldoEstoque").innerHTML = "&nbsp;";
      document.getElementById("spnVlrReferencia").innerHTML = "&nbsp;";
      //document.getElementById("spnSaldoDotacao").innerHTML = "&nbsp;";
      //document.getElementById("lblSaldoEstoque").innerHTML = "&nbsp;";
      document.getElementById("spnDotacao").innerHTML = "";
      document.getElementById('incluiItem').value          ='Incluir';
      document.getElementById('incluiItem').setAttribute('onclick','JavaScript:goOculto(\'incluirListaItens\',true,\'false\');');	
      document.frm.inCodItem.focus();
   }
  
   function ajaxJavaScriptPost(stControle, alterar){
      var url = 'OCManterSolicitacaoCompra.php?<?=Sessao::getId();?>&alterar='+alterar;
      var pars = Form.serialize('frm')+'&stCtrl='+stControle;
      var myAjax = new Ajax.Request( url, { method: 'post', parameters: pars, onComplete:executaResposta });
      void(null);
   }
  
   function executaResposta(requisicaoOriginal){
      eval( requisicaoOriginal.responseText );
   }
  
   function goOcultoOutraSolicitacao(stControle,verifica){
  
     document.frm.stCtrl.value = stControle;
  
     var erro     = false;
     var mensagem = "";
  
     campo  = document.frm.stExercicioSolicitacao.value.length;
     vcampo = document.frm.stExercicioSolicitacao.value;
  
     if(campo == 0 | vcampo == '0'){
      mensagem += "@O campo exercício é obrigatório";
      erro = true;
     }
  
     campo  = document.frm.inCodEntidadeSolicitacao.value.length;
     vcampo = document.frm.inCodEntidadeSolicitacao.value;
  
     if(campo == 0 | vcampo == '0'){
      mensagem += "@O campo entidade é obrigatório";
      erro = true;
     }
  
     campo  = document.frm.inCodSolicitacao.value.length;
     vcampo = document.frm.inCodSolicitacao.value;
  
     if(campo == 0 | vcampo == '0'){
      mensagem += "@O campo solicitação é obrigatório";
      erro = true;
     }
  
     if(erro==true){
       alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
     }else{
       var campo = new String();
       var cmp   = new Number(0);
         while(cmp in document.forms[0].elements){       		
              if(cmp !=14) {	
                  campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
              }	
           cmp++;
         }
       ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>'+campo,document.frm.stCtrl.value);
       document.frm.action                         = '<?=$pgProc;?>?<?=Sessao::getId();?>';
       document.frm.stCtrl.value                   = "";
       LimparOutraSolicitacao();
     }
   }
  
   function goOculto(stControle,verifica,alterar){
  
      if(document.frm.stCtrl.value=="" | document.frm.stCtrl.value=="buscaDespesaDiverso"){
         document.frm.stCtrl.value = stControle;
      }
  
      if(document.frm.stCtrl.value=="alteradoListaItens"){
         document.frm.stCtrl.value = "alteradoListaItens";
      }
  
      document.frm.HdnNomItem.value        = document.getElementById("stNomItem").innerHTML;
      document.frm.HdnNomUnidade.value     = document.getElementById("stUnidadeMedida").innerHTML;
      document.frm.HdnNomCentroCusto.value = document.getElementById("stNomCentroCusto").innerHTML;
  
      var erro     = false;
      var mensagem = "";
  
      campo  = document.frm.HdnCodEntidade.value.length;
      vcampo = document.frm.HdnCodEntidade.value;
     
      if(campo == 0 | vcampo == '0'){
         mensagem += "@O campo Entidade é obrigatório";
         erro = true;
      }
     
      campo  = document.frm.inCodItem.value.length;
      vcampo = document.frm.inCodItem.value;
  
      if(campo == 0 | vcampo == '0'){
         mensagem += "@O campo Código do Item é obrigatório";
         erro = true;
      }
  
      campo  = document.frm.inCodCentroCusto.value.length;
      vcampo = document.frm.inCodCentroCusto.value;
  
      if(campo == 0 | vcampo == '0'){
         mensagem += "@O campo Código do Centro do Custo é obrigatório";
         erro = true;
      }
  
      campo  = document.frm.nuQuantidade.value.length;
      vcampo = document.frm.nuQuantidade.value;
  
      if(campo == 0 | vcampo == '0.0000'){
         mensagem += "@O campo Quantidade é obrigatório";
         erro = true;
      }
  
      campo  = document.frm.nuVlTotal.value.length;
      vcampo = document.frm.nuVlTotal.value;
  
      if(campo == 0 | vcampo == '0.00'){
         mensagem += "@O campo Valor total é obrigatório";
         erro = true;
      }
  
     var boConfiguracao = new Boolean((document.frm.boConfiguracao.value=="true")?1:0);
      if(boConfiguracao==true){
         if (document.getElementById('inCodEntidade').value != ''){
            vcampo = document.frm.inCodDespesa.value;
            if(vcampo==""){
               mensagem += "@O campo Dotação Orçamentária é obrigatório";
               erro = true;
            }
         } else {
            mensagem += "@O campo Entidade é obrigatório";
            erro = true;
         }
      }
  
      if(erro & verifica){
         alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
      }else{
         var campo = new String();
         var cmp   = new Number(0);
         while(cmp in document.forms[0].elements){
           campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
           cmp++;
         }

         ajaxJavaScriptPost(stControle, alterar);
         //<?=$pgOcul."?".Sessao::getId()?>'+campo,document.frm.stCtrl.value+'&alterar='+alterar);
         // document.frm.action       = '<?=$pgProc;?>?<?=Sessao::getId();?>';
      }
   }
  
  function limpaCamposDotacao(){
      document.getElementById("stNomDespesa").innerHTML = "&nbsp;";
      if (document.frm.stCodClassificacao){
         document.getElementById("stCodClassificacao").innerHTML = '<option value="" selected="selected">Selecione</option>';
      }
      document.getElementById("inCodDespesa").value = "";
      document.getElementById("nuSaldoDotacao").innerHTML = "&nbsp;";
   }
  
   function preencheValorReservado (valor){
      var x = new Number(0);
      while ( x < document.frm.elements.length ){
         if ( document.frm.elements[x].type == "text" ){
            if ( document.frm.elements[x].name=="nuVlTotalReservado" ){
                document.frm.elements[x].value = valor;
                break;
            }
         }
         x++;
      }
   }
  
   function setFocusEntidade(){
      comboEntidade = document.getElementById('stNomEntidade');
      textEntidade = document.getElementById('inCodEntidade');
      comboEntidade.disabled = false;        
      textEntidade.disabled = false;        
   }

</script>
