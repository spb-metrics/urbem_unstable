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
    * Data de Criação   : 19/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO . 'FTCEMGComparativoPe.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    $ndias =  cal_days_in_month(CAL_GREGORIAN, $arFiltros['inPeriodo'], Sessao::read('exercicio') );
    if ($arFiltros['inPeriodo'] < 10) {
        $arFiltros['inPeriodo'] = '0'.$arFiltros['inPeriodo'];
    }

    $obFTCEMGComparativoPe = new FTCEMGComparativoPe();
    $obFTCEMGComparativoPe->setDado('exercicio'   , Sessao::read('exercicio'));
    $obFTCEMGComparativoPe->setDado('dtInicial'   , '01/'.$arFiltros['inPeriodo'].'/'.Sessao::read('exercicio')   );
    $obFTCEMGComparativoPe->setDado('dtFinal'     , $ndias.'/'.$arFiltros['inPeriodo'].'/'.Sessao::read('exercicio'));
    $obFTCEMGComparativoPe->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));

    $obFTCEMGComparativoPe->recuperaTodos($rsComparativoPe);
    $rsDados = new RecordSet();
    $arDados['mes'] = $ndias;
    $c = 1;
    while (  !$rsComparativoPe->eof() ) {
            $arDados['valor_'.$c] = $rsComparativoPe->getCampo('valor');

            $rsComparativoPe->Proximo();
            $c=$c+1;
    }

    $arDados['valor_'.$c] =  '000';
    $c=$c+1;
    $arDados['valor_'.$c] = '000';

    $rsDados->add($arDados);

    $obExportador->roUltimoArquivo->addBloco($rsDados);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor_1');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor_2');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor_3');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor_4');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor_5');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor_6');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valor_7');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
