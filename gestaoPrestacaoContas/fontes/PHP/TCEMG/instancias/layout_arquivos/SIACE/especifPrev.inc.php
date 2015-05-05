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
    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 03/02/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: especifPrev.inc.php 61835 2015-03-09 14:28:12Z michel $
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO . 'FTCEMGEspecifPrev.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    $ndias =  cal_days_in_month(CAL_GREGORIAN, $arFiltros['inPeriodo'], Sessao::read('exercicio') );
    if ($arFiltros['inPeriodo'] < 10) {
        $arFiltros['inPeriodo'] = '0'.$arFiltros['inPeriodo'];
    }

    $obFTCEMGEspecifPrev = new FTCEMGEspecifPrev();
    $obFTCEMGEspecifPrev->setDado('stExercicio'   , Sessao::read('exercicio'));
    $obFTCEMGEspecifPrev->setDado('dtInicio'      , '01/'.$arFiltros['inPeriodo'].'/'.Sessao::read('exercicio')   );
    $obFTCEMGEspecifPrev->setDado('dtFinal'       , $ndias.'/'.$arFiltros['inPeriodo'].'/'.Sessao::read('exercicio'));
    $obFTCEMGEspecifPrev->setDado('stEntidades'   , implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGEspecifPrev->setDado('stRpps'        , 'false');

    $obFTCEMGEspecifPrev->recuperaTodos($rsEspecifPrev);

    while (  !$rsEspecifPrev->eof() ) {
            $rsEspecifPrev->setCampo('mes', $arFiltros['inPeriodo']);
            $rsEspecifPrev->Proximo();
    }
    $rsEspecifPrev->setPrimeiroElemento();

    $obExportador->roUltimoArquivo->addBloco($rsEspecifPrev);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('caixa');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('aplicacoes_financeiras');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('banco');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    
