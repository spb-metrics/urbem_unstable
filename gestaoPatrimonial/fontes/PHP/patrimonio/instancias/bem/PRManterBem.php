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

 * Data de Criação: 12/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: PRManterBem.php 63088 2015-07-23 17:04:56Z arthur $

 * Casos de uso: uc-03.01.06
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioApoliceBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioReavaliacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemComprado.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemResponsavel.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemMarca.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemPlanoAnalitica.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemPlanoDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacaoReavaliacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioInventarioHistoricoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioHistoricoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencaoPaga.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacaoAnulada.class.php";
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALBemCompradoTipoDocumentoFiscal.class.php';
include_once TTGO.'TTGOPatrimonioBemObra.class.php';
include_once(CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemProcesso.class.php");


$stPrograma = "ManterBem";
$pgFilt	    = "FL".$stPrograma.".php";
$pgList	    = "LS".$stPrograma.".php";
$pgForm	    = "FM".$stPrograma.".php";
$pgProc	    = "PR".$stPrograma.".php";
$pgOcul	    = "OC".$stPrograma.".php";
$pgJs	    = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioBem 		       = new TPatrimonioBem();
$obTPatrimonioReavaliacao	       = new TPatrimonioReavaliacao();
$obTPatrimonioDepreciacao	       = new TPatrimonioDepreciacao();
$obTPatrimonioBemPlanoAnalitica        = new TPatrimonioBemPlanoAnalitica;
$obTPatrimonioBemResponsavel           = new TPatrimonioBemResponsavel();
$obTPatrimonioHistoricoBem             = new TPatrimonioHistoricoBem();
$obTPatrimonioApoliceBem               = new TPatrimonioApoliceBem();
$obTPatrimonioBemAtributoEspecie       = new TPatrimonioBemAtributoEspecie();
$obTPatrimonioBemComprado              = new TPatrimonioBemComprado();
$obTPatrimonioManutencao               = new TPatrimonioManutencao();
$obTPatrimonioManutencaoPaga           = new TPatrimonioManutencaoPaga();
$obTAdministracaoConfiguracao          = new TAdministracaoConfiguracao;
$obTPatrimonioBemMarca                 = new TPatrimonioBemMarca();
$obTPatrimonioInventarioHistoricoBem   = new TPatrimonioInventarioHistoricoBem();
$obTPatrimonioBemPlanoDepreciacao      = new TPatrimonioBemPlanoDepreciacao();
$obTPatrimonioBemProcesso              = new TPatrimonioBemProcesso();

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTPatrimonioBem );

switch ($stAcao) {
    case 'lote' :
    case 'incluir':
        $identificacao = ( $_REQUEST['stPlacaIdentificacao'] == 'sim' ) ? 't' : 'f';
        $inQtdeLote = $_REQUEST['inQtdeLote'];
        $numeroPlaca = $_REQUEST['stNumeroPlaca'];
        $arrayPlacas = array();
        $stMensagem = '';

        if ($identificacao != 'f') {
            for ($i = 0; $i < $inQtdeLote; $i++) {
            //verifica se o numero da placa já existe
            $stFiltro = " WHERE num_placa = '".$numeroPlaca."' ";
            $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );
            if ( $rsBem->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existem bens com placas no intervalo selecionado, escolha um novo intervalo';
                breal;
            } else {
                $arrayPlacas[] = $numeroPlaca;
                $numeroPlaca++;
            }
            }
        }

        if ($stMensagem == '') {
            // Verifica a integridade dos valores
            if( $_REQUEST['stNumNotaFiscal'] != '' && empty($_REQUEST['dataNotaFiscal']) ){
                $stMensagem = 'O campo Data da Nota Fiscal deve ser preenchido';
             }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['dataNotaFiscal'] != '' ){
                $stMensagem = 'O campo Data da Nota Fiscal não deve ser preenchido, quando não houver  um Número da Nota Fiscal';
             }
             
            if ($_REQUEST['inValorBem'] == '0,00') {
                $stMensagem = 'Valor do bem inválido';
            } elseif (!empty($_REQUEST['inValorDepreciacao']) && (str_replace(",",".",str_replace(".", "", $_REQUEST['inValorDepreciacao'])) > str_replace(",",".",str_replace(".", "", $_REQUEST['inValorBem'])))) {
                $stMensagem = 'O valor da Depreciação Inicial não pode ser maior que o valor do bem.';
            } elseif ( $_REQUEST['dtDepreciacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtDepreciacao'])) ) {
                $stMensagem = 'A data de depreciação deve ser maior ou igual a data de aquisição';
            } elseif ( $_REQUEST['dtVencimento'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtVencimento'])) ) {
                $stMensagem = 'A data de vencimento da garantia deve ser maior ou igual a data de aquisição';
            } elseif ( $_REQUEST['dtIncorporacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtIncorporacao'])) ) {
                $stMensagem = 'A data de incorporação deve ser maior ou igual a data de aquisição';
            } elseif ($_REQUEST['stPlacaIdentificacao'] == 'sim' AND $_REQUEST['stNumeroPlaca'] == '') {
                $stMensagem = 'Número da placa inválido';
            } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) < implode('',array_reverse(explode('/',$_REQUEST['dtAquisicao']))) ) {
                $stMensagem = 'A data de início do responsável deve ser maior ou igual a data de aquisição';
            } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) > date('Ymd') ) {
                $stMensagem = 'A data de início do responsável deve ser menor ou igual a data de hoje';
            } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodSeguradora'] == '') {
                $stMensagem = 'Selecione uma seguradora';
            } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodApolice'] == '') {
                $stMensagem = 'Selecione uma apólice';
            } elseif ($_REQUEST['inQtdeLote'] <= 0) {
                $stMensagem = 'A Quantidade deve ser maior que zero';
            } elseif ($_REQUEST['boDepreciacaoAcelerada'] == 'true' && (empty($_REQUEST['flQuotaDepreciacaoAcelerada']) || $_REQUEST['flQuotaDepreciacaoAcelerada'] == '0,00')) {
                $stMensagem = 'O valor da quota acelerada deve ser informado e maior que zero';
            }elseif($boDepreciavel === 'true' && !empty($_REQUEST['inCodContaDepreciacao']) && ($_REQUEST['flQuotaDepreciacaoAnual'] == '0,00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '0.00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '')){
                $stMensagem = 'O valor da quota de depreciação Anual deve ser maior que zero';
            }elseif(empty($_REQUEST['inCodContaDepreciacao']) && isset($_REQUEST['flQuotaDepreciacaoAnual']) && $_REQUEST['flQuotaDepreciacaoAnual'] != '0,00' && $_REQUEST['flQuotaDepreciacaoAnual'] != ''){
                $stMensagem = 'O valor da Conta Contábil de Depreciação Acumulada deve ser informado.';
            }elseif(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02 ){
                if($_REQUEST['stNumNotaFiscal'] != '' and  $_REQUEST['inCodTipoDocFiscal'] == ''){
                    $stMensagem = 'Informe o Tipo do Documento Fiscal';    
                }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['inCodTipoDocFiscal'] != '' ){
                    $stMensagem = 'O campo Tipo do Documento Fiscal não deve ser preenchido, quando não houver  um Número da Nota Fiscal';
                }
            }else {
                $rsAtributosDinamicos = Sessao::read('rsAtributosDinamicos');
            if (is_array( $rsAtributosDinamicos->arElementos ) ) {
                $rsAtributosDinamicos->arElementos = array_reverse($rsAtributosDinamicos->arElementos);
                while ( !$rsAtributosDinamicos->eof() ) {
                if ( $rsAtributosDinamicos->getCampo('nao_nulo') == 'f' AND $_REQUEST['Atributo_'.$rsAtributosDinamicos->getCampo('cod_atributo').'_'.$rsAtributosDinamicos->getCampo('cod_cadastro')] == '' ) {
                    $stMensagem = 'Preencha o campo '.$rsAtributosDinamicos->getCampo('nom_atributo');
                    break;
                }
                $rsAtributosDinamicos->proximo();
                }
            }
            }
        }

        if (!$stMensagem) {
            //loop acrescentado para a inclusão em lote
            //se não for em lote, inclui apenas uma vez
            for ($i = 0; $i < $inQtdeLote; $i++) {
                //inclui na table patrimonio.bem
                $obTPatrimonioBem->proximoCod( $inCodBem );
                //coloca no array os códigos do bem para demonstrar na mensagem
                $arCodBem[] = $inCodBem;
                $obTPatrimonioBem->setDado( 'cod_bem', $inCodBem );
                $obTPatrimonioBem->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                $obTPatrimonioBem->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
                $obTPatrimonioBem->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
                $obTPatrimonioBem->setDado( 'numcgm', $_REQUEST['inCodFornecedor'] );
                $obTPatrimonioBem->setDado( 'descricao', $_REQUEST['stNomBem'] );
                $obTPatrimonioBem->setDado( 'detalhamento', $_REQUEST['stDetalhamentoBem'] );
                $obTPatrimonioBem->setDado( 'dt_aquisicao', $_REQUEST['dtAquisicao'] );
                $obTPatrimonioBem->setDado( 'vida_util', $_REQUEST['inVidaUtil'] );
                $obTPatrimonioBem->setDado( 'dt_incorporacao', $_REQUEST['dtIncorporacao'] );
                $obTPatrimonioBem->setDado( 'dt_depreciacao', $_REQUEST['dtDepreciacao'] );
                $obTPatrimonioBem->setDado( 'dt_garantia', $_REQUEST['dtVencimento'] );
                $obTPatrimonioBem->setDado( 'vl_bem', str_replace(',','.',str_replace('.','',$_REQUEST['inValorBem'])) );
                $obTPatrimonioBem->setDado( 'vl_depreciacao', (float) str_replace(',','.',str_replace('.','',$_REQUEST['inValorDepreciacao'])) );
                $obTPatrimonioBem->setDado( 'identificacao', $identificacao );
                if ($identificacao != 'f') {
                    $obTPatrimonioBem->setDado( 'num_placa', $arrayPlacas[$i] );
                } else {
                    $arEstrutura = $obTPatrimonioBem->getEstrutura();
                    foreach ($arEstrutura as $key => $value) {
                        if ($value->stNomeCampo == 'num_placa') {
                            $keyField = $key;
                            break;
                        }
                    }
                    # Seta provisoriamente o tipo do campo como INTEGER para gravar o valor null ao invés de ''.
                    $arEstrutura[$keyField]->setTipoCampo('integer');
                    $obTPatrimonioBem->setEstrutura($arEstrutura);
                    $obTPatrimonioBem->setDado( 'num_placa', 'null' );
                }

                //Configuração de Depreciação
                $stDepreciacaoAcelerada = $_REQUEST['boDepreciacaoAcelerada'];
                $boDepreciavel          = $_REQUEST['boDepreciavel'];
                $inCodBem               = $inCodBem;
                $inCodPlano             = $_REQUEST['inCodContaAnalitica'];
                $inCodContaDepreciacao  = $_REQUEST['inCodContaDepreciacao'];
                $inExercicio            = Sessao::getExercicio();

                if (!empty($_REQUEST['stChaveProcesso'])) {
                    $arProcesso = array();
                    $arProcesso = explode("/", $_REQUEST['stChaveProcesso']);
                    $inCodProcesso = $arProcesso[0];
                    $stAnoProcesso = $arProcesso[1];    
                } else {
                    $arProcesso = "";
                }

                if ($boDepreciavel === 'true') {
                    $obTPatrimonioBem->setDado( 'depreciavel', true);
                    $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', $_REQUEST['flQuotaDepreciacaoAnual'] );

                    if ($stDepreciacaoAcelerada === "true") {
                        $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'true' );
                        $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', $_REQUEST['flQuotaDepreciacaoAcelerada'] );
                    } else {
                        $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
                        $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
                    }
                } else {
                    $obTPatrimonioBem->setDado( 'depreciavel', 'false');
                    $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
                    $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', '0,00' );
                    $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
                }
                
                $obTPatrimonioBem->inclusao();
                
                if(!empty($_REQUEST['stChaveProcesso'])){
                    $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
                    $obTPatrimonioBemProcesso->setDado('ano_exercicio', $stAnoProcesso);
                    $obTPatrimonioBemProcesso->setDado('cod_processo', $inCodProcesso);
                    $obTPatrimonioBemProcesso->inclusao();
                }
                
                if(!empty($inCodContaDepreciacao)){
                    $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem'  , $inCodBem );
                    $obTPatrimonioBemPlanoDepreciacao->setDado( 'exercicio', $inExercicio);
                    $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_plano', $inCodContaDepreciacao);
                    $obTPatrimonioBemPlanoDepreciacao->inclusao();
                }
                
                if (!empty($inCodPlano)) {
                    $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                    $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
                    $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio', $inExercicio);
                    $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_plano', $inCodPlano);

                    $rsBemPlanoAnalitica = new RecordSet();
                    $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                    if ($rsBemPlanoAnalitica->getCampo('timestamp') == '') {
                        $obTPatrimonioBemPlanoAnalitica->inclusao();
                    }
                    
                } else {
                    $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                    $obTPatrimonioBemPlanoAnalitica->setDado('cod_plano', $inCodPlano);
                    $obTPatrimonioBemPlanoAnalitica->setDado('cod_bem'  , $inCodBem );
                    $obTPatrimonioBemPlanoAnalitica->setDado('exercicio', $inExercicio);

                    $rsBemPlanoAnalitica = new RecordSet();
                    $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                    if ($rsBemPlanoAnalitica->getNumLinhas() > 0) {
                        $obTPatrimonioBemPlanoAnalitica->exclusao();
                    }
                }

                //Insere reavaliações
                $rsReavaliacao = new RecordSet;
                $rsReavaliacao->preenche(Sessao::read('arReavaliacao'));

                while (!$rsReavaliacao->eof() ) {
                    $inCodBem = $inCodBem;
                    $inCodReavaliacao = $rsReavaliacao->getCampo('inCodReavaliacao');
                    $dtReavaliacao = $rsReavaliacao->getCampo('dtReavaliacao');
                    $inVidaUtilReavaliacao = $rsReavaliacao->getCampo('inVidaUtilReavaliacao');
                    $flValorBemReavaliacao = $rsReavaliacao->getCampo('flValorBemReavaliacao');
                    $stMotivoReavaliacao = $rsReavaliacao->getCampo('stMotivoReavaliacao');
                    $inserir = $rsReavaliacao->getCampo('inserir');

                    if ($inCodBem != '' && $inCodReavaliacao === 0 && $dtReavaliacao != '' && $inVidaUtilReavaliacao  != '' && $flValorBemReavaliacao  != '' && $stMotivoReavaliacao != '' && $inserir === 'true') {
                        $obTPatrimonioReavaliacao->proximoCod( $inCodReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'cod_bem', $inCodBem );
                        $obTPatrimonioReavaliacao->setDado( 'dt_reavaliacao', $dtReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'vida_util', $inVidaUtilReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'vl_reavaliacao', $flValorBemReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'motivo', trim($stMotivoReavaliacao) );
                        $obTPatrimonioReavaliacao->inclusao();
                    }
                    $rsReavaliacao->proximo();
                }
              
                //inclui na table patrimonio.bem_comprado
                $arMontaCodOrgaoM = explode("-", $_REQUEST['inMontaCodOrgaoM']);
                $arMontaCodUnidadeM = explode("-", $_REQUEST['inMontaCodUnidadeM']);
                $obTPatrimonioBemComprado->setDado( 'cod_bem'         , $inCodBem );
                $obTPatrimonioBemComprado->setDado( 'exercicio'       , $_REQUEST['stExercicio']     );
                $obTPatrimonioBemComprado->setDado( 'cod_entidade'    , $_REQUEST['inCodEntidade']   );
                $obTPatrimonioBemComprado->setDado( 'cod_empenho'     , $_REQUEST['inNumEmpenho']    );
                $obTPatrimonioBemComprado->setDado( 'nota_fiscal'     , $_REQUEST['stNumNotaFiscal'] );
                $obTPatrimonioBemComprado->setDado( 'num_orgao'       , $_REQUEST['inCodOrgao']      );
                $obTPatrimonioBemComprado->setDado( 'num_unidade'     , $_REQUEST['inCodUnidade']    );
                $obTPatrimonioBemComprado->setDado( 'data_nota_fiscal', $_REQUEST['dataNotaFiscal'] );
                
                if ( $_FILES['fileArquivoNF']['name'] != '' ) {
                    $stDestinoAnexo    = CAM_GP_PAT_ANEXOS;
                    $stEnderecoArquivo = $_FILES['fileArquivoNF']['tmp_name'];
                    $stNomeArquivo	   = $_FILES['fileArquivoNF']['name'] ;
                    if (file_exists($stDestinoAnexo.$stNomeArquivo)) {
                        $stMensagem = 'Arquivo já existente, informe um arquivo com outro nome.';
                    } else {
                        if ($_FILES['fileArquivoNF']['size'] < 10485760) {
                            //Seta campo com nome do arquivo
                            $obTPatrimonioBemComprado->setDado( 'caminho_nf' , $_FILES['fileArquivoNF']['name']);
                            
                            $boMoveArquivo = move_uploaded_file( $stEnderecoArquivo, $stDestinoAnexo.$stNomeArquivo );
                            if (!$boMoveArquivo) {
                                $stMensagem = 'Erro ao incluir arquivo.';
                            }
                        } else {
                            $stMensagem = 'Arquivo excede tamanho máximo de 10MB.';
                        }
                    }
                }
                
                $obTPatrimonioBemComprado->inclusao();
                
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02 AND $_REQUEST['inCodTipoDocFiscal'] ) {
                    $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
                    $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'                   , $inCodBem );
                    $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_tipo_documento_fiscal' , $_REQUEST['inCodTipoDocFiscal'] );
                    $obTCEALBemCompradoTipoDocumentoFiscal->inclusao();
                }

                $arOrgao = explode('/',$_REQUEST['inCodOrgao'] );
                //inclui na table patrimonio.historico_bem
                $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $inCodBem );
                $obTPatrimonioHistoricoBem->setDado( 'cod_situacao', $_REQUEST['inCodSituacao'] );
                $obTPatrimonioHistoricoBem->setDado( 'cod_local', $_REQUEST['inCodLocal'] );
                $obTPatrimonioHistoricoBem->setDado( 'cod_orgao', $_REQUEST['hdnUltimoOrgaoSelecionado'] );
                $obTPatrimonioHistoricoBem->setDado( 'ano_exercicio', Sessao::getExercicio() );
                $obTPatrimonioHistoricoBem->setDado( 'descricao', $_REQUEST['stDescricaoSituacao'] );
                $obTPatrimonioHistoricoBem->inclusao();

                //incluir na table patrimonio.bem_responsavel
                $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $inCodBem );
                $obTPatrimonioBemResponsavel->setDado( 'numcgm', $_REQUEST['inNumResponsavel'] );
                $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $_REQUEST['dtInicioResponsavel'] );
                $obTPatrimonioBemResponsavel->inclusao();

                //incluir na table patrimonio.bem_marca se estiver informada
                if ($_REQUEST['inCodMarca'] != '') {
                    $obTPatrimonioBemMarca->setDado( 'cod_bem', $inCodBem );
                    $obTPatrimonioBemMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
                    $obTPatrimonioBemMarca->inclusao();
                }

                //inclui na table patrimonio.apolice_bem
                if ($_REQUEST['stApolice'] == 'sim') {
                    $obTPatrimonioApoliceBem->setDado( 'cod_bem', $inCodBem );
                    $obTPatrimonioApoliceBem->setDado( 'cod_apolice', $_REQUEST['inCodApolice'] );
                    $obTPatrimonioApoliceBem->inclusao();
                }

                //incluir na table patrimonio.bem_atributo_especie
                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_bem', $inCodBem );
                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_modulo', 6 );
                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_cadastro', 1 );

                foreach ($_POST as $stKey => $stValue) {
                    if ( strstr( $stKey, 'Atributo_' ) AND $stValue != '' ) {
                        $arAtributo = explode( '_', $stKey );
                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_atributo', $arAtributo[1] );
                        $obTPatrimonioBemAtributoEspecie->setDado( 'valor', $stValue );
                        $obTPatrimonioBemAtributoEspecie->inclusao();
                    }
                }
            }

            if ($inQtdeLote > 1) {
                $stMsg = 'Bens: '.$arCodBem[0].' à '.$arCodBem[$i-1];
            } else {
                $stMsg = 'Bem: '.$inCodBem.' - '.$_REQUEST['stNomBem'];
            }

            $obTPatrimonioGrupo = new TPatrimonioGrupo;
            $obTPatrimonioGrupo->setDado('cod_grupo'    , $_REQUEST['inCodGrupo']);
            $obTPatrimonioGrupo->setDado('cod_natureza' , $_REQUEST['inCodNatureza']);
            $obTPatrimonioGrupo->recuperaDadosGrupo($rsGrupo);

            if ($boDepreciavel == true && ($_REQUEST['flQuotaDepreciacaoAnual'] == "0.00" || empty($_REQUEST['flQuotaDepreciacaoAnual'])) && $rsGrupo->getCampo('depreciacao') == '0.00') {
                $stMsg .= ". O bem não pode ser depreciado pois não existem quotas definidas.";
            }
            
            if($_REQUEST['inCodObra'] != '') {
                $inCodObra = explode('|', $_REQUEST['inCodObra']);
                $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
                $obTTGOPatrimonioBemObra->setDado('cod_bem'  , $inCodBem);
                $obTTGOPatrimonioBemObra->setDado('ano_obra' , $inCodObra[0]);
                $obTTGOPatrimonioBemObra->setDado('cod_obra' , $inCodObra[1]);
                $obTTGOPatrimonioBemObra->inclusao();
            }
            
            $stMensagem = "Incluir Bem concluído com sucesso! ($stMsg)";

            $stJs .= "jQuery('#stNumeroPlaca', window.parent.frames['telaPrincipal'].document).val('".$numeroPlaca."');";
            $stJs .= "jQuery('#stSpnListaReavaliacao', window.parent.frames['telaPrincipal'].document).html('');";
            $stJs .= "window.parent.frames['telaPrincipal'].HabilitaLayer('layer_1');";
            $stJs .= "d.getElementById('stCodClassificacao').focus();";
            $stJs .= "alertaAviso('$stMensagem','form','aviso','".Sessao::getId()."');";
            Sessao::remove('arReavaliacao');
            SistemaLegado::executaFrameOculto($stJs);
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

    break;

    case 'alterar' :

        # Exclui as reavaliações.
        $rsReavaliacaoExcluir = new RecordSet;
        $rsReavaliacaoExcluir->preenche(Sessao::read('arReavaliacaoExcluir'));
                        
        while (!$rsReavaliacaoExcluir->eof()) {
            $inCodReavaliacao = $rsReavaliacaoExcluir->getCampo('inCodReavaliacao');
            $inCodBem         = $rsReavaliacaoExcluir->getCampo('inCodBem');
            
            $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
            $obTPatrimonioDepreciacaoReavaliacao->recuperaReavaliacao($rsDepreciacao, " AND reavaliacao.cod_reavaliacao = ".$inCodReavaliacao);
            
            $obTPatrimonioDepreciacaoReavaliacao->recuperaRelacaoDepreciacao($rsRelacaoDepreciacao, "WHERE depreciacao_reavaliacao.cod_reavaliacao = ".$inCodReavaliacao." AND depreciacao_reavaliacao.cod_bem = ".$inCodBem);
                        
            if ($rsDepreciacao->getNumLinhas() > 0) {
                $stMensagem = "Não é possível excluir essa reavaliação, depreciação com data de competência igual ou superior";
            } else {
                $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
                $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_depreciacao', $rsRelacaoDepreciacao->getCampo('cod_depreciacao') );
                $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                $obTPatrimonioDepreciacaoReavaliacao->exclusao();
                
                $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao;
                $obTPatrimonioReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                $obTPatrimonioReavaliacao->setDado( 'cod_bem'        , $inCodBem );
                $obTPatrimonioReavaliacao->exclusao();
            }

            $rsReavaliacaoExcluir->proximo();
        }

        Sessao::remove('arReavaliacaoExcluir');
        Sessao::remove('stDepreciacaoCompetencia');

        # Exclui as depreciações.
        $rsDepreciacaoExcluir = new RecordSet;
        $rsDepreciacaoExcluir->preenche(Sessao::read('arDepreciacaoExcluir'));

        while (!$rsDepreciacaoExcluir->eof()) {
            $inCodBem         = $rsDepreciacaoExcluir->getCampo('inCodBem');
            $inCodDepreciacao = $rsDepreciacaoExcluir->getCampo('inCodDepreciacao');
            $timestamp        = $rsDepreciacaoExcluir->getCampo('timestamp');

            $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
            $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_depreciacao', $inCodDepreciacao );
            $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_bem', $inCodBem );
            #$obTPatrimonioDepreciacaoReavaliacao->setDado( 'timestamp', $timestamp);
            $obTPatrimonioDepreciacaoReavaliacao->exclusao();

            $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao;
            $obTPatrimonioDepreciacao->setDado( 'cod_depreciacao', $inCodDepreciacao );
            $obTPatrimonioDepreciacao->setDado( 'cod_bem', $inCodBem );
            #$obTPatrimonioDepreciacao->setDado( 'timestamp', $timestamp);
            $obTPatrimonioDepreciacao->exclusao();

            $rsDepreciacaoExcluir->proximo();
        }

        Sessao::remove('arDepreciacaoExcluir');

        // Insere reavaliações
        $rsReavaliacao = new RecordSet;
        $rsReavaliacao->preenche(Sessao::read('arReavaliacao'));

        while (!$rsReavaliacao->eof() ) {
            $inCodBem              = $rsReavaliacao->getCampo('inCodBem');
            $inCodReavaliacao      = $rsReavaliacao->getCampo('inCodReavaliacao');
            $dtReavaliacao         = $rsReavaliacao->getCampo('dtReavaliacao');
            $inVidaUtilReavaliacao = $rsReavaliacao->getCampo('inVidaUtilReavaliacao');
            $flValorBemReavaliacao = $rsReavaliacao->getCampo('flValorBemReavaliacao');
            $stMotivoReavaliacao   = $rsReavaliacao->getCampo('stMotivoReavaliacao');
            $inserir               = $rsReavaliacao->getCampo('inserir');

            if ($inCodBem != '' && $inCodReavaliacao === 0 && $dtReavaliacao != '' && $inVidaUtilReavaliacao  != '' && $flValorBemReavaliacao  != '' && $stMotivoReavaliacao != '' && $inserir === 'true') {
                $obTPatrimonioReavaliacao->proximoCod( $inCodReavaliacao );
                $obTPatrimonioReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                $obTPatrimonioReavaliacao->setDado( 'cod_bem', $inCodBem );
                $obTPatrimonioReavaliacao->setDado( 'dt_reavaliacao', $dtReavaliacao );
                $obTPatrimonioReavaliacao->setDado( 'vida_util', $inVidaUtilReavaliacao );
                $obTPatrimonioReavaliacao->setDado( 'vl_reavaliacao', $flValorBemReavaliacao );
                $obTPatrimonioReavaliacao->setDado( 'motivo', trim($stMotivoReavaliacao) );
                $obTPatrimonioReavaliacao->inclusao();
            }
            $rsReavaliacao->proximo();

        }

        Sessao::remove('arReavaliacao');

        $boDepreciavel          = $_REQUEST['boDepreciavel'];
        $stDepreciacaoAcelerada = $_REQUEST['boDepreciacaoAcelerada'];
        $inCodBem               = $_REQUEST['inCodBem'];
        $inCodPlano             = $_REQUEST['inCodContaAnalitica'];
        $inCodContaDepreciacao  = $_REQUEST['inCodContaDepreciacao'];
        $inExercicio            = Sessao::getExercicio();

        if (!empty($_REQUEST['stChaveProcesso'])) {
            $arProcesso = array();
            $arProcesso = explode("/", $_REQUEST['stChaveProcesso']);
            $inCodProcesso = $arProcesso[0];
            $stAnoProcesso = $arProcesso[1];    
        } else {
            $arProcesso = "";
        }
        
        if ($boDepreciavel === 'true') {

            if (!empty($inCodPlano)) {
                $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_plano', $inCodPlano);
                $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
                $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio', $inExercicio);

                $rsBemPlanoAnalitica = new RecordSet();
                $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                if ($rsBemPlanoAnalitica->getCampo('timestamp') == '' || ($rsBemPlanoAnalitica->getCampo('timestamp') < $rsBemPlanoAnalitica->getCampo('ultimo_timestamp'))) {
                    $obTPatrimonioBemPlanoAnalitica->inclusao();
                }
            } else {
                $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                $obTPatrimonioBemPlanoAnalitica->setDado('cod_plano', $inCodPlano);
                $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
                $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio', $inExercicio);

                $rsBemPlanoAnalitica = new RecordSet();
                $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                if ($rsBemPlanoAnalitica->getNumLinhas() > 0) {
                    $obTPatrimonioBemPlanoAnalitica->exclusao();
                }
            }

            if ($stDepreciacaoAcelerada === "true") {
                $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'true' );
                $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', $_REQUEST['flQuotaDepreciacaoAcelerada'] );
            } else {
                $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
                $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
            }

            $obTPatrimonioBem->setDado( 'depreciavel', 'true');
            $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', $_REQUEST['flQuotaDepreciacaoAnual'] );
        } else {
            # Terá vinculo com a GF.
            # $obTPatrimonioBemPlanoAnalitica->recuperaBemPlanoAnalitica($rsBemPlanoAnalitica);
            # $obTPatrimonioBemPlanoAnalitica->exclusao();

            $obTPatrimonioBem->setDado( 'depreciavel', 'false');
            $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
            $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', '0,00' );
            $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
        }

        //verifica se já existe um responsável pelo bem
        $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioBemResponsavel->recuperaUltimoResponsavel( $rsBemResponsavel );

        //verifica se o numero da placa já existe
        $stFiltro = " WHERE num_placa = '".$_REQUEST['stNumeroPlaca']."' AND cod_bem  <> ".$_REQUEST['inCodBem'];
        $obTPatrimonioBem->recuperaTodos($rsBem, $stFiltro);
        
        // Reupera alguma depreciação na competencia se o bem possuir
        $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
        $obTPatrimonioDepreciacao->setDado('cod_bem', $inCodBem);
        $obTPatrimonioDepreciacao->recuperaDepreciacao($rsDepreciado, " AND SUBSTR(depreciacao.competencia, 0,5) = '".Sessao::getExercicio()."'");
        
        // Recupera o cod_plano se estiver cadastrado no bem
        $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem'   , $inCodBem );
        $obTPatrimonioBemPlanoDepreciacao->setDado( 'exercicio' , $inExercicio);
        $obTPatrimonioBemPlanoDepreciacao->recuperaBemPlanoDepreciacao( $rsBemPlanoDepreciacao );
        
        // Recupera o cod_plano se estiver cadastrado no grupo        
        $obTPatrimonioGrupo = new TPatrimonioGrupo();
        $obTPatrimonioGrupo->setDado('cod_bem' , $inCodBem);
        $obTPatrimonioGrupo->recuperaGrupoPlanoDepreciacao( $rsGrupoPlanoDepreciacao );
                
        // Verifica se o bem possui depreicação na competencia atual, caso sim, não poderá alterar o valor da conta contabil de depreciação até que anule todas as depreciações
        if( $rsDepreciado->getNumLinhas() >= 1 ){
            
            // Verifica se a conta foi modificada e está cadastrada no bem, pois é a que prevalece sobre o grupo
            if ( $rsBemPlanoDepreciacao->getNumLinhas() >= 1 && $rsBemPlanoDepreciacao->getCampo("cod_plano") != $inCodContaDepreciacao ) {
                $stMensagem = "Já existem depreciações lançadas para este bem. Anule-as para alterar a Conta Contábil de Depreciação.";
            // se não verifica se sofreu alteração e está diferente da cadastrada no grupo
            } else if ($rsGrupoPlanoDepreciacao->getNumLinhas() >= 1 && $rsGrupoPlanoDepreciacao->getCampo('cod_plano') != $inCodContaDepreciacao && !empty($inCodContaDepreciacao)) {
                $stMensagem = "Já existem depreciações lançadas para este bem. Anule-as para alterar a Conta Contábil de Depreciação.";
            }
            
        } else {    
            //Caso não exista depreciação faz o processo de incluir ou excluir um conta contabil de depreciação para o bem
            $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem'  , $inCodBem );
            $obTPatrimonioBemPlanoDepreciacao->setDado( 'exercicio', $inExercicio);
            
            if (!empty($inCodContaDepreciacao)) {
                $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_plano', $inCodContaDepreciacao);
                $obTPatrimonioBemPlanoDepreciacao->inclusao();
            } else {
                $obTPatrimonioBemPlanoDepreciacao->exclusao();
            }
        } 
        
        //verifica a integridade dos valores
        if ($_REQUEST['inValorBem'] == '0,00') {
            $stMensagem = 'Valor do bem inválido';
        } elseif (!empty($_REQUEST['inValorDepreciacao']) && (str_replace(",",".",str_replace(".", "", $_REQUEST['inValorDepreciacao'])) > str_replace(",",".",str_replace(".", "", $_REQUEST['inValorBem'])))) {
            $stMensagem = 'O valor da Depreciação Inicial não pode ser maior que o valor do bem.';
        } elseif ( $_REQUEST['dtDepreciacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtDepreciacao'])) ) {
            $stMensagem = 'A data de depreciação deve ser maior ou igual a data de aquisição';
        } elseif ( $_REQUEST['dtVencimento'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtVencimento'])) ) {
            $stMensagem = 'A data de vencimento da garantia deve ser maior ou igual a data de aquisição';
        } elseif ( $_REQUEST['dtIncorporacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtIncorporacao'])) ) {
            $stMensagem = 'A data de incorporação deve ser maior ou igual a data de aquisição';
        } elseif ($_REQUEST['stPlacaIdentificacao'] == 'sim' AND $_REQUEST['stNumeroPlaca'] == '') {
            $stMensagem = 'Número da placa inválido';
        } elseif ( $rsBem->getNumLinhas() > 0 ) {
            $stMensagem = 'Este número da placa já existe';
        } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) < implode('',array_reverse(explode('/',$_REQUEST['dtAquisicao']))) ) {
            $stMensagem = 'A data de início do responsável deve ser maior ou igual a data de aquisição';
        } elseif ( $rsBemResponsavel->getNumLinhas() > 0 AND ( implode('',array_reverse(explode('/',$rsBemResponsavel->getCampo('dt_inicio')))) >  implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) ) AND ( $_REQUEST['inNumResponsavel'] <> $rsBemResponsavel->getCampo('numcgm') )) {
            $stMensagem = 'A data de início do responsável deve ser posterior ou igual a do atual responsável('.implode('/',array_reverse(explode('-',$rsBemResponsavel->getCampo('dt_inicio')))).')';
        } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) > date('Ymd') ) {
            $stMensagem = 'A data de início do responsável deve ser menor ou igual a data de hoje';
        } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodSeguradora'] == '') {
            $stMensagem = 'Selecione uma seguradora';
        } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodApolice'] == '') {
            $stMensagem = 'Selecione uma apólice';
        } elseif (((float) (str_replace(',','.',$_REQUEST['flQuotaDepreciacaoAnual'])) + (float) (str_replace(',','.',$_REQUEST['flQuotaDepreciacaoAcelerada']))) > 100) {
            $stMensagem = 'A soma das quotas (anual+acelerada) não pode ultrapassar 100%.';
        } elseif ($_REQUEST['boDepreciacaoAcelerada'] == 'true' && (empty($_REQUEST['flQuotaDepreciacaoAcelerada']) || $_REQUEST['flQuotaDepreciacaoAcelerada'] == '0,00')) {
            $stMensagem = 'O valor da quota acelerada deve ser informado e maior que zero.';
        }elseif($boDepreciavel === 'true' && !empty($_REQUEST['inCodContaDepreciacao']) && ($_REQUEST['flQuotaDepreciacaoAnual'] == '0,00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '0.00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '')){
            $stMensagem = 'O valor da quota de depreciação Anual deve ser maior que zero';
        }elseif(empty($_REQUEST['inCodContaDepreciacao']) && isset($_REQUEST['flQuotaDepreciacaoAnual']) && $_REQUEST['flQuotaDepreciacaoAnual'] != '0,00'){
            $stMensagem = 'O valor da Conta Contábil de Depreciação Acumulada deve ser informado.';
        }elseif(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02 ){
                if($_REQUEST['stNumNotaFiscal'] != '' and  $_REQUEST['inCodTipoDocFiscal'] == ''){
                    $stMensagem = 'Informe o Tipo do Documento Fiscal';    
                }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['inCodTipoDocFiscal'] != '' ){
                    $stMensagem = 'O campo Tipo do Documento Fiscal não deve ser preenchido, quando não houver  um Número da Nota Fiscal';
                }
        }
        if( $_REQUEST['stNumNotaFiscal'] != '' && empty($_REQUEST['dataNotaFiscal']) ){
                $stMensagem = 'O campo Data da Nota Fiscal deve ser preenchido';
        }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['dataNotaFiscal'] != '' ){
                $stMensagem = 'O campo Data da Nota Fiscal não deve ser preenchido, quando não houver um Número da Nota Fiscal';
        }
        $rsAtributosDinamicos = Sessao::read('rsAtributosDinamicos');
        if ( is_array( $rsAtributosDinamicos->arElementos ) ) {
            while ( !$rsAtributosDinamicos->eof() ) {
                if ( $rsAtributosDinamicos->getCampo('nao_nulo') == 'f' AND $_REQUEST['Atributo_'.$rsAtributosDinamicos->getCampo('cod_atributo').'_'.$rsAtributosDinamicos->getCampo('cod_cadastro')] == '' ) {
                    $stMensagem = 'Preencha o campo '.$rsAtributosDinamicos->getCampo('nom_atributo');
                    break;
                }
                $rsAtributosDinamicos->proximo();
            }
        }

        if (!$stMensagem) {
            
            //altera a table patrimonio.bem
            $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioBem->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioBem->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
            $obTPatrimonioBem->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
            $obTPatrimonioBem->setDado( 'numcgm', $_REQUEST['inCodFornecedor'] );
            $obTPatrimonioBem->setDado( 'descricao', $_REQUEST['stNomBem'] );
            $obTPatrimonioBem->setDado( 'detalhamento', $_REQUEST['stDetalhamentoBem'] );
            $obTPatrimonioBem->setDado( 'dt_aquisicao', $_REQUEST['dtAquisicao'] );
            $obTPatrimonioBem->setDado( 'vida_util', $_REQUEST['inVidaUtil'] );
            $obTPatrimonioBem->setDado( 'dt_incorporacao', $_REQUEST['dtIncorporacao'] );
            $obTPatrimonioBem->setDado( 'dt_depreciacao', $_REQUEST['dtDepreciacao'] );
            $obTPatrimonioBem->setDado( 'dt_garantia', $_REQUEST['dtVencimento'] );
            $obTPatrimonioBem->setDado( 'vl_bem', str_replace(',','.',str_replace('.','',$_REQUEST['inValorBem'])) );
            
            if ($_REQUEST['inValorDepreciacao'] != '') {
                $obTPatrimonioBem->setDado( 'vl_depreciacao', str_replace(',','.',str_replace('.','',$_REQUEST['inValorDepreciacao'])) );
            } else {
                $obTPatrimonioBem->setDado( 'vl_depreciacao', 0.00);
            }
            
            $obTPatrimonioBem->setDado( 'identificacao', ( $_REQUEST['stPlacaIdentificacao'] == 'sim' ) ? true : false );

            if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
                $obTPatrimonioBem->setDado( 'num_placa', $_REQUEST['stNumeroPlaca'] );
            } else {
                $arEstrutura = $obTPatrimonioBem->getEstrutura();
                foreach ($arEstrutura as $key => $value) {
                    if ($value->stNomeCampo == 'num_placa') {
                        $keyField = $key;
                        break;
                    }
                }

                # Seta provisoriamente o tipo do campo como INTEGER para gravar o valor null ao invés de ''.
                $arEstrutura[$keyField]->setTipoCampo('integer');
                $obTPatrimonioBem->setEstrutura($arEstrutura);
                $obTPatrimonioBem->setDado( 'num_placa', 'null' );
            }

            $obTPatrimonioBem->alteracao();
            
            if (!empty($_REQUEST['stChaveProcesso'])) {
                $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
                $obTPatrimonioBemProcesso->setDado('ano_exercicio', $stAnoProcesso);
                $obTPatrimonioBemProcesso->setDado('cod_processo', $inCodProcesso);
                $obTPatrimonioBemProcesso->recuperaPorChave($rsProcesso);

                if ($rsProcesso->getNumLinhas() > 0) {
                    $obTPatrimonioBemProcesso->alteracao();
                }  else {
                    $obTPatrimonioBemProcesso->inclusao();
                }
            } elseif (!empty($_REQUEST['hdnChaveProcesso'])) {
                $arProcessoAux = explode("/",$_REQUEST['hdnChaveProcesso']);
                $stAnoProcessoAux = $arProcessoAux[0];
                $stCodProcessoAux = $arProcessoAux[1];
                $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
                $obTPatrimonioBemProcesso->setDado('ano_exercicio', $stAnoProcessoAux);
                $obTPatrimonioBemProcesso->setDado('cod_processo', $stCodProcessoAux);
                $obTPatrimonioBemProcesso->recuperaPorChave($rsProcesso);

                if ($rsProcesso->getNumLinhas() > 0) {
                    $obTPatrimonioBemProcesso->exclusao();
                }
            }

            $obTPatrimonioBemComprado->recuperaTodos($rsBemComprado, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
            if ( $rsBemComprado->getNumLinhas() > 0) {
                //altera a table patrimonio.bem_comprado
                $arMontaCodOrgaoM   = explode("-", $_REQUEST['inMontaCodOrgaoM'  ]);
                $arMontaCodUnidadeM = explode("-", $_REQUEST['inMontaCodUnidadeM']);

                $obTPatrimonioBemComprado->setDado( 'cod_bem'          , $_REQUEST['inCodBem'       ]);
                $obTPatrimonioBemComprado->setDado( 'exercicio'        , $_REQUEST['stExercicio'    ]);
                $obTPatrimonioBemComprado->setDado( 'cod_entidade'     , $_REQUEST['inCodEntidade'  ]);
                $obTPatrimonioBemComprado->setDado( 'cod_empenho'      , $_REQUEST['inNumEmpenho'   ]);
                $obTPatrimonioBemComprado->setDado( 'nota_fiscal'      , $_REQUEST['stNumNotaFiscal']);
                $obTPatrimonioBemComprado->setDado( 'num_orgao'        , $_REQUEST['inCodOrgao'     ]);
                $obTPatrimonioBemComprado->setDado( 'num_unidade'      , $_REQUEST['inCodUnidade'   ]);
                $obTPatrimonioBemComprado->setDado( 'data_nota_fiscal' , $_REQUEST['dataNotaFiscal' ]);
                
                if ( $_FILES['fileArquivoNF']['name'] != '' ) {
                    $stDestinoAnexo    = CAM_GP_PAT_ANEXOS;
                    $stEnderecoArquivo = $_FILES['fileArquivoNF']['tmp_name'];
                    $stNomeArquivo	   = $_FILES['fileArquivoNF']['name'] ;
                    if (file_exists($stDestinoAnexo.$stNomeArquivo)) {
                        $stMensagem = 'Arquivo já existente, informe um arquivo com outro nome.';
                    } else {
                        if ($_FILES['fileArquivoNF']['size'] < 10485760) {
                            //Seta campo com nome do arquivo
                            $obTPatrimonioBemComprado->setDado( 'caminho_nf' , $_FILES['fileArquivoNF']['name']);
                            
                            $boMoveArquivo = move_uploaded_file( $stEnderecoArquivo, $stDestinoAnexo.$stNomeArquivo );
                            if (!$boMoveArquivo) {
                                $stMensagem = 'Erro ao incluir arquivo.';
                            }
                        } else {
                            $stMensagem = 'Arquivo excede tamanho máximo de 10MB.';
                        }
                    }
                }

                $obTPatrimonioBemComprado->alteracao();
               
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02  ) {
                    $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
                    $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'    , $inCodBem );
                    $obTCEALBemCompradoTipoDocumentoFiscal->recuperaPorChave($rsTipoNotaFiscal);
                    
                    if($_REQUEST['inCodTipoDocFiscal']){
                        $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_tipo_documento_fiscal' , $_REQUEST['inCodTipoDocFiscal'] );

                        if( $rsTipoNotaFiscal->getNumLinhas() > 0){
                             $obTCEALBemCompradoTipoDocumentoFiscal->alteracao();
                        }else{
                            $obTCEALBemCompradoTipoDocumentoFiscal->inclusao();
                        }
                    }

                    if(empty($_REQUEST['inCodTipoDocFiscal'])){ 
                        if( $rsTipoNotaFiscal->getNumLinhas() > 0) {
                            $obTCEALBemCompradoTipoDocumentoFiscal->exclusao();
                        }
                    }
                }
            } else {
                //inclui na table patrimonio.bem_comprado
                $arMontaCodOrgaoM   = explode("-", $_REQUEST['inMontaCodOrgaoM'  ] );
                $arMontaCodUnidadeM = explode("-", $_REQUEST['inMontaCodUnidadeM'] );

                $obTPatrimonioBemComprado->setDado( 'cod_bem'          , $_REQUEST['inCodBem'       ] );
                $obTPatrimonioBemComprado->setDado( 'exercicio'        , $_REQUEST['stExercicio'    ] );
                $obTPatrimonioBemComprado->setDado( 'cod_entidade'     , $_REQUEST['inCodEntidade'  ] );
                $obTPatrimonioBemComprado->setDado( 'cod_empenho'      , $_REQUEST['inNumEmpenho'   ] );
                $obTPatrimonioBemComprado->setDado( 'nota_fiscal'      , $_REQUEST['stNumNotaFiscal'] );
                $obTPatrimonioBemComprado->setDado( 'num_orgao'        , $_REQUEST['inCodOrgao'     ] );
                $obTPatrimonioBemComprado->setDado( 'num_unidade'      , $_REQUEST['inCodUnidade'   ] );
                $obTPatrimonioBemComprado->setDado( 'data_nota_fiscal' , $_REQUEST['dataNotaFiscal' ] );

                $obTPatrimonioBemComprado->inclusao();
                
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02  ) {
                    $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
                    $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'    , $inCodBem );
                    $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_tipo_documento_fiscal' , $_REQUEST['inCodTipoDocFiscal'] );
                    $obTCEALBemCompradoTipoDocumentoFiscal->inclusao();
                }
            }
            //inclui na table patrimonio.historico_bem
            $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioHistoricoBem->setDado( 'cod_situacao', $_REQUEST['inCodSituacao'] );
            $obTPatrimonioHistoricoBem->setDado( 'cod_local', $_REQUEST['inCodLocal'] );
            $obTPatrimonioHistoricoBem->setDado( 'cod_orgao', $_REQUEST['hdnUltimoOrgaoSelecionado'] );
            $obTPatrimonioHistoricoBem->setDado( 'ano_exercicio', Sessao::getExercicio() );
            $obTPatrimonioHistoricoBem->setDado( 'descricao', $_REQUEST['stDescricaoSituacao'] );
            $obTPatrimonioHistoricoBem->inclusao();

            //verifica se existe um responsável cadastrado
            $obTPatrimonioBemResponsavel->recuperaTodos( $rsResponsavel, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
            if ( $rsResponsavel->getNumLinhas() > 0) {
                $obTPatrimonioBemResponsavel->setDado( 'numcgm',$_REQUEST['inNumResponsavel'] );
                $obTPatrimonioBemResponsavel->setDado( 'dt_inicio',$_REQUEST['dtInicioResponsavel'] );
                $obTPatrimonioBemResponsavel->recuperaUltimoResponsavel( $rsUltimoResponsavel );
                if ( $rsUltimoResponsavel->getNumLinhas() <= 0) {
                    $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                    $obTPatrimonioBemResponsavel->setDado( 'numcgm', $_REQUEST['inNumResponsavel'] );
                    $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $_REQUEST['dtInicioResponsavel'] );
                    $obTPatrimonioBemResponsavel->setDado( 'dt_fim', '' );
                    $obTPatrimonioBemResponsavel->inclusao();

                    //altera a table patrimonio.bem_responsavel
                    //coloca a data de fim do último responsável e inclui o novo
                    $obTPatrimonioBemResponsavel->setDado( 'cod_bem'  , $_REQUEST['inCodBem'] );
                    $obTPatrimonioBemResponsavel->setDado( 'timestamp', $rsBemResponsavel->getCampo( 'timestamp' ) );
                    $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $rsBemResponsavel->getCampo( 'dt_inicio' ) );
                    $obTPatrimonioBemResponsavel->setDado( 'dt_fim'   , $_REQUEST['dtInicioResponsavel'] );
                    $obTPatrimonioBemResponsavel->setDado( 'numcgm'   , $rsBemResponsavel->getCampo( 'numcgm' ) );
                    $obTPatrimonioBemResponsavel->alteracao();

                }
            } else {
                $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obTPatrimonioBemResponsavel->setDado( 'numcgm', $_REQUEST['inNumResponsavel'] );
                $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $_REQUEST['dtInicioResponsavel'] );
                $obTPatrimonioBemResponsavel->setDado( 'dt_fim', '' );
                $obTPatrimonioBemResponsavel->inclusao();
            }

            $obTPatrimonioBemMarca->recuperaTodos( $rsMarca, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
            if ( $rsMarca->eof()) {
                if ($_REQUEST['inCodMarca'] != '') {
                    $obTPatrimonioBemMarca->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                    $obTPatrimonioBemMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
                    $obTPatrimonioBemMarca->inclusao();
                }
            } else {
                if ($_REQUEST['inCodMarca'] != '') {
                    $obTPatrimonioBemMarca->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                    $obTPatrimonioBemMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
                    $obTPatrimonioBemMarca->alteracao();
                }
            }

            //inclui na table patrimonio.apolice_bem
            if ($_REQUEST['stApolice'] == 'sim') {
                $obTPatrimonioApoliceBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obTPatrimonioApoliceBem->recuperaMaxApoliceBem( $rsApoliceBem );

                //verifica se a última apolice inserida na base não é a mesma que está sendo alterada
                if ( $_REQUEST['inCodApolice'] != $rsApoliceBem->getCampo('cod_apolice') ) {
                    $obTPatrimonioApoliceBem->setDado( 'cod_apolice', $_REQUEST['inCodApolice'] );
                    $obTPatrimonioApoliceBem->inclusao();
                }
            }

            //deleta todos os registros da table patrimonio.bem_atributo_especie
            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioBemAtributoEspecie->exclusao();

            //inclui na table patrimonio.bem_atributo_especie
            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_modulo', 6 );
            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_cadastro', 1 );

            $obTPatrimonioGrupo = new TPatrimonioGrupo;
            $obTPatrimonioGrupo->setDado('cod_grupo'    , $_REQUEST['inCodGrupo']);
            $obTPatrimonioGrupo->setDado('cod_natureza' , $_REQUEST['inCodNatureza']);
            $obTPatrimonioGrupo->recuperaDadosGrupo($rsGrupo);

            if ($boDepreciavel == true && ($_REQUEST['flQuotaDepreciacaoAnual'] == "0.00" || empty($_REQUEST['flQuotaDepreciacaoAnual'])) && $rsGrupo->getCampo('depreciacao') == '0.00') {
                $stMsg .= ". O bem não pode ser depreciado pois não existem quotas definidas.";
            }

            $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
            $obTTGOPatrimonioBemObra->setDado('cod_bem', $_REQUEST['inCodBem']);
                        
            $obTTGOPatrimonioBemObra->recuperaPorChave($rsBemObra);
            
            if( $rsBemObra->getNumLinhas() > 0) {
                $obTTGOPatrimonioBemObra->exclusao();
            }
                        
            if($_REQUEST['inCodObra'] != '') {
                $inCodObra = explode('|', $_REQUEST['inCodObra']);
                $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
                $obTTGOPatrimonioBemObra->setDado('cod_bem'  , $_REQUEST['inCodBem']);
                $obTTGOPatrimonioBemObra->setDado('ano_obra' , $inCodObra[0]);
                $obTTGOPatrimonioBemObra->setDado('cod_obra' , $inCodObra[1]);
                $obTTGOPatrimonioBemObra->inclusao();
            }
            
            foreach ($_POST as $stKey => $stValue) {
                if (strstr( $stKey, 'Atributo_' ) AND $stValue != '' ) {
                    $arAtributo = explode( '_', $stKey );
                    $obTPatrimonioBemAtributoEspecie->setDado( 'cod_atributo', $arAtributo[1] );
                    $obTPatrimonioBemAtributoEspecie->setDado( 'valor', $stValue );
                    $obTPatrimonioBemAtributoEspecie->inclusao();
                }
            }

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem: ".$_REQUEST['inCodBem'].' - '.$_REQUEST['stNomBem'].$stMsg,"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
        }

        break;

    case 'excluir' :

        $inCodBem = $_REQUEST['inCodBem'];
        
         //exclui da tabela bem_comprado_tipo_documento_fiscal
        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02  ) {
            $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
            $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'    , $inCodBem );
            $obTCEALBemCompradoTipoDocumentoFiscal->recuperaPorChave($rsTipoNotaFiscal);

            if( $rsTipoNotaFiscal->getNumLinhas() > 0) {
                $obTCEALBemCompradoTipoDocumentoFiscal->exclusao();
            }
        }
        
        //exclui da tabela bem_comprado
        $obTPatrimonioBemComprado->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioBemComprado->exclusao();
        
        //exclui da tabela apolice_bem
        $obTPatrimonioApoliceBem->recuperaTodos( $rsApoliceBem, ' WHERE cod_bem = '.$inCodBem.' ' );
        while ( !$rsApoliceBem->eof() ) {
            $obTPatrimonioApoliceBem->setDado( 'cod_bem', $rsApoliceBem->getCampo('cod_bem'));
            $obTPatrimonioApoliceBem->setDado( 'cod_apolice', $rsApoliceBem->getCampo('cod_apolice'));
            $obTPatrimonioApoliceBem->exclusao();
            $rsApoliceBem->proximo();
        }

        //exclui da tabela bem_responsavel
        $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioBemResponsavel->exclusao();

        //exclui da tabela bem_marca
        $obTPatrimonioBemMarca->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioBemMarca->exclusao();

        //exclui da table iventario_historico_bem
        $obTPatrimonioInventarioHistoricoBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioInventarioHistoricoBem->recuperaTodos( $rsInventario, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
        while (!$rsInventario->eof()) {
            $obTPatrimonioInventarioHistoricoBem->setDado('id_inventario', $rsInventario->getCampo('id_inventario'));
            $obTPatrimonioInventarioHistoricoBem->exclusao();
            $rsInventario->proximo();
        }

        //exclui da table historico_bem
        $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioHistoricoBem->exclusao();

        //exclui da table bem_atributo_especie
        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioBemAtributoEspecie->exclusao();

        //exclui da table manutencao_paga
        $obTPatrimonioManutencaoPaga->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioManutencaoPaga->exclusao();

        //exclui da table manutencao
        $obTPatrimonioManutencao->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioManutencao->exclusao();

        $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
        $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioDepreciacaoReavaliacao->exclusao();

        //exclui da tabela reavaliação
        $obTPatrimonioReavaliacao->setDado('cod_bem', $inCodBem);
        $rsReavaliacao = new RecordSet();
        $obTPatrimonioReavaliacao->recuperaRelacionamento($rsReavaliacao);

        while (!$rsReavaliacao->eof()) {
            $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
            $obTPatrimonioReavaliacao->setDado('cod_bem', $inCodBem);
            $obTPatrimonioReavaliacao->setDado('cod_reavaliacao', $rsReavaliacao->getCampo('cod_reavaliacao'));
            $obTPatrimonioReavaliacao->exclusao();
            $rsReavaliacao->proximo();
        }
        
        //exclui da tabela depreciação
        $obTPatrimonioDepreciacao->setDado('cod_bem', $inCodBem);
        $rsDepreciacao = new RecordSet();
        $obTPatrimonioDepreciacao->recuperaDepreciacao($rsDepreciacao);

        while (!$rsDepreciacao->eof()) {
            $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
            $obTPatrimonioDepreciacao->setDado('cod_bem', $inCodBem);
            $obTPatrimonioDepreciacao->setDado('cod_depreciacao', $rsDepreciacao->getCampo('cod_depreciacao'));
            $obTPatrimonioDepreciacao->exclusao();
            $rsDepreciacao->proximo();
        }
        
        //exclui da tabela bem_plano_depreciacao
        $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioBemPlanoDepreciacao->exclusao();

        //exclui da tabela bem_plano_conta_analica
        $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio',  Sessao::getExercicio());
        $obTPatrimonioBemPlanoAnalitica->recuperaBemPlanoAnalitica($rsBemPlanoAnalitica);

        while (!$rsBemPlanoAnalitica->eof()) {
            $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica();
            $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
            $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio',  Sessao::getExercicio());
            $obTPatrimonioBemPlanoAnalitica->setDado( 'timestamp', $rsBemPlanoAnalitica->getCampo('timestamp'));
            $obTPatrimonioBemPlanoAnalitica->exclusao();
            $rsBemPlanoAnalitica->proximo();
        }

        $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
        $obTTGOPatrimonioBemObra->setDado('cod_bem', $_REQUEST['inCodBem']);
        $obTTGOPatrimonioBemObra->exclusao();

        $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
        $obTPatrimonioBemProcesso->exclusao();
        
        $obTPatrimonioBem->setDado( 'cod_bem', $inCodBem );
        $obTPatrimonioBem->recuperaPorChave( $rsBem );
        $obTPatrimonioBem->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem: ".$inCodBem.' - '.$rsBem->getCampo('descricao'),"excluir","aviso", Sessao::getId(), "../");

    break;
}

Sessao::encerraExcecao();
