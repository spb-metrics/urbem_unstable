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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 11/03/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/IMontaQuantidadeValores.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGEmpenhoRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGProcessoAdesaoRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGItemRegistroPrecos.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
include_once CAM_GF_PPA_COMPONENTES.'MontaOrgaoUnidade.class.php';
include_once CAM_GP_ALM_COMPONENTES.'IMontaItemUnidade.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';
//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroPreco";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once ($pgJs);
include_once ($pgOcul);

$stAcao = $request->get('stAcao');

if ($stAcao == 'alterar' && ($request->get('inNroProcessoAdesao') != '' && $request->get('stExercicioProcessoAdesao') != '')) {
    $rsProcessoAdesao = new RecordSet();
    $obTTCEMGProcessoAdesaoRegistroPrecos = new TTCEMGProcessoAdesaoRegistroPrecos();
    $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
    $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('exercicio_adesao'       , $request->get('stExercicioProcessoAdesao'));
    $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('numero_processo_adesao' , $request->get('inNroProcessoAdesao'));
    $obTTCEMGProcessoAdesaoRegistroPrecos->recuperaProcesso($rsProcessoAdesao);

    $inCodEntidade                = $rsProcessoAdesao->getCampo('cod_entidade');
    $stCodigoProcessoAdesao       = $rsProcessoAdesao->getCampo('codigo_processo_adesao');
    $dtAberturaProcessoAdesao     = $rsProcessoAdesao->getCampo('data_abertura_processo_adesao');
    $inNumOrgaoGerenciador        = $rsProcessoAdesao->getCampo('numcgm_orgao_gerenciador');
    $stNomOrgaoGerenciador        = $rsProcessoAdesao->getCampo('nomcgm_orgao_gerenciador');
    $stCodigoProcessoLicitacao    = $rsProcessoAdesao->getCampo('numero_processo_licitacao');
    $stExercicioProcessoLicitacao = $rsProcessoAdesao->getCampo('exercicio_licitacao');
    $inCodigoModalidadeLicitacao  = $rsProcessoAdesao->getCampo('codigo_modalidade_licitacao');
    $inNumeroModalidade           = $rsProcessoAdesao->getCampo('numero_modalidade');
    $dtAtaRegistroPreco           = $rsProcessoAdesao->getCampo('data_ata_registro_preco');
    $dtAtaRegistroPrecoValidade   = $rsProcessoAdesao->getCampo('data_ata_registro_preco_validade');
    $stNaturazaProcedimento       = $rsProcessoAdesao->getCampo('natureza_procedimento');
    $dtPublicacaoAvisoIntencao    = $rsProcessoAdesao->getCampo('data_publicacao_aviso_intencao');
    $txtObjetoAdesao              = $rsProcessoAdesao->getCampo('objeto_adesao');
    $inNumCGMResponsavel          = $rsProcessoAdesao->getCampo('numcgm_responsavel');
    $stNomCGMResponsavel          = $rsProcessoAdesao->getCampo('nomcgm_responsavel');
    $inDescontoTabela             = $rsProcessoAdesao->getCampo('desconto_tabela');
    $inProcessoLote               = $rsProcessoAdesao->getCampo('processo_lote');
    $stUnidadeOrcamentaria        = $rsProcessoAdesao->getCampo('unidade_orcamentaria');

    $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
    $obTTCEMGItemRegistroPrecos->setDado('numero_processo_adesao' , $request->get('inNroProcessoAdesao'));
    $obTTCEMGItemRegistroPrecos->setDado('exercicio_adesao'       , $request->get('stExercicioProcessoAdesao'));
    $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
    $obTTCEMGItemRegistroPrecos->recuperaListaItem($rsItem);
    
    $arItens = array();
    $inCount = 0;
    
    # Carrega os itens para alteração
    while (!($rsItem->eof())) {

        $arItens[$inCount]['inCodItem']        = $rsItem->getCampo('cod_item');
        $arItens[$inCount]['stNomItem']        = $rsItem->getCampo('descricao_resumida');
        $arItens[$inCount]['stNomUnidade']     = $rsItem->getCampo('nom_unidade');
        $arItens[$inCount]['nuVlReferencia']   = number_format($rsItem->getCampo('vl_cotacao_preco_unitario'), 4, ',', '.');
        $arItens[$inCount]['nuQuantidade']     = number_format($rsItem->getCampo('quantidade_cotacao'), 4, ',', '.');
        $arItens[$inCount]['nuVlTotal']        = number_format(($rsItem->getCampo('vl_cotacao_preco_unitario') * $rsItem->getCampo('quantidade_cotacao')), 4, ',', '.');
        $arItens[$inCount]['dtCotacao']        = $rsItem->getCampo('data_cotacao');
        $arItens[$inCount]['stCodigoLote']     = ($rsItem->getCampo('cod_lote') != 0) ? $rsItem->getCampo('cod_lote') : '0';
        $arItens[$inCount]['nuPercentualLote'] = $rsItem->getCampo('percentual_desconto_lote');
        $arItens[$inCount]['txtDescricaoLote'] = $rsItem->getCampo('descricao_lote');
        $arItens[$inCount]['nuVlUnitario']     = number_format($rsItem->getCampo('preco_unitario'), 4, ',', '.');
        $arItens[$inCount]['nuQtdeLicitada']   = number_format($rsItem->getCampo('quantidade_licitada'), 4, ',', '.');
        $arItens[$inCount]['nuQtdeAderida']    = number_format($rsItem->getCampo('quantidade_aderida'), 4, ',', '.');
        $arItens[$inCount]['nuPercentualItem'] = $rsItem->getCampo('percentual_desconto');
        $arItens[$inCount]['inNumCGMVencedor'] = $rsItem->getCampo('numcgm_vencedor');
        $arItens[$inCount]['stNomCGMVencedor'] = $rsItem->getCampo('nomcgm_vencedor');
        
        $arItens[$inCount]['inId'] = ($inCount + 1);

        $inCount++;
        $rsItem->proximo();
    }

    Sessao::write('arItens', $arItens);
    
    $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
    $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_processo_adesao' , $request->get('inNroProcessoAdesao'));
    $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio_adesao'       , $request->get('stExercicioProcessoAdesao'));
    $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
    $obTTCEMGEmpenhoRegistroPrecos->recuperaPorChave($rsEmpenho);
    
    $arEmpenhos = array();
    $inCount = 0;
    

    $obErro = new Erro();
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho();
    # Carrega os empenhos para alteração
    while (!($rsEmpenho->eof())) {
        
        if ( $rsEmpenho->getCampo('exercicio_empenho') == Sessao::getExercicio() ) {
            $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor";
            $obTEmpenhoEmpenho->setDado( "tribunal", "TCEMG");
            $obTEmpenhoEmpenho->setDado( "exercicio", $rsEmpenho->getCampo('exercicio_empenho') );
            $stFiltro  = " AND tabela.exercicio = '".$rsEmpenho->getCampo('exercicio_empenho')."' ";
            $stFiltro .= " AND tabela.cod_entidade IN (".$rsEmpenho->getCampo('cod_entidade')." ) ";
            $stFiltro .= " AND tabela.cod_empenho = ".$rsEmpenho->getCampo('cod_empenho')." ";
            
            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
            $stOrder = ($stOrder) ? $stOrder : "tabela.cod_empenho";
            $obErro = $obTEmpenhoEmpenho->recuperaConsultaEmpenho( $rsLista, $stFiltro, $stOrder, $boTransacao );
        } else {
            $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor";
            $obTEmpenhoEmpenho->setDado( "tribunal", "TCEMG");
            $obTEmpenhoEmpenho->setDado( "exercicio", $rsEmpenho->getCampo('exercicio_empenho') );
            $stFiltro  = " AND tabela.exercicio = '".$rsEmpenho->getCampo('exercicio_empenho')."' ";
            $stFiltro .= " AND tabela.cod_entidade IN (".$rsEmpenho->getCampo('cod_entidade')." ) ";
            $stFiltro .= " AND tabela.cod_empenho = ".$rsEmpenho->getCampo('cod_empenho')." ";
            
            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
            $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
            $obErro   = $obTEmpenhoEmpenho->recuperaRestosConsultaEmpenho( $rsLista, $stFiltro, $stOrder, $boTransacao );
        }

        $arEmpenhos[$inCount]['cod_entidade']   = $rsLista->getCampo('cod_entidade');
        $arEmpenhos[$inCount]['exercicio']      = $rsLista->getCampo('exercicio');
        $arEmpenhos[$inCount]['cod_empenho']    = $rsLista->getCampo('cod_empenho');
        $arEmpenhos[$inCount]['nom_fornecedor'] = $rsLista->getCampo('nom_fornecedor');
        $arEmpenhos[$inCount]['vl_empenhado']   = $rsLista->getCampo('vl_empenhado');
        $arEmpenhos[$inCount]['dt_empenho']     = $rsLista->getCampo('dt_empenho');
        $arEmpenhos[$inCount]['inId'] = ($inCount + 1);

        $inCount++;
        $rsEmpenho->proximo();
    }

    Sessao::write('arEmpenhos', $arEmpenhos);
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setId( "stExercicio" );
$obHdnExercicio->setValue( Sessao::getExercicio() );

# Entidade Principal
$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->setId('inCodEntidade');
$obITextBoxSelectEntidade->setName('inCodEntidade');
$obITextBoxSelectEntidade->setObrigatorio(true);
$obITextBoxSelectEntidade->setCodEntidade($inCodEntidade);
$obITextBoxSelectEntidade->obTextBox->obEvento->setOnChange("jQuery('#stCodigoProcesso').val('');");
$obITextBoxSelectEntidade->obSelect->obEvento->setOnChange("jQuery('#stCodigoProcesso').val('');");

if ($stAcao == 'alterar') {
    $obITextBoxSelectEntidade->setLabel(true);
}

$obTxtCodigoProcesso = new TextBox();
$obTxtCodigoProcesso->setName('stCodigoProcesso');
$obTxtCodigoProcesso->setId('stCodigoProcesso');
$obTxtCodigoProcesso->setRotulo('Nro. do Processo de Adesão');
$obTxtCodigoProcesso->setTitle('Número do processo de adesão do órgão à Ata de Registro de Preços.');
$obTxtCodigoProcesso->setMaxLength(17);
$obTxtCodigoProcesso->setMascara('999999999999/9999');
$obTxtCodigoProcesso->setNull(false);
$obTxtCodigoProcesso->setValue( $stCodigoProcessoAdesao );
$obTxtCodigoProcesso->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade='+jQuery('#inCodEntidade').val()+'&stNumProcesso='+this.value,'validaNroProcesso');");

if ($stAcao == 'alterar') {
    $obTxtCodigoProcesso->setReadOnly(true);
}

$obIMontaUnidadeOrcamentaria = new MontaOrgaoUnidade();
$obIMontaUnidadeOrcamentaria->setRotulo('Unidade Executora');
$obIMontaUnidadeOrcamentaria->setValue( $stUnidadeOrcamentaria );
$obIMontaUnidadeOrcamentaria->setCodOrgao('');
$obIMontaUnidadeOrcamentaria->setCodUnidade('');
$obIMontaUnidadeOrcamentaria->setActionPosterior($pgProc);
$obIMontaUnidadeOrcamentaria->setNull(false);

$obDtAbertura = new Data(); 
$obDtAbertura->setName('dtAberturaProcesso');
$obDtAbertura->setId('dtAberturaProcesso');
$obDtAbertura->setTitle('Data de abertura do processo de adesão.');
$obDtAbertura->setRotulo('Data de abertura do processo');
$obDtAbertura->setNull(false);
$obDtAbertura->setValue( $dtAberturaProcessoAdesao );

$obBscOrgaoGerenciador = new BuscaInner;
$obBscOrgaoGerenciador->setRotulo( "CGM do Orgão Gerenciador" );
$obBscOrgaoGerenciador->setTitle( "Informe o código CGM do Orgão Gerenciador" );
$obBscOrgaoGerenciador->setNull( false );
$obBscOrgaoGerenciador->setId( "inNomOrgaoGerenciador" );
$obBscOrgaoGerenciador->setValue($stNomOrgaoGerenciador);
$obBscOrgaoGerenciador->obCampoCod->setName("inNumOrgaoGerenciador");
$obBscOrgaoGerenciador->obCampoCod->setId("inNumOrgaoGerenciador");
$obBscOrgaoGerenciador->obCampoCod->setValue( $inNumOrgaoGerenciador );
$obBscOrgaoGerenciador->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumOrgaoGerenciador='+this.value,'buscaOrgaoGerenciador');");
$obBscOrgaoGerenciador->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumOrgaoGerenciador','inNomOrgaoGerenciador','','".Sessao::getId()."&stCtrl=buscaOrgaoGerenciador','800','550');" );

$obTxtNumeroProcessoLicitacao = new TextBox();
$obTxtNumeroProcessoLicitacao->setName('stNroProcessoLicitacao');
$obTxtNumeroProcessoLicitacao->setId('stNroProcessoLicitacao');
$obTxtNumeroProcessoLicitacao->setRotulo('Nro. do Processo de Licitação');
$obTxtNumeroProcessoLicitacao->setTitle('Número sequencial do processo cadastrado no órgão gerenciador do registro de preços por exercício.');
$obTxtNumeroProcessoLicitacao->setMaxLength(20);
$obTxtNumeroProcessoLicitacao->setSize(23);
$obTxtNumeroProcessoLicitacao->setNull(false);
$obTxtNumeroProcessoLicitacao->setValue( $stCodigoProcessoLicitacao );

$obTxtExercicioProcessoLicitacao = new TextBox();
$obTxtExercicioProcessoLicitacao->setName('stExercicioProcessoLicitacao');
$obTxtExercicioProcessoLicitacao->setId('stExercicioProcessoLicitacao');
$obTxtExercicioProcessoLicitacao->setRotulo('Exercício do Processo de Licitação');
$obTxtExercicioProcessoLicitacao->setMaxLength(4);
$obTxtExercicioProcessoLicitacao->setSize(5);
$obTxtExercicioProcessoLicitacao->setNull(false);
$obTxtExercicioProcessoLicitacao->setValue( (!empty($stExercicioProcessoLicitacao) ? $stExercicioProcessoLicitacao : Sessao::getExercicio()) );

$obRadioCodigoModalidadeLicitacaoConcorrencia = new Radio();
$obRadioCodigoModalidadeLicitacaoConcorrencia->setRotulo('Modalidade da Licitação');
$obRadioCodigoModalidadeLicitacaoConcorrencia->setTitle('Somente os Municípios com população inferior a cinquenta mil habitantes devem preencher este campo.');
$obRadioCodigoModalidadeLicitacaoConcorrencia->setName('inCodModalidadeLicitacao');
$obRadioCodigoModalidadeLicitacaoConcorrencia->setLabel("Concorrência");
$obRadioCodigoModalidadeLicitacaoConcorrencia->setValue("1");
$obRadioCodigoModalidadeLicitacaoConcorrencia->setNull(false);

if ( $inCodigoModalidadeLicitacao == 1) {
    $obRadioCodigoModalidadeLicitacaoConcorrencia->setChecked(true);    
}

$obRadioCodigoModalidadeLicitacaoPregao = new Radio;
$obRadioCodigoModalidadeLicitacaoPregao->setName('inCodModalidadeLicitacao');
$obRadioCodigoModalidadeLicitacaoPregao->setLabel("Pregão");
$obRadioCodigoModalidadeLicitacaoPregao->setValue("2");
$obRadioCodigoModalidadeLicitacaoPregao->setNull(false);

if ( $inCodigoModalidadeLicitacao == 2) {
    $obRadioCodigoModalidadeLicitacaoPregao->setChecked(true);    
}

$obTxtNumeroModalidade = new TextBox();
$obTxtNumeroModalidade->setName('stNroModalidade');
$obTxtNumeroModalidade->setId('stNroModalidade');
$obTxtNumeroModalidade->setRotulo('Nro. da Modalidade');
$obTxtNumeroModalidade->setTitle('Número sequencial da Modalidade por exercício.');
$obTxtNumeroModalidade->setMaxLength(10);
$obTxtNumeroModalidade->setNull(false);
$obTxtNumeroModalidade->setValue( $inNumeroModalidade );

$obDtAtaRegistroPreco = new Data(); 
$obDtAtaRegistroPreco->setName('dtAtaRegistroPreco');
$obDtAtaRegistroPreco->setId('dtAtaRegistroPreco');
$obDtAtaRegistroPreco->setTitle('Data da Ata do Registro de Preços');
$obDtAtaRegistroPreco->setRotulo('Data da Ata');
$obDtAtaRegistroPreco->setNull(false);
$obDtAtaRegistroPreco->setValue( $dtAtaRegistroPreco );

$obDtValidadeAtaRegistroPreco = new Data(); 
$obDtValidadeAtaRegistroPreco->setName('dtValidadeAtaRegistroPreco');
$obDtValidadeAtaRegistroPreco->setId('dtValidadeAtaRegistroPreco');
$obDtValidadeAtaRegistroPreco->setTitle('Data de Validade da Ata do Registro de Preços.');
$obDtValidadeAtaRegistroPreco->setRotulo('Data de Validade da Ata');
$obDtValidadeAtaRegistroPreco->setNull(false);
$obDtValidadeAtaRegistroPreco->setValue( $dtAtaRegistroPrecoValidade );

$obRadioNaturezaProcedimentoParticipante = new Radio();
$obRadioNaturezaProcedimentoParticipante->setRotulo('Natureza do Procedimento');
$obRadioNaturezaProcedimentoParticipante->setTitle('Os valores possíveis para identificar a Natureza do Procedimento de Adesão são:</br>
1 - Órgão Participante (órgão ou entidade que participa dos procedimentos iniciais do SRP e integra a Ata de Registro de Preços);</br>
2 - Órgão Não Participante (órgão ou entidade que não está contemplado na Ata de Registro de Preços).');
$obRadioNaturezaProcedimentoParticipante->setName('inNaturezaProcedimento');
$obRadioNaturezaProcedimentoParticipante->setLabel("Órgão Participante");
$obRadioNaturezaProcedimentoParticipante->setValue("1");
$obRadioNaturezaProcedimentoParticipante->setNull(false);

if ( $stNaturazaProcedimento == 1) {
    $obRadioNaturezaProcedimentoParticipante->setChecked(true);    
}

$obRadioNaturezaProcedimentoNaoParticipante = new Radio;
$obRadioNaturezaProcedimentoNaoParticipante->setName('inNaturezaProcedimento');
$obRadioNaturezaProcedimentoNaoParticipante->setLabel("Órgão Não Participante");
$obRadioNaturezaProcedimentoNaoParticipante->setValue("2");
$obRadioNaturezaProcedimentoNaoParticipante->setNull(false);

if ( $stNaturazaProcedimento == 2) {
    $obRadioNaturezaProcedimentoNaoParticipante->setChecked(true);    
}

$obDtPublicacaoAvisoIntencao = new Data(); 
$obDtPublicacaoAvisoIntencao->setName('dtPublicacaoAvisoIntencao');
$obDtPublicacaoAvisoIntencao->setId('dtPublicacaoAvisoIntencao');
$obDtPublicacaoAvisoIntencao->setTitle ('Data de Publicação do Aviso de Intenção.');
$obDtPublicacaoAvisoIntencao->setRotulo('Data de Publicação do Aviso de Intenção');
$obDtPublicacaoAvisoIntencao->setValue( $dtPublicacaoAvisoIntencao );

$obTxtAreaObjetoAdesao = new TextArea();
$obTxtAreaObjetoAdesao->setName('txtAreaObjetoAdesao');
$obTxtAreaObjetoAdesao->setId('txtAreaObjetoAdesao');
$obTxtAreaObjetoAdesao->setRotulo('Objeto da Adesão');
$obTxtAreaObjetoAdesao->setTitle('Objeto da Adesão.');
$obTxtAreaObjetoAdesao->setMaxCaracteres(500);
$obTxtAreaObjetoAdesao->setNull(false);
$obTxtAreaObjetoAdesao->setValue( $txtObjetoAdesao );

$obBscCGMResponsavel = new BuscaInner;
$obBscCGMResponsavel->setRotulo( "CGM Responsável pelo Detalhamento" );
$obBscCGMResponsavel->setTitle( "Informe o código do CGM responsável pelo processo" );
$obBscCGMResponsavel->setNull( false );
$obBscCGMResponsavel->setId( "inNomCGMResponsavel" );
$obBscCGMResponsavel->setValue($stNomCGMResponsavel);
$obBscCGMResponsavel->obCampoCod->setName("inNumCGMResponsavel");
$obBscCGMResponsavel->obCampoCod->setId("inNumCGMResponsavel");
$obBscCGMResponsavel->obCampoCod->setValue( $inNumCGMResponsavel );
$obBscCGMResponsavel->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumCGMResponsavel='+this.value,'buscaCGMResponsavel');");
$obBscCGMResponsavel->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMResponsavel','inNomCGMResponsavel','fisica','".Sessao::getId()."&stCtrl=buscaCGMResponsavel','800','550');" );

$obDescontoTabelaSim = new Radio();
$obDescontoTabelaSim->setName('inDescontoTabela');
$obDescontoTabelaSim->setId('inDescontoTabela');
$obDescontoTabelaSim->setRotulo('Desconto Tabela');
$obDescontoTabelaSim->setTitle('Informar se foi utilizado como critério de adjudicação a oferta de desconto sobre tabela de preços praticados no mercado.');
$obDescontoTabelaSim->setLabel('Sim');
$obDescontoTabelaSim->setValue(1);
$obDescontoTabelaSim->setNull(false);
if ( $inDescontoTabela == 1) {
    $obDescontoTabelaSim->setChecked(true);    
}
$obDescontoTabelaSim->obEvento->setOnClick("if (jQuery('#nuPercentualLote')) { jQuery('#nuPercentualLote').removeAttr('disabled'); }");

$obDescontoTabelaNao = new Radio();
$obDescontoTabelaNao->setName('inDescontoTabela');
$obDescontoTabelaNao->setId('inDescontoTabela');
$obDescontoTabelaNao->setLabel('Não');
$obDescontoTabelaNao->setValue(2);
$obDescontoTabelaNao->setNull(false);
if ( $inDescontoTabela == 2) {
    $obDescontoTabelaNao->setChecked(true);    
}
$obDescontoTabelaNao->obEvento->setOnClick("jQuery('#nuPercentualLote').attr('disabled', 'disabled'); jQuery('#nuPercentualItem').attr('disabled', 'disabled');");

# Caso o Registro de Preço já tenha desconto em tabela, não poderá ser alterado.
$obHdnDescontoTabela = new Hidden;
$obHdnDescontoTabela->setName  ( "inDescontoTabela" );
$obHdnDescontoTabela->setId    ( "inDescontoTabela" );
$obHdnDescontoTabela->setValue ( $inDescontoTabela  );

$obLblDescontoTabela = new Label;
$obLblDescontoTabela->setName   ( "stLabelDescontoTabela" );
$obLblDescontoTabela->setId     ( "stLabelDescontoTabela" );
$obLblDescontoTabela->setRotulo ( "Desconto Tabela"  );
$obLblDescontoTabela->setValue  ( ($inDescontoTabela == 1) ? "&nbsp;Sim" : "&nbsp;Não" );

$obProcessoPorLoteSim = new Radio();
$obProcessoPorLoteSim->setName('inProcessoPorLote');
$obProcessoPorLoteSim->setId('inProcessoPorLote');
$obProcessoPorLoteSim->setRotulo('Processo por Lote');
$obProcessoPorLoteSim->setTitle('Informar se o processo foi realizado por lote.');
$obProcessoPorLoteSim->setLabel('Sim');
$obProcessoPorLoteSim->setValue('1');
$obProcessoPorLoteSim->setNull(false);
if ( $inProcessoLote == 1) {
    $obProcessoPorLoteSim->setChecked(true);    
}
$obProcessoPorLoteSim->obEvento->setOnClick("montaParametrosGET('montaFormLote', 'stCtrl'); jQuery('#nuPercentualItem').attr('disabled', 'disabled'); ");

$obProcessoPorLoteNao = new Radio();
$obProcessoPorLoteNao->setName('inProcessoPorLote');
$obProcessoPorLoteNao->setId('inProcessoPorLote');
$obProcessoPorLoteNao->setLabel('Não');
$obProcessoPorLoteNao->setValue('2');
$obProcessoPorLoteNao->setNull(false);
if ( $inProcessoLote == 2) {
    $obProcessoPorLoteNao->setChecked(true);    
}
$obProcessoPorLoteNao->obEvento->setOnClick("jQuery('#spnLote').html(''); if (jQuery('#inDescontoTabela:checked').val() == 1) { jQuery('#nuPercentualItem').removeAttr('disabled'); }");

# Caso o Registro de Preço seja por lote e já tenha item vinculado, não poderá ser alterado.
$obHdnProcessoPorLote = new Hidden;
$obHdnProcessoPorLote->setName  ( "inProcessoPorLote" );
$obHdnProcessoPorLote->setId    ( "inProcessoPorLote" );
$obHdnProcessoPorLote->setValue ( $inProcessoLote  );

$obLblProcessoPorLote = new Label;
$obLblProcessoPorLote->setName   ( "stLabelProcessoPorLote" );
$obLblProcessoPorLote->setId     ( "stLabelProcessoPorLote" );
$obLblProcessoPorLote->setRotulo ( "Processo por Lote"  );
$obLblProcessoPorLote->setValue  ( ($inProcessoLote == 1) ? "&nbsp;Sim" : "&nbsp;Não" );

# Inclui formulário de itens
include_once 'FMManterRegistroPrecoItem.php';

# Inclui formulário de empenhos
include_once 'FMManterRegistroPrecoEmpenho.php';

# Elementos da Aba Detalhes
$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->addAba ( "Detalhamento" );
$obFormulario->addTitulo( "Adesão a Registro de Preços" );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addComponente( $obITextBoxSelectEntidade );
$obFormulario->addComponente( $obTxtCodigoProcesso );
$obIMontaUnidadeOrcamentaria->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obDtAbertura );
$obFormulario->addComponente( $obBscOrgaoGerenciador );
$obFormulario->addComponente( $obTxtNumeroProcessoLicitacao );
$obFormulario->addComponente( $obTxtExercicioProcessoLicitacao );
$obFormulario->agrupaComponentes (array($obRadioCodigoModalidadeLicitacaoConcorrencia, $obRadioCodigoModalidadeLicitacaoPregao));
$obFormulario->addComponente( $obTxtNumeroModalidade );
$obFormulario->addComponente( $obDtAtaRegistroPreco );
$obFormulario->addComponente( $obDtValidadeAtaRegistroPreco );
$obFormulario->agrupaComponentes (array($obRadioNaturezaProcedimentoParticipante, $obRadioNaturezaProcedimentoNaoParticipante));
$obFormulario->addComponente( $obDtPublicacaoAvisoIntencao );
$obFormulario->addComponente( $obTxtAreaObjetoAdesao );
$obFormulario->addComponente( $obBscCGMResponsavel );

if ($stAcao == "alterar") {
    $obFormulario->addHidden( $obHdnDescontoTabela );
    $obFormulario->addComponente ( $obLblDescontoTabela ) ;

    $obFormulario->addHidden( $obHdnProcessoPorLote );
    $obFormulario->addComponente ( $obLblProcessoPorLote ) ;
} else {
    $obFormulario->agrupaComponentes( array($obDescontoTabelaSim, $obDescontoTabelaNao) );
    $obFormulario->agrupaComponentes( array($obProcessoPorLoteSim, $obProcessoPorLoteNao) );
}

# Elementos da Aba Itens
$obFormulario->addAba  ( "Itens" );
$obFormulario->addSpan ( $obSpnLote );
$obFormulario->addTitulo( "Dados do Item" );
$obFormulario->addComponente( $obDtContacao );
$obMontaItemUnidade->geraFormulario($obFormulario);
$obMontaQuantidadeValores->geraFormulario($obFormulario);
$obFormulario->addComponente( $obVlrPrecoUnitario );
$obFormulario->addComponente( $obIntQtdeLicitada );
$obFormulario->addComponente( $obIntQtdeAderida );
$obFormulario->addComponente( $obVlrPercentualItem );
$obFormulario->addComponente( $obBscCGMVencedor );
$obFormulario->addSpan ( $obSpnItem );
$obFormulario->agrupaComponentes( array($obBtnSalvar, $obBtnLimpar) );
$obFormulario->addSpan( $obSpanListaItem );

$obFormulario->addAba  ( "Empenhos ");
$obFormulario->addTitulo( "Dados do Empenho" );
$obFormulario->addComponente( $obTxtExercicioEmpenho );
$obFormulario->addComponente( $obBscEmpenho );
$obFormulario->addComponente( $obBtnIncluirEmpenho );
$obFormulario->addSpan ( $obSpnEmpenhos );

$obFormulario->OK();
$obFormulario->show();



$stJs .= montaListaItens();
$stJs .= montaListaEmpenho();
if ($stAcao == 'alterar' && $inProcessoLote == 1) {
    $stJs .= "montaParametrosGET('montaFormLote', 'stCtrl'); ";
}

SistemaLegado::executaFrameOculto($stJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>