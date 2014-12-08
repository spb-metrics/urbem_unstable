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
/*
    * Página Oculto para publicação do contrato
    * Data de Criação   : 10/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * $Id: PRManterContrato.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.23
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
//include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php"          );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContrato.class.php"           );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoArquivo.class.php"    );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoAnulado.class.php"    );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoDocumento.class.php"  );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoAditivos.class.php"   );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoLicitacao.class.php"  );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoAditivos.class.php"   );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoContrato.class.php" );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" 	     );
include_once(TLIC."TLicitacaoDocumento.class.php");

$stAcao = $request->get('stAcao');

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGeraContrato.php";

$obTContrato = new TLicitacaoContrato();
$obTLicitacaoContratoLicitacao = new TLicitacaoContratoLicitacao();
$obTContratoAditivos = new TLicitacaoContratoAditivos;
$obTContratoAditivos->obTLicitacaoContrato = &$obTContrato;
$obTContratoDocumento = new TLicitacaoContratoDocumento;
$obTContratoDocumento->obTLicitacaoContrato = &$obTContrato;
$obTContratoAnulado = new TLicitacaoContratoAnulado;
$obTPublicacaoContrato = new TLicitacaoPublicacaoContrato;
$obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTContrato );
Sessao::getTransacao()->setMapeamento( $obTContratoDocumento );
Sessao::getTransacao()->setMapeamento( $obTContratoAditivos );
Sessao::getTransacao()->setMapeamento( $obTContratoAnulado );
Sessao::getTransacao()->setMapeamento( $obTPublicacaoContrato );
Sessao::getTransacao()->setMapeamento( $obTLicitacaoContratoArquivo );

$arDocumentos = Sessao::read('arDocumentos');
$arValores = Sessao::read('arValores');

switch ($stAcao) {
    case "incluir":

        if (strlen($_REQUEST['nmValorGarantiaExecucao']) < 19) {

            $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
            $obTLicitacaoLicitacao->setDado( 'cod_licitacao'	,$_REQUEST['inCodLicitacao'	] 	);
            $obTLicitacaoLicitacao->setDado( 'cod_modalidade'	,$_REQUEST['inCodModalidade'] 	);
            $obTLicitacaoLicitacao->setDado( 'cod_entidade'		,$_REQUEST['inCodEntidade'] 	);
            $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );

            unset($stMensagem);

            $obLicitacaoDocumento   = new TLicitacaoDocumento();
            $obLicitacaoDocumento->recuperaTodos( $rsDocumentosLicitacao,$stFiltro,' ORDER BY nom_documento ' );

            $numDocumentosLicitacao  = count($rsDocumentosLicitacao->arElementos);
            $numDocumentosParticipante = count($arDocumentos);

            //    Exige ao menos um documento, seja lançado manual ou automático pela licitação.
            if ($numDocumentosParticipante == 0 && $numDocumentosLicitacao == 0) {
                $stMensagem = 'É necessário informar todos os documentos do participante!';
            }

            // exige que seja ao menos lançado um documento para o participante
            if ($numDocumentosParticipante == 0 && $numDocumentosLicitacao > 0) {
                $stMensagem = 'É necessário informar ao menos um documento do participante!';
            }

            if ( implode(array_reverse(explode('/',$_REQUEST['dtVencimento']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) ) {
                $stMensagem = 'A data de vencimento deve ser igual ou superior a data de assinatura! ('.$_REQUEST['dtAssinatura'].')';
            } elseif ( implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) <= implode(array_reverse(explode('/',$rsLicitacao->getCampo('dt_licitacao')))) ) {
                $stMensagem = 'A data de assinatura deve ser superior a data de inclusão da licitação! ('.$rsLicitacao->getCampo('dt_licitacao').')';
            } elseif ( implode(array_reverse(explode('/',$_REQUEST['dtInicioExecucao']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) ) {
                $stMensagem = 'A data de Início da execução deve ser igual ou maior a data de assinatura do contrato! ('.$_REQUEST['dtAssinatura'].')';
            } elseif ( implode(array_reverse(explode('/',$_REQUEST['dtFimExecucao']))) < implode(array_reverse(explode('/',$_REQUEST['dtInicioExecucao']))) ) {
                $stMensagem = 'A data de Fim de execução deve ser igual ou maior a data de Início da execução! ('.$_REQUEST['dtInicioExecucao'].')';
            } elseif ( !strstr($_REQUEST['nmValorGarantiaExecucao'],',')) {
                $stMensagem = 'Valor da Garantia de Execução inválido!';
            }

           if ( count( $arValores ) <= 0 ) {
                $stMensagem = 'É necessário pelo menos um veículo de publicação!';
            } else {
                foreach ($arValores as $arTemp) {
                    if ( implode('',array_reverse(explode('/',$arTemp['dtDataPublicacao']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) ) {
                        $stMensagem = 'A data de publicação do veículo '.$arTemp['inVeiculo'].' deve ser maior ou igual a data de assinatura do contrato!';
                        break;
                    }

                }
            }
            if ($stMensagem == '') {

                $obTContrato->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                $obTContrato->setDado('exercicio',Sessao::getExercicio());
                $obTContrato->setDado('cgm_responsavel_juridico',$_REQUEST['inCGM']);
                $obTContrato->setDado('cgm_contratado',$_REQUEST['inCGMContratado']);

                $obTContrato->setDado('cod_documento', 0 );
                $obTContrato->setDado('cod_tipo_documento', 0 );

                $obTContrato->setDado('dt_assinatura', $_REQUEST['dtAssinatura']);
                $obTContrato->setDado('vencimento', $_REQUEST['dtVencimento']);
                $obTContrato->setDado('valor_garantia', str_replace(',','.',str_replace('.','',$_REQUEST['nmValorGarantiaExecucao'])) );
                $obTContrato->setDado('valor_contratado', $_REQUEST['hdnValorContrato'] );

                $obTContrato->setDado('inicio_execucao' , $_REQUEST['dtInicioExecucao'] );
                $obTContrato->setDado('fim_execucao'    , $_REQUEST['dtFimExecucao'] );
                $obTContrato->setDado('cod_tipo_contrato'    , $_REQUEST['inTipoContrato'] );

                $obTContrato->inclusao();

                $inCountDocumentos = count($arDocumentos);
                for ($inPosTransf = 0; $inPosTransf < $inCountDocumentos; $inPosTransf++) {
                    $obTContratoDocumento->setDado('cod_documento',$arDocumentos[$inPosTransf]["inCodDocumento"]);
                    $obTContratoDocumento->setDado('num_documento',$arDocumentos[$inPosTransf]["stNumDocumento"]);
                    $obTContratoDocumento->setDado('dt_validade',$arDocumentos[$inPosTransf]["dtValidade"]);
                    $obTContratoDocumento->setDado('dt_emissao', $arDocumentos[$inPosTransf]["dtEmissao"]);
                    $obTContratoDocumento->inclusao();
                }

                //inclui os dados da publicacao do contrato
                foreach ($arValores as $arTemp) {
                    $obTPublicacaoContrato->setDado( 'num_contrato', $obTContrato->getDado('num_contrato') );
                    $obTPublicacaoContrato->setDado( 'numcgm', $arTemp['inVeiculo'] );
                    $obTPublicacaoContrato->setDado( 'dt_publicacao', $arTemp['dtDataPublicacao'] );
                    $obTPublicacaoContrato->setDado( 'num_publicacao',$arTemp['inNumPublicacao'] );
                    $obTPublicacaoContrato->setDado( 'exercicio', Sessao::getExercicio() );
                    $obTPublicacaoContrato->setDado( 'cod_entidade', $_REQUEST['inCodEntidade'] );
                    $obTPublicacaoContrato->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
                    $obTPublicacaoContrato->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
                    $obTPublicacaoContrato->setDado( 'observacao', $arTemp['stObservacao'] );
                    $obTPublicacaoContrato->inclusao();
                }

                $obTLicitacaoContratoLicitacao->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                $obTLicitacaoContratoLicitacao->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                $obTLicitacaoContratoLicitacao->setDado('exercicio', Sessao::getExercicio());
                $obTLicitacaoContratoLicitacao->setDado('exercicio_licitacao', $_REQUEST['stExercicioLicitacao']);
                $obTLicitacaoContratoLicitacao->setDado('cod_licitacao',$_REQUEST['inCodLicitacao']);
                $obTLicitacaoContratoLicitacao->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);

                $obTLicitacaoContratoLicitacao->inclusao();

                $obTOrcamentoEntidade = new TOrcamentoEntidade();
                $stFiltro = " AND 	E.cod_entidade = ".$obTContrato->getDado('cod_entidade')." ";
                $obTOrcamentoEntidade->recuperaRelacionamento( $rsEntidade, $stFiltro );

                /***************************************************************************************************************/
                $pathToSave = CAM_GP_LIC_ANEXOS.'contrato/';
                $arquivos   = array(array());
                $i = 0;

                foreach ($_FILES as $key=>$info) {
                    foreach ($info as $key=>$dados) {
                    for ( $i = 0; $i < sizeof( $dados ); $i++ ) {
                        $arquivos[$i][$key] = $info[$key][$i];
                    }
                    }
                }

                // Fazemos o upload normalmente, igual no exemplo anterior
                $i = 0;
                foreach ($arquivos as $file) {
                    // Verificar se o campo do arquivo foi preenchido
                    if ($file['name'] != '') {
                    $arquivoTmp   = $file['tmp_name'];
                    $arquivoUnico = md5(microtime()).$i.$file['name'];

                    if (move_uploaded_file( $arquivoTmp, $pathToSave.$arquivoUnico )) {
                        $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
                        $obTLicitacaoContratoArquivo->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                        $obTLicitacaoContratoArquivo->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                        $obTLicitacaoContratoArquivo->setDado('exercicio'   , $obTContrato->getDado('exercicio'));
                        $obTLicitacaoContratoArquivo->setDado('nom_arquivo' , $file['name']);
                        $obTLicitacaoContratoArquivo->setDado('arquivo'     , $arquivoUnico);
                        $obTLicitacaoContratoArquivo->inclusao();
                    }
                    }

                    $i++;
                }
                /***************************************************************************************************************/

                SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$obTContrato->getDado('num_contrato')."/".$obTContrato->getDado('exercicio')." da entidade ".$obTContrato->getDado('cod_entidade')." - ".$rsEntidade->getCampo('nom_cgm'),"incluir", "aviso", Sessao::getId(),"");

                if ($_REQUEST['boImprimirContrato']) {
                $arRelatorio = array();
                include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                 );
                $obTEntidade = new TOrcamentoEntidade;
                $obTEntidade->setDado('cod_entidade', $_REQUEST['inCodEntidade'] );
                $obTEntidade->setDado('exercicio', Sessao::getExercicio());
                $obTEntidade->recuperaRelacionamentoNomes($rsRelatorio);
                $arRelatorio['nomEntidade'] = $rsRelatorio->getCampo('entidade');
                $arRelatorio['nomPrefeito'] = $rsRelatorio->getCampo('responsavel');
                $obTEntidade = new TCGM;
                $stFiltro = " and CGM.numcgm = ".$rsRelatorio->getCampo('numcgm');;
                $obTEntidade->recuperaRelacionamentoSintetico($rsRelatorio, $stFiltro);
                $arRelatorio['cgcEntidade'] = $rsRelatorio->getCampo('documento');
                include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"                                 );
                $obTFornecedor = new TCGM;
                $obTFornecedor->setDado('numcgm',$_REQUEST['inCGMContratado'] );
                $obTFornecedor->recuperaRelacionamentoFornecedor($rsFornecedor);

                $arRelatorio['cgcFornecedor'] = $rsFornecedor->getCampo('documento');
                $arRelatorio['cgmFornecedor'] = $rsFornecedor->getCampo('numcgm');
                $arRelatorio['nomFornecedor'] = $rsFornecedor->getCampo('nom_cgm');
                $arRelatorio['nom_logradouro'] = $rsFornecedor->getCampo('tipo_logradouro').' '.$rsFornecedor->getCampo('logradouro').' '.$rsFornecedor->getCampo('numero').' '.$rsFornecedor->getCampo('complemento').', '.$rsFornecedor->getCampo('bairro').', '.$rsFornecedor->getCampo('cidade').'/'.$rsFornecedor->getCampo('uf');
                $arRelatorio['nomRepresentante'] = $_REQUEST['stNomCGM'];
                $arRelatorio['cgmRepresentante'] = $_REQUEST['inCGM'];
                $arRelatorio['dataInicio'] = $_REQUEST['dtAssinatura'];
                $arRelatorio['dataVigencia'] = $_REQUEST['dtVencimento'];

                $arRelatorio['numContrato'] = $obTContrato->getDado('num_contrato');
                $arRelatorio['codModalidade'] = $_REQUEST['inCodModalidade'];
                $arRelatorio['codLicitacao'] = $_REQUEST['inCodLicitacao'];
                $arRelatorio['codEntidade'] = $_REQUEST['inCodEntidade'];
                $arRelatorio['descObjeto'] = $_REQUEST['hdnDescObjeto'];

                //CONSULTANDO ARQUIVO TEMPLATE
                include_once( TADM.'TAdministracaoModeloArquivosDocumento.class.php');
                $obTAdministracaoArquivosDocumento = new TAdministracaoModeloArquivosDocumento();
                $obTAdministracaoArquivosDocumento->setDado( 'cod_acao', Sessao::read('acao') );
                $obTAdministracaoArquivosDocumento->setDado( 'cod_documento', 0);
                $obTAdministracaoArquivosDocumento->recuperaDocumentos( $rsTemplate );
                $arRelatorio['nomDocumentoSxw'] =$rsTemplate->getCampo('nome_arquivo_template');
                Sessao::write('arRelatorio', $arRelatorio);
                SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
                 }
            } else {
                sistemaLegado::exibeAviso(urlencode($stMensagem),'n_incluir','erro');
            }
        } else {
            sistemaLegado::exibeAviso(urlencode('O valor de garantia informado é maior que o permitido!'),'n_incluir','erro');
        }
    break;

    case "alterar":
            if (strlen($_REQUEST['nmValorGarantiaExecucao']) < 19) {
        $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
        $obTLicitacaoLicitacao->setDado( 'cod_licitacao'	,$_REQUEST['inCodLicitacao'	] 	);
        $obTLicitacaoLicitacao->setDado( 'cod_modalidade'	,$_REQUEST['inCodModalidade'] 	);
        $obTLicitacaoLicitacao->setDado( 'cod_entidade'		,$_REQUEST['inCodModalidade'] 	);
        $obTLicitacaoLicitacao->setDado( 'cod_exercicio'    ,Sessao::getExercicio()	);
        $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );

        unset($stMensagem);

        if ( implode(array_reverse(explode('/',$_REQUEST['dtVencimento']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) ) {
            $stMensagem = 'A data de vencimento deve ser igual ou superior a data de assinatura! ('.$_REQUEST['dtAssinatura'].')';
        } elseif ( SistemaLegado::comparaDatas($rsLicitacao->getCampo('dt_licitacao'),$_REQUEST['dtAssinatura'])) {
            $stMensagem = 'A data de assinatura deve ser superior a data de inclusão da licitação! ('.$rsLicitacao->getCampo('dt_licitacao').')';
        } elseif ( implode(array_reverse(explode('/',$_REQUEST['dtInicioExecucao']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) ) {
            $stMensagem = 'A data de Início da execução deve ser igual ou maior a data de assinatura do contrato! ('.$_REQUEST['dtAssinatura'].')';
        } elseif ( implode(array_reverse(explode('/',$_REQUEST['dtFimExecucao']))) < implode(array_reverse(explode('/',$_REQUEST['dtInicioExecucao']))) ) {
            $stMensagem = 'A data de Fim de execução deve ser igual ou maior a data de Início da execução! ('.$_REQUEST['dtInicioExecucao'].')';
        } elseif ( !strstr($_REQUEST['nmValorGarantiaExecucao'],',')) {
            $stMensagem = 'Valor da Garantia de Execução inválido!';
        }
        if ( count( $arValores ) <= 0 ) {
            $stMensagem = 'É necessário pelo menos um veículo de publicação!';
        }
        if ( count( $arDocumentos ) <= 0 ) {
            $stMensagem = 'É necessário informar ao menos um documento do participante!';
        } else {
            foreach ($arValores as $arTemp) {
            if ( implode('',array_reverse(explode('/',$arTemp['dtDataPublicacao']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) ) {
                $stMensagem = 'A data de publicação do veículo '.$arTemp['inVeiculo'].' deve ser maior ou igual a data de assinatura do contrato!';
                break;
            }
            }
        }

        if ($stMensagem == '') {
            $obTContrato->setDado('cod_licitacao',$_REQUEST['inCodLicitacao']);
            $obTContrato->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
            $obTContrato->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
            $obTContrato->setDado('exercicio',Sessao::getExercicio());
            $obTContrato->setDado('num_contrato',$_REQUEST['inNumContrato']);
            $obTContrato->setDado('cgm_responsavel_juridico',$_REQUEST['inCGM']);
            $obTContrato->setDado('cgm_contratado',$_REQUEST['inCGMContratado']);

            $obTContrato->setDado('inicio_execucao' , $_REQUEST['dtInicioExecucao'] );
            $obTContrato->setDado('fim_execucao'    , $_REQUEST['dtFimExecucao'] );

            $obTContrato->setDado('cod_documento', 0 );
            $obTContrato->setDado('cod_tipo_documento', 0);

            $obTContrato->setDado('dt_assinatura', $_REQUEST['dtAssinatura']);
            $obTContrato->setDado('vencimento', $_REQUEST['dtVencimento']);
            $obTContrato->setDado('valor_garantia', str_replace(',','.',str_replace('.','',$_REQUEST['nmValorGarantiaExecucao'])) );
            $obTContrato->setDado('valor_contratado', $_REQUEST['hdnValorContrato']);

            $obTContrato->alteracao();

            $inCountDocumentos = count($arDocumentos);

            $obTContratoDocumento->exclusao();

            for ($inPosTransf = 0; $inPosTransf < $inCountDocumentos; $inPosTransf++) {
                $obTContratoDocumento->setDado('cod_documento',       $arDocumentos[$inPosTransf]["inCodDocumento"]);
                $obTContratoDocumento->setDado('num_documento',$arDocumentos[$inPosTransf]["stNumDocumento"]);
                $obTContratoDocumento->setDado('dt_validade', $arDocumentos[$inPosTransf]["dtValidade"]);
                $obTContratoDocumento->setDado('dt_emissao',  $arDocumentos[$inPosTransf]["dtEmissao"]);
                $obTContratoDocumento->inclusao();
            }

            for ($inPosTransf = 0; $inPosTransf < $inCountDocumentos; $inPosTransf++) {
            if ($arDocumentos[$inPosTransf]['boAlterado']) {
                $obTContratoDocumento->setDado('cod_documento', $arDocumentos[$inPosTransf]["inCodDocumento"]);
                $obTContratoDocumento->setDado('num_documento', $arDocumentos[$inPosTransf]["stNumDocumento"]);
                $obTContratoDocumento->setDado('dt_validade',   $arDocumentos[$inPosTransf]["dtValidade"]);
                $obTContratoDocumento->setDado('dt_emissao',    $arDocumentos[$inPosTransf]["dtEmissao"]);
                $obTContratoDocumento->alteracao();
            }
            }

            $obTContratoAditivos->exclusao();
            $obTContratoAditivos->setCampoCod('num_contrato');
            $arAditivos = Sessao::read('arAditivos');
            $inCountAditivos = count($arAditivos);
            for ($inPosTransf = 0; $inPosTransf < $inCountAditivos; $inPosTransf++) {
            $obTContratoAditivos->setDado('cod_norma',$arAditivos[$inPosTransf]["inCodNorma"]);
            $obTContratoAditivos->setDado('dt_vencimento',$arAditivos[$inPosTransf]["dtVencimento"]);
            $obTContratoAditivos->inclusao();
            }

            //exclui os veiculos de publicidade existentes
            $obTPublicacaoContrato->setDado( 'num_contrato', $_REQUEST['inNumContrato'] );
            $obTPublicacaoContrato->setDado( 'exercicio', Sessao::getExercicio() );
            $obTPublicacaoContrato->setDado( 'cod_entidade', $_REQUEST['inCodEntidade'] );
            $obTPublicacaoContrato->exclusao();

            //inclui os veiculos que estao na sessao
            foreach ($arValores as $arTemp) {
            $obTPublicacaoContrato->setDado( 'numcgm', $arTemp['inVeiculo'] );
            $obTPublicacaoContrato->setDado( 'dt_publicacao', $arTemp['dtDataPublicacao'] );
            $obTPublicacaoContrato->setDado( 'num_publicacao', $arTemp['inNumPublicacao'] );
            $obTPublicacaoContrato->setDado( 'num_licitacao', $_REQUEST['inNumPublicacao'] );
            $obTPublicacaoContrato->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
            $obTPublicacaoContrato->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
            $obTPublicacaoContrato->setDado( 'observacao', $arTemp['stObservacao'] );
            $obTPublicacaoContrato->inclusao();
            }
            include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                 );
            $obTEntidade = new TOrcamentoEntidade;
            $obTEntidade->setDado('cod_entidade', $_REQUEST['inCodEntidade'] );
            $obTEntidade->setDado('exercicio', Sessao::getExercicio());
            $obTEntidade->recuperaRelacionamentoNomes($rsRelatorio);

            /***************************************************************************************************************/
            //DELETA ARQUIVOS ANTIGOS
            $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
            $obTLicitacaoContratoArquivo->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
            $obTLicitacaoContratoArquivo->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
            $obTLicitacaoContratoArquivo->setDado('exercicio'   , $obTContrato->getDado('exercicio'));
            $obTLicitacaoContratoArquivo->excluirArquivos();

            $pathToSave = CAM_GP_LIC_ANEXOS.'contrato/';
            $arquivos   = array(array());
            $i = 0;

            foreach ($_FILES as $key=>$info) {
            foreach ($info as $key=>$dados) {
                for ( $i = 0; $i < sizeof( $dados ); $i++ ) {
                $arquivos[$i][$key] = $info[$key][$i];
                }
            }
            }

            // Fazemos o upload normalmente, igual no exemplo anterior
            $i = 0;
            foreach ($arquivos as $file) {
            // Verificar se o campo do arquivo foi preenchido
            if ($file['name'] != '') {
                $arquivoTmp   = $file['tmp_name'];
                $arquivoUnico = md5(microtime()).$i.$file['name'];

                if (move_uploaded_file( $arquivoTmp, $pathToSave.$arquivoUnico )) {
                $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
                $obTLicitacaoContratoArquivo->setDado('num_contrato', $obTContrato->getDado('num_contrato'));
                $obTLicitacaoContratoArquivo->setDado('cod_entidade', $obTContrato->getDado('cod_entidade'));
                $obTLicitacaoContratoArquivo->setDado('exercicio'   , $obTContrato->getDado('exercicio'));
                $obTLicitacaoContratoArquivo->setDado('nom_arquivo' , $file['name']);
                $obTLicitacaoContratoArquivo->setDado('arquivo'     , $arquivoUnico);
                $obTLicitacaoContratoArquivo->inclusao();
                }
            }

            $i++;
            }

            $arArquivos = Sessao::read('arArquivos');
            foreach ($arArquivos as $arquivo) {
            $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
            $obTLicitacaoContratoArquivo->setDado('num_contrato', $arquivo['num_contrato']);
            $obTLicitacaoContratoArquivo->setDado('cod_entidade', $arquivo['cod_entidade']);
            $obTLicitacaoContratoArquivo->setDado('exercicio'   , $arquivo['exercicio']);
            $obTLicitacaoContratoArquivo->setDado('nom_arquivo' , $arquivo['nom_arquivo']);
            $obTLicitacaoContratoArquivo->setDado('arquivo'     , $arquivo['arquivo']);
            $obTLicitacaoContratoArquivo->inclusao();
            }
            /***************************************************************************************************************/

            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$obTContrato->getDado('num_contrato')."/".$obTContrato->getDado('exercicio')." da entidade ".$obTContrato->getDado('cod_entidade')." - ". $rsRelatorio->getCampo('entidade'),"alterar", "aviso", Sessao::getId(),"");

            if ($_REQUEST['boImprimirContrato']) {
            $arRelatorio = array();
            $arRelatorio['nomEntidade'] = $rsRelatorio->getCampo('entidade');
            $arRelatorio['nomPrefeito'] = $rsRelatorio->getCampo('responsavel');
            $obTEntidade = new TCGM;
            $stFiltro = " and CGM.numcgm = ".$rsRelatorio->getCampo('numcgm');;
            $obTEntidade->recuperaRelacionamentoSintetico($rsRelatorio, $stFiltro);
            $arRelatorio['cgcEntidade'] = $rsRelatorio->getCampo('documento');
            include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"                                 );
            $obTFornecedor = new TCGM;
            $obTFornecedor->setDado('numcgm',$_REQUEST['inCGMContratado'] );
            $obTFornecedor->recuperaRelacionamentoFornecedor($rsFornecedor, $stFiltro);
            $arRelatorio['cgcFornecedor'] = $rsFornecedor->getCampo('documento');
            $arRelatorio['nomFornecedor'] = $_REQUEST['stNomContratado'];
            $arRelatorio['nom_logradouro'] = $rsFornecedor->getCampo('tipo_logradouro').' '.$rsFornecedor->getCampo('logradouro').' '.$rsFornecedor->getCampo('numero').' '.$rsFornecedor->getCampo('complemento').', '.$rsFornecedor->getCampo('bairro').', '.$rsFornecedor->getCampo('cidade').'/'.$rsFornecedor->getCampo('uf');
            $arRelatorio['nomOrgao'] = '';
            $arRelatorio['nomUnidade'] = '';
            $arRelatorio['numContrato'] = $obTContrato->getDado('num_contrato');
            $arRelatorio['codModalidade'] = $_REQUEST['inCodModalidade'];
            $arRelatorio['codLicitacao'] = $_REQUEST['inCodLicitacao'];
            $arRelatorio['codEntidade'] = $_REQUEST['inCodEntidade'];
            $arRelatorio['descObjeto'] = $_REQUEST['hdnDescObjeto'];
            $arRelatorio['dataInicio'] = $_REQUEST['dtAssinatura'];
            $arRelatorio['dataVigencia'] = $_REQUEST['dtVencimento'];
            $arRelatorio['cgmFornecedor'] = $rsFornecedor->getCampo( 'numcgm' );

            $obTRepresentante = new TCGM();
            $obTRepresentante->setDado('numcgm',$_REQUEST['inCGM']);
            $obTRepresentante->recuperaRelacionamentoSintetico( $rsRepresentante );
            $arRelatorio['nomRepresentante'] = $rsRepresentante->getCampo('nom_cgm');
            $arRelatorio['cgmRepresentante'] = $_REQUEST['inCGM'];

            //CONSULTANDO ARQUIVO TEMPLATE
            include_once( TADM.'TAdministracaoModeloArquivosDocumento.class.php');
            $obTAdministracaoArquivosDocumento = new TAdministracaoModeloArquivosDocumento();
            $obTAdministracaoArquivosDocumento->setDado( 'cod_acao', Sessao::read('acao') );
            $obTAdministracaoArquivosDocumento->setDado( 'cod_documento', 0 );
            $obTAdministracaoArquivosDocumento->recuperaDocumentos( $rsTemplate );
            $arRelatorio['nomDocumentoSxw'] =$rsTemplate->getCampo('nome_arquivo_template');
            Sessao::write('arRelatorio', $arRelatorio);
            SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
            }
                } else {
            sistemaLegado::exibeAviso(urlencode($stMensagem),'n_incluir','erro');
                }
        } else {
            sistemaLegado::exibeAviso(urlencode('O valor de garantia informado é maior que o permitido!'),'n_incluir','erro');
        }
    break;

    case "anular";
            if ( ( sistemaLegado::comparaDatas( $_REQUEST['stDataAnulacao'], $_REQUEST['dtAssinatura'] ) ) || ( $_REQUEST['stDataAnulacao'] == $_REQUEST['dtAssinatura'] ) ) {
                $obTContratoAnulado->setDado('num_contrato',$_REQUEST['inNumContrato']);
                $obTContratoAnulado->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                $obTContratoAnulado->setDado('exercicio',Sessao::getExercicio());
                $obTContratoAnulado->setDado('dt_anulacao', $_REQUEST['stDataAnulacao']);
                $obTContratoAnulado->setDado('motivo', $_REQUEST['stMotivo']);
                $obTContratoAnulado->inclusao();
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$obTContratoAnulado->getDado('exercicio')." - ".$obTContratoAnulado->getDado('cod_entidade')." - ".$obTContratoAnulado->getDado('num_contrato'),"incluir", "aviso", Sessao::getId(),"");
            } else {
                sistemaLegado::exibeAviso(urlencode('<i><b>Data de Anulação</b></i> deve ser maior ou igual a <b><i>Data da Assinatura</i></b>.'),'n_anular','erro');
            }
        break;
    }

Sessao::encerraExcecao();
