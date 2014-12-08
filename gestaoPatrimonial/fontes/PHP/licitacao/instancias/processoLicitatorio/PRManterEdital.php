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
    * Pagina de formulário para Incluir Edital
    * Data de Criação   : 20/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: PRManterEdital.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( TLIC."TLicitacaoEdital.class.php" );
include_once ( TLIC."TLicitacaoComissao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEdital";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

// QUANDO TIVER O COMPONENTE QUE SELECIONA DOCUMENTOS E TEMPLATES
// ESSA VARIAVEL RECEBERA O VALOR DO DOCUMENTO SELECIONADO
$pgGera       = "OCGeraDocumentoEdital.php";

include_once( $pgJS );

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

$obTLicitacaoEdital = new TLicitacaoEdital();
Sessao::getTransacao()->setMapeamento( $obTLicitacaoEdital );

function buscaDataTerminoVigenciaComissao()
{
    $rsDataTermino = new RecordSet;
    $obTLicitacaoComissao = new TLicitacaoComissao;

    $obTLicitacaoComissao->setDado('cod_licitacao', $_REQUEST['inCodLicitacao'] );
    $obTLicitacaoComissao->setDado('cod_modalidade', $_REQUEST['inCodModalidade'] );
    $obTLicitacaoComissao->setDado('cod_entidade', $_REQUEST['inCodEntidade'] );
    $obTLicitacaoComissao->setDado('exercicio', $_REQUEST['stExercicioLicitacao'] );

    $obTLicitacaoComissao->recuperaDataTerminoComissao($rsDataTermino);

    return $rsDataTermino->getCampo('dt_termino');
}

// FUNÇÃO PARA COMPARAR DATAS RETORNANDO TRUE E A PRIMEIRA DATA FOR MAIOR OU IGUAL A SEGUNDA* a que tem no sistema está meio esquisita *
function cmpDt($dt1,$dt2)
{
    $arDt = explode('/',$dt1);
    $dt1  = $arDt[2].$arDt[1].$arDt[0];
    $arDt = explode('/',$dt2);
    $dt2  = $arDt[2].$arDt[1].$arDt[0];
    if ($dt1 >= $dt2) {
            return true;
    } else {
            return false;
    }
}

/**
 * Recebe uma data no formato DD/MM/YYYY e retorna a data no formato YYYY-MM-DD
 * @param string $data
 * @return string
 */
function dataYMD($data)
{
    $tmp = explode('/',$data);
    if (count($tmp) == 3) {
        return $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
    } else {
        return $data;
    }
}

switch ($stAcao) {
    case 'incluir':
            $stMensagem = '';

            //verifica se a data de abertura é superior a data de entrega
            if ( implode('',array_reverse(explode('/',$_REQUEST['dtEntrega']))) > implode('',array_reverse(explode('/',$_REQUEST['dtAbertura']))) && $stMensagem == '' ) {
                $stMensagem = 'Data e hora da abertura (<b><i>'.$_REQUEST['dtAbertura'].' '.$_REQUEST['stHoraAbertura'].'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$_REQUEST['dtEntrega'].' '.$_REQUEST['stHoraEntrega'].'</i></b>).';
            } elseif ( ($_REQUEST['dtEntrega'] == $_REQUEST['dtAbertura']) && ( str_replace(':','',$_REQUEST['stHoraEntrega']) > str_replace(':','',$_REQUEST['stHoraAbertura']) ) && $stMensagem == '' ) {
                $stMensagem = 'Data e hora da abertura (<b><i>'.$_REQUEST['dtAbertura'].' '.$_REQUEST['stHoraAbertura'].'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$_REQUEST['dtEntrega'].' '.$_REQUEST['stHoraEntrega'].'</i></b>).';
            }

            // VERIFICA SE A DATA DE APROVAÇÃO É SUPERIOR A DATA DE ENTREGA
            if ( ( cmpDt($_REQUEST['dtAprovacao'], $_REQUEST['dtEntrega']) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de aprovação do jurídico (<b><i>'.$_REQUEST['dtAprovacao'].'</i></b>) deve ser menor que a data de entrega (<b><i>'.$_REQUEST['dtEntrega'].'</i></b>).';
            }

            // VERIFICA SE A DATA DE VALIDADE É SUPERIOR A DATA DE ABERTURA
            if ( ( cmpDt($_REQUEST['dtAbertura'], $_REQUEST['dtValidade']) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de validade das propostas (<b><i>'.$_REQUEST['dtValidade'].'</i></b>) deve ser maior que a data de abertura das propostas(<b><i>'.$_REQUEST['dtAbertura'].'</i></b>).';
            }

            $dtInicio = dataYMD($_REQUEST['dtEntrega']).' 00:00:00';
            $dtFim = dataYMD($_REQUEST['dtValidade']).' 23:59:59';
            $qtd_dias_validade = SistemaLegado::datediff('d', $dtInicio, $dtFim);

            if ( ($_REQUEST['inCodEdital'] == '0') && $stMensagem =='' ) {
                $stMensagem = 'O número do edital inválido.';
            }
            //verifica se não existe um edital com o mesmo número no banco
            if ( ($_REQUEST['inCodEdital'] > 0 ) && $stMensagem == '' ) {
                $obTLicitacaoEdital->setDado( 'num_edital', $_REQUEST['inCodEdital'] );
                $obTLicitacaoEdital->setDado( 'exercicio', Sessao::getExercicio());
                $obTLicitacaoEdital->recuperaPorChave( $rsEdital );
                if ( $rsEdital->getNumLinhas() > 0 ) {
                    $stMensagem = 'Já existe um edital com este número.';
                }
            }

            $dtTerminoVigencia = buscaDataTerminoVigenciaComissao();
            // VERIFICA SE A DATA DE ABERTURA É SUPERIOR A DATA DE TERMINO DA VIGÊNCIA(COMISSÂO)
            if ( ( cmpDt($_REQUEST['dtAbertura'], $dtTerminoVigencia) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de abertura das propostas ( <b><i>'.$_REQUEST['dtAbertura'].'</i></b> ) deve ser menor ou igual a data de vigência da comissão de licitação( <b><i>'.$dtTerminoVigencia.'</i></b> )!';
            }

            if ($stMensagem == '') {
                if ($_REQUEST['inCodEdital'] != '') {
                    $obTLicitacaoEdital->setDado( 'num_edital'               , $_REQUEST['inCodEdital'] );
                }
                $obTLicitacaoEdital->setDado( 'exercicio'               , Sessao::getExercicio()                 );

                //*  POR QUE AINDA NÃO TEM O COMPONENTE QUE SELECIONA O DOCUMENTO O CODIGO TIPO E O CODIGO DO
                // DOCUMENTO ESTÃO FIXADOS COMO 0 (NÃO INFORMADO)

                $exercicioLicitacao = $_REQUEST['stExercicioLicitacao'];

                $obTLicitacaoEdital->setDado( 'cod_tipo_documento'      , 0    								 );
                $obTLicitacaoEdital->setDado( 'cod_documento'           , 0    							     );

                $obTLicitacaoEdital->setDado( 'responsavel_juridico'    , $_REQUEST['inResponsavelJuridico'] );
                $obTLicitacaoEdital->setDado( 'exercicio_licitacao'     ,  $exercicioLicitacao );
                $obTLicitacaoEdital->setDado( 'cod_entidade'            , $_REQUEST['inCodEntidade']         );
                $obTLicitacaoEdital->setDado( 'cod_modalidade'          , $_REQUEST['inCodModalidade']       );
                $obTLicitacaoEdital->setDado( 'cod_licitacao'           , $_REQUEST['inCodLicitacao']        );
                $obTLicitacaoEdital->setDado( 'local_entrega_propostas' , $_REQUEST['stLocalEntrega']        );
                $obTLicitacaoEdital->setDado( 'dt_entrega_propostas'    , $_REQUEST['dtEntrega']             );
                $obTLicitacaoEdital->setDado( 'hora_entrega_propostas'  , $_REQUEST['stHoraEntrega']         );
                $obTLicitacaoEdital->setDado( 'local_abertura_propostas', $_REQUEST['stLocalAbertura']       );
                $obTLicitacaoEdital->setDado( 'dt_abertura_propostas'   , $_REQUEST['dtAbertura']            );
                $obTLicitacaoEdital->setDado( 'hora_abertura_propostas' , $_REQUEST['stHoraAbertura']        );
                $obTLicitacaoEdital->setDado( 'dt_validade_proposta'    , $_REQUEST['dtValidade']            );
                $obTLicitacaoEdital->setDado( 'observacao_validade_proposta', $_REQUEST['txtValidade']       );
                $obTLicitacaoEdital->setDado( 'condicoes_pagamento'     , stripslashes(stripslashes($_REQUEST['txtCodPagamento']))     );
                $obTLicitacaoEdital->setDado( 'local_entrega_material'  , $_REQUEST['stLocalMaterial']       );
                $obTLicitacaoEdital->setDado( 'dt_aprovacao_juridico'   , $_REQUEST['dtAprovacao']           );

                $obTLicitacaoEdital->inclusao();
            }

            if ($stMensagem == '') {
                $_REQUEST['qtdDiasValidade'] = $qtd_dias_validade;
                sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Edital: ".$obTLicitacaoEdital->getDado('num_edital')."/".Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
                Sessao::write('request', $_REQUEST);
                if ($_REQUEST['boGerarDocumento'] == 'S') {
                    SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
                }
            } else {
                sistemaLegado::exibeAviso(urlencode($stMensagem),'n_incluir','erro');
            }
    break;

    case 'alterar':

        //verifica se a data de abertura é superior a data de entrega
            if ( implode('',array_reverse(explode('/',$_REQUEST['dtEntrega']))) > implode('',array_reverse(explode('/',$_REQUEST['dtAbertura']))) && $stMensagem == '' ) {
                $stMensagem = 'Data e hora da abertura (<b><i>'.$_REQUEST['dtAbertura'].' '.$_REQUEST['stHoraAbertura'].'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$_REQUEST['dtEntrega'].' '.$_REQUEST['stHoraEntrega'].'</i></b>).';
            } elseif ( ($_REQUEST['dtEntrega'] == $_REQUEST['dtAbertura']) && ( str_replace(':','',$_REQUEST['stHoraEntrega']) > str_replace(':','',$_REQUEST['stHoraAbertura']) ) && $stMensagem == '' ) {
            $stMensagem = 'Data e hora da abertura (<b><i>'.$_REQUEST['dtAbertura'].' '.$_REQUEST['stHoraAbertura'].'</i></b>) deve ser igual ou maior a data e hora de entrega (<b><i>'.$_REQUEST['dtEntrega'].' '.$_REQUEST['stHoraEntrega'].'</i></b>).';
            }

            // VERIFICA SE A DATA DE APROVAÇÃO É SUPERIOR A DATA DE ENTREGA
            if ( ( cmpDt($_REQUEST['dtAprovacao'], $_REQUEST['dtEntrega']) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de aprovação do jurídico (<b><i>'.$_REQUEST['dtAprovacao'].'</i></b>) deve ser menor que a data de entrega (<b><i>'.$_REQUEST['dtEntrega'].'</i></b>).';
            }

            // VERIFICA SE A DATA DE VALIDADE É SUPERIOR A DATA DE ABERTURA
            if ( ( cmpDt($_REQUEST['dtAbertura'], $_REQUEST['dtValidade']) ) && $stMensagem == '' ) {
                $stMensagem = 'Data de validade das propostas (<b><i>'.$_REQUEST['dtValidade'].'</i></b>) deve ser maior que a data de abertura das propostas(<b><i>'.$_REQUEST['dtAbertura'].'</i></b>).';
            }

            $dtInicio = dataYMD($_REQUEST['dtEntrega']).' 00:00:00';
            $dtFim = dataYMD($_REQUEST['dtValidade']).' 23:59:59';
            $qtd_dias_validade = SistemaLegado::datediff('d', $dtInicio, $dtFim);

            if ($stMensagem == '') {
            $obTLicitacaoEdital->setDado( 'num_edital'              , $_REQUEST['inNumEdital']           );
            $obTLicitacaoEdital->setDado( 'exercicio'               , Sessao::getExercicio()                 );

                // POR QUE AINDA NÃO TEM O COMPONENTE QUE SELECIONA O DOCUMENTO O CODIGO TIPO E O CODIGO DO
                // DOCUMENTO ESTÃO FIXADOS COMO 0 (NÃO INFORMADO)

            $obTLicitacaoEdital->setDado( 'cod_tipo_documento'      , 0    								 );
            $obTLicitacaoEdital->setDado( 'cod_documento'           , 0    							     );

                $obTLicitacaoEdital->setDado( 'responsavel_juridico'    , $_REQUEST['inResponsavelJuridico'] );
                $obTLicitacaoEdital->setDado( 'exercicio_licitacao'     , $_REQUEST['stExercicioLicitacao'] );
                $obTLicitacaoEdital->setDado( 'cod_entidade'            , $_REQUEST['inCodEntidade']         );
                $obTLicitacaoEdital->setDado( 'cod_modalidade'          , $_REQUEST['inCodModalidade']       );
                $obTLicitacaoEdital->setDado( 'cod_licitacao'           , $_REQUEST['inCodLicitacao']        );
                $obTLicitacaoEdital->setDado( 'local_entrega_propostas' , $_REQUEST['stLocalEntrega']        );
                $obTLicitacaoEdital->setDado( 'dt_entrega_propostas'    , $_REQUEST['dtEntrega']             );
                $obTLicitacaoEdital->setDado( 'hora_entrega_propostas'  , $_REQUEST['stHoraEntrega']         );
                $obTLicitacaoEdital->setDado( 'local_abertura_propostas', $_REQUEST['stLocalAbertura']       );
                $obTLicitacaoEdital->setDado( 'dt_abertura_propostas'   , $_REQUEST['dtAbertura']            );
                $obTLicitacaoEdital->setDado( 'hora_abertura_propostas' , $_REQUEST['stHoraAbertura']        );
                $obTLicitacaoEdital->setDado( 'dt_validade_proposta'    , $_REQUEST['dtValidade']            );
                $obTLicitacaoEdital->setDado( 'observacao_validade_proposta', $_REQUEST['txtValidade']       );
                $obTLicitacaoEdital->setDado( 'condicoes_pagamento'     , $_REQUEST['txtCodPagamento']       );
                $obTLicitacaoEdital->setDado( 'local_entrega_material'  , $_REQUEST['stLocalMaterial']       );
                $obTLicitacaoEdital->setDado( 'dt_aprovacao_juridico'   , $_REQUEST['dtAprovacao']           );

                $obTLicitacaoEdital->alteracao();

                sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Edital: ".$obTLicitacaoEdital->getDado('num_edital')."/".Sessao::getExercicio(),"alterar","aviso", Sessao::getId(), "../");
                if ($_REQUEST['boGerarDocumento'] == 'S') {
                    $_REQUEST['qtdDiasValidade'] = $qtd_dias_validade;
                    Sessao::write('request', $_REQUEST);
                    SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
                }
            } else {
            sistemaLegado::exibeAviso(urlencode($stMensagem),'n_alterar','erro');
            }
    break;

    case 'anular':
        $arEdital = explode('/',$_REQUEST['stNumEdital']);
        include_once ( TLIC. "TLicitacaoEditalAnulado.class.php" );
        $obTLicitacaoEditalAnulado = new TLicitacaoEditalAnulado();
        $obTLicitacaoEditalAnulado->setDado( 'num_edital', $arEdital[0] );
        $obTLicitacaoEditalAnulado->setDado( 'exercicio' , $arEdital[1] );
        $obTLicitacaoEditalAnulado->setDado( 'justificativa', $_REQUEST['stJustificativa'] );

        $obTLicitacaoEditalAnulado->inclusao();

        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=anular","Edital: ".$arEdital[0]."/".$arEdital[1], "anular","aviso", Sessao::getId(), "../");
    break;

    case 'imprimir':

        Sessao::write('request', $_REQUEST);

        if ($_REQUEST['boGerarDocumento'] == 'S') {
            SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
        }
    break;

}

Sessao::encerraExcecao();
