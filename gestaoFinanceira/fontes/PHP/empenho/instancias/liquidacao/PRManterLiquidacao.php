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
    * Página de Processamento de Anulação de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRManterLiquidacao.php 63351 2015-08-20 13:42:42Z evandro $

    $Revision: 32142 $
    $Name$
    $Autor:$
    $Date: 2008-01-07 11:40:55 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-02.03.24,uc-02.03.04, uc-02.03.05
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
include( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
SistemaLegado::BloqueiaFrames(true,true);
//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

$pgProxLiquidacao = explode("?",$pgList);
$pgProxEmpenho = $request->get('pgProxEmpenho');
$stAcaoEmpenho = $request->get('stAcaoEmpenho');
$acaoEmpenho = ($request->get('acaoEmpenho')!='') ? $request->get('acaoEmpenho'): 822;
$moduloEmpenho = $request->get('moduloEmpenho');
$funcionalidadeEmpenho = $request->get('funcionalidadeEmpenho');
 
$obTransacao = new Transacao();
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

if (strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio(),$boTransacao))=='true') {

    if (empty($_REQUEST['inCodTipoDocumentoTxt'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }

    if (empty($_REQUEST['inCodTipoDocumento'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }

    if ($_REQUEST['inCodTipoDocumento'] == 6) {
            if (empty($_REQUEST['inCodTipoReciboTxt'])) {
                    SistemaLegado::exibeAviso('Campo Tipo Recibo inválido',"n_incluir","erro");
                    SistemaLegado::liberaFrames(true,true);
                     die;
        }
        if (empty($_REQUEST['inCodTipoRecibo'])) {
               SistemaLegado::exibeAviso('Campo Tipo Recibo inválido!',"n_incluir","erro");
               SistemaLegado::liberaFrames(true,true);
                die;
        }
    }
}


//se fro prefeitura de Tocantins
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao) == 27) { 
    if (empty($_REQUEST['inCodTipoDocumentoTxt'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }

    if (empty($_REQUEST['inCodTipoDocumento'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }
    
    if (empty($_REQUEST['inNumeroDocumento'])) {
        if ($_REQUEST['inCodTipoDocumento'] == 1)
            $tipoDocumento = "Número Nota Fiscal ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 2)
            $tipoDocumento = " Número do Recibo ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 3)
            $tipoDocumento = " Número da Documento ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 4)
            $tipoDocumento = " Número da Folha de Pagamento ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 5)
            $tipoDocumento = " Número do Bilhete ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 6)
            $tipoDocumento = " Número da Nota Fiscal Eletronica ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 7)
            $tipoDocumento = " Número do Cupom Fiscal ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 9)
            $tipoDocumento = " Número da Documento ";
               
           SistemaLegado::exibeAviso('Campo'.$tipoDocumento.' inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }
}

//se for prefeitura de Alagoas
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 02) { 

    if (empty($_REQUEST['inCodTipoDocumentoTxt'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }

    if (empty($_REQUEST['inCodTipoDocumento'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }
    
    if (empty($_REQUEST['inNumeroDocumento'])) {
        if ($_REQUEST['inCodTipoDocumento'] == 1)
            $tipoDocumento = "Número Nota Fiscal ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 2)
            $tipoDocumento = " Número do Recibo ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 3)
            $tipoDocumento = " Número da Documento ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 4)
            $tipoDocumento = " Número da Folha de Pagamento ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 5)
            $tipoDocumento = " Número do Bilhete ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 6)
            $tipoDocumento = " Número da Nota Fiscal Eletronica ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 7)
            $tipoDocumento = " Número do Cupom Fiscal ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 9)
            $tipoDocumento = " Número da Documento ";
               
           SistemaLegado::exibeAviso('Campo'.$tipoDocumento.' inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }
    
    if ($_REQUEST['inCodTipoDocumento'] == 1 OR $_REQUEST['inCodTipoDocumento'] == 6) {
        if (empty($_POST['stAutorizacao'])) {
            SistemaLegado::exibeAviso('Campo Autorização Nota Fiscal inválido',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
            
        if (empty($_POST['stModelo'])) {
            SistemaLegado::exibeAviso('Campo Modelo Nota Fiscal inválido',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if ($_REQUEST['inCodTipoDocumento']==6&&empty($_REQUEST['stNumXmlNFe'])) {
           SistemaLegado::exibeAviso('Campo Número da Chave de Acesso inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
        }
    }

    
}

//se for prefeitura de Minas Gerais
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11) { 
    if($_REQUEST['stIncluirNF']=="Sim"){
        if (empty($_REQUEST['inCodTipoNota'])) {
            SistemaLegado::exibeAviso('Campo Tipo Docto Fiscal inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if (empty($_REQUEST['dtEmissaoNF'])) {
            SistemaLegado::exibeAviso('Campo Data de Emissão inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if (empty($_REQUEST['stExercicioNF'])) {
            SistemaLegado::exibeAviso('Campo Exercício inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if(($_REQUEST['inCodTipoNota']==1||$_REQUEST['inCodTipoNota']==4)&&empty($_REQUEST['inChave'])){
            SistemaLegado::exibeAviso('Campo Chave de Acesso inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if($_REQUEST['inCodTipoNota']==2||$_REQUEST['inCodTipoNota']==3){
            if (empty($_REQUEST['inNumeroNF'])) {
                SistemaLegado::exibeAviso('Campo Número do Docto Fiscal inválido!',"n_incluir","erro");
                SistemaLegado::liberaFrames(true,true);
                die;
            }
            if (empty($_REQUEST['inNumSerie'])) {
                SistemaLegado::exibeAviso('Campo Série do Docto Fiscal inválido!',"n_incluir","erro");
                SistemaLegado::liberaFrames(true,true);
                die;
            }
            if ($_REQUEST['inCodTipoNota']==2&&empty($_REQUEST['inChaveMunicipal'])) {
                SistemaLegado::exibeAviso('Campo Chave de Acesso Municipal inválido!',"n_incluir","erro");
                SistemaLegado::liberaFrames(true,true);
                die;
            }
        }elseif (!is_numeric($_REQUEST['inChave']) ) {
            SistemaLegado::exibeAviso('Campo Chave de Acesso Estadual inválido, digite somente números!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
    }
}

// se a prefeitura for Pernambuco
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) { 

    if (empty($_REQUEST['inCodTipoDocumentoTxt'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }

    if (empty($_REQUEST['inCodTipoDocumento'])) {
           SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }
    
    if (empty($_REQUEST['inNumeroDocumento'])) {
        if ($_REQUEST['inCodTipoDocumento'] == 1)
            $tipoDocumento = "Número Nota Fiscal ";
        elseif ($_REQUEST['inCodTipoDocumento'] == 9)
            $tipoDocumento = " Número do Documento ";
               
           SistemaLegado::exibeAviso('Campo'.$tipoDocumento.' inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
    }
    
}

include( $pgJS );

$obREmpenhoEmpenho        = new REmpenhoEmpenho;
$obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenho );

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_Liq" );
$obAtributos->recuperaVetor( $arChave    );

$stAcao = $request->get('stAcao');

//Trecho de código do filtro
$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            $stFiltro .= "&".$stCampo."=".urlencode( implode(',', $stValor) );
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

//valida a utilização da rotina de encerramento do mês contábil
$arDtAutorizacao = explode('/', $_POST['stDtLiquidacao']);
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9, '', $boTransacao );
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ', $boTransacao);

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::LiberaFrames(true,true);
    SistemaLegado::exibeAviso(urlencode("Mês da Liquidação encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "liquidar":
        
        SistemaLegado::LiberaFrames(true,true);
        Sessao::setTrataExcecao(true);

        $obErro = new Erro;
        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obREmpenhoNotaLiquidacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ( sistemaLegado::comparaDatas($_REQUEST['stDtLiquidacao'], '31/12/'.Sessao::getExercicio()) ) {
            $obErro->setDescricao("Campo Data de Liquidação deve ser menor que '31/12/".Sessao::getExercicio().".");
        }

        if (SistemaLegado::comparaDatas($_REQUEST["stDtLiquidacao"], date('d/m/Y'))) {
            $obErro->setDescricao("Campo Data de Liquidação deve ser menor ou igual a data de hoje.");
        }

        if ( !$obErro->ocorreu() ) {

            $obREmpenhoEmpenho->setExercicio ( $_REQUEST["dtExercicioEmpenho"] );
            $obREmpenhoEmpenho->setCodEmpenho ( $_REQUEST["inCodEmpenho"] );
            $obREmpenhoEmpenho->setCodPreEmpenho ( $_REQUEST["inCodPreEmpenho"] );
            $obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $_POST['inCodDespesa'] );
            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obErro = $obREmpenhoEmpenho->consultar( $boTransacao );

            if ( !$obErro->ocorreu() ) {

                $obREmpenhoNotaLiquidacao->setExercicio    ( Sessao::getExercicio() );
                $obREmpenhoNotaLiquidacao->setExercRP      ( Sessao::getExercicio() );
                $obREmpenhoNotaLiquidacao->setDtLiquidacao ( $_REQUEST["stDtLiquidacao"] );
                $obREmpenhoNotaLiquidacao->setDtVencimento ( $_REQUEST["dtValidadeFinal"] );
                $obREmpenhoNotaLiquidacao->setObservacao   ( substr( $_REQUEST["stObservacao"], 0 , 400 ) );
                $obREmpenhoNotaLiquidacao->setCodContaContabilFinanc ( $_REQUEST["inCodContaContabilFinanc"] );

                // Definição das contas credito e debito
                $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->setExercicio ( Sessao::getExercicio() );
                isset($_REQUEST["inCodContaDebito"]) AND $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->setCodPlano ( $_REQUEST["inCodContaDebito"] );
                $obErro = $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->consultar( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaCredito->setExercicio ( Sessao::getExercicio() );
                    isset($_REQUEST["inCodContaCredito"]) AND $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaCredito->setCodPlano ( $_REQUEST["inCodContaCredito"] );
                    $obErro = $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaCredito->consultar( $boTransacao );
                }

                isset($_REQUEST["inCodHistoricoPatrimon"]) AND $obREmpenhoNotaLiquidacao->setCodHistorico( $_REQUEST["inCodHistoricoPatrimon"] );
                isset($_REQUEST["stComplemento"]) AND $obREmpenhoNotaLiquidacao->setComplemento( $_REQUEST["stComplemento"] );

                $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_POST['inNumOrgao'] );

                foreach ($_POST as $stChave => $stValor) {
                    if ( strstr( $stChave, "nuValor" ) ) {
                        $arCampoItem = explode( "_", $stChave );
                        $arItemPreEmpenho[$arCampoItem[1]] = $stValor;
                    }
                }

                if (isset($arItemPreEmpenho)) {
                    for ( $inContItens = 0; $inContItens <  count( $obREmpenhoEmpenho->arItemPreEmpenho ); $inContItens++ ) {
                        $obREmpenhoEmpenho->arItemPreEmpenho[$inContItens]->setValorALiquidar( $arItemPreEmpenho[$obREmpenhoEmpenho->arItemPreEmpenho[$inContItens]->getNumItem()] );
                    }
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obREmpenhoNotaLiquidacao->incluir( $boTransacao ); 
        }

        if (!$obErro->ocorreu()) {
            $obAdministracaoConfiguracao = new TAdministracaoConfiguracao;
            $obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.parametro = 'seta_tipo_documento_liq_tceam' AND exercicio = '".Sessao::getExercicio()."'");
            $boIncluirDocumento = $rsAdministracaoConfiguracao->getCampo('valor');

            //se for prefeitura do Rio Grande do Norte, inclui as informações da nota fiscal
            if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 20 ) {
                if ($request->get('stNumeroNF') || $request->get('stSerieNF') || $request->get('stCodValidacaoNF') || $request->get('stCodValidacaoNF') || $request->get('stModeloNF')) {
                    include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNNotaFiscal.class.php';
                    $obTTCERNNotaFiscal = new TTCERNNotaFiscal;
                    $obTTCERNNotaFiscal->setDado('cod_nota_liquidacao', $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCERNNotaFiscal->setDado('cod_entidade'       , $_POST['inCodEntidade']);
                    $obTTCERNNotaFiscal->setDado('exercicio'          , Sessao::getExercicio());
                    $obTTCERNNotaFiscal->setDado('nro_nota'           , $request->get('stNumeroNF',''));
                    $obTTCERNNotaFiscal->setDado('nro_serie'          , $request->get('stSerieNF',''));
                    $obTTCERNNotaFiscal->setDado('data_emissao'       , $request->get('stDtEmissaoNF',''));
                    $obTTCERNNotaFiscal->setDado('cod_validacao'      , $request->get('stCodValidacaoNF',''));
                    $obTTCERNNotaFiscal->setDado('modelo'             , $request->get('stModeloNF',''));
                    $obTTCERNNotaFiscal->inclusao( $boTransacao );
                }
            }
            
             //se for prefeitura de Alagoas, inclui as informações de documento
            if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao ) == 02 ) { 
                include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALDocumento.class.php';
                $obTTCEALDocumento = new TTCEALDocumento;
                
                if ( $_POST['inCodTipoDocumento'] == 1 OR $_POST['inCodTipoDocumento'] == 6) {
                    $obTTCEALDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCEALDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCEALDocumento->setDado('descricao'      , $_POST['stDescricao']);
                    $obTTCEALDocumento->setDado('autorizacao'    , $_POST['stAutorizacao']);
                    $obTTCEALDocumento->setDado('modelo'         , $_POST['stModelo']);
                    if($_POST['inCodTipoDocumento'] == 6)
                        $obTTCEALDocumento->setDado('nro_xml_nfe', $_REQUEST['stNumXmlNFe']);
                    $obTTCEALDocumento->inclusao( $boTransacao );
                }
                
                if ( $_POST['inCodTipoDocumento'] == 7 ) {
                    $obTTCEALDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCEALDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCEALDocumento->setDado('descricao'      , $_POST['stDescricao']);
                    if(!empty($_POST['stAutorizacao'])){
                        $obTTCEALDocumento->setDado('autorizacao'    , $_POST['stAutorizacao']);
                    }
                    if(!empty($_POST['stModelo'])){
                        $obTTCEALDocumento->setDado('modelo'         , $_POST['stModelo']);
                    }
                    
                    $obTTCEALDocumento->inclusao( $boTransacao );
                }
                
                if ( $_POST['inCodTipoDocumento'] == 2 OR $_POST['inCodTipoDocumento'] == 3 OR $_POST['inCodTipoDocumento'] == 4 OR $_POST['inCodTipoDocumento'] == 5 ) {
                    
                    $obTTCEALDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCEALDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCEALDocumento->setDado('descricao'      , $_POST['stDescricao']);
                    $obTTCEALDocumento->inclusao( $boTransacao );
                
                }
                
                if ( $_POST['inCodTipoDocumento'] == 9 ) {
                    $obTTCEALDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCEALDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCEALDocumento->setDado('descricao'      , $_POST['stDescricao']);
                     if(!empty($_POST['stAutorizacao'])){
                        $obTTCEALDocumento->setDado('autorizacao'    , $_POST['stAutorizacao']);
                    }
                    if(!empty($_POST['stModelo'])){
                        $obTTCEALDocumento->setDado('modelo'         , $_POST['stModelo']);
                    }
                    $obTTCEALDocumento->inclusao( $boTransacao );
                }
            }
            
             //se for prefeitura de Tocantins, inclui as informações de documento
            if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 27 ) {
                include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETONotaLiquidacaoDocumento.class.php';
                $obTTCETONotaLiquidacaoDocumento = new TTCETONotaLiquidacaoDocumento;
                
                if ( $_POST['inCodTipoDocumento'] == 1 OR $_POST['inCodTipoDocumento'] == 6) {
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $_POST['stDescricao']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('autorizacao'    , $_POST['stAutorizacao']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('modelo'         , $_POST['stModelo']);
                    if($_POST['inCodTipoDocumento'] == 6)
                        $obTTCETONotaLiquidacaoDocumento->setDado('nro_xml_nfe', $_REQUEST['stNumXmlNFe']);
                    $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                }
                
                if ( $_POST['inCodTipoDocumento'] == 7 ) {
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $_POST['stDescricao']);
                    if(!empty($_POST['stAutorizacao'])){
                        $obTTCETONotaLiquidacaoDocumento->setDado('autorizacao'    , $_POST['stAutorizacao']);
                    }
                    if(!empty($_POST['stModelo'])){
                        $obTTCETONotaLiquidacaoDocumento->setDado('modelo'         , $_POST['stModelo']);
                    }
                    
                    $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                }
                
                if ( $_POST['inCodTipoDocumento'] == 2 OR $_POST['inCodTipoDocumento'] == 3 OR $_POST['inCodTipoDocumento'] == 4 OR $_POST['inCodTipoDocumento'] == 5 ) {
                    
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $_POST['stDescricao']);
                    $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                
                }
                
                if ( $_POST['inCodTipoDocumento'] == 9 ) {
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $_POST['dtDocumento']);
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $_POST['stDescricao']);
                     if(!empty($_POST['stAutorizacao'])){
                        $obTTCETONotaLiquidacaoDocumento->setDado('autorizacao'    , $_POST['stAutorizacao']);
                    }
                    if(!empty($_POST['stModelo'])){
                        $obTTCETONotaLiquidacaoDocumento->setDado('modelo'         , $_POST['stModelo']);
                    }
                    $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                }
            }
            
            // Se for prefeitura do amazonas, será incluído o tipo de documento
            if ($boIncluirDocumento == 'true') {
                if ($_POST['inCodTipoDocumento'] != "") {
                    include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMDocumento.class.php';
                    $obTTCEAMDocumento = new TTCEAMDocumento;
                    $obTTCEAMDocumento->proximoCod($inCodDocumento);
                    $obTTCEAMDocumento->setDado('cod_documento'  , $inCodDocumento);
                    $obTTCEAMDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCEAMDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEAMDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCEAMDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEAMDocumento->setDado('vl_comprometido', $_POST['nuValorComprometido']);
                    $obTTCEAMDocumento->setDado('vl_total'       , $_POST['nuValorTotal']);
                    $obTTCEAMDocumento->inclusao( $boTransacao );

                    if ($_REQUEST['inCodTipoDocumento'] == 1) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoBilhete.class.php';
                        $obTTCEAMTipoDocumentoBilhete = new TTCEAMTipoDocumentoBilhete;
                        $obTTCEAMTipoDocumentoBilhete->proximoCod($inCodTipoDocumentoBilhete);
                        $obTTCEAMTipoDocumentoBilhete->setDado('cod_tipo_documento_bilhete', $inCodTipoDocumentoBilhete);
                        $obTTCEAMTipoDocumentoBilhete->setDado('cod_documento'             , $inCodDocumento);
                        $obTTCEAMTipoDocumentoBilhete->setDado('numero'                    , $_POST['stNumero']);
                        $obTTCEAMTipoDocumentoBilhete->setDado('dt_emissao'                , $_POST['dtEmissao']);
                        $obTTCEAMTipoDocumentoBilhete->setDado('dt_saida'                  , $_POST['dtSaida']);
                        $obTTCEAMTipoDocumentoBilhete->setDado('hora_saida'                , $_POST['hrSaida']);
                        $obTTCEAMTipoDocumentoBilhete->setDado('destino'                   , $_POST['stDestino']);
                        $obTTCEAMTipoDocumentoBilhete->setDado('dt_chegada'                , $_POST['dtChegada']);
                        $obTTCEAMTipoDocumentoBilhete->setDado('hora_chegada'              , $_POST['hrChegada']);
                        $obTTCEAMTipoDocumentoBilhete->setDado('motivo'                    , $_POST['stMotivo']);
                        $obTTCEAMTipoDocumentoBilhete->inclusao( $boTransacao );

                    } elseif ($_REQUEST['inCodTipoDocumento'] == 2) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoDiaria.class.php';
                        $obTTCEAMTipoDocumentoDiaria = new TTCEAMTipoDocumentoDiaria;
                        $obTTCEAMTipoDocumentoDiaria->proximoCod($inCodTipoDocumentoDiaria);
                        $obTTCEAMTipoDocumentoDiaria->setDado('cod_tipo_documento_diaria', $inCodTipoDocumentoDiaria);
                        $obTTCEAMTipoDocumentoDiaria->setDado('cod_documento'            , $inCodDocumento);
                        $obTTCEAMTipoDocumentoDiaria->setDado('funcionario'              , $_POST['stFuncionario']);
                        $obTTCEAMTipoDocumentoDiaria->setDado('matricula'                , $_POST['stMatricula']);
                        $obTTCEAMTipoDocumentoDiaria->setDado('dt_saida'                 , $_POST['dtSaida']);
                        $obTTCEAMTipoDocumentoDiaria->setDado('hora_saida'               , $_POST['hrSaida']);
                        $obTTCEAMTipoDocumentoDiaria->setDado('destino'                  , $_POST['stDestino']);
                        $obTTCEAMTipoDocumentoDiaria->setDado('dt_retorno'               , $_POST['dtRetorno']);
                        $obTTCEAMTipoDocumentoDiaria->setDado('hora_retorno'             , $_POST['hrRetorno']);
                        $obTTCEAMTipoDocumentoDiaria->setDado('motivo'                   , $_POST['stMotivo']);
                        $obTTCEAMTipoDocumentoDiaria->inclusao( $boTransacao );

                    } elseif ($_REQUEST['inCodTipoDocumento'] == 3) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoDiverso.class.php';
                        $obTTCEAMTipoDocumentoDiverso = new TTCEAMTipoDocumentoDiverso;
                        $obTTCEAMTipoDocumentoDiverso->proximoCod($inCodTipoDocumentoDiverso);
                        $obTTCEAMTipoDocumentoDiverso->setDado('cod_tipo_documento_diverso', $inCodTipoDocumentoDiverso);
                        $obTTCEAMTipoDocumentoDiverso->setDado('cod_documento'             , $inCodDocumento);
                        $obTTCEAMTipoDocumentoDiverso->setDado('numero'                    , $_POST['stNumero']);
                        $obTTCEAMTipoDocumentoDiverso->setDado('data'                      , $_POST['dtDiverso']);
                        $obTTCEAMTipoDocumentoDiverso->setDado('descricao'                 , $_POST['stDescricao']);
                        $obTTCEAMTipoDocumentoDiverso->setDado('nome_documento'            , $_POST['stNomeDocumento']);
                        $obTTCEAMTipoDocumentoDiverso->inclusao( $boTransacao );

                    } elseif ($_REQUEST['inCodTipoDocumento'] == 4) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoFolha.class.php';
                        $obTTCEAMTipoDocumentoFolha = new TTCEAMTipoDocumentoFolha;
                        $obTTCEAMTipoDocumentoFolha->proximoCod($inCodTipoDocumentoFolha);
                        $obTTCEAMTipoDocumentoFolha->setDado('cod_tipo_documento_folha', $inCodTipoDocumentoFolha);
                        $obTTCEAMTipoDocumentoFolha->setDado('cod_documento'           , $inCodDocumento);
                        $obTTCEAMTipoDocumentoFolha->setDado('mes'                     , $_POST['inMes']);
                        $obTTCEAMTipoDocumentoFolha->setDado('exercicio'               , $_POST['stExercicio']);
                        $obTTCEAMTipoDocumentoFolha->inclusao( $boTransacao );

                    } elseif ($_REQUEST['inCodTipoDocumento'] == 5) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoNota.class.php';
                        $obTTCEAMTipoDocumentoNota = new TTCEAMTipoDocumentoNota;
                        $obTTCEAMTipoDocumentoNota->proximoCod($inCodTipoDocumentoNota);
                        $obTTCEAMTipoDocumentoNota->setDado('cod_tipo_documento_nota', $inCodTipoDocumentoNota);
                        $obTTCEAMTipoDocumentoNota->setDado('cod_documento'          , $inCodDocumento);
                        $obTTCEAMTipoDocumentoNota->setDado('numero_nota_fiscal'     , $_POST['stNumeroNotaFiscal']);
                        $obTTCEAMTipoDocumentoNota->setDado('numero_serie'           , $_POST['stNumeroSerie']);
                        $obTTCEAMTipoDocumentoNota->setDado('numero_subserie'        , $_POST['stNumeroSubserie']);
                        $obTTCEAMTipoDocumentoNota->setDado('data'                   , $_POST['dtNota']);
                        $obTTCEAMTipoDocumentoNota->inclusao( $boTransacao );

                    } elseif ($_REQUEST['inCodTipoDocumento'] == 6) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoRecibo.class.php';
                        $obTTCEAMTipoDocumentoRecibo = new TTCEAMTipoDocumentoRecibo;
                        $obTTCEAMTipoDocumentoRecibo->proximoCod($inCodTipoDocumentoRecibo);
                        $obTTCEAMTipoDocumentoRecibo->setDado('cod_tipo_documento_recibo', $inCodTipoDocumentoRecibo);
                        $obTTCEAMTipoDocumentoRecibo->setDado('cod_documento'            , $inCodDocumento);
                        $obTTCEAMTipoDocumentoRecibo->setDado('cod_tipo_recibo'          , $_POST['inCodTipoRecibo']);
                        $obTTCEAMTipoDocumentoRecibo->setDado('numero'                   , $_POST['stNumero']);
                        $obTTCEAMTipoDocumentoRecibo->setDado('valor'                    , $_POST['nuValor']);
                        $obTTCEAMTipoDocumentoRecibo->setDado('data'                     , $_POST['dtRecibo']);
                        $obTTCEAMTipoDocumentoRecibo->inclusao( $boTransacao );

                    }
                }
            }
            
             //se for prefeitura de Pernambuco, inclui as informações de documento
            if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16 ) { 
                include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDocumento.class.php';
                $obTTCEPEDocumento = new TTCEPEDocumento;
                
                if ( $_POST['inCodTipoDocumento'] == 1) {
                    $obTTCEPEDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCEPEDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEPEDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCEPEDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEPEDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCEPEDocumento->setDado('serie'          , $_POST['inSerieDocumento']);
                    $obTTCEPEDocumento->setDado('cod_uf'         , $_POST['stUfDocumento']);
                    $obTTCEPEDocumento->inclusao( $boTransacao );
                }
                
                if ( $_POST['inCodTipoDocumento'] == 9 ) {
                    $obTTCEPEDocumento->setDado('cod_tipo'       , $_POST['inCodTipoDocumento']);
                    $obTTCEPEDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEPEDocumento->setDado('cod_entidade'   , $_POST['inCodEntidade']);
                    $obTTCEPEDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEPEDocumento->setDado('nro_documento'  , $_POST['inNumeroDocumento']);
                    $obTTCEPEDocumento->setDado('serie'          , $_POST['inSerieDocumento']);
                    $obTTCEPEDocumento->setDado('cod_uf'         , $_POST['stUfDocumento']);
                    $obTTCEPEDocumento->inclusao( $boTransacao );
                }
            }
            
            //se for prefeitura de Minas Gerais, inclui as informações de documento fiscal
            if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11 && $_REQUEST['stIncluirNF']=="Sim") {
                include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php";
                include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscalEmpenhoLiquidacao.class.php";
                
                $obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;
                $obTTCEMGNotaFiscal->setDado( 'exercicio'                   , $_REQUEST['stExercicioNF']       	    );
                $obTTCEMGNotaFiscal->proximoCod($inCodNota);
    
                $obTTCEMGNotaFiscal->setDado( 'cod_nota'                    , $inCodNota                            );
                $obTTCEMGNotaFiscal->setDado( 'cod_entidade'                , $_REQUEST['inCodEntidade']            );
                $obTTCEMGNotaFiscal->setDado( 'data_emissao'                , $_REQUEST['dtEmissaoNF']              );
                $obTTCEMGNotaFiscal->setDado( 'cod_tipo'                    , $_REQUEST['inCodTipoNota']            );
                if ($_REQUEST['inNumeroNF'] != '') {
                    $obTTCEMGNotaFiscal->setDado('nro_nota'                 , $_REQUEST['inNumeroNF']               );
                }
                if ($_REQUEST['inNumSerie'] != '') {
                    $obTTCEMGNotaFiscal->setDado('nro_serie'                , $_REQUEST['inNumSerie']               );
                }
                if ($_REQUEST['stAIFD'] != '') {
                    $obTTCEMGNotaFiscal->setDado('aidf'                     , $_REQUEST['stAIFD']                   );
                }
                if ($_REQUEST['inNumInscricaoMunicipal'] != '') {
                    $obTTCEMGNotaFiscal->setDado('inscricao_municipal'      , $_REQUEST['inNumInscricaoMunicipal']  );
                }
                if ($_REQUEST['inNumInscricaoEstadual'] != '') {
                    $obTTCEMGNotaFiscal->setDado('inscricao_estadual'       , $_REQUEST['inNumInscricaoEstadual']   );
                }
                if ($_REQUEST['inChave']) {
                    $obTTCEMGNotaFiscal->setDado ( 'chave_acesso'           , $_REQUEST['inChave']                  );
                }
                if ($_REQUEST['inChaveMunicipal']) {
                    $obTTCEMGNotaFiscal->setDado ( 'chave_acesso_municipal' , $_REQUEST['inChaveMunicipal']         );
                }
    
                $nuVlTotalDoctoFiscal = str_replace('.', '' , $_REQUEST['nuTotalNf']);
                $nuVlTotalDoctoFiscal = str_replace(',', '.', $nuVlTotalDoctoFiscal);
    
                $nuVlDescontoDoctoFiscal = str_replace('.', '' , $_REQUEST['nuVlDesconto']);
                $nuVlDescontoDoctoFiscal = str_replace(',', '.', $nuVlDescontoDoctoFiscal);
    
                $obTTCEMGNotaFiscal->setDado( 'vl_total'        , (float)$nuVlTotalDoctoFiscal);
                $obTTCEMGNotaFiscal->setDado( 'vl_desconto'     , (float)$nuVlDescontoDoctoFiscal);
                $obTTCEMGNotaFiscal->setDado( 'vl_total_liquido', (float)$nuVlTotalDoctoFiscal - (float)$nuVlDescontoDoctoFiscal );
    
                $obErro = $obTTCEMGNotaFiscal->inclusao( $boTransacao );
                
                if (!$obErro->ocorreu()) {
                    $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;
    
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota'             , $inCodNota                    );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio'            , $_REQUEST['stExercicioNF']    );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_entidade'         , $_REQUEST['inCodEntidade']    );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_empenho'          , $_REQUEST['inCodEmpenho']     );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_empenho'    , $_REQUEST['dtExercicioEmpenho']           );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota_liquidacao'  , $obREmpenhoNotaLiquidacao->getCodNota()   );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_liquidacao' , Sessao::getExercicio()        );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_associado'         , $nuVlTotalDoctoFiscal         );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_liquidacao'        , $nuVlTotalDoctoFiscal         );

                    $obErro = $obTTCEMGNotaFiscalEmpenho->inclusao($boTransacao);
                }
            }
        }
        
        //se for prefeitura de Bahia, inclui as informações de documento fiscal
        if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 5 ) { 
            include_once(CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTCMBANotaFiscalLiquidacao.class.php");
            $obTTCMBANotaFiscalLiquidacao = new TTCMBANotaFiscalLiquidacao();
            
            $obTTCMBANotaFiscalLiquidacao->setDado('cod_nota_liquidacao'  , $obREmpenhoNotaLiquidacao->getCodNota() );
            $obTTCMBANotaFiscalLiquidacao->setDado('exercicio_liquidacao' , $obREmpenhoNotaLiquidacao->getExercicio() );
            $obTTCMBANotaFiscalLiquidacao->setDado('cod_entidade'         , $request->get('inCodEntidade') );

            if ( $request->get('stAnoNotaFiscal') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('ano' , $request->get('stAnoNotaFiscal') );                
            }
            if ( $request->get('stNumeroNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('nro_nota' , $request->get('stNumeroNF') );                
            }
            if ( $request->get('stSerieNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('nro_serie', $request->get('stSerieNF') );                
            }
            if ( $request->get('stSubSerieNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('nro_subserie', $request->get('stSubSerieNF') );                
            }
            if ( $request->get('stDtEmissaoNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('data_emissao', $request->get('stDtEmissaoNF') );                
            }
            if ( $request->get('nuValorNotaFiscal') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('vl_nota', $request->get('nuValorNotaFiscal') );                
            }
            if ( $request->get('stObjetoNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('descricao', $request->get('stObjetoNF') );                
            }
            if ( $request->get('stUFUnidadeFederacao') ) {
                $inCodUF = SistemaLegado::pegaDado("cod_uf","sw_uf"," WHERE sigla_uf = '".$request->get('stUFUnidadeFederacao')."'");
                $obTTCMBANotaFiscalLiquidacao->setDado('cod_uf', $inCodUF );                
            }

            $obErro = $obTTCMBANotaFiscalLiquidacao->inclusao($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            /* Salvar assinaturas configuráveis se houverem */
            $arAssinaturas = Sessao::read('assinaturas');
            if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {
                include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoAssinatura.class.php" );
                $arAssinatura = $arAssinaturas['selecionadas'];
                $obTEmpenhoNotaLiquidacaoAssinatura = new TEmpenhoNotaLiquidacaoAssinatura;
                $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'cod_entidade', $_POST['inCodEntidade'] );
                $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'cod_nota', $obREmpenhoNotaLiquidacao->getCodNota() );

                $boInserir = 'true';
                $inCount = 0;
                $arAssinaInseridos = array();
                $arAssinaturaTemp = array_reverse($arAssinatura);
                foreach ($arAssinaturaTemp as $arAssina) {

                    foreach ($arAssinaInseridos as $inTemp => $inCGMTemp) {
                        if ($arAssina['inCGM'] != $inCGMTemp) {
                                    $boInserir = 'true';
                        } else {
                            $boInserir = 'false';
                            break;
                        }
                    }
                    if ($boInserir == 'true') {
                        $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'num_assinatura', 0 );
                        $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                        $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                        $obErro = $obTEmpenhoNotaLiquidacaoAssinatura->inclusao( $boTransacao );
                        $arAssinaInseridos[$inCount] = $arAssina['inCGM'];
                    }
                    $inCount++;
                }
                unset($obTEmpenhoNotaLiquidacaoAssinatura);
            }
        }
        
        if ( !$obErro->ocorreu() ) {

            if ($_REQUEST['stEmitirEmpenho']) {
                if ($_REQUEST['boEmitirOP'] == "S") {
                   $pgProx = CAM_GF_EMP_INSTANCIAS."ordemPagamento/FMManterOrdemPagamento.php";
                    $stFiltroEmissaoOP  = "&acao=816&modulo=10&funcionalidade=203";
                    $stFiltroEmissaoOP .= "&inCodEntidade=".$_REQUEST["inCodEntidade"]."&inCodEmpenho=".$_REQUEST["inCodEmpenho"];
                    $stFiltroEmissaoOP .= "&dtExercicioEmpenho=".$_REQUEST["dtExercicioEmpenho"];
                    $stFiltroEmissaoOP .= "&inCodNota=".$obREmpenhoNotaLiquidacao->getCodNota()."&stExercicioNota=".Sessao::getExercicio();
                    $stFiltroEmissaoOP .= "&stLiq=1";
                    $stFiltroEmissaoOP .= "&dtDataVencimento=".$_REQUEST['dtValidadeFinal'];
                    $stFiltroEmissaoOP .= "&stEmitirEmpenho=S";
                    $stFiltroEmissaoOP .= "&pgProxEmpenho=".$pgProxEmpenho."&stAcaoEmpenho=".$stAcaoEmpenho;
                    $stFiltroEmissaoOP .= "&stAcaoLiquidacao=liquidar";
                    $stFiltroEmissaoOP .= "&pgProxLiquidacao=".$pgProxLiquidacao[0];
                    $stFiltroEmissaoOP .= "&acaoEmpenho=".$acaoEmpenho."&moduloEmpenho=".$moduloEmpenho."&funcionalidadeEmpenho=".$funcionalidadeEmpenho;
                    $stFiltroEmissaoOP .= "&acaoLiquidacao=812&moduloLiquidacao=10&funcionalidadeLiquidacao=202";
                    $stFiltroEmissaoOP .= "&pgDespesasFixas=".$_REQUEST['pgDespesasFixas'];
                    print '<script type="text/javascript">
                                mudaMenu         ( "Ordem de Pagamento","203" );
                           </script>';

                    SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=incluir".$stFiltroEmissaoOP,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","", "aviso",Sessao::getId(), "../");

                } else {
                    print '<script type="text/javascript">
                                mudaMenu         ( "Empenho","82" );
                           </script>';
                    $stFiltro  = "&acao=".$acaoEmpenho."&cod_gestao_pass=2&stNomeGestao=Financeira&modulos=Empenho&modulo=".$moduloEmpenho."&funcionalidade=".$funcionalidadeEmpenho."&nivel=1&acaoLiquidar=812";

                    if ($_REQUEST['pgDespesasFixas'] != "") {
                        $pgProx = CAM_GF_EMP_INSTANCIAS."empenho/FMManterDespesasMensaisFixas.php";
                    } else {
                        $pgProx = $pgProxEmpenho;
                    }

                   SistemaLegado::alertaAviso($pgProx.'?'."stAcao=".$stAcaoEmpenho.$stFiltro,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","","aviso",Sessao::getId(), "../");

                }

            } else {
                if ($_REQUEST['boEmitirOP'] == "S") {
                    $pgProx = CAM_GF_EMP_INSTANCIAS."ordemPagamento/FMManterOrdemPagamento.php";
                    $stFiltroEmissaoOP  = "&acao=816&modulo=10&funcionalidade=203";
                    $stFiltroEmissaoOP .= "&inCodEntidade=".$_REQUEST["inCodEntidade"]."&inCodEmpenho=".$_REQUEST["inCodEmpenho"];
                    $stFiltroEmissaoOP .= "&dtExercicioEmpenho=".$_REQUEST["dtExercicioEmpenho"];
                    $stFiltroEmissaoOP .= "&stLiq=1";
                    $stFiltroEmissaoOP .= "&dtDataVencimento=".$_REQUEST['dtValidadeFinal'];
                    $stFiltroEmissaoOP .= "&inCodNota=".$obREmpenhoNotaLiquidacao->getCodNota()."&stExercicioNota=".Sessao::getExercicio();
                    $stFiltroEmissaoOP .= "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos');
                    $stFiltroEmissaoOP .= "&stAcaoLiquidacao=liquidar";
                    $stFiltroEmissaoOP .= "&acaoLiquidacao=812&moduloLiquidacao=10&funcionalidadeLiquidacao=202";
                    $stFiltroEmissaoOP .= "&pgProxLiquidacao=".$pgProxLiquidacao[0];
                    print '<script type="text/javascript">
                                mudaMenu         ( "Ordem de Pagamento","203" );
                           </script>';

                  SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=incluir".$stFiltroEmissaoOP,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","","aviso",Sessao::getId(),"../");

                 } else {
                 SistemaLegado::alertaAviso($pgList."?"."&stAcao=liquidar".$stFiltro,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","","aviso",Sessao::getId(), "../");
                }
            }
            
            Sessao::write('assinaturasPdf', $arAssinaturas);

            Sessao::geraURLRandomica();
            
            $stCaminho = CAM_GF_EMP_INSTANCIAS."liquidacao/OCRelatorioNotaLiquidacaoEmpenho.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodNota=".$obREmpenhoNotaLiquidacao->getCodNota()."&stDtLiquidacao=".$obREmpenhoNotaLiquidacao->getDtLiquidacao()."&acao=812";
            $stCampos .= "&inCodEntidade=" .$_POST['inCodEntidade']."&boImplantado=".$obREmpenhoEmpenho->getImplantado()."&stExercicioNota=".Sessao::getExercicio()."&dtExercicioEmpenho=".$_REQUEST["dtExercicioEmpenho"];
            SistemaLegado::executaFrameOculto( "var x = setTimeout('window.open(\'".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."\',\'oculto\');',50);" );
       } else {
            $stErro = $obErro->getDescricao();
            if (strpos(strtolower($stErro),"context") > 0) {
                $stErro = substr($stErro,0,strpos(strtolower($stErro),"context:"));
                $stErro = $stErro." Erro Auditado! ";
            }
            if (strpos(strtolower($stErro),"erro") === 0) {
                $stErro = substr($stErro,6);
            }
            SistemaLegado::exibeAviso(urlencode($stErro),"n_incluir","erro");
            
            SistemaLegado::liberaFrames(true,true);
       }
       
       Sessao::encerraExcecao();

    break;
}
