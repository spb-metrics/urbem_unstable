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
    * Arquivo de geracao do arquivo receitaCorrente TCM/MG
    * Data de Criação   : 29/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO. 'FTCEMGReceitaCorrente.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    $ndias =  cal_days_in_month(CAL_GREGORIAN, $arFiltros['inPeriodo'], Sessao::read('exercicio') );
    if ($arFiltros['inPeriodo'] < 10) {
         $arFiltros['inPeriodo'] = '0'.$arFiltros['inPeriodo'];
    }

    $obFTCEMGReceitaCorrente = New FTCEMGReceitaCorrente();
    $obFTCEMGReceitaCorrente->setDado('stExercicio'            , Sessao::read('exercicio'));
    $obFTCEMGReceitaCorrente->setDado('stFiltro'               , '');
    $obFTCEMGReceitaCorrente->setDado('dtInicial'              , '01/'.$arFiltros['inPeriodo'].'/'.Sessao::read('exercicio')   );
    $obFTCEMGReceitaCorrente->setDado('dtFinal'                , $ndias.'/'.$arFiltros['inPeriodo'].'/'.Sessao::read('exercicio'));
    $obFTCEMGReceitaCorrente->setDado('stCodEntidades'         , implode(',', $arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGReceitaCorrente->setDado('stCodEstruturalInicial' , '');
    $obFTCEMGReceitaCorrente->setDado('stCodEstruturalFinal'   , '');
    $obFTCEMGReceitaCorrente->setDado('stCodReduzidoInicial'   , '');
    $obFTCEMGReceitaCorrente->setDado('stCodReduzidoFinal'     , '');
    $obFTCEMGReceitaCorrente->setDado('inCodRecurso'           , '');
    $obFTCEMGReceitaCorrente->setDado('stDestinacaoRecurso'    , '');
    $obFTCEMGReceitaCorrente->setDado('inCodDetalhamento'      , '');

    $obFTCEMGReceitaCorrente->recuperaTodos($rsReceitaCorrente);

    $arDados['mes']     = $ndias;
    $arRealizada['mes'] = $ndias;
    while ( !$rsReceitaCorrente->eof()) {
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.2.0.0.00.00.00.00.00') {
                $arDados['1']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['1'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.5.0.0.00.00.00.00.00') {
                $arDados['2']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['2'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.4.0.0.00.00.00.00.00') {
                $arDados['3']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['3'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.6.0.0.00.00.00.00.00') {
                $arDados['4']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['4'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.9.0.0.00.00.00.00.00') {
                $arDados['5']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['5'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.1.2.0.00.00.00.00.00') {
                $arDados['6']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['6'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.3.4.0.00.00.00.00.00') {
                $arDados['7']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['7'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.3.9.0.00.00.00.00.00') {
                $arDados['8']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['8'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.1.3.0.00.00.00.00.00') {
                $arDados['9']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['9'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.1.1.2.02.00.00.00.00') {
                $arDados['10']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['10'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.1.1.3.05.00.00.00.00') {
                $arDados['11']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['11'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.1.1.2.08.00.00.00.00') {
                $arDados['12']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['12'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.2.1.01.02.00.00.00') {
                $arDados['13']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['13'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.1.1.2.04.31.00.00.00') {
                $arDados['14']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['14'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.2.2.01.01.00.00.00') {
                $arDados['15']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['15'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.2.2.01.02.00.00.00') {
                $arDados['16']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['16'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.2.2.01.04.00.00.00') {
                $arDados['17']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['17'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.2.4.01.00.00.00.00') {
                $arDados['18']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['18'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '2.4.7.0.00.00.00.00.00') {
                $arDados['19']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['19'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.6.1.99.00.00.00.00') {
                $arDados['20']     = $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['20'] = $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 1)  == '9') {
                $arDados['21']     = $arDados[21] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['21'] = $arRealizada[21] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
        $rsReceitaCorrente->proximo();
    }

    if ($arRealizada[21] == '0') {
        $arRealizada[21] = '000';
    }
    $rsTemp = new RecordSet;
    for ($c=1;$c<4;$c++) {
         $arDados['cod_tipo'] = '0'.$c;
         $arFinal[] = $arDados;
    }
    $arRealizada['cod_tipo'] = '04';
    $arFinal[] = $arRealizada;

    $rsTemp->preenche($arFinal);

    $obExportador->roUltimoArquivo->addBloco($rsTemp);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('1');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('2');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('3');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('4');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('5');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('6');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('7');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('8');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('9');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('10');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('11');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('12');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('13');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('14');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('15');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('16');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('17');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('18');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('19');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('20');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('21');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
