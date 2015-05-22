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

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGReceitaCorrente.class.php');
    
    $arFiltros = Sessao::read('filtroRelatorio');
    
    $obFTCEMGReceitaCorrente = New FTCEMGReceitaCorrente();
    $obFTCEMGReceitaCorrente->setDado('stExercicio'            , Sessao::read('exercicio'));
    $obFTCEMGReceitaCorrente->setDado('stCodEntidades'         , implode(',', $arFiltros['inCodEntidadeSelecionado']));

    if ($arFiltros['stTipoPeriodo'] == 'mensal') {
        $stDataInicial = "01/".$arFiltros['inPeriodo']."/".Sessao::read('exercicio');
        $stDataFinal = SistemaLegado::retornaUltimoDiaMes($arFiltros['inPeriodo'], Sessao::read('exercicio'));
    } else {
        SistemaLegado::periodoInicialFinalBimestre($stDataInicial, $stDataFinal, $arFiltros['inPeriodo'], Sessao::read('exercicio'));
    }

    $obFTCEMGReceitaCorrente->setDado('stDataInicial'          , $stDataInicial);
    $obFTCEMGReceitaCorrente->setDado('stDataFinal'            , $stDataFinal);
    
    $obFTCEMGReceitaCorrente->recuperaTodos($rsReceitaCorrente);
    
    $arDados = array();
    $arRealizada = array();
     
     $arDados['mes']     = $arFiltros['inPeriodo'];
     $arRealizada['mes'] = $arFiltros['inPeriodo'];
     
    while ( !$rsReceitaCorrente->eof()) {
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.2') {
                $arDados['recContrib']     = $arDados['recContrib'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recContrib'] = $arRealizada['recContrib'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.5') {
                $arDados['recIndust']     = $arDados['recIndust'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recIndust'] = $arRealizada['recIndust'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.4') {
                $arDados['recAgropec']     = $arDados['recAgropec'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recAgropec'] = $arRealizada['recAgropec'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.6') {
                $arDados['recServ']     = $arDados['recServ'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recServ'] = $arRealizada['recServ'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.9') {
                $arDados['outrasRecCor']     = $arDados['outrasRecCor'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['outrasRecCor'] = $arRealizada['outrasRecCor'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '9.1.1') {
                $arDados['deducoesExcFundeb']     = $arDados['deducoesExcFundeb'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['deducoesExcFundeb'] = $arRealizada['deducoesExcFundeb'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '1.1.2') {
                $arDados['tribTaxas']     = $arDados['tribTaxas'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['tribTaxas'] = $arRealizada['tribTaxas'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '1.1.3') {
                $arDados['tribContMelhoria']     = $arDados['tribContMelhoria'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['tribContMelhoria'] = $arRealizada['tribContMelhoria'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.2.02') {
                $arDados['recIPTU']     = $arDados['recIPTU'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recIPTU'] = $arRealizada['recIPTU'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.3.05') {
                $arDados['recISSQN']     = $arDados['recISSQN'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recISSQN'] = $arRealizada['recISSQN'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.2.08') {
                $arDados['recITBI']     = $arDados['recITBI'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recITBI'] = $arRealizada['recITBI'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,10) == '1.1.1.2.04') {
                $arDados['transfIRRF']     = $arDados['transfIRRF'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['transfIRRF'] = $arRealizada['transfIRRF'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,7) == '1.3.2.5') {
                $arDados['recAplic']     = $arDados['recAplic'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['recAplic'] = $arRealizada['recAplic'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( (substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,3) == '1.3') || 
                 (substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,7) == '1.3.2.5')) {
               
                    $valorOutrasRecursosPrevisto   = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.3.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('valor_previsto') :  $valorOutrasRecursosPrevisto - $rsReceitaCorrente->getCampo('valor_previsto');
                    $valorOutrasRecursosArrecadado = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.3.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('arrecadado_periodo') :  $valorOutrasRecursosArrecadado - $rsReceitaCorrente->getCampo('arrecadado_periodo');
               
                    $arDados['outrasRec']     = $valorOutrasRecursosPrevisto;
                    $arRealizada['outrasRec'] = $valorOutrasRecursosArrecadado;
               
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.1.01.02') {
                $arDados['cotaParteFPM']     = $arDados['cotaParteFPM'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['cotaParteFPM'] = $arRealizada['cotaParteFPM'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.2.01.01') {
                $arDados['cotaParteICMS']     = $arDados['cotaParteICMS'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['cotaParteICMS'] = $arRealizada['cotaParteICMS'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.2.01.04') {
                $arDados['cotaParteIPI']     = $arDados['cotaParteIPI'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['cotaParteIPI'] = $arRealizada['cotaParteIPI'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,13) == '1.7.2.2.01.02') {
                $arDados['cotaParteIPVA']     = $arDados['cotaParteIPVA'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['cotaParteIPVA'] = $arRealizada['cotaParteIPVA'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,16) == '1.7.2.1.01.02.06') {
                $arDados['transfFUNDEB']     = $arDados['transfFUNDEB'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['transfFUNDEB'] = $arRealizada['transfFUNDEB'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( substr($rsReceitaCorrente->getCampo('cod_estrutural'),0,5) == '1.7.6') {
                $arDados['convenios']     = $arDados['convenios'] + $rsReceitaCorrente->getCampo('valor_previsto');
                $arRealizada['convenios'] = $arRealizada['convenios'] + $rsReceitaCorrente->getCampo('arrecadado_periodo');
            }
            if ( (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 3)   == '1.7') ||
                 (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13)  == '1.7.2.1.01.02') ||
                 (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13)  == '1.7.2.2.01.01') ||
                 (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13)  == '1.7.2.2.01.04') ||
                 (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 13)  == '1.7.2.2.01.02') ||
                 (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 16)  == '1.7.2.1.01.02.06') ||
                 (substr($rsReceitaCorrente->getCampo('cod_estrutural'), 0, 5)   == '1.7.6')) {
                
                    $valorOutrasTransferenciasPrevisto   = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('valor_previsto') :  $valorOutrasTransferenciasPrevisto - $rsReceitaCorrente->getCampo('valor_previsto');
                    $valorOutrasTransferenciasArrecadado = ( $rsReceitaCorrente->getCampo('cod_estrutural') == '1.7.0.0.00.00.00.00.00') ? $rsReceitaCorrente->getCampo('arrecadado_periodo') :  $valorOutrasTransferenciasArrecadado - $rsReceitaCorrente->getCampo('arrecadado_periodo');
               
                    $arDados['outrasTransf']     = $valorOutrasTransferenciasPrevisto;
                    $arRealizada['outrasTransf'] = $valorOutrasTransferenciasArrecadado;
                
            }
            
        $rsReceitaCorrente->proximo();
    }
  
    $rsTemp = new RecordSet;
    
    // Atribui o valor para previsão anual inicial, previsão anual atualizada e prevista, os 3 primeiros tipos, que possuem o valor de campo da consulta, de acordo com o campo valor_previsto .
    for ($inContador = 1; $inContador < 4 ; $inContador++) {
         $arDados['cod_tipo'] = '0'.$inContador;
         $arFinal[] = $arDados;
    }
    
    // Adiciona por último no array os valores do tipo 4, realizada, que possui valores da consulta do campo arrecadado_periodo.
    $arRealizada['cod_tipo'] = '04';
    $arFinal[] = $arRealizada;

    $rsTemp->preenche($arFinal);

    $obExportador->roUltimoArquivo->addBloco($rsTemp);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recContrib');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recIndust');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recAgropec');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recServ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outrasRecCor');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('deducoesExcFundeb');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tribTaxas');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tribContMelhoria');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recIPTU');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recISSQN');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recITBI');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('transfIRRF');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('recAplic');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outrasRec');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteFPM');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteICMS');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteIPI');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cotaParteIPVA');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('transfFUNDEB');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('convenios');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outrasTransf');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
?>