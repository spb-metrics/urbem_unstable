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
    * Pagina de processamento para Incluir Cadastro/Certificação
    * Data de Criação   : 03/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.13

    $Id: PRManterCertificacao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"   );
include_once( TLIC."TLicitacaoParticipanteCertificacao.class.php" );
include_once( TLIC."TLicitacaoCertificacaoDocumentos.class.php"   );
include_once( TLIC."TLicitacaoDocumentoAtributoValor.class.php"   );
include_once( TLIC."TLicitacaoDocumentosAtributos.class.php"      );

$stPrograma = "ManterCertificacao";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";
$pgGera       = "OCGeraCertificadoFornecedor.php";

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setPersistenteValores  (  new TLicitacaoDocumentoAtributoValor );
$obRCadastroDinamico->setCodCadastro( 1 );
$obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

$obTLicitacaoParticipanteCertificacao = new TLicitacaoParticipanteCertificacao();
$obTLicitacaoCertificacaoDocumentos   = new TLicitacaoCertificacaoDocumentos();
$obTLicitacaoDocumentoAtributoValor   = new TLicitacaoDocumentoAtributoValor();
$obTLicitacaoDocumentosAtributos      = new TLicitacaoDocumentosAtributos();

Sessao::getTransacao()->setMapeamento( $obTLicitacaoParticipanteCertificacao );
Sessao::getTransacao()->setMapeamento( $obTLicitacaoCertificacaoDocumentos );
Sessao::getTransacao()->setMapeamento( $obTLicitacaoDocumentoAtributoValor );
Sessao::getTransacao()->setMapeamento( $obTLicitacaoDocumentosAtributos );

switch ($stAcao) {
    case "incluir":
        //if ( implode(array_reverse(explode('/',$_REQUEST['dtDataVigencia']))) < date('Ymd') ) {
        //        $stMensagem = 'Data de vigência inferior ao dia de hoje.';
        //}
        if ( implode(array_reverse(explode('/',$_REQUEST['dtDataRegistro']))) > implode(array_reverse(explode('/',$_REQUEST['dtDataVigencia']))) ) {
                $stMensagem = 'A data de registro deve ser menor que a data de vigência.';
        } elseif ( count( Sessao::read('arDocs') ) == 0 ) {
                $stMensagem = 'Ao menos um documento deve ser incluído.';
        }

        if (!$stMensagem) {

            $obTLicitacaoParticipanteCertificacao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cgm_fornecedor', $_REQUEST['inCodFornecedor'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_tipo_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'dt_registro', $_REQUEST['dtDataRegistro'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'final_vigencia', $_REQUEST['dtDataVigencia'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'observacao', $_REQUEST['hdnObservacao'] );

            $obTLicitacaoParticipanteCertificacao->inclusao();

            foreach ( Sessao::read('arDocs') as $key => $value ) {
                $obTLicitacaoCertificacaoDocumentos->obTLicitacaoParticipanteCertificacao = & $obTLicitacaoParticipanteCertificacao;
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cod_documento', $value['cod_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cgm_fornecedor', $_REQUEST['inCodFornecedor'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'num_documento', $value['num_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_emissao', $value['data_emissao'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_validade', $value['data_validade'] );
                $obTLicitacaoCertificacaoDocumentos->inclusao();

                if ( is_array($value['atributos']) ) {
                    foreach ($value['atributos'] as $key => $value2) {
                        $arKey = explode('_',$key);
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_documento', $value['cod_documento'] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_cadastro', $arKey[2] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_modulo', 37 );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_atributo', $arKey[1] );

                        $obTLicitacaoDocumentoAtributoValor->obTLicitacaoCertificacaoDocumentos = & $obTLicitacaoCertificacaoDocumentos;
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'valor', $value2 );
                        $obTLicitacaoDocumentoAtributoValor->inclusao();
                    }
                }
            }
            SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao",'Incluir Cadastro / Certificação concluído com sucesso ('.str_pad($obTLicitacaoCertificacaoDocumentos->getDado("num_certificacao"),6,"0",STR_PAD_LEFT).'/'.$obTLicitacaoCertificacaoDocumentos->getDado("exercicio").')! ', "", "aviso", Sessao::getId(), "../");
            $requestTMP = $_REQUEST;
            $requestTMP['request']['inNumCertificacao'] = str_pad($obTLicitacaoCertificacaoDocumentos->getDado("num_certificacao"),6,"0",STR_PAD_LEFT);
            $requestTMP['stExercicio'] = $obTLicitacaoCertificacaoDocumentos->getDado("exercicio");

            Sessao::write('request' , $requestTMP);

            $stLink = '&inCodFornecedor='.$_REQUEST['inCodFornecedor'];
            $stLink.= '&stExercicio='.$obTLicitacaoCertificacaoDocumentos->getDado("exercicio"       );
            $stLink.= '&inNumCertificacao='.str_pad($obTLicitacaoCertificacaoDocumentos->getDado("num_certificacao"),6,"0",STR_PAD_LEFT);
            $stLink.= '&dtDataRegistro='.$_REQUEST['dtDataRegistro'];
            $stLink.= '&dtDataVigencia='.$_REQUEST['dtDataVigencia'];
            SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId().$stLink);
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem),"n_alterar","erro");
        }
    break;

    case 'alterar':

        if ( count( Sessao::read('arDocs') ) == 0 ) {
            $stMensagem = 'Ao menos um documento deve ser incluído.';
        }
        if (!$stMensagem) {
            $obTLicitacaoParticipanteCertificacao->setDado( 'exercicio', $_REQUEST['stHdnExercicio'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cgm_fornecedor', $_REQUEST['inHdnCodFornecedor'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_tipo_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'cod_documento', 0 );
            $obTLicitacaoParticipanteCertificacao->setDado( 'dt_registro', $_REQUEST['dtHdnDataRegistro'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'final_vigencia', $_REQUEST['dtHdnDataVigencia'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'observacao', $_REQUEST['hdnObservacao'] );
            $obTLicitacaoParticipanteCertificacao->setDado( 'num_certificacao', intval($_REQUEST['inNumCertificacao']) );

            $obTLicitacaoParticipanteCertificacao->alteracao();

            // verificação da chave do array de itens
            $obTLicitacaoCertificacaoDocumentos->obTLicitacaoParticipanteCertificacao = & $obTLicitacaoParticipanteCertificacao;
            $obTLicitacaoCertificacaoDocumentos->recuperaPorChave( $rsItens );

            while ( !$rsItens->eof() ) {
                $obTLicitacaoDocumentoAtributoValor->obTLicitacaoCertificacaoDocumentos = & $obTLicitacaoCertificacaoDocumentos;
                $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_documento', $rsItens->getCampo('cod_documento') );
                $obTLicitacaoDocumentoAtributoValor->exclusao();
                $rsItens->proximo();
            }
            $obTLicitacaoCertificacaoDocumentos->exclusao();

            $arDocs = Sessao::read('arDocs');

            foreach ($arDocs as $key => $value) {
                $obTLicitacaoCertificacaoDocumentos->obTLicitacaoParticipanteCertificacao = & $obTLicitacaoParticipanteCertificacao;
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cod_documento', $value['cod_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'num_documento', $value['num_documento'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_emissao', $value['data_emissao'] );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'dt_validade', $value['data_validade'] );
                $obTLicitacaoCertificacaoDocumentos->inclusao();

                if ( is_array($value['atributos']) && ( count($value['atributos']) > 0 )) {
                    foreach ($value['atributos'] as $key => $value2) {
                        $arKey = explode('_',$key);
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_documento', $value['cod_documento'] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_cadastro', $arKey[2] );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_modulo', 37 );
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'cod_atributo', $arKey[1] );
                        $obTLicitacaoDocumentoAtributoValor->obTLicitacaoCertificacaoDocumentos = & $obTLicitacaoCertificacaoDocumentos;
                        $obTLicitacaoDocumentoAtributoValor->setDado( 'valor', $value2 );
                        $obTLicitacaoDocumentoAtributoValor->inclusao();
                    }
                }
            }
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao", "Número da certificação: ".$_REQUEST['inNumCertificacao']."/".$_REQUEST['stHdnExercicio'], "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem),"n_alterar","erro");
        }
    break;
}
Sessao::encerraExcecao();
