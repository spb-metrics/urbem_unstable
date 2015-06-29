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
<?
/**
    * Arquivo JS utilizado na Duplicação de Autorização
    * Data de Criação   : 01/12/2004


    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $
    
    * Casos de uso: uc-02.03.02
                    uc-02.01.08 
*/
?>

<script type="text/javascript">

function buscaFornecedor(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = 'OCManterAutorizacao.php?<?=Sessao::getId();?>&stCtrl=buscaFornecedor';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function alterar( inCodOrgao ){
    document.frm.stCtrl.value = 'alterar';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodOrgao='+inCodOrgao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function incluirItem() {
    var mensagem = "";
    var nuQuantidade = document.frm.nuQuantidade.value;
    var nuUnitario   = document.frm.nuVlUnitario.value;
    var nuTotal      = document.frm.nuVlTotal.value;
    
    if (document.frm.stTipoItem.value=='Catalogo') {
        if(!document.frm.inCodItem.value){
        mensagem += '@Campo Item inválido!()';
        }
    }
    
    if (document.frm.stTipoItem.value=='Descricao') {
        if(!document.frm.stNomItem.value){
        mensagem += '@Campo Descrição do Item inválido!()';
        }
    } 
    if(!document.frm.nuQuantidade.value)
        mensagem += '@Campo Quantidade inválido!()';
    
    nuQuantidade = nuQuantidade.replace( new  RegExp("[.]","g") ,'');
    nuQuantidade = nuQuantidade.replace( "," ,'.');
    if( nuQuantidade <= 0 )
        mensagem += "@Campo Quantidade com valor inválido!( o valor deve ser maior que 0 (zero) )";
    

    if(!document.frm.inCodUnidade.value)
        mensagem += '@Campo Unidade inválido!()';
    if(!document.frm.nuVlUnitario.value)
        mensagem += '@Campo Valor Unitário inválido!()';

    nuUnitario = nuUnitario.replace( new  RegExp("[.]","g") ,'');
    nuUnitario = nuUnitario.replace( "," ,'.');
    if( nuUnitario <= 0 )
        mensagem += "@Campo Valor Unitário com valor inválido!(o valor deve ser maior que 0 (zero) )";
    
    if(!document.frm.nuVlTotal.value)
        mensagem += '@Campo Valor Total inválido!()';

    nuTotal = nuTotal.replace( new  RegExp("[.]","g") ,'');
    nuTotal = nuTotal.replace( "," ,'.');
    if( nuTotal <= 0 )
        mensagem += "@Campo Valor Total com valor inválido!(o valor deve ser maior que 0 (zero) )";

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.getElementById('stTipoItemRadio1').setAttribute('disabled',true);
        document.getElementById('stTipoItemRadio2').setAttribute('disabled',true);
        document.frm.Ok.disabled = true;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluiItemPreEmpenho';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limparItem();
    }
}

function alterarEmpenho(stControle,inIndice)
{
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getExercicio()?>&num_item='+inIndice,stControle);
}

function alterarItem() 
{
    var mensagem = "";
    var nuQuantidade = document.frm.nuQuantidade.value;
    var nuUnitario   = document.frm.nuVlUnitario.value;

    if (document.frm.stTipoItem.value=='Catalogo') {
        if(!document.frm.inCodItem.value){
        mensagem += '@Campo Item inválido!()';
        }
    }
    
    if (document.frm.stTipoItem.value=='Descricao') {
        if(!document.frm.stNomItem.value){
        mensagem += '@Campo Descrição do Item inválido!()';
        }
    }
    if(!document.frm.nuQuantidade.value)
        mensagem += '@Campo Quantidade inválido!()';

    nuQuantidade = nuQuantidade.replace( new  RegExp("[.]","g") ,'');
    nuQuantidade = nuQuantidade.replace( "," ,'.');
    if( nuQuantidade == 0 )
        mensagem += "@Campo Quantidade com valor inválido!( o valor deve ser maior que 0 (zero) )";


    if(!document.frm.inCodUnidade.value)
        mensagem += '@Campo Unidade inválido!()';
    if(!document.frm.nuVlUnitario.value)
        mensagem += '@Campo Valor Unitário inválido!()';

    nuUnitario = nuUnitario.replace( new  RegExp("[.]","g") ,'');
    nuUnitario = nuUnitario.replace( "," ,'.');
    if( nuUnitario == 0 )
        mensagem += "@Campo Valor Unitário com valor inválido!(o valor deve ser maior que 0 (zero) )";

    if(!document.frm.nuVlTotal.value)
        mensagem += '@Campo Valor Total inválido!()';

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.Ok.disabled = true;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'alteradoItemPreEmpenho';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        document.frm.btnIncluir.value='Incluir';
        document.frm.stCtrl.value = 'incluiItemPreEmpenho';
        limparItem();
    }
}

function excluirItem(stControle, inNumItem ){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumItem=' + inNumItem;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    
    limparItem();
}

function limparItem() {
    if (document.frm.inCodMaterial) {
        document.frm.inCodMaterial.value = '';
        document.getElementById("stNomMaterial").innerHTML = '&nbsp;';
    }
   
    document.frm.stNomItem.value = '';
    document.frm.stNomItem.readOnly = false;
    document.frm.stComplemento.value = '';
    document.frm.nuQuantidade.value = '';
    document.frm.inCodUnidade.value = document.frm.inCodUnidadePadrao.value;
    document.frm.nuVlUnitario.value = '';
    document.frm.nuVlTotal.value = '';
    document.frm.inCodUnidadeMedida.value = '';
    document.frm.stNomUnidade.value = '';
    document.frm.inCodItem.value = '';
    document.getElementById('stNomItemCatalogo').innerHTML = '';
    document.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';
}

function gerarValorTotal() {
    var nuVlUnidade = document.frm.nuVlUnitario.value;
    var nuQuantidade = document.frm.nuQuantidade.value;
    var nuVlTotal = "";
    if( nuVlUnidade && nuQuantidade ) {
        nuVlUnidade = nuVlUnidade.replace( new  RegExp("[.]","g") ,'');
        nuVlUnidade = nuVlUnidade.replace( "," ,'.');
        nuQuantidade = nuQuantidade.replace( new  RegExp("[.]","g") ,'');
        nuQuantidade = nuQuantidade.replace( "," ,'.');
        nuVlTotal = nuVlUnidade * nuQuantidade;
        nuVlTotal = Math.round(nuVlTotal*Math.pow(10,2))/Math.pow(10,2);
        nuVlTotal = new String(nuVlTotal);
        arVlTotal = nuVlTotal.split(".") ;
        if( !arVlTotal[1] )
            arVlTotal[1] = '00';
        var inCount = 0;
        var inValor = "";
        for( var i = (arVlTotal[0].length-1); i >= 0; i-- ) {
            if( inCount == 3 ) {
               inValor = '.' + inValor;
               inCount = 0;
            }
            inValor = arVlTotal[0].charAt(i) + inValor;
            inCount++;
        }
        nuVlTotal = inValor + ',' + arVlTotal[1];
        document.frm.nuVlTotal.value = nuVlTotal;
    }
}

function limparCampos() {
    var f = document.frm;
    f.inCodDespesa.value = "";
    f.inCodDespesa.disabled=true;
    limpaSelect(f.stCodClassificacao,0);
    f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');
    document.getElementById("stNomDespesa").innerHTML = "&nbsp;";
    f.inCodDespesa.disabled=false;
}

function limparItens() {
    BloqueiaFrames(true,false);
    document.frm.stCtrl.value = 'limpar';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    LiberaFrames( true, false );
}

function limparTodos() {
    limparCampos();
    limparItens();
    document.getElementById('stTipoItemRadio1').disabled = false;
    document.getElementById('stTipoItemRadio2').disabled = false;
    var d = document;
    d.getElementById( "stNomFornecedor" ).innerHTML = "&nbsp;";
    d.getElementById( "spnSaldoDotacao" ).innerHTML = "";
    d.getElementById( "spnLista"        ).innerHTML = "";    
    d.getElementById( "nuValorTotal" ).innerHTML = "";
    if( d.getElementById( "nuVlReserva" ) ) d.getElementById( "nuVlReserva" ).innerHTML = "";
    if( d.frm.inCodUnidadeOrcamento ) {
        limpaSelect(d.frm.inCodUnidadeOrcamento,0);
        d.frm.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');
    }
    if( d.frm.inCodUnidadeOrgao ) { 
        limpaSelect(d.frm.inCodUnidadeOrgao,0);
        d.frm.inCodUnidadeOrgao.options[0] = new Option('Selecione','', 'selected');
    }
    if( d.frm.inCodOrgao ) {
        limpaSelect(d.frm.inCodOrgao,0);
        d.frm.inCodOrgao.options[0] = new Option('Selecione','', 'selected');
    }
    document.frm.inCodEntidade.focus();
}

function proximoFoco(valor) {
    if ( valor.length == 0 ) {
        document.frm.Ok.focus();
    }
}

function buscaDtAutorizacao( valor ) {
    if( valor != "") {
        limparCampos();
        BloqueiaFrames(true,false);
        buscaDado('buscaDtAutorizacao');
    }
}
function unidadeItem(ent){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = "unidadeItem";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&codItem='+ent;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}
function habilitaCampos(ent) {
    var f = document.frm;
    
    //habilita campos para Catalogo
    if( ent == 'Catalogo' ){
        jQuery("#stTipoItem").val(ent);
        jQuery("#stNomItem").prop("disabled",true);
        jQuery("#stNomItem").val("");
        jQuery("#inCodItem").prop("disabled",false);
        jQuery("#inCodItem").val("");
        jQuery("#stNomItemCatalogo").prop("disabled",false);
        jQuery("#stNomItemCatalogo").html("&nbsp;");
        jQuery("#imgBuscar").prop("hidden", false);
        jQuery("#stUnidadeMedida").html("");
        jQuery("#inCodUnidade").prop("disabled", true);
    }

    //habilita campos para Descricao
    if( ent == 'Descricao' ){
        jQuery("#stTipoItem").val(ent);
        jQuery("#stNomItem").prop("disabled",false);
        jQuery("#inCodItem").prop("disabled",true);
        jQuery("#inCodItem").val("");
        jQuery("#stNomItemCatalogo").attr("disabled", true);
        jQuery("#stNomItemCatalogo").html("&nbsp;");
        jQuery("#imgBuscar").prop("hidden", true);
        jQuery("#stUnidadeMedida").html("");
        jQuery("#inCodUnidade").prop("disabled", false);
    }
}
function bloqueiaTipoItem(ent){
    if (ent==''||ent==null){
        var ent2=document.getElementById('stTipoItem').value;
    }else{
        var ent2=ent;
    }
    habilitaCampos(ent2);
    
    jq('#stTipoItemRadio1').attr('disabled',true);
    jq('#stTipoItemRadio2').attr('disabled',true);
    
    if (ent=='Catalogo') {
        jq('#stTipoItemRadio1').attr('checked',true);
        jq('#stTipoItemRadio2').attr('checked',false);
    }
    
    if (ent=='Descricao') {
        jq('#stTipoItemRadio1').attr('checked',false);
        jq('#stTipoItemRadio2').attr('checked',true);
    }
}
</script>
                
