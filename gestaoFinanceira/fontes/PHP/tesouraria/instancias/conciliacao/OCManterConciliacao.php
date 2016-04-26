<?php
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
?>
<?php
 /**
    * Paginae Oculta para funcionalidade Conciliacao Bancária
    * Data de Criação   : 07/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage tesouraria
    * @ignore conciliacao

    * $Id: OCManterConciliacao.php 65087 2016-04-22 14:27:07Z carlos.silva $

    * Casos de uso: uc-02.04.19

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";
include_once CAM_FW_COMPONENTES."/Table/TableTree.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO."/TTCMBATipoConciliacaoLancamentoContabil.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function montaLista($arRecordSet , $boExecuta = true)
{
    $inCount = 0;
    $arLista = array();
    $arRecordSet = ( is_array( $arRecordSet ) ) ? $arRecordSet : array();
    foreach ($arRecordSet as $key => $array) {
        foreach ($array as $stCampo => $stValor) {
            $arLista[$inCount][$stCampo] = $stValor;
        }
        if ( $array['tipo'] == 'P' and trim($array['observacao']) ) {
            if( !strstr( $array['observacao'], "Borderô" ) )
                $arLista[$inCount]['descricao'] = $array['descricao']." - ".$array['observacao'];
        }
        $inCount++;
    }
    
    $rsLista = new RecordSet;
    $rsLista->preenche( $arLista );

    $rsLista->addFormatacao("vl_lancamento", "NUMERIC_BR");
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false);
    $obLista->setMostraSelecionaTodos( true );
    $obLista->setTitulo("Movimentação");
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data Conciliação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conciliar");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    if(SistemaLegado::isTCMBA($boTransacao)) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Tipo de Conciliação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    }
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_lancamento" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[vl_lancamento]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_conciliacao" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    
    $obChkConciliar = new CheckBox;
    $obChkConciliar->setName ( "boConciliar_[id]_");
    $obChkConciliar->setClass( "boConciliar" );
    $obChkConciliar->setValue( "true" );
    $obChkConciliar->obEvento->setOnChange( "ajustaSaldo(this.name);ajaxJavaScript('OCManterConciliacao.php?id='+this.id+'&conciliar='+this.checked,'conciliarMovimentacao');" );
    
    $obLista->addDadoComponente( $obChkConciliar );
    $obLista->ultimoDado->setCampo( "[conciliar]" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDadoComponente();
    
    if(SistemaLegado::isTCMBA($boTransacao)) {
        $obTTCMBATipoConciliacaoLancamentoContabil = new TTCMBATipoConciliacaoLancamentoContabil();
        $obTTCMBATipoConciliacaoLancamentoContabil->recuperaTodos( $rsTipoConciliacaoLancamentoContabil, ' ORDER BY cod_tipo_conciliacao ' );
        
        $obSelectTipoConciliacao = new Select();
        $obSelectTipoConciliacao->setName   ( "idTipoConciliacao_[id]_" );
        $obSelectTipoConciliacao->addOption ( "","Selecione" );
        $obSelectTipoConciliacao->setCampoID( "cod_tipo_conciliacao" );
        $obSelectTipoConciliacao->setCampoDesc( "descricao" );
        $obSelectTipoConciliacao->preencheCombo( $rsTipoConciliacaoLancamentoContabil );
        $obSelectTipoConciliacao->setStyle  ( "width:180px;" );
        $obSelectTipoConciliacao->setValue  ( "[cod_tipo_conciliacao]" );
    
        $obLista->addDadoComponente( $obSelectTipoConciliacao );
        $obLista->ultimoDado->setCampo( "cod_tipo_conciliacao" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDadoComponente();
    }
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo("<div id=HboConciliar_[id] style='display: none' >[vl_lancamento]</div>");
    $obLista->commitDadoComponente();

    $obLista->montaInnerHTML();
    $stHTML = $obLista->getHTML();

    if ($boExecuta) {
        echo "d.getElementById('spnMovimentacao').innerHTML = '".$stHTML."';";
    } else {
        return str_replace("'", "&quot;", $stHTML);
    }
}

function montaListaPendencia($boExecuta = true,$arRequest)
{
    $arCabecalhos = array(
                            array(  'descricao' => 'Entradas não consideradas pelo banco'
                                  , 'chave'     => 'entradaBanco')
                          , array(  'descricao' => 'Entradas não consideradas pela tesouraria'
                                  , 'chave'     => 'entradaTesouraria')
                          , array(  'descricao' => 'Saídas não consideradas pelo banco'
                                  , 'chave'     => 'saidaBanco')
                          , array(  'descricao' => 'Saídas não consideradas pela tesouraria'
                                  , 'chave'     => 'saidaTesouraria')
                         );

    $rsCabecalho = new RecordSet;
    $rsCabecalho->preenche($arCabecalhos);

    $obTableTree = new TableTree;
    $obTableTree->setArquivo('OCManterConciliacao.php');
    $obTableTree->setParametros( array("chave") );
    $obTableTree->setComplementoParametros( 'stCtrl=detalharLista&stExercicio='.$arRequest['stExercicio'].'&inMes='.$arRequest['inMes']);
    $obTableTree->setRecordset($rsCabecalho);
    $obTableTree->setSummary('Movimentação Pendente');
   // $obTableTree->setConditional(true);

    $obTableTree->Body->addCampo( 'descricao', 'E' );
    $obTableTree->montaHTML(true);
    $stHTML = $obTableTree->getHtml();

    if ($boExecuta) {
        echo "jq('#spnMovimentacaoPendente').innerHTML = '".$stHTML."';";
    } else {
        return $stHTML;
    }

}

function montaListaPendenciaDetalhada($stChave,$arRequest)
{
    $arMovimentacaoPendencia = Sessao::read('arMovimentacaoPendenciaListagem');
    $arPendenciasMarcadas = Sessao::read('arPendenciasMarcadas');
    $arLista = $arMovimentacaoPendencia[$stChave];

    if (count($arLista) > 0) {

        $rsLista = new RecordSet;
        $rsLista->preenche($arLista);
        $rsLista->addFormatacao("vl_lancamento", "NUMERIC_BR");

        $obChkConciliar = new CheckBox;
        $obChkConciliar->setName("boPendencia_[tipo]-[sequencia]_[linha]");
        $obChkConciliar->setId("boPendencia_[tipo]-[sequencia]_[linha]");
        $obChkConciliar->obEvento->setOnChange( "ajustaSaldoPendente(this.name);" );

        $obTable = new Table;
        $obTable->setRecordset($rsLista);
        //$obTable->setConditional(true);
        $obTable->addLineNumber(false);

        $obTable->Head->addCabecalho('Data', 10);
        $obTable->Head->addCabecalho('Descrição', 50);
        $obTable->Head->addCabecalho('Valor', 15);
        $obTable->Head->addCabecalho('Data Conciliação', 10);
        $obTable->Head->addCabecalho('Conciliar', 5);

        $obTable->Body->addCampo('[dt_lancamento]', 'C');
        $obTable->Body->addCampo('[descricao]', 'E');
        $obTable->Body->addCampo("<div id=\"HboPendencia_[tipo]-[sequencia]_[linha]\">[vl_lancamento]</div>", 'D');
        $obTable->Body->addCampo('[dt_conciliacao]', 'C');
        $obTable->Body->addCampo($obChkConciliar, 'D');

        $obTable->montaHTML(true);
        $stHTML = $obTable->getHtml();

        $stHTML .= "<script>\n jq('.tabela input[type=checkbox]').click( function () { ajaxJavaScript('OCManterConciliacao.php?', 'conciliarPendente', '&'+this.id+'='+this.checked); });";

        $arElementos = $rsLista->getElementos();

        foreach ($arElementos as $inChave => $arDados) {
            $stIdentificador = "boPendencia_".$arDados['tipo']."-".$arDados['sequencia']."_".$arDados['linha'];
            if (($arDados['conciliar'] == 'true') AND (substr(implode('',array_reverse(explode('/',$arDados['dt_conciliacao']))),0,6) != $arRequest['stExercicio'].$arRequest['inMes'])) {
                $stHTML .= "jq('#".$stIdentificador."').attr('disabled',true);";
            }
            if (isset($arPendenciasMarcadas['boPendencia_'.$arDados['tipo']."-".$arDados['sequencia']."_".$arDados['linha']])) {
                $stHTML .= "\n jq('#".$stIdentificador."').attr('checked','checked');";
            }

        }
        $stHTML .= "</script>";
    } else {
        $stHTML = "Nenhum registro encontrado.";
    }

    return $stHTML;
}

function montaListaManual($arRecordSet , $boExecuta = true)
{
    $inCount = 0;
    $arLista = array();
    $arRecordSet = ( is_array( $arRecordSet ) ) ? $arRecordSet : array();
    foreach ($arRecordSet as $key => $array) {
        foreach ($array as $stCampo => $stValor) {
            $arLista[$inCount][$stCampo] = $stValor;
        }
        if ( $array['tipo'] == 'P' and trim($array['observacao']) ) {
            if( !strstr( $array['observacao'], "Borderô" ) )
                $arLista[$inCount]['descricao'] = $array['descricao']."<br>".$array['observacao'];
        }
        $inCount++;
    }
    $rsLista = new RecordSet;
    $rsLista->preenche( $arLista );
    $rsLista->addFormatacao("vl_lancamento", "NUMERIC_BR");
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false);
    $obLista->setTitulo("Movimentação Pendente");
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data ");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 50 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data Conciliação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conciliar");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_lancamento" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo("<div id='HboManual_[id]'>[vl_lancamento]</div>");
    $obLista->commitDadoComponente();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo("dt_conciliacao");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDadoComponente();

    $obChkConciliar = new CheckBox;
    $obChkConciliar->setName ( "boManual_[id]_");
    $obChkConciliar->setValue( "true" );
    $obChkConciliar->obEvento->setOnChange("ajustaSaldo(this.name);ajaxJavaScript('OCManterConciliacao.php?id='+this.id+'&conciliar='+this.checked,'conciliarMovimentacaoManual');");

    $obLista->addDadoComponente( $obChkConciliar );
    $obLista->ultimoDado->setCampo( "[conciliar]" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDadoComponente();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'carregarConciliacao' );" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'excluirConciliacao' );" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto("d.frm.btIncluir.disabled = false;d.frm.btAlterar.disabled = true;d.getElementById('spnMovimentacaoManual').innerHTML = '".$stHTML."';");
    } else {
        return $stHTML;
    }
}

function carregarConciliacao($inIdCarregar)
{
    $inIdCarregar       = str_replace("M","", $inIdCarregar);
    $arMovimentacaoManual = Sessao::read('arMovimentacaoManual');
    $inCodPlano         = $arMovimentacaoManual[$inIdCarregar]['cod_plano'];
    $inExercicio        = $arMovimentacaoManual[$inIdCarregar]['exercicio'];
    $stDtLancamento     = $arMovimentacaoManual[$inIdCarregar]['dt_lancamento'];
    $stTipoValor        = $arMovimentacaoManual[$inIdCarregar]['tipo_valor'];
    $nuValorLancamento  = $arMovimentacaoManual[$inIdCarregar]['vl_lancamento' ];
    $stDescricao        = $arMovimentacaoManual[$inIdCarregar]['descricao' ];
    $boConciliar        = $arMovimentacaoManual[$inIdCarregar]['conciliar' ];
    $inCodLote          = $arMovimentacaoManual[$inIdCarregar]['cod_lote' ];
    $stTipo             = $arMovimentacaoManual[$inIdCarregar]['tipo' ];
    $inCodEntidade      = $arMovimentacaoManual[$inIdCarregar]['cod_entidade' ];
    $stTipoMovimentacao = $arMovimentacaoManual[$inIdCarregar]['tipo_movimentacao' ];
    $stId               = $arMovimentacaoManual[$inIdCarregar]['id'];
    $nuValorLancamento  = number_format($nuValorLancamento, 2, ',', '.');
    if ($nuValorLancamento < 0) {
        $stJs .= "document.getElementById('typeD').checked = true;";
    } else {
        $stJs .= "document.getElementById('typeC').checked = true;";
    }
    $nuValorLancamento = str_replace("-","", $nuValorLancamento);

    $stJs .= "document.frm.stDtMovimentacao.value = '".$stDtLancamento."';";
    $stJs .= "document.frm.nuValor.value = '".$nuValorLancamento."';";
    $stJs .= "document.frm.stDescricao.value = '".$stDescricao."';";
    $stJs .= "document.frm.btIncluir.disabled = true;";
    $stJs .= "document.frm.btAlterar.disabled = false;";
    $stJs .= "document.frm.nuValor.focus(true);";

    echo $stJs;

}

function excluirConciliacao($inIdExcluir)
{
    $arConciliacoes = array();
    $inId = 0;
    $arConciliacao = Sessao::read('arMovimentacaoManual');

    for ($i = 0; $i < count($arConciliacao); $i++) {
        if ($arConciliacao[$i]['id'] != $inIdExcluir) {
            $arConciliacoes[$inId]['cod_plano']           = $arConciliacao[$i]['cod_plano'];
            $arConciliacoes[$inId]['exercicio']           = $arConciliacao[$i]['exercicio'];
            $arConciliacoes[$inId]['dt_lancamento']       = $arConciliacao[$i]['dt_lancamento'];
            $arConciliacoes[$inId]['vl_lancamento' ]      = $arConciliacao[$i]['vl_lancamento'];
            $arConciliacoes[$inId]['tipo_valor']          = $arConciliacao[$i]['tipo_valor'];
            $arConciliacoes[$inId]['descricao' ]          = $arConciliacao[$i]['descricao'];
            $arConciliacoes[$inId]['id']                  = "M".$inId;
            $arConciliacoes[$inId]['conciliar' ]          = $arConciliacao[$i]['conciliar'];
            $arConciliacoes[$inId]['cod_lote' ]           = $arConciliacao[$i]['cod_lote'];
            $arConciliacoes[$inId]['tipo' ]               = $arConciliacao[$i]['tipo'];
            $arConciliacoes[$inId]['cod_entidade' ]       = $arConciliacao[$i]['cod_entidade'];
            $arConciliacoes[$inId]['tipo_movimentacao' ]  = $arConciliacao[$i]['tipo_movimentacao'];
            $inId++;
        } else {
            $vlConciliar = $arConciliacao[$i]['vl_lancamento'];
        }
    }

    Sessao::write('arMovimentacaoManual', $arConciliacoes);

    $js = "executaFuncaoAjax( 'montaListaMovimentacaoManual' );";
    $js .= "
        valorAdicionado = '".$vlConciliar."';
        valorAdicionado = valorAdicionado.replace(',','.');
        valorContabilConciliado = parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value;
        valorContabilConciliado = valorContabilConciliado.replace(',','.');

        soma = parseFloat(valorContabilConciliado) + parseFloat(valorAdicionado);
        soma = Math.round(soma*100)/100;
        soma = soma.toString().replace('.',',');
        parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value = soma;
        calculaSaldo();
    ";

    echo $js;
}

switch ($_REQUEST["stCtrl"]) {
case "montaListaMovimentacao":
    $arMovimentacaoManual = Sessao::read('arMovimentacaoManual');
    $arMovimentacao = Sessao::read('arMovimentacao');
    
    if ($arMovimentacaoManual) {
        $stHtmlManual = montaListaManual ($arMovimentacaoManual, false);
        $stJs .= "d.getElementById('spnMovimentacaoManual').innerHTML='".$stHtmlManual."';";
        
        //percorre as movimentacoes manuais e desabilita as que estiverem conciliadas e nao forem do mes
        foreach ($arMovimentacaoManual as $inKey => $arValue) {
            if ((substr(implode('',array_reverse(explode('/',$arValue['dt_conciliacao']))),0,6) != $_REQUEST['stExercicio'].$_REQUEST['inMes']) AND ($arValue['conciliado'] == 't')) {
                $stJs .= "jq('#boManual_M".$inKey."_".($inKey+1)."').attr('disabled',true);";
            }
        }
    }
    
    if ($arMovimentacao) {
        $stHtmlMovi = montaLista ($arMovimentacao, false);
        $stJs  = "d.getElementById('spnMovimentacao').innerHTML = '".$stHtmlMovi."';";
        
        $i = 1;
        foreach ($arMovimentacao as $inKey => $arValue) {
            if ((substr(implode('',array_reverse(explode('/',$arValue['dt_conciliacao']))),0,6) != $_REQUEST['stExercicio'].$_REQUEST['inMes']) AND ($arValue['conciliar'] == 'true')) {
            //$stJs .= "jq('#layer_2 #boConciliar_".$arValue['id']."_".$i."').attr('disabled',true);";
            }
            $i++;
        }
    }
    
    $stHtmlPendencia = montaListaPendencia(false,$_REQUEST);
    $stJs .= "d.getElementById('spnMovimentacaoPendente').innerHTML='".$stHtmlPendencia."';";
    
    //adiciona um listener para o selecionar todos da tab movimentacao corrente
    $stJs .= "jq('#boTodos').removeAttr('onchange');";
    $stJs .= "jq('#boTodos').click(function () {
                                       if (this.checked) {
                                           jq('#spnMovimentacao input:checkbox:not(#boTodos)').each(function () {
                                                                                          jq(this).attr('checked','checked');
                                                                                      });
                                       } else {
                                           jq('#spnMovimentacao input:checkbox').each(function () {
                                                                         if (!this.disabled) {
                                                                             jq(this).removeAttr('checked');
                                                                         }
                                                                     });
                                       }
                                       ajaxJavaScript('OCManterConciliacao.php?boTodos=true&conciliar='+this.checked+'&saldoConciliado='+jq('#nuSaldoContabilConciliado').val()+'&inMes='+jq('#inMes').val(),'conciliarMovimentacao');
                                   });";

    echo $stJs;

    break;

case "montaListaMovimentacaoManual":
    $stHtmlMovManual = montaListaManual(Sessao::read('arMovimentacaoManual'), false);
    $stJs .= "d.getElementById('spnMovimentacaoManual').innerHTML='".$stHtmlMovManual."';";
    echo $stJs;
    break;

case "detalharLista":
    echo montaListaPendenciaDetalhada($_REQUEST['chave'],$_REQUEST);
    break;

case 'conciliarMovimentacaoManual':

    $arMovimentacaoManual = Sessao::read('arMovimentacaoManual');
    $arId = explode('_',$_REQUEST['id']);

    $arMovimentacaoManual[str_replace('M','',$arId[1])]['conciliar'] = ($_REQUEST['conciliar'] == 'true') ? 1 : 0;
    $arMovimentacaoManual[str_replace('M','',$arId[1])]['conciliado_sessao'] = ($_REQUEST['conciliar'] == 'true') ? 1 : 0;

    sessao::write('arMovimentacaoManual',$arMovimentacaoManual);

    break;

case 'conciliarMovimentacao':
    $arMovimentacao = Sessao::read('arMovimentacaoAux');
    if (!$_REQUEST['boTodos']) {
        $arId = explode('_',$_REQUEST['id']);
        foreach ($arMovimentacao as $inKey => $arValue) {
            if ($inKey == $arId[2]-1) {
                $arMovimentacao[$inKey]['conciliar'] = ($_REQUEST['conciliar'] == 'true') ? 'true' : '';
                $arMovimentacao[$inKey]['conciliado_sessao'] = ($_REQUEST['conciliar'] == 'true') ? 'true' : '';
            }
        }
    } else {
        $flValorConciliado = 0;
        $flValorTotal = (float) str_replace(',','.',str_replace('.','',$_REQUEST['saldoConciliado']));

        foreach ($arMovimentacao as $inKey => $arValue) {
            if ($_REQUEST['conciliar'] == 'true') {
                if ($arValue['conciliar'] == '') {
                    $flValorTotal = bcsub($flValorTotal,$arValue['vl_lancamento'],2);
                }
            } else {
                if ($arValue['conciliar'] == 'true') {
                    $flValorTotal = bcadd($flValorTotal,$arValue['vl_lancamento'],2);
                }
            }
            $arMovimentacao[$inKey]['conciliar'] = ($_REQUEST['conciliar'] == 'true') ? 'true' : '';
            $arMovimentacao[$inKey]['conciliado_sessao'] = ($_REQUEST['conciliar'] == 'true') ? 'true' : '';

        }
        $stJs .= "jq('#nuSaldoContabilConciliado').val('".number_format(($flValorTotal), 2,',','.')."');";
        $stJs .= "calculaSaldo()";
    }
    echo $stJs;
    Sessao::write('arMovimentacaoAux',$arMovimentacao);
    break;

case "incluirMovimentacao":

    $nuValor = str_replace( '.','' ,$_POST['nuValor'] );
    $nuValor = str_replace( ',','.',$nuValor          );
    $arMovimentacaoManual = Sessao::read('arMovimentacaoManual');
    $inCount = sizeof($arMovimentacaoManual);
    $arMovimentacaoManual[$inCount]['cod_plano']           = $_POST['inCodPlano'];
    $arMovimentacaoManual[$inCount]['exercicio']           = $_POST['exercicio' ];
    $arMovimentacaoManual[$inCount]['dt_lancamento']       = $_POST['stDtMovimentacao'];
    $arMovimentacaoManual[$inCount]['tipo_valor']          = $_POST['stTipoMovimento'];
    $arMovimentacaoManual[$inCount]['vl_lancamento' ]      = ( $_POST['stTipoMovimento']=='C' ) ? $nuValor : $nuValor*(-1);
    $arMovimentacaoManual[$inCount]['descricao' ]          = $_POST['stDescricao'];
    $arMovimentacaoManual[$inCount]['conciliar' ]          = 0;
    $arMovimentacaoManual[$inCount]['cod_lote' ]           = 0;
    $arMovimentacaoManual[$inCount]['tipo' ]               = "";
    $arMovimentacaoManual[$inCount]['dt_conciliacao' ]     = '&nbsp;';
    $arMovimentacaoManual[$inCount]['cod_entidade' ]       = $_POST['inCodEntidade'];
    $arMovimentacaoManual[$inCount]['tipo_movimentacao' ]  = "M";
    $arMovimentacaoManual[$inCount]['id']                  = "M".$inCount;

    for ($i = 0; $i < $inCount; $i++) {
        $arMovimentacaoManual[$i]['conciliar' ] = 0;
        foreach ($_REQUEST as $stKey => $stValue) {
            if (strpos($stKey,'boManual_M'. $i) !== false) {
                $arMovimentacaoManual[$i]['conciliar' ] = 1;
            }
        }
    }

    Sessao::write('arMovimentacaoManual', $arMovimentacaoManual);
    montaListaManual( $arMovimentacaoManual );
    break;

case "carregarConciliacao":
    carregarConciliacao( $_GET['id'] );
    excluirConciliacao( $_GET['id'] );
    break;

case "excluirConciliacao":
    excluirConciliacao( $_GET['id'] );
    break;

case "saldoTesouraria":
    if ($_REQUEST['inCodEntidade']) {
        $obRTesourariaConciliacao = new RTesourariaConciliacao();
        $obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setExercicio ( $_REQUEST['stExercicio'] );
        $obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setCodPlano  ( $_REQUEST['inCodPlano' ] );
        $obErro = $obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->consultarSaldoTesouraria( $nuVlSaldo, '01/01/'.$_REQUEST['stExercicio'], $_REQUEST['stDtExtrato'] );
        if ( !$obErro->ocorreu() ) {
            $arFiltro = Sessao::read('filtro');
            $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio ( $_REQUEST['stExercicio'] );
            $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano  ( $_REQUEST['inCodPlano' ] );
            $obRTesourariaConciliacao->setDataInicial   ( $arFiltro['stDataInicial'] );
            $obRTesourariaConciliacao->setDataFinal     ( $_REQUEST['stDtExtrato']  );
            $obRTesourariaConciliacao->setDataFinal     ( $_REQUEST['stDtExtrato']  );
            $obRTesourariaConciliacao->setMes           ( intval($arFiltro['inMes']) );
            $obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
            $obRTesourariaConciliacao->listarMovimentacao($rsLista);

            $arMovimentacaoAux = Sessao::read('arMovimentacaoAux');
            $arMovimentacao = Sessao::read('arMovimentacao');
            $arMovimentacaoAux = $rsLista->getElementos();
            $arMovimentacao = $arMovimentacaoAux;
            sort($arMovimentacao);

            if ( Sessao::read('boAgrupar') ) {
                $inCount = 0;
                for ( $x = 0 ; $x < count( $arMovimentacao ); $x++ ) {
                    foreach ($arMovimentacao[$x] as $key => $value) {
                        $arMovAgrupada[$inCount][$key] = $value;
                    }
                    // Agrupa bordero
                    if ($arMovimentacao[$x]['cod_bordero']) {
                        $inCodBordero = $arMovimentacao[$x]['cod_bordero'];
                        $nuVlMovConciliada = 0;
                        $nuVlMovNConciliada = 0;
                        $stIndiceMovCinciliada  = "";
                        $stIndiceMovNCinciliada = "";
                        while ($arMovimentacao[$x]['cod_bordero'] == $inCodBordero) {
                            if ($arMovimentacao[$x]['conciliar']) {
                                $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                                $stIndiceMovCinciliada .= $arMovimentacao[$x]['indices'].",";
                            } else {
                                $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                                $stIndiceMovNCinciliada .= $arMovimentacao[$x]['indices'].",";
                            }
                            $x++;
                        }
                        $x--;
                        if ($nuVlMovConciliada) {
                            if( $nuVlMovConciliada >= 0 )
                                $stDescricao = "Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            else
                                $stDescricao = "Estorno de Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                            $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                            $arMovAgrupada[$inCount]['conciliar']     = true;
                            $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                            $inCount++;
                        }
                        if ($nuVlMovNConciliada) {
                            foreach ($arMovimentacao[$x] as $key => $value) {
                                $arMovAgrupada[$inCount][$key] = $value;
                            }
                            if( $nuVlMovConciliada >= 0 )
                                $stDescricao = "Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            else
                                $stDescricao = "Estorno de Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                            $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                            $arMovAgrupada[$inCount]['conciliar']     = false;
                            $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                            $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                            $inCount++;
                        }
                        $inCount--;
                    }
                    // Agrupa Arrecadacao
                    if ($arMovimentacao[$x]['cod_receita']) {
                        $inCodReceita = $arMovimentacao[$x]['cod_receita'];
                        $nuVlMovConciliada  = 0;
                        $nuVlMovNConciliada = 0;
                        $stIndiceMovCinciliada  = "";
                        $stIndiceMovNCinciliada = "";
                        while ($arMovimentacao[$x]['cod_receita'] == $inCodReceita) {
                            if ($arMovimentacao[$x]['conciliar']) {
                                $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                                $stIndiceMovCinciliada .= $arMovimentacao[$x]['indices'].",";
                            } else {
                                $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                                $stIndiceMovNCinciliada .= $arMovimentacao[$x]['indices'].",";
                            }
                            $x++;
                        }
                        $x--;
                        if ($nuVlMovConciliada) {
                            if( $nuVlMovConciliada >= 0 )
                                $stDescricao  = "Arrecadação da receita ".$inCodReceita."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            else
                                $stDescricao  = "Estorno de Arrecadação da receita ".$inCodReceita."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                            $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                            $arMovAgrupada[$inCount]['conciliar']     = true;
                            $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                            $inCount++;
                        }
                        if ($nuVlMovNConciliada) {
                            foreach ($arMovimentacao[$x] as $key => $value) {
                                $arMovAgrupada[$inCount][$key] = $value;
                            }
                            if( $nuVlMovNConciliada >= 0 )
                                $stDescricao  = "Arrecadação da receita ".$inCodReceita."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            else
                                $stDescricao  = "Estorno de Arrecadação da receita ".$inCodReceita."-".$arMovimentacao[$x]['cod_entidade']."/".Sessao::getExercicio();
                            $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                            $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                            $arMovAgrupada[$inCount]['conciliar']     = false;
                            $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                            $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                            $inCount++;
                        }
                        $inCount--;
                    }
                $inCount++;
                }
                $arMovimentacao = $arMovAgrupada;
            }
            $stHTML = montaLista( $arMovimentacao, false );
        }
    } else {
       $nuVlSaldo = "0.00";
    }
    Sessao::write('arMovimentacao', $arMovimentacao);
    Sessao::write('arMovimentacaoAux', $arMovimentacaoAux);
    Sessao::write('arMovimentacaoManual', $arMovimentacaoManual);
    $stJs .= "LiberaFrames( true, false );";
    $stJs .= "d.getElementById('nuSaldoTesouraria').innerHTML='".number_format( $nuVlSaldo, 2, ',', '.' )."';";
    $stJs .= "f.nuSaldoTesouraria.value='".$nuVlSaldo."';";
    $stJs .= "f.stDtMovimentacao.value='".$_REQUEST['stDtExtrato']."';";
    $stJs .= "calculaSaldo();";
    $stJs .= "d.getElementById('spnMovimentacao').innerHTML='".$stHTML."';";
    echo $stJs;

    break;

case "conciliarPendente":
    $arPendenciasMarcadas = Sessao::read('arPendenciasMarcadas');
    foreach ($_REQUEST as $stChave => $stValue) {
        if (ereg("^boPendencia", $stChave)) {
            if ($stValue == 'true') {
                $arPendenciasMarcadas[$stChave] = true;
            } else {
                unset($arPendenciasMarcadas[$stChave]);
            }
        }
    }
    Sessao::write('arPendenciasMarcadas', $arPendenciasMarcadas);
    break;
}
?>
